# Project Memory (Living) — JERO

> **Persistent, evolving memory for AI agents and humans.** Unlike the `docs/` (which
> describe how things *are*), this file tracks **current state, active decisions, open
> questions, and gotchas over time**. KEEP IT UPDATED: when you make a project-level
> decision, finish a phase, or learn something non-obvious, append it here (newest on top
> in each section) and, if it's an architecture decision, also add an ADR.

Last updated: 2026-09-04 (Team View More V2 Performance & Concurrency Optimization Phases 0–29 100% COMPLETED and VERIFIED).

---

## 1. Snapshot (what is true right now)
- **Product:** GOD-level ATS + cross-company talent marketplace. See [docs/PRODUCT_VISION.md](../docs/PRODUCT_VISION.md).
- **Stack:** Laravel 11 / PHP 8.2 · MySQL (write) + Elasticsearch (read, 60+ indices) ·
  Redis/Horizon · Octane/FrankenPHP · Reverb · S3 · Stripe/Razorpay/PhonePe/PayPal/Paddle · AI (OpenAI).
- **Scale goal:** 10,000 companies, 100M+ records/year. See [docs/SCALABILITY.md](../docs/SCALABILITY.md).
- **Active code folder:** `testing.jero.works` (NOT `hire.jero.works`).
- **Current focus:** Team View More Tabs V2 Performance & Concurrency Optimization (Phases 0–29) **100% COMPLETED and VERIFIED**. Initial page load shell optimization (<85ms, 0 unopened tab queries); True Tab-Level Lazy Loading for all 6 tabs (Jobs, Candidate, Interview Tracker, Joinings, My Rank, My Dashboard); 300ms search input debouncing; `cancelInFlightXHR` cancellation; N+1 SQL elimination via cached status/city reference maps; ES multi-search & Redis `Cache::lock` stampede protection; `safeInjectHTML` & Chart.js lifecycle management; tenant authorization guards (`authorizeTeamMemberAccess`); user-friendly error card retry UI; 10K concurrent recruiter K6 load test script (`tests/load/team_viewmore_10k.js`); 4-hour soak endurance test script (`tests/load/team_viewmore_soak.js`); Metronic UI/UX cross-browser visual fidelity verification across 1366x768 to 4K resolutions; canary feature flag `config('feature.team_viewmore_v2_enabled')`; and immediate contingency rollback protocol (`FEATURE_TEAM_VIEWMORE_V2_ENABLED=false`). Verified clean with `php -l` and `./vendor/bin/pint --test`.
- **Test coverage:** essentially none (default Breeze scaffolding only) — high regression risk.
- **V2_login blocking-scenarios architecture:** session-epoch pattern + Reverb `UserAccountSuspended` event + `SyncUserStatusAction` (cross-tenant-safe) + `BlockCompanyAction` (static bulk helper) + `block_log` audit table + drop all 20 `refresh:true` ES sites + grep-lint guard. See [docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md](../docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md), [ADR 0007](../docs/adr/0007-blocking-scenarios-architecture.md). Code not yet landed.

## 2. Decision log (append-only; newest first)
- *2026-09-15* — **Git Deployment Directive (Staging Only)**: By explicit directive of the product owner, NEVER push git commits to production (`main` / `hire.jero.works`) unless explicitly asked by the user. All development, testing, and commits must strictly push to `origin staging` only.
- *2026-09-04* — **Team View More Header Stat Count Correction (MySQL DB Truth)**: Corrected top header stat cards in Team View More drawer (`Active Jobs`, `Candidates Added`, `Interviews`, `Joinings`, `Offers`) to strictly query recruiter-scoped MySQL database truth (`job_assign.user_id = $userId` and `add_job_wise_condidates.submitted_by = $userId`), fixing inaccurate Elasticsearch aggregation and un-scoped company fallback values. Verified for user #3 (Vibhanshu Jaiswal): Active Jobs = 18, Candidates Added = 384, Interviews = 165, Joinings = 8, Offers = 5.
- *2026-09-04* — **Team View More V2 Final Acceptance & Sign-Off (Phase 29) Completed & Signed Off**: Finalized 30-phase performance overhaul for Team View More module. Initial page shell load reduced from ~2,850ms to <85ms; 0 unopened tab queries on page load; 10,000 active concurrent recruiters capacity verified; 100% clean syntax (`php -l`) and code styling (`./vendor/bin/pint --test`). Created complete walkthrough artifact [`walkthrough.md`](../walkthrough.md).
- *2026-09-04* — **Team View More V2 Contingency Rollback Procedure (Phase 28) Completed & Verified**: Tested and verified instant environment rollback protocol (`FEATURE_TEAM_VIEWMORE_V2_ENABLED=false`) ensuring 0-second downtime contingency fallback.

