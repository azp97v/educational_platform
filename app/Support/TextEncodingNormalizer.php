<?php

namespace App\Support;

final class TextEncodingNormalizer
{
    private const MARKER_REGEX = '/[\x{00A7}\x{201E}\x{201C}\x{2026}\x{00E2}\x{00C3}\x{00D8}\x{00D9}\x{00A5}\x{00A2}\x{00A3}\x{00AC}\x{00B1}\x{00B0}\x{00B3}\x{00B2}\x{00B9}\x{00BE}\x{00B7}]/u';
    private const BAD_SEQUENCE_REGEX = '/(?:\x{00C3}.|\x{00E2}.|\x{00D8}.|\x{00D9}.|\x{0637}[\x{00A7}\x{201E}\x{201C}\x{2026}\x{00A5}\x{00A2}\x{00A3}\x{00AC}\x{00B1}\x{00B0}\x{00B3}\x{00B2}\x{00B9}\x{00BE}\x{00B7}]|\x{0638}[\x{00A7}\x{201E}\x{201C}\x{2026}\x{00A5}\x{00A2}\x{00A3}\x{00AC}\x{00B1}\x{00B0}\x{00B3}\x{00B2}\x{00B9}\x{00BE}\x{00B7}])/u';

    public static function normalizeString(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        if (!preg_match(self::MARKER_REGEX, $text) && !preg_match(self::BAD_SEQUENCE_REGEX, $text)) {
            return $text;
        }

        $candidates = [$text];
        self::pushIterativeCandidates($candidates, $text, 'Windows-1256', 4);
        self::pushIterativeCandidates($candidates, $text, 'Windows-1252', 2);
        self::pushCandidate($candidates, self::convert($text, 'ISO-8859-1'));

        $snapshot = $candidates;
        foreach ($snapshot as $candidate) {
            self::pushIterativeCandidates($candidates, $candidate, 'Windows-1256', 2);
            self::pushIterativeCandidates($candidates, $candidate, 'Windows-1252', 1);
        }

        $best = $text;
        $baseScore = self::score($text);
        $bestScore = $baseScore;

        foreach ($candidates as $candidate) {
            $score = self::score($candidate);
            if ($score > $bestScore) {
                $best = $candidate;
                $bestScore = $score;
            }
        }

        if ($best === $text) {
            return $text;
        }

        $baseMarkers = self::countMatches(self::MARKER_REGEX, $text);
        $bestMarkers = self::countMatches(self::MARKER_REGEX, $best);
        $baseBadSequences = self::countMatches(self::BAD_SEQUENCE_REGEX, $text);
        $bestBadSequences = self::countMatches(self::BAD_SEQUENCE_REGEX, $best);

        if ($bestScore >= ($baseScore + 4) && ($bestMarkers < $baseMarkers || $bestBadSequences < $baseBadSequences)) {
            return self::restoreQuotedSegments($text, $best);
        }

        return $text;
    }

    public static function normalizeMixed($value)
    {
        if (is_string($value)) {
            return self::normalizeString($value);
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = self::normalizeMixed($item);
            }
        }

        return $value;
    }

    private static function convert(string $text, string $targetEncoding): ?string
    {
        $converted = @iconv('UTF-8', $targetEncoding.'//IGNORE', $text);

        if (!is_string($converted) || $converted === '') {
            return null;
        }

        if (strlen($converted) < max(3, (int) floor(strlen($text) * 0.15))) {
            return null;
        }

        return $converted;
    }

    private static function pushCandidate(array &$candidates, ?string $candidate): void
    {
        if (!is_string($candidate) || $candidate === '') {
            return;
        }

        if (!in_array($candidate, $candidates, true)) {
            $candidates[] = $candidate;
        }
    }

    private static function pushIterativeCandidates(array &$candidates, string $seed, string $targetEncoding, int $maxSteps): void
    {
        $current = $seed;
        for ($i = 0; $i < $maxSteps; $i++) {
            $next = self::convert($current, $targetEncoding);
            if (!is_string($next) || $next === '' || $next === $current) {
                break;
            }
            self::pushCandidate($candidates, $next);
            $current = $next;
        }
    }

    private static function restoreQuotedSegments(string $original, string $normalized): string
    {
        if (!preg_match('/"\s*"/u', $normalized)) {
            return $normalized;
        }

        preg_match_all('/"([^"]+)"/u', $original, $matches);
        $quoted = array_values(array_filter(
            $matches[1] ?? [],
            static fn (string $part): bool => trim($part) !== '' && !preg_match(self::MARKER_REGEX, $part)
        ));

        if (empty($quoted)) {
            return $normalized;
        }

        foreach ($quoted as $part) {
            if (!preg_match('/"\s*"/u', $normalized)) {
                break;
            }
            $normalized = preg_replace('/"\s*"/u', '"'.$part.'"', $normalized, 1) ?? $normalized;
        }

        return $normalized;
    }

    private static function score(string $text): int
    {
        if (!mb_check_encoding($text, 'UTF-8')) {
            return -100000;
        }

        $arabic = self::countMatches('/[\x{0600}-\x{06FF}]/u', $text);
        $markers = self::countMatches(self::MARKER_REGEX, $text);
        $badSequences = self::countMatches(self::BAD_SEQUENCE_REGEX, $text);
        $replacement = substr_count($text, "\u{FFFD}");
        $lamAlef = substr_count($text, 'ال');

        return ($arabic * 4) + ($lamAlef * 2) - ($markers * 22) - ($badSequences * 16) - ($replacement * 30);
    }

    private static function countMatches(string $pattern, string $text): int
    {
        return preg_match_all($pattern, $text) ?: 0;
    }
}
