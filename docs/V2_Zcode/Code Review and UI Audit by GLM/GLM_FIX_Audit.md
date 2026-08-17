# GLM_FIX_Audit — Full Codebase Audit Report

> **Purpose of this document:** Master audit of the JERO codebase (multi-tenancy security, backend
> architecture, UI/UX layer) produced by the GLM (ZCode) review on **2026-08-17**. It records verified
> findings with file:line evidence, a risk matrix, and feeds the companion execution document
> [GOD_LEVEL_IMPLEMENTATION_PLAN.md](GOD_LEVEL_IMPLEMENTATION_PLAN.md).
>
> **Auditor:** ZCode (GLM) | **Branch audited:** `staging` @ `4be24090` | **Scope:** `app/`, `routes/`,
> `resources/views/`, `database/schema/mysql-schema.sql`, `docs/`

---

## 1. Executive Summary

JERO is functional but carries **critical multi-tenancy debt**. The single highest risk:
**6,025 of 6,938 raw `DB::` queries in controllers have no `company_id` filter**, several of which are
actively exploitable IDOR (Insecure Direct Object Reference) endpoints exposing candidate PII
(resumes, names, phones, CTC). Two safety layers exist but are both compromised:

1. **Eloquent global scopes** — exist on 31 models, but `DB::table()` bypasses them entirely, and
   6 models have their scopes commented out (including `Job`).
2. **Manual `where('company_id', ...)`** — the only protection for raw queries; applied in only
   913 of 6,938 calls. No central enforcement exists (no TenantScope class, no middleware, no CI rule).

Secondary findings: unauthenticated debug routes in production, a live `dd()` killing a route,
18 `env()` violations that return `null` after `config:cache`, god controllers up to 16,182 lines,
near-zero test coverage (35 test files for 239 controllers), and a UI layer where 57% of the
1,642 blade files embed their own `<style>` blocks.

---

## 2. Multi-Tenancy Security Audit (CRITICAL)

### 2.1 How tenancy is supposed to work

- Column-based multi-tenancy: shared DB, `company_id` discriminator. **93 of ~219 tables** have a
  `company_id` column (verified against `database/schema/mysql-schema.sql`).
- Eloquent models add a `company` global scope in `boot()`/`booted()` — but each model copy-pastes
  its own inline closure (no shared `TenantScope` class exists).
- **`docs/ARCHITECTURE.md:25-29`** already carries a `[WARNING]` that scoping is inconsistent.
- **`docs/AI_RULES.md` rule 10** requires manual `company_id` filtering on raw queries — a human
  process, not a system guarantee.

### 2.2 Broken safety layer #1 — Eloquent global scopes

| Status | Models | Detail |
|---|---|---|
| **Active** | 25 | `JobApplication`, `Company`, `Client`, `CandidateStatus`, `BenchApplication`, `Document`, `InterviewSchedule`, `Department`, `Designation`, `Skill`, `ThemeSetting`, `TodoItem`, etc. |
| **Disabled (commented out)** | 6 | `Job` (`app/Models/Job.php:30-34` — scope registered but WHERE clause commented → empty body), `ApplicationStatus` (`:14`), `NoticePeriod` (`:14`), `OpenworkspaceJob` (`:31`), `OpenworkspaceInterviewSchedule` (`:18`), `Role` (`:27`) |
| **No scope at all** | 64 | `User` (only a local `scopeCompany()`, never auto-applied), `Comment`, `MeetingSchedule`, `Ticket`, `Subscription`, all `Elastic*` models, etc. |

**Inconsistent superadmin bypass logic (verified by direct read):**

- `JobApplication.php:27` skips scope when `!auth()->user()->is_superadmin` (flag check).
- `Company.php:63-67` skips when `$user->roles->count() > 0` (roles check) and console.
- Different bypass rules on different models = unpredictable behavior for superadmin/console contexts.

### 2.3 Broken safety layer #2 — raw `DB::table()` bypasses everything

`DB::table()` never passes through Eloquent, so **no global scope applies**. Counts (grep across
`app/Http/Controllers/`):

- Total raw `DB::` calls: **6,938**
- With explicit `company_id` filter: **913**
- **Unscoped: 6,025**

### 2.4 Classification of the 6,025 (risk triage)

