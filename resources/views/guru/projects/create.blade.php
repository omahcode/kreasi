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
                            <button type="button" class="btn btn-sm btn-outline-main rounded-pill px-12 py-4 text-12 fw-semibold" data-bs-toggle="modal" data-bs-target="#autoGroupModal">
                                <i class="ph ph-magic-wand me-2"></i>Bagi Otomatis
                            </button>
                        </div>

                        <div class="p-16 rounded-12 bg-main-50 mb-16 border border-primary-100">
                            <p class="text-13 text-gray-700 mb-0">
                                Centang siswa untuk proyek ini atau gunakan fitur <strong>Bagi Otomatis</strong> untuk mengacak anggota kelompok dan ketua secara instan.
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
<!-- Modal Auto Grouping -->
<div class="modal fade" id="autoGroupModal" tabindex="-1" aria-labelledby="autoGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-16 border-0 shadow">
            <div class="modal-header border-bottom border-gray-100 p-20">
                <div class="d-flex align-items-center gap-12">
                    <span class="w-40 h-40 rounded-circle bg-main-50 text-main-600 flex-center text-20 shrink-0">
                        <i class="ph ph-magic-wand"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-gray-900 mb-2" id="autoGroupModalLabel">Pembagian Kelompok Otomatis</h5>
                        <p class="text-gray-500 text-12 mb-0">Bagi siswa dalam satu kelas ke kelompok-kelompok secara seimbang dan cepat.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <form id="autoGroupForm" onsubmit="event.preventDefault(); runAutoGroup();">
                    <div class="row g-3 mb-20">
                        <div class="col-md-6">
                            <label class="form-label text-13 fw-semibold">Pilih Kelas Sasaran <span class="text-danger">*</span></label>
                            <select id="autoGroupClassId" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" data-count="{{ $c->students->count() }}">{{ $c->name }} ({{ $c->students->count() }} Siswa)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-13 fw-semibold">Metode Pembagian <span class="text-danger">*</span></label>
                            <select id="autoGroupMethod" class="form-select" onchange="updateMethodLabel(this.value)">
                                <option value="members_per_group">Jumlah Anggota per Kelompok</option>
                                <option value="total_groups">Target Total Jumlah Kelompok</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label id="autoGroupSizeLabel" class="form-label text-13 fw-semibold">Jumlah Siswa per Kelompok <span class="text-danger">*</span></label>
                            <input type="number" id="autoGroupSize" class="form-control" value="4" min="1" max="50" required>
                            <small class="text-gray-400 text-11" id="autoGroupHint">Contoh: Diisi 4, maka 32 siswa akan dibagi menjadi 8 kelompok.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-13 fw-semibold">Opsi Tambahan</label>
                            <div class="d-flex flex-column gap-8 mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="autoGroupBalanceGender" checked>
                                    <label class="form-check-label text-13 text-gray-700" for="autoGroupBalanceGender">
                                        Seimbangkan rasio Gender (L & P)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="autoGroupAutoLeader" checked>
                                    <label class="form-check-label text-13 text-gray-700" for="autoGroupAutoLeader">
                                        Tunjuk 1 Ketua Otomatis di tiap kelompok
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-8 mb-20">
                        <button type="submit" class="btn btn-main rounded-pill px-20 text-13" id="btnRunAutoGroup">
                            <i class="ph ph-shuffle me-4"></i> Acak & Buat Susunan Kelompok
                        </button>
                    </div>
                </form>

                <!-- Preview Area -->
                <div id="autoGroupLoading" class="text-center py-24 d-none">
                    <div class="spinner-border text-main-600 mb-8" role="status"></div>
                    <div class="text-13 text-gray-500">Menyusun kelompok secara seimbang...</div>
                </div>

                <div id="autoGroupResults" class="d-none">
                    <div class="flex-between align-items-center mb-12 pb-8 border-bottom border-gray-100">
                        <h6 class="text-14 fw-bold text-gray-800 mb-0">
                            Pratinjau Hasil (<span id="previewTotalGroups">0</span> Kelompok dari <span id="previewTotalStudents">0</span> Siswa)
                        </h6>
                        <span class="badge bg-success-50 text-success-700 text-11 px-10 py-4">
                            <i class="ph ph-check-circle me-2"></i>Siap Diterapkan
                        </span>
                    </div>

                    <div class="row g-3 max-h-350 overflow-y-auto scroll-sm p-4" id="groupCardsContainer">
                        <!-- Group Cards rendered here dynamically -->
                    </div>

                    <!-- Action Selector -->
                    <div class="mt-20 p-16 rounded-12 bg-gray-50 border border-gray-200">
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <label class="form-label text-13 fw-bold text-gray-800 mb-2">Pilih Cara Penerapan:</label>
                                <div class="text-12 text-gray-500">
                                    Pilih apakah ingin memasukkan <strong>satu kelompok terpilih</strong> ke formulir saat ini, atau <strong>langsung menerbitkan proyek untuk semua kelompok</strong> sekaligus.
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end d-flex gap-8 justify-content-md-end flex-wrap">
                                <button type="button" class="btn btn-outline-main rounded-pill px-16 text-12" onclick="applySingleGroupToForm()">
                                    <i class="ph ph-arrow-down-left me-2"></i>Terapkan ke Form Ini
                                </button>
                                <button type="button" class="btn btn-success rounded-pill px-16 text-12" onclick="batchSubmitProjects()">
                                    <i class="ph ph-check-double me-2"></i>Terbitkan Semua Kelompok
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentAutoGroups = [];

    function updateMethodLabel(method) {
        const label = document.getElementById('autoGroupSizeLabel');
        const hint = document.getElementById('autoGroupHint');
        const input = document.getElementById('autoGroupSize');
        if (method === 'members_per_group') {
            label.innerHTML = 'Jumlah Siswa per Kelompok <span class="text-danger">*</span>';
            hint.textContent = 'Contoh: Diisi 4, maka 32 siswa akan dibagi menjadi 8 kelompok.';
            input.value = 4;
        } else {
            label.innerHTML = 'Target Total Jumlah Kelompok <span class="text-danger">*</span>';
            hint.textContent = 'Contoh: Diisi 6, maka seluruh siswa kelas akan didistribusikan ke 6 kelompok.';
            input.value = 6;
        }
    }

    async function runAutoGroup() {
        const classId = document.getElementById('autoGroupClassId').value;
        const method = document.getElementById('autoGroupMethod').value;
        const size = document.getElementById('autoGroupSize').value;
        const balanceGender = document.getElementById('autoGroupBalanceGender').checked;
        const autoLeader = document.getElementById('autoGroupAutoLeader').checked;

        if (!classId) {
            alert('Pilih kelas terlebih dahulu.');
            return;
        }

        const loading = document.getElementById('autoGroupLoading');
        const results = document.getElementById('autoGroupResults');
        const container = document.getElementById('groupCardsContainer');

        loading.classList.remove('d-none');
        results.classList.add('d-none');
        container.innerHTML = '';

        try {
            const response = await fetch("{{ route('guru.projects.auto-group') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    class_id: classId,
                    method: method,
                    size: parseInt(size),
                    balance_gender: balanceGender,
                    auto_leader: autoLeader,
                }),
            });

            const data = await response.json();
            loading.classList.add('d-none');

            if (!response.ok || !data.success) {
                alert(data.message || 'Gagal memproses pembagian kelompok.');
                return;
            }

            currentAutoGroups = data.groups;
            document.getElementById('previewTotalGroups').textContent = data.total_groups;
            document.getElementById('previewTotalStudents').textContent = data.total_students;

            renderGroupCards(data.groups);
            results.classList.remove('d-none');
        } catch (err) {
            loading.classList.add('d-none');
            alert('Terjadi kesalahan jaringan: ' + err.message);
        }
    }

    function renderGroupCards(groups) {
        const container = document.getElementById('groupCardsContainer');
        container.innerHTML = '';

        groups.forEach((g, gIdx) => {
            const col = document.createElement('div');
            col.className = 'col-md-6';

            let membersHtml = '';
            g.members.forEach(m => {
                const genderBadge = m.gender === 'L' ? '<span class="badge bg-primary-50 text-primary-600 text-10">L</span>' : (m.gender === 'P' ? '<span class="badge bg-danger-50 text-danger-600 text-10">P</span>' : '');
                const leaderBadge = m.is_leader ? '<span class="badge bg-warning-50 text-warning-700 text-10 fw-bold"><i class="ph ph-crown me-1"></i>Ketua</span>' : '';
                membersHtml += `
                    <div class="d-flex align-items-center justify-content-between p-6 px-10 rounded-8 bg-gray-50 mb-4 border border-gray-100 text-12">
                        <div class="d-flex align-items-center gap-6 text-truncate">
                            <span class="fw-semibold text-gray-800 text-truncate">${m.name}</span>
                            ${genderBadge}
                        </div>
                        <div class="shrink-0 ms-8">
                            ${leaderBadge}
                        </div>
                    </div>
                `;
            });

            col.innerHTML = `
                <div class="card border border-gray-200 rounded-12 p-12 h-100 bg-white shadow-2xs">
                    <div class="flex-between align-items-center mb-8 pb-4 border-bottom border-gray-100">
                        <div class="d-flex align-items-center gap-6">
                            <span class="w-24 h-24 rounded-circle bg-main-50 text-main-600 flex-center text-11 fw-bold">${g.group_number}</span>
                            <span class="fw-bold text-gray-900 text-13">${g.name}</span>
                        </div>
                        <span class="text-11 text-gray-500">${g.members.length} Siswa</span>
                    </div>
                    <div class="members-list">
                        ${membersHtml}
                    </div>
                    <div class="mt-8 pt-6 border-top border-gray-100 text-end">
                        <button type="button" class="btn btn-xs btn-outline-main rounded-pill px-8 text-11" onclick="selectSingleGroup(${gIdx})">
                            <i class="ph ph-check me-1"></i>Pilih Kelompok Ini
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(col);
        });
    }

    function selectSingleGroup(groupIndex) {
        if (!currentAutoGroups[groupIndex]) return;
        const g = currentAutoGroups[groupIndex];

        // Uncheck all students first
        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.checked = false;
        });
        document.querySelectorAll('.leader-radio').forEach(r => {
            r.checked = false;
            r.disabled = true;
        });

        // Check selected group members
        g.members.forEach(m => {
            const cb = document.getElementById('student' + m.student_id);
            const radio = document.getElementById('leader' + m.student_id);
            if (cb) {
                cb.checked = true;
                if (radio) {
                    radio.disabled = false;
                    if (m.is_leader) {
                        radio.checked = true;
                    }
                }
            }
        });

        // Update title with group name if empty or default
        const titleInput = document.querySelector('input[name="title"]');
        if (titleInput && (!titleInput.value || titleInput.value.includes('Kelompok'))) {
            titleInput.value = `${titleInput.value.split('-')[0].trim()} - ${g.name}`.replace(/^-\s*/, '');
        }

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('autoGroupModal'));
        if (modal) modal.hide();
    }

    function applySingleGroupToForm() {
        if (!currentAutoGroups.length) return;
        selectSingleGroup(0);
    }

    function batchSubmitProjects() {
        if (!currentAutoGroups.length) {
            alert('Belum ada susunan kelompok.');
            return;
        }

        const titleInput = document.querySelector('input[name="title"]');
        const descInput = document.querySelector('textarea[name="description"]');

        if (!titleInput.value.trim()) {
            alert('Harap isi Judul Pokok Proyek di formulir terlebih dahulu (misal: "Aplikasi Kasir Toko").');
            titleInput.focus();
            const modal = bootstrap.Modal.getInstance(document.getElementById('autoGroupModal'));
            if (modal) modal.hide();
            return;
        }

        if (!descInput.value.trim()) {
            alert('Harap isi Deskripsi Proyek di formulir terlebih dahulu.');
            descInput.focus();
            const modal = bootstrap.Modal.getInstance(document.getElementById('autoGroupModal'));
            if (modal) modal.hide();
            return;
        }

        if (!confirm(`Terbitkan ${currentAutoGroups.length} proyek kelompok sekaligus untuk seluruh kelas?`)) {
            return;
        }

        // Create a hidden form to submit batch
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('guru.projects.batch-store') }}";
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrf);

        const fields = [
            { name: 'title_prefix', value: titleInput.value },
            { name: 'description', value: descInput.value },
            { name: 'goals', value: document.querySelector('textarea[name="goals"]')?.value || '' },
            { name: 'instructions', value: document.querySelector('textarea[name="instructions"]')?.value || '' },
            { name: 'status', value: document.querySelector('select[name="status"]')?.value || 'active' },
            { name: 'deadline', value: document.querySelector('input[name="deadline"]')?.value || '' },
            { name: 'rubric', value: document.querySelector('textarea[name="rubric"]')?.value || '' },
            { name: 'groups_json', value: JSON.stringify(currentAutoGroups) },
        ];

        fields.forEach(f => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = f.name;
            input.value = f.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

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
