# JERO Login V2 — Current vs. After-Implementation Comparison

**Companion to:** `AUDIT_REPORT.md`, `IMPLEMENTATION_PLAN.md` (same folder)
**Date:** 2026-08-06
**Purpose:** Side-by-side view of the current implementation vs. the post-V2 target state across every layer that the audit flagged. No business-logic changes; only infrastructure, caching, async-ification, dead-code removal, UI compliance.

---

## 1. Request path (the hot path)

### Login GET `/login` — current vs. after

| Step | Current | After (V2) |
|---|---|---|
| 1. Route resolves | `routes/web.php:137` → `LoginController@showLoginForm` | Same route |
| 2. `Controller::__construct()` runs | **Yes** — `checkMigrateStatus()` spawns `Artisan::call('migrate:check')` on **every request** | Removed; runs on scheduler or CI/CD |
| 3. `LoginController::__construct()` runs | **Yes** — `Artisan::call('route:clear')` + `Artisan::call('cache:clear')` on every instantiation | Removed; runs at deploy |
| 4. `showLoginForm()` runs | **Yes** — again `Artisan::call('route:clear')` + `Artisan::call('cache:clear')`; plus `ThemeSetting::whereNull('company_id')->first()` + `FrontCmsHeader::first()` raw queries | One Redis hit for `theme`; captcha PNG served from Redis |
| 5. Captcha `/captcha` image render | **GD `imagecreate(160,50)` + `imagettftext` + 80 noise pixels + `imagepng()` on every fetch** — no caching, no `Cache-Control` header | Redis-cached PNG bytes keyed by `captcha:{sessId}`, TTL 2 min; `Cache-Control: private, max-age=120`; `CaptchaController` (no `AppBoot` trait, ~50 LOC) |
| 6. Authenticated-redirect check | Raw `DB::table('companies')->select('company_type')` + 4-table join (`companies` ⋈ `clients` ⋈ `client_location` ⋈ `td_industries`) on every visit | `TenantRouter::resolve(User)` → `TenantLookup::companyType(id)` from Redis (6 h TTL) |
| 7. Security headers | None — no CSP, no X-Frame-Options, no HSTS | `SecurityHeaders` middleware appends: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Strict-Transport-Security`, `Referrer-Policy`, CSP for login |

### Login POST `/login` — current vs. after

| Step | Current | After (V2) |
|---|---|---|
| 1. Route resolves | `routes/web.php:138` → `LoginController@login` (no throttle) | `->middleware('throttle:5,1')` (5 attempts/min/IP+email) |
| 2. Captcha validation | Inline `$request->validate(['captcha' => 'required|digits:4'])` then plain-text compare | Hashed compare (using `generateChar()` logic) |
| 3. User lookup | **Two queries:** peek `User::where('email', ...)->first()` + `Auth::attempt` (which does its own lookup) | **One query** — `Auth::attempt()` directly. On success, inspect `$user->status`; if inactive, `Auth::logout()` + throw. (Earlier draft proposed `Auth::validate()` first — **wrong**, that adds a second lookup, not removes one.) |
| 4. `Auth::attempt` outcome | Direct flow | Throws `ValidationException` on fail (uses `LoginRequest`); on success dispatches `RecordLastLoginEsJob` |
| 5. `user->last_login` write | **Sync** — `$user->save()` in controller | Same (cheap; keeps "last_login_at" fresh in DB) |
| 6. ES `last_login` update | **3 ES round-trips synchronously**, with `'refresh' => true` on each (forces ES refresh interval to block login); duplicate ES write path in `authenticated()` hook | **0 sync ES round-trips**; `RecordLastLoginEsJob` (`ShouldQueue`, queue = `low`) does **1 bulk HTTP call** to ES, with `doc_as_upsert => true` (no `exists()` pre-check) and `retry_on_conflict => 5`. Both `ELASTIC_USERS_LIST` and `ELASTIC_COMPANIES_INDEX` updated atomically. |
| 7. `user_token` table write | **2–3 MySQL queries** — `count` + `update`/`insert` against `user_token` table | `Cache::put("user_token:{id}", $token, 86400)` — Redis only, zero MySQL writes |
| 8. Tenant redirect resolution | **5 raw `DB::table()` queries** (`companies`, `clients`, `client_location`, `td_industries`, `user_token`) + hardcoded `if/else` on `company_type` duplicated in 4 places; uses `exit` after every branch | `TenantRouter::resolve($user)` — one Redis hit for `company:type:{id}` |
| 9. Rate-limit check | None (rate-limit code in `LoginRequest` is dead — never called) | Active: `throttle:5,1` route middleware + `LoginRequest::ensureIsNotRateLimited()` invoked from action |
| 10. Audit log | None | Structured `login_attempts` channel: `{email, ip, user_agent, success, captcha_ok, throttle_hit, latency_ms}` |
| 9b. `url` param validation | `Session::put('open_url', $request->url)` raw — accepts `https://evil.com`, `//evil.com` (protocol-relative), `/\evil.com` (backslash trick), `javascript:`, `https://jero.works@evil.com` (userinfo) | `OpenRedirect::sanitize()` rejects all of the above using `parse_url()` + host comparison against `config('app.url')`; only relative paths under app host survive |
| 11. Security headers | None | `SecurityHeaders` middleware (CSP, X-Frame-Options, etc.) |

