<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Notification;
use App\Models\PklGroup;
use App\Models\PklGroupMember;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PklGroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = PklGroup::with(['company', 'mentorTeacher', 'students.user', 'students.schoolClass'])
            ->withCount('students')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($cq) => $cq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('mentorTeacher', fn($tq) => $tq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('students.user', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $groups = $query->paginate(10)->withQueryString();

        return view('admin.pkl_groups.index', compact('groups', 'search'));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $students = Student::with(['user', 'schoolClass'])->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        return view('admin.pkl_groups.create', compact('companies', 'teachers', 'students', 'activeYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'exists:companies,id'],
            'mentor_teacher_id' => ['required', 'exists:users,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'leader_id' => ['nullable', 'exists:students,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama kelompok PKL wajib diisi.',
            'company_id.required' => 'Pilih mitra industri DU/DI.',
            'mentor_teacher_id.required' => 'Pilih guru pembimbing PKL.',
            'student_ids.required' => 'Pilih minimal satu siswa anggota kelompok.',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();

        DB::transaction(function () use ($request, $activeYear) {
            $group = PklGroup::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
                'mentor_teacher_id' => $request->mentor_teacher_id,
                'academic_year_id' => $activeYear?->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
                'status' => 'active',
            ]);

            $leaderId = $request->leader_id ?: $request->student_ids[0];

            foreach ($request->student_ids as $studentId) {
                PklGroupMember::create([
                    'pkl_group_id' => $group->id,
                    'student_id' => $studentId,
                    'is_leader' => ($studentId == $leaderId),
                ]);

                // Kirim notifikasi ke siswa
                $student = Student::with('user')->find($studentId);
                if ($student && $student->user) {
                    Notification::create([
                        'user_id' => $student->user->id,
                        'title' => 'Ploting Penempatan PKL',
                        'message' => "Anda telah di-plot ke kelompok PKL {$group->name} di {$group->company->name}.",
                        'type' => 'pkl',
                        'link' => route('siswa.pkl.index'),
                    ]);
                }
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'CREATE_PKL_GROUP',
                'details' => "Membuat ploting kelompok PKL: {$group->name} dengan " . count($request->student_ids) . " siswa.",
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.pkl-groups.index')
            ->with('success', 'Kelompok PKL berhasil di-plot dan siswa telah terhubung.');
    }

    public function edit($id)
    {
        $group = PklGroup::with(['members', 'company', 'mentorTeacher'])->findOrFail($id);
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $students = Student::with(['user', 'schoolClass'])->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $selectedStudentIds = $group->members->pluck('student_id')->toArray();
        $currentLeaderId = $group->members->firstWhere('is_leader', true)?->student_id;

        return view('admin.pkl_groups.edit', compact(
            'group', 'companies', 'teachers', 'students', 'activeYear', 'selectedStudentIds', 'currentLeaderId'
        ));
    }

    public function update(Request $request, $id)
    {
        $group = PklGroup::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'exists:companies,id'],
            'mentor_teacher_id' => ['required', 'exists:users,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'leader_id' => ['nullable', 'exists:students,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $group) {
            $group->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
                'mentor_teacher_id' => $request->mentor_teacher_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
            ]);

            // Sync members
            $group->members()->delete();
            $leaderId = $request->leader_id ?: $request->student_ids[0];

            foreach ($request->student_ids as $studentId) {
                PklGroupMember::create([
                    'pkl_group_id' => $group->id,
                    'student_id' => $studentId,
                    'is_leader' => ($studentId == $leaderId),
                ]);
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'UPDATE_PKL_GROUP',
                'details' => "Memperbarui ploting kelompok PKL: {$group->name}",
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.pkl-groups.index')
            ->with('success', "Kelompok PKL {$group->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, $id)
    {
        $group = PklGroup::findOrFail($id);
        $name = $group->name;

        $group->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_PKL_GROUP',
            'details' => "Menghapus ploting kelompok PKL: {$name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.pkl-groups.index')
            ->with('success', "Kelompok PKL {$name} berhasil dihapus.");
    }
}
