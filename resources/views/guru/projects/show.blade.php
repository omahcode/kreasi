@extends('layouts.app')

@section('title', $project->title)

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('guru.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="{{ route('guru.projects.index') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Proyek PjBL</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Detail Proyek</span></li>
                </ul>
            </div>
            <div class="d-flex gap-8">
                <a href="{{ route('guru.projects.edit', $project->id) }}" class="btn btn-outline-warning rounded-pill px-16 text-13">
                    <i class="ph ph-pencil-simple me-4"></i> Edit Proyek
                </a>
                <a href="{{ route('guru.projects.index') }}" class="btn btn-outline-gray rounded-pill px-16 text-13">
                    <i class="ph ph-arrow-left me-4"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Project Overview Header Banner -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-24 bg-white shadow-xs">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-16 mb-16">
                <div>
                    <div class="d-flex align-items-center gap-8 mb-8">
                        <span class="badge {{ $project->type === 'group' ? 'bg-primary-50 text-primary-700' : 'bg-info-50 text-info-700' }} text-12 text-uppercase px-12 py-4">
                            {{ $project->type === 'group' ? 'Proyek Kelompok' : 'Proyek Individu' }}
                        </span>
                        <span class="badge {{ $project->status === 'active' ? 'bg-success-50 text-success-700' : 'bg-gray-100 text-gray-700' }} text-12 px-12 py-4 text-capitalize">
                            Status: {{ $project->status }}
                        </span>
                    </div>
                    <h3 class="mb-8 fw-bold text-gray-900">{{ $project->title }}</h3>
                    <p class="text-gray-600 text-15 mb-0" style="max-width: 800px;">{{ $project->description }}</p>
                </div>
                <div class="text-end">
                    <span class="text-gray-500 text-13 d-block">Batas Waktu:</span>
                    <strong class="text-danger text-16 d-block mt-2">
                        <i class="ph ph-calendar me-2"></i> {{ $project->deadline ? $project->deadline->format('d M Y, H:i') : 'Tidak ditentukan' }}
                    </strong>
                    @if($project->attachment)
                        <div class="mt-8">
                            <a href="{{ asset('storage/' . $project->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-16 text-12">
                                <i class="ph ph-file-arrow-down me-4"></i> Unduh Lampiran Panduan
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="row g-3 pt-16 border-top border-gray-100 text-center">
                <div class="col-sm-3 col-6 border-end border-gray-100">
                    <span class="text-gray-400 text-12 d-block">Total Anggota</span>
                    <h5 class="mb-0 fw-bold text-gray-900 mt-2">{{ $project->students->count() }} Siswa</h5>
                </div>
                <div class="col-sm-3 col-6 border-end border-gray-100">
                    <span class="text-gray-400 text-12 d-block">Ketua Kelompok</span>
                    <h5 class="mb-0 fw-bold text-warning-700 mt-2">{{ $leader ? $leader->student->user->name : '-' }}</h5>
                </div>
                <div class="col-sm-3 col-6 border-end border-gray-100">
                    <span class="text-gray-400 text-12 d-block">Pengumpulan Tugas</span>
                    <h5 class="mb-0 fw-bold text-primary-700 mt-2">{{ $project->submissions->count() }} Berkas</h5>
                </div>
                <div class="col-sm-3 col-6">
                    <span class="text-gray-400 text-12 d-block">Catatan Logbook</span>
                    <h5 class="mb-0 fw-bold text-success-700 mt-2">{{ $project->logs->count() }} Aktivitas</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-0 overflow-hidden">
            <div class="card-header bg-gray-50 border-bottom border-gray-100 p-0">
                <ul class="nav nav-tabs border-0 px-16 pt-8" id="pjblTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold text-14 py-12 px-20" id="tab-overview" data-bs-toggle="tab" data-bs-target="#content-overview" type="button">
                            <i class="ph ph-article me-4"></i> Rincian & Instruksi
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-14 py-12 px-20" id="tab-members" data-bs-toggle="tab" data-bs-target="#content-members" type="button">
                            <i class="ph ph-users me-4"></i> Anggota Siswa ({{ $project->students->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-14 py-12 px-20" id="tab-submissions" data-bs-toggle="tab" data-bs-target="#content-submissions" type="button">
                            <i class="ph ph-tray me-4"></i> Pengumpulan Tugas ({{ $project->submissions->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-14 py-12 px-20" id="tab-logs" data-bs-toggle="tab" data-bs-target="#content-logs" type="button">
                            <i class="ph ph-notepad me-4"></i> Logbook Siswa ({{ $project->logs->count() }})
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-24">
                <div class="tab-content" id="pjblTabsContent">
                    <!-- Tab 1: Overview -->
                    <div class="tab-pane fade show active" id="content-overview" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="text-14 fw-bold text-gray-900 mb-8"><i class="ph ph-target me-4 text-main-600"></i> Tujuan Pembelajaran</h6>
                                <div class="p-16 rounded-12 bg-gray-50 text-14 text-gray-700 whitespace-pre-line border border-gray-100">
                                    {{ $project->goals ?: 'Tidak ada tujuan khusus yang dicantumkan.' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-14 fw-bold text-gray-900 mb-8"><i class="ph ph-check-square me-4 text-main-600"></i> Rubrik & Kriteria Penilaian</h6>
                                <div class="p-16 rounded-12 bg-gray-50 text-14 text-gray-700 whitespace-pre-line border border-gray-100">
                                    {{ $project->rubric ?: 'Rubrik penilaian standar RPL.' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <h6 class="text-14 fw-bold text-gray-900 mb-8"><i class="ph ph-list-numbers me-4 text-main-600"></i> Instruksi Pengerjaan Proyek</h6>
                                <div class="p-16 rounded-12 bg-gray-50 text-14 text-gray-700 whitespace-pre-line border border-gray-100">
                                    {{ $project->instructions ?: 'Ikuti langkah-langkah kerja sesuai arahan guru pembimbing di kelas.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Anggota Siswa -->
                    <div class="tab-pane fade" id="content-members" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-gray-50 text-gray-600 text-13">
                                    <tr>
                                        <th class="ps-16">Nama Siswa</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        <th>Peran di Proyek</th>
                                        <th>Kontak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->students as $member)
                                        @php
                                            $isMemberLeader = $member->pivot->role === 'leader';
                                        @endphp
                                        <tr>
                                            <td class="ps-16">
                                                <div class="d-flex align-items-center gap-10">
                                                    <img src="{{ $member->user->avatar_url }}" alt="" class="w-36 h-36 rounded-circle object-fit-cover">
                                                    <div>
                                                        <span class="text-14 fw-bold text-gray-900">{{ $member->user->name }}</span>
                                                        <span class="text-11 text-gray-400 d-block">{{ $member->user->email }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-13 text-gray-700">{{ $member->nis }}</span></td>
                                            <td><span class="badge bg-gray-100 text-gray-800 text-12">{{ $member->schoolClass->name ?? '-' }}</span></td>
                                            <td>
                                                @if($isMemberLeader)
                                                    <span class="badge bg-warning-50 text-warning-700 text-12 px-10 py-4">
                                                        <i class="ph ph-crown me-2"></i> Ketua Kelompok
                                                    </span>
                                                @else
                                                    <span class="badge bg-info-50 text-info-700 text-12 px-10 py-4">
                                                        Anggota
                                                    </span>
                                                @endif
                                            </td>
                                            <td><span class="text-13 text-gray-500">{{ $member->user->phone ?: '-' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Submissions -->
                    <div class="tab-pane fade" id="content-submissions" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-gray-50 text-gray-600 text-13">
                                    <tr>
                                        <th class="ps-16">Siswa Pengumpul</th>
                                        <th>Versi</th>
                                        <th>Waktu Kumpul</th>
                                        <th>Catatan Siswa</th>
                                        <th>Status Review</th>
                                        <th>Nilai</th>
                                        <th class="text-end pe-16">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($project->submissions as $sub)
                                        @php $latestRev = $sub->reviews->first(); @endphp
                                        <tr>
                                            <td class="ps-16">
                                                <span class="text-14 fw-bold text-gray-900">{{ $sub->student->user->name ?? 'Siswa' }}</span>
                                                <small class="text-gray-400 d-block">NIS: {{ $sub->student->nis ?? '-' }}</small>
                                            </td>
                                            <td><span class="badge bg-gray-100 text-gray-700 text-12">v{{ $sub->version }}</span></td>
                                            <td><span class="text-13 text-gray-600">{{ $sub->created_at->format('d M Y, H:i') }}</span></td>
                                            <td><span class="text-13 text-gray-500 text-truncate d-inline-block" style="max-width: 180px;">{{ $sub->notes ?: '-' }}</span></td>
                                            <td>
                                                @if($sub->status === 'approved')
                                                    <span class="badge bg-success-50 text-success-700 text-12">Disetujui</span>
                                                @elseif($sub->status === 'revision_required')
                                                    <span class="badge bg-warning-50 text-warning-700 text-12">Perlu Revisi</span>
                                                @else
                                                    <span class="badge bg-primary-50 text-primary-700 text-12">Submitted</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-14 {{ $latestRev && $latestRev->score >= 75 ? 'text-success-600' : 'text-gray-800' }}">
                                                    {{ $latestRev ? $latestRev->score : '-' }}
                                                </strong>
                                            </td>
                                            <td class="text-end pe-16">
                                                <a href="{{ route('guru.submissions.show', $sub->id) }}" class="btn btn-sm btn-main rounded-pill px-16 text-12">
                                                    Review & Beri Nilai
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-32 text-gray-400">
                                                <i class="ph ph-tray text-36 mb-8 d-block"></i>
                                                Belum ada berkas tugas yang dikumpulkan siswa.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Logbook Siswa -->
                    <div class="tab-pane fade" id="content-logs" role="tabpanel">
                        <div class="d-flex flex-column gap-16">
                            @forelse($project->logs as $log)
                                <div class="p-16 rounded-12 border border-gray-100 bg-white">
                                    <div class="flex-between mb-8">
                                        <div class="d-flex align-items-center gap-8">
                                            <img src="{{ $log->student->user->avatar_url }}" alt="" class="w-32 h-32 rounded-circle">
                                            <div>
                                                <h6 class="mb-0 text-14 fw-bold">{{ $log->student->user->name }}</h6>
                                                <small class="text-gray-400">{{ $log->created_at->format('d M Y, H:i') }} ({{ $log->created_at->diffForHumans() }})</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-main-50 text-main-600 text-12 px-10 py-4">
                                            Progres: {{ $log->progress_percentage }}%
                                        </span>
                                    </div>
                                    <p class="text-gray-700 text-14 mb-12">{{ $log->description }}</p>
                                    @if($log->image)
                                        <div>
                                            <a href="{{ asset('storage/' . $log->image) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $log->image) }}" alt="Dokumentasi" class="rounded-8 border border-gray-100" style="max-height: 180px; object-fit: cover;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-32 text-gray-400">
                                    <i class="ph ph-notepad text-36 mb-8 d-block"></i>
                                    Belum ada catatan aktivitas logbook dari siswa pada proyek ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