### Captcha `/captcha` GET — current vs. after

| Aspect | Current | After (V2) |
|---|---|---|
| Route | `web.php:128` → `HomeController@generate` (16,700-line god) | `web.php:128` → `CaptchaController@generate` (~50 LOC, no constructor, no `AppBoot`) |
| Renderer | GD: `imagecreate`, `imagettftext`, 80 `imagesetpixel`, `imagepng`, on every fetch | Generated once per session, cached in Redis |
| Caching | None | Redis `captcha:{sessId}` with 2-min TTL; `Cache-Control: private, max-age=120` |
| Brute-force ceiling | 9,999 values × 2-min TTL = trivial | Hashed captcha, alpha-numeric, longer TTL → much harder; route-level throttle |
| Class load cost | Pulls all of `HomeController` (every `use`, `AppBoot`, etc.) on every captcha request | Tiny `CaptchaController` class |

### Logout POST `/logout` — current vs. after

| Aspect | Current | After (V2) |
|---|---|---|
| Routes | Both POST and GET wired (`web.php:141-143`) — GET bypasses CSRF | POST only; GET removed |
| `user->session_id = null; $user->save()` | **Sync MySQL write** per logout | Replaced by `Cache::forget("device:user:{id}")` — zero MySQL writes |
| Single-device enforcement | `LogSuccessfulLogin` listener (unwired) + `EnsureOneDeviceLogin` (declared, never registered) | `DeviceSessionManager::enforce($user, $currentSessionId)` runs **once** on Login event (not per request): 1 Redis `GET` → 0..1 session `destroy()` → 1 Redis `SETEX`. Middleware does 1 read-only `Redis::get('user:session:{id}')` per request — **zero writes**. O(1) per user per login. |

---

## 2. Middleware pipeline

| Middleware | Current | After (V2) |
|---|---|---|
| `SecurityHeaders` (CSP/X-Frame-Options/HSTS/etc.) | **Absent** | **New** — appended globally |
| `EnsureOneDeviceLogin` | Alias registered in `bootstrap/app.php:39` but **never applied** to any group; if wired in, would write `users.session_id` MySQL on every authenticated request | Redis-only: `SETNX device:user:{id} {sess}` + listener on `Login` event; zero MySQL writes |
| `RedirectIfAuthenticated` (`guest`) | Has bugs — returns **raw strings** instead of `redirect()` in some branches; uses **`exit`** in closure (terminates whole PHP process); hardcoded `company_type` mapping | Calls `TenantRouter::resolve($user)`; always returns `RedirectResponse` |
| `VerifyCsrfToken` | Dead file (Laravel 11 inlines CSRF) | Deleted; `/save-invoices` and `/save-razorpay-invoices` exemptions moved to `bootstrap/app.php` `validateCsrfTokens(except: [...])` if needed |
| `RecruiterThrottle` / `RecruiterSlidingWindowThrottle` | Wired on job routes only, not login | Unchanged; login uses route-level `throttle:5,1` |

