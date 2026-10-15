<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('KREASI RPL');
        $response->assertSee('Selamat Datang Kembali');
    }

    public function test_admin_can_login_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@kreasi.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');

        $admin = User::where('email', 'admin@kreasi.test')->first();
        $this->actingAs($admin);

        $dashResponse = $this->get('/admin/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Dashboard Administrator');
    }

    public function test_guru_can_login_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => 'guru@kreasi.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/guru/dashboard');

        $guru = User::where('email', 'guru@kreasi.test')->first();
        $this->actingAs($guru);

        $dashResponse = $this->get('/guru/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Dashboard Guru Pembimbing');
    }

    public function test_siswa_can_login_using_nis_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => '2026120101', // Ketua kelompok NIS
            'password' => 'password',
        ]);

        $response->assertRedirect('/siswa/dashboard');

        $siswa = User::where('email', 'ketua@kreasi.test')->first();
        $this->actingAs($siswa);

        $dashResponse = $this->get('/siswa/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Proyek PjBL Saya');
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $siswa = User::where('email', 'siswa@kreasi.test')->first();
        $this->actingAs($siswa);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }
}
