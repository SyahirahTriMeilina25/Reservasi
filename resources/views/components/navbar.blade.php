<!-- CSS yang diperbaiki untuk spacing mobile -->
<style>
    /* Dropdown menu desktop - TETAP SAMA */
    .custom-dropdown-menu {
        min-width: 220px !important;
        white-space: nowrap;
        left: auto !important;
        right: 0 !important;
        margin-top: 8px !important;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        padding: 8px;
        z-index: 1050;
    }
    
    /* Mobile improvements - HANYA SPACING YANG DIPERBAIKI */
    @media (max-width: 991.98px) {
        .custom-dropdown-menu {
            position: static !important;
            float: none !important;
            width: 100% !important;
            margin-top: 0.5rem !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
            background-color: #f8fafc !important;
        }
    
        /* Navbar collapse styling - TETAP SAMA */
        .navbar-collapse {
            background-color: #ffffff !important;
            padding: 1rem !important;
            margin-top: 0.5rem !important;
            border-radius: 12px !important;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #f1f5f9 !important;
        }
    
        /* Nav items spacing - TETAP SAMA */
        .navbar-nav {
            margin-bottom: 0.5rem;
        }
        
        .navbar-nav .nav-item {
            margin: 0.5rem 0;
            padding-bottom: 0.5rem;
        }
        
        .navbar-nav .nav-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .navbar-nav .nav-link {
            padding: 0.5rem 0 !important;
            font-size: 1rem;
        }
    
        /* Account section spacing - DIPERBAIKI JARAK */
        .d-flex.align-items-center {
            margin-top: 0.25rem !important; /* Dikurangi dari 1rem jadi 0.5rem */
            padding-top: 0.25rem !important; /* Dikurangi dari 1rem jadi 0.5rem */
            justify-content: flex-start !important;
        }
        
        .custom-dropdown-btn {
            background: none !important;
            border: none !important;
            padding: 0.5rem 0 !important;
            text-align: left !important;
            font-weight: bold !important;
            color: #333 !important;
        }
        
        .custom-dropdown-btn:hover {
            color: #0d6efd !important;
        }
    
        /* Burger button styling - TETAP SAMA */
        .navbar-toggler {
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
        }
    
        .navbar-toggler-icon {
            filter: brightness(0.6);
            width: 1.2em !important;
            height: 1.2em !important;
        }
    }
    
    /* Small mobile adjustments - TETAP SAMA */
    @media (max-width: 576px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    
        .navbar-brand {
            font-size: 1.1rem !important;
        }
        
        .navbar-brand img {
            width: 25px !important;
            height: 25px !important;
        }
    
        .custom-dropdown-menu {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
    }
    
    /* Desktop styling - TETAP SAMA */
    .navbar-toggler {
        border: none !important;
        padding: 4px 8px !important;
        border-radius: 6px;
    }
    
    .navbar-toggler:focus {
        box-shadow: none !important;
    }
    
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        width: 1.5em;
        height: 1.5em;
    }
    
    /* Dropdown item spacing - TETAP SAMA */
    .custom-dropdown-item {
        padding: 12px 16px !important;
        border-radius: 8px !important;
        margin: 2px 4px !important;
        display: flex !important;
        align-items: center !important;
        transition: all 0.2s ease !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .custom-dropdown-item:hover {
        background-color: #e9ecef !important;
    }
    
    /* Smooth transitions - TETAP SAMA */
    .navbar-collapse {
        transition: all 0.3s ease;
    }
</style>

<!-- Navbar HTML yang sama, tapi dengan styling yang diperbaiki -->
<div class="bg-gradient-bar"></div>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top" style="box-shadow: 0px 0px 10px 1px #afafaf">
    <div class="container">
        @if(Auth::guard('admin')->check())
            <a class="navbar-brand me-4" href="{{ route('admin.dashboard') }}" style="font-family: 'Viga', sans-serif; font-weight: 600; font-size: 25px;">
        @elseif(Auth::guard('dosen')->check())
            <a class="navbar-brand me-4" href="{{ url('/persetujuan') }}" style="font-family: 'Viga', sans-serif; font-weight: 600; font-size: 25px;">
        @else
            <a class="navbar-brand me-4" href="{{ url('/usulanbimbingan') }}" style="font-family: 'Viga', sans-serif; font-weight: 600; font-size: 25px;">
        @endif
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2c/LOGO-UNRI.png" alt="SITEI Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
            SEPTI
        </a>

        <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @if(Auth::guard('admin')->check())
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/dashboard*') ? 'active' : '' }}" 
                           style="font-weight: bold;" 
                           href="{{ route('admin.dashboard') }}">DASHBOARD</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/data-admin*') || Request::is('admin/edit-admin*') ? 'active' : '' }}" 
                           style="font-weight: bold;" 
                           href="{{ route('admin.dataadmin') }}">KELOLA ADMIN</a>
                    </li>
                @elseif(Auth::guard('dosen')->check())
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('persetujuan*') || Request::is('masukkanjadwal*') || Request::is('riwayatdosen*') || Request::is('editusulan*') || Request::is('terimausulanbimbingan*') ? 'active' : '' }}" 
                           style="font-weight: bold;" 
                           href="{{ url('/persetujuan') }}">RESERVASI</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('usulanbimbingan*') || Request::is('pilihjadwal*') || Request::is('detaildaftar*') || Request::is('riwayatmahasiswa*') ? 'active' : '' }}" 
                           style="font-weight: bold;" 
                           href="{{ url('/usulanbimbingan') }}">RESERVASI</a>
                    </li>
                @endif
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn text-dark dropdown-toggle custom-dropdown-btn" style="font-weight: bold;" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        AKUN
                    </button>
                    <ul class="dropdown-menu custom-dropdown-menu fw-semibold" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item custom-dropdown-item" href="/profil">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="dropdown-item p-0">
                                @csrf
                                <button type="submit" class="dropdown-item w-100 custom-dropdown-item text-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- JavaScript yang sama - tetap berfungsi -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.getElementById('navbarNav');

    if (toggler && navbarCollapse) {
        toggler.addEventListener('click', function () {
            const isShown = navbarCollapse.classList.contains('show');
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, { toggle: false });
            isShown ? bsCollapse.hide() : bsCollapse.show();
        });
    }

    // Auto close on menu click (mobile only)
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(function (navLink) {
        navLink.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                const bsCollapse = new bootstrap.Collapse(navbarCollapse, { toggle: false });
                bsCollapse.hide();
            }
        });
    });
});
</script>