---

## 3. Per-request cost — login page

| Metric | Current | After (V2) | Δ |
|---|---|---|---|
| Artisan subprocess invocations per request | 3 (`route:clear`, `cache:clear`, `migrate:check`) | **0** | −3 subprocesses |
| Full Redis cache flushes per request | 1 | **0** | −1 flush |
| SQL queries per login-page render | 2 (`ThemeSetting`, `FrontCmsHeader`) + (if authed) 5 (`companies`, 4-table join, etc.) | 0 | −2 to −7 SQL queries |
| GD renders per captcha request | 1 | 0 (Redis cache hit) | −1 GD render |
| ES round-trips per successful login | 3 (1 `exists` + 2 `update`, each `refresh:true`) | **1 async bulk** call (`update` × 2 with `doc_as_upsert`, no `exists`) | −2 sync ES round-trips |
| MySQL writes per successful login | 4–7 (`users.last_login`, `users.session_id`, `user_token` count/insert/update, `companies.last_login` potentially) | 1 (`users.last_login` only) | −3 to −6 MySQL writes |
| SQL queries per successful login | 5–10 | 1 (`Auth::attempt` only — `company_type` from Redis) | −4 to −9 queries |
| Captcha GD renders per login-page view | 1 | 0 (cached PNG) or moved client-side | −1 GD render |
| Auth lookups per login attempt | 2 (peek + Auth::attempt) | **1** (`Auth::attempt` directly — no `Auth::validate` pre-check) | −1 lookup |
| Rate-limit check per login | none | 1 Redis hit | +1 Redis hit (necessary) |
| Brute-force attempts per IP per minute | **Unlimited** | 5 | Capped |
| Open-redirect attack surface | Open | Closed | Eliminated |
| CSP / security headers | 0 | 5 | New |

---

## 4. Architecture

### Current state

```
HTTP /login
    ↓
routes/web.php → LoginController
    ↓
Controller::__construct()
    ├─ showInstall()         (Envato verify)
    ├─ checkMigrateStatus()  (Artisan::call 'migrate:check' — PER REQUEST)
    ├─ GlobalSetting::first() → $this->global
    ├─ SmsSetting::first()
    ├─ ThemeSetting::whereNull('company_id')->first()
    ├─ view()->share('smsSettings', ...)
    └─ middleware: $this->user = auth()->user(); if (superadmin) config froiden_envato
    
LoginController::__construct()
    ├─ parent::__construct()      ← re-runs all the above
    ├─ $this->middleware('guest')->except('logout')
    ├─ App::setLocale($this->global->locale)
    ├─ Artisan::call('route:clear')   ← per-request
    └─ Artisan::call('cache:clear')   ← per-request, flushes all Redis

LoginController::showLoginForm()  (called by GET /login)
    ├─ Artisan::call('route:clear')   ← again
    ├─ Artisan::call('cache:clear')   ← again
    ├─ if (auth()->check()) {
    │     $companyRow = DB::table('companies')->where('id', auth()->user()->company_id)->first();
    │     $companyRowRedirect = DB::table('companies')->join(...) // 4-table join
    │     // hardcoded if/else on company_type 1/2/3
    │ }
    ├─ ThemeSetting::whereNull('company_id')->first()   (re-fetched, uncached)
    ├─ FrontCmsHeader::first()                          (re-fetched, uncached)
    └─ view('auth.login', [...])

HomeController::generate()  (called by GET /captcha)
    ├─ imagecreate(160, 50)
    ├─ imagecolorallocate ×2
    ├─ imagettftext (loads DejaVuSerif-Italic.ttf from disk)
    ├─ 80 × imagesetpixel (noise)
    ├─ imagepng
    ├─ Session::put('captcha_code', $captcha)  ← plain text, not hashed
    └─ Session::put('captcha_expires', now()->addMinutes(2))

LoginController::login()  (called by POST /login)
    ├─ $request->validate(['captcha' => 'required|digits:4'])
    ├─ session('captcha_code') check (plain compare)
    ├─ (super-code path) decrypt + bypass
    ├─ (toc=1 userId/companyid path) bcrypt + DB::table('users')->update(...) + ES sync
    ├─ User::where('email', ...)->first()  ← lookup 1
    ├─ if (Hash::check) Session::put('open_url', $request->url)   ← OPEN REDIRECT
    ├─ Auth::attempt($credentials)          ← lookup 2 + auth
    ├─ $user->status !== 'active' check
    ├─ $user->last_login = Carbon::now(); $user->save()   ← sync MySQL write
    ├─ ES exists → ES update refresh:true   ← ES round-trip 1, 2
    ├─ ES exists → ES update refresh:true   ← ES round-trip 3, 4
    ├─ (duplicate in authenticated() hook)
    │     $companyRow = DB::table('companies')->where('id', ...)->first()
    │     $companyRowRedirect = DB::table('companies')->join(...)  // 4-table join
    │     $Company::findOrFail → $Company->last_login = ...; $Company->save()
    │     DB::table('users')->where('id', $user_id)->update(['remember_token' => ''])  // sync write
    │     ES exists → ES update refresh:true  ← duplicate ES path
    │     // hardcoded if/else on company_type 1/2/3 (all go to service_company)
    ├─ redirectTo()
    │     DB::table('user_token')->select('id')->where('user_id', $user_id)->count()  // sync read
    │     DB::table('user_token')->where('user_id', ...)->update(['token' => ...])  // sync write
    │     // hardcoded if/else on company_type 1/2/3 (5 SQL queries here)
    └─ return redirect(...)

Logout:  GET /logout OR POST /logout
    ├─ auth()->logout()
    ├─ $user->session_id = null; $user->save()  ← sync MySQL write
    └─ $request->session()->invalidate()
```

