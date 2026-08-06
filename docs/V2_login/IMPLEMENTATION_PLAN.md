# JERO Login V2 — Implementation Plan

**Companion to:** `AUDIT_REPORT.md` (same folder)
**Date:** 2026-08-06
**Goal:** Implement the GOD-level audit findings as a phased rollout. Each phase ends with a verifiable gate. No business-logic changes.

---

## Phase index

| Phase | Theme | Risk | Dev-days | Order |
|---|---|---|---|---|
| 0 | Stop-the-bleed: kill artisan-on-request, rate-limit, open-redirect fix | Low | 0.5 | **Today** |
| 1 | Async & cache: ES queue, captcha cache, `company_type` cache | Medium | 2 | This sprint |
| 2 | Architectural cleanup: extract `LoginAction` + tenant router | Medium | 3 | This sprint |
| 3 | Single-device via Redis; rewire or delete `EnsureOneDeviceLogin` / `LogSuccessfulLogin` | Medium | 1.5 | Next sprint |
| 4 | UI/UX compliance + view cleanup | Low | 1.5 | Next sprint |
| 5 | Dead-code removal: Breeze stubs, `CustomloginController` | Low | 0.5 | Next sprint |
| 6 | Verification, load test, sign-off | — | 1 | Final |

**Total estimated: ~10 dev-days** across 2 sprints.

---

## Phase 0 — Stop-the-bleed (≈ 0.5 day)

### 0.1 Remove `Artisan::call` from `LoginController`
**File:** `app/Http/Controllers/Auth/LoginController.php`
**Lines:** `64-65`, `384-385`, `487-488`
**Change:** Delete all `Artisan::call('route:clear')` and `Artisan::call('cache:clear')` calls (4 sites).
**Verify:** `grep -n "Artisan::call" app/Http/Controllers/Auth/LoginController.php` → 0 hits (or only those that remain by design). After login, no `route:clear` or `cache:clear` in `storage/logs/laravel.log`.
**Risk:** Routes still cached at deploy → ensure `php artisan route:cache` runs in CI/CD.
**Rollback:** Revert commit.

### 0.2 Move `migrate:check` out of request path
**File:** `app/Http/Controllers/Controller.php`
**Lines:** `20-44`, `45-56`
**Change:**
- Remove `$this->checkMigrateStatus();` from constructor.
- Add `$schedule->call(...)->everyFiveMinutes()` in `app/Console/Kernel.php` (or a new `routes/console.php` closure for Laravel 11) to run the migrate-status check off-request.
- Alternatively: rely on CI/CD deploy step to run migrations; remove the check entirely.
**Verify:** `grep -rn "migrate:check" app/` should show only the console scheduler (or none). Request profiler shows 0 artisan invocations.
**Risk:** If migrations are not run elsewhere, deployments could break — confirm CI/CD does it.
**Rollback:** Re-add to constructor.

### 0.3 Add `throttle:5,1` to login POST route
**File:** `routes/web.php`
**Line:** `138`
**Change:**
```php
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
```
**Verify:** `curl -X POST /login` 6 times rapidly with wrong creds → 6th returns HTTP 429.
**Risk:** Mobile users on flaky networks could see 429s. Consider `throttle:10,1` if false-positive risk is high.
**Rollback:** Drop middleware.

