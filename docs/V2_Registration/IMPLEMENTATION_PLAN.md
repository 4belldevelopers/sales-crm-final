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
- **No change to trial-package assignment** behavior. **BLOCKER:** before Sprint 3 task 3.7 ships, the product owner must confirm in writing which `packages.id` is the trial package and whether `is_trial=1` correctly filters it. Until then, the controller keeps the existing `Package::first()` behavior unchanged (see §10.A for the verification protocol).
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
| **ADR-V2R-11** | **Bind every Action class as `$this->app->scoped(...)` in `AppServiceProvider::registerActions()`** | Guarantees a fresh instance per request under Octane/FrankenPHP; prevents tenant data leaking between requests on the same worker. Implementation: §10.A |
| **ADR-V2R-12** | **Wrap the registration write path in `DB::transaction(fn() => ..., 3)`** — auto-retry on `PDOException` SQLSTATE 40001 (serialization failure) or 1213 (deadlock) | InnoDB deadlocks on concurrent auto-increment + permission reads are expected at 10k signups/day; Laravel 11's 2nd-arg `attempts` retry is the idiomatic fix. Implementation: §10.A |
| **ADR-V2R-13** | **`CompanyRegistered` / `UserRegistered` events MUST dispatch AFTER `DB::commit()` returns**, not from inside the transaction closure | Combined with ADR-V2R-08 (`after_commit=true` on queues), this prevents ghost tenants receiving welcome emails or being indexed in ES only to be rolled back. Implementation: §10.C |
| **ADR-V2R-14** | **Trial package query rewrite (Sprint 3 task 3.7) requires product-owner sign-off** | Wrong trial package cascades into wrong billing, wrong role permissions, and wrong email templates for every new signup. Verification protocol: §10.D |

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
| 1.10 | Force HTTPS in `getDomainSpecificUrl` for production paths (audit M4) | [`app/Helper/start.php:87`](../../app/Helper/start.php:87) | Audit §13 M4 |

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
| 2.10 | **Resend endpoint hardening (audit H1)**: rotate token on resend, throttle 3/hr per email + 10/hr per IP, require signed URL or session token | [`routes/web.php:361`](../../routes/web.php:361), [`SaasFrontController.php:1749–1832`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1749) | Audit §13 H1 |

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
| 3.7 | Move `Package::first()` to use `where('is_trial', 1)` | [`SaasFrontController.php:419, 2303`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:419) | Finding #6 (**BLOCKER — see §10.A**) |
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
| 4.10 | **Wire the dead `email-verification.blade.php`** as the wrong/expired-token page (audit M1, L2) | [`SaasFrontController.php:3526`](../../app/Http/Controllers/SaasFront/SaasFrontController.php:3526), `resources/views/saas-front/email-verification.blade.php` | Audit §13 M1, L2 |

#### Verification

- [ ] `php artisan tinker` → `User::create(['is_superadmin' => 1, ...])` rejects the field.
- [ ] New user record has no `super_password` value.
- [ ] `/verify-email` route returns 404 (Breeze removed) or works (if wired).

---

### Sprint 5 — Architectural refactor (5 dev-days)

> P95 <250ms. Horizontally scalable. ES drift-free. "Ghost tenant" impossible.

This is the big one. The 1,193-line `companyRegister()` becomes a thin orchestrator that dispatches a chain of Action classes.

**Container binding — every Action is scoped (ADR-V2R-11, §10.B):**

In `app/Providers/AppServiceProvider.php::registerActions()`:
```php
$this->app->scoped(CreateCompanyAction::class);
$this->app->scoped(CreateCompanyUserAction::class);
$this->app->scoped(BootstrapTenantDefaultsAction::class);
$this->app->scoped(SeedCoinLedgerAction::class);
$this->app->scoped(SeedCommercialRatesAction::class);
$this->app->scoped(IssueVerificationTokenAction::class);
```

**Transactional retry — Laravel 11 `DB::transaction(fn, attempts)` (ADR-V2R-12, §10.A):**

```php
$company = DB::transaction(fn () => $this->createCompanyAction->execute($request), 3);
$user    = DB::transaction(fn () => $this->createCompanyUserAction->execute($request, $company), 3);
```

