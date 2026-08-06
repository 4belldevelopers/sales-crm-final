# V2 Registration — GOD-Level Implementation Plan

> **Companion to:** [`AUDIT.md`](AUDIT.md) — every fix in this plan maps to a finding in the audit.
> **Goal:** Make `/register` production-safe at current load in Sprint 1, horizontally scalable to 10,000 concurrent users on the existing 32 GB / 32 GB / 32 GB stack after Sprint 5, with no business-logic changes.
> **Constraints:** No existing flow may change (same fields, same validation rules, same roles, same package assignment, same email templates, same redirects). Every change must be structural (caching, queueing, indexing) or zero-cost (indexes, rate limits, dead-code removal).
> **Reference standards:** [docs/UI_UX_STANDARDS.md](../UI_UX_STANDARDS.md), [docs/AI_RULES.md](../AI_RULES.md), [docs/CODE_DOCUMENTATION_STANDARD.md](../CODE_DOCUMENTATION_STANDARD.md).

---

## 0. Scope, Non-Goals, and Targets

### In scope

| Surface | Files | Outcome |
|---|---|---|
| Public `/register` page | [`register.blade.php`](../../resources/views/saas-front/register.blade.php) + invitation + teamReg | <300ms TTI on 3G; accessible; works offline; no captcha-block |
| `POST /company-register` | [`SaasFrontController::companyRegister`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:357) | <400ms P95 (from ~2s today); idempotent; transactionally safe |
| AJAX keystroke probes | 4 endpoints in [`routes/web.php:351–359`](../../routes/web.php) | <100ms P95; cached; throttled; debounced |
| Email verification | `getEmailVerification`, `resend`, welcome mail | signed URLs; queued; drift-safe |
| Tenant bootstrap | [`CompanyObserver::created`](../../app/Observers/CompanyObserver.php:26) | Registered; idempotent; retryable |
| ES sync | 7+ indices currently written sync | Queued + bulked; drift watcher |
| DB schema | 4 indexes / 1 column add | Zero-downtime `ALTER` migrations |

### Non-goals (explicitly out of scope)

- **No new fields** on the form.
- **No change to validation rules** beyond what the existing `RegisterForm` already declares.
- **No change to email templates** or subjects.
- **No change to role names** (`admin`, `Sub Administrator`, etc.).
- **No change to trial-package assignment** behavior (auto-pick first `is_trial=1`).
- **No change to existing payment flows** — registration remains free, post-registration billing unchanged.
- **No refactor of unrelated god controllers** (`HomeController`, etc.).

### Targets (measured before/after each sprint)

| Metric | Today | Sprint 1 | Sprint 3 | Sprint 5 |
|---|---|---|---|---|
| `POST /company-register` P95 | 1.5–2.5s | ~700ms | ~400ms | ~250ms |
| Keystroke probe P95 | ~150ms (no cache) | ~50ms (cached) | ~30ms | ~20ms |
| ES writes per signup (sync) | ~30 | ~30 | 0 | 0 |
| MySQL QPS from registration | baseline | -50% | -80% | -95% |
| Octane workers needed | ~25 | ~10 | ~5 | ~2 |
| 404 console errors on page load | 6 | 0 | 0 | 0 |
| Bot traffic blocked | 0% | 80% | 99% | 99.9% |

---

## 1. Architecture Decisions (frozen at planning time)

