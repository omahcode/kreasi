<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\PklChapter;
use App\Models\PklReport;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PklController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            abort(403, 'Profil siswa Anda belum terhubung.');
        }

        $report = PklReport::where('student_id', $student->id)
            ->with(['mentorTeacher', 'chapters', 'pklGroup.company', 'pklGroup.students.user'])
            ->first();

        $pklGroup = $student->currentPklGroup();
        if ($pklGroup) {
            $pklGroup->load(['company', 'mentorTeacher', 'students.user']);

            if ($report) {
                $needsSync = false;
                $syncData = [];

                if ($pklGroup->mentor_teacher_id && $report->mentor_teacher_id !== $pklGroup->mentor_teacher_id) {
                    $syncData['mentor_teacher_id'] = $pklGroup->mentor_teacher_id;
                    $needsSync = true;
                }

                if ($report->pkl_group_id !== $pklGroup->id) {
                    $syncData['pkl_group_id'] = $pklGroup->id;
                    $needsSync = true;
                }

                if ($pklGroup->company) {
                    if ($report->company_id !== $pklGroup->company_id) {
                        $syncData['company_id'] = $pklGroup->company_id;
                        $needsSync = true;
                    }
                    if ($report->company_name !== $pklGroup->company->name) {
                        $syncData['company_name'] = $pklGroup->company->name;
                        $needsSync = true;
                    }
                    if ($pklGroup->company->address && $report->company_address !== $pklGroup->company->address) {
                        $syncData['company_address'] = $pklGroup->company->address;
                        $needsSync = true;
                    }
                }

                if ($needsSync) {
                    $report->update($syncData);
                    $report->load(['mentorTeacher', 'chapters', 'pklGroup.company', 'pklGroup.students.user']);
                }
            }
        }

        $teachers = User::where('role', 'guru')->get();

        $chapterTitles = [
            1 => 'Bab I: Pendahuluan (Latar Belakang, Tujuan, Manfaat)',
            2 => 'Bab II: Gambaran Umum Perusahaan / Mitra DUDI',
            3 => 'Bab III: Pelaksanaan Praktik Kerja Lapangan',
            4 => 'Bab IV: Hasil Karya & Implementasi Sistem Rekayasa Perangkat Lunak',
            5 => 'Bab V: Penutup (Kesimpulan & Saran)',
        ];

        return view('siswa.pkl.index', compact('report', 'teachers', 'chapterTitles', 'pklGroup'));
    }

    public function storeTitle(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $pklGroup = $student->currentPklGroup();

        if ($pklGroup) {
            $request->validate([
                'title' => ['required', 'string', 'max:255'],
            ], [
                'title.required' => 'Judul laporan PKL wajib diisi.',
            ]);

            $companyName = $pklGroup->company->name ?? 'DUDI';
            $companyAddress = $pklGroup->company->address ?? '';
            $mentorTeacherId = $pklGroup->mentor_teacher_id;
            $startDate = $pklGroup->start_date;
            $endDate = $pklGroup->end_date;
            $pklGroupId = $pklGroup->id;
            $companyId = $pklGroup->company_id;
        } else {
            $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'company_name' => ['required', 'string', 'max:255'],
                'company_address' => ['nullable', 'string'],
                'mentor_teacher_id' => ['required', 'exists:users,id'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date'],
            ], [
                'title.required' => 'Judul laporan PKL wajib diisi.',
                'company_name.required' => 'Nama industri / instansi tempat PKL wajib diisi.',
                'mentor_teacher_id.required' => 'Pilih guru pembimbing PKL Anda.',
            ]);

            $companyName = $request->company_name;
            $companyAddress = $request->company_address;
            $mentorTeacherId = $request->mentor_teacher_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $pklGroupId = null;
            $companyId = null;
        }

        $report = PklReport::create([
            'student_id' => $student->id,
            'pkl_group_id' => $pklGroupId,
            'company_id' => $companyId,
            'title' => $request->title,
            'company_name' => $companyName,
            'company_address' => $companyAddress,
            'mentor_teacher_id' => $mentorTeacherId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'submitted',
            'title_status' => 'submitted',
        ]);

        // Create 5 chapter draft records
        $chapterTitles = [
            1 => 'Bab I: Pendahuluan',
            2 => 'Bab II: Gambaran Umum Perusahaan',
            3 => 'Bab III: Pelaksanaan PKL',
            4 => 'Bab IV: Hasil Karya & Pembahasan',
            5 => 'Bab V: Penutup & Saran',
        ];

        foreach ($chapterTitles as $num => $title) {
            PklChapter::create([
                'report_id' => $report->id,
                'chapter_number' => $num,
                'title' => $title,
                'status' => 'draft',
            ]);
        }

        // Notify mentor teacher
        if ($mentorTeacherId) {
            Notification::create([
                'user_id' => $mentorTeacherId,
                'title' => 'Pengajuan Judul Laporan PKL Baru',
                'message' => "Siswa {$user->name} telah mengajukan judul laporan PKL: {$request->title} di {$companyName}",
                'link' => route('guru.pkl.show', $report->id),
            ]);
        }

        AuditLog::record($user->id, 'submit_pkl_title', "Mengajukan judul laporan PKL: {$request->title} ({$companyName})");

        return back()->with('success', 'Pengajuan judul laporan PKL berhasil dikirimkan. Menunggu verifikasi dari Guru Pembimbing.');
    }

    public function updateTitle(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        $report = PklReport::where('student_id', $student->id)->firstOrFail();

        // 1. Jika judul sudah disetujui (ACC), siswa tidak dapat mengedit lagi
        if (($report->title_status ?? '') === 'approved') {
            return back()->with('error', 'Judul laporan PKL telah disetujui (ACC) oleh Guru Pembimbing dan sudah terkunci sehingga tidak dapat diubah lagi.');
        }

        // 2. Siswa hanya dapat mengubah judul laporan (data perusahaan & guru pembimbing hanya dapat dilihat)
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ], [
            'title.required' => 'Judul laporan PKL wajib diisi.',
        ]);

        $report->update([
            'title' => $request->title,
            'status' => 'submitted', // re-submit after revision
            'title_status' => 'submitted',
            'rejection_note' => null,
        ]);

        Notification::create([
            'user_id' => $report->mentor_teacher_id,
            'title' => 'Revisi Judul PKL Dikirimkan',
            'message' => "Siswa {$user->name} telah memperbarui pengajuan judul laporan PKL: {$request->title}",
            'link' => route('guru.pkl.show', $report->id),
        ]);

        AuditLog::record($user->id, 'update_pkl_title', "Memperbarui revisi judul laporan PKL: {$request->title}");

        return back()->with('success', 'Revisi judul laporan PKL berhasil dikirim ulang ke Guru Pembimbing.');
    }

    public function uploadChapter(Request $request, $chapterNumber)
    {
        $user = Auth::user();
        $student = $user->student;

        $report = PklReport::where('student_id', $student->id)->firstOrFail();

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'file.required' => 'Silakan pilih berkas dokumen Bab '.$chapterNumber.' yang akan diunggah.',
            'file.mimes' => 'Format file harus berupa PDF atau Word (.doc, .docx).',
            'file.max' => 'Ukuran file maksimal adalah 20MB.',
        ]);

        $chapter = PklChapter::where('report_id', $report->id)
            ->where('chapter_number', $chapterNumber)
            ->firstOrFail();

        // Delete old file if exists
        if ($chapter->file) {
            Storage::disk('public')->delete($chapter->file);
        }

        $filePath = $request->file('file')->store('pkl_chapters', 'public');

        $chapter->update([
            'file' => $filePath,
            'notes' => $request->notes,
            'status' => 'submitted',
        ]);

        // Notify mentor teacher
        if ($report->mentor_teacher_id) {
            Notification::create([
                'user_id' => $report->mentor_teacher_id,
                'title' => "Bimbingan Bab {$chapterNumber} Diunggah",
                'message' => "Siswa {$user->name} telah mengunggah berkas Bab {$chapterNumber} laporan PKL untuk direview.",
                'link' => route('guru.pkl.show', $report->id),
            ]);
        }

        AuditLog::record($user->id, 'upload_pkl_chapter', "Mengunggah berkas Bab {$chapterNumber} laporan PKL");

        return back()->with('success', "Berkas Bab {$chapterNumber} berhasil diunggah dan siap dibimbing oleh guru.");
    }
}