### 0.4 Validate `url` param (close open redirect)
**File:** `app/Http/Controllers/Auth/LoginController.php`
**Lines:** `473`, `:182-184`, `:328`
**Change:** Replace `Session::put('open_url', $request->url)` with strict sanitisation. The simple `Str::startsWith($url, '/')` check is **not safe** — it accepts protocol-relative URLs like `//evil.com` (the browser interprets them as external). Use proper URL parsing with host comparison:
```php
// app/Support/OpenRedirect.php
class OpenRedirect
{
    /**
     * Returns a safe internal redirect URL, or null if the input is external / malformed.
     *
     * Blocks:
     *   - Fully-qualified URLs to other hosts   https://evil.com
     *   - Protocol-relative URLs                //evil.com
     *   - Backslash-prefixed protocol-relative  \\evil.com or /\evil.com
     *   - javascript:/data: schemes
     *   - URLs with userinfo                    https://jero.works@evil.com
     */
    public static function sanitize(?string $input): ?string
    {
        if ($input === null || $input === '') {
            return null;
        }

        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        // Reject obvious tricks up-front.
        if (preg_match('#^\\\\#', $input) || str_contains($input, '\\')) {
            return null;
        }
        if (preg_match('#^//[^/]#', $input)) {
            return null; // protocol-relative
        }
        if (preg_match('#^(javascript|data|vbscript):#i', $input)) {
            return null;
        }

        $parts = parse_url($input);
        if ($parts === false) {
            return null;
        }

        // Absolute URL → host must match app host.
        if (isset($parts['scheme']) || isset($parts['host'])) {
            $host = $parts['host'] ?? '';
            if (strcasecmp($host, $appHost) !== 0) {
                return null;
            }
            // Strip userinfo if present (jcro.works@evil.com → reject).
            if (isset($parts['user']) || isset($parts['pass'])) {
                return null;
            }
        }

        // Reconstruct relative path: must begin with a single `/` (not `//`).
        $path = $parts['path'] ?? '';
        if (! str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return null;
        }

        return $path . (isset($parts['query']) ? '?' . $parts['query'] : '')
                    . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
    }
}
```

Then in the login flow:
```php
$url = OpenRedirect::sanitize($request->input('url'));
if ($url !== null) {
    Session::put('open_url', $url);
}
```

**Verify:**
- `curl /login?url=https://evil.com` → after submit, redirect stays on `app.url`.
- `curl /login?url=//evil.com` → blocked (protocol-relative).
- `curl /login?url=/\evil.com` → blocked (backslash trick).
- `curl /login?url=javascript:alert(1)` → blocked.
- `curl /login?url=https://jero.works@evil.com` → blocked (userinfo).
- `curl /login?url=/internal/page` → preserved (relative).
**Risk:** Existing deep-link flows using fully-qualified URLs to the app host (e.g., password reset emails) may break. Audit before shipping. If any exist, normalise them to relative paths at the source.
**Rollback:** Restore original code.

### 0.5 Remove GET `/logout` route
**File:** `routes/web.php`
**Line:** `143`
**Change:** Delete `Route::get('logout', [LoginController::class, 'logout']);`
**Verify:** `curl -I /logout` → 419 (CSRF) or redirect to login, not the logout flow.
**Risk:** External systems linking to `/logout` would break — check before removal.
**Rollback:** Re-add route.

**Phase 0 exit gate:**
- [ ] No artisan calls on login request path
- [ ] Login POST returns 429 after 5 failed attempts
- [ ] Open-redirect attempt stays internal
- [ ] GET `/logout` returns CSRF error
- [ ] All routes still resolve (`php artisan route:list` clean)

---

## Phase 1 — Async & cache (≈ 2 days)

### 1.1 Cache captcha PNG bytes in Redis
**Files:**
- New: `app/Http/Controllers/Auth/CaptchaController.php`
- Modify: `resources/views/auth/login.blade.php` (`captchaImg` src, `refreshCaptcha()`)

**Change:**
1. Extract `generate()` from `HomeController` into `CaptchaController` (no constructor; no `AppBoot`).
2. After generating PNG bytes, store in `Redis::put("captcha:{session_id}", $pngBytes, now()->addMinutes(2))`.
3. Serve from cache on subsequent requests; regenerate if expired or session changed.
4. Send `Cache-Control: private, max-age=120`.
5. Hash the captcha value (use `generateChar()` logic).
**Verify:**
- `redis-cli GET captcha:{sessId}` returns PNG bytes.
- Browser `curl -I /captcha` returns `Cache-Control: private, max-age=120`.
- Two consecutive `/captcha` requests hit Redis only after first.
- Wrong captcha returns invalid; correct captcha passes.
**Risk:** Session regeneration on login would invalidate cached captcha — desired behaviour.
**Rollback:** Direct GD render.

