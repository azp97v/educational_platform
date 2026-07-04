<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_429_after_too_many_attempts(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', [
                'email' => 'test@test.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }

    public function test_register_returns_429_after_fast_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/register', [
                'name' => 'Test User ' . $i,
                'email' => 'test' . $i . '@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'student',
            ]);
        }

        $response = $this->post('/register', [
            'name' => 'Extra User',
            'email' => 'extra@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $response->assertStatus(429);
    }

    public function test_forgot_password_throttled(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/forgot-password', ['email' => 'test@test.com']);
        }

        $response = $this->post('/forgot-password', ['email' => 'test@test.com']);

        $response->assertStatus(429);
    }

    public function test_search_throttled(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        for ($i = 0; $i < 20; $i++) {
            $this->actingAs($user)->get('/student/messaging/search?q=test');
        }

        $response = $this->actingAs($user)->get('/student/messaging/search?q=test');
        $response->assertStatus(429);
    }
}
