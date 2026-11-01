@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-16">
        <div>
            <div class="breadcrumb mb-8">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover-text-main-600 text-14">Beranda</a>
                <span class="text-gray-400 mx-8">/</span>
                <span class="text-gray-500 text-14">Monitoring</span>
                <span class="text-gray-400 mx-8">/</span>
                <span class="text-main-600 text-14 fw-semibold">Matriks Progres Siswa</span>
            </div>
            <h4 class="mb-4 text-22 fw-bold text-gray-800">Matriks Capaian Siswa (PKL & PjBL)</h4>
            <p class="text-gray-500 text-14 mb-0">Pantau progres laporan bab PKL kelas XII dan keterlibatan proyek PjBL dalam satu tampilan komprehensif.</p>
        </div>
        <div class="flex-align gap-12 flex-wrap">
            <a href="{{ route('admin.student-progress.index') }}" class="btn btn-outline-main rounded-pill px-20 py-10 flex-align gap-8 text-14 fw-medium">
                <i class="ph ph-folder-open text-18"></i>
                <span>Kelola Berkas & Upload</span>
            </a>
            <a href="{{ route('admin.traffic.index') }}" class="btn btn-outline-secondary rounded-pill px-20 py-10 flex-align gap-8 text-14 fw-medium">
                <i class="ph ph-chart-line-up text-18"></i>
                <span>Monitoring Trafik & Storage</span>
            </a>
        </div>
    </div>

    <!-- Filter & Legend Card -->
    <div class="card border border-gray-100 rounded-16 bg-white p-20 shadow-none mb-20">
        <div class="flex-between flex-wrap gap-16">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.student-progress.matrix') }}" class="d-flex align-items-center flex-wrap gap-12">
                <select name="class_id" class="form-select rounded-pill text-13 border-gray-200 py-8 px-16" onchange="this.form.submit()" style="min-width: 170px;">
                    <option value="">Semua Kelas / Rombel</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <div class="position-relative" style="min-width: 240px;">
                    <input type="text" name="search" value="{{ $search }}" class="form-control rounded-pill text-13 border-gray-200 ps-36 pe-16 py-8" placeholder="Cari nama siswa / NIS...">
                    <span class="position-absolute top-50 translate-middle-y ms-12 text-gray-400 text-16 d-flex">
                        <i class="ph ph-magnifying-glass"></i>
                    </span>
                </div>

                @if($search || $classId)
                    <a href="{{ route('admin.student-progress.matrix') }}" class="btn btn-outline-secondary rounded-pill px-14 py-8 text-12 flex-align gap-4">
                        <i class="ph ph-arrow-counter-clockwise"></i> Reset
                    </a>
                @endif
            </form>

            <!-- Legend Status Indicators -->
            <div class="flex-align gap-12 flex-wrap">
                <span class="text-12 text-gray-500 fw-medium">Keterangan Bab:</span>
                <span class="badge bg-success-50 text-success-700 border border-success-200 px-8 py-4 rounded-pill text-11 fw-semibold flex-align gap-4">
                    <i class="ph ph-check-circle text-12"></i> Disetujui
                </span>
                <span class="badge bg-warning-50 text-warning-700 border border-warning-200 px-8 py-4 rounded-pill text-11 fw-semibold flex-align gap-4">
                    <i class="ph ph-clock text-12"></i> Menunggu
                </span>
                <span class="badge bg-danger-50 text-danger-700 border border-danger-200 px-8 py-4 rounded-pill text-11 fw-semibold flex-align gap-4">
                    <i class="ph ph-warning-circle text-12"></i> Revisi
                </span>
                <span class="badge bg-gray-100 text-gray-500 border border-gray-200 px-8 py-4 rounded-pill text-11 fw-semibold flex-align gap-4">
                    <i class="ph ph-minus text-12"></i> Belum Ada
                </span>
            </div>
        </div>
    </div>

    <!-- Matrix Table Card -->
    <div class="card border border-gray-100 rounded-16 bg-white p-24 shadow-none">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-gray-50 text-gray-500 text-12 text-uppercase">
                    <tr>
                        <th class="py-12 px-16 rounded-start-8">Siswa & Kelas</th>
                        <th class="py-12 px-16">Mitra PKL & Pembimbing</th>
                        <th class="py-12 px-8 text-center">Bab 1</th>
                        <th class="py-12 px-8 text-center">Bab 2</th>
                        <th class="py-12 px-8 text-center">Bab 3</th>
                        <th class="py-12 px-8 text-center">Bab 4</th>
                        <th class="py-12 px-8 text-center">Bab 5</th>
                        <th class="py-12 px-16 text-center">Proyek PjBL</th>
                        <th class="py-12 px-16 text-center">Logbook</th>
                        <th class="py-12 px-16 text-end rounded-end-8">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        @php
                            $user = $st->user;
                            $report = $st->pklReport;
                            $chapters = $report ? $report->chapters->keyBy('chapter_number') : collect();
                            $group = $st->pklGroups->first();
                            $company = $group?->company;
                            $teacher = $group?->mentorTeacher;
                            $pjblCount = $st->projects->count();
                            $submissionCount = $st->projects->flatMap->submissions->count();
                            $logCount = $st->projectLogs->count();
                        @endphp
                        <tr class="border-bottom border-gray-50 text-13">
                            <!-- Student Info -->
                            <td class="py-12 px-16">
                                <div class="flex-align gap-10">
                                    <div class="w-36 h-36 rounded-circle bg-main-100 text-main-700 flex-center flex-shrink-0 text-13 fw-bold">
                                        {{ strtoupper(substr($user->name ?? 'Siswa', 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="d-block fw-semibold text-gray-800 text-14">{{ $user->name ?? 'Siswa' }}</span>
                                        <span class="text-gray-400 text-12">NIS: {{ $st->nis }} &bull; {{ $st->schoolClass->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- PKL Info -->
                            <td class="py-12 px-16">
                                @if($company)
                                    <span class="d-block fw-semibold text-gray-800 text-13">{{ $company->name }}</span>
                                    <span class="text-gray-400 text-12">
                                        <i class="ph ph-chalkboard-teacher me-2"></i>{{ $teacher->name ?? 'Belum Ditentukan' }}
                                    </span>
                                @else
                                    <span class="text-gray-400 fst-italic text-12">Belum ada plotting PKL</span>
                                @endif
                            </td>

                            <!-- Chapters 1 to 5 status -->
                            @for($ch = 1; $ch <= 5; $ch++)
                                @php
                                    $chItem = $chapters->get($ch);
                                    $chStatus = $chItem ? strtolower($chItem->status) : null;
                                @endphp
                                <td class="py-12 px-8 text-center">
                                    @if($chItem)
                                        @if($chStatus === 'approved')
                                            <a href="{{ route('admin.student-progress.download', ['type' => 'pkl', 'id' => $chItem->id]) }}" 
                                               class="badge bg-success-50 text-success-700 border border-success-200 px-8 py-5 rounded-pill text-11 fw-semibold d-inline-flex flex-align gap-2 text-decoration-none" 
                                               title="Bab {{ $ch }}: Disetujui (Klik unduh)">
                                                <i class="ph ph-check text-11"></i> Bab {{ $ch }}
                                            </a>
                                        @elseif($chStatus === 'revision')
                                            <a href="{{ route('admin.student-progress.download', ['type' => 'pkl', 'id' => $chItem->id]) }}" 
                                               class="badge bg-danger-50 text-danger-700 border border-danger-200 px-8 py-5 rounded-pill text-11 fw-semibold d-inline-flex flex-align gap-2 text-decoration-none" 
                                               title="Bab {{ $ch }}: Revisi (Klik unduh)">
                                                <i class="ph ph-warning text-11"></i> Bab {{ $ch }}
                                            </a>
                                        @else
                                            <a href="{{ route('admin.student-progress.download', ['type' => 'pkl', 'id' => $chItem->id]) }}" 
                                               class="badge bg-warning-50 text-warning-700 border border-warning-200 px-8 py-5 rounded-pill text-11 fw-semibold d-inline-flex flex-align gap-2 text-decoration-none" 
                                               title="Bab {{ $ch }}: Menunggu Review (Klik unduh)">
                                                <i class="ph ph-clock text-11"></i> Bab {{ $ch }}
                                            </a>
                                        @endif
                                    @else
                                        <span class="badge bg-gray-50 text-gray-400 border border-gray-100 px-8 py-5 rounded-pill text-11 fw-medium" title="Bab {{ $ch }}: Belum diunggah">
                                            -
                                        </span>
                                    @endif
                                </td>
                            @endfor

                            <!-- PjBL Submissions -->
                            <td class="py-12 px-16 text-center">
                                <span class="badge bg-primary-50 text-primary-700 border border-primary-200 px-10 py-5 rounded-pill text-12 fw-semibold">
                                    {{ $submissionCount }} Submisi
                                </span>
                            </td>

                            <!-- Logbook Entries -->
                            <td class="py-12 px-16 text-center">
                                <span class="badge bg-info-50 text-info-700 border border-info-200 px-10 py-5 rounded-pill text-12 fw-semibold">
                                    {{ $logCount }} Jurnal
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-12 px-16 text-end">
                                <a href="{{ route('admin.student-progress.index', ['search' => $st->nis]) }}" 
                                   class="btn btn-outline-main rounded-pill px-12 py-6 text-12 flex-align gap-4 d-inline-flex" 
                                   title="Lihat seluruh file siswa ini">
                                    <i class="ph ph-folder-open text-14"></i> Berkas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-40 text-gray-400">
                                <i class="ph ph-users-three text-48 d-block mb-12 text-gray-300"></i>
                                <span class="d-block text-14 fw-medium text-gray-600">Tidak ada data siswa ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
            <div class="mt-20 pt-16 border-top border-gray-100 flex-between flex-wrap gap-12">
                <span class="text-13 text-gray-500">
                    Menampilkan {{ $students->firstItem() }} hingga {{ $students->lastItem() }} dari {{ $students->total() }} siswa
                </span>
                <div>
                    {{ $students->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
