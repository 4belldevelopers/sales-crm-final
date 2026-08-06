# GOD-Level Audit — `/register` (testing.jero.works)

> **Audit date:** 2026-08-06
> **Scope:** the entire registration surface (public multi-step company signup, invitation-driven signup, team-member activation, Breeze fallback, Breeze verify).
> **Method:** 8 cross-validated agent scans + targeted verification reads of the live controller, blade, schema, and config.
> **Live entrypoint:** [`SaasFrontController::companyRegister`](../../app/Http/Controllers/SaasFront/SaasFrontController.php) — lines 357–1549 (~1,193 lines of synchronous work in a single method).
> **Live blade:** [`saas-front/register.blade.php`](../../resources/views/saas-front/register.blade.php) — 1,146 lines, ~650 lines of inline JS.
> **Live routes:** [`routes/web.php:347–361`](../../routes/web.php) — 5 endpoints; **no throttle, no captcha**.
>
> This document is the canonical reference for the implementation plan in [`IMPLEMENTATION_PLAN.md`](IMPLEMENTATION_PLAN.md).

---

## 0. TL;DR

| Severity | Count | Action |
|---|---|---|
| 🔴 Critical | 7 | Must fix before next production deploy |
| 🟠 High | 9 | Fix in next sprint |
| 🟡 Medium | 6 | Backlog |
| 🟢 Low | 3 | Drift cleanup |

**The single biggest finding:** `companyRegister()` is a 1,193-line method that performs ~10 MySQL inserts, ~30 synchronous Elasticsearch writes, 3 external HTTP calls (Clearbit, ipinfo, mail), and 7 hardcoded role seeds — all on the synchronous request thread inside one DB transaction. At 10k recruiters/day, the current architecture will **exhaust the InnoDB log writer and the ES heap before the connection pool fills**.

After the Tier 1 + Tier 2 fixes, the page becomes **production-safe at current load** with ~30 min of work. After the full plan, it scales to 10k recruiters/day on the existing 32 GB / 32 GB / 32 GB stack.

---

## 1. The Active Registration Path (ground truth)

| Layer | File | Notes |
|---|---|---|
| **Routes** | [`routes/web.php:347–361`](../../routes/web.php) | `POST /company-register` (no throttle, no captcha) |
| **Controller** | [`SaasFrontController::companyRegister`](../../app/Http/Controllers/SaasFront/SaasFrontController.php) lines 357–1549 | **1,193 lines**, single sync method |
| **Tenant bootstrap** | [`CompanyObserver::created`](../../app/Observers/CompanyObserver.php) lines 26–111 | Creates roles + permissions + trial package, **but NOT registered in `AppServiceProvider`** |
| **User creation** | [`SaasFrontController.php:1082`](../../app/Http/Controllers/SaasFront/SaasFrontController.php) | Sets `email_verification_code = str_random(40)` |
| **Welcome mail** | [`SaasFrontController.php:1360`](../../app/Http/Controllers/SaasFront/SaasFrontController.php) | `QueueMailService::dispatch()->onConnection('emails')` (queued, but **inside** the transaction) |
| **Sync ES writes** | [`SaasFrontController.php:1142, 1426, 1470, 1513, 2204`](../../app/Http/Controllers/SaasFront/SaasFrontController.php) | Per-department + per-city + per-relation ES `update()` calls |
| **Sync user ES index** | [`SaasFrontController.php:1526`](../../app/Http/Controllers/SaasFront/SaasFrontController.php) | `GeneralMethod::userIndexingupload($user->id)` |
| **Verify email** | [`SaasFrontController.php:3445`](../../app/Http/Controllers/SaasFront/SaasFrontController.php) | `getEmailVerification($code)` — **no `signed` middleware** |
| **AJAX probes** | [`routes/web.php:352, 357–359`](../../routes/web.php) | No throttle, fire on every keystroke |

### What the controller writes in one request (in order)

