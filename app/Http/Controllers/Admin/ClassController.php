<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::withCount('classes')->latest()->get();
        $classes = SchoolClass::with(['academicYear'])->withCount('students')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        return view('admin.classes.index', compact('academicYears', 'classes', 'activeYear'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'grade_level' => ['required', 'string'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $class = SchoolClass::create([
            'name' => $request->name,
            'grade_level' => $request->grade_level,
            'academic_year_id' => $request->academic_year_id,
        ]);

        AuditLog::record(Auth::id(), 'create_class', "Menambahkan kelas baru: {$class->name}");

        return back()->with('success', "Kelas {$class->name} berhasil ditambahkan.");
    }

    public function updateClass(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'grade_level' => ['required', 'string'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $class->update([
            'name' => $request->name,
            'grade_level' => $request->grade_level,
            'academic_year_id' => $request->academic_year_id,
        ]);

        AuditLog::record(Auth::id(), 'update_class', "Memperbarui kelas: {$class->name}");

        return back()->with('success', "Kelas {$class->name} berhasil diperbarui.");
    }

    public function destroyClass($id)
    {
        $class = SchoolClass::withCount('students')->findOrFail($id);

        if ($class->students_count > 0) {
            return back()->with('error', "Kelas {$class->name} tidak dapat dihapus karena masih memiliki {$class->students_count} siswa.");
        }

        $className = $class->name;
        $class->delete();

        AuditLog::record(Auth::id(), 'delete_class', "Menghapus kelas: {$className}");

        return back()->with('success', "Kelas {$className} berhasil dihapus.");
    }

    public function storeAcademicYear(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:30', 'unique:academic_years,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active');

        if ($isActive) {
            AcademicYear::query()->update(['is_active' => false]);
        }

        $year = AcademicYear::create([
            'name' => $request->name,
            'is_active' => $isActive,
        ]);

        AuditLog::record(Auth::id(), 'create_academic_year', "Menambahkan tahun ajaran: {$year->name}");

        return back()->with('success', "Tahun ajaran {$year->name} berhasil ditambahkan.");
    }

    public function setActiveYear($id)
    {
        AcademicYear::query()->update(['is_active' => false]);
        $year = AcademicYear::findOrFail($id);
        $year->update(['is_active' => true]);

        AuditLog::record(Auth::id(), 'activate_academic_year', "Mengaktifkan tahun ajaran: {$year->name}");

        return back()->with('success', "Tahun ajaran {$year->name} sekarang berstatus aktif.");
    }
}
