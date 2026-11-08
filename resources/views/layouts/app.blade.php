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
    <link rel="stylesheet" href="{{ asset('assets/css/kreasi-custom.css') }}?v={{ filemtime(public_path('assets/css/kreasi-custom.css')) }}">
    
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

        /* ==========================================================================
           MINIMALIST TOAST NOTIFICATION SYSTEM (KREASI MODERN TOAST)
           Ultra-sleek, clean typography, soft shadow, no scrollbar
           ========================================================================== */
        .kreasi-toast-container {
            position: fixed;
            top: 20px;
            right: 24px;
            z-index: 9999999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
            max-width: 420px;
            width: calc(100% - 48px);
        }

        .kreasi-toast-item {
            pointer-events: auto;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 14px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.02);
            position: relative;
            overflow: hidden;
            transform: translateY(-12px) scale(0.96);
            opacity: 0;
            transition: all 0.24s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .kreasi-toast-item.show {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .kreasi-toast-item.hide {
            transform: translateY(-8px) scale(0.96);
            opacity: 0;
            pointer-events: none;
        }

        .kreasi-toast-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 15px;
        }

        .kreasi-toast-item.success .kreasi-toast-icon {
            background-color: #ecfdf5;
            color: #059669;
        }

        .kreasi-toast-item.error .kreasi-toast-icon {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .kreasi-toast-item.warning .kreasi-toast-icon {
            background-color: #fffbeb;
            color: #d97706;
        }

        .kreasi-toast-item.info .kreasi-toast-icon {
            background-color: #eff6ff;
            color: #0284c7;
        }

        .kreasi-toast-message {
            flex-grow: 1;
            font-size: 13.5px;
            font-weight: 500;
            color: #1e293b;
            line-height: 1.45;
            word-break: break-word;
            white-space: normal;
        }

        .kreasi-toast-close {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .kreasi-toast-close:hover {
            color: #334155;
            background-color: #f1f5f9;
        }

        .kreasi-toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            width: 100%;
            background-color: #10b981;
            transform-origin: left center;
        }

        .kreasi-toast-item.error .kreasi-toast-progress { background-color: #ef4444; }
        .kreasi-toast-item.warning .kreasi-toast-progress { background-color: #f59e0b; }
        .kreasi-toast-item.info .kreasi-toast-progress { background-color: #0284c7; }

        @media (max-width: 576px) {
            .kreasi-toast-container {
                top: 16px;
                left: 16px;
                right: 16px;
                width: auto;
                max-width: 100%;
            }
        }

        /* Fallback override for any SweetAlert2 toast (NO horizontal scrollbar) */
        .swal2-toast {
            width: auto !important;
            max-width: 440px !important;
            padding: 12px 16px !important;
            border-radius: 14px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08) !important;
            overflow: hidden !important;
        }
        .swal2-toast .swal2-title {
            white-space: normal !important;
            word-break: break-word !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            line-height: 1.45 !important;
            padding: 0 4px !important;
        }
        .swal2-toast .swal2-icon {
            width: 26px !important;
            height: 26px !important;
            min-width: 26px !important;
            margin: 0 !important;
            font-size: 12px !important;
        }
        .swal2-toast .swal2-timer-progress-bar {
            height: 2px !important;
            background: #10b981 !important;
        }
        /* Card Cute Color Text Utilities for High Contrast */
        .text-sky-700 { color: #0284c7 !important; }
        .text-sky-800 { color: #0369a1 !important; }
        .text-sky-900 { color: #0c4a6e !important; }
        .text-orange-600 { color: #ea580c !important; }
        .text-orange-700 { color: #c2410c !important; }
        .text-orange-800 { color: #9a3412 !important; }

        /* Force badges inside candy cards to always have strong contrast */
        .candy-sky .badge.bg-white { color: #0369a1 !important; }
        .candy-peach .badge.bg-white { color: #c2410c !important; }
        .candy-mint .badge.bg-white { color: #15803d !important; }
        .candy-berry .badge.bg-white { color: #6b21a8 !important; }

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
                padding-bottom: 100px !important;
            }
            .dashboard-footer {
                margin-bottom: 75px !important;
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
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-20 rounded-16 border-0 shadow-xs bg-danger-50 text-danger-900 d-flex align-items-center gap-12 py-14 px-20" role="alert">
                    <span class="w-32 h-32 rounded-circle bg-danger text-white flex-center text-18 shrink-0 shadow-xs">
                        <i class="ph ph-warning-circle"></i>
                    </span>
                    <div class="text-14 fw-medium flex-grow-1">
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
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
        // Override native window.alert with SweetAlert2
        window.alert = function(message) {
            Swal.fire({
                title: 'Perhatian',
                text: message,
                icon: 'info',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#064A8E',
                customClass: {
                    popup: 'rounded-24 p-24 shadow-xl border border-gray-100',
                    title: 'text-18 fw-bold text-gray-900 mb-8',
                    htmlContainer: 'text-14 text-gray-600 mb-20',
                    confirmButton: 'btn btn-main rounded-pill px-24 py-10 fw-semibold'
                }
            });
        };

        // Minimalist Kreasi Toast System (Zero scrollbars, sleek typography, micro-progress)
        window.KreasiToast = (function() {
            let container = null;

            function getContainer() {
                if (!container || !document.body.contains(container)) {
                    container = document.createElement('div');
                    container.className = 'kreasi-toast-container';
                    document.body.appendChild(container);
                }
                return container;
            }

            const icons = {
                success: 'ph-bold ph-check',
                error: 'ph-bold ph-x',
                warning: 'ph-bold ph-warning',
                info: 'ph-bold ph-info'
            };

            function show(message, type = 'success', duration = 3800) {
                if (!message) return;
                const cont = getContainer();

                const toast = document.createElement('div');
                toast.className = `kreasi-toast-item ${type}`;

                const iconClass = icons[type] || icons.info;
                toast.innerHTML = `
                    <div class="kreasi-toast-icon">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="kreasi-toast-message">${message}</div>
                    <button type="button" class="kreasi-toast-close" title="Tutup">
                        <i class="ph ph-x"></i>
                    </button>
                    <div class="kreasi-toast-progress"></div>
                `;

                cont.appendChild(toast);

                // Animate entrance
                requestAnimationFrame(() => {
                    toast.classList.add('show');
                });

                const progressBar = toast.querySelector('.kreasi-toast-progress');
                let remaining = duration;
                let startTime = Date.now();
                let timer = null;

                function startProgress() {
                    progressBar.style.transition = `transform ${remaining}ms linear`;
                    progressBar.style.transform = 'scaleX(0)';
                    timer = setTimeout(dismiss, remaining);
                }

                function pauseProgress() {
                    clearTimeout(timer);
                    const elapsed = Date.now() - startTime;
                    remaining = Math.max(0, remaining - elapsed);
                    const currentScale = remaining / duration;
                    progressBar.style.transition = 'none';
                    progressBar.style.transform = `scaleX(${currentScale})`;
                }

                function resumeProgress() {
                    startTime = Date.now();
                    startProgress();
                }

                function dismiss() {
                    clearTimeout(timer);
                    toast.classList.remove('show');
                    toast.classList.add('hide');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 250);
                }

                toast.addEventListener('mouseenter', pauseProgress);
                toast.addEventListener('mouseleave', resumeProgress);
                toast.querySelector('.kreasi-toast-close').addEventListener('click', dismiss);

                startProgress();

                return { dismiss };
            }

            return {
                show: show,
                success: (msg, d) => show(msg, 'success', d),
                error: (msg, d) => show(msg, 'error', d),
                warning: (msg, d) => show(msg, 'warning', d),
                info: (msg, d) => show(msg, 'info', d),
                fire: function(opts) {
                    if (typeof opts === 'string') return show(opts, 'success');
                    const type = opts.icon || opts.type || 'success';
                    const msg = opts.title || opts.text || opts.message || '';
                    const dur = opts.timer || 3800;
                    return show(msg, type, dur);
                }
            };
        })();

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success') || session('swal_success'))
                window.KreasiToast.success('{{ addslashes(session('success') ?? session('swal_success')) }}');
            @endif

            @if(session('error') || session('swal_error'))
                window.KreasiToast.error('{{ addslashes(session('error') ?? session('swal_error')) }}');
            @endif

            @if(session('status'))
                window.KreasiToast.info('{{ addslashes(session('status')) }}');
            @endif

            // Automatically upgrade all forms with onsubmit containing confirm(...) to data-confirm
            function upgradeConfirmForms() {
                document.querySelectorAll('form').forEach(form => {
                    const onsubmitAttr = form.getAttribute('onsubmit');
                    if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                        const match = onsubmitAttr.match(/confirm\(\s*['"`](.*?)['"`]\s*\)/s);
                        const message = match ? match[1] : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                        form.removeAttribute('onsubmit');
                        form.setAttribute('data-confirm', message);

                        const isDelete = form.querySelector('input[name="_method"][value="DELETE"]') || form.action.includes('destroy') || form.action.includes('delete') || message.toLowerCase().includes('hapus');
                        if (isDelete) {
                            form.setAttribute('data-confirm-title', 'Konfirmasi Hapus');
                            form.setAttribute('data-confirm-icon', 'warning');
                            form.setAttribute('data-confirm-btn', 'Ya, Hapus!');
                        }
                    }
                });
            }
            upgradeConfirmForms();

            // Intercept submit on forms that require confirmation
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (!form || !form.hasAttribute('data-confirm') || form.dataset.confirmed === 'true') {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                const message = form.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                const icon = form.getAttribute('data-confirm-icon') || 'warning';
                const confirmBtnText = form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
                const isDelete = form.querySelector('input[name="_method"][value="DELETE"]') || form.action.includes('destroy') || form.action.includes('delete') || confirmBtnText.toLowerCase().includes('hapus') || icon === 'warning';

                Swal.fire({
                    title: title,
                    text: message,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: isDelete ? '#dc2626' : '#064A8E',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="ph ph-check me-2"></i> ' + confirmBtnText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        popup: 'rounded-24 p-24 shadow-2xl border border-gray-100',
                        title: 'text-18 fw-bold text-gray-900 mb-8',
                        htmlContainer: 'text-14 text-gray-600 mb-20',
                        confirmButton: (isDelete ? 'btn btn-danger' : 'btn btn-main') + ' rounded-pill px-20 py-10 fw-semibold shadow-xs',
                        cancelButton: 'btn btn-outline-secondary rounded-pill px-18 py-10 fw-medium me-8'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            }, true);

            // Intercept clicks on links or non-form elements with data-confirm
            document.addEventListener('click', function(e) {
                const target = e.target.closest('[data-confirm]');
                if (!target || target.tagName === 'FORM') return;

                const parentForm = target.closest('form');
                if (parentForm && target.type === 'submit') {
                    // Let form submit event handle it
                    if (!parentForm.hasAttribute('data-confirm')) {
                        parentForm.setAttribute('data-confirm', target.getAttribute('data-confirm'));
                        if (target.hasAttribute('data-confirm-title')) parentForm.setAttribute('data-confirm-title', target.getAttribute('data-confirm-title'));
                        if (target.hasAttribute('data-confirm-btn')) parentForm.setAttribute('data-confirm-btn', target.getAttribute('data-confirm-btn'));
                        if (target.hasAttribute('data-confirm-icon')) parentForm.setAttribute('data-confirm-icon', target.getAttribute('data-confirm-icon'));
                    }
                    return;
                }

                if (target.tagName === 'A' && target.href && target.href !== '#' && target.dataset.confirmed !== 'true') {
                    e.preventDefault();
                    const message = target.getAttribute('data-confirm');
                    const title = target.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                    const icon = target.getAttribute('data-confirm-icon') || 'question';
                    const confirmBtnText = target.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';

                    Swal.fire({
                        title: title,
                        text: message,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: '#064A8E',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: confirmBtnText,
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-24 p-24 shadow-2xl border border-gray-100',
                            title: 'text-18 fw-bold text-gray-900 mb-8',
                            htmlContainer: 'text-14 text-gray-600 mb-20',
                            confirmButton: 'btn btn-main rounded-pill px-20 py-10 fw-semibold',
                            cancelButton: 'btn btn-outline-secondary rounded-pill px-18 py-10 fw-medium me-8'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            target.dataset.confirmed = 'true';
                            window.location.href = target.href;
                        }
                    });
                }
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
