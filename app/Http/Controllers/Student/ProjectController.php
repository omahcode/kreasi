<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return view('siswa.projects.index', ['projects' => collect()]);
        }

        $projects = $student->projects()
            ->with(['creator', 'members.student.user'])
            ->withCount(['submissions', 'logs'])
            ->latest()
            ->paginate(9);

        return view('siswa.projects.index', compact('projects'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Profil siswa Anda belum terhubung.');
        }

        $project = $student->projects()
            ->with(['creator', 'students.user', 'students.schoolClass', 'members', 'logs.student.user'])
            ->findOrFail($id);

        $mySubmissions = Submission::where('project_id', $project->id)
            ->where('student_id', $student->id)
            ->with('reviews.teacher')
            ->latest()
            ->get();

        $latestSubmission = $mySubmissions->first();
        $isLeader = $project->pivot->role === 'leader';

        return view('siswa.projects.show', compact('project', 'mySubmissions', 'latestSubmission', 'isLeader'));
    }

    public function submitTask(Request $request, $id)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $project = $student->projects()->findOrFail($id);

        $request->validate([
            'file' => ['required', 'file', 'mimes:zip,rar,pdf,doc,docx,tar,gz', 'max:25600'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'file.required' => 'Wajib memilih file pekerjaan/tugas yang akan diunggah.',
            'file.max' => 'Ukuran file tugas maksimal adalah 25MB.',
        ]);

        $latestSub = Submission::where('project_id', $project->id)
            ->where('student_id', $student->id)
            ->latest('version')
            ->first();

        $version = $latestSub ? ($latestSub->version + 1) : 1;
        $filePath = $request->file('file')->store('project_submissions', 'public');

        $submission = Submission::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'file' => $filePath,
            'version' => $version,
            'notes' => $request->notes,
            'status' => 'submitted',
        ]);

        // Notify teacher
        Notification::create([
            'user_id' => $project->created_by,
            'title' => 'Pengumpulan Tugas PjBL Baru',
            'message' => "Siswa {$user->name} mengunggah pengumpulan tugas (v{$version}) pada proyek: {$project->title}",
            'link' => route('guru.submissions.show', $submission->id),
        ]);

        AuditLog::record($user->id, 'submit_pjbl_task', "Mengunggah pengumpulan tugas v{$version} pada proyek {$project->title}");

        return back()->with('success', "Tugas versi {$version} berhasil dikumpulkan dan siap direview oleh Guru Pembimbing.");
    }
}
