<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Student;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PjblTest extends TestCase
{
    use RefreshDatabase;

    protected $guru;
    protected $ketua;
    protected $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->guru = User::where('email', 'guru@kreasi.test')->first();
        $this->ketua = User::where('email', 'ketua@kreasi.test')->first();
        $this->siswa = User::where('email', 'siswa@kreasi.test')->first();
    }

    public function test_guru_can_create_group_project_and_assign_members(): void
    {
        $this->actingAs($this->guru);

        $student1 = $this->ketua->student;
        $student2 = $this->siswa->student;

        $response = $this->post(route('guru.projects.store'), [
            'title' => 'Sistem Informasi E-Commerce RPL',
            'description' => 'Membangun aplikasi toko online lengkap dengan keranjang belanja.',
            'goals' => 'Menguasai MVC dan relational database.',
            'instructions' => 'Gunakan Laravel dan MySQL.',
            'type' => 'group',
            'status' => 'active',
            'deadline' => now()->addDays(14)->format('Y-m-d\TH:i'),
            'student_ids' => [$student1->id, $student2->id],
            'leader_student_id' => $student1->id,
        ]);

        $response->assertRedirect(route('guru.projects.index'));
        $this->assertDatabaseHas('projects', ['title' => 'Sistem Informasi E-Commerce RPL']);
        $this->assertDatabaseHas('project_members', [
            'student_id' => $student1->id,
            'role' => 'leader',
        ]);
        $this->assertDatabaseHas('project_members', [
            'student_id' => $student2->id,
            'role' => 'member',
        ]);
    }

    public function test_student_can_view_project_and_submit_task(): void
    {
        // 1. Create project first
        $this->actingAs($this->guru);
        $student = $this->siswa->student;

        $project = Project::create([
            'title' => 'Web Service API Siswa',
            'description' => 'REST API untuk manajemen data siswa.',
            'type' => 'individual',
            'status' => 'active',
            'deadline' => now()->addDays(7),
            'created_by' => $this->guru->id,
        ]);
        $project->members()->create([
            'student_id' => $student->id,
            'role' => 'member',
        ]);

        // 2. Student views project
        $this->actingAs($this->siswa);
        $viewResponse = $this->get(route('siswa.projects.show', $project->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Web Service API Siswa');

        // 3. Student submits task
        $dummyFile = UploadedFile::fake()->create('source_code_v1.zip', 500, 'application/zip');
        $submitResponse = $this->post(route('siswa.projects.submit', $project->id), [
            'file' => $dummyFile,
            'notes' => 'Berikut source code versi 1 selesai dibuat.',
        ]);

        $submitResponse->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'project_id' => $project->id,
            'student_id' => $student->id,
            'version' => 1,
            'status' => 'submitted',
        ]);
    }

    public function test_student_can_record_logbook_entry(): void
    {
        $this->actingAs($this->guru);
        $student = $this->siswa->student;

        $project = Project::create([
            'title' => 'Aplikasi Perpustakaan',
            'description' => 'Sistem sirkulasi buku.',
            'type' => 'individual',
            'status' => 'active',
            'created_by' => $this->guru->id,
        ]);
        $project->members()->create([
            'student_id' => $student->id,
            'role' => 'member',
        ]);

        $this->actingAs($this->siswa);

        $response = $this->post(route('siswa.logbook.store'), [
            'project_id' => $project->id,
            'description' => 'Mengerjakan halaman login dan validasi autentikasi.',
            'progress_percentage' => 40,
        ]);

        $response->assertRedirect(route('siswa.logbook.index'));
        $this->assertDatabaseHas('project_logs', [
            'project_id' => $project->id,
            'student_id' => $student->id,
            'progress_percentage' => 40,
        ]);
    }

    public function test_teacher_can_review_and_grade_submission(): void
    {
        // 1. Setup project & submission
        $project = Project::create([
            'title' => 'Aplikasi Penggajian',
            'description' => 'Payroll software.',
            'type' => 'individual',
            'status' => 'active',
            'created_by' => $this->guru->id,
        ]);
        $student = $this->siswa->student;
        $project->members()->create(['student_id' => $student->id, 'role' => 'member']);

        $submission = Submission::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'file' => 'submissions/dummy.zip',
            'version' => 1,
            'status' => 'submitted',
        ]);

        // 2. Teacher reviews submission
        $this->actingAs($this->guru);

        $reviewResponse = $this->post(route('guru.submissions.review', $submission->id), [
            'status' => 'approved',
            'score' => 92.5,
            'comment' => 'Karya sangat bagus, struktur kode rapi dan memenuhi rubrik.',
        ]);

        $reviewResponse->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('reviews', [
            'submission_id' => $submission->id,
            'score' => 92.5,
            'status' => 'approved',
        ]);
    }
}
