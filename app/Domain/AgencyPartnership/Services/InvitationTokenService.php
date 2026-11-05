<?php

namespace App\Domain\AgencyPartnership\Services;

/**
 * InvitationTokenService
 *
 * Generates and validates cryptographically secure 256-bit agency invitation tokens.
 * Complies with OWASP token security: 64-char hex raw token, SHA-256 hashed at rest.
 */
class InvitationTokenService
{
    /**
     * Generate a new 256-bit cryptographically secure token.
     *
     * @return array{raw_token: string, token_hash: string, url: string}
     */
    public function generate(): array
    {
        $rawToken = bin2hex(random_bytes(32)); // 64 hex characters (256 bits of entropy)
        $tokenHash = hash('sha256', $rawToken);
        $url = url('/partner-invite/'.$rawToken);

        return [
            'raw_token' => $rawToken,
            'token_hash' => $tokenHash,
            'url' => $url,
        ];
    }

    /**
     * Compute the SHA-256 hash of a raw token for database lookup.
     */
    public function hash(string $rawToken): string
    {
        return hash('sha256', trim($rawToken));
    }

    /**
     * Verify raw token against stored token hash using constant-time comparison.
     */
    public function verify(string $rawToken, string $storedHash): bool
    {
        return hash_equals($storedHash, $this->hash($rawToken));
    }
}