The 2nd-arg `attempts = 3` re-runs the closure on `PDOException` with SQLSTATE 40001 or 1213 (deadlock). The Action class must be idempotent inside the closure — which is why each Action accepts the `Request` + `Company` and re-derives state rather than mutating shared object properties.

**Event dispatch — AFTER commit, never inside (ADR-V2R-13, §10.C):**

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
        $company = DB::transaction(
            fn () => $this->createCompanyAction->execute($request),
            3, // ADR-V2R-12 — retry on deadlock/serialization failure
        );

        $user = DB::transaction(
            fn () => $this->createCompanyUserAction->execute($request, $company),
            3,
        );
    } catch (\Throwable $e) {
        return Reply::error('Registration failed.');
    }

    // ADR-V2R-13 — dispatch AFTER DB::commit() returns, NEVER inside the closure.
    // Listener queues honor config('queue.connections.emails.after_commit') = true.
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

#### 5.8 Wire Action classes as `scoped` bindings (ADR-V2R-11, §10.B)

Done in `app/Providers/AppServiceProvider.php::registerActions()`:
```php
public function registerActions(): void
{
    $this->app->scoped(CreateCompanyAction::class);
    $this->app->scoped(CreateCompanyUserAction::class);
    $this->app->scoped(BootstrapTenantDefaultsAction::class);
    $this->app->scoped(SeedCoinLedgerAction::class);
    $this->app->scoped(SeedCommercialRatesAction::class);
    $this->app->scoped(IssueVerificationTokenAction::class);
}
```

Add the action-class facade IDs that the controller's constructor will type-hint against.

#### 5.9 Confirm `after_commit = true` on the `emails` queue (ADR-V2R-08, ADR-V2R-13, §10.C)

Verify and (if missing) add to `config/queue.php`:
```php
'emails' => [
    'driver' => 'redis',
    'queue'  => 'default',
    'connection' => 'default',
    'after_commit' => true, // ADR-V2R-13 — required to prevent ghost-tenant mails
    'retry_after' => 90,
],
```

If the connection name is different in this codebase, apply the same flag there. The contract we rely on: any listener that calls `Mail::queue()` or `dispatch(new ...MailJob())` will only execute after the outer `DB::transaction()` commits.

#### Verification

- [ ] Unit tests for each Action pass.
- [ ] Feature test: full registration → assert all expected rows + 1 ES bulk job dispatched, not 30 sync.
- [ ] Force a failure mid-flow → assert no orphan ES docs, no orphan MySQL rows, no orphan mail jobs.
- [ ] Horizon dashboard: `indexing` queue processes in parallel with `registration`.
- [ ] MySQL `SHOW ENGINE INNODB STATUS` shows log writer utilization <60% at 100 simulated signups/min.
- [ ] **ADR-V2R-11 check:** Pest test mocks the `App\Actions\Registration\BootstrapTenantDefaultsAction`; verify it is RESOLVED FRESH for each `Company` request (i.e., two requests on the same Octane worker do not share mutation state).
- [ ] **ADR-V2R-12 check:** synthetic deadlock test (`PDOException('40001', 'Serialization failure', 1213)` injected) → transaction retries 3 times then succeeds without surfacing to the user.
- [ ] **ADR-V2R-13 check:** synthetic rollback test (force a constraint violation mid-transaction) → `Mail::queue()` is NOT observed in Horizon, `IndexCompanyBulkJob` is NOT observed on the `indexing` queue, `ats_companies` ES index is NOT touched.
- [ ] **Audit C1 check:** verify route is `signed`, audit-logged, and throttled to 10/hr.
- [ ] **Audit C2 check:** `email_verified_at IS NOT NULL` for every user one would expect to be verified (sample query).
- [ ] **Audit H3 check:** simultaneous double-click on the verify link fires ONE confirmation mail, not two.
- [ ] **Audit H4 check:** verify HTTP returns in <50ms; the `MarkUserVerifiedJob` runs in Horizon.

#### 5.10 Email-verify URL signed + token expiry (audit C1)

In the welcome mail body, replace:
```php
$urlLink = route('get-email-verification', $user->email_verification_code);
```
with:
```php
$urlLink = URL::temporarySignedRoute(
    'get-email-verification',
    now()->addMinutes(30),
    ['code' => $user->email_verification_code],
);
```

