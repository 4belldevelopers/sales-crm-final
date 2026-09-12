# /docs — AI-Friendly Documentation Index

Generated to optimize this repo for AI coding agents (Claude Code, Cursor, Windsurf).
**The auto-loaded entry point is [`../CLAUDE.md`](../CLAUDE.md)** (mirrored by `AGENTS.md`,
`.cursorrules`, `.windsurfrules`). Docs describe the code as analyzed; items are marked
**[INFERRED]** / **[UNCERTAIN]** where not authoritatively confirmed.

## Read order
0. [AI_SETUP_FOR_DEVELOPERS.md](AI_SETUP_FOR_DEVELOPERS.md) — **new devs start here**: where files live + how your AI tool loads them
0b. [AI_KICKOFF_PROMPT.md](AI_KICKOFF_PROMPT.md) — paste-into-any-AI prompt to load the rules + read the schema first
1. [../CLAUDE.md](../CLAUDE.md) — project memory & entry point (start here)
2. [AI_BOOTSTRAP.md](AI_BOOTSTRAP.md) — start-here protocol
3. [PRODUCT_VISION.md](PRODUCT_VISION.md) — what we're building & why (the concept)
4. [ARCHITECTURE.md](ARCHITECTURE.md) — system map & tech stack
5. [GLOSSARY.md](GLOSSARY.md) — domain vocabulary
6. [MODULE_MAP.md](MODULE_MAP.md) — files per module
7. [AI_RULES.md](AI_RULES.md) — hard rules (tenancy, ES sync, no schema guessing)

## Reference
- [DB_SCHEMA.md](DB_SCHEMA.md) — inferred schema (real schema is in the live DB ⚠️)
- [API_CONTRACTS.md](API_CONTRACTS.md) — routes, middleware, DataTables/webhook contracts
- [DATA_FLOW.md](DATA_FLOW.md) — step-by-step workflow traces
- [DEPENDENCY_GRAPH.md](DEPENDENCY_GRAPH.md) — coupling, hotspots, dead code
- [BAD_PATTERNS.md](BAD_PATTERNS.md) — tech-debt register
- [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md) — targets & bottlenecks
- [DOCUMENTATION_TODO.md](DOCUMENTATION_TODO.md) — where inline docs are most needed
- [VERIFY.md](VERIFY.md) — how to validate changes safely
- [CODE_DOCUMENTATION_STANDARD.md](CODE_DOCUMENTATION_STANDARD.md) — **comment the purpose of every new code** (templates + rules)

## How to execute well (AI performance kit)
- [RECIPES.md](RECIPES.md) — end-to-end cookbooks for common tasks (the speed unlock)
- [AI_GUARDRAILS.md](AI_GUARDRAILS.md) — ground truth, anti-hallucination, pre/post self-checks
- [DEFINITION_OF_DONE.md](DEFINITION_OF_DONE.md) — the single gate every change must pass
- [TESTING.md](TESTING.md) — how to test this app (tenant-isolation, ES mocking, schema:dump)
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) — common errors → causes → fixes
- [PROMPT_LIBRARY.md](PROMPT_LIBRARY.md) — copy-paste prompts to drive the AI
- [ENFORCEMENT.md](ENFORCEMENT.md) — automated guards/hooks/CI/PHPStan that make the rules unskippable
- [ENTERPRISE_PLAYBOOK.md](ENTERPRISE_PLAYBOOK.md) — GOD-mode strategy: lightning speed, near-zero server load, clean icon-based design, feature velocity + AI operating doctrine
- [adr/](adr/) — architecture decision records

