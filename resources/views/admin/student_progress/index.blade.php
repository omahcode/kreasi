@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Page Header -->
    <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-16">
        <div>
            <div class="breadcrumb mb-8">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover-text-main-600 text-14">Beranda</a>
                <span class="text-gray-400 mx-8">/</span>
                <span class="text-gray-500 text-14">Monitoring</span>
                <span class="text-gray-400 mx-8">/</span>
                <span class="text-main-600 text-14 fw-semibold">Kelola Berkas Siswa</span>
            </div>
            <h4 class="mb-4 text-22 fw-bold text-gray-800">Manajemen Berkas & Progres Siswa</h4>
            <p class="text-gray-500 text-14 mb-0">Kelola seluruh file yang diunggah siswa (laporan PKL, tugas PjBL, & jurnal logbook) dengan kendali CRUD penuh.</p>
        </div>
        <div class="flex-align gap-12 flex-wrap">
            <a href="{{ route('admin.student-progress.matrix') }}" class="btn btn-outline-main rounded-pill px-20 py-10 flex-align gap-8 text-14 fw-medium">
                <i class="ph ph-grid-four text-18"></i>
                <span>Lihat Matriks Progres Siswa</span>
            </a>
            <button type="button" class="btn btn-main rounded-pill px-20 py-10 flex-align gap-8 text-14 fw-semibold" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="ph ph-upload-simple text-18"></i>
                <span>Unggah Berkas Baru</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-16 mb-24">
        <div class="col-xl-3 col-sm-6">
            <div class="card border border-gray-100 rounded-16 p-20 bg-white h-100 shadow-none">
                <div class="flex-between align-items-center mb-8">
                    <span class="text-gray-500 text-13 fw-medium">Total Seluruh Berkas</span>
                    <div class="w-36 h-36 rounded-10 bg-main-50 text-main-600 flex-center text-18">
                        <i class="ph ph-files"></i>
                    </div>
                </div>
                <h4 class="mb-0 text-24 fw-bold text-gray-800">{{ number_format($totalFilesCount) }}</h4>
                <span class="text-12 text-gray-400 mt-4 d-block">Tersimpan di storage public</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border border-gray-100 rounded-16 p-20 bg-white h-100 shadow-none">
                <div class="flex-between align-items-center mb-8">
                    <span class="text-gray-500 text-13 fw-medium">Bab Laporan PKL</span>
                    <div class="w-36 h-36 rounded-10 bg-danger-50 text-danger-600 flex-center text-18">
                        <i class="ph ph-file-pdf"></i>
                    </div>
                </div>
                <h4 class="mb-0 text-24 fw-bold text-gray-800">{{ number_format($totalPklCount) }}</h4>
                <span class="text-12 text-gray-400 mt-4 d-block">Dokumen Bab 1 - 5 PKL</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border border-gray-100 rounded-16 p-20 bg-white h-100 shadow-none">
                <div class="flex-between align-items-center mb-8">
                    <span class="text-gray-500 text-13 fw-medium">Submisi Tugas PjBL</span>
                    <div class="w-36 h-36 rounded-10 bg-primary-50 text-primary-600 flex-center text-18">
                        <i class="ph ph-archive"></i>
                    </div>
                </div>
                <h4 class="mb-0 text-24 fw-bold text-gray-800">{{ number_format($totalSubmissionCount) }}</h4>
                <span class="text-12 text-gray-400 mt-4 d-block">Arsip ZIP/RAR proyek siswa</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border border-gray-100 rounded-16 p-20 bg-white h-100 shadow-none">
                <div class="flex-between align-items-center mb-8">
                    <span class="text-gray-500 text-13 fw-medium">Lampiran Logbook</span>
                    <div class="w-36 h-36 rounded-10 bg-info-50 text-info-600 flex-center text-18">
                        <i class="ph ph-image"></i>
                    </div>
                </div>
                <h4 class="mb-0 text-24 fw-bold text-gray-800">{{ number_format($totalLogbookCount) }}</h4>
                <span class="text-12 text-gray-400 mt-4 d-block">Foto dokumentasi aktivitas</span>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card border border-gray-100 rounded-16 bg-white p-24 shadow-none">
        <!-- Navigation Tabs & Filter Bar -->
        <div class="border-bottom border-gray-100 pb-16 mb-20 flex-between flex-wrap gap-16">
            <!-- Tabs -->
            <ul class="nav nav-pills gap-8" role="tablist">
                <li class="nav-item">
                    <a href="{{ route('admin.student-progress.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}" 
                       class="nav-link rounded-pill px-16 py-8 text-13 fw-medium {{ $typeFilter === 'all' ? 'active bg-main-600 text-white' : 'bg-gray-50 text-gray-600 hover-bg-gray-100' }}">
                        Semua Berkas ({{ $totalFilesCount }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.student-progress.index', array_merge(request()->except('type', 'page'), ['type' => 'pkl'])) }}" 
                       class="nav-link rounded-pill px-16 py-8 text-13 fw-medium {{ $typeFilter === 'pkl' ? 'active bg-main-600 text-white' : 'bg-gray-50 text-gray-600 hover-bg-gray-100' }}">
                        Laporan PKL ({{ $totalPklCount }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.student-progress.index', array_merge(request()->except('type', 'page'), ['type' => 'submission'])) }}" 
                       class="nav-link rounded-pill px-16 py-8 text-13 fw-medium {{ $typeFilter === 'submission' ? 'active bg-main-600 text-white' : 'bg-gray-50 text-gray-600 hover-bg-gray-100' }}">
                        Submisi PjBL ({{ $totalSubmissionCount }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.student-progress.index', array_merge(request()->except('type', 'page'), ['type' => 'logbook'])) }}" 
                       class="nav-link rounded-pill px-16 py-8 text-13 fw-medium {{ $typeFilter === 'logbook' ? 'active bg-main-600 text-white' : 'bg-gray-50 text-gray-600 hover-bg-gray-100' }}">
                        Jurnal Logbook ({{ $totalLogbookCount }})
                    </a>
                </li>
            </ul>

            <!-- Filter & Search Controls -->
            <form method="GET" action="{{ route('admin.student-progress.index') }}" class="d-flex align-items-center flex-wrap gap-12 ms-auto">
                <input type="hidden" name="type" value="{{ $typeFilter }}">
                
                <!-- Class Filter -->
                <select name="class_id" class="form-select rounded-pill text-13 border-gray-200 py-8 px-16" onchange="this.form.submit()" style="min-width: 160px;">
                    <option value="">Semua Rombel / Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <!-- Search Input -->
                <div class="position-relative" style="min-width: 220px;">
                    <input type="text" name="search" value="{{ $search }}" class="form-control rounded-pill text-13 border-gray-200 ps-36 pe-16 py-8" placeholder="Cari siswa / nama file...">
                    <span class="position-absolute top-50 translate-middle-y ms-12 text-gray-400 text-16 d-flex">
                        <i class="ph ph-magnifying-glass"></i>
                    </span>
                </div>

                @if($search || $classId || $statusFilter)
                    <a href="{{ route('admin.student-progress.index', ['type' => $typeFilter]) }}" class="btn btn-outline-secondary rounded-pill px-12 py-8 text-12 flex-align gap-4" title="Reset Filter">
                        <i class="ph ph-arrow-counter-clockwise"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Files Data Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-gray-50 text-gray-500 text-12 text-uppercase">
                    <tr>
                        <th class="py-12 px-16 rounded-start-8">Siswa & Kelas</th>
                        <th class="py-12 px-16">Tipe Berkas</th>
                        <th class="py-12 px-16">Nama Berkas & Detail</th>
                        <th class="py-12 px-16">Konteks / Proyek</th>
                        <th class="py-12 px-16 text-center">Status</th>
                        <th class="py-12 px-16 text-end rounded-end-8">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $item)
                        <tr class="border-bottom border-gray-50 text-13">
                            <!-- Student & Class -->
                            <td class="py-12 px-16">
                                <div class="flex-align gap-10">
                                    <div class="w-36 h-36 rounded-circle bg-main-100 text-main-700 flex-center flex-shrink-0 text-13 fw-bold">
                                        {{ strtoupper(substr($item['student_name'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="d-block fw-semibold text-gray-800 text-14">{{ $item['student_name'] }}</span>
                                        <span class="text-gray-400 text-12">NIS: {{ $item['student_nis'] }} &bull; {{ $item['class_name'] }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- File Type Badge -->
                            <td class="py-12 px-16">
                                <span class="badge bg-{{ $item['badge_color'] }}-50 text-{{ $item['badge_color'] }}-700 border border-{{ $item['badge_color'] }}-200 px-10 py-5 rounded-pill text-11 fw-semibold flex-align gap-4 d-inline-flex">
                                    <i class="ph {{ $item['icon'] }} text-14"></i>
                                    <span>{{ $item['type_label'] }}</span>
                                </span>
                            </td>

                            <!-- File Name, Size, & Upload Date -->
                            <td class="py-12 px-16">
                                <div style="max-width: 260px;">
                                    <span class="d-block fw-semibold text-gray-800 text-truncate" title="{{ $item['title'] }}">
                                        {{ $item['title'] }}
                                    </span>
                                    <div class="flex-align gap-8 text-gray-400 text-11 mt-2">
                                        <span>{{ $item['file_size'] > 0 ? round($item['file_size'] / 1024, 1) . ' KB' : '-' }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $item['created_at'] ? $item['created_at']->format('d M Y, H:i') : '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Context / Project Name -->
                            <td class="py-12 px-16">
                                <span class="text-gray-600 text-12 fw-medium text-truncate d-inline-block" style="max-width: 180px;" title="{{ $item['context_name'] }}">
                                    {{ $item['context_name'] }}
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-12 px-16 text-center">
                                @php
                                    $st = strtolower($item['status']);
                                    $stClass = 'secondary';
                                    $stLabel = ucfirst($st);
                                    if (in_array($st, ['approved', 'disetujui', 'completed'])) {
                                        $stClass = 'success';
                                        $stLabel = 'Disetujui';
                                    } elseif (in_array($st, ['pending', 'submitted', 'menunggu'])) {
                                        $stClass = 'warning';
                                        $stLabel = 'Menunggu';
                                    } elseif (in_array($st, ['revision', 'revisi', 'rejected'])) {
                                        $stClass = 'danger';
                                        $stLabel = 'Revisi';
                                    }
                                @endphp
                                <span class="badge bg-{{ $stClass }}-50 text-{{ $stClass }}-700 border border-{{ $stClass }}-200 px-10 py-4 rounded-pill text-11 fw-semibold">
                                    {{ $stLabel }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-12 px-16 text-end">
                                <div class="flex-align justify-content-end gap-6">
                                    <!-- Download Button -->
                                    @if($item['file_exists'])
                                        <a href="{{ route('admin.student-progress.download', ['type' => $item['type'], 'id' => $item['id']]) }}" 
                                           class="btn btn-outline-main btn-action-circle" 
                                           title="Unduh Berkas Siswa">
                                            <i class="ph ph-download-simple"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary btn-action-circle" disabled title="Berkas fisik tidak ditemukan">
                                            <i class="ph ph-file-dashed"></i>
                                        </button>
                                    @endif

                                    <!-- Edit / Ganti Berkas Modal Trigger (For PKL & Submission) -->
                                    @if(in_array($item['type'], ['pkl', 'submission']))
                                        <button type="button" 
                                                class="btn btn-outline-warning btn-action-circle" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal-{{ $item['type'] }}-{{ $item['id'] }}" 
                                                title="Edit Status & Ganti Berkas">
                                            <i class="ph ph-pencil-simple"></i>
                                        </button>
                                    @endif

                                    <!-- Delete Button with SweetAlert2 Confirmation -->
                                    <form action="{{ route('admin.student-progress.destroy', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger btn-action-circle" 
                                                data-confirm="Apakah Anda yakin ingin menghapus berkas '{{ $item['title'] }}' milik siswa '{{ $item['student_name'] }}'? File fisik di server akan dibersihkan permanen."
                                                data-confirm-title="Hapus Berkas Siswa?"
                                                data-confirm-icon="warning"
                                                data-confirm-btn="Ya, Hapus Permanen"
                                                title="Hapus Berkas Permanen">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Edit Modal for this item -->
                                @if(in_array($item['type'], ['pkl', 'submission']))
                                    <div class="modal fade text-start" id="editModal-{{ $item['type'] }}-{{ $item['id'] }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-16 shadow-lg">
                                                <form action="{{ route('admin.student-progress.update', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header border-bottom border-gray-100 p-20">
                                                        <h5 class="modal-title text-16 fw-bold text-gray-800">
                                                            <i class="ph ph-pencil-line text-warning-600 me-6"></i> Edit Berkas Siswa
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-20">
                                                        <div class="p-12 rounded-12 bg-gray-50 border border-gray-100 mb-16">
                                                            <span class="text-gray-400 text-11 fw-semibold d-block text-uppercase">Informasi Siswa</span>
                                                            <span class="d-block fw-bold text-gray-800 text-14">{{ $item['student_name'] }} (NIS: {{ $item['student_nis'] }})</span>
                                                            <span class="text-gray-500 text-12">{{ $item['type_label'] }} &bull; {{ $item['class_name'] }}</span>
                                                        </div>

                                                        @if($item['type'] === 'pkl')
                                                            <div class="mb-16">
                                                                <label class="form-label text-13 fw-semibold text-gray-700">Judul Bab Laporan</label>
                                                                <input type="text" name="title" class="form-control rounded-12 text-13 border-gray-200" value="{{ $item['title'] }}" required>
                                                            </div>
                                                        @endif

                                                        <div class="mb-16">
                                                            <label class="form-label text-13 fw-semibold text-gray-700">Status Validasi</label>
                                                            <select name="status" class="form-select rounded-12 text-13 border-gray-200" required>
                                                                <option value="pending" {{ $item['status'] === 'pending' || $item['status'] === 'submitted' ? 'selected' : '' }}>Pending / Menunggu Review</option>
                                                                <option value="approved" {{ $item['status'] === 'approved' ? 'selected' : '' }}>Approved / Disetujui</option>
                                                                <option value="revision" {{ $item['status'] === 'revision' ? 'selected' : '' }}>Revision / Perlu Revisi</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-16">
                                                            <label class="form-label text-13 fw-semibold text-gray-700">Catatan / Feedback</label>
                                                            <textarea name="notes" class="form-control rounded-12 text-13 border-gray-200" rows="3" placeholder="Tambahkan catatan evaluasi berkas...">{{ $item['notes'] !== '-' ? $item['notes'] : '' }}</textarea>
                                                        </div>

                                                        <div class="mb-0">
                                                            <label class="form-label text-13 fw-semibold text-gray-700">Ganti Berkas Fisik (Opsional)</label>
                                                            <input type="file" name="new_file" class="form-control rounded-12 text-13 border-gray-200">
                                                            <span class="text-11 text-gray-400 mt-4 d-block">Pilih file baru jika ingin mengganti file yang ada di server.</span>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top border-gray-100 p-16">
                                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-20 py-8 text-13" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-main rounded-pill px-24 py-8 text-13 fw-semibold">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-40 text-gray-400">
                                <i class="ph ph-folder-dashed text-48 d-block mb-12 text-gray-300"></i>
                                <span class="d-block text-14 fw-medium text-gray-600">Tidak ada berkas yang ditemukan.</span>
                                <span class="text-12 text-gray-400">Coba ubah kata kunci pencarian atau bersihkan filter.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($files->hasPages())
            <div class="mt-20 pt-16 border-top border-gray-100 flex-between flex-wrap gap-12">
                <span class="text-13 text-gray-500">
                    Menampilkan {{ $files->firstItem() }} hingga {{ $files->lastItem() }} dari total {{ $files->total() }} berkas
                </span>
                <div>
                    {{ $files->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal: Upload Berkas Siswa Baru (Manual Admin Upload) -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-16 shadow-lg">
            <form action="{{ route('admin.student-progress.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom border-gray-100 p-20">
                    <h5 class="modal-title text-16 fw-bold text-gray-800">
                        <i class="ph ph-upload-simple text-main-600 me-6"></i> Unggah Berkas Siswa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-20">
                    <!-- Student Selection -->
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold text-gray-700">Pilih Siswa <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select rounded-12 text-13 border-gray-200" required>
                            <option value="">-- Pilih Siswa Pemilik Berkas --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->user->name ?? 'Siswa' }} ({{ $st->nis }} - {{ $st->schoolClass->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- File Type -->
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold text-gray-700">Jenis Berkas <span class="text-danger">*</span></label>
                        <select name="type" id="uploadTypeSelect" class="form-select rounded-12 text-13 border-gray-200" required>
                            <option value="pkl">Laporan PKL (Bab PDF/DOCX)</option>
                            <option value="submission">Submisi Tugas Proyek PjBL (ZIP/RAR)</option>
                        </select>
                    </div>

                    <!-- PKL Specific: Chapter Number -->
                    <div class="mb-16" id="chapterNumberGroup">
                        <label class="form-label text-13 fw-semibold text-gray-700">Bab Laporan PKL</label>
                        <select name="chapter_number" class="form-select rounded-12 text-13 border-gray-200">
                            <option value="1">Bab 1 - Pendahuluan</option>
                            <option value="2">Bab 2 - Gambaran Umum Perusahaan</option>
                            <option value="3">Bab 3 - Pelaksanaan Praktik Kerja</option>
                            <option value="4">Bab 4 - Pembahasan Temuan & Tugas</option>
                            <option value="5">Bab 5 - Penutup & Kesimpulan</option>
                        </select>
                    </div>

                    <!-- Project Selection (For Submissions) -->
                    <div class="mb-16 d-none" id="projectSelectGroup">
                        <label class="form-label text-13 fw-semibold text-gray-700">Proyek PjBL Terkait</label>
                        <select name="project_id" class="form-select rounded-12 text-13 border-gray-200">
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}">{{ $proj->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- File Upload Input -->
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold text-gray-700">Pilih Berkas <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control rounded-12 text-13 border-gray-200" required>
                        <span class="text-11 text-gray-400 mt-4 d-block">Maksimal 50 MB (Format PDF, DOCX, ZIP, RAR).</span>
                    </div>

                    <!-- Title / Description -->
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold text-gray-700">Judul / Keterangan Berkas</label>
                        <input type="text" name="title" class="form-control rounded-12 text-13 border-gray-200" placeholder="Contoh: Revisi Bab 1 Pendahuluan">
                    </div>

                    <!-- Status -->
                    <div class="mb-0">
                        <label class="form-label text-13 fw-semibold text-gray-700">Status Validasi Awal</label>
                        <select name="status" class="form-select rounded-12 text-13 border-gray-200" required>
                            <option value="approved">Approved / Disetujui Langsung</option>
                            <option value="pending">Pending / Menunggu Verifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-gray-100 p-16">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-20 py-8 text-13" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-main rounded-pill px-24 py-8 text-13 fw-semibold">Unggah Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('uploadTypeSelect');
        const chapterGroup = document.getElementById('chapterNumberGroup');
        const projectGroup = document.getElementById('projectSelectGroup');

        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                if (this.value === 'submission') {
                    chapterGroup.classList.add('d-none');
                    projectGroup.classList.remove('d-none');
                } else {
                    chapterGroup.classList.remove('d-none');
                    projectGroup.classList.add('d-none');
                }
            });
        }
    });
</script>
@endpush

