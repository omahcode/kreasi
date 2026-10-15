<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Notification;
use App\Models\PklGroup;
use App\Models\PklGroupMember;
use App\Models\Student;
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
            ->where('mentor_teacher_id', Auth::id())
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($cq) => $cq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('students.user', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $groups = $query->paginate(10)->withQueryString();

        return view('guru.pkl_groups.index', compact('groups', 'search'));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $students = Student::with(['user', 'schoolClass'])->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        return view('guru.pkl_groups.create', compact('companies', 'students', 'activeYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'exists:companies,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'leader_id' => ['nullable', 'exists:students,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama kelompok PKL wajib diisi.',
            'company_id.required' => 'Pilih mitra industri DU/DI yang telah didaftarkan Admin.',
            'student_ids.required' => 'Pilih minimal satu siswa anggota kelompok bimbingan Anda.',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();

        DB::transaction(function () use ($request, $activeYear) {
            $group = PklGroup::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
                'mentor_teacher_id' => Auth::id(), // Otomatis guru yang sedang login
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
                        'title' => 'Ploting Kelompok PKL',
                        'message' => "Guru Pembimbing " . Auth::user()->name . " telah memplot Anda ke kelompok {$group->name}.",
                        'type' => 'pkl',
                        'link' => route('siswa.pkl.index'),
                    ]);
                }
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'TEACHER_CREATE_PKL_GROUP',
                'details' => "Guru membuat ploting bimbingan PKL: {$group->name}",
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('guru.pkl-groups.index')
            ->with('success', 'Kelompok siswa bimbingan PKL berhasil dibuat.');
    }

    public function edit($id)
    {
        $group = PklGroup::where('mentor_teacher_id', Auth::id())
            ->with(['members', 'company'])
            ->findOrFail($id);

        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $students = Student::with(['user', 'schoolClass'])->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $selectedStudentIds = $group->members->pluck('student_id')->toArray();
        $currentLeaderId = $group->members->firstWhere('is_leader', true)?->student_id;

        return view('guru.pkl_groups.edit', compact(
            'group', 'companies', 'students', 'activeYear', 'selectedStudentIds', 'currentLeaderId'
        ));
    }

    public function update(Request $request, $id)
    {
        $group = PklGroup::where('mentor_teacher_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'exists:companies,id'],
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
        });

        return redirect()->route('guru.pkl-groups.index')
            ->with('success', "Data kelompok bimbingan PKL {$group->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, $id)
    {
        $group = PklGroup::where('mentor_teacher_id', Auth::id())->findOrFail($id);
        $name = $group->name;
        $group->delete();

        return redirect()->route('guru.pkl-groups.index')
            ->with('success', "Kelompok bimbingan PKL {$name} berhasil dihapus.");
    }
}
