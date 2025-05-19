@extends('layouts.app')

@section('title', 'Persetujuan Bimbingan')

@push('styles')
<style>

    /* Style untuk event yang dibatalkan */
.cancelled-event {
    text-decoration: line-through !important;
    opacity: 0.7 !important;
    background-color: #6c757d !important;
    border-color: #6c757d !important;
}

/* Style untuk tombol batalkan */
.fc-event .cancel-button {
    position: absolute;
    top: 2px;
    right: 2px;
    background: rgba(255,255,255,0.7);
    border: none;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    line-height: 16px;
    font-size: 14px;
    color: #dc3545;
    cursor: pointer;
    z-index: 10;
    display: none;
}

.fc-event:hover .cancel-button {
    display: block;
}

/* Style untuk notifikasi undangan */
.notification-popup {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 300px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    border-left: 4px solid #28a745;
    overflow: hidden;
    z-index: 9999;
    animation: slideInUp 0.3s ease-out;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.notification-header .close-btn {
    border: none;
    background: none;
    font-size: 20px;
    cursor: pointer;
    color: #6c757d;
}

.notification-body {
    padding: 15px;
}

/* Animasi untuk notifikasi */
@keyframes slideInUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slideOutDown {
    from { transform: translateY(0); opacity: 1; }
    to { transform: translateY(100%); opacity: 0; }
}
    /* ==============================================
       1. STYLE UNTUK HALAMAN UTAMA (KOMPONEN CARD & TABEL)
       ============================================== */
    /* Action icons di tabel bimbingan */
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
    
    /* Style untuk card */
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px;
    }
    
    .card-header h5 {
        margin-bottom: 0;
        font-weight: 600;
        color: #333;
    }
    
    .card-body {
        padding: 20px;
    }
    
    /* Style untuk paginasi */
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
    
    /* Styling umum untuk tabel */
    .table {
        margin-bottom: 0;
        border-color: #f0f0f0;
        border-collapse: collapse !important;
    }
    
    .table th {
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 600;
        border-top: none;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6 !important;
        padding: 12px 10px;
    }
    
    .table td {
        vertical-align: middle;
        border: 1px solid #dee2e6 !important;
        padding: 12px 10px;
        border-color: #f0f0f0;
    }
    
    /* ==============================================
       2. STYLE UMUM UNTUK SEMUA MODAL
       ============================================== */
    /* Base style untuk modal */
    .modal .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .modal .modal-header {
        padding: 12px 15px;
        border-bottom: none;
    }
    
    .modal .modal-body {
        padding: 15px;
    }
    
    .modal .modal-footer {
        padding: 12px 16px;
        background-color: #f8f9fa;
        border-top: 1px solid #eaeaea;
        text-align: center;
        justify-content: center;
    }
    
    /* Animasi masuk modal */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: scale(0.95);
        opacity: 0;
    }
    
    .modal.show .modal-dialog {
        transform: scale(1);
        opacity: 1;
    }
    
    /* Tombol close untuk semua modal */
    .close-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 18px;
        line-height: 1;
        padding: 0;
        margin: 0;
        opacity: 0.7;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .close-btn:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.3);
    }
    
    /* Style umum untuk tombol */
    .modal .btn {
        min-width: 100px;
        font-size: 14px;
        padding: 6px 15px;
        display: inline-flex;
        align-items: center;
    }
    
    /* Style untuk tombol Batal di semua modal */
    .modal .btn-secondary,
    #modalBatal .btn-secondary,
    #modalTerima .btn-secondary,
    #modalTolak .btn-secondary {
        background-color: #4b5563 !important; /* Abu-abu gelap */
        border-color: #374151 !important;
        color: white !important;
        text-align: center !important;
        padding: 6px 15px !important;
        box-shadow: none !important;
        display: flex; /* Ganti inline-flex menjadi flex */
        justify-content: center; /* Memastikan teks di tengah horizontal */
        align-items: center; /* Memastikan teks di tengah vertikal */
    }
    
    .modal .btn-secondary:hover,
    #modalBatal .btn-secondary:hover,
    #modalTerima .btn-secondary:hover,
    #modalTolak .btn-secondary:hover {
        background-color: #374151 !important; /* Abu-abu lebih gelap saat hover */
        border-color: #1f2937 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }
    
    /* ==============================================
       3. MODAL APPROVAL (PARENT CLASS UNTUK MODAL TERIMA & TOLAK)
       ============================================== */
    .modal-approval .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
        overflow: hidden;
    }
    
    .modal-approval .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
    }
    
    .modal-approval .modal-title i {
        margin-right: 12px;
        background-color: rgba(255, 255, 255, 0.2);
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    .modal-approval .btn-close {
        background-color: rgba(255, 255, 255, 0.3);
        opacity: 1;
        padding: 8px;
        border-radius: 50%;
        transition: background-color 0.2s;
    }
    
    .modal-approval .btn-close:hover {
        background-color: rgba(255, 255, 255, 0.5);
    }
    
    .modal-approval .modal-body {
        padding: 25px;
    }
    
    .modal-approval .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 10px;
    }
    
    .modal-approval .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s;
    }
    
    .modal-approval .form-control:focus {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
        border-color: #3b82f6;
    }
    
    .modal-approval .input-group-text {
        background-color: #f3f4f6;
        border-color: #e5e7eb;
        color: #4b5563;
        border-radius: 10px 0 0 10px;
    }
    
    .modal-approval .input-group .form-control {
        border-radius: 0 10px 10px 0;
    }
    
    .modal-approval .modal-footer {
        padding: 16px 25px;
        border-top: 1px solid #f3f4f6;
        background-color: #f9fafb;
    }
    
    /* Tombol aksi */
    .modal-approval .btn {
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 10px;
        transition: all 0.2s;
    }
    
    .modal-approval .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .modal-approval .btn-secondary {
        background-color: #e5e7eb;
        color: #4b5563;
        border: none;
    }
    
    .modal-approval .btn-secondary:hover {
        background-color: #d1d5db;
    }
    
    /* ==============================================
       4. MODAL TERIMA USULAN
       ============================================== */
    /* Header modal terima */
    .modal-approval.accept .modal-header {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        color: white;
        border-bottom: none;
        padding: 20px 25px;
    }
    
    /* Header modal terima dengan warna baru */
    #modalTerima .modal-header {
        background: linear-gradient(135deg, #0ca678 0%, #087f5b 100%);
    }
    
    /* Tombol setujui */
    .modal-approval .btn-success {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        border: none;
    }
    
    #modalTerima .btn-success {
        background: linear-gradient(135deg, #0ca678 0%, #087f5b 100%);
        border: none;
    }
    
    /* ==============================================
       5. MODAL TOLAK USULAN
       ============================================== */
    /* Header modal tolak */
    .modal-approval.reject .modal-header {
        background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
        color: white;
        border-bottom: none;
        padding: 20px 25px;
    }
    
    #modalTolak .modal-header {
        background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
    }
    
    /* Tombol tolak */
    .modal-approval .btn-danger {
        background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
        border: none;
    }