### After (V2) state

```
HTTP /login
    ↓
SecurityHeaders middleware                          ← NEW: CSP, X-Frame-Options, etc.
    ↓
routes/web.php → LoginController (thin wrapper)
    ↓
Controller::__construct() (no Artisan::call)
    └─ (skip) checkMigrateStatus — moved to scheduler / CI
    
LoginController::showLoginForm()
    ├─ if (auth()->check()) → return TenantRouter::resolve($user)  // 1 Redis hit for company_type
    ├─ ThemeSetting: Redis-cached (1 h TTL)
    ├─ FrontCmsHeader: Redis-cached (1 h TTL)
    └─ view('auth.login', [...])

GET /captcha → CaptchaController@generate           ← NEW tiny controller (~50 LOC)
    ├─ Redis::get("captcha:{sessId}")  ← cache hit → return PNG bytes + Cache-Control header
    ├─ (cache miss) imagecreate + imagepng → Redis::put(... 2 min)
    └─ Session::put('captcha_code', hash('sha256', $captcha))

POST /login → throttle:5,1 middleware                ← NEW
    ↓
LoginController::login() (or AttemptLoginAction)
    ├─ $request->validate([...])
    ├─ hash_equals(session('captcha_code'), hash('sha256', $request->captcha))   ← hashed compare
    ├─ super-code: validate + bypass
    ├─ Auth::attempt($credentials) — single users lookup (no `Auth::validate` pre-check)
    │    └─ on success: if (user->status !== 'active') Auth::logout() + throw
    ├─ DeviceSessionManager::enforce($user, session()->getId())  ← Redis-only single-device (1 GET + 0..1 destroy + 1 SETEX)
    ├─ $user->last_login = now(); $user->save()    ← single sync MySQL write
    ├─ RecordLastLoginEsJob::dispatch($user->id, $user->company_id)  ← queued, async
    ├─ Cache::put("user_token:{id}", $token, 86400)   ← Redis, no MySQL
    ├─ OpenRedirect::sanitize($request->input('url')) → Session::put('open_url', $url)  ← safe redirect target
    └─ return redirect(TenantRouter::resolve($user))

POST /logout
    ├─ Cache::forget("user:session:{id}")          ← Redis only
    └─ $request->session()->invalidate()

GET /captcha → Cache-Control: private, max-age=120, PNG bytes from Redis
```