| ID | Decision | Rationale |
|---|---|---|
| **ADR-V2R-01** | Use **Cloudflare Turnstile** for captcha (not hCaptcha, not reCAPTCHA v3) | Free, no Google tracking, accessible, supports invisible mode, lowest friction |
| **ADR-V2R-02** | Slice `companyRegister()` into **Action classes** (`CreateCompanyAction`, `BootstrapTenantDefaultsAction`, `AssignAdminRoleAction`, `SeedCoinLedgerAction`, `IndexNewCompanyAction`) | Each action owns one transactional boundary; testable in isolation; matches project convention (Recipes doc) |
| **ADR-V2R-03** | Introduce **dedicated Horizon queues**: `registration`, `indexing`, `bulk-upload`, `mass-mail` | Single-queue starvation is documented in `PERFORMANCE_BUDGET.md` |
| **ADR-V2R-04** | All ES writes go through a **single ES bulk-index job** that batches by tenant | Replaces ~30 sync calls with 1 queued bulk request |
| **ADR-V2R-05** | Extract inline JS from registration blades to **`public/dist/registration-stepper.v{N}.min.js`** with `@vite` | Cacheable, versionable, minified; replaces 650-line inline block |
| **ADR-V2R-06** | Consolidate the 4 duplicate `consultancy/register.blade.php` files via **`@include` partial** | Single source of truth; locks in fixes 1–N from drift |
| **ADR-V2R-07** | Add **`companies.domain_key`** generated column with UNIQUE index | Replaces `LIKE '%domain%'` with index hit |
| **ADR-V2R-08** | Adopt **`after_commit = true`** on the `emails` queue connection | Mail fires only after MySQL commit (prevents ghost-tenant emails) |
| **ADR-V2R-09** | Introduce a **`RegisterUserCompany` event + listener chain** for post-commit side effects | Decouples tenant-bootstrap from the controller; makes the flow testable |
| **ADR-V2R-10** | Standardize on **Redis cache, Redis session, Redis queue** for the registration hot path | One env-var change; -50% MySQL QPS overnight |

---

## 2. Infrastructure Prerequisites (do these once before Sprint 1)

### 2.1 Add Redis cache store

**File:** [`config/cache.php`](../../config/cache.php)

Add a Redis store entry:
```php
'redis' => [
    'driver' => 'redis',
    'connection' => 'cache',
    'lock_connection' => 'default',
],
```

### 2.2 Add Redis DB index for cache

**File:** `.env`
```ini
REDIS_CACHE_DB=1
SESSION_DRIVER=redis
CACHE_STORE=redis
```

### 2.3 Dedicated Horizon queues

**File:** [`config/horizon.php`](../../config/horizon.php)

Replace the single `supervisor-1` with:
```php
'environments' => [
    'production' => [
        'supervisor-registration' => [
            'queue' => ['registration', 'emails-low'],
            'maxProcesses' => 10,
            'balance' => 'auto',
        ],
        'supervisor-indexing' => [
            'queue' => ['indexing'],
            'maxProcesses' => 4,
            'balance' => 'simple',
        ],
        'supervisor-bulk' => [
            'queue' => ['bulk-upload', 'mass-mail'],
            'maxProcesses' => 6,
            'balance' => 'auto',
        ],
    ],
],
```

**File:** [`config/queue.php`](../../config/queue.php)

Add a dedicated `indexing` connection:
```php
'indexing' => [
    'driver' => 'redis',
    'queue' => 'indexing',
    'connection' => 'default',
    'retry_after' => 600,
],
```

Set `'after_commit' => true` on the `emails` connection (ADR-V2R-08).

### 2.4 MySQL — add cache-friendly indexes

Migration (no business-logic change):
```sql
ALTER TABLE packages ADD KEY packages_is_trial_status (is_trial, status);
ALTER TABLE subscriptions ADD KEY subscriptions_company_id (company_id);
ALTER TABLE users ADD KEY users_company_email (company_id, email);
ALTER TABLE companies ADD UNIQUE KEY companies_sub_domain_unique (sub_domain);
```

Run online (MySQL 8 supports `ALGORITHM=INPLACE, LOCK=NONE` for these).

---

## 3. Sprint Plan (6 sprints, ~12 dev-days)

Each sprint is independently shippable. Each one ends with the [Definition of Done](../DEFINITION_OF_DONE.md) gate.

---

### Sprint 1 — Production-safe at current load (0.5 dev-day)

> Closes 6 latent bugs. -50% MySQL QPS. No behavioral change.

#### Tasks

