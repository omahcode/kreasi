@extends('layouts.app')

@section('title', 'Edit Proyek: ' . $project->title)

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('guru.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="{{ route('guru.projects.show', $project->id) }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">{{ $project->title }}</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Edit Proyek</span></li>
                </ul>
            </div>
            <a href="{{ route('guru.projects.show', $project->id) }}" class="btn btn-outline-gray rounded-pill px-16 text-13">
                <i class="ph ph-arrow-left me-4"></i> Batal
            </a>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-24">
            <h4 class="mb-4 fw-bold text-gray-900">Perbarui Informasi Proyek PjBL</h4>
            <p class="text-gray-500 text-14 mb-24">Perbarui judul, instruksi pengerjaan, status, batas waktu, atau lampiran panduan.</p>

            <form method="POST" action="{{ route('guru.projects.update', $project->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Judul Proyek PjBL <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $project->title) }}" required>
                        </div>

                        <div class="row g-3 mb-16">
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold">Jenis Penugasan</label>
                                <select name="type" class="form-select" required>
                                    <option value="group" {{ $project->type === 'group' ? 'selected' : '' }}>Proyek Kelompok</option>
                                    <option value="individual" {{ $project->type === 'individual' ? 'selected' : '' }}>Proyek Individu</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold">Status Proyek</label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="draft" {{ $project->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                                    <option value="archived" {{ $project->status === 'archived' ? 'selected' : '' }}>Diarsipkan (Archived)</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold">Deadline</label>
                                <input type="datetime-local" name="deadline" class="form-control" value="{{ $project->deadline ? $project->deadline->format('Y-m-d\TH:i') : '' }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold">Ganti File Lampiran</label>
                                <input type="file" name="attachment" class="form-control">
                                @if($project->attachment)
                                    <small class="text-gray-500 d-block mt-4">File saat ini: <a href="{{ asset('storage/' . $project->attachment) }}" target="_blank">Lihat file</a></small>
                                @endif
                            </div>
                        </div>

                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Deskripsi Singkat</label>
                            <textarea name="description" rows="3" class="form-control" required>{{ old('description', $project->description) }}</textarea>
                        </div>

                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Tujuan Pembelajaran</label>
                            <textarea name="goals" rows="2" class="form-control">{{ old('goals', $project->goals) }}</textarea>
                        </div>

                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Instruksi Pengerjaan</label>
                            <textarea name="instructions" rows="3" class="form-control">{{ old('instructions', $project->instructions) }}</textarea>
                        </div>

                        <div class="mb-16">
                            <label class="form-label text-13 fw-semibold">Rubrik Penilaian</label>
                            <textarea name="rubric" rows="2" class="form-control">{{ old('rubric', $project->rubric) }}</textarea>
                        </div>

                        <div class="mt-24">
                            <button type="submit" class="btn btn-main rounded-pill px-24">
                                <i class="ph ph-floppy-disk me-4"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
