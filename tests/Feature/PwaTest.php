<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaTest extends TestCase
{
    public function test_offline_fallback_page_renders(): void
    {
        $response = $this->get('/offline');
        $response->assertStatus(200);
        $response->assertSee('Anda Sedang Offline');
        $response->assertSee('Koneksi internet Anda terputus');
    }

    public function test_manifest_json_is_valid(): void
    {
        $manifestPath = public_path('manifest.json');
        $this->assertFileExists($manifestPath);

        $content = file_get_contents($manifestPath);
        $data = json_decode($content, true);

        $this->assertIsArray($data);
        $this->assertEquals('KREASI RPL - Platform PjBL & Bimbingan PKL Digital', $data['name']);
        $this->assertEquals('KREASI RPL', $data['short_name']);
        $this->assertEquals('standalone', $data['display']);
        $this->assertEquals('#064A8E', $data['theme_color']);
        $this->assertNotEmpty($data['icons']);
    }

    public function test_service_worker_file_exists(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);

        $content = file_get_contents($swPath);
        $this->assertStringContainsString('CACHE_NAME', $content);
        $this->assertStringContainsString('/offline', $content);
    }
}
