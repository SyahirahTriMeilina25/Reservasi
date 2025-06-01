<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Konsentrasi;
use App\Models\UserPhoto; // ADD THIS LINE - This was missing!

class AdminController extends Controller
{
    // Dashboard Admin
    public function index()
    {
        try {
            Log::info('Memulai load admin dashboard');

            // Inisialisasi variabel statistik
            $totalMahasiswa = 0;
            $totalDosen = 0;
            $totalKonsentrasi = 0;

            // Inisialisasi collection kosong untuk paginator
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), // kosong
                0, // total
                50, // per page
                1, // halaman sekarang
                ['path' => request()->url()]
            );

            $dosenList = $emptyPaginator;
            $riwayatDosenList = $emptyPaginator;

            // Cek tabel yang ada dan ambil nama tabel yang benar
            // Langkah 1: Deteksi tabel mahasiswa
            $mahasiswaTable = null;
            if (Schema::hasTable('mahasiswas')) {
                $mahasiswaTable = 'mahasiswas';
                Log::info('Menggunakan tabel mahasiswas');
            } elseif (Schema::hasTable('mahasiswa')) {
                $mahasiswaTable = 'mahasiswa';
                Log::info('Menggunakan tabel mahasiswa');
            }

            // Langkah 2: Deteksi tabel dosen
            $dosenTable = null;
            if (Schema::hasTable('dosens')) {
                $dosenTable = 'dosens';
                Log::info('Menggunakan tabel dosens');
            } elseif (Schema::hasTable('dosen')) {
                $dosenTable = 'dosen';
                Log::info('Menggunakan tabel dosen');
            }

            // Langkah 3: Deteksi tabel konsentrasi
            $konsentrasiTable = null;
            if (Schema::hasTable('konsentrasi')) {
                $konsentrasiTable = 'konsentrasi';
                Log::info('Menggunakan tabel konsentrasi');
            }

            // Langkah 4: Deteksi tabel bimbingan
            $bimbinganTable = null;
            foreach (['usulan_bimbingans', 'bimbingan', 'jadwal_bimbingans'] as $potentialTable) {
                if (Schema::hasTable($potentialTable)) {
                    $bimbinganTable = $potentialTable;
                    Log::info("Tabel bimbingan ditemukan: $bimbinganTable");
                    break;
                }
            }

            // Ambil data statistik jika tabel ditemukan
            if ($mahasiswaTable) {
                $totalMahasiswa = DB::table($mahasiswaTable)->count();
                Log::info("Total mahasiswa: $totalMahasiswa");
            }

            if ($dosenTable) {
                $totalDosen = DB::table($dosenTable)->count();
                Log::info("Total dosen: $totalDosen");
            }

            if ($konsentrasiTable) {
                $totalKonsentrasi = DB::table($konsentrasiTable)->count();
                Log::info("Total konsentrasi: $totalKonsentrasi");
            }

            // Jika tabel dosen ditemukan, ambil data dosen dengan bimbingan hari ini
            if ($dosenTable) {
                try {
                    // Coba pendekatan dengan join bimbingan jika tersedia
                    if ($bimbinganTable) {
                        Log::info("Mencoba query dosen dengan join $bimbinganTable");

                        // Cek kolom yang tersedia
                        $bimbinganColumns = Schema::getColumnListing($bimbinganTable);
                        Log::info("Kolom pada tabel $bimbinganTable: " . implode(', ', $bimbinganColumns));

                        // Tentukan kolom yang tepat untuk filter
                        $tanggalColumn = in_array('tanggal', $bimbinganColumns) ? 'tanggal' : 'waktu_mulai';
                        $nipColumn = in_array('nip_dosen', $bimbinganColumns) ? 'nip_dosen' : 'nip';
                        $statusColumn = in_array('status', $bimbinganColumns) ? 'status' : null;

                        Log::info("Menggunakan kolom: tanggal=$tanggalColumn, nip=$nipColumn, status=$statusColumn");

                        // Query 1: Daftar dosen dengan bimbingan hari ini
                        $dosenListQuery = DB::table($dosenTable)
                            ->select(
                                $dosenTable . '.nip',
                                $dosenTable . '.nama',
                                $dosenTable . '.nama_singkat'
                            );

                        // Jika tabel bimbingan tersedia, tambahkan join dan count
                        if ($bimbinganTable) {
                            $dosenListQuery->leftJoin($bimbinganTable, function ($join) use ($dosenTable, $bimbinganTable, $nipColumn, $tanggalColumn, $statusColumn) {
                                $join->on($dosenTable . '.nip', '=', $bimbinganTable . '.' . $nipColumn);

                                if ($tanggalColumn) {
                                    $join->where($bimbinganTable . '.' . $tanggalColumn, '=', date('Y-m-d'));
                                }

                                if ($statusColumn) {
                                    // Jika ada kolom status, coba filter berdasarkan status DISETUJUI
                                    // Namun jika gagal, ini akan ditangani dalam try-catch
                                    $join->where($bimbinganTable . '.' . $statusColumn, '=', 'DISETUJUI');
                                }
                            });

                            $dosenListQuery->addSelect(DB::raw('COUNT(DISTINCT ' . $bimbinganTable . '.id) as total_bimbingan_hari_ini'));
                        } else {
                            // Jika tidak ada tabel bimbingan, tampilkan 0
                            $dosenListQuery->addSelect(DB::raw('0 as total_bimbingan_hari_ini'));
                        }

                        // Group by dan order
                        $dosenListQuery->groupBy($dosenTable . '.nip', $dosenTable . '.nama', $dosenTable . '.nama_singkat')
                            ->orderBy($dosenTable . '.nama');

                        $dosenList = $dosenListQuery->paginate(50);
                        Log::info('Query dosen list berhasil, jumlah: ' . $dosenList->count());

                        // Query 2: Riwayat bimbingan dosen
                        $riwayatDosenListQuery = DB::table($dosenTable)
                            ->select(
                                $dosenTable . '.nip',
                                $dosenTable . '.nama',
                                $dosenTable . '.nama_singkat'
                            );

                        // Jika tabel bimbingan tersedia, tambahkan join dan count
                        if ($bimbinganTable) {
                            $riwayatDosenListQuery->leftJoin($bimbinganTable, $dosenTable . '.nip', '=', $bimbinganTable . '.' . $nipColumn)
                                ->addSelect(DB::raw('COUNT(DISTINCT ' . $bimbinganTable . '.id) as total_bimbingan'));
                        } else {
                            // Jika tidak ada tabel bimbingan, tampilkan 0
                            $riwayatDosenListQuery->addSelect(DB::raw('0 as total_bimbingan'));
                        }

                        // Group by dan order
                        $riwayatDosenListQuery->groupBy($dosenTable . '.nip', $dosenTable . '.nama', $dosenTable . '.nama_singkat')
                            ->orderBy($dosenTable . '.nama');

                        $riwayatDosenList = $riwayatDosenListQuery->paginate(50);
                        Log::info('Query riwayat dosen list berhasil, jumlah: ' . $riwayatDosenList->count());
                    } else {
                        // Jika tabel bimbingan tidak tersedia, gunakan query sederhana
                        Log::info('Tabel bimbingan tidak tersedia, menggunakan query sederhana');

                        $dosenList = DB::table($dosenTable)
                            ->select(
                                $dosenTable . '.nip',
                                $dosenTable . '.nama',
                                $dosenTable . '.nama_singkat',
                                DB::raw('0 as total_bimbingan_hari_ini')
                            )
                            ->orderBy($dosenTable . '.nama')
                            ->paginate(50);

                        $riwayatDosenList = DB::table($dosenTable)
                            ->select(
                                $dosenTable . '.nip',
                                $dosenTable . '.nama',
                                $dosenTable . '.nama_singkat',
                                DB::raw('0 as total_bimbingan')
                            )
                            ->orderBy($dosenTable . '.nama')
                            ->paginate(50);
                    }
                } catch (\Exception $e) {
                    // Jika terjadi error, gunakan fallback ke query sederhana
                    Log::error('Error saat query dosen: ' . $e->getMessage());
                    Log::info('Menggunakan fallback query sederhana');

                    try {
                        $dosenList = DB::table($dosenTable)
                            ->select(
                                $dosenTable . '.nip',
                                $dosenTable . '.nama',
                                $dosenTable . '.nama_singkat',
                                DB::raw('0 as total_bimbingan_hari_ini')
                            )
                            ->orderBy($dosenTable . '.nama')
                            ->paginate(50);

                        $riwayatDosenList = DB::table($dosenTable)
                            ->select(
                                $dosenTable . '.nip',
                                $dosenTable . '.nama',
                                $dosenTable . '.nama_singkat',
                                DB::raw('0 as total_bimbingan')
                            )
                            ->orderBy($dosenTable . '.nama')
                            ->paginate(50);
                    } catch (\Exception $fallbackError) {
                        // Jika masih error, gunakan paginator kosong
                        Log::error('Fallback query juga error: ' . $fallbackError->getMessage());
                        $dosenList = $emptyPaginator;
                        $riwayatDosenList = $emptyPaginator;
                    }
                }
            }

