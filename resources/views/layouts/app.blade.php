<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - KREASI RPL</title>
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#064A8E">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="KREASI RPL">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo/icon-192.png') }}">
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/kreasi-logo.png') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- File upload -->
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css') }}">
    <!-- Plyr -->
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <!-- Apex Charts -->
    <link rel="stylesheet" href="{{ asset('assets/css/apexcharts.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}?v={{ filemtime(public_path('assets/css/main.css')) }}">
    <!-- KREASI RPL Custom Branding -->
    <link rel="stylesheet" href="{{ asset('assets/css/kreasi-custom.css') }}?v={{ filemtime(public_path('assets/css/kreasi-custom.css')) }}">
    
    <style>
        /* Force button visibility across entire application regardless of browser cache */
        .btn-outline-main,
        .btn-outline-primary {
            background-color: #eef5fc !important;
            border: 1.5px solid #064A8E !important;
            color: #064A8E !important;
            font-weight: 600 !important;
        }
        .btn-outline-main *,
        .btn-outline-primary * {
            color: #064A8E !important;
        }
        .btn-outline-main:hover,
        .btn-outline-primary:hover {
            background-color: #064A8E !important;
            border-color: #064A8E !important;
            color: #ffffff !important;
        }
        .btn-outline-main:hover *,
        .btn-outline-primary:hover * {
            color: #ffffff !important;
        }

        .btn-outline-gray,
        .btn-outline-secondary {
            background-color: #f1f5f9 !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #334155 !important;
            font-weight: 600 !important;
        }
        .btn-outline-gray *,
        .btn-outline-secondary * {
            color: #334155 !important;
        }
        .btn-outline-gray:hover,
        .btn-outline-secondary:hover {
            background-color: #e2e8f0 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        .btn-outline-gray:hover *,
        .btn-outline-secondary:hover * {
            color: #0f172a !important;
        }

        .btn-outline-warning {
            background-color: #fffbeb !important;
            border: 1.5px solid #f59e0b !important;
            color: #b45309 !important;
            font-weight: 600 !important;
        }
        .btn-outline-warning * {
            color: #b45309 !important;
        }
        .btn-outline-warning:hover {
            background-color: #f59e0b !important;
            border-color: #f59e0b !important;
            color: #ffffff !important;
        }
        .btn-outline-warning:hover * {
            color: #ffffff !important;
        }

        .btn-outline-danger {
            background-color: #fef2f2 !important;
            border: 1.5px solid #ef4444 !important;
            color: #b91c1c !important;
            font-weight: 600 !important;
        }
        .btn-outline-danger * {
            color: #b91c1c !important;
        }
        .btn-outline-danger:hover {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
            color: #ffffff !important;
        }
        .btn-outline-danger:hover * {
            color: #ffffff !important;
        }

        .btn-outline-success {
            background-color: #f0fdf4 !important;
            border: 1.5px solid #22c55e !important;
            color: #15803d !important;
            font-weight: 600 !important;
        }
        .btn-outline-success * {
            color: #15803d !important;
        }
        .btn-outline-success:hover {
            background-color: #16a34a !important;
            border-color: #16a34a !important;
            color: #ffffff !important;
        }
        .btn-outline-success:hover * {
            color: #ffffff !important;
        }

        .btn-action-circle {
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 14px !important;
        }
        /* Mobile Bottom Navigation */
        @media (max-width: 991.98px) {
            .mobile-bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #ffffff;
                border-top: 1px solid #e2e8f0;
                display: flex;
                justify-content: space-around;
                align-items: center;
                padding: 8px 12px;
                z-index: 1040;
                box-shadow: 0 -4px 12px rgba(0,0,0,0.06);
            }
            .mobile-bottom-nav .nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                font-size: 11px;
                color: #64748b;
                text-decoration: none;
                font-weight: 500;
            }
            .mobile-bottom-nav .nav-item i {
                font-size: 20px;
                margin-bottom: 2px;
            }
            .mobile-bottom-nav .nav-item.active {
                color: var(--kreasi-blue);
                font-weight: 600;
            }
            .dashboard-body {
                padding-bottom: 75px !important;
            }
        }
    </style>

    @livewireStyles
    @stack('styles')
