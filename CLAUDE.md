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
12. **Product Company & Staffing Company Code Freeze Mandate:** Files under `resources/views/product_company/` and `resources/views/staffing_company/` (and their respective controllers/modules) are strictly frozen. Do NOT edit, refactor, or apply UI/feature changes to Product Company or Staffing Company files unless explicitly instructed otherwise. Active development, currency enhancements, and ATS feature work are strictly scoped to **Service Company** (`resources/views/service_company/`) and **Admin** (`resources/views/admin/`).
13. **Latest Technology Adoption & Standard (October 2026):** For modern features, background workers, and performance optimizations, always follow [docs/Latest_Technology_JERO.WORKS](docs/Latest_Technology_JERO.WORKS/LATEST_TECHNOLOGY_FOR_JERO_WORKS.md). Mandate **Bun** for high-speed offline/static compilation & test runs, **Mojo & local ONNX CPU** for zero-cloud bare-metal vector math ($0 ongoing API fees; zero LangChain), and **Dragonfly** for 4M QPS multi-tenant caching.
14. **Mandatory Modern Card + Sub-Card Design & Header Background Standard:** All entity listings, drawer tabs (such as the Client Drawer Jobs tab), drawers (e.g. Job Status drawer), overview views, and dashboards across JERO **MUST AND ONLY** use the **Modern Card + Sub-Card Enterprise Design System** ([docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md](docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md)): Tier 1 toolbar card with dedicated background color (`#f8fafc`, border `#e2e8f0`, radius 10px) with dynamic right-corner search bar (300ms debounce + clear button) and primary action button; Tier 2 dedicated sub-tabs card (`#ffffff` or soft `#f8fafc`, border `#e2e8f0`, radius 10px) with single active primary blue highlight (`#009ef7`); Tier 3 primary card stream with discrete upper sub-cards (company logo, comp/location, team, agency, pipeline) and bottom activity strip. **MANDATORY HEADER BACKGROUND COLOR:** All drawer headers (`.sd-topbar`, modal topbars), card headers (`.card-header`, `.jsd-card-header`), sub-card section headers, and comparison matrix / table header rows (`thead tr`) **MUST ALWAYS** have a dedicated background color (e.g. `#f8fafc` or `#f1f5f9` with crisp border `#e2e8f0`, or soft semantic tint) — never transparent, plain white, or unanchored text. This visually anchors section boundaries, elevates contrast against card bodies, and ensures the best enterprise UI/UX implementations. Sprawling raw HTML tables, unstyled floating sub-tabs, and placeholder matching badges ("15 Matching") are strictly forbidden. Must perfectly fit 15.6" screens (`1366×768` & `1920×1080`) with zero horizontal overflow.
15. **Mandatory Module Subfolder Storage & Pattern for Audits, Feedback, Changes, Features & Suggestions:** All new documents generated for any audit, feedback, code/system changes, new features, or suggestions/recommendations MUST strictly be saved into their dedicated module/feature named folder under `docs/audit documents/<module_or_feature_name>/` (e.g. `docs/audit documents/<module_or_feature_name>/YYYY-MM-DD-<topic>-<type>.md`). Loose documents at the root of `docs/` or `docs/audit documents/` are strictly prohibited. Documents must follow the standardized layout (Metadata, Summary, Architecture/CQRS, Changes Matrix, 15.6" Screen Fit, Verification) so anyone can easily understand the changes.
16. **Zero-Cron & Just-In-Time (JIT) Evaluation Mandate (Zero Idle Server/Bandwidth Strain):** Never introduce background polling cron jobs, periodic `schedule:run` commands, or recurring database sweepers for record expiration, status timeouts, or state reconciliation unless explicitly authorized. Polling cron jobs consume server CPU/RAM cycles, boot framework overhead repeatedly, and waste database IOPS. Instead, ALWAYS implement **Just-In-Time (JIT) / Lazy Evaluation**: (a) compute expiration/state on read queries (`WHERE expires_at > NOW()`), (b) lazily transition/self-heal expired states on write/access actions within the incoming request transaction, or (c) use delayed queue jobs (`->delay(...)`) for exact one-time events. Zero idle background CPU strain and 0 KB bandwidth waste.
17. **Client-First Resume Parser & Entity Resolution Mandate (`JeroIdentityEngine`):** All resume parsing and ATS field extraction (Candidate Name, Current Company, Designation, Experience, Skills, Education, Gender) MUST execute 100% in Browser RAM (`User RAM + WebAssembly + Client NLP`) with 0% server/DB read strain (ADR 0008). Never introduce backend server parsing endpoints. Name resolution uses 5-vector identity triangulation (spatial decay + bounded email decomposition + strict zero-authority filename tie-breaker capped at +35 pts + negative blacklist + 250+ role truncator). Current company uses the Employment Chronology Resolver (month/year precision, ongoing active roles prioritized, project descriptions ignored), Contextual Academic Guards, and the Canonical Employer Knowledge Graph (`ServiceNow`, `PwC`, `EY`, `HCLTech`, `HP`, `CRED`) with a 3-tier confidence threshold gate ($\ge 140$ High, $80\text{--}139$ Medium, $< 80$ Low/Unconfirmed). See [`docs/audit documents/resume_parser/2026-11-10-supergod-mode-resume-parser-name-and-company-extraction-architecture.md`](docs/audit%20documents/resume_parser/2026-11-10-supergod-mode-resume-parser-name-and-company-extraction-architecture.md).
18. **Mandatory Default User/Account-Setting Timezone on All Dates & Times:** Always resolve the user/organization's configured default timezone from Account Settings (`companies.timezone` via `GeneralMethod::defaultTimeZone($company_id)` or `GeneralMethod::getTenantDefaultsPayload($company_id)`) instead of displaying raw timestamps or hardcoded timezones. Always convert dates/times into that configured default timezone and visibly include **date, time, and timezone abbreviation together** (e.g., `12 Nov 26, 03:45 PM IST` via `d M y, h:i A T`) across all cards, drawers, tables, timelines, and exports.

---

## Start-here reading order (cheap context before touching code)
1. This file (CLAUDE.md)
2. [docs/AI_BOOTSTRAP.md](docs/AI_BOOTSTRAP.md) — the 5-step working protocol
3. [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) — system map & stack
4. [docs/AI_RULES.md](docs/AI_RULES.md) — hard rules (full God-level mandate)
5. [docs/GLOSSARY.md](docs/GLOSSARY.md) — domain vocabulary (what "bench", "requisition", "coins"… mean)
6. [docs/Latest_Technology_JERO.WORKS/LATEST_TECHNOLOGY_FOR_JERO_WORKS.md](docs/Latest_Technology_JERO.WORKS/LATEST_TECHNOLOGY_FOR_JERO_WORKS.md) — modern systems engineering standard (Bun, Mojo, Dragonfly, DuckDB-WASM)
7. The relevant [`.ai/tasks/*.md`](.ai/tasks/) playbook + [`.ai/summaries/*`](.ai/summaries/)

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
16. **Modern Card + Sub-Card Design & Header Background Standard:** All entity listings, drawer tabs, drawers, and overview dashboards MUST adhere strictly to the 3-tier Card + Sub-Card design system with dedicated background-colored toolbar cards (`#f8fafc`), dynamic right-corner search bar, sub-tabs card, and **dedicated background colors on all headers** (drawer headers, card headers, sub-card section headers, table theads: `#f8fafc` / `#f1f5f9` with border `#e2e8f0`) for immediate hierarchy comprehension and best UI/UX implementation ([docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md](docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md)). Raw tables and unstyled headers/tabs are strictly forbidden.
17. **Mandatory Module Subfolder Storage for Audits, Feedback & Proposals:** Save all new documentation into its dedicated module or feature folder in `docs/audit documents/<module_or_feature_name>/` using the standard architectural pattern. Loose files at root are strictly forbidden.
18. **Client-First Resume Parser Parity:** Resume parsing runs 100% in client browser RAM (`JeroIdentityEngine`). Maintain identical parity across all 4 parser bridge files (`public/js/jero-client-parser.js`, `js/jero-client-parser.js`, `resources/views/service_company/job-applications/jero-client-parser.blade.php`, `resources/views/service_company/bench-candidates/jero-bench-parser.blade.php`). Never introduce backend server parsing endpoints.
19. **Always Use Account-Setting Default Timezone with Date & Time:** Resolve the user/organization's configured default timezone via `GeneralMethod::defaultTimeZone($company_id)` / `GeneralMethod::getTenantDefaultsPayload($company_id)` (`companies.timezone`), convert timestamps to that timezone, and always display date, time, and timezone abbreviation (`d M y, h:i A T`).
20. **Canonical Job Classification Tags & Color Combinations (Never Use Bare `"Internal"`):** NEVER display bare `"Internal"`, `"Internal Job"`, or `"Internal Team"` labels in the UI — always use the exact tag + color tokens:
    - **Own-Company Hiring (`is_self_company === true`):** Tag **`My Organization`** (`.type-my-org`, `fa-id-badge`) → Cyan `#ecfeff` bg, `#0891b2` text, `#a5f3fc` border (KPI `my_organization`: `#ecfeff` bg, `#22d3ee` border, `#0891b2` icon). Never call this `"Internal"`.
    - **Managed Non-Marketplace Client (`is_direct_client === false && is_self_company === false`):** Tag **`Internal Client`** (`.type-internal`, `fa-building`) → Blue `#eff6ff` bg, `#2563eb` text, `#bfdbfe` border (KPI `internal`: `#fff7ed` bg, `#fb923c` border, `#ea580c` icon). Never shorten to bare `"Internal"`.
    - **Marketplace Client (`is_direct_client === true`):** Tag **`Direct Client`** (`.type-direct`, `fa-shield-alt`) → Emerald `#ecfdf5` bg, `#059669` text, `#a7f3d0` border (KPI `direct`: `#ecfdf5` bg, `#34d399` border, `#059669` icon).
    - **Pending Job Requests (`is_job_request === true`):** Render BOTH **`Direct Client`** (`.type-direct` Emerald) and **`Job Request`** (`.type-request`, `fa-handshake`, Violet `#f5f3ff` bg, `#7c3aed` text, `#ddd6fe` border; KPI `job_request`: `#faf5ff` bg, `#c084fc` border, `#9333ea` icon).

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
