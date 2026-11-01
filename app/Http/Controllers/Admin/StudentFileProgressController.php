<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PklChapter;
use App\Models\PklReport;
use App\Models\Project;
use App\Models\ProjectLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentFileProgressController extends Controller
{
    /**
     * Display student files hub and progress table.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $classId = $request->query('class_id');
        $typeFilter = $request->query('type', 'all'); // all, pkl, submission, logbook
        $statusFilter = $request->query('status');

        $classes = SchoolClass::orderBy('name')->get();

        // 1. PKL Chapters Query
        $pklQuery = PklChapter::with(['report.student.user', 'report.student.schoolClass', 'report.company']);

        if ($search) {
            $pklQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('file', 'like', "%{$search}%")
                    ->orWhereHas('report.student.user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('report.student', fn ($sq) => $sq->where('nis', 'like', "%{$search}%"));
            });
        }

        if ($classId) {
            $pklQuery->whereHas('report.student', fn ($sq) => $sq->where('class_id', $classId));
        }

        if ($statusFilter) {
            $pklQuery->where('status', $statusFilter);
        }

        // 2. Project Submissions Query
        $submissionQuery = Submission::with(['student.user', 'student.schoolClass', 'project']);

        if ($search) {
            $submissionQuery->where(function ($q) use ($search) {
                $q->where('file', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('student.user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('student', fn ($sq) => $sq->where('nis', 'like', "%{$search}%"))
                    ->orWhereHas('project', fn ($pq) => $pq->where('title', 'like', "%{$search}%"));
            });
        }

        if ($classId) {
            $submissionQuery->whereHas('student', fn ($sq) => $sq->where('class_id', $classId));
        }

        if ($statusFilter) {
            $submissionQuery->where('status', $statusFilter);
        }

        // 3. Project Logs Query
        $logQuery = ProjectLog::with(['student.user', 'student.schoolClass', 'project'])->whereNotNull('image');

        if ($search) {
            $logQuery->where(function ($q) use ($search) {
                $q->where('image', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('student.user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('student', fn ($sq) => $sq->where('nis', 'like', "%{$search}%"));
            });
        }

        if ($classId) {
            $logQuery->whereHas('student', fn ($sq) => $sq->where('class_id', $classId));
        }

        // Aggregate unified list of files
        $fileItems = collect();

        if ($typeFilter === 'all' || $typeFilter === 'pkl') {
            $pklChapters = $pklQuery->latest()->get();
            foreach ($pklChapters as $ch) {
                $fileItems->push([
                    'id' => $ch->id,
                    'type' => 'pkl',
                    'type_label' => 'Bab Laporan PKL',
                    'badge_color' => 'danger',
                    'icon' => 'ph-file-pdf',
                    'title' => 'Bab '.$ch->chapter_number.': '.($ch->title ?: 'Laporan PKL'),
                    'file_name' => basename($ch->file ?? ''),
                    'file_path' => $ch->file,
                    'file_exists' => $ch->file && Storage::disk('public')->exists($ch->file),
                    'file_size' => $ch->file && Storage::disk('public')->exists($ch->file) ? Storage::disk('public')->size($ch->file) : 0,
                    'student' => $ch->report?->student,
                    'student_name' => $ch->report?->student?->user?->name ?? 'Siswa Tidak Ditemukan',
                    'student_nis' => $ch->report?->student?->nis ?? '-',
                    'class_name' => $ch->report?->student?->schoolClass?->name ?? '-',
                    'context_name' => $ch->report?->company_name ?? ($ch->report?->company?->name ?? 'PKL Industri'),
                    'status' => $ch->status ?? 'pending',
                    'notes' => $ch->notes ?? $ch->feedback ?? '-',
                    'created_at' => $ch->created_at,
                ]);
            }
        }

        if ($typeFilter === 'all' || $typeFilter === 'submission') {
            $submissions = $submissionQuery->latest()->get();
            foreach ($submissions as $sub) {
                $fileItems->push([
                    'id' => $sub->id,
                    'type' => 'submission',
                    'type_label' => 'Submisi Proyek PjBL',
                    'badge_color' => 'primary',
                    'icon' => 'ph-archive',
                    'title' => 'Submisi '.($sub->version ? 'v'.$sub->version : '').': '.($sub->project?->title ?? 'Proyek PjBL'),
                    'file_name' => basename($sub->file ?? ''),
                    'file_path' => $sub->file,
                    'file_exists' => $sub->file && Storage::disk('public')->exists($sub->file),
                    'file_size' => $sub->file && Storage::disk('public')->exists($sub->file) ? Storage::disk('public')->size($sub->file) : 0,
                    'student' => $sub->student,
                    'student_name' => $sub->student?->user?->name ?? 'Siswa Tidak Ditemukan',
                    'student_nis' => $sub->student?->nis ?? '-',
                    'class_name' => $sub->student?->schoolClass?->name ?? '-',
                    'context_name' => $sub->project?->title ?? 'Proyek PjBL',
                    'status' => $sub->status ?? 'submitted',
                    'notes' => $sub->notes ?? '-',
                    'created_at' => $sub->created_at,
                ]);
            }
        }

        if ($typeFilter === 'all' || $typeFilter === 'logbook') {
            $logs = $logQuery->latest()->get();
            foreach ($logs as $log) {
                $fileItems->push([
                    'id' => $log->id,
                    'type' => 'logbook',
                    'type_label' => 'Lampiran Jurnal / Logbook',
                    'badge_color' => 'info',
                    'icon' => 'ph-image',
                    'title' => 'Bukti Jurnal: '.Str::limit($log->description, 35),
                    'file_name' => basename($log->image ?? ''),
                    'file_path' => $log->image,
                    'file_exists' => $log->image && Storage::disk('public')->exists($log->image),
                    'file_size' => $log->image && Storage::disk('public')->exists($log->image) ? Storage::disk('public')->size($log->image) : 0,
                    'student' => $log->student,
                    'student_name' => $log->student?->user?->name ?? 'Siswa Tidak Ditemukan',
                    'student_nis' => $log->student?->nis ?? '-',
                    'class_name' => $log->student?->schoolClass?->name ?? '-',
                    'context_name' => $log->project?->title ?? 'Proyek PjBL',
                    'status' => 'approved',
                    'notes' => $log->description ?? '-',
                    'created_at' => $log->created_at,
                ]);
            }
        }

        // Sort unified files list by latest
        $fileItems = $fileItems->sortByDesc('created_at')->values();

        // Calculate counts
        $totalPklCount = PklChapter::count();
        $totalSubmissionCount = Submission::count();
        $totalLogbookCount = ProjectLog::whereNotNull('image')->count();

        // Paginate manually for collection (15 items per page)
        $page = (int) $request->get('page', 1);
        $perPage = 15;
        $paginatedFiles = new LengthAwarePaginator(
            $fileItems->forPage($page, $perPage),
            $fileItems->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $students = Student::with(['user', 'schoolClass'])->get();
        $projects = Project::orderBy('title')->get();

        return view('admin.student_progress.index', [
            'files' => $paginatedFiles,
            'classes' => $classes,
            'students' => $students,
            'projects' => $projects,
            'typeFilter' => $typeFilter,
            'statusFilter' => $statusFilter,
            'classId' => $classId,
            'search' => $search,
            'totalPklCount' => $totalPklCount,
            'totalSubmissionCount' => $totalSubmissionCount,
            'totalLogbookCount' => $totalLogbookCount,
            'totalFilesCount' => $totalPklCount + $totalSubmissionCount + $totalLogbookCount,
        ]);
    }

    /**
     * Display student progress matrix (PjBL + PKL).
     */
    public function progressMatrix(Request $request)
    {
        $search = $request->query('search');
        $classId = $request->query('class_id');

        $classes = SchoolClass::orderBy('name')->get();

        $studentsQuery = Student::with([
            'user',
            'schoolClass',
            'pklGroups.company',
            'pklGroups.mentorTeacher',
            'pklReport.chapters',
            'projects.submissions' => fn ($q) => $q->latest(),
            'projectLogs',
        ]);

        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($classId) {
            $studentsQuery->where('class_id', $classId);
        }

        $students = $studentsQuery->paginate(20)->withQueryString();

        return view('admin.student_progress.matrix', [
            'students' => $students,
            'classes' => $classes,
            'classId' => $classId,
            'search' => $search,
        ]);
    }

    /**
     * Download any student file safely.
     */
    public function download(Request $request, string $type, int $id)
    {
        $filePath = null;
        $fileName = null;

        if ($type === 'pkl') {
            $chapter = PklChapter::with('report.student.user')->findOrFail($id);
            $filePath = $chapter->file;
            $fileName = 'PKL_Bab'.$chapter->chapter_number.'_'.($chapter->report?->student?->nis ?? $id).'.'.File::extension($filePath);
        } elseif ($type === 'submission') {
            $sub = Submission::with(['student', 'project'])->findOrFail($id);
            $filePath = $sub->file;
            $fileName = 'PjBL_'.Str::slug($sub->project?->title ?? 'Submission').'_'.($sub->student?->nis ?? $id).'.'.File::extension($filePath);
        } elseif ($type === 'logbook') {
            $log = ProjectLog::with('student')->findOrFail($id);
            $filePath = $log->image;
            $fileName = 'Logbook_'.($log->student?->nis ?? $id).'_'.$id.'.'.File::extension($filePath);
        }

        if (! $filePath || ! Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'Berkas fisik tidak ditemukan di server.');
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DOWNLOAD_STUDENT_FILE',
            'details' => "Mengunduh berkas {$type} ID #{$id} ({$fileName}).",
            'ip_address' => $request->ip(),
        ]);

        return Storage::disk('public')->download($filePath, $fileName);
    }

    /**
     * Store manual file upload on behalf of a student.
     */
    public function storeFile(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'type' => ['required', 'in:pkl,submission'],
            'file' => ['required', 'file', 'max:51200'], // max 50MB
            'title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string'],
            'chapter_number' => ['nullable', 'integer', 'min:1', 'max:10'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);

        $student = Student::with('user')->findOrFail($request->student_id);

        if ($request->type === 'pkl') {
            // Find or create PKL Report for student
            $currentGroup = $student->pklGroups()->latest()->first();
            $companyName = $currentGroup?->company?->name ?? 'Mitra DU/DI PKL';
            $companyId = $currentGroup?->company_id;

            $report = PklReport::firstOrCreate(
                ['student_id' => $student->id],
                [
                    'title' => 'Laporan Praktik Kerja Lapangan',
                    'company_name' => $companyName,
                    'company_id' => $companyId,
                    'mentor_teacher_id' => $currentGroup?->mentor_teacher_id,
                    'status' => 'approved',
                ]
            );

            $storedPath = $request->file('file')->store('pkl_chapters', 'public');

            $chapter = PklChapter::create([
                'report_id' => $report->id,
                'chapter_number' => $request->chapter_number ?: 1,
                'title' => $request->title ?: 'Bab '.($request->chapter_number ?: 1),
                'file' => $storedPath,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'UPLOAD_STUDENT_FILE',
                'details' => "Admin mengunggah berkas PKL Bab {$chapter->chapter_number} untuk {$student->user->name}.",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('admin.student-progress.index', ['type' => 'pkl'])
                ->with('success', "Berkas Laporan PKL berhasil diunggah untuk siswa {$student->user->name}.");
        }

        // Project Submission
        $project = Project::find($request->project_id) ?? Project::first();
        $storedPath = $request->file('file')->store('project_submissions', 'public');

        $submission = Submission::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'file' => $storedPath,
            'version' => 1,
            'notes' => $request->notes ?: 'Diunggah oleh Administrator',
            'status' => $request->status,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPLOAD_STUDENT_FILE',
            'details' => "Admin mengunggah submisi proyek {$project->title} untuk {$student->user->name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.student-progress.index', ['type' => 'submission'])
            ->with('success', "Berkas Submisi Proyek berhasil diunggah untuk siswa {$student->user->name}.");
    }

    /**
     * Update student file metadata / status / notes.
     */
    public function updateFile(Request $request, string $type, int $id)
    {
        $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'title' => ['nullable', 'string', 'max:255'],
            'new_file' => ['nullable', 'file', 'max:51200'],
        ]);

        if ($type === 'pkl') {
            $chapter = PklChapter::findOrFail($id);
            $chapter->status = $request->status;
            if ($request->filled('title')) {
                $chapter->title = $request->title;
            }
            if ($request->filled('notes')) {
                $chapter->feedback = $request->notes;
            }

            if ($request->hasFile('new_file')) {
                if ($chapter->file && Storage::disk('public')->exists($chapter->file)) {
                    Storage::disk('public')->delete($chapter->file);
                }
                $chapter->file = $request->file('new_file')->store('pkl_chapters', 'public');
            }

            $chapter->save();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'UPDATE_STUDENT_FILE',
                'details' => "Memperbarui status/berkas Bab PKL ID #{$id} menjadi: {$chapter->status}.",
                'ip_address' => $request->ip(),
            ]);

            return back()->with('success', 'Data berkas PKL siswa berhasil diperbarui.');
        } elseif ($type === 'submission') {
            $sub = Submission::findOrFail($id);
            $sub->status = $request->status;
            if ($request->filled('notes')) {
                $sub->notes = $request->notes;
            }

            if ($request->hasFile('new_file')) {
                if ($sub->file && Storage::disk('public')->exists($sub->file)) {
                    Storage::disk('public')->delete($sub->file);
                }
                $sub->file = $request->file('new_file')->store('project_submissions', 'public');
                $sub->version = ($sub->version ?? 1) + 1;
            }

            $sub->save();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'UPDATE_STUDENT_FILE',
                'details' => "Memperbarui status/berkas Submisi Proyek ID #{$id} menjadi: {$sub->status}.",
                'ip_address' => $request->ip(),
            ]);

            return back()->with('success', 'Data submisi proyek siswa berhasil diperbarui.');
        }

        return back()->with('info', 'Tidak ada perubahan yang dilakukan.');
    }

    /**
     * Delete a student file permanently.
     */
    public function destroyFile(Request $request, string $type, int $id)
    {
        $fileName = 'file';

        if ($type === 'pkl') {
            $chapter = PklChapter::findOrFail($id);
            if ($chapter->file && Storage::disk('public')->exists($chapter->file)) {
                Storage::disk('public')->delete($chapter->file);
            }
            $fileName = $chapter->title ?? "Bab {$chapter->chapter_number}";
            $chapter->delete();
        } elseif ($type === 'submission') {
            $sub = Submission::findOrFail($id);
            if ($sub->file && Storage::disk('public')->exists($sub->file)) {
                Storage::disk('public')->delete($sub->file);
            }
            $fileName = basename($sub->file ?? 'submisi');
            $sub->delete();
        } elseif ($type === 'logbook') {
            $log = ProjectLog::findOrFail($id);
            if ($log->image && Storage::disk('public')->exists($log->image)) {
                Storage::disk('public')->delete($log->image);
            }
            $fileName = basename($log->image ?? 'logbook');
            $log->image = null;
            $log->save();
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_STUDENT_FILE',
            'details' => "Menghapus berkas {$type} ({$fileName}) milik siswa.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Berkas '{$fileName}' berhasil dihapus secara permanen.");
    }
}
