@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row gy-4">
    <!-- Header Welcome -->
    <div class="col-12">
        <div class="card bg-main-50 border-0 p-24 rounded-16">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-16">
                <div>
                    <h3 class="mb-4 text-main-600 fw-bold">Dashboard Administrator &bull; KREASI RPL</h3>
                    <p class="text-gray-600 text-15 mb-0">
                        Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>. Pantau seluruh aktivitas pembelajaran PjBL dan PKL siswa secara realtime.
                    </p>
                </div>
                <div class="d-flex gap-8">
                    <a href="{{ route('admin.students.import') }}" class="btn btn-main rounded-pill px-20">
                        <i class="ph ph-file-arrow-up me-4"></i> Import Siswa Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Widgets (Edmate Style) -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Total Siswa Terdaftar</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-gray-900">{{ $totalStudents }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-50 text-main-600 text-2xl">
                        <i class="ph-fill ph-student"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-success-600 fw-semibold"><i class="ph ph-users"></i> Aktif</span> di sistem jurusan RPL
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Total Guru / Pembimbing</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-gray-900">{{ $totalTeachers }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-success-50 text-success-600 text-2xl">
                        <i class="ph-fill ph-chalkboard-teacher"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-success-600 fw-semibold"><i class="ph ph-check"></i> Terverifikasi</span> pembimbing PjBL/PKL
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Total Proyek PjBL</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-gray-900">{{ $totalProjects }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-50 text-warning-600 text-2xl">
                        <i class="ph-fill ph-folder-notch"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-warning-600 fw-semibold"><i class="ph ph-kanban"></i> Project Based</span> Learning aktif
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Rombongan Belajar (Kelas)</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-gray-900">{{ $totalClasses }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-info-50 text-info-600 text-2xl">
                        <i class="ph-fill ph-chalkboard"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-info-600 fw-semibold"><i class="ph ph-graduation-cap"></i> Tingkat</span> X, XI, dan XII RPL
                </div>
            </div>
        </div>
    </div>

    <!-- PKL Statistics Breakdown -->
    <div class="col-lg-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <div class="flex-between mb-20">
                    <h5 class="mb-0 fw-bold text-gray-900">Statistik Bimbingan PKL Kelas XII</h5>
                    <span class="badge bg-main-50 text-main-600 px-12 py-6 rounded-pill text-13">
                        Total: {{ $pklStats['total'] }} Laporan
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-16 rounded-12 bg-success-50 border border-success-100 flex-between">
                            <div>
                                <span class="text-gray-600 text-13 d-block">Approved (Disetujui)</span>
                                <h4 class="mb-0 text-success-700 fw-bold mt-4">{{ $pklStats['approved'] }}</h4>
                            </div>
                            <span class="w-40 h-40 rounded-circle bg-success-600 text-white flex-center text-xl">
                                <i class="ph ph-check-circle"></i>
                            </span>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-16 rounded-12 bg-primary-50 border border-primary-100 flex-between">
                            <div>
                                <span class="text-gray-600 text-13 d-block">Submitted (Diajukan)</span>
                                <h4 class="mb-0 text-primary-700 fw-bold mt-4">{{ $pklStats['submitted'] }}</h4>
                            </div>
                            <span class="w-40 h-40 rounded-circle bg-primary-600 text-white flex-center text-xl">
                                <i class="ph ph-paper-plane-tilt"></i>
                            </span>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-16 rounded-12 bg-warning-50 border border-warning-100 flex-between">
                            <div>
                                <span class="text-gray-600 text-13 d-block">Perlu Revisi</span>
                                <h4 class="mb-0 text-warning-700 fw-bold mt-4">{{ $pklStats['revision_required'] }}</h4>
                            </div>
                            <span class="w-40 h-40 rounded-circle bg-warning-600 text-white flex-center text-xl">
                                <i class="ph ph-arrow-counter-clockwise"></i>
                            </span>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-16 rounded-12 bg-gray-50 border border-gray-100 flex-between">
                            <div>
                                <span class="text-gray-600 text-13 d-block">Draft (Penyusunan)</span>
                                <h4 class="mb-0 text-gray-700 fw-bold mt-4">{{ $pklStats['draft'] }}</h4>
                            </div>
                            <span class="w-40 h-40 rounded-circle bg-gray-500 text-white flex-center text-xl">
                                <i class="ph ph-file-dashed"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-24 pt-20 border-top border-gray-100 flex-between">
                    <span class="text-gray-500 text-14">Ingin melihat laporan lengkap bimbingan DUDI?</span>
                    <a href="{{ route('admin.classes.index') }}" class="text-main-600 fw-semibold text-14 hover-text-decoration-underline">
                        Lihat Data Akademik &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Audit Logs -->
    <div class="col-lg-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <div class="flex-between mb-20">
                    <h5 class="mb-0 fw-bold text-gray-900">Aktivitas Terakhir (Audit Log)</h5>
                    <a href="{{ route('admin.audit-logs') }}" class="text-main-600 text-13 fw-semibold hover-text-decoration-underline">
                        Semua Log &rarr;
                    </a>
                </div>

                <div class="max-h-350 overflow-y-auto scroll-sm">
                    @forelse($recentActivities as $log)
                        <div class="d-flex align-items-start gap-12 pb-16 mb-16 border-bottom border-gray-100">
                            <span class="w-36 h-36 rounded-circle bg-main-50 text-main-600 flex-center flex-shrink-0 text-lg">
                                <i class="ph ph-shield"></i>
                            </span>
                            <div class="flex-grow-1">
                                <div class="flex-between">
                                    <h6 class="text-14 fw-semibold text-gray-900 mb-2">
                                        {{ $log->user->name ?? 'Sistem' }}
                                    </h6>
                                    <span class="text-gray-400 text-12">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-600 text-13 mb-4">{{ $log->description ?? $log->action }}</p>
                                <span class="badge bg-gray-100 text-gray-700 text-11">{{ $log->action }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-32 text-gray-400">
                            <i class="ph ph-clipboard-text text-32 mb-8 d-block"></i>
                            Belum ada riwayat aktivitas tercatat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
