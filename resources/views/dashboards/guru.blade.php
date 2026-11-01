@extends('layouts.app')

@section('title', 'Guru Dashboard')

@section('content')
<div class="row gy-4">
    <!-- Hero Welcome Banner (Clean, Jelas, Rapi & Elegan) -->
    <div class="col-12">
        <div class="card border-0 rounded-20 position-relative overflow-hidden p-24 text-white" 
             style="background: linear-gradient(135deg, #064A8E 0%, #0861B5 60%, #13B8B0 100%) !important; box-shadow: 0 10px 25px -5px rgba(6, 74, 142, 0.22);">
            
            <img src="{{ asset('assets/images/bg/grettings-pattern.png') }}" alt="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover pointer-events-none" style="opacity: 0.15; mix-blend-mode: overlay;">

            <div class="row align-items-center g-20 position-relative z-1">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-8 rounded-pill px-14 py-6 text-12 fw-semibold mb-12 shadow-xs" 
                         style="background: rgba(255, 255, 255, 0.2); color: #ffffff; backdrop-filter: blur(4px);">
                        <span class="text-16">👋</span>
                        <span>Semangat Mengajar, {{ Auth::user()->name }}!</span>
                        <span class="badge rounded-pill px-8 py-2 text-11 fw-bold" style="background: #22c55e; color: #ffffff;">Guru Pembimbing 🌟</span>
                    </div>
                    <h3 class="text-white fw-bold mb-8 text-24">
                        Dashboard Guru Pembimbing &bull; Ruang PjBL & PKL ✨
                    </h3>
                    <p class="text-white text-14 mb-20" style="opacity: 0.9; max-width: 560px; line-height: 1.55;">
                        Bimbing siswa-siswi RPL menjadi talenta digital unggulan. Buat proyek tantangan baru, tinjau progres tugas, dan evaluasi laporan bab PKL secara interaktif.
                    </p>
                    
                    <div class="d-flex flex-wrap gap-10">
                        <a href="{{ route('guru.projects.create') }}" 
                           class="btn rounded-pill px-16 py-8 text-13 fw-bold flex-align gap-6 shadow-sm"
                           style="background-color: #ffffff !important; color: #064A8E !important; border: 0 !important;">
                            <i class="ph ph-plus-circle text-16" style="color: #064A8E !important;"></i>
                            <span style="color: #064A8E !important;">Buat Proyek Baru</span>
                        </a>
                        <a href="{{ route('guru.submissions.index') }}" 
                           class="btn rounded-pill px-16 py-8 text-13 fw-semibold flex-align gap-6"
                           style="background: rgba(255, 255, 255, 0.18) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                            <i class="ph ph-tray text-16 text-white"></i>
                            <span class="text-white">Review Tugas</span>
                        </a>
                        <a href="{{ route('guru.pkl.index') }}" 
                           class="btn rounded-pill px-16 py-8 text-13 fw-semibold flex-align gap-6"
                           style="background: rgba(255, 255, 255, 0.18) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                            <i class="ph ph-briefcase text-16 text-white"></i>
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
                            <span>Inspirasi RPL! 💡</span>
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
                        🚀 Proyek Aktif Saya
                    </span>
                    <h2 class="mb-0 fw-bold text-gray-900 text-32">{{ $activeProjects }}</h2>
                </div>
                <div class="w-52 h-52 rounded-20 bg-white text-main-600 flex-center flex-shrink-0 text-26 shadow-sm">
                    <i class="ph-fill ph-folder-notch"></i>
                </div>
            </div>
            <div class="pt-12 border-top border-sky-200 border-opacity-60 flex-between text-12">
                <span class="badge bg-white text-sky-800 rounded-pill px-10 py-4 fw-semibold shadow-xs">
                    {{ $totalMyProjects }} Proyek Dibuat
                </span>
                <span class="text-sky-700 fw-medium">Semester Ini</span>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card card-cute card-hover-cute candy-mint p-20 h-100 shadow-none">
            <div class="flex-between align-items-start gap-12 mb-16">
                <div>
                    <span class="text-success-800 text-13 fw-bold text-uppercase d-block mb-4 letter-spacing-1">
                        📥 Pengumpulan Masuk
                    </span>
                    <h2 class="mb-0 fw-bold text-gray-900 text-32">{{ $incomingSubmissions }}</h2>
                </div>
                <div class="w-52 h-52 rounded-20 bg-white text-success-600 flex-center flex-shrink-0 text-26 shadow-sm">
                    <i class="ph-fill ph-tray"></i>
                </div>
            </div>
            <div class="pt-12 border-top border-success-200 border-opacity-60 flex-between text-12">
                <span class="badge bg-white text-success-800 rounded-pill px-10 py-4 fw-semibold shadow-xs">
                    Menunggu Dinilai
                </span>
                <span class="text-success-700 fw-medium">Tugas Siswa</span>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card card-cute card-hover-cute candy-peach p-20 h-100 shadow-none">
            <div class="flex-between align-items-start gap-12 mb-16">
                <div>
                    <span class="text-orange-800 text-13 fw-bold text-uppercase d-block mb-4 letter-spacing-1">
                        ✏️ Perlu Revisi
                    </span>
                    <h2 class="mb-0 fw-bold text-gray-900 text-32">{{ $pendingReviews }}</h2>
                </div>
                <div class="w-52 h-52 rounded-20 bg-white text-orange-600 flex-center flex-shrink-0 text-26 shadow-sm">
                    <i class="ph-fill ph-clock-counter-clockwise"></i>
                </div>
            </div>
            <div class="pt-12 border-top border-orange-200 border-opacity-60 flex-between text-12">
                <span class="badge bg-white text-orange-800 rounded-pill px-10 py-4 fw-semibold shadow-xs">
                    Tinjau Ulang
                </span>
                <span class="text-orange-700 fw-medium">Feedback Aktif</span>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card card-cute card-hover-cute candy-berry p-20 h-100 shadow-none">
            <div class="flex-between align-items-start gap-12 mb-16">
                <div>
                    <span class="text-purple-800 text-13 fw-bold text-uppercase d-block mb-4 letter-spacing-1">
                        💼 Bimbingan PKL
                    </span>
                    <h2 class="mb-0 fw-bold text-gray-900 text-32">{{ $myPklMentees->count() }}</h2>
                </div>
                <div class="w-52 h-52 rounded-20 bg-white text-purple-600 flex-center flex-shrink-0 text-26 shadow-sm">
                    <i class="ph-fill ph-briefcase"></i>
                </div>
            </div>
            <div class="pt-12 border-top border-purple-200 border-opacity-60 flex-between text-12">
                <span class="badge bg-white text-purple-800 rounded-pill px-10 py-4 fw-semibold shadow-xs">
                    Siswa Bimbingan
                </span>
                <span class="text-purple-700 fw-medium">Mitra Industri</span>
            </div>
        </div>
    </div>

    <!-- Recent Submissions Table -->
    <div class="col-lg-7">
        <div class="card card-cute border border-gray-100 bg-white p-24 h-100 shadow-none">
            <div class="flex-between flex-wrap gap-12 mb-20">
                <div>
                    <h5 class="mb-4 text-16 fw-bold text-gray-900 flex-align gap-8">
                        <span class="w-32 h-32 rounded-10 bg-primary-50 text-primary-600 flex-center text-18">
                            <i class="ph ph-check-square-offset"></i>
                        </span>
                        <span>Pengumpulan Tugas Siswa Terbaru</span>
                    </h5>
                    <p class="text-gray-400 text-13 mb-0">Tinjau dan berikan feedback nilai pada hasil tugas PjBL.</p>
                </div>
                <a href="{{ route('guru.submissions.index') }}" class="badge bg-gray-50 text-gray-600 border border-gray-200 px-12 py-6 rounded-pill text-12 fw-semibold hover-bg-gray-100 transition-2">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-gray-50 text-gray-500 text-12 text-uppercase">
                        <tr>
                            <th class="ps-16 py-10 rounded-start-8">Siswa / Kelompok</th>
                            <th class="py-10">Proyek</th>
                            <th class="py-10">Versi</th>
                            <th class="py-10">Status</th>
                            <th class="text-end pe-16 py-10 rounded-end-8">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubmissions as $sub)
                            <tr class="border-bottom border-gray-50 text-13">
                                <td class="ps-16 py-12">
                                    <div class="flex-align gap-10">
                                        <div class="w-32 h-32 rounded-circle bg-main-100 text-main-700 flex-center fw-bold text-12 flex-shrink-0">
                                            {{ strtoupper(substr($sub->student->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-semibold text-gray-900">{{ $sub->student->user->name ?? '-' }}</span>
                                            <span class="text-gray-400 text-11">NIS: {{ $sub->student->nis ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-12">
                                    <span class="text-13 fw-semibold text-gray-800 text-truncate d-inline-block" style="max-width: 160px;" title="{{ $sub->project->title ?? '-' }}">
                                        {{ $sub->project->title ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-12">
                                    <span class="badge bg-gray-100 text-gray-700 rounded-pill px-8 py-3 text-11 fw-semibold">v{{ $sub->version }}</span>
                                </td>
                                <td class="py-12">
                                    @if($sub->status === 'approved')
                                        <span class="badge bg-success-50 text-success-700 border border-success-200 px-8 py-4 rounded-pill text-11 fw-semibold">✅ Disetujui</span>
                                    @elseif($sub->status === 'revision_required')
                                        <span class="badge bg-warning-50 text-warning-700 border border-warning-200 px-8 py-4 rounded-pill text-11 fw-semibold">✏️ Revisi</span>
                                    @else
                                        <span class="badge bg-primary-50 text-primary-700 border border-primary-200 px-8 py-4 rounded-pill text-11 fw-semibold">📬 Submitted</span>
                                    @endif
                                </td>
                                <td class="text-end pe-16 py-12">
                                    <a href="{{ route('guru.submissions.show', $sub->id) }}" class="btn btn-sm btn-main rounded-pill px-14 py-6 text-12 fw-semibold">
                                        Tinjau
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-40 text-gray-400">
                                    <i class="ph ph-tray text-36 mb-8 d-block text-gray-300"></i>
                                    <span class="text-13">Belum ada pengumpulan tugas siswa masuk.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active Projects List -->
    <div class="col-lg-5">
        <div class="card card-cute border border-gray-100 bg-white p-24 h-100 shadow-none">
            <div class="flex-between flex-wrap gap-12 mb-20">
                <div>
                    <h5 class="mb-4 text-16 fw-bold text-gray-900 flex-align gap-8">
                        <span class="w-32 h-32 rounded-10 bg-warning-50 text-warning-600 flex-center text-18">
                            <i class="ph ph-rocket-launch"></i>
                        </span>
                        <span>Proyek PjBL Saya</span>
                    </h5>
                    <p class="text-gray-400 text-13 mb-0">Daftar proyek dan tantangan belajar aktif.</p>
                </div>
                <a href="{{ route('guru.projects.index') }}" class="badge bg-gray-50 text-gray-600 border border-gray-200 px-12 py-6 rounded-pill text-12 fw-semibold hover-bg-gray-100 transition-2">
                    Kelola &rarr;
                </a>
            </div>

            <div class="d-flex flex-column gap-12">
                @forelse($myProjects as $proj)
                    <div class="p-16 rounded-16 border border-gray-100 hover-border-main-600 hover-bg-gray-50 transition-2">
                        <div class="flex-between mb-8">
                            <h6 class="mb-0 text-14 fw-bold text-gray-900">{{ $proj->title }}</h6>
                            <span class="badge bg-main-50 text-main-700 rounded-pill px-8 py-3 text-10 fw-semibold text-uppercase">{{ $proj->type }}</span>
                        </div>
                        <p class="text-gray-500 text-12 mb-10 text-line-2">{{ Str::limit($proj->description, 80) }}</p>
                        <div class="flex-between text-12 text-gray-400 pt-10 border-top border-gray-100">
                            <span class="flex-align gap-4"><i class="ph ph-calendar text-14 text-main-600"></i> {{ $proj->deadline ? $proj->deadline->format('d M Y') : 'Tanpa batas waktu' }}</span>
                            <span class="badge bg-success-50 text-success-700 rounded-pill px-8 py-2 text-11 fw-semibold">{{ $proj->status }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-40 text-gray-400">
                        <i class="ph ph-folder-dashed text-36 mb-8 d-block text-gray-300"></i>
                        <span class="text-13 d-block mb-12">Belum ada proyek yang Anda buat.</span>
                        <a href="{{ route('guru.projects.create') }}" class="btn btn-sm btn-main rounded-pill px-18 py-8 text-12 fw-semibold">
                            + Buat Proyek Pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