## Per-feature audits & implementation plans
- [OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md) — **Master OpenDoc WASM Engine Architecture & Phased Roadmap**: 0% Server CPU, sub-500ms client-side typesetting, 8 GOD-level use cases (15-option Download Center, Bench Talent Privacy Shield, Live JD Match Auditor, Pre-Upload Sanitizer, Resume Diff Tool, Dynamic Offer Letter Generator).
- [V2_Zcode/](V2_Zcode/) — **ZCode (GLM) audits home — one subfolder per audit** (convention; see its [README.md](V2_Zcode/README.md) index):
  - [Code Review and UI Audit by GLM/](V2_Zcode/Code%20Review%20and%20UI%20Audit%20by%20GLM/) — codebase-wide review (2026-08-17): multi-tenancy security (6,025 unscoped raw `DB::` queries, IDOR endpoints, 6 disabled model scopes), backend debt (god controllers, debug routes, `env()` violations), UI findings (inline CSS, font conflict, card-system adoption) + 3-track GOD-level remediation plan (A: 20-step tenancy audit · B: backend upgrade · C: UI standardization). Read: [GLM_FIX_Audit.md](V2_Zcode/Code%20Review%20and%20UI%20Audit%20by%20GLM/GLM_FIX_Audit.md) → [GOD_LEVEL_IMPLEMENTATION_PLAN.md](V2_Zcode/Code%20Review%20and%20UI%20Audit%20by%20GLM/GOD_LEVEL_IMPLEMENTATION_PLAN.md)
  - [SAAS Architecture Audit by GLM/](V2_Zcode/SAAS%20Architecture%20Audit%20by%20GLM/) — repository-wide auth/tenancy audit (2026-08-17, analysis-only): how tenancy, roles (Spatie + legacy `role_user`), permissions (~67 global, 42 checked), middleware, coins/packages, jobs, files, and ES isolation actually work; 5 critical gaps incl. file-download ownership, role-less Company-scope bypass, systemic raw-query IDOR. Read: [SAAS_ARCHITECTURE_AUDIT.md](V2_Zcode/SAAS%20Architecture%20Audit%20by%20GLM/SAAS_ARCHITECTURE_AUDIT.md) → [SAAS_AUTHORIZATION_IMPLEMENTATION_GUIDE.md](V2_Zcode/SAAS%20Architecture%20Audit%20by%20GLM/SAAS_AUTHORIZATION_IMPLEMENTATION_GUIDE.md) (AI-facing) → [AUTHORIZATION_GAPS.md](V2_Zcode/SAAS%20Architecture%20Audit%20by%20GLM/AUTHORIZATION_GAPS.md) · [PERMISSION_MATRIX.md](V2_Zcode/SAAS%20Architecture%20Audit%20by%20GLM/PERMISSION_MATRIX.md) · [TENANT_ISOLATION_AUDIT.md](V2_Zcode/SAAS%20Architecture%20Audit%20by%20GLM/TENANT_ISOLATION_AUDIT.md) · [ACTION_INVENTORY.md](V2_Zcode/SAAS%20Architecture%20Audit%20by%20GLM/ACTION_INVENTORY.md)
- [V2_Apptracker/](V2_Apptracker/) — `service_company/apptracker` GOD-level audit + 13-step roll-out plan (Service+Admin scoped, Aug 2026)
- [V2_ServiceCompanyAppTrackerController/](V2_ServiceCompanyAppTrackerController/) — controller-only audit: 33-method ledger, hidden shared-state machine, duplication matrix, 6-phase slice refactor
- [Add-Edit-Submit-Audit.md](V2_Add-Edit-Submit/Add-Edit-Submit-Audit.md) — unified CRUD + transitions sweep (196 routes, 55 entity rows, 46 transition-mapped entities, 13 hotspots, 7-phase refactor plan ~31 dev-days) across Service Co / Super Admin / Admin / Front scope (2026-08-15). Evidence: [evidence/_Add-Edit-Submit-Audit-evidence.md](V2_Add-Edit-Submit/evidence/_Add-Edit-Submit-Audit-evidence.md)
- [Perf-10K-Plan.md](V2_Perf-10K/Perf-10K-Plan.md) — Add/Edit/Submit perf plan for 10K-concurrent on 3×32 GB VMs (write-path / read-path / per-screen / infra buckets, ~38 dev-days, 8 phases). Budget: [perf-budget.json](V2_Perf-10K/perf-budget.json). Load harness: [tests/load/k6/perf-10k.js](../tests/load/k6/perf-10k.js). Tightens [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md) targets; complements [SCALABILITY.md](SCALABILITY.md) long-horizon.
- [SUBSCRIPTION_TRANSACTIONS_10K_SCALABILITY_PLAN.md](V2_SUBSCRIPTION/SUBSCRIPTION_TRANSACTIONS_10K_SCALABILITY_PLAN.md) — 10K+ concurrent recruiter scalability plan for Subscription & Wallet Center transactions tab (`newindex#tab=transactions`). Covers database composite indexes, Redis caching, job throttling, and query optimization.
- [PERSONA_SCOPE.md](PERSONA_SCOPE.md) — **scope rule: ignore Product Co and Staffing Co** for all audits, plans, refactors. Read first.