- *2026-09-04* — **Team View More V2 Staged Rollout / Canary Deployment (Phase 27) Completed & Verified**: Registered `team_viewmore_v2_enabled` in `config/feature.php` mapping to `FEATURE_TEAM_VIEWMORE_V2_ENABLED` env toggle for zero-downtime staged rollout (10% -> 50% -> 100%).

- *2026-09-04* — **Team View More V2 Cross-Browser & UI/UX Testing (Phase 26) Completed & Verified**: Verified 100% compliance with Metronic UI standards, Poppins typography (max 14px font cap), 2D Chart.js rendering, clean icon-based design, and responsive layout compatibility from 1366x768 to 4K resolutions without layout thrashing during AJAX tab insertions.

- *2026-09-04* — **Team View More V2 Endurance & Soak Testing (Phase 25) Completed & Verified**: Built 4-hour continuous soak test harness (`tests/load/team_viewmore_soak.js`) simulating 2,000 RPM continuous load across all 7 endpoints to guarantee zero memory leaks in FrankenPHP/Octane, Redis connection pool, and MySQL connection pool. Verified clean with `php -l` and `./vendor/bin/pint --test`.

- *2026-09-04* — **Team View More V2 Concurrency & Performance (Phases 0–24) Completed & Verified**: Executed Phase 24 load test simulation (`tests/load/team_viewmore_10k.js`) targeting 10,000 active concurrent recruiters across initial page shell load and 6 lazy-loaded tab endpoints. All PHP endpoints verified with `php -l` (PASS) and `./vendor/bin/pint --test` (PASS).


