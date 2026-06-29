# AI Guardrails — Ground Truth, Anti-Hallucination & Self-Check

> How an AI agent stays **correct** in a huge, non-standard codebase where guessing is
> easy and expensive. These are behavioral guardrails layered on [AI_RULES.md](AI_RULES.md).
> The meta-rule: **verify before you assert; never invent.**

---

## 1. Ground-truth sources (trust these, in order)
1. **The live code** (grep/read the actual file region) — over any doc or memory.
2. **The live DB** for schema (`php artisan db:table <t>`) — schema is NOT in migrations.
3. **`php artisan route:list`** for routes — over reading route files whole.
4. **Config/`.env` keys** for ES index names, integrations — never hardcode.
5. Docs (`docs/`, `.ai/`) for *orientation & intent* — they may lag the code (point-in-time).

> If a doc/memory and the code disagree, **the code wins** — then fix the doc.

## 2. Things you must NEVER assume — verify each
| Claim | How to verify |
|---|---|
| "Column X exists / its type" | `php artisan db:table <table>` (live DB) |
| "Route/method exists" | `php artisan route:list --path=<x>` |
| "This ES index is named …" | read `config`/`.env` `ELASTIC_*` |
| "There's a validation rule for this" | `ls app/Http/Requests | grep -i <entity>` |
| "A service/helper already does this" | grep `app/Services`, `app/Traits`, `app/Helper` |
| "Tenant scope is applied here" | read the model + the actual query (raw = not scoped) |
| "This is dead code / safe to remove" | grep for references; check it's `OLD_*`/`oldBackUp` |

## 3. Anti-hallucination rules
- **No invented identifiers** — class/table/column/route/index names must be confirmed in
  code/DB/config. If unconfirmed, say so and verify, don't guess.
- **No invented APIs** — don't assume a package method exists; check the version in
  `composer.json`/`composer.lock` and the vendor signature.
- **No invented migrations/columns** — schema is in the live DB; propose, confirm, then act.
- **Preserve real spellings** — `condidate`, `commericial`, `Caompany`, `transection`,
  and `metronics` (= Metronic theme) are real identifiers, not typos to "fix."
- **Mark uncertainty explicitly** — use `[INFERRED]` / `[UNCERTAIN]` / "needs confirmation"
  rather than presenting a guess as fact.
- **Cite where you looked** — reference `file:line` so the human can check you.

## 4. Pre-edit self-check (answer before writing code)
- [ ] Did I confirm the route/method/columns against ground truth (not memory)?
- [ ] Which exact files will I change, and why? (Stated the plan.)
- [ ] Does this touch tenancy? (Raw query → did I add `company_id`?)
- [ ] Does this touch an indexed entity? (Did I update the `Elastic*Index` + Observer?)
- [ ] Is any work heavy? (Then it must be queued.)
- [ ] Am I using `config()` not `env()` outside config?
- [ ] Am I matching existing patterns (no unprompted new layers/refactors)?
- [ ] Is this business logic (ranking/billing/status)? (Then get sign-off.)

## 5. Post-edit self-check (before saying "done")
- [ ] New code has purpose comments/docblocks ([CODE_DOCUMENTATION_STANDARD.md](CODE_DOCUMENTATION_STANDARD.md)).
- [ ] Ran the relevant checks in [VERIFY.md](VERIFY.md) — and report exactly what ran + result.
- [ ] No leftover `dd()`/`dump()`/debug/commented-out code introduced.
- [ ] No `{!! !!}` on user data (escape it).
- [ ] Stated before/after numbers for any performance claim.
- [ ] Satisfied [DEFINITION_OF_DONE.md](DEFINITION_OF_DONE.md).

## 6. Honesty protocol (report faithfully)
- If tests fail, **say so** with the output. If a step was skipped (e.g., couldn't reach
  remote DB/ES), **say that** — don't claim verification you didn't do.
- If you're unsure, **ask or flag**, don't fabricate confidence.
- If you discover the task contradicts a doc/memory, **surface it** rather than silently
  proceeding.
- Don't claim "fixed/done/verified" unless you actually verified it.

## 7. Token & focus discipline (be fast as well as right)
- Read docs/summaries first; **grep god files for the method**, never read them whole.
- Load only the files the task needs. Prefer `route:list`/`db:table` over reading big files.
- Stop when you have enough to act — don't over-explore or re-derive known facts.

## 8. Stop-and-ask triggers (don't proceed alone)
- A DB **schema change** is implied.
- A change to **business logic** (ranking/matching, billing, status pipeline).
- Anything **cross-tenant** before isolation is hardened.
- Deleting/overwriting code you didn't create and that contradicts its description.
- An **irreversible / outward-facing** action (sending mail, payments, prod data, deploys).

> Net: an agent that verifies ground truth, never invents, self-checks before and after,
> reports honestly, and asks at the right moments is one you can trust at scale.
