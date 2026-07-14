# Troubleshooting / FAQ — Common Errors & Fixes

> Symptom → likely cause → fix, for the traps specific to this app. Saves an AI agent (and
> you) from re-diagnosing the same issues. Add new ones as you hit them.

---

### Search / lists are empty, wrong, or stale
- **Cause:** Elasticsearch is the read store; the index is stale or the doc was never built.
- **Fix:** confirm the entity's Observer/Job re-indexes on write; re-run the matching
  `php artisan ...Index*ToElasticsearch` backfill; verify `curl -u elastic:*** http://<ES_HOST>:9200/<index>/_count`.
  Check the index name comes from `config`/`.env` `ELASTIC_*`, not a hardcoded string.

### ES host/keys, Zoom, or payments suddenly "null" / fail in production
- **Cause:** `env()` used outside `config/` returns **null** once `php artisan config:cache`
  runs (required for Octane). [BUG-02](BUG_REPORT.md).
- **Fix:** move the value into a `config/*.php` entry, read via `config()`; then
  `php artisan config:clear && php artisan config:cache`.

### A user sees another tenant's data
- **Cause:** missing `company_id` filter on a raw `DB::table()` query, or a model whose
  `company` scope is off (e.g. `JobApplication`). [SEC-02/BUG-03](SECURITY.md).
- **Fix:** add `->where('company_id', …)`; add a tenant-isolation test ([TESTING.md](TESTING.md)).

### White screen / debug dump in the middle of a flow
- **Cause:** a leftover `dd()`/`die()` in the code path (e.g. social login). [BUG-01](BUG_REPORT.md).
- **Fix:** `grep -rnE "^\s*(dd|die|var_dump)\(" app/`; remove; replace real error paths with
  `Log::error()` + a safe response.

### Config/route/view change "not taking effect"
- **Cause:** cached config/routes/views (common under Octane).
- **Fix:** `php artisan config:clear route:clear view:clear`; restart Octane
  (`php artisan octane:reload`). Remember to re-cache in prod.

### Route returns 404 though it "exists"
- **Cause:** placed outside the correct middleware group, wrong prefix/name, or route cache.
- **Fix:** `php artisan route:list --path=<x>`; ensure it's in the right persona group;
  `php artisan route:clear`.

### Stale data after editing under Octane / random cross-request weirdness
- **Cause:** request/tenant state stored in a static property or singleton (Octane keeps
  workers warm). [ADR 0003](adr/0003-octane-frankenphp.md).
- **Fix:** don't hold request data in statics; reset/avoid stateful singletons; test under Octane.

### Queue jobs not running / piling up
- **Cause:** Horizon not running, wrong queue connection, or one heavy job type starving
  the single `default` queue.
- **Fix:** `php artisan horizon` / check `horizon:status`; verify `QUEUE_CONNECTION=redis`;
  split queues (see [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md)); `queue:work --once` to test.

### Page slow / DB load spikes
- **Cause:** N+1, non-sargable `WHERE DATE()`, reading MySQL where ES should serve, missing index.
- **Fix:** follow [RECIPES.md](RECIPES.md) Recipe 5 (measure → diagnose → fix → re-measure).

### "Column not found" / migration confusion
- **Cause:** schema is in the **live DB**, not migrations; assumed a column that doesn't exist.
- **Fix:** `php artisan db:table <t>` to see real columns; **don't** run `migrate` against
  prod; confirm before coding. [ADR 0005](adr/0005-schema-in-live-db.md).

### Tests won't build the schema / `RefreshDatabase` fails
- **Cause:** no migrations for most tables. [TESTING.md](TESTING.md) §5.
- **Fix:** `php artisan schema:dump` against a dev DB; use `.env.testing` + a test DB.

### DataTables grid shows nothing / JS error
- **Cause:** the `*_ajax` data route returns wrong shape or errors; missing the page/ajax pair.
- **Fix:** ensure JSON `{data, recordsTotal, recordsFiltered}`; check the `*_ajax` route
  exists and is reachable; look at the network response.

### Mail/WhatsApp not sending
- **Cause:** SMTP settings null (env→config issue), job not queued, or external WhatsApp
  service down.
- **Fix:** verify `smtp_settings`/config; check the queued mail job ran (Horizon); for
  WhatsApp, the external Node service must be up (`/whatsapp/status`).

### Vite/asset build fails or styles missing
- **Fix:** `npm install` then `npm run dev` (local) / `npm run build` (prod); confirm
  Metronic assets are referenced; check `vite.config`/manifest.

---
> Pattern: when something "doesn't show," suspect **ES drift** or **env/cache** before
> assuming a logic bug. When data "leaks," suspect a **raw query without `company_id`**.
