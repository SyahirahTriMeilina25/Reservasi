@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')

@push('styles')
<style>
    /* Form styling */
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

    .form-control:disabled {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #6b7280;
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
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Prevent autofill styling */
    .form-control:-webkit-autofill,
    .form-control:-webkit-autofill:hover,
    .form-control:-webkit-autofill:focus,
    .form-control:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px #f9fafb inset !important;
        -webkit-text-fill-color: #374151 !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    /* Card styling */
    .card-custom {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card-custom:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .card-body-custom {
        padding: 2rem;
    }

    /* Alert styling */
    .alert-custom {
        border: none;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }

    .alert-danger-custom {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #dc2626;
        border-left: 4px solid #ef4444;
    }

    .alert-danger-custom ul {
        margin-bottom: 0;
        padding-left: 1.25rem;
    }

    .alert-danger-custom li {
        margin-bottom: 0.25rem;
    }

    /* Success Alert Styling */
    .alert-success-custom {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        color: #16a34a;
        border-left: 4px solid #22c55e;
        animation: slideInFromTop 0.5s ease-out;
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

    /* Required asterisk */
    .required-asterisk {
        color: #ef4444;
        font-weight: bold;
    }

    /* Password field with toggle */
    .password-field {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s ease;
        z-index: 10;
    }

    .password-toggle:hover {
        color: #667eea;
        background-color: rgba(102, 126, 234, 0.1);
    }

    .password-toggle:focus {
        outline: none;
        color: #667eea;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
    }

    .form-control.has-toggle {
        padding-right: 45px;
    }

    /* Password matching feedback */
    .password-match {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .password-match.match {
        color: #10b981;
    }

    .password-match.no-match {
        color: #ef4444;
    }

    .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 0.5rem;
        transition: all 0.3s ease;
    }

    .strength-weak { background-color: #ef4444; }
    .strength-medium { background-color: #f59e0b; }
    .strength-strong { background-color: #10b981; }

    /* Responsive design */
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

    /* Animation for form validation */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .is-invalid {
        animation: shake 0.5s ease-in-out;
    }

    /* Loading button animation */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        margin: auto;
        border: 2px solid transparent;
        border-top-color: currentColor;
        border-radius: 50%;
        animation: button-loading-spinner 1s ease infinite;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    @keyframes button-loading-spinner {
        from {
            transform: rotate(0turn);
        }
        to {
            transform: rotate(1turn);
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-2 gradient-text fw-bold">Tambah Mahasiswa</h1>
            <hr>
            <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
                <a href="{{ route('admin.datamahasiswa') }}">
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
                <div class="card-body card-body-custom">
                    <h5 class="mb-4 fw-bold">Informasi Mahasiswa</h5>
                    <hr class="mb-4">
                    
                    <form action="{{ route('admin.simpanmahasiswa') }}" method="POST" id="tambahMahasiswaForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="nim" class="form-label">
                                NIM <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nim') is-invalid @enderror" 
                                   id="nim" 
                                   name="nim" 
                                   value="{{ old('nim') }}" 
                                   required
                                   placeholder="Masukkan NIM"
                                   maxlength="20"
                                   autocomplete="off">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Nomor Induk Mahasiswa
                            </div>
                            @error('nim')
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
                                   value="{{ old('nama') }}" 
                                   required
                                   placeholder="Masukkan nama lengkap"
                                   autocomplete="off">
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
                                   value="{{ old('email') }}" 
                                   required
                                   placeholder="contoh@email.com"
                                   autocomplete="new-email"
                                   data-lpignore="true"
                                   readonly
                                   onfocus="this.removeAttribute('readonly');">
                            @error('email')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="angkatan" class="form-label">
                                Angkatan <span class="required-asterisk">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('angkatan') is-invalid @enderror" 
                                   id="angkatan" 
                                   name="angkatan" 
                                   value="{{ old('angkatan') }}" 
                                   required
                                   placeholder="Contoh: 2024"
                                   autocomplete="off">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Tahun masuk kuliah
                            </div>
                            @error('angkatan')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="prodi_id" class="form-label">
                                Program Studi <span class="required-asterisk">*</span>
                            </label>
                            <select class="form-select @error('prodi_id') is-invalid @enderror" 
                                    id="prodi_id" 
                                    name="prodi_id" 
                                    required>
                                <option value="">Pilih Program Studi</option>
                                @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama_prodi }}
                                </option>
                                @endforeach
                            </select>
                            @error('prodi_id')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="konsentrasi_id" class="form-label">
                                Konsentrasi (Opsional)
                            </label>
                            <select class="form-select @error('konsentrasi_id') is-invalid @enderror" 
                                    id="konsentrasi_id" 
                                    name="konsentrasi_id">
                                <option value="">Pilih Konsentrasi (Opsional)</option>
                                @foreach($konsentrasis as $konsentrasi)
                                <option value="{{ $konsentrasi->id }}" {{ old('konsentrasi_id') == $konsentrasi->id ? 'selected' : '' }}>
                                    {{ $konsentrasi->nama_konsentrasi }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Pilih konsentrasi jika sudah ditentukan
                            </div>
                            @error('konsentrasi_id')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                Password <span class="required-asterisk">*</span>
                            </label>
                            <div class="password-field">
                                <input type="password" 
                                       class="form-control has-toggle @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required
                                       placeholder="Masukkan password"
                                       minlength="6"
                                       autocomplete="new-password"
                                       data-lpignore="true"
                                       readonly
                                       onfocus="this.removeAttribute('readonly');">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')" tabindex="-1">
                                    <i class="bi bi-eye" id="password-icon"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="passwordStrength"></div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Minimum 6 karakter. Rekomendasi: gunakan NIM sebagai password awal
                            </div>
                            @error('password')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                Konfirmasi Password <span class="required-asterisk">*</span>
                            </label>
                            <div class="password-field">
                                <input type="password" 
                                       class="form-control has-toggle @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required
                                       placeholder="Ulangi password"
                                       autocomplete="new-password"
                                       data-lpignore="true"
                                       readonly
                                       onfocus="this.removeAttribute('readonly');">
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')" tabindex="-1">
                                    <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                            <div class="password-match" id="passwordMatch"></div>
                            @error('password_confirmation')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-md-flex justify-content-end">
                            <div class="btn-group-mobile d-md-none">
                                <button type="submit" class="btn btn-gradient" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>Simpan Data
                                </button>
                            </div>
                            <div class="btn-group-desktop d-none d-md-flex">
                                <button type="submit" class="btn btn-gradient" id="submitBtnDesktop">
                                    <i class="bi bi-save me-2"></i>Simpan Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto hide success alert after 5 seconds
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'all 0.5s ease-out';
            successAlert.style.transform = 'translateY(-100%)';
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.remove();
            }, 500);
        }, 5000);
    }

    // Event handler untuk form submit
    const tambahForm = document.getElementById('tambahMahasiswaForm');
    if (tambahForm) {
        tambahForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this);
        });
    }

    // Password elements
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordMatch = document.getElementById('passwordMatch');
    const passwordStrength = document.getElementById('passwordStrength');
    
    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 6) strength += 1;
        if (password.length >= 8) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        return strength;
    }
    
    // Update password strength indicator
    function updatePasswordStrength() {
        const password = passwordInput.value;
        const strength = checkPasswordStrength(password);
        
        passwordStrength.style.width = '100%';
        
        if (password.length === 0) {
            passwordStrength.style.width = '0%';
            passwordStrength.className = 'password-strength';
        } else if (strength <= 2) {
            passwordStrength.className = 'password-strength strength-weak';
        } else if (strength <= 4) {
            passwordStrength.className = 'password-strength strength-medium';
        } else {
            passwordStrength.className = 'password-strength strength-strong';
        }
    }
    
    // Check password match
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword === '') {
            passwordMatch.textContent = '';
            passwordMatch.className = 'password-match';
            return;
        }
        
        if (password === confirmPassword) {
            passwordMatch.innerHTML = '<i class="bi bi-check-circle me-1"></i>Password cocok';
            passwordMatch.className = 'password-match match';
        } else {
            passwordMatch.innerHTML = '<i class="bi bi-x-circle me-1"></i>Password tidak cocok';
            passwordMatch.className = 'password-match no-match';
        }
    }
    
    // Auto-fill password based on NIM
    const nimInput = document.getElementById('nim');
    nimInput.addEventListener('blur', function() {
        if (this.value && !passwordInput.value) {
            passwordInput.value = this.value;
            confirmPasswordInput.value = this.value;
            updatePasswordStrength();
            checkPasswordMatch();
        }
    });
    
    // Event listeners
    passwordInput.addEventListener('input', function() {
        updatePasswordStrength();
        checkPasswordMatch();
    });
    
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    
    // Prevent autofill on password and email fields
    const sensitiveFields = ['email', 'password', 'password_confirmation'];
    sensitiveFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            // Clear any autofilled value
            setTimeout(() => {
                if (field.value && field.value.length > 0 && !field.dataset.userFilled) {
                    field.value = '';
                }
            }, 100);
            
            // Mark field as user-filled when user types
            field.addEventListener('input', function() {
                this.dataset.userFilled = 'true';
            });
            
            // Additional prevention for autofill
            field.addEventListener('animationstart', function(e) {
                if (e.animationName === 'onAutoFillStart') {
                    this.value = '';
                }
            });
        }
    });
    
    // Setup form validation
    setupFormValidation();
    
    // Initialize
    updatePasswordStrength();
    checkPasswordMatch();
});