            Log::info('Rendering admin dashboard view');
            return view('bimbingan.admin.dashboard', compact(
                'totalMahasiswa',
                'totalDosen',
                'totalKonsentrasi',
                'dosenList',
                'riwayatDosenList'
            ));
        } catch (\Exception $e) {
            Log::error('Error di AdminController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback untuk menampilkan dashboard meskipun error
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), // kosong
                0, // total
                50, // per page
                1, // halaman sekarang
                ['path' => request()->url()]
            );

            return view('bimbingan.admin.dashboard', [
                'totalMahasiswa' => 0,
                'totalDosen' => 0,
                'totalKonsentrasi' => 0,
                'dosenList' => $emptyPaginator,
                'riwayatDosenList' => $emptyPaginator,
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }

    // Detail Dosen - MENGGUNAKAN PAGINATION
    public function detailDosen($nip, Request $request)
    {
        try {
            Log::info('Memulai detailDosen dengan NIP: ' . $nip);

            // Get perPage from request or use default
            $perPage = $request->input('per_page', 10);

            // Deteksi tabel yang tersedia
            $dosenTable = Schema::hasTable('dosens') ? 'dosens' : 'dosen';
            $bimbinganTable = Schema::hasTable('usulan_bimbingans') ? 'usulan_bimbingans' : (Schema::hasTable('bimbingan') ? 'bimbingan' : null);

            // Coba load model Dosen
            try {
                $dosen = Dosen::findOrFail($nip);

                // Load prodi relationship
                $dosen->load('prodi');

                if ($bimbinganTable) {
                    // Create paginated bimbinganHariIni directly with query builder
                    $bimbinganQuery = DB::table($bimbinganTable . ' as b')
                        ->join('mahasiswas as m', 'b.nim', '=', 'm.nim')
                        ->select(
                            'b.id',
                            'b.nim',
                            'm.nama as mahasiswa_nama',
                            'b.jenis_bimbingan',
                            'b.tanggal',
                            'b.waktu_mulai',
                            'b.waktu_selesai',
                            'b.lokasi',
                            'b.status'
                        )
                        ->where('b.nip', $nip)
                        ->where('b.tanggal', date('Y-m-d'))
                        ->orderBy('b.waktu_mulai');

                    // Apply search if provided
                    if ($request->has('search') && !empty($request->search)) {
                        $searchTerm = $request->search;
                        $bimbinganQuery->where(function ($query) use ($searchTerm) {
                            $query->where('m.nama', 'like', "%{$searchTerm}%")
                                ->orWhere('b.nim', 'like', "%{$searchTerm}%")
                                ->orWhere('b.jenis_bimbingan', 'like', "%{$searchTerm}%")
                                ->orWhere('b.status', 'like', "%{$searchTerm}%");
                        });
                    }

                    $bimbinganHariIni = $bimbinganQuery->paginate($perPage);

                    $dosen->bimbinganHariIni = $bimbinganHariIni;
                } else {
                    // Create empty paginator if no bimbingan table found
                    $dosen->bimbinganHariIni = new \Illuminate\Pagination\LengthAwarePaginator(
                        collect([]), // empty collection
                        0, // total items
                        $perPage, // per page
                        1, // current page
                        ['path' => request()->url()]
                    );
                }

                return view('bimbingan.admin.detaildosen', compact('dosen'));
            } catch (\Exception $modelError) {
                // Jika model tidak tersedia, gunakan query builder
                Log::warning('Model Dosen error: ' . $modelError->getMessage());
                Log::info('Fallback ke DB query untuk detailDosen');

                $dosen = DB::table($dosenTable)->where('nip', $nip)->first();

                if (!$dosen) {
                    throw new \Exception('Dosen tidak ditemukan');
                }

                // Cari data prodi jika tersedia
                $prodiTable = Schema::hasTable('prodi') ? 'prodi' : null;

                if ($prodiTable) {
                    $prodi = DB::table($prodiTable)
                        ->where('id', $dosen->prodi_id ?? 0)
                        ->first();

                    $dosen->prodi = $prodi;
                }

                if ($bimbinganTable) {
                    // Use pagination for bimbinganHariIni
                    $bimbinganQuery = DB::table($bimbinganTable . ' as b')
                        ->join('mahasiswas as m', 'b.nim', '=', 'm.nim')
                        ->select(
                            'b.id',
                            'b.nim',
                            'm.nama as mahasiswa_nama',
                            'b.jenis_bimbingan',
                            'b.tanggal',
                            'b.waktu_mulai',
                            'b.waktu_selesai',
                            'b.lokasi',
                            'b.status'
                        )
                        ->where('b.nip', $nip)
                        ->where('b.tanggal', date('Y-m-d'))
                        ->orderBy('b.waktu_mulai');

                    // Apply search if provided
                    if ($request->has('search') && !empty($request->search)) {
                        $searchTerm = $request->search;
                        $bimbinganQuery->where(function ($query) use ($searchTerm) {
                            $query->where('m.nama', 'like', "%{$searchTerm}%")
                                ->orWhere('b.nim', 'like', "%{$searchTerm}%")
                                ->orWhere('b.jenis_bimbingan', 'like', "%{$searchTerm}%")
                                ->orWhere('b.status', 'like', "%{$searchTerm}%");
                        });
                    }

                    $bimbinganHariIni = $bimbinganQuery->paginate($perPage);

                    $dosen->bimbinganHariIni = $bimbinganHariIni;
                } else {
                    // Create an empty paginator if no bimbingan table found
                    $dosen->bimbinganHariIni = new \Illuminate\Pagination\LengthAwarePaginator(
                        collect([]), // empty collection
                        0, // total items
                        $perPage, // per page
                        1, // current page
                        ['path' => request()->url()]
                    );
                }

                return view('bimbingan.admin.detaildosen', compact('dosen'));
            }
        } catch (\Exception $e) {
            Log::error('Error di detailDosen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal memuat detail dosen: ' . $e->getMessage());
        }
    }

    // Detail Riwayat Dosen
    // Tambahkan kode ini di AdminController.php
    // Ganti method detailRiwayatDosen dengan kode berikut:

    public function detailRiwayatDosen($nip, Request $request)
    {
        try {
            Log::info('Memulai detailRiwayatDosen dengan NIP: ' . $nip);

            // Get perPage from request or use default
            $perPage = $request->input('per_page', 50);

            // Deteksi tabel yang tersedia
            $dosenTable = Schema::hasTable('dosens') ? 'dosens' : 'dosen';
            $bimbinganTable = Schema::hasTable('usulan_bimbingans') ? 'usulan_bimbingans' : (Schema::hasTable('bimbingan') ? 'bimbingan' : null);

            // Coba load model Dosen
            try {
                $dosen = Dosen::findOrFail($nip);

                // Load prodi relationship
                $dosen->load('prodi');

                if ($bimbinganTable) {
                    // Create paginated bimbingan directly with query builder
                    $bimbinganQuery = DB::table($bimbinganTable . ' as b')
                        ->join('mahasiswas as m', 'b.nim', '=', 'm.nim')
                        ->select(
                            'b.id',
                            'b.nim',
                            'm.nama as mahasiswa_nama',
                            'b.jenis_bimbingan',
                            'b.tanggal',
                            'b.waktu_mulai',
                            'b.waktu_selesai',
                            'b.lokasi',
                            'b.status'
                        )
                        ->where('b.nip', $nip)
                        ->whereIn('b.status', ['SELESAI', 'DISETUJUI', 'DIBATALKAN', 'DITOLAK'])
                        ->orderBy('b.tanggal', 'desc')
                        ->orderBy('b.waktu_mulai', 'desc');

                    // Apply search if provided
                    if ($request->has('search') && !empty($request->search)) {
                        $searchTerm = $request->search;
                        $bimbinganQuery->where(function ($query) use ($searchTerm) {
                            $query->where('m.nama', 'like', "%{$searchTerm}%")
                                ->orWhere('b.nim', 'like', "%{$searchTerm}%")
                                ->orWhere('b.jenis_bimbingan', 'like', "%{$searchTerm}%")
                                ->orWhere('b.status', 'like', "%{$searchTerm}%");
                        });
                    }

                    $bimbingan = $bimbinganQuery->paginate($perPage);

                    return view('bimbingan.admin.detailriwayatdosen', compact('dosen', 'bimbingan'));
                } else {
                    // Create empty paginator if no bimbingan table found
                    $bimbingan = new \Illuminate\Pagination\LengthAwarePaginator(
                        collect([]), // empty collection
                        0, // total items
                        $perPage, // per page
                        1, // current page
                        ['path' => request()->url()]
                    );

                    return view('bimbingan.admin.detailriwayatdosen', compact('dosen', 'bimbingan'));
                }
            } catch (\Exception $modelError) {
                // Jika model tidak tersedia, gunakan query builder
                Log::warning('Model Dosen error: ' . $modelError->getMessage());
                Log::info('Fallback ke DB query untuk detailRiwayatDosen');

                $dosen = DB::table($dosenTable)->where('nip', $nip)->first();

                if (!$dosen) {
                    throw new \Exception('Dosen tidak ditemukan');
                }

                // Cari data prodi jika tersedia
                $prodiTable = Schema::hasTable('prodi') ? 'prodi' : null;

                if ($prodiTable) {
                    $prodi = DB::table($prodiTable)
                        ->where('id', $dosen->prodi_id ?? 0)
                        ->first();

                    $dosen->prodi = $prodi;
                }

                if ($bimbinganTable) {
                    // Use pagination for bimbingan
                    $bimbinganQuery = DB::table($bimbinganTable . ' as b')
                        ->join('mahasiswas as m', 'b.nim', '=', 'm.nim')
                        ->select(
                            'b.id',
                            'b.nim',
                            'm.nama as mahasiswa_nama',
                            'b.jenis_bimbingan',
                            'b.tanggal',
                            'b.waktu_mulai',
                            'b.waktu_selesai',
                            'b.lokasi',
                            'b.status'
                        )
                        ->where('b.nip', $nip)
                        ->whereIn('b.status', ['SELESAI', 'DISETUJUI', 'DIBATALKAN', 'DITOLAK'])
                        ->orderBy('b.tanggal', 'desc')
                        ->orderBy('b.waktu_mulai', 'desc');

                    // Apply search if provided
                    if ($request->has('search') && !empty($request->search)) {
                        $searchTerm = $request->search;
                        $bimbinganQuery->where(function ($query) use ($searchTerm) {
                            $query->where('m.nama', 'like', "%{$searchTerm}%")
                                ->orWhere('b.nim', 'like', "%{$searchTerm}%")
                                ->orWhere('b.jenis_bimbingan', 'like', "%{$searchTerm}%")
                                ->orWhere('b.status', 'like', "%{$searchTerm}%");
                        });
                    }

                    $bimbingan = $bimbinganQuery->paginate($perPage);

                    return view('bimbingan.admin.detailriwayatdosen', compact('dosen', 'bimbingan'));
                } else {
                    // Create an empty paginator if no bimbingan table found
                    $bimbingan = new \Illuminate\Pagination\LengthAwarePaginator(
                        collect([]), // empty collection
                        0, // total items
                        $perPage, // per page
                        1, // current page
                        ['path' => request()->url()]
                    );

                    return view('bimbingan.admin.detailriwayatdosen', compact('dosen', 'bimbingan'));
                }
            }
        } catch (\Exception $e) {
            Log::error('Error di detailRiwayatDosen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal memuat riwayat dosen: ' . $e->getMessage());
        }
    }

    // Data Mahasiswa
    public function dataMahasiswa(Request $request)
    {
        try {
            Log::info('Memulai dataMahasiswa');
            $perPage = $request->input('per_page', 10);

            // Deteksi tabel yang tersedia
            $mahasiswaTable = Schema::hasTable('mahasiswas') ? 'mahasiswas' : 'mahasiswa';
            $prodiTable = Schema::hasTable('prodi') ? 'prodi' : null;
            $konsentrasiTable = Schema::hasTable('konsentrasi') ? 'konsentrasi' : null;

            // Coba load model
            try {
                $mahasiswas = Mahasiswa::with(['prodi', 'konsentrasi'])
                    ->orderBy('nim')
                    ->paginate($perPage);

                return view('bimbingan.admin.datamahasiswa', compact('mahasiswas'));
            } catch (\Exception $modelError) {
                // Jika model error, gunakan query builder
                Log::warning('Model Mahasiswa error: ' . $modelError->getMessage());
                Log::info('Fallback ke DB query untuk dataMahasiswa');

                $query = DB::table($mahasiswaTable);

                // Join dengan tabel yang tersedia
                if ($prodiTable && Schema::hasColumn($mahasiswaTable, 'prodi_id')) {
                    $query->leftJoin($prodiTable, $mahasiswaTable . '.prodi_id', '=', $prodiTable . '.id')
                        ->addSelect($prodiTable . '.nama_prodi');
                }

                if ($konsentrasiTable && Schema::hasColumn($mahasiswaTable, 'konsentrasi_id')) {
                    $query->leftJoin($konsentrasiTable, $mahasiswaTable . '.konsentrasi_id', '=', $konsentrasiTable . '.id')
                        ->addSelect($konsentrasiTable . '.nama_konsentrasi');
                }

                $query->addSelect($mahasiswaTable . '.*')
                    ->orderBy($mahasiswaTable . '.nim');

                $mahasiswas = $query->paginate($perPage);

                return view('bimbingan.admin.datamahasiswa', compact('mahasiswas'));
            }
        } catch (\Exception $e) {
            Log::error('Error di dataMahasiswa: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback ke paginator kosong
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), // kosong
                0, // total
                $perPage, // per page
                1, // halaman sekarang
                ['path' => request()->url()]
            );

            return view('bimbingan.admin.datamahasiswa', [
                'mahasiswas' => $emptyPaginator,
                'error' => 'Gagal memuat data mahasiswa: ' . $e->getMessage()
            ]);
        }
    }

    // Data Dosen
    public function dataDosen(Request $request)
    {
        try {
            Log::info('Memulai dataDosen');
            $perPage = $request->input('per_page', 10);

            // Deteksi tabel yang tersedia
            $dosenTable = Schema::hasTable('dosens') ? 'dosens' : 'dosen';
            $prodiTable = Schema::hasTable('prodi') ? 'prodi' : null;

            // Coba load model
            try {
                $dosens = Dosen::with('prodi')
                    ->orderBy('nip')
                    ->paginate($perPage);

                return view('bimbingan.admin.datadosen', compact('dosens'));
            } catch (\Exception $modelError) {
                // Jika model error, gunakan query builder
                Log::warning('Model Dosen error: ' . $modelError->getMessage());
                Log::info('Fallback ke DB query untuk dataDosen');

                $query = DB::table($dosenTable);

                // Join dengan tabel yang tersedia
                if ($prodiTable && Schema::hasColumn($dosenTable, 'prodi_id')) {
                    $query->leftJoin($prodiTable, $dosenTable . '.prodi_id', '=', $prodiTable . '.id')
                        ->addSelect($prodiTable . '.nama_prodi');
                }

                $query->addSelect($dosenTable . '.*')
                    ->orderBy($dosenTable . '.nip');

                $dosens = $query->paginate($perPage);

                return view('bimbingan.admin.datadosen', compact('dosens'));
            }
        } catch (\Exception $e) {
            Log::error('Error di dataDosen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback ke paginator kosong
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), // kosong
                0, // total
                $perPage, // per page
                1, // halaman sekarang
                ['path' => request()->url()]
            );

            return view('bimbingan.admin.datadosen', [
                'dosens' => $emptyPaginator,
                'error' => 'Gagal memuat data dosen: ' . $e->getMessage()
            ]);
        }
    }

    // Data Konsentrasi
    public function dataKonsentrasi(Request $request)
    {
        try {
            Log::info('Memulai dataKonsentrasi');
            $perPage = $request->input('per_page', 10);

            // Deteksi tabel yang tersedia
            $konsentrasiTable = Schema::hasTable('konsentrasi') ? 'konsentrasi' : null;

            if (!$konsentrasiTable) {
                throw new \Exception('Tabel konsentrasi tidak ditemukan');
            }

            // Coba load model
            try {
                $konsentrasis = Konsentrasi::orderBy('id')
                    ->paginate($perPage);

                return view('bimbingan.admin.datakonsentrasi', compact('konsentrasis'));
            } catch (\Exception $modelError) {
                // Jika model error, gunakan query builder
                Log::warning('Model Konsentrasi error: ' . $modelError->getMessage());
                Log::info('Fallback ke DB query untuk dataKonsentrasi');

                $konsentrasis = DB::table($konsentrasiTable)
                    ->orderBy('id')
                    ->paginate($perPage);

                return view('bimbingan.admin.datakonsentrasi', compact('konsentrasis'));
            }
        } catch (\Exception $e) {
            Log::error('Error di dataKonsentrasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback ke paginator kosong
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), // kosong
                0, // total
                $perPage, // per page
                1, // halaman sekarang
                ['path' => request()->url()]
            );

            return view('bimbingan.admin.datakonsentrasi', [
                'konsentrasis' => $emptyPaginator,
                'error' => 'Gagal memuat data konsentrasi: ' . $e->getMessage()
            ]);
        }
    }

    // Tambah Mahasiswa
    public function tambahMahasiswa()
    {
        try {
            Log::info('Memulai tambahMahasiswa form');

            // Deteksi tabel yang tersedia
            $prodiTable = Schema::hasTable('prodi') ? 'prodi' : null;
            $konsentrasiTable = Schema::hasTable('konsentrasi') ? 'konsentrasi' : null;
            $roleTable = Schema::hasTable('role') ? 'role' : null;

            if (!$prodiTable) {
                Log::error('Tabel prodi tidak ditemukan');
                return back()->with('error', 'Tabel program studi tidak ditemukan dalam database.');
            }

            // Ambil data prodi
            $prodis = DB::table($prodiTable)
                ->select('id', 'nama_prodi')
                ->orderBy('nama_prodi')
                ->get();

            // Ambil data konsentrasi (opsional)
            $konsentrasis = collect([]);
            if ($konsentrasiTable) {
                $konsentrasis = DB::table($konsentrasiTable)
                    ->select('id', 'nama_konsentrasi')
                    ->orderBy('nama_konsentrasi')
                    ->get();
            }

            // Ambil data role untuk mahasiswa
            $roles = collect([]);
            if ($roleTable) {
                $roles = DB::table($roleTable)
                    ->select('id', 'role_akses')
                    ->where('role_akses', 'mahasiswa')
                    ->get();
            }

            // Log jumlah data yang ditemukan
            Log::info('Data untuk form tambah mahasiswa:', [
                'prodis' => $prodis->count(),
                'konsentrasis' => $konsentrasis->count(),
                'roles' => $roles->count()
            ]);

            // Validasi apakah ada data yang diperlukan
            if ($prodis->isEmpty()) {
                Log::warning('Tidak ada data program studi');
                return back()->with('error', 'Belum ada data program studi. Silakan tambahkan program studi terlebih dahulu.');
            }

            return view('bimbingan.admin.tambahmahasiswa', compact('prodis', 'konsentrasis', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error di tambahMahasiswa: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal memuat form tambah mahasiswa: ' . $e->getMessage());
        }
    }

    // Simpan Mahasiswa
    public function simpanMahasiswa(Request $request)
    {
        try {
            Log::info('Memulai simpanMahasiswa dengan data: ', $request->except(['password', 'password_confirmation']));

            // Deteksi tabel yang benar
            $tableToCheck = Schema::hasTable('mahasiswas') ? 'mahasiswas' : 'mahasiswa';
            Log::info('Menggunakan tabel: ' . $tableToCheck);

            // Validasi input dengan pesan error kustom
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nim' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:' . $tableToCheck . ',nim',
                    'regex:/^[0-9]+$/' // Hanya angka
                ],
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    'min:2'
                ],
                'angkatan' => [
                    'required',
                    'integer'
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:' . $tableToCheck . ',email'
                ],
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'confirmed' // Ini akan mencari field password_confirmation
                ],
                'password_confirmation' => [
                    'required',
                    'string',
                    'min:6'
                ],
                'prodi_id' => [
                    'required',
                    'integer',
                    'exists:prodi,id'
                ],
                'konsentrasi_id' => [
                    'nullable',
                    'integer',
                    'exists:konsentrasi,id'
                ]
            ], [
                // Pesan error kustom
                'nim.required' => 'NIM wajib diisi.',
                'nim.unique' => 'NIM sudah digunakan oleh mahasiswa lain.',
                'nim.regex' => 'NIM hanya boleh berisi angka.',
                'nim.max' => 'NIM maksimal 20 karakter.',

                'nama.required' => 'Nama lengkap wajib diisi.',
                'nama.min' => 'Nama lengkap minimal 2 karakter.',
                'nama.max' => 'Nama lengkap maksimal 255 karakter.',

                'angkatan.required' => 'Angkatan wajib diisi.',
                'angkatan.integer' => 'Angkatan harus berupa angka.',

                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan oleh mahasiswa lain.',
                'email.max' => 'Email maksimal 255 karakter.',

                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 6 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',

                'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
                'password_confirmation.min' => 'Konfirmasi password minimal 6 karakter.',

                'prodi_id.required' => 'Program studi wajib dipilih.',
                'prodi_id.exists' => 'Program studi yang dipilih tidak valid.',

                'konsentrasi_id.exists' => 'Konsentrasi yang dipilih tidak valid.'
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ', $validator->errors()->toArray());

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data yang Anda masukkan tidak valid.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Data yang Anda masukkan tidak valid. Silakan periksa kembali.');
            }

            // Validasi tambahan - cek duplikasi dengan case-insensitive
            $existingMahasiswa = DB::table($tableToCheck)
                ->where(function ($query) use ($request) {
                    $query->whereRaw('LOWER(nim) = ?', [strtolower($request->nim)])
                        ->orWhereRaw('LOWER(email) = ?', [strtolower($request->email)]);
                })
                ->first();

            if ($existingMahasiswa) {
                $duplicateField = '';
                if (strtolower($existingMahasiswa->nim) === strtolower($request->nim)) {
                    $duplicateField = 'NIM';
                } elseif (strtolower($existingMahasiswa->email) === strtolower($request->email)) {
                    $duplicateField = 'Email';
                }

                Log::warning('Data duplikat ditemukan: ' . $duplicateField);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $duplicateField . ' sudah digunakan oleh mahasiswa lain.',
                        'errors' => [
                            strtolower($duplicateField) => [$duplicateField . ' sudah digunakan oleh mahasiswa lain.']
                        ]
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('error', $duplicateField . ' sudah digunakan oleh mahasiswa lain.');
            }

            // Mulai transaksi database
            DB::beginTransaction();

            try {
                // Buat data mahasiswa baru
                $mahasiswaData = [
                    'nim' => $request->nim,
                    'nama' => $request->nama,
                    'angkatan' => $request->angkatan,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'prodi_id' => $request->prodi_id,
                    'konsentrasi_id' => $request->konsentrasi_id,
                    'role_id' => 2, // Default role untuk mahasiswa
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Simpan menggunakan Eloquent jika tersedia, atau Query Builder
                try {
                    $mahasiswa = Mahasiswa::create($mahasiswaData);
                    $nimMahasiswa = $mahasiswa->nim;
                } catch (\Exception $modelError) {
                    // Fallback ke Query Builder
                    Log::warning('Model Mahasiswa error, menggunakan Query Builder: ' . $modelError->getMessage());

                    DB::table($tableToCheck)->insert($mahasiswaData);
                    $nimMahasiswa = $request->nim;
                }

                // Commit transaksi
                DB::commit();

                Log::info('Mahasiswa berhasil disimpan: ' . $request->nama . ' (' . $nimMahasiswa . ')');

                // Response sukses
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data mahasiswa "' . $request->nama . '" berhasil ditambahkan.',
                        'redirect' => route('admin.datamahasiswa'),
                        'data' => [
                            'nim' => $nimMahasiswa,
                            'nama' => $request->nama
                        ]
                    ], 200);
                }

                return redirect()
                    ->route('admin.datamahasiswa')
                    ->with('success', 'Data mahasiswa "' . $request->nama . '" berhasil ditambahkan.');
            } catch (\Exception $dbError) {
                // Rollback transaksi jika terjadi error
                DB::rollback();
                throw $dbError;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in simpanMahasiswa: ' . $e->getMessage(), [
                'sql' => $e->getSql() ?? 'No SQL',
                'bindings' => $e->getBindings() ?? [],
                'trace' => $e->getTraceAsString()
            ]);

            // Handle specific database errors
            $errorMessage = 'Terjadi kesalahan saat menyimpan data.';

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), 'nim') !== false) {
                    $errorMessage = 'NIM sudah digunakan oleh mahasiswa lain.';
                } elseif (strpos($e->getMessage(), 'email') !== false) {
                    $errorMessage = 'Email sudah digunakan oleh mahasiswa lain.';
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('General error in simpanMahasiswa: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    // Tambah Dosen
    public function tambahDosen()
    {
        try {
            Log::info('Memulai tambahDosen form');

            // Deteksi tabel yang tersedia
            $prodiTable = Schema::hasTable('prodi') ? 'prodi' : null;
            $roleTable = Schema::hasTable('role') ? 'role' : null;

            if (!$prodiTable) {
                Log::error('Tabel prodi tidak ditemukan');
                return back()->with('error', 'Tabel program studi tidak ditemukan dalam database.');
            }

            if (!$roleTable) {
                Log::error('Tabel role tidak ditemukan');
                return back()->with('error', 'Tabel role tidak ditemukan dalam database.');
            }

            // Ambil data prodi
            $prodis = DB::table($prodiTable)
                ->select('id', 'nama_prodi')
                ->orderBy('nama_prodi')
                ->get();

            // Ambil data role untuk dosen
            $roles = DB::table($roleTable)
                ->select('id', 'role_akses')
                ->whereIn('role_akses', ['dosen', 'koordinator_prodi'])
                ->orderBy('role_akses')
                ->get();

            // Log jumlah data yang ditemukan
            Log::info('Data untuk form tambah dosen:', [
                'prodis' => $prodis->count(),
                'roles' => $roles->count()
            ]);

            // Validasi apakah ada data yang diperlukan
            if ($prodis->isEmpty()) {
                Log::warning('Tidak ada data program studi');
                return back()->with('error', 'Belum ada data program studi. Silakan tambahkan program studi terlebih dahulu.');
            }

            if ($roles->isEmpty()) {
                Log::warning('Tidak ada role untuk dosen');
                return back()->with('error', 'Tidak ada role yang tersedia untuk dosen. Silakan hubungi administrator sistem.');
            }

            return view('bimbingan.admin.tambahdosen', compact('prodis', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error di tambahDosen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal memuat form tambah dosen: ' . $e->getMessage());
        }
    }

    // Simpan Dosen
    public function simpanDosen(Request $request)
    {
        try {
            Log::info('Memulai simpanDosen dengan data: ', $request->all());

            // Deteksi tabel yang benar
            $tableToCheck = Schema::hasTable('dosens') ? 'dosens' : 'dosen';
            Log::info('Menggunakan tabel: ' . $tableToCheck);

            // Validasi input dengan pesan error kustom
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nip' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:' . $tableToCheck . ',nip',
                    'regex:/^[0-9]+$/' // Hanya angka
                ],
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    'min:2'
                ],
                'nama_singkat' => [
                    'required',
                    'string',
                    'max:50',
                    'min:2',
                    'unique:' . $tableToCheck . ',nama_singkat'
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:' . $tableToCheck . ',email'
                ],
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'confirmed' // Ini akan mencari field password_confirmation
                ],
                'password_confirmation' => [
                    'required',
                    'string',
                    'min:6'
                ],
                'prodi_id' => [
                    'required',
                    'integer',
                    'exists:prodi,id'
                ],
                'role_id' => [
                    'required',
                    'integer',
                    'exists:role,id'
                ]
            ], [
                // Pesan error kustom
                'nip.required' => 'NIP wajib diisi.',
                'nip.unique' => 'NIP sudah digunakan oleh dosen lain.',
                'nip.regex' => 'NIP hanya boleh berisi angka.',
                'nip.max' => 'NIP maksimal 20 karakter.',

                'nama.required' => 'Nama lengkap wajib diisi.',
                'nama.min' => 'Nama lengkap minimal 2 karakter.',
                'nama.max' => 'Nama lengkap maksimal 255 karakter.',

                'nama_singkat.required' => 'Nama singkat wajib diisi.',
                'nama_singkat.unique' => 'Nama singkat sudah digunakan oleh dosen lain.',
                'nama_singkat.min' => 'Nama singkat minimal 2 karakter.',
                'nama_singkat.max' => 'Nama singkat maksimal 50 karakter.',

                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan oleh dosen lain.',
                'email.max' => 'Email maksimal 255 karakter.',

                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 6 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',

                'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
                'password_confirmation.min' => 'Konfirmasi password minimal 6 karakter.',

                'prodi_id.required' => 'Program studi wajib dipilih.',
                'prodi_id.exists' => 'Program studi yang dipilih tidak valid.',

                'role_id.required' => 'Jabatan wajib dipilih.',
                'role_id.exists' => 'Jabatan yang dipilih tidak valid.'
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ', $validator->errors()->toArray());

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data yang Anda masukkan tidak valid.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Data yang Anda masukkan tidak valid. Silakan periksa kembali.');
            }

            // Validasi tambahan - cek duplikasi dengan case-insensitive
            $existingDosen = DB::table($tableToCheck)
                ->where(function ($query) use ($request) {
                    $query->whereRaw('LOWER(nip) = ?', [strtolower($request->nip)])
                        ->orWhereRaw('LOWER(email) = ?', [strtolower($request->email)])
                        ->orWhereRaw('LOWER(nama_singkat) = ?', [strtolower($request->nama_singkat)]);
                })
                ->first();

            if ($existingDosen) {
                $duplicateField = '';
                if (strtolower($existingDosen->nip) === strtolower($request->nip)) {
                    $duplicateField = 'NIP';
                } elseif (strtolower($existingDosen->email) === strtolower($request->email)) {
                    $duplicateField = 'Email';
                } elseif (strtolower($existingDosen->nama_singkat) === strtolower($request->nama_singkat)) {
                    $duplicateField = 'Nama singkat';
                }

                Log::warning('Data duplikat ditemukan: ' . $duplicateField);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $duplicateField . ' sudah digunakan oleh dosen lain.',
                        'errors' => [
                            strtolower(str_replace(' ', '_', $duplicateField)) => [$duplicateField . ' sudah digunakan oleh dosen lain.']
                        ]
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('error', $duplicateField . ' sudah digunakan oleh dosen lain.');
            }

            // Cek apakah role yang dipilih valid untuk dosen
            $selectedRole = DB::table('role')->where('id', $request->role_id)->first();
            if (!$selectedRole || !in_array($selectedRole->role_akses, ['dosen', 'koordinator_prodi'])) {
                Log::warning('Role tidak valid untuk dosen: ' . $request->role_id);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jabatan yang dipilih tidak valid untuk dosen.',
                        'errors' => [
                            'role_id' => ['Jabatan yang dipilih tidak valid untuk dosen.']
                        ]
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('error', 'Jabatan yang dipilih tidak valid untuk dosen.');
            }

            // Mulai transaksi database
            DB::beginTransaction();

            try {
                // Buat data dosen baru
                $dosenData = [
                    'nip' => $request->nip,
                    'nama' => $request->nama,
                    'nama_singkat' => $request->nama_singkat,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'prodi_id' => $request->prodi_id,
                    'role_id' => $request->role_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Simpan menggunakan Eloquent jika tersedia, atau Query Builder
                try {
                    $dosen = Dosen::create($dosenData);
                    $nipDosen = $dosen->nip;
                } catch (\Exception $modelError) {
                    // Fallback ke Query Builder
                    Log::warning('Model Dosen error, menggunakan Query Builder: ' . $modelError->getMessage());

                    DB::table($tableToCheck)->insert($dosenData);
                    $nipDosen = $request->nip;
                }

                // Commit transaksi
                DB::commit();

                Log::info('Dosen berhasil disimpan: ' . $request->nama . ' (' . $nipDosen . ')');

                // Response sukses
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data dosen "' . $request->nama . '" berhasil ditambahkan.',
                        'redirect' => route('admin.datadosen'),
                        'data' => [
                            'nip' => $nipDosen,
                            'nama' => $request->nama
                        ]
                    ], 200);
                }

                return redirect()
                    ->route('admin.datadosen')
                    ->with('success', 'Data dosen "' . $request->nama . '" berhasil ditambahkan.');
            } catch (\Exception $dbError) {
                // Rollback transaksi jika terjadi error
                DB::rollback();
                throw $dbError;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in simpanDosen: ' . $e->getMessage(), [
                'sql' => $e->getSql() ?? 'No SQL',
                'bindings' => $e->getBindings() ?? [],
                'trace' => $e->getTraceAsString()
            ]);

            // Handle specific database errors
            $errorMessage = 'Terjadi kesalahan saat menyimpan data.';

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), 'nip') !== false) {
                    $errorMessage = 'NIP sudah digunakan oleh dosen lain.';
                } elseif (strpos($e->getMessage(), 'email') !== false) {
                    $errorMessage = 'Email sudah digunakan oleh dosen lain.';
                } elseif (strpos($e->getMessage(), 'nama_singkat') !== false) {
                    $errorMessage = 'Nama singkat sudah digunakan oleh dosen lain.';
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('General error in simpanDosen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    // Tambah Konsentrasi
    public function tambahKonsentrasi()
    {
        try {
            Log::info('Memulai tambahKonsentrasi form');

            // Cek apakah tabel konsentrasi ada
            if (!Schema::hasTable('konsentrasi')) {
                Log::error('Tabel konsentrasi tidak ditemukan');
                return back()->with('error', 'Tabel konsentrasi tidak ditemukan dalam database.');
            }

            // Cek struktur tabel konsentrasi
            $columns = Schema::getColumnListing('konsentrasi');
            Log::info('Kolom tabel konsentrasi: ' . implode(', ', $columns));

            if (!in_array('nama_konsentrasi', $columns)) {
                Log::error('Kolom nama_konsentrasi tidak ditemukan');
                return back()->with('error', 'Struktur tabel konsentrasi tidak sesuai. Kolom nama_konsentrasi tidak ditemukan.');
            }

            // Ambil data konsentrasi yang sudah ada untuk referensi (opsional)
            try {
                $existingKonsentrasi = DB::table('konsentrasi')
                    ->select('id', 'nama_konsentrasi')
                    ->orderBy('nama_konsentrasi')
                    ->limit(10)
                    ->get();

                Log::info('Konsentrasi yang sudah ada: ' . $existingKonsentrasi->count());

                return view('bimbingan.admin.tambahkonsentrasi', compact('existingKonsentrasi'));
            } catch (\Exception $queryError) {
                Log::warning('Error saat mengambil data konsentrasi existing: ' . $queryError->getMessage());

                // Tetap lanjutkan tanpa data existing
                $existingKonsentrasi = collect([]);
                return view('bimbingan.admin.tambahkonsentrasi', compact('existingKonsentrasi'));
            }
        } catch (\Exception $e) {
            Log::error('Error di tambahKonsentrasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal memuat form tambah konsentrasi: ' . $e->getMessage());
        }
    }

    // Simpan Konsentrasi
    public function simpanKonsentrasi(Request $request)
    {
        try {
            Log::info('Memulai simpanKonsentrasi dengan data: ', $request->all());

            // Validasi input dengan pesan error kustom
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nama_konsentrasi' => [
                    'required',
                    'string',
                    'max:255',
                    'min:2',
                    'unique:konsentrasi,nama_konsentrasi',
                    'regex:/^[a-zA-Z\s\-\.]+$/' // Hanya huruf, spasi, strip, dan titik
                ]
            ], [
                // Pesan error kustom
                'nama_konsentrasi.required' => 'Nama konsentrasi wajib diisi.',
                'nama_konsentrasi.unique' => 'Nama konsentrasi sudah ada.',
                'nama_konsentrasi.min' => 'Nama konsentrasi minimal 2 karakter.',
                'nama_konsentrasi.max' => 'Nama konsentrasi maksimal 255 karakter.',
                'nama_konsentrasi.regex' => 'Nama konsentrasi hanya boleh berisi huruf, spasi, tanda strip (-), dan titik (.).'
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ', $validator->errors()->toArray());

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data yang Anda masukkan tidak valid.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Data yang Anda masukkan tidak valid. Silakan periksa kembali.');
            }

            // Validasi tambahan - cek duplikasi dengan case-insensitive
            $existingKonsentrasi = DB::table('konsentrasi')
                ->whereRaw('LOWER(nama_konsentrasi) = ?', [strtolower($request->nama_konsentrasi)])
                ->first();

            if ($existingKonsentrasi) {
                Log::warning('Konsentrasi duplikat ditemukan: ' . $request->nama_konsentrasi);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nama konsentrasi sudah ada.',
                        'errors' => [
                            'nama_konsentrasi' => ['Nama konsentrasi sudah ada.']
                        ]
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('error', 'Nama konsentrasi sudah ada.');
            }

            // Mulai transaksi database
            DB::beginTransaction();

            try {
                // Clean dan format nama konsentrasi
                $namaKonsentrasi = trim($request->nama_konsentrasi);
                $namaKonsentrasi = ucwords(strtolower($namaKonsentrasi)); // Title case

                // Buat data konsentrasi baru
                $konsentrasiData = [
                    'nama_konsentrasi' => $namaKonsentrasi,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Simpan menggunakan Eloquent jika tersedia, atau Query Builder
                try {
                    $konsentrasi = Konsentrasi::create($konsentrasiData);
                    $idKonsentrasi = $konsentrasi->id;
                } catch (\Exception $modelError) {
                    // Fallback ke Query Builder
                    Log::warning('Model Konsentrasi error, menggunakan Query Builder: ' . $modelError->getMessage());

                    $idKonsentrasi = DB::table('konsentrasi')->insertGetId($konsentrasiData);
                }

                // Commit transaksi
                DB::commit();

                Log::info('Konsentrasi berhasil disimpan: ' . $namaKonsentrasi . ' (ID: ' . $idKonsentrasi . ')');

                // Response sukses
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Konsentrasi "' . $namaKonsentrasi . '" berhasil ditambahkan.',
                        'redirect' => route('admin.datakonsentrasi'),
                        'data' => [
                            'id' => $idKonsentrasi,
                            'nama_konsentrasi' => $namaKonsentrasi
                        ]
                    ], 200);
                }

                return redirect()
                    ->route('admin.datakonsentrasi')
                    ->with('success', 'Konsentrasi "' . $namaKonsentrasi . '" berhasil ditambahkan.');
            } catch (\Exception $dbError) {
                // Rollback transaksi jika terjadi error
                DB::rollback();
                throw $dbError;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in simpanKonsentrasi: ' . $e->getMessage(), [
                'sql' => $e->getSql() ?? 'No SQL',
                'bindings' => $e->getBindings() ?? [],
                'trace' => $e->getTraceAsString()
            ]);

            // Handle specific database errors
            $errorMessage = 'Terjadi kesalahan saat menyimpan data.';

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorMessage = 'Nama konsentrasi sudah ada.';
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('General error in simpanKonsentrasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    // Edit Mahasiswa
    public function editMahasiswa($nim)
    {
        try {
            Log::info('Memulai editMahasiswa dengan NIM: ' . $nim);

            // Deteksi tabel yang tersedia
            $mahasiswaTable = Schema::hasTable('mahasiswas') ? 'mahasiswas' : 'mahasiswa';
            $prodiTable = Schema::hasTable('prodi') ? 'prodi' : null;
            $konsentrasiTable = Schema::hasTable('konsentrasi') ? 'konsentrasi' : null;

            if (!$prodiTable) {
                Log::error('Tabel prodi tidak ditemukan');
                return back()->with('error', 'Tabel program studi tidak ditemukan dalam database.');
            }

            // Coba load model Mahasiswa
            try {
                $mahasiswa = Mahasiswa::findOrFail($nim);
            } catch (\Exception $modelError) {
                // Fallback ke query builder
                Log::warning('Model Mahasiswa error: ' . $modelError->getMessage());

                $mahasiswa = DB::table($mahasiswaTable)->where('nim', $nim)->first();

                if (!$mahasiswa) {
                    throw new \Exception('Mahasiswa tidak ditemukan');
                }
            }

            // Ambil data prodi
            $prodis = DB::table($prodiTable)
                ->select('id', 'nama_prodi')
                ->orderBy('nama_prodi')
                ->get();

            // Ambil data konsentrasi (opsional)
            $konsentrasis = collect([]);
            if ($konsentrasiTable) {
                $konsentrasis = DB::table($konsentrasiTable)
                    ->select('id', 'nama_konsentrasi')
                    ->orderBy('nama_konsentrasi')
                    ->get();
            }

            // Log jumlah data yang ditemukan
            Log::info('Data untuk form edit mahasiswa:', [
                'prodis' => $prodis->count(),
                'konsentrasis' => $konsentrasis->count()
            ]);

            // Validasi apakah ada data yang diperlukan
            if ($prodis->isEmpty()) {
                Log::warning('Tidak ada data program studi');
                return back()->with('error', 'Belum ada data program studi. Silakan tambahkan program studi terlebih dahulu.');
            }

            return view('bimbingan.admin.editmahasiswa', compact('mahasiswa', 'prodis', 'konsentrasis'));
        } catch (\Exception $e) {
            Log::error('Error di editMahasiswa: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal memuat form edit mahasiswa: ' . $e->getMessage());
        }
    }

    // Update Mahasiswa
    /**
     * Update Mahasiswa dengan konfirmasi dan AJAX support
     */
    public function updateMahasiswa(Request $request, $nim)
    {
        try {
            Log::info('Memulai updateMahasiswa dengan NIM: ' . $nim, $request->except(['password']));

            // Deteksi tabel yang benar
            $tableToCheck = Schema::hasTable('mahasiswas') ? 'mahasiswas' : 'mahasiswa';
            Log::info('Menggunakan tabel: ' . $tableToCheck);

            // Validasi input dengan unique yang mengecualikan record saat ini
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    'min:2'
                ],
                'angkatan' => [
                    'required',
                    'integer'
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:' . $tableToCheck . ',email,' . $nim . ',nim' // Exclude current record
                ],
                'password' => [
                    'nullable',
                    'string',
                    'min:6'
                ],
                'prodi_id' => [
                    'required',
                    'integer',
                    'exists:prodi,id'
                ],
                'konsentrasi_id' => [
                    'nullable',
                    'integer',
                    'exists:konsentrasi,id'
                ]
            ], [
                // Pesan error kustom
                'nama.required' => 'Nama lengkap wajib diisi.',
                'nama.min' => 'Nama lengkap minimal 2 karakter.',
                'nama.max' => 'Nama lengkap maksimal 255 karakter.',

                'angkatan.required' => 'Angkatan wajib diisi.',
                'angkatan.integer' => 'Angkatan harus berupa angka.',

                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan oleh mahasiswa lain.',
                'email.max' => 'Email maksimal 255 karakter.',

                'password.min' => 'Password minimal 6 karakter.',

                'prodi_id.required' => 'Program studi wajib dipilih.',
                'prodi_id.exists' => 'Program studi yang dipilih tidak valid.',

                'konsentrasi_id.exists' => 'Konsentrasi yang dipilih tidak valid.'
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ', $validator->errors()->toArray());

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data yang Anda masukkan tidak valid.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Data yang Anda masukkan tidak valid. Silakan periksa kembali.');
            }

            // Validasi tambahan - cek duplikasi dengan case-insensitive (exclude current record)
            $existingMahasiswa = DB::table($tableToCheck)
                ->where('nim', '!=', $nim) // Exclude current record
                ->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
                ->first();

            if ($existingMahasiswa) {
                Log::warning('Email duplikat ditemukan: ' . $request->email);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email sudah digunakan oleh mahasiswa lain.',
                        'errors' => [
                            'email' => ['Email sudah digunakan oleh mahasiswa lain.']
                        ]
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('error', 'Email sudah digunakan oleh mahasiswa lain.');
            }

            // Mulai transaksi database
            DB::beginTransaction();

            try {
                // Update data mahasiswa
                $updateData = [
                    'nama' => $request->nama,
                    'angkatan' => $request->angkatan,
                    'email' => $request->email,
                    'prodi_id' => $request->prodi_id,
                    'konsentrasi_id' => $request->konsentrasi_id,
                    'updated_at' => now()
                ];

                // Tambahkan password jika diisi
                if ($request->filled('password')) {
                    $updateData['password'] = Hash::make($request->password);
                }

                // Update menggunakan Eloquent jika tersedia, atau Query Builder
                try {
                    $mahasiswa = Mahasiswa::findOrFail($nim);
                    $mahasiswa->update($updateData);
                    $namaMahasiswa = $mahasiswa->nama;
                } catch (\Exception $modelError) {
                    // Fallback ke Query Builder
                    Log::warning('Model Mahasiswa error, menggunakan Query Builder: ' . $modelError->getMessage());

                    DB::table($tableToCheck)->where('nim', $nim)->update($updateData);
                    $namaMahasiswa = $request->nama;
                }

                // Commit transaksi
                DB::commit();

                Log::info('Mahasiswa berhasil diupdate: ' . $namaMahasiswa . ' (' . $nim . ')');

                // Response sukses
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data mahasiswa "' . $request->nama . '" berhasil diperbarui.',
                        'redirect' => route('admin.datamahasiswa'),
                        'data' => [
                            'nim' => $nim,
                            'nama' => $request->nama
                        ]
                    ], 200);
                }

                return redirect()
                    ->route('admin.datamahasiswa')
                    ->with('success', 'Data mahasiswa "' . $request->nama . '" berhasil diperbarui.');
            } catch (\Exception $dbError) {
                // Rollback transaksi jika terjadi error
                DB::rollback();
                throw $dbError;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in updateMahasiswa: ' . $e->getMessage(), [
                'sql' => $e->getSql() ?? 'No SQL',
                'bindings' => $e->getBindings() ?? [],
                'trace' => $e->getTraceAsString()
            ]);

            // Handle specific database errors
            $errorMessage = 'Terjadi kesalahan saat menyimpan data.';

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), 'email') !== false) {
                    $errorMessage = 'Email sudah digunakan oleh mahasiswa lain.';
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('General error in updateMahasiswa: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    // Reset password mahasiswa
    public function resetPasswordMahasiswa($nim)
    {
        try {
            Log::info('Memulai resetPasswordMahasiswa dengan NIM: ' . $nim);

            $mahasiswa = Mahasiswa::findOrFail($nim);
            Log::info('Mahasiswa ditemukan: ' . $mahasiswa->nama);

            // Reset password ke NIM
            $mahasiswa->password = Hash::make($nim);
            $mahasiswa->save();

            Log::info('Password berhasil direset untuk mahasiswa: ' . $mahasiswa->nama);

            // Log aktivitas reset password (opsional)
            Log::info('Password reset for mahasiswa: ' . $mahasiswa->nama . ' (' . $nim . ') by admin: ' . auth()->user()->name ?? 'system');

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password mahasiswa "' . $mahasiswa->nama . '" berhasil direset ke NIM.'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Password mahasiswa "' . $mahasiswa->nama . '" berhasil direset ke NIM');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Mahasiswa tidak ditemukan dengan NIM: ' . $nim);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan.'
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error di resetPasswordMahasiswa: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mereset password. Silakan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal mereset password mahasiswa: ' . $e->getMessage());
        }
    }


    // Edit Dosen
    public function editDosen($nip)
    {
        try {
            Log::info('Memulai editDosen dengan NIP: ' . $nip);

            // Deteksi tabel yang tersedia
            $dosenTable = Schema::hasTable('dosens') ? 'dosens' : 'dosen';
            $prodiTable = Schema::hasTable('prodi') ? 'prodi' : null;
            $roleTable = Schema::hasTable('role') ? 'role' : null;

            if (!$prodiTable) {
                Log::error('Tabel prodi tidak ditemukan');
                return back()->with('error', 'Tabel program studi tidak ditemukan dalam database.');
            }

            if (!$roleTable) {
                Log::error('Tabel role tidak ditemukan');
                return back()->with('error', 'Tabel role tidak ditemukan dalam database.');
            }

            // Coba load model Dosen
            try {
                $dosen = Dosen::findOrFail($nip);
            } catch (\Exception $modelError) {
                // Fallback ke query builder
                Log::warning('Model Dosen error: ' . $modelError->getMessage());

                $dosen = DB::table($dosenTable)->where('nip', $nip)->first();

                if (!$dosen) {
                    throw new \Exception('Dosen tidak ditemukan');
                }
            }

            // Ambil data prodi
            $prodis = DB::table($prodiTable)
                ->select('id', 'nama_prodi')
                ->orderBy('nama_prodi')
                ->get();

            // Ambil data role untuk dosen
            $roles = DB::table($roleTable)
                ->select('id', 'role_akses')
                ->whereIn('role_akses', ['dosen', 'koordinator_prodi'])
                ->orderBy('role_akses')
                ->get();

            // Log jumlah data yang ditemukan
            Log::info('Data untuk form edit dosen:', [
                'prodis' => $prodis->count(),
                'roles' => $roles->count()
            ]);

            // Validasi apakah ada data yang diperlukan
            if ($prodis->isEmpty()) {
                Log::warning('Tidak ada data program studi');
                return back()->with('error', 'Belum ada data program studi. Silakan tambahkan program studi terlebih dahulu.');
            }

            if ($roles->isEmpty()) {
                Log::warning('Tidak ada role untuk dosen');
                return back()->with('error', 'Tidak ada role yang tersedia untuk dosen. Silakan hubungi administrator sistem.');
            }

            return view('bimbingan.admin.editdosen', compact('dosen', 'prodis', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error di editDosen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal memuat form edit dosen: ' . $e->getMessage());
        }
    }

    // Update Dosen
    public function updateDosen(Request $request, $nip)
    {
        try {
            Log::info('Memulai updateDosen dengan NIP: ' . $nip, $request->except(['password']));

            // Deteksi tabel yang benar
            $tableToCheck = Schema::hasTable('dosens') ? 'dosens' : 'dosen';
            Log::info('Menggunakan tabel: ' . $tableToCheck);

            // Validasi input dengan unique yang mengecualikan record saat ini
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    'min:2'
                ],
                'nama_singkat' => [
                    'required',
                    'string',
                    'max:50',
                    'min:2',
                    'unique:' . $tableToCheck . ',nama_singkat,' . $nip . ',nip' // Exclude current record
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:' . $tableToCheck . ',email,' . $nip . ',nip' // Exclude current record
                ],
                'password' => [
                    'nullable',
                    'string',
                    'min:6'
                ],
                'prodi_id' => [
                    'required',
                    'integer',
                    'exists:prodi,id'
                ],
                'role_id' => [
                    'required',
                    'integer',
                    'exists:role,id'
                ]
            ], [
                // Pesan error kustom
                'nama.required' => 'Nama lengkap wajib diisi.',
                'nama.min' => 'Nama lengkap minimal 2 karakter.',
                'nama.max' => 'Nama lengkap maksimal 255 karakter.',

                'nama_singkat.required' => 'Nama singkat wajib diisi.',
                'nama_singkat.unique' => 'Nama singkat sudah digunakan oleh dosen lain.',
                'nama_singkat.min' => 'Nama singkat minimal 2 karakter.',
                'nama_singkat.max' => 'Nama singkat maksimal 50 karakter.',

                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan oleh dosen lain.',
                'email.max' => 'Email maksimal 255 karakter.',

                'password.min' => 'Password minimal 6 karakter.',

                'prodi_id.required' => 'Program studi wajib dipilih.',
                'prodi_id.exists' => 'Program studi yang dipilih tidak valid.',

                'role_id.required' => 'Jabatan wajib dipilih.',
                'role_id.exists' => 'Jabatan yang dipilih tidak valid.'
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ', $validator->errors()->toArray());

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data yang Anda masukkan tidak valid.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Data yang Anda masukkan tidak valid. Silakan periksa kembali.');
            }

            // Validasi tambahan - cek duplikasi dengan case-insensitive (exclude current record)
            $existingDosen = DB::table($tableToCheck)
                ->where('nip', '!=', $nip) // Exclude current record
                ->where(function ($query) use ($request) {
                    $query->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
                        ->orWhereRaw('LOWER(nama_singkat) = ?', [strtolower($request->nama_singkat)]);
                })
                ->first();

            if ($existingDosen) {
                $duplicateField = '';
                if (strtolower($existingDosen->email) === strtolower($request->email)) {
                    $duplicateField = 'Email';
                } elseif (strtolower($existingDosen->nama_singkat) === strtolower($request->nama_singkat)) {
                    $duplicateField = 'Nama singkat';
                }

                Log::warning('Data duplikat ditemukan: ' . $duplicateField);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $duplicateField . ' sudah digunakan oleh dosen lain.',
                        'errors' => [
                            strtolower(str_replace(' ', '_', $duplicateField)) => [$duplicateField . ' sudah digunakan oleh dosen lain.']
                        ]
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('error', $duplicateField . ' sudah digunakan oleh dosen lain.');
            }

            // Cek apakah role yang dipilih valid untuk dosen
            $selectedRole = DB::table('role')->where('id', $request->role_id)->first();
            if (!$selectedRole || !in_array($selectedRole->role_akses, ['dosen', 'koordinator_prodi'])) {
                Log::warning('Role tidak valid untuk dosen: ' . $request->role_id);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jabatan yang dipilih tidak valid untuk dosen.',
                        'errors' => [
                            'role_id' => ['Jabatan yang dipilih tidak valid untuk dosen.']
                        ]
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('error', 'Jabatan yang dipilih tidak valid untuk dosen.');
            }

            // Mulai transaksi database
            DB::beginTransaction();

            try {
                // Update data dosen
                $updateData = [
                    'nama' => $request->nama,
                    'nama_singkat' => $request->nama_singkat,
                    'email' => $request->email,
                    'prodi_id' => $request->prodi_id,
                    'role_id' => $request->role_id,
                    'updated_at' => now()
                ];

                // Tambahkan password jika diisi
                if ($request->filled('password')) {
                    $updateData['password'] = Hash::make($request->password);
                }

                // Update menggunakan Eloquent jika tersedia, atau Query Builder
                try {
                    $dosen = Dosen::findOrFail($nip);
                    $dosen->update($updateData);
                    $namaDosen = $dosen->nama;
                } catch (\Exception $modelError) {
                    // Fallback ke Query Builder
                    Log::warning('Model Dosen error, menggunakan Query Builder: ' . $modelError->getMessage());

                    DB::table($tableToCheck)->where('nip', $nip)->update($updateData);
                    $namaDosen = $request->nama;
                }

                // Commit transaksi
                DB::commit();

                Log::info('Dosen berhasil diupdate: ' . $namaDosen . ' (' . $nip . ')');

                // Response sukses
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data dosen "' . $request->nama . '" berhasil diperbarui.',
                        'redirect' => route('admin.datadosen'),
                        'data' => [
                            'nip' => $nip,
                            'nama' => $request->nama
                        ]
                    ], 200);
                }

                return redirect()
                    ->route('admin.datadosen')
                    ->with('success', 'Data dosen "' . $request->nama . '" berhasil diperbarui.');
            } catch (\Exception $dbError) {
                // Rollback transaksi jika terjadi error
                DB::rollback();
                throw $dbError;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in updateDosen: ' . $e->getMessage(), [
                'sql' => $e->getSql() ?? 'No SQL',
                'bindings' => $e->getBindings() ?? [],
                'trace' => $e->getTraceAsString()
            ]);

            // Handle specific database errors
            $errorMessage = 'Terjadi kesalahan saat menyimpan data.';

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), 'email') !== false) {
                    $errorMessage = 'Email sudah digunakan oleh dosen lain.';
                } elseif (strpos($e->getMessage(), 'nama_singkat') !== false) {
                    $errorMessage = 'Nama singkat sudah digunakan oleh dosen lain.';
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('General error in updateDosen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    // Reset password dosen
    public function resetPasswordDosen($nip)
    {
        try {
            Log::info('Memulai resetPasswordDosen dengan NIP: ' . $nip);

            $dosen = Dosen::findOrFail($nip);
            Log::info('Dosen ditemukan: ' . $dosen->nama);

            // Reset password ke NIP
            $dosen->password = Hash::make($nip);
            $dosen->save();

            Log::info('Password berhasil direset untuk dosen: ' . $dosen->nama);

            // Log aktivitas reset password (opsional)
            Log::info('Password reset for dosen: ' . $dosen->nama . ' (' . $nip . ') by admin: ' . auth()->user()->name ?? 'system');

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password dosen "' . $dosen->nama . '" berhasil direset ke NIP.'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Password dosen "' . $dosen->nama . '" berhasil direset ke NIP');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Dosen tidak ditemukan dengan NIP: ' . $nip);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan.'
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'Data dosen tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error di resetPasswordDosen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mereset password. Silakan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal mereset password dosen: ' . $e->getMessage());
        }
    }

    // Edit Konsentrasi
    public function editKonsentrasi($id)
    {
        try {
            Log::info('Memulai editKonsentrasi dengan ID: ' . $id);

            // Cek apakah tabel konsentrasi ada
            if (!Schema::hasTable('konsentrasi')) {
                Log::error('Tabel konsentrasi tidak ditemukan');
                return back()->with('error', 'Tabel konsentrasi tidak ditemukan dalam database.');
            }

            // Cek struktur tabel konsentrasi
            $columns = Schema::getColumnListing('konsentrasi');
            Log::info('Kolom tabel konsentrasi: ' . implode(', ', $columns));

            if (!in_array('nama_konsentrasi', $columns)) {
                Log::error('Kolom nama_konsentrasi tidak ditemukan');
                return back()->with('error', 'Struktur tabel konsentrasi tidak sesuai. Kolom nama_konsentrasi tidak ditemukan.');
            }

            // Coba load model Konsentrasi
            try {
                $konsentrasi = Konsentrasi::findOrFail($id);
            } catch (\Exception $modelError) {
                // Fallback ke query builder
                Log::warning('Model Konsentrasi error: ' . $modelError->getMessage());

                $konsentrasi = DB::table('konsentrasi')->where('id', $id)->first();

                if (!$konsentrasi) {
                    throw new \Exception('Konsentrasi tidak ditemukan');
                }
            }

            return view('bimbingan.admin.editkonsentrasi', compact('konsentrasi'));
        } catch (\Exception $e) {
            Log::error('Error di editKonsentrasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal memuat form edit konsentrasi: ' . $e->getMessage());
        }
    }

    // Update Konsentrasi
    public function updateKonsentrasi(Request $request, $id)
    {
        try {
            Log::info('Memulai updateKonsentrasi dengan ID: ' . $id, $request->all());

            // Validasi input dengan unique yang mengecualikan record saat ini
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nama_konsentrasi' => [
                    'required',
                    'string',
                    'max:255',
                    'min:2',
                    'unique:konsentrasi,nama_konsentrasi,' . $id, // Exclude current record
                    'regex:/^[a-zA-Z\s\-\.]+$/' // Hanya huruf, spasi, strip, dan titik
                ]
            ], [
                // Pesan error kustom
                'nama_konsentrasi.required' => 'Nama konsentrasi wajib diisi.',
                'nama_konsentrasi.unique' => 'Nama konsentrasi sudah ada.',
                'nama_konsentrasi.min' => 'Nama konsentrasi minimal 2 karakter.',
                'nama_konsentrasi.max' => 'Nama konsentrasi maksimal 255 karakter.',
                'nama_konsentrasi.regex' => 'Nama konsentrasi hanya boleh berisi huruf, spasi, tanda strip (-), dan titik (.).'
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ', $validator->errors()->toArray());

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data yang Anda masukkan tidak valid.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Data yang Anda masukkan tidak valid. Silakan periksa kembali.');
            }

            // Validasi tambahan - cek duplikasi dengan case-insensitive (exclude current record)
            $existingKonsentrasi = DB::table('konsentrasi')
                ->where('id', '!=', $id) // Exclude current record
                ->whereRaw('LOWER(nama_konsentrasi) = ?', [strtolower($request->nama_konsentrasi)])
                ->first();

            if ($existingKonsentrasi) {
                Log::warning('Konsentrasi duplikat ditemukan: ' . $request->nama_konsentrasi);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nama konsentrasi sudah ada.',
                        'errors' => [
                            'nama_konsentrasi' => ['Nama konsentrasi sudah ada.']
                        ]
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('error', 'Nama konsentrasi sudah ada.');
            }

            // Mulai transaksi database
            DB::beginTransaction();

            try {
                // Clean dan format nama konsentrasi
                $namaKonsentrasi = trim($request->nama_konsentrasi);
                $namaKonsentrasi = ucwords(strtolower($namaKonsentrasi)); // Title case

                // Update data konsentrasi
                $updateData = [
                    'nama_konsentrasi' => $namaKonsentrasi,
                    'updated_at' => now()
                ];

                // Update menggunakan Eloquent jika tersedia, atau Query Builder
                try {
                    $konsentrasi = Konsentrasi::findOrFail($id);
                    $namaKonsentrasiLama = $konsentrasi->nama_konsentrasi; // Get old name before update
                    $konsentrasi->update($updateData);
                } catch (\Exception $modelError) {
                    // Fallback ke Query Builder
                    Log::warning('Model Konsentrasi error, menggunakan Query Builder: ' . $modelError->getMessage());

                    // Get old name first
                    $oldData = DB::table('konsentrasi')->where('id', $id)->first();
                    $namaKonsentrasiLama = $oldData ? $oldData->nama_konsentrasi : 'Unknown';

                    DB::table('konsentrasi')->where('id', $id)->update($updateData);
                }

                // Commit transaksi
                DB::commit();

                Log::info('Konsentrasi berhasil diupdate: ' . $namaKonsentrasiLama . ' -> ' . $namaKonsentrasi . ' (ID: ' . $id . ')');

                // Response sukses
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Konsentrasi "' . $namaKonsentrasi . '" berhasil diperbarui.',
                        'redirect' => route('admin.datakonsentrasi'),
                        'data' => [
                            'id' => $id,
                            'nama_konsentrasi' => $namaKonsentrasi
                        ]
                    ], 200);
                }

                return redirect()
                    ->route('admin.datakonsentrasi')
                    ->with('success', 'Konsentrasi "' . $namaKonsentrasi . '" berhasil diperbarui.');
            } catch (\Exception $dbError) {
                // Rollback transaksi jika terjadi error
                DB::rollback();
                throw $dbError;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in updateKonsentrasi: ' . $e->getMessage(), [
                'sql' => $e->getSql() ?? 'No SQL',
                'bindings' => $e->getBindings() ?? [],
                'trace' => $e->getTraceAsString()
            ]);

            // Handle specific database errors
            $errorMessage = 'Terjadi kesalahan saat menyimpan data.';

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorMessage = 'Nama konsentrasi sudah ada.';
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('General error in updateKonsentrasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Menampilkan detail bimbingan
     */
    public function getDetailBimbingan($id, $origin = null)
    {
        try {
            Log::info('Admin: Memulai getDetailBimbingan dengan ID: ' . $id);

            $usulan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->join('prodi as p', 'm.prodi_id', '=', 'p.id')
                ->leftJoin('konsentrasi as k', 'm.konsentrasi_id', '=', 'k.id')
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
            $tanggal = \Carbon\Carbon::parse($usulan->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
            $waktuMulai = \Carbon\Carbon::parse($usulan->waktu_mulai)->format('H.i');
            $waktuSelesai = \Carbon\Carbon::parse($usulan->waktu_selesai)->format('H.i');

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
                'statusBadgeClass',
                'origin'
            ));
        } catch (\Exception $e) {
            Log::error('Error di admin getDetailBimbingan: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data usulan bimbingan');
        }
    }

    /**
     * Menampilkan detail riwayat bimbingan
     */
    public function getRiwayatDetail($id)
    {
        try {
            Log::info('Admin: Memulai getRiwayatDetail dengan ID: ' . $id);

            $riwayat = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->join('dosens as d', 'ub.nip', '=', 'd.nip')
                ->select(
                    'ub.*',
                    'm.nama as mahasiswa_nama',
                    'd.nama as dosen_nama'
                )
                ->where('ub.id', $id)
                ->firstOrFail();

            $tanggal = \Carbon\Carbon::parse($riwayat->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
            $waktuMulai = \Carbon\Carbon::parse($riwayat->waktu_mulai)->format('H:i');
            $waktuSelesai = \Carbon\Carbon::parse($riwayat->waktu_selesai)->format('H:i');

            // Set warna badge status
            switch ($riwayat->status) {
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
                'riwayat',
                'tanggal',
                'waktuMulai',
                'waktuSelesai',
                'statusBadgeClass'
            ));
        } catch (\Exception $e) {
            Log::error('Error di admin getRiwayatDetail: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail riwayat bimbingan');
        }
    }
    /**
     * Hapus Dosen
     * 
     * @param string $nip NIP dosen yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function hapusDosen($nip)
    {
        try {
            Log::info('Memulai hapusDosen dengan NIP: ' . $nip);

            $dosen = Dosen::findOrFail($nip);
            Log::info('Dosen ditemukan: ' . $dosen->nama);

            $namaDosen = $dosen->nama;

            // Cek apakah dosen memiliki bimbingan aktif
            $bimbinganAktif = 0;
            try {
                $bimbinganAktif = $dosen->bimbingan()
                    ->whereIn('status', ['USULAN', 'DISETUJUI'])
                    ->count();
            } catch (\Exception $bimbinganError) {
                // Jika error saat cek bimbingan, lanjutkan tanpa cek
                Log::warning('Error saat cek bimbingan aktif: ' . $bimbinganError->getMessage());
            }

            if ($bimbinganAktif > 0) {
                Log::warning('Dosen memiliki ' . $bimbinganAktif . ' bimbingan aktif');

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak dapat menghapus dosen karena masih memiliki ' . $bimbinganAktif . ' bimbingan aktif.'
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus dosen karena masih memiliki ' . $bimbinganAktif . ' bimbingan aktif');
            }

            // Hapus foto dosen jika ada
            try {
                $fotoDosen = UserPhoto::where('user_id', $nip)
                    ->where('user_type', 'dosen')
                    ->first();

                if ($fotoDosen) {
                    $fotoDosen->delete();
                    Log::info('Foto dosen berhasil dihapus');
                }
            } catch (\Exception $fotoError) {
                // Log error tapi lanjutkan proses hapus
                Log::warning('Error saat hapus foto dosen: ' . $fotoError->getMessage());
            }

            // Hapus dosen
            $dosen->delete();
            Log::info('Dosen berhasil dihapus: ' . $namaDosen);

            // Log aktivitas penghapusan
            Log::info('Dosen deleted: ' . $namaDosen . ' (' . $nip . ') by admin: ' . auth()->user()->name ?? 'system');

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data dosen "' . $namaDosen . '" berhasil dihapus.'
                ]);
            }

            return redirect()->route('admin.datadosen')
                ->with('success', 'Data dosen "' . $namaDosen . '" berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Dosen tidak ditemukan dengan NIP: ' . $nip);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan.'
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'Data dosen tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error di hapusDosen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus data dosen: ' . $e->getMessage());
        }
    }
    /**
     * Hapus Mahasiswa
     * 
     * @param string $nim NIM mahasiswa yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function hapusMahasiswa($nim)
    {
        try {
            Log::info('Memulai hapusMahasiswa dengan NIM: ' . $nim);

            $mahasiswa = Mahasiswa::findOrFail($nim);
            Log::info('Mahasiswa ditemukan: ' . $mahasiswa->nama);

            $namaMahasiswa = $mahasiswa->nama;

            // Cek apakah mahasiswa memiliki bimbingan aktif
            $bimbinganAktif = 0;
            try {
                $bimbinganAktif = $mahasiswa->bimbingan()
                    ->whereIn('status', ['USULAN', 'DISETUJUI'])
                    ->count();
            } catch (\Exception $bimbinganError) {
                // Jika error saat cek bimbingan, lanjutkan tanpa cek
                Log::warning('Error saat cek bimbingan aktif: ' . $bimbinganError->getMessage());
            }

            if ($bimbinganAktif > 0) {
                Log::warning('Mahasiswa memiliki ' . $bimbinganAktif . ' bimbingan aktif');

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak dapat menghapus mahasiswa karena masih memiliki ' . $bimbinganAktif . ' bimbingan aktif.'
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus mahasiswa karena masih memiliki ' . $bimbinganAktif . ' bimbingan aktif');
            }

            // Hapus foto mahasiswa jika ada
            try {
                $fotoMahasiswa = UserPhoto::where('user_id', $nim)
                    ->where('user_type', 'mahasiswa')
                    ->first();

                if ($fotoMahasiswa) {
                    $fotoMahasiswa->delete();
                    Log::info('Foto mahasiswa berhasil dihapus');
                }
            } catch (\Exception $fotoError) {
                // Log error tapi lanjutkan proses hapus
                Log::warning('Error saat hapus foto mahasiswa: ' . $fotoError->getMessage());
            }

            // Hapus mahasiswa
            $mahasiswa->delete();
            Log::info('Mahasiswa berhasil dihapus: ' . $namaMahasiswa);

            // Log aktivitas penghapusan
            Log::info('Mahasiswa deleted: ' . $namaMahasiswa . ' (' . $nim . ') by admin: ' . auth()->user()->name ?? 'system');

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data mahasiswa "' . $namaMahasiswa . '" berhasil dihapus.'
                ]);
            }

            return redirect()->route('admin.datamahasiswa')
                ->with('success', 'Data mahasiswa "' . $namaMahasiswa . '" berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Mahasiswa tidak ditemukan dengan NIM: ' . $nim);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan.'
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error di hapusMahasiswa: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus data mahasiswa: ' . $e->getMessage());
        }
    }
    /**
     * Hapus Konsentrasi
     * 
     * @param int $id ID konsentrasi yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function hapusKonsentrasi($id)
    {
        try {
            Log::info('Memulai hapusKonsentrasi dengan ID: ' . $id);

            $konsentrasi = Konsentrasi::findOrFail($id);
            Log::info('Konsentrasi ditemukan: ' . $konsentrasi->nama_konsentrasi);

            $namaKonsentrasi = $konsentrasi->nama_konsentrasi;

            // Cek apakah konsentrasi digunakan oleh mahasiswa
            $mahasiswaCount = 0;
            try {
                $mahasiswaCount = Mahasiswa::where('konsentrasi_id', $id)->count();
            } catch (\Exception $mahasiswaError) {
                // Jika error saat cek mahasiswa, lanjutkan tanpa cek
                Log::warning('Error saat cek mahasiswa yang menggunakan konsentrasi: ' . $mahasiswaError->getMessage());
            }

            if ($mahasiswaCount > 0) {
                Log::warning('Konsentrasi digunakan oleh ' . $mahasiswaCount . ' mahasiswa');

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak dapat menghapus konsentrasi karena masih digunakan oleh ' . $mahasiswaCount . ' mahasiswa.'
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus konsentrasi karena masih digunakan oleh ' . $mahasiswaCount . ' mahasiswa');
            }

            // Hapus konsentrasi
            $konsentrasi->delete();
            Log::info('Konsentrasi berhasil dihapus: ' . $namaKonsentrasi);

            // Log aktivitas penghapusan
            Log::info('Konsentrasi deleted: ' . $namaKonsentrasi . ' (ID: ' . $id . ') by admin: ' . auth()->user()->name ?? 'system');

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data konsentrasi "' . $namaKonsentrasi . '" berhasil dihapus.'
                ]);
            }

            return redirect()->route('admin.datakonsentrasi')
                ->with('success', 'Data konsentrasi "' . $namaKonsentrasi . '" berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Konsentrasi tidak ditemukan dengan ID: ' . $id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data konsentrasi tidak ditemukan.'
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'Data konsentrasi tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error di hapusKonsentrasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus data konsentrasi: ' . $e->getMessage());
        }
    }
    /**
     * Menampilkan halaman data admin
     */
    public function dataAdmin()
{
    try {
        Log::info('Memulai dataAdmin');
        
        // Gunakan tabel 'admin' tanpa kolom phone
        $admins = DB::table('admin as a')
            ->leftJoin('role as r', 'a.role_id', '=', 'r.id')
            ->select(
                'a.id', 
                'a.username', 
                'a.email', 
                'a.nama', 
                'a.role_id', 
                'a.created_at', 
                'a.updated_at', 
                'r.role_akses'
            )
            ->orderBy('a.id', 'asc')
            ->get();

        Log::info('Data admin berhasil dimuat, jumlah: ' . $admins->count());
        
        // Gunakan path view yang benar
        return view('bimbingan.admin.dataadmin', compact('admins'));
        
    } catch (\Exception $e) {
        Log::error('Error in dataAdmin: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()->with('error', 'Terjadi kesalahan saat memuat data admin: ' . $e->getMessage());
    }
}

/**
 * Menampilkan halaman edit admin
 */
public function editAdmin($id)
{
    try {
        // Gunakan tabel 'admin'
        $admin = DB::table('admin as a')
            ->leftJoin('role as r', 'a.role_id', '=', 'r.id')
            ->select('a.*', 'r.role_akses')
            ->where('a.id', $id)
            ->first();

        if (!$admin) {
            return redirect()->route('admin.dataadmin')->with('error', 'Data admin tidak ditemukan');
        }

        // Ambil semua role yang tersedia
        $roles = DB::table('role')
            ->select('id', 'role_akses')
            ->orderBy('id')
            ->get();

        Log::info('Form edit admin dimuat untuk ID: ' . $id);
        
        // Gunakan path view yang benar
        return view('bimbingan.admin.editadmin', compact('admin', 'roles'));
        
    } catch (\Exception $e) {
        Log::error('Error in editAdmin: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()->with('error', 'Terjadi kesalahan saat memuat data admin: ' . $e->getMessage());
    }
}

/**
 * Update data admin
 */
public function updateAdmin(Request $request, $id)
{
    try {
        // Validasi input - tanpa kolom phone
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:admin,username,' . $id,
            'email' => 'required|email|max:255|unique:admin,email,' . $id,
            'nama' => 'required|string|max:255',
            'role_id' => 'required|integer|exists:role,id',
            'password' => 'nullable|min:6|max:50'
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'nama.required' => 'Nama lengkap wajib diisi',
            'role_id.required' => 'Role wajib dipilih',
            'role_id.exists' => 'Role yang dipilih tidak valid',
            'password.min' => 'Password minimal 6 karakter',
            'password.max' => 'Password maksimal 50 karakter'
        ]);

        // Cek apakah admin exists - gunakan tabel 'admin'
        $admin = DB::table('admin')->where('id', $id)->first();
        if (!$admin) {
            Log::error('Admin tidak ditemukan dengan ID: ' . $id);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data admin tidak ditemukan'
                ], 404);
            }
            return redirect()->route('admin.dataadmin')->with('error', 'Data admin tidak ditemukan');
        }

        // Siapkan data untuk update - tanpa kolom phone
        $updateData = [
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'nama' => $validatedData['nama'],
            'role_id' => $validatedData['role_id'],
            'updated_at' => now()
        ];

        // Jika password diisi, hash dan tambahkan ke update data
        if (!empty($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
            Log::info('Password akan diupdate untuk admin ID: ' . $id);
        }

        // Update data admin - gunakan tabel 'admin'
        $updated = DB::table('admin')
            ->where('id', $id)
            ->update($updateData);

        if ($updated) {
            // Log activity
            Log::info("Data admin berhasil diupdate", [
                'admin_id' => $id,
                'admin_username' => $validatedData['username'],
                'updated_by' => Auth::guard('admin')->id() ?? 'system',
                'updated_fields' => array_keys($updateData),
                'timestamp' => now()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data admin "' . $validatedData['nama'] . '" berhasil diperbarui!',
                    'redirect' => route('admin.dataadmin')
                ]);
            }

            return redirect()->route('admin.dataadmin')
                ->with('success', 'Data admin "' . $validatedData['nama'] . '" berhasil diperbarui!');
        }

        Log::error('Gagal mengupdate admin, query tidak mempengaruhi baris apapun');
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data admin'
            ], 500);
        }

        return back()->with('error', 'Gagal mengupdate data admin')->withInput();
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validasi gagal untuk update admin', [
            'errors' => $e->validator->errors()->toArray(),
            'admin_id' => $id
        ]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->validator->errors()
            ], 422);
        }
        return back()->withErrors($e->validator)->withInput();
    } catch (\Exception $e) {
        Log::error("Error updating admin: " . $e->getMessage(), [
            'admin_id' => $id,
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data admin'
            ], 500);
        }

        return back()->with('error', 'Terjadi kesalahan saat mengupdate data admin')->withInput();
    }
}

