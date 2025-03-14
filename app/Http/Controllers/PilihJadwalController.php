<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalBimbingan;
use App\Models\UsulanBimbingan;
use App\Models\Mahasiswa;
use App\Models\Dosen;

class PilihJadwalController extends Controller
{
    protected $googleCalendarController;

    public function __construct(GoogleCalendarController $googleCalendarController)
    {
        $this->googleCalendarController = $googleCalendarController;
    }

    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $isConnected = false;
        if ($mahasiswa->hasGoogleCalendarConnected()) {
            $isConnected = app(GoogleCalendarController::class)->validateAndRefreshToken();
        }

        // Ambil daftar dosen
        $dosenList = DB::table('dosens')
            ->select('nip', 'nama')
            ->get()
            ->map(function ($dosen) {
                return [
                    'nip' => $dosen->nip,
                    'nama' => $dosen->nama
                ];
            })
            ->toArray();

        // Cek total jadwal dengan jenis bimbingan untuk debugging
        $totalJadwalDenganJenis = DB::table('jadwal_bimbingans')
            ->whereNotNull('jenis_bimbingan')
            ->where('jenis_bimbingan', '!=', '')
            ->count();

        Log::info('Total jadwal dengan jenis_bimbingan:', ['count' => $totalJadwalDenganJenis]);

        // Ambil jenis bimbingan yang tersedia untuk setiap dosen
        $jenisBimbinganPerDosen = [];
        foreach ($dosenList as $dosen) {
            // Query jadwal yang memiliki jenis bimbingan
            $jadwalDenganJenis = DB::table('jadwal_bimbingans')
                ->where('nip', $dosen['nip'])
                ->whereNotNull('jenis_bimbingan')
                ->where('jenis_bimbingan', '<>', '') // Pastikan tidak kosong
                ->where('status', 'tersedia')
                ->distinct()
                ->select('jenis_bimbingan') // Pilih kolom saja untuk efisiensi
                ->pluck('jenis_bimbingan')
                ->toArray();

            // Debug untuk setiap dosen
            Log::info("Jenis bimbingan untuk dosen {$dosen['nip']} ({$dosen['nama']}):", $jadwalDenganJenis);

            if (!empty($jadwalDenganJenis)) {
                $jenisBimbinganPerDosen[$dosen['nip']] = $jadwalDenganJenis;
            }
        }

        // Log untuk debugging
        Log::info('Data yang dikirim ke view:', [
            'jenisBimbinganPerDosen' => $jenisBimbinganPerDosen,
            'countDosen' => count($dosenList)
        ]);

