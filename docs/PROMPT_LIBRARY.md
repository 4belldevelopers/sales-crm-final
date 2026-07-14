# Prompt Library — Ready-Made Prompts to Drive the AI Well

> Copy-paste prompts (for you, the human) that pre-load the right rules/recipes so the AI
> performs at its best on this codebase. Replace `<...>`. Every prompt assumes the agent
> reads [CLAUDE.md](../CLAUDE.md) first.

---

## Add a feature
```
Add <feature> for the <Service|Product|Staffing> Company persona.
Follow docs/RECIPES.md Recipe 3 (vertical slice) and docs/AI_GUARDRAILS.md.
Plan the files first; keep the controller thin (logic in a Service/Action); keep MySQL↔ES
in sync; queue heavy work; UI per docs/UI_UX_STANDARDS.md (Metronic, ≤14px, fits all
laptops). Document the purpose of all new code. Add a tenant-isolation test. Then verify
per docs/VERIFY.md and satisfy docs/DEFINITION_OF_DONE.md. Do not change business logic or
the DB schema without flagging it first.
```

## Add a field to candidates
```
Add field <name> (<purpose/type>) to candidates. Follow docs/RECIPES.md Recipe 1.
Confirm the column against the live DB first (php artisan db:table job_applications) — do
NOT create a migration without sign-off. Update ElasticCandidatesIndex (+ bench), re-index,
add it to the form + tracker column (Metronic, ≤14px). Comment the purpose. Verify.
```

## Add a list/search screen
```
Add a list screen for <entity> for <persona>. Follow docs/RECIPES.md Recipe 2: page route +
*_ajax data route in the correct middleware group; ES-backed query via ElasticService with
company_id filter; DataTables JSON; Metronic dense table with sticky header + responsive
column priority. Document the query intent + tenancy. Verify.
```

## Fix a bug
```
Bug: <symptom, steps, expected vs actual>.
Follow docs/RECIPES.md Recipe 4 + docs/TROUBLESHOOTING.md. Reproduce with a failing test
first, find root cause (check: env-not-config, missing company_id, ES drift, N+1, unescaped
Blade), fix minimally, prove the test passes, run docs/VERIFY.md, and tell me exactly what
you ran. Don't read god controllers whole — grep the method.
```

## Optimize performance
```
<Screen/endpoint> is slow. Follow docs/RECIPES.md Recipe 5: measure first (query count/time,
ES took) and report the BEFORE number; diagnose (N+1 / non-sargable / MySQL-vs-ES / index);
fix in order (ES/cache → eager-load → index → sargable → cache); report AFTER numbers.
Confirm any index against the live schema. No behavior change.
```

## Review my pending changes
```
Review the current diff against docs/DEFINITION_OF_DONE.md, docs/AI_RULES.md, and
docs/SECURITY.md. Flag: missing company_id on raw queries, missing ES sync, env() outside
config, unescaped {!! !!} on user data, N+1, leftover dd()/debug, undocumented new code,
font >14px. Give file:line for each finding and a concrete fix. Don't rewrite; report.
```

## Investigate / explain (read-only)
```
Explain how <feature/flow> works end to end. Use docs/MODULE_MAP.md, docs/DATA_FLOW.md and
the relevant .ai/tasks file; grep for the methods (don't read god controllers whole). Cite
file:line. Mark anything inferred as [INFERRED]; verify routes/columns against ground truth.
Make no changes.
```

## Write tests
```
Add tests for <feature>. Follow docs/TESTING.md: include a tenant-isolation test, mock
external services (ES/S3/payments), use factories (create any missing ones after confirming
required columns via db:table). Run php artisan test --filter and report real results.
```

## Plan a larger initiative (no code yet)
```
Propose an implementation plan for <initiative> as a top architect. Use docs/PRODUCT_VISION.md,
docs/ROADMAP.md, docs/SCALABILITY.md. Give phased steps, files/modules affected, risks,
tenancy/ES/perf implications, and what needs my sign-off. No code changes yet.
```

## Touch payments/webhooks (high-care)
```
Work on <payment/webhook flow>. Follow docs/RECIPES.md Recipe 9 + docs/SECURITY.md SEC-05.
Verify signatures, ensure idempotency, confirm CSRF exemption, keep order/package/licence
updates consistent. This is business logic + money — show me the plan and wait for sign-off
before editing.
```

---
### Tips for driving the AI
- Always let it **plan before editing**; ask for the file list.
- Demand **before/after numbers** for performance and **what it actually ran** for verification.
- If it asserts a column/route exists, ask it to confirm via `db:table`/`route:list`.
- Point it at the specific **recipe** — that's where the speed comes from.
