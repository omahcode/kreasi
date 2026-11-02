@extends('layouts.app')

@section('title', 'Ploting Siswa Bimbingan PKL')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('guru.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="{{ route('guru.pkl.index') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Laporan PKL</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Kelompok Siswa Bimbingan</span></li>
                </ul>
            </div>
            <div class="flex-align gap-8 flex-wrap">
                <a href="{{ route('guru.pkl.index') }}" class="btn btn-outline-primary rounded-pill px-16 text-13">
                    <i class="ph ph-file-text me-4"></i> Tinjau Berkas Bab PKL
                </a>
                <a href="{{ route('guru.pkl-groups.create') }}" class="btn btn-main rounded-pill px-16 text-13">
                    <i class="ph ph-plus-circle me-4"></i> + Buat Kelompok Bimbingan
                </a>
            </div>
        </div>
    </div>

    <!-- Info banner -->
    <div class="col-12">
        <div class="p-16 rounded-16 bg-main-50 border border-main-200 d-flex align-items-center justify-content-between flex-wrap gap-12 shadow-xs">
            <div class="d-flex align-items-center gap-14">
                <span class="w-44 h-44 rounded-14 bg-white text-main-600 flex-center text-22 flex-shrink-0 shadow-xs border border-main-100">
                    <i class="ph-fill ph-users-three"></i>
                </span>
                <div>
                    <h6 class="text-14 fw-bold text-gray-900 mb-2">Ploting Siswa Bimbingan PKL Anda</h6>
                    <p class="text-13 text-gray-600 mb-0">
                        Kelola penempatan siswa bimbingan ke mitra DU/DI. Siswa yang Anda plot akan otomatis terhubung di dashboard PKL mereka.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-8">
                <span class="badge bg-white text-main-700 border border-main-200 rounded-pill px-12 py-6 text-12 fw-semibold shadow-2xs">
                    <i class="ph ph-check-circle me-2 text-main-600"></i> Terhubung Otomatis
                </span>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 shadow-none">
            <div class="card-body p-24">
                <div class="flex-between flex-wrap gap-16 mb-20">
                    <form method="GET" action="{{ route('guru.pkl-groups.index') }}" class="d-flex align-items-center gap-8 flex-grow-1" style="max-width: 440px;">
                        <div class="position-relative flex-grow-1">
                            <input type="text" name="search" value="{{ $search }}" class="form-control rounded-pill ps-36 py-8 text-13" placeholder="Cari kelompok, industri, siswa...">
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-14 text-gray-400">
                                <i class="ph ph-magnifying-glass"></i>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-main rounded-pill px-18 py-8 text-13 fw-semibold">
                            <i class="ph ph-magnifying-glass me-2"></i> Cari
                        </button>
                        @if($search)
                            <a href="{{ route('guru.pkl-groups.index') }}" class="btn btn-outline-gray rounded-pill px-14 py-8 text-13">Reset</a>
                        @endif
                    </form>

                    <div>
                        <span class="badge bg-gray-50 text-gray-700 border border-gray-200 rounded-pill px-12 py-6 text-12 fw-semibold">
                            Total: <strong class="text-gray-900">{{ $groups->total() }} Kelompok</strong>
                        </span>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-12 text-uppercase fw-semibold">
                                <th class="py-12 px-16 rounded-start-8">Nama Kelompok & Industri</th>
                                <th class="py-12 px-16">Anggota Siswa Bimbingan</th>
                                <th class="py-12 px-16">Periode PKL</th>
                                <th class="py-12 px-16 text-center">Status</th>
                                <th class="py-12 px-16 text-end rounded-end-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                                <tr class="border-bottom border-gray-100 hover-bg-gray-50 transition-1">
                                    <td class="py-16 px-16">
                                        <div class="d-flex align-items-center gap-12">
                                            <span class="w-44 h-44 rounded-14 bg-main-50 text-main-600 flex-center text-22 flex-shrink-0 shadow-xs border border-main-100">
                                                <i class="ph-fill ph-buildings"></i>
                                            </span>
                                            <div>
                                                <h6 class="text-14 fw-bold text-gray-900 mb-2">{{ $group->name }}</h6>
                                                <div class="d-flex align-items-center gap-6 text-12">
                                                    <span class="fw-semibold text-main-700">{{ $group->company->name ?? '-' }}</span>
                                                    @if($group->company?->city)
                                                        <span class="text-gray-300">&bull;</span>
                                                        <span class="text-gray-500 d-inline-flex align-items-center gap-2">
                                                            <i class="ph ph-map-pin text-11"></i> {{ $group->company->city }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-16 px-16">
                                        @php
                                            $leader = $group->students->firstWhere('pivot.is_leader', true) ?? $group->students->first();
                                            $otherStudents = $group->students->reject(fn($s) => $s->id === $leader?->id);
                                        @endphp
                                        <div class="d-flex flex-column gap-6" style="min-width: 250px; max-width: 380px;">
                                            @if($leader)
                                                <div class="d-flex align-items-center gap-6 flex-wrap">
                                                    <span class="badge bg-warning-50 text-warning-800 border border-warning-200 rounded-pill px-10 py-4 text-11 fw-semibold d-inline-flex align-items-center gap-4 shadow-2xs">
                                                        <i class="ph-fill ph-crown text-warning-600"></i> Ketua: {{ $leader->user->name }}
                                                    </span>
                                                    <span class="text-11 text-gray-400">NIS: {{ $leader->nis }}</span>
                                                </div>
                                            @endif
                                            @if($otherStudents->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-4 align-items-center">
                                                    @foreach($otherStudents as $student)
                                                        <span class="badge bg-white text-gray-700 border border-gray-200 rounded-pill px-8 py-3 text-11 fw-medium shadow-2xs" title="NIS: {{ $student->nis }} &bull; {{ $student->user->name }}">
                                                            {{ $student->user->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <span class="text-11 text-gray-400 d-inline-flex align-items-center gap-4 mt-2">
                                                <i class="ph ph-users text-gray-400"></i> {{ $group->students->count() }} Siswa Terdaftar
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-16 px-16">
                                        <div class="d-flex flex-column gap-2 text-12">
                                            <span class="fw-semibold text-gray-800 d-inline-flex align-items-center gap-6">
                                                <i class="ph ph-calendar text-main-600 text-14"></i>
                                                {{ $group->start_date ? $group->start_date->format('d M Y') : 'TBA' }} &ndash;
                                                {{ $group->end_date ? $group->end_date->format('d M Y') : 'TBA' }}
                                            </span>
                                            @if($group->start_date && $group->end_date)
                                                @php
                                                    $months = round($group->start_date->diffInMonths($group->end_date));
                                                @endphp
                                                <span class="text-11 text-gray-400 ps-20">
                                                    Durasi: ~{{ $months > 0 ? $months : 1 }} Bulan
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-16 px-16 text-center">
                                        @if($group->status === 'active')
                                            <span class="badge bg-success-50 text-success-700 border border-success-200 rounded-pill px-12 py-5 text-11 fw-semibold d-inline-flex align-items-center gap-4">
                                                <span class="w-6 h-6 rounded-circle bg-success"></span> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-gray-100 text-gray-700 rounded-pill px-12 py-5 text-11 fw-semibold">
                                                {{ ucfirst($group->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-16 px-16 text-end">
                                        <div class="d-inline-flex align-items-center gap-8 justify-content-end">
                                            <a href="{{ route('guru.pkl-groups.edit', $group->id) }}" 
                                               class="btn btn-sm btn-outline-main rounded-pill px-12 py-6 text-12 fw-semibold d-inline-flex align-items-center gap-4 shadow-2xs"
                                               title="Edit Kelompok">
                                                <i class="ph ph-pencil-simple text-13"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('guru.pkl-groups.destroy', $group->id) }}" class="d-inline" data-confirm="Hapus kelompok bimbingan PKL {{ $group->name }}?" data-confirm-title="Hapus Kelompok" data-confirm-btn="Ya, Hapus!">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle w-32 h-32 flex-center" title="Hapus Kelompok">
                                                    <i class="ph ph-trash text-14"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-40 text-gray-400">
                                        <i class="ph ph-users-three text-40 mb-12 d-block"></i>
                                        Anda belum memplot kelompok siswa bimbingan PKL.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-20">
                    {{ $groups->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
