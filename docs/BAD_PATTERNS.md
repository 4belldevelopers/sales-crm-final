# Bad Patterns / Tech Debt Register

> Findings from static inspection. Each is **descriptive, not a mandate to refactor** —
> do not change production code unless explicitly asked (per task constraints). Use
> this list to (a) avoid copying these patterns and (b) prioritize future cleanup.
> Severity: 🟥 high · 🟧 medium · 🟨 low.

---

## Architecture / structure

### 🟥 God controllers
Several controllers are enormous and mix routing, validation, business logic, ES
queries, and view data:
| File | ~Size |
|---|---|
| `app/Http/Controllers/HomeController.php` | ~813 KB |
| `ServiceCompany/ServiceCompanyJobsController.php` | ~544 KB |
| `ServiceCompany/ServiceCompanyJobApplicationController.php` | ~537 KB |
| `SuperAdmin/SuperAdminCompanyController.php` | ~510 KB |
| `ServiceCompany/ServiceCompanyAppTrackerController.php` | ~311 KB |
| `ProductCompany/ProductCompanyJobsController.php` | ~282 KB |
**Impact:** unreadable, untestable, very high token cost for agents, merge-conflict
magnet. **Mitigation now:** grep for methods; never read whole. **Future:** extract
to Services/Actions per use case.

### 🟥 No repository / thin-controller layer
Business logic lives directly in controllers and a few large services. No clear
domain layer; data access mixes Eloquent and raw `DB::table()`.

### 🟧 No formal module boundaries
`nwidart/laravel-modules` is a dependency but there is **no `Modules/` directory** —
modularity is namespace-convention only. Dead dependency or abandoned migration.

---

## Dead code / clutter (🟧 — ignore these when working)

- `OLD_InterviewScheduleController.php`, `OLD_2_InterviewScheduleController.php`,
  `old_ProductCompanyJobsController.php` (and other `OLD_*`/`old_*`).
- Root-level `ServiceCompanyJobApplicationController.php` (~526 KB) — a stray copy
  outside `app/Http/Controllers/`.
- `oldBackUp/` directory.
- Stray non-PHP-class files inside `app/`: `index.blade.php` files under
  `Controllers/ProductCompany/` and `Controllers/Admin/`, `app/Models/error_log`,
  `app/Models/constants.php`.
- `composer-setup.php` + the committed `frankenphp` (58 MB) binary at repo root.
- Duplicate-looking commands: `SendInterviewReminder` vs `SendInterviewReminders`
  vs `InterviewReminderCommand`.
- Lowercase/oddly-named models: `client_city_jobs.php`, `jobCountry.php`,
  `constants.php` (violate PSR/Eloquent conventions).

---

## Data / tenancy correctness

### 🟥 Inconsistent multi-tenant scoping
`Models/Company.php` applies a `company` global scope, but `Models/JobApplication.php`
has its company scope **commented out** (and imports `SoftDeletes` without using the
trait). Tenant isolation depends on each query — **raw `DB::table()` queries bypass
scopes entirely** → potential cross-tenant data leakage. Audit before trusting.

### 🟥 Schema not in version control
Only 3 stock migrations; real schema is in the live `dbmaster` DB. No reproducible
schema, no FK constraints, no index definitions in code. **[HIGH RISK]** for any DB
change. See [adr/0005-schema-in-live-db.md](adr/0005-schema-in-live-db.md).

### 🟧 Hand-listed `select()` columns + raw queries
Services like `ElasticCandidatesIndex` select 50+ columns by hand and use
`DB::raw('DATE(...)')` in WHERE (`Company::isFeatured`) — non-sargable, defeats
indexes, brittle to column changes.

---

## Performance

### 🟥 Single shared Horizon queue
All jobs run on `default` (`config/horizon.php`). Bulk resume uploads, mass mail, and
ES re-indexing starve interactive jobs. **Fix:** dedicated queues
(`uploads`, `mail`, `indexing`) + tuned supervisors. See [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md).

### 🟧 Dual-write MySQL + Elasticsearch
Every candidate/job mutation writes MySQL then (async) ES with no transactional
guarantee → drift; failed index jobs silently desync data.

### 🟧 N+1 / wide-row risk on `job_applications`
Central, wide table read on every tracker/dashboard. Confirm eager loading and
indexes. Likely top latency source.

### 🟨 Repeated config reads
`smtp_settings` queried ~7× across mail services per send path — cache it.

---

## Security / hygiene

### 🟥 Secrets committed to `.env` in the working tree
`.env` contains live credentials (DB root password, **Elasticsearch password in
plaintext**, payment + cloud keys). Ensure `.env` is gitignored and rotate exposed
secrets. `composer-setup.php` and PHP installers committed too.

### 🟧 Debug/test routes in `routes/web.php`
Live routes like `/mail-test` (sends a real email) and `/whatsapp/connect` ship in
the route table. Remove or gate behind `local` env.

### 🟧 Empty exception handler
`bootstrap/app.php` `withExceptions()` is empty — no centralized reporting/shaping.

---

## Testing

### 🟥 Effectively no test coverage
`tests/` contains only default Breeze/Laravel scaffolding (`ExampleTest`, auth
tests, `ProfileTest`). None of the ATS business logic (matching, billing, ES sync,
tenancy) is tested. High regression risk on any change.
