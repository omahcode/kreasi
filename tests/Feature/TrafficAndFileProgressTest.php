<?php

namespace Tests\Feature;

use App\Models\PklChapter;
use App\Models\Student;
use App\Models\TrafficLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TrafficAndFileProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_track_traffic_middleware_records_request(): void
    {
        $admin = User::where('email', 'admin@kreasi.test')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        $this->assertDatabaseHas('traffic_logs', [
            'user_id' => $admin->id,
            'method' => 'GET',
            'path' => '/admin/dashboard',
        ]);
    }

    public function test_admin_can_access_traffic_monitoring_dashboard(): void
    {
        $admin = User::where('email', 'admin@kreasi.test')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/traffic');
        $response->assertStatus(200);
        $response->assertSee('Total Akses Hari Ini');
        $response->assertSee('Total Penggunaan Storage Disk');
        $response->assertSee('Detail Seluruh Berkas di Storage');
    }

    public function test_admin_can_clear_old_traffic_logs(): void
    {
        $admin = User::where('email', 'admin@kreasi.test')->first();
        $this->actingAs($admin);

        // Create an old log (35 days ago)
        $oldLog = TrafficLog::create([
            'ip_address' => '127.0.0.1',
            'method' => 'GET',
            'path' => '/old/path',
            'status_code' => 200,
            'user_agent' => 'Mozilla/5.0',
        ]);

        TrafficLog::where('id', $oldLog->id)->update([
            'created_at' => Carbon::now()->subDays(35),
            'updated_at' => Carbon::now()->subDays(35),
        ]);

        $this->assertDatabaseHas('traffic_logs', ['id' => $oldLog->id]);

        $response = $this->post('/admin/traffic/clear');
        $response->assertRedirect('/admin/traffic');
        $this->assertDatabaseMissing('traffic_logs', ['id' => $oldLog->id]);
    }

    public function test_admin_can_view_student_files_and_progress_matrix(): void
    {
        $admin = User::where('email', 'admin@kreasi.test')->first();
        $this->actingAs($admin);

        // View student files hub
        $response = $this->get('/admin/student-progress');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Berkas', false);

        // View student progress matrix
        $matrixResponse = $this->get('/admin/student-progress/matrix');
        $matrixResponse->assertStatus(200);
        $matrixResponse->assertSee('Matriks Capaian Siswa', false);
    }

    public function test_admin_can_store_update_download_and_destroy_student_file(): void
    {
        Storage::fake('public');

        $admin = User::where('email', 'admin@kreasi.test')->first();
        $this->actingAs($admin);

        $student = Student::first();
        $fakeFile = UploadedFile::fake()->create('bab1_laporan.pdf', 300, 'application/pdf');

        // 1. Admin stores file
        $response = $this->post('/admin/student-progress/file', [
            'student_id' => $student->id,
            'type' => 'pkl',
            'file' => $fakeFile,
            'title' => 'Bab 1 Pendahuluan Siswa',
            'chapter_number' => 1,
            'status' => 'approved',
            'notes' => 'Tervalidasi oleh administrator',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pkl_chapters', [
            'title' => 'Bab 1 Pendahuluan Siswa',
            'chapter_number' => 1,
            'status' => 'approved',
        ]);

        $chapter = PklChapter::where('title', 'Bab 1 Pendahuluan Siswa')->first();
        Storage::disk('public')->assertExists($chapter->file);

        // 2. Download file
        $downloadResponse = $this->get("/admin/student-progress/download/pkl/{$chapter->id}");
        $downloadResponse->assertStatus(200);

        // 3. Update file metadata
        $updateResponse = $this->put("/admin/student-progress/file/pkl/{$chapter->id}", [
            'status' => 'revision',
            'title' => 'Bab 1 Pendahuluan (Revisi)',
            'notes' => 'Perbaiki latar belakang masalah',
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('pkl_chapters', [
            'id' => $chapter->id,
            'status' => 'revision',
            'title' => 'Bab 1 Pendahuluan (Revisi)',
            'feedback' => 'Perbaiki latar belakang masalah',
        ]);

        // 4. Destroy file
        $storedFilePath = $chapter->fresh()->file;
        $deleteResponse = $this->delete("/admin/student-progress/file/pkl/{$chapter->id}");
        $deleteResponse->assertRedirect();

        $this->assertDatabaseMissing('pkl_chapters', ['id' => $chapter->id]);
        Storage::disk('public')->assertMissing($storedFilePath);
    }
}
