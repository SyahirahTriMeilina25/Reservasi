@extends('layouts.app')

@section('title', $role === 'mahasiswa' ? 'Profil Mahasiswa' : ($role === 'dosen' ? 'Profil Dosen' : 'Profil Admin'))

@push('styles')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #059669, #2563eb);
            --secondary-gradient: linear-gradient(to right, #4ade80, #3b82f6);
            --admin-gradient: linear-gradient(135deg, #dc2626, #ea580c);
            --text-dark: #2c3e50;
            --text-light: #34495e;
            --shadow-soft: 0 8px 25px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 15px 35px rgba(0, 0, 0, 0.1);
            --shadow-strong: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        .student-profile-container {
            max-width: 850px;
            margin: 40px auto;
            perspective: 1000px;
        }

        .student-profile-card {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-medium);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .student-profile-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-strong);
        }

        .profile-header {
            background: var(--primary-gradient);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="25" r="0.5" fill="rgba(255,255,255,0.03)"/><circle cx="25" cy="75" r="0.5" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .profile-header.admin {
            background: var(--admin-gradient);
        }

        .avatar-container {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            overflow: hidden;
            display: inline-block;
            position: relative;
            border: 8px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        }

        .avatar-container:hover {
            transform: scale(1.08) rotate(3deg);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
        }

        .student-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            border: none;
            transition: all 0.3s ease;
        }

        .avatar-container:hover .student-avatar {
            transform: scale(1.1);
        }

        .camera-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: 3px solid white;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            color: white;
        }

        .camera-btn:hover {
            transform: scale(1.15);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.5);
            background: linear-gradient(135deg, #5b21b6, #4f46e5);
        }

        .profile-name {
            font-size: 32px;
            font-weight: 800;
            margin-top: 20px;
            letter-spacing: -0.8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-nim {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 1.2px;
            font-weight: 500;
            margin-top: 8px;
        }

        .profile-details {
            padding: 35px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }

        .detail-item:hover {
            background: rgba(241, 245, 249, 0.5);
            padding-left: 15px;
            padding-right: 15px;
            border-radius: 12px;
            margin: 0 -15px;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 16px;
        }

        .detail-value {
            color: var(--text-light);
            text-align: right;
            font-weight: 500;
            font-size: 15px;
        }

        .admin-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.25);
            color: white;
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .profile-actions {
            padding: 25px 35px;
            background: white;
            border-top: 1px solid rgba(226, 232, 240, 0.6);
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .profile-actions .btn {
            border-radius: 16px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .profile-actions .btn-outline-primary {
            color: #4f46e5;
            border-color: #4f46e5;
            background: rgba(79, 70, 229, 0.05);
        }

        .profile-actions .btn-outline-primary:hover {
            background: #4f46e5;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }

        .profile-actions .btn-outline-secondary {
            color: #6b7280;
            border-color: #6b7280;
            background: rgba(107, 114, 128, 0.05);
        }

        .profile-actions .btn-outline-secondary:hover {
            background: #6b7280;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(107, 114, 128, 0.3);
        }

        /* MODAL STYLES */
        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .modal-header {
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 20px 20px 0 0;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 20px 25px;
        }

        .modal-title {
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            opacity: 1;
            color: white;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 25px;
        }

        /* PASSWORD MODAL - REDUCED WHITE SPACE */
        #changePasswordModal .modal-dialog {
            max-width: 480px;
        }

        #changePasswordModal .modal-body {
            padding: 20px 25px 10px;
        }

        #changePasswordModal .modal-footer {
            border: none;
            padding: 5px 25px 25px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .form-label {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 15px;
        }

        .password-input-container {
            position: relative;
            margin-bottom: 15px;
        }

        .password-input-container .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 50px 14px 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 15px;
            background: rgba(248, 250, 252, 0.8);
        }

        .password-input-container .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.1);
            background: white;
        }

        .password-toggle-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .password-toggle-btn:hover {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
        }

        .password-help-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 6px;
            margin-bottom: 0;
        }

        /* MODAL BUTTONS */
        .modal-footer .btn {
            border-radius: 20px !important;
            padding: 12px 28px !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            min-width: 130px;
            border: none !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative;
            overflow: hidden;
        }

        .modal-footer .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .modal-footer .btn:hover::before {
            left: 100%;
        }

        .modal-footer .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563) !important;
            color: white !important;
            box-shadow: 0 8px 20px rgba(107, 114, 128, 0.25) !important;
        }

        .modal-footer .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563, #374151) !important;
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 30px rgba(107, 114, 128, 0.4) !important;
            color: white !important;
        }

        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: white !important;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25) !important;
        }

        .modal-footer .btn-primary:hover {
            background: linear-gradient(135deg, #7c3aed, #5b21b6) !important;
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.4) !important;
            color: white !important;
        }

        .modal-footer .btn-primary:disabled {
            background: linear-gradient(135deg, #d1d5db, #9ca3af) !important;
            color: #6b7280 !important;
            transform: none !important;
            box-shadow: none !important;
            cursor: not-allowed !important;
        }

        /* LOADING ANIMATION */
        .premium-loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2.5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: premium-spin 0.8s linear infinite;
        }

        @keyframes premium-spin {
            to { transform: rotate(360deg); }
        }

        /* FORGOT PASSWORD POPUP */
        .custom-forgot-password-popup {
            border-radius: 24px !important;
            padding: 0 !important;
            backdrop-filter: blur(10px) !important;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
        }

        .custom-forgot-password-popup .swal2-html-container {
            padding: 0 !important;
            margin: 0 !important;
        }

        .custom-confirm-button {
            border-radius: 20px !important;
            padding: 14px 32px !important;
            font-weight: 700 !important;
            font-size: 16px !important;
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            border: none !important;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .custom-confirm-button:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4) !important;
            background: linear-gradient(135deg, #7c3aed, #5b21b6) !important;
        }

        /* CONTACT CARDS */
        .contact-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 18px;
            padding: 25px 20px;
            flex: 1;
            max-width: 220px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .contact-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 35px rgba(14, 165, 233, 0.2);
            border-color: #0284c7;
        }

        .contact-card.email {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-color: #22c55e;
        }

        .contact-card.email:hover {
            border-color: #16a34a;
            box-shadow: 0 15px 35px rgba(34, 197, 94, 0.2);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .contact-card:hover .contact-icon {
            transform: scale(1.1) rotate(5deg);
        }

        /* FILE PREVIEW */
        #preview-image {
            border-radius: 50%;
            object-fit: cover;
            aspect-ratio: 1/1;
            width: 220px;
            height: 220px;
            border: 6px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        #preview-image:hover {
            transform: scale(1.05);
            border-color: #4f46e5;
        }

        .file-info {
            font-size: 13px;
            color: #6b7280;
            margin-top: 12px;
            padding: 12px;
            background: rgba(241, 245, 249, 0.8);
            border-radius: 10px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .student-profile-container {
                margin: 20px auto;
                padding: 0 15px;
            }

            .profile-header {
                padding: 30px 20px;
            }

            .avatar-container {
                width: 160px;
                height: 160px;
            }

            .profile-name {
                font-size: 26px;
            }

            .profile-details {
                padding: 25px 20px;
            }

            .profile-actions {
                flex-direction: column;
                padding: 20px;
            }

            .detail-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .detail-value {
                text-align: left;
            }
        }

        /* ANIMATIONS */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .student-profile-card {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4);
            }
        }
    </style>
