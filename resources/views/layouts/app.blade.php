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
    <link rel="apple-touch-icon" href="{{ asset('assets/images/thumbs/panda.png') }}">
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/thumbs/panda.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/thumbs/panda.png') }}">
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
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}?v=1.0.1">
    <!-- KREASI RPL Custom Branding -->
    <link rel="stylesheet" href="{{ asset('assets/css/kreasi-custom.css') }}?v=2.2.0">
    
    <style>
        /* ========================================================
           MINIMALIST & CLEAN MODERN SIDEBAR
           ======================================================== */
        .sidebar {
            background-color: #ffffff !important;
            border-right: 1px solid #edf2f7 !important;
            box-shadow: none !important;
        }
        .sidebar__logo {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            padding: 16px 20px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            background: #ffffff !important;
        }
        .kreasi-logo-img {
            max-height: 36px !important;
            width: auto !important;
            object-fit: contain !important;
        }
        .sidebar-menu-wrapper {
            padding: 8px 6px 24px 6px !important;
        }
        .sidebar-menu {
            display: flex !important;
            flex-direction: column !important;
            gap: 2px !important;
            padding: 0 !important;
            margin: 0 !important;
            list-style: none !important;
        }
        .sidebar-category-title {
            display: block !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            letter-spacing: 0.08em !important;
            text-transform: uppercase !important;
            color: #94a3b8 !important;
            padding: 14px 12px 4px 12px !important;
            margin: 0 !important;
            user-select: none !important;
        }
        .sidebar-menu__link {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
            color: #475569 !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            border: none !important;
            background: transparent !important;
            margin-bottom: 1px !important;
            transition: all 0.15s ease !important;
        }
        .sidebar-menu__link .icon {
            font-size: 19px !important;
            line-height: 1 !important;
            width: 20px !important;
            height: 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #94a3b8 !important;
            flex-shrink: 0 !important;
            transition: color 0.15s ease !important;
        }
        .sidebar-menu__link .text {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            display: inline-block !important;
        }
        .sidebar-menu__link:hover {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }
        .sidebar-menu__link:hover .icon {
            color: #064A8E !important;
        }
        .sidebar-menu__link.activePage {
            background-color: #eff6ff !important;
            color: #064A8E !important;
            font-weight: 600 !important;
            border: none !important;
        }
        .sidebar-menu__link.activePage .icon {
            color: #064A8E !important;
            font-weight: 600 !important;
        }

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
        <a href="{{ url('/') }}" class="sidebar__logo d-flex align-items-center px-20 py-18 position-sticky inset-block-start-0 bg-white w-100 z-1 border-bottom border-gray-100">
            <img src="{{ asset('assets/images/logo/kreasi-logo.png') }}" alt="KREASI RPL Logo" class="kreasi-logo-img">
        </a>

        <div class="sidebar-menu-wrapper overflow-y-auto scroll-sm">
            <div class="p-12 pt-8">
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
                        <li class="sidebar-menu__category">
                            <span class="sidebar-category-title">Akademik</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.users.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('admin.users.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-users"></i></span>
                                <span class="text text-nowrap">Kelola Pengguna</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.classes.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('admin.classes.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-chalkboard"></i></span>
                                <span class="text text-nowrap">Kelas & TA</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.students.sync') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('admin.students.sync*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-arrows-clockwise"></i></span>
                                <span class="text text-nowrap">Sinkronkan API</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.students.import') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('admin.students.import') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-file-arrow-up"></i></span>
                                <span class="text text-nowrap">Import Siswa</span>
                            </a>
                        </li>

                        <li class="sidebar-menu__category">
                            <span class="sidebar-category-title">PKL & Industri</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.companies.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('admin.companies.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-buildings"></i></span>
                                <span class="text text-nowrap">Mitra Industri</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.pkl-groups.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('admin.pkl-groups.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-users-three"></i></span>
                                <span class="text text-nowrap">Kelompok PKL</span>
                            </a>
                        </li>

                        <li class="sidebar-menu__category">
                            <span class="sidebar-category-title">Monitoring</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.traffic.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('admin.traffic.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-chart-line-up"></i></span>
                                <span class="text text-nowrap">Trafik & Storage</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.student-progress.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('admin.student-progress.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-folder-open"></i></span>
                                <span class="text text-nowrap">Progres & File Siswa</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('admin.audit-logs') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('admin.audit-logs') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-shield-check"></i></span>
                                <span class="text text-nowrap">Audit Log</span>
                            </a>
                        </li>

                    @elseif($role === 'guru')
                        <!-- GURU MENU -->
                        <li class="sidebar-menu__category">
                            <span class="sidebar-category-title">Pembelajaran PjBL</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('guru.projects.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('guru.projects.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-folder-notch"></i></span>
                                <span class="text text-nowrap">Proyek PjBL</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('guru.submissions.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('guru.submissions.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-check-square-offset"></i></span>
                                <span class="text text-nowrap">Review Tugas</span>
                            </a>
                        </li>

                        <li class="sidebar-menu__category">
                            <span class="sidebar-category-title">Bimbingan PKL</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('guru.pkl-groups.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('guru.pkl-groups.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-users-three"></i></span>
                                <span class="text text-nowrap">Kelompok PKL</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('guru.pkl.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('guru.pkl.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-briefcase"></i></span>
                                <span class="text text-nowrap">Laporan PKL</span>
                            </a>
                        </li>

                    @else
                        <!-- SISWA / KETUA KELOMPOK MENU -->
                        <li class="sidebar-menu__category">
                            <span class="sidebar-category-title">Pembelajaran PjBL</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('siswa.projects.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('siswa.projects.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-folder-notch-open"></i></span>
                                <span class="text text-nowrap">Proyek Saya</span>
                            </a>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('siswa.logbook.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('siswa.logbook.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-notepad"></i></span>
                                <span class="text text-nowrap">Logbook Harian</span>
                            </a>
                        </li>

                        <li class="sidebar-menu__category">
                            <span class="sidebar-category-title">Praktik Kerja Lapangan</span>
                        </li>
                        <li class="sidebar-menu__item">
                            <a href="{{ route('siswa.pkl.index') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('siswa.pkl.*') ? 'activePage' : '' }}">
                                <span class="icon"><i class="ph ph-file-text"></i></span>
                                <span class="text text-nowrap">Laporan PKL</span>
                            </a>
                        </li>
                    @endif

                    <li class="sidebar-menu__category">
                        <span class="sidebar-category-title">Pengaturan</span>
                    </li>
                    <li class="sidebar-menu__item">
                        <a href="{{ route('profile.edit') }}" class="sidebar-menu__link text-nowrap {{ request()->routeIs('profile.edit') ? 'activePage' : '' }}">
                            <span class="icon"><i class="ph ph-user-circle"></i></span>
                            <span class="text text-nowrap">Profil Saya</span>
                        </a>
                    </li>
                </ul>
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
                <div class="d-none d-md-flex align-items-center gap-12">
                    <span class="fw-bold text-gray-800 text-16 flex-align gap-8">
                        <img src="{{ asset('assets/images/thumbs/panda.png') }}" alt="" class="w-24 h-24 bounce-cute d-inline-block">
                        <span>KREASI RPL</span>
                        <span class="text-gray-400 fw-normal">&bull; SMKN Rekayasa Perangkat Lunak</span>
                    </span>
                </div>
            </div>

            <div class="flex-align gap-16">
                <!-- Date Pill -->
                <div class="d-none d-lg-flex align-items-center gap-6 bg-gray-50 border border-gray-100 rounded-pill px-14 py-6 text-12 text-gray-600">
                    <i class="ph ph-calendar-blank text-main-600 text-16"></i>
                    <span class="fw-medium">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>

                <!-- Role Tag -->
                <div class="d-none d-sm-block">
                    @if($role === 'admin')
                        <span class="badge bg-main-50 text-main-700 border border-main-200 px-12 py-6 rounded-pill text-12 fw-semibold">
                            👑 Administrator
                        </span>
                    @elseif($role === 'guru')
                        <span class="badge bg-success-50 text-success-700 border border-success-200 px-12 py-6 rounded-pill text-12 fw-semibold">
                            👨‍🏫 Guru Pembimbing
                        </span>
                    @elseif($role === 'ketua_kelompok')
                        <span class="badge bg-warning-50 text-warning-700 border border-warning-200 px-12 py-6 rounded-pill text-12 fw-semibold">
                            ⭐ Ketua Kelompok
                        </span>
                    @else
                        <span class="badge bg-primary-50 text-primary-700 border border-primary-200 px-12 py-6 rounded-pill text-12 fw-semibold">
                            🚀 Siswa RPL
                        </span>
                    @endif
                </div>

                <!-- User Dropdown Menu -->
                <div class="dropdown">
                    <button class="users arrow-down-icon border border-gray-200 rounded-pill p-4 d-inline-block pe-40 position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="position-relative">
                            <img src="{{ $user->avatar_url ?? asset('assets/images/thumbs/user-img.png') }}" alt="{{ $user->name ?? 'User' }}" class="h-32 w-32 rounded-circle object-fit-cover">
                            <span class="activation-badge w-8 h-8 position-absolute inset-block-end-0 inset-inline-end-0 pulse-badge"></span>
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
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>

    <!-- SweetAlert2 Global Confirm & Toast Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reusable Toast
            window.KreasiToast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            @if(session('swal_success'))
                window.KreasiToast.fire({
                    icon: 'success',
                    title: '{{ addslashes(session('swal_success')) }}'
                });
            @endif

            @if(session('swal_error'))
                window.KreasiToast.fire({
                    icon: 'error',
                    title: '{{ addslashes(session('swal_error')) }}'
                });
            @endif

            // Intercept [data-confirm] buttons / forms
            document.addEventListener('click', function(e) {
                const target = e.target.closest('[data-confirm]');
                if (!target) return;

                e.preventDefault();
                const message = target.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                const title = target.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                const icon = target.getAttribute('data-confirm-icon') || 'warning';
                const confirmBtnText = target.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
                const isDangerous = target.classList.contains('btn-danger') || target.classList.contains('btn-outline-danger') || icon === 'warning';

                Swal.fire({
                    title: title,
                    text: message,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonText: confirmBtnText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-16 shadow-lg border border-gray-100 p-24',
                        title: 'text-18 fw-bold text-gray-800 mb-8',
                        htmlContainer: 'text-14 text-gray-600 mb-20',
                        confirmButton: (isDangerous ? 'btn btn-danger' : 'btn btn-main') + ' rounded-pill px-24 py-10 fw-semibold',
                        cancelButton: 'btn btn-outline-secondary rounded-pill px-20 py-10 fw-medium me-12',
                        actions: 'gap-8'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = target.closest('form');
                        if (form) {
                            form.submit();
                        } else if (target.tagName === 'A' && target.href && target.href !== '#') {
                            window.location.href = target.href;
                        }
                    }
                });
            });
        });
    </script>
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
