@extends('layouts.app')

@section('title', 'Edit Konsentrasi')

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

    /* Info box styling */
    .info-box {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-left: 4px solid #0ea5e9;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }

    .info-box .info-label {
        font-weight: 600;
        color: #0369a1;
        margin-bottom: 0.25rem;
    }

    .info-box .info-value {
        font-size: 1.1rem;
        color: #1e40af;
        font-weight: 500;
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
            <h1 class="mb-2 gradient-text fw-bold">Edit Konsentrasi</h1>
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
                    
                    <form action="{{ route('admin.updatekonsentrasi', $konsentrasi->id) }}" method="POST" id="editKonsentrasiForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- HANYA NAMA KONSENTRASI YANG BISA DIEDIT -->
                        <div class="mb-4">
                            <label for="nama_konsentrasi" class="form-label">
                                Nama Konsentrasi <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_konsentrasi') is-invalid @enderror" 
                                   id="nama_konsentrasi" 
                                   name="nama_konsentrasi" 
                                   value="{{ old('nama_konsentrasi', $konsentrasi->nama_konsentrasi) }}" 
                                   required
                                   placeholder="Masukkan nama konsentrasi"
                                   maxlength="255"
                                   autocomplete="off">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Nama lengkap konsentrasi yang akan ditampilkan di sistem
                            </div>
                            @error('nama_konsentrasi')
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
        const editForm = document.getElementById('editKonsentrasiForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit(this);
            });
        }
        
        // Setup form validation
        setupFormValidation();
    });
    
    // Fungsi untuk handle form submit
    function handleFormSubmit(form) {
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnDesktop = document.getElementById('submitBtnDesktop');
        const namaKonsentrasi = form.querySelector('input[name="nama_konsentrasi"]').value;
        
        // Show confirmation alert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: `Apakah Anda yakin ingin menyimpan perubahan konsentrasi "${namaKonsentrasi}"?`,
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
            if (confirm(`Apakah Anda yakin ingin menyimpan perubahan konsentrasi "${namaKonsentrasi}"?`)) {
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
                    data.message || `Data konsentrasi "${namaKonsentrasi}" berhasil diperbarui.`,
                    function() {
                        // Redirect or reload
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            // Show success message and stay on page
                            showInlineSuccessMessage(data.message || 'Data berhasil disimpan!');
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
                btn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Perubahan';
            }
        });
    }
    
    // Fungsi untuk menampilkan success message inline
    function showInlineSuccessMessage(message) {
        // Remove existing success alert
        const existingAlert = document.getElementById('successAlert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create new success alert
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
        
        // Insert after the back button
        const backButton = document.querySelector('.btn-gradient.mb-4');
        if (backButton) {
            backButton.insertAdjacentHTML('afterend', alertHTML);
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                const newAlert = document.getElementById('successAlert');
                if (newAlert) {
                    newAlert.style.transition = 'all 0.5s ease-out';
                    newAlert.style.transform = 'translateY(-100%)';
                    newAlert.style.opacity = '0';
                    setTimeout(() => {
                        newAlert.remove();
                    }, 500);
                }
            }, 5000);
        }
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
    
    // Fungsi untuk setup form validation
    function setupFormValidation() {
        // Real-time validation untuk Nama Konsentrasi
        const namaInput = document.getElementById('nama_konsentrasi');
        if (namaInput) {
            namaInput.addEventListener('input', function() {
                // Trim whitespace dan validasi panjang
                const value = this.value.trim();
                if (value.length < 2 && value.length > 0) {
                    this.classList.add('is-invalid');
                    let feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        this.parentNode.appendChild(feedback);
                    }
                    feedback.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Nama konsentrasi minimal 2 karakter';
                } else {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback && !feedback.textContent.includes('required')) {
                        feedback.remove();
                    }
                }
            });

            // Auto-focus pada field nama
            namaInput.focus();
            namaInput.select(); // Select all text untuk kemudahan edit
        }
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