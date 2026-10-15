<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Review;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $projectId = $request->query('project_id');

        $query = Submission::whereHas('project', function ($q) {
            $q->where('created_by', Auth::id());
        })->with(['project', 'student.user', 'reviews.teacher'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $submissions = $query->paginate(15)->withQueryString();
        $myProjects = Project::where('created_by', Auth::id())->get();

        return view('guru.submissions.index', compact('submissions', 'myProjects', 'status', 'projectId'));
    }

    public function show($id)
    {
        $submission = Submission::whereHas('project', function ($q) {
            $q->where('created_by', Auth::id());
        })->with(['project', 'student.user', 'student.schoolClass', 'reviews.teacher'])->findOrFail($id);

        $previousSubmissions = Submission::where('project_id', $submission->project_id)
            ->where('student_id', $submission->student_id)
            ->where('id', '!=', $submission->id)
            ->latest()
            ->get();

        return view('guru.submissions.show', compact('submission', 'previousSubmissions'));
    }

    public function storeReview(Request $request, $id)
    {
        $submission = Submission::whereHas('project', function ($q) {
            $q->where('created_by', Auth::id());
        })->with(['project', 'student.user'])->findOrFail($id);

        $request->validate([
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:approved,revision_required,reviewed'],
            'comment' => ['required', 'string'],
        ], [
            'comment.required' => 'Wajib memberikan catatan review atau umpan balik untuk siswa.',
            'score.max' => 'Nilai maksimal adalah 100.',
        ]);

        $review = Review::create([
            'submission_id' => $submission->id,
            'teacher_id' => Auth::id(),
            'comment' => $request->comment,
            'score' => $request->score,
            'status' => $request->status,
        ]);

        $submission->update([
            'status' => $request->status,
        ]);

        // Notify student
        if ($submission->student && $submission->student->user) {
            $statusLabel = ($request->status === 'approved') ? 'Disetujui' : (($request->status === 'revision_required') ? 'Perlu Revisi' : 'Telah Dinilai');
            Notification::create([
                'user_id' => $submission->student->user_id,
                'title' => "Hasil Review Tugas: {$submission->project->title}",
                'message' => "Guru " . Auth::user()->name . " telah memberikan ulasan status '{$statusLabel}' dengan nilai: " . ($request->score ?: '-'),
                'link' => route('siswa.projects.show', $submission->project_id),
            ]);
        }

        AuditLog::record(Auth::id(), 'review_submission', "Menilai pengumpulan tugas v{$submission->version} siswa {$submission->student->user->name} pada proyek {$submission->project->title} (Skor: {$request->score})");

        return back()->with('success', 'Ulasan dan penilaian tugas siswa berhasil disimpan.');
    }
}
