# V2_Apptracker — Audits & Plans

> **Scope (per explicit user directive):** ServiceCompany + Admin only.
> - **ServiceCompany** — `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php`
>   (11,374 lines, 33 methods, ES-driven canonical implementation, wired in
>   `routes/service.php:239-287`).
> - **Admin** — `app/Http/Controllers/Admin/AdminAppTrackerController.php`
>   (1,410 lines, 40 methods, JERO internal-admin panel, wired in
>   `routes/web.php:1483-1507`).
>
> **Out of scope:** ProductCompany + Staffing controllers. (Note: 26 routes
> in `routes/web.php:887-933` do point at `ProductCompanyAppTrackerController` —
> resolved in §1.1 of `GOD_LEVEL_AUDIT.md`: reachable but deprecated; covered
> by Step 1 of the 18-step master plan.)

This folder collects GOD-level audits and implementation plans for the apptracker
feature. Start with the **GOD-level Audit**, then read the **18-Step Plan** to
see the roll-out order.

## Contents

| File | Purpose |
|---|---|
| [`GOD_LEVEL_AUDIT.md`](GOD_LEVEL_AUDIT.md) | Full audit (Aug 2026), Service+Admin-scoped: surface, critical bugs, top 10 hot-path issues, counter design, frontend weight, refactor proposal, 32 GB server math, 12-objective compliance review (§10.1). |
| [`18_STEP_IMPLEMENTATION_PLAN.md`](18_STEP_IMPLEMENTATION_PLAN.md) | Concrete **18-step** roll-out across 4 phases to scale to 10 K recruiters on 32 GB — applies to Service + Admin controllers, no business-logic changes, ~12 dev-days total. Closes all 14 objectives from the Aug 2026 review. |

## Read order

1. `GOD_LEVEL_AUDIT.md` — read first to understand *why* each fix is needed.
2. `18_STEP_IMPLEMENTATION_PLAN.md` — then read the roll-out order.
3. `../V2_ServiceCompanyAppTrackerController/CONTROLLER_AUDIT.md` — sister audit, controller-only ledger + 6-phase slice refactor.

## Convention for new audits / plans in this folder

- File name format: `SCREAMING_SNAKE_CASE.md`.
- Each file must end with a **Cross-references** section linking back to the
  audit (so future audits can diff against the baseline).
- No business-logic changes without explicit sign-off (see `../AI_RULES.md` rule #8).
- Keep tenant-isolation rules in mind (`../AI_RULES.md` rule #3 + the
  `tenant-isolation-blanket-scopes-break-marketplace` memory note).