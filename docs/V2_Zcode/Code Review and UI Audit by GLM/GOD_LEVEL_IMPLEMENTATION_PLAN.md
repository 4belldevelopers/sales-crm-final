# GOD_LEVEL_IMPLEMENTATION_PLAN — Master Execution Plan

> **Purpose of this document:** The complete, ordered execution plan to remediate everything found in
> [GLM_FIX_Audit.md](GLM_FIX_Audit.md) — multi-tenancy security (the 20-step audit), god-controller
> decomposition, UI standardization, and reliability hardening. "GOD level" = nothing left out:
> every step has an owner-ready scope, acceptance criteria, and verification command.
>
> **Created:** 2026-08-17 | **Source audit:** GLM_FIX_Audit.md | **Branch:** `audit/tenant-isolation` off `staging`
> **Duration:** ~8–10 weeks total (tenancy track ~5 weeks, then parallel tracks) | **Team:** 1–2 devs

---

## Governing Rules (from AGENTS.md / docs — apply to every step)

1. Verify every change: `php -l`, `./vendor/bin/pint`, `php artisan route:list --path=<x>`, `php artisan test` (see `docs/VERIFY.md`).
2. Document intent on every new class/method; raw queries need an intent + tenancy note (`docs/CODE_DOCUMENTATION_STANDARD.md`).
3. DB truth = `database/schema/mysql-schema.sql`. No invented migrations.
4. One logical change per commit (surgical `git revert` is the rollback plan for risky steps).
5. Keep MySQL ↔ Elasticsearch in sync on writes to indexed entities.
6. Never bulk-enable disabled scopes without first converting legitimate cross-tenant reads.

---

# TRACK A — TENANCY AUDIT (20 Steps) — Days 1–32

## Phase A0 — Preparation & Safety Net (Days 1–3)

### Step 1 — Audit branch + baseline snapshot
- `git checkout staging && git checkout -b audit/tenant-isolation`
- Generate baseline: count `DB::table(` occurrences per controller file → save `.ai/audit/baseline-queries.md` (expected: 6,938 total / 6,025 unscoped).
- **Acceptance:** baseline committed; numbers match audit §2.3.
- **Effort:** 1 hr

### Step 2 — Kill debug/test routes
- Remove or wrap in `app()->environment('local')`:
  `routes/web.php:46` (`/mail-test`), `:78` (`/redis-test`), `:83` (`/r2test`), `:106` (`/test-es`).
- Remove live `dd($cities)` at `routes/service.php:88` (route `/search-cities` currently always dies).
- **Verify:** `php artisan route:list --path=mail-test` → empty; `GET /search-cities` returns JSON.
- **Effort:** 30 min

### Step 3 — Tenant-isolation test harness
- Create `tests/Fixtures/TenantFixture.php` — companies A & B, users, seeded candidate/job/payment rows for B.
- Create `tests/Traits/AssertsTenantIsolation.php` — `actingAs($userA)` + probe B's IDs → assert 403/404/empty (intent-commented per docs standard).
- Create `tests/Feature/TenantIsolationTest.php` — starts with the audit §2.5 endpoints.
- **Acceptance:** `php artisan test --filter=TenantIsolationTest` green.
- **Effort:** 1 day

## Phase A1 — P0 Hotfixes (Days 3–5)

### Step 4 — Fix resume/candidate IDOR
- Add `->where('company_id', auth()->user()->company_id)` at `HomeController.php:800` and
  `ServiceCompanyJobApplicationController.php:1489`.
- Sweep every `DB::table('job_applications')->where('id'` pattern across all controllers in one pass.
- **Verify:** TenantIsolationTest proves cross-tenant 404; sequential enumeration (id+1…) returns nothing.
- **Effort:** 2–4 hrs

### Step 5 — Fix cross-tenant DELETE endpoints
- Guard `HomeController.php:568` (`interview_questions`), `ServiceCompanyJobApplicationController.php:196`
  (`candidate_job_skill_exp`), `:358` (`application_additional_detail`).
