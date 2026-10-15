@extends('layouts.app')

@section('title', 'Edit Ploting Kelompok PKL')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="{{ route('admin.pkl-groups.index') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Ploting Kelompok PKL</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Edit {{ $group->name }}</span></li>
                </ul>
            </div>
            <a href="{{ route('admin.pkl-groups.index') }}" class="btn btn-outline-gray rounded-pill px-16 text-13">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16">
            <div class="card-body p-24">
                <form method="POST" action="{{ route('admin.pkl-groups.update', $group->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row gy-24">
                        <!-- Left: Info Kelompok & Penempatan -->
                        <div class="col-lg-6 border-end-lg border-gray-100 pe-lg-24">
                            <h5 class="text-15 fw-bold text-gray-900 mb-16 pb-8 border-bottom border-gray-100">
                                1. Detail Penempatan Industri & Guru
                            </h5>

                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold text-gray-700">Nama Kelompok PKL <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-8 text-13 @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $group->name) }}" required>
                                @error('name')
                                    <div class="text-danger text-12 mt-4">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold text-gray-700">Mitra Industri DU/DI <span class="text-danger">*</span></label>
                                <select name="company_id" class="form-select rounded-8 text-13 @error('company_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Perusahaan Mitra --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id', $group->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }} ({{ $company->city ?? 'Kota tidak diset' }} &bull; {{ $company->sector ?? 'IT' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold text-gray-700">Guru Pembimbing PKL <span class="text-danger">*</span></label>
                                <select name="mentor_teacher_id" class="form-select rounded-8 text-13 @error('mentor_teacher_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Guru Pembimbing --</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('mentor_teacher_id', $group->mentor_teacher_id) == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }} (NIP: {{ $teacher->teacher->teacher_number ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row g-12 mb-16">
                                <div class="col-6">
                                    <label class="form-label text-13 fw-semibold text-gray-700">Tanggal Mulai PKL</label>
                                    <input type="date" name="start_date" class="form-control rounded-8 text-13" 
                                           value="{{ old('start_date', $group->start_date?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-13 fw-semibold text-gray-700">Tanggal Selesai PKL</label>
                                    <input type="date" name="end_date" class="form-control rounded-8 text-13" 
                                           value="{{ old('end_date', $group->end_date?->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold text-gray-700">Catatan / Keterangan Penempatan</label>
                                <textarea name="notes" rows="3" class="form-control rounded-8 text-13">{{ old('notes', $group->notes) }}</textarea>
                            </div>
                        </div>

                        <!-- Right: Siswa Anggota Kelompok -->
                        <div class="col-lg-6 ps-lg-24">
                            <h5 class="text-15 fw-bold text-gray-900 mb-16 pb-8 border-bottom border-gray-100">
                                2. Pilih Siswa Anggota Kelompok
                            </h5>

                            <div class="p-12 rounded-10 bg-main-50 mb-16 border border-main-200">
                                <p class="text-12 text-gray-700 mb-0">
                                    <i class="ph ph-info me-2 text-main-600"></i>
                                    Perbarui siswa yang masuk ke kelompok PKL ini dan pilih satu sebagai <strong>Ketua Kelompok</strong>.
                                </p>
                            </div>

                            @error('student_ids')
                                <div class="alert alert-danger py-8 px-12 text-13 mb-16">{{ $message }}</div>
                            @enderror

                            <!-- List Siswa -->
                            <div class="overflow-y-auto p-12 rounded-12 border border-gray-100 bg-gray-50" style="max-height: 400px;">
                                @forelse($students as $st)
                                    @php
                                        $isChecked = in_array($st->id, old('student_ids', $selectedStudentIds));
                                        $isLeader = old('leader_id', $currentLeaderId) == $st->id;
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-between p-10 rounded-10 bg-white mb-8 border border-gray-100 gap-12">
                                        <div class="form-check mb-0 flex-grow-1 text-truncate">
                                            <input class="form-check-input student-checkbox" 
                                                   type="checkbox" 
                                                   name="student_ids[]" 
                                                   value="{{ $st->id }}" 
                                                   id="st{{ $st->id }}"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   onchange="handleStudentCheck({{ $st->id }})">
                                            <label class="form-check-label text-13 fw-semibold text-gray-800 ms-6 text-truncate" for="st{{ $st->id }}">
                                                <span class="d-block text-gray-900">{{ $st->user->name }}</span>
                                                <small class="text-gray-400 d-block fw-normal">
                                                    NIS: {{ $st->nis }} &bull; {{ $st->schoolClass->name ?? 'Kelas -' }}
                                                </small>
                                            </label>
                                        </div>
                                        <div class="leader-wrapper flex-shrink-0" id="leaderWrap{{ $st->id }}" style="display: {{ $isChecked ? 'block' : 'none' }};">
                                            <div class="d-flex align-items-center bg-warning-50 px-10 py-4 rounded-pill border border-warning-200" title="Pilih sebagai Ketua Kelompok">
                                                <input class="form-check-input leader-radio me-6 m-0" 
                                                       type="radio" 
                                                       name="leader_id" 
                                                       value="{{ $st->id }}" 
                                                       id="leader{{ $st->id }}"
                                                       {{ $isLeader ? 'checked' : '' }}>
                                                <label class="form-check-label text-12 text-warning-800 fw-bold text-nowrap cursor-pointer m-0" for="leader{{ $st->id }}">
                                                    <i class="ph ph-crown me-2"></i>Ketua
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-20 text-gray-400 text-13">Belum ada siswa terdaftar.</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="col-12 pt-16 border-top border-gray-100 text-end">
                            <a href="{{ route('admin.pkl-groups.index') }}" class="btn btn-outline-gray rounded-pill px-20 me-8 text-13">Batal</a>
                            <button type="submit" class="btn btn-main rounded-pill px-24 text-13">
                                <i class="ph ph-check me-4"></i> Simpan Perubahan Ploting
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function handleStudentCheck(studentId) {
        const checkbox = document.getElementById('st' + studentId);
        const leaderWrap = document.getElementById('leaderWrap' + studentId);
        const leaderRadio = document.getElementById('leader' + studentId);

        if (checkbox.checked) {
            leaderWrap.style.display = 'block';
            const checkedLeaders = document.querySelectorAll('.leader-radio:checked');
            if (checkedLeaders.length === 0) {
                leaderRadio.checked = true;
            }
        } else {
            leaderWrap.style.display = 'none';
            if (leaderRadio.checked) {
                leaderRadio.checked = false;
                const otherCheckbox = document.querySelector('.student-checkbox:checked');
                if (otherCheckbox) {
                    const otherId = otherCheckbox.value;
                    const otherRadio = document.getElementById('leader' + otherId);
                    if (otherRadio) otherRadio.checked = true;
                }
            }
        }
    }
</script>
@endpush
