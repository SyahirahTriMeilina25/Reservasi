@extends('layouts.app')

@section('title', 'Dashboard Bimbingan')

@push('styles')
    <style>
        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            color: #2563eb;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
        }

        .page-link:hover {
            color: #1d4ed8;
            background-color: #f3f4f6;
        }

        .action-icons {
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .action-icon {
            padding: 5px;
            border-radius: 4px;
            cursor: pointer;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s;
            text-decoration: none;
        }

        .action-icon:hover {
            opacity: 0.8;
        }

        .info-icon {
            background-color: #17a2b8;
            color: white !important;
        }
        #modalSelesai .modal-dialog {
        transform: scale(0.5);
        opacity: 0;
        transition: all 0.3s ease-in-out;
        }

        #modalSelesai.show .modal-dialog {
        transform: scale(1);
        opacity: 1;
        }

        /* Animasi untuk icon check di dalam modal */
        @keyframes pulse-check {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.15);
        }
        100% {
            transform: scale(1);
        }
        }

        #modalSelesai .bi-check-circle-fill {
        animation: pulse-check 1.5s infinite;
        }

        /* Animasi untuk tombol saat hover */
        #modalSelesai .btn {
        transition: all 0.3s ease;
        }

        #modalSelesai .btn-success:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(10, 166, 101, 0.4);
        }

        #modalSelesai .btn-secondary:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(75, 85, 99, 0.3);
        }

        /* Animasi background pada icon container */
        @keyframes glow {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
        }

        #modalSelesai .rounded-circle {
        animation: glow 2s infinite;
        transition: all 0.3s ease;
        }

    /* ==============================================
    Style search 
    ============================================== */

    /* Container untuk search box */
    .search-container {
    position: relative;
    width: 100%;
    transition: all 0.3s ease;
    margin-bottom: 0;
    max-width: 300px; /* Batasi lebar maksimal */
    margin-left: auto; /* Posisikan di kanan */
    }

    @media (max-width: 768px) {
    .search-container {
        max-width: 100%; /* Pada layar kecil, biarkan penuh */
    }
    }

    /* Input search box */
    .search-box {
    width: 100%;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 50px;
    padding: 10px 40px 10px 16px;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    font-size: 14px;
    color: #495057;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .search-box:focus {
    background-color: #fff;
    border-color: #4f46e5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    outline: none;
    }

    /* Placeholder styling */
    .search-box::placeholder {
    color: #adb5bd;
    transition: opacity 0.2s;
    }

    .search-box:focus::placeholder {
    opacity: 0.5;
    }

    /* Icon search */
    .search-icon {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    transition: all 0.3s ease;
    pointer-events: none;
    z-index: 1;
    }

    /* Aturan baru: sembunyikan icon search ketika ada input */
    .search-box:not(:placeholder-shown) + .search-icon {
    opacity: 0;
    visibility: hidden;
    }

    /* Clear button */
    .search-clear {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    cursor: pointer;
    display: none; /* Hidden by default, will be shown via JS */
    font-size: 14px;
    background: #e9ecef;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    align-items: center; /* For flex display */
    justify-content: center; /* For flex display */
    z-index: 2;
    }

    .search-clear:hover {
    background-color: #dc3545;
    color: white;
    transform: translateY(-50%) scale(1.1);
    }

    /* Styling untuk text yang di-highlight */
    .highlight {
    background-color: #FFC107 !important;
    color: #000 !important;
    font-weight: bold !important;
    padding: 0 3px !important;
    border-radius: 2px !important;
    display: inline-block !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
    }

    @keyframes pulse {
    0% { background-color: rgba(255, 193, 7, 0.3); }
    50% { background-color: rgba(255, 193, 7, 0.6); }
    100% { background-color: rgba(255, 193, 7, 0.3); }
    }

    /* Styling untuk baris "tidak ada hasil" */
    .no-results-row td {
    padding: 16px !important;
    background-color: #f8f9fa !important;
    color: #6c757d;
    font-style: italic;
    }

    .no-results-row i {
    margin-right: 8px;
    color: #6c757d;
    }

    /* Responsif untuk layar sedang */
    @media (max-width: 992px) {
    .row .col-md-6:last-child {
        margin-top: 15px;
    }
    
    .search-container {
        max-width: 100%;
    }
    }

    /* Responsif untuk layar kecil/mobile */
    @media (max-width: 576px) {
    .search-box {
        padding: 8px 36px 8px 14px;
        font-size: 13px;
    }
    
    .search-icon {
        right: 14px;
        font-size: 14px;
    }
    
    .search-clear {
        width: 18px;
        height: 18px;
        font-size: 12px;
        right: 14px;
    }
}
/* Pagination styles */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #2563eb; /* Mempertahankan warna biru */
    border: 1px solid #e5e7eb;
    padding: 0.5rem 0.75rem;
}

