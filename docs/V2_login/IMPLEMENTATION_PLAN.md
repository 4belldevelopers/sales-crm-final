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

### 3.3 Foundations — Action class, Event, Queue job, Model, Migration
**Audit reference:** [`docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md`](BLOCKING_SCENARIOS_AUDIT.md) §5.1, §5.1.1, §5.3

This sub-phase and its successors (3.4–3.7) implement the refinements locked during audit review (epoch storage = Redis, Action location = `app/Actions/Auth/`, company-block channel = `company.{companyId}.dashboard`). They are the **only** state-flip entry points across the codebase once landed.

**Files:**
- New: `app/Actions/Auth/SyncUserStatusAction.php`
- New: `app/Actions/Auth/BlockCompanyAction.php` (static helper for superadmin bulk block)
- New: `app/Events/UserAccountSuspended.php` (Reverb event, `ShouldBroadcast`, mirrors `app/Events/OperationalCoinUpdated.php:20`)
- New: `app/Jobs/SyncUserStatusEsJob.php` (mirrors `app/Jobs/ElasticCandidateIndexJob.php:16` — `ShouldQueue` + `$tries = 5` + `$backoff = [10,30,60,120,300]`; queue name `es-index`)
- New: `database/migrations/2026_08_06_000001_create_block_log_table.php`
- New: `app/Models/BlockLog.php`

**Behaviour (descriptive, not code):**

- `SyncUserStatusAction::execute(targetUserId, status, workingStatus)` runs five steps in order:
  1. `DB::table('users')->where('id', $targetUserId)->where('company_id', $actor->company_id)->update([...])`. If the affected-row count is not exactly 1, `abort(403)` — this is the cross-tenant guard and closes the unguarded bulk `teamDelete` write path.
  2. `SyncUserStatusEsJob::dispatch($targetUserId)` — async ES sync to `ELASTIC_USERS_LIST` via the existing `app/Helper/GeneralMethod.php:9235` `userIndexingupload()` helper.
  3. `Cache::put("user:epoch:{$targetUserId}", now()->timestamp, 86400 * 7)` — single O(1) Redis write that locks the user's open session.
  4. `BlockLog::create([...])` — single audit-log row.
  5. `broadcast(new UserAccountSuspended(...))->toOthers()` — Reverb event on `PrivateChannel('App.Models.User.{id}')`.