@endpush

@section('content')
<div class="container my-5">
    <h1 class="mb-3 gradient-text fw-bold">
        @if($role === 'mahasiswa')
            Profil Mahasiswa
        @elseif($role === 'dosen')
            Profil Dosen
        @else
            Profil Administrator
        @endif
    </h1>
    <hr>
    
    <button class="btn btn-gradient mb-4 mt-4 d-flex align-items-center justify-content-center">
        @if($role === 'mahasiswa')
            <a href="{{ route('mahasiswa.usulanbimbingan') }}">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        @elseif($role === 'dosen')
            <a href="{{ route('dosen.persetujuan') }}">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        @else
            <a href="{{ route('admin.dashboard') }}">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        @endif
    </button>

    <div class="student-profile-container">
        <div class="student-profile-card">
            <div class="profile-header {{ $role === 'admin' ? 'admin' : '' }}">
                @if($role === 'admin')
                    <div class="admin-badge">
                        <i class="fas fa-shield-alt me-1"></i>Administrator
                    </div>
                @endif
                
                <div class="position-relative d-inline-block">
                    <div class="avatar-container">
                        <img src="{{ $profile->foto_url }}" 
                             alt="Foto Profil" 
                             class="student-avatar"
                             id="currentAvatar">
                    </div>
                    <button type="button" class="camera-btn" data-bs-toggle="modal" data-bs-target="#updateFotoModal" title="Ubah Foto Profil">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                
                <h2 class="profile-name">{{ $profile->nama ?? $profile->username ?? 'Tidak tersedia' }}</h2>
                <p class="profile-nim">
                    @if($role === 'mahasiswa')
                        NIM. {{ $profile->nim }}
                    @elseif($role === 'dosen')
                        NIP. {{ $profile->nip }}
                    @else
                        Admin ID. {{ $profile->id }} | {{ $profile->username }}
                    @endif
                </p>
            </div>
            
            <div class="profile-details">
                @if($role === 'mahasiswa')
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-graduation-cap me-2 text-primary"></i>Program Studi</span>
                        <span class="detail-value">{{ $profile->prodi->nama_prodi ?? 'Tidak tersedia' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-calendar-alt me-2 text-success"></i>Angkatan</span>
                        <span class="detail-value">{{ $profile->angkatan ?? 'Tidak tersedia' }}</span>
                    </div>
                    @if($profile->konsentrasi)
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-bullseye me-2 text-info"></i>Konsentrasi</span>
                        <span class="detail-value">{{ $profile->konsentrasi->nama_konsentrasi }}</span>
                    </div>
                    @endif
                @elseif($role === 'dosen')
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-university me-2 text-primary"></i>Program Studi</span>
                        <span class="detail-value">{{ $profile->prodi->nama_prodi ?? 'Tidak tersedia' }}</span>
                    </div>
                    @if(isset($profile->nama_singkat))
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-id-badge me-2 text-warning"></i>Nama Singkat</span>
                        <span class="detail-value">{{ $profile->nama_singkat }}</span>
                    </div>
                    @endif
                @else
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-user me-2 text-secondary"></i>Username</span>
                        <span class="detail-value">{{ $profile->username }}</span>
                    </div>
                    @if(isset($profile->nama))
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-id-card me-2 text-primary"></i>Nama Lengkap</span>
                        <span class="detail-value">{{ $profile->nama }}</span>
                    </div>
                    @endif
                @endif
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-envelope me-2 text-danger"></i>Email</span>
                    <span class="detail-value">{{ $profile->email ?? 'Tidak tersedia' }}</span>
                </div>
            </div>

            <div class="profile-actions">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fas fa-key me-2"></i>Ganti Password
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="showForgotPasswordInfo()">
                    <i class="fas fa-question-circle me-2"></i>Lupa Password?
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Foto -->
<div class="modal fade" id="updateFotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera"></i>Update Foto Profil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="fotoForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="foto" class="form-label">Pilih Foto Baru</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                        <small class="text-muted">Format: JPG, JPEG, PNG, GIF, WebP (Max: 2MB)</small>
                        
                        <div id="preview-container" class="mt-4 text-center" style="display: none;">
                            <img id="preview-image" src="" alt="Preview" class="img-thumbnail">
                            <div id="file-info" class="file-info mt-3"></div>
                        </div>
                    </div>
                </form>
                
                @if($profile->foto)
                    <hr class="my-4">
                    <button type="button" class="btn btn-danger w-100" onclick="confirmDeletePhoto()">
                        <i class="fas fa-trash me-2"></i>Hapus Foto Profil
                    </button>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="submit" form="fotoForm" class="btn btn-primary" id="uploadBtn">
                    <i class="fas fa-upload me-2"></i>Upload Foto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Change Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-key"></i>
                    Ganti Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    @csrf
                    
                    <!-- Current Password -->
                    <div class="password-input-container">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <div class="position-relative">
                            <input type="password" class="form-control" id="current_password" name="current_password" required placeholder="Masukkan password saat ini">
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye" id="current_password_icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="password-input-container">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <div class="position-relative">
                            <input type="password" class="form-control" id="new_password" name="new_password" required placeholder="Masukkan password baru">
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('new_password')">
                                <i class="fas fa-eye" id="new_password_icon"></i>
                            </button>
                        </div>
                        <small class="password-help-text">Minimal 6 karakter (bebas: huruf saja, angka saja, atau kombinasi)</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="password-input-container">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <div class="position-relative">
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required placeholder="Ulangi password baru">
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('new_password_confirmation')">
                                <i class="fas fa-eye" id="new_password_confirmation_icon"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="submit" form="passwordForm" class="btn btn-primary" id="changePasswordBtn">
                    <i class="fas fa-save me-2"></i>Ubah Password
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize elements with error checking
    const fotoInput = document.getElementById('foto');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    const fileInfo = document.getElementById('file-info');
    const uploadBtn = document.getElementById('uploadBtn');
    const currentAvatar = document.getElementById('currentAvatar');
    const fotoForm = document.getElementById('fotoForm');
    const passwordForm = document.getElementById('passwordForm');
    const changePasswordBtn = document.getElementById('changePasswordBtn');

    // Photo upload functionality
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                const maxSize = 2048000; // 2MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                
                if (file.size > maxSize) {
                    showAlert('Ukuran file terlalu besar! Maksimal 2MB.', 'error');
                    resetInput();
                    return;
                }

                if (!allowedTypes.includes(file.type)) {
                    showAlert('Format file tidak didukung! Gunakan: JPEG, PNG, GIF, atau WebP.', 'error');
                    resetInput();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    fileInfo.innerHTML = `
                        <div class="row g-2 text-start">
                            <div class="col-12"><strong><i class="fas fa-file me-1"></i>Nama:</strong> ${file.name}</div>
                            <div class="col-6"><strong><i class="fas fa-weight me-1"></i>Ukuran:</strong> ${formatFileSize(file.size)}</div>
                            <div class="col-6"><strong><i class="fas fa-image me-1"></i>Tipe:</strong> ${file.type.split('/')[1].toUpperCase()}</div>
                        </div>
                    `;
                    previewContainer.style.display = 'block';
                };
                reader.onerror = function() {
                    showAlert('Gagal membaca file. Silakan coba lagi.', 'error');
                    resetInput();
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    }

    // Photo form submission - FIXED VERSION tanpa efek button
    if (fotoForm) {
        fotoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const file = fotoInput.files[0];
            if (!file) {
                showAlert('Silakan pilih file foto terlebih dahulu!', 'error');
                return;
            }

            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<span class="premium-loading me-2"></span>Mengupload...';

            const formData = new FormData();
            formData.append('foto', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('_method', 'PUT');

            fetch('{{ route("profile.update") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update avatar langsung
                    currentAvatar.src = data.foto_url;
                    
                    // PAKSA tutup modal SEBELUM alert
                    const modal = bootstrap.Modal.getInstance(document.getElementById('updateFotoModal'));
                    if (modal) modal.hide();
                    
                    // Tunggu modal tertutup, baru reset form
                    setTimeout(() => {
                        resetFotoForm();
                        // Tampilkan alert success
                        showAlert(data.message, 'success');
                    }, 200);
                    
                } else {
                    showAlert(data.message, 'error');
                    
                    // Reset button ke state normal
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Upload Foto';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat mengupload foto.', 'error');
                
                // Reset button ke state normal
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Upload Foto';
            });
        });
    }

    // Password form submission - FIXED VERSION tanpa efek button
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;

            if (!currentPassword.trim()) {
                showAlert('Password saat ini wajib diisi!', 'error');
                return;
            }

            if (newPassword.length < 6) {
                showAlert('Password baru minimal 6 karakter!', 'error');
                return;
            }

            if (newPassword !== confirmPassword) {
                showAlert('Konfirmasi password tidak cocok!', 'error');
                return;
            }

            if (currentPassword === newPassword) {
                showAlert('Password baru harus berbeda dengan password saat ini!', 'warning');
                return;
            }

            changePasswordBtn.disabled = true;
            changePasswordBtn.innerHTML = '<span class="premium-loading me-2"></span>Mengubah...';

            const formData = new FormData();
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);
            formData.append('new_password_confirmation', confirmPassword);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route("profile.change-password") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // PAKSA tutup modal SEBELUM alert
                    const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                    if (modal) modal.hide();
                    
                    // Tunggu modal tertutup, baru reset form
                    setTimeout(() => {
                        resetPasswordForm();
                        // Tampilkan alert success
                        showAlert(data.message, 'success');
                    }, 200);
                    
                } else {
                    if (data.errors) {
                        let errorMessage = '';
                        Object.values(data.errors).forEach(errors => {
                            errors.forEach(error => {
                                errorMessage += `• ${error}<br>`;
                            });
                        });
                        showAlert(errorMessage, 'error');
                    } else {
                        showAlert(data.message, 'error');
                    }
                    
                    // Reset button ke state normal
                    changePasswordBtn.disabled = false;
                    changePasswordBtn.innerHTML = '<i class="fas fa-save me-2"></i>Ubah Password';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat mengubah password.', 'error');
                
                // Reset button ke state normal
                changePasswordBtn.disabled = false;
                changePasswordBtn.innerHTML = '<i class="fas fa-save me-2"></i>Ubah Password';
            });
        });
    }

    // Utility functions - TANPA EFEK BUTTON HIJAU
    function resetFotoForm() {
        if (fotoForm) {
            fotoForm.reset();
            previewContainer.style.display = 'none';
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Upload Foto';
            // HAPUS efek btn-success
        }
    }

    function resetPasswordForm() {
        if (passwordForm) {
            passwordForm.reset();
            changePasswordBtn.disabled = false;
            changePasswordBtn.innerHTML = '<i class="fas fa-save me-2"></i>Ubah Password';
            // HAPUS efek btn-success
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function resetInput() {
        if (fotoInput) {
            fotoInput.value = '';
            previewContainer.style.display = 'none';
        }
    }

    // Modal reset on close
    const modals = ['changePasswordModal', 'updateFotoModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                if (modalId === 'changePasswordModal') {
                    resetPasswordForm();
                } else if (modalId === 'updateFotoModal') {
                    resetFotoForm();
                }
            });
        }
    });

    // Enhanced modal cleanup - LEBIH AGRESIF
    function closeAllModals() {
        // Tutup semua modal yang sedang terbuka
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modalElement => {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        });
        
        // Paksa tutup semua modal (bahkan yang tidak terdeteksi)
        const allModals = document.querySelectorAll('.modal');
        allModals.forEach(modalElement => {
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.removeAttribute('aria-modal');
        });
        
        // Hapus SEMUA backdrop yang mungkin tertinggal
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Hapus kelas modal-open dari body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Reset z-index jika ada
        document.body.style.position = '';
        
        // Force refresh halaman jika masih ada masalah
        setTimeout(() => {
            if (document.querySelectorAll('.modal-backdrop').length > 0) {
                location.reload();
            }
        }, 100);
    }

    // Override global untuk memastikan modal tertutup saat success
    window.ensureModalsClosed = closeAllModals;
});

// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '_icon');
    
    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

// Confirm delete photo
function confirmDeletePhoto() {
    Swal.fire({
        title: 'Hapus Foto Profil?',
        text: 'Foto profil akan dikembalikan ke default. Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Ya, Hapus!',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            deletePhoto();
        }
    });
}

// Delete photo function
function deletePhoto() {
    Swal.fire({
        title: 'Menghapus foto...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('{{ route("profile.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            _method: 'DELETE'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('currentAvatar').src = data.foto_url;
            
            // Tutup modal foto
            const modal = bootstrap.Modal.getInstance(document.getElementById('updateFotoModal'));
            if (modal) modal.hide();
            
            // Pastikan semua modal tertutup
            if (window.ensureModalsClosed) window.ensureModalsClosed();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonColor: '#dc2626'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menghapus foto.',
            confirmButtonColor: '#dc2626'
        });
    });
}

// Forgot password info - UBAH KONTAK DI SINI
function showForgotPasswordInfo() {
    // Data kontak admin dari backend
    const adminContact = @json($adminContact ?? []);
    
    //⬇️ UBAH INFORMASI KONTAK ADMIN DI SINI ⬇️
    const adminPhone = adminContact.phone || '+62 812-6824-0068';  // ← GANTI NOMOR INI
    const adminEmail = adminContact.email || 'syahirahtrimeilinaa25@gmail.com';  // ← GANTI EMAIL INI
    const adminName = adminContact.name || 'Administrator SEPTI';  // ← GANTI NAMA INI
    const workingHours = adminContact.working_hours || 'Senin - Jumat, 08:00 - 17:00 WIB';  // ← GANTI JAM KERJA INI
    
    Swal.fire({
        title: '',
        html: `
            <div style="text-align: center; padding: 25px 15px;">
                <div style="margin-bottom: 30px;">
                    <div style="
                        width: 90px; 
                        height: 90px; 
                        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); 
                        border-radius: 50%; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center; 
                        margin: 0 auto 20px;
                        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.3);
                        animation: pulse 2s infinite;
                    ">
                        <i class="fas fa-question-circle" style="font-size: 2.8rem; color: white;"></i>
                    </div>
                    <h3 style="
                        margin: 0; 
                        font-size: 2rem; 
                        font-weight: 800; 
                        background: linear-gradient(135deg, #4f46e5, #7c3aed);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                        margin-bottom: 8px;
                    ">Lupa Password?</h3>
                    <p style="color: #6b7280; margin: 0; font-size: 1.1rem; font-weight: 500;">
                        Hubungi ${adminName} untuk reset password
                    </p>
                </div>

                <div style="display: flex; gap: 20px; justify-content: center; margin: 30px 0; flex-wrap: wrap;">
                    <div class="contact-card" onclick="window.open('tel:${adminPhone}', '_self')" style="
                        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
                        border: 2px solid #0ea5e9;
                        border-radius: 18px;
                        padding: 25px 20px;
                        flex: 1;
                        max-width: 220px;
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        cursor: pointer;
                        position: relative;
                        overflow: hidden;
                    ">
                        <div style="
                            width: 60px;
                            height: 60px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 18px;
                            transition: all 0.3s ease;
                            position: relative;
                            z-index: 1;
                            background: linear-gradient(135deg, #0ea5e9, #0284c7);
                        ">
                            <i class="fas fa-phone" style="color: white; font-size: 1.5rem;"></i>
                        </div>
                        <h6 style="margin: 0 0 10px; font-weight: 700; color: #0c4a6e; font-size: 1.1rem;">Telepon</h6>
                        <p style="
                            margin: 0; 
                            color: #0ea5e9; 
                            font-weight: 600;
                            font-size: 0.95rem;
                            word-break: break-all;
                        ">${adminPhone}</p>
                    </div>

                    <div class="contact-card email" onclick="window.open('mailto:${adminEmail}', '_blank')" style="
                        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
                        border: 2px solid #22c55e;
                        border-radius: 18px;
                        padding: 25px 20px;
                        flex: 1;
                        max-width: 220px;
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        cursor: pointer;
                        position: relative;
                        overflow: hidden;
                    ">
                        <div style="
                            width: 60px;
                            height: 60px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 18px;
                            transition: all 0.3s ease;
                            position: relative;
                            z-index: 1;
                            background: linear-gradient(135deg, #22c55e, #16a34a);
                        ">
                            <i class="fas fa-envelope" style="color: white; font-size: 1.5rem;"></i>
                        </div>
                        <h6 style="margin: 0 0 10px; font-weight: 700; color: #14532d; font-size: 1.1rem;">Email</h6>
                        <p style="
                            margin: 0; 
                            color: #22c55e; 
                            font-weight: 600;
                            font-size: 0.95rem;
                            word-break: break-all;
                        ">${adminEmail}</p>
                    </div>
                </div>

                <div style="
                    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                    border: 2px solid #f59e0b;
                    border-radius: 18px;
                    padding: 20px;
                    margin: 25px 0;
                ">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                        <div style="
                            width: 40px;
                            height: 40px;
                            background: linear-gradient(135deg, #f59e0b, #d97706);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        ">
                            <i class="fas fa-clock" style="color: white; font-size: 1.1rem;"></i>
                        </div>
                        <div style="text-align: left;">
                            <h6 style="margin: 0 0 4px; color: #92400e; font-weight: 700; font-size: 1.1rem;">Jam Kerja</h6>
                            <p style="margin: 0; color: #b45309; font-size: 0.95rem; font-weight: 600;">${workingHours}</p>
                        </div>
                    </div>
                </div>

                <div style="
                    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
                    border-radius: 15px;
                    padding: 20px;
                    margin-top: 25px;
                    border: 1px solid #d1d5db;
                ">
                    <p style="
                        margin: 0; 
                        color: #4b5563; 
                        font-size: 0.9rem; 
                        line-height: 1.6;
                        font-weight: 500;
                    ">
                        <i class="fas fa-lightbulb" style="color: #f59e0b; margin-right: 10px; font-size: 1.1rem;"></i>
                        <strong>Tips:</strong> Siapkan informasi akun Anda (NIM/NIP dan nama lengkap) saat menghubungi admin untuk mempercepat proses verifikasi.
                    </p>
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: '<i class="fas fa-check me-2"></i>Mengerti',
        confirmButtonColor: '#4f46e5',
        width: '600px',
        customClass: {
            popup: 'custom-forgot-password-popup',
            confirmButton: 'custom-confirm-button'
        }
    });
}

// Alert function - IMPROVED VERSION dengan paksa tutup modal
function showAlert(message, type) {
    // PAKSA tutup semua modal SEBELUM menampilkan alert
    if (window.ensureModalsClosed) {
        window.ensureModalsClosed();
    }
    
    // Tunggu sebentar untuk memastikan modal tertutup
    setTimeout(() => {
        const config = {
            html: message,
            showConfirmButton: true,
            timer: type === 'success' ? 4000 : null,
            timerProgressBar: true,
            allowOutsideClick: false,  // Cegah close dengan klik luar
            allowEscapeKey: false      // Cegah close dengan ESC
        };

        switch (type) {
            case 'success':
                config.icon = 'success';
                config.title = 'Berhasil!';
                config.confirmButtonColor = '#22c55e';
                // Setelah user klik OK, pastikan modal tertutup
                config.willClose = () => {
                    if (window.ensureModalsClosed) {
                        window.ensureModalsClosed();
                    }
                };
                break;
            case 'error':
                config.icon = 'error';
                config.title = 'Oops...';
                config.confirmButtonColor = '#dc2626';
                break;
            case 'warning':
                config.icon = 'warning';
                config.title = 'Peringatan!';
                config.confirmButtonColor = '#f59e0b';
                break;
            case 'info':
                config.icon = 'info';
                config.title = 'Informasi';
                config.confirmButtonColor = '#3b82f6';
                break;
        }

        Swal.fire(config);
    }, 100);
}

// Auto-hide session alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (alert.classList.contains('show')) {
            alert.style.animation = 'fadeOut 0.5s ease-out forwards';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }
    });
}, 5000);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Alt + P for profile photo update
    if (e.altKey && e.key === 'p') {
        e.preventDefault();
        const photoModal = new bootstrap.Modal(document.getElementById('updateFotoModal'));
        photoModal.show();
    }
    
    // Alt + K for change password
    if (e.altKey && e.key === 'k') {
        e.preventDefault();
        const passwordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
        passwordModal.show();
    }
});

// Page load animations
window.addEventListener('load', function() {
    const profileCard = document.querySelector('.student-profile-card');
    if (profileCard) {
        profileCard.style.opacity = '0';
        profileCard.style.transform = 'translateY(50px)';
        
        setTimeout(() => {
            profileCard.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            profileCard.style.opacity = '1';
            profileCard.style.transform = 'translateY(0)';
        }, 200);
    }
});
</script>
@endpush