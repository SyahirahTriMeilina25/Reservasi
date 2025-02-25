@extends('layouts.app')

@section('title', 'Pilih Jadwal Bimbingan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        form .form-label {
            font-weight: bold;
        }

        select.form-select option {
            color: black;
            font-weight: bold;
        }

        select.form-select option:disabled {
            color: #6c757d;
        }

        /* Info Box Styling */
        .info-box {
            background-color: #e8f0fe;
            border: 1px solid #1a73e8;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .info-box p {
            color: #1967d2;
            margin-bottom: 10px;
        }

        .info-box .btn-connect {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
        }

        .info-box .btn-connect:hover {
            background-color: #1557b0;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-5">
        <h1 class="mb-2 gradient-text fw-bold">Pilih Jadwal Bimbingan</h1>
        <hr>
        <button class="btn btn-gradient mb-4 mt-2">
            <a href="/usulanbimbingan" class="d-flex align-items-center justify-content-center">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </button>

        @if (!$isConnected)
            <div class="info-box">
                <p class="mb-2">Untuk menggunakan fitur ini, Anda perlu memberikan izin akses ke Google Calendar dengan
                    email: <strong>{{ Auth::guard('mahasiswa')->user()->email }}</strong></p>
                <a href="{{ route('mahasiswa.google.connect') }}" class="btn btn-connect">
                    <i class="fas fa-calendar-plus"></i>
                    Hubungkan dengan Google Calendar
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('pilihjadwal.store') }}" id="formBimbingan">
                @csrf
                <div class="mb-3">
                    <label for="pilihDosen" class="form-label">Pilih Dosen<span style="color: red;">*</span></label>
                    <select class="form-select" id="pilihDosen" name="nip" required>
                        <option value="" selected disabled>- Pilih Dosen -</option>
                        @foreach ($dosenList as $dosen)
                            <option value="{{ $dosen['nip'] }}">{{ $dosen['nama'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="jenisBimbingan" class="form-label">Pilih Jenis Bimbingan<span
                            style="color: red;">*</span></label>
                    <select class="form-select" id="jenisBimbingan" name="jenis_bimbingan" required>
                        <option value="" selected disabled>- Pilih Jenis Bimbingan -</option>
                        <option value="skripsi">Bimbingan Skripsi</option>
                        <option value="kp">Bimbingan KP</option>
                        <option value="akademik">Bimbingan Akademik</option>
                        <option value="konsultasi">Konsultasi Pribadi</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="pilihJadwal" class="form-label">Pilih Jadwal<span style="color: red;">*</span></label>
                    <select class="form-select" id="pilihJadwal" name="jadwal_id" required>
                        <option value="" selected disabled>- Pilih Dosen Terlebih Dahulu -</option>
                    </select>
                    <small class="text-muted">Menampilkan jadwal yang masih tersedia</small>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi<small class="text-muted"> (Opsional)</small></label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"
                        placeholder="Tuliskan deskripsi atau topik bimbingan Anda"></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-paper-plane me-2"></i>Usulkan
                    </button>
                </div>
            </form>
    </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil elemen yang diperlukan
            const formBimbingan = document.getElementById('formBimbingan');
            const dosenSelect = document.getElementById('pilihDosen');
            const jadwalSelect = document.getElementById('pilihJadwal');
            const jenisBimbinganSelect = document.getElementById('jenisBimbingan');

            // Fungsi untuk mengubah format tanggal ke bahasa Indonesia
            const formatTanggalIndonesia = (tanggal) => {
                const namaBulan = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                const namaHari = [
                    'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
                ];

                const date = new Date(tanggal);
                const hari = namaHari[date.getDay()];
                const tanggalNum = date.getDate();
                const bulan = namaBulan[date.getMonth()];
                const tahun = date.getFullYear();

                return `${hari}, ${tanggalNum} ${bulan} ${tahun}`;
            };

            // Fungsi untuk mengubah format waktu ke bahasa Indonesia
            const formatWaktuIndonesia = (waktu) => {
                return waktu.replace(/AM/gi, 'WIB').replace(/PM/gi, 'WIB');
            };

            // Fungsi untuk menampilkan pesan dengan SweetAlert2
            const tampilkanPesan = (icon, title, text) => {
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: text,
                    confirmButtonColor: '#1a73e8'
                });
            };

            // Hanya jalankan kode jika form bimbingan ada
            if (formBimbingan) {
                // Event listener untuk form submit
                formBimbingan.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    try {
                        // Validasi form
                        const formData = new FormData(formBimbingan);
                        const jadwalId = formData.get('jadwal_id');
                        const jenisBimbingan = formData.get('jenis_bimbingan');

                        // Cek ketersediaan jadwal
                        try {
                            const checkResponse = await fetch(
                                `/pilihjadwal/check?jadwal_id=${jadwalId}&jenis_bimbingan=${jenisBimbingan}`, {
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    }
                                });

                            if (!checkResponse.ok) {
                                throw new Error('Network response was not ok');
                            }

                            const checkResult = await checkResponse.json();

                            if (!checkResult.available) {
                                tampilkanPesan('warning', 'Tidak Dapat Mengajukan',
                                    checkResult.message || 'Jadwal tidak tersedia');
                                return;
                            }
                        } catch (error) {
                            console.error('Error checking availability:', error);
                            tampilkanPesan('error', 'Tidak dapat memeriksa jadwal',
                                'Silakan coba beberapa saat lagi');
                            return;
                        }

                        // Tampilkan loading saat mengirim data
                        Swal.fire({
                            title: 'Memproses',
                            text: 'Mohon tunggu...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Kirim data usulan bimbingan
                        const response = await fetch(formBimbingan.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(Object.fromEntries(formData))
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const result = await response.json();

                        if (result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pengajuan bimbingan berhasil dikirim',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = '/usulanbimbingan';
                            });
                        } else {
                            throw new Error(result.message || 'Server error');
                        }

                    } catch (error) {
                        console.error('Error submitting form:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak dapat memproses permintaan',
                            text: 'Silakan coba beberapa saat lagi',
                            confirmButtonColor: '#1a73e8'
                        });
                    }
                });
            }

            // Handler untuk perubahan dosen dan jenis bimbingan
            if (dosenSelect && jenisBimbinganSelect && jadwalSelect) {
                async function getAvailableJadwal() {
                    const nip = dosenSelect.value;
                    const jenisBimbingan = jenisBimbinganSelect.value;

                    if (!nip || !jenisBimbingan) {
                        jadwalSelect.innerHTML =
                            '<option value="" selected disabled>Pilih dosen dan jenis bimbingan terlebih dahulu</option>';
                        return;
                    }

                    try {
                        // Tampilkan loading state
                        jadwalSelect.innerHTML = '<option value="" selected disabled>Memuat jadwal...</option>';

                        const response = await fetch(
                            `/pilihjadwal/available?nip=${nip}&jenis_bimbingan=${jenisBimbingan}`, {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content,
                                    'Accept': 'application/json'
                                }
                            });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const result = await response.json();

                        // Reset select
                        jadwalSelect.innerHTML = '<option value="" selected disabled>- Pilih Jadwal -</option>';

                        if (result.status === 'success' && Array.isArray(result.data)) {
                            if (result.data.length === 0) {
                                jadwalSelect.innerHTML =
                                    '<option value="" disabled>Belum ada jadwal tersedia</option>';
                                tampilkanPesan('info', 'Informasi',
                                'Belum ada jadwal tersedia untuk dosen ini');
                                return;
                            }

                            // Render jadwal options dengan format Indonesia
                            result.data.forEach(jadwal => {
                                const option = document.createElement('option');
                                option.value = jadwal.id;
                                const tanggalIndonesia = formatTanggalIndonesia(jadwal.tanggal);
                                const waktuIndonesia = formatWaktuIndonesia(jadwal.waktu);
                                option.textContent = `${tanggalIndonesia} | ${waktuIndonesia}`;

                                if (jadwal.is_selected) {
                                    option.disabled = true;
                                    option.textContent += ' (Sudah dipilih)';
                                } else if (jadwal.sisa_kapasitas) {
                                    option.textContent += ` (Sisa Kuota: ${jadwal.sisa_kapasitas})`;
                                }

                                jadwalSelect.appendChild(option);
                            });
                        } else {
                            throw new Error('Invalid response format');
                        }

                    } catch (error) {
                        console.error('Error loading schedule:', error);
                        jadwalSelect.innerHTML = '<option value="" disabled>Tidak dapat memuat jadwal</option>';
                        tampilkanPesan('error', 'Tidak dapat memuat jadwal',
                            'Silakan muat ulang halaman dan coba kembali');
                    }
                }

                // Tambahkan event listeners
                dosenSelect.addEventListener('change', getAvailableJadwal);
                jenisBimbinganSelect.addEventListener('change', getAvailableJadwal);
            }
        });
    </script>
@endpush
