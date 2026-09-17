<?php

namespace App\Support;

use Illuminate\Support\Facades\Redis;

/**
 * Single Active Device Session Manager.
 *
 * Key: user:session:{userId} -> authoritative session ID (7-day TTL).
 * On login: recordLogin() overwrites the Redis key and destroys previous session data.
 * Per request: enforce() checks Redis first, with fall-back comparison against
 * users.active_session stored in DB/User model for cold-cache and Redis-outage safety.
 *
 * Intent: Ensures a user account can be active on ONLY one device at a time without
 * performing N+1 database queries on every HTTP request.
 */
class DeviceSessionManager
{
    private const KEY_PREFIX = 'user:session:';

    private const TTL_SECONDS = 604800; // 7 days

    /**
<<<<<<< HEAD
     * Called on successful login. Destroys the previous device's session in storage,
     * then records the new session ID as the authoritative active session.
     *
     * @param  int  $userId  Target user ID
     * @param  string  $sessionId  New active session ID
=======
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

        $exemptEmails = config('auth.single_device_login.exempt_emails', []);
        if (empty($exemptEmails)) {
            return false;
        }

        return in_array(strtolower(trim($email)), array_map('strtolower', $exemptEmails), true);
    }

    /**
     * Called on successful login. Destroys the previous device's session (if any),
     * then records the new session as the authoritative one.
>>>>>>> fe050944a32d421c01f57a1306b11d4af33520b1
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
            // Redis down — log/swallow but allow login flow to complete
        }
    }

    /**
     * Per-request single-session enforcement check.
     * Compares incoming request's session ID with authoritative Redis key or DB fallback.
     *
     * @param  int  $userId  Target user ID
     * @param  string  $currentSessionId  Current incoming session ID
     * @param  string|null  $dbActiveSession  Active session stored on User model (users.active_session)
     * @return bool True if session is valid and active; false if invalidated by newer login
     */
<<<<<<< HEAD
    public static function enforce(int $userId, string $currentSessionId, ?string $dbActiveSession = null): bool
=======
    public static function enforce(int $userId, string $currentSessionId, ?string $email = null): bool
>>>>>>> fe050944a32d421c01f57a1306b11d4af33520b1
    {
        if (self::isExempt($email)) {
            return true;
        }

        try {
            $stored = Redis::get(self::KEY_PREFIX.$userId);
        } catch (\Throwable) {
            $stored = null; // Redis outage fallback
        }

        if ($stored === null) {
<<<<<<< HEAD
            // Cold cache / Redis fallback: check against user's active_session in DB/memory
            if ($dbActiveSession !== null && $dbActiveSession !== '') {
                if ($dbActiveSession !== $currentSessionId) {
                    return false;
                }
            }
            try {
                Redis::setex(self::KEY_PREFIX.$userId, self::TTL_SECONDS, $currentSessionId);
            } catch (\Throwable) {
                // Ignore Redis write errors during outage fallback
            }
=======
            Redis::setex(self::KEY_PREFIX.$userId, self::TTL_SECONDS, $currentSessionId);
>>>>>>> fe050944a32d421c01f57a1306b11d4af33520b1

            return true;
        }

        return $stored === $currentSessionId;
    }

    /**
     * Destroys the active session and clears the Redis session key for a user.
     * Called by logout actions or admin force-logout triggers.
     *
     * @param  int  $userId  Target user ID
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
            // Swallow — Redis outage
        }
    }
}
