@extends('layouts.app')

@section('title', 'Sinkronisasi Data WargaRPL API - Siswa & Guru')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-gray-400 fw-normal text-14">Integrasi API</span></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Sinkronisasi WargaRPL</span></li>
                </ul>
            </div>
            <div class="flex-align gap-8 flex-wrap">
                <a href="{{ route('admin.users.index', ['role' => 'guru']) }}" class="btn btn-outline-success rounded-pill px-16 text-13">
                    <i class="ph ph-chalkboard-teacher me-4"></i> Data Guru ({{ $localTeacherCount }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'siswa']) }}" class="btn btn-outline-primary rounded-pill px-16 text-13">
                    <i class="ph ph-users me-4"></i> Data Siswa ({{ $localStudentCount }})
                </a>
                <a href="{{ route('admin.students.import') }}" class="btn btn-outline-secondary rounded-pill px-16 text-13">
                    <i class="ph ph-file-arrow-up me-4"></i> Import Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show rounded-16 border-0 shadow-sm p-16 d-flex align-items-center gap-12" role="alert">
                <span class="w-36 h-36 rounded-circle bg-success-600 text-white flex-center shrink-0 text-18">
                    <i class="ph ph-check"></i>
                </span>
                <div class="flex-grow-1 text-14">
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show rounded-16 border-0 shadow-sm p-16 d-flex align-items-center gap-12" role="alert">
                <span class="w-36 h-36 rounded-circle bg-danger-600 text-white flex-center shrink-0 text-18">
                    <i class="ph ph-x"></i>
                </span>
                <div class="flex-grow-1 text-14">
                    <strong>Gagal!</strong> {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Stat Widgets (4 Widgets) -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100 shadow-xs">
            <div class="card-body p-20">
                <span class="text-gray-500 text-13 fw-medium">Siswa di Sistem</span>
                <div class="flex-between gap-8 mt-8">
                    <h2 class="mb-0 fw-bold text-gray-900">{{ number_format($localStudentCount) }}</h2>
                    <span class="flex-shrink-0 w-44 h-44 flex-center rounded-circle bg-main-50 text-main-600 text-2xl">
                        <i class="ph-fill ph-student"></i>
                    </span>
                </div>
                <div class="mt-12 text-12 text-gray-500">
                    <span class="text-success-600 fw-semibold"><i class="ph ph-check-circle"></i> Terdaftar</span> di database KREASI
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100 shadow-xs">
            <div class="card-body p-20">
                <span class="text-gray-500 text-13 fw-medium">Guru & Pembimbing</span>
                <div class="flex-between gap-8 mt-8">
                    <h2 class="mb-0 fw-bold text-gray-900">{{ number_format($localTeacherCount) }}</h2>
                    <span class="flex-shrink-0 w-44 h-44 flex-center rounded-circle bg-success-50 text-success-600 text-2xl">
                        <i class="ph-fill ph-chalkboard-teacher"></i>
                    </span>
                </div>
                <div class="mt-12 text-12 text-gray-500">
                    <span class="text-success-600 fw-semibold"><i class="ph ph-user-check"></i> Aktif</span> pembimbing PKL & PjBL
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100 shadow-xs">
            <div class="card-body p-20">
                <span class="text-gray-500 text-13 fw-medium">Rombel / Kelas</span>
                <div class="flex-between gap-8 mt-8">
                    <h2 class="mb-0 fw-bold text-gray-900">{{ $localClassCount }}</h2>
                    <span class="flex-shrink-0 w-44 h-44 flex-center rounded-circle bg-purple-50 text-purple-600 text-2xl">
                        <i class="ph-fill ph-chalkboard"></i>
                    </span>
                </div>
                <div class="mt-12 text-12 text-gray-500">
                    <span class="text-main-600 fw-semibold"><i class="ph ph-calendar"></i> TA Aktif:</span> {{ $activeAcademicYear->name ?? '2026/2027' }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card border border-gray-100 rounded-16 h-100 shadow-xs">
            <div class="card-body p-20">
                <span class="text-gray-500 text-13 fw-medium">Integrasi API</span>
                <div class="flex-between gap-8 mt-8">
                    <h4 class="mb-0 fw-bold text-gray-900">WargaRPL</h4>
                    <span class="flex-shrink-0 w-44 h-44 flex-center rounded-circle bg-info-50 text-info-600 text-2xl">
                        <i class="ph-fill ph-cloud-arrow-down"></i>
                    </span>
                </div>
                <div class="mt-12 text-12 text-gray-500">
                    <span class="badge bg-success-50 text-success-700 px-8 py-3 rounded-pill text-11 fw-semibold">
                        <i class="ph ph-link me-2"></i> Terkoneksi API
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Segmented Tab Navigation -->
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-12 bg-white p-12 rounded-16 border border-gray-100 shadow-xs">
            <div class="d-flex align-items-center gap-8 flex-wrap">
                <button type="button" 
                        class="btn rounded-pill px-20 py-10 text-14 fw-bold transition-2 sync-tab-btn {{ ($activeTab ?? 'students') === 'students' ? 'btn-main text-white shadow-sm' : 'btn-outline-gray text-gray-600' }}" 
                        id="tabBtnStudents" 
                        onclick="switchTab('students')">
                    <i class="ph ph-student me-6 text-18"></i> Sinkronisasi Data Siswa & Rombel
                </button>
                <button type="button" 
                        class="btn rounded-pill px-20 py-10 text-14 fw-bold transition-2 sync-tab-btn {{ ($activeTab ?? 'students') === 'teachers' ? 'btn-success text-white shadow-sm' : 'btn-outline-gray text-gray-600' }}" 
                        id="tabBtnTeachers" 
                        onclick="switchTab('teachers')">
                    <i class="ph ph-chalkboard-teacher me-6 text-18"></i> Sinkronisasi Data Guru & Tendik
                    <span class="badge bg-white text-success-700 ms-6 rounded-pill px-8 py-2 text-11">Baru</span>
                </button>
            </div>
            <div class="d-flex align-items-center gap-8">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-14 py-8 text-12 fw-semibold" id="btnTestGlobal">
                    <i class="ph ph-plugs-connected me-4"></i> Uji Koneksi Endpoint API
                </button>
            </div>
        </div>
        <div id="globalTestResult" class="mt-12 d-none"></div>
    </div>

    <!-- TAB 1: SINKRONISASI SISWA -->
    <div id="tabContentStudents" class="{{ ($activeTab ?? 'students') === 'students' ? '' : 'd-none' }}">
        <div class="row gy-4">
            <!-- Left Info Card -->
            <div class="col-lg-7">
                <div class="card border border-gray-100 rounded-16 h-100 shadow-xs">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center gap-12 mb-20 pb-16 border-bottom border-gray-100">
                            <span class="w-48 h-48 rounded-circle bg-main-50 text-main-600 flex-center text-24 shrink-0">
                                <i class="ph-fill ph-student"></i>
                            </span>
                            <div>
                                <h5 class="mb-2 fw-bold text-gray-900">Sinkronisasi Data Siswa dari WargaRPL</h5>
                                <p class="text-gray-500 text-13 mb-0">Integrasi otomatis data akun siswa, NIS, NISN, dan rombongan belajar.</p>
                            </div>
                        </div>

                        <!-- Detail Table -->
                        <div class="table-responsive rounded-12 border border-gray-200 overflow-hidden mb-20">
                            <table class="table table-sm align-middle mb-0 text-13">
                                <tbody>
                                    <tr class="border-bottom border-gray-200">
                                        <td class="bg-gray-50 text-gray-600 fw-semibold ps-16 py-12" style="width: 32%;">Endpoint API</td>
                                        <td class="ps-16 py-12 text-gray-900 fw-medium">
                                            <span class="badge bg-gray-100 text-gray-800 border px-10 py-4 rounded-8 text-12 font-monospace">
                                                {{ $baseUrl }}/api/v1/students
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom border-gray-200">
                                        <td class="bg-gray-50 text-gray-600 fw-semibold ps-16 py-12">Token Terpasang</td>
                                        <td class="ps-16 py-12">
                                            <span class="badge bg-main-50 text-main-600 border border-main-200 px-10 py-4 rounded-pill font-monospace text-12 fw-bold">
                                                {{ $maskedToken }}
                                            </span>
                                            <span class="badge bg-success-50 text-success-700 px-8 py-3 rounded-pill text-11 ms-6 fw-semibold">
                                                <i class="ph ph-check me-2"></i> Aktif
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom border-gray-200">
                                        <td class="bg-gray-50 text-gray-600 fw-semibold ps-16 py-12">Hak Akses Scope</td>
                                        <td class="ps-16 py-12">
                                            <span class="badge bg-purple-50 text-purple-700 border border-purple-200 px-10 py-4 rounded-pill text-11 fw-semibold me-4">
                                                students.read
                                            </span>
                                            <span class="badge bg-info-50 text-info-700 border border-info-200 px-10 py-4 rounded-pill text-11 fw-semibold">
                                                classes.read
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-50 text-gray-600 fw-semibold ps-16 py-12">Format Akun & Sandi</td>
                                        <td class="ps-16 py-12 text-gray-700 text-12">
                                            <div>Login: <strong class="text-gray-900 font-monospace">NIS</strong> atau Email <strong class="text-gray-900 font-monospace">[NIS]@siswa.kreasi.test</strong></div>
                                            <div class="mt-4">Sandi Awal: <strong class="text-main-600 font-monospace">Tanggal Lahir (DDMMYYYY)</strong> atau NIS</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Rules list -->
                        <h6 class="text-14 fw-bold text-gray-900 mb-12">Ketentuan Sinkronisasi Siswa:</h6>
                        <ul class="list-unstyled text-13 text-gray-600 d-flex flex-column gap-10 mb-0">
                            <li class="d-flex align-items-start gap-10">
                                <span class="w-20 h-20 rounded-circle bg-success-50 text-success-600 flex-center text-12 shrink-0 mt-2">
                                    <i class="ph ph-check"></i>
                                </span>
                                <span>Siswa baru langsung dibuatkan akun pengguna dengan hak akses <strong>Siswa</strong>.</span>
                            </li>
                            <li class="d-flex align-items-start gap-10">
                                <span class="w-20 h-20 rounded-circle bg-success-50 text-success-600 flex-center text-12 shrink-0 mt-2">
                                    <i class="ph ph-check"></i>
                                </span>
                                <span>Rombongan belajar (kelas) yang belum ada di KREASI akan otomatis dibuatkan dan disesuaikan tingkatannya (X / XI / XII).</span>
                            </li>
                            <li class="d-flex align-items-start gap-10">
                                <span class="w-20 h-20 rounded-circle bg-success-50 text-success-600 flex-center text-12 shrink-0 mt-2">
                                    <i class="ph ph-check"></i>
                                </span>
                                <span>Siswa yang sudah terdaftar akan diperbarui data kelas, gender, dan NISN-nya <strong>tanpa mengubah kata sandi lama mereka</strong>.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Action Box Siswa -->
            <div class="col-lg-5">
                <div class="card border border-gray-100 rounded-16 h-100 shadow-xs">
                    <div class="card-body p-24">
                        <div class="mb-20 pb-16 border-bottom border-gray-100">
                            <h5 class="mb-2 fw-bold text-gray-900">Tindakan Sinkronisasi Siswa</h5>
                            <p class="text-gray-500 text-13 mb-0">Tarik data rombel & siswa terbaru dari WargaRPL.</p>
                        </div>

                        <!-- Main Sync Form -->
                        <form method="POST" action="{{ route('admin.students.sync.process') }}" id="studentSyncForm">
                            @csrf
                            <div class="mb-16">
                                <label class="form-label text-12 text-gray-700 fw-semibold mb-6">Bearer API Token</label>
                                <input type="text" name="token" id="studentApiTokenInput" class="form-control text-13 py-10 px-14 border-gray-200 rounded-10 font-monospace" value="{{ $token }}" placeholder="wgr_live_...">
                                <span class="text-11 text-gray-400 mt-4 d-block">Default diambil dari konfigurasi sistem (.env).</span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-column gap-10 mb-16">
                                <button type="button" onclick="confirmStudentSync()" class="btn btn-main w-100 rounded-pill py-12 px-16 text-14 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-8">
                                    <i class="ph ph-arrows-clockwise text-18"></i> Sinkronkan Semua Siswa
                                </button>
                            </div>
                        </form>

                        <div class="border-top border-gray-100 my-20"></div>

                        <!-- Preview Form Siswa -->
                        <div>
                            <label class="form-label text-12 text-gray-700 fw-semibold mb-6">Pratinjau Data Siswa API</label>
                            <form method="POST" action="{{ route('admin.students.sync.preview') }}">
                                @csrf
                                <input type="hidden" name="token" id="previewStudentTokenInput" value="{{ $token }}">
                                <div class="input-group">
                                    <input type="text" name="class_filter" class="form-control text-13 ps-14 rounded-start-pill border-gray-200" placeholder="Filter nama / kelas (contoh: XII RPL 1)" value="{{ $classFilter ?? '' }}">
                                    <button type="submit" class="btn btn-outline-primary px-16 rounded-end-pill text-13 fw-semibold">
                                        <i class="ph ph-magnifying-glass me-4"></i> Pratinjau
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: SINKRONISASI GURU & TENDIK -->
    <div id="tabContentTeachers" class="{{ ($activeTab ?? 'students') === 'teachers' ? '' : 'd-none' }}">
        <div class="row gy-4">
            <!-- Left Info Card Guru -->
            <div class="col-lg-7">
                <div class="card border border-gray-100 rounded-16 h-100 shadow-xs">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center gap-12 mb-20 pb-16 border-bottom border-gray-100">
                            <span class="w-48 h-48 rounded-circle bg-success-50 text-success-600 flex-center text-24 shrink-0">
                                <i class="ph-fill ph-chalkboard-teacher"></i>
                            </span>
                            <div>
                                <h5 class="mb-2 fw-bold text-gray-900">Sinkronisasi Data Guru & Tendik dari WargaRPL</h5>
                                <p class="text-gray-500 text-13 mb-0">Integrasi otomatis akun pendidik produktif & pembimbing PKL langsung dari API.</p>
                            </div>
                        </div>

                        <!-- Detail Table Guru -->
                        <div class="table-responsive rounded-12 border border-gray-200 overflow-hidden mb-20">
                            <table class="table table-sm align-middle mb-0 text-13">
                                <tbody>
                                    <tr class="border-bottom border-gray-200">
                                        <td class="bg-gray-50 text-gray-600 fw-semibold ps-16 py-12" style="width: 32%;">Endpoint API</td>
                                        <td class="ps-16 py-12 text-gray-900 fw-medium">
                                            <span class="badge bg-gray-100 text-gray-800 border px-10 py-4 rounded-8 text-12 font-monospace">
                                                {{ $baseUrl }}/api/v1/employees
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom border-gray-200">
                                        <td class="bg-gray-50 text-gray-600 fw-semibold ps-16 py-12">Token Terpasang</td>
                                        <td class="ps-16 py-12">
                                            <span class="badge bg-main-50 text-main-600 border border-main-200 px-10 py-4 rounded-pill font-monospace text-12 fw-bold">
                                                {{ $maskedToken }}
                                            </span>
                                            <span class="badge bg-success-50 text-success-700 px-8 py-3 rounded-pill text-11 ms-6 fw-semibold">
                                                <i class="ph ph-check me-2"></i> Aktif
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom border-gray-200">
                                        <td class="bg-gray-50 text-gray-600 fw-semibold ps-16 py-12">Hak Akses Scope</td>
                                        <td class="ps-16 py-12">
                                            <span class="badge bg-success-50 text-success-700 border border-success-200 px-10 py-4 rounded-pill text-11 fw-semibold me-4">
                                                teachers.read
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom border-gray-200">
                                        <td class="bg-gray-50 text-gray-600 fw-semibold ps-16 py-12">Identitas Login</td>
                                        <td class="ps-16 py-12 text-gray-900">
                                            <span class="badge bg-purple-50 text-purple-700 border border-purple-200 px-10 py-4 rounded-8 text-12 fw-bold">
                                                <i class="ph ph-identification-card me-4"></i> NIP (Nomor Induk Pegawai)
                                            </span>
                                            <span class="text-12 text-gray-500 ms-6">atau Email Sekolah</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-50 text-gray-600 fw-semibold ps-16 py-12">Format Kata Sandi Default</td>
                                        <td class="ps-16 py-12 text-gray-800 text-12">
                                            <div class="fw-bold text-success-700 font-monospace text-13 mb-4">
                                                <i class="ph ph-calendar-check me-4"></i> Tanggal Lahir (DDMMYYYY)
                                            </div>
                                            <p class="text-gray-600 text-12 mb-0">
                                                Otomatis diekstrak dari 8 digit awal NIP PNS/PPPK (format <code>YYYYMMDD...</code> dibalik menjadi <code>DDMMYYYY</code>).<br>
                                                <span class="text-main-600 fw-semibold">Contoh:</span> NIP <code>197910012009022003</code> (lahir 01 Okt 1979) &rarr; Sandi: <code class="bg-warning-50 text-warning-800 px-6 py-2 rounded">01101979</code>.
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Rules list Guru -->
                        <h6 class="text-14 fw-bold text-gray-900 mb-12">Ketentuan Sinkronisasi Guru & Tendik:</h6>
                        <ul class="list-unstyled text-13 text-gray-600 d-flex flex-column gap-10 mb-0">
                            <li class="d-flex align-items-start gap-10">
                                <span class="w-20 h-20 rounded-circle bg-success-50 text-success-600 flex-center text-12 shrink-0 mt-2">
                                    <i class="ph ph-check"></i>
                                </span>
                                <span>Guru baru otomatis dibuatkan akun pengguna dengan hak akses <strong>Guru Pembimbing</strong>.</span>
                            </li>
                            <li class="d-flex align-items-start gap-10">
                                <span class="w-20 h-20 rounded-circle bg-success-50 text-success-600 flex-center text-12 shrink-0 mt-2">
                                    <i class="ph ph-check"></i>
                                </span>
                                <span>Guru langsung dapat masuk ke sistem KREASI menggunakan <strong>NIP</strong> dan sandi <strong>DDMMYYYY</strong>.</span>
                            </li>
                            <li class="d-flex align-items-start gap-10">
                                <span class="w-20 h-20 rounded-circle bg-success-50 text-success-600 flex-center text-12 shrink-0 mt-2">
                                    <i class="ph ph-check"></i>
                                </span>
                                <span>Data guru yang sudah ada akan diperbarui nama, jabatan, email, dan no HP <strong>tanpa menimpa kata sandi lama mereka</strong>, kecuali opsi reset diaktifkan.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Action Box Guru -->
            <div class="col-lg-5">
                <div class="card border border-gray-100 rounded-16 h-100 shadow-xs">
                    <div class="card-body p-24">
                        <div class="mb-20 pb-16 border-bottom border-gray-100">
                            <h5 class="mb-2 fw-bold text-gray-900">Tindakan Sinkronisasi Guru</h5>
                            <p class="text-gray-500 text-13 mb-0">Tarik data seluruh guru & pembimbing PKL dari WargaRPL.</p>
                        </div>

                        <!-- Main Sync Form Guru -->
                        <form method="POST" action="{{ route('admin.students.sync.teachers.process') }}" id="teacherSyncForm">
                            @csrf
                            <div class="mb-16">
                                <label class="form-label text-12 text-gray-700 fw-semibold mb-6">Bearer API Token</label>
                                <input type="text" name="token" id="teacherApiTokenInput" class="form-control text-13 py-10 px-14 border-gray-200 rounded-10 font-monospace" value="{{ $token }}" placeholder="wgr_live_...">
                                <span class="text-11 text-gray-400 mt-4 d-block">Default diambil dari konfigurasi sistem (.env).</span>
                            </div>

                            <div class="mb-20 form-check">
                                <input class="form-check-input rounded-4" type="checkbox" name="reset_passwords" value="1" id="resetPasswordsCheck">
                                <label class="form-check-label text-13 text-gray-700" for="resetPasswordsCheck">
                                    Reset juga kata sandi guru lama ke <strong>Tanggal Lahir (DDMMYYYY)</strong>
                                </label>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-column gap-10 mb-16">
                                <button type="button" onclick="confirmTeacherSync()" class="btn btn-success w-100 rounded-pill py-12 px-16 text-14 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-8">
                                    <i class="ph ph-arrows-clockwise text-18"></i> Sinkronkan Semua Guru & Tendik
                                </button>
                            </div>
                        </form>

                        <div class="border-top border-gray-100 my-20"></div>

                        <!-- Preview Form Guru -->
                        <div>
                            <label class="form-label text-12 text-gray-700 fw-semibold mb-6">Pratinjau Data Guru API</label>
                            <form method="POST" action="{{ route('admin.students.sync.teachers.preview') }}">
                                @csrf
                                <input type="hidden" name="token" id="previewTeacherTokenInput" value="{{ $token }}">
                                <div class="input-group">
                                    <input type="text" name="search_filter" class="form-control text-13 ps-14 rounded-start-pill border-gray-200" placeholder="Cari nama guru atau NIP..." value="{{ $searchFilter ?? '' }}">
                                    <button type="submit" class="btn btn-outline-success px-16 rounded-end-pill text-13 fw-semibold">
                                        <i class="ph ph-magnifying-glass me-4"></i> Pratinjau Guru
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Results Siswa (If requested) -->
    @if(isset($previewItems))
        <div class="col-12">
            <div class="card border border-gray-100 rounded-16 overflow-hidden shadow-xs">
                <div class="card-body p-24">
                    <div class="flex-between flex-wrap gap-16 mb-20 pb-16 border-bottom border-gray-100">
                        <div>
                            <h5 class="mb-4 fw-bold text-gray-900">Hasil Pratinjau Siswa dari WargaRPL API</h5>
                            <span class="text-13 text-gray-500">Menampilkan data respons server WargaRPL sebelum disimpan ke database KREASI.</span>
                        </div>
                        <div class="d-flex gap-8 align-items-center flex-wrap">
                            <span class="badge bg-success-50 text-success-700 px-12 py-8 rounded-pill text-13 fw-semibold">
                                <i class="ph ph-user-plus me-4"></i> {{ $previewNewCount }} Siswa Baru
                            </span>
                            <span class="badge bg-main-50 text-main-600 px-12 py-8 rounded-pill text-13 fw-semibold">
                                <i class="ph ph-arrows-clockwise me-4"></i> {{ $previewUpdateCount }} Siswa Sudah Terdaftar
                            </span>
                            <button type="button" onclick="confirmStudentSync()" class="btn btn-main rounded-pill px-20 text-13 fw-semibold shadow-sm">
                                <i class="ph ph-check me-4"></i> Lanjutkan Sinkronkan Semua
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-13">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="ps-16 py-10">No</th>
                                    <th class="py-10">Nama Siswa</th>
                                    <th class="py-10">NIS</th>
                                    <th class="py-10">NISN</th>
                                    <th class="py-10">Kelas</th>
                                    <th class="py-10">Tgl Lahir / Sandi Awal</th>
                                    <th class="py-10">Status API</th>
                                    <th class="py-10 pe-16">Aksi KREASI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($previewItems as $index => $item)
                                    <tr>
                                        <td class="ps-16 fw-semibold text-gray-500">#{{ $index + 1 }}</td>
                                        <td class="fw-bold text-gray-900">{{ $item['full_name'] }}</td>
                                        <td class="font-monospace text-gray-700">{{ $item['nis'] }}</td>
                                        <td class="font-monospace text-gray-500">{{ $item['nisn'] }}</td>
                                        <td>
                                            <span class="badge bg-gray-100 text-gray-800 text-12">{{ $item['class_name'] }}</span>
                                        </td>
                                        <td>
                                            @if($item['birth_date'])
                                                <div class="text-12">
                                                    <span class="text-gray-900 fw-medium">{{ $item['birth_date'] }}</span>
                                                    <div class="text-main-600 font-monospace text-11 mt-2">Pass: <code>{{ $item['auto_password'] }}</code></div>
                                                </div>
                                            @else
                                                <div class="text-12 text-gray-400">Pass: <code>{{ $item['nis'] }}</code> (NIS)</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success-50 text-success-700 text-11">{{ $item['status'] }}</span>
                                        </td>
                                        <td class="pe-16">
                                            @if($item['is_existing'])
                                                <span class="badge bg-main-50 text-main-600 border border-main-200 text-12 px-10 py-4 rounded-pill">
                                                    <i class="ph ph-arrows-clockwise me-2"></i> Perbarui
                                                </span>
                                            @else
                                                <span class="badge bg-success-50 text-success-700 border border-success-200 text-12 px-10 py-4 rounded-pill">
                                                    <i class="ph ph-user-plus me-2"></i> Baru
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-24 text-gray-500">Tidak ada data siswa ditemukan untuk kriteria filter ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Preview Results Guru (If requested) -->
    @if(isset($previewTeacherItems))
        <div class="col-12">
            <div class="card border border-gray-100 rounded-16 overflow-hidden shadow-xs">
                <div class="card-body p-24">
                    <div class="flex-between flex-wrap gap-16 mb-20 pb-16 border-bottom border-gray-100">
                        <div>
                            <h5 class="mb-4 fw-bold text-gray-900">Hasil Pratinjau Guru & Tendik dari WargaRPL API</h5>
                            <span class="text-13 text-gray-500">Menampilkan data pendidik WargaRPL sebelum disimpan ke database KREASI.</span>
                        </div>
                        <div class="d-flex gap-8 align-items-center flex-wrap">
                            <span class="badge bg-success-50 text-success-700 px-12 py-8 rounded-pill text-13 fw-semibold">
                                <i class="ph ph-user-plus me-4"></i> {{ $previewTeacherNewCount }} Guru Baru
                            </span>
                            <span class="badge bg-purple-50 text-purple-700 px-12 py-8 rounded-pill text-13 fw-semibold">
                                <i class="ph ph-arrows-clockwise me-4"></i> {{ $previewTeacherUpdateCount }} Guru Sudah Terdaftar
                            </span>
                            <button type="button" onclick="confirmTeacherSync()" class="btn btn-success rounded-pill px-20 text-13 fw-semibold shadow-sm">
                                <i class="ph ph-check me-4"></i> Lanjutkan Sinkronkan Semua Guru
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-13">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="ps-16 py-10">No</th>
                                    <th class="py-10">Nama Guru / PTK</th>
                                    <th class="py-10">NIP (Login ID)</th>
                                    <th class="py-10">Jabatan / Status</th>
                                    <th class="py-10">No. HP / Kontak</th>
                                    <th class="py-10">Tgl Lahir & Kata Sandi</th>
                                    <th class="py-10">Status API</th>
                                    <th class="py-10 pe-16">Aksi KREASI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($previewTeacherItems as $index => $item)
                                    <tr>
                                        <td class="ps-16 fw-semibold text-gray-500">#{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-gray-900">{{ $item['full_name'] }}</div>
                                            <div class="text-11 text-gray-400 font-monospace">{{ $item['email'] }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-gray-100 text-gray-800 font-monospace text-12 px-8 py-4 rounded-8">
                                                {{ $item['nip'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-main-50 text-main-700 text-11 fw-semibold me-4">
                                                {{ $item['ptk_type'] }}
                                            </span>
                                            <span class="badge bg-gray-100 text-gray-600 text-11">
                                                {{ $item['employment_type'] }}
                                            </span>
                                        </td>
                                        <td class="text-gray-600 font-monospace text-12">
                                            {{ $item['phone'] }}
                                        </td>
                                        <td>
                                            @if($item['is_nip_standard'])
                                                <div class="text-12">
                                                    <span class="text-gray-800 fw-medium">{{ $item['birth_date'] }}</span>
                                                    <div class="text-success-700 font-monospace text-11 mt-2">
                                                        Pass: <code>{{ $item['auto_password'] }}</code>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-12 text-gray-500">
                                                    Pass: <code>{{ $item['auto_password'] }}</code>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success-50 text-success-700 text-11">{{ $item['status'] }}</span>
                                        </td>
                                        <td class="pe-16">
                                            @if($item['is_existing'])
                                                <span class="badge bg-purple-50 text-purple-700 border border-purple-200 text-12 px-10 py-4 rounded-pill">
                                                    <i class="ph ph-arrows-clockwise me-2"></i> Perbarui
                                                </span>
                                            @else
                                                <span class="badge bg-success-50 text-success-700 border border-success-200 text-12 px-10 py-4 rounded-pill">
                                                    <i class="ph ph-user-plus me-2"></i> Baru
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-24 text-gray-500">Tidak ada data guru ditemukan untuk kriteria filter ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Riwayat Aktivitas Sinkronisasi WargaRPL (Audit Logs) -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 overflow-hidden shadow-xs">
            <div class="card-body p-24">
                <div class="flex-between flex-wrap gap-8 mb-20 pb-16 border-bottom border-gray-100">
                    <div>
                        <h5 class="mb-2 fw-bold text-gray-900">Riwayat Aktivitas Sinkronisasi WargaRPL</h5>
                        <span class="text-13 text-gray-500">Catatan audit riwayat eksekusi sinkronisasi data siswa dan guru dari WargaRPL.</span>
                    </div>
                    <a href="{{ route('admin.audit-logs') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-14 py-6 text-12">
                        Lihat Semua Audit Log <i class="ph ph-arrow-right ms-4"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-13">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="ps-16 py-12">Waktu</th>
                                <th class="py-12">Pengguna Eksekutor</th>
                                <th class="py-12">Aktivitas</th>
                                <th class="py-12 pe-16">Rincian Hasil Sinkronisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSyncLogs as $log)
                                <tr>
                                    <td class="ps-16 text-gray-500 text-12">
                                        <i class="ph ph-clock me-2"></i>{{ $log->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="fw-semibold text-gray-900">
                                        {{ $log->user->name ?? 'Administrator' }}
                                    </td>
                                    <td>
                                        @if($log->action === 'sync_wargarpl_teachers')
                                            <span class="badge bg-success-50 text-success-700 border border-success-200 text-11 px-8 py-3 rounded-pill">
                                                sync_guru
                                            </span>
                                        @else
                                            <span class="badge bg-purple-50 text-purple-700 border border-purple-200 text-11 px-8 py-3 rounded-pill">
                                                sync_siswa
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-gray-700 pe-16">
                                        {{ $log->description }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-20 text-gray-400 text-13">Belum ada riwayat sinkronisasi WargaRPL yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    const tabStudents = document.getElementById('tabContentStudents');
    const tabTeachers = document.getElementById('tabContentTeachers');
    const btnStudents = document.getElementById('tabBtnStudents');
    const btnTeachers = document.getElementById('tabBtnTeachers');

    if (tab === 'teachers') {
        tabStudents.classList.add('d-none');
        tabTeachers.classList.remove('d-none');
        btnStudents.className = 'btn rounded-pill px-20 py-10 text-14 fw-bold transition-2 sync-tab-btn btn-outline-gray text-gray-600';
        btnTeachers.className = 'btn rounded-pill px-20 py-10 text-14 fw-bold transition-2 sync-tab-btn btn-success text-white shadow-sm';
    } else {
        tabTeachers.classList.add('d-none');
        tabStudents.classList.remove('d-none');
        btnTeachers.className = 'btn rounded-pill px-20 py-10 text-14 fw-bold transition-2 sync-tab-btn btn-outline-gray text-gray-600';
        btnStudents.className = 'btn rounded-pill px-20 py-10 text-14 fw-bold transition-2 sync-tab-btn btn-main text-white shadow-sm';
    }

    // Update query string quietly
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url);
}

function confirmStudentSync() {
    Swal.fire({
        title: 'Mulai Sinkronisasi Siswa?',
        text: 'Sistem akan menarik data seluruh rombel & siswa dari WargaRPL API. Akun siswa baru akan dibuatkan dengan kata sandi tanggal lahir (DDMMYYYY).',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#064A8E',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ph ph-check me-2"></i> Ya, Sinkronkan Sekarang!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-24 p-24',
            confirmButton: 'rounded-pill px-20 py-10 text-14 fw-bold',
            cancelButton: 'rounded-pill px-20 py-10 text-14'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menyinkronkan Siswa...',
                html: '<div class="text-13 text-gray-500 mt-2">Mohon tunggu beberapa saat selagi sistem berkomunikasi dengan WargaRPL...</div>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('studentSyncForm').submit();
        }
    });
}

function confirmTeacherSync() {
    const isReset = document.getElementById('resetPasswordsCheck') && document.getElementById('resetPasswordsCheck').checked;
    let extraText = 'Akun guru dibuat/diperbarui dengan ID login NIP dan kata sandi Tanggal Lahir (DDMMYYYY).';
    if (isReset) {
        extraText += ' Opsi reset kata sandi aktif untuk guru yang sudah ada.';
    }

    Swal.fire({
        title: 'Mulai Sinkronisasi Guru & Tendik?',
        text: extraText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ph ph-check me-2"></i> Ya, Sinkronkan Guru!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-24 p-24',
            confirmButton: 'rounded-pill px-20 py-10 text-14 fw-bold',
            cancelButton: 'rounded-pill px-20 py-10 text-14'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menyinkronkan Guru...',
                html: '<div class="text-13 text-gray-500 mt-2">Menarik data dari endpoint /api/v1/employees. Mohon tunggu...</div>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('teacherSyncForm').submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const btnTestGlobal = document.getElementById('btnTestGlobal');
    const studentTokenInput = document.getElementById('studentApiTokenInput');
    const teacherTokenInput = document.getElementById('teacherApiTokenInput');
    const previewStudentTokenInput = document.getElementById('previewStudentTokenInput');
    const previewTeacherTokenInput = document.getElementById('previewTeacherTokenInput');
    const globalResultBox = document.getElementById('globalTestResult');

    function syncTokens(val) {
        if (studentTokenInput) studentTokenInput.value = val;
        if (teacherTokenInput) teacherTokenInput.value = val;
        if (previewStudentTokenInput) previewStudentTokenInput.value = val;
        if (previewTeacherTokenInput) previewTeacherTokenInput.value = val;
    }

    if (studentTokenInput) {
        studentTokenInput.addEventListener('input', (e) => syncTokens(e.target.value));
    }
    if (teacherTokenInput) {
        teacherTokenInput.addEventListener('input', (e) => syncTokens(e.target.value));
    }

    if (btnTestGlobal) {
        btnTestGlobal.addEventListener('click', function () {
            btnTestGlobal.disabled = true;
            btnTestGlobal.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menguji...';
            globalResultBox.className = 'p-12 rounded-10 alert alert-info py-8 text-13 d-flex align-items-center gap-8 mb-16 shadow-xs';
            globalResultBox.innerHTML = '<i class="ph ph-spinner ph-spin text-16"></i> Menghubungi server WargaRPL...';
            globalResultBox.classList.remove('d-none');

            const tokenVal = (studentTokenInput ? studentTokenInput.value : '') || (teacherTokenInput ? teacherTokenInput.value : '');

            fetch('{{ route('admin.students.sync.test') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ token: tokenVal })
            })
            .then(response => response.json())
            .then(data => {
                btnTestGlobal.disabled = false;
                btnTestGlobal.innerHTML = '<i class="ph ph-plugs-connected me-4"></i> Uji Koneksi Endpoint API';

                if (data.success) {
                    globalResultBox.className = 'p-12 rounded-10 alert alert-success py-8 text-13 d-flex align-items-center gap-8 mb-16 shadow-xs';
                    globalResultBox.innerHTML = '<i class="ph ph-check-circle text-18 text-success-600"></i> <span>' + data.message + '</span>';
                } else {
                    globalResultBox.className = 'p-12 rounded-10 alert alert-danger py-8 text-13 d-flex align-items-center gap-8 mb-16 shadow-xs';
                    globalResultBox.innerHTML = '<i class="ph ph-warning-circle text-18 text-danger-600"></i> <span>' + (data.error || 'Koneksi gagal') + '</span>';
                }
            })
            .catch(err => {
                btnTestGlobal.disabled = false;
                btnTestGlobal.innerHTML = '<i class="ph ph-plugs-connected me-4"></i> Uji Koneksi Endpoint API';
                globalResultBox.className = 'p-12 rounded-10 alert alert-danger py-8 text-13 d-flex align-items-center gap-8 mb-16 shadow-xs';
                globalResultBox.innerHTML = '<i class="ph ph-warning-circle text-18 text-danger-600"></i> Gagal menghubungi endpoint: ' + err;
            });
        });
    }
});
</script>
@endpush
@endsection