| # | Task | Files | Audit ref |
|---|---|---|---|
| 1.1 | Set `CACHE_STORE=redis` in `.env` | `.env`, [`config/cache.php`](../../config/cache.php) | Finding #9.1, Cross-cutting #1 |
| 1.2 | Set `SESSION_DRIVER=redis` in `.env` | `.env` | Cross-cutting #2 |
| 1.3 | Delete `/run-queue` route | [`routes/web.php:247–250`](../../routes/web.php:247) | Finding #4 |
| 1.4 | Fix `RedirectIfAuthenticated` middleware (return redirect, not plain string) | [`app/Http/Middleware/RedirectIfAuthenticated.php:36,40,44`](../../app/Http/Middleware/RedirectIfAuthenticated.php:36) | Addendum A5 |
| 1.5 | Add `signed` middleware to `/email-verification/{code}` | [`routes/web.php:360`](../../routes/web.php:360) | Finding #7 |
| 1.6 | Delete `POST /register` and `Auth\RegisterController` | [`routes/web.php:147`](../../routes/web.php:147), [`app/Http/Controllers/Auth/RegisterController.php`](../../app/Http/Controllers/Auth/RegisterController.php) | §2 Dead paths |
| 1.7 | Remove three dead `use App\Notifications\*` imports | [`SaasFrontController.php:24–26`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:24) | §2 Dead paths |
| 1.8 | Add cache/session/index migration to schema | `database/migrations/2026_08_07_*.php` | §2.4 |
| 1.9 | Add `companies.sub_domain` UNIQUE + `users(company_id,email)` composite + `packages(is_trial,status)` | migration | §7 + Finding #6 |

#### Verification (per [VERIFY.md](../VERIFY.md))

- [ ] `php artisan config:cache` succeeds.
- [ ] `php artisan tinker` → `Cache::put('x',1); Cache::get('x')` returns `1` (Redis hit).
- [ ] `php artisan route:list` no longer shows `POST register`.
- [ ] `curl -i http://localhost:8000/email-verification/foo` returns `403 Invalid signature`.
- [ ] Existing user can still register end-to-end.

---

### Sprint 2 — DoS surface + asset 404s closed (1.5 dev-days)

> Bot traffic blocked. Captcha + rate limits. Page renders without console errors.

#### Tasks

| # | Task | Files | Audit ref |
|---|---|---|---|
| 2.1 | Add `RateLimiter::for('register-form'/'register-submit')` | [`app/Providers/AppServiceProvider.php`](../../app/Providers/AppServiceProvider.php:49) | Finding #2 |
| 2.2 | Apply `throttle:register-submit` to `POST /company-register` | [`routes/web.php:351`](../../routes/web.php:351) | Finding #2 |
| 2.3 | Apply `throttle:register-form` to the 4 `*Chk` probes | [`routes/web.php:352, 357–359`](../../routes/web.php:352) | Finding #2 |
| 2.4 | Add Cloudflare Turnstile widget + server-side verify | [`register.blade.php`](../../resources/views/saas-front/register.blade.php), new `App\Services\TurnstileService.php` | Finding #3 |
| 2.5 | Add hidden honeypot field (`website_url`) to all 4 register blades | All 4 register blades | Finding #3 |
| 2.6 | Run Metronic build → output to `public/demo/dist/` | CI step or one-off build script | Finding #9 |
| 2.7 | Add `countryDialCodes.js` to `public/front/assets/js/` | `public/front/assets/js/countryDialCodes.js` | Finding #9 |
| 2.8 | Replace missing `img/logo-transparent1.svg` with real logo | `public/img/` | Finding #9 |
| 2.9 | Cache probe results in Redis with 60s TTL | [`SaasFrontController.php:1550, 1584, 1600, 1657`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1550) | Finding #2 (defense-in-depth) |

#### Verification

- [ ] Submit 11 registrations from same IP in 1 hour → 11th returns 429.
- [ ] Submit 4 probe requests in 1 second → 4th returns 429.
- [ ] Submit form without Turnstile token → 422 with captcha error.
- [ ] Submit form with honeypot filled → 200 OK but no DB write.
- [ ] Browser console shows zero 404 errors on `/register`.
- [ ] Metronic stepper component actually initializes (DOM has `data-kt-stepper-element="nav"` populated).

---

### Sprint 3 — Query & cache wins (2 dev-days)

> -80% MySQL QPS. P95 <400ms.

#### Tasks

