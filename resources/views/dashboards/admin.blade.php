@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Hero Welcome Banner (Clean, Jelas, Rapi & Elegan) -->
<div class="mb-24" style="margin-bottom: 24px !important;">
    <div class="card border-0 rounded-20 position-relative overflow-hidden p-24 text-white" 
         style="background: linear-gradient(135deg, #064A8E 0%, #0861B5 60%, #13B8B0 100%) !important; box-shadow: 0 10px 25px -5px rgba(6, 74, 142, 0.22);">
        
        <!-- Pattern Overlay -->
        <img src="{{ asset('assets/images/bg/grettings-pattern.png') }}" alt="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover pointer-events-none" style="opacity: 0.15; mix-blend-mode: overlay;">
        
        <div class="row align-items-center g-20 position-relative z-1">
            <div class="col-lg-8">
                <!-- Greeting Badge -->
                <div class="d-inline-flex align-items-center gap-8 rounded-pill px-14 py-6 text-12 fw-semibold mb-12 shadow-xs" 
                     style="background: rgba(255, 255, 255, 0.2); color: #ffffff; backdrop-filter: blur(4px);">
                    <span class="text-16">👋</span>
                    <span>Halo, {{ Auth::user()->name }}!</span>
                    <span class="badge rounded-pill px-8 py-2 text-11 fw-bold" style="background: #FFB703; color: #0f172a;">
                        👑 Administrator
                    </span>
                </div>

                <!-- Heading & Subtitle -->
                <h3 class="text-white fw-bold mb-8 text-24">
                    Dashboard Administrator &bull; Pusat Komando KREASI RPL ✨
                </h3>
                <p class="text-white text-14 mb-20" style="opacity: 0.9; max-width: 560px; line-height: 1.55;">
                    Pantau aktivitas pembelajaran PjBL, bimbingan laporan PKL siswa kelas XII, dan kelola integritas file sistem secara realtime dalam satu pusat kendali terpadu.
                </p>
                
                <!-- Quick Action Buttons -->
                <div class="d-flex flex-wrap gap-10">
                    <a href="{{ route('admin.traffic.index') }}" 
                       class="btn rounded-pill px-16 py-8 text-13 fw-bold flex-align gap-6 shadow-sm"
                       style="background-color: #ffffff !important; color: #064A8E !important; border: 0 !important;">
                        <i class="ph ph-chart-line-up text-16" style="color: #064A8E !important;"></i>
                        <span style="color: #064A8E !important;">Trafik & Storage</span>
                    </a>
                    <a href="{{ route('admin.student-progress.index') }}" 
                       class="btn rounded-pill px-16 py-8 text-13 fw-semibold flex-align gap-6"
                       style="background: rgba(255, 255, 255, 0.18) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                        <i class="ph ph-folder-open text-16 text-white"></i>
                        <span class="text-white">Berkas Siswa</span>
                    </a>
                    <a href="{{ route('admin.students.sync') }}" 
                       class="btn rounded-pill px-16 py-8 text-13 fw-semibold flex-align gap-6"
                       style="background: rgba(255, 255, 255, 0.18) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                        <i class="ph ph-arrows-clockwise text-16 text-white"></i>
                        <span class="text-white">Sinkron API</span>
                    </a>
                    <a href="{{ route('admin.students.import') }}" 
                       class="btn rounded-pill px-16 py-8 text-13 fw-semibold flex-align gap-6"
                       style="background: rgba(255, 255, 255, 0.18) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                        <i class="ph ph-file-arrow-up text-16 text-white"></i>
                        <span class="text-white">Import Siswa</span>
                    </a>
                </div>
            </div>

            <!-- Mascot Image (Proportional & Cute) -->
            <div class="col-lg-4 text-center d-none d-lg-flex justify-content-center align-items-center position-relative">
                <div class="position-relative d-inline-block">
                    <img src="{{ asset('assets/images/thumbs/panda.png') }}" 
                         alt="Maskot KREASI" 
                         style="width: 145px; height: 145px; object-fit: contain; filter: drop-shadow(0 8px 16px rgba(0,0,0,0.18));">
                    <div class="bg-white rounded-pill px-12 py-4 shadow-sm position-absolute top-0 end-0 translate-middle-y text-11 fw-bold text-gray-800 flex-align gap-4" style="border: 1px solid #e2e8f0;">
                        <span>Semangat RPL! 🚀</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4 Clean & Attractive Stat Cards -->