| Class | Est. count | Risk | Action |
|---|---|---|---|
| Global/reference tables (`countries`, `global_settings`, `packages`, `payment_settings`) | ~800 | None | Exempt |
| SuperAdmin controllers (cross-company by design) | ~600 | Low | Exempt |
| Context-safe (IDs pre-filtered by scoped Elastic query; UUID lookups) | ~1,500 | Low | Verify + exempt-comment |
| Tables with no `company_id` column | ~700 | N/A | Exempt |
| **Tenant tables + sequential IDs + no filter** | **~2,400** | **HIGH** | **Fix** |

### 2.5 Verified exploitable findings (with evidence)

| # | File:Line | Table | Issue | Risk |
|---|---|---|---|---|
| 1 | `HomeController.php:800` | `job_applications` | `where('id', $id)->first()` — resume path builder, sequential int ID. Company A fetches Company B candidate record (name used in filename). | **HIGH — IDOR / PII** |
| 2 | `ServiceCompanyJobApplicationController.php:1489` | `job_applications` | `resumeAsPdf` fetch by ID only — resume download endpoint. | **HIGH — IDOR / PII** |
| 3 | `HomeController.php:568` | `interview_questions` | Delete by ID only — any user deletes any tenant's questions. | **HIGH — destructive** |
| 4 | `ServiceCompanyJobApplicationController.php:196` | `candidate_job_skill_exp` | Delete by `candidate_id`+`job_id`, no company guard. | MED-HIGH — destructive |
| 5 | `ServiceCompanyJobApplicationController.php:358` | `application_additional_detail` | Same pattern. | MED-HIGH — destructive |
| 6 | `PaymentController.php:120-166` | `cart_transaction_history`, `coin_transection_logs`, `top_up_logs`, `carts` | Lookup/update by `payment_orderId` only; tables have `company_id`. Cross-tenant payment status/coin manipulation if order ID known. | MEDIUM |
| 7 | `PaymentController.php:184-215` | `link_payments` | Lookup by `payment_link_id` only (downstream coin updates do use `company_id`). | MEDIUM |
| 8 | `HomeController.php:3598` | `users` (+joins) | Returns **all users of all companies** (names, emails, roles). | HIGH if exposed to tenant users |
| 9 | `HomeController.php:4876` | `jobs` | Fetches ALL jobs across all tenants (Elastic reindex helper — must be superadmin-only route). | Verify route guard |
| 10 | `HomeController.php:5548` | `add_job_wise_condidates` | Last 50 rows across all tenants. | Verify route guard |
| 11 | `ServiceCompanyJobsController.php:87,1271` | `jd_details` | Autocomplete searches all tenants' JD titles — cross-tenant data disclosure in suggestions. | LOW-MEDIUM |
| 12 | `ServiceCompanyJobApplicationController.php:545` | `interview_schedules` | Pluck schedule dates by assignment ID only. | MEDIUM |
| 13 | `ServiceCompanyJobApplicationController.php:2392` | `job_assign` | Partner emails plucked by `job_id` without company check. | MEDIUM |

**Correctly scoped counter-examples (do not break during fixes):** `PaymentController.php:119`
(`company_coins`), `HomeController.php:1054-1066` (`commericials`, `commericial_details`),
`HomeController.php:1512` (`job_additional_detail`), `ServiceCompanyBenchCandidatesController.php:183-254`
(`job_applications`, `bench_place`), `:2274,2379` (`bench_assign` — triple-scoped),
`SuperAdminCompanyController.php:1771` (correct `company_id` OR `thirdparty_company_id` marketplace pattern).

### 2.6 Intentional cross-tenant flows (talent marketplace — features, not bugs)

`staffing_company_relation`, `recommendation_relation`, and `thirdparty_company_id` on
`add_job_wise_condidates` implement JERO's talent-trading concept. Fixes must use the pair pattern
(already canonical at `SuperAdminCompanyController.php:1771`):

```php
->where(function ($q) use ($companyId) {
    $q->where('company_id', $companyId)
      ->orWhere('thirdparty_company_id', $companyId);
})
```

### 2.7 Schema gaps

- 93 tables have `company_id`; only ~31 models exist with scopes → 60+ tenant tables have **no
  Eloquent-layer protection at all**.
