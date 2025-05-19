<!-- resources/views/bimbingan/mahasiswa/detaildaftar.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Daftar Bimbingan')

@push('styles')
<style>

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
    <h1 class="mb-2 gradient-text fw-bold">Detail Daftar Bimbingan</h1>
    <hr>
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ route('mahasiswa.usulanbimbingan', ['tab' => 'jadwal']) }}">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </button>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <h5 class="mb-3 fw-bold">Data Bimbingan {{ $dosen->nama }}</h5>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bimbingan as $index => $item)
                        <tr class="text-center">
                            <td>{{ ($bimbingan->currentPage() - 1) * $bimbingan->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nim }}</td>
                            <td>{{ $item->mahasiswa_nama }}</td>
                            <td>{{ $item->jenis_bimbingan }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                            <td>{{ $item->lokasi ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data bimbingan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    
            @if($bimbingan instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mt-3">
                <p class="mb-3 mb-lg-0">
                    Menampilkan {{ $bimbingan->firstItem() ?? 0 }} sampai {{ $bimbingan->lastItem() ?? 0 }} 
                    dari {{ $bimbingan->total() ?? 0 }} entri
                </p>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center justify-content-lg-end mb-0">
                        {{-- Previous Page Link --}}
                        @if ($bimbingan->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">« Sebelumnya</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $bimbingan->previousPageUrl() }}&tab=jadwal">« Sebelumnya</a>
                            </li>
                        @endif
            
                        {{-- Pagination Elements --}}
                        @if($bimbingan->lastPage() > 5)
                            {{-- First Page --}}
                            <li class="page-item {{ $bimbingan->currentPage() == 1 ? 'active' : '' }}">
                                <a class="page-link" href="{{ $bimbingan->url(1) }}&tab=jadwal">1</a>
                            </li>
                            
                            {{-- Ellipsis if not on first few pages --}}
                            @if($bimbingan->currentPage() > 3)
                                <li class="page-item disabled d-none d-sm-block">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                            
                            {{-- Current Page and Surrounding Pages --}}
                            @php
                                $start = max(2, $bimbingan->currentPage() - 1);
                                $end = min($bimbingan->lastPage() - 1, $bimbingan->currentPage() + 1);
                            @endphp
                            
                            @for($i = $start; $i <= $end; $i++)
                                <li class="page-item {{ $bimbingan->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $bimbingan->url($i) }}&tab=jadwal">{{ $i }}</a>
                                </li>
                            @endfor
                            
                            {{-- Ellipsis if not on last few pages --}}
                            @if($bimbingan->currentPage() < $bimbingan->lastPage() - 2)
                                <li class="page-item disabled d-none d-sm-block">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                            
                            {{-- Last Page --}}
                            <li class="page-item {{ $bimbingan->currentPage() == $bimbingan->lastPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $bimbingan->url($bimbingan->lastPage()) }}&tab=jadwal">{{ $bimbingan->lastPage() }}</a>
                            </li>
                        @else
                            {{-- Show all pages if few pages --}}
                            @foreach($bimbingan->getUrlRange(1, $bimbingan->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $bimbingan->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}&tab=jadwal">{{ $page }}</a>
                                </li>
                            @endforeach
                        @endif
            
                        {{-- Next Page Link --}}
                        @if ($bimbingan->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $bimbingan->nextPageUrl() }}&tab=jadwal">Selanjutnya »</a>
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
});
</script>
@endpush