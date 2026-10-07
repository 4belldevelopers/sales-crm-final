# AGENTS.md — Entry Point for AI Coding Agents

> Standard entry file read by many AI tools (and humans). The **single source of truth is
> [CLAUDE.md](CLAUDE.md)** — read it first. This file mirrors the essentials so any agent
> that looks for `AGENTS.md` is oriented.

## TL;DR & System Profile
- **Product:** JERO — multi-tenant B2B hiring SaaS = GOD-level ATS + talent marketplace (companies trade talent).
- **Architecture:** CQRS: **MySQL strictly for writes (`INSERT`/`UPDATE`/`DELETE`)**, **Elasticsearch strictly for reads (100% search/queries/filters)**, Redis/Horizon queues, Octane/FrankenPHP, Reverb.
- **Hardware Profile:** Dedicated **32 GB RAM each** for Web/App Server, MySQL database, and Elasticsearch cluster.
- **Scale Target:** **10,000 concurrent users per page** with **0% server/DB read strain** achieved via Client-First / Browser-Execution (User RAM + WASM) with server fallback.
- **Work folder:** `testing.jero.works` (this one). Never `hire.jero.works`.
- **Read order:** [CLAUDE.md](CLAUDE.md) → [docs/AI_BOOTSTRAP.md](docs/AI_BOOTSTRAP.md) → [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) → [docs/AI_RULES.md](docs/AI_RULES.md) → [docs/GLOSSARY.md](docs/GLOSSARY.md).

## Non-negotiable Rules (Full list: [docs/AI_RULES.md](docs/AI_RULES.md))
1. **MySQL is ONLY for Add / Edit / Delete:** 0% MySQL reads on web/API request paths. No `SELECT` or joins on user page loads.
2. **Elasticsearch is ONLY for Read (Universal Scope):** Serves 100% of queries, filters, counts, and search results across ALL data domains (candidates, jobs, requisitions, applications, companies, coins, teams) across all 60+ indices. Zero direct UI writes.
3. **Client-First Execution (User RAM & Browser CPU First):** Heavy assembly (PDFs, WASM, formatting, previews) executes in user RAM. Server/ES is strictly a circuit-breaker fallback.
4. **10K Concurrency / 0% Server Strain Target:** Architect every page to handle 10,000 concurrent users without server collapse or UI freezes.
5. **Zero Stale Data in ATS:** Recruitment pipeline data must be 100% fresh, correct, and authoritative. Latency optimization must never serve stale candidate data.
6. **Multi-Tenant Atomic Cache Invalidation:** When candidate/job/stage details are updated, tenant cache keys (`company_id`) in Redis and client memory are instantly destroyed so all organization users see updated data in real time.
7. **Zero Business Logic Mutations & Zero Module Regressions:** For any change or upgrade on any page/feature, existing core business logic must remain 100% untouched, and NO other feature, drawer, modal, or module across the system may be affected.
8. Database truth = `database/schema/mysql-schema.sql` (217 tables) — read it before any DB work. Not in migrations; don't invent migrations.
9. Eloquent is tenant-scoped by `company_id`; raw `DB::table()` is not — filter it.
10. Keep MySQL ↔ Elasticsearch in sync on writes to indexed entities.
11. Use `config()`, never `env()` outside `config/`.
12. Queue heavy work; match existing patterns; no unprompted refactors or business-logic changes.
13. Preserve baked-in name typos (they map to real tables/indices) — rename only via NAMING_CONVENTIONS.
14. UI = Metronic, Poppins font, enterprise typography scale, clean icon-based design (NO 3D). **Must perfectly fit 15.6-inch screens (1366×768 & 1920×1080) and smaller screens** with complete responsiveness and internal scrolling ([docs/UI_UX_STANDARDS.md](docs/UI_UX_STANDARDS.md)).
15. OpenDoc WASM Engine Isolation: Download Resume Center, Forward Resume, and Online Editor are strictly 3 separate engines. Never merge them.
16. **Document the purpose of every new code** — intent comment/docblock on each new file/class/method; intent + tenancy note on complex/raw queries. See [docs/CODE_DOCUMENTATION_STANDARD.md](docs/CODE_DOCUMENTATION_STANDARD.md).
17. **Complete Page/Blade Isolation:** Every page/blade designed, developed, or updated must be completely isolated to that page. Do NOT reuse or share stateful JS/drawers/scripts across pages; changes to one page must never touch or affect another page.
18. **Product Company & Staffing Company Code Freeze:** Files under `resources/views/product_company/` and `resources/views/staffing_company/` (and their respective controllers/modules) are strictly frozen. Do NOT edit or modify them unless explicitly requested. Currency and UI enhancements are strictly scoped to **Service Company** and **Admin**.

## Build/run/verify
See [docs/VERIFY.md](docs/VERIFY.md). Key: `php -l`, `./vendor/bin/pint`,
`php artisan route:list --path=<x>`, `php artisan test`, `npm run dev|build`,
`php artisan octane:start --server=frankenphp`.

## Living memory
Project state, decisions, and gotchas: [.ai/PROJECT_MEMORY.md](.ai/PROJECT_MEMORY.md).
Update it (and [CLAUDE.md](CLAUDE.md)) when project-level facts change.
