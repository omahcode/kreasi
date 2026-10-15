@extends('layouts.app')

@section('title', 'Review Tugas: ' . $submission->project->title)

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('guru.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="{{ route('guru.submissions.index') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Review Tugas</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Detail Pengumpulan</span></li>
                </ul>
            </div>
            <a href="{{ route('guru.submissions.index') }}" class="btn btn-outline-gray rounded-pill px-16 text-13">
                <i class="ph ph-arrow-left me-4"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Main Content Review Workspace -->
    <div class="col-lg-7">
        <div class="card border border-gray-100 rounded-16 p-24 mb-24 bg-white">
            <div class="flex-between mb-16 pb-16 border-bottom border-gray-100">
                <div>
                    <span class="text-12 text-gray-500 d-block">Proyek PjBL:</span>
                    <h5 class="mb-0 fw-bold text-gray-900">{{ $submission->project->title }}</h5>
                </div>
                <span class="badge bg-main-50 text-main-600 text-13 px-12 py-6 rounded-pill">
                    Versi {{ $submission->version }}
                </span>
            </div>

            <!-- Student Profile Card -->
            <div class="d-flex align-items-center gap-12 p-16 rounded-12 bg-gray-50 mb-20 border border-gray-100">
                <img src="{{ $submission->student->user->avatar_url }}" alt="" class="w-48 h-48 rounded-circle object-fit-cover">
                <div>
                    <h6 class="mb-2 text-15 fw-bold">{{ $submission->student->user->name }}</h6>
                    <div class="text-12 text-gray-500">
                        NIS: <strong>{{ $submission->student->nis }}</strong> &bull; 
                        Kelas: <strong>{{ $submission->student->schoolClass->name ?? '-' }}</strong> &bull; 
                        Waktu Pengumpulan: <strong>{{ $submission->created_at->format('d M Y, H:i') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Student Submission Notes -->
            <div class="mb-20">
                <label class="text-13 fw-bold text-gray-700 d-block mb-6">Catatan dari Siswa / Tim:</label>
                <div class="p-16 rounded-12 bg-gray-50 text-14 text-gray-800 border border-gray-100">
                    {{ $submission->notes ?: 'Tidak ada catatan khusus yang dilampirkan oleh siswa.' }}
                </div>
            </div>

            <!-- Download Submitted File -->
            <div class="p-16 rounded-12 bg-main-50 border border-primary-200 flex-between flex-wrap gap-12 mb-20">
                <div class="d-flex align-items-center gap-10">
                    <span class="w-40 h-40 rounded-circle bg-white text-main-600 flex-center text-22 shadow-xs">
                        <i class="ph ph-file-archive"></i>
                    </span>
                    <div>
                        <span class="text-14 fw-bold text-gray-900 d-block">Berkas Tugas Siswa (v{{ $submission->version }})</span>
                        <span class="text-12 text-gray-500">Unduh untuk menguji atau memeriksa kode program / dokumen hasil karya</span>
                    </div>
                </div>
                <a href="{{ asset('storage/' . $submission->file) }}" target="_blank" download class="btn btn-main rounded-pill px-20 text-13">
                    <i class="ph ph-download-simple me-4"></i> Unduh Berkas Tugas
                </a>
            </div>

            <!-- Previous Reviews List -->
            @if($submission->reviews->count() > 0)
                <h6 class="text-14 fw-bold text-gray-900 mb-12">Riwayat Ulasan pada Versi Ini:</h6>
                <div class="d-flex flex-column gap-12 mb-16">
                    @foreach($submission->reviews as $rev)
                        <div class="p-16 rounded-12 border border-gray-100 bg-white">
                            <div class="flex-between mb-8">
                                <span class="text-12 text-gray-500">
                                    Oleh: <strong>{{ $rev->teacher->name ?? 'Guru' }}</strong> &bull; {{ $rev->created_at->format('d M Y, H:i') }}
                                </span>
                                <div>
                                    <span class="badge {{ $rev->status === 'approved' ? 'bg-success-50 text-success-700' : 'bg-warning-50 text-warning-700' }} text-11 px-8 py-2">
                                        {{ $rev->status === 'approved' ? 'Disetujui' : 'Perlu Revisi' }}
                                    </span>
                                    @if($rev->score)
                                        <span class="badge bg-main-50 text-main-600 text-11 px-8 py-2 ms-4">
                                            Nilai: {{ $rev->score }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-13 text-gray-700 mb-0">{{ $rev->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Review & Grading Form Card -->
    <div class="col-lg-5">
        <div class="card border border-gray-100 rounded-16 p-24 bg-white sticky-top" style="top: 24px;">
            <h5 class="mb-8 fw-bold text-gray-900">Form Ulasan & Penilaian Guru</h5>
            <p class="text-gray-500 text-13 mb-20">Berikan evaluasi, status persetujuan, serta nilai skor kepada siswa.</p>

            <form method="POST" action="{{ route('guru.submissions.review', $submission->id) }}">
                @csrf

                <div class="mb-16">
                    <label class="form-label text-13 fw-semibold">Status Hasil Review <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="approved" {{ $submission->status === 'approved' ? 'selected' : '' }}>
                            Disetujui (Approved / Lulus Proyek)
                        </option>
                        <option value="revision_required" {{ $submission->status === 'revision_required' ? 'selected' : '' }}>
                            Perlu Revisi (Siswa Harus Mengunggah Versi Baru)
                        </option>
                        <option value="reviewed" {{ $submission->status === 'reviewed' ? 'selected' : '' }}>
                            Telah Dinilai / Selesai Diulas
                        </option>
                    </select>
                </div>

                <div class="mb-16">
                    <label class="form-label text-13 fw-semibold">Nilai Skor Proyek (Skala 0 - 100)</label>
                    <input type="number" step="0.5" name="score" class="form-control" placeholder="Contoh: 88.5" min="0" max="100" value="{{ optional($submission->reviews->first())->score }}">
                    <small class="text-gray-400 text-11">Bisa diisi nanti jika masih tahap evaluasi bertahap.</small>
                </div>

                <div class="mb-20">
                    <label class="form-label text-13 fw-semibold">Catatan Evaluasi / Catatan Revisi <span class="text-danger">*</span></label>
                    <textarea name="comment" rows="5" class="form-control @error('comment') is-invalid @enderror" placeholder="Tuliskan umpan balik konstruktif, bagian kode/fitur yang perlu diperbaiki, atau apresiasi atas karya siswa..." required>{{ old('comment') }}</textarea>
                    @error('comment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-main rounded-pill w-100 py-12">
                    <i class="ph ph-check-circle me-4"></i> Simpan Penilaian & Kirim ke Siswa
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
