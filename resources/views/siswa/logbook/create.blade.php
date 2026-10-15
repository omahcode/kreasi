@extends('layouts.app')

@section('title', 'Catat Logbook Harian PjBL')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('siswa.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="{{ route('siswa.logbook.index') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Logbook</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Catat Aktivitas</span></li>
                </ul>
            </div>
            <a href="{{ route('siswa.logbook.index') }}" class="btn btn-outline-gray rounded-pill px-16 text-13">
                <i class="ph ph-arrow-left me-4"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Logbook Form Card -->
    <div class="col-lg-8 mx-auto">
        <div class="card border border-gray-100 rounded-16 p-24 bg-white shadow-xs">
            <h4 class="mb-4 fw-bold text-gray-900">Catat Jurnal Aktivitas Harian (Logbook)</h4>
            <p class="text-gray-500 text-14 mb-24">Tuliskan kemajuan pekerjaan, modul atau fitur yang berhasil dibuat, serta kendala teknis yang dihadapi.</p>

            <form method="POST" action="{{ route('siswa.logbook.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-20">
                    <label class="form-label text-13 fw-semibold">Pilih Proyek PjBL <span class="text-danger">*</span></label>
                    <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Proyek yang Dikerjakan --</option>
                        @foreach($myProjects as $proj)
                            <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                                {{ $proj->title }} (Guru: {{ $proj->creator->name ?? 'Guru' }})
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-20">
                    <label class="form-label text-13 fw-semibold">Perkiraan Capaian Progres (%) <span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center gap-16">
                        <input type="range" class="form-range flex-grow-1" min="0" max="100" step="5" id="progressRange" name="progress_percentage" value="{{ old('progress_percentage', 25) }}" oninput="updateRangeText(this.value)">
                        <span class="badge bg-main-600 text-white rounded-pill px-16 py-8 text-14 font-monospace" id="rangeValueBadge">25%</span>
                    </div>
                </div>

                <div class="mb-20">
                    <label class="form-label text-13 fw-semibold">Deskripsi Aktivitas Hari Ini <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Contoh:&#10;1. Membuat tabel basis data 'products' dan 'transactions'&#10;2. Menyusun tampilan antarmuka kasir menggunakan Bootstrap&#10;3. Menghubungkan controller dengan view..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-24">
                    <label class="form-label text-13 fw-semibold">Unggah Tangkapan Layar / Foto Bukti Kerja (Opsional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-gray-400 text-11">Maks. 5MB (JPG, PNG, WebP). Bukti visual kode, wireframe, atau demo fitur.</small>
                </div>

                <div class="pt-16 border-top border-gray-100 flex-between">
                    <a href="{{ route('siswa.logbook.index') }}" class="btn btn-outline-gray rounded-pill px-20">Batal</a>
                    <button type="submit" class="btn btn-main rounded-pill px-24">
                        <i class="ph ph-check-circle me-4"></i> Simpan Catatan Logbook
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateRangeText(val) {
        document.getElementById('rangeValueBadge').innerText = val + '%';
    }
</script>
@endpush
