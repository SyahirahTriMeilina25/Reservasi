@extends('layouts.app')

@section('title', 'Masukkan Jadwal Bimbingan')

@push('styles')
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.css' rel='stylesheet'>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* ================ EXTERNAL EVENT ================ */
        .external-event {
            color: #757575 !important;
            background-color: #E8AA42 !important;
            border-color: #757575 !important;
            font-style: italic;
            border: none !important;
            padding: 4px 8px !important;
            margin: 2px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            line-height: 1.4 !important;
            font-weight: 500 !important;
            box-shadow: var(--shadow-md) !important;
            transition: all 0.2s ease-in-out !important;
            font-style: italic;

        }

        a.external-event {
            color: var(--neutral-50) !important;
        }

        a.external-event:hover {
            color: #000000 !important;
        }

        .fc-event {
            border: none !important;
            padding: 2px 4px !important;
            margin: 2px !important;
            border-radius: 4px !important;
            overflow: hidden !important;
        }

        /* Base Styles */
        :root {
            /* Brand Colors */
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;

            /* Neutral Colors */
            --neutral-50: #f8fafc;
            --neutral-100: #f1f5f9;
            --neutral-200: #e2e8f0;
            --neutral-300: #cbd5e1;
            --neutral-400: #94a3b8;
            --neutral-500: #64748b;
            --neutral-600: #475569;
            --neutral-700: #334155;
            --neutral-800: #1e293b;

            /* Event Colors */
            --event-blue: #e0f2fe;
            --event-red: #fee2e2;
            --event-green: #dcfce7;
            --event-yellow: #fef3c7;

            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        /* ================ CONTAINER & LAYOUT ================ */
        #calendar {
            background: white;
        }

        .calendar-container {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            padding: 24px;
            margin: 0 auto;
            max-width: none; /* Ubah ini dari 1200px */
            width: 100%; /* Tambah ini */
            height: 100%; /* Ubah ini dari 100% */
            min-height: 800px; /* Tambah ini */
            display: flex;
            flex-direction: column;
        }

        /* ================ TOOLBAR & NAVIGATION ================ */
        .fc .fc-toolbar {
            position: relative;
            margin-bottom: 32px !important;
            padding: 16px 24px;
            background: var(--neutral-50);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .fc .fc-toolbar-title {
            font-size: 24px !important;
            font-weight: 600;
            color: var(--neutral-800);
            letter-spacing: -0.025em;
        }

        /* Button Styling */
        .fc .fc-button {
            border-radius: 12px !important;
            font-weight: 500 !important;
            height: 40px !important;
            padding: 0 20px !important;
            font-size: 14px !important;
            transition: all 0.2s ease-in-out !important;
            box-shadow: var(--shadow-md) !important;
        }

        .fc .fc-button-primary {
            background: white !important;
            border: 1px solid var(--neutral-200) !important;
            color: var(--neutral-700) !important;
        }

        .fc .fc-button-primary:hover {
            background: var(--neutral-50) !important;
            border-color: var(--neutral-300) !important;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md) !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: white !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active:hover {
            background: var(--primary-dark) !important;
        }

        /* Button Groups */
        .fc-button-group {
            box-shadow: var(--shadow-sm);
            border-radius: 12px;

            gap: 1px;
        }

        .fc-button-group .fc-button {
            border-radius: 0 !important;
            margin: 0 !important;
        }

        .fc-button-group .fc-button:first-child {
            border-top-left-radius: 12px !important;
            border-bottom-left-radius: 12px !important;
        }

        .fc-button-group .fc-button:last-child {
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
        }

        /* ================ CALENDAR HEADER ================ */
        .fc-theme-standard th {
            padding: 8px 0 4px 0 !important;
            background: white;
        }

        .fc-col-header-cell-cushion {
            padding: 4px !important;
            color: var(--neutral-600) !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em;
            text-decoration: none !important;
        }

        /* ================ CALENDAR GRID ================ */
        .fc-theme-standard td,
        .fc-theme-standard th {
            border: 1px solid var(--neutral-200) !important;
        }


        /* Date Cell Styling ukuran grid kalender*/
        .fc .fc-daygrid-day-frame {
            min-height: 120px !important;
            padding: 8px !important;
        }

        .fc .fc-daygrid-day-top {
            justify-content: center !important;
            padding-top: 0px !important;
        }

        .fc-view-harness,
        .fc-view-harness-active,
        .fc-view {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .fc .fc-daygrid-day-number {
            width: 32px !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: var(--neutral-700) !important;
            text-decoration: none !important;
            border-radius: 50% !important;
            transition: all 0.2s ease;
        }

        /* Today Styling */
        .fc .fc-day-today {
            background: var(--event-blue) !important;
        }

        .fc .fc-day-today .fc-daygrid-day-number {
            background: var(--primary-dark) !important;
            color: white !important;
            font-weight: 600 !important;
        }

        /* Weekend Days */
        .fc-day-sat,
        .fc-day-sun {
            background: var(--neutral-50) !important;
        }

        /* Other Month Days */
        .fc-day-other {
            background: var(--neutral-50) !important;
        }

        .fc-day-other .fc-daygrid-day-number {
            color: var(--neutral-400) !important;

        }

        /* ================ EVENTS STYLING ================ */
        .fc-event {
            border: none !important;
            padding: 4px 8px !important;
            margin: 2px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            line-height: 1.4 !important;
            font-weight: 500 !important;
            box-shadow: var(--shadow-md) !important;
            background-color: #161D6F;
            transition: all 0.2s ease-in-out !important;
            color: var(--neutral-50) !important;
        }

        .fc-event:hover {
            transform: translateY(-1px) scale(1.02) !important;
            box-shadow: var(--shadow-md) !important;
            color: var(--neutral-800) !important;
        }


        /* Tersedia Styling */
        .small,
        small {
            color: #A0E4CB;
            transition: color 0.1s ease-in-out;
        }

        .small:hover {
            color: #17594A !important;
        }


        /* Event Types */
        .fc-event-krs {
            background: var(--event-blue) !important;
            color: #0369a1 !important;
            border-left: 3px solid #0284c7 !important;
        }

        .fc-event-kp {
            background: var(--event-red) !important;
            color: #b91c1c !important;
            border-left: 3px solid #dc2626 !important;
        }

        .fc-event-mbkm {
            background: var(--event-yellow) !important;
            color: #92400e !important;
            border-left: 3px solid #d97706 !important;
        }

        .fc-event-skripsi {
            background: var(--event-green) !important;
            color: #166534 !important;
            border-left: 3px solid #16a34a !important;
        }

        /* ================ MODAL STYLING ================ */
        .modal-content {
            border: none;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--neutral-200);
            background: var(--neutral-50);
            border-radius: 24px 24px 0 0;
        }

        .modal-header .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--neutral-800);
        }

        .modal-body {
            padding: 24px;
        }

        /* Form Elements */
        .form-label {
            font-weight: 500;
            color: var(--neutral-700);
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid var(--neutral-300);
            padding: 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }



        /* ================ MORE EVENTS LINK ================ */
        .fc-daygrid-more-link {
            color: var(--primary) !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            text-decoration: none !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            background: var(--neutral-50) !important;
            transition: all 0.2s ease !important;
        }

        .fc-daygrid-more-link:hover {
            background: var(--neutral-100) !important;
            color: var(--primary-dark) !important;
        }

        /* ================ LOADING STATE ================ */
        .fc-loading {
            position: relative;
        }

        .fc-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin: -20px 0 0 -20px;
            border: 3px solid var(--neutral-200);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spinner 0.8s linear infinite;
        }

        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
        }

        /* ================ RESPONSIVE DESIGN ================ */
        @media (max-width: 768px) {
            .calendar-container {
                padding: 16px;
                margin: 16px;
                border-radius: 16px;
            }

            .fc .fc-toolbar {
                flex-direction: column;
                padding: 16px;
                gap: 12px;
            }

            .fc .fc-toolbar-title {
                font-size: 20px !important;
            }

            .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .fc .fc-button {
                padding: 0 16px !important;
                height: 36px !important;
                font-size: 13px !important;
            }

            .fc .fc-daygrid-day-frame {
                min-height: 80px !important;
            }
        }

        /* ================ ANIMATIONS ================ */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fc-event-new {
            animation: fadeIn 0.3s ease-out;
        }

        /* Legend Styling */
        .calendar-legend {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 8px;
        }

        .swal2-popup {
            padding: 1.5em;
        }

        .swal2-html-container {
            text-align: center !important;
            margin: 1em 0;
        }

        /* Styling untuk container detail */
        .detail-container {
            text-align: left;
            padding: 10px 0;
        }

        /* Styling untuk setiap item detail */
        .detail-item {
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-item strong {
            color: #1a73e8;
            font-weight: 600;
            font-size: 0.9em;
        }

        .detail-item span {
            color: #333;
            padding-left: 4px;
        }

        /* Styling untuk tombol */
        .swal2-confirm.swal2-styled {
            padding: 0.5em 2em;
            font-weight: 500;
        }

        .swal2-cancel.swal2-styled {
            padding: 0.5em 2em;
            font-weight: 500;
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
<div class="container mt-4">
    <h1 class="mb-2 gradient-text fw-bold">Masukkan Jadwal</h1>
    <hr>
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ url('/persetujuan') }}">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </button>
    
    @if(!$isConnected)
        <div class="info-box">
            <p class="mb-2">Untuk menggunakan fitur ini, Anda perlu memberikan izin akses ke Google Calendar dengan email: <strong>{{ $email }}</strong></p>
            <a href="{{ route('dosen.google.connect') }}" class="btn btn-connect">
                <i class="fas fa-calendar-plus"></i>
                Hubungkan dengan Google Calendar
            </a>
        </div>
    @else
        <div class="calendar-container">
            <div id="calendar" class="w-100"></div>
        </div>
    @endif
