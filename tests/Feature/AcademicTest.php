<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $guru;
    protected $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('email', 'admin@kreasi.test')->first();
        $this->guru = User::where('email', 'guru@kreasi.test')->first();
        $this->siswa = User::where('email', 'siswa@kreasi.test')->first();
    }

    public function test_admin_can_view_user_management(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Pengguna');
        $response->assertSee('Budi Santoso');
    }

    public function test_admin_can_create_new_student_user(): void
    {
        $this->actingAs($this->admin);

        $class = SchoolClass::first();

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Doni Pratama',
            'email' => 'doni@kreasi.test',
            'role' => 'siswa',
            'password' => 'password123',
            'nis' => '2026120999',
            'class_id' => $class->id,
            'gender' => 'L',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'doni@kreasi.test']);
        $this->assertDatabaseHas('students', ['nis' => '2026120999']);
    }

    public function test_admin_can_manage_classes(): void
    {
        $this->actingAs($this->admin);

        $year = AcademicYear::first();

        $response = $this->post(route('admin.classes.store'), [
            'name' => 'X RPL 2',
            'grade_level' => 'X',
            'academic_year_id' => $year->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('classes', ['name' => 'X RPL 2']);
    }

    public function test_admin_can_download_import_template(): void
    {
        $this->actingAs($this->admin);

        // Test default formatted Excel (.xlsx) template
        $response = $this->get(route('admin.students.import.template'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Test CSV format template
        $csvResponse = $this->get(route('admin.students.import.template', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_preview_and_commit_student_import(): void
    {
        $this->actingAs($this->admin);

        $class = SchoolClass::first();
        $csvContent = "Nama,NIS,Email,Kelas\n\"Bayu Wicaksono\",\"2026120888\",\"bayu@kreasi.test\",\"{$class->name}\"\n";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('students.csv', $csvContent);

        $previewResponse = $this->post(route('admin.students.import.preview'), [
            'file' => $file,
        ]);

        $previewResponse->assertStatus(200);
        $previewResponse->assertSee('Bayu Wicaksono');
        $previewResponse->assertSee('Siap Diimport');

        $commitResponse = $this->post(route('admin.students.import.commit'));
        $commitResponse->assertRedirect(route('admin.users.index', ['role' => 'siswa']));

        $this->assertDatabaseHas('users', ['email' => 'bayu@kreasi.test']);
        $this->assertDatabaseHas('students', ['nis' => '2026120888']);
    }

    public function test_admin_can_view_audit_logs(): void
    {
        $this->actingAs($this->admin);

        $dashResponse = $this->get(route('admin.audit-logs'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Audit Log');
    }

    public function test_non_admin_cannot_access_academic_management(): void
    {
        $this->actingAs($this->siswa);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(403);

        $response2 = $this->get(route('admin.classes.index'));
        $response2->assertStatus(403);
    }
}
