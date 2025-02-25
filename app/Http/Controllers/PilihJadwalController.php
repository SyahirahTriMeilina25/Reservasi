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
        
        $dosenList = DB::table('dosens')
            ->select('nip', 'nama')
            ->get()
            ->map(function($dosen) {
                return [
                    'nip' => $dosen->nip,
                    'nama' => $dosen->nama
                ];
            })
            ->toArray();

        return view('bimbingan.mahasiswa.pilihjadwal', [
            'dosenList' => $dosenList,
            'isConnected' => $isConnected,
            'email' => $mahasiswa->email
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
                ->where(function($query) use ($jadwal) {
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
            // Validasi request
            $request->validate([
                'nip' => 'required|exists:dosens,nip',
                'jenis_bimbingan' => 'required|in:skripsi,kp,akademik,konsultasi'
            ]);

            // Get jadwal dasar yang tersedia
            $baseJadwal = DB::table('jadwal_bimbingans as jb')
                ->join('dosens as d', 'jb.nip', '=', 'd.nip')
                ->where('jb.nip', $request->nip)
                ->where('jb.status', 'tersedia')
                ->where('jb.waktu_mulai', '>', now())
                ->select(
                    'jb.id',
                    'jb.event_id',
                    'jb.waktu_mulai',
                    'jb.waktu_selesai',
                    'jb.catatan',
                    'jb.lokasi',
                    'd.nama as dosen_nama'
                )
                ->get();

            $allJadwal = collect();

            foreach ($baseJadwal as $jadwal) {
                // Tambahkan jadwal asli
                $waktuMulai = Carbon::parse($jadwal->waktu_mulai);
                $waktuSelesai = Carbon::parse($jadwal->waktu_selesai);

                // Cek apakah mahasiswa sudah memilih jadwal ini
                $isSelected = DB::table('usulan_bimbingans')
                    ->where('nim', auth()->user()->nim)
                    ->where('event_id', $jadwal->event_id)
                    ->where('status', '!=', 'DITOLAK')
                    ->exists();

                $allJadwal->push([
                    'id' => $jadwal->id,
                    'event_id' => $jadwal->event_id,
                    'tanggal' => $waktuMulai->isoFormat('dddd, D MMMM Y'),
                    'waktu' => $waktuMulai->format('H:i') . ' - ' . $waktuSelesai->format('H:i'),
                    'waktu_mulai_raw' => $waktuMulai->format('Y-m-d H:i:s'), // Tambahkan ini untuk sorting
                    'lokasi' => $jadwal->lokasi,
                    'catatan' => $jadwal->catatan,
                    'dosen_nama' => $jadwal->dosen_nama,
                    'is_selected' => $isSelected
                ]);
            }

            // Sort menggunakan waktu_mulai_raw
            $sortedJadwal = $allJadwal->sortBy('waktu_mulai_raw')
                ->map(function ($item) {
                    // Hapus waktu_mulai_raw dari output
                    unset($item['waktu_mulai_raw']);
                    return $item;
                })
                ->values();

            return response()->json([
                'status' => 'success',
                'data' => $sortedJadwal
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting available jadwal: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cek ketersediaan jadwal
     */
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

            // Get event_id untuk logging
            $jadwal = DB::table('jadwal_bimbingans')
                ->where('id', $request->jadwal_id)
                ->first();
                
            if (!$jadwal) {
                return response()->json([
                    'available' => false,
                    'message' => 'Jadwal tidak ditemukan'
                ]);
            }

            // Cek existing bimbingan
            $existingBimbingan = DB::table('usulan_bimbingans')
                ->where('nim', auth()->user()->nim)
                ->where('event_id', $jadwal->event_id)
                ->where('status', '!=', 'DITOLAK')
                ->exists();

            if ($existingBimbingan) {
                return response()->json([
                    'available' => false,
                    'message' => 'Anda sudah pernah mengajukan bimbingan untuk jadwal ini'
                ]);
            }

            // Cek pending bimbingan
            $pendingBimbingan = DB::table('usulan_bimbingans')
                ->where('nim', auth()->user()->nim)
                ->where('jenis_bimbingan', $request->jenis_bimbingan)
                ->whereIn('status', ['USULAN', 'DITERIMA'])
                ->exists();

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
                ->where(function($query) use ($jadwal) {
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
                'message' => $e->getMessage()
            ], 400);
        }
    }
}