- `BlockCompanyAction::execute(companyId, newStatus, actorUserId, blockType)` runs six steps:
  1. Bulk `DB::table('users')->where('company_id', $companyId)->where('working_status', 1)->update(['status' => $newStatus, ...])`.
  2. Single `ELASTIC_USERS_LIST` `updateByQuery` — **without** `refresh:true` (rule from §3.7 below).
  3. Single `ELASTIC_COMPANIES_INDEX` `updateByQuery` with `refresh=wait_for` (only place it's permitted).
  4. `Cache::put("company:epoch:{$companyId}", now()->timestamp, 86400 * 7)` — single O(1) Redis write that locks all sessions for the company in one shot (5,000 users → 1 Redis op, not 5,000).
  5. `broadcast(new UserAccountSuspended(...))->toOthers()` on `PrivateChannel('company.{companyId}.dashboard')`.
  6. `BlockLog::create([...])`.

**Order of operations:**
1. `database/migrations/..._create_block_log_table.php` + `app/Models/BlockLog.php` first (the Actions write to this table).
2. `app/Events/UserAccountSuspended.php` next (Actions broadcast this).
3. `app/Jobs/SyncUserStatusEsJob.php` next (Actions dispatch this).
4. `app/Actions/Auth/SyncUserStatusAction.php` last (uses all of the above).
5. `app/Actions/Auth/BlockCompanyAction.php` last (uses all of the above).

**Verify:** Audit §6 verification matrix rows 1–9 (epoch gates, Reverb dispatch, cross-tenant 403, O(1) Redis invariant).
**Risk:** Low — all new files, no existing behaviour changed yet.
**Rollback:** Delete the 5 new files; no existing handlers touched in this sub-phase.

### 3.4 Epoch gate in `EnsureOneDeviceLogin`
**Audit reference:** [`docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md`](BLOCKING_SCENARIOS_AUDIT.md) §5.2

Adds the epoch comparison after the existing line 41 `$user->status !== 'active'` check in `app/Http/Middleware/EnsureOneDeviceLogin.php`. The middleware becomes a **two-stage gate**: status-check first (already exists), epoch-check second (new). Either rejection logs the user out and redirects to `/login`.

**Files:**
- Modify: `app/Http/Middleware/EnsureOneDeviceLogin.php` (insert ~7 lines after L41)
- Modify: `app/Http/Controllers/Auth/LoginController.php` (set `session(['login_timestamp' => now()->timestamp])` on successful auth — prerequisite for the epoch comparison)

**Order of operations:**
1. Modify `LoginController.php` first to set `login_timestamp` (the Action's epoch comparison has nothing to compare against otherwise).
2. Modify `EnsureOneDeviceLogin.php` next.

**Verify:** Audit §6 verification matrix row 1 (epoch-gate for `company:epoch`) and row 2 (epoch-gate for `user:epoch`).
**Risk:** Low — additive change, existing status check still runs first.
**Rollback:** Revert both edits.

### 3.5 Wire the Actions into the 3 user-block controllers + bulk teamDelete
**Audit reference:** [`docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md`](BLOCKING_SCENARIOS_AUDIT.md) §5.1 wiring list

Replaces the inline `DB::table('users')->update([...])` blocks in each of the four methods with a single `SyncUserStatusAction::execute(...)` call. Closes the **ProductCompany/StaffingCompany ES-drift footgun** and the **`teamDelete` no-`company_id`-scope footgun** simultaneously, because the Action's cross-tenant guard rejects non-matching IDs with 403.

**Files:**
- Modify: `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php` (`statusUpdate` ~L2074-2099; `teamDelete` ~L111-142)
- Modify: `app/Http/Controllers/ProductCompany/ProductCompanyTeamController.php` (`statusUpdate` ~L1854-1882)
- Modify: `app/Http/Controllers/StaffingCompany/StaffingCompanyTeamController.php` (`statusUpdate` ~L1752-1780)

**Order of operations:**
1. ServiceCo `statusUpdate` first (most complete variant — proves the pattern).
2. ProductCo + StaffingCo `statusUpdate` next (simpler: just `status` flip, no `working_status`).
3. ServiceCo `teamDelete` last — looping call exercises the cross-tenant 403 path on negative tests.

**Verify:** Audit §6 verification matrix rows 8, 9, 10 (cross-tenant guard, BlockLog row, ES `ELASTIC_USERS_LIST` reflects new `status` within 1 s, no `refresh:true`).
**Risk:** Medium — root-user block rejection (audit §1 footgun #1) means tenant admins lose the ability to "punish" themselves. Product sign-off recommended before launch.
**Rollback:** Keep the dispatch site change, comment the dispatch line — original `DB::table()->update()` block is preserved if rolled back during the same PR.

### 3.6 Wire `BlockCompanyAction` into `SuperAdminCompanyController`
**Audit reference:** [`docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md`](BLOCKING_SCENARIOS_AUDIT.md) §2 (defects 1–7), §5.1.1

Replaces three handlers' ES `updateByQuery` + `refresh:true` cascade with a single `BlockCompanyAction::execute(...)` call. The superadmin UI does not freeze on a 5,000-user company because the per-user iteration loop is gone.

**Files:**
- Modify: `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` (`checkActive` ~L11261-11344; `checkStatusOperational` ~L11346-11361; `checkStatusSales` ~L11363-11380)

**Order of operations:**
1. `checkActive` first (the real "block the company" endpoint — full integration test).
2. `checkStatusOperational` and `checkStatusSales` next (simpler: no ES sync at all, just company_coins row + epoch).

**Verify:** Audit §6 verification matrix rows 1–7 (epoch gate fires for every blocked user; superadmin UI completes in <500 ms even with 5,000 users; only one `Cache::put` and one broadcast per call).
**Risk:** Low — Action is idempotent; replay-safe.
**Rollback:** Restore the inline `DB::table()` + `updateByQuery` block per handler.

### 3.7 `refresh:true` lint guard (`scripts/ai-guards.sh`)
**Audit reference:** [`docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md`](BLOCKING_SCENARIOS_AUDIT.md) §5.5, §8

Adds a GUARD 3 to `scripts/ai-guards.sh` that fails the build if any new `'refresh' => true` ES call is introduced outside the whitelisted reference comment in `app/Jobs/ProcessOperationalCoinDeductionsJob.php`. The current 20-site list (Phase 3.6's work removes them) must be empty after that sub-phase.

**Files:**
- Modify: `scripts/ai-guards.sh`

**Change (added after existing GUARD 2 in the `for f in "${files[@]}"` loop):**

```bash
# GUARD 3: ban new "'refresh' => true" ES calls.
# 1-second natural refresh is sufficient everywhere except the single doc
# company write in BlockCompanyAction, which uses refresh=wait_for.
# Whitelisted: ProcessOperationalCoinDeductionsJob deliberately omits the flag.
if grep -nE "'refresh'\s*=>\s*true" "$f" \
     | grep -v "ProcessOperationalCoinDeductionsJob" \
     | grep -vE '^[[:space:]]*[0-9]+:[[:space:]]*(//|\*|#)' >/dev/null; then
  echo "[$f] active 'refresh' => true ES call (causes shard refresh stall):"
  grep -nE "'refresh'\s*=>\s*true" "$f" \
    | grep -v "ProcessOperationalCoinDeductionsJob" \
    | grep -vE '^[[:space:]]*[0-9]+:[[:space:]]*(//|\*|#)' | sed 's/^/    /'
  note "drop refresh:true — use natural 1s refresh, or refresh=wait_for only for single docs (BLOCKING_SCENARIOS_AUDIT §5.5)"
fi
```

**Order of operations:**
1. Apply Phase 3.6 first (removes all 20 existing `refresh:true` sites).
2. Add the lint guard **after** verifying `bash scripts/ai-guards.sh --all` exits 0 with only the whitelisted file matches.
3. Add the guard to the staged/--changed modes of the script (existing loop already iterates changed files).

**Verify:** `bash scripts/ai-guards.sh --all` exits 0; intentional re-add of `refresh:true` anywhere except `BlockCompanyAction` (with `wait_for`) fails pre-commit.
**Risk:** Low — script is the only file changed.
**Rollback:** Revert the script edit.

### 3.8 `superadmin-bulk-ops` rate limiter
**Audit reference:** [`docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md`](BLOCKING_SCENARIOS_AUDIT.md) §5.6

Caps the superadmin `checkActive` / `checkStatusOperational` / `checkStatusSales` endpoints at 10/min per superadmin. Even after `refresh:true` removal, the bulk operation itself is still heavy on a 5,000-user company — a rate limit prevents the superadmin UI from being used as a DoS vector.

**Files:**
- Modify: `routes/superadmin.php` (parent group middleware)
- Modify: `app/Providers/AppServiceProvider.php` (`configureRateLimiters()`, mirrors `resume-presign` rate limiter at L53-74)

**Order of operations:**
1. `AppServiceProvider.php` first (declares the named limiter).
2. `routes/superadmin.php` next (applies the middleware).

**Verify:** 11th request to `POST /super-admin/check-active` within 60 s returns 429; 1st-10th succeed.
**Risk:** Low — additive middleware.
**Rollback:** Remove the throttle middleware from the route group.

**Phase 3 exit gate:**
- [ ] All sub-phases 3.1–3.8 merged to `staging`
- [ ] Audit §6 verification matrix all-green
- [ ] `bash scripts/ai-guards.sh --all` exits 0
- [ ] No `app/Controller/...` still does inline `DB::table('users')->update(['status'=>...])` outside the Actions
- [ ] No `refresh:true` outside `BlockCompanyAction`'s `wait_for` exception
- [ ] Event::fake() tests pass for `UserAccountSuspended` on both event variants (single-user and company-block)

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

### 6.4 Audit table — `block_log` migration + `BlockLog` model
**Audit reference:** [`docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md`](BLOCKING_SCENARIOS_AUDIT.md) §5.3

`block_log` is the canonical record of every blocking action. The Actions created in Phase 3.3 already write to it via `BlockLog::create([...])` — this sub-phase is the prerequisite migration that gives the table somewhere to land.

**Files:**
- New: `database/migrations/2026_08_06_000001_create_block_log_table.php` (or co-located with the Phase 3.3 work)
- New: `app/Models/BlockLog.php` (Eloquent; `$table = 'block_log'`; `$timestamps = false`; `$guarded = []`; `public $incrementing = true`)

**Schema (descriptive):**

| Column | Type | Nullable | Indexed | Notes |
|--------|------|----------|---------|-------|
| `id` | bigint unsigned | no | pk | |
| `target_user_id` | bigint unsigned | yes | yes | nullable because company-wide blocks have no specific user |
| `target_company_id` | bigint unsigned | yes | yes | nullable because user-level blocks have no company-level target |
| `actor_user_id` | bigint unsigned | no | no | who performed the block |
| `block_type` | varchar(64) | no | no | enum string: `user_block`, `company_block_active`, `company_block_operational`, `company_block_sales`, `working_status_zero`, `self_deactivate`, `payment_failed`, `impersonation`, `self_delete` |
| `reason` | varchar(255) | yes | no | optional free-text — see pending product sign-off below |
| `created_at` | timestamp | no | no | `useCurrent()` |

**Order of operations:**
1. Migration + Model are written **alongside** Phase 3.3 (same files, same commit) so the Actions have a target table when they run.
2. This 6.4 sub-phase is the docs/plan reference for the migration itself; the actual file is part of Phase 3.3.

**Verify:** `php artisan migrate` succeeds; `php artisan test --filter=BlockLogTest` (new) covers `BlockLog::create()` end-to-end.
**Risk:** Low — additive table, no existing rows affected.
**Rollback:** `php artisan migrate:rollback` reverses the migration; no FK constraints to clean up.

### 6.5 Scheduler — wire `delete-account` and `licence-expire`
**Audit reference:** [`docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md`](BLOCKING_SCENARIOS_AUDIT.md) §3.3, §3.1

Both console commands exist today (`app/Console/Commands/DeleteAccount.php:51-62`, `app/Console/Commands/LicenceExpire.php:51-100`) but are **not registered** in `app/Console/Kernel.php:13-58`. The timed-delete and licence-expiry features are silently broken until these are scheduled.

**Files:**
- Modify: `app/Console/Kernel.php` (extend `protected function schedule(Schedule $schedule)`)

**Change (descriptive):**

- `$schedule->command('delete-account')->dailyAt('02:00')->timezone('Asia/Kolkata')->withoutOverlapping(50)->runInBackground();`
- `$schedule->command('licence-expire')->dailyAt('03:00')->timezone('Asia/Kolkata')->withoutOverlapping(50)->runInBackground();`

**Order of operations:**
1. Single-file edit to `app/Console/Kernel.php`. Verify with `php artisan schedule:list` that both commands appear with the expected cron expression.

**Verify:** `php artisan schedule:list` shows both; manual `php artisan delete-account --dry-run` and `php artisan licence-expire --dry-run` work; after 1 cron cycle in production, a test company with `delete_account_at < now()` flips to `status='inactive'` and the `block_log` table has a row.
**Risk:** Low — additive cron entries. Risk of overnight flip on existing test companies → run in staging for a week first.
**Rollback:** Remove the two `$schedule->command(...)` lines.

**Phase 6 exit gate:** All gates green; load test passes; security review signed off; audit table populated; scheduler visible in `schedule:list`.

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
- `docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md` — companion audit (42+ findings, refinement pass). Required reading before any Phase-3.x or Phase-6.4/6.5 work.
- `~/.claude/plans/memoized-stargazing-sutherland.md` — workflow history of the refinement + docs-only slices. Redundant with Appendix A but retained for audit trail.

---

## Appendix A — Cross-references to BLOCKING_SCENARIOS_AUDIT.md

This appendix maps every Phase-3 sub-phase and every Phase-6 audit-block in this plan to the audit section that motivates it. **No Phase-3 work should start without reading the linked audit section.**

| Plan phase | Subject | Audit reference |
|------------|---------|-----------------|
| 3.1 | Redis-only single-device enforcement | §3.6 (failed-login lockout context), §4.3 (inconsistent ES sync), §3.1 |
| 3.2 | `LogSuccessfulLogin` listener | §3.1 (related) |
| **3.3** | **Foundations (Action class + Event + Job + Model + Migration)** | **§5.1, §5.1.1, §5.3** |
| **3.4** | **Epoch gate in `EnsureOneDeviceLogin`** | **§5.2** |
| **3.5** | **Wire Actions into 3 user-block controllers + bulk teamDelete** | **§1 (defect footguns), §5.1 wiring list** |
| **3.6** | **Wire `BlockCompanyAction` into `SuperAdminCompanyController`** | **§2 (defects 1–7), §5.1.1** |
| **3.7** | **`refresh:true` lint guard** | **§5.5, §8** |
| **3.8** | **`superadmin-bulk-ops` rate limiter** | **§5.6** |
| **6.4** | **`block_log` migration + `BlockLog` model** | **§5.3** |
| **6.5** | **Scheduler for `delete-account` + `licence-expire`** | **§3.1, §3.3** |

Bold = new in this docs-only slice.

### Pending product sign-off (load-bearing for implementation)

These questions in `BLOCKING_SCENARIOS_AUDIT.md` §7 are **not answered**. They affect whether Phase-3 work ships as documented, or with adjustments.

#### Load-bearing (must be answered before Phase 3.3 PR)

1. **Hard-logout vs. soft-redirect on company block.** Audit proposes soft-redirect (epoch gate + lazy cleanup). Hard-logout would require per-user `Auth::logout()` iteration and defeats the O(1) design.
   - **If product wants hard-logout:** Phase 3.4 must gain an early-exit Redis loop before the epoch comparison. Phase 3.6's `BlockCompanyAction` would change from one Redis `SET` to one Redis `SET` + N session destroys. Design no longer O(1).
   - **Default if no answer:** soft-redirect (audit proposal). Ping product before Phase 3.6 PR.

2. **`block_log.reason` — free-text vs. typed categories only.** Migration creates a nullable `varchar(255)` column.
   - **If product wants typed categories only:** drop the column from the migration; the `block_type` string is sufficient.
   - **Default if no answer:** keep the column nullable, no UI initially. Easy to drop later.

Both items to flag in the Phase 3.3 PR description.

#### Secondary (do not block Phase 3.3, but answer before adjacent slices)

3. **`users` schema additions** (audit §4.4): `users.failed_login_attempts`, `users.locked_until`, `users.banned_at`, `users.deleted_at`, `users.last_active_at`, `users.blocked_by`, `users.blocked_at`, `users.block_reason`, `users.email_verified_at`. All require schema migrations. Decide which to add for Phase 3.7 / Phase 6.5 work vs. deferring to a separate ADR.
   - **Default if no answer:** add none in this slice. Defer all to a separate schema-additions ADR.

4. **Razorpay `payment.failed` and Stripe `invoice.payment_failed` auto-suspend** (audit §3.8): today these are no-op stubs. Should they flip `companies.status='hold'` automatically, or stay manual superadmin actions?
   - **Default if no answer:** stay manual. Auto-suspend on transient card declines creates risk; superadmin keeps manual control.

5. **Failed-login lockout policy** (audit §3.6): schema columns do not exist today; `LoginRequest` has a default of 5 attempts / 1 minute but is not wired. What is the desired policy?
   - **Default if no answer:** do nothing in this slice (out of V2_login scope per audit §5.8). Defer to a separate login-hardening ADR.

The audit doc's §7 has these as questions 1–5; this plan re-orders them so the load-bearing two come first.

### Out of scope (explicitly NOT in this audit's implementation pass)

These audit items require separate sign-off and are NOT in any Phase-3/6 sub-phase above:

- Failed-login lockout policy (audit §3.6): requires `users.failed_login_attempts`, `users.locked_until` columns → **schema change** under AI_RULE #8.
- Email-verification gate (audit §3.7): requires `users.email_verified_at` column → **schema change**.
- Razorpay `payment.failed` auto-suspend (audit §3.8): **business-logic change** (risk profile decision).
- Stripe `invoice.payment_failed` auto-suspend (audit §3.8): **business-logic change**.
- Frontend Echo listener wiring (audit §9): **frontend slice**, separate from server-side implementation.
- `teamDelete` root-user block rejection (audit §1 footgun #1): **business-logic change** (admin UX decision).
- Self-deactivate `delete_account_at` bug (audit §3.3, line 63/67 `=` vs `==`): **bug fix**, schedule separately.
- Super-admin impersonation exit route + audit (audit §3.5): **business-logic change**.
- All `users.*` columns listed in audit §4.4 (`deleted_at`, `banned_at`, `last_active_at`, `blocked_by`, `blocked_at`, `block_reason`): **schema change**.
- `docs/AI_BOOTSTRAP.md` — 5-step protocol
- `docs/RECIPES.md` — cookbooks
- `docs/VERIFY.md` — verification gates
- `docs/V2_login/AUDIT_REPORT.md` — source of all findings
- `.ai/PROJECT_MEMORY.md` — living state
