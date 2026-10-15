@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Kelola Pengguna</span></li>
                </ul>
            </div>
            <div class="flex-align gap-8 flex-wrap">
                <a href="{{ route('admin.students.import') }}" class="btn btn-outline-primary rounded-pill px-16 text-13">
                    <i class="ph ph-file-arrow-up me-4"></i> Import Siswa Excel
                </a>
                <button type="button" class="btn btn-main rounded-pill px-16 text-13" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="ph ph-user-plus me-4"></i> + Tambah Pengguna
                </button>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-16">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-16">
                <!-- Role Filter Tabs -->
                <div class="d-flex gap-8 flex-wrap">
                    <a href="{{ route('admin.users.index', ['role' => 'all', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $roleFilter === 'all' ? 'btn-main' : 'btn-outline-gray' }}">
                        Semua ({{ $counts['all'] }})
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'admin', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $roleFilter === 'admin' ? 'btn-main' : 'btn-outline-gray' }}">
                        Admin ({{ $counts['admin'] }})
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'guru', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $roleFilter === 'guru' ? 'btn-main' : 'btn-outline-gray' }}">
                        Guru ({{ $counts['guru'] }})
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'siswa', 'search' => $search]) }}" 
                       class="btn btn-sm rounded-pill px-16 {{ $roleFilter === 'siswa' ? 'btn-main' : 'btn-outline-gray' }}">
                        Siswa ({{ $counts['siswa'] }})
                    </a>
                </div>

                <!-- Search Input Form -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="w-350 position-relative">
                    <input type="hidden" name="role" value="{{ $roleFilter }}">
                    <input type="text" 
                           name="search" 
                           class="form-control ps-40 h-40 border-gray-100 rounded-pill text-13" 
                           placeholder="Cari nama, email, NIS, atau NIP..." 
                           value="{{ $search }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-400 d-flex pointer-event-none">
                        <i class="ph ph-magnifying-glass"></i>
                    </span>
                </form>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-gray-50 text-gray-600 text-13">
                            <tr>
                                <th class="ps-24">Pengguna</th>
                                <th>Kontak / Identitas</th>
                                <th>Peran (Role)</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th class="text-end pe-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                <tr>
                                    <td class="ps-24">
                                        <div class="d-flex align-items-center gap-12">
                                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-40 h-40 rounded-circle object-fit-cover shadow-xs border border-gray-100">
                                            <div>
                                                <h6 class="mb-0 text-14 fw-bold text-gray-900">{{ $u->name }}</h6>
                                                <span class="text-gray-400 text-12">{{ $u->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($u->student)
                                            <div>
                                                <span class="text-13 fw-semibold text-gray-800">NIS: {{ $u->student->nis }}</span>
                                                <span class="text-12 text-gray-500 d-block">{{ $u->student->schoolClass->name ?? 'Belum ada kelas' }}</span>
                                            </div>
                                        @elseif($u->teacher)
                                            <div>
                                                <span class="text-13 fw-semibold text-gray-800">NIP: {{ $u->teacher->teacher_number }}</span>
                                                <span class="text-12 text-gray-500 d-block">{{ $u->teacher->specialization ?? 'Guru RPL' }}</span>
                                            </div>
                                        @else
                                            <span class="text-13 text-gray-500">{{ $u->phone ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($u->role === 'admin')
                                            <span class="badge bg-danger-50 text-danger-700 text-11 px-10 py-4 text-uppercase">Admin</span>
                                        @elseif($u->role === 'guru')
                                            <span class="badge bg-primary-50 text-primary-700 text-11 px-10 py-4 text-uppercase">Guru RPL</span>
                                        @elseif($u->role === 'ketua_kelompok')
                                            <span class="badge bg-warning-50 text-warning-700 text-11 px-10 py-4 text-uppercase">Ketua Kelompok</span>
                                        @else
                                            <span class="badge bg-info-50 text-info-700 text-11 px-10 py-4 text-uppercase">Siswa RPL</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($u->status === 'active')
                                            <span class="badge bg-success-50 text-success-700 text-11 px-10 py-4">Aktif</span>
                                        @else
                                            <span class="badge bg-gray-100 text-gray-500 text-11 px-10 py-4">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-13 text-gray-500">{{ $u->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="text-end pe-24">
                                        <div class="d-flex justify-content-end align-items-center gap-6">
                                            <!-- Toggle status -->
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $u->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $u->status === 'active' ? 'btn-outline-secondary' : 'btn-outline-success' }} rounded-pill px-10 py-4 text-11" title="Ubah status">
                                                    <i class="ph {{ $u->status === 'active' ? 'ph-power' : 'ph-check-circle' }} me-2"></i>
                                                    {{ $u->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <!-- Edit button -->
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-action-circle" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}" title="Edit Pengguna">
                                                <i class="ph ph-pencil-simple"></i>
                                            </button>

                                            <!-- Delete button -->
                                            @if($u->id !== Auth::id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" onsubmit="return confirm('Hapus akun pengguna {{ $u->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-action-circle" title="Hapus Pengguna">
                                                        <i class="ph ph-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit User Modal -->
                                <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content rounded-16 border-0 shadow-lg">
                                            <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                                                <h5 class="modal-title fw-bold">Edit Pengguna: {{ $u->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.users.update', $u->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-24">
                                                    <div class="row g-3">
                                                        <div class="col-sm-6">
                                                            <label class="form-label text-13 fw-semibold">Nama Lengkap</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label text-13 fw-semibold">Email</label>
                                                            <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label text-13 fw-semibold">Peran (Role)</label>
                                                            <select name="role" class="form-select" required>
                                                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                                <option value="guru" {{ $u->role === 'guru' ? 'selected' : '' }}>Guru RPL</option>
                                                                <option value="ketua_kelompok" {{ $u->role === 'ketua_kelompok' ? 'selected' : '' }}>Ketua Kelompok</option>
                                                                <option value="siswa" {{ $u->role === 'siswa' ? 'selected' : '' }}>Siswa RPL</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label text-13 fw-semibold">Status Akun</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>Aktif</option>
                                                                <option value="inactive" {{ $u->status === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label text-13 fw-semibold">Nomor Telepon / WhatsApp</label>
                                                            <input type="text" name="phone" class="form-control" value="{{ $u->phone }}">
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label text-13 fw-semibold">Ganti Kata Sandi (Kosongkan bila tidak diubah)</label>
                                                            <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter">
                                                        </div>

                                                        @if(in_array($u->role, ['siswa', 'ketua_kelompok']))
                                                            <div class="col-sm-6">
                                                                <label class="form-label text-13 fw-semibold">NIS</label>
                                                                <input type="text" name="nis" class="form-control" value="{{ $u->student->nis ?? '' }}">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label class="form-label text-13 fw-semibold">Kelas</label>
                                                                <select name="class_id" class="form-select">
                                                                    <option value="">-- Pilih Kelas --</option>
                                                                    @foreach($classes as $c)
                                                                        <option value="{{ $c->id }}" {{ ($u->student->class_id ?? null) == $c->id ? 'selected' : '' }}>
                                                                            {{ $c->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @elseif($u->role === 'guru')
                                                            <div class="col-sm-6">
                                                                <label class="form-label text-13 fw-semibold">NIP / Kode Guru</label>
                                                                <input type="text" name="teacher_number" class="form-control" value="{{ $u->teacher->teacher_number ?? '' }}">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label class="form-label text-13 fw-semibold">Bidang Spesialisasi</label>
                                                                <input type="text" name="specialization" class="form-control" value="{{ $u->teacher->specialization ?? '' }}">
                                                            </div>
                                                        @endif
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
                                    <td colspan="6" class="text-center py-40 text-gray-400">
                                        <i class="ph ph-users-three text-36 mb-8 d-block"></i>
                                        Tidak ada data pengguna ditemukan dengan kriteria pencarian ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="p-20 border-top border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-16 border-0 shadow-lg">
            <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                <h5 class="modal-title fw-bold">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body p-24">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-13 fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap pengguna" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-13 fw-semibold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@sekolah.sch.id" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-13 fw-semibold">Peran (Role)</label>
                            <select name="role" id="newUserRole" class="form-select" onchange="toggleRoleFields(this.value)" required>
                                <option value="siswa" selected>Siswa RPL</option>
                                <option value="ketua_kelompok">Ketua Kelompok (Siswa)</option>
                                <option value="guru">Guru Pembimbing RPL</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-13 fw-semibold">Kata Sandi Default</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" value="password" required>
                        </div>

                        <!-- Student specific fields -->
                        <div id="studentFields" class="col-12">
                            <div class="row g-3 p-16 rounded-12 bg-main-50 border border-primary-100">
                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Nomor Induk Siswa (NIS)</label>
                                    <input type="text" name="nis" id="inputNis" class="form-control" placeholder="Contoh: 2026120101">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Kelas</label>
                                    <select name="class_id" id="inputClass" class="form-select">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }} (Kelas {{ $c->grade_level }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Jenis Kelamin</label>
                                    <select name="gender" class="form-select">
                                        <option value="L">Laki-Laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Alamat</label>
                                    <input type="text" name="address" class="form-control" placeholder="Alamat domisili">
                                </div>
                            </div>
                        </div>

                        <!-- Teacher specific fields -->
                        <div id="teacherFields" class="col-12 d-none">
                            <div class="row g-3 p-16 rounded-12 bg-success-50 border border-success-100">
                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">NIP / NUPTK / Kode Guru</label>
                                    <input type="text" name="teacher_number" id="inputTeacherNumber" class="form-control" placeholder="Contoh: 198507122010011002">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Mata Pelajaran / Spesialisasi</label>
                                    <input type="text" name="specialization" class="form-control" placeholder="Contoh: Pemrograman Web & PjBL">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-gray-100 py-12 px-24">
                    <button type="button" class="btn btn-outline-gray rounded-pill px-16" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-main rounded-pill px-20">Tambah Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleRoleFields(role) {
        const studentFields = document.getElementById('studentFields');
        const teacherFields = document.getElementById('teacherFields');
        const inputNis = document.getElementById('inputNis');
        const inputClass = document.getElementById('inputClass');
        const inputTeacherNumber = document.getElementById('inputTeacherNumber');

        if (role === 'siswa' || role === 'ketua_kelompok') {
            studentFields.classList.remove('d-none');
            teacherFields.classList.add('d-none');
            inputNis.required = true;
            inputClass.required = true;
            inputTeacherNumber.required = false;
        } else if (role === 'guru') {
            studentFields.classList.add('d-none');
            teacherFields.classList.remove('d-none');
            inputNis.required = false;
            inputClass.required = false;
            inputTeacherNumber.required = true;
        } else {
            studentFields.classList.add('d-none');
            teacherFields.classList.add('d-none');
            inputNis.required = false;
            inputClass.required = false;
            inputTeacherNumber.required = false;
        }
    }
</script>
@endpush
