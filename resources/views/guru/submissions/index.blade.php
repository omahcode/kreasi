@extends('layouts.app')

@section('title', 'Review & Penilaian Tugas Siswa')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('guru.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Review Tugas Siswa</span></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-16">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-16">
                <!-- Status Filter Tabs -->
                <div class="d-flex gap-8 flex-wrap">
                    <a href="{{ route('guru.submissions.index', ['status' => 'all', 'project_id' => $projectId]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'all' ? 'btn-main' : 'btn-outline-gray' }}">
                        Semua Pengumpulan
                    </a>
                    <a href="{{ route('guru.submissions.index', ['status' => 'submitted', 'project_id' => $projectId]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'submitted' ? 'btn-main' : 'btn-outline-gray' }}">
                        Perlu Direview
                    </a>
                    <a href="{{ route('guru.submissions.index', ['status' => 'revision_required', 'project_id' => $projectId]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'revision_required' ? 'btn-main' : 'btn-outline-gray' }}">
                        Menunggu Revisi
                    </a>
                    <a href="{{ route('guru.submissions.index', ['status' => 'approved', 'project_id' => $projectId]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'approved' ? 'btn-main' : 'btn-outline-gray' }}">
                        Disetujui
                    </a>
                </div>

                <!-- Project Filter Dropdown -->
                <form method="GET" action="{{ route('guru.submissions.index') }}" class="w-300">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <select name="project_id" class="form-select text-13 rounded-pill border-gray-100" onchange="this.form.submit()">
                        <option value="">-- Semua Proyek PjBL --</option>
                        @foreach($myProjects as $proj)
                            <option value="{{ $proj->id }}" {{ $projectId == $proj->id ? 'selected' : '' }}>
                                {{ $proj->title }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Submissions Table Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-gray-50 text-gray-600 text-13">
                            <tr>
                                <th class="ps-24">Siswa Pengumpul</th>
                                <th>Proyek PjBL</th>
                                <th>Versi</th>
                                <th>Waktu Pengumpulan</th>
                                <th>Status</th>
                                <th>Nilai</th>
                                <th class="text-end pe-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $sub)
                                @php $latestRev = $sub->reviews->first(); @endphp
                                <tr>
                                    <td class="ps-24">
                                        <div class="d-flex align-items-center gap-10">
                                            <img src="{{ $sub->student->user->avatar_url ?? '' }}" alt="" class="w-36 h-36 rounded-circle object-fit-cover">
                                            <div>
                                                <h6 class="mb-0 text-14 fw-bold text-gray-900">{{ $sub->student->user->name ?? 'Siswa' }}</h6>
                                                <small class="text-gray-400">NIS: {{ $sub->student->nis ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-13 fw-semibold text-gray-800 d-block text-truncate" style="max-width: 200px;">
                                            {{ $sub->project->title ?? '-' }}
                                        </span>
                                        <small class="text-gray-400 text-uppercase">{{ $sub->project->type ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-gray-100 text-gray-700 text-12 font-monospace">v{{ $sub->version }}</span>
                                    </td>
                                    <td>
                                        <span class="text-13 text-gray-700">{{ $sub->created_at->format('d M Y, H:i') }}</span>
                                        <small class="text-gray-400 d-block">{{ $sub->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($sub->status === 'approved')
                                            <span class="badge bg-success-50 text-success-700 text-12 px-10 py-4">Disetujui</span>
                                        @elseif($sub->status === 'revision_required')
                                            <span class="badge bg-warning-50 text-warning-700 text-12 px-10 py-4">Perlu Revisi</span>
                                        @else
                                            <span class="badge bg-primary-50 text-primary-700 text-12 px-10 py-4">Menunggu Review</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-14 {{ $latestRev && $latestRev->score >= 75 ? 'text-success-600' : 'text-gray-800' }}">
                                            {{ $latestRev ? $latestRev->score : '-' }}
                                        </strong>
                                    </td>
                                    <td class="text-end pe-24">
                                        <a href="{{ route('guru.submissions.show', $sub->id) }}" class="btn btn-sm btn-main rounded-pill px-16 text-12">
                                            Review & Nilai
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-40 text-gray-400">
                                        <i class="ph ph-tray text-36 mb-8 d-block"></i>
                                        Tidak ada berkas pengumpulan tugas yang sesuai kriteria filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($submissions->hasPages())
                    <div class="p-20 border-top border-gray-100">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
