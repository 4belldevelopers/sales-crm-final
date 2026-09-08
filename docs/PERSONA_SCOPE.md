# Persona Scope — In-Scope vs Out-of-Scope

> **Audience:** every AI/agent model (Claude Code, Cursor, Windsurf, etc.) working in this
> repo. Read this **before** any audit, plan, refactor, or code change. It is the single
> source of truth for which personas exist for *active product work*.

---

## TL;DR

This is a **four-persona product**. Two personas are legacy / dormant and must be
**ignored** for all new work unless the user explicitly re-activates them.

| Persona | Status | Action for AI |
|---|---|---|
| **Service Company** (agency) | ✅ **Active** | Build, audit, refactor freely |
| **Super Admin** | ✅ **Active** | Build, audit, refactor freely |
| **Admin** | ✅ **Active** | Build, audit, refactor freely |
| **Front (public marketing site)** | ✅ **Active** | Build, audit, refactor freely |
| **Product Company** (employer) | 🚫 **Dormant — ignore** | Do **not** add features, do not audit, do not fix bugs, do not list in scope |
| **Staffing Company** | 🚫 **Dormant — ignore** | Do **not** add features, do not audit, do not fix bugs, do not list in scope |

---

## Why this matters

The codebase contains controllers, routes, views, and tables for all six personas
(per [ARCHITECTURE.md](ARCHITECTURE.md)). Historically every AI task has spread
across all six personas by default. That is wasteful:

- It triples the audit surface and review time.
- It produces "fixes" and "audit rows" for screens the user does not use.
- It makes the 10K-concurrent / V2 / V3 roadmaps harder to execute.

The user directive (issued 2026-08-15 during the Add/Edit/Submit audit kickoff):
**"Ignore Product Co and Staffing Co."**

---

## What "ignore" means in practice

When running any audit, plan, refactor, doc, or task:

1. **Do not enumerate** Product Co or Staffing Co routes / controllers / views /
   tables in the output. Skip them and move on.
2. **Do not fix bugs** in those code paths, even if they are obvious. Mention is OK
   only if the same fix is needed in the in-scope persona (`Service` ←→ `Product`
   same-method patterns — see `[[product-routes-reachable-vs-directive]]`).
3. **Do not propose new features** for those personas.
4. **Do not migrate schema** specifically for those personas.
5. **Do not write tests** that target only those personas.
6. **V2 / V3 work** — read [[v2-doc-folders]] for the in-scope list. Anything in the
   `docs/` tree that is scoped to Product or Staffing is **not** in scope.

### Exception (read carefully)

If a refactor or audit shows a **shared primitive** (Observer, Service, ES index,
middleware, trait, FormRequest) that is used by both Service and Product paths, fix
it once in the shared layer, but **do not extend the change to Product-specific call
sites** unless the user asks.

---

## How to identify the persona of a file or route

- Route file: `routes/service.php` → Service Co. `routes/product.php` → **ignore**.
  `routes/superadmin.php` and `routes/admin.php` → in scope. `routes/web.php` →
  mixed; check the `prefix`/`name` and the controller namespace.
- Controller namespace: `app/Http/Controllers/ServiceCompany/` → Service Co.
  `app/Http/Controllers/ProductCompany/` → **ignore**.
  `app/Http/Controllers/StaffingCompany/` → **ignore**.
  `app/Http/Controllers/SuperAdmin/` and `app/Http/Controllers/Admin/` → in scope.
- View path: `resources/views/service_company/` → Service Co. `resources/views/
  product_company/` → **ignore**. `resources/views/staffing_company/` → **ignore**.
- DB table prefix/namespace: there is no enforced prefix, but cross-reference with
  `ats_*`/`saas_*` schema in [DB_SCHEMA.md](DB_SCHEMA.md) to confirm tenancy.

---

## Compensation for the lost persona coverage

If a future user re-activates Product Co or Staffing Co, the dormant code is still
in the repo and can be re-enabled by lifting the "ignore" rule. Until then:

- Audits must explicitly state they cover only the four in-scope personas.
- 10K-concurrent metrics, perf budgets, and V2/V3 plans must be scoped to Service +
  Admin + SuperAdmin + Front only.
- The "personas missing from this audit" footer is **mandatory** on every doc
  produced from this directive.

---

## Related links

- [[product-routes-reachable-vs-directive]] — earlier note on the same theme
- [Add-Edit-Submit-Audit.md](V2_Add-Edit-Submit/Add-Edit-Submit-Audit.md) — first audit produced under
  this scope rule
- [CLAUDE.md](../CLAUDE.md) — top-level project memory; mirrored scope note lives there
- [AI_BOOTSTRAP.md](AI_BOOTSTRAP.md) — the 5-step protocol; step 2 ("identify the
  persona") must use this scope rule