| # | Task | Files | Audit ref |
|---|---|---|---|
| 3.1 | Cache trial package + features (3-table join) in Redis with 1h TTL | [`SaasFrontController.php:419–420`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:419) | Finding #14 |
| 3.2 | Add `companies.domain_key` UNIQUE generated column + index | migration | Finding #13 |
| 3.3 | Rewrite `domainCompanyRegisterChk` to use `domain_key` (no LIKE) | [`SaasFrontController.php:1600–1656`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1600) | Finding #13 |
| 3.4 | Batch city lookup (single `whereIn` query) | [`SaasFrontController.php:1411, 1457, 1486`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1411) | Finding #10 |
| 3.5 | Single role-priority lookup (max instead of 7 queries) | [`SaasFrontController.php:1223–1281`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1223) | Finding #11 |
| 3.6 | Register `CompanyObserver::created` in `AppServiceProvider` | [`app/Providers/AppServiceProvider.php:31–41`](../../app/Providers/AppServiceProvider.php:31) | Finding #16 |
| 3.7 | Move `Package::first()` to use `where('is_trial', 1)` | [`SaasFrontController.php:419, 2303`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:419) | Finding #6 (**verify with product owner first**) |
| 3.8 | Cache Clearbit response (24h TTL, 1s timeout) | [`SaasFrontController.php:1679`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1679) | Finding #12 |
| 3.9 | Cache ipinfo response (7d TTL, 2s timeout, HTTPS) | [`GeneralMethod.php:6442`](../../app/Helper/GeneralMethod.php:6442) | Finding #12 |
| 3.10 | Use existing `RegisterForm` FormRequest (or delete if not) | [`app/Http/Requests/RegisterForm.php`](../../app/Http/Requests/RegisterForm.php) | Finding #18 |

#### Verification

- [ ] `EXPLAIN SELECT * FROM companies WHERE domain_key = 'foo.com'` uses index.
- [ ] `SHOW PROCESSLIST` during a probe burst shows MySQL receiving <20 queries (was >200).
- [ ] Horizon dashboard shows `default` queue is empty; `indexing` and `registration` queues have steady traffic.
- [ ] Existing user can still register with `is_trial=1` package assigned.

---

### Sprint 4 — Security & data-protection cleanup (1 dev-day)

> Closes GDPR exposure. Removes plaintext passwords.

#### Tasks

| # | Task | Files | Audit ref |
|---|---|---|---|
| 4.1 | Audit + remove `super_password` writes if unused | [`SaasFrontController.php:1080, 1865, 2963, 3576`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1080) | Finding #1 |
| 4.2 | If `super_password` is used, replace reads with `Hash::check()` against `password` | wherever read | Finding #1 |
| 4.3 | Audit + remove `super_admin_login_code` if unused | same files | Finding #1 |
| 4.4 | Drop `users.super_password` column if confirmed unused | migration | Finding #1 |
| 4.5 | Define `$fillable` on `User` model explicitly | [`app/Models/User.php`](../../app/Models/User.php) | Finding #22 |
| 4.6 | Add `MassAssignmentGuardTest` to assert forbidden fields reject | new test | Finding #22 |
| 4.7 | Decide: delete Breeze verify routes OR wire `MustVerifyEmail` on User | [`routes/auth.php:39–48`](../../routes/auth.php:39), [`app/Models/User.php`](../../app/Models/User.php) | Finding #19 |
| 4.8 | Delete dead `BK/` directory and old backup files | `resources/views/staffing_company/BK/`, root `OLD_*` | §2 Dead paths |
| 4.9 | Add HTML5 input types + `minlength`/`maxlength` to all 4 register blades | all 4 register blades | Finding #21 |

#### Verification

- [ ] `php artisan tinker` → `User::create(['is_superadmin' => 1, ...])` rejects the field.
- [ ] New user record has no `super_password` value.
- [ ] `/verify-email` route returns 404 (Breeze removed) or works (if wired).

---

### Sprint 5 — Architectural refactor (5 dev-days)

> P95 <250ms. Horizontally scalable. ES drift-free. "Ghost tenant" impossible.

This is the big one. The 1,193-line `companyRegister()` becomes a thin orchestrator that dispatches a chain of Action classes.

#### 5.1 Define events (ADR-V2R-09)

