@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@push('styles')
<style>
    /* Action Icons */
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

    .edit-icon {
        background-color: #ffc107;
        color: white !important;
    }

    .reset-icon {
        background-color: #6c757d;
        color: white !important;
    }
    
    .delete-icon {
        background-color: #dc3545;
        color: white !important;
    }

    /* Search Container Styling */
    .search-container {
        position: relative;
        width: 100%;
        transition: all 0.3s ease;
        margin-bottom: 0;
        max-width: 300px;
        margin-left: auto;
    }

    @media (max-width: 768px) {
        .search-container {
            max-width: 100%;
        }
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

    .highlight {
        background-color: #FFC107 !important;
        color: #000 !important;
        font-weight: bold !important;
        padding: 0 3px !important;
        border-radius: 2px !important;
        display: inline-block !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
    }

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

    @media (max-width: 992px) {
        .row .col-md-6:last-child {
            margin-top: 15px;
        }
        
        .search-container {
            max-width: 100%;
        }
    }

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

    @media (max-width: 575.98px) {
        .page-link {
            padding: 0.4rem 0.6rem;
            font-size: 0.9rem;
        }
    }

    /* Form styling */
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
    
    /* Modal styling */
    .modal-header {
        border-bottom: none;
        padding-bottom: 0.5rem;
    }
    
    .modal-footer {
        border-top: none;
        padding-top: 0.5rem;
    }

    .modal-dialog {
    max-width: 500px;
}

.modal-content {
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border: none;
    overflow: hidden;
}

.modal-header {
    padding: 1.5rem 1.5rem 0.75rem;
    align-items: center;
    border-bottom: none;
    text-align: center;
    justify-content: center;
    position: relative;
}

.modal-header .modal-title {
    font-weight: 700;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.modal-header .btn-close {
    position: absolute;
    right: 1.25rem;
    top: 1.25rem;
    padding: 0.75rem;
    margin: 0;
    opacity: 0.6;
    transition: opacity 0.2s;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

.modal-body {
    padding: 1.5rem;
    text-align: center;
}

.modal-body .d-flex {
    flex-direction: column;
    align-items: center;
    margin-bottom: 1.5rem;
}

.modal-body .rounded-circle {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-bottom: 1.5rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(0, 0, 0, 0.1);
    }
    70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(0, 0, 0, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
    }
}

.modal-body p {
    margin-bottom: 0.75rem;
}

.modal-body .fw-semibold {
    font-size: 1.15rem;
}

.modal-footer {
    border-top: none;
    padding: 0 1.5rem 1.5rem;
    justify-content: center; /* Tombol tengah */
    gap: 1rem; /* Jarak antar tombol */
}

/* Style Tombol di Modal */
.modal-footer .btn {
    border-radius: 50px;
    padding: 0.6rem 1.5rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    min-width: 150px;
}

.modal-footer .btn i {
    margin-right: 0.5rem;
    animation: iconWiggle 2s infinite;
}

@keyframes iconWiggle {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
    20%, 40%, 60%, 80% { transform: translateX(2px); }
}

.modal-footer .btn-outline-secondary {
    border-color: #dee2e6;
    color: #6c757d;
}

.modal-footer .btn-outline-secondary:hover {
    background-color: #f8f9fa;
    color: #495057;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.modal-footer .btn-primary {
    background: linear-gradient(45deg, #4f46e5, #2563eb, #3b82f6);
    background-size: 200% 200%;
    border: none;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
}

.modal-footer .btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(37, 99, 235, 0.25);
}

.modal-footer .btn-danger {
    background: linear-gradient(45deg, #dc3545, #b91c1c, #ef4444);
    background-size: 200% 200%;
    border: none;
    box-shadow: 0 4px 10px rgba(220, 53, 69, 0.2);
}

.modal-footer .btn-danger:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(220, 53, 69, 0.25);
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Icon styling */
.modal-body .rounded-circle.bg-warning {
    background-color: rgba(255, 193, 7, 0.15) !important;
    border: 2px solid rgba(255, 193, 7, 0.3);
}

.modal-body .rounded-circle.bg-danger {
    background-color: rgba(220, 53, 69, 0.15) !important;
    border: 2px solid rgba(220, 53, 69, 0.3);
}

.modal-body .text-warning {
    color: #ffc107 !important;
    animation: fadeInOut 2s infinite;
}

.modal-body .text-danger {
    color: #dc3545 !important;
    animation: fadeInOut 2s infinite;
}

@keyframes fadeInOut {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Perhatian text styling */
.modal-body .text-danger.small {
    font-size: 0.85rem;
    font-style: italic;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 1rem !important;
    padding: 0.5rem;
    background-color: rgba(220, 53, 69, 0.05);
    border-radius: 4px;
    animation: flashWarning 2s infinite;
}

@keyframes flashWarning {
    0%, 100% { background-color: rgba(220, 53, 69, 0.05); }
    50% { background-color: rgba(220, 53, 69, 0.1); }
}

.modal-body .text-danger.small::before {
    content: "\F33A"; /* Exclamation icon from Bootstrap Icons */
    font-family: "bootstrap-icons";
    margin-right: 0.5rem;
    font-size: 0.9rem;
}

/* Animation for modal */
.modal.fade .modal-dialog {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    transform: scale(0.8) translateY(-20px);
    opacity: 0;
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Modal backdrop custom styling */
.modal-backdrop.show {
    opacity: 0.5;
    animation: fadeIn 0.3s forwards;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 0.5; }
}

/* Additional animations for icon and text in modal */
.modal-header i {
    animation: spinPulse 1.5s ease-in-out infinite;
}

@keyframes spinPulse {
    0% { transform: rotate(0deg) scale(1); }
    50% { transform: rotate(10deg) scale(1.2); }
    100% { transform: rotate(0deg) scale(1); }
}
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Data Mahasiswa</h1>
    <hr>
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ route('admin.tambahmahasiswa') }}">
            <i class="bi bi-plus-lg me-2"></i>Tambah Mahasiswa
        </a>
    </button>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <h5 class="mb-3 fw-bold">Data Mahasiswa</h5>
            <hr class="mt-0 mb-3">
            <div class="row mb-3 align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Tampilkan</label>
                        <select class="form-select form-select-sm w-auto" id="perPageSelect" 
                            onchange="changePerPage(this.value)">
                            <option value="10" {{ request('per_page', 50) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 50) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 50) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <label class="ms-2">entries</label>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="search-container">
                        <input type="text" id="searchInput" class="search-box" 
                               placeholder="Cari data..." 
                               autocomplete="off" 
                               aria-label="Cari data">
                        <i class="bi bi-search search-icon"></i>
                        <span class="search-clear" id="clearSearch">×</span>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="text-center">
                        <tr>
                            <th scope="col" class="text-center align-middle">No</th>
                            <th scope="col" class="text-center align-middle">NIM</th>
                            <th scope="col" class="text-center align-middle">Nama</th>
                            <th scope="col" class="text-center align-middle">Email</th>
                            <th scope="col" class="text-center align-middle">Angkatan</th>
                            <th scope="col" class="text-center align-middle">Program Studi</th>
                            <th scope="col" class="text-center align-middle">Konsentrasi</th>
                            <th scope="col" class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswas as $index => $mahasiswa)
                        <tr class="text-center">
                            <td>{{ ($mahasiswas->currentPage() - 1) * $mahasiswas->perPage() + $loop->iteration }}</td>
                            <td>{{ $mahasiswa->nim }}</td>
                            <td>{{ $mahasiswa->nama }}</td>
                            <td>{{ $mahasiswa->email }}</td>
                            <td>{{ $mahasiswa->angkatan }}</td>
                            <td>{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
                            <td>{{ $mahasiswa->konsentrasi->nama_konsentrasi ?? '-' }}</td>
                            <td>
                                <div class="action-icons">
                                    <a href="{{ route('admin.editmahasiswa', $mahasiswa->nim) }}" 
                                       class="action-icon edit-icon" 
                                       data-bs-toggle="tooltip"
                                       title="Edit Mahasiswa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="action-icon reset-icon border-0" 
                                            data-bs-toggle="tooltip"
                                            title="Reset Password"
                                            onclick="showResetConfirmation('{{ $mahasiswa->nim }}', '{{ $mahasiswa->nama }}')">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    <button type="button" 
                                            class="action-icon delete-icon border-0" 
                                            data-bs-toggle="tooltip"
                                            title="Hapus Mahasiswa"
                                            onclick="showDeleteConfirmation('{{ $mahasiswa->nim }}', '{{ $mahasiswa->nama }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data mahasiswa</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($mahasiswas instanceof \Illuminate\Pagination\LengthAwarePaginator && $mahasiswas->total() > 0)
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mt-3">
                <p class="mb-3 mb-lg-0">
                    Menampilkan {{ $mahasiswas->firstItem() ?? 0 }} sampai {{ $mahasiswas->lastItem() ?? 0 }} 
                    dari {{ $mahasiswas->total() ?? 0 }} entri
                </p>
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0 justify-content-center justify-content-lg-end">
                        {{-- Previous Page Link --}}
                        @if ($mahasiswas->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">« Sebelumnya</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $mahasiswas->appends(request()->query())->previousPageUrl() }}" rel="prev">« Sebelumnya</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($mahasiswas->appends(request()->query())->getUrlRange(1, $mahasiswas->lastPage()) as $page => $url)
                            @if ($page == $mahasiswas->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($mahasiswas->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $mahasiswas->appends(request()->query())->nextPageUrl() }}" rel="next">Selanjutnya »</a>
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

<!-- HTML untuk Modal Reset Password (diperbaiki) -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-light">
                <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">
                    <i class="bi me-2 text-warning"></i>Konfirmasi Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-warning">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                    </div>
                    <div class="text-center">
                        <p class="mb-1 fw-semibold">Anda yakin ingin reset password?</p>
                        <p class="mb-0 text-secondary small" id="resetPasswordText">Reset password mahasiswa ...</p>
                        <p class="mb-0 mt-2 text-warning small">Password akan direset ke NIM mahasiswa</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi me-1"></i>Batal
                </button>
                <button type="button" id="confirmResetPassword" class="btn btn-primary">
                    <i class="bi me-1"></i>Ya, Reset Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- HTML untuk Modal Hapus (diperbaiki) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-light">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="bi me-2 text-danger"></i>Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-danger">
                        <i class="bi bi-exclamation-circle-fill text-danger fs-4"></i>
                    </div>
                    <div class="text-center">
                        <p class="mb-1 fw-semibold">Anda yakin ingin menghapus data ini?</p>
                        <p class="mb-0 text-secondary small" id="deleteText">Hapus data ...</p>
                        <p class="mb-0 mt-2 text-danger small">Perhatian: Tindakan ini tidak dapat dibatalkan!</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi me-1"></i>Batal
                </button>
                <button type="button" id="confirmDelete" class="btn btn-danger">
                    <i class="bi me-1"></i>Ya, Hapus Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form hidden untuk reset password -->
<form id="resetPasswordForm" method="POST" action="">
  @csrf
</form>

<!-- Form hidden untuk delete -->
<form id="deleteForm" method="POST" action="">
  @csrf
  @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables untuk menyimpan data sementara
        let currentAction = '';
        let currentNim = '';
        let currentNama = '';
        
        // Fungsi untuk mengganti jumlah entri yang ditampilkan
        function changePerPage(perPageValue) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', perPageValue);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        
        window.changePerPage = changePerPage;
        
        // Inisialisasi pencarian
        initializeSearchForTable();
        
        // Aktifkan tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 500, hide: 100 }
            });
        });
        
        // Event handler untuk tombol konfirmasi reset password
        const confirmResetBtn = document.getElementById('confirmResetPassword');
        if (confirmResetBtn) {
            confirmResetBtn.addEventListener('click', function() {
                if (currentAction === 'reset' && currentNim) {
                    handleResetPassword(currentNim, currentNama);
                }
            });
        }
        
        // Event handler untuk tombol konfirmasi delete
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (currentAction === 'delete' && currentNim) {
                    handleDelete(currentNim, currentNama);
                }
            });
        }
        
        // Fungsi global untuk menampilkan modal reset password
        window.showResetConfirmation = function(nim, nama) {
            currentAction = 'reset';
            currentNim = nim;
            currentNama = nama;
            
            document.getElementById('resetPasswordText').textContent = `Reset password mahasiswa "${nama}"?`;
            const resetModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            resetModal.show();
        };
        
        // Fungsi global untuk menampilkan modal delete
        window.showDeleteConfirmation = function(nim, nama) {
            currentAction = 'delete';
            currentNim = nim;
            currentNama = nama;
            
            document.getElementById('deleteText').textContent = `Hapus data mahasiswa "${nama}"?`;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        };
        
        // Fungsi untuk handle reset password dengan AJAX
        function handleResetPassword(nim, nama) {
            const submitBtn = document.getElementById('confirmResetPassword');
            const originalText = submitBtn.innerHTML;
            
            // Disable tombol dan show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memproses...';
            
            // Buat FormData
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
            
            const url = `{{ route('admin.resetpasswordmahasiswa', '') }}/${nim}`;
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                if (modal) modal.hide();
                
                if (data.success) {
                    showSuccessAlert(
                        'Password Berhasil Direset!', 
                        data.message || `Password mahasiswa "${nama}" telah berhasil direset ke NIM.`
                    );
                } else {
                    showErrorAlert(
                        'Gagal Reset Password', 
                        data.message || 'Terjadi kesalahan saat mereset password.'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                if (modal) modal.hide();
                
                showErrorAlert(
                    'Terjadi Kesalahan', 
                    'Tidak dapat menghubungi server. Silakan coba lagi.'
                );
            })
            .finally(() => {
                // Restore tombol
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                // Reset variables
                currentAction = '';
                currentNim = '';
                currentNama = '';
            });
        }
        
        // Fungsi untuk handle delete dengan AJAX
        function handleDelete(nim, nama) {
            const submitBtn = document.getElementById('confirmDelete');
            const originalText = submitBtn.innerHTML;
            
            // Disable tombol dan show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menghapus...';
            
            // Buat FormData
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            const url = `{{ route('admin.hapusmahasiswa', '') }}/${nim}`;
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) modal.hide();
                
                if (data.success) {
                    showSuccessAlert(
                        'Data Berhasil Dihapus!', 
                        data.message || `Data mahasiswa "${nama}" telah berhasil dihapus.`
                    );
                } else {
                    showErrorAlert(
                        'Gagal Menghapus Data', 
                        data.message || 'Terjadi kesalahan saat menghapus data.'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) modal.hide();
                
                showErrorAlert(
                    'Terjadi Kesalahan', 
                    'Tidak dapat menghubungi server. Silakan coba lagi.'
                );
            })
            .finally(() => {
                // Restore tombol
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                // Reset variables
                currentAction = '';
                currentNim = '';
                currentNama = '';
            });
        }
    });
    
    // Fungsi untuk menampilkan alert sukses
    function showSuccessAlert(title, message, callback = null) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'success',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then(() => {
                if (callback && typeof callback === 'function') {
                    callback();
                } else {
                    location.reload();
                }
            });
        } else {
            alert(title + ': ' + message);
            if (callback && typeof callback === 'function') {
                callback();
            } else {
                location.reload();
            }
        }
    }
    
    // Fungsi untuk menampilkan alert error
    function showErrorAlert(title, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'OK'
            });
        } else {
            alert(title + ': ' + message);
        }
    }
    
    // Fungsi pencarian table (tetap sama)
    function initializeSearchForTable() {
        const searchInput = document.getElementById('searchInput');
        const clearButton = document.getElementById('clearSearch');
        const table = document.querySelector('table');
        
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