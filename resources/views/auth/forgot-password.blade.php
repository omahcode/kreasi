@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <h2 class="mb-8">Lupa Kata Sandi?</h2>
    <p class="text-gray-600 text-15 mb-32">
        Masukkan email terdaftar Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
    </p>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-24" role="alert">
            <i class="ph ph-check-circle me-8"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-24" role="alert">
            <i class="ph ph-warning-circle me-8"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-24">
            <label for="email" class="form-label mb-8 h6">Alamat Email</label>
            <div class="position-relative">
                <input type="email" 
                       class="form-control py-11 ps-40 @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="nama@sekolah.sch.id" 
                       required 
                       autofocus>
                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex">
                    <i class="ph ph-envelope"></i>
                </span>
            </div>
            @error('email')
                <div class="text-danger text-13 mt-6">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-main rounded-pill w-100">Kirim Tautan Reset Sandi</button>

        <a href="{{ route('login') }}" class="my-32 text-main-600 flex-align gap-8 justify-content-center">
            <i class="ph ph-arrow-left d-flex"></i> Kembali ke Halaman Masuk
        </a>
    </form>
@endsection
