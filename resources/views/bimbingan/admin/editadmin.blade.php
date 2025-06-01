@extends('layouts.app')

@section('title', 'Edit Administrator')

@push('styles')
<style>
    /* Menggunakan style yang sama dari edit mahasiswa */
    .btn-secondary-custom {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px 0 rgba(108, 117, 125, 0.3);
    }

    .btn-secondary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px 0 rgba(108, 117, 125, 0.4);
        color: white;
    }

    .btn-warning-custom {
        background: linear-gradient(135deg, #ffc107 0%, #ff9500 100%);
        border: none;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px 0 rgba(255, 193, 7, 0.3);
    }

    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px 0 rgba(255, 193, 7, 0.4);
        color: white;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: #f9fafb;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background-color: white;
    }

    .form-text {
        color: #6b7280;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .is-invalid {
        border-color: #ef4444;
        animation: shake 0.5s ease-in-out;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .password-input-wrapper {
        position: relative;
    }

    .password-toggle-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: #6b7280;
        cursor: pointer;
        z-index: 3;
        transition: color 0.3s ease;
        padding: 4px;
        border-radius: 4px;
    }

    .password-toggle-btn:hover {
        color: #374151;
        background-color: rgba(0, 0, 0, 0.05);
    }

    .form-control.has-toggle {
        padding-right: 45px;
    }

    .card-custom {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #1A3B6E 0%, #578FCA 50%, #36C7F8 100%);
        color: white;
        padding: 1.25rem 1.5rem;
    }

    .card-body-custom {
        padding: 2rem;
    }

    .alert-custom {
        border: none;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }

    .alert-success-custom {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        color: #16a34a;
        border-left: 4px solid #22c55e;
        animation: slideInFromTop 0.5s ease-out;
    }

    .alert-danger-custom {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #dc2626;
        border-left: 4px solid #ef4444;
    }

    .required-asterisk {
        color: #ef4444;
        font-weight: bold;
    }

    @keyframes slideInFromTop {
        0% {
            transform: translateY(-100%);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    @media (max-width: 768px) {
        .card-body-custom {
            padding: 1.5rem;
        }
        
        .btn-group-mobile {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .btn-group-mobile .btn {
            width: 100%;
        }
    }

    @media (min-width: 769px) {
        .btn-group-desktop {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-2 gradient-text fw-bold">
                <i class="bi bi-shield-check me-2"></i>Edit Administrator
            </h1>
            <hr>
            
            <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
                <a href="{{ route('admin.dataadmin') }}" class="text-white text-decoration-none d-flex align-items-center">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </button>
            
            @if(session('success'))
            <div class="alert alert-success-custom alert-custom" id="successAlert">
                <div class="d-flex align-items-start">
                    <i class="bi bi-check-circle-fill me-2 mt-1 text-success"></i>
                    <div>
                        <strong>Berhasil!</strong>
                        <p class="mb-0 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if($errors->any())
            <div class="alert alert-danger-custom alert-custom">
                <div class="d-flex align-items-start">
                    <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                    <div>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-gear me-2"></i>Informasi Administrator
                    </h5>
                </div>
                <div class="card-body card-body-custom">
                    <form action="{{ route('admin.updateadmin', $admin->id) }}" method="POST" id="editAdminForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="username" class="form-label">
                                Username <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username"
                                   value="{{ old('username', $admin->username) }}" 
                                   required
                                   placeholder="Masukkan username"
                                   maxlength="255">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Username untuk login sistem
                            </div>
                            @error('username')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="nama" class="form-label">
                                Nama Lengkap <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" 
                                   name="nama" 
                                   value="{{ old('nama', $admin->nama) }}" 
                                   required
                                   placeholder="Masukkan nama lengkap"
                                   maxlength="255">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Nama lengkap administrator
                            </div>
                            @error('nama')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                Email <span class="required-asterisk">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $admin->email) }}" 
                                   required
                                   placeholder="contoh@email.com"
                                   maxlength="255">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Email untuk komunikasi sistem
                            </div>
                            @error('email')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="role_id" class="form-label">
                                Role <span class="required-asterisk">*</span>
                            </label>
                            <select class="form-select @error('role_id') is-invalid @enderror" 
                                    id="role_id" 
                                    name="role_id" 
                                    required>
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                        {{ old('role_id', $admin->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->role_akses) }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Role administrator dalam sistem
                            </div>
                            @error('role_id')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Password Baru (Opsional)</label>
                            <div class="password-input-wrapper">
                                <input type="password" 
                                       class="form-control has-toggle @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password"
                                       placeholder="Masukkan password baru"
                                       autocomplete="new-password"
                                       readonly
                                       onfocus="this.removeAttribute('readonly');">
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('password')">
                                    <i class="bi bi-eye" id="password-icon"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter.
                            </div>
                            @error('password')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-md-flex justify-content-end">
                            <div class="btn-group-mobile d-md-none">
                                <button type="submit" class="btn btn-gradient" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                            <div class="btn-group-desktop d-none d-md-flex">
                                <button type="submit" class="btn btn-gradient" id="submitBtnDesktop">
                                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card card-custom mt-4">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-key me-2"></i>Reset Password
                    </h5>
                </div>
                <div class="card-body card-body-custom">
                    <div class="d-flex align-items-start mb-3">
                        <i class="bi bi-info-circle text-warning me-2 mt-1"></i>
                        <div>
                            <p class="mb-2">Reset password administrator ke default.</p>
                            <small class="text-muted">Password akan direset menjadi: <strong>admin123</strong></small>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-warning-custom" 
                            onclick="showResetConfirmation('{{ $admin->id }}', '{{ $admin->nama ?? $admin->username }}')">
                        <i class="bi bi-key me-2"></i>Reset Password
                    </button>
                </div>
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
                    <div class="mb-3">
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
        
        // Auto hide success alert
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'all 0.5s ease-out';
                successAlert.style.transform = 'translateY(-100%)';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 500);
            }, 5000);
        }
        
        // Form validation dan submission
        const editForm = document.getElementById('editAdminForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit(this);
            });
        }
        
        // Reset password confirmation
        window.showResetConfirmation = function(adminId, adminName) {
            currentAdminId = adminId;
            currentAdminName = adminName;
            
            document.getElementById('resetPasswordText').textContent = 
                `Reset password administrator "${adminName}"?`;
            
            const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            modal.show();
        };
        
        document.getElementById('confirmResetPassword').addEventListener('click', function() {
            if (!currentAdminId) return;
            
            const btn = this;
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memproses...';
            
            fetch(`{{ route('admin.resetpasswordadmin', '') }}/${currentAdminId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                modal.hide();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                modal.hide();
                
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mereset password.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                currentAdminId = '';
                currentAdminName = '';
            });
        });
    });
    
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field && icon) {
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    }
    
    // Handle form submission
    function handleFormSubmit(form) {
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnDesktop = document.getElementById('submitBtnDesktop');
        const nama = form.querySelector('input[name="nama"]').value;
        
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: `Apakah Anda yakin ingin menyimpan perubahan data administrator "${nama}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                submitFormData(form, submitBtn, submitBtnDesktop, nama);
            }
        });
    }
    
    // Submit form data
    function submitFormData(form, submitBtn, submitBtnDesktop, nama) {
        // Loading state
        setLoadingState(submitBtn, submitBtnDesktop, true);
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => Promise.reject(data));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        showInlineSuccessMessage(data.message);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            if (error.errors) {
                let errorMessage = 'Data yang Anda masukkan tidak valid:\n';
                Object.keys(error.errors).forEach(key => {
                    errorMessage += `\n• ${error.errors[key].join(', ')}`;
                });
                
                Swal.fire({
                    title: 'Data Tidak Valid',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                
                showValidationErrors(form, error.errors);
            } else {
                Swal.fire({
                    title: 'Terjadi Kesalahan',
                    text: error.message || 'Tidak dapat menghubungi server.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .finally(() => {
            setLoadingState(submitBtn, submitBtnDesktop, false);
        });
    }
    
    // Set loading state
    function setLoadingState(submitBtn, submitBtnDesktop, isLoading) {
        const buttons = [submitBtn, submitBtnDesktop].filter(btn => btn);
        
        buttons.forEach(btn => {
            if (isLoading) {
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Perubahan';
            }
        });
    }
    
    // Show validation errors
    function showValidationErrors(form, errors) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        Object.keys(errors).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>${errors[fieldName].join(', ')}`;
                
                field.parentNode.appendChild(errorDiv);
            }
        });
    }
    
    // Show inline success message
    function showInlineSuccessMessage(message) {
        const existingAlert = document.getElementById('successAlert');
        if (existingAlert) existingAlert.remove();
        
        const alertHTML = `
            <div class="alert alert-success-custom alert-custom" id="successAlert">
                <div class="d-flex align-items-start">
                    <i class="bi bi-check-circle-fill me-2 mt-1 text-success"></i>
                    <div>
                        <strong>Berhasil!</strong>
                        <p class="mb-0 mt-1">${message}</p>
                    </div>
                </div>
            </div>
        `;
        
        const backButton = document.querySelector('.btn-gradient.mb-4');
        if (backButton) {
            backButton.insertAdjacentHTML('afterend', alertHTML);
            
            setTimeout(() => {
                const newAlert = document.getElementById('successAlert');
                if (newAlert) {
                    newAlert.style.transition = 'all 0.5s ease-out';
                    newAlert.style.transform = 'translateY(-100%)';
                    newAlert.style.opacity = '0';
                    setTimeout(() => newAlert.remove(), 500);
                }
            }, 5000);
        }
    }
</script>
@endpush