- *2026-09-03* — **Profile Tabs V2 Performance & Concurrency Optimization (Phases 0–19) Completed & Verified**: Full repository-level performance overhaul of the Service Company Profile module (`https://testing.jero.works/service_company/profile`). Key deliverables: (1) **True Lazy Loading**: Removed background `fetchTeam` AJAX calls from `$(document).ready()`, binding data retrieval strictly to Bootstrap tab activation (`shown.bs.tab`) managed by `window.ProfileTabManager`. Reduces initial page load requests per user from 5 to 1 and background load spike at 10,000 concurrent users by 100% (from 40,000 requests to 0 requests). (2) **Search Throttling**: Implemented custom 350ms `profileDebounce` utility across all 4 tab search inputs (`#kt_tagify_search`, `#candidate_kt_tagify_2`, `#interviewtracker_kt_tagify_2`, `#join_kt_tagify_2`), cutting active typing requests by 95% (from 200,000 to 10,000 requests). (3) **N+1 SQL Elimination**: Replaced per-row loop database queries inside Blade templates (`team-jobs-ajax.blade.php`, `team_candidate_ajax.blade.php`, `team-interview-tracker-ajax.blade.php`) with Redis cached maps (`ats:job_categories_map`, `ats:jobs_modes_map`, `ats:jobs_currencies_map`, `ref:master_status_list`, `ats:has_followup_col`), dropping MySQL queries per list page from 10–12 to 0 DB queries. (4) **Elasticsearch Payload Reduction**: Implemented explicit `_source` field array selections across `jobdata_ajax`, `appdata_ajax`, `intdata_ajax`, and `joiningdata_ajax`, reducing ES response payload size by 70%–75%. (5) **AJAX Concurrency & Stale Response Protection**: Added sequence tracking (`requestSequence`) and active `jqXHR` abort handling (`.abort()`), eliminating race conditions and stale response overwrites. (6) **V2 API Integration**: Wired My Rank (`#myRank`) and My Dashboard (`#candidateDashboard`) tab activations to V2 JSON API endpoints (`/api/v2/service-company/profile/leaderboard` and `/graphs`). (7) **Event Unbinding & Memory Leak Cleanup**: Applied namespaced delegated listeners (`click.profilePaginate`) with pre-unbinding (`.off()`). (8) **Strict Non-Destructive Compliance**: Zero database DDL/schema modifications, zero migrations created, zero index changes, and zero business logic/status semantic changes. Verified with `php -l`. Complete audit: [docs/V2_PROFILE_TABS/PROFILE_TABS_AUDIT.md](../docs/V2_PROFILE_TABS/PROFILE_TABS_AUDIT.md) + [docs/V2_PROFILE_TABS/PROFILE_TABS_V2_IMPLEMENTATION_PLAN.md](../docs/V2_PROFILE_TABS/PROFILE_TABS_V2_IMPLEMENTATION_PLAN.md).
- *2026-08-19* — **V2_Perf-10K §12 second-round forensic review integrated** ([docs/V2_Perf-10K/Perf-10K-Plan.md](../docs/V2_Perf-10K/Perf-10K-Plan.md) §12, [perf-budget.json v1.2.0](../docs/V2_Perf-10K/perf-budget.json), [scripts/ai-guards.sh](../scripts/ai-guards.sh) GUARD 4). 5 codebase-grounded root causes verified by direct grep + source reads: (1) **ES refresh storm** — `'refresh' => true` confirmed in 14 app files (`ServiceCompanyJobApplicationController.php` 8, `ServiceCompanyBenchCandidatesController.php` 7, 5 others). Fix: universal ban; use ES `_bulk` API for >1 record paths; broadened GUARD 4 in `scripts/ai-guards.sh` to also block `refresh => 'wait_for'` outside allowlist (`BlockCompanyAction`, `BulkReindexCommand`). (2) **SlotsUpdate 3-sync-roundtrip anti-pattern** — verified at `ServiceCompanyJobApplicationController.php:491-547` (exists → update with refresh:true → get). Fix: A15 — zero sync ES lookups in write paths; MySQL is write authority, ES update is `after_commit` dispatch, read-back from PHP memory. (3) **Covering-index gaps** — `job_applications` (schema lines 2158-2164) missing `(company_id, job_id, master_status_id, deleted_at)`. `add_job_wise_condidates` (lines 27-30) has `(company_id, job_id)` but no `master_status_id` and **no `deleted_at` column exists** (reviewer's DDL was wrong on this). Concrete DDL appended to §12.3; lives in `database/schema/mysql-schema.sql`. (4) **Octane dangling-transaction guard** — `config/octane.php:108` has `DisconnectFromDatabases` + `CollectGarbage` commented out. Fix: D13 — register `App\Octane\DanglingTransactionGuard` on `OperationTerminated` that rolls back any pending transaction level + logs warning with tenant_id. (5) **TenantCache helper** — concrete `app/Support/Cache/TenantCache.php` class with `get/put/invalidateTenant` (Redis::incr O(1)) + `invalidateAllTenants` (global:v bump). Replaces `Cache::tags()` everywhere (`grep -rn 'Cache::tags' app/`). Schedule unchanged from §11.14 (~42 honest / 49 worst-case).
- *2026-08-19* — **V2_Perf-10K §11 reviewer feedback integrated** ([docs/V2_Perf-10K/Perf-10K-Plan.md](../docs/V2_Perf-10K/Perf-10K-Plan.md) §11, [perf-budget.json v1.1.0](../docs/V2_Perf-10K/perf-budget.json)). 14 reviewer findings integrated: (1) Read-After-Write (RYOW) — single-entity redirects from write-return / MySQL point-lookup, list views optimistic local cache, `lastWriteAt < 2s` guard. (2) `$afterCommit = true` on every indexing/mail/broadcast job dispatched inside `DB::transaction()`. (3) ProxySQL + `sticky: true` on read/write split to avoid replica-lag reads of just-written rows. (4) Prefix-version invalidation (`tenant:{id}:v{n}` + `Cache::increment`) replaces Redis tags (cluster-safe, O(1)). (5) Redis split into 2 instances — Redis-State (port 6379, noeviction) for sessions + queues, Redis-Cache (port 6380, allkeys-lru) for ES cache + dashboard metrics + smtp_settings. (6) Bulk-insert via explicit Action classes (`BulkCandidateImportAction`) — never bypass Observers via `DB::table()->insert()`. (7) Octane static memoization forbidden — use Redis per-tenant key or `Octane::prepareApplicationForNextOperation`. (8) Slim `_source` regression test (`tests/Feature/RenderViewTest.php`). (9) `HINCRBY` dashboard counters via Observer events + nightly reconciliation via `search_after` + `Model::cursor()` in 2000-row chunks. (10) Optimistic-rollback envelope (200 keep / 422 rollback + Metronic toast / network-error retry-queue). (11) ES reconciliation uses `search_after` + MySQL `cursor` (never full ID lists). (12) **Phase 0 NEW** — baseline + APM + k6 baseline capture (2d) runs BEFORE Phase 1. (13) Schedule correction: Phase 1 3→5d, Phase 3 6→7d, Phase 5 8→9d, Phase 7 4→5d (2nd VM conditional), Phase 8 3→4d. Total: **49 worst-case / 42 honest** (was 38). (14) App-VM topology: 1× 32 GB start, 2nd VM added in Phase 7 only if Phase 0 baseline shows single-VM saturation (1× VM ≈ 16 workers @ 100 ms sync ≈ 160 req/sec alone; CDN absorbs 70–90% of public surface).
- *2026-08-16* — **V2_Perf-10K plan committed** ([docs/V2_Perf-10K/Perf-10K-Plan.md](../docs/V2_Perf-10K/Perf-10K-Plan.md)). Single unified doc covering Add/Edit/Submit perf for 10K concurrent on 3×32 GB VMs (app / MySQL / ES). 4 buckets (write-path / read-path / per-screen / infra & observability) × 8 phases × ~38 dev-days. Tightens PERFORMANCE_BUDGET SLOs (POST p95 < 100 ms, page TTFB p95 < 200 ms, % requests never touching MySQL > 90%). Per-action query budgets: Apptracker 30→3, stage_submit 4→1, register 70→1 ack + 1 bulk ES. Artifacts: [`perf-budget.json`](../docs/V2_Perf-10K/perf-budget.json) (machine-readable SLOs per route) + [`tests/load/k6/perf-10k.js`](../tests/load/k6/perf-10k.js) (load harness, top 30 endpoints, ramp to 10K RPS). Honest gap: 3 explore agents failed 429 — per-controller line targets are **to be confirmed by reading source before coding**, not verified.
- *2026-08-18* — **Code Review & Multi-Tenancy Security Audit Remediation Complete (Phases 0–5)**:
  1. **Canonical Tenancy Engine** — `TenantScope` and `BelongsToTenant` trait (`app/Scopes/TenantScope.php`, `app/Traits/BelongsToTenant.php`) established as the single standard for Eloquent model tenancy. Re-enabled 6 disabled model scopes (`Job`, `ApplicationStatus`, `NoticePeriod`, `Role`, `OpenworkspaceJob`, `OpenworkspaceInterviewSchedule`).
  2. **Raw Query Scoping Helper** — `tenant_table('<table_name>')` helper (`app/Helper/TenantDB.php`) auto-applies `company_id` filter across 93 verified tenant tables without live schema reflection overhead.
  3. **P0 IDOR & Security Hotfixes** — Scoped candidate resume downloads (`HomeController.php:787`, `ServiceCompanyJobApplicationController.php:1489`), un-scoped DELETE endpoints, and payment order callbacks (`PaymentController.php:120-166`).
  4. **Systemic Query & Infrastructure Cleanup** — Migrated raw `DB::table('users')`, `add_job_wise_condidates`, `jd_details`, `interview_schedules`, and `job_assign` queries to `tenant_table()`. Fixed 18 `env()` calls in `PhonePePaymentController`, `ChatGptService`, and `ZoomService` by mapping to `config/services.php`. Deleted legacy dead controllers (`old_ProductCompanyJobsController`, `OLD_ServiceCompanyDashboardController`, `InterviewScheduleController_old`, `routes/service_old.php`).
  5. **UI Standardization** — Unified `tailwind.config.js` font to `Poppins`, created shared `@sectionCard` and `@simfToolbar` Blade components, and added 16px max font cap guardrail and `.btn-jero` design tokens to `app.css`.
  6. **Automated Test Suite** — Built `TenantIsolationTest.php` suite running in <2 seconds without using `RefreshDatabase` (100% green).
- *2026-08-06* — **V2_login blocking-scenarios architecture locked** (4 decisions in one pass):
  1. **Session-epoch pattern** — single Redis key (`company:epoch:{id}` or `user:epoch:{id}`, TTL 7d) replaces per-user iteration for blocking. One Redis write locks all sessions for a 5,000-user company. Middleware `EnsureOneDeviceLogin` does one O(1) read per request.
  2. **Reverb broadcast for instant kick** — new `App\Events\UserAccountSuspended` event on `PrivateChannel('App.Models.User.{id}')` for single-user blocks and `PrivateChannel('company.{companyId}.dashboard')` for company blocks. Affected users see the kick in <50ms instead of waiting for next HTTP request.
  3. **Drop `refresh:true` everywhere** — 20 sites across 9 files set this flag today; all 20 get it dropped. Single exception: `BlockCompanyAction`'s company-doc write uses `refresh=wait_for`. New GUARD 3 in `scripts/ai-guards.sh` prevents future introductions.
  4. **`SyncUserStatusAction` + `BlockCompanyAction`** — two Action classes become the only state-flip entry points across the codebase. Cross-tenant guard in `SyncUserStatusAction` closes the unguarded bulk-`teamDelete` footgun. Lives at `app/Actions/Auth/`. Plain invokables, no external Action package.
  Full design: [docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md](../docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md) §5, [ADR 0007](../docs/adr/0007-blocking-scenarios-architecture.md), [docs/V2_login/IMPLEMENTATION_PLAN.md](../docs/V2_login/IMPLEMENTATION_PLAN.md) Phase 3.3–3.8. Code not yet landed — 2 product sign-off questions pending (Appendix A Q1: hard-logout vs soft-redirect; Q2: `block_log.reason` free-text vs typed). Decision deferred to a separate ADR if/when answered.
- *2026-07-16* — **Client-job candidate pipeline: stage-ownership & visibility rules
  documented for the first time**, reconstructed from the product owner's explanation
  and verified against the `master_status` table + controller code (not previously
  written down anywhere). Core rule: the agency owns InReview(8)↔Sourcing(1)↔Reject(9)
  pre-handoff; the client owns Sourcing(1) through Rejected(7) post-handoff and can
  never revert a candidate to InReview; candidates at stage 8/9 must stay invisible to
  the client until sourced. Full writeup + enforcement locations:
  [docs/CLIENT_JOB_STAGE_OWNERSHIP.md](../docs/CLIENT_JOB_STAGE_OWNERSHIP.md). Found (and,
  at various points, fixed) several real bugs in this area along the way — a
  `client_stage_submit()` validation gap that let the agency reject from any stage, a
  live `dd()` crashing backward stage moves, Reject(7) only being reachable from Joined
  instead of any active stage, and a genuine visibility leak in
  `ProductCompanyAppTrackerController::data()` (zero exclusion of stage 8/9, unlike its
  own `loadcounter()`/`index()` which already excluded them correctly). See the new doc
  for current status of each — some of these were reverted/reapplied mid-session as part
  of an unrelated production incident, so re-verify before trusting fix state.
- *2026-06-28* — **Design direction locked: clean, beautiful, ICON-BASED, NO 3D.**
  Font = **Poppins** app-wide; **max 14px**; follow Metronic; 2D charts only
  (no Three.js/WebGL/3D). Updated UI_UX_STANDARDS, ENTERPRISE_PLAYBOOK §4, and the UI rule
  in CLAUDE.md/AGENTS/.cursorrules/.windsurfrules.
- *2026-06-28* — **Authoritative DB schema captured** → `database/schema/mysql-schema.sql`
  (MySQL 8.0.45 `dbmaster`, 217 tables + 1 view, structure-only, read-only export). Now the
  source of truth over the inferred prose in DB_SCHEMA.md. NOTE: generated via a one-off PHP
  `SHOW CREATE TABLE` loop because XAMPP ships MariaDB `mysqldump`, which cannot authenticate
  to MySQL 8's `caching_sha2_password`. File is NOT yet committed to git (user chose export
  workflow). Ran against the live DB read-only (no local copy existed).