// Toggle password visibility
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        passwordField.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Fungsi untuk handle form submit
function handleFormSubmit(form) {
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnDesktop = document.getElementById('submitBtnDesktop');
    const nama = form.querySelector('input[name="nama"]').value;
    
    // Show confirmation alert
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Simpan Data Mahasiswa?',
            text: `Apakah Anda yakin ingin menyimpan data mahasiswa "${nama}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i></i>Ya, Simpan',
            cancelButtonText: '<i></i>Batal',
            reverseButtons: true,
            customClass: {
                popup: 'swal-wide',
                title: 'swal-title-custom',
                content: 'swal-content-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitFormData(form, submitBtn, submitBtnDesktop, nama);
            }
        });
    } else {
        if (confirm(`Apakah Anda yakin ingin menyimpan data mahasiswa "${nama}"?`)) {
            submitFormData(form, submitBtn, submitBtnDesktop, nama);
        }
    }
}

// Fungsi untuk submit form data
function submitFormData(form, submitBtn, submitBtnDesktop, nama) {
    // Show loading state
    setLoadingState(submitBtn, submitBtnDesktop, true);
    
    // Prepare form data
    const formData = new FormData(form);
    
    // Submit with fetch
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
            showSuccessAlert(
                'Data Berhasil Disimpan!',
                data.message || `Data mahasiswa "${nama}" berhasil ditambahkan.`,
                function() {
                    // Redirect or reload
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Redirect to data mahasiswa page
                        window.location.href = '{{ route("admin.datamahasiswa") }}';
                    }
                }
            );
        } else {
            showErrorAlert(
                'Gagal Menyimpan Data',
                data.message || 'Terjadi kesalahan saat menyimpan data.'
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        if (error.errors) {
            // Handle validation errors
            let errorMessage = 'Data yang Anda masukkan tidak valid:\n';
            Object.keys(error.errors).forEach(key => {
                errorMessage += `\n• ${error.errors[key].join(', ')}`;
            });
            
            showErrorAlert('Data Tidak Valid', errorMessage);
            showValidationErrors(form, error.errors);
        } else {
            showErrorAlert(
                'Terjadi Kesalahan',
                error.message || 'Tidak dapat menghubungi server. Silakan coba lagi.'
            );
        }
    })
    .finally(() => {
        // Restore button state
        setLoadingState(submitBtn, submitBtnDesktop, false);
    });
}

// Fungsi untuk set loading state pada tombol
function setLoadingState(submitBtn, submitBtnDesktop, isLoading) {
    const buttons = [submitBtn, submitBtnDesktop].filter(btn => btn);
    
    buttons.forEach(btn => {
        if (isLoading) {
            btn.disabled = true;
            btn.classList.add('btn-loading');
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
        } else {
            btn.disabled = false;
            btn.classList.remove('btn-loading');
            btn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Data';
        }
    });
}

// Fungsi untuk menampilkan validation errors pada form
function showValidationErrors(form, errors) {
    // Clear previous errors
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    
    // Show new errors
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');
            
            // Create error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>${errors[fieldName].join(', ')}`;
            
            // Insert after field or password field wrapper
            const wrapper = field.closest('.password-field') || field.parentNode;
            wrapper.appendChild(errorDiv);
        }
    });
}

