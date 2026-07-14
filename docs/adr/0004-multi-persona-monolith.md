# ADR 0004 — Single monolith with per-persona namespaces & route files

**Status:** Active (observed) · **[INFERRED] rationale**

## Decision
Keep one Laravel codebase serving all personas (Service Company, Product Company,
Staffing Company, Super Admin, Admin, Front). Separate by **controller namespace**
(`app/Http/Controllers/<Persona>/`) and **route file** (`service.php`, `product.php`,
`superadmin.php`, `web.php`), each with its own `prefix()/name()` group and middleware.
Tenancy via a `company_id` Eloquent global scope.

## Reason (inferred)
- Shared domain (jobs, candidates, interviews, billing) reused across personas;
  one codebase avoids duplication and eases shared model/service reuse.
- Per-persona route files keep persona features grouped and gated by persona/package
  middleware.

## Alternatives (inferred)
- **Separate apps/services per persona** — clean isolation, but heavy duplication and
  ops cost for a shared domain.
- **`nwidart/laravel-modules`** — the package is installed but **unused** (no
  `Modules/` dir); a real module split was apparently considered and abandoned.

## Tradeoffs
- ➕ Maximal code reuse; single deploy.
- ➖ Controllers grew into **god classes** (per-persona duplication still happened at
  the controller level — see `ServiceCompany*` vs `ProductCompany*`).
- ➖ Blast radius: a shared-model change affects all personas at once.

## Consequences
Place new code in the correct persona namespace + route group. Expect to mirror some
changes across `ServiceCompany`/`ProductCompany`/`StaffingCompany` controllers; prefer
extracting shared logic into `app/Services` to reduce that duplication.