<div class="mb-24" style="margin-bottom: 24px !important;">
    <div class="row g-4 gy-4" style="row-gap: 24px;">
        <!-- Card 1: Total Siswa -->
        <div class="col-xxl-3 col-xl-3 col-sm-6 mb-16 mb-xl-0">
            <div class="card border border-gray-100 rounded-16 p-20 bg-white h-100 shadow-none hover-shadow-sm transition-2">
                <div class="flex-between align-items-center mb-12">
                    <span class="text-gray-500 text-13 fw-semibold">Total Siswa Terdaftar</span>
                    <div class="w-44 h-44 rounded-12 bg-main-50 text-main-600 flex-center text-22 flex-shrink-0">
                        <i class="ph-fill ph-student"></i>
                    </div>
                </div>
                <h3 class="mb-8 fw-bold text-gray-900 text-28">{{ number_format($totalStudents) }}</h3>
                <div class="flex-align gap-6 text-12">
                    <span class="badge bg-main-50 text-main-700 border border-main-200 px-8 py-3 rounded-pill fw-semibold">
                        <i class="ph ph-check-circle me-2"></i> Aktif Belajar
                    </span>
                    <span class="text-gray-600 fw-medium">Jurusan RPL</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Guru -->
        <div class="col-xxl-3 col-xl-3 col-sm-6 mb-16 mb-xl-0">
            <div class="card border border-gray-100 rounded-16 p-20 bg-white h-100 shadow-none hover-shadow-sm transition-2">
                <div class="flex-between align-items-center mb-12">
                    <span class="text-gray-500 text-13 fw-semibold">Guru Pembimbing</span>
                    <div class="w-44 h-44 rounded-12 bg-success-50 text-success-600 flex-center text-22 flex-shrink-0">
                        <i class="ph-fill ph-chalkboard-teacher"></i>
                    </div>
                </div>
                <h3 class="mb-8 fw-bold text-gray-900 text-28">{{ number_format($totalTeachers) }}</h3>
                <div class="flex-align gap-6 text-12">
                    <span class="badge bg-success-50 text-success-700 border border-success-200 px-8 py-3 rounded-pill fw-semibold">
                        <i class="ph ph-check-circle me-2"></i> Terverifikasi
                    </span>
                    <span class="text-gray-600 fw-medium">PjBL & PKL</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Proyek -->
        <div class="col-xxl-3 col-xl-3 col-sm-6 mb-16 mb-xl-0">
            <div class="card border border-gray-100 rounded-16 p-20 bg-white h-100 shadow-none hover-shadow-sm transition-2">
                <div class="flex-between align-items-center mb-12">
                    <span class="text-gray-500 text-13 fw-semibold">Proyek PjBL Aktif</span>
                    <div class="w-44 h-44 rounded-12 bg-warning-50 text-warning-600 flex-center text-22 flex-shrink-0">
                        <i class="ph-fill ph-rocket-launch"></i>
                    </div>
                </div>
                <h3 class="mb-8 fw-bold text-gray-900 text-28">{{ number_format($totalProjects) }}</h3>
                <div class="flex-align gap-6 text-12">
                    <span class="badge bg-warning-50 text-warning-800 border border-warning-200 px-8 py-3 rounded-pill fw-semibold">
                        <i class="ph ph-lightning me-2"></i> Project Based
                    </span>
                    <span class="text-gray-600 fw-medium">Karya Siswa</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Kelas / Rombel -->
        <div class="col-xxl-3 col-xl-3 col-sm-6 mb-16 mb-xl-0">
            <div class="card border border-gray-100 rounded-16 p-20 bg-white h-100 shadow-none hover-shadow-sm transition-2">
                <div class="flex-between align-items-center mb-12">
                    <span class="text-gray-500 text-13 fw-semibold">Rombongan Belajar</span>
                    <div class="w-44 h-44 rounded-12 bg-purple-50 text-purple-600 flex-center text-22 flex-shrink-0">
                        <i class="ph-fill ph-chalkboard"></i>
                    </div>
                </div>
                <h3 class="mb-8 fw-bold text-gray-900 text-28">{{ number_format($totalClasses) }}</h3>
                <div class="flex-align gap-6 text-12">
                    <span class="badge bg-purple-50 text-purple-700 border border-purple-200 px-8 py-3 rounded-pill fw-semibold">
                        <i class="ph ph-graduation-cap me-2"></i> Tingkat X, XI, XII
                    </span>
                    <span class="text-gray-600 fw-medium">Kelas RPL</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PKL Statistics & Audit Logs Section -->