.page-link:hover {
    color: #1d4ed8;
    background-color: #f3f4f6;
}

.page-item.active .page-link {
    background-color: #2563eb; /* Warna biru untuk active */
    border-color: #2563eb;
    color: white;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .pagination {
        flex-wrap: wrap;
        margin-top: 10px;
    }
    
    .pagination .page-item {
        margin-bottom: 5px;
    }
    
    .d-flex.flex-column.flex-lg-row > p {
        text-align: center;
        width: 100%;
    }
}

@media (min-width: 992px) {
    .d-flex.flex-column.flex-lg-row {
        align-items: center;
    }
    
    .d-flex.flex-column.flex-lg-row > p {
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .pagination {
        margin-left: 15px;
    }
}

/* Mobile optimization */
@media (max-width: 575.98px) {
    .page-link {
        padding: 0.4rem 0.6rem;
        font-size: 0.9rem;
    }
}
    </style>
@endpush

@section('content')
    <div class="container mt-5">
        <h1 class="mb-2 gradient-text fw-bold">Usulan Bimbingan</h1>
        <hr>
        <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
            <a href="/pilihjadwal">
                <i class="bi bi-plus-lg me-2"></i>Jadwal Bimbingan
            </a>
        </button>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs" id="bimbinganTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('mahasiswa.usulanbimbingan', ['tab' => 'usulan', 'per_page' => request('per_page', 50)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'usulan' ? 'active' : '' }}">
                            Bimbingan
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('mahasiswa.usulanbimbingan', ['tab' => 'jadwal', 'per_page' => request('per_page', 50)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'jadwal' ? 'active' : '' }}">
                            Jadwal
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('mahasiswa.usulanbimbingan', ['tab' => 'riwayat', 'per_page' => request('per_page', 50)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'riwayat' ? 'active' : '' }}">
                            Riwayat
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="row mb-3 align-items-center">
                    <div class="col-lg-6 col-md-6">
                        <div class="d-flex align-items-center">
                            <label class="me-2">Tampilkan</label>
                            <select class="form-select form-select-sm w-auto"
                            onchange="window.location.href='{{ route('mahasiswa.usulanbimbingan', ['tab' => $activeTab]) }}&per_page=' + this.value">
                                <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                            </select>
                            <label class="ms-2">entries</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="search-container">
                            <input type="text" id="searchInput" class="search-box" placeholder="Cari data..." autocomplete="off" aria-label="Cari data">
                            <i class="bi bi-search search-icon"></i>
                            <span class="search-clear" id="clearSearch">×</span>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="bimbinganTabContent">
                    @if ($activeTab == 'usulan')
                        <div class="tab-pane fade show active" id="usulan" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Jenis Bimbingan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Lokasi</th>
                                            <th>Antrian</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($usulan as $index => $item)
                                            <tr class="text-center">
                                                <td>{{ ($usulan->currentPage() - 1) * $usulan->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $item->nim }}</td>
                                                <td>{{ $item->mahasiswa_nama }}</td>
                                                <td>{{ ucfirst($item->jenis_bimbingan) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                                <td>{{ $item->lokasi ?? '-' }}</td>
                                                <td>{{ $item->nomor_antrian ?? '-' }}</td>
                                                <td class="fw-bold bg-{{ $item->status === 'DISETUJUI' ? 'success' : ($item->status === 'DITOLAK' ? 'danger' : ($item->status === 'DIBATALKAN' ? 'secondary' : 'warning')) }} text-white">
                                                    {{ $item->status }}
                                                </td>
                                                <td>
                                                    @if ($item->status === 'DISETUJUI')
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <button class="btn btn-sm btn-success selesai-btn"
                                                                data-id="{{ $item->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#modalSelesai" title="Selesai">
                                                                <i class="bi bi-check2-circle"></i>
                                                            </button>

                                                            <div class="action-icons">
                                                                <a href="{{ route('mahasiswa.aksiInformasi', $item->id) }}"
                                                                    class="action-icon info-icon" data-bs-toggle="tooltip"
                                                                    title="Info">
                                                                    <i class="bi bi-info-circle"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="action-icons">
                                                            <a href="{{ route('mahasiswa.aksiInformasi', ['id' => $item->id, 'origin' => 'usulan']) }}" 
                                                                class="action-icon info-icon" data-bs-toggle="tooltip" title="Info">
                                                                 <i class="bi bi-info-circle"></i>
                                                             </a>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data usulan bimbingan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($activeTab == 'jadwal')
                        <div class="tab-pane fade show active" id="jadwal" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIP</th>
                                            <th>Nama Dosen</th>
                                            <th>Total Bimbingan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($daftarDosen as $index => $dosen)
                                            <tr class="text-center">
                                                <td>{{ ($daftarDosen->currentPage() - 1) * $daftarDosen->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $dosen->nip }}</td>
                                                <td>{{ $dosen->nama }}</td>
                                                <td>{{ $dosen->total_bimbingan }}</td>
                                                <td>
                                                    <div class="action-icons">
                                                        <a href="{{ route('mahasiswa.detaildaftar', ['nip' => $dosen->nip, 'origin' => 'jadwal']) }}" 
                                                            class="action-icon info-icon" data-bs-toggle="tooltip" title="Info">
                                                             <i class="bi bi-info-circle"></i>
                                                         </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data dosen</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($activeTab == 'riwayat')
                        <div class="tab-pane fade show active" id="riwayat" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Jenis Bimbingan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Lokasi</th>
                                            <th>Antrian</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($riwayat as $index => $item)
                                            <tr class="text-center">
                                                <td>{{ ($riwayat->currentPage() - 1) * $riwayat->perPage() + $loop->iteration }}</td>
                                                <td>{{ $item->nim }}</td>
                                                <td>{{ $item->mahasiswa_nama }}</td>
                                                <td>{{ ucfirst($item->jenis_bimbingan) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                                <td>{{ $item->lokasi && trim($item->lokasi) !== '' ? $item->lokasi : '-' }}
                                                </td>
                                                <td>{{ $item->nomor_antrian ?? '-' }}</td>
                                                <td class="fw-bold {{ 
                                                    $item->status === 'DISETUJUI' ? 'bg-success' : (
                                                        $item->status === 'DITOLAK' ? 'bg-danger' : (
                                                            $item->status === 'DIBATALKAN' ? 'bg-secondary' : (
                                                                $item->status === 'SELESAI' ? 'bg-primary' : 'bg-warning'
                                                            )
                                                        )
                                                    ) 
                                                }} text-white">{{ $item->status }}</td>
                                                <td>
                                                    <div class="action-icons">
                                                        <a href="{{ route('mahasiswa.aksiInformasi', ['id' => $item->id, 'origin' => 'riwayat']) }}" 
                                                            class="action-icon info-icon" data-bs-toggle="tooltip" title="Info">
                                                             <i class="bi bi-info-circle"></i>
                                                         </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada riwayat bimbingan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mt-3">
                    <p class="mb-3 mb-lg-0">
                        @if ($activeTab == 'usulan' && $usulan->total() > 0)
                            Menampilkan {{ $usulan->firstItem() }} sampai {{ $usulan->lastItem() }} dari {{ $usulan->total() }} entri
                        @elseif($activeTab == 'jadwal' && $daftarDosen->total() > 0)
                            Menampilkan {{ $daftarDosen->firstItem() }} sampai {{ $daftarDosen->lastItem() }} dari {{ $daftarDosen->total() }} entri
                        @elseif($activeTab == 'riwayat' && $riwayat->total() > 0)
                            Menampilkan {{ $riwayat->firstItem() }} sampai {{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} entri
                        @endif
                    </p>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center justify-content-lg-end mb-0">
                            {{-- Previous Page for USULAN tab --}}
                            @if ($activeTab == 'usulan')
                                @if ($usulan->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Sebelumnya</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $usulan->previousPageUrl() }}&tab=usulan">« Sebelumnya</a>
                                    </li>
                                @endif
                
                                {{-- Page Numbers for USULAN tab --}}
                                @foreach ($usulan->getUrlRange(1, $usulan->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $usulan->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}&tab=usulan">{{ $page }}</a>
                                    </li>
                                @endforeach
                
                                {{-- Next Page for USULAN tab --}}
                                @if ($usulan->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $usulan->nextPageUrl() }}&tab=usulan">Selanjutnya »</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Selanjutnya »</span>
                                    </li>
                                @endif
                                
                            {{-- Previous Page for JADWAL tab --}}
                            @elseif($activeTab == 'jadwal')
                                @if ($daftarDosen->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Sebelumnya</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $daftarDosen->previousPageUrl() }}&tab=jadwal">« Sebelumnya</a>
                                    </li>
                                @endif
                
                                {{-- Page Numbers for JADWAL tab --}}
                                @foreach ($daftarDosen->getUrlRange(1, $daftarDosen->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $daftarDosen->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}&tab=jadwal">{{ $page }}</a>
                                    </li>
                                @endforeach
                
                                {{-- Next Page for JADWAL tab --}}
                                @if ($daftarDosen->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $daftarDosen->nextPageUrl() }}&tab=jadwal">Selanjutnya »</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Selanjutnya »</span>
                                    </li>
                                @endif
                                
                            {{-- Previous Page for RIWAYAT tab --}}
                            @elseif($activeTab == 'riwayat')
                                @if ($riwayat->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Sebelumnya</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $riwayat->previousPageUrl() }}&tab=riwayat">« Sebelumnya</a>
                                    </li>
                                @endif
                
                                {{-- Page Numbers for RIWAYAT tab --}}
                                @foreach ($riwayat->getUrlRange(1, $riwayat->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $riwayat->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}&tab=riwayat">{{ $page }}</a>
                                    </li>
                                @endforeach
                
                                {{-- Next Page for RIWAYAT tab --}}
                                @if ($riwayat->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $riwayat->nextPageUrl() }}&tab=riwayat">Selanjutnya »</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Selanjutnya »</span>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Selesai -->
    <div class="modal fade" id="modalSelesai" tabindex="-1" aria-labelledby="modalSelesaiLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow border-0">
                <div class="modal-header border-0 bg-success text-white">
                    <h5 class="modal-title fw-bold" id="modalSelesaiLabel">
                        Konfirmasi Selesai Bimbingan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="d-flex justify-content-center mb-4">
                        <div class="rounded-circle bg-success bg-opacity-10" style="width: 90px; height: 90px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 42px;"></i>
                        </div>
                    </div>
                    <p class="mb-1">Apakah Anda yakin sesi bimbingan ini telah selesai?</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-success px-4" id="confirmSelesai">
                        Ya, Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
    // Variabel untuk menyimpan ID bimbingan yang akan diselesaikan
    let currentSelesaiId = null;
    
    // Inisialisasi modal dengan Bootstrap
    const bsModalSelesai = new bootstrap.Modal(document.getElementById('modalSelesai'));

    // Setup handler untuk semua tombol selesai
    document.querySelectorAll('.selesai-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Ambil ID dari atribut data-id pada tombol
            currentSelesaiId = this.getAttribute('data-id');
            console.log('Button selesai diklik, ID:', currentSelesaiId);
            
            // Ambil informasi baris dari tabel
            try {
                const row = this.closest('tr');
                if (row) {
                    const mahasiswaNama = row.querySelector('td:nth-child(3)').textContent.trim();
                    const jenisBimbingan = row.querySelector('td:nth-child(4)').textContent.trim();
                    
                    // Update isi modal dengan informasi kontekstual
                    const mhsNameConfirm = document.getElementById('mhs-name-confirm');
                    const jenisBimbinganConfirm = document.getElementById('jenis-bimbingan-confirm');
                    
                    if (mhsNameConfirm) mhsNameConfirm.textContent = mahasiswaNama;
                    if (jenisBimbinganConfirm) jenisBimbinganConfirm.textContent = jenisBimbingan;
                }
            } catch (error) {
                console.error('Error saat mengambil data baris:', error);
            }
            
            // Tampilkan modal konfirmasi
            bsModalSelesai.show();
        });
    });

    // Handler untuk tombol konfirmasi pada modal
    const confirmSelesaiBtn = document.getElementById('confirmSelesai');
    if (confirmSelesaiBtn) {
        confirmSelesaiBtn.addEventListener('click', async function() {
            if (!currentSelesaiId) {
                console.error('ID tidak valid');
                return;
            }

            try {
                // Tutup modal konfirmasi
                bsModalSelesai.hide();

                // Tampilkan loading state dengan SweetAlert
                Swal.fire({
                    title: 'Memproses',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Kirim request ke server
                const response = await fetch(`/usulanbimbingan/selesai/${currentSelesaiId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Server response error: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Tampilkan notifikasi sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Bimbingan telah diselesaikan',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Reload halaman setelah sukses
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak dapat memproses permintaan',
                    text: error.message || 'Silakan coba beberapa saat lagi',
                    confirmButtonColor: '#1a73e8'
                });
            }
        });
    }

    // Reset ID ketika modal ditutup
    const modalElement = document.getElementById('modalSelesai');
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function() {
            currentSelesaiId = null;
        });
    }
    // FITUR PENCARIAN - Perbaikan
    // Inisialisasi pencarian untuk tab yang aktif saat ini 
    function initializeSearch() {
        const activeTab = document.querySelector('.tab-pane.active');
        if (!activeTab) return;
        
        const searchInput = document.getElementById('searchInput');
        const clearButton = document.getElementById('clearSearch');
        const table = activeTab.querySelector('table');
        
        if (searchInput && clearButton && table) {
            initializeSearchForTable(searchInput, clearButton, table);
        }
    }
    
    // Panggil fungsi inisialisasi pencarian saat halaman dimuat
    initializeSearch();
    
    // Tambahkan event listener untuk perubahan tab
    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.addEventListener('click', function() {
            // Tunggu sesaat sampai tab aktif benar-benar diubah
            setTimeout(initializeSearch, 100);
        });
    });
});

// Fungsi untuk menginisialisasi pencarian pada tabel (pindahkan ke luar closure utama)
function initializeSearchForTable(searchInput, clearButton, table) {
    // Pastikan semua elemen ada
    if (!searchInput || !clearButton || !table) return;
    
    // Reset status pencarian
    searchInput.value = '';
    clearButton.style.display = 'none';
    
    // Kembalikan semua konten sel asli
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            if (cell.hasAttribute('data-original')) {
                cell.textContent = cell.getAttribute('data-original');
                cell.removeAttribute('data-original');
            }
        });
        row.style.display = '';
    });
    
    // Hapus event listener lama (jika ada)
    searchInput.removeEventListener('input', handleSearchInput);
    clearButton.removeEventListener('click', handleClearSearch);
    
    // Tambahkan event untuk input pencarian
    searchInput.addEventListener('input', handleSearchInput);
    
    // Event untuk tombol clear
    clearButton.addEventListener('click', handleClearSearch);
    
    function handleSearchInput() {
        const searchTerm = this.value.toLowerCase().trim();
        
        // Tampilkan/sembunyikan tombol clear
        clearButton.style.display = searchTerm ? 'flex' : 'none';
        
        // Sembunyikan icon search jika ada input
        const searchIcon = this.nextElementSibling;
        if (searchIcon && searchIcon.classList.contains('search-icon')) {
            searchIcon.style.opacity = searchTerm ? '0' : '1';
            searchIcon.style.visibility = searchTerm ? 'hidden' : 'visible';
        }
        
        // Filter tabel
        const rows = table.querySelectorAll('tbody tr');
        let matchFound = false;
        
        // Hapus pesan tidak ditemukan jika ada
        const existingNoResults = table.querySelector('.no-results-row');
        if (existingNoResults) {
            existingNoResults.remove();
        }
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let rowMatch = false;
            
            cells.forEach(cell => {
                const cellText = cell.textContent;
                const lowerCellText = cellText.toLowerCase();
                
                // Jika sel berisi istilah pencarian
                if (lowerCellText.includes(searchTerm)) {
                    rowMatch = true;
                    
                    // Hanya tambahkan highlighting jika kita memiliki kata pencarian
                    if (searchTerm) {
                        // Simpan teks asli sebelum kita memodifikasinya
                        if (!cell.hasAttribute('data-original')) {
                            cell.setAttribute('data-original', cellText);
                        }
                        
                        // Escape karakter khusus regex
                        const escapedSearchTerm = searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        
                        // Highlight kata yang cocok
                        const regex = new RegExp(`(${escapedSearchTerm})`, 'gi');
                        const highlightedText = cell.getAttribute('data-original').replace(regex, 
                            '<span class="highlight">$1</span>'
                        );
                        
                        // Perbarui HTML
                        cell.innerHTML = highlightedText;
                    }
                } else if (cell.hasAttribute('data-original') && searchTerm) {
                    // Kembalikan ke teks asli jika sel ini tidak lagi cocok
                    cell.textContent = cell.getAttribute('data-original');
                    cell.removeAttribute('data-original');
                }
            });
            
            // Tampilkan/sembunyikan baris berdasarkan kecocokan
            if (rowMatch || !searchTerm) {
                row.style.display = '';
                matchFound = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Jika pencarian dihapus, kembalikan semua sel ke teks asli
        if (!searchTerm) {
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach(cell => {
                    if (cell.hasAttribute('data-original')) {
                        cell.textContent = cell.getAttribute('data-original');
                        cell.removeAttribute('data-original');
                    }
                });
            });
        }
        
        // Tampilkan pesan jika tidak ada hasil
        if (!matchFound && searchTerm) {
            const tbody = table.querySelector('tbody');
            if (tbody) {
                const colCount = table.querySelectorAll('thead th').length;
                const noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="${colCount}" class="text-center py-3">
                        <i class="bi bi-search me-2"></i> Tidak ada data yang cocok dengan pencarian "${searchTerm}"
                    </td>
                `;
                tbody.appendChild(noResultsRow);
            }
        }
    }
    
    function handleClearSearch() {
        searchInput.value = '';
        
        // Kembalikan semua konten sel asli
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                if (cell.hasAttribute('data-original')) {
                    cell.textContent = cell.getAttribute('data-original');
                    cell.removeAttribute('data-original');
                }
            });
            row.style.display = ''; // Tampilkan semua baris
        });
        
        // Hapus pesan "tidak ada hasil" jika ada
        const existingNoResults = table.querySelector('.no-results-row');
        if (existingNoResults) {
            existingNoResults.remove();
        }
        
        // Sembunyikan tombol clear dan kembalikan ikon pencarian
        this.style.display = 'none';
        const searchIcon = searchInput.nextElementSibling;
        if (searchIcon && searchIcon.classList.contains('search-icon')) {
            searchIcon.style.opacity = '1';
            searchIcon.style.visibility = 'visible';
        }
        
        searchInput.focus();
    }
}
    </script>
@endpush
@endsection
