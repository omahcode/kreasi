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
                    <span class="badge {{ $report->status === 'approved' ? 'bg-success-50 text-success-700' : 'bg-primary-50 text-primary-700' }} text-13 px-14 py-6 rounded-pill">
                        Status Laporan: {{ strtoupper($report->status) }}
                    </span>
                    @if($report->status !== 'approved')
                        <div class="mt-8">
                            <form method="POST" action="{{ route('guru.pkl.final-approval', $report->id) }}" onsubmit="return confirm('Sahkan kelulusan dan persetujuan final seluruh laporan PKL siswa ini?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-16 text-12">
                                    <i class="ph ph-seal-check me-4"></i> Sahkan Persetujuan Final Laporan
                                </button>
                            </form>
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
                    <!-- Title Verification Actions -->
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-16 text-12 mb-8" data-bs-toggle="modal" data-bs-target="#reviewTitleModal">
                        <i class="ph ph-check-circle me-4"></i> Verifikasi / Revisi Judul
                    </button>
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
                                    <a href="{{ asset('storage/' . $chapter->file) }}" target="_blank" download class="btn btn-sm btn-outline-primary rounded-pill px-12 py-4 text-12">
                                        <i class="ph ph-download-simple me-2"></i> Unduh Bab
                                    </a>
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