/* ==============================================
       6. MODAL BATAL PERSETUJUAN
       ============================================== */
    /* Ukuran modal */
    #modalBatal .modal-dialog {
        max-width: 450px;
        margin: 1rem auto;
        transition: all 0.3s ease;
    }
    
    /* Animasi untuk modal */
    #modalBatal .modal-content {
        animation: fade-in 0.3s ease;
    }
    
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Header modal */
    #modalBatal .modal-header {
        background: linear-gradient(135deg, #f53844 0%, #d2001a 100%);
        padding: 8px 15px;
        border-bottom: none;
        color: white;
    }
    
    #modalBatal .modal-title {
        color: white;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    
    #modalBatal .modal-title i {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        padding: 6px;
        margin-right: 8px;
        font-size: 16px;
    }
    
    /* Body dan form */
    #modalBatal .modal-body {
        padding: 12px;
    }
    
    #modalBatal .form-group {
        margin-bottom: 12px;
    }
    
    #modalBatal .form-label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    #modalBatal .form-control {
        font-size: 14px;
        min-height: 50px;
    }
    
    /* Section related schedules */
    #modalBatal .related-schedules {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px;
        margin-top: 12px;
    }
    
    #modalBatal .d-flex.align-items-center.mb-2 {
        margin-bottom: 6px !important;
        flex-wrap: wrap; /* Untuk tampilan mobile */
    }
    
    #modalBatal h6.fw-bold.mb-0 {
        font-size: 13px;
        margin-bottom: 0;
    }
    
    #modalBatal h6.fw-bold {
        color: #374151;
        margin-bottom: 8px;
    }
    
    /* Tabel responsive dalam modal */
    #modalBatal .table-responsive {
        max-height: 150px;
        overflow-y: auto;
        margin: 8px 0;
        border-radius: 6px;
    }
    
    #modalBatal .table {
        margin: 0;
        width: 100%;
        border-collapse: collapse;
    }
    
    #modalBatal .table thead th {
        background-color: #f3f4f6;
        color: #374151;
        font-weight: 600;
        text-align: center;
        padding: 6px;
        font-size: 12px;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    #modalBatal .table tbody td {
        padding: 6px;
        vertical-align: middle;
        text-align: center;
        font-size: 12px;
    }
    
    /* Responsif untuk perangkat mobile */
    @media (max-width: 576px) {
        #modalBatal .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 20px);
        }
        
        #modalBatal .modal-header {
            padding: 8px 12px;
        }
        
        #modalBatal .modal-body {
            padding: 10px;
        }
        
        #modalBatal .modal-title {
            font-size: 15px;
        }
        
        #modalBatal .modal-title i {
            padding: 5px;
            margin-right: 6px;
            font-size: 14px;
        }
        
        #modalBatal .form-label {
            font-size: 13px;
        }
        
        #modalBatal .form-control {
            font-size: 13px;
            min-height: 40px;
        }
        
        #modalBatal .table {
            font-size: 11px;
        }
        
        #modalBatal .table thead th {
            padding: 5px 3px;
            font-size: 11px;
        }
        
        #modalBatal .table tbody td {
            padding: 5px 3px;
            font-size: 11px;
        }
        
        /* Sembunyikan kolom yang kurang penting di mobile */
        #modalBatal .table th:nth-child(4), /* Jenis Bimbingan */
        #modalBatal .table td:nth-child(4) {
            display: none;
        }
        
        #modalBatal .btn {
            font-size: 13px;
            padding: 5px 12px;
        }
        
        #modalBatal .d-flex.align-items-center.mb-2 h6 {
            font-size: 12px;
            margin-right: 5px;
        }
        
        #modalBatal .form-check-label {
            font-size: 11px;
        }
        
        #modalBatal .form-check-input {
            width: 16px;
            height: 16px;
        }
    }
    #modalBatal #selectAllRelatedSchedules + label {
    font-size: 12px; /* Mengurangi ukuran font */
    font-weight: normal;
    color: #555;
    }
    
    /* Table untuk tampilan compact */
    #modalBatal .table-sm th,
    #modalBatal .table-sm td {
        padding: 4px;
        font-size: 12px;
        vertical-align: middle;
    }
    
    /* Tombol footer */
    #modalBatal .modal-footer {
        padding: 10px;
        justify-content: center;
    }
    
    #modalBatal .btn {
        font-size: 14px;
        padding: 5px 15px;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    
    #modalBatal .btn:hover {
        transform: translateY(-2px);
    }
    
    #modalBatal .btn-danger {
        background: linear-gradient(135deg, #f53844 0%, #d2001a 100%);
        border: none;
    }
    
    #modalBatal .btn-danger:hover {
        background: linear-gradient(135deg, #d2001a 0%, #b50014 100%);
        box-shadow: 0 4px 8px rgba(210, 0, 26, 0.2);
    }
    
    /* ==============================================
       7. MODAL SELESAI BIMBINGAN
       ============================================== */
    #modalSelesai .modal-header {
        background-color: #10b981;
        color: white;
        border-bottom: none;
    }
    
    #modalSelesai .btn-success {
        background-color: #10b981;
        border: none;
    }
    #modalSelesai .modal-dialog {
    transform: scale(0.5);
    opacity: 0;
    transition: all 0.3s ease-in-out;
    }

    #modalSelesai.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
    }

    /* Animasi untuk icon check di dalam modal */
    @keyframes pulse-check {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.15);
    }
    100% {
        transform: scale(1);
    }
    }

    #modalSelesai .bi-check-circle-fill {
    animation: pulse-check 1.5s infinite;
    }

    /* Animasi untuk tombol saat hover */
    #modalSelesai .btn {
    transition: all 0.3s ease;
    }

    #modalSelesai .btn-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(10, 166, 101, 0.4);
    }

    #modalSelesai .btn-secondary:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(75, 85, 99, 0.3);
    }

    /* Animasi background pada icon container */
    @keyframes glow {
    0% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
    }

    #modalSelesai .rounded-circle {
    animation: glow 2s infinite;
    transition: all 0.3s ease;
    }


/* ==============================================
   8. Style search - LENGKAP DIPERBARUI
   ============================================== */

/* Container untuk search box */
.search-container {
  position: relative;
  width: 100%;
  transition: all 0.3s ease;
  margin-bottom: 0;
  max-width: 300px; /* Batasi lebar maksimal */
  margin-left: auto; /* Posisikan di kanan */
}

@media (max-width: 768px) {
  .search-container {
    max-width: 100%; /* Pada layar kecil, biarkan penuh */
  }
}

/* Input search box */
.search-box {
  width: 100%;
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 50px;
  padding: 10px 40px 10px 16px;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  font-size: 14px;
  color: #495057;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.search-box:focus {
  background-color: #fff;
  border-color: #4f46e5;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
  outline: none;
}

/* Placeholder styling */
.search-box::placeholder {
  color: #adb5bd;
  transition: opacity 0.2s;
}

.search-box:focus::placeholder {
  opacity: 0.5;
}

/* Icon search */
.search-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  transition: all 0.3s ease;
  pointer-events: none;
  z-index: 1;
}

/* Aturan baru: sembunyikan icon search ketika ada input */
.search-box:not(:placeholder-shown) + .search-icon {
  opacity: 0;
  visibility: hidden;
}

/* Clear button */
.search-clear {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  cursor: pointer;
  display: none; /* Hidden by default, will be shown via JS */
  font-size: 14px;
  background: #e9ecef;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  align-items: center; /* For flex display */
  justify-content: center; /* For flex display */
  z-index: 2;
}

.search-clear:hover {
  background-color: #dc3545;
  color: white;
  transform: translateY(-50%) scale(1.1);
}

/* Styling untuk text yang di-highlight */
.highlight {
  background-color: #FFC107 !important;
  color: #000 !important;
  font-weight: bold !important;
  padding: 0 3px !important;
  border-radius: 2px !important;
  display: inline-block !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
}

@keyframes pulse {
  0% { background-color: rgba(255, 193, 7, 0.3); }
  50% { background-color: rgba(255, 193, 7, 0.6); }
  100% { background-color: rgba(255, 193, 7, 0.3); }
}

/* Styling untuk baris "tidak ada hasil" */
.no-results-row td {
  padding: 16px !important;
  background-color: #f8f9fa !important;
  color: #6c757d;
  font-style: italic;
}

.no-results-row i {
  margin-right: 8px;
  color: #6c757d;
}

/* Responsif untuk layar sedang */
@media (max-width: 992px) {
  .row .col-md-6:last-child {
    margin-top: 15px;
  }
  
  .search-container {
    max-width: 100%;
  }
}

/* Responsif untuk layar kecil/mobile */
@media (max-width: 576px) {
  .search-box {
    padding: 8px 36px 8px 14px;
    font-size: 13px;
  }
  
  .search-icon {
    right: 14px;
    font-size: 14px;
  }
  
  .search-clear {
    width: 18px;
    height: 18px;
    font-size: 12px;
    right: 14px;
  }
}

/* ==============================================
   9. Style pagination 
   ============================================== */

   /* Pagination styles */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #2563eb; /* Mempertahankan warna biru */
    border: 1px solid #e5e7eb;
    padding: 0.5rem 0.75rem;
}

.page-link:hover {
    color: #1d4ed8;
    background-color: #f3f4f6;
}

.page-item.active .page-link {
    background-color: #2563eb; /* Warna biru untuk active */
    border-color: #2563eb;
    color: white;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .pagination {
        flex-wrap: wrap;
        margin-top: 10px;
    }
    
    .pagination .page-item {
        margin-bottom: 5px;
    }
    
    .d-flex.flex-column.flex-lg-row > p {
        text-align: center;
        width: 100%;
    }
}

