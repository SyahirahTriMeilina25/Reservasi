<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\PilihJadwalController;
use App\Http\Controllers\MasukkanJadwalController;
use App\Http\Controllers\PesanController;
use App\Http\Controllers\ProfilController;
use App\Models\JadwalBimbingan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Log;

// Route untuk guest (belum login)
Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::get('/datausulanbimbingan', function () {
    return view('bimbingan.admin.datausulanbimbingan');
});

// ===============================================================================
// ROUTES UNTUK MAHASISWA & DOSEN (PESAN)
// ===============================================================================
Route::middleware(['auth:mahasiswa,dosen'])->group(function () {
    Route::prefix('pesan')->group(function () {
        Route::get('/dashboardkonsultasi', function () {
            if (auth()->guard('mahasiswa')->check()) {
                return app(PesanController::class)->indexMahasiswa();
            } else {
                return app(PesanController::class)->indexDosen();
            }
        })->name('pesan.dashboardkonsultasi');

        Route::get('/getMahasiswaByAngkatan', [PesanController::class, 'getMahasiswaByAngkatan'])->name('pesan.getMahasiswaByAngkatan');
        Route::get('/create', [PesanController::class, 'create'])->name('pesan.create');
        Route::post('/store', [PesanController::class, 'store'])->name('pesan.store');

        // Route lainnya untuk pesan
        Route::get('/{id}', [PesanController::class, 'show'])->name('pesan.show');
        Route::patch('/{id}/status', [PesanController::class, 'updateStatus'])->name('pesan.updateStatus');
        Route::post('/request-notification', [PesanController::class, 'requestNotification'])->name('pesan.requestNotification');
        Route::get('/filterAktif', [PesanController::class, 'filterAktif'])->name('pesan.filterAktif');
        Route::get('/filterSelesai', [PesanController::class, 'filterSelesai'])->name('pesan.filterSelesai');
        Route::get('/getDosen', [PesanController::class, 'getDosen'])->name('pesan.getDosen');
        Route::post('/reply/{id}', [PesanController::class, 'storeReply'])->name('pesan.reply');
        Route::post('/end/{id}', [PesanController::class, 'endChat'])->name('pesan.end');
        Route::get('/attachment/{id}', [PesanController::class, 'downloadAttachment'])->name('pesan.attachment');
    });
});

// ===============================================================================
// ROUTES UNTUK MAHASISWA, DOSEN & ADMIN (PROFIL + VERIFY PAGE)
// ===============================================================================
Route::middleware(['auth:mahasiswa,dosen,admin'])->group(function () {
    Route::controller(ProfilController::class)->group(function () {
        Route::get('/profil', 'show')->name('profile.show');
        Route::put('/profil/update', 'update')->name('profile.update');
        Route::delete('/profil/remove', 'remove')->name('profile.remove');
        Route::post('/profil/change-password', 'changePassword')->name('profile.change-password');
    });

    Route::post('/verify-page', function (Request $request) {
        if ($request->page_token !== session('page_token')) {
            return redirect()->route('login');
        }
        return redirect()->back();
    });
});

// ===============================================================================
// ROUTES KHUSUS MAHASISWA
// ===============================================================================
Route::middleware(['auth:mahasiswa', 'checkRole:mahasiswa'])->group(function () {
    Route::controller(MahasiswaController::class)->group(function () {
        Route::get('/usulanbimbingan', 'index')->name('mahasiswa.usulanbimbingan');
        Route::post('/usulanbimbingan/selesai/{id}', 'selesaiBimbingan')->name('mahasiswa.selesaibimbingan');
        Route::get('/aksi-informasi/{id}/{origin?}', [MahasiswaController::class, 'getDetailBimbingan'])
            ->name('mahasiswa.aksiInformasi');
        Route::get('/detaildaftar/{nip}', 'getDetailDaftar')->name('mahasiswa.detaildaftar');
    });

    // Bimbingan routes
    Route::controller(PilihJadwalController::class)->prefix('pilihjadwal')->group(function () {
        Route::get('/', 'index')->name('pilihjadwal.index');
        Route::post('/store', 'store')->name('pilihjadwal.store');
        Route::get('/available', 'getAvailableJadwal')->name('pilihjadwal.available');
        Route::get('/check', 'checkAvailability')->name('pilihjadwal.check');
        Route::post('/create-event/{usulanId}', 'createGoogleCalendarEvent')->name('pilihjadwal.create-event');

        Route::get('/dosen/{nip}/jenis-bimbingan', 'getJenisBimbingan')->name('pilihjadwal.getJenisBimbingan');
        Route::post('/cancel/{id}', 'cancelBooking')->name('pilihjadwal.cancel');
    });

    Route::controller(GoogleCalendarController::class)->prefix('mahasiswa')->group(function () {
        Route::get('/google/connect', 'connect')->name('mahasiswa.google.connect');
        Route::get('/google/callback', 'callback')->name('mahasiswa.google.callback');
    });
});

