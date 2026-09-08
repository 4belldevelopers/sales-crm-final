<?php

namespace App\Actions\Auth;

use App\Helper\GeneralMethod;
use App\Jobs\Auth\RecordLastLoginEsJob;
use App\Models\User;
use App\Support\DeviceSessionManager;
use App\Support\OpenRedirect;
use App\Support\TenantRouter;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * Handles the login POST flow: captcha validation, team-invite first-login,
 * super-code bypass, and normal Auth::attempt().
 *
 * Phase 1: ES last_login update is async via RecordLastLoginEsJob (single bulk call).
 * Phase 3: Add DeviceSessionManager::enforce() after Auth::attempt() succeeds.
 */
class AttemptLoginAction
{
    public function execute(Request $request): RedirectResponse
    {
        $startAt   = microtime(true);
        // Bypass paths (team-invite, super-code) skip captcha — treat as captcha-ok for log field.
        $captchaOk = $this->isBypassPath($request);

        // Captcha — skipped when a bypass token is present (team-invite, super-code).
        if (! $this->isBypassPath($request)) {
            $request->validate(['captcha' => 'required|digits:4']);

            if (! session('captcha_code') || now()->gt(session('captcha_expires'))) {
                $this->logAttempt($request, false, false, $startAt, 'captcha_expired');
                return back()->withErrors(['captcha' => 'Captcha expired']);
            }
            if ($request->captcha != session('captcha_code')) {
                $this->logAttempt($request, false, false, $startAt, 'captcha_invalid');
                return back()->withErrors(['captcha' => 'Invalid captcha']);
            }
            session()->forget(['captcha_code', 'captcha_expires']);
            $captchaOk = true;
        }

        // Team invite first-login: activates the user and records contact info.
        if ($request->has('userId') && $request->has('companyid') && $request->has('toc')) {
            $this->handleTeamInvite($request);
        }

        // Super-admin impersonation via encrypted super_code.
        $supercode = $request->has('super_code') ? GeneralMethod::value_decrypt($request->super_code) : '';
        if ($supercode === 'SUPERCODE' && ! empty($request->super_code)) {
            return $this->handleSuperCode($request, $startAt, $captchaOk);
        }

        Session::forget('token_check');

        // Phase 1.4: single Auth::attempt() call — eliminates the User::where pre-check.
        $credentials = $request->only('email', 'password');
        if (! Auth::attempt($credentials)) {
            $this->logAttempt($request, false, $captchaOk, $startAt, 'bad_credentials');
            return redirect()->back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            $this->logAttempt($request, false, $captchaOk, $startAt, 'account_inactive');
            return redirect()->back()->withErrors(['email' => 'Your account is not active. Please contact the admin.']);
        }

        // Store open-redirect URL now that credentials are confirmed valid.
        if (! empty($request->url)) {
            $safe = OpenRedirect::sanitize($request->url);
            if ($safe !== null) {
                Session::put('open_url', $safe);
            }
        }

        if ($user->id === 1) {
            $this->logAttempt($request, true, $captchaOk, $startAt);
            return redirect(route('superadmin.dashboard.index'));
        }

        $user->last_login = Carbon::now()->toDateTimeString();
        $user->save();

        // Phase 3.1: record authoritative session in Redis; Phase 3.4: seed epoch-gate timestamp.
        DeviceSessionManager::recordLogin($user->id, session()->getId());
        session(['login_at' => now()->timestamp]);

        // Phase 1.2: 1 async bulk ES call replaces 4 synchronous round-trips.
        RecordLastLoginEsJob::dispatch($user->id, $user->company_id, now()->toIso8601String())
            ->onConnection('emails');

        $this->logAttempt($request, true, $captchaOk, $startAt);
        return redirect()->intended(TenantRouter::dashboardUrl($user));
    }

    private function isBypassPath(Request $request): bool
    {
        return $request->has('userId')
            || $request->has('companyid')
            || $request->has('toc')
            || $request->has('super_code');
    }

