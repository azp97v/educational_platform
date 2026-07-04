<?php

namespace Tests\Feature\Admin;

use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_view_settings(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/settings');

        $response->assertStatus(200);
        $response->assertSee('إعدادات المنصة');
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($user)->get('/admin/settings');

        $response->assertStatus(403);
    }

    public function test_admin_can_update_settings(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'platform_name' => 'منصة اختبار',
            'timezone' => 'Asia/Riyadh',
            'locale' => 'ar',
            'registration_enabled' => '1',
            'session_timeout' => '120',
            'max_login_attempts' => '5',
            'smart_rewind_enabled' => '1',
            'certificates_enabled' => '1',
            'gamification_enabled' => '0',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('0', PlatformSetting::where('key', 'gamification_enabled')->first()->value);
    }

    public function test_settings_update_validates_input(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'platform_name' => '',
            'session_timeout' => '-1',
            'max_login_attempts' => '0',
        ]);

        $response->assertSessionHasErrors(['platform_name', 'session_timeout', 'max_login_attempts']);
    }
}