// ===============================================================================
// ROUTES KHUSUS DOSEN
// ===============================================================================
Route::middleware(['auth:dosen', 'checkRole:dosen'])->group(function () {
    Route::controller(DosenController::class)->group(function () {
        Route::get('/persetujuan', 'index')->name('dosen.persetujuan');
        Route::get('/detail-bimbingan/{id}/{origin?}', [DosenController::class, 'getDetailBimbingan'])
            ->name('dosen.detailbimbingan');
        Route::post('/terimausulanbimbingan/terima/{id}', 'terima')->name('dosen.detailbimbingan.terima');
        Route::post('/terimausulanbimbingan/tolak/{id}', 'tolak')->name('dosen.detailbimbingan.tolak');
        Route::post('/persetujuan/terima/{id}', 'terima')->name('dosen.persetujuan.terima');
        Route::post('/persetujuan/tolak/{id}', 'tolak')->name('dosen.persetujuan.tolak');
        Route::post('/persetujuan/selesai/{id}', [DosenController::class, 'selesaikan'])->name('dosen.persetujuan.selesai');
        Route::get('/dosen/detail/{nip}', [DosenController::class, 'dosenDetail'])->name('dosen.detail');
        Route::get('/dosen/riwayat-detail/{nip}', [DosenController::class, 'riwayatDosenDetail'])->name('dosen.riwayat.detail');
        Route::get('/persetujuan/related-schedules/{id}', [DosenController::class, 'getRelatedSchedules'])
            ->name('persetujuan.related-schedules');
        Route::post('/persetujuan/batal/{id}', [DosenController::class, 'batalkanPersetujuan'])
            ->name('persetujuan.batal')
            ->middleware(['auth']);
    });

    // Jadwal routes
    Route::controller(MasukkanJadwalController::class)->prefix('masukkanjadwal')->group(function () {
        Route::get('/', 'index')->name('dosen.jadwal.index');
        Route::post('/store', 'store')->name('dosen.jadwal.store');
        Route::delete('/{eventId}', 'destroy')->name('dosen.jadwal.destroy');
        Route::post('/debug-store', [MasukkanJadwalController::class, 'debugStore']);
    });

    Route::controller(GoogleCalendarController::class)->prefix('dosen')->group(function () {
        Route::get('/google/connect', 'connect')->name('dosen.google.connect');
        Route::get('/google/events', 'getEvents')->name('dosen.google.events');
        Route::get('/google/callback', 'callback')->name('dosen.google.callback');
        Route::get('/dosen/google/events', [MasukkanJadwalController::class, 'getEvents'])
            ->name('dosen.google.events')
            ->middleware(['auth:dosen']);
    });
});