### 1.2 Queue ES `last_login` update — single bulk round-trip, no pre-checks
**File:** `app/Http/Controllers/Auth/LoginController.php`
**Lines:** `209-250` and `358-377`
**Change:**
1. Create `App\Jobs\Auth\RecordLastLoginEsJob` implementing `ShouldQueue`, `$queue = 'low'`, with 3 retries, 30s backoff.
2. Replace inline ES writes with `RecordLastLoginEsJob::dispatch($user->id, $user->company_id, now()->toIso8601String())`.
3. Drop `'refresh' => true`. Default ES refresh (1s) is sufficient.
4. Remove the duplicate ES update in `authenticated()` — only one dispatch per login.
5. **Use the ES Bulk API in a single round-trip with `doc_as_upsert => true` to eliminate `exists()` pre-checks entirely:**
```php
// App\Jobs\Auth\RecordLastLoginEsJob::handle()
$timestamp = now()->toIso8601String();

$params = [
    'body' => [
        ['update' => ['_index' => ELASTIC_USERS_LIST,    '_id' => $this->userId,    'retry_on_conflict' => 5]],
        ['doc' => ['last_login' => $timestamp], 'doc_as_upsert' => true],
        ['update' => ['_index' => ELASTIC_COMPANIES_INDEX, '_id' => $this->companyId, 'retry_on_conflict' => 5]],
        ['doc' => ['last_login' => $timestamp], 'doc_as_upsert' => true],
    ],
];

$this->elasticService->getClient()->bulk($params);
```
This is **1 HTTP round-trip to ES** (not 4 as today) and **0 `exists()` pre-checks** — `doc_as_upsert` means the document is created on first update and modified on subsequent updates, atomic per index, retried on conflict.
**Verify:**
- Horizon dashboard shows `RecordLastLoginEsJob` queued.
- 5s after login, ES doc `last_login` updated on both indices.
- ES bulk request log shows 1 request per login (not 4).
- Login POST P99 latency drops significantly (measure before/after).
- ES `update` with `doc_as_upsert` works on first login (no doc yet) and subsequent logins.
**Risk:** Eventual consistency for `last_login` — UI doesn't depend on it. Bulk failures surface as job retries.
**Rollback:** Restore inline ES writes (one at a time, not bulk).

### 1.3 Cache `company_type` by `company_id`
**Files:**
- Modify: `app/Http/Controllers/Auth/LoginController.php` (all 5+ `DB::table('companies')` sites)
- New: `app/Support/TenantLookup.php` (helper)
- Modify: `app/Observers/CompanyObserver.php` (invalidate cache on update) — check if exists; create if not.

**Change:**
```php
// app/Support/TenantLookup.php
public static function companyType(int $companyId): ?int
{
    return Cache::remember(
        "company:type:{$companyId}",
        now()->addHours(6),
        fn() => DB::table('companies')->where('id', $companyId)->value('company_type')
    );
}

public static function profileCompletion(int $companyId): ?object
{
    return Cache::remember(
        "company:profile:{$companyId}",
        now()->addHours(1),
        fn() => DB::table('companies')
            ->join('clients', 'clients.company_id', '=', 'companies.id')
            ->join('client_location', 'client_location.client_id', '=', 'clients.id')
            ->where('clients.self_company', 1)
            ->where('companies.id', $companyId)
            ->select('companies.description', 'companies.company_name', 'companies.company_size', 'companies.founded', 'client_location.city_id')
            ->first()
    );
}
```
**Verify:**
- `redis-cli GET company:type:1` returns `1`.
- First login after cache TTL: 1 SQL query (MISS). Subsequent: 0 SQL queries (HIT).
- Cache invalidates when company record updates.
**Risk:** Stale `company_type` if cache TTL > observer invalidation lag. Cap at 6h + observer invalidation.
**Rollback:** Restore raw queries.

### 1.4 Drop `Auth::validate()` pre-check — single DB pass via `Auth::attempt()` only
**File:** `app/Http/Controllers/Auth/LoginController.php`
**Lines:** `179-186`
**Change:** The earlier draft proposed `Auth::validate($credentials)` followed by `Auth::attempt($credentials)`. **This is wrong** — both calls fetch the same user record, costing 2 identical SELECT queries. Instead:
1. Call `Auth::attempt($credentials)` **directly**.
2. If it succeeds, inspect `$user->status`. If `!== 'active'`, immediately call `Auth::logout()` and `throw ValidationException`.
3. Move `Session::put('open_url', ...)` into the success branch after the active check.
```php
$credentials = $request->only('email', 'password');
if (! Auth::attempt($credentials, $request->boolean('remember'))) {
    RateLimiter::hit($this->throttleKey());
    throw ValidationException::withMessages(['email' => trans('auth.failed')]);
}

$user = Auth::user();
if ($user->status !== 'active') {
    Auth::logout();
    throw ValidationException::withMessages(['email' => 'Your account is not active. Please contact the admin.']);
}

if (! empty($request->input('url'))) {
    $url = OpenRedirect::sanitize($request->input('url'));
    if ($url !== null) {
        Session::put('open_url', $url);
    }
}
```
**Verify:** Login SQL count drops by 1 vs. the previous plan (1 `users` lookup instead of 2).
**Risk:** None — `Auth::attempt` already does what `Auth::validate` would do, but with the bonus of writing the session.
**Rollback:** Restore the peek query.

