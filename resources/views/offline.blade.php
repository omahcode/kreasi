@extends('layouts.auth')

@section('title', 'Koneksi Offline')

@section('content')
<div class="text-center py-24">
    <span class="w-80 h-80 rounded-circle bg-warning-50 text-warning-600 flex-center mx-auto text-40 mb-20 shadow-xs">
        <i class="ph ph-wifi-slash"></i>
    </span>
    <h3 class="mb-8 fw-bold text-gray-900">Anda Sedang Offline</h3>
    <p class="text-gray-600 text-15 mb-24" style="max-width: 400px; margin: 0 auto;">
        Koneksi internet Anda terputus. KREASI RPL tetap menyimpan data aplikasi pada perangkat Anda. Silakan hubungkan kembali internet Anda.
    </p>

    <div class="d-flex justify-content-center gap-12">
        <button type="button" onclick="window.location.reload()" class="btn btn-main rounded-pill px-24 py-10">
            <i class="ph ph-arrows-clockwise me-4"></i> Muat Ulang Halaman
        </button>
        <a href="{{ route('login') }}" class="btn btn-outline-gray rounded-pill px-20 py-10">
            Halaman Masuk
        </a>
    </div>
</div>
@endsection
