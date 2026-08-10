<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

/**
 * V2_login Phase 3.2 — replaced by DeviceSessionManager::recordLogin() in AttemptLoginAction.
 *
 * The original listener wrote users.session_id to MySQL on every Login event (BUG-01).
 * Session tracking is now Redis-only (user:session:{id}).
 * Class kept as a stub in case it is wired in a config/package not yet discovered.
 */
class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        // intentionally empty — see docblock above
    }
}
