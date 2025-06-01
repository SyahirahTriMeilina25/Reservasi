@extends('layouts.app')

@section('title', 'Riwayat Bimbingan Dosen')

@push('styles')
<style>
    /* Card Informasi Dosen yang Diperbaiki */
.info-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: none;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    margin-bottom: 2rem;
}

.info-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4f46e5, #7c3aed, #ec4899, #f59e0b);
    background-size: 200% 100%;
    animation: gradientMove 3s ease-in-out infinite;
}

@keyframes gradientMove {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(79, 70, 229, 0.15);
}

.info-card .card-body {
    padding: 2rem;
    background: white;
    border-radius: 0 0 16px 16px;
}

.info-card h5 {
    color: #4f46e5;
    font-weight: 700;
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    padding-bottom: 10px;
    border-bottom: 2px solid rgba(79, 70, 229, 0.1);
}

.info-card h5 i {
    color: #7c3aed;
    background: rgba(124, 58, 237, 0.1);
    padding: 8px;
    border-radius: 8px;
    font-size: 1.1rem;
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 12px 16px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 10px;
    border-left: 4px solid #4f46e5;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.info-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(90deg, rgba(79, 70, 229, 0.1), transparent);
    transition: width 0.3s ease;
}

.info-item:hover::before {
    width: 100%;
}

.info-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
    border-left-color: #7c3aed;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-label {
    font-weight: 700;
    color: #374151;
    min-width: 150px;
    flex-shrink: 0;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 1;
}

.info-value {
    color: #1e293b;
    font-weight: 600;
    margin-left: 15px;
    position: relative;
    z-index: 1;
    flex: 1;
}

