@extends('layouts.app')

@section('title', 'Pilih Jadwal Bimbingan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        /* Style untuk form dan select */
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

        /* Styling untuk pilihan jadwal */
        .jadwal-tersedia {
            color: #16a34a;
        }
        
        .jadwal-penuh {
            color: #dc2626;
        }
        
        .jadwal-selesai {
            color: #6b7280;
        }
        
        select.form-select option:disabled {
            color: #dc2626 !important;
            font-style: italic;
        }

        /* ===== POPUP KONFIRMASI MODERN YANG RAPI ===== */
        /* Container utama */
        .clean-modal {
            border-radius: 16px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            padding: 0 !important;
            max-width: 400px !important;
            width: 90% !important;
        }

        /* Header biru */
        .clean-header {
            background-color: #0066cc;
            color: white;
            padding: 16px 20px;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            border-radius: 16px 16px 0 0;
        }

        /* Container untuk informasi */
        .clean-info {
            padding: 20px;
            background-color: #f5f8fc;
            padding-bottom: 5px !important;
        }

        /* Style untuk setiap item informasi - rata kiri */
        .clean-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px !important;
            padding-bottom: 8px !important;;
        }
        

        /* Area untuk ikon */
        .clean-icon {
            width: 40px;
            height: 40px;
            background-color: #e6f0ff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .clean-icon i {
            color: #0066cc;
            font-size: 16px;
        }

        /* Container teks - rata kiri */
        .clean-text {
            flex: 1;
            text-align: left;
        }

        /* Label - rata kiri */
        .clean-label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 3px;
            text-align: left;
        }

        /* Value - rata kiri */
        .clean-value {
            color: #111827;
            font-weight: 500;
            font-size: 15px;
            text-align: left;
        }

        /* Area pesan */
        .clean-message {
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .clean-question {
            color: #4b5563;
            font-size: 15px;
            margin: 0;
        }

        /* Area tombol */
        .clean-actions {
            display: flex;
            padding: 20px;
            gap: 12px;
        }

        /* Tombol batal */
        .clean-btn-cancel {
            flex: 1;
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .clean-btn-cancel:hover {
            background-color: #e5e7eb;
        }

        /* Tombol konfirmasi */
        .clean-btn-confirm {
            flex: 1;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .clean-btn-confirm:hover {
            background-color: #0055b3;
        }
        
    </style>
@endpush

@section('content')
<div id="dosenData" data-jenis-bimbingan="{{ json_encode($jenisBimbinganPerDosen ?? []) }}"></div>
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
                        <option value="mbkm">Bimbingan MBKM</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="pilihJadwal" class="form-label">Pilih Jadwal<span style="color: red;">*</span></label>
                    <select class="form-select" id="pilihJadwal" name="jadwal_id" required>
                        <option value="" selected disabled>- Pilih Dosen Terlebih Dahulu -</option>
                    </select>
                    {{-- <small class="text-muted">Menampilkan jadwal yang masih tersedia</small> --}}
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi<small class="text-muted"> (Opsional)</small></label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"
                        placeholder="Tuliskan deskripsi atau topik bimbingan Anda"></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-gradient">
                        Usulkan
                    </button>
                </div>
            </form>
    </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen yang diperlukan
        const formBimbingan = document.getElementById('formBimbingan');
        const dosenSelect = document.getElementById('pilihDosen');
        const jadwalSelect = document.getElementById('pilihJadwal');
        const jenisBimbinganSelect = document.getElementById('jenisBimbingan');
        
        
        // Ambil data jenis bimbingan per dosen dari elemen tersembunyi jika ada
        let jenisBimbinganPerDosen = {};
        const dosenDataElement = document.getElementById('dosenData');
        if (dosenDataElement && dosenDataElement.dataset.jenisBimbingan) {
            try {
                jenisBimbinganPerDosen = JSON.parse(dosenDataElement.dataset.jenisBimbingan);
                console.log('Data jenis bimbingan per dosen dari server:', jenisBimbinganPerDosen);
            } catch (e) {
                console.error('Error parsing jenis bimbingan data:', e);
            }
        }
    
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
    
        // Fungsi untuk menampilkan pesan dengan SweetAlert2
        const tampilkanPesan = (icon, title, text) => {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonColor: '#1a73e8'
            });
        };
    
        // Mapping nama jenis bimbingan
        const jenisOptions = {
            'akademik': 'Bimbingan Akademik',
            'kp': 'Bimbingan KP',
            'mbkm': 'Bimbingan MBKM',
            'skripsi': 'Bimbingan Skripsi',
            'konsultasi': 'Konsultasi Pribadi',
            'lainnya': 'Lainnya'
        };
    
        // Handler untuk perubahan dosen
        if (dosenSelect) {
            dosenSelect.addEventListener('change', function() {
                const selectedDosen = this.value;
                console.log('Dosen yang dipilih:', selectedDosen);
                
                // Reset jenis bimbingan dropdown
                jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>- Pilih Jenis Bimbingan -</option>';
                
                // Tampilkan loading state
                jenisBimbinganSelect.innerHTML += '<option value="" disabled>Memuat data...</option>';
                
                // Ambil jenis bimbingan dari server dengan endpoint yang lebih spesifik
                fetch(`/pilihjadwal/dosen/${selectedDosen}/jenis-bimbingan`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Reset dropdown setelah data diambil
                        jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>- Pilih Jenis Bimbingan -</option>';
                        
                        console.log('Respons API jenis bimbingan:', data);
                        
                        if (data.success) {
                            // Jika berhasil mendapatkan data jenis bimbingan
                            if (data.jenisBimbingan && data.jenisBimbingan.length > 0) {
                                console.log('Menampilkan jenis bimbingan tersedia:', data.jenisBimbingan);
                                
                                // Tampilkan jenis bimbingan yang tersedia dari server
                                data.jenisBimbingan.forEach(jenis => {
                                    const option = document.createElement('option');
                                    option.value = jenis;
                                    option.textContent = jenisOptions[jenis] || jenis;
                                    jenisBimbinganSelect.appendChild(option);
                                });
                                
                                // Jika hanya ada satu jenis bimbingan, otomatis pilih dan load jadwal
                                if (data.jenisBimbingan.length === 1) {
                                    console.log('Hanya satu jenis bimbingan tersedia, otomatis memilih:', data.jenisBimbingan[0]);
                                    jenisBimbinganSelect.value = data.jenisBimbingan[0];
                                    // Trigger event change untuk memuat jadwal
                                    getAvailableJadwal();
                                }
                            } else {
                                console.log('Tidak ada jenis bimbingan tersedia');
                                jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>Tidak ada jadwal tersedia</option>';
                                tampilkanPesan('info', 'Informasi', 'Dosen belum menyediakan jadwal bimbingan');
                            }
                        } else {
                            console.log('API mengembalikan status sukses=false');
                            jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>Terjadi kesalahan</option>';
                            tampilkanPesan('error', 'Terjadi Kesalahan', data.message || 'Tidak dapat memuat jenis bimbingan');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching jenis bimbingan:', error);
                        // Reset dropdown jika terjadi error
                        jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>Terjadi kesalahan</option>';
                        tampilkanPesan('error', 'Terjadi Kesalahan', 'Tidak dapat memuat jenis bimbingan. Silakan coba lagi.');
                    });
                
                // Reset jadwal dropdown
                jadwalSelect.innerHTML = '<option value="" selected disabled>- Pilih Jenis Bimbingan Terlebih Dahulu -</option>';
            });
        }
    
        // Function untuk mengambil jadwal
        async function getAvailableJadwal() {
            const nip = dosenSelect.value;
            const jenisBimbingan = jenisBimbinganSelect.value;
            
            console.log('Mengambil jadwal untuk:', {nip, jenisBimbingan});
    
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json'
                        }
                    });
    
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
    
                const result = await response.json();
                
                // LOG DEBUG
                console.log('Respons API jadwal:', result);
                
                // Log detail struktur data untuk debugging
                if (result.data && result.data.length > 0) {
                    console.log('Detail struktur jadwal pertama:', JSON.stringify(result.data[0], null, 2));
                }
    
                // Reset select
                jadwalSelect.innerHTML = '<option value="" selected disabled>- Pilih Jadwal -</option>';
    
                if (result.status === 'success' && result.data && Array.isArray(result.data)) {
                    if (result.data.length === 0) {
                        jadwalSelect.innerHTML =
                            '<option value="" disabled>Belum ada jadwal tersedia</option>';
                        tampilkanPesan('info', 'Informasi', 'Belum ada jadwal tersedia untuk dosen dan jenis bimbingan ini');
                        return;
                    }
    
                    // Render jadwal options
                    result.data.forEach(jadwal => {
                        const option = document.createElement('option');
                        option.value = jadwal.id;
                        
                        // Gunakan teks yang sudah diformat dari backend
                        if (jadwal.text) {
                            const isPenuh = jadwal.has_kuota_limit && jadwal.jumlah_pendaftar >= jadwal.kapasitas;
                        // PERUBAHAN #2: Tambahkan indikator status
                        if (isPenuh) {
                            option.textContent = jadwal.text + ' [PENUH]';
                            option.disabled = true;
                            option.style.color = '#dc2626'; // Merah
                            option.style.fontStyle = 'italic';
                        } else {
                            option.textContent = jadwal.text;
                        }
                        } else {
                            // Ekstrak tanggal dari waktu_mulai (format: YYYY-MM-DD HH:MM:SS)
                            const tanggalStr = jadwal.waktu_mulai ? jadwal.waktu_mulai.split(' ')[0] : (jadwal.tanggal || null);
                            const tanggalIndonesia = tanggalStr ? formatTanggalIndonesia(tanggalStr) : 'Tanggal tidak tersedia';
                            
                            // Format waktu dari waktu_mulai dan waktu_selesai
                            const waktuMulai = jadwal.waktu_mulai 
                                ? jadwal.waktu_mulai.split(' ')[1].substring(0, 5) 
                                : (jadwal.waktu || 'N/A');
                            
                            const waktuSelesai = jadwal.waktu_selesai 
                                ? jadwal.waktu_selesai.split(' ')[1].substring(0, 5) 
                                : 'N/A';
                            
                            // Tambahkan informasi dosen jika tersedia
                            let additionalInfo = '';
                            if (jadwal.dosen_nama) {
                                additionalInfo = ` - ${jadwal.dosen_nama}`;
                            }
                            
                            // Tambahkan informasi kuota jika tersedia
                            if (jadwal.kapasitas > 0) {
            // PERUBAHAN #4: Gunakan jumlah_pendaftar alih-alih pengajuanCount
            const pengajuanCount = jadwal.jumlah_pendaftar || 0;
            
            // PERUBAHAN #5: Tampilkan status berdasarkan kuota
            if (pengajuanCount >= jadwal.kapasitas) {
                additionalInfo += ` | Kuota: PENUH`;
                option.disabled = true;
                option.style.color = '#dc2626'; // Merah
            } else if (pengajuanCount > (jadwal.kapasitas * 0.7)) {
                // Hampir penuh (>70%)
                additionalInfo += ` | Kuota: ${pengajuanCount}/${jadwal.kapasitas} (Hampir Penuh)`;
                option.style.color = '#d97706'; // Kuning/Orange
            } else {
                additionalInfo += ` | Kuota: ${pengajuanCount}/${jadwal.kapasitas}`;
            }
        } else {
            additionalInfo += " | Kuota Tidak Terbatas";
        }
        
        option.textContent = `${tanggalIndonesia} | ${waktuMulai}-${waktuSelesai}${additionalInfo}`;
    }

    jadwalSelect.appendChild(option);
});
                    
                    // Jika hanya ada satu jadwal, otomatis pilih
                    if (result.data.length === 1) {
                        console.log('Hanya ada satu jadwal tersedia, otomatis memilih:', result.data[0].id);
                        jadwalSelect.value = result.data[0].id;
                    }
                } else {
                    throw new Error('Invalid response format');
                }
            } catch (error) {
                console.error('Error loading schedule:', error);
                jadwalSelect.innerHTML = '<option value="" disabled>Tidak dapat memuat jadwal</option>';
                tampilkanPesan('error', 'Tidak dapat memuat jadwal', 'Silakan muat ulang halaman dan coba kembali');
            }
        }
    
        // Tambahkan event listeners
        if (jenisBimbinganSelect) {
            jenisBimbinganSelect.addEventListener('change', getAvailableJadwal);
        }
    
        // Handler form submit
        if (formBimbingan) {
            formBimbingan.addEventListener('submit', async function(e) {
                e.preventDefault();
    
                try {
                    // Validasi form
                    const formData = new FormData(formBimbingan);
                    const jadwalId = formData.get('jadwal_id');
                    const jenisBimbingan = formData.get('jenis_bimbingan');
    
                    if (!jadwalId || !jenisBimbingan) {
                        tampilkanPesan('warning', 'Form tidak lengkap', 'Silakan pilih jadwal dan jenis bimbingan');
                        return;
                    }
    
                    // Cek ketersediaan jadwal
                    try {
                        // Tampilkan loading
                        Swal.fire({
                            title: 'Memeriksa jadwal',
                            text: 'Mohon tunggu...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
    
                        const checkResponse = await fetch(`/pilihjadwal/check?jadwal_id=${jadwalId}&jenis_bimbingan=${jenisBimbingan}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Accept': 'application/json'
                            }
                        });
    
                        if (!checkResponse.ok) {
                            throw new Error('Network response was not ok');
                        }
    
                        const checkResult = await checkResponse.json();
                        console.log('Respons cek ketersediaan:', checkResult);
    
                        if (!checkResult.available) {
                            Swal.close();
                            tampilkanPesan('warning', 'Tidak Dapat Mengajukan', 
                                checkResult.message || 'Jadwal tidak tersedia');
                            return;
                        }
    
                        // Konfirmasi pengajuan
                        const confirmResult = await Swal.fire({
    html: `
        <div class="clean-header">
            Konfirmasi Pengajuan
        </div>
        
        <div class="clean-info">
            <div class="clean-item">
                <div class="clean-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="clean-text">
                    <div class="clean-label">Dosen</div>
                    <div class="clean-value">${dosenSelect.options[dosenSelect.selectedIndex].text}</div>
                </div>
            </div>
            
            <div class="clean-item">
                <div class="clean-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="clean-text">
                    <div class="clean-label">Jenis Bimbingan</div>
                    <div class="clean-value">${jenisBimbinganSelect.options[jenisBimbinganSelect.selectedIndex].text}</div>
                </div>
            </div>
            
            <div class="clean-item">
                <div class="clean-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="clean-text">
                    <div class="clean-label">Jadwal</div>
                    <div class="clean-value">${jadwalSelect.options[jadwalSelect.selectedIndex].text.split(' |')[0]}</div>
                </div>
            </div>
        </div>
        
        <div class="clean-message">
            <p class="clean-question">
                Apakah Anda yakin ingin mengajukan bimbingan untuk jadwal ini?
            </p>
        </div>
        
        <div class="clean-actions">
            <button id="btnBatal" class="clean-btn-cancel">Batal</button>
            <button id="btnKonfirmasi" class="clean-btn-confirm">Ya, ajukan!</button>
        </div>
    `,
    showConfirmButton: false,
    showCancelButton: false,
    customClass: {
        popup: 'clean-modal'
    },
    width: 'auto',
    didOpen: () => {
        // Event listener untuk tombol batal
        document.getElementById('btnBatal').addEventListener('click', () => {
            Swal.close();
        });
        
        // Event listener untuk tombol konfirmasi
        document.getElementById('btnKonfirmasi').addEventListener('click', async () => {
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
            try {
                const formData = new FormData(formBimbingan);
                const response = await fetch(formBimbingan.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
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
                    confirmButtonColor: '#0066cc'
                });
            }
        });
    }
});
    
                        if (!confirmResult.isConfirmed) {
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
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
                        console.error('Error checking availability:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak dapat memeriksa jadwal',
                            text: 'Silakan coba beberapa saat lagi',
                            confirmButtonColor: '#1a73e8'
                        });
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
    });</script>
@endpush
