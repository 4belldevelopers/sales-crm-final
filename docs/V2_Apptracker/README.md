# V2_Apptracker — Audits & Plans

> Apptracker feature scope: `service_company/apptracker` (also mirrors into
> `ProductCompany`, `StaffingCompany`, `Admin`, `SuperAdmin` personas).

This folder collects GOD-level audits and implementation plans for the apptracker
feature. Start with the **GOD-level Audit**, then read the **10-Step Plan** to
see the roll-out order.

## Contents

| File | Purpose |
|---|---|
| [`GOD_LEVEL_AUDIT.md`](GOD_LEVEL_AUDIT.md) | Full audit (Aug 2026): surface, critical bugs, top 10 hot-path issues, counter design, frontend weight, refactor proposal, 32 GB server math. |
| [`10_STEP_IMPLEMENTATION_PLAN.md`](10_STEP_IMPLEMENTATION_PLAN.md) | Concrete 10-step roll-out to scale to 10 K recruiters on 32 GB — no business-logic changes, ~3 dev-days total. |

## Read order

1. `GOD_LEVEL_AUDIT.md` — read first to understand *why* each fix is needed.
2. `10_STEP_IMPLEMENTATION_PLAN.md` — then read the roll-out order.

## Convention for new audits / plans in this folder

- File name format: `SCREAMING_SNAKE_CASE.md`.
- Each file must end with a **Cross-references** section linking back to the
  audit (so future audits can diff against the baseline).
- No business-logic changes without explicit sign-off (see `../AI_RULES.md` rule #8).
- Keep tenant-isolation rules in mind (`../AI_RULES.md` rule #3 + the
  `tenant-isolation-blanket-scopes-break-marketplace` memory note).