/* Responsive */
@media (max-width: 768px) {
    .info-card .card-body {
        padding: 1.5rem;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    
    .info-label {
        min-width: auto;
        margin-bottom: 5px;
        text-align: left ;
    }
    
    .info-value {
        margin-left: 0;
        width: 100%;
    }
}

    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.85rem;
        background-color: rgba(255, 255, 255, 0.7);
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.2s;
        border-left: 3px solid #3b82f6;
    }

    .info-item:hover {
        background-color: rgba(255, 255, 255, 0.95);
        box-shadow: 0 3px 8px rgba(0,0,0,0.05);
        transform: translateX(3px);
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .info-label {
        font-weight: 600;
        color: #374151;
        width: 120px;
        flex-shrink: 0;
    }

    .info-value {
        color: #4b5563;
        flex: 1;
        font-weight: 500;
    }

    /* Tombol dan form */
    form .form-label {
        font-weight: bold;
    }
    
    select.form-select option {
        color: black;
        font-weight: bold;
    }

    select.form-select option:disabled {
        color: #6c757d;
    }
    
    /* Tombol info - persis seperti di file original */
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

    /* ==============================================
    8. Style search - LENGKAP DIPERBARUI
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
    @media (max-width: 992px) {
        .row .col-md-6:last-child {
            margin-top: 15px;
        }
        
        .search-container {
            max-width: 100%;
        }
        
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
    @media (max-width: 576px) {
        .info-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .info-label {
            width: 100%;
            margin-bottom: 4px;
            text-align: left;
        }
        
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
        
        .page-link {
            padding: 0.4rem 0.6rem;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Riwayat Bimbingan Dosen</h1>
    <hr>
    
    {{-- Tombol Kembali - Cek route yang tersedia --}}
    @if(Route::has('admin.dashboard'))
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ route('admin.dashboard') }}">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </button>
    @elseif(Route::has('dosen.persetujuan'))
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ route('dosen.persetujuan', ['tab' => 'pengelola']) }}">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </button>
    @else
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center" onclick="window.history.back()">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </button>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Card Informasi Dosen yang Diperbaiki -->
    <div class="card info-card">
        <div class="card-body">
            <h5 class="mb-3 fw-bold">
                <i class="bi bi-person-badge me-2"></i>Informasi Dosen
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">NIP:</span>
                        <span class="info-value">{{ $dosen->nip ?? 'Tidak tersedia' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama:</span>
                        <span class="info-value">{{ $dosen->nama ?? 'Tidak tersedia' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $dosen->email ?? 'Tidak tersedia' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Program Studi:</span>
                        <span class="info-value">{{ $dosen->prodi->nama_prodi ?? 'Tidak tersedia' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <h5 class="mb-3 fw-bold">Riwayat Bimbingan</h5>
            <hr class="mt-0 mb-3">
            
            <div class="row mb-3 align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Tampilkan</label>
                        <select class="form-select form-select-sm w-auto" id="show-entries">
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
    
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="text-center">
                        <tr>
                            <th scope="col" class="text-center align-middle">No.</th>
                            <th scope="col" class="text-center align-middle">NIM</th>
                            <th scope="col" class="text-center align-middle">Nama</th>
                            <th scope="col" class="text-center align-middle">Jenis Bimbingan</th>
                            <th scope="col" class="text-center align-middle">Tanggal</th>
                            <th scope="col" class="text-center align-middle">Waktu</th>
                            <th scope="col" class="text-center align-middle">Lokasi</th>
                            <th scope="col" class="text-center align-middle">Status</th>
                            <th scope="col" class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Adaptasi untuk kedua kemungkinan struktur data (untuk kompatibilitas)
                            $bimbinganData = isset($bimbingan) ? $bimbingan : (isset($dosen->bimbingan) ? $dosen->bimbingan : collect());
                            $isPaginated = $bimbinganData instanceof \Illuminate\Pagination\LengthAwarePaginator;
                        @endphp
                        
                        @forelse($bimbinganData as $index => $item)
                        <tr class="text-center">
                            <td>
                                @if($isPaginated)
                                    {{ ($bimbinganData->currentPage() - 1) * $bimbinganData->perPage() + $loop->iteration }}
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td>{{ $item->nim ?? '' }}</td>
                            <td>{{ $item->mahasiswa_nama ?? '' }}</td>
                            <td>{{ $item->jenis_bimbingan ?? '' }}</td>
                            <td>
                                @if(isset($item->tanggal))
                                    {{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}
                                @endif
                            </td>
                            <td>
                                @if(isset($item->waktu_mulai) && isset($item->waktu_selesai))
                                    {{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}
                                @endif
                            </td>
                            <td>{{ $item->lokasi ?? 'Lt 2 jurusan' }}</td>
                            
                            <!-- Status dengan penulisan kode yang sama persis -->
                            <td class="fw-bold {{ 
                                $item->status === 'DISETUJUI' ? 'bg-success' : (
                                    $item->status === 'DITOLAK' ? 'bg-danger' : (
                                        $item->status === 'DIBATALKAN' ? 'bg-secondary' : (
                                            $item->status === 'SELESAI' ? 'bg-primary' : 'bg-warning'
                                        )
                                    )
                                ) 
                            }} text-white">{{ $item->status ?? 'MENUNGGU' }}
                            </td>
                            
                            <!-- Tombol info dengan action-icons sesuai contoh -->
                            <td>
                                <div class="action-icons">
                                    <a href="{{ route('admin.getDetailBimbingan', ['id' => $item->id, 'origin' => 'detailriwayatdosen']) }}" 
                                        class="action-icon info-icon" data-bs-toggle="tooltip" title="Info">
                                         <i class="bi bi-info-circle"></i>
                                     </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data riwayat bimbingan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    
            @if($isPaginated && $bimbinganData->hasPages())
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mt-3">
                <p class="mb-3 mb-lg-0">
                    Menampilkan {{ $bimbinganData->firstItem() ?? 0 }} sampai {{ $bimbinganData->lastItem() ?? 0 }} 
                    dari {{ $bimbinganData->total() ?? 0 }} entri
                </p>
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0 justify-content-center justify-content-lg-end">
                        {{-- Previous Page Link --}}
                        @if ($bimbinganData->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">« Sebelumnya</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $bimbinganData->previousPageUrl() }}" rel="prev">« Sebelumnya</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                        $urlParams = request()->except('page');
                        @endphp

                        @foreach ($bimbinganData->getUrlRange(1, $bimbinganData->lastPage()) as $page => $url)
                            @php
                            $currentUrl = $url;
                            foreach($urlParams as $key => $value) {
                                $currentUrl .= (parse_url($currentUrl, PHP_URL_QUERY) ? '&' : '?') . $key . '=' . $value;
                            }
                            @endphp
                            
                            @if ($page == $bimbinganData->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $currentUrl }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($bimbinganData->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $bimbinganData->nextPageUrl() }}" rel="next">Selanjutnya »</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Selanjutnya »</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @elseif($isPaginated)
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mt-3">
                <p class="mb-3 mb-lg-0">
                    Menampilkan {{ $bimbinganData->firstItem() ?? 0 }} sampai {{ $bimbinganData->lastItem() ?? 0 }} 
                    dari {{ $bimbinganData->total() ?? 0 }} entri
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk mengganti jumlah entri yang ditampilkan
        const showEntries = document.getElementById('show-entries');
        if (showEntries) {
            showEntries.addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.delete('page'); // Reset ke halaman 1
                window.location.href = url.toString();
            });
        }
        
        // Fungsi untuk pencarian
        const searchInput = document.getElementById('searchInput');
        const clearButton = document.getElementById('clearSearch');
        const table = document.querySelector('.table');
        
        if (searchInput && clearButton && table) {
            // Set awal tombol clear
            clearButton.style.display = 'none';
            
            // Tambahkan event untuk input pencarian
            searchInput.addEventListener('input', function() {
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
            });
            
            // Event untuk tombol clear
            clearButton.addEventListener('click', function() {
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
            });
        }
        
        // Initialize tooltips jika Bootstrap 5 digunakan
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });
</script>
@endpush