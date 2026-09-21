# CLAUDE.md — Project Memory & AI Entry Point

> **This file is auto-loaded by Claude Code (and is the canonical memory for any AI
> tool).** Read it first, every session. It is intentionally short — it orients you and
> points to the detailed knowledge base in [`docs/`](docs/) and [`.ai/`](.ai/). Keep it
> updated when project-level facts change.

---

## What this product is (one paragraph)
**JERO** is a multi-tenant **B2B hiring SaaS** = a **GOD-level ATS** (Applicant Tracking
System) **+ a talent marketplace** where companies trade talent (agency bench, sourced
candidates, requirements) with each other. One Laravel 11 codebase serves six personas
(Service Company / Product Company / Staffing Company / Super Admin / Admin / public
Front). It is built strictly CQRS-style: **MySQL = write store (Add/Edit/Delete only), Elasticsearch = read store (100% search/queries/filters)**, with
Redis/Horizon queues, Octane/FrankenPHP runtime, Reverb websockets, and multiple payment
gateways. Target: scale to **10,000 concurrent users per page** with **0% server read strain** via **Client-First Browser Execution (User RAM + WASM)** without failing.
Full concept: [docs/PRODUCT_VISION.md](docs/PRODUCT_VISION.md).

> **Persona scope (2026-08-15 directive):** **Build/audit/refactor only the four in-scope personas**
> (Service Company, Super Admin, Admin, public Front). **Product Company and Staffing Company are
> DORMANT — ignore them.** Do not enumerate, fix, refactor, or feature-add in those paths. Full rule:
> [docs/PERSONA_SCOPE.md](docs/PERSONA_SCOPE.md).

## Where the code lives & Git Deployment Rules ⚠️
**All work happens in this folder — `JWMaincode28June2026` (the `testing.jero.works`
codebase)** and **only** here. **Never make changes in any other folder** (e.g.
`hire.jero.works`, which is effectively empty). Every change — code, docs, config — must
be made inside this folder.

> **Git Deployment Directive (2026-09-15):** **NEVER push git to production (`main` / `hire.jero.works`) unless explicitly asked by the user.** Stick strictly to the `staging` branch only.

---

## 🏛️ The 11 God-Level Architectural Directives (V4 Mandate)
1. **MySQL is ONLY for Add / Edit / Delete:** 0% MySQL reads on web/API request paths. No `SELECT` or table joins on user page loads.
2. **Elasticsearch is ONLY for Read (Universal Scope):** Serves 100% of queries, filters, counts, and search results across ALL data domains (candidates, jobs, requisitions, applications, companies, coins, teams) across all 60+ indices. Zero direct UI writes.
3. **Hardware Profile (32 GB RAM per Tier):** 32 GB RAM dedicated to App Server (Octane/FrankenPHP + Redis), 32 GB RAM dedicated to MySQL (~24 GB InnoDB buffer pool for writes), 32 GB RAM dedicated to Elasticsearch (16 GB JVM heap + 16 GB OS page cache).
4. **10,000 Concurrent Users Per Page Scalability Target:** Every page/action must handle 10K concurrent users without server collapse, worker starvation, or UI freezes (long tasks > 50ms banned).
5. **Target 0% Server Strain:** Eliminate cloud server load for document formatting, vector PDF assembly, and static rendering by running on the edge.
6. **Client-First / Browser Execution (User RAM First):** Execute assembly, compilation, and rendering on the user's browser (User RAM, WASM, Web Workers, `pdf-lib`). Server/ES is strictly a circuit-breaker fallback.
7. **Zero Stale Data in ATS:** Recruitment pipeline data must be 100% fresh, correct, and authoritative. Latency optimization must never compromise data integrity.
8. **Multi-Tenant Atomic Cache Invalidation:** All cache keys are strictly scoped by `company_id`. Any update to candidate/job/stage details immediately purges the tenant cache across Redis and the organization so all users see real-time updated data.
9. **Zero Business Logic Alteration & Strict Blast Radius Isolation (Zero Regressions):** For any change or upgrade on any page/feature, existing core business logic (scoring, pipelines, billing, status workflows) must remain 100% untouched, and NO other page, modal, drawer, or module must be affected or broken.
10. **Complete Page/Blade Isolation (Strict Anti-Entanglement Mandate):** Every page/blade designed, developed, or updated must be completely isolated to that page. Never reuse or share stateful JS/drawers/scripts across pages (e.g. CV Tracker vs App Tracker vs Client App); changes to one page must NEVER touch, affect, or break another page or module.
11. **15.6-Inch Screen Sizing & Small-Screen Responsiveness:** All UI/UX designs, tables, modals, cards, and drawers must perfectly fit a standard 15.6-inch laptop screen (1366×768 & 1920×1080) and smaller screens with zero horizontal page scroll, internal drawer scroll, and max 14px Poppins font.

---

## Start-here reading order (cheap context before touching code)
1. This file (CLAUDE.md)
2. [docs/AI_BOOTSTRAP.md](docs/AI_BOOTSTRAP.md) — the 5-step working protocol
3. [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) — system map & stack
4. [docs/AI_RULES.md](docs/AI_RULES.md) — hard rules (full God-level mandate)
5. [docs/GLOSSARY.md](docs/GLOSSARY.md) — domain vocabulary (what "bench", "requisition", "coins"… mean)
6. The relevant [`.ai/tasks/*.md`](.ai/tasks/) playbook + [`.ai/summaries/*`](.ai/summaries/)

