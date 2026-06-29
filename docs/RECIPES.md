# Recipes — End-to-End Cookbooks for Common Tasks

> The single biggest speed + correctness boost for an AI agent: **step-by-step recipes for
> THIS codebase**, so you don't re-derive the vertical slice each time. Each recipe lists
> the exact files, order, and gotchas. Follow them; deviate only with reason. Cross-refs:
> [AI_RULES.md](AI_RULES.md), [DATA_FLOW.md](DATA_FLOW.md), [CODE_DOCUMENTATION_STANDARD.md](CODE_DOCUMENTATION_STANDARD.md),
> [DEFINITION_OF_DONE.md](DEFINITION_OF_DONE.md).

Universal pre-flight (every recipe assumes this):
1. Read [CLAUDE.md](../CLAUDE.md) + the relevant `.ai/tasks/*.md`.
2. Find the route/method: `php artisan route:list --path=<x>` (never read god controllers whole).
3. Confirm any DB column against the **live DB** (`php artisan db:table <t>`) — schema isn't in migrations.
4. Plan the files you'll touch before editing. Document purpose on all new code.

---

## Recipe 1 — Add a new field to a candidate (JobApplication)
**Use when:** a new attribute must appear on candidates and in trackers/search.
1. **DB (ask first):** confirm/agree the column with the owner; the column lives in the
   **live DB**, not migrations — do **not** auto-generate a migration. Verify with
   `php artisan db:table job_applications`.
2. **Model:** `app/Models/JobApplication.php` — add to `$fillable`/`$casts` if needed;
   add an accessor if it's derived. Comment its purpose.