// ===============================================================================
// ROUTES KHUSUS ADMIN
// ===============================================================================
Route::middleware(['web', 'auth:admin', 'checkRole:admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/dosen/{nip}', [AdminController::class, 'detailDosen'])->name('admin.detaildosen');
    Route::get('/dosen/riwayat/{nip}', [AdminController::class, 'detailRiwayatDosen'])->name('admin.detailriwayatdosen');

    // Detail Bimbingan dan Riwayat
    Route::get('/detail-bimbingan/{id}/{origin?}', [AdminController::class, 'getDetailBimbingan'])->name('admin.getDetailBimbingan');
    Route::get('/detail-riwayat-dosen/{id}', [AdminController::class, 'getRiwayatDetail'])->name('admin.getRiwayatDetail');


    // Manajemen Data Admin
    Route::get('/data-admin', [AdminController::class, 'dataAdmin'])->name('admin.dataadmin');
    Route::get('/edit-admin/{id}', [AdminController::class, 'editAdmin'])->name('admin.editadmin');
    Route::put('/update-admin/{id}', [AdminController::class, 'updateAdmin'])->name('admin.updateadmin');
    Route::post('/reset-password-admin/{id}', [AdminController::class, 'resetPasswordAdmin'])->name('admin.resetpasswordadmin');


    // Manajemen Data Mahasiswa
    Route::get('/data-mahasiswa', [AdminController::class, 'dataMahasiswa'])->name('admin.datamahasiswa');
    Route::get('/tambah-mahasiswa', [AdminController::class, 'tambahMahasiswa'])->name('admin.tambahmahasiswa');
    Route::post('/simpan-mahasiswa', [AdminController::class, 'simpanMahasiswa'])->name('admin.simpanmahasiswa');
    Route::get('/edit-mahasiswa/{nim}', [AdminController::class, 'editMahasiswa'])->name('admin.editmahasiswa');
    Route::put('/update-mahasiswa/{nim}', [AdminController::class, 'updateMahasiswa'])->name('admin.updatemahasiswa');
    Route::post('/reset-password-mahasiswa/{nim}', [AdminController::class, 'resetPasswordMahasiswa'])->name('admin.resetpasswordmahasiswa');
    Route::delete('/hapus-mahasiswa/{nim}', [AdminController::class, 'hapusMahasiswa'])->name('admin.hapusmahasiswa');

    // Manajemen Data Dosen
    Route::get('/data-dosen', [AdminController::class, 'dataDosen'])->name('admin.datadosen');
    Route::get('/tambah-dosen', [AdminController::class, 'tambahDosen'])->name('admin.tambahdosen');
    Route::post('/simpan-dosen', [AdminController::class, 'simpanDosen'])->name('admin.simpandosen');
    Route::get('/edit-dosen/{nip}', [AdminController::class, 'editDosen'])->name('admin.editdosen');
    Route::put('/update-dosen/{nip}', [AdminController::class, 'updateDosen'])->name('admin.updatedosen');
    Route::post('/reset-password-dosen/{nip}', [AdminController::class, 'resetPasswordDosen'])->name('admin.resetpassworddosen');
    Route::delete('/hapus-dosen/{nip}', [AdminController::class, 'hapusDosen'])->name('admin.hapusdosen');

    // Manajemen Konsentrasi
    Route::get('/data-konsentrasi', [AdminController::class, 'dataKonsentrasi'])->name('admin.datakonsentrasi');
    Route::get('/tambah-konsentrasi', [AdminController::class, 'tambahKonsentrasi'])->name('admin.tambahkonsentrasi');
    Route::post('/simpan-konsentrasi', [AdminController::class, 'simpanKonsentrasi'])->name('admin.simpankonsentrasi');
    Route::get('/edit-konsentrasi/{id}', [AdminController::class, 'editKonsentrasi'])->name('admin.editkonsentrasi');
    Route::put('/update-konsentrasi/{id}', [AdminController::class, 'updateKonsentrasi'])->name('admin.updatekonsentrasi');
    Route::delete('/hapus-konsentrasi/{id}', [AdminController::class, 'hapusKonsentrasi'])->name('admin.hapuskonsentrasi');
});

// ===============================================================================
// ROUTES DEBUGGING & UTILITIES
// ===============================================================================
Route::get('/debug-jadwal', function () {
    $jadwals = DB::table('jadwal_bimbingans')
        ->whereNotNull('jenis_bimbingan')
        ->get();
    return $jadwals;
});

Route::get('/debug-jadwal-jenis', function () {
    $jadwals = DB::table('jadwal_bimbingans')
        ->select('id', 'event_id', 'nip', 'jenis_bimbingan', 'waktu_mulai')
        ->get();

    return $jadwals;
});

Route::get('/debug-jadwal-detail', function () {
    return DB::table('jadwal_bimbingans')
        ->select('id', 'nip', 'jenis_bimbingan', 'has_kuota_limit')
        ->get();
});

Route::get('/debug-struktur-tabel', function () {
    $columns = DB::getSchemaBuilder()->getColumnListing('jadwal_bimbingans');
    return [
        'columns' => $columns,
        'has_jenis_bimbingan' => in_array('jenis_bimbingan', $columns),
        'has_has_kuota_limit' => in_array('has_kuota_limit', $columns)
    ];
});