**Phase 1 exit gate:**
- [ ] Captcha served from Redis on cache hit
- [ ] ES `last_login` updated within 5s of login (queued, single bulk call)
- [ ] `company_type` cache hit rate > 95% in production
- [ ] Login POST SQL count ≤ 2 (single `users` lookup via `Auth::attempt`)
- [ ] `RecordLastLoginEsJob` does 1 ES round-trip per login (bulk, no `exists()` pre-check)

---

## Phase 2 — Architectural cleanup (≈ 3 days)

### 2.1 Extract CQRS Action classes for login
**Files:**
- New: `app/Actions/Auth/AttemptLoginAction.php`
- New: `app/Actions/Auth/ShowLoginFormAction.php`
- New: `app/Actions/Auth/LogoutAction.php`
- New: `app/Support/TenantRouter.php` (tenant route resolution)
- Modify: `app/Http/Controllers/Auth/LoginController.php` (delegate to actions; thin controller)

**Change:**
```php
// app/Actions/Auth/AttemptLoginAction.php
class AttemptLoginAction
{
    public function __construct(
        private TenantRouter $router,
        private RecordLastLoginEsJob $recordJob,
        private DeviceSessionManager $deviceSessions,
    ) {}

    public function execute(Request $request): RedirectResponse
    {
        // 1. Validate captcha (if not bypass)
        // 2. Auth::attempt($credentials) directly — single users lookup
        //    On success:
        //      - If user->status !== 'active', Auth::logout() + throw
        //      - user->last_login = now(); user->save()  (single MySQL write)
        //      - RecordLastLoginEsJob::dispatch($user->id, $user->company_id)
        //      - DeviceSessionManager::enforce($user)  // Redis only, O(1)
        //      - Return redirect(TenantRouter::resolve($user))
        //    On super-code path: return special redirect
    }
}

// app/Support/TenantRouter.php
class TenantRouter
{
    private const ROUTES = [
        1 => 'service_company/dashboard',
        2 => 'service_company/dashboard',
        3 => 'service_company/dashboard',
    ];

    public static function resolve(User $user): string
    {
        if ($user->is_superadmin) {
            return 'super-admin/dashboard';
        }
        $type = TenantLookup::companyType($user->company_id);
        return self::ROUTES[$type] ?? 'login';
    }

    public static function profileEditRoute(int $userId): string
    {
        return "service_company/profile/{$userId}/edit";
    }
}
```
**Verify:**
- `php artisan route:list --path=login` resolves correctly.
- All four hardcoded tenant-route maps collapsed to one source of truth.
- Controller `LoginController` ≤ 200 lines.
**Risk:** Behavioural parity required — every code path must produce the same redirect as today. Write a test harness that replays login attempts.
**Rollback:** Restore controller-based logic.

### 2.2 Wire `LoginRequest` to the route
**File:** `routes/web.php`
**Line:** `138`
**Change:**
```php
Route::post('login', [LoginController::class, 'login'])
    ->middleware('throttle:5,1');
```
And in `AttemptLoginAction::execute`, call `app(LoginRequest::class)` so the existing rate-limit logic (5 attempts/min per IP+email) is applied.

Alternative — use Laravel's `Route::post(...)->uses(...)` typed parameter.

**Verify:** Failed login attempt increments `RateLimiter` (Redis key visible). 6th attempt blocked.
**Risk:** Double-throttling if `throttle:` middleware + `LoginRequest::ensureIsNotRateLimited()` both fire. Test.
**Rollback:** Keep throttle only on route.

### 2.3 Centralise tenant route resolution
**Files:**
- New: `app/Support/TenantRouter.php`
- Modify: `RedirectIfAuthenticated.php` (use `TenantRouter::resolve`)
- Modify: `LoginController::redirectTo()` (delegate)
- Modify: `CustomloginController` (will be deleted in Phase 5)

**Verify:** All 4 places use the same `TenantRouter` constant map.
**Risk:** None if behaviour is preserved.
**Rollback:** N/A.