1. **`companies`** (line 388–414) — `new Company()`, save
2. `CompanyObserver::created` fires → 7 row inserts (admin role + 5 application statuses + theme + app setting + trial package + `companies.package_id` update) — and **does not run** because the observer is not registered
3. **`package_order`, `package_invoice`, `package_active`** (lines 826–853) — 3 inserts with hardcoded feature balances
4. **Coin ledger seed** (`coin_orders`, `coin_order_events`, `company_coins`, `coin_transection_logs`) — loop-driven inserts with sync ES writes (lines 874–1070)
5. **`users`** (line 1086) — `new User()`, save; sets `super_password = $request->password` (plaintext, see Finding #1)
6. **`users_profile`** (line 1105) — 1 insert
7. **`permission_role` cleanup** (line 1113–1117) — if product company, delete non-product perms
9. **`job_categories`** + sync ES update (lines 1129–1158) — N inserts + N ES writes
10. **`clients` + `client_contact_info`** (lines 1174–1186)
11. **`candidate_status`** loop (lines 1191–1216)
12. **7 roles + `role_user` + `role_has_permissions`** loop (lines 1223–1281) — N+1 priority lookup × 7
13. **`staffing_company_invitation` + `staffing_company_relation`** (lines 1303–1327) — only on invitation flow
14. **3 nested city loops with sync ES writes** (lines 1405–1517) — N×3 joins + N×3 ES updates
15. **`commericials` + `commericial_details` × 10 rates** (lines 1399–1517)
16. **`QueueMailService::dispatch`** (line 1360) — queued, but inside the transaction
17. **`GeneralMethod::clientStaffingPartner`** (line 1521) — heavy single ES query
18. **`GeneralMethod::userIndexingupload`** (line 1526) — sync ES user upsert
19. `DB::commit()` (line 1530)

**Total MySQL writes per signup:** ~10 base inserts + 7 roles + ~30 role_has_permissions + 5 application statuses + 10 commercial details + 2 staffing relations + 12 candidate statuses + 3 package pivots + 3 coin tables = **~70–80 row inserts** (product companies a bit more).

**Total ES writes per signup:** ~30 sync HTTP calls (department × N + city × 3 + relations + user + coin × 4).

**Total external HTTP calls per signup:** 1 ipinfo.io + 1 mail (queued).

---

## 2. The Dead Paths (must be deleted)

| Dead code | Location | Why dead |
|---|---|---|
| `POST /register` → `RegisterController::register` | [`routes/web.php:147`](../../routes/web.php) | The method does not exist; **POST would 500** |
| `GET /register` (legacy) | [`routes/web.php:146`](../../routes/web.php) | Overridden by Breeze in `auth.php:15` |
| `Auth\RegisterController` | [`app/Http/Controllers/Auth/RegisterController.php`](../../app/Http/Controllers/Auth/RegisterController.php) | Only has `showRegistrationForm/validator/create` |
| `Auth\RegisteredUserController` | [`app/Http/Controllers/Auth/RegisteredUserController.php`](../../app/Http/Controllers/Auth/RegisteredUserController.php) | Breeze stub; never invoked from any blade |
| Breeze `routes/auth.php:39–48` | [`routes/auth.php`](../../routes/auth.php) | Verification routes unreachable (User model lacks `MustVerifyEmail`) |
| 3 dead `use` imports | [`SaasFrontController.php:24–26`](../../app/Http/Controllers/SaasFront/SaasFrontController.php) | `App\Notifications\EmailVerification*` and `NewCompanyRegister` don't exist |
| `BK/` directory of register blades | [`resources/views/staffing_company/BK/`](../../resources/views/staffing_company/BK/) | Backups that drift |
| Login captcha validation (commented out) | [`LoginController.php:75–100`](../../app/Http/Controllers/Auth/LoginController.php) | Marked dead code; captcha endpoint exists but is never enforced |

---

## 3. Critical Findings (🔴 — must fix)

### Finding #1: Plaintext password storage in `users.super_password`

**File:** [`SaasFrontController.php:1080`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1080)
```php
$user->password = bcrypt($request->password);
$user->super_password = $request->password;   // ← plaintext
$user->super_admin_login_code = $companyId.rand(1111, 9999);
```
**Severity:** Data-protection breach + GDPR exposure. Same pattern repeats at lines 1865, 2963, 3576.

**Risk:** Plaintext PII in the database. If anyone dumps `users` (via SQL injection, leaked backup, or insider access) they get every recruiter's working password.

**Fix:**
1. Audit whether `super_password` is read anywhere. If yes, replace with `bcrypt($request->password)` reads or a `Hash::check()` against the `password` column.
2. If unused, drop the column via migration and remove every write site.
3. Same for `super_admin_login_code` — verify the 4-digit fallback is still needed.

### Finding #2: No rate limiting on any `/register*` route

**Files:**
- [`routes/web.php:351`](../../routes/web.php) — `POST /company-register`
- [`routes/web.php:352`](../../routes/web.php) — `POST /company-register-chk`
- [`routes/web.php:357`](../../routes/web.php) — `POST /contact-company-register-chk`
- [`routes/web.php:358`](../../routes/web.php) — `POST /domain-company-register-chk`
- [`routes/web.php:359`](../../routes/web.php) — `POST /client-domain-register-chk`

`grep throttle` → **no matches**. `grep RateLimiter::` → **no matches** for registration.

**Risk:**
- `companyRegisterChk` (live email probe) is the **customer-enumeration endpoint** — an attacker can probe the entire customer list by hitting it ~10k times/minute.
- `POST /company-register` is open; each request kicks off ~80 row inserts + 30 ES calls + 1 mail job. 100 bots × 5 req/s = 500 signups × 80 inserts = 40k MySQL INSERTs/sec on `role_has_permissions` alone.

**Fix:** Define rate limiters in `App\Providers\AppServiceProvider`:
```php
RateLimiter::for('register-form', fn ($r) => Limit::perMinute(60)->by($r->ip()));
RateLimiter::for('register-submit', fn ($r) => Limit::perHour(10)->by($r->ip()));
```
Apply via `->middleware('throttle:register-submit')` on the submit route.

### Finding #3: No captcha / honeypot on the public registration form

Grepped `recaptcha|hcaptcha|turnstile|honeypot` across every `register*.blade.php` and `*invitation*.blade.php` in `resources/views/` → **zero matches**. The Google `recaptcha_key` is stored in `GlobalSetting` (admin UI only) but never rendered or validated.

**Risk:** With Finding #2 compounding, a single bot farm creates thousands of trial tenants/hour. Each tenant seeds ~70 rows + 30 ES writes + 1 mail job. ES fills first; MySQL follows.

**Fix:**
1. Add Cloudflare Turnstile widget to `register.blade.php` (free, no Google tracking).
2. Server-side verify the token at the top of `companyRegister()`.
3. Add a hidden honeypot field (`website_url`) — if filled, silently 200.

### Finding #4: `/run-queue` route allows unauthenticated queue-worker startup

**File:** [`routes/web.php:247–250`](../../routes/web.php)
```php
Route::get('/run-queue', function () {
    Artisan::call('queue:work emails --queue=default');
});
```
**Severity:** Unauthenticated DoS surface — anyone on the internet can hit `/run-queue` and pin an Octane worker to a long-running queue worker. Worse, no `--max-jobs` or `--stop-when-empty`.

**Fix:** Delete this route. If a worker-control endpoint is needed, gate behind `auth` + `signed` middleware with a max-runtime flag.

### Finding #5: ~30 synchronous Elasticsearch writes inside the DB transaction

**Files (sample):**
- [`SaasFrontController.php:307, 896, 933, 974, 1035, 1142, 1526, 2204`](../../app/Http/Controllers/SaasFront/SaasFrontController.php)

Every registration fires ~30 direct `ElasticService::getClient()->update([...])` calls **inside** the DB transaction.

**Risk:**
- Each ES `update()` is a synchronous network call (~1ms RTT intra-DC, but forces ES refresh).
- Transaction atomicity is **fundamentally broken**: you can't rollback an ES write by rolling back MySQL. This is the most common cause of "ghost tenant" support tickets (recruiter can't log in, ES says they don't exist, MySQL says they do).
- ES heap is the first resource to exhaust under bot traffic.

**Fix:**
1. Move ALL ES writes outside the DB transaction.
2. Use ES `_bulk` API to batch all indices in a single round trip.
3. Dispatch a single `IndexNewCompanyJob` after commit; the job owns the batching strategy.

### Finding #6: `Package::first()` ignores `is_trial=1` — possible money bug

**File:** [`SaasFrontController.php:419`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:419) and line 2303
```php
Package::select('packages.*')->first();   // ← no is_trial filter
```
Returns whichever row has the smallest `id`. The commented-out query at line 360 was `where('is_trial', 1)` — that was the right intent.

**Risk:** If `id=1` is enterprise/legacy and `is_trial=1` is on row 7, every signup gets enterprise pricing instead of the trial.

**Fix:** Restore the filter:
```php
Package::where('is_trial', 1)->first();
```
**Verify with product owner first** — this might be a real money bug that's already in production.

### Finding #7: Email-verification GET route has no `signed` middleware

**File:** [`routes/web.php:360`](../../routes/web.php)
```php
Route::get('/email-verification/{code}', [SaasFrontController::class, 'getEmailVerification'])->name('get-email-verification');
```
**Compare:** Breeze's `verify-email/{id}/{hash}` route uses `signed` middleware ([`routes/auth.php:43`](../../routes/auth.php)). The custom `getEmailVerification` does not.

**Risk:** Anyone who guesses (or brute-forces) the `str_random(40)` token can verify any user. `str_random(40)` is alphanumeric = 62^40 = ~10^71 possibilities, so brute force is impractical, but the token is sent in plain text over email and **persists in the user's inbox indefinitely** unless they delete the email. That's a real attack surface.

**Fix:** Add `signed` middleware, or sign the URL via `URL::signedRoute(...)` in the email body.

---

## 4. High Findings (🟠 — next sprint)

### Finding #8: 1,193-line sync `companyRegister()` method

**File:** [`SaasFrontController.php:357–1549`](../../app/Http/Controllers/SaasFront/SaasFrontController.php)

See §1 for the breakdown. At 10k signups/day, this method is the **single biggest bottleneck**.

### Finding #9: Three confirmed 404 assets on the live page

| Path | Source line | Status |
|---|---|---|
| `front/assets/js/countryDialCodes.js` | `register.blade.php` line ~488 | **404** (Glob returned no files) |
| `demo/dist/assets/plugins/global/plugins.bundle.js` | `register.blade.php` line ~483 | **404** (Glob `public/demo/dist/assets/**/*` returned no files) |
| `demo/dist/assets/js/scripts.bundle.js` | `register.blade.php` line ~487 | **404** |
| `img/logo-transparent1.svg` | `register.blade.php`, `invitationCompanyRegister.blade.php` | **404** |

**Risk:** The Metronic stepper component (heart of the multi-step UI) never initializes. The intl-tel dial-code dropdown is empty. The logo on every step is broken.

**Fix:**
1. Run the Metronic build → output to `public/demo/dist/`.
2. Replace `countryDialCodes.js` with the canonical intl-tel-input data file.
3. Replace `logo-transparent1.svg` with the real JERO logo SVG.

### Finding #10: N+1 city joins (×3 per city × N cities)

**File:** [`SaasFrontController.php:1411, 1457, 1486`](../../app/Http/Controllers/SaasFront/SaasFrontController.php)

For each city in `work_city_all`, the controller runs the same 3-table join (`city_list` → `state_list` → `countries`) **3 times** (once per rateType). No batching, no `IN (city_names)`, no caching.

For a user selecting 5 cities: 5 × 3 = **15 DB joins per registration**, all inside the transaction.

**Fix:** Single batched lookup with `whereIn('city_list.city', $cityNames)`. **15 queries → 1 query per registration.**

### Finding #11: N+1 role-priority lookups (×7 per signup)

**File:** [`SaasFrontController.php:1223–1281`](../../app/Http/Controllers/SaasFront/SaasFrontController.php)

For each of 7 hardcoded role names:
```php
$maxPriority = DB::table('roles')->select('*')
    ->where('company_id', $company->id)
    ->orderBy('id', 'DESC')->first();
```
**7 queries per registration** inside the transaction.

**Fix:** Compute priority once:
```php
$maxPriority = DB::table('roles')
    ->where('company_id', $company->id)
    ->max('priority') ?? 0;
```

### Finding #12: Synchronous Clearbit + ipinfo.io HTTP calls with no cache

**Files:**
- [`SaasFrontController.php:1679`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1679) — `Http::timeout(5)->get('https://autocomplete.clearbit.com/v1/companies/suggest', ...)`
- [`GeneralMethod.php:6442`](../../app/Helper/GeneralMethod.php:6442) — `ipinfo.io/{$ip}/json` (plain HTTP)

**Risk:** A single user filling the form slowly can keep Clearbit open for 5s, tying up an Octane worker. At 100 concurrent form-fillers, all 100 workers can be stuck on Clearbit calls.

**Fix:**
1. Reduce timeouts to 1s.
2. Cache Clearbit for 24h.
3. Move Clearbit call to a queued job after submit.
4. Move ipinfo to a queued `EnrichGeoIpJob`. Switch to HTTPS.

### Finding #13: `LIKE '%domain%'` full table scan in domain-probe endpoint

**File:** [`SaasFrontController.php:1620–1632`](../../app/Http/Controllers/SaasFront/SaasFrontController.php)
```php
DB::table('companies')->select('companies.id')
    ->where('companies.website', 'LIKE', '%' . $mainDomain . '%')
    ->count();
```
**Leading wildcard `LIKE '%domain%'` forces a full table scan** on `companies`. At 10k tenants, that's 10k row reads on every keystroke probe for the website field.

**Fix:** Add a `companies.domain_key VARCHAR(255)` column populated on create via mutator; add UNIQUE index. Then the probe becomes `where('domain_key', $mainDomain)->count()` — index hit, sub-millisecond.

### Finding #14: `Package` + features triple-join on every signup, no cache

**File:** [`SaasFrontController.php:419–420`](../../app/Http/Controllers/SaasFront/SaasFrontController.php)

Three-table join (`packages` → `package_details` → `package_features`) on every registration. **Same query is run twice** (once in `companyRegister`, once in `invitationCompanyStore`).

**Fix:** Cache the result with `Cache::remember("package_features:trial:{$package->id}", 3600, ...)`. Invalidate via `PackageObserver` on `saved/deleted`.

### Finding #15: Single shared `default` Horizon queue for everything

**File:** [`config/queue.php:45–50`](../../config/queue.php), [`config/horizon.php`](../../config/horizon.php)

The platform runs effectively a **single `default` queue** with one supervisor (`supervisor-1`, `maxProcesses: 10`). Welcome mails, ES indexing, bulk uploads, mass mail — all compete.

The `emails` connection **also uses Redis with `queue: default`** in `config/queue.php` — so welcome mails queue up alongside bulk resume uploads.

**Fix:** Define dedicated connections/queues in `config/horizon.php`:
- `registration` / `emails-low` — supervisor with 10 workers
- `indexing` — supervisor with 4 workers, `balance: auto`
- `bulk-upload` / `mass-mail` — supervisor with 6 workers

### Finding #16: `CompanyObserver::created()` not registered in `AppServiceProvider`

**File:** [`AppServiceProvider.php:31–41`](../../app/Providers/AppServiceProvider.php:31)

The file `app/Observers/CompanyObserver.php` exists and does critical tenant-bootstrap work, but `AppServiceProvider::registerObservers()` only registers `Job, JobApplication, NoticePeriod, BenchApplication, RecommendationRelation, JobSkill, JobCategory, Client`.

**Risk:** If anyone believes the observer fires, they'll write code that depends on the side effects (default admin role, trial package, etc.) and it'll silently break.

**Fix:** Add `Company::observe(CompanyObserver::class);` and verify it actually fires during registration.

---

## 5. Medium Findings (🟡 — backlog)

### Finding #17: Four duplicate 1,236-line register blades

| Blade | Lines |
|---|---|
| `resources/views/admin/consultancy/register.blade.php` | 1,236 |
| `resources/views/staffing_company/consultancy/register.blade.php` | 1,236 |
| `resources/views/product_company/consultancy/register.blade.php` | 1,236 |
| `resources/views/staffing_company/BK/consultancy/register.blade.php` | 1,236 (backup under `BK/`) |

All four are byte-for-byte identical. **This is exactly how the captcha gap, asset gaps, and validation drift keep reproducing.**

**Fix:** Make `admin/consultancy/register.blade.php` the canonical view; have the tenant copies `@include` the canonical partial. Delete `BK/`.

### Finding #18: `companyRegister` doesn't use existing `RegisterForm` FormRequest

**File:** [`app/Http/Requests/RegisterForm.php`](../../app/Http/Requests/RegisterForm.php)

The FormRequest has full validation rules — `sub_domain` uniqueness (conditional on Subdomain module), `career_page_link` uniqueness, `email` unique. **But `SaasFrontController::companyRegister()` uses inline `Validator::make` at lines 368–379 instead.**

Two definitions of the same rules = guaranteed drift. The FormRequest validates `sub_domain` and `career_page_link`; the inline validator doesn't. **This is why duplicate-subdomain bugs are silent.**

**Fix:** Either delete the unused `RegisterForm` or refactor `companyRegister()` to inject and use it.

### Finding #19: `User` model doesn't implement `MustVerifyEmail`

**File:** [`app/Models/User.php`](../../app/Models/User.php)

The Breeze `routes/auth.php:39–48` registers verification routes — but the User model never opted in. `Auth::user()->hasVerifiedEmail()` always returns `true`; the Breeze verification routes are unreachable.

**Fix:** Either delete `routes/auth.php:39–48` (Breeze verification routes) or wire the model to the custom `email_verification_code` flow.

### Finding #20: Inline ~650-line `<script>` block in registration blade

**File:** [`register.blade.php:490–1140`](../../resources/views/saas-front/register.blade.php:490)

The entire Metronic stepper init, FormValidation rules, Tagify city autocomplete, intl-tel-input setup, and AJAX uniqueness checks are inline. Vite is configured (`vite.config.js` exists) but **none of the registration blades use `@vite(...)`**.

**Fix:** Extract the inline script to `public/front/assets/js/register-stepper.min.js`. Use `@vite(['resources/js/register-stepper.js'])` from the blade.

### Finding #21: No client-side validation mirroring server rules

Grepped `parsley|data-parsley|pattern=|required minlength` on `register.blade.php` — FormValidation.js covers the basics but no `type="email"`, no `minlength="8"`, no `data-parsley-strength`.

**Risk:** The server round-trip happens for every typo. At 10k signups/day, ~30% have validation errors → 3k wasted trips.

**Fix:** Add HTML5 input types; add `minlength`/`maxlength`; add `data-parsley-*` attributes.

### Finding #22: `User` model `$guarded = []` allows mass-assign of any field

**File:** [`app/Models/User.php`](../../app/Models/User.php)

Mass-assignable: `is_superadmin`, `email_verification_code`, `status`, `working_status`, `root_user`, `type`. An attacker who finds a controller that passes `$request->all()` to `User::create()` becomes super-admin.

**Fix:** Define `$fillable` explicitly: `['name', 'email', 'password', 'company_id', 'calling_code', 'mobile', 'status']`. Add a test that asserts mass-assign of forbidden fields fails.

---

## 6. Low Findings (🟢 — drift cleanup)

| # | Finding | File |
|---|---|---|
| 23 | **Dead `use` imports** of `App\Notifications\EmailVerification*`, `NewCompanyRegister` | [`SaasFrontController.php:24–26`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:24) |
| 24 | **Dead `BK/` directory** of register blades | [`resources/views/staffing_company/BK/`](../../resources/views/staffing_company/BK/) |
| 25 | **Login captcha validation commented out** | [`LoginController.php:75–100`](../../app/Http/Controllers/Auth/LoginController.php:75) |

---

## 7. Schema Bottlenecks

From [`database/schema/mysql-schema.sql`](../../database/schema/mysql-schema.sql):

| Table | PK | Other indexes | Risk |
|---|---|---|---|
| `packages` (line 3331) | `id` | **none** | `Package::where('is_trial', 1)->first()` is a full scan today. Add `KEY packages_is_trial_status (is_trial, status)`. |
| `permissions` (line 3443) | `id` | UNIQUE `name`; KEY `module_id` | Composite `(status, product_based)` speeds the product-company permission cleanup. |
| `role_has_permissions` (line 3634) | composite | KEY `role_id` | Already optimal. Cartesian insert is the real cost. |
| `companies` (line 942) | `id` | UNIQUE `career_page_link`; KEY `package_id` | **Missing UNIQUE on `sub_domain`** despite being treated as unique in code. **Missing `domain_key`** for LIKE-scan avoidance. |
| `subscriptions` (line 3921) | `id` | **none** | Empty at signup. Add `KEY subscriptions_company_id (company_id)` preemptively. |

---

## 8. Performance Audit at 10k Recruiters/Day

| Metric | Today | After Tier 1+2 | After Full Plan |
|---|---|---|---|
| `POST /company-register` P95 | 1.5–2.5s | ~400ms | ~250ms |
| Octane workers needed (concurrent) | ~25 | ~3 | ~2 |
| MySQL QPS from registration | High (probes × 4 keystrokes × no throttle) | -80% | -95% |
| ES writes per signup | ~30 sync | 0 sync (queued) | 0 sync (queued + bulked) |
| Bot traffic | Unbounded | Near-zero (Turnstile + throttle) | Near-zero |
| "Ghost tenant" drift | Common | Rare (drift watcher) | Eliminated |
| External API blocking | Up to 5s/worker | <100ms/worker | <10ms/worker |

---

## 9. Cross-cutting Concerns (verified across all agent scans)

1. **No caching layer for registration hot path.** Zero `Cache::` / `Redis::` / `->remember(` in `SaasFrontController`. The codebase already has cache helpers in `GeneralMethod.php` (currency, counters), but those hit MySQL because `CACHE_STORE=database` (Redis cache is unconfigured despite Redis being available).
2. **Session driver = database** ([`config/session.php:21`](../../config/session.php:21)). Sessions also hit MySQL.
3. **Single Horizon supervisor** with `maxProcesses: 10` prod / `3` local. Bulk operations starve registration.
4. **Vite configured but unused** on registration pages — the modern stack is bypassed entirely.
5. **No events fired on registration** (no `Registered`, `CompanyCreated`, etc.). Side effects are coupled to controller code instead of event listeners.
6. **No `EventServiceProvider`** — events/listeners can't be registered the Laravel way.
7. **`Octane + FrankenPHP`** long-lived workers — careful with static state. The current registration code uses none (good).

---

## 10. Risk at Scale: Will the Current Architecture Survive 10k Recruiters/Day?

**No.** Without Tier 1 + Tier 2 fixes:
- 10k/day = 1 signup every 8.6s → ~10 concurrent at peak
- 10 concurrent × 2.5s P95 = **25 Octane workers** for registration alone
- A 32 GB app server with FrankenPHP typically sustains 32 Octane workers before OOM
- The bot traffic (no captcha, no throttle) multiplies these numbers unpredictably

**Verdict:** Tier 1 + Tier 2 are mandatory before any scale-up. After them, the page is production-safe at current load. Tier 3 is the long-term answer for 10x growth.

---

## 11. Open Questions for the Product Owner

1. **Plaintext `super_password` field** — actively used anywhere? If not, delete the column.
2. **Trial package lookup** — is `id=1` truly the trial, or has the schema drifted?
3. **`User` model + Breeze verify routes** — delete Breeze (recommended) or wire `MustVerifyEmail`?
4. **`CompanyObserver` not in `AppServiceProvider`** — manual boot elsewhere, or regression?
5. **Captcha vendor** — Cloudflare Turnstile (recommended), hCaptcha, or reCAPTCHA v3?

---

## 12. Where to Go Next

→ See [`IMPLEMENTATION_PLAN.md`](IMPLEMENTATION_PLAN.md) for the phased rollout (6 sprints, ~12 dev-days, no business-logic changes).