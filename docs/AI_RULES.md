# AI Coding Rules — GOD-Level Architectural Mandates

> **Mandatory rules for all AI coding agents (Claude Code, Cursor, Windsurf, Copilot, Antigravity) and human engineers working on JERO.**
> These rules are non-negotiable. Violations lead to production downtime, server collapse under 10K concurrency, data corruption, or cross-tenant data leaks.

---

## 🏛️ THE 11 GOD-LEVEL ARCHITECTURAL DIRECTIVES (V4 MANDATE)

### 1. MySQL is ONLY for Add / Edit / Delete (Strict CQRS Command Store)
- **Role:** MySQL (`dbmaster`, 217 tables) is the authoritative System of Record (SoR) strictly dedicated to transactional writes: `INSERT`, `UPDATE`, and `DELETE`.
- **Zero MySQL Reads on Request Paths:** Web requests, drawer activations, list rendering, search queries, filter operations, and dashboard loads must **NEVER** execute `SELECT` queries, multi-table joins, or table scans against MySQL.
- **Transactional Discipline:** Wrap write mutations in `DB::transaction()`, commit quickly, and immediately trigger the synchronization pipeline to Elasticsearch and atomic Redis cache invalidation.

### 2. Elasticsearch is ONLY for Read (Universal CQRS Query Store)
- **Universal Read Scope (All Data Domains across 60+ Indices):** Elasticsearch 7.17 is the **exclusive read store for ALL platform data and entities** — it is **strictly NOT limited to candidates**. It serves 100% of queries, searches, filters, counts, lookups, and aggregations across all data domains:
  - **Candidates & Bench:** Profiles, resumes, skills, dossiers, matching scores (`ats_candidates_elastic`, `ats_candidates_bench_elastic`).
  - **Jobs & Requisitions:** Active/closed jobs, requisitions, client jobs, skills requirements, applicant counters (`ats_jobs_elastic`, `ats_requisitions_elastic`, `ats_job_metronics_skills`).
  - **Applications & Pipeline Stages:** Application pipelines, hiring stages, status tracking, candidate requests (`ats_job_applications_elastic`, `ats_application_stages`).
  - **Companies & Agency Marketplace:** Company profiles, agency/client relations, marketplace trades, recommendations (`ats_companies_elastic`, `ats_client_and_agency_relation_index`).
  - **Coins, Billing & Transactions:** Wallet balances, coin deductions, packages, transaction logs, email quotas (`ats_company_coins`, `ats_coin_transection_logs`, `ats_email_count`).
  - **Users, Teams & Client Contacts:** Recruiter directories, internal team rosters, client stakeholders (`ats_users_list`, `ats_client_list`).
  - **Audit Trails & Timelines:** Recruiter comments, interview feedback, timelines, dispatch logs.
- **Zero MySQL Reads Across ALL Entities:** If data is being queried, searched, filtered, counted, aggregated, or displayed on ANY page or drawer, it **MUST be read from Elasticsearch (or Redis cache)**, NEVER from MySQL.
- **Zero Direct UI Writes:** Web requests must never write directly to Elasticsearch. All state changes enter via MySQL first and synchronize to Elasticsearch via Observers or queued Horizon jobs (`Elastic*Index`).
- **Sub-15ms Read Budget:** Denormalized indices must be structured so that query responses complete within 5ms–15ms.

### 3. Dedicated Hardware Specification: 32 GB RAM per Tier
The platform infrastructure is provisioned with high-performance dedicated resources:
- **Application / Web Tier:** **32 GB RAM dedicated** to Laravel 11 Octane + FrankenPHP workers + Redis (`CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`).
- **MySQL Database Tier:** **32 GB RAM dedicated** to MySQL with InnoDB buffer pool sized at ~24 GB (75% rule) dedicated exclusively to transaction logging, indexing, and write throughput.
- **Elasticsearch Cluster Tier:** **32 GB RAM dedicated** with JVM heap locked at 16 GB (50% rule) and 16 GB reserved for OS filesystem cache / Lucene mmap.

### 4. 10,000 Concurrent Users Per Page Scalability Target
- Every module, view, table, drawer, modal, and action must be engineered to sustain **10,000 active concurrent recruiters/users** per page simultaneously without:
  - Page freezes or browser crashes (main thread long tasks > 50ms strictly forbidden).
  - PHP-FPM / Octane worker pool starvation.
  - MySQL connection pool exhaustion.
  - Elasticsearch 429 queue rejections.
- Design every vertical slice under the assumption that 10K recruiters are clicking buttons at the exact same second.

### 5. Target 0% Server Strain (Server / MySQL / Elasticsearch)
- **Relentless Server Load Elimination:**
  - 0% MySQL read load on user visits.
  - 0% Elasticsearch load for repeated page actions through multi-tenant Redis and client-side caching.
  - 0% Server CPU spent on heavy document assembly (PDFs, watermarking, vector generation, formatting).
  - 0% Server bandwidth wasted streaming massive pre-rendered Blade HTML (payloads must be micro-JSON < 5 KB).