- Every added `WHERE company_id = ?` must hit an index — index coverage per tenant table must be
  verified (see Plan Step 19).

---

## 3. Backend Architecture Audit

### 3.1 God controllers (27 controllers over 500 lines; top 5 over 10K)

| Controller | Lines |
|---|---|
| `HomeController.php` | **16,182** |
| `ServiceCompany/ServiceCompanyJobsController.php` | 14,320 |
| `ServiceCompany/ServiceCompanyJobApplicationController.php` | 13,526 |
| `SuperAdmin/SuperAdminCompanyController.php` | 12,038 |
| `ServiceCompany/ServiceCompanyAppTrackerController.php` | 11,474 |
| `ServiceCompany/ServiceCompanyBenchCandidatesController.php` | 7,471 |
| `ProductCompany/ProductCompanyJobsController.php` | 7,226 |
| `ProductCompany/old_ProductCompanyJobsController.php` | 7,046 (dead) |
| `ServiceCompany/ServiceCompanyCvTrackerController.php` | 6,490 |
| `ServiceCompany/ServiceCompanyTeamController.php` | 4,289 |

Counts: 239 controllers, 94 models, 45 services, 43 jobs.

### 3.2 Recent commit pattern review (are we following vertical slices?)

| Commit | Pattern | Verdict |
|---|---|---|
| `f568f45f` "commercials" | Logic in `AccountSettingsService`, thin controller | ✅ Correct vertical slice |
| `6fce2cf9` "Offer template" | 9 lines of business logic added into the 13.5K-line controller (`createOffer()` ~line 4435) | ❌ Controller dump |
| `4be24090` "Services" | Blade-only label rename | Neutral |
| `bc480419` | `env()` → `config()` fixes | Neutral/good |

### 3.3 Routes

| File | Routes | Issues |
|---|---|---|
| `routes/web.php` (1,646 lines) | 1,191 | `:46` `/mail-test` (unauth, mails hardcoded address), `:78` `/redis-test`, `:83` `/r2test` (unauth write to prod storage), `:106` `/test-es`; commented dd at `:1631` |
| `routes/service.php` (625 lines) | 459 | **`:88` live `dd($cities)` — `/search-cities` route always dies** |
| `routes/product.php` | 229 | — |
| `routes/superadmin.php` | 221 | — |
| `routes/service_old.php` | — | Dead backup file in tree |

### 3.4 `env()` outside `config/` — 18 violations (returns `null` after `config:cache`)

Key offenders: `PhonePePaymentController.php:12-15` (4 gateway creds), `HomeController.php:240`,
`ChatGptService.php:9`, `ZoomService.php:32-33`, `PaymentSettings.php:18-20` + `StripeSettings.php:18-20`
(duplicate traits, same Stripe env calls), `Helper/GeneralMethod.php:6180` (`CUSTOM_ENCRYPTION_KEY`).

### 3.5 Test coverage

35 test files (18 Feature / 10 Unit / 1 Performance / 1 Chaos) for 239 controllers + 94 models.
No tests reference the god controllers. No tenant-isolation tests exist.

### 3.6 Other debt

- **Queues:** only 108 `dispatch()` calls across the controller layer — payments, resume parsing,
  bulk email largely synchronous.
- **Elasticsearch:** no write-sync (no observers/hooks); indexes rebuilt via artisan commands only →
  stale search results.
- **Dead code:** `old_ProductCompanyJobsController` (7K), `OLD_ServiceCompanyDashboardController` (3.8K),
  `InterviewScheduleController_old` (3.2K), `routes/service_old.php`, `resources/views/staffing_company/BK/`.

---

## 4. UI/UX Audit (vs `docs/UI_UX_STANDARDS.md`)

### 4.1 Scale

1,642 blade files; 204 files >1,000 lines. Largest: `service_company/job-applications/showdetail.blade.php`
(10,491), `create-offer.blade.php` (6,081), `dashboard/index.blade.php` (4,833), `account/index.blade.php` (4,799).

### 4.2 Findings

