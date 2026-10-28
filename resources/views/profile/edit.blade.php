@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="row gy-4">
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16">
            <div class="card-body p-24">
                <h4 class="mb-8 fw-bold text-gray-900">Pengaturan Akun & Profil</h4>
                <p class="text-gray-500 text-14 mb-24">Perbarui informasi identitas akun, kontak, dan kata sandi Anda.</p>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-4 text-center border-end border-gray-100 pe-md-32">
                            <div class="mb-16 position-relative d-inline-block">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-120 h-120 rounded-circle object-fit-cover border border-4 border-gray-100 shadow-sm">
                            </div>
                            <h5 class="mb-4 text-16 fw-bold">{{ $user->name }}</h5>
                            <span class="badge bg-main-50 text-main-600 text-12 text-uppercase mb-12">
                                {{ str_replace('_', ' ', $user->role) }}
                            </span>
                            
                            <div class="mt-16 text-start">
                                <label for="avatar" class="form-label text-13 fw-semibold">Ubah Foto Profil</label>
                                <input type="file" name="avatar" id="avatar" class="form-control form-control-sm" accept="image/*">
                                <small class="text-gray-400 text-11">Maks. 2MB (JPG, PNG, WebP)</small>
                            </div>
                        </div>

                        <div class="col-md-8 ps-md-32">
                            <h5 class="text-15 fw-bold text-gray-800 mb-16 pb-8 border-bottom border-gray-100">Informasi Pribadi</h5>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Alamat Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Nomor WhatsApp / HP</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                                </div>

                                @if($user->student)
                                    <div class="col-sm-3">
                                        <label class="form-label text-13 fw-semibold">NIS</label>
                                        <input type="text" class="form-control bg-gray-50" value="{{ $user->student->nis }}" disabled>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label text-13 fw-semibold">Kelas</label>
                                        <input type="text" class="form-control bg-gray-50" value="{{ $user->student->schoolClass->name ?? '-' }}" disabled>
                                    </div>
                                    @if($user->student->birth_date)
                                        <div class="col-sm-6">
                                            <label class="form-label text-13 fw-semibold">Tanggal Lahir</label>
                                            <input type="text" class="form-control bg-gray-50" value="{{ \Carbon\Carbon::parse($user->student->birth_date)->translatedFormat('d F Y') }}" disabled>
                                        </div>
                                    @endif
                                @elseif($user->teacher)
                                    <div class="col-sm-6">
                                        <label class="form-label text-13 fw-semibold">NIP / Kode Guru</label>
                                        <input type="text" class="form-control bg-gray-50" value="{{ $user->teacher->teacher_number }}" disabled>
                                    </div>
                                @endif
                            </div>

                            <h5 class="text-15 fw-bold text-gray-800 mt-32 mb-16 pb-8 border-bottom border-gray-100">Keamanan & Ubah Kata Sandi (Opsional)</h5>

                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <label class="form-label text-13 fw-semibold">Kata Sandi Saat Ini</label>
                                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Sandi saat ini">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label text-13 fw-semibold">Kata Sandi Baru</label>
                                    <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Min. 8 karakter">
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label text-13 fw-semibold">Konfirmasi Sandi Baru</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi sandi baru">
                                </div>
                            </div>

                            <div class="mt-32">
                                <button type="submit" class="btn btn-main rounded-pill px-24">
                                    <i class="ph ph-floppy-disk me-4"></i> Simpan Perubahan Profil
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dedicated Change Password Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16">
            <div class="card-body p-24">
                <div class="d-flex align-items-center gap-12 mb-8">
                    <span class="w-40 h-40 rounded-circle bg-warning-50 text-warning-600 flex-center text-20 shrink-0">
                        <i class="ph ph-shield-check"></i>
                    </span>
                    <div>
                        <h4 class="mb-2 fw-bold text-gray-900">Ubah Kata Sandi Akun</h4>
                        <p class="text-gray-500 text-14 mb-0">Pastikan akun Anda menggunakan kata sandi acak dan panjang agar tetap terlindungi.</p>
                    </div>
                </div>

                <hr class="border-gray-100 my-20">

                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label text-13 fw-semibold">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan kata sandi lama Anda" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-gray-400 text-12">Bagi siswa yang baru diimpor, sandi awal adalah tanggal lahir (DDMMYYYY) atau nomor NIS.</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-13 fw-semibold">Kata Sandi Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-13 fw-semibold">Ulangi Kata Sandi Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" required>
                        </div>
                    </div>

                    <div class="mt-24 d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning rounded-pill px-24 text-white">
                            <i class="ph ph-key me-4"></i> Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
