@extends('layouts.app')

@section('title', 'Import Data Siswa Excel')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Header -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="{{ route('admin.users.index', ['role' => 'siswa']) }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Siswa</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Import Excel</span></li>
                </ul>
            </div>
            <div class="flex-align gap-8 flex-wrap">
                <a href="{{ route('admin.students.import.template', ['format' => 'xlsx']) }}" class="btn btn-success rounded-pill px-16 text-13 shadow-sm">
                    <i class="ph ph-file-xls me-4"></i> Unduh Template Excel (.xlsx)
                </a>
                <a href="{{ route('admin.students.import.template', ['format' => 'csv']) }}" class="btn btn-outline-secondary rounded-pill px-16 text-13">
                    <i class="ph ph-file-csv me-4"></i> Unduh Format CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Upload Form Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16 p-24">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <h5 class="mb-8 fw-bold text-gray-900">Petunjuk Import Data Siswa</h5>
                    <p class="text-gray-600 text-14 mb-16">
                        Gunakan file template Excel yang telah disediakan agar data terstruktur rapi ke dalam kolom-kolom yang sesuai:
                    </p>

                    <!-- Preview Kolom Template -->
                    <div class="border border-gray-200 rounded-12 p-12 bg-white mb-16 shadow-2xs">
                        <div class="text-12 fw-bold text-gray-600 mb-8 d-flex align-items-center justify-content-between">
                            <span><i class="ph ph-columns me-4 text-main-600"></i> Struktur Kolom Template:</span>
                            <span class="badge bg-success-50 text-success-700 text-11">Sudah Berkolom & Rapi</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center mb-0 text-11">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="py-6">A: Nama</th>
                                        <th class="py-6">B: NIS</th>
                                        <th class="py-6">C: Email</th>
                                        <th class="py-6">D: Kelas</th>
                                        <th class="py-6">E: Tgl Lahir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-gray-600">
                                        <td class="text-start ps-8">Bima Satria</td>
                                        <td>2026120110</td>
                                        <td class="text-start ps-8">bima@kreasi.test</td>
                                        <td><span class="badge bg-gray-100 text-gray-700 text-10">XII RPL 1 <i class="ph ph-caret-down text-8"></i></span></td>
                                        <td>15-08-2008</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <ul class="list-unstyled text-13 text-gray-700 d-flex flex-column gap-8 mb-0">
                        <li class="d-flex align-items-start gap-8">
                            <span class="w-20 h-20 rounded-circle bg-success-50 text-success-600 flex-center text-12 shrink-0 mt-2"><i class="ph ph-check"></i></span>
                            <span>Template Excel (.xlsx) sudah dilengkapi <strong>garis kolom, border sel, dan dropdown pilihan kelas</strong>.</span>
                        </li>
                        <li class="d-flex align-items-start gap-8">
                            <span class="w-20 h-20 rounded-circle bg-success-50 text-success-600 flex-center text-12 shrink-0 mt-2"><i class="ph ph-check"></i></span>
                            <span>Nomor <strong>NIS</strong> otomatis diformat sebagai teks agar tidak berubah menjadi notasi ilmiah.</span>
                        </li>
                        <li class="d-flex align-items-start gap-8">
                            <span class="w-20 h-20 rounded-circle bg-success-50 text-success-600 flex-center text-12 shrink-0 mt-2"><i class="ph ph-check"></i></span>
                            <span>Password awal akun siswa <strong>otomatis dibuat dari Tanggal Lahir (DDMMYYYY)</strong>. Contoh: 15-08-2008 &rarr; password <code>15082008</code> (fallback NIS jika kosong).</span>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-7">
                    <form method="POST" action="{{ route('admin.students.import.preview') }}" enctype="multipart/form-data" class="p-24 rounded-16 border-2 border-dashed border-gray-200 text-center bg-gray-50">
                        @csrf
                        <span class="w-64 h-64 rounded-circle bg-main-50 text-main-600 flex-center mx-auto text-32 mb-16">
                            <i class="ph ph-file-arrow-up"></i>
                        </span>
                        <h6 class="mb-4 text-16 fw-bold">Pilih File Spreadsheet Siswa</h6>
                        <p class="text-gray-500 text-13 mb-16">Mendukung format .xlsx, .xls, atau .csv (Maks. 5MB)</p>

                        <div class="max-w-400 mx-auto mb-16">
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>

                        <button type="submit" class="btn btn-main rounded-pill px-24">
                            <i class="ph ph-magnifying-glass me-4"></i> Unggah & Pratinjau Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Results (If Form submitted) -->
    @if(isset($previewRows))
        <div class="col-12">
            <div class="card border border-gray-100 rounded-16 overflow-hidden">
                <div class="card-body p-24">
                    <div class="flex-between flex-wrap gap-16 mb-20 pb-16 border-bottom border-gray-100">
                        <div>
                            <h5 class="mb-4 fw-bold text-gray-900">Hasil Pratinjau & Validasi Data Siswa</h5>
                            <span class="text-13 text-gray-500">Periksa dan pastikan data di bawah sudah benar sebelum disimpan ke sistem.</span>
                        </div>
                        <div class="d-flex gap-8 align-items-center">
                            <span class="badge bg-success-50 text-success-700 px-12 py-6 rounded-pill text-13 fw-semibold">
                                <i class="ph ph-check-circle me-4"></i> {{ $validCount }} Data Valid
                            </span>
                            @if($errorCount > 0)
                                <span class="badge bg-danger-50 text-danger-700 px-12 py-6 rounded-pill text-13 fw-semibold">
                                    <i class="ph ph-warning-circle me-4"></i> {{ $errorCount }} Data Bermasalah
                                </span>
                            @endif
                            @if($validCount > 0)
                                <form method="POST" action="{{ route('admin.students.import.commit') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success rounded-pill px-20 text-13">
                                        <i class="ph ph-check me-4"></i> Import {{ $validCount }} Siswa Valid
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-gray-50 text-gray-600 text-13">
                                <tr>
                                    <th class="ps-16">Baris</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Email</th>
                                    <th>Kelas</th>
                                    <th>Tgl Lahir / Password Awal</th>
                                    <th>Status Validasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previewRows as $row)
                                    <tr class="{{ !$row['is_valid'] ? 'table-danger' : '' }}">
                                        <td class="ps-16 fw-semibold text-gray-500">#{{ $row['row_index'] }}</td>
                                        <td class="fw-bold text-gray-900">{{ $row['nama'] ?: '-' }}</td>
                                        <td class="text-gray-700">{{ $row['nis'] ?: '-' }}</td>
                                        <td class="text-gray-700">{{ $row['email'] ?: '-' }}</td>
                                        <td>
                                            <span class="badge bg-gray-100 text-gray-800 text-12">{{ $row['kelas_nama'] ?: '-' }}</span>
                                        </td>
                                        <td>
                                            @if($row['birth_date'])
                                                <div class="text-12">
                                                    <span class="text-gray-900 fw-medium"><i class="ph ph-calendar me-2"></i>{{ \Carbon\Carbon::parse($row['birth_date'])->format('d M Y') }}</span>
                                                    <div class="text-main-600 font-monospace text-11 mt-2">Pass: <code>{{ $row['auto_password'] }}</code></div>
                                                </div>
                                            @else
                                                <div class="text-12">
                                                    <span class="text-gray-400 fst-italic">Tidak diisi</span>
                                                    <div class="text-gray-600 font-monospace text-11 mt-2">Pass: <code>{{ $row['nis'] }}</code> (NIS)</div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row['is_valid'])
                                                <span class="badge bg-success-50 text-success-700 text-12">
                                                    <i class="ph ph-check me-2"></i> Siap Diimport
                                                </span>
                                            @else
                                                <div class="text-danger text-12 fw-medium">
                                                    @foreach($row['errors'] as $err)
                                                        <div>&bull; {{ $err }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
