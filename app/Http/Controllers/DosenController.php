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
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventReminder;
use Google_Service_Calendar_EventReminders;
use Google_Service_Calendar_EventAttendee;


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
            $perPage = intval($request->query('per_page', 50));
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

    /**
     * Menyetujui usulan bimbingan dan mengirim undangan ke Google Calendar
     */
    public function terima(Request $request, $id)
    {
        try {
            Log::info('Memulai terima usulan dengan ID: ' . $id, [
                'request' => $request->all()
            ]);

            $usulan = UsulanBimbingan::with(['mahasiswa', 'dosen'])->findOrFail($id);

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

            // Temukan jadwal
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

            if ($jadwalMulai->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal bimbingan tidak dapat disetujui karena waktu sudah lewat'
                ], 400);
            }

            DB::beginTransaction();

            // Update status di database
            if ($usulan->setujui($request->lokasi)) {
                try {
                    // --- LANGKAH 1: UPDATE EVENT DOSEN DAN TAMBAHKAN MAHASISWA SEBAGAI ATTENDEE ---

                    // Validasi koneksi Google Calendar
                    if (!$this->googleCalendarController->validateAndRefreshToken()) {
                        throw new \Exception('Tidak dapat terhubung ke Google Calendar. Silahkan hubungkan ulang.');
                    }

                    // Dapatkan ID event di Google Calendar dosen
                    $dosenEventId = $usulan->event_id;

                    // Log untuk debug
                    Log::info('Mendapatkan event dari Google Calendar dengan ID: ' . $dosenEventId);

                    // Inisialisasi service Google Calendar untuk dosen
                    $service = new \Google_Service_Calendar($this->googleCalendarController->getClient());

                    try {
                        // Dapatkan event dosen langsung dengan Google API
                        $eventDosen = $service->events->get('primary', $dosenEventId);

                        // Tambahkan mahasiswa sebagai attendee
                        $attendees = $eventDosen->getAttendees() ?: [];

                        // Validasi email mahasiswa
                        $mahasiswaEmail = $usulan->mahasiswa->email;
                        if (!filter_var($mahasiswaEmail, FILTER_VALIDATE_EMAIL)) {
                            Log::warning('Email mahasiswa tidak valid: ' . $mahasiswaEmail);
                            throw new \Exception('Email mahasiswa tidak valid');
                        }

                        // Cek apakah mahasiswa sudah ada di daftar attendee
                        $emailExists = false;
                        foreach ($attendees as $attendee) {
                            if ($attendee->getEmail() === $mahasiswaEmail) {
                                $emailExists = true;
                                break;
                            }
                        }

                        if (!$emailExists) {
                            // Tambahkan mahasiswa sebagai attendee
                            $attendee = new \Google_Service_Calendar_EventAttendee();
                            $attendee->setEmail($mahasiswaEmail);
                            $attendee->setDisplayName($usulan->mahasiswa->nama); // Tambahkan nama mahasiswa untuk tampilan lebih baik
                            $attendee->setResponseStatus('needsAction'); // Status pending agar ada notifikasi
                            $attendees[] = $attendee;

                            // Update daftar attendee
                            $eventDosen->setAttendees($attendees);

                            // Buat deskripsi yang informatif dan dinamis
                            $existingDescription = $eventDosen->getDescription() ?: '';

                            // Hanya tambahkan info mahasiswa baru jika belum ada
                            if (strpos($existingDescription, "NIM: {$usulan->nim}") === false) {
                                // Format deskripsi yang lebih terstruktur
                                $attendeeInfo = "\n• Mahasiswa: {$usulan->mahasiswa->nama} (NIM: {$usulan->nim})" .
                                    "\n  Jenis: " . ucfirst($usulan->jenis_bimbingan) .
                                    "\n  Lokasi: {$request->lokasi}" .
                                    "\n  Nomor Antrian: {$usulan->nomor_antrian}\n";

                                // Update deskripsi dengan menambahkan info mahasiswa
                                $newDescription = $existingDescription;

                                // Tambahkan header "Daftar Mahasiswa" jika belum ada
                                if (strpos($existingDescription, "DAFTAR MAHASISWA BIMBINGAN:") === false) {
                                    $newDescription .= "\n\n==== DAFTAR MAHASISWA BIMBINGAN: ====";
                                }

                                $newDescription .= $attendeeInfo;
                                $eventDosen->setDescription($newDescription);
                            }

                            // Tambahkan logging untuk melacak parameter sendUpdates
                            Log::info('Updating dosen event with sendUpdates=all parameter', [
                                'event_id' => $eventDosen->getId(),
                                'attendees_count' => count($attendees)
                            ]);

                            // PERBAIKAN 1: Pastikan event memiliki reminder yang tepat
                            $reminders = new \Google_Service_Calendar_EventReminders();
                            $reminders->setUseDefault(false);

                            $reminderItems = [];

                            // Email 1 hari sebelumnya
                            $emailReminder = new \Google_Service_Calendar_EventReminder();
                            $emailReminder->setMethod('email');
                            $emailReminder->setMinutes(24 * 60); // 1440 menit = 1 hari
                            $reminderItems[] = $emailReminder;

                            // Popup 30 menit sebelumnya
                            $popupReminder1 = new \Google_Service_Calendar_EventReminder();
                            $popupReminder1->setMethod('popup');
                            $popupReminder1->setMinutes(30);
                            $reminderItems[] = $popupReminder1;

                            // Tambahkan reminder ke event
                            $reminders->setOverrides($reminderItems);
                            $eventDosen->setReminders($reminders);

                            // Update event dengan parameter sendUpdates=all untuk trigger email notification
                            $updatedEvent = $service->events->update('primary', $dosenEventId, $eventDosen, [
                                'sendUpdates' => 'all', // PENTING: Parameter ini mengaktifkan notifikasi email
                                'conferenceDataVersion' => 0,
                                'supportsAttachments' => true
                            ]);

                            // PERBAIKAN 2: Double-ensure notifikasi terkirim dengan method lain
                            try {
                                // Gunakan method updateEventAttendees sebagai cadangan
                                $notificationResult = $this->googleCalendarController->updateEventAttendees(
                                    $dosenEventId,
                                    $attendees,
                                    [
                                        'description' => $newDescription ?? $existingDescription,
                                        'sendUpdates' => 'all', // Penting untuk notifikasi
                                        'reminders' => [
                                            'useDefault' => false,
                                            'overrides' => [
                                                ['method' => 'email', 'minutes' => 24 * 60],
                                                ['method' => 'popup', 'minutes' => 30]
                                            ]
                                        ]
                                    ]
                                );

                                Log::info('Berhasil mengirim notifikasi dengan updateEventAttendees', [
                                    'event_id' => $dosenEventId
                                ]);
                            } catch (\Exception $e) {
                                Log::warning('Error menggunakan updateEventAttendees: ' . $e->getMessage());
                                // Tetap lanjutkan proses meskipun ada error di sini
                            }

                            Log::info('Event dosen berhasil diupdate dengan attendee baru', [
                                'event_id' => $updatedEvent->getId(),
                                'attendees' => array_map(function ($a) {
                                    return $a->getEmail();
                                }, $updatedEvent->getAttendees())
                            ]);
                        } else {
                            Log::info('Mahasiswa sudah terdaftar sebagai attendee');
                        }
                    } catch (\Exception $e) {
                        Log::error('Gagal mendapatkan atau mengupdate event dosen: ' . $e->getMessage(), [
                            'event_id' => $dosenEventId,
                            'exception' => $e
                        ]);
                        throw $e;
                    }

                    // --- LANGKAH 2: SIMPAN EVENT ID DI DATABASE ---
                    // Update usulan dengan ID event yang sama dengan jadwal (bukan format gabungan)
                    $usulan->event_id = $dosenEventId;
                    $usulan->save();

                    // --- LANGKAH 3: MENGELOLA AKSES MAHASISWA KE EVENT ---
                    $mahasiswa = $usulan->mahasiswa;

                    // Cek apakah mahasiswa terhubung dengan Google Calendar
                    if ($mahasiswa && $mahasiswa->hasGoogleCalendarConnected()) {
                        try {
                            // Inisialisasi controller baru dengan akses token mahasiswa
                            $googleCalendarMhs = new GoogleCalendarController();

                            if ($googleCalendarMhs->initWithUserToken($mahasiswa)) {
                                // TINGKAT AKSES KALENDER: Tambahkan event ke kalender mahasiswa sebagai MIRROR
                                // Ini akan memastikan mahasiswa dapat melihat event tanpa duplikasi

                                // Inisialisasi service
                                $serviceMhs = new \Google_Service_Calendar($googleCalendarMhs->getClient());

                                // Ambil event dosen sebagai rujukan
                                $eventReference = $service->events->get('primary', $dosenEventId);

                                // Cek apakah event sudah ada di kalender mahasiswa dengan menggunakan iCalUID
                                $optParams = [
                                    'maxResults' => 10,
                                    'orderBy' => 'startTime',
                                    'singleEvents' => true,
                                    'iCalUID' => $eventReference->getICalUID()
                                ];

                                $eventsResult = $serviceMhs->events->listEvents('primary', $optParams);
                                $existingEvents = $eventsResult->getItems();

                                // Jika event sudah ada, cukup update status
                                if (!empty($existingEvents)) {
                                    $existingEvent = $existingEvents[0];

                                    // Update deskripsi dengan informasi terbaru
                                    $mhsDescription =
                                        "NIM: {$usulan->nim}\n" .
                                        "Dosen: {$usulan->dosen->nama}\n" .
                                        "Jenis: " . ucfirst($usulan->jenis_bimbingan) . "\n" .
                                        "Lokasi: {$request->lokasi}\n" .
                                        "Status: TERKONFIRMASI\n" .
                                        "Waktu Konfirmasi: " . now()->format('d-m-Y H:i') . "\n" .
                                        "Nomor Antrian: {$usulan->nomor_antrian}\n" .
                                        ($usulan->deskripsi ? "Catatan: {$usulan->deskripsi}" : "");

                                    $existingEvent->setDescription($mhsDescription);
                                    $existingEvent->setColorId('9'); // Hijau = confirmed

                                    // Update lokasi jika berubah
                                    if ($request->lokasi) {
                                        $existingEvent->setLocation($request->lokasi);
                                    }

                                    // PERBAIKAN 3: Update event dengan sendUpdates=all untuk mengirim notifikasi
                                    $serviceMhs->events->update('primary', $existingEvent->getId(), $existingEvent, [
                                        'sendUpdates' => 'all' // Tambahkan parameter ini
                                    ]);

                                    Log::info('Event yang ada di kalender mahasiswa berhasil diupdate dengan notifikasi', [
                                        'event_id' => $existingEvent->getId()
                                    ]);
                                } else {
                                    // PENTING: Kita TIDAK membuat event baru di kalender mahasiswa
                                    // Sebagai gantinya, kita mengimpor event dosen sebagai "instance" di kalender mahasiswa
                                    // Ini menggunakan fitur "import" dari Google Calendar API

                                    // Buat event baru yang identik
                                    $mirrorEvent = new \Google_Service_Calendar_Event();
                                    $mirrorEvent->setSummary('Bimbingan ' . ucfirst($usulan->jenis_bimbingan) . ' dengan ' . $usulan->dosen->nama);

                                    // Gunakan iCalUID yang sama untuk menautkan dengan event dosen
                                    $mirrorEvent->setICalUID($eventReference->getICalUID());

                                    // Set deskripsi khusus untuk mahasiswa
                                    $mhsDescription =
                                        "NIM: {$usulan->nim}\n" .
                                        "Dosen: {$usulan->dosen->nama}\n" .
                                        "NIP: {$usulan->dosen->nip}\n" .
                                        "Jenis: " . ucfirst($usulan->jenis_bimbingan) . "\n" .
                                        "Lokasi: {$request->lokasi}\n" .
                                        "Status: TERKONFIRMASI\n" .
                                        "Waktu Konfirmasi: " . now()->format('d-m-Y H:i') . "\n" .
                                        "Nomor Antrian: {$usulan->nomor_antrian}\n" .
                                        ($usulan->deskripsi ? "Catatan: {$usulan->deskripsi}" : "");

                                    $mirrorEvent->setDescription($mhsDescription);

                                    // Waktu yang sama dengan event dosen
                                    $mirrorEvent->setStart($eventReference->getStart());
                                    $mirrorEvent->setEnd($eventReference->getEnd());

                                    // Set lokasi jika ada
                                    if ($request->lokasi) {
                                        $mirrorEvent->setLocation($request->lokasi);
                                    }

                                    // Set warna event (9 = hijau)
                                    $mirrorEvent->setColorId('9');

                                    // Set reminder
                                    $reminders = new \Google_Service_Calendar_EventReminders();
                                    $reminders->setUseDefault(false);

                                    $reminderItems = [];

                                    // Email 1 hari sebelumnya
                                    $emailReminder = new \Google_Service_Calendar_EventReminder();
                                    $emailReminder->setMethod('email');
                                    $emailReminder->setMinutes(24 * 60); // 1440 menit = 1 hari
                                    $reminderItems[] = $emailReminder;

                                    // Popup 30 menit sebelumnya
                                    $popupReminder1 = new \Google_Service_Calendar_EventReminder();
                                    $popupReminder1->setMethod('popup');
                                    $popupReminder1->setMinutes(30);
                                    $reminderItems[] = $popupReminder1;

                                    // Popup 5 menit sebelumnya
                                    $popupReminder2 = new \Google_Service_Calendar_EventReminder();
                                    $popupReminder2->setMethod('popup');
                                    $popupReminder2->setMinutes(5);
                                    $reminderItems[] = $popupReminder2;

                                    // Tambahkan reminder ke event
                                    $reminders->setOverrides($reminderItems);
                                    $mirrorEvent->setReminders($reminders);

                                    // PERBAIKAN 4: Tambahkan attendees di event mahasiswa
                                    $mhsAttendees = [];

                                    // Tambahkan dosen sebagai attendee
                                    $dosenAttendee = new \Google_Service_Calendar_EventAttendee();
                                    $dosenAttendee->setEmail($usulan->dosen->email);
                                    $dosenAttendee->setDisplayName($usulan->dosen->nama);
                                    $dosenAttendee->setResponseStatus('accepted');
                                    $mhsAttendees[] = $dosenAttendee;

                                    // Tambahkan mahasiswa juga sebagai attendee (agar mendapat notifikasi)
                                    $mhsAttendee = new \Google_Service_Calendar_EventAttendee();
                                    $mhsAttendee->setEmail($mahasiswa->email);
                                    $mhsAttendee->setDisplayName($mahasiswa->nama);
                                    $mhsAttendee->setResponseStatus('accepted');
                                    $mhsAttendees[] = $mhsAttendee;

                                    $mirrorEvent->setAttendees($mhsAttendees);

                                    // PERBAIKAN 5: Impor event ke kalender mahasiswa dengan sendUpdates=all
                                    $importedEvent = $serviceMhs->events->import('primary', $mirrorEvent, [
                                        'conferenceDataVersion' => 0,
                                        'supportsAttachments' => true,
                                        'sendUpdates' => 'all' // Tambahkan parameter ini untuk kirim notifikasi
                                    ]);

                                    Log::info('Event berhasil diimpor ke kalender mahasiswa dengan notifikasi', [
                                        'imported_event_id' => $importedEvent->getId()
                                    ]);

                                    // PERBAIKAN 6: Double-ensure dengan update tambahan untuk kirim notifikasi
                                    try {
                                        // Tambahan update untuk memastikan notifikasi terkirim
                                        $serviceMhs->events->update('primary', $importedEvent->getId(), $importedEvent, [
                                            'sendUpdates' => 'all'
                                        ]);

                                        Log::info('Additional update untuk memastikan notifikasi event mahasiswa terkirim');
                                    } catch (\Exception $e) {
                                        Log::warning('Gagal melakukan update tambahan pada event mahasiswa: ' . $e->getMessage());
                                        // Tetap lanjutkan proses meskipun ada error di sini
                                    }
                                }
                            } else {
                                Log::warning('Tidak dapat menginisialisasi Google Calendar dengan token mahasiswa');
                            }
                        } catch (\Exception $e) {
                            Log::error('Error saat mengelola event di kalender mahasiswa: ' . $e->getMessage(), [
                                'trace' => $e->getTraceAsString()
                            ]);
                            // Kita tidak throw exception di sini agar proses utama tetap jalan
                        }
                    } else {
                        Log::info('Mahasiswa belum terhubung dengan Google Calendar');
                    }

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Usulan bimbingan berhasil disetujui dan undangan telah dikirim'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error Google Calendar: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);

                    // Tetap commit perubahan database meskipun ada error Google Calendar
                    DB::commit();
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
            Log::error('Error in approve consultation: ' . $e->getMessage(), [
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
            Log::info('Memulai tolak usulan dengan ID: ' . $id, [
                'request' => $request->all()
            ]);

            DB::beginTransaction();

            $usulan = UsulanBimbingan::findOrFail($id);

            // Cek apakah usulan sudah ditolak sebelumnya
            if ($usulan->status === 'DITOLAK') {
                return response()->json([
                    'success' => false,
                    'message' => 'Usulan bimbingan ini sudah ditolak sebelumnya'
                ], 400);
            }

            // Update status usulan menjadi DITOLAK
            $usulan->update([
                'status' => 'DITOLAK',
                'keterangan' => $request->keterangan
            ]);

            // Ambil jadwal terkait
            $jadwal = JadwalBimbingan::where('event_id', $usulan->event_id)->first();

            if ($jadwal) {
                // Log debug
                Log::info('Updating JadwalBimbingan status:', [
                    'jadwal_id' => $jadwal->id,
                    'event_id' => $jadwal->event_id,
                    'waktu_mulai' => $jadwal->waktu_mulai,
                    'today' => Carbon::now()->format('Y-m-d H:i:s'),
                    'is_past_date' => Carbon::parse($jadwal->waktu_selesai)->isPast()
                ]);

                // Hitung ulang jumlah pendaftar dari usulan yang aktif
                $pendaftarCount = DB::table('usulan_bimbingans')
                    ->where('event_id', $jadwal->event_id)
                    ->whereIn('status', ['USULAN', 'DISETUJUI'])
                    ->count();

                // Cara 1: PERBAIKAN UTAMA - CEK DATABASE UNTUK NILAI YANG DIPERBOLEHKAN
                // Dapatkan daftar nilai yang diizinkan untuk kolom status
                $allowedStatusValues = DB::select("SHOW COLUMNS FROM jadwal_bimbingans WHERE Field = 'status'");
                $enumValues = [];

                if (!empty($allowedStatusValues) && isset($allowedStatusValues[0]->Type)) {
                    // Ekstrak nilai ENUM dari definisi kolom (misal: enum('tersedia','penuh','tidak_tersedia'))
                    preg_match('/enum\((.*)\)/', $allowedStatusValues[0]->Type, $matches);
                    if (isset($matches[1])) {
                        // Pisahkan nilai-nilai yang diperbolehkan
                        $enumStr = $matches[1];
                        $enumValues = array_map(function ($val) {
                            return trim($val, "'\"");
                        }, explode(',', $enumStr));

                        Log::info('Status enum values:', ['values' => $enumValues]);
                    }
                }

                // Tentukan status yang tepat, pastikan ada di daftar nilai yang diizinkan
                $newStatus = '';

                if (Carbon::parse($jadwal->waktu_selesai)->isPast()) {
                    // Jika 'selesai' diizinkan, gunakan. Jika tidak, gunakan nilai default 'tersedia'
                    $newStatus = in_array('selesai', $enumValues) ? 'selesai' : 'tersedia';
                } else if ($jadwal->has_kuota_limit && $pendaftarCount >= $jadwal->kapasitas) {
                    $newStatus = in_array('penuh', $enumValues) ? 'penuh' : 'tersedia';
                } else {
                    $newStatus = 'tersedia'; // Default status
                }

                // Hitung sisa kapasitas
                $sisaKapasitas = $jadwal->has_kuota_limit ?
                    max(0, $jadwal->kapasitas - $pendaftarCount) : 0;

                // Cara 2: ALTERNATIF - MENGGUNAKAN UPDATE RAW SQL
                // Ini memberi kita kontrol lebih terhadap query SQL yang dijalankan
                $updated = DB::statement("
                UPDATE jadwal_bimbingans 
                SET jumlah_pendaftar = ?, 
                    sisa_kapasitas = ?, 
                    status = ?, 
                    updated_at = ? 
                WHERE id = ?
            ", [$pendaftarCount, $sisaKapasitas, $newStatus, now(), $jadwal->id]);

                // Log hasil update
                Log::info('Status jadwal diperbarui setelah penolakan:', [
                    'event_id' => $jadwal->event_id,
                    'jumlah_pendaftar_baru' => $pendaftarCount,
                    'sisa_kapasitas' => $sisaKapasitas,
                    'status_baru' => $newStatus,
                    'update_success' => $updated
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
    /**
     * Menyelesaikan bimbingan yang telah disetujui
     */
    public function selesaikan($id)
    {
        Log::info('Fungsi selesaikan dipanggil dengan ID: ' . $id);
        try {
            // Mulai transaksi database
            DB::beginTransaction();

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

            $jadwal = JadwalBimbingan::where('event_id', $usulan->event_id)->first();
            if ($jadwal) {
                // Ambil nilai ENUM yang valid dari database
                $validStatusQuery = DB::select("SHOW COLUMNS FROM jadwal_bimbingans WHERE Field = 'status'");
                $validStatusValues = [];

                if (!empty($validStatusQuery) && isset($validStatusQuery[0]->Type)) {
                    preg_match('/enum\((.*)\)/', $validStatusQuery[0]->Type, $matches);
                    if (isset($matches[1])) {
                        $validStatusValues = array_map(function ($val) {
                            return trim($val, "'\"");
                        }, explode(',', $matches[1]));
                    }
                }

                // Tentukan status yang akan digunakan (selesai jika valid, atau tersedia sebagai fallback)
                $newStatus = in_array('selesai', $validStatusValues) ? 'selesai' : 'tersedia';

                // Update jadwal dengan status yang valid
                DB::table('jadwal_bimbingans')
                    ->where('id', $jadwal->id)
                    ->update([
                        'status' => $newStatus,
                        'updated_at' => now()
                    ]);

                Log::info('Jadwal diupdate dengan status: ' . $newStatus, [
                    'jadwal_id' => $jadwal->id,
                    'valid_statuses' => $validStatusValues
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bimbingan berhasil diselesaikan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in selesaikan: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e->getTraceAsString()
            ]);

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
            $perPage = $request->input('per_page', 50);

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
            $perPage = $request->input('per_page', 50);

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

    /**
     * Membatalkan persetujuan bimbingan
     */

    public function batalkanPersetujuan($id, Request $request)
    {
        try {
            Log::info('Memulai batalkan usulan dengan ID: ' . $id, [
                'request' => $request->all()
            ]);

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
            $bimbingan = UsulanBimbingan::with(['mahasiswa', 'dosen'])->findOrFail($id);

            // Validasi jadwal yang sudah lewat
            $jadwalDate = Carbon::parse($bimbingan->tanggal)->startOfDay();
            $today = Carbon::now()->startOfDay();

            if ($jadwalDate->lt($today)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal bimbingan tidak dapat dibatalkan karena sudah lewat waktu'
                ], 400);
            }

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

            // Simpan daftar semua ID usulan bimbingan yang dibatalkan (termasuk ID utama)
            $allCanceledIds = [$id];

            // Jika ada jadwal terkait yang dipilih, batalkan juga
            if ($request->filled('related_schedules') && count($request->related_schedules) > 0) {
                $relatedIds = $request->related_schedules;
                $allCanceledIds = array_merge($allCanceledIds, $relatedIds);

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

            // Update jadwal terkait di JadwalBimbingan
            $jadwal = JadwalBimbingan::where('event_id', $bimbingan->event_id)->first();
            if ($jadwal) {
                // Hitung ulang jumlah pendaftar dari usulan yang aktif
                $pendaftarCount = DB::table('usulan_bimbingans')
                    ->where('event_id', $jadwal->event_id)
                    ->whereIn('status', ['USULAN', 'DISETUJUI'])
                    ->count();

                // Tentukan status yang tepat berdasarkan konstanta
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

                // === PERBAIKAN: PROSES UPDATE GOOGLE CALENDAR ===
                try {
                    $dosen = Auth::user();
                    if ($dosen->hasGoogleCalendarConnected()) {
                        if ($this->googleCalendarController->validateAndRefreshToken()) {
                            $client = $this->googleCalendarController->getClient();
                            $service = new \Google_Service_Calendar($client);

                            // LANGKAH 1: UPDATE EVENT DOSEN
                            try {
                                $event = $service->events->get('primary', $jadwal->event_id);

                                // Siapkan daftar semua mahasiswa yang dibatalkan untuk header deskripsi
                                $canceledStudents = [];

                                // Ambil semua data usulan yang dibatalkan
                                $allCanceledUsulan = UsulanBimbingan::with('mahasiswa')
                                    ->whereIn('id', $allCanceledIds)
                                    ->get();

                                foreach ($allCanceledUsulan as $usulan) {
                                    $canceledStudents[] = [
                                        'nama' => $usulan->mahasiswa->nama,
                                        'nim' => $usulan->nim,
                                        'email' => $usulan->mahasiswa->email
                                    ];
                                }

                                // Ambil data attendees
                                $attendees = $event->getAttendees() ?: [];
                                $updatedAttendees = [];

                                // Buat array email mahasiswa yang dibatalkan untuk filter attendees
                                $canceledEmails = array_map(function ($student) {
                                    return $student['email'];
                                }, $canceledStudents);

                                // Filter attendees untuk menghapus mahasiswa yang dibatalkan
                                foreach ($attendees as $attendee) {
                                    if (!in_array($attendee->getEmail(), $canceledEmails)) {
                                        $updatedAttendees[] = $attendee;
                                    }
                                }

                                // Update deskripsi event
                                $existingDescription = $event->getDescription() ?: '';
                                $newDescription = $existingDescription;

                                // Informasi pembatalan
                                $cancelInfo = "\n\n--- PEMBATALAN BIMBINGAN ---\n";
                                $cancelInfo .= "Mahasiswa berikut dibatalkan bimbingannya:\n";

                                foreach ($canceledStudents as $student) {
                                    $cancelInfo .= "- {$student['nama']} (NIM: {$student['nim']})\n";
                                }

                                $cancelInfo .= "Alasan: {$request->alasan}\n";
                                $cancelInfo .= "Waktu pembatalan: " . now()->format('d-m-Y H:i');

                                // Tambahkan info pembatalan ke deskripsi
                                $newDescription .= $cancelInfo;
                                $event->setDescription($newDescription);

                                // Update attendees
                                $event->setAttendees($updatedAttendees);

                                // Update event dosen dengan sendUpdates=all
                                $updatedEvent = $service->events->update('primary', $jadwal->event_id, $event, [
                                    'sendUpdates' => 'all' // Kirim notifikasi ke semua attendees
                                ]);

                                Log::info('Berhasil update event dosen dengan pembatalan massal', [
                                    'event_id' => $jadwal->event_id,
                                    'mahasiswa_dibatalkan' => count($canceledStudents)
                                ]);

                                // LANGKAH 2: KIRIM NOTIFIKASI PEMBATALAN KHUSUS KE SETIAP MAHASISWA
                                foreach ($canceledStudents as $student) {
                                    // Cari usulan mahasiswa
                                    $mahasiswaUsulan = $allCanceledUsulan->where('nim', $student['nim'])->first();

                                    if (!$mahasiswaUsulan) continue;

                                    // Cari objek mahasiswa
                                    $mahasiswa = $mahasiswaUsulan->mahasiswa;

                                    if (!$mahasiswa) continue;

                                    // Buat event terpisah khusus untuk notifikasi pembatalan
                                    $cancelNotificationEvent = new \Google_Service_Calendar_Event();
                                    $cancelNotificationEvent->setSummary('[DIBATALKAN] Bimbingan dengan ' . $dosen->nama);

                                    // Gunakan waktu yang sama dengan event asli
                                    $cancelNotificationEvent->setStart($event->getStart());
                                    $cancelNotificationEvent->setEnd($event->getEnd());

                                    // Deskripsikan pembatalan
                                    $cancelDesc = "NOTIFIKASI PEMBATALAN BIMBINGAN\n\n";
                                    $cancelDesc .= "Bimbingan dengan dosen {$dosen->nama} telah DIBATALKAN.\n\n";
                                    $cancelDesc .= "Detail bimbingan:\n";
                                    $cancelDesc .= "- NIM: {$mahasiswaUsulan->nim}\n";
                                    $cancelDesc .= "- Tanggal: " . Carbon::parse($mahasiswaUsulan->tanggal)->format('d-m-Y') . "\n";
                                    $cancelDesc .= "- Waktu: {$mahasiswaUsulan->waktu_mulai} - {$mahasiswaUsulan->waktu_selesai}\n";
                                    $cancelDesc .= "- Jenis: " . ucfirst($mahasiswaUsulan->jenis_bimbingan) . "\n\n";
                                    $cancelDesc .= "Alasan pembatalan: {$request->alasan}\n";
                                    $cancelDesc .= "Waktu pembatalan: " . now()->format('d-m-Y H:i');

                                    $cancelNotificationEvent->setDescription($cancelDesc);

                                    // Set sebagai cancelled
                                    $cancelNotificationEvent->setStatus('cancelled');

                                    // Tambahkan mahasiswa sebagai satu-satunya attendee
                                    $mahasiswaAttendee = new \Google_Service_Calendar_EventAttendee();
                                    $mahasiswaAttendee->setEmail($mahasiswa->email);
                                    $mahasiswaAttendee->setDisplayName($mahasiswa->nama);
                                    $cancelNotificationEvent->setAttendees([$mahasiswaAttendee]);

                                    // Buat event notifikasi
                                    try {
                                        $notifEvent = $service->events->insert('primary', $cancelNotificationEvent, [
                                            'sendUpdates' => 'all', // Kirim notifikasi email
                                            'supportsAttachments' => true
                                        ]);

                                        Log::info('Berhasil membuat notifikasi pembatalan untuk mahasiswa', [
                                            'nama' => $mahasiswa->nama,
                                            'nim' => $mahasiswa->nim,
                                            'notif_event_id' => $notifEvent->getId()
                                        ]);
                                    } catch (\Exception $e) {
                                        Log::error('Gagal membuat notifikasi pembatalan untuk mahasiswa: ' . $mahasiswa->nim, [
                                            'error' => $e->getMessage()
                                        ]);
                                    }

                                    // LANGKAH 3: UPDATE EVENT DI KALENDER MAHASISWA
                                    if ($mahasiswa->hasGoogleCalendarConnected()) {
                                        $googleCalendarMhs = new GoogleCalendarController();

                                        if ($googleCalendarMhs->initWithUserToken($mahasiswa)) {
                                            $serviceMhs = new \Google_Service_Calendar($googleCalendarMhs->getClient());

                                            // Hapus semua event di kalender mahasiswa yang terkait dengan event ini
                                            try {
                                                // STRATEGI 1: Cari berdasarkan waktu dan tanggal spesifik
                                                $startTime = Carbon::parse($mahasiswaUsulan->tanggal . ' ' . $mahasiswaUsulan->waktu_mulai)
                                                    ->subHour()
                                                    ->toRfc3339String();

                                                $endTime = Carbon::parse($mahasiswaUsulan->tanggal . ' ' . $mahasiswaUsulan->waktu_selesai)
                                                    ->addHour()
                                                    ->toRfc3339String();

                                                $optParams = [
                                                    'timeMin' => $startTime,
                                                    'timeMax' => $endTime,
                                                    'singleEvents' => true,
                                                    'orderBy' => 'startTime',
                                                    'q' => $dosen->nama // Cari berdasarkan nama dosen
                                                ];

                                                $events = $serviceMhs->events->listEvents('primary', $optParams);
                                                $found = false;

                                                foreach ($events->getItems() as $event) {
                                                    $eventDesc = $event->getDescription() ?: '';
                                                    $eventTitle = $event->getSummary() ?: '';

                                                    // Cek apakah ini event bimbingan yang tepat
                                                    if ((strpos($eventDesc, "NIM: {$mahasiswaUsulan->nim}") !== false &&
                                                            strpos($eventDesc, "Dosen: {$dosen->nama}") !== false) ||
                                                        (strpos($eventTitle, 'Bimbingan') !== false &&
                                                            strpos($eventTitle, $dosen->nama) !== false)
                                                    ) {
                                                        // HAPUS event dari kalender mahasiswa
                                                        $serviceMhs->events->delete('primary', $event->getId());

                                                        Log::info('Event di kalender mahasiswa berhasil dihapus', [
                                                            'mahasiswa' => $mahasiswa->nim,
                                                            'event_id' => $event->getId()
                                                        ]);

                                                        $found = true;
                                                    }
                                                }

                                                // Jika tidak ditemukan, kirim notifikasi pembatalan terpisah
                                                if (!$found) {
                                                    // Buat event notifikasi pembatalan terpisah
                                                    $newEvent = new \Google_Service_Calendar_Event();
                                                    $newEvent->setSummary('[DIBATALKAN] Bimbingan dengan ' . $dosen->nama);

                                                    // Atur waktu sesuai jadwal
                                                    $startDateTime = new \Google_Service_Calendar_EventDateTime();
                                                    $startDateTime->setDateTime(Carbon::parse($mahasiswaUsulan->tanggal . ' ' . $mahasiswaUsulan->waktu_mulai)->toRfc3339String());
                                                    $startDateTime->setTimeZone('Asia/Jakarta');
                                                    $newEvent->setStart($startDateTime);

                                                    $endDateTime = new \Google_Service_Calendar_EventDateTime();
                                                    $endDateTime->setDateTime(Carbon::parse($mahasiswaUsulan->tanggal . ' ' . $mahasiswaUsulan->waktu_selesai)->toRfc3339String());
                                                    $endDateTime->setTimeZone('Asia/Jakarta');
                                                    $newEvent->setEnd($endDateTime);

                                                    // Deskripsi
                                                    $cancelDesc = "NOTIFIKASI PEMBATALAN BIMBINGAN\n\n";
                                                    $cancelDesc .= "Bimbingan dengan dosen {$dosen->nama} telah DIBATALKAN.\n\n";
                                                    $cancelDesc .= "Detail bimbingan:\n";
                                                    $cancelDesc .= "- NIM: {$mahasiswaUsulan->nim}\n";
                                                    $cancelDesc .= "- Tanggal: " . Carbon::parse($mahasiswaUsulan->tanggal)->format('d-m-Y') . "\n";
                                                    $cancelDesc .= "- Waktu: {$mahasiswaUsulan->waktu_mulai} - {$mahasiswaUsulan->waktu_selesai}\n";
                                                    $cancelDesc .= "- Jenis: " . ucfirst($mahasiswaUsulan->jenis_bimbingan) . "\n\n";
                                                    $cancelDesc .= "Alasan pembatalan: {$request->alasan}\n";
                                                    $cancelDesc .= "Waktu pembatalan: " . now()->format('d-m-Y H:i');

                                                    $newEvent->setDescription($cancelDesc);

                                                    // Tambahkan mahasiswa sebagai attendee
                                                    $mhsAttendee = new \Google_Service_Calendar_EventAttendee();
                                                    $mhsAttendee->setEmail($mahasiswa->email);
                                                    $mhsAttendee->setDisplayName($mahasiswa->nama);
                                                    $newEvent->setAttendees([$mhsAttendee]);

                                                    // Buat event notifikasi yang hanya untuk notifikasi
                                                    try {
                                                        $notifEvent = $serviceMhs->events->insert('primary', $newEvent, [
                                                            'sendUpdates' => 'all' // Kirim notifikasi email
                                                        ]);

                                                        // Segera hapus event tersebut agar tidak tampil di kalender
                                                        try {
                                                            $serviceMhs->events->delete('primary', $notifEvent->getId());
                                                            Log::info('Event notifikasi berhasil dihapus setelah notifikasi terkirim', [
                                                                'mahasiswa' => $mahasiswa->nim,
                                                                'notif_event_id' => $notifEvent->getId()
                                                            ]);
                                                        } catch (\Exception $e) {
                                                            Log::warning('Gagal menghapus event notifikasi: ' . $e->getMessage());
                                                        }
                                                    } catch (\Exception $e) {
                                                        Log::error('Gagal membuat event notifikasi untuk mahasiswa: ' . $mahasiswa->nim, [
                                                            'error' => $e->getMessage()
                                                        ]);
                                                    }
                                                }
                                            } catch (\Exception $e) {
                                                Log::error('Gagal mengelola event di kalender mahasiswa: ' . $mahasiswa->nim, [
                                                    'error' => $e->getMessage()
                                                ]);
                                            }
                                        } else {
                                            Log::warning('Tidak bisa menginisialisasi Google Calendar mahasiswa: ' . $mahasiswa->nim);
                                        }
                                    } else {
                                        Log::info('Mahasiswa tidak terhubung dengan Google Calendar: ' . $mahasiswa->nim);
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::error('Gagal mendapatkan atau mengupdate event dosen: ' . $e->getMessage(), [
                                    'event_id' => $jadwal->event_id,
                                    'exception' => $e
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error saat memperbarui Google Calendar: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Jangan throw exception di sini
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