Apply `signed` middleware to the route in `routes/web.php:360` (already in Sprint 1 task 1.5 — confirm it stayed).

In `getEmailVerification`, add:
```php
if (now()->greaterThan($user->email_verification_expires_at)) {
    return view('saas-front.email-verification', ['expired' => true, ...$this->data]);
}
```

#### 5.11 Schema migration — `email_verified_at`, `email_verification_expires_at`, UNIQUE on code (audit C2 + M3 + L1)

Migration (online, zero-downtime):
```sql
ALTER TABLE users
  ADD COLUMN email_verified_at TIMESTAMP NULL AFTER email_verification_code,
  ADD COLUMN email_verification_expires_at TIMESTAMP NULL AFTER email_verified_at,
  ADD UNIQUE KEY users_email_verification_code_unique (email_verification_code);
```

Update `User` model:
```php
class User extends Authenticatable implements MustVerifyEmail
{
    // existing $fillable / $guarded ...
    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => now(),
            'status' => 'active',
            'email_verification_code' => null,
        ])->save();
    }
}
```

Populate on every existing active signup via a one-off artisan command in the migration.

#### 5.12 Success page → redirect to a navigable URL (audit H2)

Replace the JSON-injected DOM at [SaasFrontController.php:1528–1531](../../app/Http/Controllers/SaasFront/SaasFrontController.php:1528) with:

```php
$request->session()->put('registration.just_registered_email', $user->email);
return redirect()->route('registration.success', ['id' => $user->id]);
```

New routes:
```php
Route::get('/registration-success/{id}', [SaasFrontController::class, 'registrationSuccess'])
    ->name('registration.success')
    ->middleware('throttle:register-success');
```

New `registrationSuccess($id)` method reads the email from the session, renders the existing `registration_success.blade.php`, and clears the session key on consume. Refresh-safe.

#### 5.13 Verify race protection — `DB::transaction` + `lockForUpdate` (audit H3)

Replace [SaasFrontController.php:3471](../../app/Http/Controllers/SaasFront/SaasFrontController.php:3471):
```php
DB::transaction(function () use ($code) {
    $user = User::where('email_verification_code', $code)
        ->whereNotNull('email_verification_code')
        ->lockForUpdate()
        ->first();

    if (! $user) {
        return null; // bubble up to view('saas-front.email-verification', ['expired' => true])
    }

    $user->forceFill([
        'email_verification_code' => null,
        'email_verified_at' => now(),
        'status' => 'active',
        'working_status' => 1,
    ])->save();

    return $user;
});
```

This makes double-click idempotent at the SQL level.

#### 5.14 `MarkUserVerifiedJob` — off-thread mail + ES (audit H4)

Move the side effects from [SaasFrontController.php:3481–3494](../../app/Http/Controllers/SaasFront/SaasFrontController.php:3481) into a queued job:

```php
class MarkUserVerifiedJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 5;
    public $backoff = [30, 60, 120, 240];
    public $timeout = 600;

    public function __construct(public int $userId) {}

    public function handle(): void
    {
        $user = User::withoutGlobalScopes()->findOrFail($this->userId);

        QueueMailService::dispatch(
            $user->email, 'Company Profile Confirmation',
            'email.company-profile.account-confirmation-team',
            $this->dataFor($user),
            [], $fromAddress, $senderName, [], $user->company_id,
        )->onConnection('emails');

        ElasticCompanyIndex::dispatch($user->company_id)->onConnection('emails');
        GeneralMethod::userIndexingupload($user->id);
    }
}
```

