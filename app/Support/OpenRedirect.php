<?php

namespace App\Support;

class OpenRedirect
{
    /**
     * Returns $url only if it stays on the same host, otherwise returns $fallback.
     * Blocks //evil.com, javascript:, backslash-host tricks, and absolute external URLs.
     */
    public static function sanitize(?string $url, string $fallback = '/'): string
    {
        if (empty($url)) {
            return $fallback;
        }

        $parsed = parse_url($url);

        // Allow only paths (no scheme/host = same-origin relative URL).
        if (!empty($parsed['scheme']) || !empty($parsed['host'])) {
            return $fallback;
        }

        // Reject protocol-relative URLs starting with // or \\.
        if (preg_match('#^[/\\\\]{2}#', $url)) {
            return $fallback;
        }

        return $url;
    }
}
