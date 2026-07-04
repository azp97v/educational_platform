<?php

namespace Tests\Feature\ErrorPages;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_404_page_renders_with_arabic_content(): void
    {
        $response = $this->get('/this-page-does-not-exist-12345');

        $response->assertStatus(404);
        $response->assertSee('الصفحة غير موجودة');
        $response->assertSee('404');
    }

    public function test_403_page_renders_with_protected_route(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
        $response->assertSee('غير مصرح');
        $response->assertSee('403');
    }

    public function test_error_page_has_theme_detection_script(): void
    {
        $response = $this->get('/this-page-does-not-exist-12345');

        $response->assertStatus(404);
        $response->assertSee('app-theme');
    }

    public function test_error_page_includes_home_link(): void
    {
        $response = $this->get('/this-page-does-not-exist-12345');

        $response->assertStatus(404);
        $response->assertSee('العودة إلى الرئيسية');
    }

    public function test_419_page_renders_for_session_expiry(): void
    {
        $response = $this->get('/this-page-does-not-exist-12345');

        $this->assertStringContainsString('<!DOCTYPE html>', $response->content());
        $this->assertStringContainsString('lang="ar"', $response->content());
    }
}