Dispatch from the controller AFTER the DB transaction commits (inside §10.C guardrails). Result: verify HTTP returns in ~50ms; mail + ES run on the queue.

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
| 3 | Sprint 3 task 3.7 (trial package) merged before product-owner sign-off | Reading the §10.D BLOCKER into the PR template; the PR cannot merge without the written confirmation linked |
| 5 | Action-class refactor subtly changes behavior | Each Action has a Pest test BEFORE the refactor; refactor; re-run tests |
| 5 | Event-listener migration changes order of side effects | Document current order; assert new order matches via tests |
| 5 | InnoDB deadlocks cause user-visible 500s | `DB::transaction(fn, 3)` retries; verify with `120 concurrent / 60s` synthetic-load test (see §10.A) |
| 5 | Octane worker leaks tenant state across requests | `scoped` binding + Pest test asserting fresh instance per request (see §10.B) |
| 5 | "Ghost tenant" mail on rollback | `after_commit = true` on `emails` connection + dispatch outside closure (see §10.C) |
| 5 | Verify-thread blocks on SMTP, ties up Octane workers | `MarkUserVerifiedJob` (5.14) — verify HTTP returns in <50ms |
| 5 | Double-click on verify link fires duplicate mail + ES reindex | `lockForUpdate()` (5.13) — idempotent at SQL level |
| 5 | Token leaks via HTTP-not-HTTPS path | `getDomainSpecificUrl` (1.10) + audit-log every verify |
| 5 | Resend endpoint enumeration → spam | Token rotation + IP throttle (2.10) |
| 5 | `email_verification_code` collision if token is shortened in future | UNIQUE index added (5.11) |
| 6 | Vite build emits different bundle hashes → asset-versioning issues | Set `APP_ENV=production`; verify asset paths resolve |

---

## 5. Rollback Plan (per sprint)