</head> 
<body>
    
    <!-- Preloader Start -->
    <div class="preloader">
        <div class="loader"></div>
    </div>
    <!-- Preloader End -->

    <!-- Sidebar Overlay -->
    <div class="side-overlay"></div>

    <!-- ============================ Sidebar Start ============================ -->
    <aside class="sidebar">
        <!-- Close button for mobile -->
        <button type="button" class="sidebar-close-btn text-gray-500 hover-text-white hover-bg-main-600 text-md w-24 h-24 border border-gray-100 hover-border-main-600 d-xl-none d-flex flex-center rounded-circle position-absolute">
            <i class="ph ph-x"></i>
        </button>
        
        <!-- Sidebar Brand Logo -->
        <a href="{{ url('/') }}" class="sidebar__logo text-center p-20 position-sticky inset-block-start-0 bg-white w-100 z-1 pb-10 d-block">
            <img src="{{ asset('assets/images/logo/kreasi-logo.png') }}" alt="KREASI RPL Logo" class="kreasi-logo-img">
        </a>

        <div class="sidebar-menu-wrapper overflow-y-auto scroll-sm">
            <div class="p-20 pt-10">
                <ul class="sidebar-menu">
                    @php
                        $user = Auth::user();
                        $role = $user->role ?? 'siswa';
                        $dashboardRoute = in_array($role, ['siswa', 'ketua_kelompok']) ? 'siswa.dashboard' : $role . '.dashboard';
                    @endphp

                    <!-- Dashboard Link -->
                    <li class="sidebar-menu__item">
                        <a href="{{ route($dashboardRoute) }}" class="sidebar-menu__link {{ request()->routeIs($dashboardRoute) ? 'activePage' : '' }}">
                            <span class="icon"><i class="ph ph-squares-four"></i></span>
                            <span class="text">Dashboard</span>
                        </a>
                    </li>

                    @if($role === 'admin')
                        <!-- ADMIN MENU -->
                        <li class="sidebar-menu__item">
                            <span class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">Manajemen Akademik</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.users.index') }}" class="sidebar-menu__link {{ request()->routeIs('admin.users.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-users"></i></span>
                                <span class="text">Kelola Pengguna</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.classes.index') }}" class="sidebar-menu__link {{ request()->routeIs('admin.classes.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-chalkboard"></i></span>
                                <span class="text">Kelas & Tahun Ajaran</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.students.import') }}" class="sidebar-menu__link {{ request()->routeIs('admin.students.import') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-file-arrow-up"></i></span>
                                <span class="text">Import Siswa Excel</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <span class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">Manajemen PKL & Industri</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.companies.index') }}" class="sidebar-menu__link {{ request()->routeIs('admin.companies.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-buildings"></i></span>
                                <span class="text">Mitra Industri (DU/DI)</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.pkl-groups.index') }}" class="sidebar-menu__link {{ request()->routeIs('admin.pkl-groups.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-users-three"></i></span>
                                <span class="text">Ploting Kelompok PKL</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <span class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">Monitoring & Laporan</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.audit-logs') }}" class="sidebar-menu__link {{ request()->routeIs('admin.audit-logs') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-activity"></i></span>
                                <span class="text">Audit Log & Aktivitas</span>
                            </a>
                        </li>

                    @elseif($role === 'guru')
                        <!-- GURU MENU -->
                        <li class="sidebar-menu__item">
                            <span class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">Project Based Learning</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('guru.projects.index') }}" class="sidebar-menu__link {{ request()->routeIs('guru.projects.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-folder-notch"></i></span>
                                <span class="text">Daftar Proyek PjBL</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('guru.submissions.index') }}" class="sidebar-menu__link {{ request()->routeIs('guru.submissions.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-check-square-offset"></i></span>
                                <span class="text">Review Tugas & Nilai</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <span class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">Bimbingan PKL</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('guru.pkl-groups.index') }}" class="sidebar-menu__link {{ request()->routeIs('guru.pkl-groups.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-users-three"></i></span>
                                <span class="text">Ploting Siswa Bimbingan</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('guru.pkl.index') }}" class="sidebar-menu__link {{ request()->routeIs('guru.pkl.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-briefcase"></i></span>
                                <span class="text">Berkas Laporan PKL</span>
                            </a>
                        </li>

                    @else
                        <!-- SISWA / KETUA KELOMPOK MENU -->
                        <li class="sidebar-menu__item">
                            <span class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">PjBL Siswa</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('siswa.projects.index') }}" class="sidebar-menu__link {{ request()->routeIs('siswa.projects.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-folder-notch-open"></i></span>
                                <span class="text">Proyek Saya</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('siswa.logbook.index') }}" class="sidebar-menu__link {{ request()->routeIs('siswa.logbook.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-notepad"></i></span>
                                <span class="text">Logbook Aktivitas</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <span class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">Laporan PKL</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('siswa.pkl.index') }}" class="sidebar-menu__link {{ request()->routeIs('siswa.pkl.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-file-text"></i></span>
                                <span class="text">Laporan PKL (Bab I-V)</span>
                            </a>
                        </li>
                    @endif

                    <li class="sidebar-menu__item">
                        <span class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">Pengaturan Akun</span>
                    </li>
                    <li class="sidebar-menu__item">
                        <a href="{{ route('profile.edit') }}" class="sidebar-menu__link {{ request()->routeIs('profile.edit') ? 'activePage' : '' }}">
                            <span class="icon"><i class="ph ph-user-circle"></i></span>
                            <span class="text">Profil Saya</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Role Indicator Badge Card -->
            <div class="p-20 pt-20">
                <div class="bg-main-50 p-16 rounded-16 text-center border border-primary-50">
                    <span class="w-48 h-48 rounded-circle bg-white shadow-sm flex-center mx-auto text-main-600 text-24 mb-12">
                        <i class="ph ph-identification-badge"></i>
                    </span>
                    <h6 class="mb-4 text-15 fw-bold text-gray-800">{{ $user->name ?? 'Pengguna' }}</h6>
                    <span class="badge bg-main-600 text-white rounded-pill px-12 py-4 text-12 text-uppercase">
                        {{ str_replace('_', ' ', $role) }}
                    </span>
                </div>
            </div>
        </div>
    </aside>
    <!-- ============================ Sidebar End ============================ -->

    <!-- ============================ Main Wrapper Start ============================ -->
    <div class="dashboard-main-wrapper">
        <!-- Top Navbar -->
        <div class="top-navbar flex-between gap-16">
            <div class="flex-align gap-16">
                <!-- Mobile Toggle Button -->
                <button type="button" class="toggle-btn d-xl-none d-flex text-26 text-gray-500">
                    <i class="ph ph-list"></i>
                </button>
                <div class="d-none d-md-flex align-items-center">
                    <span class="fw-semibold text-gray-700 text-16">KREASI RPL &bull; SMKN Rekayasa Perangkat Lunak</span>
                </div>
            </div>

            <div class="flex-align gap-16">
                <!-- Role Tag -->
                <div class="d-none d-sm-block">
                    <span class="badge bg-main-50 text-main-600 border border-main-200 px-12 py-6 rounded-pill text-13 fw-semibold">
                        <i class="ph ph-user-check me-4"></i> {{ strtoupper(str_replace('_', ' ', $role)) }}
                    </span>
                </div>

                <!-- User Dropdown Menu -->
                <div class="dropdown">
                    <button class="users arrow-down-icon border border-gray-200 rounded-pill p-4 d-inline-block pe-40 position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="position-relative">
                            <img src="{{ $user->avatar_url ?? asset('assets/images/thumbs/user-img.png') }}" alt="{{ $user->name ?? 'User' }}" class="h-32 w-32 rounded-circle object-fit-cover">
                            <span class="activation-badge w-8 h-8 position-absolute inset-block-end-0 inset-inline-end-0"></span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu--lg border-0 bg-transparent p-0">
                        <div class="card border border-gray-100 rounded-12 box-shadow-custom">
                            <div class="card-body">
                                <div class="flex-align gap-12 mb-20 pb-20 border-bottom border-gray-100">
                                    <img src="{{ $user->avatar_url ?? asset('assets/images/thumbs/user-img.png') }}" alt="" class="w-54 h-54 rounded-circle object-fit-cover">
                                    <div>
                                        <h5 class="mb-4 text-16">{{ $user->name ?? 'User' }}</h5>
                                        <p class="fw-medium text-13 text-gray-500 mb-0">{{ $user->email ?? '' }}</p>
                                        <span class="badge bg-main-100 text-main-600 text-11 mt-4">{{ strtoupper($role) }}</span>
                                    </div>
                                </div>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-4">
                                        <a href="{{ route('profile.edit') }}" class="py-10 text-15 px-16 hover-bg-gray-50 text-gray-700 rounded-8 flex-align gap-8 fw-medium">
                                            <span class="text-xl text-primary-600 d-flex"><i class="ph ph-user-circle"></i></span>
                                            <span class="text">Profil Saya</span>
                                        </a>
                                    </li>
                                    <li class="pt-8 border-top border-gray-100">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-100 text-start py-10 text-15 px-16 hover-bg-danger-50 text-gray-700 hover-text-danger-600 rounded-8 flex-align gap-8 fw-medium border-0 bg-transparent">
                                                <span class="text-xl text-danger-600 d-flex"><i class="ph ph-sign-out"></i></span>
                                                <span class="text">Keluar (Logout)</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content Body -->
        <div class="dashboard-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-24 rounded-12" role="alert">
                    <i class="ph ph-check-circle me-8 text-lg"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-24 rounded-12" role="alert">
                    <i class="ph ph-x-circle me-8 text-lg"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Dashboard Footer -->
        <div class="dashboard-footer p-24 text-center border-top border-gray-100 bg-white">
            <p class="text-gray-500 text-14 mb-0">
                &copy; {{ date('Y') }} <strong>KREASI RPL</strong> &bull; Platform PjBL & Bimbingan PKL Siswa Digital. All rights reserved.
            </p>
        </div>
    </div>
    <!-- ============================ Main Wrapper End ============================ -->

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav d-lg-none">
        <a href="{{ route($dashboardRoute) }}" class="nav-item {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">
            <i class="ph ph-squares-four"></i>
            <span>Beranda</span>
        </a>
        @if($role === 'guru')
            <a href="{{ route('guru.projects.index') }}" class="nav-item {{ request()->routeIs('guru.projects.*') ? 'active' : '' }}">
                <i class="ph ph-folder-notch"></i>
                <span>PjBL</span>
            </a>
            <a href="{{ route('guru.pkl.index') }}" class="nav-item {{ request()->routeIs('guru.pkl.*') ? 'active' : '' }}">
                <i class="ph ph-briefcase"></i>
                <span>PKL</span>
            </a>
        @elseif($role === 'admin')
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="ph ph-users"></i>
                <span>Pengguna</span>
            </a>
            <a href="{{ route('admin.classes.index') }}" class="nav-item {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                <i class="ph ph-chalkboard"></i>
                <span>Kelas</span>
            </a>
        @else
            <a href="{{ route('siswa.projects.index') }}" class="nav-item {{ request()->routeIs('siswa.projects.*') ? 'active' : '' }}">
                <i class="ph ph-folder-notch-open"></i>
                <span>PjBL</span>
            </a>
            <a href="{{ route('siswa.pkl.index') }}" class="nav-item {{ request()->routeIs('siswa.pkl.*') ? 'active' : '' }}">
                <i class="ph ph-file-text"></i>
                <span>PKL</span>
            </a>
        @endif
        <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="ph ph-user-circle"></i>
            <span>Profil</span>
        </a>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/boostrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/phosphor-icon.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @livewireScripts
    @stack('scripts')
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(reg) {
                        console.log('KREASI RPL PWA ServiceWorker ready: ', reg.scope);
                    })
                    .catch(function(err) {
                        console.log('KREASI RPL ServiceWorker error: ', err);
                    });
            });
        }
    </script>
</body>
</html>
