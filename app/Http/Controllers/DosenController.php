<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\UsulanBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Support\Facades\Validator;
use Google_Service_Calendar;

class DosenController extends Controller
{
    protected $googleCalendarController;

    public function __construct(GoogleCalendarController $googleCalendarController)
    {
        $this->googleCalendarController = $googleCalendarController;
    }
    public function index(Request $request)
    {
        try {
            $activeTab = $request->query('tab', 'usulan');
            $perPage = $request->query('per_page', 10);
            $nip = Auth::user()->nip;
            $dosen = Auth::user();

            // Default values
            $usulan = collect();
            $jadwal = collect();
            $riwayat = collect();
            $dosenList = collect();
            $riwayatDosenList = collect();

            // Buat token untuk verifikasi halaman
            $token = csrf_token();
            session(['page_token' => $token]);

            // Load data based on active tab
            switch ($activeTab) {
                case 'usulan':
                    $usulan = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->join('jadwal_bimbingans as jb', function ($join) {
                            $join->on('ub.event_id', '=', 'jb.event_id')
                                ->on('ub.nip', '=', 'jb.nip');
                        })
                        ->select(
                            'ub.*',
                            'm.nama as mahasiswa_nama',
                            'jb.lokasi as lokasi_default',
                            DB::raw('(SELECT COUNT(*) FROM usulan_bimbingans 
                    WHERE event_id = ub.event_id 
                    AND status = "DISETUJUI") as total_antrian')
                        )
                        ->where('jb.nip', $nip)
                        // DIHAPUS: ->where('jb.status', 'tersedia')
                        ->where('ub.status', 'USULAN')
                        ->orderBy('jb.waktu_mulai', 'asc')
                        ->orderBy('ub.created_at', 'desc')
                        ->paginate($perPage);
                    break;

                case 'jadwal':
                    $jadwal = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->where('ub.nip', $nip)
                        ->where('status', 'DISETUJUI')
                        ->select(
                            'ub.*',
                            'm.nama as mahasiswa_nama',
                            DB::raw('(SELECT COUNT(*) FROM usulan_bimbingans 
                                    WHERE event_id = ub.event_id 
                                    AND status = "DISETUJUI" 
                                    AND nomor_antrian <= ub.nomor_antrian) as posisi_antrian'),
                            DB::raw('(SELECT COUNT(*) FROM usulan_bimbingans 
                                    WHERE event_id = ub.event_id 
                                    AND status = "DISETUJUI") as total_antrian')
                        )
                        ->orderBy('ub.tanggal', 'desc')
                        ->orderBy('ub.waktu_mulai', 'asc')
                        ->paginate($perPage);
                    break;

                case 'riwayat':
                    $riwayat = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->where('ub.nip', $nip)
                        ->whereIn('ub.status', ['SELESAI', 'DITOLAK', 'DIBATALKAN'])
                        ->select('ub.*', 'm.nama as mahasiswa_nama')
                        ->orderBy('ub.tanggal', 'desc')
                        ->orderBy('ub.waktu_mulai', 'desc')
                        ->paginate($perPage);
                    break;

                case 'pengelola':
                    // Tab baru untuk koordinator prodi
                    if ($dosen->isKoordinatorProdi()) {
                        $prodiId = $dosen->prodi_id;

                        // Daftar dosen dengan total bimbingan hari ini
                        $dosenList = DB::table('dosens')
                            ->leftJoin('usulan_bimbingans', function ($join) {
                                $join->on('dosens.nip', '=', 'usulan_bimbingans.nip')
                                    ->where('usulan_bimbingans.tanggal', '=', date('Y-m-d'))
                                    ->where('usulan_bimbingans.status', '=', 'DISETUJUI');
                            })
                            ->where('dosens.prodi_id', $prodiId)
                            ->select(
                                'dosens.nip',
                                'dosens.nama',
                                'dosens.nama_singkat',
                                DB::raw('COUNT(DISTINCT usulan_bimbingans.id) as total_bimbingan_hari_ini')
                            )
                            ->groupBy('dosens.nip', 'dosens.nama', 'dosens.nama_singkat')
                            ->paginate($perPage);

                        // Riwayat bimbingan semua dosen
                        $riwayatDosenList = DB::table('dosens')
                            ->leftJoin('usulan_bimbingans', 'dosens.nip', '=', 'usulan_bimbingans.nip')
                            ->where('dosens.prodi_id', $prodiId)
                            ->where(function ($query) {
                                $query->whereIn('usulan_bimbingans.status', ['DISETUJUI', 'SELESAI', 'DIBATALKAN', 'DITOLAK'])
                                    ->orWhereNull('usulan_bimbingans.status'); // Untuk menangani dosen tanpa bimbingan
                            })
                            ->select(
                                'dosens.nip',
                                'dosens.nama',
                                'dosens.nama_singkat',
                                DB::raw('COUNT(DISTINCT usulan_bimbingans.id) as total_bimbingan')
                            )
                            ->groupBy('dosens.nip', 'dosens.nama', 'dosens.nama_singkat')
                            ->paginate($perPage);
                    }
                    break;
            }

            return view('bimbingan.dosen.persetujuan', compact(
                'activeTab',
                'usulan',
                'jadwal',
                'riwayat',
                'dosenList',
                'riwayatDosenList',
                'token'
            ));
        } catch (\Exception $e) {
            Log::error('Error in dosen index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    public function getDetailBimbingan($id)
    {
        try {
            $usulan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->join('prodi as p', 'm.prodi_id', '=', 'p.id')
                ->join('konsentrasi as k', 'm.konsentrasi_id', '=', 'k.id')
                ->join('dosens as d', 'ub.nip', '=', 'd.nip')
                ->select(
                    'ub.*',
                    'm.nama as mahasiswa_nama',
                    'p.nama_prodi',
                    'k.nama_konsentrasi',
                    'd.nama as dosen_nama'
                )
                ->where('ub.id', $id)
                ->firstOrFail();

            // Format tanggal ke format Indonesia
            $tanggal = Carbon::parse($usulan->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
            $waktuMulai = Carbon::parse($usulan->waktu_mulai)->format('H.i');
            $waktuSelesai = Carbon::parse($usulan->waktu_selesai)->format('H.i');

            // Set warna badge status
            switch ($usulan->status) {
                case 'DISETUJUI':
                    $statusBadgeClass = 'bg-success';
                    break;
                case 'DITOLAK':
                    $statusBadgeClass = 'bg-danger';
                    break;
                case 'USULAN':
                    $statusBadgeClass = 'bg-warning';
                    break;
                case 'SELESAI':
                    $statusBadgeClass = 'bg-primary';
                    break;
                case 'DIBATALKAN':
                    $statusBadgeClass = 'bg-secondary';
                    break;
                default:
                    $statusBadgeClass = '';
                    break;
            }
            return view('bimbingan.aksiInformasi', compact(
                'usulan',
                'tanggal',
                'waktuMulai',
                'waktuSelesai',
                'statusBadgeClass'
            ));
        } catch (\Exception $e) {
            Log::error('Error di getDetailBimbingan: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data usulan bimbingan');
        }
    }

    public function getRiwayatDetail($id)
    {
        try {
            $riwayat = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.id', $id)
                ->where('ub.status', 'SELESAI')
                ->select('ub.*', 'm.nama as mahasiswa_nama')
                ->firstOrFail();

            $tanggal = Carbon::parse($riwayat->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
            $waktuMulai = Carbon::parse($riwayat->waktu_mulai)->format('H:i');
            $waktuSelesai = Carbon::parse($riwayat->waktu_selesai)->format('H:i');

            return view('bimbingan.riwayatdosen', compact(
                'riwayat',
                'tanggal',
                'waktuMulai',
                'waktuSelesai'
            ));
        } catch (\Exception $e) {
            Log::error('Error getting riwayat detail: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail riwayat bimbingan');
        }
    }

    public function editUsulan($id)
    {
        try {
            $usulan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.id', $id)
                ->where('ub.status', 'DISETUJUI')
                ->select('ub.*', 'm.nama as mahasiswa_nama')
                ->firstOrFail();

            return view('bimbingan.dosen.editusulan', compact('usulan'));
        } catch (\Exception $e) {
            Log::error('Error in editUsulan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data usulan untuk diedit');
        }
    }

    public function updateUsulan(Request $request, $id)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'waktu_mulai' => 'required|date_format:H:i',
                'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
                'lokasi' => 'required|string|max:255'
            ]);

            DB::table('usulan_bimbingans')
                ->where('id', $id)
                ->update([
                    'tanggal' => $request->tanggal,
                    'waktu_mulai' => $request->waktu_mulai,
                    'waktu_selesai' => $request->waktu_selesai,
                    'lokasi' => $request->lokasi,
                    'updated_at' => now()
                ]);

            return redirect()
                ->route('dosen.persetujuanbimbingan', ['tab' => 'usulan'])
                ->with('success', 'Usulan bimbingan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error in updateUsulan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui usulan bimbingan');
        }
    }

    public function terima(Request $request, $id)
    {
        try {
            $usulan = UsulanBimbingan::with('mahasiswa')->findOrFail($id);

            // Cek apakah usulan sudah disetujui sebelumnya
            $existingApproval = UsulanBimbingan::where('nim', $usulan->nim)
                ->where('event_id', $usulan->event_id)
                ->where('status', 'DISETUJUI')
                ->first();

            if ($existingApproval) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usulan bimbingan ini sudah disetujui sebelumnya'
                ], 400);
            }