**Phase 2 exit gate:**
- [ ] `LoginController` ≤ 200 lines
- [ ] `TenantRouter::resolve()` is the only place that maps `company_type` → dashboard
- [ ] `Auth::attempt` flow SQL count ≤ 2
- [ ] All login redirect paths produce identical URLs as before (snapshot test)

---

## Phase 3 — Single-device via Redis (≈ 1.5 days)

### 3.1 Replace `EnsureOneDeviceLogin` with Redis-only enforcement (read-then-invalidate, O(1))
**Files:**
- Modify: `app/Http/Middleware/EnsureOneDeviceLogin.php`
- Modify: `bootstrap/app.php` (register middleware on `web` group)
- New: `app/Support/DeviceSessionManager.php`
- Modify: `app/Listeners/LogSuccessfulLogin.php` (or delete)

**Change:**
```php
// app/Support/DeviceSessionManager.php
// Single-device enforcement via Redis only. Zero MySQL writes.
class DeviceSessionManager
{
    private const KEY_PREFIX = 'user:session:';
    private const TTL_SECONDS = 86400; // 24 hours

    /**
     * On Login event: read any existing session for this user, invalidate it
     * if it differs from the current session, then store the new session.
     * Cost: 1 Redis GET + 0..1 session destroys + 1 Redis SETEX. O(1) per user.
     */
    public function enforce(User $user, string $currentSessionId): void
    {
        $key = self::KEY_PREFIX . $user->id;
        $oldSessionId = Redis::get($key);

        if ($oldSessionId && $oldSessionId !== $currentSessionId) {
            // Invalidate the previous device's session in Redis storage
            // so its next request fails the auth check.
            session()->getHandler()->destroy($oldSessionId);
        }

        // Always overwrite with the current session, with TTL.
        Redis::set($key, $currentSessionId, 'EX', self::TTL_SECONDS);
    }
}
```

```php
// New middleware — Redis-only, no MySQL writes, no per-request Redis SET
public function handle($request, Closure $next)
{
    if (!Auth::check()) {
        return $next($request);
    }

    $user = Auth::user();
    if ($user->status !== 'active') {
        Auth::logout();
        return redirect('/login')->withErrors(['device_message' => '...']);
    }

    $currentSession = session()->getId();
    $cachedSession = Redis::get('user:session:' . $user->id);

    if ($cachedSession && $cachedSession !== $currentSession) {
        // A different device owns this user's session — block this request.
        Auth::logout();
        return redirect('/login')->withErrors([
            'device_message' => 'Your account is active on another device. Please log in again.',
        ]);
    }

    return $next($request);
}
```

Hook the manager into the Login event:
```php
// AppServiceProvider::boot()
Event::listen(function (\Illuminate\Auth\Events\Login $event) {
    app(DeviceSessionManager::class)->enforce($event->user, session()->getId());
});
```

**Why this beats MySQL:** The Redis `GET` + conditional `destroy()` + `SETEX` is 3 round-trips at the very moment a user logs in (not per page view). Middleware does only 1 `GET` (read-only) per request — no writes. Under 10 K concurrent, MySQL writes go to **zero**, while Redis absorbs the read traffic in single-digit ms.

**Verify:**
- Login from browser A: `redis-cli GET user:session:1` shows session A.
- Login from browser B (same user): session A is destroyed in Redis session storage; `user:session:1` updated to session B; subsequent requests on device A fail.
- MySQL `users.session_id` is **never** written.
- Horizon shows `RecordLastLoginEsJob` and `LoginAuditJob` (if any) — no sync DB writes from login path.
**Risk:** If Redis goes down, single-device enforcement fails open (acceptable trade-off; document in runbook).
**Rollback:** Restore MySQL writes via `users.session_id`; keep middleware as-is.

### 3.2 Wire `LogSuccessfulLogin` (or delete)
**File:** `app/Listeners/LogSuccessfulLogin.php`
**Decision tree:**
- If Phase 3.1 Redis-only enforcement covers the use case: **delete** the listener.
- If still wanted for analytics: rewrite as `ShouldQueue` job that just records `user_id` + `session_id` + `logged_in_at` to a `login_audit` table or to Elasticsearch (`login_events` index). No business logic.

**Verify:** No MySQL `users.session_id` writes during login.
**Risk:** Lose audit trail if not preserved elsewhere.
**Rollback:** Restore.

