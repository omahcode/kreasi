@extends('layouts.app')

@section('title', 'Logbook Aktivitas Siswa')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('siswa.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Logbook Harian PjBL</span></li>
                </ul>
            </div>
            <a href="{{ route('siswa.logbook.create') }}" class="btn btn-main rounded-pill px-16 text-13">
                <i class="ph ph-plus-circle me-4"></i> + Catat Logbook Baru
            </a>
        </div>
    </div>

    <!-- Logbook Timeline Content -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-24 bg-white">
            <div class="flex-between mb-20 pb-16 border-bottom border-gray-100">
                <div>
                    <h5 class="mb-2 fw-bold text-gray-900">Catatan Jurnal Aktivitas Siswa</h5>
                    <span class="text-13 text-gray-500">Mendokumentasikan progres harian dan tahapan pengerjaan proyek PjBL.</span>
                </div>
            </div>

            <div class="d-flex flex-column gap-16">
                @forelse($logs as $log)
                    <div class="p-20 rounded-16 border border-gray-100 hover-border-main-600 transition-2 bg-white">
                        <div class="flex-between flex-wrap gap-12 mb-12">
                            <div>
                                <span class="badge bg-main-50 text-main-600 text-12 mb-6">
                                    <i class="ph ph-folder me-2"></i> {{ $log->project->title ?? 'Proyek' }}
                                </span>
                                <h6 class="mb-0 text-14 text-gray-500">
                                    <i class="ph ph-clock me-2"></i> {{ $log->created_at->format('d M Y, H:i') }} ({{ $log->created_at->diffForHumans() }})
                                </h6>
                            </div>
                            <div>
                                <span class="badge bg-success-50 text-success-700 text-13 px-12 py-6 rounded-pill fw-semibold">
                                    Progres: {{ $log->progress_percentage }}%
                                </span>
                            </div>
                        </div>

                        <p class="text-14 text-gray-800 mb-12 whitespace-pre-line">{{ $log->description }}</p>

                        @if($log->image)
                            <div class="pt-8 border-top border-gray-100">
                                <span class="text-12 text-gray-500 d-block mb-8">Bukti Tangkapan Layar / Dokumentasi:</span>
                                <a href="{{ asset('storage/' . $log->image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $log->image) }}" alt="Dokumentasi Pekerjaan" class="rounded-12 border border-gray-200 shadow-xs" style="max-height: 220px; object-fit: cover;">
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-48 text-gray-400">
                        <span class="w-64 h-64 rounded-circle bg-main-50 text-main-600 flex-center mx-auto text-32 mb-16">
                            <i class="ph ph-notepad"></i>
                        </span>
                        <h5 class="mb-4 text-16 fw-bold text-gray-800">Belum Ada Catatan Logbook</h5>
                        <p class="text-gray-500 text-13 mb-20">Mulai catat aktivitas harian atau fitur yang Anda selesaikan hari ini.</p>
                        <a href="{{ route('siswa.logbook.create') }}" class="btn btn-main rounded-pill px-20 text-13">
                            + Catat Aktivitas Pertama
                        </a>
                    </div>
                @endforelse
            </div>

            @if($logs->hasPages())
                <div class="mt-24 pt-16 border-top border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
