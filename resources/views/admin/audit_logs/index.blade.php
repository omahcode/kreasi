@extends('layouts.app')

@section('title', 'Monitoring Aktivitas & Audit Log')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Audit Log & Aktivitas</span></li>
                </ul>
            </div>
            <div>
                <!-- Search -->
                <form method="GET" action="{{ route('admin.audit-logs') }}" class="w-300 position-relative">
                    <input type="text" 
                           name="search" 
                           class="form-control ps-40 h-40 border-gray-100 rounded-pill text-13" 
                           placeholder="Cari aksi, user, deskripsi..." 
                           value="{{ $search }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-400 d-flex pointer-event-none">
                        <i class="ph ph-magnifying-glass"></i>
                    </span>
                </form>
            </div>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-gray-50 text-gray-600 text-13">
                            <tr>
                                <th class="ps-24">Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi (Action)</th>
                                <th>Deskripsi</th>
                                <th>Alamat IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td class="ps-24 text-13 text-gray-500" style="white-space: nowrap;">
                                        <div class="fw-medium text-gray-800">{{ $log->created_at->format('d M Y, H:i') }}</div>
                                        <small class="text-gray-400">{{ $log->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <div class="d-flex align-items-center gap-8">
                                                <img src="{{ $log->user->avatar_url }}" alt="" class="w-32 h-32 rounded-circle object-fit-cover">
                                                <div>
                                                    <span class="text-13 fw-semibold text-gray-900 d-block">{{ $log->user->name }}</span>
                                                    <span class="text-11 text-gray-400">{{ $log->user->email }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-13 text-gray-400 fst-italic">Sistem / Tamu</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-main-50 text-main-600 text-12 font-monospace">{{ $log->action }}</span>
                                    </td>
                                    <td>
                                        <span class="text-13 text-gray-700">{{ $log->description ?: '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-12 text-gray-500 font-monospace">{{ $log->ip_address ?: '127.0.0.1' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-40 text-gray-400">
                                        <i class="ph ph-activity text-36 mb-8 d-block"></i>
                                        Belum ada riwayat aktivitas log yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="p-20 border-top border-gray-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
