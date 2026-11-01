@extends('layouts.app')

@section('title', 'Siswa Dashboard')

@section('content')
<div class="row gy-4">
    <!-- Student Header Banner (Clean, Jelas, Rapi & Elegan) -->
    <div class="col-12">
        <div class="card border-0 rounded-20 position-relative overflow-hidden p-24 text-white" 
             style="background: linear-gradient(135deg, #064A8E 0%, #0861B5 60%, #13B8B0 100%) !important; box-shadow: 0 10px 25px -5px rgba(6, 74, 142, 0.22);">
            
            <img src="{{ asset('assets/images/bg/grettings-pattern.png') }}" alt="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover pointer-events-none" style="opacity: 0.15; mix-blend-mode: overlay;">

            <div class="row align-items-center g-20 position-relative z-1">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-8 rounded-pill px-14 py-6 text-12 fw-semibold mb-12 shadow-xs" 
                         style="background: rgba(255, 255, 255, 0.2); color: #ffffff; backdrop-filter: blur(4px);">
                        <span class="text-16">👋</span>
                        <span>Hai, {{ Auth::user()->name }}!</span>
                        @if(Auth::user()->isGroupLeader())
                            <span class="badge rounded-pill px-8 py-2 text-11 fw-bold" style="background: #FFB703; color: #0f172a;">⭐ Ketua Kelompok</span>
                        @else
                            <span class="badge rounded-pill px-8 py-2 text-11 fw-bold" style="background: #38bdf8; color: #0f172a;">🚀 Siswa RPL</span>
                        @endif
                    </div>
                    <h3 class="text-white fw-bold mb-8 text-24">
                        Rumah Karya & Bimbingan Digital RPL ✨
                    </h3>
                    <p class="text-white text-14 mb-20" style="opacity: 0.9; max-width: 560px; line-height: 1.55;">
                        NIS: <strong>{{ Auth::user()->student->nis ?? '-' }}</strong> &bull; 
                        Kelas: <strong>{{ Auth::user()->student->schoolClass->name ?? 'XII RPL' }}</strong> &bull;
                        Terus berinovasi dalam tugas proyek PjBL dan susun laporan PKL terbaikmu dengan semangat!
                    </p>
                    
                    <div class="d-flex flex-wrap gap-10">
                        <a href="{{ route('siswa.logbook.create') }}" 
                           class="btn rounded-pill px-16 py-8 text-13 fw-bold flex-align gap-6 shadow-sm"
                           style="background-color: #ffffff !important; color: #064A8E !important; border: 0 !important;">
                            <i class="ph ph-plus-circle text-16" style="color: #064A8E !important;"></i>
                            <span style="color: #064A8E !important;">Catat Jurnal Logbook</span>
                        </a>
                        <a href="{{ route('siswa.projects.index') }}" 
                           class="btn rounded-pill px-16 py-8 text-13 fw-semibold flex-align gap-6"
                           style="background: rgba(255, 255, 255, 0.18) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                            <i class="ph ph-folder-open text-16 text-white"></i>
                            <span class="text-white">Proyek Saya</span>
                        </a>
                        <a href="{{ route('siswa.pkl.index') }}" 
                           class="btn rounded-pill px-16 py-8 text-13 fw-semibold flex-align gap-6"
                           style="background: rgba(255, 255, 255, 0.18) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                            <i class="ph ph-file-text text-16 text-white"></i>
                            <span class="text-white">Laporan Bab PKL</span>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 text-center d-none d-lg-flex justify-content-center align-items-center position-relative">
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('assets/images/thumbs/panda.png') }}" 
                             alt="Panda Maskot" 
                             style="width: 145px; height: 145px; object-fit: contain; filter: drop-shadow(0 8px 16px rgba(0,0,0,0.18));">
                        <div class="bg-white rounded-pill px-12 py-4 shadow-sm position-absolute top-0 end-0 translate-middle-y text-11 fw-bold text-gray-800 flex-align gap-4" style="border: 1px solid #e2e8f0;">
                            <span>Kreatif Tanpa Batas! 💻</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Cute Candy Stat Cards -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card card-cute card-hover-cute candy-sky p-20 h-100 shadow-none">
            <div class="flex-between align-items-start gap-12 mb-16">
                <div>
                    <span class="text-sky-700 text-13 fw-bold text-uppercase d-block mb-4 letter-spacing-1">
                        🎨 Proyek PjBL Diikuti
                    </span>
                    <h2 class="mb-0 fw-bold text-gray-900 text-32">{{ $myProjects->count() }}</h2>
                </div>
                <div class="w-52 h-52 rounded-20 bg-white text-main-600 flex-center flex-shrink-0 text-26 shadow-sm">
                    <i class="ph-fill ph-folder-notch-open"></i>
                </div>
            </div>
            <div class="pt-12 border-top border-sky-200 border-opacity-60 flex-between text-12">
                <span class="badge bg-white text-sky-800 rounded-pill px-10 py-4 fw-semibold shadow-xs">
                    Aktif Semester Ini
                </span>
                <span class="text-sky-700 fw-medium">Tugas Tim</span>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card card-cute card-hover-cute candy-peach p-20 h-100 shadow-none">
            <div class="flex-between align-items-start gap-12 mb-16">
                <div>
                    <span class="text-orange-800 text-13 fw-bold text-uppercase d-block mb-4 letter-spacing-1">
                        ⏰ Deadline Mendatang
                    </span>
                    <h2 class="mb-0 fw-bold text-gray-900 text-32">{{ $upcomingDeadlines->count() }}</h2>
                </div>
                <div class="w-52 h-52 rounded-20 bg-white text-orange-600 flex-center flex-shrink-0 text-26 shadow-sm">
                    <i class="ph-fill ph-calendar-check"></i>
                </div>
            </div>
            <div class="pt-12 border-top border-orange-200 border-opacity-60 flex-between text-12">
                <span class="badge bg-white text-orange-800 rounded-pill px-10 py-4 fw-semibold shadow-xs">
                    Perhatikan Waktu
                </span>
                <span class="text-orange-700 fw-medium">Target Tugas</span>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card card-cute card-hover-cute candy-mint p-20 h-100 shadow-none">
            <div class="flex-between align-items-start gap-12 mb-16">
                <div>
                    <span class="text-success-800 text-13 fw-bold text-uppercase d-block mb-4 letter-spacing-1">
                        📖 Catatan Logbook
                    </span>
                    <h2 class="mb-0 fw-bold text-gray-900 text-32">{{ $recentLogs->count() }}</h2>
                </div>
                <div class="w-52 h-52 rounded-20 bg-white text-success-600 flex-center flex-shrink-0 text-26 shadow-sm">
                    <i class="ph-fill ph-notepad"></i>
                </div>
            </div>
            <div class="pt-12 border-top border-success-200 border-opacity-60 flex-between text-12">
                <span class="badge bg-white text-success-800 rounded-pill px-10 py-4 fw-semibold shadow-xs">
                    Jurnal Terisi
                </span>
                <span class="text-success-700 fw-medium">Aktivitas Harian</span>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card card-cute card-hover-cute candy-berry p-20 h-100 shadow-none">
            <div class="flex-between align-items-start gap-12 mb-16">
                <div>
                    <span class="text-purple-800 text-13 fw-bold text-uppercase d-block mb-4 letter-spacing-1">
                        💼 Status Laporan PKL
                    </span>
                    <h4 class="mb-0 fw-bold text-gray-900 mt-4">
                        @if($pklReport)
                            @if($pklReport->status === 'approved' && $pklReport->chapters->where('status', 'approved')->count() >= 5)
                                Disetujui Final
                            @elseif($pklReport->status === 'in_progress')
                                In Progress
                            @elseif($pklReport->status === 'revision_required')
                                Perlu Revisi
                            @elseif($pklReport->status === 'submitted')
                                Menunggu Review
                            @else
                                {{ ucwords(str_replace('_', ' ', $pklReport->status)) }}
                            @endif
                        @else
                            Belum Ada
                        @endif
                    </h4>
                </div>
                <div class="w-52 h-52 rounded-20 bg-white text-purple-600 flex-center flex-shrink-0 text-26 shadow-sm">
                    <i class="ph-fill ph-briefcase"></i>
                </div>
            </div>
            <div class="pt-12 border-top border-purple-200 border-opacity-60 flex-between text-12">
                <span class="badge bg-white text-purple-800 rounded-pill px-10 py-4 fw-semibold shadow-xs">
                    {{ $pklReport ? Str::limit($pklReport->company_name, 16) : 'Belum Ajukan' }}
                </span>
                <span class="text-purple-700 fw-medium">Mitra DU/DI</span>
            </div>
        </div>
    </div>

    <!-- Card Penempatan & Rekan Kelompok PKL -->
    <div class="col-12">
        <div class="card card-cute border border-gray-100 bg-white shadow-none overflow-hidden">
            <div class="card-header bg-main-50 py-16 px-24 border-bottom border-gray-100 flex-between flex-wrap gap-12">
                <div class="d-flex align-items-center gap-12">
                    <span class="w-40 h-40 rounded-12 bg-white text-main-600 flex-center text-20 shadow-xs">
                        <i class="ph ph-buildings"></i>
                    </span>
                    <div>
                        <h5 class="mb-0 text-16 fw-bold text-gray-900">Penempatan & Rekan Kelompok PKL</h5>
                        <span class="text-12 text-gray-500">Informasi resmi penempatan Praktik Kerja Lapangan (DU/DI) Kelas XII</span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('siswa.pkl.index') }}" class="btn btn-sm btn-main rounded-pill px-18 py-6 text-12 fw-semibold">
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
                                <span class="text-12 text-gray-400 text-uppercase fw-bold d-block mb-4">Nama Kelompok & Industri</span>
                                <h5 class="text-16 fw-bold text-gray-900 mb-4">{{ $pklGroup->name }}</h5>
                                <div class="d-flex align-items-center gap-8 text-13 text-main-600 fw-bold">
                                    <i class="ph ph-buildings text-16"></i>
                                    {{ $pklGroup->company->name ?? '-' }}
                                </div>
                                <p class="text-12 text-gray-600 mt-4 mb-0">
                                    <i class="ph ph-map-pin text-gray-400 me-2"></i>
                                    {{ $pklGroup->company->address ?? '-' }} ({{ $pklGroup->company->city ?? '-' }})
                                </p>
                            </div>

                            <div class="pt-14 border-top border-gray-100 mb-16">
                                <span class="text-12 text-gray-400 text-uppercase fw-bold d-block mb-4">Guru Pembimbing Sekolah</span>
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

                            <div class="pt-14 border-top border-gray-100">
                                <span class="text-12 text-gray-400 text-uppercase fw-bold d-block mb-4">Periode Pelaksanaan</span>
                                <div class="text-13 text-gray-800 fw-semibold">
                                    <i class="ph ph-calendar text-main-600 me-4"></i>
                                    {{ $pklGroup->start_date ? $pklGroup->start_date->format('d F Y') : 'TBA' }} s/d 
                                    {{ $pklGroup->end_date ? $pklGroup->end_date->format('d F Y') : 'TBA' }}
                                </div>
                            </div>
                        </div>

                        <!-- Right Info: Anggota Kelompok -->
                        <div class="col-lg-7 ps-lg-24">
                            <div class="flex-between mb-14">
                                <span class="text-12 text-gray-400 text-uppercase fw-bold">
                                    Daftar Teman Satu Kelompok ({{ $pklGroup->students->count() }} Siswa)
                                </span>
                                <span class="badge bg-main-50 text-main-700 text-11 rounded-pill px-10 py-4 fw-semibold">
                                    Tim Magang 🤝
                                </span>
                            </div>

                            <div class="row g-12">
                                @foreach($pklGroup->students as $member)
                                    <div class="col-sm-6">
                                        <div class="p-14 rounded-16 border {{ $member->user_id === Auth::id() ? 'border-primary-600 bg-main-50' : 'border-gray-100 bg-white' }} h-100 flex-between">
                                            <div class="d-flex align-items-center gap-10">
                                                <div class="w-36 h-36 rounded-circle bg-gray-100 text-gray-700 flex-center text-13 fw-bold flex-shrink-0">
                                                    {{ strtoupper(substr($member->user->name, 0, 1)) }}
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
                                                    <span class="badge bg-warning-50 text-warning-700 rounded-pill px-8 py-4 text-11 border border-warning-200 fw-bold">
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
                                <div class="mt-16 p-12 rounded-12 bg-gray-50 border border-gray-100">
                                    <span class="text-11 text-gray-400 fw-bold d-block mb-2 text-uppercase">Catatan Penempatan:</span>
                                    <p class="text-12 text-gray-700 mb-0">{{ $pklGroup->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-32 text-gray-400">
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
        <div class="card card-cute border border-gray-100 bg-white p-24 h-100 shadow-none">
            <div class="flex-between flex-wrap gap-12 mb-20">
                <div>
                    <h5 class="mb-4 text-16 fw-bold text-gray-900 flex-align gap-8">
                        <span class="w-32 h-32 rounded-10 bg-main-50 text-main-600 flex-center text-18">
                            <i class="ph ph-folder-notch-open"></i>
                        </span>
                        <span>Proyek PjBL Saya</span>
                    </h5>
                    <p class="text-gray-400 text-13 mb-0">Tantangan proyek yang sedang Anda kerjakan.</p>
                </div>
                <a href="{{ route('siswa.projects.index') }}" class="badge bg-gray-50 text-gray-600 border border-gray-200 px-12 py-6 rounded-pill text-12 fw-semibold hover-bg-gray-100 transition-2">
                    Lihat Detail &rarr;
                </a>
            </div>

            <div class="d-flex flex-column gap-16">
                @forelse($myProjects as $project)
                    <div class="p-20 rounded-16 border border-gray-100 hover-border-main-600 hover-bg-gray-50 transition-2 bg-white">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-12 mb-12">
                            <div>
                                <h5 class="mb-4 text-16 fw-bold text-gray-900">{{ $project->title }}</h5>
                                <span class="text-gray-500 text-13">
                                    Guru Pembimbing: <strong class="text-main-600">{{ $project->creator->name ?? 'Guru RPL' }}</strong>
                                </span>
                            </div>
                            <span class="badge bg-main-50 text-main-700 rounded-pill px-10 py-4 text-11 fw-bold text-uppercase">
                                {{ $project->pivot->role ?? 'member' }}
                            </span>
                        </div>

                        <p class="text-gray-600 text-13 mb-16">{{ Str::limit($project->description, 120) }}</p>

                        <div class="flex-between flex-wrap gap-12 pt-12 border-top border-gray-100">
                            <div class="d-flex align-items-center gap-16 text-12 text-gray-500">
                                <span>
                                    <i class="ph ph-calendar text-main-600 me-4"></i> 
                                    Deadline: {{ $project->deadline ? $project->deadline->format('d M Y') : '-' }}
                                </span>
                                <span>
                                    <i class="ph ph-users text-main-600 me-4"></i> 
                                    {{ $project->students->count() }} Anggota
                                </span>
                            </div>
                            <a href="{{ route('siswa.projects.show', $project->id) }}" class="btn btn-sm btn-main rounded-pill px-16 py-6 text-12 fw-semibold">
                                Buka Proyek &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-40 text-gray-400">
                        <i class="ph ph-folder-open text-40 mb-12 d-block text-gray-300"></i>
                        <span class="text-13">Anda belum terdaftar dalam proyek PjBL apapun.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- PKL Chapter Tracker -->
    <div class="col-lg-4">
        <div class="card card-cute border border-gray-100 bg-white p-24 h-100 shadow-none">
            <div class="flex-between flex-wrap gap-12 mb-20">
                <div>
                    <h5 class="mb-4 text-16 fw-bold text-gray-900 flex-align gap-8">
                        <span class="w-32 h-32 rounded-10 bg-success-50 text-success-600 flex-center text-18">
                            <i class="ph ph-file-text"></i>
                        </span>
                        <span>Bab Laporan PKL</span>
                    </h5>
                    <p class="text-gray-400 text-13 mb-0">Capaian Bab 1 s.d. Bab 5.</p>
                </div>
                <a href="{{ route('siswa.pkl.index') }}" class="badge bg-gray-50 text-gray-600 border border-gray-200 px-12 py-6 rounded-pill text-12 fw-semibold hover-bg-gray-100 transition-2">
                    Kelola &rarr;
                </a>
            </div>

            @if($pklReport)
                <div class="p-16 rounded-16 candy-sky mb-20">
                    <h6 class="mb-4 text-14 fw-bold text-sky-900">{{ $pklReport->title }}</h6>
                    <span class="text-12 text-sky-700 d-block">{{ $pklReport->company_name }}</span>
                    <div class="mt-8">
                        <span class="badge bg-white text-sky-800 rounded-pill px-10 py-4 text-11 fw-bold shadow-xs">
                            Status: @if($pklReport->status === 'in_progress') In Progress @elseif($pklReport->status === 'revision_required') Perlu Revisi @elseif($pklReport->status === 'submitted') Menunggu Review @elseif($pklReport->status === 'approved' && $pklReport->chapters->where('status', 'approved')->count() >= 5) Disetujui Final @else {{ ucwords(str_replace('_', ' ', $pklReport->status)) }} @endif
                        </span>
                    </div>
                </div>

                <!-- Chapters list -->
                <div class="d-flex flex-column gap-10">
                    @for($i = 1; $i <= 5; $i++)
                        @php
                            $chapter = $pklReport->chapters->firstWhere('chapter_number', $i);
                        @endphp
                        <div class="p-12 rounded-12 border border-gray-100 flex-between hover-bg-gray-50 transition-1">
                            <div class="d-flex align-items-center gap-10">
                                <span class="w-28 h-28 rounded-circle bg-main-100 text-main-700 flex-center text-12 fw-bold">
                                    {{ $i }}
                                </span>
                                <span class="text-13 fw-semibold text-gray-800">Bab {{ $i }}</span>
                            </div>
                            <div>
                                @if($chapter && $chapter->status === 'approved')
                                    <span class="badge bg-success-50 text-success-700 rounded-pill px-8 py-3 text-11 fw-semibold">✅ Disetujui</span>
                                @elseif($chapter && $chapter->status === 'submitted')
                                    <span class="badge bg-primary-50 text-primary-700 rounded-pill px-8 py-3 text-11 fw-semibold">📬 Review</span>
                                @elseif($chapter && $chapter->status === 'revision_required')
                                    <span class="badge bg-warning-50 text-warning-700 rounded-pill px-8 py-3 text-11 fw-semibold">✏️ Revisi</span>
                                @else
                                    <span class="badge bg-gray-50 text-gray-400 rounded-pill px-8 py-3 text-11 fw-medium">Belum Upload</span>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            @else
                <div class="text-center py-36 text-gray-400">
                    <i class="ph ph-file-dashed text-36 mb-12 d-block text-gray-300"></i>
                    <p class="text-13 mb-16 text-gray-500">Anda belum mengajukan judul laporan PKL.</p>
                    <a href="{{ route('siswa.pkl.index') }}" class="btn btn-sm btn-main rounded-pill px-18 py-8 text-12 fw-semibold">
                        + Ajukan Judul PKL
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