**New file:** `app/Events/CompanyRegistered.php`
```php
class CompanyRegistered
{
    public function __construct(
        public readonly Company $company,
        public readonly User $user,
        public readonly array $requestData,
    ) {}
}
```

**New file:** `app/Events/UserRegistered.php`
```php
class UserRegistered
{
    public function __construct(
        public readonly User $user,
        public readonly string $rawPassword, // for welcome mail only
    ) {}
}
```

#### 5.2 Define Action classes

| Action | Responsibility | Transaction boundary |
|---|---|---|
| `App\Actions\Registration\CreateCompanyAction` | Validate + insert `Company` + set `sub_domain` + initial package seed | 1 tx |
| `App\Actions\Registration\CreateCompanyUserAction` | Insert `User` + `users_profile` + `assignRole('admin')` | 1 tx |
| `App\Actions\Registration\BootstrapTenantDefaultsAction` | Create roles, attach permissions, seed `application_status`, `ThemeSetting`, `ApplicationSetting`, trial `CompanyPackage` | 1 tx (or queued) |
| `App\Actions\Registration\SeedCoinLedgerAction` | Insert coin/email/whatsapp counters | 1 tx (or queued) |
| `App\Actions\Registration\SeedCommercialRatesAction` | Insert `commericials`, `commericial_details`, city `client_location` rows | 1 tx |
| `App\Actions\Registration\IssueVerificationTokenAction` | Generate token + dispatch welcome mail | none (idempotent) |

Each Action:
- Accepts a `Request` (or `DTO`) + `Company` (or `User`)
- Returns the affected model(s) or void
- Logs an intent docblock per [CODE_DOCUMENTATION_STANDARD](../CODE_DOCUMENTATION_STANDARD.md)
- Has a Pest test

#### 5.3 Define post-commit listeners

**New file:** `app/Listeners\IndexNewCompany.php`
```php
class IndexNewCompany
{
    public function handle(CompanyRegistered $event): void
    {
        IndexCompanyBulkJob::dispatch($event->company->id)
            ->onQueue('indexing');
    }
}
```

**New file:** `app/Jobs\IndexCompanyBulkJob.php`
```php
class IndexCompanyBulkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = [30, 60, 120];
    public $timeout = 600;

    public function __construct(public int $companyId) {}

    public function handle(ElasticService $es): void
    {
        $company = Company::withoutGlobalScopes()->findOrFail($this->companyId);

        $es->getClient()->bulk([
            'body' => [
                ['update' => ['_index' => 'ats_companies', '_id' => $company->id]],
                ['doc' => $this->companyDoc($company), 'doc_as_upsert' => true]],
                ['update' => ['_index' => 'ats_company_coins', '_id' => $company->id]],
                ['doc' => $this->coinsDoc($company), 'doc_as_upsert' => true]],
                // ... one entry per index that needs to be written
            ],
        ]);
    }
}
```

#### 5.4 Rewrite `companyRegister`

**Target:** [`SaasFrontController.php:357`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:357) becomes ~50 lines:

```php
public function companyRegister(RegisterRequest $request): JsonResponse
{
    try {
        DB::beginTransaction();

        $company = $this->createCompanyAction->execute($request);
        $user    = $this->createCompanyUserAction->execute($request, $company);

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        return Reply::error('Registration failed.');
    }

    // Post-commit side effects — cannot fail the registration
    event(new CompanyRegistered($company, $user, $request->validated()));
    event(new UserRegistered($user, $request->password));

    return Reply::dataOnly([
        'status' => 'success',
        'view'   => view('saas-front.registration_success', [...])->render(),
    ]);
}
```

The `BootstrapTenantDefaultsAction` becomes a **listener on `CompanyRegistered`** that runs **after commit** as a queued job. This way a permission-attach failure doesn't leave a half-baked tenant.

#### 5.5 Move ES writes outside transaction

All 30+ inline `ElasticService::getClient()->update(...)` calls are removed from the controller. The `IndexCompanyBulkJob` is the only ES write path, dispatched after `DB::commit()`.

#### 5.6 Set `after_commit = true` on the `emails` connection

ADR-V2R-08. Prevents the "ghost tenant" mail-on-rollback scenario.

#### 5.7 Drift watcher

