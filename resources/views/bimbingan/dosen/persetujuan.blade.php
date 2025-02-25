@extends('layouts.app')

@section('title', 'Persetujuan Bimbingan')

@push('styles')
    <style>
        .gradient-text {
            background: linear-gradient(to right, #059669, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-gradient {
            background: linear-gradient(to right, #4ade80, #3b82f6);
            border: none;
            color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            z-index: 1;
            cursor: pointer;
        }

        .btn-gradient a {
            color: white;
            text-decoration: none;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-gradient:hover a {
            color: black;
        }

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

        .approve-icon {
            background-color: #28a745;
            color: white !important;
        }

        .reject-icon {
            background-color: #dc3545;
            color: white !important;
        }

        .edit-icon {
            background-color: #F3B806;
            color: white !important;
        }

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
        }

        .page-item.disabled .page-link {
            color: #9ca3af;
            background-color: #ffffff;
            border-color: #e5e7eb;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-5">
        <h1 class="mb-2 gradient-text fw-bold">Persetujuan Bimbingan</h1>
        <hr>
        <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
            <a href="{{ route('dosen.jadwal.index') }}">
                <i class="bi bi-plus-lg me-2"></i> Masukkan Jadwal Bimbingan
            </a>
        </button>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs" id="bimbinganTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'usulan', 'per_page' => request('per_page', 10)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'usulan' ? 'active' : '' }}">
                            Usulan Bimbingan
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'jadwal', 'per_page' => request('per_page', 10)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'jadwal' ? 'active' : '' }}">
                            Daftar Jadwal
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'riwayat', 'per_page' => request('per_page', 10)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'riwayat' ? 'active' : '' }}">
                            Riwayat
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <label class="me-2">Tampilkan</label>
                            <select class="form-select form-select-sm w-auto"
                                onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => $activeTab]) }}&per_page=' + this.value">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                            </select>
                            <label class="ms-2">entries</label>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="bimbinganTabContent">
                    @if ($activeTab == 'usulan')
                        <div class="tab-pane fade show active" id="usulan" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Jenis Bimbingan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Lokasi</th>
                                            <th>Antrian</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($usulan as $index => $item)
                                            <tr class="text-center" data-id="{{ $item->id }}">
                                                <td>{{ ($usulan->currentPage() - 1) * $usulan->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $item->nim }}</td>
                                                <td>{{ $item->mahasiswa_nama }}</td>
                                                <td>{{ ucfirst($item->jenis_bimbingan) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                                <td>{{ $item->lokasi && trim($item->lokasi) !== '' ? $item->lokasi : '-' }}
                                                </td>
                                                <td>{{ $item->nomor_antrian && trim($item->nomor_antrian) !== '' ? $item->nomor_antrian : '-' }}</td>
                                                <td
                                                    class="fw-bold bg-{{ $item->status === 'DISETUJUI' ? 'success' : ($item->status === 'DITOLAK' ? 'danger' : 'warning') }} text-white">
                                                    {{ $item->status }}</td>
                                                <td>
                                                    <div class="action-icons">
                                                        @if ($item->status == 'USULAN')
                                                            <a href="#" class="action-icon approve-icon"
                                                                data-bs-toggle="tooltip" title="Setujui">
                                                                <i class="bi bi-check-lg"></i>
                                                            </a>
                                                            <a href="#" class="action-icon reject-icon"
                                                                data-bs-toggle="tooltip" title="Tolak">
                                                                <i class="bi bi-x-lg"></i>
                                                            </a>
                                                        @endif
                                                        <div class="action-icons">
                                                            <a href="{{ route('dosen.detailbimbingan', $item->id) }}"
                                                                class="action-icon info-icon" data-bs-toggle="tooltip"
                                                                title="Info">
                                                                <i class="bi bi-info-circle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data usulan bimbingan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($activeTab == 'jadwal')
                        <div class="tab-pane fade show active" id="jadwal" role="tabpanel">
                            <!-- Google Calendar Integration -->
                            <div class="mb-4">
                                @if (auth()->user()->hasGoogleCalendarConnected())
                                    <div class="card mb-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Google Calendar</h5>
                                            <div>
                                                @if (auth()->user()->isGoogleTokenExpired())
                                                    <a href="{{ route('dosen.google.connect') }}"
                                                        class="btn btn-sm btn-warning me-2">
                                                        <i class="bi bi-arrow-clockwise me-1"></i> Hubungkan Ulang
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="ratio ratio-16x9">
                                                <iframe
                                                    src="https://calendar.google.com/calendar/embed?src={{ urlencode(auth()->user()->email) }}&mode=WEEK&showPrint=0&showCalendars=0&showTz=0&hl=id"
                                                    style="border: 0" frameborder="0" scrolling="no"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        <div>
                                            Anda perlu menghubungkan Google Calendar jika ingin menggunakan fitur Kalender.
                                            <a href="{{ route('dosen.google.connect') }}" class="alert-link">
                                                Klik di sini untuk menghubungkan
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Tabel daftar mahasiswa yang disetujui -->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Jenis Bimbingan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Lokasi</th>
                                            <th>Antrian</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($jadwal as $index => $item)
                                            <tr class="text-center">
                                                <td>{{ ($jadwal->currentPage() - 1) * $jadwal->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $item->nim }}</td>
                                                <td>{{ $item->mahasiswa_nama }}</td>
                                                <td>{{ ucfirst($item->jenis_bimbingan) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                                <td>{{ $item->lokasi && trim($item->lokasi) !== '' ? $item->lokasi : '-' }}
                                                </td>
                                                <td>{{ $item->nomor_antrian && trim($item->nomor_antrian) !== '' ? $item->nomor_antrian : '-' }}</td>
                                                <td class="fw-bold text-white bg-success">DISETUJUI</td>
                                                <td>
                                                    <div class="action-icons">
                                                        <a href="{{ route('dosen.detailbimbingan', $item->id) }}"
                                                            class="action-icon info-icon" data-bs-toggle="tooltip"
                                                            title="Info">
                                                            <i class="bi bi-info-circle"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada jadwal bimbingan aktif</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($activeTab == 'riwayat')
                        <div class="tab-pane fade show active" id="riwayat" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Jenis Bimbingan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Lokasi</th>
                                            <th>Antrian</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($riwayat as $index => $item)
                                            <tr class="text-center">
                                                <td>{{ ($riwayat->currentPage() - 1) * $riwayat->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $item->nim }}</td>
                                                <td>{{ $item->mahasiswa_nama }}</td>
                                                <td>{{ ucfirst($item->jenis_bimbingan) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                                <td>{{ $item->lokasi && trim($item->lokasi) !== '' ? $item->lokasi : '-' }}
                                                </td>
                                                <td>{{ $item->nomor_antrian && trim($item->nomor_antrian) !== '' ? $item->nomor_antrian : '-' }}</td>
                                                <td
                                                    class="fw-bold {{ $item->status === 'SELESAI' ? 'bg-success' : 'bg-danger' }} text-white">
                                                    {{ $item->status }}
                                                </td>
                                                <td>
                                                    <div class="action-icons">
                                                        <a href="{{ route('dosen.detailbimbingan', $item->id) }}"
                                                            class="action-icon info-icon" data-bs-toggle="tooltip"
                                                            title="Info">
                                                            <i class="bi bi-info-circle"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data riwayat bimbingan
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-3">
                    <p class="mb-2">
                        @if ($activeTab == 'usulan' && $usulan->total() > 0)
                            Menampilkan {{ $usulan->firstItem() }} sampai {{ $usulan->lastItem() }} dari
                            {{ $usulan->total() }} entri
                        @elseif($activeTab == 'jadwal' && $jadwal->total() > 0)
                            Menampilkan {{ $jadwal->firstItem() }} sampai {{ $jadwal->lastItem() }} dari
                            {{ $jadwal->total() }} entri
                        @elseif($activeTab == 'riwayat' && $riwayat->total() > 0)
                            Menampilkan {{ $riwayat->firstItem() }} sampai {{ $riwayat->lastItem() }} dari
                            {{ $riwayat->total() }} entri
                        @endif
                    </p>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                            @if ($activeTab == 'usulan')
                                @if ($usulan->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">Sebelumnya</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $usulan->previousPageUrl() }}&tab=usulan">Sebelumnya</a>
                                    </li>
                                @endif

                                @foreach ($usulan->getUrlRange(1, $usulan->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $usulan->currentPage() ? 'active' : '' }}">
                                        <a class="page-link"
                                            href="{{ $url }}&tab=usulan">{{ $page }}</a>
                                    </li>
                                @endforeach

                                @if ($usulan->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $usulan->nextPageUrl() }}&tab=usulan">Selanjutnya</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Selanjutnya</span>
                                    </li>
                                @endif
                            @elseif($activeTab == 'jadwal')
                                <!-- Similar pagination structure for jadwal -->
                            @elseif($activeTab == 'riwayat')
                                <!-- Similar pagination structure for riwayat -->
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Terima -->
    <div class="modal fade" id="modalTerima" tabindex="-1" aria-labelledby="modalTerimaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalTerimaLabel">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Terima Usulan Bimbingan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form Lokasi -->
                    <div class="form-group">
                        <label for="lokasiBimbingan" class="form-label fw-bold">Lokasi Bimbingan <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-location-dot"></i>
                            </span>
                            <input type="text" class="form-control" id="lokasiBimbingan" required
                                placeholder="Contoh: Ruang Dosen Lt.2, Meeting Room, atau Link Meeting">
                        </div>
                        <div class="invalid-feedback">Lokasi bimbingan wajib diisi</div>
                        <small class="text-muted">Masukkan lokasi fisik atau link meeting untuk bimbingan online</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-success" id="confirmTerima">
                        <i class="fas fa-check me-2"></i>Setujui Usulan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalTolakLabel">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        Tolak Usulan Bimbingan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form Alasan -->
                    <div class="form-group">
                        <label for="alasanPenolakan" class="form-label fw-bold">Alasan Penolakan <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-comment-alt"></i>
                            </span>
                            <textarea class="form-control" id="alasanPenolakan" rows="3" required
                                placeholder="Contoh: Jadwal bertabrakan dengan kegiatan lain, Mohon ajukan di waktu lain"></textarea>
                        </div>
                        <div class="invalid-feedback">Alasan penolakan wajib diisi</div>
                        <small class="text-muted">Berikan alasan yang jelas agar mahasiswa dapat mengajukan ulang dengan
                            penyesuaian</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmTolak">
                        <i class="fas fa-times me-2"></i>Tolak Usulan
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
    let currentRow = null;
    let currentId = null;

    function initializeTooltips() {
        if (typeof bootstrap !== 'undefined') {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => {
                if (!bootstrap.Tooltip.getInstance(tooltip)) {
                    new bootstrap.Tooltip(tooltip);
                }
            });
        }
    }

    initializeTooltips();

    // Function to update row after approval/rejection
    function updateRowAfterAction(row, id, lokasi, status) {
        if (!row) return;

        const statusCell = row.querySelector('td:nth-child(9)'); // Adjusted to correct column
        if (statusCell) {
            statusCell.textContent = status;
            statusCell.className = 'fw-bold text-white';

            if (status === 'DISETUJUI') {
                statusCell.classList.add('bg-success');
            } else if (status === 'DITOLAK') {
                statusCell.classList.add('bg-danger');
            } else {
                statusCell.classList.add('bg-warning');
            }
        }

        if (lokasi) {
            const lokasiCell = row.querySelector('td:nth-child(7)');
            if (lokasiCell) {
                lokasiCell.textContent = lokasi;
            }
        }

        const actionCell = row.querySelector('.action-icons');
        if (actionCell) {
            actionCell.innerHTML = `
                <a href="/dosen/detailbimbingan/${id}" 
                   class="action-icon info-icon" 
                   data-bs-toggle="tooltip" 
                   title="Info">
                    <i class="bi bi-info-circle"></i>
                </a>`;
            initializeTooltips();
        }
    }

    // Setup modal handling for approve action
    document.querySelectorAll('.approve-icon').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            currentRow = this.closest('tr');
            currentId = currentRow.getAttribute('data-id');

            if (!currentRow || !currentId) return;

            const modalInstance = new bootstrap.Modal(document.getElementById('modalTerima'));
            modalInstance.show();
        });
    });

    // Setup modal handling for reject action
    document.querySelectorAll('.reject-icon').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            currentRow = this.closest('tr');
            currentId = currentRow.getAttribute('data-id');

            if (!currentRow || !currentId) return;

            const modalInstance = new bootstrap.Modal(document.getElementById('modalTolak'));
            modalInstance.show();
        });
    });

    // Handle approve confirmation
    document.getElementById('confirmTerima')?.addEventListener('click', async function() {
        const lokasiInput = document.getElementById('lokasiBimbingan');
        if (!lokasiInput || !currentId || !currentRow) return;

        const lokasi = lokasiInput.value.trim();
        if (!lokasi) {
            lokasiInput.classList.add('is-invalid');
            return;
        }

        try {
            this.disabled = true;

            const response = await fetch(`/persetujuan/terima/${currentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    lokasi: lokasi
                })
            });

            const data = await response.json();

            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTerima'));
                if (modal) modal.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Usulan bimbingan berhasil disetujui',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat memproses usulan'
            });
        } finally {
            this.disabled = false;
        }
    });

    // Handle reject confirmation
    document.getElementById('confirmTolak')?.addEventListener('click', async function() {
        const alasanInput = document.getElementById('alasanPenolakan');
        if (!alasanInput || !currentId || !currentRow) return;

        const alasan = alasanInput.value.trim();
        if (!alasan) {
            alasanInput.classList.add('is-invalid');
            return;
        }

        try {
            this.disabled = true;

            const response = await fetch(`/persetujuan/tolak/${currentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    keterangan: alasan
                })
            });

            const data = await response.json();

            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTolak'));
                if (modal) modal.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Usulan bimbingan telah ditolak',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat memproses usulan'
            });
        } finally {
            this.disabled = false;
        }
    });

    // Handle modal cleanup
    ['modalTerima', 'modalTolak'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        modal?.addEventListener('hidden.bs.modal', function() {
            const input = modalId === 'modalTerima' ?
                document.getElementById('lokasiBimbingan') :
                document.getElementById('alasanPenolakan');

            if (input) {
                input.classList.remove('is-invalid');
                input.value = '';
            }

            currentRow = null;
            currentId = null;
        });
    });
});
</script>
@endpush