/**
 * Reset password admin ke default
 */
public function resetPasswordAdmin(Request $request, $id)
{
    try {
        // Gunakan tabel 'admin'
        $admin = DB::table('admin')->where('id', $id)->first();

        if (!$admin) {
            Log::error('Admin tidak ditemukan untuk reset password, ID: ' . $id);
            
            return response()->json([
                'success' => false,
                'message' => 'Data admin tidak ditemukan'
            ], 404);
        }

        // Password default: admin123
        $defaultPassword = 'admin123';
        $hashedPassword = Hash::make($defaultPassword);

        Log::info('Memulai reset password untuk admin: ' . $admin->username);

        // Update password di tabel 'admin'
        $updated = DB::table('admin')
            ->where('id', $id)
            ->update([
                'password' => $hashedPassword,
                'updated_at' => now()
            ]);

        if ($updated) {
            // Log untuk security audit
            Log::info("Password admin berhasil direset", [
                'admin_id' => $id,
                'admin_username' => $admin->username,
                'admin_nama' => $admin->nama ?? 'Unknown',
                'reset_by' => Auth::guard('admin')->id() ?? 'system',
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Password admin \"{$admin->nama}\" berhasil direset ke: {$defaultPassword}"
            ]);
        }

        Log::error('Gagal mereset password admin, query tidak mempengaruhi baris apapun', [
            'admin_id' => $id
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mereset password admin'
        ], 500);
    } catch (\Exception $e) {
        Log::error("Error resetting admin password: " . $e->getMessage(), [
            'admin_id' => $id,
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mereset password'
        ], 500);
    }
}


    /**
     * Helper method untuk cek duplikasi data dengan exclude current record
     */
    private function checkDuplicateData($table, $field, $value, $excludeField, $excludeValue)
    {
        return DB::table($table)
            ->where($excludeField, '!=', $excludeValue)
            ->whereRaw("LOWER({$field}) = ?", [strtolower($value)])
            ->first();
    }

    /**
     * Helper method untuk format response error
     */
    private function formatErrorResponse($request, $message, $errors = [])
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ], 422);
        }

        return back()
            ->withErrors($errors)
            ->withInput()
            ->with('error', $message);
    }

    /**
     * Helper method untuk format response success
     */
    private function formatSuccessResponse($request, $message, $redirectRoute = null, $data = [])
    {
        if ($request->ajax()) {
            $response = [
                'success' => true,
                'message' => $message,
                'data' => $data
            ];

            if ($redirectRoute) {
                $response['redirect'] = $redirectRoute;
            }

            return response()->json($response, 200);
        }

        if ($redirectRoute) {
            return redirect($redirectRoute)->with('success', $message);
        }

        return back()->with('success', $message);
    }

    /**
     * Helper method untuk validasi role dosen
     */
    private function validateDosenRole($roleId)
    {
        $role = DB::table('role')->where('id', $roleId)->first();
        return $role && in_array($role->role_akses, ['dosen', 'koordinator_prodi']);
    }

    /**
     * Helper method untuk get table name with fallback
     */
    private function getTableName($primaryTable, $fallbackTable)
    {
        return Schema::hasTable($primaryTable) ? $primaryTable : $fallbackTable;
    }

    /**
     * Helper method untuk check if table exists
     */
    private function ensureTableExists($tableName, $description)
    {
        if (!Schema::hasTable($tableName)) {
            throw new \Exception("Tabel {$description} tidak ditemukan dalam database.");
        }
        return true;
    }

    /**
     * Helper method untuk log activity
     */
    private function logActivity($action, $entityType, $entityId, $entityName, $details = [])
    {
        Log::info("{$action} {$entityType}: {$entityName} (ID: {$entityId})", array_merge([
            'admin_user' => auth()->user()->name ?? 'system',
            'timestamp' => now(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'entity_name' => $entityName
        ], $details));
    }
}
