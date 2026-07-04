<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class CspHeadersTest extends TestCase
{
    public function test_csp_header_is_present_on_html_pages(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('Content-Security-Policy');
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
    }

    public function test_no_unsafe_inline_in_script_src(): void
    {
        $response = $this->get('/login');
        $csp = $response->headers->get('Content-Security-Policy');

        $scriptSrc = $this->extractDirective($csp, 'script-src');
        $this->assertStringNotContainsString("'unsafe-inline'", $scriptSrc,
            'CSP should not allow unsafe-inline scripts');
    }

    public function test_nonce_is_present_in_script_src(): void
    {
        $response = $this->get('/login');
        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("'nonce-", $csp,
            'CSP should contain a nonce for scripts');
    }

    public function test_inline_script_tags_have_nonce_attribute(): void
    {
        $response = $this->get('/login');
        $html = $response->content();

        preg_match_all('/<script\b(?!\s[^>]*\bsrc\s*=\s*["\'])([^>]*)>/i', $html, $inlineScripts);

        if (!empty($inlineScripts[0])) {
            foreach ($inlineScripts[0] as $tag) {
                $this->assertStringContainsString('nonce="', $tag,
                    'Every inline script tag must have a nonce attribute');
            }
        }
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy');
    }

    private function extractDirective(string $csp, string $directive): string
    {
        preg_match('/' . preg_quote($directive, '/') . '\s+(.*?)(?:;|$)/i', $csp, $matches);
        return $matches[1] ?? '';
    }
}