| Sprint | Rollback procedure |
|---|---|
| 1 | Revert `.env` (`CACHE_STORE=database`, `SESSION_DRIVER=database`); re-add `/run-queue` route; restore `signed` middleware removal. **Risk: low** (1 PR, mostly env + route changes) |
| 2 | Turn off Turnstile widget; remove `throttle:` middleware; restore old asset paths via Git revert. **Risk: low** |
| 3 | Revert query changes; restore `Package::first()` to old behavior. **Risk: medium** (DB indexes may stay; test thoroughly). Sprint 3 task 3.7's `where('is_trial',1)` requires §10.D sign-off before the rollback-on-bad-data path is exercised. |
| 4 | Re-add `super_password` writes if removal breaks auth. **Risk: low** |
| 5 | The monolith is preserved in git history. Revert the `companyRegister()` rewrite to restore old behavior. **Risk: medium** (large surface; tests must pass). Also revert `scoped` bindings (Action classes revert to Laravel's default `transient`), revert `after_commit` on `emails`, revert `DB::transaction(fn, 3)` to plain `DB::transaction(fn)`. |
| 6 | Revert Vite changes; restore inline scripts. **Risk: low** |

**Hardening rollback notes (§10):**
- If a `scoped` Action binding causes a ServiceProvider boot failure (rare — most likely caught at PR time), swap to `singleton` + manual reset on each `execute()` for that one class. NOT recommended long-term; the cross-tenant leak risk is real.
- If `after_commit = true` causes a mail-batching regression (e.g. mail now takes >5 min to deliver because the outer transaction is also waiting), the fix is to ensure ALL register-flow dispatched jobs run from outside the transaction closure — never disable `after_commit = true` for the `emails` connection while a registration transaction is open.

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
- [ ] **Hardening acks (Sprint 5+ only):**
  - [ ] §10.A — `DB::transaction` retries verified.
  - [ ] §10.B — Action classes bound as `scoped`.
  - [ ] §10.C — event dispatch happens OUTSIDE the transaction closure.
- [ ] **Sprint 3 task 3.7 only:** §10.D product-owner sign-off memo is linked in the PR description.

---

## 7. Sprint Summary

| Sprint | Effort | Cumulative | Outcomes |
|---|---|---|---|
| **1** | 0.5 day | 0.5 day | Production-safe at current load; -50% MySQL QPS; verify-route signed (1.5); HTTPS-only mail links (1.10) |
| **2** | 1.5 days | 2 days | DoS surface closed; captcha + rate limit; page renders clean; resend enumeration hardened (2.10) |
| **3** | 2 days | 4 days | P95 -400ms; -80% MySQL QPS |
| **4** | 1 day | 5 days | GDPR-safe; Breeze cleaned up; wrong-token page wired (4.10) |
| **5** | 6 days | 11 days | P95 -250ms; ES drift-free; horizontally scalable; verify-thread offloaded (5.10–5.14) |
| **6** | 2 days | 13 days | TTI -300ms; single-source register blade |
| **Buffer** | 3 days | 16 days | Review, prod-deploy, post-deploy verification |

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
- [ ] **§10.A** — 100-concurrent signups test produces zero user-visible 500s (no deadlock escapes).
- [ ] **§10.B** — Two requests on the same Octane worker resolve fresh `CreateCompanyAction` instances.
- [ ] **§10.C** — Synthetic rollback test produces zero mail jobs and zero ES bulk jobs observed in Horizon.
- [ ] **§10.D** — Trial package confirmation memo archived in `/docs/V2_Registration/product-owner-acks/`.
- [ ] **Audit C1** — Verify token expiry enforced; signed middleware applied; 10/hr/IP throttle on the route.
- [ ] **Audit C2** — `email_verified_at IS NOT NULL` for every user with `status='active'` after the backfill.
- [ ] **Audit H1** — `GET /resend/{id}` is rejected without a valid session token / signed URL.
- [ ] **Audit H2** — `GET /registration-success/{id}` returns the success page after a fresh registration; refresh does not replay the POST.
- [ ] **Audit H3** — Synthetic double-click test fires one confirmation mail, not two.
- [ ] **Audit H4** — Verify HTTP P95 <50ms; side-effect work observed in Horizon, not on the request thread.

---

## 10. Hardening Notes (concurrency, Octane, fallbacks)

These four notes are **non-negotiable** for an enterprise-grade multi-tenant registration flow. They prevent the most common production failures in this exact architecture: cross-tenant data leaks (Octane), phantom mail/ES writes on rollback (after_commit), InnoDB deadlock-failures under concurrent signups (retry), and silent wrong-trial-package regressions (sign-off).

---

### 10.A Transaction Deadlock Resilience (ADR-V2R-12)

**Failure mode:** Under 10k signups/day and `--innodb_thread_concurrency` defaults, two concurrent transactions on the same row group (e.g. permissions cache + role lookup) raise `PDOException` with SQLSTATE `40001` (serialization failure) or `1213` (deadlock). Laravel's default `DB::transaction()` treats this as fatal and surfaces a 500. For a public-facing registration, that's a hard outage.

**Fix:** Laravel 11's `DB::transaction()` accepts a 2nd argument `attempts` — re-runs the closure on `deadlock_aware` `PDOException`s:

```php
$company = DB::transaction(
    fn () => $this->createCompanyAction->execute($request),
    3, // up to 3 total attempts (1 original + 2 retries)
);
```

**Idempotency requirement:** The 2nd and 3rd runs must be safe. Since each Action receives a `Request` and re-derives state (does NOT mutate Action-object properties), running it twice is safe up to the `INSERT` moment — the unique constraints on `companies.sub_domain`, `users.email`, and `users(company_id,email)` will reject duplicates on retry, which we want (rather than creating duplicate rows).

**Pair with `innodb_deadlock_detect = ON`** (default) and a reasonable `innodb_lock_wait_timeout` (default 50s is fine for this flow).

**Verification:** A Pest test that wraps the closure in a `Mock` that throws `PDOException('40001', 'Deadlock', 1213)` on first call, then succeeds; assert the user-visible behavior is success.

---

### 10.B Octane Worker State Hygiene (ADR-V2R-11)

**Failure mode:** Octane/FrankenPHP keeps the framework booted between requests. If Action classes are bound as `singleton` (or as a default eager-resolved binding), two requests processed by the same worker could share a `CreateCompanyAction` instance whose `$lastRequest` or `$pendingCompany` property carries data from the previous tenant. **This is a cross-tenant data leak** — the most catastrophic failure mode in a multi-tenant SaaS.

**Fix:** Bind every Action as `scoped`:

```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    parent::register();

    $this->app->scoped(CreateCompanyAction::class);
    $this->app->scoped(CreateCompanyUserAction::class);
    $this->app->scoped(BootstrapTenantDefaultsAction::class);
    $this->app->scoped(SeedCoinLedgerAction::class);
    $this->app->scoped(SeedCommercialRatesAction::class);
    $this->app->scoped(IssueVerificationTokenAction::class);
}
```

Laravel's `scoped()` binding lifecycle: instance is created on first `resolve()` within a request and cleanly discarded at the end of that request. Octane enforces this automatically.

**Verification:** A Pest test that resolves `CreateCompanyAction`, mutates a hypothetical `$lastRequest`, then resolves again — assert the second instance has the property at its initial value (proving fresh instance).

**Anti-patterns to reject during code review:**
- `singleton` on any Action class.
- Static properties on Action classes.
- Action classes that accept or store state in static helpers.
- Constructor injection of `Request` (would carry tenant context across requests).

---

### 10.C Decoupled Post-Commit Events (ADR-V2R-13)

**Failure mode:** The classic "ghost tenant" — a user who registered, immediately received a welcome email, then had their row rolled back 5 seconds later because some downstream step (e.g. permission attach) failed. In production this happened above already: the `QueueMailService::dispatch` at `SaasFrontController.php:1360` is **inside** the open transaction.

**Fix (three layers, in order):**

1. **Application-level — dispatch AFTER commit:**
   ```php
   $company = DB::transaction(fn () => $this->createCompanyAction->execute($request), 3);
   $user    = DB::transaction(fn () => $this->createCompanyUserAction->execute($request, $company), 3);

   // Outside any transaction — committed state.
   event(new CompanyRegistered($company, $user, $request->validated()));
   event(new UserRegistered($user, $request->password));
   ```

2. **Queue-level — `after_commit = true`:** Even if a listener queues a job from inside a `CompanyRegistered` listener that runs in a `DB::transaction` block, set `'after_commit' => true` on the `emails` connection (Laravel 11 feature) so the queued job is held until the outermost transaction commits:
   ```php
   // config/queue.php
   'emails' => [
       'driver' => 'redis',
       'queue'  => 'default',
       'connection' => 'default',
       'after_commit' => true,
   ],
   ```

3. **Listener-level — `ShouldQueue`:** A listener that runs `Mail::queue()` must implement `ShouldQueue` so the mail job enters the queue and never fires synchronously from a request worker.

**Verification:** A Pest test that throws an exception inside the closure during a `DB::transaction` — assert that NO `Mail::queue()` call is observed (via `Mail::fake()`), NO `IndexCompanyBulkJob` is dispatched (via `Bus::fake()`), and the `ats_companies` ES index is untouched (via `ElasticTest::assertNoIndexCall()`).

---

### 10.D Trial Package Assignment BLOCKER (ADR-V2R-14)

**Why this is a blocker:** The current `Package::first()` at `SaasFrontController.php:419` and `:2303` returns the lowest-id package. The audit confirms a missing `where('is_trial', 1)` filter. If `id=1` happens to be the trial package today, the code is correct by accident. If `id=1` is ever changed (package re-ordering, import, manual id-fix), the wrong package is assigned to every new signup — and that cascades into:

- Wrong billing plan attached to the new tenant.
- Wrong coin/email allowance (commercial rates table seeded from this package).
- Wrong email template (`welcomeTrial` vs `welcomeStandard`).
- Wrong default role permissions.

**Verification protocol — must complete BEFORE Sprint 3 task 3.7 merges:**

1. Connect to the production DB read-only (`SELECT` privileges only).
2. Run:
   ```sql
   SELECT id, name, is_trial, status, created_at
   FROM packages
   ORDER BY is_trial DESC, id ASC;
   ```
3. Confirm in a written memo (Slack/email):
   - Which `packages.id` is the trial package in production?
   - Is `is_trial = 1` set on it?
   - Are there any other `is_trial = 1` rows that would collide?
   - Are there any `is_trial = 0` rows with `id < (the trial package id)` that the current `Package::first()` would mistakenly pick?
4. Product owner (or delegation: a signatory with package-billing authority) confirms in writing.
5. **Only then** merge the `where('is_trial', 1)` change.

**If the product owner is unavailable**, the change does NOT ship. The Sprint 3 task 3.7 row stays in `pending` state. The other 9 Sprint 3 tasks proceed independently.

**Fallback if we need to ship without blocker clearance:** Keep the existing `Package::first()` and add a runtime guard:
```php
$package = Package::where('is_trial', 1)->first() ?? Package::find(1);
\Log::warning('Trial package lookup fell back to id=1', [
    'is_trial_hit' => Package::where('is_trial', 1)->exists(),
]);
```
This preserves current behavior while emitting a signal that the lookup was non-ideal. **Do not treat this as a long-term solution** — the blocker must be cleared eventually.

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