Route::get('/debug-jenis-bimbingan', function () {
    $dosenList = DB::table('dosens')
        ->select('nip', 'nama')
        ->get();

    $jenisBimbinganPerDosen = [];
    foreach ($dosenList as $dosen) {
        $jadwalDenganJenis = DB::table('jadwal_bimbingans')
            ->where('nip', $dosen->nip)
            ->whereNotNull('jenis_bimbingan')
            ->where('jenis_bimbingan', '!=', '')
            ->distinct()
            ->pluck('jenis_bimbingan')
            ->toArray();

        $jenisBimbinganPerDosen[$dosen->nip] = $jadwalDenganJenis;
    }

    return $jenisBimbinganPerDosen;
});

// Tambahkan ini di route yang aman (misal di route debug atau buat yang baru)
Route::get('/fix-jadwal-jenis-bimbingan/{nip}/{jenis}', function ($nip, $jenis) {
    $updatedCount = DB::table('jadwal_bimbingans')
        ->where('nip', $nip)
        ->whereNull('jenis_bimbingan')
        ->update(['jenis_bimbingan' => $jenis]);

    return "Updated {$updatedCount} jadwal for NIP {$nip} with jenis_bimbingan '{$jenis}'";
});

Route::get('/fix-jadwal/{id}/{jenis}', function ($id, $jenis) {
    $jadwal = JadwalBimbingan::find($id);
    if ($jadwal) {
        $jadwal->jenis_bimbingan = $jenis;
        $jadwal->save();
        return "Jadwal ID: $id diupdate ke jenis bimbingan: $jenis";
    }
    return "Jadwal tidak ditemukan";
});

Route::get('/fix-jenis-bimbingan', function () {
    // Update jadwal ke jenis bimbingan "skripsi" untuk dosen tertentu
    $updated = DB::table('jadwal_bimbingans')
        ->where('nip', '198501012015041025')
        ->where(function ($query) {
            $query->whereNull('jenis_bimbingan')
                ->orWhere('jenis_bimbingan', '');
        })
        ->update(['jenis_bimbingan' => 'skripsi']);

    // Tampilkan jadwal setelah update
    $jadwals = DB::table('jadwal_bimbingans')
        ->where('nip', '198501012015041025')
        ->select('id', 'nip', 'jenis_bimbingan', 'waktu_mulai')
        ->get();

    return [
        'updated_count' => $updated,
        'jadwals' => $jadwals
    ];
});

Route::get('/debug-jadwal-dosen/{nip}', function ($nip) {
    $jadwals = DB::table('jadwal_bimbingans')
        ->where('nip', $nip)
        ->select('id', 'event_id', 'jenis_bimbingan', 'waktu_mulai', 'waktu_selesai')
        ->orderBy('id', 'desc')
        ->get();

    return [
        'count' => $jadwals->count(),
        'data' => $jadwals
    ];
});

Route::get('/debug-schema', function () {
    $columns = DB::getSchemaBuilder()->getColumnListing('jadwal_bimbingans');
    $columnTypes = [];

    foreach ($columns as $column) {
        $columnTypes[$column] = DB::connection()->getDoctrineColumn('jadwal_bimbingans', $column)->getType()->getName();
    }

    return $columnTypes;
});

Route::get('/update-jadwal/{id}/{jenis}', function ($id, $jenis) {
    $jadwal = JadwalBimbingan::find($id);
    if ($jadwal) {
        $jadwal->jenis_bimbingan = $jenis;
        $jadwal->save();
        return "Jadwal ID: $id diupdate ke jenis bimbingan: $jenis";
    }
    return "Jadwal tidak ditemukan";
});

Route::get('/update-dosen-jadwal/{nip}/{jenis}', function ($nip, $jenis) {
    // Validasi jenis bimbingan
    $validJenis = ['skripsi', 'kp', 'akademik', 'konsultasi', 'mbkm', 'lainnya'];
    if (!in_array($jenis, $validJenis)) {
        return "Jenis bimbingan tidak valid. Pilih dari: " . implode(', ', $validJenis);
    }

    // Update semua jadwal null untuk dosen ini
    $updated = DB::table('jadwal_bimbingans')
        ->where('nip', $nip)
        ->whereNull('jenis_bimbingan')
        ->update(['jenis_bimbingan' => $jenis]);

    return "Updated $updated jadwal untuk dosen $nip ke '$jenis'";
});

Route::get('/run-update-jadwal', function () {
    Artisan::call('jadwal:update-status');
    return "Status jadwal berhasil diperbarui";
});

