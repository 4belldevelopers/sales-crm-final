# AI Bootstrap — Start Here

Paste/point any AI coding agent at this file when starting a task in this repo.

## What this project is
A multi-tenant Laravel 11 hiring/ATS SaaS (JERO). Personas: Service Company (agency),
Product Company (employer), Staffing Company, Super Admin, Admin, public Front.
Elasticsearch is a denormalized read store (not just search); Redis/Horizon for
queues+cache+sessions; Octane/FrankenPHP runtime; multiple payment gateways.
Full detail: [ARCHITECTURE.md](ARCHITECTURE.md).

## The 5-step protocol (do this every task)

### 1. Read architecture docs first (cheap context)
- [ARCHITECTURE.md](ARCHITECTURE.md) — system map
- [MODULE_MAP.md](MODULE_MAP.md) — which files belong to which module
- [AI_RULES.md](AI_RULES.md) — hard rules (tenancy, ES sync, no schema guessing)
- `.ai/tasks/<module>.md` — module playbook for the area you're touching
- `.ai/summaries/*` — one-line-per-class indexes

### 2. Identify the module & persona
Determine which persona route file applies (`service.php` / `product.php` /
`superadmin.php` / `web.php`) and which module owns the feature. Use
[MODULE_MAP.md](MODULE_MAP.md). Find the exact route: `php artisan route:list --path=<x>`.

### 3. Load ONLY the required files
- Controllers are huge — `Grep` for the method, read just that span. Never whole-file.
- Pull in the related Service, FormRequest, Model, Observer, and ES index service.
- Skip dead code (`OLD_*`, `old_*`, `oldBackUp/`) and broad view scans.

### 4. Make a plan before editing
State: files to change, why, tenancy impact (`company_id`?), whether ES re-index /
an Observer is involved, whether work must be queued, and any business-logic risk.
**If the task implies a DB schema change, STOP and confirm** — schema is in the live
DB, not migrations.

### 5. Validate after editing
Run the relevant checks in [VERIFY.md](VERIFY.md): syntax, `route:list`, targeted
tests, `pint`, build. Report what you ran and the result honestly.

## Hard "do not" list (see AI_RULES.md for full)
- ❌ Read 500 KB+ controllers or full route files into context.
- ❌ Rename existing tables/columns/indices or "fix" baked-in typos.
- ❌ Add migrations or assume columns without confirming the live schema.
- ❌ Do heavy work synchronously (queue it).
- ❌ Forget `company_id` on raw `DB::table()` queries (tenant leak).
- ❌ Change ranking/billing/status business logic unprompted.
- ❌ Write to `hire.jero.works` — use `testing.jero.works`.

## Quick orientation commands
```bash
php artisan route:list --path=<fragment>     # find a route + controller@method
ls app/Http/Requests | grep -i <entity>      # find validation
ls app/Services | grep -i <thing>            # find service
grep -rn "function <method>" app/Http/Controllers/<Persona>/<File>.php
```