// Fungsi untuk setup form validation
function setupFormValidation() {
    // Real-time validation untuk email
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.classList.add('is-invalid');
                let feedback = this.parentNode.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    this.parentNode.appendChild(feedback);
                }
                feedback.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Format email tidak valid';
            } else {
                this.classList.remove('is-invalid');
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback && !feedback.textContent.includes('required')) {
                    feedback.remove();
                }
            }
        });
    }
    
    // Real-time validation untuk NIM
    const nimInput = document.getElementById('nim');
    if (nimInput) {
        nimInput.addEventListener('input', function() {
            // Hanya izinkan angka dan huruf
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
        });
    }
    
    // Prevent autofill interference
    const allInputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="number"]');
    allInputs.forEach(input => {
        input.addEventListener('focus', function() {
            if (this.hasAttribute('readonly')) {
                this.removeAttribute('readonly');
            }
        });
    });
}

// Fungsi untuk menampilkan alert sukses
function showSuccessAlert(title, message, callback = null) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: message,
            icon: 'success',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK',
            timer: 4000,
            timerProgressBar: true,
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            },
            customClass: {
                popup: 'swal-success-custom',
                title: 'swal-title-success',
                content: 'swal-content-success'
            }
        }).then(() => {
            if (callback && typeof callback === 'function') {
                callback();
            }
        });
    } else {
        alert(title + ': ' + message);
        if (callback && typeof callback === 'function') {
            callback();
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
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'swal-error-custom',
                title: 'swal-title-error',
                content: 'swal-content-error'
            },
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            }
        });
    } else {
        alert(title + ': ' + message);
    }
}

// Add CSS for autofill prevention
const style = document.createElement('style');
style.textContent = `
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-animation: autofill 0s forwards;
        animation: autofill 0s forwards;
    }
    
    @-webkit-keyframes autofill {
        to {
            color: #374151;
            background: #f9fafb;
        }
    }
    
    @keyframes autofill {
        to {
            color: #374151;
            background: #f9fafb;
        }
    }
    
    @-webkit-keyframes onAutoFillStart {
        from { background: yellow; }
        to { background: yellow; }
    }
    
    @keyframes onAutoFillStart {
        from { background: yellow; }
        to { background: yellow; }
    }
    
    input:-webkit-autofill {
        -webkit-animation-name: onAutoFillStart;
        animation-name: onAutoFillStart;
    }
`;
document.head.appendChild(style);
</script>
@endpush