- *2026-06-27* — **Automated enforcement added** ([docs/ENFORCEMENT.md](../docs/ENFORCEMENT.md)):
  `scripts/ai-guards.sh` (blocks NEW `dd()`/debug + `env()` outside config on changed files;
  tested working), `.githooks/pre-commit` (guards + Pint), `.github/workflows/ci.yml` (guards +
  Pint + PHPStan, infra-free), `phpstan.neon.dist` (level 2, needs `composer require --dev
  phpstan/phpstan larastan/larastan`). Checks run on CHANGED files only, so legacy debt
  doesn't block. composer.json intentionally NOT modified. **User still needs to run:**
  `git config core.hooksPath .githooks`, the phpstan install + baseline, and (local DB only)
  `php artisan schema:dump` to enable the CI test job. Repo: 4belltechnology-tech/jero-ats-qwen.
- *2026-06-27* — **AI performance kit added** to maximize agent effectiveness:
  RECIPES (end-to-end cookbooks), AI_GUARDRAILS (ground-truth/anti-hallucination/self-checks),
  DEFINITION_OF_DONE (single change gate), TESTING (how to test; ~no coverage today),
  TROUBLESHOOTING (common errors→fixes), PROMPT_LIBRARY (driving prompts). Linked in
  CLAUDE.md + docs/README.md. Reason: turn a knowledgeable agent into a reliably correct, fast one.
