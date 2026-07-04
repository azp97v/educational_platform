<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class ErrorMaskingTest extends TestCase
{
    public function test_login_with_wrong_credentials_does_not_leak_details(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'wrong-password-12345',
        ]);

        $content = $response->content();
        $this->assertStringNotContainsString('file:', $content);
        $this->assertStringNotContainsString('Stack trace', $content);
        $this->assertStringNotContainsString('getMessage()', $content);
    }

    public function test_api_returns_json_without_trace(): void
    {
        $response = $this->postJson('/ajax/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'wrong-password-12345',
        ]);

        $response->assertJsonMissing(['file', 'line', 'trace', 'exception']);
    }

    public function test_error_page_does_not_show_sensitive_info(): void
    {
        $response = $this->get('/this-page-does-not-exist-12345');

        $content = $response->content();
        $this->assertStringNotContainsString('Symfony', $content);
        $this->assertStringNotContainsString('Whoops', $content, '', true);
    }
}