**New file:** `app/Console\Commands\WatchCompanyIndexDrift.php`
```php
class WatchCompanyIndexDrift extends Command
{
    protected $signature = 'drift:watch-companies';
    public function handle(): void { /* scheduled every 5 min */ }
}
```

Compares MySQL `companies.id` against ES `ats_companies` IDs and re-indexes missing rows.

#### Verification

- [ ] Unit tests for each Action pass.
- [ ] Feature test: full registration → assert all expected rows + 1 ES bulk job dispatched, not 30 sync.
- [ ] Force a failure mid-flow → assert no orphan ES docs, no orphan MySQL rows, no orphan mail jobs.
- [ ] Horizon dashboard: `indexing` queue processes in parallel with `registration`.
- [ ] MySQL `SHOW ENGINE INNODB STATUS` shows log writer utilization <60% at 100 simulated signups/min.

---

### Sprint 6 — Frontend consolidation (2 dev-days)

> TTI -300ms. Single source of truth for the register blade. Inline JS extracted.

#### Tasks

| # | Task | Files | Audit ref |
|---|---|---|---|
| 6.1 | Extract inline JS from `register.blade.php` to `resources/js/registration/stepper.js` | both files | Finding #20 |
| 6.2 | Extract inline JS from `invitationCompanyRegister.blade.php` | both files | Finding #20 |
| 6.3 | Add `resources/js/registration/team.js` | new file | Finding #20 |
| 6.4 | Add `resources/js/registration/turnstile.js` | new file | Finding #3 |
| 6.5 | Wire `@vite(['resources/js/registration/app.js'])` to all 4 register blades | all 4 blades | Finding #20 |
| 6.6 | Run `npm run build` to emit versioned hashed bundles to `public/build/` | `vite.config.js`, CI | Finding #20 |
| 6.7 | Consolidate 4 duplicate `consultancy/register.blade.php` files via `@include` partial | new partial, 4 blades updated | Finding #17 |
| 6.8 | Delete `staffing_company/BK/` directory | `resources/views/staffing_company/BK/` | §2 Dead paths |
| 6.9 | Add Metronic `.section-card` wrapper around each step (per UI_UX_STANDARDS §10) | all 4 register blades | [UI_UX §10](../UI_UX_STANDARDS.md) |
| 6.10 | Sticky stepper header on scroll | all 4 register blades | [UI_UX §3](../UI_UX_STANDARDS.md) |

#### Verification

- [ ] `npm run build` produces hashed bundles in `public/build/`.
- [ ] Page source on `/register` references the hashed bundle (no inline 650-line `<script>`).
- [ ] Lighthouse TTI <300ms on simulated 3G.
- [ ] Only ONE `consultancy/register.blade.php` exists after Sprint 6.
- [ ] All 4 register blades use the canonical partial.

---

## 4. Risk Register (per sprint)

