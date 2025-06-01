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
        }

        .student-profile-container {
            max-width: 800px;
            margin: 50px auto;
            perspective: 1000px;
        }

        .student-profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.4s ease-in-out;
            transform-style: preserve-3d;
        }

        .student-profile-card:hover {
            transform: rotateX(0) rotateY(0) scale(1);
            box-shadow: 0 30px 50px rgba(0, 0, 0, 0.15);
        }

        .profile-header {
            background: var(--primary-gradient);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .profile-header.admin {
            background: var(--admin-gradient);
        }

        /* PERBAIKAN AVATAR - MEMBUAT BULAT SEMPURNA */
        .avatar-container {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            overflow: hidden;
            display: inline-block;
            position: relative;
            border: 6px solid white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .avatar-container:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .student-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            border: none;
        }

        /* Tombol camera yang lebih baik */
        .camera-btn {
            position: absolute;
            bottom: -5px;
            right: -5px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 2px solid #dee2e6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .camera-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            background: #f8f9fa;
        }

        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-top: 15px;
            letter-spacing: -0.5px;
        }

        .profile-nim {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            letter-spacing: 1px;
        }

        .profile-details {
            padding: 30px;
            background: linear-gradient(to right, #f8f9fa, #f1f3f5);
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .detail-value {
            color: var(--text-light);
            text-align: right;
        }

        .admin-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .file-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        /* Preview image di modal */
        #preview-image {
            border-radius: 50%;
            object-fit: cover;
            aspect-ratio: 1/1;
            width: 200px;
            height: 200px;
            border: 4px solid #dee2e6;
        }

        /* Loading state */
        .loading {
            opacity: 0.6;
            pointer-events: none;
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
        {{-- Button Kembali --}}
        <button class="btn btn-gradient mb-4 mt-4 d-flex align-items-center justify-content-center">
            @if($role === 'mahasiswa')
                <a href="{{ route('mahasiswa.usulanbimbingan') }}" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>
            @elseif($role === 'dosen')
            <a href="{{ route('dosen.persetujuan') }}" class="text-white text-decoration-none">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
            @else
                <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none">
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
                
                {{-- PERBAIKAN CONTAINER AVATAR --}}
                <div class="position-relative d-inline-block">
                    <div class="avatar-container">
                        <img src="{{ $profile->foto_url }}" 
                             alt="Foto Profil" 
                             class="student-avatar"
                             id="currentAvatar">
                    </div>
                    <button type="button" class="camera-btn" data-bs-toggle="modal" data-bs-target="#updateFotoModal">
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
                        <span class="detail-label">Program Studi</span>
                        <span class="detail-value">{{ $profile->prodi->nama_prodi ?? 'Tidak tersedia' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Angkatan</span>
                        <span class="detail-value">{{ $profile->angkatan ?? 'Tidak tersedia' }}</span>
                    </div>
                    @if($profile->konsentrasi)
                    <div class="detail-item">
                        <span class="detail-label">Konsentrasi</span>
                        <span class="detail-value">{{ $profile->konsentrasi->nama_konsentrasi }}</span>
                    </div>
                    @endif
                @elseif($role === 'dosen')
                    <div class="detail-item">
                        <span class="detail-label">Program Studi</span>
                        <span class="detail-value">{{ $profile->prodi->nama_prodi ?? 'Tidak tersedia' }}</span>
                    </div>
                    @if(isset($profile->nama_singkat))
                    <div class="detail-item">
                        <span class="detail-label">Nama Singkat</span>
                        <span class="detail-value">{{ $profile->nama_singkat }}</span>
                    </div>
                    @endif
                @else
                    <div class="detail-item">
                        <span class="detail-label">Role</span>
                        <span class="detail-value">
                            <span class="badge bg-danger">{{ $profile->role->role_akses ?? 'Administrator' }}</span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Username</span>
                        <span class="detail-value">{{ $profile->username }}</span>
                    </div>
                    @if(isset($profile->nama))
                    <div class="detail-item">
                        <span class="detail-label">Nama Lengkap</span>
                        <span class="detail-value">{{ $profile->nama }}</span>
                    </div>
                    @endif
                    <div class="detail-item">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">
                            <span class="badge bg-success">Aktif</span>
                        </span>
                    </div>
                @endif
                
                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ $profile->email ?? 'Tidak tersedia' }}</span>
                </div>

                {{-- Info foto jika ada --}}
                @if($profile->foto)
                <div class="detail-item">
                    <span class="detail-label">Info Foto</span>
                    <span class="detail-value">
                        <div class="file-info">
                            <div>{{ $profile->foto->original_name ?? 'Foto Profil' }}</div>
                            <div>{{ number_format(($profile->foto->file_size ?? 0) / 1024, 1) }} KB</div>
                        </div>
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Foto -->
<div class="modal fade" id="updateFotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="fotoForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="foto" class="form-label">Pilih Foto Baru</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                        <small class="text-muted">Format: JPG, JPEG, PNG, GIF (Max: 2MB)</small>
                        
                        <!-- Preview foto -->
                        <div id="preview-container" class="mt-3 text-center" style="display: none;">
                            <img id="preview-image" src="" alt="Preview" class="img-thumbnail">
                            <div id="file-info" class="file-info mt-2"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="uploadBtn">Upload Foto</button>
                    </div>
                </form>
                
                @if($profile->foto)
                    <hr>
                    <form action="{{ route('profile.remove') }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Yakin ingin menghapus foto profil?')">Hapus Foto Profil</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-3" role="alert" style="z-index: 9999;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed bottom-0 end-0 m-3" role="alert" style="z-index: 9999;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@endsection

@push('scripts')
<script>
// Enhanced JavaScript untuk view (tambahkan ke @push('scripts'))
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('foto');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    const fileInfo = document.getElementById('file-info');
    const uploadBtn = document.getElementById('uploadBtn');
    const currentAvatar = document.getElementById('currentAvatar');

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // ✅ Enhanced validation
            const maxSize = 2048000; // 2MB
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            
            // Validasi ukuran file
            if (file.size > maxSize) {
                showError('Ukuran file terlalu besar! Maksimal 2MB.');
                resetInput();
                return;
            }

            // Validasi tipe file
            if (!allowedTypes.includes(file.type)) {
                showError('Format file tidak didukung! Gunakan: JPEG, PNG, GIF, atau WebP.');
                resetInput();
                return;
            }

            // ✅ Check image dimensions (optional)
            const img = new Image();
            img.onload = function() {
                // Optional: limit dimensions
                if (this.width > 2000 || this.height > 2000) {
                    showWarning('Gambar terlalu besar. Untuk hasil terbaik, gunakan gambar dengan ukuran maksimal 2000x2000 pixel.');
                }
            };

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                img.src = e.target.result; // For dimension check
                
                fileInfo.innerHTML = `
                    <div><strong>Nama:</strong> ${file.name}</div>
                    <div><strong>Ukuran:</strong> ${formatFileSize(file.size)}</div>
                    <div><strong>Tipe:</strong> ${file.type}</div>
                `;
                previewContainer.style.display = 'block';
            };
            
            reader.onerror = function() {
                showError('Gagal membaca file. Silakan coba lagi.');
                resetInput();
            };
            
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });

    // ✅ Enhanced form submission
    document.getElementById('fotoForm').addEventListener('submit', function(e) {
        const file = fotoInput.files[0];
        if (!file) {
            e.preventDefault();
            showError('Silakan pilih file foto terlebih dahulu!');
            return;
        }

        // Disable button untuk mencegah double submit
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
        
        // Add loading class to form
        this.classList.add('loading');
        
        // ✅ Set timeout untuk re-enable button jika error
        setTimeout(() => {
            if (uploadBtn.disabled) {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = 'Upload Foto';
                this.classList.remove('loading');
            }
        }, 30000); // 30 detik timeout
    });

    // ✅ Helper functions
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function showError(message) {
        // Create or update error alert
        showAlert(message, 'danger');
    }

    function showWarning(message) {
        showAlert(message, 'warning');
    }

    function showAlert(message, type) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.temp-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show temp-alert position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px;';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    function resetInput() {
        fotoInput.value = '';
        previewContainer.style.display = 'none';
    }

    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.temp-alert)');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('show')) {
                alert.classList.remove('show');
            }
        });
    }, 5000);
});
</script>
@endpush