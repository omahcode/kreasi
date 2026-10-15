<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\PklGroup;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PklGroupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_manage_companies(): void
    {
        $admin = User::where('email', 'admin@kreasi.test')->first();
        $this->actingAs($admin);

        // View companies index
        $response = $this->get('/admin/companies');
        $response->assertStatus(200);
        $response->assertSee('PT. Solusi Teknologi Nusantara');

        // Add new company
        $response = $this->post('/admin/companies', [
            'name' => 'PT. Toko Media Nusantara',
            'sector' => 'E-Commerce Platform',
            'city' => 'Jakarta Barat',
            'contact_person' => 'Agus Wijaya',
        ]);

        $response->assertRedirect('/admin/companies');
        $this->assertDatabaseHas('companies', ['name' => 'PT. Toko Media Nusantara']);
    }

    public function test_admin_can_plot_pkl_group(): void
    {
        $admin = User::where('email', 'admin@kreasi.test')->first();
        $this->actingAs($admin);

        $company = Company::first();
        $guru = User::where('role', 'guru')->first();
        $students = Student::take(2)->get();

        $response = $this->post('/admin/pkl-groups', [
            'name' => 'Kelompok PKL Mobile Flutter',
            'company_id' => $company->id,
            'mentor_teacher_id' => $guru->id,
            'student_ids' => $students->pluck('id')->toArray(),
            'leader_id' => $students->first()->id,
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
        ]);

        $response->assertRedirect('/admin/pkl-groups');
        $this->assertDatabaseHas('pkl_groups', ['name' => 'Kelompok PKL Mobile Flutter']);
    }

    public function test_guru_can_create_pkl_group_for_mentees(): void
    {
        $guru = User::where('email', 'guru@kreasi.test')->first();
        $this->actingAs($guru);

        $company = Company::first();
        $students = Student::take(2)->get();

        $response = $this->post('/guru/pkl-groups', [
            'name' => 'Kelompok Bimbingan Cloud Dev',
            'company_id' => $company->id,
            'student_ids' => $students->pluck('id')->toArray(),
            'leader_id' => $students->first()->id,
        ]);

        $response->assertRedirect('/guru/pkl-groups');
        $this->assertDatabaseHas('pkl_groups', [
            'name' => 'Kelompok Bimbingan Cloud Dev',
            'mentor_teacher_id' => $guru->id,
        ]);
    }

    public function test_student_dashboard_displays_pkl_group_and_mentor_teammates(): void
    {
        $studentUser = User::where('email', 'ketua@kreasi.test')->first();
        $this->actingAs($studentUser);

        $response = $this->get('/siswa/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Penempatan', false);
        $response->assertSee('Rekan Kelompok PKL', false);
        $response->assertSee('PT. Solusi Teknologi Nusantara', false);
        $response->assertSee('Guru Pembimbing Sekolah', false);
        $response->assertSee('Daftar Teman Satu Kelompok', false);
    }

    public function test_student_pkl_page_uses_plotted_group_and_locks_company(): void
    {
        $studentUser = User::where('email', 'ketua@kreasi.test')->first();
        $this->actingAs($studentUser);

        $response = $this->get('/siswa/pkl');
        $response->assertStatus(200);
        $response->assertSee('Penempatan Resmi');
        $response->assertSee('PT. Solusi Teknologi Nusantara');

        // Submit title using plotted group
        $response = $this->post('/siswa/pkl/title', [
            'title' => 'Pengembangan Modul Billing Otomatis pada PT. Solusi Teknologi Nusantara',
        ]);

        $response->assertRedirect('/siswa/pkl');
        $this->assertDatabaseHas('pkl_reports', [
            'title' => 'Pengembangan Modul Billing Otomatis pada PT. Solusi Teknologi Nusantara',
            'company_name' => 'PT. Solusi Teknologi Nusantara',
        ]);
    }
}
