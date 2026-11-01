<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('email', 'siswa@kreasi.test')->first();
    }

    public function test_user_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Akun & Profil', false);
        $response->assertSee($this->user->name);
    }

    public function test_user_can_update_profile_and_upload_avatar(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('my_avatar.jpg', 600, 600);

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'Siswa Baru',
            'email' => 'siswa_baru@kreasi.test',
            'phone' => '081234567890',
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertSame('Siswa Baru', $this->user->name);
        $this->assertSame('siswa_baru@kreasi.test', $this->user->email);
        $this->assertNotNull($this->user->avatar);
    }

    public function test_user_can_upload_large_avatar_that_is_accepted_for_auto_compression(): void
    {
        Storage::fake('public');

        // Create a 3.5MB fake image file (exceeds standard 2MB limit, but within 12MB auto-compress threshold)
        $file = UploadedFile::fake()->image('large_photo.png', 1200, 1200)->size(3500);

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertNotNull($this->user->avatar);
    }
}
