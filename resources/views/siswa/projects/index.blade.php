@extends('layouts.app')

@section('title', 'Proyek PjBL Saya')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('siswa.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Proyek PjBL Saya</span></li>
                </ul>
            </div>
            <a href="{{ route('siswa.logbook.create') }}" class="btn btn-main rounded-pill px-16 text-13">
                <i class="ph ph-plus-circle me-4"></i> Catat Logbook Harian
            </a>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="col-12">
        <div class="row g-4">
            @forelse($projects as $project)
                @php
                    $isLeader = $project->pivot->role === 'leader';
                @endphp
                <div class="col-xxl-4 col-md-6">
                    <div class="card border border-gray-100 rounded-16 h-100 hover-border-main-600 transition-2 bg-white d-flex flex-column">
                        <div class="card-body p-24 flex-grow-1">
                            <div class="flex-between mb-12">
                                <span class="badge {{ $project->type === 'group' ? 'bg-primary-50 text-primary-700' : 'bg-info-50 text-info-700' }} text-11 text-uppercase px-10 py-4">
                                    {{ $project->type === 'group' ? 'Proyek Kelompok' : 'Proyek Individu' }}
                                </span>
                                @if($isLeader)
                                    <span class="badge bg-warning-50 text-warning-700 text-11 px-10 py-4">
                                        <i class="ph ph-crown me-2"></i> Ketua Kelompok
                                    </span>
                                @else
                                    <span class="badge bg-gray-100 text-gray-700 text-11 px-10 py-4">
                                        Anggota
                                    </span>
                                @endif
                            </div>

                            <h5 class="mb-8 text-16 fw-bold text-gray-900">
                                <a href="{{ route('siswa.projects.show', $project->id) }}" class="text-inherit hover-text-main-600">
                                    {{ $project->title }}
                                </a>
                            </h5>

                            <p class="text-gray-500 text-13 mb-16 text-line-2">
                                {{ Str::limit($project->description, 110) }}
                            </p>

                            <div class="p-12 rounded-10 bg-gray-50 mb-16 text-12 text-gray-600">
                                <div class="mb-4">
                                    <i class="ph ph-chalkboard-teacher me-4 text-main-600"></i> Guru: 
                                    <strong>{{ $project->creator->name ?? 'Guru RPL' }}</strong>
                                </div>
                                <div>
                                    <i class="ph ph-users me-4 text-main-600"></i> Tim: 
                                    <strong>{{ $project->students->count() }} Anggota</strong>
                                </div>
                            </div>

                            <div class="text-12 text-gray-500">
                                <i class="ph ph-calendar text-danger me-4"></i> Batas Waktu: 
                                <strong class="text-gray-800">{{ $project->deadline ? $project->deadline->format('d M Y, H:i') : 'Fleksibel' }}</strong>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top border-gray-100 p-16 px-24 flex-between">
                            <span class="text-12 text-gray-400">
                                <i class="ph ph-tray me-2"></i> {{ $project->submissions_count }} Pengumpulan
                            </span>
                            <a href="{{ route('siswa.projects.show', $project->id) }}" class="btn btn-sm btn-main rounded-pill px-16 text-12">
                                Buka Proyek &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border border-gray-100 rounded-16 p-48 text-center text-gray-400">
                        <span class="w-64 h-64 rounded-circle bg-main-50 text-main-600 flex-center mx-auto text-32 mb-16">
                            <i class="ph ph-folder-open"></i>
                        </span>
                        <h5 class="mb-4 text-16 fw-bold text-gray-800">Belum Ada Proyek Ditugaskan</h5>
                        <p class="text-gray-500 text-13 mb-0">Anda belum ditugaskan ke dalam proyek PjBL apapun oleh guru pembimbing.</p>
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