3. **Validation:** add the rule in the relevant `app/Http/Requests/*` (don't inline).
4. **Controller/Action:** persist it in the create/update path (grep the method in the
   persona controller; keep the controller thin / push to a service).
5. **Elasticsearch (critical):** add the field to the candidate document in
   `app/Services/ElasticCandidatesIndex.php` (and bench: `ElasticBenchCandidatesIndex.php`).
   Otherwise trackers/search won't show it.
6. **Re-index:** trigger via the Observer on save (already wired) and backfill existing
   rows: run the candidate `Index*ToElasticsearch` command.
7. **UI:** add to the Blade form + tracker column (Metronic, ≤14px, responsive column
   priority — [UI_UX_STANDARDS.md](UI_UX_STANDARDS.md)).
8. **Test + verify:** add a test for persistence + ES doc shape; run [VERIFY.md](VERIFY.md).
> Gotcha: reads come from ES — if the field "doesn't show," it's almost always a missing
> ES mapping/index step, not a DB bug.

---

## Recipe 2 — Add a new list/grid screen (DataTables + Elasticsearch)
**Use when:** a new searchable list (candidates, jobs, clients…) is needed.
1. **Routes (pair):** in the persona route file (`service.php`/`product.php`), add a **page
   route** + an **`*_ajax` data route** inside the correct middleware group
   (`auth` → persona `prefix/name` → `EnsureOneDeviceLogin` → package). Follow the existing
   `all_data` / `all_data_ajax` pattern.
2. **Controller:** page method returns the Blade view; `*_ajax` method builds the ES query
   (bool/filter, `from/size` or `search_after`) via `ElasticService` and returns DataTables
   JSON (`{data, recordsTotal, recordsFiltered}`). **Filter `company_id`** (tenancy).
3. **View:** Metronic table, server-side DataTables config pointing at the `*_ajax` route;
   sticky header, responsive `data-priority`, virtualization for big sets.
4. **Document** the query intent + tenancy in a comment.
5. **Verify:** load the screen, check JSON envelope, paginate, search.
> Gotcha: don't render thousands of rows client-side — server-side pagination + ES only.

---

## Recipe 3 — Build a full new feature (vertical slice)
**Use when:** any net-new capability. This is the canonical pattern.
```
1. Route        persona route file, correct middleware group
2. FormRequest  app/Http/Requests/<Feature>Request.php (validation)
3. Action/Service  app/Services or a new Action class with one handle() — business logic here, NOT in the controller
4. Controller   thin: validate → call Action → return view/JSON
5. Model        relations/casts as needed (confirm columns vs live DB)
6. ES sync      update/create the Elastic*Index service if the entity is indexed
7. Observer     wire model events → re-index job (avoid save-loops)
8. Job          queue any heavy work (Redis/Horizon, correct queue)
9. Reverb       broadcast an event instead of client polling, if realtime
10. Blade       Metronic component, ≤14px, responsive, x- component if reusable
11. Test        feature/unit test incl. a tenant-isolation assertion
12. Docs        purpose comments on all new code; update .ai/PROJECT_MEMORY if a decision was made
```
Verify per [VERIFY.md](VERIFY.md); satisfy [DEFINITION_OF_DONE.md](DEFINITION_OF_DONE.md).

---

## Recipe 4 — Fix a bug safely
1. **Reproduce** (write a failing test first if feasible — [TESTING.md](TESTING.md)).
2. **Locate** by route → controller method (grep) → service/model. Don't read god files whole.
3. **Root-cause**, don't patch symptoms. Check the usual suspects:
   `env()` outside config (null under cache), missing `company_id` on raw query, ES drift,
   missing eager-load (N+1), unescaped Blade.
4. **Fix** minimally; match existing patterns; document the why.
5. **Prove** the failing test now passes; check no regression; run [VERIFY.md](VERIFY.md).
6. If it's one of the known issues, update [BUG_REPORT.md](BUG_REPORT.md) status.

---

## Recipe 5 — Optimize a slow query / screen
1. **Measure first**: log query count + time (Telescope/`DB::enableQueryLog()`); capture ES
   `took`. Record the before number.
2. **Diagnose**: N+1? non-sargable `WHERE DATE()`? missing index? reading MySQL where it
   should read ES? too-wide `select`?
3. **Fix in this order**: move read to ES/cache → eager-load → add/confirm index (verify
   live schema) → make predicates sargable → cache (Redis, event-invalidated).
4. **Re-measure**: state before/after. Don't claim "faster" without numbers.
Refs: [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md), [ENTERPRISE_PLAYBOOK.md](ENTERPRISE_PLAYBOOK.md).

---

## Recipe 6 — Add/modify an Elasticsearch index or field
1. Update the building service (`app/Services/Elastic*Index.php`) to emit the new field
   (pre-shaped for the UI).
2. Ensure the **Observer**/Job re-indexes on write; **never hardcode index names** — read
   from `config`/`.env` `ELASTIC_*` (and prefer **aliases**).
3. **Backfill** with the matching `app/Console/Commands/Index*ToElasticsearch.php`.
4. For mapping changes at scale: create new index → reindex → alias swap (zero-downtime),
   per [SCALABILITY.md](SCALABILITY.md).
5. Verify a doc landed: `curl -u elastic:*** http://<ES_HOST>:9200/<index>/_count`.

---

## Recipe 7 — Add a queued background job
1. `php artisan make:job <Name>`; document its purpose + which queue.
2. Make it **idempotent** (safe to retry); set `$tries`/`backoff`.
3. Dispatch onto the **right queue** (`indexing`/`uploads`/`mail`/…), not just `default`.
4. Heavy I/O (S3, mail, ES bulk, AI) belongs here — never inline in a request.
5. Test with `php artisan queue:work --once`; watch Horizon for failures.

---

## Recipe 8 — Add a new route to a persona
1. Open the right file (`service.php`/`product.php`/`superadmin.php`/`web.php`).
2. Place inside the matching group: `auth` → `prefix()->name()` → `EnsureOneDeviceLogin` →
   `custom-package`/`active-package`. Add RBAC (`role`/`permission`) if needed.
3. Name it `dot.case` (persona-prefixed). Add the controller method (thin).
4. `php artisan route:list --path=<x>` to confirm it resolves.

---

## Recipe 9 — Touch a payment / webhook flow ⚠️
1. Identify the gateway (Stripe/Razorpay/PhonePe/PayPal/Paddle) + its controller/trait.
2. **Verify webhook signature**; ensure **idempotency** (store processed event IDs);
   confirm the path is in `VerifyCsrfToken::$except`.
3. Keep `Order`/`CompanyPackage`/`licence_expire_on` updates consistent across gateways.
4. Never log secrets; never trust client-sent amounts. Get sign-off (money = business logic).
Refs: [SECURITY.md](SECURITY.md) SEC-05, [adr/0006](adr/0006-multi-gateway-payments.md).

---

## Recipe 10 — Cross-tenant marketplace feature ⚠️ (advanced)
**Pre-req:** tenant isolation hardened first ([BUG-03](BUG_REPORT.md)/[SEC-02](SECURITY.md)).
1. Use the **marketplace plane**, not per-tenant indices — separate, consent-gated ES
   indices and a visibility model. Do **not** apply the per-tenant `company_id` scope here;
   apply the consent/visibility rules instead.
2. Event-driven: listing/request/offer → Reverb push + audit log + (if settlement) coins
   ledger entry.
3. Progressive PII disclosure (anonymized → unlock). Audit who viewed what.
Refs: [MARKETPLACE_VISION.md](MARKETPLACE_VISION.md).

---

### When no recipe fits
Compose from Recipe 3 (vertical slice) + the relevant `.ai/tasks/*.md`. If you create a
genuinely new repeatable pattern, **add a recipe here** so the next agent reuses it.
