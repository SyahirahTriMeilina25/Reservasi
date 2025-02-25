@extends('layouts.app')

@section('title', 'Riwayat Bimbingan')

@push('styles')
<style>
    .status-badge {
        display: inline-block;
        margin-top: 10px;
        padding: 5px 15px;
        background-color: #17a2b8;
        color: white;
        border-radius: 5px;
        font-size: 0.9em;
    }
    
    .text-bold {
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Riwayat Bimbingan</h1>
    <hr>
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ url('/usulanbimbingan#riwayat') }}">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </button>

    <div class="container">
        <div class="row">
            <!-- Mahasiswa Info Card -->
            <div class="col-lg-6 col-md-12 bg-white rounded-start px-4 py-3 mb-2 shadow-sm">
                <h5 class="text-bold">Mahasiswa</h5>
                <hr>
                <p class="card-title text-muted text-sm">Nama</p>
                <p class="card-text text-start">Syahirah Tri Meilina</p>
                <p class="card-title text-muted text-sm">NIM</p>
                <p class="card-text text-start">2107110255</p>
                <p class="card-title text-muted text-sm">Program Studi</p>
                <p class="card-text text-start">Teknik Informatika S1</p>
                <p class="card-title text-muted text-sm">Konsentrasi</p>
                <p class="card-text text-start">Rekayasa Perangkat Lunak</p>
            </div>

            <!-- Dosen Pembimbing Card -->
            <div class="col-lg-6 col-md-12 bg-white rounded-end px-4 py-3 mb-2 shadow-sm">
                <h5 class="text-bold">Dosen Pembimbing</h5>
                <hr>
                <p class="card-title text-secondary text-sm">Nama Pembimbing</p>
                <p class="card-text text-start">Edi Susilo, S.Pd., M.Kom., M.Eng.</p>
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
                <p class="card-text text-start">Bimbingan Skripsi</p>
                <p class="card-title text-muted text-sm">Tanggal</p>
                <p class="card-text text-start">Senin, 30 September 2024</p>
                <p class="card-title text-muted text-sm">Waktu</p>
                <p class="card-text text-start">13.30 - 16.00</p>
                <p class="card-title text-muted text-sm">Deskripsi</p>
                <p class="card-text text-start">Izin bapak, saya ingin melakukan bimbingan bab 1 skripsi saya pak</p>
            </div>

            <!-- Keterangan Usulan Card -->
            <div class="col-lg-6 col-md-12 px-4 py-3 mb-2 bg-white rounded-end shadow-sm">
                <h5 class="text-bold">Keterangan Usulan</h5>
                <hr>
                <p class="card-title text-secondary text-sm">Status Usulan</p>
                <p class="card-text text-start">
                    <span class="status-badge">SELESAI</span>
                </p>
                <p class="card-title text-secondary text-sm">Keterangan</p>
                <p class="card-text text-start">Usulan Jadwal Bimbingan Selesai</p>
            </div>
        </div>
    </div>
</div>
@endsection