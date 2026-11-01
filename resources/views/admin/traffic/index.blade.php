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
                <span class="text-main-600 text-14 fw-semibold">Trafik & Storage</span>
            </div>
            <h4 class="mb-4 text-22 fw-bold text-gray-800">Monitoring Trafik & Penyimpanan</h4>
            <p class="text-gray-500 text-14 mb-0">Pantau intensitas akses aplikasi secara real-time dan analisis penggunaan disk storage berkas siswa & proyek.</p>
        </div>
        <div class="flex-align gap-12 flex-wrap">
            <a href="{{ route('admin.student-progress.index') }}" class="btn btn-outline-main rounded-pill px-20 py-10 flex-align gap-8 text-14 fw-medium">
                <i class="ph ph-folder-open text-18"></i>
                <span>Kelola Berkas Siswa</span>
            </a>
            <form action="{{ route('admin.traffic.clear') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" 
                        class="btn btn-outline-danger rounded-pill px-20 py-10 flex-align gap-8 text-14 fw-medium"
                        data-confirm="Apakah Anda yakin ingin membersihkan log akses yang berumur lebih dari 30 hari? Tindakan ini tidak dapat dibatalkan."
                        data-confirm-title="Bersihkan Log Akses Lama?"
                        data-confirm-icon="warning"
                        data-confirm-btn="Ya, Bersihkan">
                    <i class="ph ph-trash text-18"></i>
                    <span>Bersihkan Log Lama (>30 Hari)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- 4 Key Stat Widgets -->
    <div class="row g-20 mb-24">
        <!-- Widget 1: Hits Today -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border border-gray-100 rounded-16 h-100 bg-white p-20 shadow-none hover-shadow-sm transition-2">
                <div class="flex-between align-items-start gap-12 mb-16">
                    <div>
                        <span class="text-gray-500 text-13 fw-medium d-block mb-4">Total Akses Hari Ini</span>
                        <h3 class="mb-0 text-24 fw-bold text-gray-800">{{ number_format($hitsToday) }}</h3>
                    </div>
                    <div class="w-48 h-48 rounded-12 bg-main-50 text-main-600 flex-center flex-shrink-0 text-24">
                        <i class="ph ph-activity"></i>
                    </div>
                </div>
                <div class="flex-align gap-8 text-12 text-gray-500">
                    <span class="badge bg-main-100 text-main-700 px-8 py-4 rounded-pill fw-semibold">
                        <i class="ph ph-broadcast me-2"></i> {{ $uniqueIpsToday }} IP Unik
                    </span>
                    <span>terdeteksi hari ini</span>
                </div>
            </div>
        </div>

        <!-- Widget 2: Weekly Hits -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border border-gray-100 rounded-16 h-100 bg-white p-20 shadow-none hover-shadow-sm transition-2">
                <div class="flex-between align-items-start gap-12 mb-16">
                    <div>
                        <span class="text-gray-500 text-13 fw-medium d-block mb-4">Trafik 7 Hari Terakhir</span>
                        <h3 class="mb-0 text-24 fw-bold text-gray-800">{{ number_format($hitsThisWeek) }}</h3>
                    </div>
                    <div class="w-48 h-48 rounded-12 bg-success-50 text-success-600 flex-center flex-shrink-0 text-24">
                        <i class="ph ph-chart-line-up"></i>
                    </div>
                </div>
                <div class="flex-align gap-8 text-12 text-gray-500">
                    <span class="text-gray-700 fw-semibold">{{ number_format($totalHits) }}</span>
                    <span>total keseluruhan log tersimpan</span>
                </div>
            </div>
        </div>

        <!-- Widget 3: Total Storage Used -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border border-gray-100 rounded-16 h-100 bg-white p-20 shadow-none hover-shadow-sm transition-2">
                <div class="flex-between align-items-start gap-12 mb-16">
                    <div>
                        <span class="text-gray-500 text-13 fw-medium d-block mb-4">Penggunaan Storage Disk</span>
                        <h3 class="mb-0 text-24 fw-bold text-gray-800">{{ $storage['total_formatted'] }}</h3>
                    </div>
                    <div class="w-48 h-48 rounded-12 bg-warning-50 text-warning-600 flex-center flex-shrink-0 text-24">
                        <i class="ph ph-hard-drive"></i>
                    </div>
                </div>
                <div class="flex-align gap-8 text-12 text-gray-500">
                    <span class="badge bg-warning-100 text-warning-800 px-8 py-4 rounded-pill fw-semibold">
                        {{ $storage['total_files'] }} Berkas
                    </span>
                    <span>dalam storage public</span>
                </div>
            </div>
        </div>

        <!-- Widget 4: Active Users Today -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border border-gray-100 rounded-16 h-100 bg-white p-20 shadow-none hover-shadow-sm transition-2">
                <div class="flex-between align-items-start gap-12 mb-16">
                    <div>
                        <span class="text-gray-500 text-13 fw-medium d-block mb-4">Pengguna Login Hari Ini</span>
                        <h3 class="mb-0 text-24 fw-bold text-gray-800">{{ number_format($uniqueUsersToday) }}</h3>
                    </div>
                    <div class="w-48 h-48 rounded-12 bg-purple-50 text-purple-600 flex-center flex-shrink-0 text-24">
                        <i class="ph ph-users"></i>
                    </div>
                </div>
                <div class="flex-align gap-8 text-12 text-gray-500">
                    <span class="text-success-600 fw-semibold flex-align gap-4">
                        <i class="ph ph-circle-fill text-8"></i> Live session aktif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <div class="row g-20 mb-24">
        <!-- Daily Hits Chart (14 Days) -->
        <div class="col-xl-8">
            <div class="card border border-gray-100 rounded-16 bg-white p-24 h-100 shadow-none">
                <div class="flex-between flex-wrap gap-12 mb-20">
                    <div>
                        <h5 class="mb-4 text-16 fw-bold text-gray-800">Tren Intensitas Akses Harian</h5>
                        <p class="text-gray-400 text-13 mb-0">Statistik jumlah request halaman selama 14 hari terakhir.</p>
                    </div>
                    <span class="badge bg-gray-50 text-gray-600 border border-gray-200 px-12 py-6 rounded-pill text-12 fw-medium">
                        14 Hari Terakhir
                    </span>
                </div>
                <div id="trafficDailyChart" style="min-height: 280px;"></div>
            </div>
        </div>

        <!-- Access Breakdown: Roles, Devices & Browsers -->
        <div class="col-xl-4">
            <div class="card border border-gray-100 rounded-16 bg-white p-24 h-100 shadow-none">
                <h5 class="mb-16 text-16 fw-bold text-gray-800">Distribusi Pengguna (7 Hari)</h5>
                
                <!-- Roles Breakdown -->
                <div class="mb-24">
                    <span class="text-gray-400 text-12 fw-semibold text-uppercase d-block mb-12">Berdasarkan Hak Akses / Peran</span>
                    @php
                        $roleTotal = max(1, array_sum($roleHits));
                    @endphp
                    <div class="d-flex flex-column gap-12">
                        <div>
                            <div class="flex-between text-13 mb-4">
                                <span class="fw-medium text-gray-700 flex-align gap-6">
                                    <span class="w-8 h-8 rounded-circle bg-main-600"></span> Siswa
                                </span>
                                <span class="text-gray-500 fw-semibold">{{ $roleHits['siswa'] }} ({{ round(($roleHits['siswa'] / $roleTotal) * 100) }}%)</span>
                            </div>
                            <div class="progress h-6 rounded-pill bg-gray-100">
                                <div class="progress-bar bg-main-600 rounded-pill" style="width: {{ round(($roleHits['siswa'] / $roleTotal) * 100) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex-between text-13 mb-4">
                                <span class="fw-medium text-gray-700 flex-align gap-6">
                                    <span class="w-8 h-8 rounded-circle bg-success-600"></span> Guru Pembimbing
                                </span>
                                <span class="text-gray-500 fw-semibold">{{ $roleHits['guru'] }} ({{ round(($roleHits['guru'] / $roleTotal) * 100) }}%)</span>
                            </div>
                            <div class="progress h-6 rounded-pill bg-gray-100">
                                <div class="progress-bar bg-success-600 rounded-pill" style="width: {{ round(($roleHits['guru'] / $roleTotal) * 100) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex-between text-13 mb-4">
                                <span class="fw-medium text-gray-700 flex-align gap-6">
                                    <span class="w-8 h-8 rounded-circle bg-warning-600"></span> Administrator
                                </span>
                                <span class="text-gray-500 fw-semibold">{{ $roleHits['admin'] }} ({{ round(($roleHits['admin'] / $roleTotal) * 100) }}%)</span>
                            </div>
                            <div class="progress h-6 rounded-pill bg-gray-100">
                                <div class="progress-bar bg-warning-600 rounded-pill" style="width: {{ round(($roleHits['admin'] / $roleTotal) * 100) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex-between text-13 mb-4">
                                <span class="fw-medium text-gray-700 flex-align gap-6">
                                    <span class="w-8 h-8 rounded-circle bg-gray-400"></span> Tamu / Guest (Login Page)
                                </span>
                                <span class="text-gray-500 fw-semibold">{{ $roleHits['guest'] }} ({{ round(($roleHits['guest'] / $roleTotal) * 100) }}%)</span>
                            </div>
                            <div class="progress h-6 rounded-pill bg-gray-100">
                                <div class="progress-bar bg-gray-400 rounded-pill" style="width: {{ round(($roleHits['guest'] / $roleTotal) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 my-16">

                <!-- Devices & Browsers Summary -->
                <div class="row g-12">
                    <div class="col-6">
                        <div class="p-12 rounded-12 bg-gray-50 border border-gray-100">
                            <span class="text-gray-400 text-11 fw-semibold d-block text-uppercase mb-4">Perangkat Utama</span>
                            <div class="flex-align gap-8 text-gray-800 fw-bold text-14">
                                <i class="ph ph-laptop text-18 text-main-600"></i>
                                <span>Desktop ({{ $deviceHits['Desktop'] }})</span>
                            </div>
                            <span class="text-12 text-gray-500 mt-2 d-block">Mobile: {{ $deviceHits['Mobile'] }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-12 rounded-12 bg-gray-50 border border-gray-100">
                            <span class="text-gray-400 text-11 fw-semibold d-block text-uppercase mb-4">Browser Populer</span>
                            <div class="flex-align gap-8 text-gray-800 fw-bold text-14">
                                <i class="ph ph-globe text-18 text-success-600"></i>
                                <span>Chrome ({{ $browserHits['Chrome'] }})</span>
                            </div>
                            <span class="text-12 text-gray-500 mt-2 d-block">Edge/Firefox: {{ $browserHits['Edge / Lainnya'] + $browserHits['Firefox'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Usage & Detail Section -->
    <div class="row g-20 mb-24">
        <!-- Storage Breakdown by Category -->
        <div class="col-xl-5">
            <div class="card border border-gray-100 rounded-16 bg-white p-24 h-100 shadow-none">
                <div class="flex-between flex-wrap gap-12 mb-20">
                    <div>
                        <h5 class="mb-4 text-16 fw-bold text-gray-800">Total Penggunaan Storage Disk</h5>
                        <p class="text-gray-400 text-13 mb-0">Total ruang penyimpanan dan rincian per kategori berkas.</p>
                    </div>
                    <span class="badge bg-main-50 text-main-600 border border-main-200 px-12 py-6 rounded-pill text-12 fw-semibold">
                        {{ $storage['total_formatted'] }}
                    </span>
                </div>

                <!-- Total Storage Summary Card (Tanpa Alokasi Kuota Khusus) -->
                <div class="p-20 rounded-16 bg-main-50 border border-main-100 mb-20">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-12">
                        <div class="d-flex align-items-center gap-12">
                            <div class="w-48 h-48 rounded-12 bg-main-600 text-white flex-center text-24 shadow-xs flex-shrink-0">
                                <i class="ph ph-hard-drives"></i>
                            </div>
                            <div>
                                <span class="text-12 text-main-700 fw-semibold d-block">Total Terpakai di Server</span>
                                <h3 class="text-22 fw-bold text-gray-900 mb-0">{{ $storage['total_formatted'] }}</h3>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-white text-main-700 border border-main-200 px-12 py-6 rounded-pill text-12 fw-semibold">
                                {{ $storage['total_files'] }} Berkas
                            </span>
                            <span class="d-block text-11 text-gray-500 mt-4">Direktori public storage</span>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown Rows -->
                <div class="d-flex flex-column gap-12">
                    @foreach($storage['categories'] as $catKey => $cat)
                        <div class="storage-category-card flex-between align-items-center gap-16 p-12 rounded-12 border border-gray-100 hover-bg-gray-50 transition-2 cursor-pointer" 
                             data-category="{{ $catKey }}" 
                             title="Klik untuk melihat berkas kategori ini">
                            <div class="flex-align gap-12">
                                <div class="w-40 h-40 rounded-10 bg-{{ $cat['color'] }}-50 text-{{ $cat['color'] }}-600 flex-center flex-shrink-0 text-20">
                                    <i class="ph {{ $cat['icon'] }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-2 text-14 fw-semibold text-gray-800">{{ $cat['label'] }}</h6>
                                    <span class="text-gray-400 text-12">{{ $cat['count'] }} berkas tersimpan</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="d-block text-14 fw-bold text-gray-800">{{ $cat['formatted'] }}</span>
                                <span class="badge bg-gray-100 text-gray-600 px-8 py-2 rounded-pill text-11 fw-medium">{{ $cat['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Detail Seluruh Berkas di Storage -->
        <div class="col-xl-7">
            <div class="card border border-gray-100 rounded-16 bg-white p-24 h-100 shadow-none d-flex flex-column">
                <div class="flex-between flex-wrap gap-12 mb-16">
                    <div>
                        <h5 class="mb-4 text-16 fw-bold text-gray-800">Detail Seluruh Berkas di Storage</h5>
                        <p class="text-gray-400 text-13 mb-0">Inspeksi, pratinjau, dan unduh seluruh berkas yang tersimpan.</p>
                    </div>
                    <div class="d-flex align-items-center gap-8">
                        <span id="storageFilteredCount" class="badge bg-gray-100 text-gray-700 text-12 px-12 py-6 rounded-pill fw-semibold">
                            {{ $storage['total_files'] }} Berkas
                        </span>
                        <a href="{{ route('admin.student-progress.index') }}" class="text-13 text-main-600 hover-text-main-700 fw-semibold flex-align gap-4">
                            <span>Hub Berkas Siswa</span>
                            <i class="ph ph-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Filter Pills & Search -->
                <div class="row g-2 mb-16 align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex gap-4 flex-wrap">
                            <button type="button" class="btn btn-sm btn-main storage-filter-btn rounded-pill px-12 py-4 text-11" data-filter="all">Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-gray storage-filter-btn rounded-pill px-12 py-4 text-11" data-filter="pkl_chapters">PKL</button>
                            <button type="button" class="btn btn-sm btn-outline-gray storage-filter-btn rounded-pill px-12 py-4 text-11" data-filter="avatars">Foto Profil</button>
                            <button type="button" class="btn btn-sm btn-outline-gray storage-filter-btn rounded-pill px-12 py-4 text-11" data-filter="project_submissions">Submisi</button>
                            <button type="button" class="btn btn-sm btn-outline-gray storage-filter-btn rounded-pill px-12 py-4 text-11" data-filter="project_attachments">Lampiran</button>
                            <button type="button" class="btn btn-sm btn-outline-gray storage-filter-btn rounded-pill px-12 py-4 text-11" data-filter="other">Lainnya</button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="position-relative">
                            <input type="text" id="storageFileSearch" class="form-control form-control-sm border-gray-100 rounded-pill ps-32 text-12" placeholder="Cari nama / path berkas...">
                            <span class="position-absolute top-50 translate-middle-y ms-12 text-gray-400 d-flex pointer-event-none">
                                <i class="ph ph-magnifying-glass"></i>
                            </span>
                        </div>
                    </div>
                </div>

                @if(count($storage['all_files']) > 0)
                    <div class="table-responsive flex-grow-1" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="sticky-top bg-white border-bottom border-gray-100 text-gray-400 text-12 text-uppercase">
                                <tr>
                                    <th class="ps-0 py-8">Nama & Path Berkas</th>
                                    <th class="py-8">Kategori</th>
                                    <th class="py-8 text-end">Ukuran</th>
                                    <th class="py-8 text-end pe-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="storageFileListBody">
                                @foreach($storage['all_files'] as $idx => $f)
                                    <tr class="storage-file-row border-bottom border-gray-50 hover-bg-gray-50 transition-1"
                                        data-category="{{ $f['category_key'] }}"
                                        data-search="{{ strtolower($f['name'] . ' ' . $f['relative_path'] . ' ' . $f['category']) }}">
                                        <td class="ps-0 py-10">
                                            <div class="flex-align gap-10">
                                                <div class="w-32 h-32 rounded-8 bg-gray-100 text-gray-600 flex-center flex-shrink-0 text-16">
                                                    @if(in_array($f['extension'], ['pdf']))
                                                        <i class="ph ph-file-pdf text-danger-600"></i>
                                                    @elseif(in_array($f['extension'], ['zip', 'rar', '7z']))
                                                        <i class="ph ph-archive text-warning-600"></i>
                                                    @elseif(in_array($f['extension'], ['jpg', 'jpeg', 'png', 'webp']))
                                                        <i class="ph ph-image text-info-600"></i>
                                                    @elseif(in_array($f['extension'], ['doc', 'docx']))
                                                        <i class="ph ph-file-doc text-primary-600"></i>
                                                    @else
                                                        <i class="ph ph-file text-gray-600"></i>
                                                    @endif
                                                </div>
                                                <div style="max-width: 210px;">
                                                    <span class="d-block text-13 fw-semibold text-gray-800 text-truncate" title="{{ $f['name'] }}">
                                                        {{ $f['name'] }}
                                                    </span>
                                                    <span class="text-gray-400 text-11 text-truncate d-block" title="{{ $f['relative_path'] }}">
                                                        {{ $f['relative_path'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-10">
                                            <span class="badge bg-{{ $f['color'] }}-50 text-{{ $f['color'] }}-700 border border-{{ $f['color'] }}-200 px-8 py-3 rounded-pill text-11 fw-medium">
                                                {{ Str::limit($f['category'], 16) }}
                                            </span>
                                        </td>
                                        <td class="py-10 text-end">
                                            <span class="text-12 fw-bold text-gray-800 d-block">{{ $f['size_formatted'] }}</span>
                                            <span class="text-10 text-gray-400">{{ $f['modified_at']->format('d/m/y H:i') }}</span>
                                        </td>
                                        <td class="py-10 text-end pe-0 text-nowrap">
                                            <div class="d-inline-flex gap-4">
                                                <a href="{{ $f['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-8 py-2 text-11" title="Lihat / Buka Berkas">
                                                    <i class="ph ph-eye me-2"></i> Lihat
                                                </a>
                                                <a href="{{ $f['url'] }}" download class="btn btn-sm btn-outline-secondary rounded-pill px-8 py-2 text-11" title="Unduh Berkas">
                                                    <i class="ph ph-download-simple"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-8 py-2 text-11" data-bs-toggle="modal" data-bs-target="#fileModal{{ $idx }}" title="Detail Info">
                                                    <i class="ph ph-info"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Detail Berkas -->
                                    <div class="modal fade" id="fileModal{{ $idx }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-16 border-0 shadow-lg">
                                                <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                                                    <div class="d-flex align-items-center gap-10">
                                                        <span class="w-36 h-36 rounded-circle bg-main-50 text-main-600 flex-center text-18">
                                                            <i class="ph ph-file-text"></i>
                                                        </span>
                                                        <div>
                                                            <h5 class="modal-title fw-bold text-16 mb-0">Detail Berkas Storage</h5>
                                                            <span class="text-12 text-gray-400">Informasi berkas pada direktori server</span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-24">
                                                    <div class="p-16 rounded-12 bg-gray-50 border border-gray-100 mb-16">
                                                        <label class="text-11 text-gray-400 text-uppercase fw-bold d-block mb-4">Nama Berkas</label>
                                                        <div class="text-14 fw-bold text-gray-900 text-break">{{ $f['name'] }}</div>
                                                    </div>

                                                    <div class="row g-3">
                                                        <div class="col-6">
                                                            <label class="text-11 text-gray-400 text-uppercase fw-bold d-block mb-2">Kategori</label>
                                                            <span class="badge bg-{{ $f['color'] }}-50 text-{{ $f['color'] }}-700 border border-{{ $f['color'] }}-200 px-10 py-4 rounded-pill text-12">
                                                                {{ $f['category'] }}
                                                            </span>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="text-11 text-gray-400 text-uppercase fw-bold d-block mb-2">Format / Ekstensi</label>
                                                            <span class="text-13 fw-semibold text-gray-800">.{{ strtoupper($f['extension']) ?: '-' }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="text-11 text-gray-400 text-uppercase fw-bold d-block mb-2">Ukuran Berkas</label>
                                                            <div class="text-13 fw-bold text-gray-900">{{ $f['size_formatted'] }} <small class="text-gray-400 font-monospace">({{ number_format($f['size']) }} bytes)</small></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="text-11 text-gray-400 text-uppercase fw-bold d-block mb-2">Terakhir Diubah</label>
                                                            <div class="text-13 text-gray-800">{{ $f['modified_at']->isoFormat('D MMMM Y, HH:mm') }}</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="text-11 text-gray-400 text-uppercase fw-bold d-block mb-2">Path Lengkap Storage</label>
                                                            <code class="d-block p-10 rounded-8 bg-gray-100 text-12 text-break font-monospace">storage/app/public/{{ $f['relative_path'] }}</code>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top border-gray-100 py-12 px-24 flex-between">
                                                    <a href="{{ $f['url'] }}" download class="btn btn-outline-secondary rounded-pill px-16 text-12">
                                                        <i class="ph ph-download-simple me-2"></i> Unduh
                                                    </a>
                                                    <div class="d-flex gap-8">
                                                        <button type="button" class="btn btn-outline-gray rounded-pill px-16 text-12" data-bs-dismiss="modal">Tutup</button>
                                                        <a href="{{ $f['url'] }}" target="_blank" class="btn btn-main rounded-pill px-18 text-12">
                                                            <i class="ph ph-arrow-square-out me-2"></i> Buka / Pratinjau
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="storageEmptySearch" class="text-center py-32 d-none">
                            <i class="ph ph-magnifying-glass text-32 text-gray-300 mb-8 d-block"></i>
                            <span class="text-gray-500 text-13">Tidak ada berkas yang sesuai dengan kata kunci pencarian atau filter.</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-40 my-auto">
                        <i class="ph ph-folder-dashed text-48 text-gray-300 mb-8 d-block"></i>
                        <span class="text-gray-500 text-14">Belum ada berkas yang diunggah ke storage sistem.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Real-time Access Activity Logs Table -->
    <div class="card border border-gray-100 rounded-16 bg-white p-24 shadow-none">
        <div class="flex-between flex-wrap gap-12 mb-20">
            <div>
                <h5 class="mb-4 text-16 fw-bold text-gray-800">Aktivitas Trafik Terkini (Real-Time Requests)</h5>
                <p class="text-gray-400 text-13 mb-0">Menampilkan 25 log request terakhir yang masuk ke server KREASI RPL.</p>
            </div>
            <div class="flex-align gap-8">
                <span class="badge bg-success-50 text-success-700 border border-success-200 px-12 py-6 rounded-pill text-12 fw-semibold flex-align gap-4">
                    <i class="ph ph-check-circle text-14"></i> Pencatatan Aktif
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-gray-50 text-gray-500 text-12 text-uppercase">
                    <tr>
                        <th class="py-12 px-16 rounded-start-8">Waktu</th>
                        <th class="py-12 px-16">Pengguna</th>
                        <th class="py-12 px-16">Metode & Halaman (Path)</th>
                        <th class="py-12 px-16">IP Address</th>
                        <th class="py-12 px-16 rounded-end-8">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        <tr class="border-bottom border-gray-50 text-13">
                            <td class="py-12 px-16 text-nowrap text-gray-500">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                                <span class="text-gray-400 text-11 d-block">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="py-12 px-16">
                                @if($log->user)
                                    <div class="flex-align gap-8">
                                        <div class="w-28 h-28 rounded-circle bg-main-100 text-main-700 flex-center text-12 fw-bold">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-semibold text-gray-800">{{ $log->user->name }}</span>
                                            <span class="badge bg-main-50 text-main-600 px-6 py-2 rounded-pill text-10 fw-medium">
                                                {{ strtoupper($log->user->role) }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 fst-italic">Tamu / Belum Login</span>
                                @endif
                            </td>
                            <td class="py-12 px-16">
                                <div class="flex-align gap-8">
                                    <span class="badge {{ $log->method === 'POST' ? 'bg-primary-50 text-primary-700' : ($log->method === 'DELETE' ? 'bg-danger-50 text-danger-700' : 'bg-gray-100 text-gray-700') }} px-8 py-3 rounded-pill text-11 fw-semibold">
                                        {{ $log->method }}
                                    </span>
                                    <span class="text-gray-700 font-monospace text-12">{{ Str::limit($log->path, 45) }}</span>
                                </div>
                            </td>
                            <td class="py-12 px-16 font-monospace text-gray-500 text-12">
                                {{ $log->ip_address }}
                            </td>
                            <td class="py-12 px-16">
                                @php
                                    $sc = $log->status_code ?? 200;
                                    $badgeColor = $sc >= 500 ? 'danger' : ($sc >= 400 ? 'warning' : 'success');
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}-50 text-{{ $badgeColor }}-700 border border-{{ $badgeColor }}-200 px-8 py-4 rounded-pill text-11 fw-bold">
                                    {{ $sc }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-32 text-gray-400">
                                Belum ada catatan log aktivitas yang terekam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Daily Traffic Hits Chart
        const chartCategories = @json($chartDates);
        const chartSeriesData = @json($chartHits);

        const options = {
            series: [{
                name: 'Jumlah Request / Akses',
                data: chartSeriesData
            }],
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 2.5,
                colors: ['#064A8E']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 90, 100],
                    colorStops: [
                        { offset: 0, color: '#064A8E', opacity: 0.3 },
                        { offset: 100, color: '#064A8E', opacity: 0.01 }
                    ]
                }
            },
            xaxis: {
                categories: chartCategories,
                labels: {
                    style: { colors: '#94a3b8', fontSize: '11px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '11px' },
                    formatter: function (val) { return Math.round(val); }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 3,
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val + " Hits";
                    }
                }
            },
            colors: ['#064A8E']
        };

        const chart = new ApexCharts(document.querySelector("#trafficDailyChart"), options);
        chart.render();

        // Storage files live search and category filter
        const searchInput = document.getElementById('storageFileSearch');
        const filterButtons = document.querySelectorAll('.storage-filter-btn');
        const categoryCards = document.querySelectorAll('.storage-category-card');
        const fileRows = document.querySelectorAll('.storage-file-row');
        const emptyRow = document.getElementById('storageEmptySearch');
        const countBadge = document.getElementById('storageFilteredCount');

        let currentCategory = 'all';
        let currentSearch = '';

        function filterFiles() {
            let visibleCount = 0;
            fileRows.forEach(row => {
                const rowCat = row.getAttribute('data-category');
                const rowText = row.getAttribute('data-search') ? row.getAttribute('data-search').toLowerCase() : '';

                const matchCat = (currentCategory === 'all' || rowCat === currentCategory);
                const matchSearch = (!currentSearch || rowText.includes(currentSearch));

                if (matchCat && matchSearch) {
                    row.classList.remove('d-none');
                    visibleCount++;
                } else {
                    row.classList.add('d-none');
                }
            });

            if (emptyRow) {
                emptyRow.classList.toggle('d-none', visibleCount > 0);
            }
            if (countBadge) {
                countBadge.textContent = visibleCount + ' Berkas';
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                currentSearch = this.value.trim().toLowerCase();
                filterFiles();
            });
        }

        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                filterButtons.forEach(b => {
                    b.classList.remove('btn-main');
                    b.classList.add('btn-outline-gray');
                });
                this.classList.remove('btn-outline-gray');
                this.classList.add('btn-main');

                currentCategory = this.getAttribute('data-filter');
                filterFiles();
            });
        });

        categoryCards.forEach(card => {
            card.addEventListener('click', function() {
                const cat = this.getAttribute('data-category');
                const targetBtn = document.querySelector(`.storage-filter-btn[data-filter="${cat}"]`);
                if (targetBtn) {
                    targetBtn.click();
                }
            });
        });
    });
</script>
@endpush