            // Temukan jadwal tanpa perlu filter apapun
            $jadwal = JadwalBimbingan::where('event_id', $usulan->event_id)->first();

            if (!$jadwal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal bimbingan tidak ditemukan'
                ], 404);
            }

            // Cek apakah waktu jadwal sudah lewat
            $now = Carbon::now('Asia/Jakarta');
            $jadwalMulai = Carbon::parse($jadwal->waktu_mulai)->setTimezone('Asia/Jakarta');

            // Jika waktu mulai jadwal sudah lewat, tolak permintaan
            if ($jadwalMulai->isPast()) {
                Log::info('Menolak persetujuan usulan karena waktu sudah lewat', [
                    'usulan_id' => $id,
                    'jadwal_id' => $jadwal->id,
                    'waktu_jadwal' => $jadwalMulai->toDateTimeString(),
                    'waktu_sekarang' => $now->toDateTimeString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal bimbingan tidak dapat disetujui karena waktu sudah lewat'
                ], 400);
            }

            // Log untuk debugging
            Log::info('Menerima usulan bimbingan:', [
                'usulan_id' => $id,
                'jadwal_id' => $jadwal->id,
                'tanggal' => $usulan->tanggal,
                'waktu' => $usulan->waktu_mulai . ' - ' . $usulan->waktu_selesai,
                'mahasiswa' => $usulan->nim
            ]);

            DB::beginTransaction();

            if ($usulan->setujui($request->lokasi)) {
                try {
                    // BAGIAN 1: Kode yang sudah ada - Menambahkan mahasiswa sebagai attendee
                    // Debug log untuk memeriksa event_id
                    Log::info('Mencari event dengan ID: ' . $usulan->event_id);

                    // Cari event di calendar dosen
                    $requestObj = new \Illuminate\Http\Request();
                    $events = $this->googleCalendarController->getEvents($requestObj);

                    // Debug log untuk melihat response events
                    Log::info('Events response:', ['events' => $events]);

                    if (!$events || !isset($events->original)) {
                        throw new \Exception('Tidak bisa mengambil events dari Google Calendar');
                    }

                    $event = collect($events->original)->first(function ($event) use ($usulan) {
                        return isset($event['id']) && $event['id'] === $usulan->event_id;
                    });

                    if ($event) {
                        $existingAttendees = $event['attendees'] ?? [];
                        Log::info('Existing attendees:', ['attendees' => $existingAttendees]);

                        $mahasiswaEmail = $usulan->mahasiswa->email;
                        $emailExists = collect($existingAttendees)->contains('email', $mahasiswaEmail);

                        if (!$emailExists) {
                            Log::info('Menambahkan attendee baru:', ['email' => $mahasiswaEmail]);

                            $existingAttendees[] = [
                                'email' => $mahasiswaEmail,
                                'responseStatus' => 'needsAction'
                            ];

                            $description =
                                "Dosen: {$usulan->dosen->nama}\n" .
                                "Mahasiswa: {$usulan->mahasiswa->nama}\n" .
                                "NIM: {$usulan->nim}\n" .
                                "Nomor Antrian: {$usulan->nomor_antrian}\n" .
                                "Lokasi: {$request->lokasi}\n";

                            $this->googleCalendarController->updateEventAttendees(
                                $usulan->event_id,
                                $existingAttendees,
                                [
                                    'description' => $description,
                                    'sendUpdates' => 'all',
                                    'reminders' => [
                                        'useDefault' => false,
                                        'overrides' => [
                                            ['method' => 'email', 'minutes' => 24 * 60],
                                            ['method' => 'popup', 'minutes' => 30]
                                        ]
                                    ]
                                ]
                            );

                            Log::info('Berhasil menambahkan attendee dengan notifikasi');
                        }

                        // BAGIAN 2 YANG DIREVISI UNTUK MENGATASI EVENT DUPLIKAT
                        // Ini adalah bagian utama yang perlu diperbaiki
                        $mahasiswa = $usulan->mahasiswa;

                        if ($mahasiswa && $mahasiswa->hasGoogleCalendarConnected()) {
                            try {
                                // Inisialisasi GoogleCalendarController dengan token mahasiswa
                                $googleCalendarTmp = new GoogleCalendarController();

                                if ($googleCalendarTmp->initWithUserToken($mahasiswa)) {
                                    // Cek apakah event sudah ada di calendar mahasiswa
                                    // TAMBAHAN: Gunakan tanggal yang lebih spesifik
                                    $tanggalSaja = Carbon::parse($usulan->tanggal)->format('Y-m-d');
                                    $waktuMulai = Carbon::parse($tanggalSaja . ' ' . $usulan->waktu_mulai);
                                    $waktuSelesai = Carbon::parse($tanggalSaja . ' ' . $usulan->waktu_selesai);

                                    // ⚠️ PERBAIKAN UTAMA: Cek event yang sudah ada di kalender mahasiswa dengan metode findEventByTimeAndKeyword
                                    // Metode ini akan mencari event dengan waktu dan keyword yang sama
                                    $eventExists = $googleCalendarTmp->findEventByTimeAndKeyword(
                                        $tanggalSaja,
                                        $usulan->waktu_mulai,
                                        $usulan->waktu_selesai,
                                        'Bimbingan ' . ucfirst($usulan->jenis_bimbingan)
                                    );

                                    if (!$eventExists) {
                                        // Jika tidak ditemukan event dengan metode di atas, cek secara manual
                                        // Buat request untuk mencari event pada hari yang sama
                                        $mahasiswaRequestObj = new \Illuminate\Http\Request([
                                            'start' => $tanggalSaja,
                                            'end' => $tanggalSaja,
                                            'filter_duplicates' => false
                                        ]);

                                        $mahasiswaEvents = $googleCalendarTmp->getEvents($mahasiswaRequestObj);

                                        if ($mahasiswaEvents && isset($mahasiswaEvents->original) && is_array($mahasiswaEvents->original)) {
                                            $waktuMulaiStr = $waktuMulai->format('H:i');
                                            $waktuSelesaiStr = $waktuSelesai->format('H:i');
                                            $searchKeywords = [
                                                'Bimbingan ' . ucfirst($usulan->jenis_bimbingan),
                                                $usulan->dosen->nama,
                                                $usulan->nim
                                            ];

                                            // Pencarian lebih spesifik untuk event yang sudah ada
                                            foreach ($mahasiswaEvents->original as $existingEvent) {
                                                if (!isset($existingEvent['start']) || !isset($existingEvent['end'])) {
                                                    continue;
                                                }

                                                $existingStart = Carbon::parse($existingEvent['start'])->format('H:i');
                                                $existingEnd = Carbon::parse($existingEvent['end'])->format('H:i');
                                                $existingTitle = $existingEvent['title'] ?? '';
                                                $existingDesc = $existingEvent['extendedProps']['description'] ?? '';

                                                // Jika waktu sama atau hampir sama (toleransi 5 menit)
                                                $startDiff = abs(Carbon::parse($existingStart)->diffInMinutes(Carbon::parse($waktuMulaiStr)));
                                                $endDiff = abs(Carbon::parse($existingEnd)->diffInMinutes(Carbon::parse($waktuSelesaiStr)));

                                                if ($startDiff <= 5 && $endDiff <= 5) {
                                                    // Periksa apakah judul atau deskripsi mengandung kata kunci
                                                    foreach ($searchKeywords as $keyword) {
                                                        if (
                                                            stripos($existingTitle, $keyword) !== false ||
                                                            stripos($existingDesc, $keyword) !== false
                                                        ) {

                                                            Log::info('Event yang mirip ditemukan di calendar mahasiswa:', [
                                                                'event_id' => $existingEvent['id'] ?? 'unknown',
                                                                'title' => $existingTitle,
                                                                'waktu' => "$existingStart - $existingEnd",
                                                                'keyword_match' => $keyword
                                                            ]);

                                                            $eventExists = true;
                                                            break 2; // Keluar dari kedua loop
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    // Buat event baru hanya jika tidak ditemukan event yang mirip
                                    if (!$eventExists) {
                                        Log::info('Membuat event baru di calendar mahasiswa');

                                        $eventData = [
                                            'summary' => 'Bimbingan ' . ucfirst($usulan->jenis_bimbingan) . ' dengan ' . $usulan->dosen->nama,
                                            'description' =>
                                            "NIM: {$usulan->nim}\n" .
                                                "Dosen: {$usulan->dosen->nama}\n" .
                                                "Jenis: " . ucfirst($usulan->jenis_bimbingan) . "\n" .
                                                "Lokasi: {$request->lokasi}\n" .
                                                ($usulan->deskripsi ? "Deskripsi: {$usulan->deskripsi}" : ""),
                                            'location' => $request->lokasi,
                                            'start' => $waktuMulai,
                                            'end' => $waktuSelesai,
                                            'reminders' => [
                                                'useDefault' => false,
                                                'overrides' => [
                                                    ['method' => 'email', 'minutes' => 24 * 60], // 1 hari sebelumnya
                                                    ['method' => 'popup', 'minutes' => 30], // 30 menit sebelumnya
                                                    ['method' => 'popup', 'minutes' => 5]  // 5 menit sebelumnya
                                                ]
                                            ]
                                        ];

                                        // Buat event di Google Calendar mahasiswa
                                        $createdEvent = $googleCalendarTmp->createEvent($eventData);

                                        Log::info('Berhasil membuat event di calendar mahasiswa:', [
                                            'event_id' => $createdEvent->id ?? 'unknown',
                                            'mahasiswa' => $mahasiswa->nim,
                                            'dosen' => $usulan->nip
                                        ]);
                                    } else {
                                        Log::info('Melewatkan pembuatan event baru karena event serupa sudah ada');
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::error('Error saat membuat event di calendar mahasiswa:', [
                                    'message' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                                // Lanjutkan proses meskipun gagal membuat event
                            }
                        }

                        DB::commit();
                        return response()->json([
                            'success' => true,
                            'message' => 'Usulan bimbingan berhasil disetujui dan undangan telah dikirim'
                        ]);
                    }

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Usulan bimbingan berhasil disetujui (tanpa notifikasi calendar)'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Google Calendar Error Detail:', [
                        'message' => $e->getMessage(),
                        'event_id' => $usulan->event_id,
                        'trace' => $e->getTraceAsString()
                    ]);

                    DB::commit(); // Tetap commit meskipun ada error Google Calendar
                    return response()->json([
                        'success' => true,
                        'message' => 'Usulan bimbingan berhasil disetujui (tanpa notifikasi calendar)'
                    ]);
                }
            }

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui usulan bimbingan'
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in approve consultation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses usulan'
            ], 500);
        }
    }

    public function tolak(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $usulan = UsulanBimbingan::findOrFail($id);

            // Update status usulan menjadi DITOLAK
            $usulan->update([
                'status' => 'DITOLAK',
                'keterangan' => $request->keterangan
            ]);

            // TAMBAHAN: Update jadwal bimbingan untuk mengembalikan kuota
            $jadwal = JadwalBimbingan::where('event_id', $usulan->event_id)->first();

            if ($jadwal) {
                // Hitung ulang jumlah pendaftar dari usulan yang aktif
                $pendaftarCount = DB::table('usulan_bimbingans')
                    ->where('event_id', $jadwal->event_id)
                    ->whereIn('status', ['USULAN', 'DISETUJUI'])
                    ->count();

                // Tentukan status yang tepat berdasarkan kondisi
                if (Carbon::parse($jadwal->waktu_selesai)->isPast()) {
                    $newStatus = JadwalBimbingan::STATUS_SELESAI;
                } else if ($jadwal->has_kuota_limit && $pendaftarCount >= $jadwal->kapasitas) {
                    $newStatus = JadwalBimbingan::STATUS_PENUH;
                } else {
                    $newStatus = JadwalBimbingan::STATUS_TERSEDIA;
                }

                // Hitung sisa kapasitas
                $sisaKapasitas = $jadwal->has_kuota_limit ?
                    max(0, $jadwal->kapasitas - $pendaftarCount) : 0;

                // Update jadwal menggunakan model Eloquent
                $jadwal->jumlah_pendaftar = $pendaftarCount;
                $jadwal->sisa_kapasitas = $sisaKapasitas;
                $jadwal->status = $newStatus;
                $jadwal->updated_at = now();
                $jadwal->save();

                Log::info('Status jadwal diperbarui setelah penolakan:', [
                    'event_id' => $jadwal->event_id,
                    'jumlah_pendaftar_baru' => $pendaftarCount,
                    'sisa_kapasitas' => $sisaKapasitas,
                    'status_baru' => $newStatus
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usulan bimbingan berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menolak usulan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses usulan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function selesaikan($id)
    {
        Log::info('Fungsi selesaikan dipanggil dengan ID: ' . $id);
        try {
            $usulan = UsulanBimbingan::findOrFail($id);

            if ($usulan->status !== 'DISETUJUI') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya bimbingan yang disetujui yang dapat diselesaikan'
                ], 422);
            }

            $usulan->update([
                'status' => 'SELESAI'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bimbingan berhasil diselesaikan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in selesaikan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan bimbingan'
            ], 500);
        }
    }

    public function dosenDetail(Request $request, $nip)
    {
        try {
            $dosen = Dosen::where('nip', $nip)->firstOrFail();
            $perPage = $request->input('per_page', 10);

            // Ambil daftar bimbingan hari ini
            $bimbingan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.nip', $nip)
                ->where('ub.tanggal', date('Y-m-d'))
                ->where('ub.status', 'DISETUJUI')
                ->select(
                    'ub.*',
                    'm.nama as mahasiswa_nama'
                )
                ->orderBy('ub.waktu_mulai')
                ->paginate($perPage);

            return view('bimbingan.dosen.detaildaftar', compact('dosen', 'bimbingan'));
        } catch (\Exception $e) {
            Log::error('Error in dosenDetail: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat detail dosen');
        }
    }

    public function riwayatDosenDetail(Request $request, $nip)
    {
        try {
            $dosen = Dosen::where('nip', $nip)->firstOrFail();
            $perPage = $request->input('per_page', 10);

            // Ambil semua riwayat bimbingan
            $bimbingan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.nip', $nip)
                ->whereIn('ub.status', ['SELESAI', 'DISETUJUI', 'DIBATALKAN', 'DITOLAK'])
                ->select(
                    'ub.*',
                    'm.nama as mahasiswa_nama'
                )
                ->orderBy('ub.tanggal', 'desc')
                ->orderBy('ub.waktu_mulai', 'desc')
                ->paginate($perPage);

            return view('bimbingan.dosen.riwayatdetail', compact('dosen', 'bimbingan'));
        } catch (\Exception $e) {
            Log::error('Error in riwayatDosenDetail: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat riwayat detail dosen');
        }
    }

    public function getRelatedSchedules($id)
    {
        try {
            DB::enableQueryLog();
            // Get the schedule to be canceled
            Log::info('getRelatedSchedules dipanggil dengan ID: ' . $id);
            $usulan = UsulanBimbingan::findOrFail($id);
            Log::info('Query parameters:', [
                'nip' => $usulan->nip,
                'tanggal' => $usulan->tanggal,
                'waktu_mulai' => $usulan->waktu_mulai,
                'waktu_selesai' => $usulan->waktu_selesai
            ]);


            // Query related schedules with proper scoping of conditions
            $relatedSchedules = DB::table('usulan_bimbingans as ub')
                ->leftJoin('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.id', '!=', $id)
                ->where('ub.nip', $usulan->nip)
                ->where('ub.tanggal', $usulan->tanggal)
                ->where('ub.status', 'DISETUJUI')
                ->where(function ($query) use ($usulan) {
                    // Either exact same time or overlapping time
                    $query->where(function ($q) use ($usulan) {
                        // Exact same time
                        $q->where('ub.waktu_mulai', $usulan->waktu_mulai)
                            ->where('ub.waktu_selesai', $usulan->waktu_selesai);
                    })
                        ->orWhere(function ($q) use ($usulan) {
                            // Start time overlaps
                            $q->where('ub.waktu_mulai', '>=', $usulan->waktu_mulai)
                                ->where('ub.waktu_mulai', '<', $usulan->waktu_selesai);
                        })
                        ->orWhere(function ($q) use ($usulan) {
                            // End time overlaps
                            $q->where('ub.waktu_selesai', '>', $usulan->waktu_mulai)
                                ->where('ub.waktu_selesai', '<=', $usulan->waktu_selesai);
                        })
                        ->orWhere(function ($q) use ($usulan) {
                            // Session completely encompasses current session
                            $q->where('ub.waktu_mulai', '<=', $usulan->waktu_mulai)
                                ->where('ub.waktu_selesai', '>=', $usulan->waktu_selesai);
                        });
                })
                ->select(
                    'ub.id',
                    'ub.nim',
                    'm.nama as mahasiswa_nama',
                    'ub.jenis_bimbingan',
                    'ub.waktu_mulai',
                    'ub.waktu_selesai'
                )
                ->get();


            Log::info('Related schedules count: ' . $relatedSchedules->count());
            // You can also log the actual SQL query for debugging
            $query = DB::getQueryLog();
            Log::info('Last executed query:', end($query) ?: ['No query logged']);


            return response()->json([
                'success' => true,
                'schedules' => $relatedSchedules
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mendapatkan jadwal terkait: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan jadwal terkait: ' . $e->getMessage()
            ], 500);
        }
    }

    public function batalkanPersetujuan($id, Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'alasan' => 'required|string',
                'related_schedules' => 'nullable|array',
                'related_schedules.*' => 'integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            DB::beginTransaction();

            // Cari data bimbingan utama
            $bimbingan = UsulanBimbingan::findOrFail($id);

            // Pastikan status saat ini adalah DISETUJUI
            if ($bimbingan->status !== 'DISETUJUI') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya bimbingan yang telah disetujui yang dapat dibatalkan'
                ], 400);
            }

            // Update status dan tambahkan alasan untuk bimbingan utama
            $bimbingan->status = 'DIBATALKAN';
            $bimbingan->keterangan = $request->alasan;
            $bimbingan->updated_at = now();
            $bimbingan->save();

            // Hitung jumlah total pembatalan (mulai dari 1 untuk bimbingan utama)
            $totalBatalkan = 1;

            // Jika ada jadwal terkait yang dipilih, batalkan juga
            if ($request->filled('related_schedules') && count($request->related_schedules) > 0) {
                $relatedIds = $request->related_schedules;

                // Batch update untuk jadwal terkait
                $updated = UsulanBimbingan::whereIn('id', $relatedIds)
                    ->where('nip', $bimbingan->nip) // Pastikan hanya jadwal dosen yang sama
                    ->where('status', 'DISETUJUI') // Pastikan hanya yang statusnya DISETUJUI
                    ->update([
                        'status' => 'DIBATALKAN',
                        'keterangan' => $request->alasan,
                        'updated_at' => now()
                    ]);

                $totalBatalkan += $updated;

                // Log pembatalan massal
                Log::info('Pembatalan bimbingan massal:', [
                    'id_utama' => $id,
                    'jadwal_terkait' => $relatedIds,
                    'dosen' => Auth::user()->nip,
                    'alasan' => $request->alasan,
                    'total_dibatalkan' => $totalBatalkan
                ]);
            } else {
                // Log pembatalan tunggal
                Log::info('Persetujuan bimbingan dibatalkan:', [
                    'id' => $id,
                    'dosen' => Auth::user()->nip,
                    'alasan' => $request->alasan
                ]);
            }

            // BAGIAN INI YANG DIPERBAIKI - Update jadwal terkait
            $jadwal = JadwalBimbingan::where('event_id', $bimbingan->event_id)->first();
            if ($jadwal) {
                // Hitung ulang jumlah pendaftar dari usulan yang aktif
                $pendaftarCount = DB::table('usulan_bimbingans')
                    ->where('event_id', $jadwal->event_id)
                    ->whereIn('status', ['USULAN', 'DISETUJUI'])
                    ->count();

                // Tentukan status yang tepat berdasarkan kondisi
                if (Carbon::parse($jadwal->waktu_selesai)->isPast()) {
                    $newStatus = JadwalBimbingan::STATUS_SELESAI;
                } else if ($jadwal->has_kuota_limit && $pendaftarCount >= $jadwal->kapasitas) {
                    $newStatus = JadwalBimbingan::STATUS_PENUH;
                } else {
                    $newStatus = JadwalBimbingan::STATUS_TERSEDIA;
                }

                // Hitung sisa kapasitas
                $sisaKapasitas = $jadwal->has_kuota_limit ?
                    max(0, $jadwal->kapasitas - $pendaftarCount) : 0;

                // Update jadwal menggunakan model Eloquent
                $jadwal->jumlah_pendaftar = $pendaftarCount;
                $jadwal->sisa_kapasitas = $sisaKapasitas;
                $jadwal->status = $newStatus;
                $jadwal->updated_at = now();
                $jadwal->save();

                Log::info('Status jadwal diperbarui setelah pembatalan:', [
                    'event_id' => $jadwal->event_id,
                    'jumlah_pendaftar_baru' => $pendaftarCount,
                    'sisa_kapasitas' => $sisaKapasitas,
                    'status_baru' => $newStatus
                ]);

                // PERBAIKAN BARU: Cari event bimbingan milik mahasiswa dan hapus
                try {
                    // 1. Dapatkan data mahasiswa
                    $mahasiswa = $bimbingan->mahasiswa;

                    if ($mahasiswa && $mahasiswa->hasGoogleCalendarConnected()) {
                        $googleCalendarTmp = new GoogleCalendarController();

                        if ($googleCalendarTmp->initWithUserToken($mahasiswa)) {
                            // 2. Cari event terkait di calendar mahasiswa
                            $tanggalSaja = Carbon::parse($bimbingan->tanggal)->format('Y-m-d');
                            $waktuMulai = $bimbingan->waktu_mulai;
                            $waktuSelesai = $bimbingan->waktu_selesai;
                            $keyword = 'Bimbingan ' . ucfirst($bimbingan->jenis_bimbingan);

                            // Buat request untuk mencari event pada hari yang sama
                            $mahasiswaRequestObj = new \Illuminate\Http\Request([
                                'start' => $tanggalSaja,
                                'end' => $tanggalSaja,
                                'filter_duplicates' => false
                            ]);

                            $mahasiswaEvents = $googleCalendarTmp->getEvents($mahasiswaRequestObj);

                            if ($mahasiswaEvents && isset($mahasiswaEvents->original) && is_array($mahasiswaEvents->original)) {
                                foreach ($mahasiswaEvents->original as $existingEvent) {
                                    if (!isset($existingEvent['start']) || !isset($existingEvent['end'])) {
                                        continue;
                                    }

                                    $existingStart = Carbon::parse($existingEvent['start'])->format('H:i');
                                    $existingEnd = Carbon::parse($existingEvent['end'])->format('H:i');
                                    $existingTitle = $existingEvent['title'] ?? '';
                                    $existingDesc = $existingEvent['extendedProps']['description'] ?? '';

                                    // Jika waktu dan judul cocok, ini kemungkinan event yang kita cari
                                    $startDiff = abs(Carbon::parse($existingStart)->diffInMinutes(Carbon::parse($waktuMulai)));
                                    $endDiff = abs(Carbon::parse($existingEnd)->diffInMinutes(Carbon::parse($waktuSelesai)));

                                    $matchesTitle = stripos($existingTitle, $keyword) !== false;
                                    $matchesDesc = stripos($existingDesc, $bimbingan->dosen->nama) !== false;

                                    if (($startDiff <= 10 && $endDiff <= 10) && ($matchesTitle || $matchesDesc)) {
                                        // Hapus event dari kalender mahasiswa
                                        $eventId = $existingEvent['id'] ?? null;
                                        if ($eventId) {
                                            try {
                                                $googleCalendarTmp->deleteEvent($eventId);
                                                Log::info('Berhasil menghapus event di calendar mahasiswa:', [
                                                    'event_id' => $eventId,
                                                    'title' => $existingTitle,
                                                    'mahasiswa' => $mahasiswa->nim
                                                ]);
                                            } catch (\Exception $deleteEx) {
                                                Log::error('Gagal menghapus event di calendar mahasiswa:', [
                                                    'event_id' => $eventId,
                                                    'mahasiswa' => $mahasiswa->nim,
                                                    'error' => $deleteEx->getMessage()
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // 3. Update Google Calendar dosen (jadwal utama)
                    $dosen = Auth::user();
                    if ($dosen->hasGoogleCalendarConnected()) {
                        // Pertama, update deskripsi event untuk menunjukkan status dibatalkan
                        if (method_exists($this->googleCalendarController, 'updateEventDescription')) {
                            $description = "Dosen: {$dosen->nama}\n" .
                                "NIP: {$dosen->nip}\n" .
                                "Email: {$dosen->email}\n";

                            // Tambahkan informasi kuota
                            if ($jadwal->has_kuota_limit) {
                                $description .= "Kuota: Terbatas ({$pendaftarCount}/{$jadwal->kapasitas} mahasiswa)\n";
                            } else {
                                $description .= "Kuota: Tidak terbatas\n";
                            }

                            // Tambahkan informasi jenis bimbingan
                            if ($jadwal->jenis_bimbingan) {
                                $jenisBimbinganText = match ($jadwal->jenis_bimbingan) {
                                    'skripsi' => 'Bimbingan Skripsi',
                                    'kp' => 'Bimbingan KP',
                                    'akademik' => 'Bimbingan Akademik',
                                    'konsultasi' => 'Konsultasi Pribadi',
                                    'mbkm' => 'Bimbingan MBKM',
                                    'lainnya' => 'Lainnya',
                                    default => 'Bimbingan'
                                };

                                $description .= "Jenis: {$jenisBimbinganText}\n";
                            }

                            // Tambahkan lokasi jika ada
                            if ($jadwal->lokasi) {
                                $description .= "Lokasi: {$jadwal->lokasi}\n";
                            }

                            // Status dibatalkan dengan alasan
                            $description .= "Status: DIBATALKAN\n";
                            $description .= "Alasan: {$request->alasan}\n";

                            // Tambahkan catatan jika ada
                            if ($jadwal->catatan) {
                                $description .= "Catatan: {$jadwal->catatan}";
                            }

                            // Update deskripsi event di Google Calendar
                            if ($this->googleCalendarController->validateAndRefreshToken()) {
                                $client = $this->googleCalendarController->getClient();
                                $service = new \Google_Service_Calendar($client);

                                try {
                                    $event = $service->events->get('primary', $jadwal->event_id);

                                    // Tambahkan "DIBATALKAN" di judul event
                                    $oldTitle = $event->getSummary();
                                    $event->setSummary('[DIBATALKAN] ' . $oldTitle);

                                    // Update deskripsi
                                    $event->setDescription($description);

                                    // Ubah warna event (optional, jika memungkinkan)
                                    $event->setColorId('8'); // 8 typically represents gray/canceled in Google Calendar

                                    // Update event
                                    $service->events->update('primary', $jadwal->event_id, $event);

                                    Log::info('Google Calendar event diperbarui dengan status dibatalkan', [
                                        'event_id' => $jadwal->event_id
                                    ]);
                                } catch (\Exception $e) {
                                    Log::warning('Error updating Google Calendar event: ' . $e->getMessage());
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Tangani error Google Calendar tapi jangan ganggu proses utama
                    Log::warning('Google Calendar error: ' . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $totalBatalkan > 1
                    ? "Berhasil membatalkan $totalBatalkan jadwal bimbingan"
                    : "Persetujuan bimbingan berhasil dibatalkan"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat membatalkan persetujuan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method untuk memperbarui deskripsi event di Google Calendar
     */
    private function updateEventDescription($googleCalendarController, $eventId, $description)
    {
        try {
            $client = $googleCalendarController->getClient();
            $service = new \Google_Service_Calendar($client);

            // Dapatkan event yang ada
            $event = $service->events->get('primary', $eventId);

            // Update deskripsi
            $event->setDescription($description);

            // Simpan perubahan
            return $service->events->update('primary', $eventId, $event);
        } catch (\Exception $e) {
            Log::error('Error updating Google Calendar event description: ' . $e->getMessage());
            throw $e;
        }
    }
}
