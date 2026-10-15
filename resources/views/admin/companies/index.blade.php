@extends('layouts.app')

@section('title', 'Manajemen Mitra Industri (DU/DI)')

@section('content')
<div class="row gy-4">
    <!-- Breadcrumb & Actions -->
    <div class="col-12">
        <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-0">
                <ul class="flex-align gap-4">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 fw-normal text-14 hover-text-main-600">Beranda</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-medium text-14">Mitra Industri (DU/DI)</span></li>
                </ul>
            </div>
            <div class="flex-align gap-8 flex-wrap">
                <a href="{{ route('admin.pkl-groups.index') }}" class="btn btn-outline-primary rounded-pill px-16 text-13">
                    <i class="ph ph-users-three me-4"></i> Ploting Kelompok PKL
                </a>
                <button type="button" class="btn btn-main rounded-pill px-16 text-13" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                    <i class="ph ph-plus-circle me-4"></i> + Tambah Perusahaan
                </button>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="col-12">
        <div class="card border border-gray-100 rounded-16">
            <div class="card-body p-24">
                <!-- Filter & Search Toolbar -->
                <div class="flex-between flex-wrap gap-16 mb-20">
                    <form method="GET" action="{{ route('admin.companies.index') }}" class="d-flex align-items-center gap-8 flex-grow-1" style="max-width: 420px;">
                        <div class="position-relative flex-grow-1">
                            <input type="text" name="search" value="{{ $search }}" class="form-control rounded-pill ps-36 py-8 text-13" placeholder="Cari nama perusahaan, bidang usaha, kota, PIC...">
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-14 text-gray-400">
                                <i class="ph ph-magnifying-glass"></i>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-main rounded-pill px-16 py-8 text-13">Cari</button>
                        @if($search)
                            <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-gray rounded-pill px-12 py-8 text-13">Reset</a>
                        @endif
                    </form>

                    <div class="text-gray-500 text-13">
                        Total Industri: <strong class="text-gray-900">{{ $companies->total() }} Perusahaan</strong>
                    </div>
                </div>

                <!-- Companies Table -->
                <div class="table-responsive">
                    <table class="table text-nowrap align-middle">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-12 text-uppercase fw-semibold">
                                <th class="py-12 px-16 rounded-start-8">Nama Perusahaan / DU/DI</th>
                                <th class="py-12 px-16">Bidang Usaha</th>
                                <th class="py-12 px-16">Kota & Alamat</th>
                                <th class="py-12 px-16">Kontak PIC</th>
                                <th class="py-12 px-16 text-center">Kelompok PKL</th>
                                <th class="py-12 px-16 text-center">Status</th>
                                <th class="py-12 px-16 text-end rounded-end-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr class="border-bottom border-gray-100">
                                    <td class="py-16 px-16">
                                        <div class="d-flex align-items-center gap-12">
                                            <span class="w-40 h-40 rounded-circle bg-main-50 text-main-600 flex-center text-18 fw-bold flex-shrink-0">
                                                <i class="ph ph-buildings"></i>
                                            </span>
                                            <div>
                                                <h6 class="text-14 fw-bold text-gray-900 mb-2">{{ $company->name }}</h6>
                                                <span class="text-12 text-gray-500">{{ $company->email ?? 'Tidak ada email' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-16 px-16">
                                        <span class="badge bg-primary-50 text-primary-700 rounded-pill px-10 py-4 text-12">
                                            {{ $company->sector ?? 'Umum / RPL' }}
                                        </span>
                                    </td>
                                    <td class="py-16 px-16">
                                        <div class="text-13 text-gray-800 fw-medium">{{ $company->city ?? '-' }}</div>
                                        <small class="text-12 text-gray-500 d-inline-block text-truncate" style="max-width: 200px;">
                                            {{ $company->address ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="py-16 px-16">
                                        <div class="text-13 text-gray-800 fw-medium">{{ $company->contact_person ?? '-' }}</div>
                                        <small class="text-12 text-gray-500">{{ $company->phone ?? '-' }}</small>
                                    </td>
                                    <td class="py-16 px-16 text-center">
                                        <span class="badge bg-main-50 text-main-600 rounded-pill px-10 py-4 text-12 fw-semibold">
                                            {{ $company->pkl_groups_count }} Kelompok
                                        </span>
                                    </td>
                                    <td class="py-16 px-16 text-center">
                                        @if($company->is_active)
                                            <span class="badge bg-success-50 text-success-700 rounded-pill px-10 py-4 text-11 fw-semibold">Aktif</span>
                                        @else
                                            <span class="badge bg-danger-50 text-danger-700 rounded-pill px-10 py-4 text-11 fw-semibold">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-16 px-16 text-end">
                                        <div class="d-inline-flex align-items-center gap-6">
                                            <button type="button" class="btn btn-sm btn-outline-main rounded-circle p-6 w-32 h-32 flex-center"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editCompanyModal{{ $company->id }}"
                                                    title="Edit Perusahaan">
                                                <i class="ph ph-pencil-simple text-14"></i>
                                            </button>

                                            <form method="POST" action="{{ route('admin.companies.toggle-status', $company->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $company->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-circle p-6 w-32 h-32 flex-center"
                                                        title="{{ $company->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="ph {{ $company->is_active ? 'ph-power' : 'ph-check-circle' }} text-14"></i>
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.companies.destroy', $company->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mitra industri ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-6 w-32 h-32 flex-center" title="Hapus Perusahaan">
                                                    <i class="ph ph-trash text-14"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editCompanyModal{{ $company->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered text-start">
                                                <div class="modal-content border-0 rounded-16 shadow-lg">
                                                    <div class="modal-header border-bottom border-gray-100 p-20">
                                                        <h5 class="modal-title fw-bold text-gray-900">Edit Mitra Industri DU/DI</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST" action="{{ route('admin.companies.update', $company->id) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body p-20">
                                                            <div class="mb-16">
                                                                <label class="form-label text-13 fw-semibold text-gray-700">Nama Perusahaan / DU/DI <span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control rounded-8 text-13" value="{{ old('name', $company->name) }}" required>
                                                            </div>
                                                            <div class="mb-16">
                                                                <label class="form-label text-13 fw-semibold text-gray-700">Bidang Usaha / Sektor</label>
                                                                <input type="text" name="sector" class="form-control rounded-8 text-13" value="{{ old('sector', $company->sector) }}" placeholder="Contoh: Software Development, EdTech, Game Dev">
                                                            </div>
                                                            <div class="row g-12 mb-16">
                                                                <div class="col-6">
                                                                    <label class="form-label text-13 fw-semibold text-gray-700">Kota</label>
                                                                    <input type="text" name="city" class="form-control rounded-8 text-13" value="{{ old('city', $company->city) }}">
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label text-13 fw-semibold text-gray-700">PIC / Kontak Person</label>
                                                                    <input type="text" name="contact_person" class="form-control rounded-8 text-13" value="{{ old('contact_person', $company->contact_person) }}">
                                                                </div>
                                                            </div>
                                                            <div class="row g-12 mb-16">
                                                                <div class="col-6">
                                                                    <label class="form-label text-13 fw-semibold text-gray-700">Nomor Telepon / WhatsApp</label>
                                                                    <input type="text" name="phone" class="form-control rounded-8 text-13" value="{{ old('phone', $company->phone) }}">
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label text-13 fw-semibold text-gray-700">Email Instansi</label>
                                                                    <input type="email" name="email" class="form-control rounded-8 text-13" value="{{ old('email', $company->email) }}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-16">
                                                                <label class="form-label text-13 fw-semibold text-gray-700">Alamat Lengkap Kantor</label>
                                                                <textarea name="address" rows="3" class="form-control rounded-8 text-13">{{ old('address', $company->address) }}</textarea>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active{{ $company->id }}" {{ $company->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label text-13 text-gray-700" for="active{{ $company->id }}">
                                                                    Status Mitra Aktif (dapat dipilih untuk ploting PKL)
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top border-gray-100 p-16">
                                                            <button type="button" class="btn btn-outline-gray rounded-pill px-16 text-13" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-main rounded-pill px-20 text-13">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-40 text-gray-400">
                                        <i class="ph ph-buildings text-40 mb-12 d-block"></i>
                                        Belum ada mitra industri DU/DI yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-20">
                    {{ $companies->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Company Modal -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-16 shadow-lg">
            <div class="modal-header border-bottom border-gray-100 p-20">
                <h5 class="modal-title fw-bold text-gray-900">Tambah Mitra Industri DU/DI Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.companies.store') }}">
                @csrf
                <div class="modal-body p-20">
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold text-gray-700">Nama Perusahaan / DU/DI <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-8 text-13" placeholder="Contoh: PT Solusi Teknologi Nusantara" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold text-gray-700">Bidang Usaha / Sektor</label>
                        <input type="text" name="sector" class="form-control rounded-8 text-13" placeholder="Contoh: Software Development, Web & Mobile, IoT">
                    </div>
                    <div class="row g-12 mb-16">
                        <div class="col-6">
                            <label class="form-label text-13 fw-semibold text-gray-700">Kota</label>
                            <input type="text" name="city" class="form-control rounded-8 text-13" placeholder="Contoh: Jakarta Selatan">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-13 fw-semibold text-gray-700">PIC / Kontak Person</label>
                            <input type="text" name="contact_person" class="form-control rounded-8 text-13" placeholder="Nama pembimbing industri">
                        </div>
                    </div>
                    <div class="row g-12 mb-16">
                        <div class="col-6">
                            <label class="form-label text-13 fw-semibold text-gray-700">Nomor Telepon / WA</label>
                            <input type="text" name="phone" class="form-control rounded-8 text-13" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-13 fw-semibold text-gray-700">Email Instansi</label>
                            <input type="email" name="email" class="form-control rounded-8 text-13" placeholder="hrd@perusahaan.com">
                        </div>
                    </div>
                    <div class="mb-16">
                        <label class="form-label text-13 fw-semibold text-gray-700">Alamat Lengkap Kantor</label>
                        <textarea name="address" rows="3" class="form-control rounded-8 text-13" placeholder="Jalan, Gedung, Nomor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-gray-100 p-16">
                    <button type="button" class="btn btn-outline-gray rounded-pill px-16 text-13" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-main rounded-pill px-20 text-13">Simpan Perusahaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
