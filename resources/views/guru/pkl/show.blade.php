@extends('layouts.app')

@section('title', 'Bimbingan Laporan PKL: ' . $report->student->user->name)

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('guru.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="{{ route('guru.pkl.index') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Bimbingan PKL</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Detail Bimbingan</span></li>
                </ul>
            </div>
            <a href="{{ route('guru.pkl.index') }}" class="btn btn-outline-gray rounded-pill px-16 text-13">
                <i class="ph ph-arrow-left me-4"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Student & Title Overview Card -->
    @php
        $totalChapters = 5;
        $approvedChaptersCount = $report->chapters->where('status', 'approved')->whereNotNull('file')->count();
        $hasPendingOrRevision = $report->chapters->whereIn('status', ['revision_required', 'submitted', 'draft'])->count() > 0;
        $allChaptersUploaded = $report->chapters->count() >= $totalChapters && $report->chapters->every(fn($c) => !empty($c->file));
        $allChaptersApproved = ($approvedChaptersCount >= $totalChapters) && !$hasPendingOrRevision && $allChaptersUploaded;

        // Strictly only final approved if status is 'approved' AND all 5 chapters are truly approved without revision!
        $isFinalApproved = ($report->status === 'approved') && $allChaptersApproved;
        $canFinalApprove = $allChaptersApproved && !$isFinalApproved;
    @endphp
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-24 bg-white">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-16 mb-20 pb-20 border-bottom border-gray-100">
                <div class="d-flex align-items-center gap-16">
                    <img src="{{ $report->student->user->avatar_url }}" alt="" class="w-64 h-64 rounded-circle object-fit-cover shadow-xs border">
                    <div>
                        <h4 class="mb-2 fw-bold text-gray-900">{{ $report->student->user->name }}</h4>
                        <div class="text-13 text-gray-500">
                            NIS: <strong>{{ $report->student->nis }}</strong> &bull; 
                            Kelas: <strong>{{ $report->student->schoolClass->name ?? '-' }}</strong> &bull; 
                            Guru Pembimbing: <strong class="text-main-600">{{ $report->mentorTeacher->name ?? 'Belum Ditentukan' }}</strong>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    @if($isFinalApproved)
                        <span class="badge bg-success-50 text-success-700 border border-success-200 text-13 px-14 py-6 rounded-pill">
                            Status Laporan: DISETUJUI FINAL
                        </span>
                        <div class="mt-8">
                            <span class="badge bg-success text-white text-12 px-14 py-6 rounded-pill shadow-xs">
                                <i class="ph ph-seal-check me-4 text-14"></i> Seluruh Bab Tuntas & Disahkan
                            </span>
                        </div>
                    @elseif($canFinalApprove)
                        <span class="badge bg-primary-50 text-primary-700 text-13 px-14 py-6 rounded-pill">
                            Status Laporan: SIAP DISAHKAN
                        </span>
                        <div class="mt-8">
                            <form method="POST" action="{{ route('guru.pkl.final-approval', $report->id) }}" data-confirm="Sahkan kelulusan dan persetujuan final seluruh laporan PKL siswa ini? Seluruh 5 bab telah disetujui tanpa ada revisi." data-confirm-title="Pengesahan Laporan PKL" data-confirm-btn="Ya, Sahkan!" data-confirm-icon="question">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-16 text-12 shadow-xs">
                                    <i class="ph ph-seal-check me-4"></i> Sahkan Persetujuan Final Laporan
                                </button>
                            </form>
                        </div>
                    @elseif(($report->title_status ?? '') === 'revision_required' || $report->status === 'revision_required')
                        <span class="badge bg-warning-50 text-warning-700 text-13 px-14 py-6 rounded-pill">
                            Status Laporan: PERLU REVISI JUDUL
                        </span>
                        <div class="mt-8">
                            <span class="text-12 text-warning-700 d-inline-flex align-items-center gap-4 bg-warning-50 border border-warning-200 rounded-pill px-12 py-5">
                                <i class="ph ph-warning-circle"></i> Menunggu siswa merevisi judul
                            </span>
                        </div>
                    @elseif(($report->title_status ?? '') === 'approved' || $report->status === 'in_progress' || $report->status === 'approved')
                        <span class="badge bg-primary-50 text-primary-700 text-13 px-14 py-6 rounded-pill">
                            Status Laporan: BIMBINGAN BERJALAN
                        </span>
                        <div class="mt-8">
                            <span class="text-12 text-gray-500 d-inline-flex align-items-center gap-4 bg-gray-50 border border-gray-100 rounded-pill px-12 py-5">
                                <i class="ph ph-info text-main-600"></i> Pengesahan aktif setelah seluruh 5 bab disetujui ({{ $approvedChaptersCount }}/5)
                            </span>
                        </div>
                    @else
                        <span class="badge bg-primary-50 text-primary-700 text-13 px-14 py-6 rounded-pill">
                            Status Laporan: MENUNGGU VERIFIKASI JUDUL
                        </span>
                        <div class="mt-8">
                            <span class="text-12 text-gray-500 d-inline-flex align-items-center gap-4 bg-gray-50 border border-gray-100 rounded-pill px-12 py-5">
                                <i class="ph ph-clock text-main-600"></i> Silakan verifikasi judul terlebih dahulu
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Title Details -->
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="text-12 text-gray-400 d-block">Judul Laporan PKL:</label>
                    <h5 class="text-16 fw-bold text-gray-900 mb-8">{{ $report->title }}</h5>
                    <div class="text-13 text-gray-600">
                        <i class="ph ph-buildings me-2 text-main-600"></i> Mitra Industri (DUDI): <strong>{{ $report->company_name }}</strong>
                        @if($report->company_address)
                            <span class="text-gray-400 ms-4">({{ $report->company_address }})</span>
                        @endif
                    </div>
                    @if($report->rejection_note)
                        <div class="p-12 rounded-8 bg-warning-50 border border-warning-200 mt-12 text-13 text-warning-800">
                            <strong>Catatan Revisi Judul:</strong> {{ $report->rejection_note }}
                        </div>
                    @endif
                </div>

                <div class="col-md-4 text-md-end">
                    @if(($report->title_status ?? '') === 'approved')
                        <span class="badge bg-success-50 text-success-700 border border-success-200 rounded-pill px-16 py-8 text-12 fw-semibold d-inline-flex align-items-center gap-6">
                            <i class="ph ph-check-circle text-16"></i> Judul Telah Disetujui
                        </span>
                    @else
                        <!-- Title Verification Actions -->
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-16 text-12 mb-8" data-bs-toggle="modal" data-bs-target="#reviewTitleModal">
                            <i class="ph ph-check-circle me-4"></i> Verifikasi / Revisi Judul
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chapter Management (Bab I s/d Bab V) -->
    <div class="col-12">
        <h5 class="mb-16 fw-bold text-gray-900"><i class="ph ph-books me-4 text-main-600"></i> Bimbingan Bab per Bab (Bab I s/d Bab V)</h5>

        <div class="row g-4">
            @for($i = 1; $i <= 5; $i++)
                @php
                    $chapter = $report->chapters->firstWhere('chapter_number', $i);
                @endphp
                <div class="col-12">
                    <div class="card border border-gray-100 rounded-16 p-20 bg-white">
                        <div class="flex-between flex-wrap gap-12 mb-12">
                            <div class="d-flex align-items-center gap-12">
                                <span class="w-40 h-40 rounded-circle {{ $chapter && $chapter->status === 'approved' ? 'bg-success text-white' : 'bg-main-50 text-main-600' }} flex-center fw-bold text-16">
                                    {{ $i }}
                                </span>
                                <div>
                                    <h6 class="mb-2 text-15 fw-bold text-gray-900">{{ $chapterTitles[$i] }}</h6>
                                    @if($chapter && $chapter->file)
                                        <span class="text-12 text-gray-400">
                                            Diunggah: {{ $chapter->updated_at->format('d M Y, H:i') }} ({{ $chapter->updated_at->diffForHumans() }})
                                        </span>
                                    @else
                                        <span class="text-12 text-gray-400">Siswa belum mengunggah dokumen bab ini.</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                @if($chapter && $chapter->status === 'approved')
                                    <span class="badge bg-success-50 text-success-700 text-12 px-12 py-6 rounded-pill">
                                        <i class="ph ph-check-circle me-2"></i> Disetujui
                                    </span>
                                @elseif($chapter && $chapter->status === 'revision_required')
                                    <span class="badge bg-warning-50 text-warning-700 text-12 px-12 py-6 rounded-pill">
                                        <i class="ph ph-arrow-counter-clockwise me-2"></i> Perlu Revisi
                                    </span>
                                @elseif($chapter && $chapter->status === 'submitted')
                                    <span class="badge bg-primary-50 text-primary-700 text-12 px-12 py-6 rounded-pill">
                                        <i class="ph ph-tray me-2"></i> Menunggu Bimbingan
                                    </span>
                                @else
                                    <span class="badge bg-gray-100 text-gray-500 text-12 px-12 py-6 rounded-pill">
                                        Draft
                                    </span>
                                @endif

                                @if($chapter && $chapter->file)
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-14 py-4 text-12 flex-align gap-4" data-bs-toggle="modal" data-bs-target="#previewChapterModal{{ $chapter->id }}">
                                        <i class="ph ph-eye text-14"></i> Preview Bab
                                    </button>
                                @endif

                                @if($chapter)
                                    <button type="button" class="btn btn-sm btn-main rounded-pill px-16 text-12" data-bs-toggle="modal" data-bs-target="#reviewChapterModal{{ $chapter->id }}">
                                        Bimbing / Koreksi Bab
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Feedback / Notes preview -->
                        @if($chapter && $chapter->feedback)
                            <div class="p-12 rounded-8 bg-gray-50 border border-gray-100 mt-8 text-13 text-gray-700">
                                <strong class="text-main-600"><i class="ph ph-chat-text me-2"></i> Catatan Bimbingan Guru:</strong> {{ $chapter->feedback }}
                            </div>
                        @endif

                        @if($chapter && $chapter->notes)
                            <div class="mt-4 text-12 text-gray-500 ps-4">
                                <em>Catatan Siswa: "{{ $chapter->notes }}"</em>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Preview Chapter Modal -->
                @if($chapter && $chapter->file)
                    @php
                        $fileExtension = strtolower(pathinfo($chapter->file, PATHINFO_EXTENSION));
                        $fileUrl = asset('storage/' . $chapter->file);
                    @endphp
                    <div class="modal fade" id="previewChapterModal{{ $chapter->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                            <div class="modal-content rounded-16 border-0 shadow-lg">
                                <div class="modal-header border-bottom border-gray-100 py-16 px-24 flex-between">
                                    <div class="d-flex align-items-center gap-10">
                                        <span class="w-36 h-36 rounded-circle bg-main-50 text-main-600 flex-center fw-bold text-14">
                                            <i class="ph ph-file-text"></i>
                                        </span>
                                        <div>
                                            <h5 class="modal-title fw-bold text-16 mb-0">{{ $chapterTitles[$i] }}</h5>
                                            <span class="text-12 text-gray-400">Pratinjau Dokumen Siswa (Format .{{ strtoupper($fileExtension) }})</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-8">
                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-14 text-12 flex-align gap-4">
                                            <i class="ph ph-arrow-square-out text-14"></i> Buka di Tab Baru
                                        </a>
                                        <a href="{{ $fileUrl }}" download class="btn btn-sm btn-main rounded-pill px-14 text-12 flex-align gap-4">
                                            <i class="ph ph-download-simple text-14"></i> Unduh Berkas
                                        </a>
                                        <button type="button" class="btn-close ms-8" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                </div>
                                <div class="modal-body p-0 bg-gray-50" style="min-height: 580px;">
                                    @if($fileExtension === 'pdf')
                                        <iframe src="{{ $fileUrl }}#toolbar=1" class="w-100 border-0" style="height: 72vh; min-height: 580px;" allowfullscreen>
                                            <div class="p-32 text-center">
                                                <p class="text-gray-600">Peramban Anda tidak mendukung pratinjau PDF langsung.</p>
                                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-main rounded-pill px-20">Buka PDF di Tab Baru</a>
                                            </div>
                                        </iframe>
                                    @elseif(in_array($fileExtension, ['png', 'jpg', 'jpeg', 'webp']))
                                        <div class="text-center p-24">
                                            <img src="{{ $fileUrl }}" alt="{{ $chapterTitles[$i] }}" class="img-fluid rounded-8 shadow-sm" style="max-height: 75vh;">
                                        </div>
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center p-48 text-center" style="min-height: 480px;">
                                            <span class="w-72 h-72 rounded-circle bg-primary-50 text-primary-600 flex-center text-36 mb-16 shadow-xs">
                                                <i class="ph ph-file-doc"></i>
                                            </span>
                                            <h5 class="text-18 fw-bold text-gray-900 mb-8">Dokumen Microsoft Word (.{{ strtoupper($fileExtension) }})</h5>
                                            <p class="text-gray-500 text-13 mb-24 max-w-440">
                                                Berkas ini berformat dokumen Word (.{{ $fileExtension }}). Anda dapat membuka pratinjau melalui Google Docs Viewer atau langsung mengunduhnya ke perangkat Anda.
                                            </p>
                                            <div class="d-flex gap-12 flex-wrap justify-content-center">
                                                <a href="https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}&embedded=true" target="_blank" class="btn btn-outline-primary rounded-pill px-20 text-13">
                                                    <i class="ph ph-eye me-4"></i> Pratinjau via Google Viewer
                                                </a>
                                                <a href="{{ $fileUrl }}" download class="btn btn-main rounded-pill px-20 text-13">
                                                    <i class="ph ph-download-simple me-4"></i> Unduh Berkas Word
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer border-top border-gray-100 py-12 px-24 flex-between">
                                    <span class="text-12 text-gray-500">
                                        <i class="ph ph-info me-2 text-main-600"></i> Pembimbing dapat membaca dokumen langsung di atas atau mengunduh jika diperlukan.
                                    </span>
                                    <button type="button" class="btn btn-outline-gray rounded-pill px-20 text-13" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Review Chapter Modal -->
                @if($chapter)
                    <div class="modal fade" id="reviewChapterModal{{ $chapter->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-16 border-0 shadow-lg">
                                <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                                    <h5 class="modal-title fw-bold">Bimbingan Bab {{ $chapter->chapter_number }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('guru.pkl.chapters.review', $chapter->id) }}">
                                    @csrf
                                    <div class="modal-body p-24">
                                        <div class="mb-16">
                                            <label class="form-label text-13 fw-semibold">Status Bab <span class="text-danger">*</span></label>
                                            <select name="status" class="form-select" required>
                                                <option value="approved" {{ $chapter->status === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                                                <option value="revision_required" {{ $chapter->status === 'revision_required' ? 'selected' : '' }}>Perlu Revisi (Siswa Mengunggah Versi Perbaikan)</option>
                                                <option value="submitted" {{ $chapter->status === 'submitted' ? 'selected' : '' }}>Tinjau Ulang</option>
                                            </select>
                                        </div>
                                        <div class="mb-16">
                                            <label class="form-label text-13 fw-semibold">Catatan Bimbingan / Arahan Revisi <span class="text-danger">*</span></label>
                                            <textarea name="feedback" rows="4" class="form-control" placeholder="Tuliskan koreksi penulisan, diagram alir, atau saran perbaikan bab ini..." required>{{ old('feedback', $chapter->feedback) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top border-gray-100 py-12 px-24">
                                        <button type="button" class="btn btn-outline-gray rounded-pill px-16" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-main rounded-pill px-20">Simpan Ulasan Bimbingan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endfor
        </div>
    </div>
</div>

<!-- Modal Review Title -->
<div class="modal fade" id="reviewTitleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-16 border-0 shadow-lg">
            <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                <h5 class="modal-title fw-bold">Verifikasi Judul Laporan PKL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('guru.pkl.title.review', $report->id) }}">
                @csrf
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold">Status Judul <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="approved" {{ $report->status === 'approved' ? 'selected' : '' }}>Setujui Judul (Siswa Dapat Mulai Unggah Bab I - V)</option>
                            <option value="revision_required" {{ $report->status === 'revision_required' ? 'selected' : '' }}>Minta Revisi Judul</option>
                        </select>
                    </div>
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold">Catatan Revisi / Saran (Wajib jika revisi)</label>
                        <textarea name="rejection_note" rows="3" class="form-control" placeholder="Tuliskan saran penyempurnaan judul atau kriteria industri...">{{ old('rejection_note', $report->rejection_note) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-gray-100 py-12 px-24">
                    <button type="button" class="btn btn-outline-gray rounded-pill px-16" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-main rounded-pill px-20">Simpan Status Judul</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