        return view('bimbingan.mahasiswa.pilihjadwal', [
            'dosenList' => $dosenList,
            'isConnected' => $isConnected,
            'email' => $mahasiswa->email,
            'jenisBimbinganPerDosen' => $jenisBimbinganPerDosen
        ]);
    }

    public function store(Request $request)
    {
        try {
            if (!$this->googleCalendarController->validateAndRefreshToken()) {
                return response()->json(['error' => 'Belum terautentikasi dengan Google Calendar'], 401);
            }
            Log::info('Request pengajuan bimbingan:', $request->all());

            // Validasi request
            $request->validate([
                'nip' => 'required|exists:dosens,nip',
                'jenis_bimbingan' => 'required|in:skripsi,kp,akademik,konsultasi',
                'jadwal_id' => 'required|exists:jadwal_bimbingans,id',
                'deskripsi' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Cek jadwal dan dosen
            $jadwal = DB::table('jadwal_bimbingans as jb')
                ->join('dosens as d', 'jb.nip', '=', 'd.nip')
                ->where('jb.id', $request->jadwal_id)
                ->where('jb.status', 'tersedia')
                ->where('jb.waktu_mulai', '>', now())
                ->select('jb.*', 'd.nama as dosen_nama')
                ->first();

            if (!$jadwal) {
                throw new \Exception('Jadwal tidak tersedia atau sudah penuh');
            }

            // Cek apakah mahasiswa sudah memiliki bimbingan yang sama
            $existingBimbingan = DB::table('usulan_bimbingans')
                ->where('nim', Auth::guard('mahasiswa')->user()->nim)
                ->where('jenis_bimbingan', $request->jenis_bimbingan)
                ->whereIn('status', ['USULAN', 'DITERIMA'])
                ->exists();

            if ($existingBimbingan) {
                throw new \Exception('Anda masih memiliki pengajuan bimbingan yang dalam proses');
            }

            // Cek bentrok jadwal
            $bentrok = DB::table('usulan_bimbingans')
                ->where('nim', Auth::guard('mahasiswa')->user()->nim)
                ->where('tanggal', Carbon::parse($jadwal->waktu_mulai)->toDateString())
                ->where(function ($query) use ($jadwal) {
                    $query->whereBetween('waktu_mulai', [
                        Carbon::parse($jadwal->waktu_mulai)->format('H:i'),
                        Carbon::parse($jadwal->waktu_selesai)->format('H:i')
                    ])
                        ->orWhereBetween('waktu_selesai', [
                            Carbon::parse($jadwal->waktu_mulai)->format('H:i'),
                            Carbon::parse($jadwal->waktu_selesai)->format('H:i')
                        ]);
                })
                ->where('status', '!=', 'DITOLAK')
                ->exists();

            if ($bentrok) {
                throw new \Exception('Anda sudah memiliki jadwal bimbingan di waktu yang sama');
            }

            $mahasiswa = Auth::guard('mahasiswa')->user();

            // Simpan ke database
            $bimbingan = UsulanBimbingan::create([
                'nim' => $mahasiswa->nim,
                'nip' => $request->nip,
                'dosen_nama' => $jadwal->dosen_nama,
                'mahasiswa_nama' => $mahasiswa->nama,
                'jenis_bimbingan' => $request->jenis_bimbingan,
                'tanggal' => Carbon::parse($jadwal->waktu_mulai)->toDateString(),
                'waktu_mulai' => Carbon::parse($jadwal->waktu_mulai)->format('H:i'),
                'waktu_selesai' => Carbon::parse($jadwal->waktu_selesai)->format('H:i'),
                'lokasi' => $jadwal->lokasi,
                'deskripsi' => $request->deskripsi,
                'status' => 'USULAN',
                'event_id' => $jadwal->event_id,
                'nomor_antrian' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            Log::info('Berhasil membuat usulan bimbingan:', [
                'bimbingan_id' => $bimbingan,
                'event_id' => $jadwal->event_id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal bimbingan berhasil diajukan',
                'data' => $bimbingan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error membuat jadwal bimbingan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableJadwal(Request $request)
    {
        try {
            $nip = $request->nip;
            $jenisBimbingan = $request->jenis_bimbingan;

            // Log untuk debugging
            Log::info('Request get available jadwal:', [
                'nip' => $nip,
                'jenis_bimbingan' => $jenisBimbingan
            ]);

            // Debug: Cek jadwal tersedia untuk dosen ini
            $totalJadwalDosen = DB::table('jadwal_bimbingans')
                ->where('nip', $nip)
                ->where('status', 'tersedia')
                ->where('waktu_mulai', '>', now())
                ->count();

            Log::info('Total jadwal tersedia untuk dosen:', [
                'nip' => $nip,
                'count' => $totalJadwalDosen
            ]);

            // Debug: Cek jadwal tersedia dengan jenis bimbingan ini
            $totalJadwalDenganJenis = DB::table('jadwal_bimbingans')
                ->where('nip', $nip)
                ->where('jenis_bimbingan', $jenisBimbingan)
                ->where('status', 'tersedia')
                ->where('waktu_mulai', '>', now())
                ->count();

            Log::info('Total jadwal dengan jenis spesifik:', [
                'nip' => $nip,
                'jenis_bimbingan' => $jenisBimbingan,
                'count' => $totalJadwalDenganJenis
            ]);

            Log::info(
                'Semua jadwal untuk dosen ini:',
                DB::table('jadwal_bimbingans')
                    ->where('nip', $nip)
                    ->select('id', 'event_id', 'jenis_bimbingan', 'waktu_mulai')
                    ->get()
                    ->toArray()
            );

            // Debug query SQL
            DB::enableQueryLog();


            // Query jadwal-jadwal yang tersedia
            $jadwals = DB::table('jadwal_bimbingans as jb')
                ->join('dosens as d', 'jb.nip', '=', 'd.nip')
                ->select(
                    'jb.id',
                    'jb.event_id',
                    'jb.waktu_mulai',
                    'jb.waktu_selesai',
                    'jb.catatan',
                    'jb.lokasi',
                    'jb.jenis_bimbingan',
                    'jb.kapasitas',
                    'jb.sisa_kapasitas',
                    'jb.has_kuota_limit',
                    'd.nama as dosen_nama'
                )
                ->where('jb.nip', $nip)
                ->where('jb.status', 'tersedia')
                ->where('jb.waktu_mulai', '>', now())
                ->where(function ($query) use ($jenisBimbingan) {
                    // PERBAIKAN DI SINI: Hanya tampilkan jadwal dengan jenis bimbingan yang cocok,
                    // tanpa menampilkan jadwal null
                    $query->where('jb.jenis_bimbingan', $jenisBimbingan)
                        ->orWhereNull('jb.jenis_bimbingan');
                })
                ->where(function ($query) {
                    // Filter jadwal dengan kuota tersedia
                    $query->where('jb.has_kuota_limit', false)
                        ->orWhere('jb.sisa_kapasitas', '>', 0);
                })
                ->get();

            Log::info('Nilai yang akan disimpan:', [
                'jenis_bimbingan' => $jenisBimbingan,
                'request_data' => $request->all()
            ]);
            Log::info('Query SQL:', ['query' => DB::getQueryLog()]);

            // Debug
            Log::info("Jumlah jadwal ditemukan: " . $jadwals->count());
            if ($jadwals->count() > 0) {
                Log::info("Contoh jadwal pertama:", (array)$jadwals->first());
            }

            // Transform jadwals untuk format yang sesuai dengan frontend
            $transformedJadwals = [];
            foreach ($jadwals as $jadwal) {
                // Gunakan waktu_mulai sebagai tanggal dan waktu
                $tanggal = Carbon::parse($jadwal->waktu_mulai)->format('Y-m-d');
                $waktuMulai = Carbon::parse($jadwal->waktu_mulai)->format('H:i');
                $waktuSelesai = Carbon::parse($jadwal->waktu_selesai)->format('H:i');

                $hari = Carbon::parse($jadwal->waktu_mulai)->locale('id')->isoFormat('dddd');
                $tanggalFormat = Carbon::parse($jadwal->waktu_mulai)->locale('id')->isoFormat('D MMMM YYYY');

                // Format display text dengan waktu mulai dan selesai
                $displayText = "{$hari}, {$tanggalFormat} | {$waktuMulai}-{$waktuSelesai}";

                Log::info('Nilai has_kuota_limit: ' . json_encode($jadwal->has_kuota_limit));
                Log::info('Nilai kapasitas: ' . $jadwal->kapasitas);
                Log::info('Nilai sisa_kapasitas: ' . $jadwal->sisa_kapasitas);

                // Tambahkan informasi kuota
                if ($jadwal->kapasitas > 0) {
                    // Hitung pengajuan untuk jadwal ini, termasuk yang sudah DISETUJUI
                    $pengajuanData = DB::table('usulan_bimbingans')
                        ->where('event_id', $jadwal->event_id)
                        ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI']) // Tambahkan DISETUJUI
                        ->get();

                    $pengajuanCount = $pengajuanData->count();

                    Log::info('Pengajuan untuk jadwal ' . $jadwal->id . ':', [
                        'event_id' => $jadwal->event_id,
                        'count' => $pengajuanCount,
                        'data' => $pengajuanData->toArray(),
                        'status_included' => ['USULAN', 'DITERIMA', 'DISETUJUI']
                    ]);
                    // Tampilkan jumlah slot yang terpakai dari total kapasitas
                    $displayText .= " | Kuota: {$pengajuanCount}/{$jadwal->kapasitas} terpakai";
                } else {
                    $displayText .= " | Kuota Tidak Terbatas";
                }

                $transformedJadwals[] = [
                    'id' => $jadwal->id,
                    'event_id' => $jadwal->event_id,
                    'tanggal' => $tanggal,
                    'waktu' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'text' => $displayText,
                    'is_selected' => false,
                    'sisa_kapasitas' => $jadwal->sisa_kapasitas
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $transformedJadwals
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting available jadwal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat jadwal: ' . $e->getMessage()
            ], 400);
        }
    }

    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'jadwal_id' => 'required|exists:jadwal_bimbingans,id',
                'jenis_bimbingan' => 'required|in:skripsi,kp,akademik,konsultasi'
            ]);

            Log::info('Check Availability Request:', [
                'nim' => auth()->user()->nim,
                'jadwal_id' => $request->jadwal_id,
                'jenis_bimbingan' => $request->jenis_bimbingan
            ]);

            // Get jadwal untuk memeriksa
            $jadwal = DB::table('jadwal_bimbingans')
                ->where('id', $request->jadwal_id)
                ->first();

            if (!$jadwal) {
                return response()->json([
                    'available' => false,
                    'message' => 'Jadwal tidak ditemukan'
                ]);
            }

            // Log untuk debugging
            Log::info('Jadwal detail for checkAvailability:', (array)$jadwal);
            Log::info('Parameter pengecekan availability:', [
                'nim_dari_auth' => auth()->user()->nim,
                'event_id' => $jadwal->event_id
            ]);

            // Periksa apakah jadwal sesuai dengan jenis bimbingan yang dipilih
            if (property_exists($jadwal, 'jenis_bimbingan') && $jadwal->jenis_bimbingan && $jadwal->jenis_bimbingan !== $request->jenis_bimbingan) {
                return response()->json([
                    'available' => false,
                    'message' => 'Jadwal tidak tersedia untuk jenis bimbingan yang dipilih'
                ]);
            }

            if (property_exists($jadwal, 'has_kuota_limit') && $jadwal->has_kuota_limit) {
                if (property_exists($jadwal, 'sisa_kapasitas') && $jadwal->sisa_kapasitas <= 0) {
                    return response()->json([
                        'available' => false,
                        'message' => 'Kuota jadwal ini sudah penuh'
                    ]);
                }

                // Jika kuota terbatas dan hanya untuk 1 mahasiswa, cek apakah sudah ada yang mengambil
                if (property_exists($jadwal, 'kapasitas') && $jadwal->kapasitas == 1) {
                    $existingBooking = DB::table('usulan_bimbingans')
                        ->where('event_id', $jadwal->event_id)
                        ->where('nim', '!=', auth()->user()->nim)
                        ->where('status', '!=', 'DITOLAK')
                        ->exists();

                    if ($existingBooking) {
                        return response()->json([
                            'available' => false,
                            'message' => 'Jadwal sudah diambil mahasiswa lain'
                        ]);
                    }
                }
            }

            // Cek existing bimbingan dengan event_id yang sama
            $existingBimbingan = DB::table('usulan_bimbingans')
                ->where('nim', auth()->user()->nim)
                ->where('event_id', $jadwal->event_id)
                ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI']) // Tambahkan DISETUJUI
                ->get();

            Log::info('Existing bimbingan yang ditemukan:', [
                'count' => $existingBimbingan->count(),
                'data' => $existingBimbingan->toArray()
            ]);

            Log::info('Memeriksa usulan bimbingan yang sudah ada:', [
                'nim' => auth()->user()->nim,
                'event_id' => $jadwal->event_id,
                'found' => $existingBimbingan->count(),
                'data' => $existingBimbingan
            ]);

            if ($existingBimbingan->count() > 0) {
                return response()->json([
                    'available' => false,
                    'message' => 'Anda sudah pernah mengajukan bimbingan untuk jadwal ini'
                ]);
            }

            // Cek pending bimbingan
            $pendingBimbingan = DB::table('usulan_bimbingans')
                ->where('nim', auth()->user()->nim)
                ->where('jenis_bimbingan', $request->jenis_bimbingan)
                ->where('event_id', '!=', $jadwal->event_id) // Tambahkan ini untuk mengecualikan jadwal yang sedang dipilih
                ->where('status', 'USULAN')
                ->exists();

            Log::info('Cek pengajuan yang masih dalam proses (selain jadwal ini):', [
                'nim' => auth()->user()->nim,
                'jenis_bimbingan' => $request->jenis_bimbingan,
                'event_id_current' => $jadwal->event_id,
                'has_pending' => $pendingBimbingan
            ]);

            if ($pendingBimbingan) {
                return response()->json([
                    'available' => false,
                    'message' => 'Anda masih memiliki pengajuan bimbingan yang dalam proses'
                ]);
            }

            // Cek jadwal bentrok
            $bentrok = DB::table('usulan_bimbingans')
                ->where('nim', auth()->user()->nim)
                ->where('tanggal', Carbon::parse($jadwal->waktu_mulai)->toDateString())
                ->where(function ($query) use ($jadwal) {
                    $query->whereBetween('waktu_mulai', [
                        Carbon::parse($jadwal->waktu_mulai)->format('H:i'),
                        Carbon::parse($jadwal->waktu_selesai)->format('H:i')
                    ])
                        ->orWhereBetween('waktu_selesai', [
                            Carbon::parse($jadwal->waktu_mulai)->format('H:i'),
                            Carbon::parse($jadwal->waktu_selesai)->format('H:i')
                        ]);
                })
                ->where('status', '!=', 'DITOLAK')
                ->exists();

            if ($bentrok) {
                return response()->json([
                    'available' => false,
                    'message' => 'Anda sudah memiliki jadwal bimbingan di waktu yang sama'
                ]);
            }

            return response()->json([
                'available' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Check Availability Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'available' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 400);
        }
    }
    public function getJenisBimbingan($nip)
    {
        try {
            Log::info('Fetching jenis bimbingan for dosen:', ['nip' => $nip]);

            // 1. Cek jadwal dengan jenis bimbingan yang ditentukan
            DB::enableQueryLog();

            $jadwalDenganJenis = DB::table('jadwal_bimbingans')
                ->where('nip', $nip)
                ->whereNotNull('jenis_bimbingan')
                ->where('jenis_bimbingan', '!=', '')
                ->where('status', 'tersedia')
                ->where('waktu_mulai', '>', now())
                ->distinct()
                ->pluck('jenis_bimbingan')
                ->toArray();

            Log::info('SQL query jadwal dengan jenis:', [
                'query' => DB::getQueryLog(),
                'result' => $jadwalDenganJenis
            ]);
            Log::info('Jadwal dengan jenis bimbingan spesifik:', [
                'count' => count($jadwalDenganJenis),
                'jenis' => $jadwalDenganJenis
            ]);

            // 2. Cek apakah ada jadwal tanpa jenis bimbingan spesifik
            $hasUnspecifiedJadwal = DB::table('jadwal_bimbingans')
                ->where('nip', $nip)
                ->where(function ($query) {
                    $query->whereNull('jenis_bimbingan')
                        ->orWhere('jenis_bimbingan', '');
                })
                ->where('status', 'tersedia')
                ->where('waktu_mulai', '>', now())
                ->exists();

            Log::info('Ada jadwal tanpa jenis bimbingan spesifik:', [
                'exists' => $hasUnspecifiedJadwal
            ]);


            // 3. Tentukan jenis bimbingan yang akan ditampilkan
            $jenisBimbingan = [];

            // Perubahan: Tampilkan semua jenis bimbingan yang ada di sistem
            $allJenisBimbingan = ['skripsi', 'kp', 'akademik', 'konsultasi'];

            // Ada 2 opsi di sini - pilih salah satu sesuai kebutuhan:

            // OPSI 1: Hanya tampilkan jenis bimbingan yang tersedia dalam jadwal dosen
            if (!empty($jadwalDenganJenis)) {
                // Jika ada jadwal dengan jenis spesifik, tampilkan jenis-jenis tersebut
                $jenisBimbingan = $jadwalDenganJenis;
                Log::info('Menampilkan jenis bimbingan dari jadwal spesifik');
            }

            if ($hasUnspecifiedJadwal) {
                // Jika ada jadwal tanpa jenis spesifik, tambahkan semua jenis bimbingan
                $allJenisBimbingan = ['skripsi', 'kp', 'akademik', 'konsultasi'];
                $jenisBimbingan = array_merge($jenisBimbingan, $allJenisBimbingan);
                Log::info('Menambahkan semua jenis bimbingan karena ada jadwal non-spesifik');
            }

            // Hapus duplikat dan urutkan
            $jenisBimbingan = array_unique($jenisBimbingan);
            sort($jenisBimbingan);

            Log::info('Jenis bimbingan yang akan ditampilkan:', $jenisBimbingan);

            return response()->json([
                'success' => true,
                'jenisBimbingan' => $jenisBimbingan,
                'hasUnspecified' => $hasUnspecifiedJadwal,
                'jadwalDenganJenis' => $jadwalDenganJenis // Tambahkan ini untuk debugging
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting jenis bimbingan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
