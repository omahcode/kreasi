<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Company::withCount('pklGroups')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        $companies = $query->paginate(10)->withQueryString();

        return view('admin.companies.index', compact('companies', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'name.required' => 'Nama industri / instansi wajib diisi.',
        ]);

        $company = Company::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_COMPANY',
            'details' => "Menambahkan mitra industri DU/DI baru: {$company->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.companies.index')
            ->with('success', "Mitra industri {$company->name} berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $company->update([
            'name' => $validated['name'],
            'sector' => $validated['sector'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'contact_person' => $validated['contact_person'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE_COMPANY',
            'details' => "Memperbarui data mitra industri: {$company->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.companies.index')
            ->with('success', "Data industri {$company->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $name = $company->name;

        if ($company->pklGroups()->exists()) {
            return back()->with('error', "Industri {$name} tidak dapat dihapus karena sudah memiliki kelompok siswa PKL aktif.");
        }

        $company->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_COMPANY',
            'details' => "Menghapus data mitra industri: {$name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.companies.index')
            ->with('success', "Data mitra industri {$name} berhasil dihapus.");
    }

    public function toggleStatus(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->update(['is_active' => !$company->is_active]);

        $statusStr = $company->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.companies.index')
            ->with('success', "Status industri {$company->name} berhasil {$statusStr}.");
    }
}