| # | Finding | Evidence | Severity |
|---|---|---|---|
| 1 | **943/1,642 files (57%) embed `<style>` blocks**; pervasive inline `style=` with hardcoded hex colors | `account/v2/tabs/services.blade.php` — 20+ inline styles, 18 unique hex values | High |
| 2 | **Font conflict:** Tailwind config says `Figtree`, standards mandate `Poppins` | `tailwind.config.js:15` vs `UI_UX_STANDARDS.md` | High |
| 3 | **16px font cap violated** via Metronic `fs-3` (24px) / `fs-4` | `services.blade.php:4`, `default-terms-list.blade.php:63,108,124,140` | Medium |
| 4 | **Card system ignored:** only 8 files use `.section-card`, 4 use `.cardshow`, 4 use `.simf-toolbar-card` (mandatory since 2026-08-01) | grep counts | High |
| 5 | **No dark mode / a11y:** zero `data-bs-theme` / `prefers-color-scheme`; standards require WCAG AA + reduced motion | grep across layouts | Medium |
| 6 | **561 duplicated view paths** across `admin/`, `staffing_company/`, `service_company/`, `product_company/` + `BK/` backup dir | path diff | High (maintenance) |
| 7 | **Responsiveness:** media queries cover 1024–1440 & 1440–2560 only; fixed widths (`width:1025px`) break 1366×768 target | `account/index.blade.php:21-26`, `#kt_activitiesJob` | Medium |
| 8 | **Button inconsistency:** Metronic `btn-primary` vs inline-styled buttons vs custom `.prev-btn/.submit-btn` with raw hex | `services.blade.php`, `create-offer.blade.php` | Medium |
| 9 | **Missing states:** no loading/empty/error states on newer tabs | `services.blade.php`, `default-terms-list.blade.php` (hardcoded rows) | Medium |
| 10 | Vite/Tailwind configured but effectively unused; real assets served raw from `metronic_assets/` + `public/assets/` | `vite.config.js`, layout `<link>` tags | Low |

### 4.3 Positive patterns to preserve

- `create-offer.blade.php` correctly enforces Poppins + 16px cap via scoped CSS (`:115-132`).
- Stepper component structure (`.co-stepper`) is well-organized — candidate for extraction into a shared component.
- `account/v2/tabs/*` file sizing (120 lines) is the right granularity for new views.

---

## 5. Consolidated Risk Matrix

| Risk | Severity | Likelihood | Effort to fix |
|---|---|---|---|
| Cross-tenant IDOR on resume/candidate endpoints | **Critical** | Confirmed code paths | Hours |
| Cross-tenant DELETE endpoints | **Critical** | Confirmed code paths | Hours |
| Payment record/coin manipulation | High | Medium (UUID gates) | 1 day |
| Disabled model scopes (`Job` etc.) | High | Certain (every Eloquent Job query unscoped) | Days (regression risk) |
| Unauthenticated debug routes (`/r2test`, `/mail-test`) | High | Trivially reachable | Minutes |
| `env()` outside config (prod nulls) | High | Fires on `config:cache` | 1 day |
| God controllers | High (maintainability) | Ongoing | Weeks |
| Near-zero test coverage | High | Ongoing | Weeks |
| Stale ES (no write-sync) | Medium | Ongoing | Days |
| Sync heavy work (108 dispatches) | Medium | Ongoing | Weeks |
| UI fragmentation (inline CSS, font war, no card system) | Medium | Ongoing | Weeks |
| Dead code in tree | Low | — | Hours |

---

## 6. What NOT to do

- Do **not** enable the `Job` global scope in one bulk change — superadmin/admin/cron and marketplace
  flows read Jobs cross-tenant today; convert those to `withoutGlobalScope()` first (pattern already at `Job.php:81`).
- Do **not** blanket-add `company_id` filters to marketplace tables (`staffing_company_relation`,
  `recommendation_relation`, `thirdparty_company_id` flows) — use the pair-where pattern (§2.6).
- Do **not** invent migrations for missing `company_id` indexes (AGENTS.md rule 2 — schema truth is
  `mysql-schema.sql`; coordinate DBA-approved changes).
- Do **not** refactor UI while fixing tenancy — separate branches, separate PRs.

---

*Companion execution document: [GOD_LEVEL_IMPLEMENTATION_PLAN.md](GOD_LEVEL_IMPLEMENTATION_PLAN.md)*
