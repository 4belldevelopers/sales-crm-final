# AI Coding Rules

Rules for any AI agent (Claude Code, Cursor, Windsurf, etc.) working in this repo.
These exist because this codebase is **huge** (controllers up to ~800 KB, ~2,240
routes, 86 models) and partly **non-standard** (schema not in migrations, ES as a
read store). Following these saves tokens and prevents breakage.

## Context / token discipline
1. **Read the docs first**, not the code: `ARCHITECTURE.md` → `MODULE_MAP.md` →
   the relevant `.ai/tasks/*.md` → `.ai/summaries/*`. Only then open source.
2. **Never read a large controller whole.** `HomeController`,
   `ServiceCompany*Controller`, `SuperAdminCompanyController`, `SaasFrontController`
   etc. are hundreds of KB. Use `Grep` for the method/route name and read only that
   region (with a small line window).
3. **Never read route files whole.** Use `php artisan route:list --path=<x>` or grep
   the `->name()`.
4. **Don't scan `resources/views/` broadly** — it's enormous (see memory
   [[agency-marketplace-views]] for where key views live). Target by feature.
5. **Ignore dead code:** anything matching `OLD_`, `old_`, `OLD_2_`, files under
   `oldBackUp/`, stray `*.blade.php` / `error_log` inside `app/`, and the root-level
   `ServiceCompanyJobApplicationController.php`. See `BAD_PATTERNS.md`.

## Make changes that fit
6. **Match the existing pattern.** This app has no repository layer and fat
   controllers/services. Don't introduce new architectural layers unprompted.
7. **Reuse, don't duplicate.** Before writing logic, check `app/Services/`,
   `app/Http/Requests/` (55 form requests), `app/Traits/`, and `app/Helper/`.
8. **Preserve existing spellings/typos** (`condidate`, `ElasticCaompany`,
   `commericial`, `coin_transection_logs`) — they map to real tables/ES indices.
   Renaming breaks things.
9. **Respect the route group nesting** (`auth` → persona `prefix/name` →
   `EnsureOneDeviceLogin` → `package` middleware). Add routes in the right group.

## Data & tenancy
10. **Tenancy:** Eloquent is auto-scoped by `company_id` (`TenantScope` / `BelongsToTenant`). **Raw
    `DB::table()` queries must use `tenant_table('<name>')` or append `company_id` explicitly.** Legitimate cross-tenant or global queries require a `// TENANCY-EXEMPT: <reason>` comment.
11. **Ask before any schema change.** Schema lives in the **live remote DB**, not
    migrations. Do not invent migrations or assume columns — confirm against the DB.
12. **Keep MySQL ↔ Elasticsearch in sync.** When adding a model write path, check
    whether an Observer should re-index ES (or you'll create drift). When changing
    indexed fields, update the matching `Elastic*Index` service.

## Performance
13. **Queue heavy work.** Resume parse/upload, mail, mass mail, bulk import, ES bulk
    re-index → dispatch a Job (Redis/Horizon). Never do them inline in a request.
14. **Watch N+1.** Eager-load relations; don't loop queries. Confirm indexed columns
    before adding WHERE/ORDER on big tables (`job_applications`, `jobs`).
15. **Octane safety:** workers are long-lived — don't store request state in static
    properties/singletons; don't leak resources.

## Documentation (mandatory)
20. **State the purpose of every new code.** Every new file/class/method needs a short
    intent comment or docblock explaining the *why*; complex/raw queries need an intent +
    `company_id` tenancy note; business logic and ES-sync points must be explained. Keep
    comments truthful and current; leave no commented-out/debug code. Undocumented new
    code is an incomplete change. Templates: [CODE_DOCUMENTATION_STANDARD.md](CODE_DOCUMENTATION_STANDARD.md).

## Process
16. **Plan before editing** (see `AI_BOOTSTRAP.md`). State which files you'll touch.
17. **Don't change business logic** (ranking/matching, billing, status pipelines)
    unless explicitly asked. Flag uncertainty instead of guessing.
18. **Verify after editing** using `docs/VERIFY.md`.
19. **All work goes in `testing.jero.works`**, never `hire.jero.works` (per project
    convention).