- Sweep all `->delete()` raw calls on tenant tables (93-table list from Step 11).
- **Verify:** cross-tenant delete affects 0 rows (assert in test).
- **Effort:** 4 hrs

### Step 6 — Fix PaymentController lookups
- Scope lookups/updates at `PaymentController.php:120-166` (`cart_transaction_history`,
  `coin_transection_logs`, `top_up_logs`, `carts`) and `:184` (`link_payments`) with `company_id`
  alongside `payment_orderId`.
- **Caution:** webhook/callback context — where company isn't derivable yet, resolve via the order's
  stored `company_id` first, then filter. Test in Razorpay sandbox.
- **Effort:** 1 day

## Phase A2 — Centralized Enforcement (Days 5–10)

### Step 7 — Canonical `TenantScope` + `BelongsToTenant`
- `app/Scopes/TenantScope.php` — single implementation: skip `runningInConsole()`, skip `is_superadmin`,
  else `where(table.company_id, user->company_id)`. (Resolves the `is_superadmin` vs `roles->count()`
  bypass inconsistency — audit §2.2.)
- `app/Traits/BelongsToTenant.php` — boots the scope.
- **Acceptance:** unit test: scoped for tenant user, unscoped for superadmin + console.
- **Effort:** 1 day

### Step 8 — Migrate 31 models to the trait; re-enable the 6 disabled scopes
- Replace inline closures with the trait (behavior-identical for the 25 active).
- Re-enable ONE MODEL PER COMMIT (order: `ApplicationStatus` → `NoticePeriod` → `Role` →
  `OpenworkspaceJob` → `OpenworkspaceInterviewSchedule` → **`Job` last, biggest blast radius**).
  Before each: grep that model's usages; convert legitimate cross-tenant reads to
  `withoutGlobalScope(TenantScope::class)` (pattern exists at `Job.php:81`).
- **Verify after each commit:** `php artisan test` + manual pass of the affected module.
- **Effort:** 2 days

### Step 9 — `tenant_table()` raw-query helper
- `app/Helper/TenantDB.php` + global helper: wraps `DB::table($t)`, auto-applies `company_id` when
  `$t` is in the explicit `TENANT_TABLES` constant (93 verified tables — a static list, NOT
  `Schema::hasColumn()`, which queries schema live per call and is too slow under Octane).
- Cross-tenant exemptions keep raw `DB::table()` + a `// TENANCY-EXEMPT: <reason>` comment (rule 20).
- **Effort:** 1 day

### Step 10 — CI guardrail (no regression)
- CI check failing any **added line** with `DB::table('<tenant-table>')` lacking `company_id` in the
  statement chain or a `TENANCY-EXEMPT` comment (diff-scoped grep — only new violations block).
- Append rule to `docs/AI_RULES.md` + `.ai/PROJECT_MEMORY.md`.
- **Acceptance:** a deliberately unscoped test PR fails CI.
- **Effort:** 1 day

## Phase A3 — Systematic Migration of 6,025 Queries (Days 10–25)

### Step 11 — Full classified inventory
- One-off `php artisan audit:tenant-queries` → `.ai/audit/query-inventory.csv`
  (file, line, table, class: exempt-global / superadmin / context-safe / **high-risk ~2,400** / triage).
- **Effort:** 1 day

### Step 12 — Batch 1: `HomeController` (16,182 lines)
- Known targets: `whatsapp_instances` (225–396), `interview_questions` (615–630), `users` (3598),
  `jobs` reindex (4876), `add_job_wise_condidates` (5548). Reindex helpers = intentional cross-tenant →
  exempt-comment + confirm superadmin-only routes.
- **Verify:** per 500 lines — `php -l`, pint, feature tests.
- **Effort:** 3–4 days

### Step 13 — Batch 2: ServiceCompany controllers (~54K lines)
- Order: `JobApplicationController` (13.5K) → `JobsController` (14.3K) → `AppTrackerController` (11.5K)
  → `BenchCandidatesController` (7.5K) → `CvTrackerController` (6.5K).
