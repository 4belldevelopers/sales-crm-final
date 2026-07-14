# Definition of Done — The Gate for Every Change

> One checklist that every change (by AI or human) must pass before it's "done." If any box
> is unchecked, the change is incomplete. Keep it honest — see [AI_GUARDRAILS.md](AI_GUARDRAILS.md) §6.

## Correctness
- [ ] Does what was asked; root cause addressed (not a symptom patch).
- [ ] Verified against **ground truth** (columns via live DB, routes via `route:list`) — not memory.
- [ ] No business logic (ranking/matching, billing, status) changed without sign-off.

## Tenancy & data
- [ ] Every new/changed query is tenant-safe: Eloquent scoped, **raw `DB::table()` filters
      `company_id`** explicitly.
- [ ] Indexed entity changes update the matching `Elastic*Index` service **and** re-index
      (Observer/Job); existing rows backfilled if needed.
- [ ] No schema invented; any column change confirmed + signed off (schema is in live DB).

## Performance
- [ ] No new N+1 (eager-loaded); no non-sargable predicate added.
- [ ] Heavy work (resume/upload, mail, bulk, ES reindex, AI) is **queued**, not inline.
- [ ] Reads use ES/cache where appropriate; performance claims include **before/after numbers**.
- [ ] `config()` used, never `env()` outside `config/`.

## Security
- [ ] User data escaped (`{{ }}`, no `{!! !!}` on user input).
- [ ] Object-level authorization checked on `{id}` routes (no IDOR); RBAC applied.
- [ ] No secrets in code/logs; webhooks verify signature + idempotency.
- [ ] No `dd()`/`dump()`/debug routes / commented-out code introduced.

## UI (if applicable)
- [ ] Follows **Metronic**; **no font > 14px**; fits 1366×768 → 4K.
- [ ] Dense tables: sticky header, responsive column priority, server-side pagination/virtualization.
- [ ] WCAG AA contrast + keyboard/focus; reusable `x-` component where sensible.

## Documentation
- [ ] **Purpose stated on all new code** (intent comment/docblock; tenancy note on raw
      queries) per [CODE_DOCUMENTATION_STANDARD.md](CODE_DOCUMENTATION_STANDARD.md).
- [ ] Affected docs updated (e.g. [BUG_REPORT.md](BUG_REPORT.md) status, [MODULE_MAP.md](MODULE_MAP.md)).
- [ ] A new repeatable pattern → added to [RECIPES.md](RECIPES.md).
- [ ] A project decision → logged in [.ai/PROJECT_MEMORY.md](../.ai/PROJECT_MEMORY.md) (+ ADR if architectural).

## Tests & verification
- [ ] Test added/updated, incl. a **tenant-isolation assertion** for data features ([TESTING.md](TESTING.md)).
- [ ] Ran the relevant [VERIFY.md](VERIFY.md) steps; **reported exactly what ran + result**.
- [ ] Matched existing patterns; no unprompted refactor/new layer.

## Final
- [ ] Built as a vertical slice (Recipe 3) where applicable; controller stayed thin.
- [ ] Stated uncertainty honestly; flagged any stop-and-ask trigger encountered.

> Copy this list into the PR/summary and tick it. "Done" = every applicable box checked
> and verified, not assumed.
