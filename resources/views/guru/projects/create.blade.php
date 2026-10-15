@extends('layouts.app')

@section('title', 'Buat Proyek PjBL Baru')

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
                    <li><span class="text-main-600 fw-medium text-14">Buat Proyek</span></li>
                </ul>
            </div>
            <a href="{{ route('guru.projects.index') }}" class="btn btn-outline-gray rounded-pill px-16 text-13">
                <i class="ph ph-arrow-left me-4"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-24">
            <h4 class="mb-4 fw-bold text-gray-900">Formulir Proyek PjBL Siswa</h4>
            <p class="text-gray-500 text-14 mb-24">Tentukan detail penugasan, instruksi, rubrik penilaian, serta anggota kelompok siswa.</p>

            <form method="POST" action="{{ route('guru.projects.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <!-- Project Details Section -->
                    <div class="col-xl-7 col-lg-6 pe-lg-24 border-end border-gray-100">
                        <h5 class="text-15 fw-bold text-gray-800 mb-16 pb-8 border-bottom border-gray-100">1. Informasi Pokok Proyek</h5>

                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Judul Proyek PjBL <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Contoh: Aplikasi Sistem Kasir Restoran Berbasis Web & PWA" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-16">
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold">Jenis Penugasan <span class="text-danger">*</span></label>
                                <select name="type" id="projectType" class="form-select" onchange="handleTypeChange(this.value)" required>
                                    <option value="group" {{ old('type') === 'group' ? 'selected' : '' }}>Proyek Kelompok (PjBL Group)</option>
                                    <option value="individual" {{ old('type') === 'individual' ? 'selected' : '' }}>Proyek Individu (Mandiri)</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold">Status Awal</label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Langsung Aktifkan (Bisa Diakses Siswa)</option>
                                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold">Batas Waktu Pengumpulan (Deadline)</label>
                                <input type="datetime-local" name="deadline" class="form-control" value="{{ old('deadline') }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold">File Lampiran / Modul (Opsional)</label>
                                <input type="file" name="attachment" class="form-control" accept=".pdf,.zip,.rar,.doc,.docx,.ppt,.pptx">
                                <small class="text-gray-400 text-11">Maks. 10MB (PDF, DOCX, ZIP)</small>
                            </div>
                        </div>

                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Deskripsi Singkat Proyek <span class="text-danger">*</span></label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Jelaskan ringkasan proyek yang harus dikerjakan siswa..." required>{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Tujuan Pembelajaran (Goals)</label>
                            <textarea name="goals" rows="2" class="form-control" placeholder="1. Mampu merancang basis data relasional&#10;2. Menerapkan clean code MVC...">{{ old('goals') }}</textarea>
                        </div>

                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Instruksi & Alur Kerja Siswa</label>
                            <textarea name="instructions" rows="3" class="form-control" placeholder="Tahap 1: Analisis kebutuhan&#10;Tahap 2: Desain wireframe UI&#10;Tahap 3: Implementasi kode & pengujian...">{{ old('instructions') }}</textarea>
                        </div>

                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Rubrik Penilaian (Kriteria Skor)</label>
                            <textarea name="rubric" rows="2" class="form-control" placeholder="Kerapian kode (25%), Kelengkapan fitur (35%), Desain UI/UX (20%), Kerjasama tim (20%)...">{{ old('rubric') }}</textarea>
                        </div>
                    </div>

                    <!-- Student Assignment Section -->
                    <div class="col-xl-5 col-lg-6 ps-lg-24">
                        <div class="flex-between mb-16 pb-8 border-bottom border-gray-100">
                            <h5 class="text-15 fw-bold text-gray-800 mb-0">2. Penugasan Siswa & Ketua</h5>
                        </div>

                        <div class="p-16 rounded-12 bg-main-50 mb-16 border border-primary-100">
                            <p class="text-13 text-gray-700 mb-0">
                                Centang siswa yang masuk ke dalam proyek ini. Pada mode <strong>Kelompok</strong>, pilih satu siswa sebagai <strong>Ketua Kelompok</strong>.
                            </p>
                        </div>

                        <!-- Class filter for picking students -->
                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Filter Berdasarkan Kelas</label>
                            <select id="classFilter" class="form-select" onchange="filterStudentsByClass(this.value)">
                                <option value="all">-- Tampilkan Semua Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->students->count() }} Siswa)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Students List with Checkbox & Leader Radio -->
                        <div class="max-h-400 overflow-y-auto scroll-sm p-12 rounded-12 border border-gray-100 bg-gray-50">
                            @forelse($classes as $classItem)
                                <div class="class-group-section mb-16" data-class-id="{{ $classItem->id }}">
                                    <div class="text-12 fw-bold text-primary-700 mb-8 border-bottom border-gray-200 pb-4">
                                        {{ $classItem->name }}
                                    </div>
                                    @forelse($classItem->students as $st)
                                        <div class="d-flex align-items-center justify-content-between p-10 rounded-10 bg-white mb-8 border border-gray-100 gap-12">
                                            <div class="form-check mb-0 flex-grow-1 text-truncate">
                                                <input class="form-check-input student-checkbox" 
                                                       type="checkbox" 
                                                       name="student_ids[]" 
                                                       value="{{ $st->id }}" 
                                                       id="student{{ $st->id }}"
                                                       onchange="toggleLeaderRadio({{ $st->id }})">
                                                <label class="form-check-label text-13 fw-semibold text-gray-800 ms-6 text-truncate" for="student{{ $st->id }}">
                                                    <span class="d-block text-gray-900">{{ $st->user->name }}</span>
                                                    <small class="text-gray-400 d-block fw-normal">NIS: {{ $st->nis }}</small>
                                                </label>
                                            </div>
                                            <div class="leader-radio-container flex-shrink-0" id="leaderContainer{{ $st->id }}">
                                                <div class="d-flex align-items-center bg-warning-50 px-10 py-4 rounded-pill border border-warning-200" title="Jadikan Ketua Kelompok">
                                                    <input class="form-check-input leader-radio me-6 m-0" 
                                                           type="radio" 
                                                           name="leader_student_id" 
                                                           value="{{ $st->id }}" 
                                                           id="leader{{ $st->id }}"
                                                           disabled>
                                                    <label class="form-check-label text-12 text-warning-800 fw-bold text-nowrap cursor-pointer m-0" for="leader{{ $st->id }}">
                                                        <i class="ph ph-crown me-2"></i>Ketua
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-12 text-gray-400 p-4">Tidak ada siswa di kelas ini.</div>
                                    @endforelse
                                </div>
                            @empty
                                <div class="text-center py-24 text-gray-400 text-13">Belum ada kelas & siswa terdaftar.</div>
                            @endforelse
                        </div>

                        <div class="mt-24">
                            <button type="submit" class="btn btn-main rounded-pill w-100 py-12">
                                <i class="ph ph-check-circle me-4"></i> Buat & Terbitkan Proyek PjBL
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleLeaderRadio(studentId) {
        const checkbox = document.getElementById('student' + studentId);
        const radio = document.getElementById('leader' + studentId);
        if (checkbox.checked) {
            radio.disabled = false;
            const checkedLeaders = document.querySelectorAll('.leader-radio:checked');
            if (checkedLeaders.length === 0) {
                radio.checked = true;
            }
        } else {
            radio.disabled = true;
            if (radio.checked) {
                radio.checked = false;
                const nextChecked = document.querySelector('.student-checkbox:checked');
                if (nextChecked) {
                    const nextRadio = document.getElementById('leader' + nextChecked.value);
                    if (nextRadio) nextRadio.checked = true;
                }
            }
        }
    }

    function filterStudentsByClass(classId) {
        const groups = document.querySelectorAll('.class-group-section');
        groups.forEach(group => {
            if (classId === 'all' || group.getAttribute('data-class-id') === classId) {
                group.style.display = 'block';
            } else {
                group.style.display = 'none';
            }
        });
    }

    function handleTypeChange(type) {
        const leaderContainers = document.querySelectorAll('.leader-radio-container');
        leaderContainers.forEach(el => {
            el.style.display = (type === 'group') ? 'block' : 'none';
        });
    }
</script>
@endpush