### 6. Client-First / Browser Execution (User RAM & Browser CPU First)
- **Client Compute Mandate:** Offload heavy computing from cloud servers to the user's local machine (leveraging user RAM, GPU, and multi-core CPU).
  - Document formatting, vector PDF compilation, redaction, skill keyword highlighting, and client dossier creation must execute client-side via **WebAssembly (WASM), Web Workers, and `pdf-lib`**.
  - Static drawer shells and engines must be cached in browser memory / `CacheStorage` and pre-warmed via `requestIdleCallback` without bloating host pages.
  - Documents and assets must be uploaded **directly to Cloudflare R2 / S3 via presigned PUT URLs**, bypassing the Laravel application server.
- **Graceful Server Fallback:** The server / Elasticsearch is strictly a **resilience fallback**. If browser RAM exhausts, WASM fails, or an unsupported browser API is detected, the system gracefully degrades to server-side generation via circuit breaker.

### 7. Fastest Response with 100% Authoritative, Fresh ATS Data
- **Zero Stale Data Tolerance:** Because JERO is a mission-critical Applicant Tracking System and talent marketplace, recruiters cannot make hiring decisions on stale data. Candidate hiring stages, interview times, coin balances, lock statuses, and recruiter notes must be authoritative.
- Fast response (< 16ms UI interaction, < 50ms drawer activation) must **NEVER** compromise data correctness. Never serve stale candidate data in the name of caching.

### 8. Multi-Tenant Cache Invalidation & Organization-Wide Real-Time Synchronization
- **Strict Tenant Partitioning:** Every cache key in Redis, client memory, or browser storage **MUST** be scoped by tenant (`company_id`).
- **Instant Atomic Invalidation:** The exact millisecond any candidate, job, application, or company detail is added or edited, the corresponding tenant cache keys **MUST BE INSTANTLY DESTROYED / PURGED** across Redis and the organization.
- **Real-Time Consistency:** All users and recruiters within that organization/company must immediately receive the authoritative updated state without ghost reads, stale views, or delay.

### 9. Zero Business Logic Alteration & Strict Blast Radius Isolation (Zero Regressions)
- **Zero Business Logic Mutations:** For any new change, performance optimization, refactor, or upgrade on ANY page or feature, the existing core business logic **MUST REMAIN 100% UNTOUCHED AND UNALTERED** unless explicitly commanded by the user. This includes: candidate matching & fit scoring algorithms, hiring stage transition rules, coin billing rates, credit deductions, permission checks, and ATS status workflows. Never invent, alter, or "improve" business logic unprompted.
- **Strict Blast Radius Isolation (Zero Cross-Module Impact):** Upgrading or modifying one page, drawer, tab, or feature must **NEVER affect, break, degrade, or mutate any other feature or module across the system**. Changes must be self-contained:
  - Modifying Forward Resume must never impact Mass Email, Send Email, Candidate Export, Copy, Quick Upload, Selection Bar, Application Tracker, Bench Candidates, or Marketplace.
  - Modifying filters must never break sorting, pagination, or drawer interactions.
- **Guaranteed Backward Compatibility:** All upgrades must run as isolated vertical slices behind feature flags with 100% functional fallback paths, guaranteeing that existing systems and workflows continue operating with zero disruption.

### 10. Complete Page & Blade Isolation (Strict Anti-Entanglement Mandate)
- **Absolute Page/Blade Isolation:** Every page, Blade view, drawer, and modal designed, developed, updated, or edited must be **completely isolated** to its own page scope.
- **Never Entangle Pages:** **DO NOT** reuse or share stateful JavaScript controllers, global window objects, shared DOM element IDs, or tightly coupled Blade templates across different pages or sections (e.g. CV Tracker, Application Tracker, Client Application, Bench Candidates, Job Requisitions).
- **Zero Cross-Module Ripple Effects:** When upgrading, fixing, or adding features to Page A, never use shared concepts or direct code hooks that bind Page A to Page B. A change or upgrade on one page must **NEVER** produce side-effects, syntax collisions, state corruption, or regressions on other pages, blades, drawers, or modules.
- **Isolated Component Architecture:** Each page owns its dedicated, isolated JavaScript module/class namespace (e.g. `window.CvTrackerManager`, `window.AppTrackerManager`), scoped DOM selectors, page-specific lifecycle hooks, and self-contained styling.

### 11. 15.6-Inch Screen Fit & Small-Screen Responsiveness Mandate
- **Primary Design Baseline:** Every UI/UX design, table, drawer, modal, card, filter bar, and dashboard layout must **perfectly fit on a standard 15.6-inch laptop screen** (primary recruiter resolutions: **1366×768** and **1920×1080** at 125% OS scaling) **and smaller screens**, with 100% responsiveness.
- **Zero Horizontal Page Explosion:** The main page layout must never generate an unwanted horizontal scrollbar. Content must fluidly reflow or utilize clean inner container scrolling.
- **Strict Typography & Metronic Cap:** Adhere strictly to the Metronic theme and **Poppins font**. Maximum font size is **14px** across all data tables, form inputs, labels, toolbars, and buttons (secondary captions at 11px–12px; section headers max 14px bold). Never use fonts larger than 14px on data pages.
- **Viewport Height & Drawer Constraints:**
  - Standard 768px screen height leaves only ~600px usable viewport after browser tabs and address bars. All modals and drawers must scroll internally (`overflow-y: auto`) without cutting off footers or primary action buttons.
  - Drawer widths must be strictly responsive (e.g., `w-100 w-lg-650px` or max 80vw–85vw on 1366px screens) so context is never lost.
  - Sticky bottom action bars (selection bars, bulk action trays) must anchor reliably within the visible viewport without overlapping table pagination.

