@extends('layouts.app')

@section('title', 'Data Administrator')

@push('styles')
<style>
    /* Action Icons - sama seperti data dosen */
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

    .edit-icon {
        background-color: #ffc107;
        color: white !important;
    }

    .reset-icon {
        background-color: #6c757d;
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

    /* Modal styling */
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

    .modal-footer {
        border-top: none;
        padding: 0 1.5rem 1.5rem;
        justify-content: center;
        gap: 1rem;
    }

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

    .modal-footer .btn:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Data Administrator</h1>
    <hr>
    
    <a href="{{ route('admin.dashboard') }}" class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center" style="width: fit-content;">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
    
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
            <h5 class="mb-3 fw-bold">Data Administrator</h5>
            <hr class="mt-0 mb-3">
            
            <!-- Stats Card -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-0">Total Administrator</h6>
                                    <h2 class="mb-0">{{ $admins->count() }}</h2>
                                </div>
                                <div class="ms-3">
                                    <i class="bi bi-people-fill fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Tampilkan</label>
                        <select class="form-select form-select-sm w-auto" id="perPageSelect">
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
                            <th scope="col" class="text-center align-middle">Username</th>
                            <th scope="col" class="text-center align-middle">Nama</th>
                            <th scope="col" class="text-center align-middle">Email</th>
                            <th scope="col" class="text-center align-middle">Role</th>
                            <th scope="col" class="text-center align-middle">Tanggal Dibuat</th>
                            <th scope="col" class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $index => $admin)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $admin->username }}</td>
                            <td>{{ $admin->nama ?? '-' }}</td>
                            <td>{{ $admin->email ?? '-' }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $admin->role_akses ?? 'Administrator' }}
                                </span>
                            </td>
                            <td>{{ $admin->created_at ? \Carbon\Carbon::parse($admin->created_at)->format('d M Y') : '-' }}</td>
                            <td>
                                <div class="action-icons">
                                    <a href="{{ route('admin.editadmin', $admin->id) }}" 
                                       class="action-icon edit-icon" 
                                       data-bs-toggle="tooltip"
                                       title="Edit Admin">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="action-icon reset-icon border-0" 
                                            data-bs-toggle="tooltip"
                                            title="Reset Password"
                                            onclick="showResetConfirmation('{{ $admin->id }}', '{{ addslashes($admin->nama ?? $admin->username) }}')">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data administrator</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Konfirmasi Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="rounded-circle bg-warning mb-3" style="width: 80px; height: 80px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-key-fill text-warning fs-1"></i>
                    </div>
                    <h6 class="mb-3">Yakin ingin reset password?</h6>
                    <p class="text-muted" id="resetPasswordText">Reset password administrator...</p>
                    <div class="alert alert-warning mt-3">
                        <small><i class="bi bi-info-circle me-1"></i>Password akan direset ke: <strong>admin123</strong></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="confirmResetPassword" class="btn btn-warning">
                    <i class="bi bi-key me-1"></i>Ya, Reset Password
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentAdminId = '';
    let currentAdminName = '';

    // Fungsi untuk menampilkan konfirmasi reset password
    window.showResetConfirmation = function(adminId, adminName) {
        currentAdminId = adminId;
        currentAdminName = adminName;
        
        document.getElementById('resetPasswordText').textContent = 
            `Reset password administrator "${adminName}"?`;
        
        // Gunakan Bootstrap 5 modal
        const modalElement = document.getElementById('resetPasswordModal');
        if (modalElement && window.bootstrap) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    };

    // Event listener untuk tombol konfirmasi reset password
    const confirmResetBtn = document.getElementById('confirmResetPassword');
    if (confirmResetBtn) {
        confirmResetBtn.addEventListener('click', function() {
            if (!currentAdminId) return;
            
            const btn = this;
            const originalText = btn.innerHTML;
            
            // Loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memproses...';
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const token = csrfToken ? csrfToken.getAttribute('content') : '';
            
            // AJAX request
            fetch(`/admin/reset-password-admin/${currentAdminId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Close modal
                const modalElement = document.getElementById('resetPasswordModal');
                if (modalElement && window.bootstrap) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                }
                
                if (data.success) {
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#10b981'
                        });
                    } else {
                        alert('Berhasil: ' + data.message);
                    }
                } else {
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Close modal
                const modalElement = document.getElementById('resetPasswordModal');
                if (modalElement && window.bootstrap) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                }
                
                if (window.Swal) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mereset password.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    alert('Terjadi kesalahan saat mereset password.');
                }
            })
            .finally(() => {
                // Reset button
                btn.disabled = false;
                btn.innerHTML = originalText;
                currentAdminId = '';
                currentAdminName = '';
            });
        });
    }

    // Initialize search functionality
    initializeSearchForTable();
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            delay: { show: 500, hide: 100 }
        });
    });

    // Auto hide alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('show')) {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            }
        });
    }, 5000);
});

// Search functionality
function initializeSearchForTable() {
    const searchInput = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearSearch');
    const table = document.querySelector('table');
    
    if (!searchInput || !clearButton || !table) return;
    
    // Reset search state
    searchInput.value = '';
    clearButton.style.display = 'none';
    
    // Search input event
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        // Show/hide clear button
        clearButton.style.display = searchTerm ? 'flex' : 'none';
        
        // Filter table
        const rows = table.querySelectorAll('tbody tr');
        let matchFound = false;
        
        // Remove existing no results message
        const existingNoResults = table.querySelector('.no-results-row');
        if (existingNoResults) {
            existingNoResults.remove();
        }
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let rowMatch = false;
            
            cells.forEach(cell => {
                const cellText = cell.textContent.toLowerCase();
                
                if (cellText.includes(searchTerm)) {
                    rowMatch = true;
                    
                    // Highlight matching text
                    if (searchTerm) {
                        if (!cell.hasAttribute('data-original')) {
                            cell.setAttribute('data-original', cell.textContent);
                        }
                        
                        const escapedSearchTerm = searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        const regex = new RegExp(`(${escapedSearchTerm})`, 'gi');
                        const highlightedText = cell.getAttribute('data-original').replace(regex, 
                            '<span class="highlight">$1</span>'
                        );
                        
                        cell.innerHTML = highlightedText;
                    }
                } else if (cell.hasAttribute('data-original') && searchTerm) {
                    cell.textContent = cell.getAttribute('data-original');
                    cell.removeAttribute('data-original');
                }
            });
            
            // Show/hide row based on match
            if (rowMatch || !searchTerm) {
                row.style.display = '';
                matchFound = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Reset highlighting if search is cleared
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
        
        // Show no results message
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
    
    // Clear search event
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        
        // Reset all cell content
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
        
        // Remove no results message
        const existingNoResults = table.querySelector('.no-results-row');
        if (existingNoResults) {
            existingNoResults.remove();
        }
        
        // Hide clear button and show search icon
        this.style.display = 'none';
        const searchIcon = searchInput.nextElementSibling;
        if (searchIcon && searchIcon.classList.contains('search-icon')) {
            searchIcon.style.opacity = '1';
            searchIcon.style.visibility = 'visible';
        }
        
        searchInput.focus();
    });
}
</script>
@endpush