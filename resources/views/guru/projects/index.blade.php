@extends('layouts.app')

@section('title', 'Manajemen Proyek PjBL')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('guru.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Proyek PjBL</span></li>
                </ul>
            </div>
            <div class="flex-align gap-8 flex-wrap">
                <a href="{{ route('guru.projects.create') }}" class="btn btn-main rounded-pill px-16 text-13">
                    <i class="ph ph-plus-circle me-4"></i> + Buat Proyek Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-16">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-16">
                <!-- Status Filter Tabs -->
                <div class="d-flex gap-8 flex-wrap">
                    <a href="{{ route('guru.projects.index', ['status' => 'all', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'all' ? 'btn-main' : 'btn-outline-gray' }}">
                        Semua Proyek
                    </a>
                    <a href="{{ route('guru.projects.index', ['status' => 'active', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'active' ? 'btn-main' : 'btn-outline-gray' }}">
                        Aktif
                    </a>
                    <a href="{{ route('guru.projects.index', ['status' => 'draft', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'draft' ? 'btn-main' : 'btn-outline-gray' }}">
                        Draft
                    </a>
                    <a href="{{ route('guru.projects.index', ['status' => 'completed', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'completed' ? 'btn-main' : 'btn-outline-gray' }}">
                        Selesai
                    </a>
                </div>

                <!-- Search Input Form -->
                <form method="GET" action="{{ route('guru.projects.index') }}" class="w-300 position-relative">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="text" 
                           name="search" 
                           class="form-control ps-40 h-40 border-gray-100 rounded-pill text-13" 
                           placeholder="Cari judul atau topik proyek..." 
                           value="{{ $search }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-400 d-flex pointer-event-none">
                        <i class="ph ph-magnifying-glass"></i>
                    </span>
                </form>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="col-12">
        <div class="row g-4">
            @forelse($projects as $project)
                <div class="col-xxl-4 col-md-6">
                    <div class="card border border-gray-100 rounded-16 h-100 hover-border-main-600 transition-2 bg-white d-flex flex-column">
                        <div class="card-body p-24 flex-grow-1">
                            <div class="flex-between mb-12">
                                <span class="badge {{ $project->type === 'group' ? 'bg-primary-50 text-primary-700' : 'bg-info-50 text-info-700' }} text-11 text-uppercase px-10 py-4">
                                    <i class="ph {{ $project->type === 'group' ? 'ph-users-three' : 'ph-user' }} me-2"></i>
                                    {{ $project->type === 'group' ? 'Proyek Kelompok' : 'Proyek Individu' }}
                                </span>
                                <span class="badge {{ $project->status === 'active' ? 'bg-success-50 text-success-700' : 'bg-gray-100 text-gray-700' }} text-11 px-10 py-4 text-capitalize">
                                    {{ $project->status }}
                                </span>
                            </div>

                            <h5 class="mb-8 text-16 fw-bold text-gray-900">
                                <a href="{{ route('guru.projects.show', $project->id) }}" class="text-inherit hover-text-main-600">
                                    {{ $project->title }}
                                </a>
                            </h5>

                            <p class="text-gray-500 text-13 mb-16 text-line-2">
                                {{ Str::limit($project->description, 110) }}
                            </p>

                            <div class="p-12 rounded-10 bg-gray-50 mb-16 flex-between text-12 text-gray-600">
                                <span><i class="ph ph-tray me-4 text-main-600"></i> {{ $project->submissions_count }} Pengumpulan</span>
                                <span><i class="ph ph-notepad me-4 text-main-600"></i> {{ $project->logs_count }} Logbook</span>
                                <span><i class="ph ph-users me-4 text-main-600"></i> {{ $project->students->count() }} Siswa</span>
                            </div>

                            <div class="text-12 text-gray-500 mb-8">
                                <i class="ph ph-calendar text-danger me-4"></i> Deadline: 
                                <strong class="text-gray-800">{{ $project->deadline ? $project->deadline->format('d M Y, H:i') : 'Fleksibel' }}</strong>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top border-gray-100 p-16 px-24 flex-between">
                            <a href="{{ route('guru.projects.show', $project->id) }}" class="btn btn-sm btn-main rounded-pill px-16 text-12">
                                Kelola Proyek &rarr;
                            </a>
                            <div class="d-flex gap-6">
                                <a href="{{ route('guru.projects.edit', $project->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-10 py-2 text-12" title="Edit">
                                    <i class="ph ph-pencil-simple"></i>
                                </a>
                                <form method="POST" action="{{ route('guru.projects.destroy', $project->id) }}" onsubmit="return confirm('Hapus proyek ini beserta seluruh tugas terkait?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-10 py-2 text-12" title="Hapus">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border border-gray-100 rounded-16 p-48 text-center text-gray-400">
                        <span class="w-64 h-64 rounded-circle bg-main-50 text-main-600 flex-center mx-auto text-32 mb-16">
                            <i class="ph ph-folder-open"></i>
                        </span>
                        <h5 class="mb-4 text-16 fw-bold text-gray-800">Belum Ada Proyek PjBL</h5>
                        <p class="text-gray-500 text-13 mb-20">Mulai buat penugasan Project Based Learning untuk siswa kelas RPL Anda.</p>
                        <div>
                            <a href="{{ route('guru.projects.create') }}" class="btn btn-main rounded-pill px-24 text-13">
                                + Buat Proyek Pertama
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if($projects->hasPages())
            <div class="mt-24">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
