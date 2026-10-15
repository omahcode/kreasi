@extends('layouts.app')

@section('title', $project->title)

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('siswa.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="{{ route('siswa.projects.index') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Proyek Saya</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Ruang Kerja Proyek</span></li>
                </ul>
            </div>
            <div class="d-flex gap-8 flex-wrap">
                <a href="{{ route('siswa.logbook.create') }}" class="btn btn-outline-primary rounded-pill px-16 text-13">
                    <i class="ph ph-notepad me-4"></i> Catat Logbook
                </a>
                <a href="{{ route('siswa.projects.index') }}" class="btn btn-outline-gray rounded-pill px-16 text-13">
                    <i class="ph ph-arrow-left me-4"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Review Status Alert (If any) -->
    @if($latestSubmission && $latestSubmission->status === 'revision_required')
        @php $latestRev = $latestSubmission->reviews->first(); @endphp
        <div class="col-12">
            <div class="alert alert-warning border-2 border-warning rounded-16 p-20 shadow-xs mb-0">
                <div class="d-flex align-items-start gap-16">
                    <span class="w-44 h-44 rounded-circle bg-warning text-dark flex-center flex-shrink-0 text-22">
                        <i class="ph-fill ph-warning-circle"></i>
                    </span>
                    <div>
                        <h5 class="text-16 fw-bold text-gray-900 mb-4">Perlu Revisi dari Guru Pembimbing! (Versi {{ $latestSubmission->version }})</h5>
                        <p class="text-gray-700 text-14 mb-8">
                            <strong>Catatan Guru:</strong> "{{ $latestRev ? $latestRev->comment : 'Silakan perbaiki bagian yang belum sesuai instruksi.' }}"
                        </p>
                        <span class="text-12 text-gray-500">Silakan unggah berkas perbaikan versi baru pada tab <em>Pengumpulan Tugas</em> di bawah.</span>
                    </div>
                </div>
            </div>
        </div>
    @elseif($latestSubmission && $latestSubmission->status === 'approved')
        @php $latestRev = $latestSubmission->reviews->first(); @endphp
        <div class="col-12">
            <div class="alert alert-success border-2 border-success rounded-16 p-20 shadow-xs mb-0">
                <div class="d-flex align-items-start gap-16">
                    <span class="w-44 h-44 rounded-circle bg-success text-white flex-center flex-shrink-0 text-22">
                        <i class="ph-fill ph-check-circle"></i>
                    </span>
                    <div>
                        <h5 class="text-16 fw-bold text-success-800 mb-4">Proyek Disetujui oleh Guru Pembimbing!</h5>
                        <p class="text-gray-700 text-14 mb-4">
                            Nilai Skor Anda: <strong class="text-success-700 text-18">{{ $latestRev && $latestRev->score ? $latestRev->score : 'A' }}</strong> &bull;
                            "{{ $latestRev ? $latestRev->comment : 'Kerja bagus!' }}"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Project Header Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-24 bg-white">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-16">
                <div>
                    <div class="d-flex align-items-center gap-8 mb-8">
                        <span class="badge {{ $project->type === 'group' ? 'bg-primary-50 text-primary-700' : 'bg-info-50 text-info-700' }} text-12 text-uppercase px-12 py-4">
                            {{ $project->type === 'group' ? 'Proyek Kelompok' : 'Proyek Individu' }}
                        </span>
                        @if($isLeader)
                            <span class="badge bg-warning-50 text-warning-700 text-12 px-12 py-4">
                                <i class="ph ph-crown me-2"></i> Peran Anda: Ketua Kelompok
                            </span>
                        @else
                            <span class="badge bg-gray-100 text-gray-700 text-12 px-12 py-4">
                                Peran Anda: Anggota Tim
                            </span>
                        @endif
                    </div>
                    <h3 class="mb-8 fw-bold text-gray-900">{{ $project->title }}</h3>
                    <p class="text-gray-600 text-15 mb-0" style="max-width: 800px;">{{ $project->description }}</p>
                </div>
                <div class="text-end">
                    <span class="text-gray-500 text-13 d-block">Guru Pembimbing:</span>
                    <strong class="text-15 text-gray-800 d-block mt-2">{{ $project->creator->name ?? 'Guru RPL' }}</strong>
                    <div class="mt-8 text-13 text-danger fw-semibold">
                        <i class="ph ph-calendar me-2"></i> Deadline: {{ $project->deadline ? $project->deadline->format('d M Y, H:i') : 'Fleksibel' }}
                    </div>
                </div>
            </div>

            @if($project->attachment)
                <div class="mt-20 pt-16 border-top border-gray-100 flex-between flex-wrap gap-12 bg-main-50 p-16 rounded-12">
                    <div class="d-flex align-items-center gap-8">
                        <i class="ph ph-file-text text-main-600 text-22"></i>
                        <span class="text-14 text-gray-800 fw-medium">Panduan / Modul Resmi dari Guru</span>
                    </div>
                    <a href="{{ asset('storage/' . $project->attachment) }}" target="_blank" class="btn btn-sm btn-main rounded-pill px-16 text-12">
                        <i class="ph ph-download-simple me-4"></i> Unduh Berkas Panduan
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Workspace Tabs -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-0 overflow-hidden">
            <div class="card-header bg-gray-50 border-bottom border-gray-100 p-0">
                <ul class="nav nav-tabs border-0 px-16 pt-8" id="studentPjblTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold text-14 py-12 px-20" id="stab-overview" data-bs-toggle="tab" data-bs-target="#scontent-overview" type="button">
                            <i class="ph ph-article me-4"></i> Instruksi & Tujuan
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-14 py-12 px-20" id="stab-submission" data-bs-toggle="tab" data-bs-target="#scontent-submission" type="button">
                            <i class="ph ph-tray me-4"></i> Pengumpulan Tugas ({{ $mySubmissions->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-14 py-12 px-20" id="stab-team" data-bs-toggle="tab" data-bs-target="#scontent-team" type="button">
                            <i class="ph ph-users me-4"></i> Anggota Tim ({{ $project->students->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-14 py-12 px-20" id="stab-logs" data-bs-toggle="tab" data-bs-target="#scontent-logs" type="button">
                            <i class="ph ph-notepad me-4"></i> Logbook Aktivitas ({{ $project->logs->count() }})
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-24">
                <div class="tab-content" id="studentPjblTabsContent">
                    <!-- Tab 1: Overview & Guidelines -->
                    <div class="tab-pane fade show active" id="scontent-overview" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="text-14 fw-bold text-gray-900 mb-8"><i class="ph ph-target me-4 text-main-600"></i> Tujuan Proyek</h6>
                                <div class="p-16 rounded-12 bg-gray-50 text-14 text-gray-700 whitespace-pre-line border border-gray-100">
                                    {{ $project->goals ?: 'Selesaikan proyek sesuai standar kurikulum RPL.' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-14 fw-bold text-gray-900 mb-8"><i class="ph ph-check-square me-4 text-main-600"></i> Rubrik Penilaian Guru</h6>
                                <div class="p-16 rounded-12 bg-gray-50 text-14 text-gray-700 whitespace-pre-line border border-gray-100">
                                    {{ $project->rubric ?: 'Kriteria penilaian mencakup fungsionalitas, struktur kode, dan kerja tim.' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <h6 class="text-14 fw-bold text-gray-900 mb-8"><i class="ph ph-list-numbers me-4 text-main-600"></i> Langkah & Instruksi Kerja</h6>
                                <div class="p-16 rounded-12 bg-gray-50 text-14 text-gray-700 whitespace-pre-line border border-gray-100">
                                    {{ $project->instructions ?: 'Ikuti arahan guru pembimbing di ruang kelas / laboratorium RPL.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Submissions & Upload -->
                    <div class="tab-pane fade" id="scontent-submission" role="tabpanel">
                        <div class="row g-4">
                            <!-- Submission Upload Form -->
                            <div class="col-lg-5">
                                <div class="p-20 rounded-16 border border-gray-200 bg-gray-50">
                                    <h6 class="text-15 fw-bold text-gray-900 mb-4">Unggah / Kumpulkan Berkas Tugas</h6>
                                    <p class="text-gray-500 text-13 mb-16">
                                        Unggah arsip ZIP/RAR berisi source code, laporan, atau dokumentasi hasil karya.
                                    </p>

                                    <form method="POST" action="{{ route('siswa.projects.submit', $project->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-16">
                                            <label class="form-label text-13 fw-semibold">Pilih Berkas (.ZIP, .RAR, .PDF)</label>
                                            <input type="file" name="file" class="form-control" accept=".zip,.rar,.pdf,.tar,.gz" required>
                                            <small class="text-gray-400 text-11">Maksimal 25MB</small>
                                        </div>

                                        <div class="mb-16">
                                            <label class="form-label text-13 fw-semibold">Catatan Pengumpulan</label>
                                            <textarea name="notes" rows="3" class="form-control" placeholder="Contoh: Telah menyelesaikan modul otentikasi dan basis data..."></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-main rounded-pill w-100 py-10 text-13">
                                            <i class="ph ph-paper-plane-tilt me-4"></i> Kumpulkan Tugas Proyek
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Submissions History -->
                            <div class="col-lg-7">
                                <h6 class="text-15 fw-bold text-gray-900 mb-12">Riwayat Pengumpulan & Catatan Guru:</h6>
                                <div class="d-flex flex-column gap-12">
                                    @forelse($mySubmissions as $sub)
                                        @php $rev = $sub->reviews->first(); @endphp
                                        <div class="p-16 rounded-12 border border-gray-100 bg-white">
                                            <div class="flex-between mb-8">
                                                <div>
                                                    <span class="badge bg-main-50 text-main-600 text-12 font-monospace">Versi {{ $sub->version }}</span>
                                                    <span class="text-12 text-gray-400 ms-8">{{ $sub->created_at->format('d M Y, H:i') }}</span>
                                                </div>
                                                <div>
                                                    @if($sub->status === 'approved')
                                                        <span class="badge bg-success-50 text-success-700 text-12">Disetujui</span>
                                                    @elseif($sub->status === 'revision_required')
                                                        <span class="badge bg-warning-50 text-warning-700 text-12">Perlu Revisi</span>
                                                    @else
                                                        <span class="badge bg-primary-50 text-primary-700 text-12">Menunggu Review</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-13 text-gray-700 mb-8">
                                                <strong>Catatan Pengumpulan:</strong> {{ $sub->notes ?: '-' }}
                                            </div>

                                            @if($rev)
                                                <div class="p-12 rounded-8 bg-gray-50 border border-gray-100 mt-8">
                                                    <div class="flex-between mb-4">
                                                        <strong class="text-12 text-main-600"><i class="ph ph-chat-text me-2"></i> Review Guru ({{ $rev->teacher->name ?? 'Guru' }}):</strong>
                                                        @if($rev->score)
                                                            <span class="badge bg-success-600 text-white text-11">Nilai: {{ $rev->score }}</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-13 text-gray-800 mb-0">{{ $rev->comment }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-32 text-gray-400">
                                            <i class="ph ph-tray text-36 mb-8 d-block"></i>
                                            Belum ada berkas tugas yang Anda kumpulkan pada proyek ini.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Team Members -->
                    <div class="tab-pane fade" id="scontent-team" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-gray-50 text-gray-600 text-13">
                                    <tr>
                                        <th class="ps-16">Nama Anggota</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        <th>Peran di Tim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->students as $member)
                                        @php $isLead = $member->pivot->role === 'leader'; @endphp
                                        <tr>
                                            <td class="ps-16">
                                                <div class="d-flex align-items-center gap-10">
                                                    <img src="{{ $member->user->avatar_url }}" alt="" class="w-36 h-36 rounded-circle object-fit-cover">
                                                    <div>
                                                        <span class="text-14 fw-bold text-gray-900">{{ $member->user->name }}</span>
                                                        <span class="text-11 text-gray-400 d-block">{{ $member->user->email }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-13 text-gray-700">{{ $member->nis }}</span></td>
                                            <td><span class="badge bg-gray-100 text-gray-800 text-12">{{ $member->schoolClass->name ?? '-' }}</span></td>
                                            <td>
                                                @if($isLead)
                                                    <span class="badge bg-warning-50 text-warning-700 text-12 px-10 py-4">
                                                        <i class="ph ph-crown me-2"></i> Ketua Kelompok
                                                    </span>
                                                @else
                                                    <span class="badge bg-info-50 text-info-700 text-12 px-10 py-4">
                                                        Anggota
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Logbook Feed -->
                    <div class="tab-pane fade" id="scontent-logs" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-16">
                            <h6 class="text-15 fw-bold text-gray-900 mb-0">Catatan Aktivitas Harian Tim</h6>
                            <a href="{{ route('siswa.logbook.create') }}" class="btn btn-sm btn-main rounded-pill px-16 text-12">
                                + Catat Aktivitas Baru
                            </a>
                        </div>

                        <div class="d-flex flex-column gap-12">
                            @forelse($project->logs as $log)
                                <div class="p-16 rounded-12 border border-gray-100 bg-white">
                                    <div class="flex-between mb-8">
                                        <div class="d-flex align-items-center gap-8">
                                            <img src="{{ $log->student->user->avatar_url }}" alt="" class="w-28 h-28 rounded-circle">
                                            <span class="text-13 fw-bold text-gray-900">{{ $log->student->user->name }}</span>
                                            <small class="text-gray-400">&bull; {{ $log->created_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <span class="badge bg-main-50 text-main-600 text-11">
                                            Progres: {{ $log->progress_percentage }}%
                                        </span>
                                    </div>
                                    <p class="text-14 text-gray-700 mb-8">{{ $log->description }}</p>
                                    @if($log->image)
                                        <div>
                                            <a href="{{ asset('storage/' . $log->image) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $log->image) }}" alt="Bukti kerja" class="rounded-8 border" style="max-height: 160px; object-fit: cover;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-32 text-gray-400">
                                    <i class="ph ph-notepad text-36 mb-8 d-block"></i>
                                    Belum ada catatan logbook yang dibuat untuk proyek ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