- Known targets: `:196`, `:358`, `interview_schedules :545`, `job_assign :2392`, `jd_details :87,1271`.
- Preserve already-correct scopes (audit §2.5 counter-examples) — normalize only.
- **Effort:** 5–6 days

### Step 14 — Batch 3: Payment + Product + Staffing + remaining
- `PaymentController`, `PhonePePaymentController` (also fix its 4 `env()` calls here),
  `ProductCompany/*`, `StaffingCompany/*`, `Front/*`, `Auth/*`.
- **Effort:** 3–4 days

### Step 15 — Marketplace flows (intentional cross-tenant)
- Apply pair-where (`company_id OR thirdparty_company_id`) — canonical at `SuperAdminCompanyController.php:1771` —
  to `staffing_company_relation`, `recommendation_relation`, `add_job_wise_condidates` flows.
- Record every intentional exemption in `.ai/audit/marketplace-exemptions.md`.
- **Effort:** 2 days

## Phase A4 — Verification & Hardening (Days 25–32)

### Step 16 — TenantIsolationTest coverage for ALL ID-accepting endpoints
- Script-extract `{id}`-style routes from `route:list`; generate probe tests (expect 150–300 cases).
- **Acceptance:** zero failures.
- **Effort:** 2 days

### Step 17 — Manual pen-test (two real staging accounts)
- URL ID swapping (candidate detail, resume download, offer edit, interview schedule), POST body
  replay with foreign IDs, JSON response inspection (autocompletes like `jd_details` search).
- **Effort:** 1 day

### Step 18 — Close model-layer gaps + update architecture docs
- Add `BelongsToTenant` (or create minimal models) for high-traffic tenant tables lacking protection:
  `AddJobWiseCondidate`, `users_profile`, `job_assign`, `timeline`, `company_coins`, rest of inventory.
- Update `docs/ARCHITECTURE.md` — remove the stale `[WARNING]`; document TenantScope/`tenant_table()`
  as canonical.
- **Effort:** 1–2 days

### Step 19 — Performance verification of added WHERE clauses
- Audit `company_id` index coverage on the 93 tenant tables vs `mysql-schema.sql`; record missing
  indexes in `.ai/PROJECT_MEMORY.md` (no invented migrations — DBA-approved changes only).
- Extend `tests/Performance/` benchmarks for the 10 hottest endpoints. Target: ≤5% regression.
- **Effort:** 1 day

### Step 20 — Documentation, rollout, lock-in
- Final report: unscoped queries before (6,025) → after (target 0 non-exempt); tests added; endpoints fixed.
- Update `docs/ARCHITECTURE.md`, `docs/AI_RULES.md` (rule 10 → use `tenant_table()`), `.ai/PROJECT_MEMORY.md`.
- Merge to `staging` → 24-hour error-monitoring watch → promote. Team briefing on the new guardrails.
- **Effort:** 1 day

---

# TRACK B — BACKEND UPGRADE (starts after Step 10, parallel with A3)

### Step B1 — Delete dead code
- `old_ProductCompanyJobsController` (7K), `OLD_ServiceCompanyDashboardController` (3.8K),
  `InterviewScheduleController_old` (3.2K), `routes/service_old.php`, `resources/views/staffing_company/BK/`,
  duplicate `app/Traits/PaymentSettings.php` vs `StripeSettings.php` (merge into one).
- **Effort:** 4 hrs

### Step B2 — Fix all 18 `env()` violations
- Move to `config/services.php` (+ `config/jero.php` where fitting), read via `config()`:
  `PhonePePaymentController:12-15`, `HomeController:240`, `ChatGptService:9`, `ZoomService:32-33`,
  Stripe traits `:18-20`, `GeneralMethod:6180` (encryption key → config with env fallback documented in `.env.example`).
- **Verify:** `php artisan config:cache` then hit PhonePe/Zoom flows on staging.
- **Effort:** 1 day

### Step B3 — God-controller decomposition (rolling, 1 controller per sprint)
- Order by risk×traffic: `HomeController` → `ServiceCompanyJobsController` → `ServiceCompanyJobApplicationController`
  → `SuperAdminCompanyController` → `AppTrackerController`.
