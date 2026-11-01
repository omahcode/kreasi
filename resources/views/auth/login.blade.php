@extends('layouts.auth')

@section('title', 'Masuk ke Akun')

@section('content')
    <h2 class="mb-8">Selamat Datang Kembali! &#128075;</h2>
    <p class="text-gray-600 text-15 mb-32">Masuk untuk mengakses dashboard PjBL dan bimbingan PKL Anda.</p>

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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-24">
            <label for="login" class="form-label mb-8 h6">Email, NIS, atau NIP</label>
            <div class="position-relative">
                <input type="text" 
                       class="form-control py-11 ps-40 @error('login') is-invalid @enderror" 
                       id="login" 
                       name="login" 
                       value="{{ old('login') }}" 
                       placeholder="Masukkan Email, NIS Siswa, atau NIP Guru" 
                       required 
                       autofocus>
                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex">
                    <i class="ph ph-user"></i>
                </span>
            </div>
            @error('login')
                <div class="text-danger text-13 mt-6">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-24">
            <label for="password" class="form-label mb-8 h6">Kata Sandi</label>
            <div class="position-relative">
                <input type="password" 
                       class="form-control py-11 ps-40 @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       placeholder="Masukkan kata sandi akun" 
                       required>
                <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" id="#password" style="cursor: pointer;"></span>
                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex">
                    <i class="ph ph-lock"></i>
                </span>
            </div>
            @error('password')
                <div class="text-danger text-13 mt-6">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-32 flex-between flex-wrap gap-8">
            <div class="form-check mb-0 flex-shrink-0">
                <input class="form-check-input flex-shrink-0 rounded-4" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label text-15 flex-grow-1" for="remember">Ingat Saya</label>
            </div>
            <a href="{{ route('password.request') }}" class="text-main-600 hover-text-decoration-underline text-15 fw-medium">Lupa Kata Sandi?</a>
        </div>

        <button type="submit" class="btn btn-main rounded-pill w-100">Masuk ke KREASI RPL</button>
    </form>
@endsection
