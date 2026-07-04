<?php

namespace App\Http\Middleware;

use App\Support\TextEncodingNormalizer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeMojibakeEncoding
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (!method_exists($response, 'getContent') || !method_exists($response, 'setContent')) {
            return $response;
        }

        $contentType = strtolower((string) $response->headers->get('Content-Type', ''));
        $isTextual = str_contains($contentType, 'text/html') || str_contains($contentType, 'application/json');

        if (!$isTextual) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || $content === '') {
            return $response;
        }

        if (!preg_match('/[\x{00A7}\x{201E}\x{201C}\x{2026}\x{00E2}\x{00C3}\x{00D8}\x{00D9}\x{00A5}\x{00A2}\x{00A3}\x{00AC}\x{00B1}\x{00B0}\x{00B3}\x{00B2}\x{00B9}\x{00BE}\x{00B7}]/u', $content) && !preg_match('/(?:\x{00C3}.|\x{00E2}.|\x{00D8}.|\x{00D9}.)/u', $content)) {
            return $response;
        }

        $fixed = preg_replace_callback(
            '/[^\s<>{}\[\]"]*[\x{00A7}\x{201E}\x{201C}\x{2026}\x{00E2}\x{00C3}\x{00D8}\x{00D9}\x{00A5}\x{00A2}\x{00A3}\x{00AC}\x{00B1}\x{00B0}\x{00B3}\x{00B2}\x{00B9}\x{00BE}\x{00B7}][^\s<>{}\[\]"]*/u',
            static function (array $matches): string {
                return TextEncodingNormalizer::normalizeString($matches[0]) ?? $matches[0];
            },
            $content
        );

        if (is_string($fixed) && $fixed !== $content) {
            $response->setContent($fixed);
        }

        return $response;
    }
}