| Sprint | Risk | Mitigation |
|---|---|---|
| 1 | Cache store change breaks existing cached data | Drain `database` cache to Redis first; test in staging |
| 1 | `signed` middleware on verify-email breaks in-flight emails | New users only; old tokens continue to work because the controller checks `email_verification_code` regardless of signature — the signed middleware only protects against URL tampering |
| 2 | Turnstile false-positives block legitimate users | Use Turnstile invisible mode (no UI); keep server-side fallback to existing captcha image (which exists but isn't enforced — wire it as fallback) |
| 3 | Batched city lookup may return duplicates → insert fails | Use `upsert` instead of `insert` for `client_location`; verify with staging data |
| 5 | Action-class refactor subtly changes behavior | Each Action has a Pest test BEFORE the refactor; refactor; re-run tests |
| 5 | Event-listener migration changes order of side effects | Document current order; assert new order matches via tests |
| 6 | Vite build emits different bundle hashes → asset-versioning issues | Set `APP_ENV=production`; verify asset paths resolve |

---

## 5. Rollback Plan (per sprint)

| Sprint | Rollback procedure |
|---|---|
| 1 | Revert `.env` (`CACHE_STORE=database`, `SESSION_DRIVER=database`); re-add `/run-queue` route; restore `signed` middleware removal. **Risk: low** (1 PR, mostly env + route changes) |
| 2 | Turn off Turnstile widget; remove `throttle:` middleware; restore old asset paths via Git revert. **Risk: low** |
| 3 | Revert query changes; restore `Package::first()` to old behavior. **Risk: medium** (DB indexes may stay; test thoroughly) |
| 4 | Re-add `super_password` writes if removal breaks auth. **Risk: low** |
| 5 | The monolith is preserved in git history. Revert the `companyRegister()` rewrite to restore old behavior. **Risk: medium** (large surface; tests must pass) |
| 6 | Revert Vite changes; restore inline scripts. **Risk: low** |

---

## 6. Definition of Done (per sprint, per [DEFINITION_OF_DONE.md](../DEFINITION_OF_DONE.md))

Every PR that lands must satisfy:

- [ ] All audit findings tagged to this sprint are closed or explicitly deferred (with reason).
- [ ] No new `dd()`, `env()`, or `dd()`-debug routes introduced (CI guard via [ENFORCEMENT.md](../ENFORCEMENT.md)).
- [ ] No new unescaped `{!! !!}` Blade outputs.
- [ ] Every new file/class/method has an intent docblock per [CODE_DOCUMENTATION_STANDARD](../CODE_DOCUMENTATION_STANDARD.md).
- [ ] Every new tenant-scoped query has a tenancy note.
- [ ] Existing registration flow still works end-to-end (Pest feature test).
- [ ] Performance before/after numbers recorded in this doc.
- [ ] No business-logic changes (validation rules, role names, package assignment, email subjects, redirects all unchanged).

---

## 7. Sprint Summary

| Sprint | Effort | Cumulative | Outcomes |
|---|---|---|---|
| **1** | 0.5 day | 0.5 day | Production-safe at current load; -50% MySQL QPS |
| **2** | 1.5 days | 2 days | DoS surface closed; captcha + rate limit; page renders clean |
| **3** | 2 days | 4 days | P95 -400ms; -80% MySQL QPS |
| **4** | 1 day | 5 days | GDPR-safe; Breeze cleaned up |
| **5** | 5 days | 10 days | P95 -250ms; ES drift-free; horizontally scalable |
| **6** | 2 days | 12 days | TTI -300ms; single-source register blade |
| **Buffer** | 3 days | 15 days | Review, prod-deploy, post-deploy verification |

---

## 8. Post-Implementation Verification

After Sprint 6, the audit's success criteria:

- [ ] All 25 audit findings addressed (closed or explicitly deferred).
- [ ] Lighthouse TTI <300ms on `/register` (simulated 3G).
- [ ] `POST /company-register` P95 <250ms under 100-concurrent-user load test.
- [ ] 10k signups/day simulated in staging — no resource exhausts.
- [ ] Zero "ghost tenant" drift incidents over 30 days.
- [ ] Zero captcha-block on legitimate users.
- [ ] All three 404 assets resolved.
- [ ] All four duplicate `consultancy/register.blade.php` files consolidated.
- [ ] `bash scripts/ai-guards.sh` passes.
- [ ] `php artisan test --testsuite=Registration` passes.

---

## 9. References

- [AUDIT.md](AUDIT.md) — every finding cited in this plan
- [docs/RECIPES.md](../RECIPES.md) — vertical-slice cookbook (Route + FormRequest + Action + ES sync + Observer + Reverb + Blade + test)
- [docs/AI_RULES.md](../AI_RULES.md) — the 13 hard rules
- [docs/UI_UX_STANDARDS.md](../UI_UX_STANDARDS.md) — Metronic + Poppins + 14px cap + card layout
- [docs/DEFINITION_OF_DONE.md](../DEFINITION_OF_DONE.md) — per-PR gate
- [docs/TESTING.md](../TESTING.md) — Pest test conventions
- [docs/PERFORMANCE_BUDGET.md](../PERFORMANCE_BUDGET.md) — the single-queue bottleneck rationale
- [docs/ENFORCEMENT.md](../ENFORCEMENT.md) — `scripts/ai-guards.sh`, CI gates
- [docs/CODE_DOCUMENTATION_STANDARD.md](../CODE_DOCUMENTATION_STANDARD.md) — intent docblock requirements
