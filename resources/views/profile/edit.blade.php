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
                                <img id="avatarPreview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-120 h-120 rounded-circle object-fit-cover border border-4 border-gray-100 shadow-sm">
                            </div>
                            <h5 class="mb-4 text-16 fw-bold">{{ $user->name }}</h5>
                            <span class="badge bg-main-50 text-main-600 text-12 text-uppercase mb-12">
                                {{ str_replace('_', ' ', $user->role) }}
                            </span>
                            
                            <div class="mt-16 text-start">
                                <label for="avatar" class="form-label text-13 fw-semibold">Ubah Foto Profil</label>
                                <input type="file" name="avatar" id="avatar" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/jpg">
                                <small class="text-gray-400 text-11 d-block mt-4">Batas 2MB (JPG, PNG, WebP). Foto &gt;2MB otomatis dikompres.</small>
                                <div id="avatarCompressStatus" class="mt-8 text-11 d-none"></div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const statusDiv = document.getElementById('avatarCompressStatus');

    if (!avatarInput || !avatarPreview) return;

    avatarInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        // Ensure file is image
        if (!file.type.startsWith('image/')) {
            Swal.fire({
                icon: 'error',
                title: 'Format Berkas Salah',
                text: 'Harap unggah berkas gambar foto (JPG, PNG, atau WebP).',
                confirmButtonColor: '#064A8E'
            });
            this.value = '';
            return;
        }

        const maxSizeBytes = 2 * 1024 * 1024; // 2 MB

        if (file.size > maxSizeBytes) {
            const rawMb = (file.size / (1024 * 1024)).toFixed(2);

            // Alert user that the photo exceeds 2MB and system will automatically compress it
            Swal.fire({
                icon: 'info',
                title: 'Foto Melebihi 2 MB',
                html: `Ukuran foto kamu (<b>${rawMb} MB</b>) melebihi batas 2 MB.<br><span class="text-main-600 fw-semibold">Sistem akan otomatis mengompres foto kamu</span> agar ukurannya pas, ringan, dan cepat disimpan.`,
                confirmButtonText: 'Baik, Lanjutkan',
                confirmButtonColor: '#064A8E',
                customClass: {
                    popup: 'rounded-24 p-24 shadow-2xl border border-gray-100',
                    title: 'text-18 fw-bold text-gray-900 mb-8',
                    htmlContainer: 'text-14 text-gray-600 mb-20',
                    confirmButton: 'btn btn-main rounded-pill px-24 py-10 fw-semibold'
                }
            });

            // Perform automatic client-side compression
            compressImage(file, 1000, 0.82, function(compressedFile) {
                // Assign compressed file to the input element via DataTransfer
                try {
                    const dt = new DataTransfer();
                    dt.items.add(compressedFile);
                    avatarInput.files = dt.files;
                } catch (e) {
                    console.warn('DataTransfer not supported, backend will handle compression:', e);
                }

                // Update preview image
                const previewUrl = URL.createObjectURL(compressedFile);
                avatarPreview.src = previewUrl;

                const compressedKb = (compressedFile.size / 1024).toFixed(0);
                if (statusDiv) {
                    statusDiv.classList.remove('d-none');
                    statusDiv.className = 'mt-8 text-11 text-success fw-medium d-flex align-items-center gap-1';
                    statusDiv.innerHTML = `<i class="ph ph-check-circle"></i> Berhasil dikompres: <b>${rawMb} MB</b> ➔ <b>${compressedKb} KB</b>`;
                }

                // Toast notification
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    width: 'auto',
                    customClass: {
                        popup: 'rounded-16 shadow-lg border border-gray-100 py-12 px-18',
                        title: 'text-14 fw-semibold text-gray-800'
                    }
                });
                Toast.fire({
                    icon: 'success',
                    title: `Foto berhasil dikompres (${compressedKb} KB)`
                });
            });
        } else {
            // Under 2MB
            const previewUrl = URL.createObjectURL(file);
            avatarPreview.src = previewUrl;

            if (statusDiv) {
                statusDiv.classList.remove('d-none');
                statusDiv.className = 'mt-8 text-11 text-muted d-flex align-items-center gap-1';
                statusDiv.innerHTML = `<i class="ph ph-file-image"></i> Ukuran: ${(file.size / 1024).toFixed(0)} KB (Sesuai batas)`;
            }
        }
    });

    function compressImage(file, maxDimension, quality, callback) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                let width = img.width;
                let height = img.height;

                if (width > maxDimension || height > maxDimension) {
                    if (width >= height) {
                        height = Math.round((height / width) * maxDimension);
                        width = maxDimension;
                    } else {
                        width = Math.round((width / height) * maxDimension);
                        height = maxDimension;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(function(blob) {
                    if (!blob) {
                        callback(file);
                        return;
                    }
                    const baseName = file.name.substring(0, file.name.lastIndexOf('.')) || 'avatar';
                    const newFile = new File([blob], `${baseName}.webp`, {
                        type: 'image/webp',
                        lastModified: Date.now()
                    });
                    callback(newFile);
                }, 'image/webp', quality);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
