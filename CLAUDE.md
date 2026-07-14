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
Front). It is built CQRS-style: **MySQL = write store, Elasticsearch = read store**, with
Redis/Horizon queues, Octane/FrankenPHP runtime, Reverb websockets, and multiple payment
gateways. Goal: scale to **10,000 companies / 100M+ records per year** without failing.
Full concept: [docs/PRODUCT_VISION.md](docs/PRODUCT_VISION.md).

## Where the code lives ⚠️
**All work happens in this folder — `JWMaincode28June2026` (the `testing.jero.works`
codebase)** and **only** here. **Never make changes in any other folder** (e.g.
`hire.jero.works`, which is effectively empty). Every change — code, docs, config — must
be made inside this folder.

---

## Start-here reading order (cheap context before touching code)
1. This file (CLAUDE.md)
2. [docs/AI_BOOTSTRAP.md](docs/AI_BOOTSTRAP.md) — the 5-step working protocol
3. [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) — system map & stack
4. [docs/AI_RULES.md](docs/AI_RULES.md) — hard rules
5. [docs/GLOSSARY.md](docs/GLOSSARY.md) — domain vocabulary (what "bench", "requisition", "coins"… mean)
6. The relevant [`.ai/tasks/*.md`](.ai/tasks/) playbook + [`.ai/summaries/*`](.ai/summaries/)

## The full knowledge base ([docs/README.md](docs/README.md) is the index)
- **Understand the code:** ARCHITECTURE · MODULE_MAP · DB_SCHEMA · API_CONTRACTS · DATA_FLOW · DEPENDENCY_GRAPH
- **Work safely:** AI_RULES · AI_BOOTSTRAP · VERIFY · NAMING_CONVENTIONS · CODE_DOCUMENTATION_STANDARD
- **Execute well (do this for every task):** [RECIPES](docs/RECIPES.md) (cookbooks) · [AI_GUARDRAILS](docs/AI_GUARDRAILS.md) (verify, don't hallucinate) · [DEFINITION_OF_DONE](docs/DEFINITION_OF_DONE.md) (the gate) · [TESTING](docs/TESTING.md) · [TROUBLESHOOTING](docs/TROUBLESHOOTING.md) · [PROMPT_LIBRARY](docs/PROMPT_LIBRARY.md)
- **Automated enforcement:** [ENFORCEMENT](docs/ENFORCEMENT.md) — `scripts/ai-guards.sh` (no new `dd()`/`env()`), pre-commit hook, GitHub Actions CI, PHPStan. Run `bash scripts/ai-guards.sh` before committing.
- **Quality & risk:** BAD_PATTERNS · BUG_REPORT · SECURITY · PERFORMANCE_BUDGET · DOCUMENTATION_TODO
- **Product & strategy:** PRODUCT_VISION · ATS_FEATURES · MARKETPLACE_VISION · SCALABILITY · UI_UX_STANDARDS · ENTERPRISE_PLAYBOOK · ROADMAP · adr/
- **Machine-readable:** `.ai/summaries/` (1-line class indexes) · `.ai/tasks/` (module playbooks) · `.ai/PROJECT_MEMORY.md` (living state & decisions)

---

## The 13 rules that prevent breakage (full list in [docs/AI_RULES.md](docs/AI_RULES.md))
1. **Never read 500 KB+ controllers or full route files whole** — grep for the method.
   (`HomeController` ~813 KB, several `*Controller` 500 KB+, `web.php` ~158 KB.)
2. **Database truth = [`database/schema/mysql-schema.sql`](database/schema/mysql-schema.sql)** (real structure, 217 tables) — **read it before any DB work**. Schema is NOT in migrations, so never invent migrations; confirm columns there (or `php artisan db:table <t>`).
3. **Tenancy:** Eloquent is scoped by `company_id`; **raw `DB::table()` is NOT** — filter it yourself.
4. **Keep MySQL ↔ Elasticsearch in sync** — writes to indexed entities must re-index (Observer/Job).
5. **Use `config()`, never `env()` outside `config/`** — `env()` returns null under `config:cache`/Octane (active bug — see BUG-02).
6. **Queue heavy work** (resume parse/upload, mail, bulk, ES reindex).
7. **Preserve baked-in typos** (`condidate`, `commericial`, `Caompany`, `transection`) — they map to real tables/indices. Rename only via the safe procedure in NAMING_CONVENTIONS.
8. **No business-logic changes** (ranking/matching, billing, status pipelines) without sign-off.
9. **Match existing patterns** — no repository layer; fat controllers + services. Don't add layers unprompted.
10. **UI:** follow Metronic, **Poppins font**, **max font 14px**, **clean icon-based design (NO 3D)**, must fit 1366×768 → 4K (see UI_UX_STANDARDS).
11. **Build features as vertical slices** (Route + FormRequest + Action + ES sync + Observer + Reverb event + Blade component + **test**), extracted out of god controllers.
12. **Measure & verify** (before/after numbers; run [docs/VERIFY.md](docs/VERIFY.md)); mark uncertainty explicitly.
13. **State the purpose of every new code** — every new file/class/method gets a short intent comment/docblock (the *why*), complex/raw queries get an intent + tenancy note. Undocumented new code = incomplete change. See [docs/CODE_DOCUMENTATION_STANDARD.md](docs/CODE_DOCUMENTATION_STANDARD.md).

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