---

## 🔒 CONTEXT & TOKEN DISCIPLINE
1. **Read docs first, not the code:** `ARCHITECTURE.md` → `MODULE_MAP.md` → `.ai/tasks/*.md` → `.ai/summaries/*`.
2. **Never read large controllers whole:** `HomeController`, `ServiceCompany*Controller`, `SuperAdminCompanyController`, etc. are 500 KB–800 KB. Use `Grep` for the method/action and view only that specific line window.
3. **Never read route files whole:** Use `php artisan route:list --path=<x>` or grep the `->name()`.
4. **Don't scan `resources/views/` broadly:** Target views by exact feature.
5. **Ignore dead code:** Anything matching `OLD_`, `old_`, `OLD_2_`, `oldBackUp/`, stray `*.blade.php` / `error_log` inside `app/`, and root-level legacy controllers. See `BAD_PATTERNS.md`.

---

## 🛠️ ARCHITECTURAL FIT & CODING STANDARDS
6. **Match existing patterns:** No unprompted architectural layers (no repository pattern). Stick to tested vertical slices: Route + FormRequest + Action/Service + ES read sync + Observer + Blade/JS module.
7. **Reuse existing helpers:** Check `app/Services/`, `app/Http/Requests/` (55 form requests), `app/Traits/`, and `app/Helper/`.
8. **Preserve baked-in typos:** Names like `condidate`, `ElasticCaompany`, `commericial`, `coin_transection_logs` map to real remote MySQL tables and ES indices. Renaming them breaks production.
9. **Respect route middleware hierarchy:** `auth` → persona `prefix/name` → `EnsureOneDeviceLogin` → `package` middleware.
10. **Tenancy enforcement:** Eloquent is tenant-scoped by `company_id` (`TenantScope` / `BelongsToTenant`). **Raw `DB::table()` queries must use `tenant_table('<name>')` or append `company_id` explicitly.** Any legitimate cross-tenant query requires a `// TENANCY-EXEMPT: <reason>` comment.
11. **Database schema truth:** The remote live database schema in `database/schema/mysql-schema.sql` (217 tables) is the sole truth. Schema is NOT in migrations; do not invent migrations.
12. **Queue heavy work:** Resume parsing, bulk imports, mass mail, background re-indexing → dispatch to Redis/Horizon. Never run inline.
13. **Octane safety:** FrankenPHP / Octane workers are long-lived. Never store request state in static class properties or singletons; avoid memory leaks.
14. **Document every change:** Every new file, class, or method requires a clear intent docblock explaining the *why*. Undocumented code is considered incomplete.
15. **UI & UX Standard:** Metronic theme, **Poppins font**, **maximum font 14px**, clean icon-based design (NO 3D). **Must perfectly fit 15.6-inch laptop screens (1366×768 & 1920×1080) and smaller screens** with 100% responsiveness, zero page-level horizontal overflow, and internal drawer scrolling ([UI_UX_STANDARDS.md](UI_UX_STANDARDS.md)).
16. **OpenDoc WASM Engine Isolation Mandate:** The three document engines — Download Resume Center (`AgyResumeRenderer.js` / `showdetail.blade.php`), Forward Resume (`AgyForwardResumeEngine.js` / `ForwardResumeManager.js`), and Online Editor (`AgyEditorWasmEngine.js` / `resume-studio.blade.php`) — are **strictly isolated into three separate, independent engines**. Never merge them, cross-import them, or break their separation under the guise of "DRY".
17. **Complete Page/Blade Isolation Mandate:** Every page, Blade view, drawer, and modal must be completely isolated to that page. Do NOT reuse, share, or bind stateful JS controllers, global variables, or coupled Blade templates across different pages/sections (e.g. CV Tracker, App Tracker, Client App). Changes to one page must NEVER affect or break another.
18. **Product Company & Staffing Company Code Freeze Mandate:** Files under `resources/views/product_company/` and `resources/views/staffing_company/` (and their respective backend controllers/modules) are strictly frozen and MUST NOT be edited, refactored, or modified. Currency dropdown upgrades, UI enhancements, and ATS feature work are strictly scoped to **Service Company** (`resources/views/service_company/`, `app/Http/Controllers/ServiceCompany/`) and **Admin** (`resources/views/admin/`, `app/Http/Controllers/Admin/`) portals unless explicitly instructed otherwise by the product owner.
