@extends('layouts.app')

@section('title', 'Manajemen Kelas & Tahun Ajaran')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Kelas & Tahun Ajaran</span></li>
                </ul>
            </div>
            <div class="flex-align gap-8 flex-wrap">
                <button type="button" class="btn btn-outline-primary rounded-pill px-16 text-13" data-bs-toggle="modal" data-bs-target="#addYearModal">
                    <i class="ph ph-calendar-plus me-4"></i> + Tahun Ajaran
                </button>
                <button type="button" class="btn btn-main rounded-pill px-16 text-13" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    <i class="ph ph-plus-circle me-4"></i> + Tambah Kelas
                </button>
            </div>
        </div>
    </div>

    <!-- Academic Year Cards -->
    <div class="col-lg-4">
        <div class="card border border-gray-100 rounded-16 h-100">
            <div class="card-body p-24">
                <div class="flex-between mb-16 pb-12 border-bottom border-gray-100">
                    <div>
                        <h5 class="mb-2 fw-bold text-gray-900">Tahun Ajaran</h5>
                        <span class="text-12 text-gray-500">Tahun Ajaran Aktif: <strong class="text-main-600">{{ $activeYear->name ?? 'Belum Diatur' }}</strong></span>
                    </div>
                </div>

                <div class="d-flex flex-column gap-12">
                    @forelse($academicYears as $year)
                        <div class="p-16 rounded-12 border {{ $year->is_active ? 'border-primary-600 bg-main-50' : 'border-gray-100 bg-white' }} flex-between">
                            <div>
                                <h6 class="mb-4 text-14 fw-bold text-gray-900">{{ $year->name }}</h6>
                                <span class="text-12 text-gray-500">{{ $year->classes_count }} Rombel Kelas</span>
                            </div>
                            <div class="text-end">
                                @if($year->is_active)
                                    <span class="badge bg-main-600 text-white rounded-pill px-12 py-4 text-11">AKTIF</span>
                                @else
                                    <form method="POST" action="{{ route('admin.academic-years.activate', $year->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-gray rounded-pill px-12 py-2 text-11">
                                            Aktifkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-24 text-gray-400 text-13">Belum ada tahun ajaran.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Classes List Table -->
    <div class="col-lg-8">
        <div class="card border border-gray-100 rounded-16 overflow-hidden">
            <div class="card-body p-24">
                <div class="flex-between mb-16 pb-12 border-bottom border-gray-100">
                    <div>
                        <h5 class="mb-2 fw-bold text-gray-900">Daftar Rombongan Belajar (Kelas)</h5>
                        <span class="text-12 text-gray-500">Total: {{ $classes->count() }} Kelas terdaftar</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-gray-50 text-gray-600 text-13">
                            <tr>
                                <th class="ps-16">Nama Kelas</th>
                                <th>Tingkat</th>
                                <th>Tahun Ajaran</th>
                                <th>Jumlah Siswa</th>
                                <th class="text-end pe-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $c)
                                <tr>
                                    <td class="ps-16">
                                        <div class="d-flex align-items-center gap-8">
                                            <span class="w-32 h-32 rounded-circle bg-main-50 text-main-600 flex-center fw-bold text-12">
                                                <i class="ph ph-chalkboard"></i>
                                            </span>
                                            <span class="fw-bold text-14 text-gray-900">{{ $c->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-50 text-primary-700 text-12">Kelas {{ $c->grade_level }}</span>
                                    </td>
                                    <td>
                                        <span class="text-13 text-gray-700">{{ $c->academicYear->name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-gray-100 text-gray-800 text-12 px-10 py-4">
                                            <i class="ph ph-users me-2"></i> {{ $c->students_count }} Siswa
                                        </span>
                                    </td>
                                    <td class="text-end pe-16">
                                        <div class="d-flex justify-content-end align-items-center gap-6">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-warning btn-action-circle"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editClassModal{{ $c->id }}"
                                                    title="Edit Kelas">
                                                <i class="ph ph-pencil-simple"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.classes.destroy', $c->id) }}" onsubmit="return confirm('Hapus kelas {{ $c->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action-circle" title="Hapus Kelas">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Class Modal -->
                                <div class="modal fade" id="editClassModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-16 border-0 shadow-lg">
                                            <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                                                <h5 class="modal-title fw-bold">Edit Kelas {{ $c->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.classes.update', $c->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-24">
                                                    <div class="mb-16">
                                                        <label class="form-label text-13 fw-semibold">Nama Kelas</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $c->name }}" required>
                                                    </div>
                                                    <div class="mb-16">
                                                        <label class="form-label text-13 fw-semibold">Tingkat Kelas</label>
                                                        <select name="grade_level" class="form-select" required>
                                                            <option value="X" {{ $c->grade_level == 'X' ? 'selected' : '' }}>Kelas X (Sepuluh)</option>
                                                            <option value="XI" {{ $c->grade_level == 'XI' ? 'selected' : '' }}>Kelas XI (Sebelas)</option>
                                                            <option value="XII" {{ $c->grade_level == 'XII' ? 'selected' : '' }}>Kelas XII (Dua Belas - PjBL & PKL)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-16">
                                                        <label class="form-label text-13 fw-semibold">Tahun Ajaran</label>
                                                        <select name="academic_year_id" class="form-select" required>
                                                            @foreach($academicYears as $year)
                                                                <option value="{{ $year->id }}" {{ $c->academic_year_id == $year->id ? 'selected' : '' }}>
                                                                    {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top border-gray-100 py-12 px-24">
                                                    <button type="button" class="btn btn-outline-gray rounded-pill px-16" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-main rounded-pill px-20">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-32 text-gray-400">
                                        <i class="ph ph-chalkboard text-36 mb-8 d-block"></i>
                                        Belum ada kelas terdaftar. Tambahkan kelas baru melalui tombol di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-16 border-0 shadow-lg">
            <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                <h5 class="modal-title fw-bold">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.classes.store') }}">
                @csrf
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: XII RPL 1" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold">Tingkat Kelas</label>
                        <select name="grade_level" class="form-select" required>
                            <option value="XII">Kelas XII (Dua Belas - PjBL & PKL)</option>
                            <option value="XI">Kelas XI (Sebelas)</option>
                            <option value="X">Kelas X (Sepuluh)</option>
                        </select>
                    </div>
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold">Tahun Ajaran</label>
                        <select name="academic_year_id" class="form-select" required>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-gray-100 py-12 px-24">
                    <button type="button" class="btn btn-outline-gray rounded-pill px-16" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-main rounded-pill px-20">Tambah Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Academic Year Modal -->
<div class="modal fade" id="addYearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-16 border-0 shadow-lg">
            <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                <h5 class="modal-title fw-bold">Tambah Tahun Ajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.academic-years.store') }}">
                @csrf
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold">Nama Tahun Ajaran</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: 2026/2027" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                        <label class="form-check-label text-13" for="is_active">Set sebagai tahun ajaran aktif sekarang</label>
                    </div>
                </div>
                <div class="modal-footer border-top border-gray-100 py-12 px-24">
                    <button type="button" class="btn btn-outline-gray rounded-pill px-16" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-main rounded-pill px-20">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
