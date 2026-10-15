@extends('layouts.app')

@section('title', 'Siswa Dashboard')

@section('content')
<div class="row gy-4">
    <!-- Student Header Banner -->
    <div class="col-12">
        <div class="card bg-main-50 border-0 p-24 rounded-16">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-16">
                <div>
                    <div class="d-flex align-items-center gap-8 mb-6">
                        <h3 class="mb-0 text-main-600 fw-bold">Hai, {{ Auth::user()->name }}! &#128075;</h3>
                        @if(Auth::user()->isGroupLeader())
                            <span class="badge bg-warning text-dark rounded-pill px-10 py-4 text-12">
                                <i class="ph ph-crown me-2"></i> Ketua Kelompok
                            </span>
                        @else
                            <span class="badge bg-info-100 text-info-700 rounded-pill px-10 py-4 text-12">
                                <i class="ph ph-student me-2"></i> Siswa RPL
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-600 text-15 mb-0">
                        NIS: <strong>{{ Auth::user()->student->nis ?? '-' }}</strong> &bull; 
                        Kelas: <strong>{{ Auth::user()->student->schoolClass->name ?? 'XII RPL 1' }}</strong> &bull;
                        Rumah digital kreativitas, proyek inovatif, dan bimbingan PKL Anda.
                    </p>
                </div>
                <div class="d-flex gap-8 flex-wrap">
                    <a href="{{ route('siswa.logbook.create') }}" class="btn btn-main rounded-pill px-16">
                        <i class="ph ph-plus-circle me-4"></i> Catat Logbook
                    </a>
                    <a href="{{ route('siswa.pkl.index') }}" class="btn btn-outline-primary rounded-pill px-16">
                        <i class="ph ph-file-text me-4"></i> Bimbingan PKL
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Widgets -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Proyek PjBL Diikuti</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-gray-900">{{ $myProjects->count() }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-50 text-main-600 text-2xl">
                        <i class="ph-fill ph-folder-notch-open"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-main-600 fw-semibold">Aktif</span> dikerjakan semester ini
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Deadline Mendatang</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-warning-600">{{ $upcomingDeadlines->count() }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-50 text-warning-600 text-2xl">
                        <i class="ph-fill ph-calendar-check"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-warning-600 fw-semibold">Perhatikan</span> tanggal pengumpulan
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Catatan Logbook</span>
                <div class="flex-between gap-8 mt-12">
                    <h2 class="mb-0 fw-bold text-primary-600">{{ $recentLogs->count() }}</h2>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-primary-50 text-primary-600 text-2xl">
                        <i class="ph-fill ph-notepad"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    <span class="text-primary-600 fw-semibold">Dokumentasi</span> aktivitas harian
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <span class="text-gray-500 text-14 fw-medium">Status Laporan PKL</span>
                <div class="flex-between gap-8 mt-12">
                    <h4 class="mb-0 fw-bold text-gray-900 text-capitalize">
                        {{ $pklReport->status ?? 'Belum Ada' }}
                    </h4>
                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-success-50 text-success-600 text-2xl">
                        <i class="ph-fill ph-briefcase"></i>
                    </span>
                </div>
                <div class="mt-16 text-13 text-gray-500">
                    @if($pklReport)
                        {{ $pklReport->company_name ?? 'DUDI' }}
                    @else
                        Ajukan judul laporan PKL Anda
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Card Penempatan & Rekan Kelompok PKL -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 shadow-xs overflow-hidden">
            <div class="card-header bg-main-50 py-16 px-24 border-bottom border-gray-100 flex-between flex-wrap gap-12">
                <div class="d-flex align-items-center gap-12">
                    <span class="w-36 h-36 rounded-circle bg-white text-main-600 flex-center text-18 shadow-xs">
                        <i class="ph ph-buildings"></i>
                    </span>
                    <div>
                        <h5 class="mb-0 text-16 fw-bold text-gray-900">Penempatan & Rekan Kelompok PKL</h5>
                        <span class="text-12 text-gray-500">Informasi resmi ploting Praktik Kerja Lapangan (DU/DI) Kelas XII</span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('siswa.pkl.index') }}" class="btn btn-sm btn-main rounded-pill px-16 text-12">
                        <i class="ph ph-file-text me-4"></i> Kelola Laporan PKL
                    </a>
                </div>
            </div>
            <div class="card-body p-24">
                @if($pklGroup)
                    <div class="row gy-20">
                        <!-- Left Info: Industri & Guru -->
                        <div class="col-lg-5 border-end-lg border-gray-100 pe-lg-24">
                            <div class="mb-16">
                                <span class="text-12 text-gray-500 text-uppercase fw-semibold d-block mb-4">Nama Kelompok & Industri</span>
                                <h5 class="text-16 fw-bold text-gray-900 mb-4">{{ $pklGroup->name }}</h5>
                                <div class="d-flex align-items-center gap-8 text-13 text-main-600 fw-semibold">
                                    <i class="ph ph-buildings text-16"></i>
                                    {{ $pklGroup->company->name ?? '-' }}
                                </div>
                                <p class="text-12 text-gray-600 mt-4 mb-0">
                                    <i class="ph ph-map-pin text-gray-400 me-2"></i>
                                    {{ $pklGroup->company->address ?? '-' }} ({{ $pklGroup->company->city ?? '-' }})
                                </p>
                            </div>

                            <div class="pt-12 border-top border-gray-100 mb-16">
                                <span class="text-12 text-gray-500 text-uppercase fw-semibold d-block mb-4">Guru Pembimbing Sekolah</span>
                                <div class="d-flex align-items-center gap-12">
                                    <div class="w-40 h-40 rounded-circle bg-primary-50 text-primary-600 flex-center text-18 fw-bold">
                                        <i class="ph ph-chalkboard-teacher"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-14 fw-bold text-gray-900 mb-2">{{ $pklGroup->mentorTeacher->name ?? '-' }}</h6>
                                        <span class="text-12 text-gray-500">
                                            NIP: {{ $pklGroup->mentorTeacher->teacher->teacher_number ?? '-' }} &bull; {{ $pklGroup->mentorTeacher->email }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-12 border-top border-gray-100">
                                <span class="text-12 text-gray-500 text-uppercase fw-semibold d-block mb-4">Periode Pelaksanaan</span>
                                <div class="text-13 text-gray-800 fw-medium">
                                    <i class="ph ph-calendar text-main-600 me-4"></i>
                                    {{ $pklGroup->start_date ? $pklGroup->start_date->format('d F Y') : 'TBA' }} s/d 
                                    {{ $pklGroup->end_date ? $pklGroup->end_date->format('d F Y') : 'TBA' }}
                                </div>
                            </div>
                        </div>

                        <!-- Right Info: Anggota Kelompok -->
                        <div class="col-lg-7 ps-lg-24">
                            <div class="flex-between mb-12">
                                <span class="text-12 text-gray-500 text-uppercase fw-semibold">
                                    Daftar Teman Satu Kelompok ({{ $pklGroup->students->count() }} Siswa)
                                </span>
                                <span class="badge bg-main-50 text-main-600 text-11 rounded-pill px-10 py-4">
                                    Tim Magang
                                </span>
                            </div>

                            <div class="row g-12">
                                @foreach($pklGroup->students as $member)
                                    <div class="col-sm-6">
                                        <div class="p-12 rounded-12 border {{ $member->user_id === Auth::id() ? 'border-primary-600 bg-main-50' : 'border-gray-100 bg-white' }} h-100 flex-between">
                                            <div class="d-flex align-items-center gap-10">
                                                <div class="w-36 h-36 rounded-circle bg-gray-100 text-gray-700 flex-center text-13 fw-bold flex-shrink-0">
                                                    {{ substr($member->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="text-13 fw-bold text-gray-900 mb-2">
                                                        {{ $member->user->name }}
                                                        @if($member->user_id === Auth::id())
                                                            <span class="text-main-600 text-11">(Anda)</span>
                                                        @endif
                                                    </h6>
                                                    <span class="text-11 text-gray-500">NIS: {{ $member->nis }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                @if($member->pivot->is_leader)
                                                    <span class="badge bg-warning-50 text-warning-700 rounded-pill px-8 py-4 text-11 border border-warning-200">
                                                        <i class="ph ph-crown me-2"></i> Ketua
                                                    </span>
                                                @else
                                                    <span class="badge bg-gray-50 text-gray-600 rounded-pill px-8 py-4 text-11 border border-gray-200">
                                                        Anggota
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($pklGroup->notes)
                                <div class="mt-16 p-12 rounded-8 bg-gray-50 border border-gray-100">
                                    <span class="text-11 text-gray-500 fw-bold d-block mb-2">Catatan Penempatan:</span>
                                    <p class="text-12 text-gray-700 mb-0">{{ $pklGroup->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-24 text-gray-400">
                        <span class="w-56 h-56 rounded-circle bg-gray-100 text-gray-500 flex-center mx-auto text-28 mb-12">
                            <i class="ph ph-users-three"></i>
                        </span>
                        <h6 class="text-14 fw-bold text-gray-700 mb-4">Belum Ada Ploting Kelompok PKL</h6>
                        <p class="text-13 text-gray-500 mb-0" style="max-width: 480px; margin: 0 auto;">
                            Anda belum dimasukkan ke kelompok penempatan PKL oleh Guru Pembimbing atau Administrator. Hubungi guru pembimbing PKL Anda untuk informasi penempatan industri.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- My Projects Section -->
    <div class="col-lg-8">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <div class="flex-between mb-20">
                    <h5 class="mb-0 fw-bold text-gray-900">Proyek PjBL Saya</h5>
                    <a href="{{ route('siswa.projects.index') }}" class="text-main-600 text-13 fw-semibold hover-text-decoration-underline">
                        Lihat Detail &rarr;
                    </a>
                </div>

                <div class="d-flex flex-column gap-16">
                    @forelse($myProjects as $project)
                        <div class="p-20 rounded-12 border border-gray-100 hover-border-main-600 transition-2 bg-white">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-12 mb-12">
                                <div>
                                    <h5 class="mb-4 text-16 fw-bold text-gray-900">{{ $project->title }}</h5>
                                    <span class="text-gray-500 text-13">
                                        Guru Pembimbing: <strong>{{ $project->creator->name ?? 'Guru RPL' }}</strong>
                                    </span>
                                </div>
                                <span class="badge bg-main-50 text-main-600 text-12 text-uppercase">
                                    {{ $project->pivot->role ?? 'member' }}
                                </span>
                            </div>

                            <p class="text-gray-600 text-14 mb-16">{{ Str::limit($project->description, 120) }}</p>

                            <div class="flex-between flex-wrap gap-12 pt-12 border-top border-gray-100">
                                <div class="d-flex align-items-center gap-16 text-13 text-gray-500">
                                    <span>
                                        <i class="ph ph-calendar text-main-600 me-4"></i> 
                                        Deadline: {{ $project->deadline ? $project->deadline->format('d M Y') : '-' }}
                                    </span>
                                    <span>
                                        <i class="ph ph-users text-main-600 me-4"></i> 
                                        {{ $project->students->count() }} Anggota
                                    </span>
                                </div>
                                <a href="{{ route('siswa.projects.show', $project->id) }}" class="btn btn-sm btn-main rounded-pill px-16">
                                    Buka Proyek
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-40 text-gray-400">
                            <i class="ph ph-folder-open text-40 mb-12 d-block"></i>
                            Anda belum terdaftar dalam proyek PjBL apapun.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- PKL Chapter Tracker -->
    <div class="col-lg-4">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <div class="flex-between mb-20">
                    <h5 class="mb-0 fw-bold text-gray-900">Bimbingan PKL (Bab I - V)</h5>
                    <a href="{{ route('siswa.pkl.index') }}" class="text-main-600 text-13 fw-semibold hover-text-decoration-underline">
                        Kelola &rarr;
                    </a>
                </div>

                @if($pklReport)
                    <div class="p-16 rounded-12 bg-main-50 mb-20">
                        <h6 class="mb-4 text-14 fw-bold text-main-600">{{ $pklReport->title }}</h6>
                        <span class="text-12 text-gray-600 d-block">{{ $pklReport->company_name }}</span>
                        <div class="mt-8">
                            <span class="badge bg-white text-main-600 text-11 border border-main-200">
                                Status: {{ strtoupper($pklReport->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Chapters list -->
                    <div class="d-flex flex-column gap-10">
                        @for($i = 1; $i <= 5; $i++)
                            @php
                                $chapter = $pklReport->chapters->firstWhere('chapter_number', $i);
                            @endphp
                            <div class="p-12 rounded-8 border border-gray-100 flex-between">
                                <div class="d-flex align-items-center gap-8">
                                    <span class="w-28 h-28 rounded-circle bg-gray-100 text-gray-700 flex-center text-12 fw-bold">
                                        {{ $i }}
                                    </span>
                                    <span class="text-13 fw-medium text-gray-800">Bab {{ $i }}</span>
                                </div>
                                <div>
                                    @if($chapter && $chapter->status === 'approved')
                                        <span class="badge bg-success-50 text-success-700 text-11">Disetujui</span>
                                    @elseif($chapter && $chapter->status === 'submitted')
                                        <span class="badge bg-primary-50 text-primary-700 text-11">Review</span>
                                    @elseif($chapter && $chapter->status === 'revision_required')
                                        <span class="badge bg-warning-50 text-warning-700 text-11">Revisi</span>
                                    @else
                                        <span class="badge bg-gray-50 text-gray-400 text-11">Belum Upload</span>
                                    @endif
                                </div>
                            </div>
                        @endfor
                    </div>
                @else
                    <div class="text-center py-32 text-gray-400">
                        <i class="ph ph-file-dashed text-36 mb-12 d-block"></i>
                        <p class="text-13 mb-16">Anda belum mengajukan judul laporan PKL.</p>
                        <a href="{{ route('siswa.pkl.index') }}" class="btn btn-sm btn-main rounded-pill px-16">
                            + Ajukan Judul PKL
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
