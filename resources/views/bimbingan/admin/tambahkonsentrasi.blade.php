@extends('layouts.app')

@section('title', 'Tambah Konsentrasi')

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

    /* Character counter */
    .char-counter {
        font-size: 0.75rem;
        color: #9ca3af;
        text-align: right;
        margin-top: 0.25rem;
    }

    .char-counter.warning {
        color: #f59e0b;
    }

    .char-counter.danger {
        color: #ef4444;
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
            <h1 class="mb-2 gradient-text fw-bold">Tambah Konsentrasi</h1>
            <hr>
            <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
                <a href="{{ route('admin.datakonsentrasi') }}">
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
                    <h5 class="mb-4 fw-bold">Informasi Konsentrasi</h5>
                    <hr class="mb-4">
                    
                    <form action="{{ route('admin.simpankonsentrasi') }}" method="POST" id="tambahKonsentrasiForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="nama_konsentrasi" class="form-label">
                                Nama Konsentrasi <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_konsentrasi') is-invalid @enderror" 
                                   id="nama_konsentrasi" 
                                   name="nama_konsentrasi" 
                                   value="{{ old('nama_konsentrasi') }}" 
                                   required
                                   placeholder="Masukkan nama konsentrasi"
                                   maxlength="100"
                                   autocomplete="off"
                                   oninput="updateCharCounter()">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Nama lengkap konsentrasi yang akan ditampilkan dalam sistem
                            </div>
                            <div class="char-counter" id="charCounter">0/100 karakter</div>
                            @error('nama_konsentrasi')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <div class="alert alert-info border-0 rounded-3" style="background: linear-gradient(135deg, #e0f2fe 0%, #e1f5fe 100%); color: #0277bd;">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-lightbulb me-2 mt-1"></i>
                                    <div>
                                        <strong>Tips:</strong>
                                        <ul class="mb-0 mt-1 ps-3">
                                            <li>Gunakan nama yang jelas dan mudah dipahami</li>
                                            <li>Contoh: "Sistem Informasi", "Jaringan Komputer", "Multimedia"</li>
                                            <li>Hindari singkatan yang tidak umum</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
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
    const tambahForm = document.getElementById('tambahKonsentrasiForm');
    if (tambahForm) {
        tambahForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this);
        });
    }

    // Initialize character counter
    updateCharCounter();
    
    // Focus on nama_konsentrasi field when page loads
    const namaKonsentrasiInput = document.getElementById('nama_konsentrasi');
    if (namaKonsentrasiInput) {
        namaKonsentrasiInput.focus();
    }
    
    // Auto-capitalize first letter of each word
    namaKonsentrasiInput.addEventListener('input', function() {
        let value = this.value;
        // Capitalize first letter of each word
        value = value.replace(/\b\w/g, function(letter) {
            return letter.toUpperCase();
        });
        this.value = value;
        updateCharCounter();
    });
    
    // Real-time validation
    namaKonsentrasiInput.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback && !feedback.textContent.includes('required')) {
                feedback.remove();
            }
        }
    });
});

function updateCharCounter() {
    const input = document.getElementById('nama_konsentrasi');
    const counter = document.getElementById('charCounter');
    const currentLength = input.value.length;
    const maxLength = 100;
    
    counter.textContent = `${currentLength}/${maxLength} karakter`;
    
    // Update counter color based on usage
    counter.className = 'char-counter';
    if (currentLength > maxLength * 0.8) {
        counter.classList.add('warning');
    }
    if (currentLength > maxLength * 0.95) {
        counter.classList.remove('warning');
        counter.classList.add('danger');
    }
}

// Fungsi untuk handle form submit
function handleFormSubmit(form) {
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnDesktop = document.getElementById('submitBtnDesktop');
    const namaKonsentrasi = form.querySelector('input[name="nama_konsentrasi"]').value.trim();
    
    // Validasi basic
    if (!namaKonsentrasi) {
        const namaInput = form.querySelector('input[name="nama_konsentrasi"]');
        namaInput.focus();
        namaInput.classList.add('is-invalid');
        return;
    }
    
    // Show confirmation alert
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Simpan Data Konsentrasi?',
            text: `Apakah Anda yakin ingin menyimpan konsentrasi "${namaKonsentrasi}"?`,
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
                submitFormData(form, submitBtn, submitBtnDesktop, namaKonsentrasi);
            }
        });
    } else {
        if (confirm(`Apakah Anda yakin ingin menyimpan konsentrasi "${namaKonsentrasi}"?`)) {
            submitFormData(form, submitBtn, submitBtnDesktop, namaKonsentrasi);
        }
    }
}

// Fungsi untuk submit form data
function submitFormData(form, submitBtn, submitBtnDesktop, namaKonsentrasi) {
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
                data.message || `Konsentrasi "${namaKonsentrasi}" berhasil ditambahkan.`,
                function() {
                    // Redirect or reload
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Redirect to data konsentrasi page
                        window.location.href = '{{ route("admin.datakonsentrasi") }}';
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
            
            // Insert after field
            field.parentNode.appendChild(errorDiv);
        }
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
</script>
@endpush