@extends('layouts.app')
@section('title', 'Detail Mahasiswa')

@push('styles')
    <style>
        .status-badge {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 15px;
            color: white;
            border-radius: 5px;
            font-size: 0.9em;
        }

        .text-bold {
            font-weight: 600;
        }

        /* Perbaikan untuk text wrapping */
        .text-content {
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: pre-wrap;
            max-width: 100%;
            line-height: 1.5;
        }

        .keterangan-text {
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: pre-line;
            max-width: 100%;
            line-height: 1.6;
            font-size: 0.95em;
        }

        .deskripsi-text {
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: pre-line;
            max-width: 100%;
            line-height: 1.6;
            font-size: 0.95em;
        }

        /* Responsive text sizing */
        @media (max-width: 768px) {
            .keterangan-text,
            .deskripsi-text {
                font-size: 0.9em;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mt-5">
        <h1 class="mb-2 gradient-text fw-bold">Detail Mahasiswa</h1>
        <hr>
        <div class="card-header border-0 pb-0">
            <div class="d-flex">
                @php
                    $backRoute = '#';
                    if (auth()->guard('mahasiswa')->check()) {
                        $backRoute = match($origin ?? 'usulan') {
                            'usulan' => route('mahasiswa.usulanbimbingan', ['tab' => 'usulan']),
                            'jadwal' => route('mahasiswa.usulanbimbingan', ['tab' => 'jadwal']),
                            'riwayat' => route('mahasiswa.usulanbimbingan', ['tab' => 'riwayat']),
                            'detaildaftar' => url()->previous(),
                            default => route('mahasiswa.usulanbimbingan')
                        };
                    } elseif (auth()->guard('dosen')->check()) {
                        $backRoute = match($origin ?? 'usulan') {
                            'usulan' => route('dosen.persetujuan', ['tab' => 'usulan']),
                            'jadwal' => route('dosen.persetujuan', ['tab' => 'jadwal']),
                            'riwayat' => route('dosen.persetujuan', ['tab' => 'riwayat']),
                            'pengelola' => route('dosen.persetujuan', ['tab' => 'pengelola']),
                            'riwayatdetail' => url()->previous(),
                            'detaildaftar' => url()->previous(),
                            default => route('dosen.persetujuan')
                        };
                    } elseif (auth()->guard('admin')->check()) {
                        $backRoute = match($origin ?? 'dashboard') {
                            'detaildosen' => url()->previous(),
                            'detailriwayatdosen' => url()->previous(),
                            default => route('admin.dashboard')
                        };
                    }
                @endphp
                <a href="{{ $backRoute }}" class="btn btn-gradient mb-3">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <!-- Mahasiswa Info Card -->
                <div class="col-lg-6 col-md-12 bg-white rounded-start px-4 py-3 mb-2 shadow-sm">
                    <h5 class="text-bold">Mahasiswa</h5>
                    <hr>
                    <p class="card-title text-muted text-sm">Nama</p>
                    <p class="card-text text-start text-content">{{ $usulan->mahasiswa_nama }}</p>
                    <p class="card-title text-muted text-sm">NIM</p>
                    <p class="card-text text-start text-content">{{ $usulan->nim }}</p>
                    <p class="card-title text-muted text-sm">Program Studi</p>
                    <p class="card-text text-start text-content">{{ $usulan->nama_prodi }}</p>
                    <p class="card-title text-muted text-sm">Konsentrasi</p>
                    <p class="card-text text-start text-content">{{ $usulan->nama_konsentrasi }}</p>
                </div>

                <!-- Dosen Pembimbing Card -->
                <div class="col-lg-6 col-md-12 bg-white rounded-end px-4 py-3 mb-2 shadow-sm">
                    <h5 class="text-bold">Dosen Pembimbing</h5>
                    <hr>
                    <p class="card-title text-secondary text-sm">Nama</p>
                    <p class="card-text text-start text-content">{{ $usulan->dosen_nama }}</p>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <!-- Usulan Jadwal Card -->
                <div class="col-lg-6 col-md-12 px-4 py-3 mb-2 bg-white rounded-start shadow-sm">
                    <h5 class="text-bold">Data Usulan Jadwal Bimbingan</h5>
                    <hr>
                    <p class="card-title text-muted text-sm">Jenis Bimbingan</p>
                    <p class="card-text text-start text-content">{{ ucfirst($usulan->jenis_bimbingan) }}</p>
                    <p class="card-title text-muted text-sm">Tanggal</p>
                    <p class="card-text text-start text-content">{{ $tanggal }}</p>
                    <p class="card-title text-muted text-sm">Waktu</p>
                    <p class="card-text text-start text-content">{{ $waktuMulai }} - {{ $waktuSelesai }}</p>
                    <p class="card-title text-muted text-sm">Lokasi</p>
                    <p class="card-text text-start text-content">{{ $usulan->lokasi ?? '-' }}</p>
                    <p class="card-title text-muted text-sm">Antrian</p>
                    <p class="card-text text-start text-content">{{ $usulan->nomor_antrian ?? '-' }}</p>
                    <p class="card-title text-muted text-sm">Deskripsi</p>
                    @if($usulan->deskripsi && trim($usulan->deskripsi) !== '')
                        <div class="deskripsi-text">{{ $usulan->deskripsi }}</div>
                    @else
                        <p class="card-text text-start text-muted">Tidak ada deskripsi</p>
                    @endif
                </div>

                <!-- Keterangan Usulan Card -->
                <div class="col-lg-6 col-md-12 px-4 py-3 mb-2 bg-white rounded-end shadow-sm">
                    <h5 class="text-bold">Keterangan Usulan</h5>
                    <hr>
                    <p class="card-title text-secondary text-sm">Status Usulan</p>
                    <p class="card-text text-start">
                        <span class="status-badge {{ $statusBadgeClass }}">{{ strtoupper($usulan->status) }}</span>
                    </p>
                    <p class="card-title text-secondary text-sm">Keterangan</p>
                    @if($usulan->keterangan && trim($usulan->keterangan) !== '')
                        <div class="keterangan-text">{{ $usulan->keterangan }}</div>
                    @else
                        <p class="card-text text-start text-muted">Belum ada keterangan</p>
                    @endif
                    
                    @if ($usulan->created_at)
                        <p class="card-title text-secondary text-sm">Diajukan pada</p>
                        <p class="card-text text-start">
                            <span class="timestamp" data-timestamp="{{ $usulan->created_at }}">
                                {{ \Carbon\Carbon::parse($usulan->created_at)->locale('id')->isoFormat('D MMMM Y HH:mm') }}
                            </span>
                        </p>
                    @endif

                    @if ($usulan->updated_at && $usulan->status !== 'USULAN')
                        <p class="card-title text-secondary text-sm">Status Terakhir diupdate</p>
                        <p class="card-text text-start">
                            <span class="timestamp" data-timestamp="{{ $usulan->updated_at }}">
                                {{ \Carbon\Carbon::parse($usulan->updated_at)->locale('id')->isoFormat('D MMMM Y HH:mm') }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Format date to Indonesian format (15 November 2024 14:30)
        function formatDateTime(date) {
            const options = {
                timeZone: 'Asia/Jakarta',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            };

            return new Intl.DateTimeFormat('id-ID', options).format(new Date(date));
        }

        // Calculate time ago in Indonesian
        function timeAgo(date) {
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);

            let interval = Math.floor(seconds / 31536000);
            if (interval >= 1) {
                return interval === 1 ? '1 tahun yang lalu' : interval + ' tahun yang lalu';
            }

            interval = Math.floor(seconds / 2592000);
            if (interval >= 1) {
                return interval === 1 ? '1 bulan yang lalu' : interval + ' bulan yang lalu';
            }

            interval = Math.floor(seconds / 86400);
            if (interval >= 1) {
                return interval === 1 ? '1 hari yang lalu' : interval + ' hari yang lalu';
            }

            interval = Math.floor(seconds / 3600);
            if (interval >= 1) {
                return interval === 1 ? '1 jam yang lalu' : interval + ' jam yang lalu';
            }

            interval = Math.floor(seconds / 60);
            if (interval >= 1) {
                return interval === 1 ? '1 menit yang lalu' : interval + ' menit yang lalu';
            }

            if (seconds < 10) return 'baru saja';

            return Math.floor(seconds) + ' detik yang lalu';
        }

        // Update all timestamps on the page
        function updateTimestamps() {
            const timestampElements = document.querySelectorAll('.timestamp');

            timestampElements.forEach(element => {
                const timestamp = element.getAttribute('data-timestamp');
                if (timestamp) {
                    const absoluteTime = formatDateTime(timestamp);
                    const relativeTime = timeAgo(timestamp);
                    element.innerHTML = `${absoluteTime} <span class="text-muted">(${relativeTime})</span>`;
                }
            });
        }

        // Function to start the timestamp updates
        function initializeTimestamps() {
            // Initial update
            updateTimestamps();

            // Update every 30 seconds
            setInterval(updateTimestamps, 30000);

            // Add event listener for tab visibility changes
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    updateTimestamps();
                }
            });
        }

        // Start updating timestamps when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeTimestamps);
        } else {
            initializeTimestamps();
        }
    </script>
@endpush