**Phase 3 exit gate:**
- [ ] Zero MySQL writes per login (verified via DB query log)
- [ ] Single-device invariant holds
- [ ] Middleware registered on `web` group
- [ ] `LogSuccessfulLogin` either deleted or queued

---

## Phase 4 — UI/UX compliance + view cleanup (≈ 1.5 days)

### 4.1 Font sizes within 16px cap
**Files:** `resources/views/auth/login.blade.php`, `resources/views/auth/superadmin-login.blade.php`
**Lines:** `login.blade.php:103, :106`; `superadmin-login.blade.php:49, :52`
**Change:** `fs-2qx` (28px) → `fs-3` (16px); `fs-2` (22px) → `fs-4` (14px). Increase `fw-bolder` if hierarchy is lost.
**Verify:** Visual review at 1366×768 and 1920×1080; font sizes confirmed < 17px.
**Risk:** Layout may shift slightly — adjust padding.
**Rollback:** Restore.

### 4.2 Wrap form in `.section-card`
**Files:** same
**Change:** Wrap form area in `<div class="section-card">` with `<div class="section-card-header">` containing a key icon and title. Follow `UI_UX_STANDARDS §10`.
**Verify:** Visual review; satisfies §10 checklist.
**Risk:** Layout overflow at 1366×768 — verify with browser.
**Rollback:** Restore.

### 4.3 Escape `{!! !!}` outputs
**Files:** same
**Lines:** `login.blade.php:130`; `superadmin-login.blade.php:76`
**Change:** `{!! @$messsage !!}` → `{{ @$messsage }}`.
**Verify:** `<script>` in flash message renders as text, not executes.
**Risk:** None (security fix).
**Rollback:** Restore.

### 4.4 Fix `autocomplete` attributes
**Files:** same
**Lines:** `login.blade.php:141, :156`
**Change:** `autocomplete="off"` → `autocomplete="username"` on email; `autocomplete="current-password"` on password.
**Verify:** Browser password manager offers auto-fill.
**Risk:** None.
**Rollback:** Restore.

### 4.5 Move inline `<script>` to `@push('scripts')`
**File:** `resources/views/auth/login.blade.php`
**Lines:** `297-301`
**Change:** Move to `@push('scripts')` in the parent layout.
**Verify:** Captcha refresh still works.
**Risk:** Layout may not define `@stack('scripts')` — check `layouts/metronic_front_app`.
**Rollback:** Restore.

### 4.6 Extract `<x-auth-login-form>` Blade component (optional)
**Files:** New `resources/views/components/auth-login-form.blade.php`
**Change:** Componentise the two-form pattern (login + forgot-password).
**Verify:** Both views render identically.
**Risk:** Refactor surface; defer if time-pressed.
**Rollback:** Restore.

**Phase 4 exit gate:**
- [ ] All text ≤ 16px
- [ ] `.section-card` wrapper present
- [ ] `{!! !!}` replaced with `{{ }}`
- [ ] Password manager auto-fill works
- [ ] Captcha refresh works
- [ ] Visual review at 1366×768, 1920×1080, 2560×1440

---

## Phase 5 — Dead-code removal (≈ 0.5 day)

### 5.1 Delete `CustomloginController`
**File:** `app/Http/Controllers/Auth/CustomloginController.php`
**Verify first:** `grep -rn "CustomloginController" app/ routes/ config/` → no hits outside the file itself.
**Verify:** Login still works; logout still works.
**Risk:** If accidentally referenced anywhere, route resolution will break — `php artisan route:list` will surface missing class.
**Rollback:** `git revert`.

### 5.2 Delete `routes/auth.php` + 7 Breeze controllers
**Files:**
- `routes/auth.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Auth/PasswordResetLinkController.php`
- `app/Http/Controllers/Auth/NewPasswordController.php`
- `app/Http/Controllers/Auth/ConfirmablePasswordController.php`
- `app/Http/Controllers/Auth/EmailVerificationPromptController.php`
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
- `app/Http/Controllers/Auth/VerifyEmailController.php`
- `app/Http/Controllers/Auth/PasswordController.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Requests/Auth/LoginRequest.php` — keep, used by Phase 2.2