</div>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="h4 gradient-text fw-bold">Tambah Jadwal Bimbingan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Waktu Mulai</label>
                                <input type="time" class="form-control" id="startTime" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Waktu Selesai</label>
                                <input type="time" class="form-control" id="endTime" required>
                            </div>
                        </div>
                        <div id="timeValidationFeedback"></div>
                        <small class="text-muted mt-2 d-block">Jadwal tersedia pada jam kerja (08:00 - 18:00)<br>Durasi minimum bimbingan adalah 30 menit</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="eventDescription" rows="3" ></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveEvent">Simpan Jadwal</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/id.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/id.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        console.error('CSRF token tidak ditemukan');
        return;
    }

    let calendar;
    let selectedDate = null;

    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Elemen kalender tidak ditemukan');
        return;
    }

    function formatDateTime(date) {
        return moment(date).format('DD MMM YYYY HH:mm');
    }

    const tampilkanPesan = (icon, text) => {
        Swal.fire({
            icon: icon,
            text: text,
            confirmButtonColor: '#1a73e8'
        });
    };

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        views: {
            dayGridMonth: {
                titleFormat: { year: 'numeric', month: 'long' }
            },
            timeGridWeek: {
                titleFormat: { year: 'numeric', month: 'long', day: '2-digit' }
            },
            timeGridDay: {
                titleFormat: { year: 'numeric', month: 'long', day: '2-digit' }
            }
        },
        firstDay: 1,
        locale: 'id',
        buttonIcons: true,
        navLinks: true,
        editable: true,
        dayMaxEvents: true,
        selectable: true,
        selectMirror: true,
        nowIndicator: true,
        height: '800px',
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        allDaySlot: false,
        slotDuration: '00:30:00',
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '08:00',
            endTime: '18:00',
        },

        eventDidMount: function(info) {
            const eventEl = info.el;
            const event = info.event;
            
            if (event.classNames.includes('external-event')) {
                eventEl.style.opacity = '0.7';
            }
        },
        
        dateClick: function(info) {
            const hari = info.date.getDay();
            if (hari === 0 || hari === 6) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Tersedia',
                    text: 'Tidak dapat membuat jadwal di hari Sabtu atau Minggu',
                    confirmButtonColor: '#1a73e8'
                });
                return;
            }

            selectedDate = info.date;
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
        },

        eventClassNames: function(arg) {
            return ['fc-event-' + arg.event.extendedProps.jenis];
        },

        eventContent: function(arg) {
            return {
                html: `
                    <div class="fc-content">
                        <div class="fc-title">${arg.event.title}</div>
                        ${arg.event.extendedProps.status ? 
                            `<div class="fc-status small">${arg.event.extendedProps.status}</div>` : 
                            ''}
                    </div>
                `
            };
        },

        eventClick: function(info) {
            if (info.event.classNames.includes('external-event')) {
                Swal.fire({
                    title: info.event.title,
                    html: `
                        <div class="text-center">
                            <p><strong>Waktu:</strong> ${moment(info.event.start).format('HH:mm')} - ${moment(info.event.end).format('HH:mm')}</p>
                            ${info.event.extendedProps.description ? `<p><strong>Deskripsi:</strong> ${info.event.extendedProps.description}</p>` : ''}
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonColor: '#1a73e8'
                });
                return;
            }

            // Parse description untuk memisahkan informasi
            const description = info.event.extendedProps.description || '';
            const descriptionLines = description.split('\n').filter(line => line.trim());
            
            // Membuat tampilan yang lebih terstruktur
            const details = descriptionLines.reduce((acc, line) => {
                if (line.startsWith('Status:')) {
                    acc.status = line.replace('Status:', '').trim();
                } else if (line.startsWith('Catatan:')) {
                    acc.catatan = line.replace('Catatan:', '').trim();
                } else if (line.startsWith('Kapasitas:')) {
                    acc.kapasitas = line.replace('Kapasitas:', '').trim();
                }
                return acc;
            }, {});

            Swal.fire({
                title: 'Detail Jadwal Bimbingan',
                html: `
                    <div class="detail-container">
                        <div class="detail-item">
                            <strong>Tanggal:</strong>
                            <span>${moment(info.event.start).format('DD MMMM YYYY')}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Waktu:</strong>
                            <span>${moment(info.event.start).format('HH:mm')} - ${moment(info.event.end).format('HH:mm')}</span>
                        </div>
                        ${details.kapasitas ? `
                            <div class="detail-item">
                                <strong>Kapasitas:</strong>
                                <span>${details.kapasitas || ''}</span>
                            </div>
                        ` : ''}
                        ${details.catatan ? `
                            <div class="detail-item">
                                <strong>Catatan:</strong>
                                <span>${details.catatan}</span>
                            </div>
                        ` : ''}
                        <div class="detail-item">
                            <strong>Status:</strong>
                            <span>${details.status || 'Tersedia'}</span>
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Hapus Jadwal',
                cancelButtonText: 'Tutup',
                showCloseButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Konfirmasi penghapusan
                    Swal.fire({
                        title: 'Hapus Jadwal?',
                        text: "Jadwal yang dihapus tidak dapat dikembalikan",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            hapusJadwal(info.event.id);
                        }
                    });
                }
            });
        },

        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('dosen/google/events')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(events => {
                    successCallback(events);
                })
                .catch(error => {
                    console.error('Error:', error);
                    failureCallback(error);
                    tampilkanPesan('error', 'Gagal memuat jadwal');
                });
        }
    });

    calendar.render();

    // Handler Simpan Jadwal
    document.getElementById('saveEvent')?.addEventListener('click', async function() {
        try {
            const description = document.getElementById('eventDescription').value;
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;

            if (!startTime || !endTime) {
                throw new Error('Mohon isi waktu mulai dan selesai');
            }

            // Buat objek tanggal dari selectedDate
            const startDateTime = new Date(selectedDate);
            const endDateTime = new Date(selectedDate);
            
            // Parse waktu
            const [startHour, startMinute] = startTime.split(':');
            const [endHour, endMinute] = endTime.split(':');
            
            // Set jam dan menit
            startDateTime.setHours(parseInt(startHour), parseInt(startMinute), 0, 0);
            endDateTime.setHours(parseInt(endHour), parseInt(endMinute), 0, 0);

            // Debug log sebelum mengirim request
            console.log('Selected Date:', selectedDate);
            console.log('Start DateTime to send:', startDateTime.toISOString());
            console.log('End DateTime to send:', endDateTime.toISOString());
            
            // Validasi waktu selesai harus setelah waktu mulai
            if (endDateTime <= startDateTime) {
                throw new Error('Waktu selesai harus setelah waktu mulai');
            }

            // Validasi jam kerja (08:00 - 18:00)
            const startHourInt = parseInt(startHour);
            if (startHourInt < 8 || startHourInt >= 18) {
                throw new Error('Jadwal harus dalam jam kerja (08:00 - 18:00)');
            }

            // Hitung durasi dalam menit
            const durationMs = endDateTime.getTime() - startDateTime.getTime();
            const durationMinutes = Math.floor(durationMs / (1000 * 60));

            // Debug log durasi
            console.log('Duration (minutes):', durationMinutes);

            // Validasi durasi minimum (30 menit)
            if (durationMinutes < 30) {
                throw new Error(`Durasi minimum bimbingan adalah 30 menit. Durasi saat ini: ${durationMinutes} menit`);
            }

            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan Jadwal',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Log data yang akan dikirim
            const requestData = {
                start: startDateTime.toISOString(),
                end: endDateTime.toISOString(),
                description: description,
            };
            console.log('Request Data:', requestData);

            const response = await fetch('/masukkanjadwal/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });

            // Log response status dan headers
            console.log('Response Status:', response.status);
            console.log('Response Headers:', response.headers);

            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.message || 'Terjadi kesalahan pada server');
            }
            
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                document.getElementById('eventForm').reset();
                calendar.refetchEvents();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Jadwal berhasil ditambahkan',
                    confirmButtonColor: '#1a73e8'
                });
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error:', error);
            console.log('Gagal menambahkan jadwal :', error.message);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menambahkan jadwal',
                confirmButtonColor: '#1a73e8'
            });
        }
    });

    // Fungsi Hapus Jadwal
    async function hapusJadwal(eventId) {
        try {
            // Tampilkan loading
            Swal.fire({
                title: 'Menghapus Jadwal',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch(`/masukkanjadwal/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                calendar.refetchEvents();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Jadwal berhasil dihapus dari sistem dan Google Calendar',
                    confirmButtonColor: '#1a73e8'
                });
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error:', error);
            console.log('Gagal menghapus jadwal: +', error.message);
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menghapus jadwal',
                confirmButtonColor: '#1a73e8'
            });
        }
    }

    document.getElementById('eventModal').addEventListener('show.bs.modal', function () {
        document.getElementById('startTime').value = '';
        document.getElementById('endTime').value = '';
        document.getElementById('eventDescription').value = '';
    });
    document.getElementById('startTime')?.addEventListener('change', validateTimes);
    document.getElementById('endTime')?.addEventListener('change', validateTimes);

    function validateTimes() {
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;
        const saveButton = document.getElementById('saveEvent');
        const feedbackEl = document.getElementById('timeValidationFeedback');

        if (startTime && endTime) {
            const [startHour, startMinute] = startTime.split(':');
            const [endHour, endMinute] = endTime.split(':');
            
            const start = new Date();
            start.setHours(parseInt(startHour), parseInt(startMinute), 0, 0);
            
            const end = new Date();
            end.setHours(parseInt(endHour), parseInt(endMinute), 0, 0);

            const durationMinutes = Math.floor((end.getTime() - start.getTime()) / (1000 * 60));
            
            console.log('Real-time validation - Duration:', durationMinutes);

            let errorMessage = '';
            
            if (end <= start) {
                errorMessage = 'Waktu selesai harus lebih besar dari waktu mulai';
            } else if (durationMinutes < 30) {
                errorMessage = `Durasi minimum bimbingan adalah 30 menit. Durasi saat ini: ${durationMinutes} menit`;
            } else if (parseInt(startHour) < 8 || parseInt(startHour) >= 18 || 
                    parseInt(endHour) < 8 || parseInt(endHour) > 18) {
                errorMessage = 'Jadwal harus dalam jam kerja (08:00 - 18:00)';
            }

            if (errorMessage) {
                feedbackEl.innerHTML = `<div class="text-danger small mt-2">${errorMessage}</div>`;
                saveButton.disabled = true;
            } else {
                feedbackEl.innerHTML = `<div class="text-success small mt-2">Durasi bimbingan: ${durationMinutes} menit</div>`;
                saveButton.disabled = false;
            }
        }
    }

});
</script>
@endpush