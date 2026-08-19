# Project Memory (Living) — JERO

> **Persistent, evolving memory for AI agents and humans.** Unlike the `docs/` (which
> describe how things *are*), this file tracks **current state, active decisions, open
> questions, and gotchas over time**. KEEP IT UPDATED: when you make a project-level
> decision, finish a phase, or learn something non-obvious, append it here (newest on top
> in each section) and, if it's an architecture decision, also add an ADR.

Last updated: 2026-08-06 (V2_login audit + planning slices complete).

---

## 1. Snapshot (what is true right now)
- **Product:** GOD-level ATS + cross-company talent marketplace. See [docs/PRODUCT_VISION.md](../docs/PRODUCT_VISION.md).
- **Stack:** Laravel 11 / PHP 8.2 · MySQL (write) + Elasticsearch (read, 60+ indices) ·
  Redis/Horizon · Octane/FrankenPHP · Reverb · S3 · Stripe/Razorpay/PhonePe/PayPal/Paddle · AI (OpenAI).
- **Scale goal:** 10,000 companies, 100M+ records/year. See [docs/SCALABILITY.md](../docs/SCALABILITY.md).
- **Active code folder:** `testing.jero.works` (NOT `hire.jero.works`).
- **Current focus:** V2_login slice fully designed (audit + plan + comparison + ADR), **awaiting** `app/` implementation per [docs/V2_login/IMPLEMENTATION_PLAN.md](../docs/V2_login/IMPLEMENTATION_PLAN.md) Phase 3.3–3.8 + 6.4/6.5. 2 product sign-off questions still open.
- **Test coverage:** essentially none (default Breeze scaffolding only) — high regression risk.
- **V2_login blocking-scenarios architecture:** session-epoch pattern + Reverb `UserAccountSuspended` event + `SyncUserStatusAction` (cross-tenant-safe) + `BlockCompanyAction` (static bulk helper) + `block_log` audit table + drop all 20 `refresh:true` ES sites + grep-lint guard. See [docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md](../docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md), [ADR 0007](../docs/adr/0007-blocking-scenarios-architecture.md). Code not yet landed.

## 2. Decision log (append-only; newest first)
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
