<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verifies Cloudflare Turnstile challenge responses against the siteverify API.
 *
 * Design: fail-open on HTTP errors so a Turnstile outage never blocks a real signup.
 * Bypass: in local/testing environments the token is always accepted.
 */
class TurnstileService
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function verify(string $token, string $ip = ''): bool
    {
        if (empty($token)) {
            return false;
        }

        // Always pass in non-production so feature tests don't need a live token.
        if (app()->environment('local', 'testing')) {
            return true;
        }

        try {
            $response = Http::timeout(3)->asForm()->post(self::VERIFY_URL, [
                'secret'   => config('services.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);

            return $response->successful() && ($response->json('success') === true);
        } catch (\Throwable $e) {
            // Fail open: Turnstile outage must never block a legitimate signup.
            Log::warning('turnstile.verify_failed_open', [
                'exception' => $e->getMessage(),
                'ip'        => $ip,
            ]);

            return true;
        }
    }
}