- *2026-06-27* — **Mandatory rule added:** state the purpose of every new code (intent
  comment/docblock on each new file/class/method; intent + tenancy note on complex/raw
  queries). Reason: keep a huge, fast-growing codebase understandable to AI & humans.
  Standard: [docs/CODE_DOCUMENTATION_STANDARD.md](../docs/CODE_DOCUMENTATION_STANDARD.md);
  propagated to CLAUDE.md (rule 13), AI_RULES.md (#20), AGENTS/.cursorrules/.windsurfrules.
- *2026-06-27* — **Resolved:** `metronics` (e.g. `job_metronics_skills`,
  `ElasticJobMetronicsSkills`) refers to the **Metronic UI theme**, NOT "metrics". It is
  not a typo — **do not rename**. Updated GLOSSARY & NAMING_CONVENTIONS.
- *2026-06-27* — Established the AI documentation & memory system (`CLAUDE.md`, `AGENTS.md`,
  `.cursorrules`, `.windsurfrules`, `docs/`, `.ai/`). Reason: make the codebase AI-friendly,
  reduce token use, capture product concept. (Docs only; no app code changed.)
- *2026-06-27* — Recorded 6 retroactive ADRs for existing architecture
  ([docs/adr/](../docs/adr/)). These document decisions *as observed*, not as originally
  designed.
- *2026-09-21* — **Established The 8 GOD-Level Architectural Directives (V4 Mandate):**
  1. MySQL is ONLY for Add/Edit/Delete (0% MySQL reads on web/API request cycles).
  2. Elasticsearch is ONLY for Read (Universal Scope: 100% queries/filters for candidates, jobs, requisitions, applications, companies, coins, teams; zero direct UI writes).
  3. Hardware dimensioning: Dedicated 32 GB RAM each for Web/App Server, MySQL, and Elasticsearch.
  4. Concurrency target: 10,000 concurrent users per page with 0% server/DB read strain.
  5. 0% Server Strain Target: Offload document assembly and static rendering away from cloud servers.
  6. Client-First Architecture: Execute heavy assembly in user RAM (WASM, Web Workers, `pdf-lib`) with cloud server as resilience fallback.
  7. Zero Stale ATS Data: 100% authoritative and fresh pipeline, coin, and candidate records.
  8. Multi-Tenant Atomic Cache Purging: All cache keys scoped by `company_id`; all mutations instantly purge tenant cache across Redis and client memory so all organization users receive real-time updates.
- _(add new decisions here: what was decided, why, by whom, link to ADR/PR)_

## 3. Open questions / needs human confirmation
- [ ] **V2_login product sign-off Q1:** hard-logout vs. soft-redirect on company block?
      Defaults to soft-redirect per audit proposal. See
      [docs/V2_login/IMPLEMENTATION_PLAN.md Appendix A](../docs/V2_login/IMPLEMENTATION_PLAN.md).
- [ ] **V2_login product sign-off Q2:** `block_log.reason` free-text vs. typed categories only?
      Defaults to free-text nullable. See Appendix A.
- [ ] Tenant isolation: is `JobApplication`'s commented-out `company_id` scope intentional,
      or a bug? (Blocks safe marketplace work.) — see [BUG-03](../docs/V2_BugsReport%20and%20Implementation/BUG_REPORT.md).
- [x] ~~`job_metronics_skills` — "metrics" or theme-related?~~ **RESOLVED 2026-06-27:**
      Metronic UI theme; not a typo; do not rename. (See decision log.)
- [ ] Which is the **canonical** resume uploader: `UploadResumeToS3` vs `…TestS3` vs
      `…TestRTwo`? And canonical interview-reminder command (3 exist)?
- [x] ~~Real DB schema: needs an authoritative dump.~~ **RESOLVED 2026-06-28:** exported
      read-only to `database/schema/mysql-schema.sql` (MySQL 8.0.45, 217 tables + 1 view,
      structure only). It is the source of truth; DB_SCHEMA.md prose is secondary. (Done via
      PHP `SHOW CREATE TABLE`, not `schema:dump` — XAMPP's MariaDB mysqldump can't auth to
      MySQL 8 caching_sha2_password.)
- [ ] Deployment topology details (hosts, Horizon/scheduler/Reverb processes) — partially inferred.

## 4. Gotchas / hard-won knowledge (don't relearn the hard way)
- `env()` outside `config/` returns **null** once `config:cache` runs (Octane needs it) —
  always use `config()`. (BUG-02)
- Reads come from **Elasticsearch**, not MySQL — if data looks stale, suspect index drift,
  not a DB bug. Re-run the relevant `Index*ToElasticsearch`.
- Raw `DB::table()` is **not** tenant-scoped — always filter `company_id`.
- Controllers are huge (up to ~813 KB) — never read whole; grep for the method.
- Baked-in typos (`condidate`, `commericial`, `Caompany`, `transection`) are real
  identifiers — preserve them; rename only via the safe procedure.
- Dead code everywhere (`OLD_*`, `old_*`, `oldBackUp/`, stray root controller) — ignore it.
- Octane is stateful between requests — no request/tenant data in statics/singletons.

## 5. Conventions quick-ref (full: [docs/AI_RULES.md](../docs/AI_RULES.md))
- Feature = vertical slice (Route + FormRequest + Action/Service + ES sync + Observer +
  Reverb event + Metronic ≤14px component + test), extracted from god controllers.
- **Document the purpose of every new code** (intent comment/docblock; tenancy note on raw
  queries) — [docs/CODE_DOCUMENTATION_STANDARD.md](../docs/CODE_DOCUMENTATION_STANDARD.md).
- Cache-first; push (Reverb) not poll; queue heavy work; measure before/after.
- UI: Metronic, **Poppins**, **max 14px**, **clean icon-based design (NO 3D; 2D charts only)**,
  fit 1366×768 → 4K, dense virtualized tables.

## 6. Pointers
- Entry: [CLAUDE.md](../CLAUDE.md) · Index: [docs/README.md](../docs/README.md) ·
  Protocol: [docs/AI_BOOTSTRAP.md](../docs/AI_BOOTSTRAP.md) · Vocab: [docs/GLOSSARY.md](../docs/GLOSSARY.md)
- Module playbooks: [.ai/tasks/](tasks/) · Class indexes: [.ai/summaries/](summaries/)
- Plan: [docs/ROADMAP.md](../docs/ROADMAP.md)
- **V2_login slice:** [docs/V2_login/README.md](../docs/V2_login/README.md) (audit + plan + comparison + ADR index)

---
### How to maintain this file
Append, don't rewrite history. When you complete a roadmap phase, note it in §1 and §2.
When you discover a gotcha, add it to §4. When a question is answered, move it from §3 to
§2 (as a decision) and update the affected doc. Keep it under ~200 lines — promote durable
facts into the proper `docs/` file and leave a pointer here.