<div class="row g-4 gy-4" style="row-gap: 24px;">
    <!-- PKL Statistics Breakdown -->
    <div class="col-lg-6 mb-24 mb-lg-0">
        <div class="card border border-gray-100 rounded-16 bg-white p-24 h-100 shadow-none">
            <div class="flex-between flex-wrap gap-12 mb-20">
                <div>
                    <h5 class="mb-4 text-16 fw-bold text-gray-900 flex-align gap-8">
                        <span class="w-32 h-32 rounded-10 bg-main-50 text-main-600 flex-center text-18">
                            <i class="ph ph-briefcase"></i>
                        </span>
                        <span>Bimbingan PKL Kelas XII</span>
                    </h5>
                    <p class="text-gray-400 text-13 mb-0">Status pengajuan dan progres penulisan bab laporan PKL.</p>
                </div>
                <span class="badge bg-main-50 text-main-700 border border-main-200 px-12 py-6 rounded-pill text-12 fw-bold">
                    Total: {{ $pklStats['total'] }} Laporan 📑
                </span>
            </div>

            <div class="row g-16 gy-16" style="row-gap: 16px;">
                <!-- Approved -->
                <div class="col-sm-6 mb-16" style="margin-bottom: 16px !important;">
                    <div class="p-16 rounded-14 flex-between align-items-center h-100 shadow-2xs" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                        <div>
                            <span class="text-12 fw-semibold d-block" style="color: #166534;">Disetujui (Approved)</span>
                            <h3 class="mb-0 fw-bold mt-4 text-22" style="color: #15803d;">{{ $pklStats['approved'] }}</h3>
                            <span class="text-11 d-block mt-2" style="color: #16a34a;">Tervalidasi pembimbing</span>
                        </div>
                        <div class="w-40 h-40 rounded-circle flex-center text-20 shadow-xs" style="background: #ffffff; color: #16a34a;">
                            <i class="ph ph-check-circle"></i>
                        </div>
                    </div>
                </div>

                <!-- Submitted -->
                <div class="col-sm-6 mb-16" style="margin-bottom: 16px !important;">
                    <div class="p-16 rounded-14 flex-between align-items-center h-100 shadow-2xs" style="background-color: #f0f9ff; border: 1px solid #bae6fd;">
                        <div>
                            <span class="text-12 fw-semibold d-block" style="color: #075985;">Diajukan (Submitted)</span>
                            <h3 class="mb-0 fw-bold mt-4 text-22" style="color: #0284c7;">{{ $pklStats['submitted'] }}</h3>
                            <span class="text-11 d-block mt-2" style="color: #0284c7;">Menunggu review guru</span>
                        </div>
                        <div class="w-40 h-40 rounded-circle flex-center text-20 shadow-xs" style="background: #ffffff; color: #0284c7;">
                            <i class="ph ph-paper-plane-tilt"></i>
                        </div>
                    </div>
                </div>

                <!-- Revision -->
                <div class="col-sm-6 mb-16 mb-sm-0">
                    <div class="p-16 rounded-14 flex-between align-items-center h-100 shadow-2xs" style="background-color: #fff7ed; border: 1px solid #fed7aa;">
                        <div>
                            <span class="text-12 fw-semibold d-block" style="color: #9a3412;">Perlu Revisi</span>
                            <h3 class="mb-0 fw-bold mt-4 text-22" style="color: #ea580c;">{{ $pklStats['revision_required'] }}</h3>
                            <span class="text-11 d-block mt-2" style="color: #ea580c;">Catatan perbaikan aktif</span>
                        </div>
                        <div class="w-40 h-40 rounded-circle flex-center text-20 shadow-xs" style="background: #ffffff; color: #ea580c;">
                            <i class="ph ph-arrow-counter-clockwise"></i>
                        </div>
                    </div>
                </div>

                <!-- Draft -->
                <div class="col-sm-6">
                    <div class="p-16 rounded-14 flex-between align-items-center h-100 shadow-2xs" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <div>
                            <span class="text-12 fw-semibold d-block" style="color: #475569;">Draf Penyusunan</span>
                            <h3 class="mb-0 fw-bold mt-4 text-22" style="color: #334155;">{{ $pklStats['draft'] }}</h3>
                            <span class="text-11 d-block mt-2" style="color: #64748b;">Sedang disusun siswa</span>
                        </div>
                        <div class="w-40 h-40 rounded-circle flex-center text-20 shadow-xs" style="background: #ffffff; color: #64748b;">
                            <i class="ph ph-file-dashed"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-20 pt-16 border-top border-gray-100 flex-between">
                <span class="text-gray-500 text-13">Ingin melihat capaian per bab setiap siswa?</span>
                <a href="{{ route('admin.student-progress.matrix') }}" class="text-main-600 fw-bold text-13 flex-align gap-4 hover-text-main-700">
                    <span>Lihat Matriks Progres</span>
                    <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Audit Logs Section -->
    <div class="col-lg-6">
        <div class="card border border-gray-100 rounded-16 bg-white p-24 h-100 shadow-none">
            <div class="flex-between flex-wrap gap-12 mb-20">
                <div>
                    <h5 class="mb-4 text-16 fw-bold text-gray-900 flex-align gap-8">
                        <span class="w-32 h-32 rounded-10 bg-purple-50 text-purple-600 flex-center text-18">
                            <i class="ph ph-shield-check"></i>
                        </span>
                        <span>Aktivitas Sistem Terkini</span>
                    </h5>
                    <p class="text-gray-400 text-13 mb-0">Riwayat audit log dan interaksi pengguna terbaru.</p>
                </div>
                <a href="{{ route('admin.audit-logs') }}" class="badge bg-gray-50 text-gray-600 border border-gray-200 px-12 py-6 rounded-pill text-12 fw-semibold hover-bg-gray-100 transition-2">
                    Semua Log &rarr;
                </a>
            </div>

            <div class="max-h-350 overflow-y-auto scroll-sm pe-4">
                @forelse($recentActivities as $log)
                    @php
                        $act = strtoupper($log->action);
                        $badgeBg = '#f1f5f9';
                        $badgeColor = '#475569';
                        $badgeBorder = '#cbd5e1';
                        $icon = 'ph-activity';
                        $iconBg = '#f1f5f9';
                        $iconColor = '#475569';

                        if (str_contains($act, 'LOGIN')) {
                            $badgeBg = '#ecfdf5';
                            $badgeColor = '#047857';
                            $badgeBorder = '#a7f3d0';
                            $icon = 'ph-sign-in';
                            $iconBg = '#ecfdf5';
                            $iconColor = '#059669';
                        } elseif (str_contains($act, 'LOGOUT')) {
                            $badgeBg = '#f1f5f9';
                            $badgeColor = '#334155';
                            $badgeBorder = '#cbd5e1';
                            $icon = 'ph-sign-out';
                            $iconBg = '#f1f5f9';
                            $iconColor = '#475569';
                        } elseif (str_contains($act, 'DELETE') || str_contains($act, 'CLEAR') || str_contains($act, 'HAPUS')) {
                            $badgeBg = '#fef2f2';
                            $badgeColor = '#b91c1c';
                            $badgeBorder = '#fecaca';
                            $icon = 'ph-trash';
                            $iconBg = '#fef2f2';
                            $iconColor = '#dc2626';
                        } elseif (str_contains($act, 'UPLOAD') || str_contains($act, 'SYNC')) {
                            $badgeBg = '#eff6ff';
                            $badgeColor = '#0369a1';
                            $badgeBorder = '#bae6fd';
                            $icon = 'ph-cloud-arrow-up';
                            $iconBg = '#eff6ff';
                            $iconColor = '#0284c7';
                        } elseif (str_contains($act, 'UPDATE') || str_contains($act, 'EDIT') || str_contains($act, 'REVIEW')) {
                            $badgeBg = '#fff7ed';
                            $badgeColor = '#c2410c';
                            $badgeBorder = '#fed7aa';
                            $icon = 'ph-pencil-simple';
                            $iconBg = '#fff7ed';
                            $iconColor = '#ea580c';
                        }
                    @endphp
                    <div class="d-flex align-items-start gap-12 pb-14 mb-14 border-bottom border-gray-50 hover-bg-gray-50 p-8 rounded-12 transition-1">
                        <div class="w-36 h-36 rounded-circle flex-center flex-shrink-0 text-16" style="background-color: {{ $iconBg }}; color: {{ $iconColor }};">
                            <i class="ph {{ $icon }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="flex-between">
                                <h6 class="text-13 fw-bold text-gray-900 mb-2">
                                    {{ $log->user->name ?? 'Sistem' }}
                                </h6>
                                <span class="text-gray-400 text-11">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-600 text-12 mb-4">{{ $log->description ?? $log->action }}</p>
                            <span class="badge rounded-pill px-8 py-3 text-10 fw-semibold" style="background-color: {{ $badgeBg }} !important; color: {{ $badgeColor }} !important; border: 1px solid {{ $badgeBorder }} !important;">
                                {{ $log->action }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-40 text-gray-400">
                        <i class="ph ph-clipboard-text text-36 mb-8 d-block text-gray-300"></i>
                        <span class="text-13">Belum ada riwayat aktivitas yang tercatat.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
