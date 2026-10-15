<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PklReport;
use App\Models\Project;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Submission;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard.
     */
    public function admin()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalProjects = Project::count();
        $totalClasses = SchoolClass::count();

        $pklStats = [
            'total' => PklReport::count(),
            'approved' => PklReport::where('status', 'approved')->count(),
            'submitted' => PklReport::where('status', 'submitted')->count(),
            'revision_required' => PklReport::where('status', 'revision_required')->count(),
            'draft' => PklReport::where('status', 'draft')->count(),
        ];

        $recentActivities = AuditLog::with('user')->latest()->take(8)->get();
        $recentProjects = Project::with('creator')->latest()->take(5)->get();

        return view('dashboards.admin', compact(
            'totalStudents',
            'totalTeachers',
            'totalProjects',
            'totalClasses',
            'pklStats',
            'recentActivities',
            'recentProjects'
        ));
    }

    /**
     * Guru Dashboard.
     */
    public function guru()
    {
        $user = Auth::user();
        
        $activeProjects = Project::where('created_by', $user->id)
            ->where('status', 'active')
            ->count();

        $totalMyProjects = Project::where('created_by', $user->id)->count();

        // Submissions to projects created by this teacher
        $incomingSubmissions = Submission::whereHas('project', function ($q) use ($user) {
            $q->where('created_by', $user->id);
        })->where('status', 'submitted')->count();

        $pendingReviews = Submission::whereHas('project', function ($q) use ($user) {
            $q->where('created_by', $user->id);
        })->whereIn('status', ['submitted', 'revision_required'])->count();

        $recentSubmissions = Submission::with(['project', 'student.user', 'reviews'])
            ->whereHas('project', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();

        $myProjects = Project::where('created_by', $user->id)->latest()->take(5)->get();
        $myPklMentees = PklReport::with(['student.user', 'student.schoolClass'])
            ->where('mentor_teacher_id', $user->id)
            ->get();

        return view('dashboards.guru', compact(
            'activeProjects',
            'totalMyProjects',
            'incomingSubmissions',
            'pendingReviews',
            'recentSubmissions',
            'myProjects',
            'myPklMentees'
        ));
    }

    /**
     * Siswa & Ketua Kelompok Dashboard.
     */
    public function siswa()
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            // If student profile not yet linked, show empty state
            $myProjects = collect();
            $upcomingDeadlines = collect();
            $pklReport = null;
            $recentSubmissions = collect();
            $recentLogs = collect();
            return view('dashboards.siswa', compact('myProjects', 'upcomingDeadlines', 'pklReport', 'recentSubmissions', 'recentLogs'));
        }

        $myProjects = $student->projects()->with(['creator', 'members'])->get();
        
        $upcomingDeadlines = $student->projects()
            ->where('deadline', '>=', now())
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        $pklReport = $student->pklReport()->with(['mentorTeacher', 'chapters'])->first();
        $pklGroup = $student->currentPklGroup();
        if ($pklGroup) {
            $pklGroup->load(['company', 'mentorTeacher.teacher', 'students.user', 'students.schoolClass']);
        }

        $recentSubmissions = $student->submissions()->with('project')->latest()->take(5)->get();
        $recentLogs = $student->projectLogs()->with('project')->latest()->take(5)->get();

        return view('dashboards.siswa', compact(
            'myProjects',
            'upcomingDeadlines',
            'pklReport',
            'pklGroup',
            'recentSubmissions',
            'recentLogs'
        ));
    }
}
