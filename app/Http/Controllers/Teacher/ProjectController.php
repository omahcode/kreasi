<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = Project::where('created_by', Auth::id())
            ->with(['students.user', 'members'])
            ->withCount(['submissions', 'logs'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $projects = $query->paginate(12)->withQueryString();

        return view('guru.projects.index', compact('projects', 'status', 'search'));
    }

    public function create()
    {
        $classes = SchoolClass::with('students.user')->get();

        return view('guru.projects.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'goals' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'type' => ['required', 'in:group,individual'],
            'status' => ['required', 'in:draft,active'],
            'deadline' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,zip,rar,doc,docx,ppt,pptx', 'max:10240'],
            'rubric' => ['nullable', 'string'],
            // Group / Student assignment
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'leader_student_id' => ['nullable', 'exists:students,id'],
        ], [
            'student_ids.required' => 'Pilih minimal satu siswa untuk proyek ini.',
            'attachment.max' => 'File lampiran maksimal berukuran 10MB.',
        ]);

        DB::beginTransaction();
        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('project_attachments', 'public');
            }

            $project = Project::create([
                'title' => $request->title,
                'description' => $request->description,
                'goals' => $request->goals,
                'instructions' => $request->instructions,
                'type' => $request->type,
                'status' => $request->status,
                'deadline' => $request->deadline,
                'attachment' => $attachmentPath,
                'rubric' => $request->rubric,
                'created_by' => Auth::id(),
            ]);

            $leaderId = $request->input('leader_student_id');

            foreach ($request->student_ids as $studentId) {
                $isLeader = ($request->type === 'group' && $studentId == $leaderId);

                ProjectMember::create([
                    'project_id' => $project->id,
                    'student_id' => $studentId,
                    'role' => $isLeader ? 'leader' : 'member',
                ]);

                // If marked as leader, update user role to ketua_kelompok if currently siswa
                if ($isLeader) {
                    $student = Student::with('user')->find($studentId);
                    if ($student && $student->user && $student->user->role === 'siswa') {
                        $student->user->update(['role' => 'ketua_kelompok']);
                        $student->user->syncRoles('ketua_kelompok');
                    }
                }

                // Send notification to student
                $studentObj = Student::find($studentId);
                if ($studentObj) {
                    Notification::create([
                        'user_id' => $studentObj->user_id,
                        'title' => 'Tugas Proyek PjBL Baru',
                        'message' => 'Guru '.Auth::user()->name." telah menugaskan Anda ke dalam proyek: {$project->title}",
                        'link' => route('siswa.projects.show', $project->id),
                    ]);
                }
            }

            AuditLog::record(Auth::id(), 'create_project', "Membuat proyek PjBL: {$project->title} ({$project->type})");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal membuat proyek: '.$e->getMessage());
        }

        return redirect()->route('guru.projects.index')->with('success', "Proyek '{$project->title}' berhasil dibuat dan siswa telah ditugaskan.");
    }

    public function show($id)
    {
        $project = Project::where('created_by', Auth::id())
            ->with(['students.user', 'students.schoolClass', 'members', 'submissions.student.user', 'submissions.reviews', 'logs.student.user'])
            ->findOrFail($id);

        $leader = $project->leader;

        return view('guru.projects.show', compact('project', 'leader'));
    }

    public function edit($id)
    {
        $project = Project::where('created_by', Auth::id())
            ->with('members')
            ->findOrFail($id);

        $classes = SchoolClass::with('students.user')->get();
        $selectedStudentIds = $project->members->pluck('student_id')->toArray();
        $currentLeaderId = optional($project->members->firstWhere('role', 'leader'))->student_id;

        return view('guru.projects.edit', compact('project', 'classes', 'selectedStudentIds', 'currentLeaderId'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::where('created_by', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'goals' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'type' => ['required', 'in:group,individual'],
            'status' => ['required', 'in:draft,active,completed,archived'],
            'deadline' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,zip,rar,doc,docx,ppt,pptx', 'max:10240'],
            'rubric' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('attachment')) {
            if ($project->attachment) {
                Storage::disk('public')->delete($project->attachment);
            }
            $project->attachment = $request->file('attachment')->store('project_attachments', 'public');
        }

        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'goals' => $request->goals,
            'instructions' => $request->instructions,
            'type' => $request->type,
            'status' => $request->status,
            'deadline' => $request->deadline,
            'rubric' => $request->rubric,
        ]);

        AuditLog::record(Auth::id(), 'update_project', "Memperbarui proyek PjBL: {$project->title}");

        return redirect()->route('guru.projects.show', $project->id)->with('success', 'Informasi proyek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $project = Project::where('created_by', Auth::id())->findOrFail($id);

        if ($project->attachment) {
            Storage::disk('public')->delete($project->attachment);
        }

        $title = $project->title;
        $project->delete();

        AuditLog::record(Auth::id(), 'delete_project', "Menghapus proyek PjBL: {$title}");

        return redirect()->route('guru.projects.index')->with('success', "Proyek '{$title}' berhasil dihapus.");
    }

    /**
     * Preview automated grouping for PjBL without saving.
     */
    public function autoGroupPreview(Request $request)
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'method' => ['required', 'in:members_per_group,total_groups'],
            'size' => ['required', 'integer', 'min:1', 'max:50'],
            'balance_gender' => ['nullable', 'boolean'],
            'auto_leader' => ['nullable', 'boolean'],
        ]);

        $students = Student::where('class_id', $request->class_id)
            ->with('user')
            ->get();

        if ($students->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada siswa terdaftar pada kelas yang dipilih.',
            ], 422);
        }

        $balanceGender = $request->boolean('balance_gender');
        $autoLeader = $request->boolean('auto_leader', true);
        $totalStudents = $students->count();

        // Calculate number of groups
        if ($request->method === 'members_per_group') {
            $membersPerGroup = max(1, (int) $request->size);
            $numGroups = max(1, (int) ceil($totalStudents / $membersPerGroup));
        } else {
            $numGroups = min($totalStudents, max(1, (int) $request->size));
        }

        // Initialize group buckets
        $groups = [];
        for ($i = 0; $i < $numGroups; $i++) {
            $groups[$i] = [];
        }

        if ($balanceGender) {
            $males = $students->filter(fn ($s) => strtoupper((string) $s->gender) === 'L')->shuffle()->values();
            $females = $students->filter(fn ($s) => strtoupper((string) $s->gender) === 'P')->shuffle()->values();
            $others = $students->filter(fn ($s) => ! in_array(strtoupper((string) $s->gender), ['L', 'P']))->shuffle()->values();

            $idx = 0;
            foreach ($males as $st) {
                $groups[$idx % $numGroups][] = $st;
                $idx++;
            }
            foreach ($females as $st) {
                $groups[$idx % $numGroups][] = $st;
                $idx++;
            }
            foreach ($others as $st) {
                $groups[$idx % $numGroups][] = $st;
                $idx++;
            }
        } else {
            $shuffled = $students->shuffle()->values();
            foreach ($shuffled as $idx => $st) {
                $groups[$idx % $numGroups][] = $st;
            }
        }

        // Filter out empty groups (if any)
        $groups = array_values(array_filter($groups, fn ($g) => count($g) > 0));

        // Format result structure
        $resultGroups = [];
        foreach ($groups as $gIdx => $groupStudents) {
            $groupNum = $gIdx + 1;
            $leaderId = $autoLeader && ! empty($groupStudents) ? $groupStudents[0]->id : null;

            $members = [];
            foreach ($groupStudents as $st) {
                $isLeader = ($st->id === $leaderId);
                $members[] = [
                    'student_id' => $st->id,
                    'name' => $st->user->name ?? 'Siswa',
                    'nis' => $st->nis,
                    'gender' => $st->gender ?? '-',
                    'is_leader' => $isLeader,
                ];
            }

            $resultGroups[] = [
                'group_number' => $groupNum,
                'name' => "Kelompok {$groupNum}",
                'leader_id' => $leaderId,
                'leader_name' => $autoLeader && ! empty($groupStudents) ? $groupStudents[0]->user->name : null,
                'members' => $members,
            ];
        }

        return response()->json([
            'success' => true,
            'total_students' => $totalStudents,
            'total_groups' => count($resultGroups),
            'groups' => $resultGroups,
        ]);
    }

    /**
     * Batch create group projects for an entire class from auto-grouping result.
     */
    public function batchStoreGroupProjects(Request $request)
    {
        $request->validate([
            'title_prefix' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'goals' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,active'],
            'deadline' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,zip,rar,doc,docx,ppt,pptx', 'max:10240'],
            'rubric' => ['nullable', 'string'],
            'groups_json' => ['required', 'string'],
        ]);

        $groupsData = json_decode($request->groups_json, true);

        if (! is_array($groupsData) || empty($groupsData)) {
            return back()->with('error', 'Data pembagian kelompok tidak valid atau kosong.');
        }

        DB::beginTransaction();
        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('project_attachments', 'public');
            }

            $createdCount = 0;

            foreach ($groupsData as $g) {
                $groupName = $g['name'] ?? "Kelompok {$createdCount}";
                $title = "{$request->title_prefix} - {$groupName}";
                $members = $g['members'] ?? [];
                $leaderId = $g['leader_id'] ?? null;

                if (empty($members)) {
                    continue;
                }

                $project = Project::create([
                    'title' => $title,
                    'description' => $request->description,
                    'goals' => $request->goals,
                    'instructions' => $request->instructions,
                    'type' => 'group',
                    'status' => $request->status,
                    'deadline' => $request->deadline,
                    'attachment' => $attachmentPath,
                    'rubric' => $request->rubric,
                    'created_by' => Auth::id(),
                ]);

                foreach ($members as $m) {
                    $studentId = $m['student_id'] ?? null;
                    if (! $studentId) {
                        continue;
                    }

                    $isLeader = ($studentId == $leaderId || (! empty($m['is_leader'])));

                    ProjectMember::create([
                        'project_id' => $project->id,
                        'student_id' => $studentId,
                        'role' => $isLeader ? 'leader' : 'member',
                    ]);

                    if ($isLeader) {
                        $student = Student::with('user')->find($studentId);
                        if ($student && $student->user && $student->user->role === 'siswa') {
                            $student->user->update(['role' => 'ketua_kelompok']);
                            $student->user->syncRoles('ketua_kelompok');
                        }
                    }

                    $studentObj = Student::find($studentId);
                    if ($studentObj) {
                        Notification::create([
                            'user_id' => $studentObj->user_id,
                            'title' => 'Tugas Proyek PjBL Baru',
                            'message' => 'Guru '.Auth::user()->name." telah menugaskan Anda ke dalam proyek: {$project->title}",
                            'link' => route('siswa.projects.show', $project->id),
                        ]);
                    }
                }

                $createdCount++;
            }

            DB::commit();

            AuditLog::record(Auth::id(), 'batch_create_projects', "Membuat {$createdCount} proyek PjBL kelompok otomatis: {$request->title_prefix}");

            return redirect()->route('guru.projects.index')
                ->with('success', "Berhasil membuat {$createdCount} proyek PjBL kelompok secara otomatis.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan saat memproses pembagian kelompok: '.$e->getMessage());
        }
    }
}