@media (min-width: 992px) {
    .d-flex.flex-column.flex-lg-row {
        align-items: center;
    }
    
    .d-flex.flex-column.flex-lg-row > p {
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .pagination {
        margin-left: 15px;
    }
}

/* Mobile optimization */
@media (max-width: 575.98px) {
    .page-link {
        padding: 0.4rem 0.6rem;
        font-size: 0.9rem;
    }
}
    </style>
@endpush

@section('content')
    <div class="container mt-5">
        <h1 class="mb-2 gradient-text fw-bold">Persetujuan Bimbingan</h1>
        <hr>
        <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
            <a href="{{ route('dosen.jadwal.index') }}">
                <i class="bi bi-plus-lg me-2"></i> Jadwal Bimbingan
            </a>
        </button>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs" id="bimbinganTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'usulan', 'per_page' => request('per_page', 50)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'usulan' ? 'active' : '' }}">
                            Usulan
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'jadwal', 'per_page' => request('per_page', 50)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'jadwal' ? 'active' : '' }}">
                            Jadwal
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'riwayat', 'per_page' => request('per_page', 50)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'riwayat' ? 'active' : '' }}">
                            Riwayat
                        </a>
                    </li>
                    @if(auth()->user()->isKoordinatorProdi())
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'pengelola', 'per_page' => request('per_page', 50)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'pengelola' ? 'active' : '' }}">
                            Pengelola
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="bimbinganTabContent">
                    @if ($activeTab == 'usulan')
                        <div class="tab-pane fade show active" id="usulan" role="tabpanel">
                            <div class="row mb-3 align-items-center">
                                <div class="col-lg-6 col-md-6">
                                    <div class="d-flex align-items-center">
                                        <label class="me-2">Tampilkan</label>
                                        <select class="form-select form-select-sm w-auto"
                                        onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'usulan']) }}&per_page=' + this.value">
                                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                            <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                        </select>
                                        <label class="ms-2">entries</label>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="search-container">
                                        <input type="text" id="searchInput" class="search-box" placeholder="Cari data..." autocomplete="off" aria-label="Cari data">
                                        <i class="bi bi-search search-icon"></i>
                                        <span class="search-clear" id="clearSearch">×</span>
                                    </div>
                                </div>
                            </div>
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
                                                                <i class="bi bi-check-circle"></i>
                                                            </a>
                                                            <a href="#" class="action-icon reject-icon"
                                                                data-bs-toggle="tooltip" title="Tolak">
                                                                <i class="bi bi-x-circle"></i>
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
                                    <div class="card shadow border-0 rounded-4 mb-3">
                                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                                            <h5 class="mb-0">Google Calendar</h5>
                                            <div>
                                                @if (auth()->user()->isGoogleTokenExpired())
                                                <a href="{{ route('dosen.google.connect') }}" class="btn btn-sm btn-warning me-2 google-connect-btn">
                                                    <i class="bi bi-arrow-clockwise me-1"></i> Hubungkan Ulang
                                                 </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="ratio ratio-16x9">
                                                <iframe
                                                    src="https://calendar.google.com/calendar/embed?src={{ urlencode(auth()->user()->email) }}&mode=MONTH&showPrint=0&showCalendars=0&showTz=0&hl=id"
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
                            <div class="card shadow-lg border-0 rounded-4">
                                <div class="card-header bg-white p-3">
                                    <h5 class="mb-0 fw-bold">Daftar Mahasiswa Bimbingan</h5>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="d-flex align-items-center">
                                                <label class="me-2">Tampilkan</label>
                                                <select class="form-select form-select-sm w-auto"
                                                onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'jadwal']) }}&per_page=' + this.value">
                                                    <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                                    <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                                    <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                                </select>
                                                <label class="ms-2">entries</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="search-container">
                                                <input type="text" id="searchInput" class="search-box" placeholder="Cari data..." autocomplete="off" aria-label="Cari data">
                                                <i class="bi bi-search search-icon"></i>
                                                <span class="search-clear" id="clearSearch">×</span>
                                            </div>
                                        </div>
                                    </div>
                                    
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
                                                        <td>{{ ($jadwal->currentPage() - 1) * $jadwal->perPage() + $loop->iteration }}</td>
                                                        <td>{{ $item->nim }}</td>
                                                        <td>{{ $item->mahasiswa_nama }}</td>
                                                        <td>{{ ucfirst($item->jenis_bimbingan) }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                                        <td>{{ $item->lokasi && trim($item->lokasi) !== '' ? $item->lokasi : '-' }}</td>
                                                        <td>{{ $item->nomor_antrian && trim($item->nomor_antrian) !== '' ? $item->nomor_antrian : '-' }}</td>
                                                        <td class="fw-bold text-white bg-success">DISETUJUI</td>
                                                        <td>
                                                            <div class="d-flex gap-2 justify-content-center">
                                                                <button class="btn btn-sm btn-success selesai-btn"
                                                                    data-id="{{ $item->id }}" data-bs-toggle="modal"
                                                                    data-bs-target="#modalSelesai" title="Selesai">
                                                                    <i class="bi bi-check2-circle"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger batal-btn"
                                                                    data-id="{{ $item->id }}" title="Batalkan">
                                                                    <i class="bi bi-x-circle"></i>
                                                                </button>
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
                                                        <td colspan="10" class="text-center">Tidak ada jadwal bimbingan aktif</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                        @if($jadwal instanceof \Illuminate\Pagination\LengthAwarePaginator && $jadwal->total() > 0)
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <p class="mb-0">
                                                Menampilkan {{ $jadwal->firstItem() }} sampai {{ $jadwal->lastItem() }} dari
                                                {{ $jadwal->total() }} entri
                                            </p>
                                            <nav aria-label="Page navigation">
                                                <ul class="pagination justify-content-end mb-0">
                                                    {{-- Tombol Sebelumnya --}}
                                                    @if ($jadwal->onFirstPage())
                                                        <li class="page-item disabled">
                                                            <span class="page-link">« Sebelumnya</span>
                                                        </li>
                                                    @else
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $jadwal->previousPageUrl() }}&tab=jadwal">« Sebelumnya</a>
                                                        </li>
                                                    @endif

                                                    {{-- Tombol Nomor Halaman --}}
                                                    @foreach ($jadwal->getUrlRange(1, $jadwal->lastPage()) as $page => $url)
                                                        <li class="page-item {{ $page == $jadwal->currentPage() ? 'active' : '' }}">
                                                            <a class="page-link" href="{{ $url }}&tab=jadwal">{{ $page }}</a>
                                                        </li>
                                                    @endforeach

                                                    {{-- Tombol Selanjutnya --}}
                                                    @if ($jadwal->hasMorePages())
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $jadwal->nextPageUrl() }}&tab=jadwal">Selanjutnya »</a>
                                                        </li>
                                                    @else
                                                        <li class="page-item disabled">
                                                            <span class="page-link">Selanjutnya »</span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </nav>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($activeTab == 'riwayat')
                        <div class="tab-pane fade show active" id="riwayat" role="tabpanel">
                            <div class="row mb-3 align-items-center">
                                <div class="col-lg-6 col-md-6">
                                    <div class="d-flex align-items-center">
                                        <label class="me-2">Tampilkan</label>
                                        <select class="form-select form-select-sm w-auto"
                                        onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'riwayat']) }}&per_page=' + this.value">
                                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                            <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                        </select>
                                        <label class="ms-2">entries</label>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="search-container">
                                        <input type="text" id="searchInput" class="search-box" placeholder="Cari data..." autocomplete="off" aria-label="Cari data">
                                        <i class="bi bi-search search-icon"></i>
                                        <span class="search-clear" id="clearSearch">×</span>
                                    </div>
                                </div>
                            </div>
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
                                                <td class="fw-bold {{ 
                                                    $item->status === 'DISETUJUI' ? 'bg-success' : (
                                                        $item->status === 'DITOLAK' ? 'bg-danger' : (
                                                            $item->status === 'DIBATALKAN' ? 'bg-secondary' : (
                                                                $item->status === 'SELESAI' ? 'bg-primary' : 'bg-warning'
                                                            )
                                                        )
                                                    ) 
                                                }} text-white">{{ $item->status }}</td>
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

                    <!-- Di dalam div tab-content -->
                    @if ($activeTab == 'pengelola' && auth()->user()->isKoordinatorProdi())
                    <div class="tab-pane fade show active" id="pengelola" role="tabpanel">
                        <!-- Daftar Jadwal Dosen -->
                        <div class="card shadow-lg border-0 rounded-4 mb-4 daftar-dosen-section">
                            <div class="card-header bg-white p-3">
                                <h5 class="mb-0 fw-bold">Daftar Jadwal Dosen</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="d-flex align-items-center">
                                            <label class="me-2">Tampilkan</label>
                                            <select class="form-select form-select-sm w-auto"
                                            onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'pengelola']) }}&per_page=' + this.value">
                                                <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                                <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                            </select>
                                            <label class="ms-2">entries</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="search-container">
                                            <input type="text" id="searchDaftarDosen" class="search-box" placeholder="Cari daftar dosen..." autocomplete="off" aria-label="Cari daftar dosen">
                                            <i class="bi bi-search search-icon"></i>
                                            <span class="search-clear" id="clearSearchDaftarDosen">×</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead class="text-center">
                                            <tr>
                                                <th>No.</th>
                                                <th>NIP</th>
                                                <th>Nama Dosen</th>
                                                <th>Nama Singkat</th>
                                                <th>Total Bimbingan Hari Ini</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($dosenList as $index => $dosen)
                                                <tr class="text-center">
                                                    <td>{{ ($dosenList->currentPage() - 1) * $dosenList->perPage() + $loop->iteration }}</td>
                                                    <td>{{ $dosen->nip }}</td>
                                                    <td>{{ $dosen->nama }}</td>
                                                    <td>{{ $dosen->nama_singkat }}</td>
                                                    <td>{{ $dosen->total_bimbingan_hari_ini }}</td>
                                                    <td>
                                                        <div class="action-icons">
                                                            <a href="{{ route('dosen.detail', $dosen->nip) }}"
                                                                class="action-icon info-icon" data-bs-toggle="tooltip"
                                                                title="Info">
                                                                <i class="bi bi-info-circle"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data dosen</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($dosenList instanceof \Illuminate\Pagination\LengthAwarePaginator && $dosenList->total() > 0)
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="mb-0">
                                        Menampilkan {{ $dosenList->firstItem() }} sampai {{ $dosenList->lastItem() }} dari
                                        {{ $dosenList->total() }} entri
                                    </p>
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-end mb-0">
                                            {{-- Tombol Sebelumnya --}}
                                            @if ($dosenList->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">« Sebelumnya</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $dosenList->previousPageUrl() }}&tab=pengelola">« Sebelumnya</a>
                                                </li>
                                            @endif

                                            {{-- Tombol Nomor Halaman --}}
                                            @foreach ($dosenList->getUrlRange(1, $dosenList->lastPage()) as $page => $url)
                                                <li class="page-item {{ $page == $dosenList->currentPage() ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ $url }}&tab=pengelola">{{ $page }}</a>
                                                </li>
                                            @endforeach

                                            {{-- Tombol Selanjutnya --}}
                                            @if ($dosenList->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $dosenList->nextPageUrl() }}&tab=pengelola">Selanjutnya »</a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">Selanjutnya »</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Riwayat Jadwal Dosen -->
                        <div class="card shadow-lg border-0 rounded-4 riwayat-dosen-section">
                            <div class="card-header bg-white p-3">
                                <h5 class="mb-0 fw-bold">Riwayat Jadwal Dosen</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="d-flex align-items-center">
                                            <label class="me-2">Tampilkan</label>
                                            <select class="form-select form-select-sm w-auto"
                                            onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'pengelola']) }}&per_page=' + this.value">
                                                <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                                <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                            </select>
                                            <label class="ms-2">entries</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="search-container">
                                            <input type="text" id="searchRiwayatDosen" class="search-box" placeholder="Cari riwayat dosen..." autocomplete="off">
                                            <i class="bi bi-search search-icon"></i>
                                            <span class="search-clear" id="clearSearchRiwayatDosen">×</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead class="text-center">
                                            <tr>
                                                <th>No.</th>
                                                <th>NIP</th>
                                                <th>Nama Dosen</th>
                                                <th>Nama Singkat</th>
                                                <th>Total Bimbingan Keseluruhan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($riwayatDosenList as $index => $dosen)
                                                <tr class="text-center">
                                                    <td>{{ ($riwayatDosenList->currentPage() - 1) * $riwayatDosenList->perPage() + $loop->iteration }}</td>
                                                    <td>{{ $dosen->nip }}</td>
                                                    <td>{{ $dosen->nama }}</td>
                                                    <td>{{ $dosen->nama_singkat }}</td>
                                                    <td>{{ $dosen->total_bimbingan }}</td>
                                                    <td>
                                                        <div class="action-icons">
                                                            <a href="{{ route('dosen.riwayat.detail', $dosen->nip) }}"
                                                                class="action-icon info-icon" data-bs-toggle="tooltip"
                                                                title="Info">
                                                                <i class="bi bi-info-circle"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data riwayat dosen</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($riwayatDosenList instanceof \Illuminate\Pagination\LengthAwarePaginator && $riwayatDosenList->total() > 0)
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="mb-0">
                                        Menampilkan {{ $riwayatDosenList->firstItem() }} sampai {{ $riwayatDosenList->lastItem() }} dari
                                        {{ $riwayatDosenList->total() }} entri
                                    </p>
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-end mb-0">
                                            {{-- Tombol Sebelumnya --}}
                                            @if ($riwayatDosenList->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">« Sebelumnya</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $riwayatDosenList->previousPageUrl() }}&tab=pengelola">« Sebelumnya</a>
                                                </li>
                                            @endif

                                            {{-- Tombol Nomor Halaman --}}
                                            @foreach ($riwayatDosenList->getUrlRange(1, $riwayatDosenList->lastPage()) as $page => $url)
                                                <li class="page-item {{ $page == $riwayatDosenList->currentPage() ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ $url }}&tab=pengelola">{{ $page }}</a>
                                                </li>
                                            @endforeach

                                            {{-- Tombol Selanjutnya --}}
                                            @if ($riwayatDosenList->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $riwayatDosenList->nextPageUrl() }}&tab=pengelola">Selanjutnya »</a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">Selanjutnya »</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mt-3">
                    <p class="mb-3 mb-lg-0">
                        @if ($activeTab == 'usulan' && $usulan->total() > 0)
                            Menampilkan {{ $usulan->firstItem() }} sampai {{ $usulan->lastItem() }} dari
                            {{ $usulan->total() }} entri
                        @elseif($activeTab == 'riwayat' && $riwayat->total() > 0)
                            Menampilkan {{ $riwayat->firstItem() }} sampai {{ $riwayat->lastItem() }} dari
                            {{ $riwayat->total() }} entri
                        @endif
                    </p>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center justify-content-lg-end mb-0">
                            {{-- Previous Page --}}
                            @if ($activeTab == 'usulan')
                                @if ($usulan->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Sebelumnya</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $usulan->previousPageUrl() }}&tab=usulan">« Sebelumnya</a>
                                    </li>
                                @endif
                
                                {{-- Page Numbers --}}
                                @foreach ($usulan->getUrlRange(1, $usulan->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $usulan->currentPage() ? 'active' : '' }}">
                                        <a class="page-link"
                                            href="{{ $url }}&tab=usulan">{{ $page }}</a>
                                    </li>
                                @endforeach
                
                                {{-- Next Page --}}
                                @if ($usulan->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $usulan->nextPageUrl() }}&tab=usulan">Selanjutnya »</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Selanjutnya »</span>
                                    </li>
                                @endif
                            @elseif($activeTab == 'riwayat')
                                @if ($riwayat->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Sebelumnya</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $riwayat->previousPageUrl() }}&tab=riwayat">« Sebelumnya</a>
                                    </li>
                                @endif
                
                                @foreach ($riwayat->getUrlRange(1, $riwayat->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $riwayat->currentPage() ? 'active' : '' }}">
                                        <a class="page-link"
                                            href="{{ $url }}&tab=riwayat">{{ $page }}</a>
                                    </li>
                                @endforeach
                
                                @if ($riwayat->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $riwayat->nextPageUrl() }}&tab=riwayat">Selanjutnya »</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Selanjutnya »</span>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Terima yang Diperbaiki -->
    <div class="modal fade" id="modalTerima" tabindex="-1" aria-labelledby="modalTerimaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTerimaLabel">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Terima Usulan Bimbingan
                    </h5>
                    <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <!-- Form Lokasi -->
                    <div class="form-group">
                        <label for="lokasiBimbingan" class="form-label">Lokasi Bimbingan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-geo-alt-fill"></i>
                            </span>
                            <input type="text" class="form-control" id="lokasiBimbingan" required placeholder="Contoh: Ruang Dosen Lt.2, Meeting Room, atau Link Lokasi">
                        </div>
                        <div class="invalid-feedback">Lokasi bimbingan wajib diisi</div>
                        <small class="text-muted mt-2 d-block">Masukkan lokasi fisik atau link lokasi</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-success" id="confirmTerima">
                        Setujui Usulan
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- Modal Tolak yang Diperbaiki -->
<div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTolakLabel">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    Tolak Usulan Bimbingan
                </h5>
                <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <!-- Form Alasan -->
                <div class="form-group">
                    <label for="alasanPenolakan" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-chat-left-text-fill"></i>
                        </span>
                        <textarea class="form-control" id="alasanPenolakan" rows="3" required placeholder="Contoh: Jadwal bertabrakan dengan kegiatan lain, Mohon ajukan di waktu lain"></textarea>
                    </div>
                    <div class="invalid-feedback">Alasan penolakan wajib diisi</div>
                    <small class="text-muted mt-2 d-block">Berikan alasan yang jelas agar mahasiswa dapat mengajukan ulang dengan penyesuaian</small>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmTolak">
                    Tolak Usulan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Selesai -->
