<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <!-- Anti-cache meta tags -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="turbolinks-cache-control" content="no-cache">
    
    <title>SEPTI - @yield('title', 'Sistem Informasi Teknik Informatika')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Viga&display=swap" rel="stylesheet">
    
    <!-- Custom Global CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    {{-- custom logo website --}}
    <link rel="icon" href="{{ asset('images/logounri.png') }}" type="image/png">

    <!-- Page Specific Styles -->

    @stack('styles')
    <style>
        body {
            font-family: "Open Sans", sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }
        
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            mix-blend-mode: multiply;
            animation: blob 7s infinite;
            pointer-events: none;
        }
        
        .blob-1 { 
            top: 0; 
            left: 0; 
            width: 300px; 
            height: 300px; 
            background-color: rgba(74, 222, 128, 0.1); 
        }
        
        .blob-2 { 
            top: 50%; 
            right: 0; 
            width: 350px; 
            height: 350px; 
            background-color: rgba(251, 191, 36, 0.1); 
            animation-delay: 2s;
        }
        
        .blob-3 { 
            bottom: 0; 
            left: 50%; 
            width: 350px; 
            height: 350px; 
            background-color: rgba(239, 68, 68, 0.1); 
            animation-delay: 4s;
        }
        
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -50px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(50px, 50px) scale(1.05); }
        }
        
        .nav-link {
            position: relative;
            color: #4b5563;
            transition: color 0.3s ease;
            font-weight: bold;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #3674B5;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #3674B5;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after, .nav-link.active::after {
            width: 100%;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-light">
    <!-- Anti-back button script -->
    <script>
    (function() {
        // Deteksi navigasi dari cache browser (back/forward button)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Halaman dimuat dari cache browser, paksa reload
                window.location.reload(true);
            }
        });
        
        // Deteksi jenis navigasi
        if (performance && performance.navigation) {
            if (performance.navigation.type === 2) { // 2 = back/forward navigation
                // Paksa reload untuk mendapatkan state terbaru
                window.location.reload(true);
            }
        }
        
        // Deteksi histori navigasi modern
        if (window.performance && window.performance.getEntriesByType) {
            const navEntries = window.performance.getEntriesByType('navigation');
            if (navEntries.length > 0 && navEntries[0].type === 'back_forward') {
                window.location.reload(true);
            }
        }
        
        // Disable browser back functionality after logout
        history.pushState(null, null, document.URL);
        window.addEventListener('popstate', function() {
            history.pushState(null, null, document.URL);
            
            // Periksa status autentikasi ketika user mencoba kembali
            fetch('/auth-check', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache, no-store, must-revalidate, max-age=0'
                },
                cache: 'no-store'
            })
            .then(response => {
                if (!response.ok) {
                    window.location.href = "{{ route('login') }}";
                }
            })
            .catch(() => {
                window.location.href = "{{ route('login') }}";
            });
        });
    })();
    </script>
    
    @include('components.blobbackground')
    @include('components.navbar')
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <main class="flex-grow-1">
        @yield('content')
    </main>
    
    @include('components.footer')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script pengecekan autentikasi -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk memeriksa apakah user masih login
        function checkAuthStatus() {
            fetch('/auth-check', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache, no-store, must-revalidate, max-age=0'
                },
                cache: 'no-store'
            })
            .then(response => {
                if (!response.ok) {
                    window.location.href = "{{ route('login') }}";
                }
            })
            .catch(() => {
                window.location.href = "{{ route('login') }}";
            });
        }
        
        // Periksa saat halaman dimuat
        checkAuthStatus();
        
        // Periksa secara berkala
        setInterval(checkAuthStatus, 30000); // Periksa setiap 30 detik
    });
    </script>
    
    @stack('scripts')
</body>
</html>