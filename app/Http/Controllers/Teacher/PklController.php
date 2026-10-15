<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\PklChapter;
use App\Models\PklReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PklController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = PklReport::with(['student.user', 'student.schoolClass', 'chapters', 'mentorTeacher'])
            ->where(function ($q) {
                // Either assigned to this teacher or unassigned
                $q->where('mentor_teacher_id', Auth::id())
                  ->orWhereNull('mentor_teacher_id');
            })
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $query->paginate(12)->withQueryString();

        $counts = [
            'all' => PklReport::where('mentor_teacher_id', Auth::id())->count(),
            'submitted' => PklReport::where('mentor_teacher_id', Auth::id())->where('status', 'submitted')->count(),
            'revision_required' => PklReport::where('mentor_teacher_id', Auth::id())->where('status', 'revision_required')->count(),
            'approved' => PklReport::where('mentor_teacher_id', Auth::id())->where('status', 'approved')->count(),
        ];

        return view('guru.pkl.index', compact('reports', 'status', 'search', 'counts'));
    }

    public function show($id)
    {
        $report = PklReport::with(['student.user', 'student.schoolClass', 'chapters', 'mentorTeacher'])
            ->findOrFail($id);

        $chapterTitles = [
            1 => 'Bab I: Pendahuluan (Latar Belakang, Tujuan, Manfaat)',
            2 => 'Bab II: Gambaran Umum Perusahaan / DUDI',
            3 => 'Bab III: Pelaksanaan Praktik Kerja Lapangan',
            4 => 'Bab IV: Hasil Karya & Implementasi Sistem Perangkat Lunak',
            5 => 'Bab V: Penutup (Kesimpulan & Saran)',
        ];

        return view('guru.pkl.show', compact('report', 'chapterTitles'));
    }

    public function reviewTitle(Request $request, $id)
    {
        $report = PklReport::with('student.user')->findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:approved,revision_required'],
            'rejection_note' => ['nullable', 'string'],
        ]);

        $report->status = $request->status;
        $report->rejection_note = $request->rejection_note;
        if (!$report->mentor_teacher_id) {
            $report->mentor_teacher_id = Auth::id();
        }
        $report->save();

        // If title approved, ensure the 5 chapters exist
        if ($request->status === 'approved') {
            $chapterTitles = [
                1 => 'Bab I: Pendahuluan',
                2 => 'Bab II: Gambaran Umum Perusahaan',
                3 => 'Bab III: Pelaksanaan PKL',
                4 => 'Bab IV: Hasil Karya & Pembahasan',
                5 => 'Bab V: Penutup & Saran',
            ];

            foreach ($chapterTitles as $num => $title) {
                PklChapter::firstOrCreate(
                    ['report_id' => $report->id, 'chapter_number' => $num],
                    ['title' => $title, 'status' => 'draft']
                );
            }
        }

        // Send notification
        $statusLabel = ($request->status === 'approved') ? 'Disetujui' : 'Perlu Revisi';
        Notification::create([
            'user_id' => $report->student->user_id,
            'title' => "Status Judul Laporan PKL: {$statusLabel}",
            'message' => "Guru pembimbing " . Auth::user()->name . " telah menetapkan status judul PKL Anda: {$statusLabel}. " . ($request->rejection_note ? "Catatan: {$request->rejection_note}" : ''),
            'link' => route('siswa.pkl.index'),
        ]);

        AuditLog::record(Auth::id(), 'review_pkl_title', "Meninjau judul PKL siswa {$report->student->user->name}: {$statusLabel}");

        return back()->with('success', "Status judul laporan PKL berhasil diperbarui menjadi {$statusLabel}.");
    }

    public function reviewChapter(Request $request, $chapterId)
    {
        $chapter = PklChapter::with('report.student.user')->findOrFail($chapterId);

        $request->validate([
            'status' => ['required', 'in:approved,revision_required,submitted'],
            'feedback' => ['required', 'string'],
        ], [
            'feedback.required' => 'Wajib memberikan catatan bimbingan atau koreksi revisi bab.',
        ]);

        $chapter->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
        ]);

        $statusLabel = ($request->status === 'approved') ? 'Disetujui' : 'Perlu Revisi';

        Notification::create([
            'user_id' => $chapter->report->student->user_id,
            'title' => "Bimbingan PKL Bab {$chapter->chapter_number}: {$statusLabel}",
            'message' => "Guru pembimbing " . Auth::user()->name . " telah memeriksa Bab {$chapter->chapter_number}: {$statusLabel}. Catatan: {$request->feedback}",
            'link' => route('siswa.pkl.index'),
        ]);

        AuditLog::record(Auth::id(), 'review_pkl_chapter', "Meninjau Bab {$chapter->chapter_number} laporan PKL {$chapter->report->student->user->name} ({$statusLabel})");

        return back()->with('success', "Ulasan dan status Bab {$chapter->chapter_number} berhasil disimpan.");
    }

    public function finalApproval($id)
    {
        $report = PklReport::with(['chapters', 'student.user'])->findOrFail($id);

        // Check if all 5 chapters are approved
        $approvedCount = $report->chapters()->where('status', 'approved')->count();
        if ($approvedCount < 5) {
            return back()->with('error', "Persetujuan final belum dapat diberikan karena baru {$approvedCount} dari 5 bab yang berstatus disetujui.");
        }

        $report->update(['status' => 'approved']);

        Notification::create([
            'user_id' => $report->student->user_id,
            'title' => 'SELAMAT! Laporan PKL Telah Disetujui Final',
            'message' => "Seluruh Bab I s/d Bab V laporan PKL Anda di {$report->company_name} telah resmi disetujui oleh Guru Pembimbing.",
            'link' => route('siswa.pkl.index'),
        ]);

        AuditLog::record(Auth::id(), 'final_approval_pkl', "Memberikan persetujuan akhir (Final Approval) laporan PKL siswa {$report->student->user->name}");

        return back()->with('success', "Persetujuan final berhasil disahkan. Laporan PKL siswa dinyatakan tuntas.");
    }
}