<div class="modal fade" id="modalSelesai" tabindex="-1" aria-labelledby="modalSelesaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow border-0">
            <div class="modal-header border-0 bg-success text-white">
                <h5 class="modal-title fw-bold" id="modalSelesaiLabel">
                    Konfirmasi Selesai Bimbingan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="d-flex justify-content-center mb-4">
                    <div class="rounded-circle bg-success bg-opacity-10" style="width: 90px; height: 90px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 42px;"></i>
                    </div>
                </div>
                <p class="mb-1">Apakah Anda yakin sesi bimbingan ini telah selesai?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-success px-4" id="confirmSelesai">
                    Ya, Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Batal yang Super Compact -->
<div class="modal fade" id="modalBatal" tabindex="-1" aria-labelledby="modalBatalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalBatalLabel">
                    <i class="bi bi-x-circle me-2"></i>
                    Batalkan Persetujuan
                </h5>
                <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <!-- Form Alasan -->
                <div class="form-group mb-2">
                    <label for="alasanPembatalan" class="form-label fw-bold">Alasan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alasanPembatalan" rows="2" required 
                        placeholder="Contoh: Ada jadwal rapat mendadak"></textarea>
                    <small class="text-muted">Berikan alasan yang jelas kepada mahasiswa</small>
                </div>

                <!-- Daftar Mahasiswa dengan Jadwal yang Sama -->
                <div class="related-schedules" id="relatedSchedulesContainer">
                    <div class="d-flex align-items-center mb-2">
                        <h6 class="fw-bold mb-0">Mahasiswa dengan jadwal sama:</h6>
                        <div class="form-check ms-2">
                            <input class="form-check-input" type="checkbox" id="selectAllRelatedSchedules">
                            <label class="form-check-label" for="selectAllRelatedSchedules">Pilih Semua</label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info py-1 px-2 mb-2 d-none" id="noRelatedSchedules">
                        <i class="bi bi-info-circle me-1"></i>
                        Tidak ada mahasiswa lain dengan jadwal sama
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="relatedSchedulesTable">
                            <thead>
                                <tr class="table-light">
                                    <th style="width: 40px;">Pilih</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Jenis Bimbingan</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="relatedSchedulesList">
                                <!-- Data akan diisi secara dinamis oleh JavaScript -->
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Memuat data...</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary text-center px-3" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmBatal">
                    Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentRow = null;
    let currentId = null;
    let currentSelesaiId = null;
    let currentBatalId = null;
    
    // Dapatkan referensi ke modal-modal
    const modalTerima = document.getElementById('modalTerima');
    const modalTolak = document.getElementById('modalTolak');
    const modalSelesai = document.getElementById('modalSelesai');
    const modalBatal = document.getElementById('modalBatal');
    
    // Inisialisasi instance bootstrap modal
    const bsModalTerima = modalTerima ? new bootstrap.Modal(modalTerima) : null;
    const bsModalTolak = modalTolak ? new bootstrap.Modal(modalTolak) : null;
    const bsModalSelesai = modalSelesai ? new bootstrap.Modal(modalSelesai) : null;
    const bsModalBatal = modalBatal ? new bootstrap.Modal(modalBatal) : null;

    // Inisialisasi tooltips
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
            } else if (status === 'DIBATALKAN') { // Tambahkan kondisi untuk status DIBATALKAN
                statusCell.classList.add('bg-secondary');
            } else if (status === 'SELESAI') {
                statusCell.classList.add('bg-primary');
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
            e.stopPropagation();
            currentRow = this.closest('tr');
            currentId = currentRow.getAttribute('data-id');

            if (!currentRow || !currentId) {
                console.error('Tidak bisa mendapatkan ID atau baris untuk tombol approve');
                return;
            }

            console.log('Tombol approve diklik, ID:', currentId);
            if (bsModalTerima) bsModalTerima.show();
        });
    });

    // Setup modal handling for reject action
    document.querySelectorAll('.reject-icon').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            currentRow = this.closest('tr');
            currentId = currentRow.getAttribute('data-id');

            if (!currentRow || !currentId) {
                console.error('Tidak bisa mendapatkan ID atau baris untuk tombol reject');
                return;
            }

            console.log('Tombol reject diklik, ID:', currentId);
            if (bsModalTolak) bsModalTolak.show();
        });
    });

    // Setup modal handler untuk tombol selesai
    document.querySelectorAll('.selesai-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            currentSelesaiId = this.getAttribute('data-id');
            console.log('Button selesai diklik, ID:', currentSelesaiId);
            
            if (!currentSelesaiId) {
                console.error('ID tidak ditemukan untuk tombol selesai');
                return;
            }
            
            // Fetch additional data about the guidance session
            try {
                const row = this.closest('tr');
                if (row) {
                    const mahasiswaNama = row.querySelector('td:nth-child(3)').textContent.trim();
                    const jenisBimbingan = row.querySelector('td:nth-child(4)').textContent.trim();
                    
                    // Update modal with contextual information
                    const mhsNameConfirm = document.getElementById('mhs-name-confirm');
                    const jenisBimbinganConfirm = document.getElementById('jenis-bimbingan-confirm');
                    
                    if (mhsNameConfirm) mhsNameConfirm.textContent = mahasiswaNama;
                    if (jenisBimbinganConfirm) jenisBimbinganConfirm.textContent = jenisBimbingan;
                }
            } catch (error) {
                console.error('Error getting row data:', error);
            }
            
            if (bsModalSelesai) bsModalSelesai.show();
        });
    });

    // Setup event listener untuk tombol batal
    document.querySelectorAll('.batal-btn').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            currentBatalId = this.getAttribute('data-id');
            console.log('Tombol batal diklik, ID:', currentBatalId);
            
            if (!currentBatalId) {
                console.error('ID tidak ditemukan untuk tombol batal');
                return;
            }
            
            // Reset form state
            const alasanInput = document.getElementById('alasanPembatalan');
            if (alasanInput) {
                alasanInput.value = '';
                alasanInput.classList.remove('is-invalid');
            }
            
            // Reset daftar jadwal terkait
            const relatedSchedulesList = document.getElementById('relatedSchedulesList');
            if (relatedSchedulesList) {
                relatedSchedulesList.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">Memuat data jadwal terkait...</span>
                        </td>
                    </tr>
                `;
            }
            
            // Reset checkbox "Pilih Semua"
            const selectAllCheckbox = document.getElementById('selectAllRelatedSchedules');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            }
                
            // Ambil data jadwal yang berkaitan dengan waktu yang sama
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch(`/persetujuan/related-schedules/${currentBatalId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                
                const responseText = await response.text();
                console.log('Raw API Response:', responseText);
                
                // Coba parse response sebagai JSON
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Failed to parse response as JSON:', parseError);
                    throw new Error('Invalid JSON response');
                }
                console.log('Parsed API Response:', result);
                
                if (result.success) {
                    const relatedSchedules = result.schedules || [];
                    const noRelatedMsg = document.getElementById('noRelatedSchedules');
                    const tbody = document.getElementById('relatedSchedulesList');
                    
                    // Tampilkan pesan jika tidak ada jadwal terkait
                    if (relatedSchedules.length === 0) {
                        if (noRelatedMsg) noRelatedMsg.classList.remove('d-none');
                        if (tbody) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada jadwal terkait</td>
                                </tr>
                            `;
                        }
                    } else {
                        if (noRelatedMsg) noRelatedMsg.classList.add('d-none');
                        
                        // Render daftar jadwal terkait
                        if (tbody) {
                            tbody.innerHTML = '';
                            relatedSchedules.forEach(schedule => {
                                const scheduleData = schedule.stdClass || schedule;
                                
                                const nim = scheduleData.nim || schedule.nim;
                                const nama = scheduleData.mahasiswa_nama || schedule.mahasiswa_nama;
                                
                                // Periksa apakah format waktu valid sebelum konversi
                                let waktuMulai = 'Tidak tersedia';
                                let waktuSelesai = 'Tidak tersedia';
                                
                                try {
                                    // Waktu mulai
                                    if (schedule.waktu_mulai) {
                                        if (schedule.waktu_mulai.includes(':') && !schedule.waktu_mulai.includes('-')) {
                                            const [hours, minutes] = schedule.waktu_mulai.split(':');
                                            waktuMulai = `${hours}:${minutes}`;
                                        } else {
                                            waktuMulai = new Date(schedule.waktu_mulai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                                        }
                                    }
                                    
                                    // Waktu selesai
                                    if (schedule.waktu_selesai) {
                                        if (schedule.waktu_selesai.includes(':') && !schedule.waktu_selesai.includes('-')) {
                                            const [hours, minutes] = schedule.waktu_selesai.split(':');
                                            waktuSelesai = `${hours}:${minutes}`;
                                        } else {
                                            waktuSelesai = new Date(schedule.waktu_selesai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                                        }
                                    }
                                } catch (error) {
                                    console.error('Error saat memformat waktu:', error);
                                }
                                
                                tbody.innerHTML += `
                                    <tr>
                                        <td class="text-center">
                                            <input class="form-check-input related-schedule-check" 
                                                type="checkbox" 
                                                value="${schedule.id}" 
                                                id="schedule-${schedule.id}">
                                        </td>
                                        <td>${nim}</td>
                                        <td>${nama}</td>
                                        <td>${schedule.jenis_bimbingan ? schedule.jenis_bimbingan.charAt(0).toUpperCase() + schedule.jenis_bimbingan.slice(1) : ''}</td>
                                        <td>${waktuMulai} - ${waktuSelesai}</td>
                                    </tr>
                                `;
                            });
                        }       
                    }
                    
                    // Tampilkan modal
                    if (bsModalBatal) bsModalBatal.show();
                } else {
                    // TAMBAHAN: Cek apakah error karena jadwal sudah lewat
                    if (result.message && result.message.toLowerCase().includes('lewat waktu')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Dapat Dibatalkan',
                            text: 'Jadwal bimbingan tidak dapat dibatalkan karena sudah lewat waktu',
                            confirmButtonColor: '#dc3545'
                        });
                        return; // Hentikan proses
                    }
                    
                    throw new Error(result.message || 'Gagal memuat jadwal terkait');
                }
            } catch (error) {
                console.error('Error fetching related schedules:', error);
                
                // TAMBAHAN: Cek apakah error berkaitan dengan jadwal yang sudah lewat
                if (error.message && error.message.toLowerCase().includes('lewat waktu')) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Dapat Dibatalkan',
                        text: 'Jadwal bimbingan tidak dapat dibatalkan karena sudah lewat waktu',
                        confirmButtonColor: '#dc3545'
                    });
                    return; // Hentikan proses
                }
                
                const relatedSchedulesList = document.getElementById('relatedSchedulesList');
                if (relatedSchedulesList) {
                    relatedSchedulesList.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Gagal memuat data jadwal terkait
                            </td>
                        </tr>
                    `;
                }
                
                // Tetap tampilkan modal untuk error lainnya
                if (bsModalBatal) bsModalBatal.show();
            }
        });
    });

    // Event listener untuk checkbox "Pilih Semua"
    const selectAllCheckbox = document.getElementById('selectAllRelatedSchedules');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            console.log('Select all checkbox changed, checked:', this.checked);
            const isChecked = this.checked;
            document.querySelectorAll('.related-schedule-check').forEach(checkbox => {
                checkbox.checked = isChecked;
                console.log('Setting checkbox', checkbox.id, 'to', isChecked);
            });
        });
    } else {
        console.warn('Select all checkbox not found in the DOM');
    }

    // Handle approve confirmation
    document.getElementById('confirmTerima')?.addEventListener('click', async function() {
        const lokasiInput = document.getElementById('lokasiBimbingan');
        if (!lokasiInput || !currentId || !currentRow) {
            console.error('Data tidak lengkap:', { lokasiInput, currentId, currentRow });
            return;
        }

        const lokasi = lokasiInput.value.trim();
        if (!lokasi) {
            lokasiInput.classList.add('is-invalid');
            return;
        }

        try {
            this.disabled = true;
            console.log('Sending approval with ID:', currentId, 'and location:', lokasi);

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const response = await fetch(`/persetujuan/terima/${currentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    lokasi: lokasi
                })
            });

            console.log('Response status:', response.status);
            const responseText = await response.text();
            console.log('Response text:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
                console.log('Parsed JSON:', data);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                throw new Error('Invalid JSON response: ' + responseText);
            }

            if (data.success) {
                if (bsModalTerima) bsModalTerima.hide();

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
        if (!alasanInput || !currentId || !currentRow) {
            console.error('Data tidak lengkap:', { alasanInput, currentId, currentRow });
            return;
        }

        const alasan = alasanInput.value.trim();
        if (!alasan) {
            alasanInput.classList.add('is-invalid');
            return;
        }

        try {
            this.disabled = true;
            console.log('Sending rejection with ID:', currentId, 'and reason:', alasan);

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const response = await fetch(`/persetujuan/tolak/${currentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    keterangan: alasan
                })
            });

            console.log('Response status:', response.status);
            const responseText = await response.text();
            console.log('Response text:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
                console.log('Parsed JSON:', data);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                throw new Error('Invalid JSON response: ' + responseText);
            }

            if (data.success) {
                if (bsModalTolak) bsModalTolak.hide();

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

    // Handle konfirmasi selesai
    document.getElementById('confirmSelesai')?.addEventListener('click', async function() {
        if (!currentSelesaiId) {
            console.error('Error: currentSelesaiId kosong');
            return;
        }

        try {
            console.log('Mengirim request ke /persetujuan/selesai/' + currentSelesaiId);
            
            // Close the confirmation modal first
            if (bsModalSelesai) bsModalSelesai.hide();

            // Show loading state
            Swal.fire({
                title: 'Memproses',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send the request
            const response = await fetch(`/persetujuan/selesai/${currentSelesaiId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', response.status);
            
            const responseText = await response.text();
            console.log('Response text:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
                console.log('Parsed data:', data);
            } catch (parseError) {
                console.error('Error parsing JSON:', parseError);
                throw new Error('Invalid JSON response');
            }

            if (data.success) {
                // Show success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Bimbingan telah diselesaikan',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error lengkap:', error);
            Swal.fire({
                icon: 'error',
                title: 'Tidak dapat memproses permintaan',
                text: error.message || 'Silakan coba beberapa saat lagi',
                confirmButtonColor: '#1a73e8'
            });
        }
    });

    // Handle konfirmasi pembatalan
    document.getElementById('confirmBatal')?.addEventListener('click', async function() {
        const alasanInput = document.getElementById('alasanPembatalan');
        if (!alasanInput || !currentBatalId) {
            console.error('Data tidak lengkap:', { alasanInput, currentBatalId });
            return;
        }

        const alasan = alasanInput.value.trim();
        if (!alasan) {
            alasanInput.classList.add('is-invalid');
            return;
        }
        
        // Kumpulkan ID jadwal yang dipilih untuk dibatalkan
        const selectedSchedules = [];
        document.querySelectorAll('.related-schedule-check:checked').forEach(checkbox => {
            selectedSchedules.push(checkbox.value);
            console.log('Selected schedule:', checkbox.value);
        });

        try {
            // Tutup modal konfirmasi
            if (bsModalBatal) bsModalBatal.hide();

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            console.log('Sending cancellation request with data:', {
                alasan: alasan,
                related_schedules: selectedSchedules
            });

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Kirim request
            const response = await fetch(`/persetujuan/batal/${currentBatalId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    alasan: alasan,
                    related_schedules: selectedSchedules
                })
            });
            
            const responseText = await response.text();
            console.log('Cancellation response:', responseText);
            
            // Parse the response
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('Parsed API Response:', result);
            } catch (parseError) {
                console.error('Error parsing cancellation response:', parseError);
                throw new Error('Invalid JSON response from server');
            }

            if (result.success) {
                // Tampilkan notifikasi sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: selectedSchedules.length > 0 
                        ? `Berhasil membatalkan ${selectedSchedules.length + 1} jadwal bimbingan`
                        : 'Bimbingan telah dibatalkan',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            } else {
                // TAMBAHAN: Cek pesan error apakah tentang jadwal yang sudah lewat
                if (result.message && result.message.toLowerCase().includes('lewat waktu')) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Dapat Dibatalkan',
                        text: 'Jadwal bimbingan tidak dapat dibatalkan karena sudah lewat waktu',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }
                
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error during cancellation:', error);
            
            // TAMBAHAN: Cek pesan error lagi
            if (error.message && error.message.toLowerCase().includes('lewat waktu')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Dibatalkan',
                    text: 'Jadwal bimbingan tidak dapat dibatalkan karena sudah lewat waktu',
                    confirmButtonColor: '#dc3545'
                });
            } else {
                // Pesan error umum untuk masalah lainnya
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak dapat memproses permintaan',
                    text: error.message || 'Silakan coba beberapa saat lagi',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    });

    // Handle modal cleanup
    ['modalTerima', 'modalTolak', 'modalSelesai', 'modalBatal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        modal?.addEventListener('hidden.bs.modal', function() {
            if (modalId === 'modalTerima') {
                const input = document.getElementById('lokasiBimbingan');
                if (input) {
                    input.classList.remove('is-invalid');
                    input.value = '';
                }
                currentRow = null;
                currentId = null;
            } else if (modalId === 'modalTolak') {
                const input = document.getElementById('alasanPenolakan');
                if (input) {
                    input.classList.remove('is-invalid');
                    input.value = '';
                }
                currentRow = null;
                currentId = null;
            } else if (modalId === 'modalSelesai') {
                currentSelesaiId = null;
            } else if (modalId === 'modalBatal') {
                const input = document.getElementById('alasanPembatalan');
                if (input) {
                    input.classList.remove('is-invalid');
                    input.value = '';
                }
                currentBatalId = null;
            }
        });
    });

    // Implementasi fungsi pencarian
    const searchInputs = document.querySelectorAll('.search-box');
    searchInputs.forEach(searchInput => {
        const clearButton = searchInput.nextElementSibling.nextElementSibling;
        const table = searchInput.closest('.card-body').querySelector('table');
        
        if (clearButton && table) {
            // Tampilkan tombol clear saat input tidak kosong
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                clearButton.style.display = searchTerm ? 'flex' : 'none';
                
                // Filter tabel
                const rows = table.querySelectorAll('tbody tr');
                let foundMatch = false;
                
                rows.forEach(row => {
                    let rowMatch = false;
                    const cells = row.querySelectorAll('td');
                    
                    cells.forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(searchTerm.toLowerCase())) {
                            rowMatch = true;
                        }
                    });
                    
                    row.style.display = rowMatch || !searchTerm ? '' : 'none';
                    if (rowMatch) foundMatch = true;
                });
                
                // Tampilkan pesan jika tidak ada hasil
                const noResultsRow = table.querySelector('.no-results-row');
                if (!foundMatch && searchTerm) {
                    if (!noResultsRow) {
                        const tbody = table.querySelector('tbody');
                        const colCount = table.querySelectorAll('thead th').length;
                        const tr = document.createElement('tr');
                        tr.className = 'no-results-row';
                        tr.innerHTML = `<td colspan="${colCount}" class="text-center py-3">
                            <i class="bi bi-search me-2"></i> Tidak ada data yang cocok dengan pencarian "${searchTerm}"
                        </td>`;
                        tbody.appendChild(tr);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            });
            
            // Clear button handler
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        }
    });