- Pattern (proven by commit `f568f45f`): extract vertical slices into `app/Services/` + `app/Actions/`,
  thin controllers, feature test per extracted slice before moving to the next.
- Each extracted service uses `tenant_table()` from Step 9 — decomposition and tenancy fix converge.
- **Effort:** ongoing (weeks); do NOT block Track A on this.

### Step B4 — Elasticsearch write-sync
- Model observers on indexed entities (jobs, candidates, bench candidates) dispatching existing ES
  sync jobs on save/delete (heavy reindex stays queued via Horizon).
- **Effort:** 3 days

### Step B5 — Queue heavy work
- Move payment post-processing, resume parsing, bulk email/notification sends to queues
  (43 Job classes exist; only 108 dispatch call sites). Measure p95 before/after.
- **Effort:** 1 week (incremental)

---

# TRACK C — UI STANDARDIZATION (starts Week 3, parallel)

### Step C1 — Kill the font war
- `tailwind.config.js:15` → `sans: ['Poppins', ...defaultTheme.fontFamily.sans]`; drop Figtree.
- **Effort:** 30 min

### Step C2 — Global CSS guardrails
- Add to shared stylesheet: cap `fs-3/fs-4/fs-5` to 16px on app layouts (keep marketing pages exempt),
  define `.btn-jero` variants (default/processing/success per UI_UX_STANDARDS), color tokens to replace
  the 18+ ad-hoc hex values.
- **Effort:** 2 days

### Step C3 — Shared Blade components
- `@sectionCard`, `@simfToolbar`, `@jeroButton` components implementing the mandatory card + toolbar
  patterns (standards §8–9). Pilot on the 5 highest-traffic pages: dashboard, jobs list, candidate
  pipeline, app tracker, account settings.
- **Effort:** 1 week

### Step C4 — Split the 204 mega-blades (rolling)
- Start: `showdetail.blade.php` (10,491) → components; `create-offer.blade.php` (6,081) → extract the
  well-structured stepper; `account/index.blade.php` → keep the existing `account/v2/tabs/*` pattern.
- **Effort:** ongoing

### Step C5 — Consolidate the 561 duplicated views
- Shared variants in `sections/`/`components/` with role conditionals; delete `BK/`; migrate one module
  per PR (jobs first — 4 near-identical 4,979-line copies).
- **Effort:** ongoing

### Step C6 — Dark mode + a11y baseline
- Enable Metronic dark mode (`data-bs-theme`), add `prefers-color-scheme`/`prefers-reduced-motion`
  handling, fix 1366×768 breakpoints, remove fixed-width containers (`width:1025px`).
- **Effort:** 1 week

---

# Execution Order & Milestones

| Week | Track A (tenancy) | Track B (backend) | Track C (UI) |
|---|---|---|---|
| 1 | Steps 1–6 (hotfixes done) | — | — |
| 2 | Steps 7–10 (guardrails live) | B1, B2 | — |
| 3 | Steps 11–12 | B3 sprint 1 | C1, C2 |
| 4 | Steps 13 | B4 (ES sync) | C3 pilot |
| 5 | Steps 14–15 | B5 start | C4 start |
| 6 | Steps 16–17 (pen-test) | B5 | C5 start |
| 7 | Steps 18–20 (docs, rollout) | B3 sprint 2 | C6 |
| 8+ | — (CI guard maintains) | B3/B5 rolling | C4/C5 rolling |

**Definition of Done (Track A):** 0 non-exempt unscoped tenant queries · TenantIsolationTest green on
all ID-accepting endpoints · CI guard active · `docs/ARCHITECTURE.md` warning removed · manual pen-test
findings = 0.

**First actionable hour:** Step 2 + Step 4 — remove `/r2test` & `/mail-test`, guard
`HomeController.php:800`. That closes the two most exploitable holes immediately.

---

*Findings source: [GLM_FIX_Audit.md](GLM_FIX_Audit.md) · Project memory: `.ai/PROJECT_MEMORY.md`*
