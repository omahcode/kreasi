<?php

namespace Tests\Feature;

use App\Models\PklChapter;
use App\Models\PklReport;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PklTest extends TestCase
{
    use RefreshDatabase;

    protected $guru;

    protected $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->guru = User::where('email', 'guru@kreasi.test')->first();
        $this->siswa = User::where('email', 'siswa@kreasi.test')->first();
    }

    public function test_student_can_submit_pkl_title(): void
    {
        $this->actingAs($this->siswa);

        $response = $this->post(route('siswa.pkl.title.store'), [
            'title' => 'Sistem Pemantauan IoT Server Berbasis Web pada PT. Telkom',
            'company_name' => 'PT. Telekomunikasi Indonesia',
            'company_address' => 'Jl. Lembong No. 11, Bandung',
            'mentor_teacher_id' => $this->guru->id,
            'start_date' => '2026-07-01',
            'end_date' => '2026-10-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pkl_reports', [
            'student_id' => $this->siswa->student->id,
            'company_name' => 'PT. Telekomunikasi Indonesia',
            'status' => 'submitted',
        ]);

        // Assert 5 chapters were automatically prepared
        $report = PklReport::where('student_id', $this->siswa->student->id)->first();
        $this->assertEquals(5, $report->chapters()->count());
    }

    public function test_teacher_can_review_and_approve_pkl_title(): void
    {
        // 1. Submit title as student
        $student = $this->siswa->student;
        $report = PklReport::create([
            'student_id' => $student->id,
            'title' => 'Aplikasi Presensi GPS Mobile',
            'company_name' => 'PT. Inovasi Digital',
            'mentor_teacher_id' => $this->guru->id,
            'status' => 'submitted',
        ]);

        // 2. Teacher reviews and approves title
        $this->actingAs($this->guru);

        $response = $this->post(route('guru.pkl.title.review', $report->id), [
            'status' => 'approved',
            'rejection_note' => 'Judul disetujui, silakan mulai bimbingan Bab I.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pkl_reports', [
            'id' => $report->id,
            'status' => 'approved',
        ]);
    }

    public function test_student_can_upload_pkl_chapter_and_teacher_can_review(): void
    {
        $student = $this->siswa->student;
        $report = PklReport::create([
            'student_id' => $student->id,
            'title' => 'Sistem Kasir Pintar',
            'company_name' => 'CV. Solusi Mandiri',
            'mentor_teacher_id' => $this->guru->id,
            'status' => 'approved',
        ]);

        $chapter1 = PklChapter::create([
            'report_id' => $report->id,
            'chapter_number' => 1,
            'title' => 'Bab I: Pendahuluan',
            'status' => 'draft',
        ]);

        // Student uploads Bab 1
        $this->actingAs($this->siswa);
        $file = UploadedFile::fake()->create('bab_1_pendahuluan.pdf', 800, 'application/pdf');

        $uploadResponse = $this->post(route('siswa.pkl.chapters.upload', 1), [
            'file' => $file,
            'notes' => 'Berikut naskah Bab 1 untuk dibimbing.',
        ]);

        $uploadResponse->assertRedirect();
        $this->assertDatabaseHas('pkl_chapters', [
            'id' => $chapter1->id,
            'status' => 'submitted',
        ]);

        // Teacher reviews Bab 1
        $this->actingAs($this->guru);

        $reviewResponse = $this->post(route('guru.pkl.chapters.review', $chapter1->id), [
            'status' => 'approved',
            'feedback' => 'Bab I sangat lengkap, latar belakang dan rumusan masalah jelas.',
        ]);

        $reviewResponse->assertRedirect();
        $this->assertDatabaseHas('pkl_chapters', [
            'id' => $chapter1->id,
            'status' => 'approved',
        ]);
    }

    public function test_teacher_cannot_grant_final_approval_if_not_all_5_chapters_are_approved(): void
    {
        $student = $this->siswa->student;
        $report = PklReport::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Manajemen Laboratorium',
            'company_name' => 'PT. Inovasi Kreatif',
            'mentor_teacher_id' => $this->guru->id,
            'status' => 'submitted',
        ]);

        // Create 5 chapters with only 3 approved and 2 in draft/revision
        for ($i = 1; $i <= 5; $i++) {
            PklChapter::create([
                'report_id' => $report->id,
                'chapter_number' => $i,
                'title' => "Bab {$i}",
                'file' => $i <= 3 ? "pkl_chapters/bab_{$i}.pdf" : null,
                'status' => $i <= 3 ? 'approved' : 'draft',
            ]);
        }

        $this->actingAs($this->guru);

        // Attempt final approval
        $response = $this->post(route('guru.pkl.final-approval', $report->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $report->refresh();
        $this->assertNotSame('approved', $report->status);

        // Check view does not show sahkan button
        $viewResponse = $this->get(route('guru.pkl.show', $report->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertDontSee('Sahkan Persetujuan Final Laporan');
        $viewResponse->assertSee('Pengesahan aktif setelah seluruh 5 bab disetujui');
    }

    public function test_teacher_can_grant_final_approval_when_all_5_chapters_are_uploaded_and_approved(): void
    {
        $student = $this->siswa->student;
        $report = PklReport::create([
            'student_id' => $student->id,
            'title' => 'Sistem Rekomendasi Portofolio Berbasis Web',
            'company_name' => 'PT. Kode Kreatif',
            'mentor_teacher_id' => $this->guru->id,
            'status' => 'submitted',
        ]);

        // Create 5 chapters, all uploaded and approved
        for ($i = 1; $i <= 5; $i++) {
            PklChapter::create([
                'report_id' => $report->id,
                'chapter_number' => $i,
                'title' => "Bab {$i}",
                'file' => "pkl_chapters/bab_{$i}.pdf",
                'status' => 'approved',
            ]);
        }

        $this->actingAs($this->guru);

        // View should now display the Sahkan button and Preview Bab button
        $viewResponse = $this->get(route('guru.pkl.show', $report->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Sahkan Persetujuan Final Laporan');
        $viewResponse->assertSee('Preview Bab');

        // Post final approval
        $response = $this->post(route('guru.pkl.final-approval', $report->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $report->refresh();
        $this->assertSame('approved', $report->status);
    }
}