    /**
     * Team invite first-login: sets the invited user's password, activates them,
     * and records them in client_contact_info.
     * Tenancy is enforced by both company_id and user id in the update WHERE clause.
     */
    private function handleTeamInvite(Request $request): void
    {
        if ((int) $request->userId <= 0 || (int) $request->companyid <= 0 || (int) $request->toc !== 1) {
            return;
        }

        $newPassword     = bcrypt($request->password);
        $coinsDeductions = GeneralMethod::coinsDeductions(
            (int) $request->companyid, 1, $request->userId, $request->companyid, '', ''
        );

        DB::table('users')
            ->where('users.company_id', $request->companyid)
            ->where('users.id', $request->userId)
            ->limit(1)
            ->update([
                'email_verification_code' => '',
                'password'                => $newPassword,
                'remember_token'          => $request->userId,
                'status'                  => 'active',
                'super_password'          => $request->password,
                'super_admin_login_code'  => $request->companyid . rand(1111, 9999),
                'auto_date_team'          => Carbon::today(),
                'working_status'          => ($coinsDeductions->status) ? 0 : 1,
            ]);

        $teamPro = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select('users.root_user', 'users.company_id', 'users.email', 'users.name',
                     'roles.display_name', 'users.working_status', 'users_profile.designation')
            ->where('users.id', '=', $request->userId)
            ->where('users.root_user', '=', 0)
            ->first();

        if ($teamPro) {
            $cntId = DB::table('clients')
                ->where('company_id', $teamPro->company_id)
                ->where('self_company', 1)
                ->first();

            if ($cntId && ! DB::table('client_contact_info')->where('contact_person_email', $teamPro->email)->exists()) {
                DB::table('client_contact_info')->insert([
                    'client_id'                  => $cntId->id,
                    'contact_person_name'        => $teamPro->name,
                    'contact_person_email'       => $teamPro->email,
                    'contact_person_designation' => $teamPro->display_name,
                    'user_id'                    => $request->userId,
                ]);
            }
        }

        GeneralMethod::userIndexingupload($request->userId);
    }

    /** Super-admin impersonation: authenticates using super_admin_login_code as the password. */
    private function handleSuperCode(Request $request, float $startAt, bool $captchaOk): RedirectResponse
    {
        $users = DB::table('users')
            ->where('email', '=', $request->email)
            ->where('super_admin_login_code', '=', $request->password)
            ->first();

        if (! $users) {
            $this->logAttempt($request, false, $captchaOk, $startAt, 'supercode_bad_credentials');
            return redirect()->back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        Session::put('token_check', 'SUPERCODE');

        if (! Auth::loginUsingId($users->id)) {
            $this->logAttempt($request, false, $captchaOk, $startAt, 'supercode_login_failed');
            return redirect()->back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();
        if ($user->status !== 'active') {
            Auth::logout();
            $this->logAttempt($request, false, $captchaOk, $startAt, 'supercode_account_inactive');
            return redirect()->back()->withErrors(['email' => 'Your account is not active. Please contact the admin.']);
        }

        DeviceSessionManager::recordLogin($user->id, session()->getId());
        session(['login_at' => now()->timestamp]);

        $this->logAttempt($request, true, $captchaOk, $startAt);
        return redirect()->intended('service_company/dashboard');
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email, 2);
        return substr($parts[0], 0, 1) . '***@' . ($parts[1] ?? '');
    }

    private function logAttempt(Request $request, bool $success, bool $captchaOk, float $startAt, ?string $failReason = null): void
    {
        try {
            Log::channel('login_attempts')->info('login_attempt', [
                'email'       => $this->maskEmail((string) $request->input('email', '')),
                'ip'          => $request->ip(),
                'user_agent'  => substr((string) $request->userAgent(), 0, 200),
                'success'     => $success,
                'captcha_ok'  => $captchaOk,
                'latency_ms'  => (int) round((microtime(true) - $startAt) * 1000),
                'route'       => $request->route()?->getName() ?? $request->path(),
                'fail_reason' => $failReason,
            ]);
        } catch (\Throwable) {}
    }
}
