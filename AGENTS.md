# AGENTS.md — Entry Point for AI Coding Agents

> Standard entry file read by many AI tools (and humans). The **single source of truth is
> [CLAUDE.md](CLAUDE.md)** — read it first. This file mirrors the essentials so any agent
> that looks for `AGENTS.md` is oriented.

## TL;DR
- **Product:** JERO — multi-tenant B2B hiring SaaS = GOD-level ATS + talent marketplace
  (companies trade talent). Laravel 11, MySQL (write) + Elasticsearch (read), Redis/Horizon,
  Octane/FrankenPHP, Reverb, multi-gateway payments. Concept: [docs/PRODUCT_VISION.md](docs/PRODUCT_VISION.md).
- **Work folder:** `testing.jero.works` (this one). Never `hire.jero.works`.
- **Read order:** [CLAUDE.md](CLAUDE.md) → [docs/AI_BOOTSTRAP.md](docs/AI_BOOTSTRAP.md) →
  [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) → [docs/AI_RULES.md](docs/AI_RULES.md) →
  [docs/GLOSSARY.md](docs/GLOSSARY.md). Full index: [docs/README.md](docs/README.md).

## Non-negotiable rules (full list: [docs/AI_RULES.md](docs/AI_RULES.md))
1. Never read huge controllers/route files whole — grep for the method.
2. Database truth = `database/schema/mysql-schema.sql` (217 tables) — read it before any DB work. Not in migrations; don't invent migrations.
3. Eloquent is tenant-scoped by `company_id`; raw `DB::table()` is not — filter it.
4. Keep MySQL ↔ Elasticsearch in sync on writes to indexed entities.
5. Use `config()`, never `env()` outside `config/`.
6. Queue heavy work; match existing patterns; no unprompted refactors or business-logic changes.
7. Preserve baked-in name typos (they map to real tables/indices) — rename only via NAMING_CONVENTIONS.
8. UI = Metronic, Poppins font, max font 14px, clean icon-based design (NO 3D), fits 1366×768 → 4K ([docs/UI_UX_STANDARDS.md](docs/UI_UX_STANDARDS.md)).
9. Features = tested vertical slices extracted from god controllers.
10. Verify changes ([docs/VERIFY.md](docs/VERIFY.md)); state uncertainty.
11. **Document the purpose of every new code** — intent comment/docblock on each new
    file/class/method; intent + tenancy note on complex/raw queries. Undocumented new
    code = incomplete. See [docs/CODE_DOCUMENTATION_STANDARD.md](docs/CODE_DOCUMENTATION_STANDARD.md).

## Build/run/verify
See [docs/VERIFY.md](docs/VERIFY.md). Key: `php -l`, `./vendor/bin/pint`,
`php artisan route:list --path=<x>`, `php artisan test`, `npm run dev|build`,
`php artisan octane:start --server=frankenphp`.

## Living memory
Project state, decisions, and gotchas: [.ai/PROJECT_MEMORY.md](.ai/PROJECT_MEMORY.md).
Update it (and [CLAUDE.md](CLAUDE.md)) when project-level facts change.
