<?php

namespace App\Support;

use Illuminate\Support\Facades\Redis;

/**
 * V2_login Phase 3.1 — Redis-only single-device session enforcement.
 *
 * Key: user:session:{userId} → authoritative session ID (7-day TTL).
 * On login: recordLogin() overwrites the key and destroys any previous session.
 * Per request: enforce() does a single Redis GET; returns false if mismatch.
 * Fail-open: if Redis is unavailable or key is missing, seeds the key and allows.
 *
 * This replaces the previous pattern of writing users.session_id to MySQL on every
 * request (BUG-01 / EnsureOneDeviceLogin).
 */
class DeviceSessionManager
{
    private const KEY_PREFIX = 'user:session:';

    private const TTL_SECONDS = 604800; // 7 days

    /**
     * Check if single-device login enforcement is enabled in config.
     */
    public static function isEnabled(): bool
    {
        return (bool) config('auth.single_device_login.enabled', true);
    }

    /**
     * Check if a specific email address is exempt from single-device kicks.
     */
    public static function isExempt(?string $email = null): bool
    {
        if (! self::isEnabled()) {
            return true;
        }

        if (empty($email)) {
            return false;
        }

        $email = strtolower(trim($email));

        // Exempt all @4bell.com addresses by default, plus any config-defined domains
        $exemptDomains = config('auth.single_device_login.exempt_domains', ['4bell.com']);
        foreach ($exemptDomains as $domain) {
            if (str_ends_with($email, '@'.strtolower(trim($domain)))) {
                return true;
            }
        }

        $exemptEmails = config('auth.single_device_login.exempt_emails', ['vibhanshu@4bell.com']);

        return in_array($email, array_map('strtolower', $exemptEmails), true);
    }

    /**
     * Called on successful login. Destroys the previous device's session (if any),
     * then records the new session as the authoritative one.
     */
    public static function recordLogin(int $userId, string $sessionId, ?string $email = null): void
    {
        if (self::isExempt($email)) {
            return;
        }

        try {
            $old = Redis::get(self::KEY_PREFIX.$userId);
            if ($old && $old !== $sessionId) {
                session()->getHandler()->destroy($old);
                event(new \App\Events\SingleDeviceLogout($userId, $sessionId));
            }
            Redis::setex(self::KEY_PREFIX.$userId, self::TTL_SECONDS, $sessionId);
        } catch (\Throwable) {
            // Redis down — log but don't break login
        }
    }

    /**
     * Per-request check. Returns false when the current session is not the authoritative one
     * (i.e., the user has logged in from another device since this session was created).
     *
     * Fail-open: on Redis outage or cold cache, seeds the key and allows through.
     */
    public static function enforce(int $userId, string $currentSessionId, ?string $email = null): bool
    {
        if (self::isExempt($email)) {
            return true;
        }

        try {
            $stored = Redis::get(self::KEY_PREFIX.$userId);
        } catch (\Throwable) {
            return true; // Redis down — fail-open
        }

        if ($stored === null) {
            Redis::setex(self::KEY_PREFIX.$userId, self::TTL_SECONDS, $currentSessionId);

            return true;
        }

        return $stored === $currentSessionId;
    }

    /**
     * Destroys the stored session and clears the Redis key.
     * Called by admin actions that force-logout a specific user.
     */
    public static function invalidate(int $userId): void
    {
        try {
            $stored = Redis::get(self::KEY_PREFIX.$userId);
            if ($stored) {
                session()->getHandler()->destroy($stored);
            }
            Redis::del(self::KEY_PREFIX.$userId);
        } catch (\Throwable) {
            // swallow — Redis down
        }
    }
}
