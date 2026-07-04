<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login_for_admin_route(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_for_student_route(): void
    {
        $response = $this->get('/student/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_for_teacher_route(): void
    {
        $response = $this->get('/teacher/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_student_cannot_access_admin_route(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_access_admin_route(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_student_cannot_access_teacher_route(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/teacher/dashboard');

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_access_student_route(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get('/student/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_student_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/student/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_created_student_can_log_in_without_otp(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'طالب جديد',
            'email' => 'new.student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ])->assertRedirect(route('admin.index'));

        $student = User::where('email', 'new.student@example.com')->first();
        $this->assertNotNull($student);
        $this->assertNotNull($student->email_verified_at);

        $this->post('/login', [
            'email' => 'new.student@example.com',
            'password' => 'password123',
        ])->assertRedirect('/student/dashboard');
    }

    public function test_admin_can_link_student_to_teacher_on_create(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'طالب مرتبط',
            'email' => 'linked.student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'teacher_id' => $teacher->id,
        ]);

        $student = User::where('email', 'linked.student@example.com')->first();
        $this->assertEquals($teacher->id, $student->teacher_id);
    }

    public function test_last_admin_account_cannot_be_deleted(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        User::where('role', 'admin')->where('id', '!=', $admin->id)->delete();

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
