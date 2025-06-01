@extends('layouts.app')

@section('title', 'Edit Mahasiswa')

@push('styles')
<style>
    /* Button Secondary Styling */
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

    .btn-secondary-custom a {
        color: white !important;
        text-decoration: none;
    }

    /* Button Warning Styling */
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

    /* Password input styling */
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

    .password-toggle-btn:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
    }

    .form-control.has-toggle {
        padding-right: 45px;
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

    .card-header-custom {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
        padding: 1.25rem 1.5rem;
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
        justify-content: center;
        gap: 1rem;
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
        content: "\F33A";
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
            <h1 class="mb-2 gradient-text fw-bold">Edit Mahasiswa</h1>
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
                    
                    <form action="{{ route('admin.updatemahasiswa', $mahasiswa->nim) }}" method="POST" id="editMahasiswaForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="nim" class="form-label">
                                NIM <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nim') is-invalid @enderror" 
                                   id="nim" 
                                   name="nim"
                                   value="{{ old('nim', $mahasiswa->nim) }}" 
                                   required
                                   placeholder="Masukkan NIM"
                                   maxlength="20">
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
                                   value="{{ old('nama', $mahasiswa->nama) }}" 
                                   required
                                   placeholder="Masukkan nama lengkap">
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
                                   value="{{ old('email', $mahasiswa->email) }}" 
                                   required
                                   placeholder="contoh@email.com">
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
                                   value="{{ old('angkatan', $mahasiswa->angkatan) }}" 
                                   required
                                   placeholder="Contoh: 2024"
                                   min="2000"
                                   max="{{ date('Y') + 5 }}">
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
                                <option value="{{ $prodi->id }}" {{ old('prodi_id', $mahasiswa->prodi_id) == $prodi->id ? 'selected' : '' }}>
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
                                <option value="{{ $konsentrasi->id }}" {{ old('konsentrasi_id', $mahasiswa->konsentrasi_id) == $konsentrasi->id ? 'selected' : '' }}>
                                    {{ $konsentrasi->nama_konsentrasi }}
                                </option>
                                @endforeach
                            </select>
                            @error('konsentrasi_id')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Password (Opsional)</label>
                            <div class="password-input-wrapper">
                                <input type="password" 
                                       class="form-control has-toggle @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password"
                                       placeholder="Masukkan password baru"
                                       autocomplete="new-password"
                                       data-lpignore="true"
                                       readonly
                                       onfocus="this.removeAttribute('readonly');">
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('password')">
                                    <i class="bi bi-eye" id="password-icon"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Kosongkan jika tidak ingin mengubah password
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
                            <p class="mb-2">Reset password mahasiswa ke default (sama dengan NIM).</p>
                            <small class="text-muted">Password akan direset menjadi: <strong>{{ $mahasiswa->nim }}</strong></small>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-warning-custom" 
                            onclick="showResetConfirmation('{{ $mahasiswa->nim }}', '{{ $mahasiswa->nama }}')">
                        <i class="bi bi-key me-2"></i>Reset Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi me-1"></i>Batal
                </button>
                <button type="button" id="confirmResetPassword" class="btn btn-primary">
                    <i class="bi me-1"></i>Ya, Reset Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form hidden untuk reset password -->
<form id="resetPasswordForm" method="POST" action="">
    @csrf
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables untuk menyimpan data sementara
        let currentAction = '';
        let currentNim = '';
        let currentNama = '';
        
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
        
        // Event handler untuk tombol konfirmasi reset password
        const confirmResetBtn = document.getElementById('confirmResetPassword');
        if (confirmResetBtn) {
            confirmResetBtn.addEventListener('click', function() {
                if (currentAction === 'reset' && currentNim) {
                    handleResetPassword(currentNim, currentNama);
                }
            });
        }
        
        // Event handler untuk form submit
        const editForm = document.getElementById('editMahasiswaForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit(this);
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
        
        // Setup form validation
        setupFormValidation();
        
        // Prevent autofill on password field
        const passwordField = document.getElementById('password');
        if (passwordField) {
            // Clear any autofilled value
            setTimeout(() => {
                if (passwordField.value && passwordField.value.length > 0) {
                    passwordField.value = '';
                }
            }, 100);
            
            // Additional prevention
            passwordField.addEventListener('animationstart', function(e) {
                if (e.animationName === 'onAutoFillStart') {
                    this.value = '';
                }
            });
        }
    });
    
    // Fungsi untuk toggle password visibility
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
    
    // Fungsi untuk handle form submit
    function handleFormSubmit(form) {
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnDesktop = document.getElementById('submitBtnDesktop');
        const nama = form.querySelector('input[name="nama"]').value;
        
        // Show confirmation alert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: `Apakah Anda yakin ingin menyimpan perubahan data mahasiswa "${nama}"?`,
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
            if (confirm(`Apakah Anda yakin ingin menyimpan perubahan data mahasiswa "${nama}"?`)) {
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
                    data.message || `Data mahasiswa "${nama}" berhasil diperbarui.`,
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
        
        // Real-time validation untuk Angkatan
        const angkatanInput = document.getElementById('angkatan');
        if (angkatanInput) {
            angkatanInput.addEventListener('input', function() {
                const currentYear = new Date().getFullYear();
                const minYear = 2000;
                const maxYear = currentYear + 5;
                const value = parseInt(this.value);
                
                if (value && (value < minYear || value > maxYear)) {
                    this.classList.add('is-invalid');
                    let feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        this.parentNode.appendChild(feedback);
                    }
                    feedback.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>Angkatan harus antara ${minYear} - ${maxYear}`;
                } else {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback && !feedback.textContent.includes('required')) {
                        feedback.remove();
                    }
                }
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