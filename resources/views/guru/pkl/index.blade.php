@extends('layouts.app')

@section('title', 'Bimbingan Laporan PKL Kelas XII')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('guru.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Bimbingan PKL Kelas XII</span></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-16">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-16">
                <!-- Status Filter Tabs -->
                <div class="d-flex gap-8 flex-wrap">
                    <a href="{{ route('guru.pkl.index', ['status' => 'all', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'all' ? 'btn-main' : 'btn-outline-gray' }}">
                        Semua Siswa ({{ $counts['all'] }})
                    </a>
                    <a href="{{ route('guru.pkl.index', ['status' => 'submitted', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'submitted' ? 'btn-main' : 'btn-outline-gray' }}">
                        Menunggu Review ({{ $counts['submitted'] }})
                    </a>
                    <a href="{{ route('guru.pkl.index', ['status' => 'revision_required', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'revision_required' ? 'btn-main' : 'btn-outline-gray' }}">
                        Perlu Revisi ({{ $counts['revision_required'] }})
                    </a>
                    <a href="{{ route('guru.pkl.index', ['status' => 'approved', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $status === 'approved' ? 'btn-main' : 'btn-outline-gray' }}">
                        Lulus / Disetujui ({{ $counts['approved'] }})
                    </a>
                </div>

                <!-- Search Input Form -->
                <form method="GET" action="{{ route('guru.pkl.index') }}" class="w-300 position-relative">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="text" 
                           name="search" 
                           class="form-control ps-40 h-40 border-gray-100 rounded-pill text-13" 
                           placeholder="Cari siswa, judul, atau mitra DUDI..." 
                           value="{{ $search }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-400 d-flex pointer-event-none">
                        <i class="ph ph-magnifying-glass"></i>
                    </span>
                </form>
            </div>
        </div>
    </div>

    <!-- Reports Table Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-gray-50 text-gray-600 text-13">
                            <tr>
                                <th class="ps-24">Siswa Bimbingan</th>
                                <th>Judul Laporan PKL</th>
                                <th>Perusahaan / Mitra DUDI</th>
                                <th>Progres Bab (I - V)</th>
                                <th>Status Laporan</th>
                                <th class="text-end pe-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $rep)
                                @php
                                    $approvedChapters = $rep->chapters->where('status', 'approved')->count();
                                @endphp
                                <tr>
                                    <td class="ps-24">
                                        <div class="d-flex align-items-center gap-10">
                                            <img src="{{ $rep->student->user->avatar_url ?? '' }}" alt="" class="w-36 h-36 rounded-circle object-fit-cover">
                                            <div>
                                                <h6 class="mb-0 text-14 fw-bold text-gray-900">{{ $rep->student->user->name ?? 'Siswa' }}</h6>
                                                <small class="text-gray-400">NIS: {{ $rep->student->nis ?? '-' }} &bull; {{ $rep->student->schoolClass->name ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-14 fw-semibold text-gray-900 d-block text-truncate" style="max-width: 250px;">
                                            {{ $rep->title }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-13 text-gray-700 fw-medium d-block">
                                            <i class="ph ph-buildings me-2 text-main-600"></i> {{ $rep->company_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-8">
                                            <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($approvedChapters / 5) * 100 }}%"></div>
                                            </div>
                                            <span class="text-12 font-monospace fw-semibold text-gray-700">{{ $approvedChapters }}/5 Bab</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($rep->status === 'approved')
                                            <span class="badge bg-success-50 text-success-700 text-12 px-10 py-4">Disetujui Final</span>
                                        @elseif($rep->status === 'revision_required')
                                            <span class="badge bg-warning-50 text-warning-700 text-12 px-10 py-4">Perlu Revisi</span>
                                        @else
                                            <span class="badge bg-primary-50 text-primary-700 text-12 px-10 py-4">Bimbingan Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-24">
                                        <a href="{{ route('guru.pkl.show', $rep->id) }}" class="btn btn-sm btn-main rounded-pill px-16 text-12">
                                            Bimbing Laporan &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-40 text-gray-400">
                                        <i class="ph ph-briefcase text-36 mb-8 d-block"></i>
                                        Belum ada data pengajuan bimbingan laporan PKL siswa.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages())
                    <div class="p-20 border-top border-gray-100">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