---

## The full knowledge base ([docs/README.md](docs/README.md) is the index)
- **Understand the code:** ARCHITECTURE · MODULE_MAP · DB_SCHEMA · API_CONTRACTS · DATA_FLOW · DEPENDENCY_GRAPH
- **Work safely:** AI_RULES · AI_BOOTSTRAP · VERIFY · NAMING_CONVENTIONS · CODE_DOCUMENTATION_STANDARD
- **Execute well (do this for every task):** [RECIPES](docs/RECIPES.md) (cookbooks) · [AI_GUARDRAILS](docs/AI_GUARDRAILS.md) (verify, don't hallucinate) · [DEFINITION_OF_DONE](docs/DEFINITION_OF_DONE.md) (the gate) · [TESTING](docs/TESTING.md) · [TROUBLESHOOTING](docs/TROUBLESHOOTING.md) · [PROMPT_LIBRARY](docs/PROMPT_LIBRARY.md)
- **Automated enforcement:** [ENFORCEMENT](docs/ENFORCEMENT.md) — `scripts/ai-guards.sh` (no new `dd()`/`env()`), pre-commit hook, GitHub Actions CI, PHPStan. Run `bash scripts/ai-guards.sh` before committing.
- **Quality & risk:** BAD_PATTERNS · BUG_REPORT · SECURITY · PERFORMANCE_BUDGET · DOCUMENTATION_TODO
- **Product & strategy:** PRODUCT_VISION · ATS_FEATURES · MARKETPLACE_VISION · SCALABILITY · UI_UX_STANDARDS · ENTERPRISE_PLAYBOOK · ROADMAP · adr/
- **Machine-readable:** `.ai/summaries/` (1-line class indexes) · `.ai/tasks/` (module playbooks) · `.ai/PROJECT_MEMORY.md` (living state & decisions)

---

## The Non-Negotiable Rules (full list in [docs/AI_RULES.md](docs/AI_RULES.md))
1. **Never read 500 KB+ controllers or full route files whole** — grep for the method.
2. **Database truth = [`database/schema/mysql-schema.sql`](database/schema/mysql-schema.sql)** (real structure, 217 tables) — **read it before any DB work**. Schema is NOT in migrations, so never invent migrations.
3. **Tenancy:** Eloquent is scoped by `company_id`; **raw `DB::table()` is NOT** — filter it yourself.
4. **Keep MySQL ↔ Elasticsearch in sync** — writes to indexed entities must re-index (Observer/Job).
5. **Use `config()`, never `env()` outside `config/`** — `env()` returns null under `config:cache`/Octane.
6. **Queue heavy work** (resume parse/upload, mail, bulk, ES reindex).
7. **Preserve baked-in typos** (`condidate`, `commericial`, `Caompany`, `transection`) — they map to real tables/indices.
8. **No business-logic changes** (ranking/matching, billing, status pipelines) without sign-off.
9. **Match existing patterns** — no repository layer; fat controllers + services. Don't add layers unprompted.
10. **UI:** follow Metronic, **Poppins font**, **max font 14px**, **clean icon-based design (NO 3D)**, must perfectly fit **15.6-inch laptop screens (1366×768 & 1920×1080) and smaller screens** with complete responsiveness (see UI_UX_STANDARDS).
11. **Build features as vertical slices** (Route + FormRequest + Action + ES sync + Observer + Reverb event + Blade component + **test**).
12. **Measure & verify** (before/after numbers; run [docs/VERIFY.md](docs/VERIFY.md)); mark uncertainty explicitly.
13. **State the purpose of every new code** — intent comment/docblock on each new file/class/method; intent + tenancy note on complex/raw queries.
14. **Engine Isolation Mandate:** Download Resume Center (`AgyResumeRenderer.js`), Forward Resume (`AgyForwardResumeEngine.js`), and Online Editor (`AgyEditorWasmEngine.js`) are **strictly 3 separate engines**. Never merge or cross-import them.
15. **Complete Page/Blade Isolation:** Every page/blade designed, developed, or updated must be completely isolated. Never reuse or share stateful JS controllers, drawers, or coupled scripts across different pages or modules. Changes to one page must NEVER touch or affect another page.

---

## Known landmines (don't get surprised)
- 63 active `dd()` calls + debug routes (`/mail-test`, `/whatsapp/*`) in code — see BUG_REPORT.
- `JobApplication` tenant scope is **commented out** → cross-tenant leak risk (BUG-03/SEC-02).
- 1,227 unescaped `{!! !!}` Blade outputs → XSS surface (BUG-04/SEC-03).
- Secrets (incl. plaintext ES password) live in `.env` — handle with care (SEC-01).
- Dead code: `OLD_*`/`old_*`/`OLD_2_*` controllers, `oldBackUp/`, stray root controller — ignore.

## How to update memory
- Project-level facts change → update this file + [.ai/PROJECT_MEMORY.md](.ai/PROJECT_MEMORY.md).
- A decision is made → append to [.ai/PROJECT_MEMORY.md](.ai/PROJECT_MEMORY.md) decision log (or add an ADR).
- A doc becomes stale → fix the doc, not just this index.
