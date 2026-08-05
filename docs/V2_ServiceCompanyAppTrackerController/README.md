# V2_ServiceCompanyAppTrackerController — Controller Audits

> Scope: `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php`
> (11,374 lines, 33 methods, canonical ES-driven implementation for service-company
> recruiters) plus `app/Http/Controllers/Admin/AdminAppTrackerController.php`
> (1,410 lines, 40 methods, JERO internal-admin panel, still wired in
> `routes/web.php:1483-1507`).
>
> **Out of scope per user directive:** `ProductCompanyAppTrackerController.php`
> and `StaffingCompanyAppTrackerController.php`. Note: 26 routes at
> `routes/web.php:887-933` do point at `ProductCompanyAppTrackerController` —
> resolved in §1.1 of the main audit (reachable but deprecated; covered by
> Step 9 of the 13-step plan).

This folder contains controller-only audits. For the apptracker feature as a
whole (views, routes, schema, ES), see [`../V2_Apptracker/`](../V2_Apptracker/).

## Contents

| File | Purpose |
|---|---|
| [`CONTROLLER_AUDIT.md`](CONTROLLER_AUDIT.md) | 33-method ledger for ServiceCompany, Admin duplication shape, hidden shared-state machine, code-smell inventory, 6-phase refactor scope covering both live controllers. |

## Read order

1. `CONTROLLER_AUDIT.md` — single file, structured top-down: scope → ledger → state → smells → missing-methods → refactor.

## Sister documents

- `../V2_Apptracker/GOD_LEVEL_AUDIT.md` — apptracker feature audit (views, routes, schema, ES).
- `../V2_Apptracker/13_STEP_IMPLEMENTATION_PLAN.md` — perf roll-out the controller refactor builds on.
- `../AI_RULES.md` rule #8 (no business-logic change), rule #9 (no repository layer), rule #11 (vertical slices).

## Convention for new controller audits in this folder

- File names: `SCREAMING_SNAKE_CASE.md` (e.g. `CONTROLLER_AUDIT.md`, `BASE_CONTROLLER_AUDIT.md`).
- Each new audit must cross-link to `../V2_Apptracker/` and the canonical `../AI_RULES.md`.
- No business-logic changes without explicit sign-off (rule #8).