`RecordLastLoginEsJob::handle()` does **1 bulk ES round-trip**:
```php
$this->elasticService->getClient()->bulk([
    'body' => [
        ['update' => ['_index' => ELASTIC_USERS_LIST,    '_id' => $userId,    'retry_on_conflict' => 5]],
        ['doc' => ['last_login' => $timestamp], 'doc_as_upsert' => true],
        ['update' => ['_index' => ELASTIC_COMPANIES_INDEX, '_id' => $companyId, 'retry_on_conflict' => 5]],
        ['doc' => ['last_login' => $timestamp], 'doc_as_upsert' => true],
    ],
]);
```

---

## 5. Database & Elasticsearch

| Resource | Current | After (V2) |
|---|---|---|
| MySQL writes per login | `users.last_login`, `users.session_id`, `user_token` count/insert/update, `companies.last_login`, possibly `users.remember_token` reset, plus bcrypt update for team-invite `toc=1` path | `users.last_login` only (other writes moved to Redis or kept only on `toc=1` invite) |
| MySQL writes per logout | `users.session_id = null` (sync) | 0 |
| MySQL writes per request (`EnsureOneDeviceLogin` if wired) | 1 (`users.session_id` update on every authenticated page load) | 0 (Redis `SETNX`) |
| MySQL queries per login | 5–10 raw `DB::table()` (companies, clients, client_location, td_industries, user_token, plus the Eloquent user lookup) | 1–2 (`Auth::attempt` + cached `company_type` via Redis) |
| ES round-trips per login | 3 sync (exists + 2 updates, both with `refresh:true`) + duplicate 1 update in `authenticated()` hook | 0 sync; **1 async bulk** call via queued job — both indices updated in one HTTP request with `doc_as_upsert => true` (no `exists()` pre-check) and `retry_on_conflict => 5` |
| `user_token` table | INSERT/UPDATE on every login | Redis: `Cache::put("user_token:{id}", $token, 86400)` |
| `sessions` table | Used by `LogSuccessfulLogin` listener (unwired, but if wired would sync delete) | Redis-only session storage (already configured via `SESSION_DRIVER=redis` in `.env`); no MySQL session writes |
| Schema changes | n/a | None (per rule #8) |

---

## 6. File surface

| File | Status current | Status after (V2) |
|---|---|---|
| `app/Http/Controllers/Auth/LoginController.php` (825 LOC) | God controller, 3 flows mixed | Thin controller (~200 LOC) delegating to `AttemptLoginAction`, `ShowLoginFormAction`, `LogoutAction` |
| `app/Http/Controllers/Auth/CustomloginController.php` (295 LOC) | Dead (not wired) | **Deleted** |
| `app/Http/Controllers/Auth/LoginRequest.php` | Unused | Wired into `AttemptLoginAction` |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Dead (Breeze stub) | **Deleted** |
| `app/Http/Controllers/Auth/PasswordResetLinkController.php` | Dead (Breeze stub) | **Deleted** |
| `app/Http/Controllers/Auth/NewPasswordController.php` | Dead (Breeze stub) | **Deleted** |
| `app/Http/Controllers/Auth/ConfirmablePasswordController.php` | Dead (Breeze stub) | **Deleted** |
| `app/Http/Controllers/Auth/EmailVerificationPromptController.php` | Dead (Breeze stub) | **Deleted** |
| `app/Http/Controllers/Auth/EmailVerificationNotificationController.php` | Dead (Breeze stub) | **Deleted** |
| `app/Http/Controllers/Auth/VerifyEmailController.php` | Dead (Breeze stub) | **Deleted** |
| `app/Http/Controllers/Auth/PasswordController.php` | Dead (Breeze stub) | **Deleted** |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Dead (Breeze stub) | **Deleted** |
| `routes/auth.php` | Registered in file system but **never loaded** | **Deleted** |
| `app/Http/Middleware/VerifyCsrfToken.php` | Dead (Laravel 11 inlines CSRF) | **Deleted** (preserving exemptions in `bootstrap/app.php` if needed) |
| `app/Http/Controllers/Auth/CaptchaController.php` | Doesn't exist | **New** (~50 LOC) |
| `app/Actions/Auth/AttemptLoginAction.php` | Doesn't exist | **New** |
| `app/Actions/Auth/ShowLoginFormAction.php` | Doesn't exist | **New** |
| `app/Actions/Auth/LogoutAction.php` | Doesn't exist | **New** |
| `app/Support/TenantRouter.php` | Doesn't exist | **New** (single source for `company_type` → dashboard route) |
| `app/Support/TenantLookup.php` | Doesn't exist | **New** (Redis-cached company lookups) |
| `app/Jobs/Auth/RecordLastLoginEsJob.php` | Doesn't exist | **New** (`ShouldQueue`, `low` priority) |
| `app/Http/Middleware/SecurityHeaders.php` | Doesn't exist | **New** (CSP, X-Frame-Options, HSTS, etc.) |
| `app/Listeners/LogSuccessfulLogin.php` | Exists, unwired | **Deleted** (subsumed by Redis `SETNX`) |
| `app/Http/Middleware/EnsureOneDeviceLogin.php` | Exists, alias declared but never applied | Rewritten — Redis-only, registered on `web` group |
| `app/Http/Middleware/RedirectIfAuthenticated.php` | Has string-return + `exit` bugs | Fixed via `TenantRouter::resolve` |
| `app/Http/Controllers/Controller.php` | Runs `Artisan::call('migrate:check')` per request | Removed; scheduler / CI/CD handles it |
| `app/Http/Controllers/HomeController.php` (`generate()`) | Captcha lives in 16,700-line god controller | Captcha extracted to `CaptchaController` |
| `app/Providers/AppServiceProvider.php` | Defines `resume-presign`, `resume-finalize`, `resume-status` rate limiters | Adds named `login` rate limiter |
| `resources/views/auth/login.blade.php` | 301 LOC, 28px/22px headings (UI violation), `{!! !!}` XSS, `autocomplete="off"` | UI-compliant: ≤16px, escaped `{{ }}`, `autocomplete="username"` / `"current-password"`, wrapped in `.section-card` |
| `resources/views/auth/superadmin-login.blade.php` | Same UI/XSS issues | UI-compliant |
| `routes/web.php` | Has GET `/logout` (CSRF gap); no `throttle:` on login; `url` param passed raw to redirect | POST `/logout` only; `throttle:5,1` on login POST; `url` param allow-list |
| `bootstrap/app.php` | 8 middleware aliases; no `SecurityHeaders`; no `EnsureOneDeviceLogin` on `web` group | Adds `SecurityHeaders` globally; registers `EnsureOneDeviceLogin` (Redis-only) on `web` |

---

## 7. Security posture

| Concern | Current | After (V2) |
|---|---|---|
| Login brute-force defence | None — unlimited attempts | `throttle:5,1` on route + `LoginRequest::ensureIsNotRateLimited()` |
| Open redirect via `?url=` | **Open** — `Session::put('open_url', $request->url)` → `redirect($url)` | Closed — `OpenRedirect::sanitize()` blocks `https://evil.com`, `//evil.com` (protocol-relative), `/\evil.com` (backslash trick), `javascript:`/`data:`/`vbscript:` schemes, and `https://jero.works@evil.com` (userinfo). Only relative paths under `app.url` survive. |
| Logout CSRF | **Bypassable via GET `/logout`** | POST only |
| Captcha brute-force ceiling | 9,999 (4-digit numeric) | Alphanumeric + hashed + longer TTL + route throttle |
| CSP / X-Frame-Options / HSTS | **None** | `SecurityHeaders` middleware |
| Clickjacking protection | None | `X-Frame-Options: DENY` |
| MIME sniffing | None | `X-Content-Type-Options: nosniff` |
| Mixed-content / HTTPS pinning | None | `Strict-Transport-Security: max-age=31536000` |
| Referrer leakage | None | `Referrer-Policy: strict-origin-when-cross-origin` |
| XSS in login flash | `{!! @$messsage !!}` unescaped | `{{ @$messsage }}` escaped |
| Password manager auto-fill | Broken (`autocomplete="off"`) | `autocomplete="username"` / `"current-password"` |
| Single-device invariant | `EnsureOneDeviceLogin` (declared, never wired) — would write `users.session_id` MySQL on every request if wired | Redis `SETNX device:user:{id} {sess}` — zero MySQL writes |
| Session fixation | Laravel defaults | Same + Redis-only sessions (already configured) |
| `bcrypt` cost | Laravel default (12) | Unchanged |
| Token storage (CSRF + remember) | Mixed | Unchanged |

---

## 8. UI / UX compliance

| Element | Current | After (V2) |
|---|---|---|
| Welcome heading size | `fs-2qx` (28 px) — **violates UI_UX_STANDARDS §2 (16 px max)** | `fs-3` (16 px) |
| Description size | `fs-2` (22 px) — **violates §2** | `fs-4` (14 px) |
| Form card container | Raw on `#eef2f7` page background — **violates §10** | `.section-card` wrapper with header icon |
| `autocomplete` on email/password | `autocomplete="off"` — breaks password managers | `autocomplete="username"` / `"current-password"` |
| Flash message rendering | `{!! @$messsage !!}` (unescaped) — XSS | `{{ @$messsage }}` |
| Inline `<script>` after `@endsection` | `refreshCaptcha()` lives below the section | Moved to `@push('scripts')` |
| Status messages in alert-danger divs | Inline repeated in both views | Extracted to `<x-auth-flash-banner>` component (optional) |
| Social-login buttons (Google, Facebook, Apple) | Rendered but `display:none` — dead markup | Removed entirely (not implemented in this scope) |
| Login button state on submit | Has `indicator-progress` for spinner but no JS to swap label/spinner | (Out of scope — current works) |
| Captcha refresh button | Inline `onclick="refreshCaptcha()"` | Same; works |
| Responsive (1366×768, 1920×1080, 4K) | Metronic classes used | Same + verified post-refactor |

---

## 9. Observability

| Aspect | Current | After (V2) |
|---|---|---|
| Login attempt log | None — silent success/fail | Structured `login_attempts` log channel: `{email, ip, user_agent, success, captcha_ok, throttle_hit, latency_ms}` |
| Sentry / OpenTelemetry | Not configured | Out of scope here (would be a separate observability initiative) |
| `last_login` in DB | Updated on every login (sync MySQL write) | Same |
| `last_login` in ES | Updated on every login (3 sync ES round-trips with `refresh:true`) | Updated async via queued job (1 bulk call, default refresh) |
| Captcha failures | Silent | Logged |
| Throttle hits | Silent | Logged |
| Login latency (P50/P99) | Not measured | Captured per-request, logged to `login_attempts` channel |
| Failed-login counter | Not tracked | Tracked via log channel; can drive anomaly detection later |

---

## 10. Code metrics

| Metric | Current | After (V2) | Δ |
|---|---|---|---|
| `LoginController` LOC | 825 | ~200 | −625 |
| Total LOC in `app/Http/Controllers/Auth/*` | ~1,800 (incl. 11 dead controllers) | ~250 (login + captcha + password-reset + register) | −1,550 |
| Dead `routes/auth.php` | 60 LOC | 0 | −60 |
| Dead `VerifyCsrfToken.php` | 25 LOC | 0 | −25 |
| New code added (Actions, Support, Job, Captcha, SecurityHeaders) | 0 | ~600 LOC (well-tested, single-purpose) | +600 |
| Net LOC change | — | — | −1,000 to −1,500 |
| `company_type` SQL queries per login | 5+ | 0 (Redis) | −5+ |
| Sync DB writes per login | 4–7 | 1 | −3 to −6 |
| ES round-trips per login | 3 sync, `refresh:true` | 0 sync, 1 async bulk (with `doc_as_upsert`) | −3 sync |
| Login-page Artisan subprocesses | 3 per request | 0 | −3 |
| Login-page cache flushes | 1 per request (full) | 0 | −1 |

---

## 11. Behavioural guarantees

The V2 implementation preserves all existing business logic. Specifically:

| Behaviour | Preserved? |
|---|---|
| Captcha required on login (numeric 4-digit → alpha-numeric hashed) | Yes — equivalent; stronger |
| Super-admin bypass via `super_code` | Yes — same code path |
| Team-invite `toc=1` first-login flow (`userId`/`companyid`/`toc=1` → bcrypt + coins deduction + status='active') | Yes — same code path; will be moved into `AttemptLoginAction` but logic unchanged |
| Tenant redirect map (company_type 1/2/3 → `service_company/dashboard`; superadmin → `superadmin.dashboard.index`) | Yes — centralised in `TenantRouter` |
| Profile-edit redirect on first login (`remember_token === user_id`) | Yes — preserved in `TenantRouter` or action |
| Logout invalidates session | Yes — plus Redis `device:user:{id}` cleanup |
| Single-device enforcement | Yes — Redis-only instead of MySQL-only |
| Forgot/Reset password flows | Unchanged |
| Registration | Unchanged |
| Login attempt rate-limit (5/min in `LoginRequest`) | Yes — now actually wired |

---

## 12. Out-of-scope (preserved, not touched)

- Business logic of tenant redirect mapping (company_type → dashboard)
- Captcha difficulty policy (numeric vs alpha-numeric is a security trade-off, not a logic change)
- Password rules (Laravel default + bcrypt cost 12)
- Social login (currently hidden `display:none` — out of scope)
- 2FA / OTP / SMS verification on login (not in current scope; would be a separate feature)
- Database schema (per rule #8 — no migrations)
- `hire.jero.works` work (per CLAUDE.md)
- Existing password reset / registration controllers (not in audit scope)

---

## 13. Rollback strategy per layer

| Layer | Rollback |
|---|---|
| `Artisan::call` removal | `git revert` single commit |
| `migrate:check` removal | `git revert` + ensure CI/CD runs migrations |
| `throttle:5,1` | Drop the middleware parameter from route |
| Open-redirect fix | Restore original `Session::put('open_url', $request->url)` |
| GET `/logout` removal | Re-add the route line |
| Captcha cache | Restore direct GD render |
| ES `last_login` queue | Restore inline ES writes; remove job dispatch |
| `company_type` cache | Restore raw `DB::table('companies')` queries |
| `Auth::validate` swap (rejected) | N/A — replaced with single `Auth::attempt()` call (no pre-check) |
| `TenantRouter` extraction | Restore inline `if/else` (single big method) |
| Single-device Redis | Restore MySQL `users.session_id` writes |
| `SecurityHeaders` middleware | Drop from `bootstrap/app.php` aliases |
| UI compliance | Revert view files |
| Dead-code removal | `git revert` (largest blast radius if deps reintroduced) |

---

## 14. Summary scorecard

| Capability | Current (1.0x = worst) | After (target) |
|---|---|---|
| Login page render time | 1.0x (baseline) | ~0.4x (~60% faster) |
| Login POST latency (P99) | 1.0x | ~0.5x |
| MySQL writes per login | 4–7 | 1 |
| ES round-trips per login | 3 sync + 1 duplicate | 0 sync + 1 async bulk (with `doc_as_upsert`) |
| Brute-force resistance | None | Strong (5/min + captcha + hashed) |
| Open-redirect exposure | Open | Closed |
| CSP / security headers | 0 / 5 | 5 / 5 |
| Dead code in auth/ | ~1,400 LOC | 0 LOC |
| Behavioural parity | (baseline) | 100% (same outcomes, faster) |
| LOC in `LoginController` | 825 | ~200 |
| Net LOC reduction | — | ~1,000–1,500 lines removed |
| Time to ship | — | ~10 dev-days, 2 sprints |

---

## 15. Sign-off criteria (matching Implementation Plan Phase 6)

- [ ] Login page P50 < 200 ms; P99 < 800 ms (10 K concurrent, k6)
- [ ] Login POST P50 < 500 ms; P99 < 1.5 s (10 K concurrent)
- [ ] MySQL CPU < 60 %; ES CPU < 50 %; Redis CPU < 40 % at 10 K concurrent
- [ ] Brute-force ceiling capped at 5 attempts/min/IP+email (returns 429)
- [ ] Open-redirect attempt stays internal
- [ ] `company_type` Redis cache hit rate > 95 % in production
- [ ] No `Artisan::call` invocations on login request path
- [ ] No `users.session_id` MySQL writes
- [ ] All Phase 6 functional tests pass
- [ ] No regressions on existing login flows (snapshot tests)
- [ ] CSP report-only mode green for 7 days before enforcing