@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    /* Style untuk dashboard cards */
    .dashboard-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
        overflow: hidden;
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .dashboard-card .card-top {
        color: white;
        padding: 20px;
        text-align: center;
    }
    
    /* Warna gradien untuk card statistik */
    .dashboard-card .bg-primary-gradient {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    
    .dashboard-card .bg-success-gradient {
        background: linear-gradient(135deg, #36b9cc 0%, #1a8a98 100%);
    }
    
    .dashboard-card .bg-info-gradient {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }
    
    .dashboard-card .stat-icon {
        font-size: 3rem;
        margin-bottom: 10px;
        opacity: 0.8;
    }
    
    .dashboard-card .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    
    .dashboard-card .stat-label {
        font-size: 1rem;
        opacity: 0.8;
        margin-bottom: 0;
    }
    
    .dashboard-card .card-body {
        padding: 20px;
        text-align: center;
    }
    
    .dashboard-card .btn-action {
        width: 100%;
        padding: 10px;
        font-weight: 600;
        margin-top: 10px;
        border-radius: 5px;
        transition: transform 0.2s ease;
    }
    
    .dashboard-card .btn-action:hover {
        transform: translateY(-2px);
    }
    
    /* Style untuk tabel dan komponen */
    .table {
        margin-bottom: 0;
        border-color: #f0f0f0;
        border-collapse: collapse !important;
    }
    
    .table th {
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 600;
        border-top: none;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6 !important;
        padding: 12px 10px;
    }
    
    .table td {
        vertical-align: middle;
        border: 1px solid #dee2e6 !important;
        padding: 12px 10px;
        border-color: #f0f0f0;
    }
    
    /* Style untuk card */
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px;
    }
    
    .card-header h5 {
        margin-bottom: 0;
        font-weight: 600;
        color: #333;
    }
    
    .card-body {
        padding: 20px;
    }
    
    /* Style untuk action icons */
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
    
    .page-link {
        color: #2563eb;
        border: 1px solid #e5e7eb;
        padding: 0.5rem 0.75rem;
    }
    
    .page-link:hover {
        color: #1d4ed8;
        background-color: #f3f4f6;
    }
    
    .page-item.active .page-link {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    /* Style untuk search box */
    .search-container {
        position: relative;
        width: 100%;
        transition: all 0.3s ease;
        margin-bottom: 0;
        max-width: 300px;
        margin-left: auto;
    }
    
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
    
    .search-clear {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        cursor: pointer;
        display: none;
        font-size: 14px;
        background: #e9ecef;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    
    .search-clear:hover {
        background-color: #dc3545;
        color: white;
        transform: translateY(-50%) scale(1.1);
    }

    /* Style untuk teks yang di-highlight pada pencarian */
    .highlight {
        background-color: #FFC107;
        color: #000;
        font-weight: bold;
        padding: 0 3px;
        border-radius: 2px;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .search-container {
            max-width: 100%;
        }
        
        .d-flex.justify-content-between > p {
            text-align: center;
            width: 100%;
        }
        
        .pagination {
            margin-top: 10px;
            flex-wrap: wrap;
        }
    }
    
    @media (max-width: 575.98px) {
        .search-box {
            padding: 8px 36px 8px 14px;
            font-size: 13px;
        }
        
        .search-icon, .search-clear {
            right: 14px;
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Dashboard Admin</h1>
    <hr>
    
    <!-- Kartu Statistik & Navigasi -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="dashboard-card">
                <div class="card-top bg-primary-gradient">
                    <i class="bi bi-people-fill stat-icon"></i>
                    <h2 class="stat-value">{{ isset($totalMahasiswa) ? $totalMahasiswa : 0 }}</h2>
                    <p class="stat-label">Total Mahasiswa</p>
                </div>
                <div class="card-body">
                    <p>Kelola data mahasiswa, tambah, edit, dan reset password</p>
                    <a href="{{ route('admin.datamahasiswa') }}" class="btn btn-primary btn-action">
                        <i class="bi bi-database-fill me-2"></i> Kelola Data
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="dashboard-card">
                <div class="card-top bg-success-gradient">
                    <i class="bi bi-person-video3 stat-icon"></i>
                    <h2 class="stat-value">{{ isset($totalDosen) ? $totalDosen : 0 }}</h2>
                    <p class="stat-label">Total Dosen</p>
                </div>
                <div class="card-body">
                    <p>Kelola data dosen, tambah, edit, dan reset password</p>
                    <a href="{{ route('admin.datadosen') }}" class="btn btn-info text-white btn-action">
                        <i class="bi bi-database-fill me-2"></i> Kelola Data
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="dashboard-card">
                <div class="card-top bg-info-gradient">
                    <i class="bi bi-diagram-3-fill stat-icon"></i>
                    <h2 class="stat-value">{{ isset($totalKonsentrasi) ? $totalKonsentrasi : 0 }}</h2>
                    <p class="stat-label">Total Konsentrasi</p>
                </div>
                <div class="card-body">
                    <p>Kelola data konsentrasi pada program studi</p>
                    <a href="{{ route('admin.datakonsentrasi') }}" class="btn btn-success text-white btn-action">
                        <i class="bi bi-database-fill me-2"></i> Kelola Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Jadwal Dosen -->
    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-header bg-white p-3">
            <h5 class="mb-0 fw-bold">Daftar Jadwal Dosen</h5>
        </div>
        <div class="card-body p-3">
            <div class="row mb-3 align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Tampilkan</label>
                        <select class="form-select form-select-sm w-auto" id="perPageSelectDaftarDosen" onchange="changeDaftarDosenPerPage(this.value)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="ms-2">entries</label>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="search-container">
                        <input type="text" id="searchDaftarDosen" class="search-box" placeholder="Cari daftar dosen..." autocomplete="off">
                        <i class="bi bi-search search-icon"></i>
                        <span class="search-clear" id="clearSearchDaftarDosen">×</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="text-center">
                        <tr>
                            <th>No.</th>
                            <th>NIP</th>
                            <th>Nama Dosen</th>
                            <th>Nama Singkat</th>
                            <th>Total Bimbingan Hari Ini</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($dosenList) && count($dosenList) > 0)
                            @foreach($dosenList as $index => $dosen)
                                <tr class="text-center">
                                    <td>{{ ($dosenList->currentPage() - 1) * $dosenList->perPage() + $loop->iteration }}</td>
                                    <td>{{ $dosen->nip }}</td>
                                    <td>{{ $dosen->nama }}</td>
                                    <td>{{ $dosen->nama_singkat }}</td>
                                    <td>{{ $dosen->total_bimbingan_hari_ini }}</td>
                                    <td>
                                        <div class="action-icons">
                                            <a href="{{ route('admin.detaildosen', $dosen->nip) }}"
                                                class="action-icon info-icon" data-bs-toggle="tooltip"
                                                title="Info">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data dosen</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            @if(isset($dosenList) && $dosenList instanceof \Illuminate\Pagination\LengthAwarePaginator && $dosenList->total() > 0)
            <div class="d-flex justify-content-between align-items-center mt-3">
                <p class="mb-0">
                    Menampilkan {{ $dosenList->firstItem() }} sampai {{ $dosenList->lastItem() }} dari
                    {{ $dosenList->total() }} entri
                </p>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        {{-- Tombol Sebelumnya --}}
                        @if ($dosenList->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">« Sebelumnya</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $dosenList->previousPageUrl() }}">« Sebelumnya</a>
                            </li>
                        @endif

                        {{-- Tombol Nomor Halaman --}}
                        @foreach ($dosenList->getUrlRange(1, $dosenList->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $dosenList->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        {{-- Tombol Selanjutnya --}}
                        @if ($dosenList->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $dosenList->nextPageUrl() }}">Selanjutnya »</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Selanjutnya »</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Jadwal Dosen -->
    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-header bg-white p-3">
            <h5 class="mb-0 fw-bold">Riwayat Jadwal Dosen</h5>
        </div>
        <div class="card-body p-3">
            <div class="row mb-3 align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Tampilkan</label>
                        <select class="form-select form-select-sm w-auto" id="perPageSelectRiwayat" onchange="changeRiwayatPerPage(this.value)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="ms-2">entries</label>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="search-container">
                        <input type="text" id="searchRiwayatDosen" class="search-box" placeholder="Cari riwayat dosen..." autocomplete="off">
                        <i class="bi bi-search search-icon"></i>
                        <span class="search-clear" id="clearSearchRiwayatDosen">×</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="text-center">
                        <tr>
                            <th>No.</th>
                            <th>NIP</th>
                            <th>Nama Dosen</th>
                            <th>Nama Singkat</th>
                            <th>Total Bimbingan Keseluruhan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($riwayatDosenList) && count($riwayatDosenList) > 0)
                            @foreach($riwayatDosenList as $index => $dosen)
                                <tr class="text-center">
                                    <td>{{ ($riwayatDosenList->currentPage() - 1) * $riwayatDosenList->perPage() + $loop->iteration }}</td>
                                    <td>{{ $dosen->nip }}</td>
                                    <td>{{ $dosen->nama }}</td>
                                    <td>{{ $dosen->nama_singkat }}</td>
                                    <td>{{ $dosen->total_bimbingan }}</td>
                                    <td>
                                        <div class="action-icons">
                                            <a href="{{ route('admin.detailriwayatdosen', $dosen->nip) }}"
                                                class="action-icon info-icon" data-bs-toggle="tooltip"
                                                title="Info">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data riwayat dosen</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            @if(isset($riwayatDosenList) && $riwayatDosenList instanceof \Illuminate\Pagination\LengthAwarePaginator && $riwayatDosenList->total() > 0)
            <div class="d-flex justify-content-between align-items-center mt-3">
                <p class="mb-0">
                    Menampilkan {{ $riwayatDosenList->firstItem() }} sampai {{ $riwayatDosenList->lastItem() }} dari
                    {{ $riwayatDosenList->total() }} entri
                </p>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        {{-- Tombol Sebelumnya --}}
                        @if ($riwayatDosenList->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">« Sebelumnya</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $riwayatDosenList->previousPageUrl() }}">« Sebelumnya</a>
                            </li>
                        @endif

                        {{-- Tombol Nomor Halaman --}}
                        @foreach ($riwayatDosenList->getUrlRange(1, $riwayatDosenList->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $riwayatDosenList->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        {{-- Tombol Selanjutnya --}}
                        @if ($riwayatDosenList->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $riwayatDosenList->nextPageUrl() }}">Selanjutnya »</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Selanjutnya »</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi tooltips
    function initializeTooltips() {
        if (typeof bootstrap !== 'undefined') {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => {
                if (!bootstrap.Tooltip.getInstance(tooltip)) {
                    new bootstrap.Tooltip(tooltip);
                }
            });
        }
    }

    initializeTooltips();

    // Fungsi untuk mengganti jumlah data per halaman - Daftar Dosen
    window.changeDaftarDosenPerPage = function(value) {
        window.location.href = updateQueryStringParameter(window.location.href, 'dosen_per_page', value);
    };

    // Fungsi untuk mengganti jumlah data per halaman - Riwayat Dosen
    window.changeRiwayatPerPage = function(value) {
        window.location.href = updateQueryStringParameter(window.location.href, 'riwayat_per_page', value);
    };

    // Helper function untuk update query parameter di URL
    function updateQueryStringParameter(uri, key, value) {
        const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        const separator = uri.indexOf('?') !== -1 ? "&" : "?";
        
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }

    // Implementasi pencarian untuk Daftar Dosen
    setupSearch('searchDaftarDosen', 'clearSearchDaftarDosen', 0);
    
    // Implementasi pencarian untuk Riwayat Dosen
    setupSearch('searchRiwayatDosen', 'clearSearchRiwayatDosen', 1);

    // Fungsi untuk setup pencarian
    function setupSearch(inputId, clearButtonId, tableIndex) {
        const searchInput = document.getElementById(inputId);
        const clearButton = document.getElementById(clearButtonId);
        
        if (!searchInput) return;
        
        // Get the relevant table
        const tables = document.querySelectorAll('.table-responsive table');
        if (!tables[tableIndex]) return;
        
        const table = tables[tableIndex];
        
        // Tambahkan elemen clear button jika tidak ada
        if (!clearButton) {
            const parentContainer = searchInput.parentElement;
            const newClearButton = document.createElement('span');
            newClearButton.className = 'search-clear';
            newClearButton.id = clearButtonId;
            newClearButton.innerHTML = '×';
            parentContainer.appendChild(newClearButton);
            
            // Update referensi ke clear button
            clearButton = document.getElementById(clearButtonId);
        }
        
        // Tambahkan icon search jika tidak ada
        if (!searchInput.nextElementSibling || !searchInput.nextElementSibling.classList.contains('search-icon')) {
            const searchIconElement = document.createElement('i');
            searchIconElement.className = 'bi bi-search search-icon';
            searchInput.parentElement.insertBefore(searchIconElement, searchInput.nextSibling);
        }
        
        // Handle input events
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            
            // Tampilkan/sembunyikan tombol clear
            if (clearButton) {
                clearButton.style.display = searchTerm ? 'flex' : 'none';
            }
            
            // Filter table
            const rows = table.querySelectorAll('tbody tr');
            let foundMatch = false;
            
            rows.forEach(row => {
                if (row.classList.contains('no-results-row')) {
                    row.remove();
                    return;
                }
                
                let rowMatch = false;
                const cells = row.querySelectorAll('td');
                
                cells.forEach(cell => {
                    const cellText = cell.textContent.toLowerCase();
                    if (cellText.includes(searchTerm)) {
                        rowMatch = true;
                        
                        // Highlight matched text if term is not empty
                        if (searchTerm) {
                            // Save original text if not already saved
                            if (!cell.hasAttribute('data-original')) {
                                cell.setAttribute('data-original', cell.textContent);
                            }
                            
                            // Escape special regex characters
                            const escapedSearchTerm = searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                            
                            // Highlight matched words
                            const regex = new RegExp(`(${escapedSearchTerm})`, 'gi');
                            cell.innerHTML = cell.getAttribute('data-original').replace(
                                regex, 
                                '<span class="highlight">$1</span>'
                            );
                        } else if (cell.hasAttribute('data-original')) {
                            // Restore original text if search term is empty
                            cell.textContent = cell.getAttribute('data-original');
                            cell.removeAttribute('data-original');
                        }
                    }
                });
                
                row.style.display = rowMatch || !searchTerm ? '' : 'none';
                if (rowMatch) foundMatch = true;
            });
            
            // If search term is empty, restore all text
            if (!searchTerm) {
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td[data-original]');
                    cells.forEach(cell => {
                        cell.textContent = cell.getAttribute('data-original');
                        cell.removeAttribute('data-original');
                    });
                });
            }
            
            // Show "no results" message if needed
            const tbody = table.querySelector('tbody');
            if (!foundMatch && searchTerm && tbody) {
                const existingNoResultsRow = tbody.querySelector('.no-results-row');
                if (existingNoResultsRow) {
                    existingNoResultsRow.remove();
                }
                
                const noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                const colCount = table.querySelectorAll('thead th').length;
                
                noResultsRow.innerHTML = `
                    <td colspan="${colCount}" class="text-center">
                        <i class="bi bi-search me-2"></i> Tidak ada data yang cocok dengan pencarian "${searchTerm}"
                    </td>
                `;
                
                tbody.appendChild(noResultsRow);
            }
        });
        
        // Handle clear button
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        }
    }
});
</script>
@endpush