// Google Calendar connect button handler
$('.google-connect-btn').click(function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    
    // Tambahkan parameter untuk identifikasi halaman asal
    url += (url.indexOf('?') !== -1 ? '&' : '?') + 'return_to=' + encodeURIComponent(window.location.href);
    
    // Alihkan ke halaman autentikasi Google secara langsung (tanpa popup)
    window.location.href = url;
    
    // Tidak perlu event listener dan kode popup lainnya
});
    /**
 * Fungsi untuk menampilkan notifikasi undangan berhasil dikirim
 */
function showInvitationNotification() {
    // Periksa apakah element notifikasi sudah ada di halaman
    let notif = document.getElementById('invitation-notification');
    
    // Jika belum ada, buat element baru
    if (!notif) {
        notif = document.createElement('div');
        notif.id = 'invitation-notification';
        notif.className = 'notification-popup';
        notif.innerHTML = `
            <div class="notification-header">
                <span>Undangan Bimbingan</span>
                <button type="button" class="close-btn" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
            <div class="notification-body">
                <p>Undangan bimbingan berhasil dikirim ke Google Calendar mahasiswa</p>
            </div>
        `;
        
        // Style untuk notifikasi
        notif.style.position = 'fixed';
        notif.style.bottom = '20px';
        notif.style.right = '20px';
        notif.style.width = '300px';
        notif.style.backgroundColor = '#fff';
        notif.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        notif.style.borderRadius = '8px';
        notif.style.borderLeft = '4px solid #28a745';
        notif.style.overflow = 'hidden';
        notif.style.zIndex = '9999';
        notif.style.animation = 'slideInUp 0.3s ease-out';
        
        // Style untuk header
        const header = notif.querySelector('.notification-header');
        header.style.display = 'flex';
        header.style.justifyContent = 'space-between';
        header.style.alignItems = 'center';
        header.style.padding = '10px 15px';
        header.style.backgroundColor = '#f8f9fa';
        header.style.borderBottom = '1px solid #dee2e6';
        
        // Style untuk tombol close
        const closeBtn = notif.querySelector('.close-btn');
        closeBtn.style.border = 'none';
        closeBtn.style.background = 'none';
        closeBtn.style.fontSize = '20px';
        closeBtn.style.cursor = 'pointer';
        closeBtn.style.color = '#6c757d';
        
        // Style untuk body
        const body = notif.querySelector('.notification-body');
        body.style.padding = '15px';
        
        // Tambahkan ke dokumen
        document.body.appendChild(notif);
        
        // Animasi slide-in
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInUp {
                from { transform: translateY(100%); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    } else {
        // Jika sudah ada, tampilkan kembali
        notif.style.display = 'block';
        notif.style.animation = 'slideInUp 0.3s ease-out';
    }
    
    // Otomatis hilangkan notifikasi setelah 5 detik
    setTimeout(() => {
        if (notif && notif.parentNode) {
            notif.style.animation = 'slideOutDown 0.3s ease-in forwards';
            setTimeout(() => {
                if (notif && notif.parentNode) {
                    notif.remove();
                }
            }, 300);
        }
    }, 5000);
}

/**
 * Menampilkan tombol batalkan pada jadwal
 */
function setupEventButtons() {
    // Ambil semua event di kalender
    const calendarEvents = document.querySelectorAll('.fc-event');
    
    calendarEvents.forEach(event => {
        // Ambil data event dari atribut data
        const eventData = event.fcSeg && event.fcSeg.eventRange && event.fcSeg.eventRange.def;
        if (!eventData) return;
        
        const extendedProps = eventData.extendedProps || {};
        
        // Cek apakah event bisa dibatalkan
        if (extendedProps.canBeCancelled) {
            // Tambahkan tombol batalkan jika belum ada
            if (!event.querySelector('.cancel-button')) {
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'cancel-button';
                cancelBtn.innerHTML = '&times;';
                cancelBtn.title = 'Batalkan jadwal bimbingan';
                
                // Styling tombol
                cancelBtn.style.position = 'absolute';
                cancelBtn.style.top = '2px';
                cancelBtn.style.right = '2px';
                cancelBtn.style.background = 'rgba(255,255,255,0.7)';
                cancelBtn.style.border = 'none';
                cancelBtn.style.borderRadius = '50%';
                cancelBtn.style.width = '18px';
                cancelBtn.style.height = '18px';
                cancelBtn.style.lineHeight = '16px';
                cancelBtn.style.fontSize = '14px';
                cancelBtn.style.color = '#dc3545';
                cancelBtn.style.cursor = 'pointer';
                cancelBtn.style.zIndex = '10';
                cancelBtn.style.display = 'none'; // Sembunyikan tombol secara default
                
                // Tampilkan tombol saat hover
                event.addEventListener('mouseenter', () => {
                    cancelBtn.style.display = 'block';
                });
                
                event.addEventListener('mouseleave', () => {
                    cancelBtn.style.display = 'none';
                });
                
                // Tambahkan event listener untuk tombol batalkan
                cancelBtn.addEventListener('click', (e) => {
                    e.stopPropagation(); // Hindari event click pada event calendar
                    
                    // Dapatkan ID jadwal
                    const jadwalId = extendedProps.id || eventData.publicId;
                    
                    // Tampilkan modal konfirmasi
                    showCancelConfirmation(jadwalId);
                });
                
                // Tambahkan ke event
                event.appendChild(cancelBtn);
                
                // Pastikan event memiliki position relative agar tombol batalkan bisa diposisikan
                event.style.position = 'relative';
            }
        }
        
        // Tambahkan class untuk event yang dibatalkan
        if (eventData.title && eventData.title.includes('[DIBATALKAN]')) {
            event.classList.add('cancelled-event');
            event.style.textDecoration = 'line-through';
            event.style.opacity = '0.7';
            event.style.backgroundColor = '#6c757d';
            event.style.borderColor = '#6c757d';
        }
    });
}

/**
 * Menampilkan modal konfirmasi pembatalan jadwal
 */
function showCancelConfirmation(jadwalId) {
    // Periksa apakah SweetAlert2 tersedia
    if (typeof Swal === 'undefined') {
        // Jika tidak ada SweetAlert2, gunakan confirm standard
        if (confirm('Anda yakin ingin membatalkan jadwal bimbingan ini?')) {
            // Minta alasan pembatalan
            const alasan = prompt('Masukkan alasan pembatalan:');
            if (alasan) {
                submitCancelRequest(jadwalId, alasan);
            }
        }
        return;
    }
    
    // Gunakan SweetAlert2 untuk modal yang lebih baik
    Swal.fire({
        title: 'Batalkan Jadwal Bimbingan?',
        text: 'Anda akan membatalkan jadwal bimbingan ini dan mengirim notifikasi ke mahasiswa.',
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Alasan Pembatalan',
        inputPlaceholder: 'Masukkan alasan pembatalan...',
        inputAttributes: {
            'required': 'true'
        },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: (alasan) => {
            if (!alasan.trim()) {
                Swal.showValidationMessage('Alasan pembatalan harus diisi!');
                return false;
            }
            
            // Cek jadwal terkait yang bisa dibatalkan juga
            return fetch(`/dosen/get-related-schedules/${jadwalId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Gagal mendapatkan jadwal terkait');
                return response.json();
            })
            .then(data => {
                // Simpan alasan dan jadwal terkait
                return {
                    alasan: alasan,
                    relatedSchedules: data.success && data.schedules && data.schedules.length > 0 ? data.schedules : []
                };
            })
            .catch(error => {
                Swal.showValidationMessage(`Error: ${error.message}`);
                return { alasan: alasan, relatedSchedules: [] };
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const { alasan, relatedSchedules } = result.value;
            
            // Jika ada jadwal terkait, tanyakan apakah ingin dibatalkan juga
            if (relatedSchedules && relatedSchedules.length > 0) {
                showRelatedSchedulesConfirmation(jadwalId, alasan, relatedSchedules);
            } else {
                // Jika tidak ada jadwal terkait, langsung batalkan jadwal utama
                submitCancelRequest(jadwalId, alasan);
            }
        }
    });
}

/**
 * Menampilkan konfirmasi jadwal terkait
 */
function showRelatedSchedulesConfirmation(jadwalId, alasan, relatedSchedules) {
    // Buat HTML untuk daftar jadwal terkait
    let relatedHtml = '<div class="related-schedules mt-3">' +
                       '<p class="font-weight-bold mb-2">Jadwal terkait yang juga bisa dibatalkan:</p>' +
                       '<div class="related-items">';
    
    relatedSchedules.forEach(schedule => {
        relatedHtml += `
            <div class="form-check mb-2">
                <input class="form-check-input related-schedule-item" type="checkbox" value="${schedule.id}" id="schedule-${schedule.id}">
                <label class="form-check-label" for="schedule-${schedule.id}">
                    ${schedule.mahasiswa_nama} - ${schedule.jenis_bimbingan ? schedule.jenis_bimbingan.toUpperCase() : 'Umum'} 
                    (${schedule.waktu_mulai} - ${schedule.waktu_selesai})
                </label>
            </div>
        `;
    });
    
    relatedHtml += '</div></div>';
    
    Swal.fire({
        title: 'Batalkan Jadwal Terkait?',
        html: `
            <p>Ditemukan ${relatedSchedules.length} jadwal bimbingan terkait pada waktu yang sama.</p>
            ${relatedHtml}
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan Semua yang Dipilih',
        cancelButtonText: 'Hanya Batalkan Jadwal Utama',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            // Dapatkan ID jadwal terkait yang dipilih
            const selectedSchedules = [];
            document.querySelectorAll('.related-schedule-item:checked').forEach(checkbox => {
                selectedSchedules.push(checkbox.value);
            });
            
            return {
                alasan: alasan,
                selectedSchedules: selectedSchedules
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { alasan, selectedSchedules } = result.value;
            submitCancelRequest(jadwalId, alasan, selectedSchedules);
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Batalkan hanya jadwal utama
            submitCancelRequest(jadwalId, alasan);
        }
    });
}

/**
 * Mengirim permintaan pembatalan ke server
 */
function submitCancelRequest(jadwalId, alasan, relatedSchedules = []) {
    // CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Tampilkan loading
    const loadingModal = typeof Swal !== 'undefined' ? 
        Swal.fire({
            title: 'Membatalkan jadwal...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        }) : null;
    
    // Kirim request pembatalan ke server
    fetch(`/dosen/batalkan-persetujuan/${jadwalId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            alasan: alasan,
            related_schedules: relatedSchedules
        })
    })
    .then(response => response.json())
    .then(data => {
        if (loadingModal) loadingModal.close();
        
        if (data.success) {
            // Tampilkan pesan sukses
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    // Refresh halaman atau refresh calendar
                    if (typeof calendar !== 'undefined' && calendar.refetchEvents) {
                        calendar.refetchEvents();
                    } else {
                        location.reload();
                    }
                });
            } else {
                alert('Berhasil: ' + data.message);
                // Refresh halaman
                location.reload();
            }
        } else {
            // Tampilkan pesan error
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            } else {
                alert('Gagal: ' + data.message);
            }
        }
    })
    .catch(error => {
        if (loadingModal) loadingModal.close();
        
        // Tampilkan pesan error
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat memproses permintaan: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        } else {
            alert('Error: ' + error.message);
        }
    });
}

// Tambahkan listener untuk callback setelah berhasil menyetujui usulan
document.addEventListener('DOMContentLoaded', function() {
    // Event listener untuk tombol setujui usulan
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-terima-usulan')) {
            const btnEl = e.target;
            const usulanId = btnEl.dataset.id;
            const lokasi = document.getElementById('lokasi-' + usulanId).value;
            
            // Simpan event handler asli
            const originalHandler = btnEl.onclick;
            
            // Override event handler
            btnEl.onclick = function(event) {
                // Prevent default
                event.preventDefault();
                
                // Kirim AJAX request
                fetch('/dosen/terima/' + usulanId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ lokasi: lokasi })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Tampilkan notifikasi undangan berhasil dikirim
                        showInvitationNotification();
                        
                        // Refresh calendar jika ada
                        if (typeof calendar !== 'undefined' && calendar.refetchEvents) {
                            calendar.refetchEvents();
                        }
                        
                        // Tampilkan pesan sukses
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Refresh halaman
                                location.reload();
                            });
                        } else {
                            alert('Berhasil: ' + data.message);
                            location.reload();
                        }
                    } else {
                        // Tampilkan pesan error
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memproses permintaan');
                });
            };
        }
    });
    
    // Setup tombol batalkan pada event calendar jika ada
    if (typeof calendar !== 'undefined') {
        // Hook ke eventDidMount untuk menambahkan tombol batalkan
        calendar.on('eventDidMount', function(info) {
            const event = info.el;
            const eventData = info.event;
            const extendedProps = eventData.extendedProps || {};
            
            // Cek apakah event bisa dibatalkan
            if (extendedProps.canBeCancelled) {
                // Tambahkan tombol batalkan
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'cancel-button';
                cancelBtn.innerHTML = '&times;';
                cancelBtn.title = 'Batalkan jadwal bimbingan';
                
                // Styling tombol
                cancelBtn.style.position = 'absolute';
                cancelBtn.style.top = '2px';
                cancelBtn.style.right = '2px';
                cancelBtn.style.background = 'rgba(255,255,255,0.7)';
                cancelBtn.style.border = 'none';
                cancelBtn.style.borderRadius = '50%';
                cancelBtn.style.width = '18px';
                cancelBtn.style.height = '18px';
                cancelBtn.style.lineHeight = '16px';
                cancelBtn.style.fontSize = '14px';
                cancelBtn.style.color = '#dc3545';
                cancelBtn.style.cursor = 'pointer';
                cancelBtn.style.zIndex = '10';
                cancelBtn.style.display = 'none'; // Sembunyikan tombol secara default
                
                // Tampilkan tombol saat hover
                event.addEventListener('mouseenter', () => {
                    cancelBtn.style.display = 'block';
                });
                
                event.addEventListener('mouseleave', () => {
                    cancelBtn.style.display = 'none';
                });
                
                // Tambahkan event listener untuk tombol batalkan
                cancelBtn.addEventListener('click', (e) => {
                    e.stopPropagation(); // Hindari event click pada event calendar
                    
                    // Dapatkan ID jadwal
                    const jadwalId = extendedProps.id || eventData.id;
                    
                    // Tampilkan modal konfirmasi
                    showCancelConfirmation(jadwalId);
                });
                
                // Tambahkan ke event
                event.appendChild(cancelBtn);
                
                // Pastikan event memiliki position relative agar tombol batalkan bisa diposisikan
                event.style.position = 'relative';
            }
            
            // Tambahkan class untuk event yang dibatalkan
            if (eventData.title && eventData.title.includes('[DIBATALKAN]')) {
                event.classList.add('cancelled-event');
                event.style.textDecoration = 'line-through';
                event.style.opacity = '0.7';
                event.style.backgroundColor = '#6c757d';
                event.style.borderColor = '#6c757d';
            }
        });
    } else {
        // Jika FullCalendar tidak tersedia, gunakan DOM standard
        // Cek secara periodik untuk event calendar
        setInterval(setupEventButtons, 1000);
    }
});

// Tambahkan CSS untuk styling
const style = document.createElement('style');
style.textContent = `
    /* Style untuk event yang dibatalkan */
    .cancelled-event {
        text-decoration: line-through !important;
        opacity: 0.7 !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
    }
    
    /* Animasi untuk notifikasi */
    @keyframes slideInUp {
        from { transform: translateY(100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    @keyframes slideOutDown {
        from { transform: translateY(0); opacity: 1; }
        to { transform: translateY(100%); opacity: 0; }
    }
    
    /* Style untuk notifikasi */
    .notification-popup {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 300px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        border-left: 4px solid #28a745;
        overflow: hidden;
        z-index: 9999;
        animation: slideInUp 0.3s ease-out;
    }
`;
document.head.appendChild(style);
});
</script>
@endpush
