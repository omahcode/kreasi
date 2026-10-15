@extends('layouts.app')

@section('title', 'Ploting Kelompok PKL Siswa')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Actions -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Ploting Kelompok PKL</span></li>
                </ul>
            </div>
            <div class="flex-align gap-8 flex-wrap">
                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-primary rounded-pill px-16 text-13">
                    <i class="ph ph-buildings me-4"></i> Master Data Industri
                </a>
                <a href="{{ route('admin.pkl-groups.create') }}" class="btn btn-main rounded-pill px-16 text-13">
                    <i class="ph ph-plus-circle me-4"></i> + Ploting Kelompok Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16">
            <div class="card-body p-24">
                <!-- Filter & Search Toolbar -->
                <div class="flex-between flex-wrap gap-16 mb-20">
                    <form method="GET" action="{{ route('admin.pkl-groups.index') }}" class="d-flex align-items-center gap-8 flex-grow-1" style="max-width: 440px;">
                        <div class="position-relative flex-grow-1">
                            <input type="text" name="search" value="{{ $search }}" class="form-control rounded-pill ps-36 py-8 text-13" placeholder="Cari kelompok, industri, guru, siswa...">
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-14 text-gray-400">
                                <i class="ph ph-magnifying-glass"></i>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-main rounded-pill px-16 py-8 text-13">Cari</button>
                        @if($search)
                            <a href="{{ route('admin.pkl-groups.index') }}" class="btn btn-outline-gray rounded-pill px-12 py-8 text-13">Reset</a>
                        @endif
                    </form>

                    <div class="text-gray-500 text-13">
                        Total Ploting: <strong class="text-gray-900">{{ $groups->total() }} Kelompok PKL</strong>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-12 text-uppercase fw-semibold">
                                <th class="py-12 px-16 rounded-start-8">Nama Kelompok & Industri</th>
                                <th class="py-12 px-16">Guru Pembimbing</th>
                                <th class="py-12 px-16">Anggota Siswa</th>
                                <th class="py-12 px-16">Periode PKL</th>
                                <th class="py-12 px-16 text-center">Status</th>
                                <th class="py-12 px-16 text-end rounded-end-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                                <tr class="border-bottom border-gray-100">
                                    <td class="py-16 px-16">
                                        <div class="d-flex align-items-center gap-12">
                                            <span class="w-40 h-40 rounded-circle bg-main-50 text-main-600 flex-center text-18 fw-bold flex-shrink-0">
                                                <i class="ph ph-users-three"></i>
                                            </span>
                                            <div>
                                                <h6 class="text-14 fw-bold text-gray-900 mb-2">{{ $group->name }}</h6>
                                                <span class="text-12 text-main-600 fw-semibold">
                                                    <i class="ph ph-buildings me-2"></i> {{ $group->company->name ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-16 px-16">
                                        <div class="text-13 fw-semibold text-gray-900">
                                            {{ $group->mentorTeacher->name ?? 'Belum Ditentukan' }}
                                        </div>
                                        <small class="text-12 text-gray-500">
                                            NIP: {{ $group->mentorTeacher->teacher->teacher_number ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="py-16 px-16">
                                        <div class="d-flex flex-wrap gap-6" style="max-width: 320px;">
                                            @foreach($group->students as $student)
                                                <span class="badge {{ $student->pivot->is_leader ? 'bg-warning-50 text-warning-700 border border-warning-200' : 'bg-gray-50 text-gray-700 border border-gray-200' }} rounded-pill px-8 py-4 text-11">
                                                    @if($student->pivot->is_leader)
                                                        <i class="ph ph-crown me-2"></i>
                                                    @endif
                                                    {{ $student->user->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                        <small class="text-11 text-gray-400 mt-4 d-block">
                                            Total: {{ $group->students->count() }} siswa
                                        </small>
                                    </td>
                                    <td class="py-16 px-16">
                                        <div class="text-12 text-gray-700">
                                            <i class="ph ph-calendar text-main-600 me-2"></i>
                                            {{ $group->start_date ? $group->start_date->format('d M Y') : 'TBA' }} &ndash;
                                            {{ $group->end_date ? $group->end_date->format('d M Y') : 'TBA' }}
                                        </div>
                                    </td>
                                    <td class="py-16 px-16 text-center">
                                        <span class="badge bg-success-50 text-success-700 rounded-pill px-10 py-4 text-11 fw-semibold">
                                            {{ strtoupper($group->status) }}
                                        </span>
                                    </td>
                                    <td class="py-16 px-16 text-end">
                                        <div class="d-inline-flex align-items-center gap-6">
                                            <a href="{{ route('admin.pkl-groups.edit', $group->id) }}" 
                                               class="btn btn-sm btn-outline-main rounded-circle p-6 w-32 h-32 flex-center"
                                               title="Edit Kelompok">
                                                <i class="ph ph-pencil-simple text-14"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.pkl-groups.destroy', $group->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelompok PKL ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-6 w-32 h-32 flex-center" title="Hapus Kelompok">
                                                    <i class="ph ph-trash text-14"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-40 text-gray-400">
                                        <i class="ph ph-users-three text-40 mb-12 d-block"></i>
                                        Belum ada data ploting kelompok PKL siswa.
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
