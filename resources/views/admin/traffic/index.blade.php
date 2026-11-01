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

    <!-- Storage Usage & Largest Files Section -->
    <div class="row g-20 mb-24">
        <!-- Storage Breakdown by Category -->
        <div class="col-xl-6">
            <div class="card border border-gray-100 rounded-16 bg-white p-24 h-100 shadow-none">
                <div class="flex-between flex-wrap gap-12 mb-20">
                    <div>
                        <h5 class="mb-4 text-16 fw-bold text-gray-800">Analisis Kapasitas Storage Disk</h5>
                        <p class="text-gray-400 text-13 mb-0">Rincian ruang penyimpanan berdasarkan kategori berkas sistem.</p>
                    </div>
                    <span class="badge bg-main-50 text-main-600 border border-main-200 px-12 py-6 rounded-pill text-12 fw-semibold">
                        {{ $storage['total_formatted'] }} Terpakai
                    </span>
                </div>

                <!-- Disk Meter Bar -->
                <div class="p-16 rounded-12 bg-gray-50 border border-gray-100 mb-20">
                    <div class="flex-between text-13 mb-6">
                        <span class="text-gray-600 fw-medium">Alokasi Kuota Storage (5 GB Target)</span>
                        <span class="fw-bold text-gray-800">{{ $storage['quota_percentage'] }}% ({{ $storage['total_formatted'] }} / {{ $storage['quota_formatted'] }})</span>
                    </div>
                    <div class="progress h-8 rounded-pill bg-gray-200">
                        <div class="progress-bar bg-main-600 rounded-pill" role="progressbar" style="width: {{ max(1, $storage['quota_percentage']) }}%"></div>
                    </div>
                    <div class="flex-between text-11 text-gray-400 mt-6">
                        <span>0 GB</span>
                        <span>5.00 GB</span>
                    </div>
                </div>

                <!-- Category Breakdown Rows -->
                <div class="d-flex flex-column gap-16">
                    @foreach($storage['categories'] as $catKey => $cat)
                        <div class="flex-between align-items-center gap-16 p-12 rounded-12 border border-gray-100 hover-bg-gray-50 transition-2">
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

        <!-- 10 Largest Files in Storage -->
        <div class="col-xl-6">
            <div class="card border border-gray-100 rounded-16 bg-white p-24 h-100 shadow-none">
                <div class="flex-between flex-wrap gap-12 mb-20">
                    <div>
                        <h5 class="mb-4 text-16 fw-bold text-gray-800">10 Berkas Terbesar di Server</h5>
                        <p class="text-gray-400 text-13 mb-0">Pantau file dengan konsumsi storage terbesar untuk optimalisasi.</p>
                    </div>
                    <a href="{{ route('admin.student-progress.index') }}" class="text-13 text-main-600 hover-text-main-700 fw-semibold flex-align gap-4">
                        <span>Lihat Semua Berkas</span>
                        <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                @if(count($storage['largest_files']) > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr class="border-bottom border-gray-100 text-gray-400 text-12 text-uppercase">
                                    <th class="ps-0 py-8">Nama Berkas</th>
                                    <th class="py-8">Kategori</th>
                                    <th class="py-8 text-end pe-0">Ukuran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($storage['largest_files'] as $f)
                                    <tr class="border-bottom border-gray-50 hover-bg-gray-50 transition-1">
                                        <td class="ps-0 py-10">
                                            <div class="flex-align gap-10">
                                                <div class="w-32 h-32 rounded-8 bg-gray-100 text-gray-600 flex-center flex-shrink-0 text-16">
                                                    @if(in_array($f['extension'], ['pdf']))
                                                        <i class="ph ph-file-pdf text-danger-600"></i>
                                                    @elseif(in_array($f['extension'], ['zip', 'rar', '7z']))
                                                        <i class="ph ph-archive text-warning-600"></i>
                                                    @elseif(in_array($f['extension'], ['jpg', 'jpeg', 'png', 'webp']))
                                                        <i class="ph ph-image text-info-600"></i>
                                                    @else
                                                        <i class="ph ph-file text-gray-600"></i>
                                                    @endif
                                                </div>
                                                <div style="max-width: 200px;">
                                                    <span class="d-block text-13 fw-semibold text-gray-800 text-truncate" title="{{ $f['name'] }}">
                                                        {{ $f['name'] }}
                                                    </span>
                                                    <span class="text-gray-400 text-11">{{ $f['modified_at']->format('d M Y, H:i') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-10">
                                            <span class="badge bg-{{ $f['color'] }}-50 text-{{ $f['color'] }}-700 border border-{{ $f['color'] }}-200 px-8 py-3 rounded-pill text-11 fw-medium">
                                                {{ Str::limit($f['category'], 18) }}
                                            </span>
                                        </td>
                                        <td class="py-10 text-end pe-0">
                                            <span class="text-13 fw-bold text-gray-800">{{ $f['size_formatted'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-40">
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
    });
</script>
@endpush

