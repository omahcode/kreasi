<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WargaRplSyncTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('email', 'admin@kreasi.test')->first();
        $this->siswa = User::where('email', 'siswa@kreasi.test')->first();
    }

    public function test_admin_can_view_sync_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.students.sync'));
        $response->assertStatus(200);
        $response->assertSee('Sinkronisasi Data Siswa dari WargaRPL');
        $response->assertSee('WargaRPL API');
    }

    public function test_non_admin_cannot_access_sync_page(): void
    {
        $this->actingAs($this->siswa);

        $response = $this->get(route('admin.students.sync'));
        $response->assertStatus(403);
    }

    public function test_test_connection_endpoint(): void
    {
        Http::fake([
            '*/api/v1/students*' => Http::response([
                'success' => true,
                'data' => [],
                'meta' => ['total' => 10, 'last_page' => 1],
            ], 200),
            '*/api/v1/classes*' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 1, 'name' => 'XII RPL 1', 'grade' => 12],
                ],
            ], 200),
        ]);

        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.students.sync.test'), [
            'token' => 'wgr_test_token',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total_students' => 10,
            'classes_count' => 1,
        ]);
    }

    public function test_preview_endpoint_shows_student_data(): void
    {
        Http::fake([
            '*/api/v1/students*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'uuid' => 'test-uuid-1',
                        'nis' => '99887766',
                        'nisn' => '0011223344',
                        'full_name' => 'Ahmad Test',
                        'gender' => 'L',
                        'birth_date' => '2008-08-17',
                        'status' => 'Aktif',
                        'class' => ['name' => 'XII RPL 1'],
                    ],
                ],
                'meta' => ['total' => 1, 'last_page' => 1],
            ], 200),
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.students.sync.preview'), [
            'token' => 'wgr_test_token',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Ahmad Test');
        $response->assertSee('99887766');
        $response->assertSee('17082008'); // Auto-generated password from 17-08-2008
    }

    public function test_sync_process_creates_students_and_allows_login(): void
    {
        Http::fake([
            '*/api/v1/classes' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 10, 'name' => 'X PPLG 3', 'grade' => 10],
                ],
            ], 200),
            '*/api/v1/students*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'uuid' => 'sync-uuid-1',
                        'nis' => '12639999',
                        'nisn' => '0109998887',
                        'full_name' => 'Bambang Pamungkas',
                        'gender' => 'L',
                        'birth_date' => '2010-06-10',
                        'status' => 'Aktif',
                        'class' => ['name' => 'X PPLG 3'],
                    ],
                ],
                'meta' => ['total' => 1, 'last_page' => 1],
            ], 200),
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.students.sync.process'), [
            'token' => 'wgr_test_token',
        ]);

        $response->assertRedirect(route('admin.students.sync'));
        $response->assertSessionHas('success');

        // Verify class created
        $this->assertDatabaseHas('classes', [
            'name' => 'X PPLG 3',
            'grade_level' => 'X',
        ]);

        // Verify user created
        $this->assertDatabaseHas('users', [
            'name' => 'Bambang Pamungkas',
            'email' => '12639999@siswa.kreasi.test',
            'role' => 'siswa',
        ]);

        // Verify student record created
        $this->assertDatabaseHas('students', [
            'nis' => '12639999',
            'nisn' => '0109998887',
            'gender' => 'L',
        ]);

        // Verify student can log in using birth date (10062010) and NIS
        $this->post('/logout');
        $loginResponse = $this->post('/login', [
            'login' => '12639999',
            'password' => '10062010',
        ]);
        $loginResponse->assertRedirect('/siswa/dashboard');
    }

    public function test_artisan_sync_command_runs_successfully(): void
    {
        Http::fake([
            '*/api/v1/classes' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 11, 'name' => 'XI PPLG 3', 'grade' => 11],
                ],
            ], 200),
            '*/api/v1/students*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'uuid' => 'artisan-uuid-1',
                        'nis' => '12538888',
                        'nisn' => '0108887776',
                        'full_name' => 'Citra Kirana',
                        'gender' => 'P',
                        'birth_date' => '2009-12-25',
                        'status' => 'Aktif',
                        'class' => ['name' => 'XI PPLG 3'],
                    ],
                ],
                'meta' => ['total' => 1, 'last_page' => 1],
            ], 200),
        ]);

        $this->artisan('wargarpl:sync-students', ['--force' => true, '--token' => 'wgr_test_token'])
            ->expectsOutputToContain('Koneksi berhasil!')
            ->expectsOutputToContain('Berhasil menyinkronkan')
            ->assertExitCode(0);

        $this->assertDatabaseHas('students', [
            'nis' => '12538888',
            'nisn' => '0108887776',
        ]);
    }

    public function test_admin_can_preview_teachers_endpoint(): void
    {
        Http::fake([
            '*/api/v1/employees*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'uuid' => 'uuid-teacher-1',
                        'nip' => '197607062022211003',
                        'full_name' => 'Abdul Halim',
                        'gender' => 'L',
                        'ptk_type' => 'Guru',
                        'employment_type' => 'PPPK',
                        'status' => 'Aktif',
                        'phone' => '082127069219',
                        'email' => 'abdulhalim@kreasi.test',
                    ],
                ],
                'meta' => ['total' => 1, 'last_page' => 1],
            ], 200),
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.students.sync.teachers.preview'), [
            'token' => 'wgr_test_token',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Abdul Halim');
        $response->assertSee('197607062022211003');
        // Birth date from NIP 1976-07-06 is 06-07-1976, password is 06071976
        $response->assertSee('06071976');
    }

    public function test_admin_can_sync_teachers_and_teacher_can_login_with_nip_and_birthdate_password(): void
    {
        Http::fake([
            '*/api/v1/employees*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'uuid' => 'uuid-teacher-2',
                        'nip' => '197910012009022003',
                        'full_name' => 'Aam Azmiyah',
                        'gender' => 'P',
                        'ptk_type' => 'Guru Produktif',
                        'employment_type' => 'PNS',
                        'status' => 'Aktif',
                        'phone' => '081214512525',
                        'email' => 'aam.azmiyah@smkn1-cirebon.sch.id',
                    ],
                ],
                'meta' => ['total' => 1, 'last_page' => 1],
            ], 200),
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.students.sync.teachers.process'), [
            'token' => 'wgr_test_token',
        ]);

        $response->assertRedirect(route('admin.students.sync', ['tab' => 'teachers']));
        $response->assertSessionHas('success');

        // Assert database records
        $this->assertDatabaseHas('users', [
            'name' => 'Aam Azmiyah',
            'email' => 'aam.azmiyah@smkn1-cirebon.sch.id',
            'role' => 'guru',
        ]);

        $this->assertDatabaseHas('teachers', [
            'teacher_number' => '197910012009022003',
        ]);

        // Teacher logs in using NIP as login identifier and DDMMYYYY (01101979) as password
        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'login' => '197910012009022003',
            'password' => '01101979',
        ]);

        $loginResponse->assertRedirect('/guru/dashboard');
    }

    public function test_artisan_sync_teachers_command_runs_successfully(): void
    {
        Http::fake([
            '*/api/v1/students*' => Http::response([
                'success' => true,
                'data' => [],
                'meta' => ['total' => 0, 'last_page' => 1],
            ], 200),
            '*/api/v1/classes*' => Http::response([
                'success' => true,
                'data' => [],
            ], 200),
            '*/api/v1/employees*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'uuid' => 'artisan-teacher-1',
                        'nip' => '197703072006041009',
                        'full_name' => 'Abdul Nasir',
                        'gender' => 'L',
                        'ptk_type' => 'Guru',
                        'employment_type' => 'PNS',
                        'status' => 'Aktif',
                        'phone' => '082319543377',
                        'email' => 'abdul.nasir@smkn1-cirebon.sch.id',
                    ],
                ],
                'meta' => ['total' => 1, 'last_page' => 1],
            ], 200),
        ]);

        $this->artisan('wargarpl:sync-teachers', ['--force' => true, '--token' => 'wgr_test_token'])
            ->expectsOutputToContain('Koneksi berhasil!')
            ->expectsOutputToContain('Berhasil menyinkronkan')
            ->assertExitCode(0);

        $this->assertDatabaseHas('teachers', [
            'teacher_number' => '197703072006041009',
        ]);
    }
}
