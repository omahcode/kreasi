<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Project;
use App\Models\ProjectLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return view('siswa.logbook.index', ['logs' => collect(), 'myProjects' => collect()]);
        }

        $logs = ProjectLog::where('student_id', $student->id)
            ->with('project')
            ->latest()
            ->paginate(15);

        $myProjects = $student->projects()->where('projects.status', 'active')->get();

        return view('siswa.logbook.index', compact('logs', 'myProjects'));
    }

    public function create()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('siswa.dashboard')->with('error', 'Profil siswa Anda belum terhubung.');
        }

        $myProjects = $student->projects()->where('projects.status', 'active')->get();

        if ($myProjects->isEmpty()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Anda belum terdaftar pada proyek PjBL aktif untuk mengisi logbook.');
        }

        return view('siswa.logbook.create', compact('myProjects'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'description' => ['required', 'string'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'project_id.required' => 'Pilih proyek PjBL yang sedang dikerjakan.',
            'description.required' => 'Uraian aktivitas harian logbook wajib diisi.',
            'progress_percentage.required' => 'Estimasi progres wajib ditentukan.',
        ]);

        // Check if student belongs to project
        $isMember = $student->projects()->where('projects.id', $request->project_id)->exists();
        if (!$isMember) {
            return back()->withInput()->with('error', 'Anda bukan anggota dari proyek ini.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('project_logs', 'public');
        }

        $log = ProjectLog::create([
            'project_id' => $request->project_id,
            'student_id' => $student->id,
            'description' => $request->description,
            'progress_percentage' => $request->progress_percentage,
            'image' => $imagePath,
        ]);

        $project = Project::find($request->project_id);

        AuditLog::record($user->id, 'create_logbook', "Mengisi logbook aktivitas pada proyek: {$project->title} ({$request->progress_percentage}%)");

        return redirect()->route('siswa.logbook.index')->with('success', 'Catatan logbook aktivitas berhasil disimpan.');
    }
}
