@extends('layouts.app')

@section('title', 'Bimbingan Laporan PKL Siswa Kelas XII')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('siswa.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Bimbingan PKL Kelas XII</span></li>
                </ul>
            </div>
        </div>
    </div>

    @if(!$report)
        <!-- Case 1: Student has not submitted a PKL title yet -->
        <div class="col-lg-8 mx-auto">
            @if($pklGroup)
                <!-- Info Penempatan Ploting -->
                <div class="card border border-main-200 rounded-16 p-20 bg-main-50 mb-20">
                    <div class="d-flex align-items-center gap-12 mb-12">
                        <span class="w-40 h-40 rounded-circle bg-white text-main-600 flex-center text-20 shadow-xs flex-shrink-0">
                            <i class="ph ph-buildings"></i>
                        </span>
                        <div>
                            <span class="badge bg-main-600 text-white rounded-pill px-10 py-2 text-11 fw-semibold mb-2">Penempatan Resmi</span>
                            <h5 class="mb-0 text-16 fw-bold text-gray-900">{{ $pklGroup->company->name }}</h5>
                        </div>
                    </div>
                    <div class="row g-12 text-13 text-gray-700">
                        <div class="col-sm-6">
                            <span class="text-gray-500 d-block text-12">Guru Pembimbing:</span>
                            <strong>{{ $pklGroup->mentorTeacher->name }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-gray-500 d-block text-12">Periode Pelaksanaan:</span>
                            <strong>{{ $pklGroup->start_date ? $pklGroup->start_date->format('d M Y') : '-' }} s/d {{ $pklGroup->end_date ? $pklGroup->end_date->format('d M Y') : '-' }}</strong>
                        </div>
                        <div class="col-12 pt-8 border-top border-main-100">
                            <span class="text-gray-500 d-block text-12 mb-4">Teman Satu Kelompok ({{ $pklGroup->students->count() }} Siswa):</span>
                            <div class="d-flex flex-wrap gap-6">
                                @foreach($pklGroup->students as $st)
                                    <span class="badge {{ $st->pivot->is_leader ? 'bg-warning-50 text-warning-700 border border-warning-200' : 'bg-white text-gray-700 border border-gray-200' }} rounded-pill px-8 py-4 text-11">
                                        @if($st->pivot->is_leader)
                                            <i class="ph ph-crown me-2"></i>
                                        @endif
                                        {{ $st->user->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Pengajuan Judul dengan Data Ploting Terkunci -->
                <div class="card border border-gray-100 rounded-16 p-24 bg-white shadow-xs">
                    <div class="text-center mb-24">
                        <span class="w-64 h-64 rounded-circle bg-main-50 text-main-600 flex-center mx-auto text-32 mb-16">
                            <i class="ph ph-file-text"></i>
                        </span>
                        <h4 class="mb-4 fw-bold text-gray-900">Pengajuan Judul Laporan PKL</h4>
                        <p class="text-gray-500 text-14">
                            Tempat PKL dan Guru Pembimbing Anda telah di-plot oleh sekolah. Silakan ajukan judul laporan PKL Anda untuk memulai proses bimbingan Bab I - V.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('siswa.pkl.title.store') }}">
                        @csrf
                        <div class="mb-20">
                            <label class="form-label text-13 fw-semibold text-gray-700">Judul Laporan PKL <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-8 py-10 text-14 @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" 
                                   placeholder="Contoh: Rancang Bangun Sistem Informasi Monitoring Pelanggan Berbasis Web pada {{ $pklGroup->company->name }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-12 text-gray-400 mt-4 d-block">
                                Buat judul spesifik yang menggambarkan proyek / sistem yang Anda kembangkan selama PKL di {{ $pklGroup->company->name }}.
                            </small>
                        </div>

                        <div class="row g-3 mb-24">
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold text-gray-500">Perusahaan / DU/DI</label>
                                <input type="text" class="form-control rounded-8 bg-gray-50 text-13 fw-semibold text-gray-700" value="{{ $pklGroup->company->name }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-13 fw-semibold text-gray-500">Guru Pembimbing Sekolah</label>
                                <input type="text" class="form-control rounded-8 bg-gray-50 text-13 fw-semibold text-gray-700" value="{{ $pklGroup->mentorTeacher->name }}" readonly>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-main rounded-pill w-100 py-12 text-14 fw-semibold shadow-xs">
                            <i class="ph ph-paper-plane-tilt me-4"></i> Ajukan Judul Laporan PKL
                        </button>
                    </form>
                </div>

            @else
                <!-- Student not yet plotted -->
                <div class="card border border-gray-100 rounded-16 p-32 bg-white shadow-xs text-center">
                    <span class="w-80 h-80 rounded-circle bg-warning-50 text-warning-600 flex-center mx-auto text-40 mb-20 shadow-xs">
                        <i class="ph ph-users-three"></i>
                    </span>
                    <h4 class="mb-8 fw-bold text-gray-900">Belum Ada Ploting Penempatan PKL</h4>
                    <p class="text-gray-600 text-14 mb-24" style="max-width: 480px; margin: 0 auto;">
                        Anda belum dimasukkan ke kelompok penempatan PKL oleh Administrator atau Guru Pembimbing. Setelah Anda di-plot ke mitra industri (DU/DI), Anda dapat langsung mengajukan judul laporan PKL di halaman ini.
                    </p>
                    <div class="d-flex justify-content-center gap-12">
                        <a href="{{ route('siswa.dashboard') }}" class="btn btn-outline-gray rounded-pill px-20 py-10 text-13">
                            &larr; Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            @endif
        </div>

    @else
        <!-- Case 2: Student already has submitted a title -->

        <!-- Revision Alert If needed -->
        @if($report->status === 'revision_required')
            <div class="col-12">
                <div class="alert alert-warning border-2 border-warning rounded-16 p-20 shadow-xs mb-0">
                    <div class="d-flex align-items-start gap-16">
                        <span class="w-44 h-44 rounded-circle bg-warning text-dark flex-center flex-shrink-0 text-22">
                            <i class="ph-fill ph-warning-circle"></i>
                        </span>
                        <div class="flex-grow-1">
                            <h5 class="text-16 fw-bold text-gray-900 mb-4">Pengajuan Judul Perlu Revisi!</h5>
                            <p class="text-gray-700 text-14 mb-12">
                                <strong>Catatan Guru Pembimbing:</strong> "{{ $report->rejection_note ?: 'Perbaiki judul agar sesuai dengan bidang keahlian RPL.' }}"
                            </p>
                            <button type="button" class="btn btn-sm btn-dark rounded-pill px-16 text-12" data-bs-toggle="modal" data-bs-target="#editTitleModal">
                                <i class="ph ph-pencil-simple me-2"></i> Perbaiki & Ajukan Ulang Judul
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($report->status === 'submitted')
            <div class="col-12">
                <div class="alert alert-primary border-primary-100 rounded-16 p-20 shadow-xs mb-0">
                    <div class="d-flex align-items-center gap-16">
                        <span class="w-44 h-44 rounded-circle bg-primary text-white flex-center flex-shrink-0 text-22">
                            <i class="ph ph-clock"></i>
                        </span>
                        <div>
                            <h5 class="text-16 fw-bold text-gray-900 mb-2">Judul Laporan Sedang Ditinjau Guru</h5>
                            <p class="text-gray-600 text-14 mb-0">
                                Pengajuan judul Anda sedang dalam antrean verifikasi oleh Guru Pembimbing (<strong>{{ $report->mentorTeacher->name ?? 'Guru RPL' }}</strong>). Anda dapat mulai mengunggah Bab I s/d V setelah judul disetujui.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($report->status === 'approved')
            @php
                $approvedChapters = $report->chapters->where('status', 'approved')->count();
            @endphp
            @if($approvedChapters === 5)
                <div class="col-12">
                    <div class="alert alert-success border-2 border-success rounded-16 p-20 shadow-xs mb-0">
                        <div class="d-flex align-items-center gap-16">
                            <span class="w-48 h-48 rounded-circle bg-success text-white flex-center flex-shrink-0 text-26">
                                <i class="ph-fill ph-seal-check"></i>
                            </span>
                            <div>
                                <h4 class="text-18 fw-bold text-success-800 mb-2">SELAMAT! Seluruh Laporan PKL Telah Disetujui Final</h4>
                                <p class="text-gray-700 text-14 mb-0">
                                    Bab I sampai dengan Bab V laporan PKL Anda telah dinyatakan lengkap dan disahkan oleh Guru Pembimbing. Anda siap untuk sidang laporan PKL!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- PKL Info Banner -->
        <div class="col-12">
            <div class="card border border-gray-100 rounded-16 p-24 bg-white">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-16">
                    <div>
                        <div class="d-flex align-items-center gap-8 mb-8">
                            <span class="badge bg-main-50 text-main-600 text-12 text-uppercase px-12 py-4">
                                Laporan PKL Kelas XII
                            </span>
                            <span class="badge {{ $report->status === 'approved' ? 'bg-success-50 text-success-700' : 'bg-primary-50 text-primary-700' }} text-12 px-12 py-4 text-capitalize">
                                Status Judul: {{ $report->status }}
                            </span>
                        </div>
                        <h4 class="mb-6 fw-bold text-gray-900">{{ $report->title }}</h4>
                        <div class="text-13 text-gray-600">
                            <i class="ph ph-buildings me-2 text-main-600"></i> Mitra Industri: <strong>{{ $report->company_name }}</strong> &bull;
                            Guru Pembimbing: <strong class="text-main-600">{{ $report->mentorTeacher->name ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="text-end">
                        @if($report->status !== 'approved')
                            <button type="button" class="btn btn-sm btn-outline-gray rounded-pill px-16 text-12" data-bs-toggle="modal" data-bs-target="#editTitleModal">
                                <i class="ph ph-pencil-simple me-2"></i> Edit Judul / Data PKL
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 5 Chapters Section (Bab I - V) -->
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-16">
                <h5 class="mb-0 fw-bold text-gray-900">Proses Bimbingan Bab I s/d Bab V</h5>
                <span class="badge bg-main-50 text-main-600 px-12 py-6 rounded-pill text-13">
                    Progres: {{ $report->chapters->where('status', 'approved')->count() }}/5 Bab Disetujui
                </span>
            </div>

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
                                            <span class="text-12 text-gray-500">
                                                Berkas saat ini: <a href="{{ asset('storage/' . $chapter->file) }}" target="_blank" download class="text-main-600 fw-semibold hover-text-decoration-underline">Unduh Bab {{ $i }}</a>
                                                &bull; Diperbarui {{ $chapter->updated_at->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-12 text-gray-400">Belum ada dokumen yang diunggah.</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    @if($chapter && $chapter->status === 'approved')
                                        <span class="badge bg-success-50 text-success-700 text-12 px-12 py-6 rounded-pill">
                                            <i class="ph ph-check-circle me-2"></i> Disetujui Guru
                                        </span>
                                    @elseif($chapter && $chapter->status === 'revision_required')
                                        <span class="badge bg-warning-50 text-warning-700 text-12 px-12 py-6 rounded-pill">
                                            <i class="ph ph-arrow-counter-clockwise me-2"></i> Perlu Revisi
                                        </span>
                                    @elseif($chapter && $chapter->status === 'submitted')
                                        <span class="badge bg-primary-50 text-primary-700 text-12 px-12 py-6 rounded-pill">
                                            <i class="ph ph-tray me-2"></i> Menunggu Bimbingan Guru
                                        </span>
                                    @else
                                        <span class="badge bg-gray-100 text-gray-500 text-12 px-12 py-6 rounded-pill">
                                            Belum Diunggah
                                        </span>
                                    @endif

                                    <!-- Upload button -->
                                    <button type="button" class="btn btn-sm btn-main rounded-pill px-16 text-12" data-bs-toggle="modal" data-bs-target="#uploadModal{{ $i }}">
                                        <i class="ph ph-upload-simple me-2"></i> {{ $chapter && $chapter->file ? 'Unggah Revisi' : 'Unggah Bab' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Teacher feedback message box -->
                            @if($chapter && $chapter->feedback)
                                <div class="p-16 rounded-12 bg-main-50 border border-primary-200 mt-8">
                                    <div class="flex-between mb-4">
                                        <strong class="text-13 text-main-600">
                                            <i class="ph ph-chat-centered-text me-2"></i> Catatan Bimbingan dari Guru Pembimbing:
                                        </strong>
                                    </div>
                                    <p class="text-13 text-gray-800 mb-0 whitespace-pre-line">{{ $chapter->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Upload Modal for Chapter i -->
                    <div class="modal fade" id="uploadModal{{ $i }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-16 border-0 shadow-lg">
                                <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                                    <h5 class="modal-title fw-bold">Unggah Berkas {{ $chapterTitles[$i] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('siswa.pkl.chapters.upload', $i) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body p-24">
                                        <div class="mb-16">
                                            <label class="form-label text-13 fw-semibold">Pilih Berkas Bab (PDF / Word) <span class="text-danger">*</span></label>
                                            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx" required>
                                            <small class="text-gray-400 text-11">Maksimal 20MB. Format .pdf, .doc, atau .docx</small>
                                        </div>
                                        <div class="mb-16">
                                            <label class="form-label text-13 fw-semibold">Catatan untuk Guru Pembimbing</label>
                                            <textarea name="notes" rows="3" class="form-control" placeholder="Contoh: Sudah diperbaiki diagram UML dan lampiran sesuai arahan bimbingan sebelumnya...">{{ $chapter ? $chapter->notes : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top border-gray-100 py-12 px-24">
                                        <button type="button" class="btn btn-outline-gray rounded-pill px-16" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-main rounded-pill px-20">Unggah Dokumen</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Edit Title Modal -->
        <div class="modal fade" id="editTitleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-16 border-0 shadow-lg">
                    <div class="modal-header border-bottom border-gray-100 py-16 px-24">
                        <h5 class="modal-title fw-bold">Edit / Revisi Data Judul PKL</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('siswa.pkl.title.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-24">
                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold">Judul Laporan PKL <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $report->title) }}" required>
                            </div>

                            <div class="row g-3 mb-16">
                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Nama Perusahaan / DUDI <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $report->company_name) }}" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-13 fw-semibold">Guru Pembimbing <span class="text-danger">*</span></label>
                                    <select name="mentor_teacher_id" class="form-select" required>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ $report->mentor_teacher_id == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-16">
                                <label class="form-label text-13 fw-semibold">Alamat Perusahaan / Mitra Industri</label>
                                <textarea name="company_address" rows="2" class="form-control">{{ old('company_address', $report->company_address) }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top border-gray-100 py-12 px-24">
                            <button type="button" class="btn btn-outline-gray rounded-pill px-16" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-main rounded-pill px-20">Kirim Revisi Judul</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
