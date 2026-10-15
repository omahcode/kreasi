@extends('layouts.app')

@section('title', 'Guru Dashboard')

@section('content')
<div class="row gy-4">
    <!-- Header Welcome -->
    <div class="col-12">
        <div class="card bg-main-50 border-0 p-24 rounded-16">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-16">
                <div>
                    <h3 class="mb-4 text-main-600 fw-bold">Dashboard Guru Pembimbing &bull; KREASI RPL</h3>
                    <p class="text-gray-600 text-15 mb-0">
                        Selamat datang, <strong>{{ Auth::user()->name }}</strong>. Pantau proyek PjBL siswa, periksa pengumpulan tugas, dan bimbing laporan PKL kelas XII.
                    </p>
                </div>
                <div class="d-flex gap-8">
                    <a href="{{ route('guru.projects.create') }}" class="btn btn-main rounded-pill px-20">
                        <i class="ph ph-plus-circle me-4"></i> Buat Proyek PjBL
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Widgets -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Proyek PjBL Aktif</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-gray-900">{{ $activeProjects }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-50 text-main-600 text-2xl">
                        <i class="ph-fill ph-folder-notch"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-main-600 fw-semibold">{{ $totalMyProjects }}</span> total proyek dibuat
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Pengumpulan Masuk</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-primary-600">{{ $incomingSubmissions }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-primary-50 text-primary-600 text-2xl">
                        <i class="ph-fill ph-tray"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-primary-600 fw-semibold">Menunggu</span> penilaian Anda
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Revisi Menunggu</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-warning-600">{{ $pendingReviews }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-50 text-warning-600 text-2xl">
                        <i class="ph-fill ph-clock-counter-clockwise"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-warning-600 fw-semibold">Tugas</span> perlu ditinjau ulang
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Bimbingan PKL</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-success-600">{{ $myPklMentees->count() }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-success-50 text-success-600 text-2xl">
                        <i class="ph-fill ph-briefcase"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-success-600 fw-semibold">Siswa</span> dalam bimbingan laporan DUDI
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Submissions Table -->
    <div class="col-lg-7">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <div class="flex-between mb-20">
                    <h5 class="mb-0 fw-bold text-gray-900">Pengumpulan Tugas Siswa Terbaru</h5>
                    <a href="{{ route('guru.submissions.index') }}" class="text-main-600 text-13 fw-semibold hover-text-decoration-underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-gray-50 text-gray-600 text-13">
                            <tr>
                                <th class="ps-16">Siswa / Kelompok</th>
                                <th>Proyek</th>
                                <th>Versi</th>
                                <th>Status</th>
                                <th class="text-end pe-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSubmissions as $sub)
                                <tr>
                                    <td class="ps-16">
                                        <div class="d-flex align-items-center gap-8">
                                            <span class="w-32 h-32 rounded-circle bg-main-50 text-main-600 flex-center fw-bold text-12">
                                                {{ substr($sub->student->user->name ?? 'S', 0, 1) }}
                                            </span>
                                            <div>
                                                <h6 class="mb-0 text-14 fw-semibold">{{ $sub->student->user->name ?? '-' }}</h6>
                                                <span class="text-gray-400 text-11">NIS: {{ $sub->student->nis ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-13 fw-medium text-gray-800 text-truncate d-inline-block" style="max-width: 160px;">
                                            {{ $sub->project->title ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-gray-100 text-gray-700 text-12">v{{ $sub->version }}</span>
                                    </td>
                                    <td>
                                        @if($sub->status === 'approved')
                                            <span class="badge bg-success-50 text-success-700">Disetujui</span>
                                        @elseif($sub->status === 'revision_required')
                                            <span class="badge bg-warning-50 text-warning-700">Revisi</span>
                                        @else
                                            <span class="badge bg-primary-50 text-primary-700">Submitted</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-16">
                                        <a href="{{ route('guru.submissions.show', $sub->id) }}" class="btn btn-sm btn-main rounded-pill px-12 py-4 text-12">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-32 text-gray-400">
                                        <i class="ph ph-tray text-32 mb-8 d-block"></i>
                                        Belum ada pengumpulan tugas masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Projects List -->
    <div class="col-lg-5">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <div class="flex-between mb-20">
                    <h5 class="mb-0 fw-bold text-gray-900">Proyek PjBL Saya</h5>
                    <a href="{{ route('guru.projects.index') }}" class="text-main-600 text-13 fw-semibold hover-text-decoration-underline">
                        Kelola &rarr;
                    </a>
                </div>

                <div class="d-flex flex-column gap-12">
                    @forelse($myProjects as $proj)
                        <div class="p-16 rounded-12 border border-gray-100 hover-border-main-600 transition-2">
                            <div class="flex-between mb-8">
                                <h6 class="mb-0 text-14 fw-semibold text-gray-900">{{ $proj->title }}</h6>
                                <span class="badge bg-main-50 text-main-600 text-11 text-uppercase">{{ $proj->type }}</span>
                            </div>
                            <p class="text-gray-500 text-12 mb-8 text-line-2">{{ Str::limit($proj->description, 80) }}</p>
                            <div class="flex-between text-12 text-gray-400 pt-8 border-top border-gray-100">
                                <span><i class="ph ph-calendar me-4"></i> Deadline: {{ $proj->deadline ? $proj->deadline->format('d M Y') : '-' }}</span>
                                <span class="badge bg-success-50 text-success-600">{{ $proj->status }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-32 text-gray-400">
                            <i class="ph ph-folder-notch text-32 mb-8 d-block"></i>
                            Belum ada proyek dibuat.
                            <div class="mt-12">
                                <a href="{{ route('guru.projects.create') }}" class="btn btn-sm btn-main rounded-pill px-16">
                                    + Buat Proyek Pertama
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
