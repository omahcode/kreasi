@extends('layouts.app')

@section('title', 'Ploting Kelompok PKL Baru')

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
                    <li><span class="text-main-600 fw-medium text-14">Buat Ploting Baru</span></li>
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
                <form method="POST" action="{{ route('admin.pkl-groups.store') }}">
                    @csrf
                    <div class="row gy-24">
                        <!-- Left: Info Kelompok & Penempatan -->
                        <div class="col-lg-6 border-end-lg border-gray-100 pe-lg-24">
                            <h5 class="text-15 fw-bold text-gray-900 mb-16 pb-8 border-bottom border-gray-100">
                                1. Detail Penempatan Industri & Guru
                            </h5>

                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold text-gray-700">Nama Kelompok PKL <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-8 text-13 @error('name') is-invalid @enderror" 
                                       placeholder="Contoh: Kelompok PKL PT Solusi Teknologi Nusantara" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger text-12 mt-4">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold text-gray-700">Mitra Industri DU/DI <span class="text-danger">*</span></label>
                                <select name="company_id" class="form-select rounded-8 text-13 @error('company_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Perusahaan Mitra --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }} ({{ $company->city ?? 'Kota tidak diset' }} &bull; {{ $company->sector ?? 'IT' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-11 text-gray-500 mt-4 d-block">
                                    Mitra industri dikelola oleh Admin di menu Master Industri DU/DI.
                                </small>
                                @error('company_id')
                                    <div class="text-danger text-12 mt-4">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold text-gray-700">Guru Pembimbing PKL <span class="text-danger">*</span></label>
                                
                                <!-- Real Hidden Select (preserves HTML form submission and backend request) -->
                                <select name="mentor_teacher_id" id="mentorTeacherSelect" class="d-none" required>
                                    <option value="">-- Pilih Guru Pembimbing --</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('mentor_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }} (NIP: {{ $teacher->teacher->teacher_number ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Searchable Combobox Component -->
                                <div class="position-relative" id="mentorTeacherCombobox">
                                    <button type="button" 
                                            class="form-control text-start d-flex justify-content-between align-items-center rounded-8 text-13 bg-white border-gray-200 py-10 px-12 @error('mentor_teacher_id') is-invalid @enderror" 
                                            id="mentorTeacherTrigger"
                                            style="cursor: pointer;">
                                        <span id="mentorTeacherTriggerText" class="text-truncate text-gray-500">
                                            -- Pilih Guru Pembimbing --
                                        </span>
                                        <span class="d-flex align-items-center gap-6 text-gray-400 flex-shrink-0 ms-8">
                                            <i class="ph ph-magnifying-glass text-14"></i>
                                            <i class="ph ph-caret-down text-14 transition-2" id="mentorTeacherCaret"></i>
                                        </span>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div class="position-absolute start-0 top-100 w-100 mt-4 rounded-12 shadow-lg bg-white border border-gray-100 p-12 d-none" 
                                         id="mentorTeacherDropdown" 
                                         style="z-index: 1050;">
                                        <!-- Search Input -->
                                        <div class="position-relative mb-8">
                                            <input type="text" 
                                                   class="form-control text-13 ps-36 py-8 rounded-8 border-gray-200" 
                                                   id="mentorTeacherSearchInput" 
                                                   placeholder="Ketik nama guru atau NIP..." 
                                                   autocomplete="off">
                                            <span class="position-absolute top-50 translate-middle-y ms-12 text-gray-400 d-flex pointer-events-none">
                                                <i class="ph ph-magnifying-glass text-16"></i>
                                            </span>
                                            <button type="button" class="btn btn-sm p-0 position-absolute top-50 translate-middle-y end-0 me-10 text-gray-400 hover-text-gray-600 d-none" id="mentorTeacherClearSearch">
                                                <i class="ph ph-x text-14"></i>
                                            </button>
                                        </div>

                                        <!-- Header Info -->
                                        <div class="d-flex justify-content-between align-items-center mb-6 px-4 text-11 text-gray-400">
                                            <span id="mentorTeacherCount">{{ count($teachers) }} Guru Tersedia</span>
                                            <span class="text-11 text-main-600 fw-medium">Pilih salah satu</span>
                                        </div>

                                        <!-- List Options (Scrollable) -->
                                        <div class="overflow-y-auto scroll-sm" style="max-height: 250px;" id="mentorTeacherOptionsList">
                                            <div id="mentorTeacherNoMatch" class="text-center py-24 text-gray-400 text-12 d-none">
                                                <i class="ph ph-user-circle-dashed text-28 text-gray-300 d-block mb-4"></i>
                                                Tidak ada guru pembimbing yang cocok.
                                            </div>

                                            @foreach($teachers as $teacher)
                                                @php
                                                    $nip = $teacher->teacher->teacher_number ?? '-';
                                                    $isSelected = (old('mentor_teacher_id') == $teacher->id);
                                                @endphp
                                                <div class="mentor-teacher-option p-8 rounded-8 cursor-pointer d-flex align-items-center justify-content-between mb-2 transition-1 hover-bg-gray-50 {{ $isSelected ? 'bg-main-50 border border-main-200' : '' }}"
                                                     style="cursor: pointer;"
                                                     data-id="{{ $teacher->id }}"
                                                     data-name="{{ strtolower($teacher->name) }}"
                                                     data-nip="{{ strtolower($nip) }}"
                                                     data-display-name="{{ $teacher->name }}"
                                                     data-display-nip="{{ $nip }}">
                                                    <div class="d-flex align-items-center gap-10 text-truncate">
                                                        <div class="w-32 h-32 rounded-circle bg-white text-main-600 border border-main-100 flex-center text-12 fw-bold flex-shrink-0 shadow-2xs">
                                                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                                        </div>
                                                        <div class="text-truncate">
                                                            <div class="fw-semibold text-13 text-gray-900 text-truncate teacher-name">{{ $teacher->name }}</div>
                                                            <div class="text-11 text-gray-400 teacher-nip">NIP: {{ $nip }}</div>
                                                        </div>
                                                    </div>
                                                    <span class="check-badge text-main-600 text-16 flex-shrink-0 ms-8 {{ $isSelected ? '' : 'd-none' }}">
                                                        <i class="ph-fill ph-check-circle"></i>
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @error('mentor_teacher_id')
                                    <div class="text-danger text-12 mt-4">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-12 mb-16">
                                <div class="col-6">
                                    <label class="form-label text-13 fw-semibold text-gray-700">Tanggal Mulai PKL</label>
                                    <input type="date" name="start_date" class="form-control rounded-8 text-13" value="{{ old('start_date') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-13 fw-semibold text-gray-700">Tanggal Selesai PKL</label>
                                    <input type="date" name="end_date" class="form-control rounded-8 text-13" value="{{ old('end_date') }}">
                                </div>
                            </div>

                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold text-gray-700">Catatan / Keterangan Penempatan</label>
                                <textarea name="notes" rows="3" class="form-control rounded-8 text-13" placeholder="Informasi shift, pembagian divisi, kontak supervisor kantor, dll...">{{ old('notes') }}</textarea>
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
                                    Centang siswa yang masuk ke kelompok PKL ini, lalu tentukan satu siswa sebagai <strong>Ketua Kelompok</strong>.
                                </p>
                            </div>

                            @error('student_ids')
                                <div class="alert alert-danger py-8 px-12 text-13 mb-16">{{ $message }}</div>
                            @enderror

                            @php
                                $studentClasses = $students->pluck('schoolClass.name')->filter()->unique()->sort()->values();
                            @endphp

                            <!-- Search & Filter Controls -->
                            <div class="row g-8 mb-12">
                                <div class="col-sm-7">
                                    <div class="position-relative">
                                        <input type="text" id="searchStudentInput" class="form-control text-13 ps-36 rounded-pill border-gray-200" placeholder="Cari nama atau NIS siswa...">
                                        <span class="position-absolute top-50 translate-middle-y ms-12 text-gray-400 d-flex pointer-event-none">
                                            <i class="ph ph-magnifying-glass text-16"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <select id="filterClassSelect" class="form-select text-13 rounded-pill border-gray-200">
                                        <option value="">Semua Rombel / Kelas</option>
                                        @foreach($studentClasses as $className)
                                            <option value="{{ strtolower($className) }}">{{ $className }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Counter Info -->
                            <div class="flex-between align-items-center mb-10 px-4">
                                <span class="text-12 text-gray-500" id="studentTotalCountText">
                                    Menampilkan <strong id="visibleStudentCount">{{ count($students) }}</strong> dari {{ count($students) }} siswa
                                </span>
                                <span class="badge bg-main-50 text-main-600 rounded-pill px-10 py-4 text-11 fw-semibold" id="selectedCountBadge">
                                    <i class="ph ph-check me-2"></i> <span id="selectedCountNumber">0</span> Siswa Dipilih
                                </span>
                            </div>

                            <!-- List Siswa -->
                            <div class="overflow-y-auto p-12 rounded-12 border border-gray-100 bg-gray-50" style="max-height: 400px;" id="studentListContainer">
                                <div id="noStudentMatch" class="text-center py-24 text-gray-400 text-13 d-none">
                                    <i class="ph ph-magnifying-glass text-24 text-gray-300 d-block mb-4"></i>
                                    Tidak ada siswa yang cocok dengan kata kunci pencarian.
                                </div>

                                @forelse($students as $st)
                                    <div class="student-item d-flex align-items-center justify-content-between p-10 rounded-10 bg-white mb-8 border border-gray-100 gap-12"
                                         data-name="{{ strtolower($st->user->name ?? '') }}"
                                         data-nis="{{ $st->nis }}"
                                         data-class="{{ strtolower($st->schoolClass->name ?? '') }}">
                                        <div class="form-check mb-0 flex-grow-1 text-truncate">
                                            <input class="form-check-input student-checkbox" 
                                                   type="checkbox" 
                                                   name="student_ids[]" 
                                                   value="{{ $st->id }}" 
                                                   id="st{{ $st->id }}"
                                                   {{ in_array($st->id, old('student_ids', [])) ? 'checked' : '' }}
                                                   onchange="handleStudentCheck({{ $st->id }})">
                                            <label class="form-check-label text-13 fw-semibold text-gray-800 ms-6 text-truncate cursor-pointer" for="st{{ $st->id }}">
                                                <span class="d-block text-gray-900">{{ $st->user->name }}</span>
                                                <small class="text-gray-400 d-block fw-normal">
                                                    NIS: {{ $st->nis }} &bull; {{ $st->schoolClass->name ?? 'Kelas -' }}
                                                </small>
                                            </label>
                                        </div>
                                        <div class="leader-wrapper flex-shrink-0" id="leaderWrap{{ $st->id }}" style="display: {{ in_array($st->id, old('student_ids', [])) ? 'block' : 'none' }};">
                                            <div class="d-flex align-items-center bg-warning-50 px-10 py-4 rounded-pill border border-warning-200" title="Pilih sebagai Ketua Kelompok">
                                                <input class="form-check-input leader-radio me-6 m-0" 
                                                       type="radio" 
                                                       name="leader_id" 
                                                       value="{{ $st->id }}" 
                                                       id="leader{{ $st->id }}"
                                                       {{ old('leader_id') == $st->id ? 'checked' : '' }}>
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
                                <i class="ph ph-check me-4"></i> Simpan Ploting Kelompok PKL
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
            // If no leader selected yet, select this one
            const checkedLeaders = document.querySelectorAll('.leader-radio:checked');
            if (checkedLeaders.length === 0) {
                leaderRadio.checked = true;
            }
        } else {
            leaderWrap.style.display = 'none';
            if (leaderRadio.checked) {
                leaderRadio.checked = false;
                // auto select first checked checkbox
                const otherCheckbox = document.querySelector('.student-checkbox:checked');
                if (otherCheckbox) {
                    const otherId = otherCheckbox.value;
                    const otherRadio = document.getElementById('leader' + otherId);
                    if (otherRadio) otherRadio.checked = true;
                }
            }
        }
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
        const countNumberEl = document.getElementById('selectedCountNumber');
        if (countNumberEl) {
            countNumberEl.textContent = checkedCount;
        }
    }

    function filterStudents() {
        const searchInput = document.getElementById('searchStudentInput');
        const filterClass = document.getElementById('filterClassSelect');
        const keyword = (searchInput ? searchInput.value : '').trim().toLowerCase();
        const selectedClass = (filterClass ? filterClass.value : '').trim().toLowerCase();

        const items = document.querySelectorAll('.student-item');
        let visibleCount = 0;

        items.forEach(function (item) {
            const name = item.getAttribute('data-name') || '';
            const nis = item.getAttribute('data-nis') || '';
            const itemClass = item.getAttribute('data-class') || '';

            const matchesSearch = !keyword || name.includes(keyword) || nis.includes(keyword);
            const matchesClass = !selectedClass || itemClass === selectedClass;

            if (matchesSearch && matchesClass) {
                item.style.setProperty('display', 'flex', 'important');
                visibleCount++;
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });

        const noMatchEl = document.getElementById('noStudentMatch');
        if (noMatchEl) {
            if (visibleCount === 0) {
                noMatchEl.classList.remove('d-none');
            } else {
                noMatchEl.classList.add('d-none');
            }
        }

        const visibleCountEl = document.getElementById('visibleStudentCount');
        if (visibleCountEl) {
            visibleCountEl.textContent = visibleCount;
        }
    }

    function initMentorTeacherSearchable() {
        const trigger = document.getElementById('mentorTeacherTrigger');
        const dropdown = document.getElementById('mentorTeacherDropdown');
        const searchInput = document.getElementById('mentorTeacherSearchInput');
        const clearSearchBtn = document.getElementById('mentorTeacherClearSearch');
        const options = document.querySelectorAll('.mentor-teacher-option');
        const select = document.getElementById('mentorTeacherSelect');
        const triggerText = document.getElementById('mentorTeacherTriggerText');
        const countEl = document.getElementById('mentorTeacherCount');
        const noMatchEl = document.getElementById('mentorTeacherNoMatch');
        const caret = document.getElementById('mentorTeacherCaret');

        if (!trigger || !dropdown || !select) return;

        function syncTriggerWithSelected() {
            const selectedId = select.value;
            if (!selectedId) {
                triggerText.innerHTML = '<span class="text-gray-400">-- Pilih Guru Pembimbing --</span>';
                return;
            }

            const selectedOption = Array.from(options).find(opt => opt.getAttribute('data-id') === selectedId);
            if (selectedOption) {
                const name = selectedOption.getAttribute('data-display-name');
                const nip = selectedOption.getAttribute('data-display-nip');
                triggerText.innerHTML = `<span class="fw-bold text-gray-900">${name}</span> <span class="badge bg-main-50 text-main-700 border border-main-200 ms-6 font-normal text-11">NIP: ${nip}</span>`;
                
                options.forEach(opt => {
                    const isThis = opt.getAttribute('data-id') === selectedId;
                    opt.classList.toggle('bg-main-50', isThis);
                    opt.classList.toggle('border', isThis);
                    opt.classList.toggle('border-main-200', isThis);
                    const badge = opt.querySelector('.check-badge');
                    if (badge) badge.classList.toggle('d-none', !isThis);
                });
            }
        }

        syncTriggerWithSelected();

        function openDropdown() {
            dropdown.classList.remove('d-none');
            if (caret) caret.style.transform = 'rotate(180deg)';
            trigger.setAttribute('aria-expanded', 'true');
            trigger.classList.remove('is-invalid', 'border-danger');
            setTimeout(() => {
                if (searchInput) searchInput.focus();
            }, 50);
        }

        function closeDropdown() {
            dropdown.classList.add('d-none');
            if (caret) caret.style.transform = 'rotate(0deg)';
            trigger.setAttribute('aria-expanded', 'false');
        }

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (dropdown.classList.contains('d-none')) {
                openDropdown();
            } else {
                closeDropdown();
            }
        });

        document.addEventListener('click', function (e) {
            const combobox = document.getElementById('mentorTeacherCombobox');
            if (combobox && !combobox.contains(e.target)) {
                closeDropdown();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !dropdown.classList.contains('d-none')) {
                closeDropdown();
                trigger.focus();
            }
        });

        function filterTeachers() {
            const keyword = (searchInput ? searchInput.value : '').trim().toLowerCase();
            let visible = 0;

            if (clearSearchBtn) {
                clearSearchBtn.classList.toggle('d-none', keyword.length === 0);
            }

            options.forEach(function (opt) {
                const name = opt.getAttribute('data-name') || '';
                const nip = opt.getAttribute('data-nip') || '';
                const matches = !keyword || name.includes(keyword) || nip.includes(keyword);

                if (matches) {
                    opt.style.setProperty('display', 'flex', 'important');
                    visible++;
                } else {
                    opt.style.setProperty('display', 'none', 'important');
                }
            });

            if (noMatchEl) {
                noMatchEl.classList.toggle('d-none', visible > 0);
            }

            if (countEl) {
                countEl.textContent = keyword ? `${visible} Guru Ditemukan` : `${options.length} Guru Tersedia`;
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterTeachers);
        }

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                searchInput.value = '';
                filterTeachers();
                searchInput.focus();
            });
        }

        options.forEach(function (opt) {
            opt.addEventListener('click', function (e) {
                e.stopPropagation();
                const teacherId = opt.getAttribute('data-id');
                select.value = teacherId;
                select.dispatchEvent(new Event('change'));
                syncTriggerWithSelected();
                closeDropdown();
                trigger.focus();
            });
        });

        const form = trigger.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!select.value) {
                    e.preventDefault();
                    trigger.classList.add('is-invalid', 'border-danger');
                    openDropdown();
                    if (window.KreasiToast) {
                        window.KreasiToast.warning('Silakan pilih guru pembimbing terlebih dahulu.');
                    }
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchStudentInput');
        const filterClass = document.getElementById('filterClassSelect');

        if (searchInput) {
            searchInput.addEventListener('input', filterStudents);
        }

        if (filterClass) {
            filterClass.addEventListener('change', filterStudents);
        }

        updateSelectedCount();
        initMentorTeacherSearchable();
    });
</script>
@endpush