// Di routes/web.php
Route::get('/jadwal/{id}/status', function ($id) {
    // Cari jadwal berdasarkan ID atau event_ID
    if (!is_numeric($id)) {
        $jadwal = \App\Models\JadwalBimbingan::where('event_id', $id)->first();
        if (!$jadwal) return response()->json(['error' => 'Jadwal tidak ditemukan'], 404);
        $id = $jadwal->id;
    } else {
        $jadwal = \App\Models\JadwalBimbingan::find($id);
        if (!$jadwal) return response()->json(['error' => 'Jadwal tidak ditemukan'], 404);
    }

    // Hitung pendaftar (termasuk yang SELESAI)
    $pendaftarCount = DB::table('usulan_bimbingans')
        ->where('event_id', $jadwal->event_id)
        ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI', 'SELESAI'])
        ->count();

    // Hitung jumlah yang SELESAI
    $selesaiCount = DB::table('usulan_bimbingans')
        ->where('event_id', $jadwal->event_id)
        ->where('status', 'SELESAI')
        ->count();

    // Hitung jumlah yang AKTIF (tidak termasuk SELESAI)
    $aktifCount = $pendaftarCount - $selesaiCount;

    // PERBAIKAN: Ambil nilai-nilai ENUM yang valid
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

    // PERBAIKAN: Tentukan status berdasarkan nilai yang valid
    $newStatus = '';
    if ($jadwal->has_kuota_limit && $pendaftarCount >= $jadwal->kapasitas) {
        $newStatus = 'penuh'; // Ini kemungkinan valid
    } else if (\Carbon\Carbon::parse($jadwal->waktu_selesai)->isPast()) {
        // PERBAIKAN: Gunakan status tersedia sebagai default, bukan selesai
        $newStatus = 'tersedia';
    } else {
        $newStatus = 'tersedia'; // Default status
    }

    // PERBAIKAN: Pastikan status valid sebelum mengupdate
    if (!in_array($newStatus, $validStatusValues)) {
        $newStatus = 'tersedia'; // Gunakan tersedia sebagai fallback aman
    }

    // Hitung sisa kapasitas
    $sisaKapasitas = $jadwal->has_kuota_limit ?
        max(0, $jadwal->kapasitas - $pendaftarCount) : 0;

    // PERBAIKAN: Jangan gunakan DB::raw, gunakan binding parameter biasa
    try {
        DB::table('jadwal_bimbingans')
            ->where('id', $jadwal->id)
            ->update([
                'status' => $newStatus,
                'jumlah_pendaftar' => $pendaftarCount,
                'sisa_kapasitas' => $sisaKapasitas,
                'updated_at' => now()
            ]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Error update status: ' . $e->getMessage());
        // Jangan throw error, biarkan tetap lanjut dengan status terakhir
    }

    // Ambil jadwal yang sudah diupdate untuk respons
    $updatedJadwal = \App\Models\JadwalBimbingan::find($jadwal->id);

    // TAMBAHAN: Berikan status label yang benar meskipun tidak ada di database
    $statusLabel = match ($updatedJadwal->status) {
        'tersedia' => 'Tersedia',
        'tidak_tersedia' => 'Tidak Tersedia',
        'penuh' => 'Penuh',
        'dibatalkan' => 'Dibatalkan',
        default => 'Tersedia'
    };

    return response()->json([
        'status' => $updatedJadwal->status,
        'status_label' => $statusLabel,
        'jumlah_pendaftar' => $pendaftarCount,
        'kapasitas' => $updatedJadwal->kapasitas,
        'selesai_count' => $selesaiCount,
        'aktif_count' => $aktifCount,
        'has_kuota_limit' => $updatedJadwal->has_kuota_limit,
        'sisa_kapasitas' => $updatedJadwal->sisa_kapasitas
    ]);
});

Route::get('/debug-jadwal-status/{id}', function ($id) {
    $jadwal = \App\Models\JadwalBimbingan::find($id);

    if (!$jadwal) {
        return "Jadwal tidak ditemukan";
    }

    $pendaftarCount = DB::table('usulan_bimbingans')
        ->where('event_id', $jadwal->event_id)
        ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
        ->count();

    return [
        'id' => $jadwal->id,
        'event_id' => $jadwal->event_id,
        'status' => $jadwal->status,
        'status_label' => $jadwal->status_label,
        'jumlah_pendaftar_db' => $jadwal->jumlah_pendaftar,
        'jumlah_pendaftar_hitung' => $pendaftarCount,
        'kapasitas' => $jadwal->kapasitas,
        'has_kuota_limit' => $jadwal->has_kuota_limit,
        'waktu_mulai' => $jadwal->waktu_mulai,
        'waktu_selesai' => $jadwal->waktu_selesai,
        'sudah_lewat' => \Carbon\Carbon::parse($jadwal->waktu_selesai)->isPast()
    ];
});

Route::get('/debug-event-id/{id}', function ($id) {
    $jadwal = \App\Models\JadwalBimbingan::where('event_id', $id)->first();
    if ($jadwal) {
        return [
            'found_by_event_id' => true,
            'id' => $jadwal->id,
            'event_id' => $jadwal->event_id
        ];
    }

    $jadwal = \App\Models\JadwalBimbingan::find($id);
    if ($jadwal) {
        return [
            'found_by_id' => true,
            'id' => $jadwal->id,
            'event_id' => $jadwal->event_id
        ];
    }

    return ['error' => 'Not found with either method'];
});

// ===============================================================================
// ROUTES AUTHENTICATION & SESSION DEBUGGING
// ===============================================================================

// Route untuk pengecekan status autentikasi
Route::get('/auth-check', function () {
    if (Auth::guard('mahasiswa')->check() || Auth::guard('dosen')->check() || Auth::guard('admin')->check()) {
        return response()->json(['authenticated' => true], 200);
    }
    return response()->json(['authenticated' => false], 401);
})->middleware('web');

Route::get('/check-all-auth', function () {
    return response()->json([
        'mahasiswa' => Auth::guard('mahasiswa')->check(),
        'dosen' => Auth::guard('dosen')->check(),
        'admin' => Auth::guard('admin')->check(),
        'session_id' => session()->getId(),
        'session_data' => array_keys(session()->all())
    ]);
});

// Tambahkan di bagian atas routes/web.php
Route::get('/debug-auth', function () {
    $output = [];

    // Periksa tabel role
    $roles = DB::table('role')->get();
    $output['roles'] = $roles->toArray();

    // Periksa admin
    $admins = DB::table('admin')->get();
    $output['admins'] = $admins->toArray();

    // Periksa relasi admin-role
    $adminInfo = [];
    foreach ($admins as $admin) {
        $role = DB::table('role')->where('id', $admin->role_id ?? 0)->first();
        $adminInfo[] = [
            'id' => $admin->id,
            'username' => $admin->username,
            'role_id' => $admin->role_id,
            'role_name' => $role ? $role->role_akses : 'TIDAK DITEMUKAN'
        ];
    }
    $output['admin_roles'] = $adminInfo;

    return response()->json($output);
});

// Tambahkan route untuk memperbaiki admin
Route::get('/fix-admin-role', function () {
    // Cari role admin
    $adminRole = DB::table('role')->where('role_akses', 'admin')->first();

    if (!$adminRole) {
        // Buat role admin jika tidak ada
        $roleId = DB::table('role')->insertGetId([
            'role_akses' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $message = "Role admin dibuat dengan ID: $roleId";
    } else {
        $roleId = $adminRole->id;
        $message = "Role admin ditemukan dengan ID: $roleId";
    }

    // Update semua admin yang tidak memiliki role_id valid
    $updated = DB::table('admin')
        ->whereNull('role_id')
        ->orWhereNotIn('role_id', function ($query) {
            $query->select('id')->from('role');
        })
        ->update(['role_id' => $roleId]);

    return response()->json([
        'message' => $message,
        'admin_updated' => $updated
    ]);
});

Route::get('/test-session', function () {
    // Simpan data ke session
    session(['test_key' => 'test_value_' . time()]);

    // Cek apakah tersimpan
    echo '<h1>Session Test</h1>';
    echo '<p>Session driver: ' . config('session.driver') . '</p>';
    echo '<p>Session ID: ' . session()->getId() . '</p>';
    echo '<p>Test key value: ' . session('test_key') . '</p>';

    // Cek dalam database
    echo '<h2>Session dari Database:</h2>';
    echo '<pre>';
    print_r(DB::table('sessions')->where('id', session()->getId())->first());
    echo '</pre>';
});

// Tambahkan di routes/web.php
// Route DEBUG - HAPUS SETELAH PERBAIKAN SELESAI
Route::get('/debug-session', function () {
    // Tampilkan info session
    echo "<h1>Session Debug</h1>";

    echo "<h2>Session ID: </h2>";
    echo session()->getId();

    echo "<h2>Session Data:</h2>";
    echo "<pre>";
    print_r(session()->all());
    echo "</pre>";

    echo "<h2>Auth Status:</h2>";
    echo "Admin: " . (Auth::guard('admin')->check() ? "Logged In" : "Not Logged In") . "<br>";
    echo "Mahasiswa: " . (Auth::guard('mahasiswa')->check() ? "Logged In" : "Not Logged In") . "<br>";
    echo "Dosen: " . (Auth::guard('dosen')->check() ? "Logged In" : "Not Logged In") . "<br>";

    echo "<h2>Session in Database:</h2>";
    echo "<pre>";
    try {
        $sessionId = session()->getId();
        $dbSession = DB::table('sessions')->where('id', $sessionId)->first();
        if ($dbSession) {
            print_r((array)$dbSession);
        } else {
            echo "Session not found in database!";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    echo "</pre>";

    echo "<h2>Session Configuration:</h2>";
    echo "<pre>";
    echo "Driver: " . config('session.driver') . "\n";
    echo "Lifetime: " . config('session.lifetime') . " minutes\n";
    echo "Cookie: " . config('session.cookie') . "\n";
    echo "Path: " . config('session.path') . "\n";
    echo "Domain: " . (config('session.domain') ?: "NULL") . "\n";
    echo "Secure: " . (config('session.secure') ? "Yes" : "No") . "\n";
    echo "HTTP Only: " . (config('session.http_only') ? "Yes" : "No") . "\n";
    echo "Same Site: " . config('session.same_site') . "\n";
    echo "</pre>";

    echo "<p><a href='/'>Home</a> | <a href='/debug-session'>Refresh</a> | <a href='/logout'>Logout</a></p>";
});

// Route Direct Login - HAPUS SETELAH PERBAIKAN SELESAI
Route::get('/direct-admin-login', function () {
    try {
        // Cari admin
        $admin = \App\Models\Admin::where('username', 'admin')->first();

        if (!$admin) {
            return "Admin user not found!";
        }

        // Login admin
        Auth::guard('admin')->login($admin, true);

        // Set session
        session([
            'login_admin_' . md5('admin') => 1,
            'role' => 'admin',
            'role_akses' => 'admin',
            'role_id' => $admin->role_id,
            'user_id' => $admin->id,
            'user_name' => $admin->nama ?? 'Administrator',
            'username' => $admin->username
        ]);

        // Regenerate session
        session()->regenerate();
        session()->save();

        return redirect()->route('admin.dashboard');
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/debug-admin-dashboard', function () {
    return "Halaman debug admin dashboard - Anda berhasil login";
})->middleware(['auth:admin', 'checkRole:admin']);

Route::get('/set-secure-admin-password', function () {
    $admin = \App\Models\Admin::where('username', 'admin')->first();
    if ($admin) {
        // Gunakan password yang lebih kompleks
        $securePassword = 'Admin_' . uniqid() . '_2025!';
        $admin->password = Hash::make($securePassword);
        $admin->save();
        return "Admin password updated to: " . $securePassword;
    }
    return "Admin not found";
});

Route::get('/debug-admin-session', function () {
    echo "<h1>Admin Session Debug</h1>";

    echo "<h2>Auth Status:</h2>";
    echo "Admin Authenticated: " . (Auth::guard('admin')->check() ? "Yes" : "No") . "<br>";

    echo "<h2>Session Data:</h2>";
    echo "<pre>";
    print_r(session()->all());
    echo "</pre>";

    echo "<h2>Admin User:</h2>";
    echo "<pre>";
    print_r(Auth::guard('admin')->user());
    echo "</pre>";

    return;
})->middleware(['web']);



// TAMBAHKAN INI SEMENTARA UNTUK DEBUG
Route::get('/debug-admin', function() {
    return "Debug: Admin route working";
});

Route::get('/debug-admin-auth', function() {
    if (Auth::guard('admin')->check()) {
        return "Admin authenticated: " . Auth::guard('admin')->user()->username;
    }
    return "Admin NOT authenticated";
})->middleware(['auth:admin']);

// ===============================================================================
// LOGOUT ROUTE
// ===============================================================================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