## Product & engineering strategy (architect-level)
- [PRODUCT_VISION.md](PRODUCT_VISION.md) — the concept: pitch, personas, problem, how it works, principles
- [ROADMAP.md](ROADMAP.md) — sequenced build plan (Phase 0 → 6) tying every strategy doc together
- [GLOSSARY.md](GLOSSARY.md) — domain & system vocabulary
- [MARKETPLACE_VISION.md](MARKETPLACE_VISION.md) — talent-trading marketplace: architecture + phased build plan
- [ATS_FEATURES.md](ATS_FEATURES.md) — GOD-level ATS feature roadmap (have vs. build)
- [SCALABILITY.md](SCALABILITY.md) — scaling to 10K companies / 100M+ records/yr without failing
- [UI_UX_STANDARDS.md](UI_UX_STANDARDS.md) — Metronic, ≤14px type scale, fits 15.6" + all laptops, auto-fit data
- [SECURITY.md](SECURITY.md) — security risk register + remediation program
- [BUG_REPORT.md](BUG_REPORT.md) — concrete bugs found (with line refs) + fix plan
- [CLIENT_JOB_STAGE_OWNERSHIP.md](CLIENT_JOB_STAGE_OWNERSHIP.md) — agency ↔ client stage-ownership & candidate-visibility rules for client-job AppTracker pages, the standing search/sort/filter design+logic convention for all AppTracker candidate-list pages, and (§8) the shared stage-change drawer architecture across AppTracker/CV Tracker/ClientApp/Offer/Joining Tracker — row-trigger gating per page, sub-stage/un-reject bugs fixed
- [NAMING_CONVENTIONS.md](NAMING_CONVENTIONS.md) — naming standards + SAFE rename procedure (names map to real tables/indices)

## AI memory & machine-readable context
- [`../CLAUDE.md`](../CLAUDE.md) · `../AGENTS.md` · `../.cursorrules` · `../.windsurfrules` — auto-loaded entry files (one shared brain across tools)
- [`../.ai/PROJECT_MEMORY.md`](../.ai/PROJECT_MEMORY.md) — **living** state, decision log, open questions, gotchas (update this over time)
- `../.ai/summaries/` — one-line-per-class indexes (controllers, services, models, jobs)
- `../.ai/tasks/` — per-module playbooks

## Golden rules (full list in AI_RULES.md)
- Never read 500 KB+ controllers or full route files whole — grep for the method.
- Schema is in the live DB, not migrations — confirm columns, don't invent migrations.
- Eloquent is tenant-scoped by `company_id`; raw `DB::table()` is **not** — filter it.
- Keep MySQL ↔ Elasticsearch in sync (observers/jobs); don't bypass.
- Preserve baked-in typos (`condidate`, `commericial`, `Caompany`) — they map to real names.
- All work goes in `testing.jero.works`, never `hire.jero.works`.
