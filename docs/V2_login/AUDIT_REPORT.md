# JERO Login Page — GOD-Level Audit Report

**Audit date:** 2026-08-06
**Scope:** All login-related code paths (controllers, middleware, listeners, views, routes, captcha, session/auth config).
**Stack:** Laravel 11 + Octane/FrankenPHP + Redis (sessions/cache) + MySQL (write store) + Elasticsearch (read store) + Reverb.
**Target:** 10,000 concurrent recruiters with near real-time performance and minimal server load.
**Constraint:** No business-logic changes (rule #8). Only infrastructure, caching, async-ification, dead-code removal, UI standards compliance.

---

## 1. Files audited

| Path | Role | LOC |
|---|---|---|
| `app/Http/Controllers/Auth/LoginController.php` | Active login controller (god class) | 825 |
| `app/Http/Controllers/Auth/CustomloginController.php` | Dead alternate login controller | 295 |
| `app/Http/Controllers/Auth/LoginRequest.php` | Breeze-style FormRequest (unused) | 85 |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Breeze stub (route never loaded) | ~30 |
| `app/Http/Controllers/Auth/PasswordResetLinkController.php` | Breeze stub (dead) | ~30 |
| `app/Http/Controllers/Auth/NewPasswordController.php` | Breeze stub (dead) | ~50 |
| `app/Http/Controllers/Auth/ConfirmablePasswordController.php` | Breeze stub (dead) | ~30 |
| `app/Http/Controllers/Auth/EmailVerification*.php` | Breeze stub (dead) | small |
| `app/Http/Controllers/Auth/VerifyEmailController.php` | Breeze stub (dead) | small |
| `app/Http/Controllers/Auth/PasswordController.php` | Breeze stub (dead) | small |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Breeze stub (dead) | small |
| `app/Http/Controllers/Auth/ForgotPasswordController.php` | Legacy password reset | ~30 |
| `app/Http/Controllers/Auth/ResetPasswordController.php` | Legacy password reset | ~250 |
| `app/Http/Controllers/Auth/RegisterController.php` | Legacy register | ~70 |
| `app/Http/Middleware/EnsureOneDeviceLogin.php` | Single-device middleware (declared, never wired) | 71 |
| `app/Http/Middleware/RedirectIfAuthenticated.php` | Guest guard redirect | 80 |
| `app/Http/Middleware/VerifyCsrfToken.php` | Dead file (Laravel 11 inlines CSRF) | 25 |
| `app/Http/Middleware/RecruiterThrottle.php` | Job-route throttle (not on login) | 48 |
| `app/Http/Middleware/RecruiterSlidingWindowThrottle.php` | Job-route sliding window (not on login) | 64 |
| `app/Http/Controllers/Controller.php` | Base controller (runs `migrate:check` per request) | 72 |
| `app/Http/Controllers/HomeController.php` (`generate()`) | Captcha GD renderer | ~50 (method) |
| `app/Listeners/LogSuccessfulLogin.php` | Login event listener (never registered) | 28 |
| `app/Providers/AppServiceProvider.php` | Rate limiter registry (login rate limit missing) | ~120 |
| `app/Helper/GeneralMethod.php` | `value_encrypt`/`value_decrypt`, `coinsDeductions`, `userIndexingupload` used in login | n/a |
| `app/Helper/start.php` | `superAdmin()`, `company()`, `module_enabled()` global helpers | ~115 |
| `app/Services/ElasticService.php` | ES client used by login | n/a |
| `app/Models/User.php` | Eloquent user model | 109 |
| `resources/views/auth/login.blade.php` | Primary login view | 301 |
| `resources/views/auth/superadmin-login.blade.php` | Super-admin login view | 210 |
| `routes/web.php` (lines 128, 137–143, 150–160) | Login/captcha/password routes | ~30 (slice) |
| `routes/auth.php` | Breeze auth routes (registered but not loaded) | 60 |
| `bootstrap/app.php` | Middleware aliases + 419 redirect handler | 59 |
| `config/auth.php` | Auth guards/providers | 116 |
| `config/session.php` | Session config | 218 |
| `.env` | Runtime config (Redis session driver, Redis cache, Octane) | n/a |

---

## 2. Severity legend

| Tier | Definition |
|---|---|
| **P0 — Critical** | Will block the 10K target. Fix before next release. |
| **P1 — High** | Significant server load or scaling headroom loss. |
| **P2 — Medium** | Wasteful, slows some paths, manageable now. |
| **P3 — Low** | Dead code, style, micro-optimisation. |

---

## 3. Findings (ranked by severity)

### P0-1 — `Artisan::call('route:clear')` + `Artisan::call('cache:clear')` on every login-related controller instantiation
**Files:** `app/Http/Controllers/Auth/LoginController.php:64-65`, `:384-385`, `:487-488`
- `__construct()`, `showLoginForm()`, and `superLoginForms()` all call `Artisan::call('route:clear')` and `Artisan::call('cache:clear')`.
- `route:clear` walks every route file (`web.php` is ~158 KB) and rebuilds the route cache. `cache:clear` flushes **every** Redis cache key for **every** concurrent user, invalidating hot caches for all 10K recruiters.
- Effect: Login page load adds hundreds of ms and destroys in-flight cached data.
**Fix:** Delete both calls. Routes are pre-cleared at deploy time; caches must not be invalidated on request.

### P0-2 — `Artisan::call('migrate:check')` on every controller constructor
**File:** `app/Http/Controllers/Controller.php:47`
```php
public function __construct()
{
    $this->showInstall();
    $this->checkMigrateStatus();   // ← artisan on every request
}
```
- Every controller (including `LoginController`) calls `checkMigrateStatus()` in its base constructor, which spawns an artisan invocation per HTTP request.
- Under 10K concurrent this is 10K sub-process style invocations per second.
**Fix:** Move migration check to a scheduled job (every 5 minutes) or delete — CI/CD should run migrations.

### P0-3 — Captcha GD render on every page load (no caching, no client-side reuse)
**File:** `app/Http/Controllers/HomeController.php:664-701` (`generate()`)
- Each `/captcha` request: allocates `imagecreate(160,50)`, loads a TTF font from disk, draws noise pixels, PNG-encodes, returns.
- `<img src="{{ route('captcha.generate') }}">` is fetched every login page view with **no `Cache-Control` header**.
- Under 10K recruiters, that's 10K GD renders/sec on the app server, no caching, no client-side reuse.
- Bonus: numeric-only 4-digit captcha is brute-forceable in milliseconds.
**Fix:** Cache the PNG bytes in Redis per session, send `Cache-Control: private, max-age=120`; switch to alpha-numeric captcha; add `throttle:captcha` middleware.

### P0-4 — Synchronous Elasticsearch writes on every successful login (with `refresh: true`)
**File:** `app/Http/Controllers/Auth/LoginController.php:209-250` and again `:358-377` in `authenticated()`
```php
$this->elasticService->getClient()->exists([...]);            // round-trip 1
$this->elasticService->getClient()->update(['refresh' => true]); // round-trip 2 (forces refresh)
$this->elasticService->getClient()->exists([...]);             // round-trip 3
$this->elasticService->getClient()->update(['refresh' => true]); // round-trip 4
```
- Three ES round-trips per login, with `refresh:true` (the most expensive ES operation).
- `authenticated()` (Laravel event hook) runs a **duplicate** ES update path — two updates per login with no de-duplication.
- 10K concurrent logins → 30K ES ops, all forcing immediate refresh.
**Fix:** Queue the `last_login` ES update (Horizon job, no `refresh:true`); delete the duplicate path in `authenticated()`.

### P0-5 — No rate limiting on login route (5-attempts/min in dead code)
**Files:** `routes/web.php:137-138`, `app/Http/Requests/Auth/LoginRequest.php:60-76`
- Login route is `Route::post('login', ...)` with **no `throttle:` middleware**.
- `LoginRequest::ensureIsNotRateLimited()` exists but **is never called** — `LoginController::login()` uses inline `$request->validate(...)`, never type-hints `LoginRequest`.
- Effect: Unlimited brute-force attempts against credentials + captcha + ES `last_login` update path.
**Fix:** Add `->middleware('throttle:5,1')` to login POST; or call `LoginRequest::ensureIsNotRateLimited()` from `login()`.

### P1-1 — `LoginController` is a 825-line god controller
**File:** `app/Http/Controllers/Auth/LoginController.php`
- Contains `login()` (186 lines), `showLoginForm()`, `superLoginForms()` (95% duplicate), `authenticated()`, `redirectTo()`, `credentials()`, `validateLogin()` (dead), `logout()`, `rulesValidate()`, `get_domain()`.
- `login()` mixes captcha check, super-code bypass, password-set-on-login (when `userId+companyid+toc` present — team-invite flow), and the main credentials check, with hardcoded company_type dispatch that ignores `company_type === 3` (all redirected to `service_company/dashboard`).
- `authenticated()` and `login()` **both** write `$user->last_login` and ES — race condition possible.
- `redirectTo()` runs **5 SQL queries** (companies, clients, client_location, td_industries, user_token) every single login.
**Fix:** Extract `LoginService` / CQRS Action per Rule 11 — `AttemptLoginAction`, `RecordLastLoginAction`, `ResolveRedirectAction`.

### P1-2 — Raw `DB::table()` queries on every login (no caching)
**File:** `LoginController.php:84, :206, :263, :266-273, :397, :399-406, :500, :502-509, :518, :696, :718`
- 5+ raw `DB::table('companies')` queries per login (with joins to `clients`, `client_location`, `td_industries` for the `companyRowRedirect` block).
- The `companyRowRedirect` join block is a **4-table join** run on every login attempt and again on every already-authenticated visit to the login page.
- Companies rarely change `company_type` — this should be cached.
**Fix:** `Cache::remember("company:type:{id}", 6h, fn() => DB::table('companies')->where('id', $id)->first(['company_type']))`. Invalidate via Company observer.

### P1-3 — Two user lookups per login (peek + `Auth::attempt`)
**File:** `LoginController.php:179-180`
```php
$user = User::where('email', $request->email)->first();   // lookup 1
if ($user && Hash::check($request->password, $user->password)) { ... }
$credentials = $request->only('email', 'password');
if (Auth::attempt($credentials)) { ... }                   // lookup 2 inside
```
- Two `users` queries per login.
**Fix:** Use `Auth::validate($credentials)` first (returns bool, no session write), then `Auth::attempt()` only on success.

### P1-4 — `EnsureOneDeviceLogin` middleware: declared but NEVER registered globally
**Files:** `bootstrap/app.php:39`, `app/Http/Middleware/EnsureOneDeviceLogin.php`
- Middleware alias is registered, but no `$middleware->appendToGroup('web', EnsureOneDeviceLogin::class)` exists.
- If it were wired in: per authenticated request it would do a `users` UPDATE (`session_id = currentSessionId`) — a write per page load.
**Fix:** Either wire it up and replace the MySQL write with Redis `SETNX device:user:{id} {session}`, or delete the middleware as abandoned.

### P1-5 — `LogSuccessfulLogin` listener: synchronous DB writes (currently unwired)
**File:** `app/Listeners/LogSuccessfulLogin.php:12-27`
- Runs on the Login event inside `Auth::attempt()`. Not currently wired (no `EventServiceProvider`, no `Event::listen` call).
- If reactivated: `DB::table('sessions')->where(...)->delete()` + `$user->save()` — two sync writes per login.
**Fix:** Implement with `ShouldQueue` or delete entirely.

### P1-6 — `user_token` table: INSERT/UPDATE on every login
**File:** `LoginController.php:696-710` (in `redirectTo()`)
```php
$userCounter = DB::table('user_token')->select('id')->where('user_id', $user_id)->count();
if ($userCounter > 0) {
    DB::table('user_token')->where('user_id', ...)->update(['token' => $token]);
} else {
    DB::table('user_token')->insert([...]);
}
```
- 2–3 MySQL writes per login, all sequential.
**Fix:** `Cache::put("user_token:{id}", $token, 86400)` in Redis. Zero MySQL writes.

### P1-7 — `redirectTo()` uses `exit` inside every branch (control-flow smell + open redirect surface)
**File:** `LoginController.php:687-756`, `:295-310`, `:719-733`, `RedirectIfAuthenticated.php:32-46`
- Returns a string with `exit` inside if-blocks. Reachable code falls through silently if `company_type` is 4+ (returns null → `redirect(null)` crashes).
- 6 SQL queries in `redirectTo()` alone. Hardcoded tenant mapping duplicated in **4 different places** (`LoginController`, `CustomloginController`, `RedirectIfAuthenticated`, implicit redirect).
**Fix:** Centralise in `TenantRouter::dashboardRoute(User $u): string` with a constant map.

### P1-8 — `session()->getHandler()->destroy()` on every authenticated request
**File:** `app/Http/Middleware/EnsureOneDeviceLogin.php:50`
- If a stale session exists, it destroys it synchronously on the request thread.
- Combined with the MySQL `session_id` write, this is two sync writes per page view.
**Fix:** See P1-4 — Redis-only enforcement.

### P1-9 — GET `/logout` route (CSRF gap)
**File:** `routes/web.php:141-143`
```php
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('logout',  [LoginController::class, 'logout']);
```
- GET logout bypasses CSRF and runs the same handler including `$user->save()`.
**Fix:** Remove the GET route.

### P2-1 — Captcha numeric 4-digit = brute-forceable ceiling
**File:** `HomeController::generate():667` — 9999 possible values, 2-min expiry, no per-attempt throttle.
**Fix:** Alpha-numeric 5–6 chars; `throttle:captcha,30,1`.

### P2-2 — Captcha stored as plain session value, not hashed
**File:** `HomeController::generate():670` stores plain value; `generateChar():715` (unused) uses `hash('sha256', ...)`.
**Fix:** Use `generateChar()`'s logic in `generate()` and hash-compare server-side.

### P2-3 — Login views: JS handler inline after `@endsection`
**File:** `login.blade.php:297-301`
**Fix:** Move `<script>` to `@push('scripts')` or to a dedicated `auth/login.js`.

### P2-4 — XSS surface in login views (rule #6 / BUG-04)
**File:** `login.blade.php:130`, `superadmin-login.blade.php:76`
```blade
<strong>{{ @$class }}</strong> {!! @$messsage !!}
```
- `{!! @$messsage !!}` is unescaped.
**Fix:** `{{ @$messsage }}`.

### P2-5 — Unused `CustomloginController` (295 lines) alongside active `LoginController`
**File:** `app/Http/Controllers/Auth/CustomloginController.php`
- Not wired to any route. `get_domain()` is recursive without `return` (fatal).
**Fix:** Delete.

### P2-6 — `routes/auth.php` registered but NEVER LOADED (Breeze stubs are dead code)
**Files:** `routes/auth.php`, `bootstrap/app.php:22-27`
- `bootstrap/app.php` only loads `web:` — `auth.php` is orphaned. Its 7 controllers (`AuthenticatedSessionController`, `PasswordResetLinkController`, `NewPasswordController`, `ConfirmablePasswordController`, `EmailVerification*`, `VerifyEmailController`, `PasswordController`, `RegisteredUserController`) are dead code.
- A future dev might think `LoginRequest::ensureIsNotRateLimited()` is the rate-limit gate when it isn't.
**Fix:** Delete `routes/auth.php` + 7 controllers, or wire it via `->withRouting(auth: ...)`.

### P2-7 — Captcha routed through `HomeController` (16,700-line god)
**File:** `HomeController::generate()`
- Pulls all `use` statements and `AppBoot` trait on every captcha request.
**Fix:** Extract `CaptchaController` (50 lines, no constructor).

### P2-8 — `autocomplete="off"` breaks password managers
**File:** `login.blade.php:141, :156`
**Fix:** `autocomplete="username"` and `autocomplete="current-password"`.

### P2-9 — `remember_token` abused as workflow flag
**File:** `LoginController.php:295-310`, `:719-733`, `RedirectIfAuthenticated.php:32-46`
- Laravel remember-me token compared against `user_id` to gate profile-edit redirect. Security primitive used as state flag.
**Fix:** Introduce `must_complete_profile` boolean column or session flag.

### P2-10 — Font sizes exceed UI_UX cap
**File:** `login.blade.php:103, :106`, `superadmin-login.blade.php:49, :52`
- `fs-2qx` (28px) and `fs-2` (22px) violate `UI_UX_STANDARDS.md §2` (16px max).
**Fix:** `fs-3` (16px) or `fs-4` (14px).

### P2-11 — Login views not wrapped in `.section-card`
**File:** `login.blade.php:118-291`, `superadmin-login.blade.php:64-207`
- Raw content rendered on `#eef2f7` page background.
**Fix:** Wrap form in `<div class="section-card">` with header icon (per `UI_UX_STANDARDS §10`).

### P2-12 — Mixed legacy `Form::open` and raw HTML
**File:** `login.blade.php:124, :237`
**Fix:** Extract `<x-auth-login-form>` Blade component or use `@livewire`.

### P2-13 — `value_encrypt`/`value_decrypt` for super-code
**File:** `LoginController.php:106, :592`, `superadmin-login.blade.php:98`
- "SUPERCODE" decrypted server-side on every login attempt; relies on `GeneralMethod::value_decrypt` reversibility.
**Fix:** Audit crypto; prefer short-lived token in DB.

### P3-1 — `CustomloginController::get_domain()` recursion without `return`
**File:** `CustomloginController.php:283-294` — line 291 missing `return`.
**Fix:** P2-5 (delete).

### P3-2 — DRY violation: `CustomloginController::redirectTo()` duplicates `LoginController::redirectTo()` minus one branch
**Fix:** P2-5 (delete).

### P3-3 — `LoginController::logout()` `$user->session_id = null; $user->save()` after auth already nulled it
**File:** `LoginController.php:765-767`
**Fix:** Remove redundant save.

### P3-4 — `LoginController::superLoginForms()` 100% duplicate of `showLoginForm()` minus view name
**File:** `LoginController.php:485-584`
**Fix:** Extract `showLoginForm(string $view)`.

### P3-5 — Hardcoded `redirect()->intended('service_company/dashboard')` ignores `session('url.intended')`
**File:** `LoginController.php:252`
- Logic split between `login()` and `redirectTo()`.
**Fix:** Single resolution point in `ResolveRedirectAction`.

### P3-6 — Repeated `@if(session('accountnotverify'))` in both views
**Fix:** Extract to `<x-auth-flash-banner>`.

### P3-7 — `action: [HomeController::class, 'resumExtract']` typo / inconsistency
**File:** `routes/web.php:171` — `action:` mixed with `->name(...)`. Stylistic.
**Fix:** Normalise to `use [...]`.

### P3-8 — `$redirectTo = '/admin/dashboard'` (line 50) is dead — never used
**File:** `LoginController.php:50`
**Fix:** Delete.

### P3-9 — `LoginController::validateLogin()` is unused (AuthenticatesUsers trait removed)
**File:** `LoginController.php:669-685`
**Fix:** Delete.

### P3-10 — 7 Breeze controllers in `app/Http/Controllers/Auth/*` are dead code
**Fix:** P2-6 (delete or wire).

### P3-11 — **Open redirect via `url` param** (security)
**File:** `LoginController.php:473`, `:182-184`, `:328`, `login.blade.php:142`
- `?url=...` → `Session::put('open_url', $request->url)` → `redirect($url)` after login. No validation, accepts `javascript:`, external URLs, etc.
- **GOD-level refinement:** A naive `Str::startsWith($url, '/') && !Str::contains($url, '//')` check is **not safe** — it accepts `//evil.com` (protocol-relative, browser interprets as external) and backslash-prefixed tricks like `/\evil.com`. The fix uses `parse_url()` with strict host comparison against `config('app.url')` and explicit rejection of:
  - Fully-qualified external URLs
  - Protocol-relative URLs (`//evil.com`)
  - Backslash-prefixed URLs (`/\evil.com`, `\\evil.com`)
  - `javascript:`, `data:`, `vbscript:` schemes
  - URLs with userinfo (`https://jero.works@evil.com`)
**Fix:** Implement `App\Support\OpenRedirect::sanitize(?string $input): ?string` per Phase 0.4 of the implementation plan.

### P3-12 — `Controller::__construct()` runs `auth()->user()` in middleware on every request even when unauthenticated
**File:** `app/Http/Controllers/Controller.php:34-41`
- Each request runs an `auth()` call regardless of route. Cheap but redundant for login (unauthenticated) pages.
**Fix:** Lazy resolve: `$this->user = fn() => auth()->user();`.

### P3-13 — Two competing `__construct` paths in `LoginController` and `CustomloginController` both call `parent::__construct()`
- Redundant; both call `App::setLocale($this->global->locale)` from the parent. Will be a problem if locale changes per-request later.

### P3-14 — No CSP / security headers middleware in app
**File:** `app/Http/Middleware/` (no CSP middleware present)
- Grep for `csp`, `X-Frame`, `Strict-Transport`, `nosniff`, `Content-Security` returns no PHP matches.
- Login views are unprotected against clickjacking (`X-Frame-Options`) and content injection (CSP).
- Only `TrustProxies.php` is registered; no security headers set.
**Fix:** Add a `SecurityHeaders` middleware (CSP, X-Frame-Options: DENY, X-Content-Type-Options: nosniff, Strict-Transport-Security, Referrer-Policy: strict-origin-when-cross-origin). Apply globally via `$middleware->append(SecurityHeaders::class)`.

### P3-15 — `RedirectIfAuthenticated` returns raw strings in some branches + uses `exit` in closure
**File:** `app/Http/Middleware/RedirectIfAuthenticated.php:32-46`
```php
if($user_id == $remember_token) {
    ...
    return 'service_company/profile/'.$user_id.'/edit';   // ← returns string, not redirect
    exit;                                                  // ← exits the whole PHP process
}
```
- String return would crash `redirect(null)` if a caller expected a `RedirectResponse`.
- `exit` inside a middleware closure terminates the entire PHP process — not just the request.
**Fix:** Use `return redirect(...)` consistently. Replace `exit` with `return`. (Will be subsumed by Phase 2 `TenantRouter` refactor.)

### P3-16 — `app/Http/Middleware/VerifyCsrfToken.php` is dead (Laravel 11 inlines CSRF)
**File:** `app/Http/Middleware/VerifyCsrfToken.php`
- `$except` list with `/save-invoices` and `/save-razorpay-invoices`; `handle()` commented out.
- Laravel 11 registers CSRF middleware automatically via `bootstrap/app.php`; this file is not loaded.
- Risk: a future dev thinks this is the source of truth and adds exceptions that don't apply.
**Fix:** Delete the file (after confirming no reference in `bootstrap/app.php` or anywhere else). Note: `/save-invoices` and `/save-razorpay-invoices` must be re-added as CSRF exemptions in the new global CSRF config if currently relied upon.

### P3-17 — No observability hooks (Sentry, OpenTelemetry, structured login metrics)
**File:** various (no login instrumentation)
- No `Sentry`, `Bugsnag`, `OpenTelemetry`, or analytics calls in the login path.
- Only `last_login` is recorded (sync MySQL + ES); no failure-attempt counter, no captcha-skip counter, no throttle-trip counter.
**Fix:** Add a `login_attempts` log channel (structured JSON: email, ip, success/fail, captcha_ok, throttle_hit, latency_ms). Wire via Telescope tag or Sentry breadcrumb.

### P3-18 — No dedicated `resources/js/login.js`; login is plain HTML form post
**File:** `resources/js/` (no login-specific module)
- All client-side behaviour is inline `<script>` in the blade view.
- No progressive enhancement, no client-side validation feedback, no fetch-based submission, no offline handling.
**Fix:** Defer — current form-post UX is acceptable. If PWA/offline support becomes a goal, extract to `resources/js/auth/login.js` (Vite entry).

---

## 4. Quantified impact (per-request, login page hit)

| Concern | Current | After fixes |
|---|---|---|
| MySQL writes per login | 4–7 (users last_login, session_id, user_token, companies/clients/client_location update) | 0–1 (only user_token moved to Redis, OR none if we cache) |
| ES round-trips per login | 3 (exists + 2 updates, refresh:true) | 0 (queued) |
| Artisan subprocesses per login-page load | 4 (`route:clear` ×2, `cache:clear` ×2, `migrate:check`) | 0 |
| Redis cache flushes per login-page load | 1 (full cache:clear) | 0 |
| SQL queries per login | 5–10 | 1 (cached `company_type`) |
| Captcha GD renders per login-page view | 1 | 0 (cached PNG) or moved client-side |
| Auth lookups per login attempt | 2 (peek + Auth::attempt) | 1 (`Auth::validate`) |
| Rate-limit check per login | none | 1 Redis hit |
| Brute-force attempts/min per IP | unlimited | 5 |

---

## 5. Architecture recommendations (no business-logic changes)

1. **CQRS Action class for login** (`app/Actions/Auth/AttemptLoginAction.php`, `ShowLoginFormAction.php`, `LogoutAction.php`) — vertical slice per Rule 11.
2. **Cache tenant routing map** in Redis (`company_type:{id}` → route, 24h TTL). Populate on Company create/update via Observer.
3. **No synchronous ES writes from login path** — push `last_login` to a low-priority Horizon queue; default ES refresh (1s) is sufficient for an informational field.
4. **Captcha off the request path** — precompute PNG into Redis per session, send `Cache-Control: private, max-age=120`. Future: client-side Canvas + token, zero server cost.
5. **Single-device enforcement via Redis** — `SETNX device:user:{id} {session}`. Zero MySQL writes.
6. **Rate-limit every auth endpoint by default** — login, register, password-reset, captcha-refresh, social-callback.
7. **Centralise tenant route resolution** — one helper `TenantRouter::dashboardRoute(User $u): string` used by login, redirect, middleware. Cache the result.
8. **Move `migrate:check` to scheduled job** — never run from request path.
9. **Delete Breeze stubs and `CustomloginController`** — reduce class-loading surface; reduce confusion.
10. **Validate `url` param** — internal-only allow-list; closes open redirect (P3-11).

---

## 6. Quick wins (do first, biggest ROI)

| # | Action | Estimated server load cut |
|---|---|---|
| 1 | Delete `Artisan::call('route:clear')` + `cache:clear` from `LoginController` (P0-1) | ~90% login-page Redis flushes eliminated |
| 2 | Move `checkMigrateStatus` out of controller constructor (P0-2) | 5–15ms saved per request |
| 3 | Cache captcha PNG bytes in Redis + `Cache-Control` header (P0-3) | ~95% GD renders eliminated |
| 4 | Queue ES `last_login` updates, drop `refresh:true` (P0-4) | ES stops blocking login |
| 5 | Add `throttle:5,1` to login POST (P0-5) | Brute-force ceiling capped |
| 6 | Cache `company_type` by `company_id` (P1-2) | 4 SQL → 1 Redis hit per login |
| 7 | Wire `LoginRequest` to the route (P2-6) | Enables rate limit + validation reuse |
| 8 | Validate `url` param against allow-list (P3-11) | Closes open redirect |
| 9 | Drop dead `routes/auth.php` controllers OR wire them in (P2-6) | Removes confusion surface |
| 10 | Delete `CustomloginController.php` (P2-5) | −295 LOC |

---

## 7. Files to touch (priority order)

1. `app/Http/Controllers/Auth/LoginController.php` — delete `Artisan::call` lines, queue ES update, drop duplicates, cache `company_type`, use `Auth::validate`.
2. `app/Http/Controllers/Controller.php` — move `checkMigrateStatus` out of constructor.
3. `app/Http/Controllers/HomeController.php` — extract `generate()` into `CaptchaController`; cache PNG bytes; hash captcha value.
4. `routes/web.php` — add `throttle` to login route; remove GET logout; add validation on `url` query param.
5. `app/Http/Requests/Auth/LoginRequest.php` — already correct; just wire it.
6. `bootstrap/app.php` — either register `EnsureOneDeviceLogin` (and fix it) or delete it; register new `SecurityHeaders` middleware globally.
7. `app/Listeners/LogSuccessfulLogin.php` — implement with `ShouldQueue` or delete.
8. `resources/views/auth/login.blade.php`, `superadmin-login.blade.php` — UI standards compliance, escape `{!! !!}`, `autocomplete` values, `.section-card` wrapper, font sizes.
9. `routes/auth.php` + 7 controllers in `app/Http/Controllers/Auth/*` — delete or wire.
10. `app/Http/Controllers/Auth/CustomloginController.php` — delete.
11. `app/Http/Middleware/EnsureOneDeviceLogin.php` — replace MySQL writes with Redis `SETNX`.
12. `app/Http/Middleware/VerifyCsrfToken.php` — delete (Laravel 11 inlines CSRF); preserve `/save-invoices`, `/save-razorpay-invoices` exemptions in `bootstrap/app.php` `validateCsrfTokens` if relied upon.
13. `app/Http/Middleware/RedirectIfAuthenticated.php` — replace `return 'string'` + `exit` patterns with `return redirect(...)`; will be subsumed by Phase 2 `TenantRouter`.
14. `app/Http/Middleware/SecurityHeaders.php` (NEW) — CSP, X-Frame-Options, X-Content-Type-Options, Strict-Transport-Security, Referrer-Policy.

---

## 8. Verification matrix (to be applied after fixes)

| Check | How |
|---|---|
| Login page load time | `php artisan route:list --path=login` + Octane benchmark; target < 300ms |
| Login POST latency (P50, P99) | Telescope / Horizon dashboard, request_log channel |
| Brute-force attempts blocked | Attempt 6 wrong passwords → `429 Too Many Requests` |
| Captcha served from cache | `redis-cli GET captcha:{session}` + `curl -I` shows `Cache-Control` |
| `last_login` updated in ES | Check `ELASTIC_USERS_LIST` doc 5s after login (no `refresh:true` queue lag) |
| Single-device invariant | Login from device A, then B → device A session invalidated |
| No sync `migrate:check` on requests | `grep -c 'migrate:check'` in request profiler = 0 |
| No `route:clear`/`cache:clear` on login | `grep -c` in request profiler = 0 |
| `company_type` cache hit rate | Redis `INFO` → `keyspace_hits / (hits + misses)` for `company:type:*` > 95% |
| Open-redirect fixed | `curl /login?url=https://evil.com` → after login, redirect to internal route or `/` |

---

## 9. Out of scope (explicit)

- **Business logic changes:** login flow order, captcha difficulty policy, redirect rules per `company_type`, password rules.
- **Multi-factor authentication** — not in current scope; would be a separate feature task.
- **OAuth/Social login** — present as disabled links (display:none) but not implemented; out of scope here.
- **Schema changes** — no migrations. The `session_id` column on `users` (used by `EnsureOneDeviceLogin` and `LogSuccessfulLogin`) stays.
- **`hire.jero.works`** — per CLAUDE.md, work happens only in this repo.

---

**Estimated cumulative benefit of the P0 fixes:** login-page server load down ~85–95%; ES steady-state during peak login windows unaffected; brute-force ceiling capped; single-device invariant no longer scales with MySQL write throughput.