**Verify first:** No references outside dead controllers. Confirm `bootstrap/app.php` does NOT load `auth.php` (it currently doesn't).
**Verify:** Login still works; password reset still works (uses `ForgotPasswordController` + `ResetPasswordController`, not Breeze stubs).
**Risk:** If dev wires `auth.php` in future, will need to restore — document the deletion in commit message.
**Rollback:** `git revert`.

### 5.3 Delete dead methods from `LoginController`
**Lines:** `LoginController.php:50`, `:669-685`, `:586-667` (`credentials()` unused), `:686-756` (`redirectTo()` replaced by `TenantRouter::resolve`).
**Verify:** Compile clean; login flow tested.
**Risk:** Test before delete; some custom routes might still call `credentials()` directly.
**Rollback:** `git revert`.

### 5.4 Replace `EnsureOneDeviceLogin` MySQL writes with Redis (or delete entirely if Phase 3 made it obsolete)
**File:** `app/Http/Middleware/EnsureOneDeviceLogin.php`
**Decision:** If Phase 3 Redis implementation lives elsewhere, delete this middleware; remove from `bootstrap/app.php` aliases.
**Verify:** No references.
**Risk:** Confirm no production route still expects this middleware by alias.
**Rollback:** `git revert`.

### 5.5 Delete dead `VerifyCsrfToken.php`
**File:** `app/Http/Middleware/VerifyCsrfToken.php`
**Verify first:** That `/save-invoices` and `/save-razorpay-invoices` (the `$except` list) are either (a) no longer relied upon, or (b) re-added to `bootstrap/app.php` via `$middleware->validateCsrfTokens(except: [...])`.
**Change:** Delete the file; if the two exemptions are needed, register them in `bootstrap/app.php`.
**Verify:** No 419 errors on the two exempt routes (if they remain in use).
**Risk:** If an external system posts to those endpoints without a CSRF token, requests will start failing.
**Rollback:** `git revert`.

### 5.6 Fix `RedirectIfAuthenticated` raw-string + `exit` bugs
**File:** `app/Http/Middleware/RedirectIfAuthenticated.php`
**Lines:** `32-46`
**Change:** Replace `return 'service_company/profile/'.$user_id.'/edit'; exit;` with `return redirect('service_company/profile/'.$user_id.'/edit');`. Use `TenantRouter::resolve($user)` once Phase 2 lands.
**Verify:** All four `company_type` branches produce a `RedirectResponse`, not a string.
**Risk:** Behavioural parity — these branches redirect to profile-edit; ensure the URL is correct.
**Rollback:** `git revert`.

### 5.7 Add `SecurityHeaders` middleware
**File:** New `app/Http/Middleware/SecurityHeaders.php`; modify `bootstrap/app.php`
**Change:**
- Create middleware that sets: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Strict-Transport-Security: max-age=31536000; includeSubDomains`, `Referrer-Policy: strict-origin-when-cross-origin`, `Content-Security-Policy` (login-specific allow-list: `default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval'` — adjust to allow Metronic assets).
- Register globally: `$middleware->append(SecurityHeaders::class);` in `bootstrap/app.php`.
**Verify:** `curl -I /login` shows all 5 headers.
**Risk:** CSP too strict breaks Metronic inline styles or Bootstrap JS — test in browser before/after.
**Rollback:** Remove middleware registration.

### 5.8 Add login observability (structured log channel)
**File:** New `app/Logging/LoginAttemptFormatter.php`; modify `config/logging.php`
**Change:** Add a `login_attempts` channel that writes structured JSON: `{email, ip, user_agent, success, captcha_ok, throttle_hit, latency_ms, route}`.
Wire from `LoginController::login()` and `AttemptLoginAction::execute()`.
**Verify:** Each login attempt produces exactly one log entry with all fields populated.
**Risk:** PII in logs — mask email if compliance requires.
**Rollback:** Drop the channel.

**Phase 5 exit gate:**
- [ ] `grep -rn "CustomloginController" app/ routes/ config/` → 0 hits
- [ ] `php artisan route:list` clean
- [ ] Login + logout + password-reset + captcha all functional
- [ ] No `Artisan::call` in any auth controller
- [ ] No `DB::table('users')->update('session_id')` in any controller

---

## Phase 6 — Verification & load test (≈ 1 day)

### 6.1 Functional tests
**Coverage:**
- [ ] Login with valid credentials → dashboard
- [ ] Login with invalid credentials → flash error, no leak
- [ ] Login with wrong captcha → flash error, no DB writes
- [ ] Login rate-limited after 5 attempts
- [ ] Login throttled across IP+email combinations
- [ ] Super-admin login flow (super_code bypass)
- [ ] Logout clears session, redirects to login
- [ ] Forgot password sends email
- [ ] Reset password with valid token
- [ ] Open-redirect attempt stays internal
- [ ] Single-device: device B login invalidates device A
- [ ] Session_id column NOT updated on login

### 6.2 Load test
**Tool:** `wrk` or `k6`
**Scenario:** 10,000 concurrent recruiters, 60s sustained.
**Endpoints:**
- `GET /login` (login page render)
- `POST /login` (login POST with valid creds)
- `GET /captcha` (captcha refresh)

**Metrics:**
- P50 latency: login page < 200ms, login POST < 500ms
- P99 latency: login page < 800ms, login POST < 1500ms
- Error rate: < 0.1%
- MySQL CPU: < 60%
- ES CPU: < 50%
- Redis CPU: < 40%

**Before/after comparison:** Repeat same load test before Phase 1 to capture baseline.

### 6.3 Security review
- [ ] Rate limit verified on login POST
- [ ] Open redirect closed
- [ ] CSRF on logout only
- [ ] Captcha brute-force resistance
- [ ] Session fixation resistance (Laravel default + Redis-only)
- [ ] Password hashing verified (bcrypt cost 12)

**Phase 6 exit gate:** All gates green; load test passes; security review signed off.

---

## Cross-cutting concerns

### Observability
- Add a custom Horizon metric for `RecordLastLoginEsJob` queue depth.
- Log login attempts (success/fail) at structured level — NOT via `LogSuccessfulLogin` listener (delete that; use Telescope or a dedicated `LoginAudit` log channel).
- Capture `login_post_latency_ms` as a per-request metric in `config/horizon.php` tags.

### Configuration
- `config/auth.php` — no change needed.
- `config/session.php` — confirm `SESSION_DRIVER=redis` (already set in `.env`).
- `.env` — confirm `REDIS_CLIENT=phpredis` (already set); confirm `CACHE_STORE=redis` (already set).

### Caching strategy summary

| Cache key | TTL | Invalidator | Purpose |
|---|---|---|---|
| `company:type:{id}` | 6h | `CompanyObserver` | company_type lookup |
| `company:profile:{id}` | 1h | `CompanyObserver` | profile-completion check |
| `device:user:{id}` | 24h | Login event | single-device invariant |
| `captcha:{sessId}` | 2min | session expiry | captcha PNG |
| `user_token:{id}` | 24h | login flow | CSRF/auth token (replaces `user_token` table) |

### Risk register

| Risk | Probability | Impact | Mitigation |
|---|---|---|---|
| Rate-limit false positives | Medium | Medium | Use 10/min for first month; monitor 429s |
| Captcha cache invalidation race | Low | Low | Regenerate on session change |
| ES eventual consistency for `last_login` | Low | Low | Document; UI doesn't depend on it |
| Redis cache stampede on `company:type` | Low | Medium | Use `Cache::lock()` for cache-fill |
| TenantRouter behaviour parity | Medium | High | Snapshot tests before refactor |
| Open-redirect fix breaks deep-links | Medium | Medium | Audit existing `url` param usage before shipping |

---

## Sign-off checklist

- [ ] All Phase 0–5 changes merged to `staging`.
- [ ] Phase 6 verification (functional + load + security) passed.
- [ ] Observability dashboards in place.
- [ ] Rollback procedure documented for each phase.
- [ ] Architecture diagram updated (optional — `docs/ARCHITECTURE.md`).
- [ ] `docs/UI_UX_STANDARDS.md` checklist confirmed for login views.
- [ ] `CLAUDE.md` / `.ai/PROJECT_MEMORY.md` updated to reflect post-V2 architecture.

---

## References

- `docs/UI_UX_STANDARDS.md` — UI standards compliance
- `docs/AI_RULES.md` — hard rules (no schema changes, no business-logic changes)
- `docs/ARCHITECTURE.md` — system map
- `docs/AI_BOOTSTRAP.md` — 5-step protocol
- `docs/RECIPES.md` — cookbooks
- `docs/VERIFY.md` — verification gates
- `docs/V2_login/AUDIT_REPORT.md` — source of all findings
- `.ai/PROJECT_MEMORY.md` — living state
