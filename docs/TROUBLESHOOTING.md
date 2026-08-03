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

### Client-side PDF export button does nothing — no PDF, no alert, no console error visible
- **Cause:** the `<script>` wrapping the helpers IIFE-never-closed, so `window.X = fn`
  lives **outside** the closed IIFE or the IIFE never resolves to a global. The
  inline `onclick="X(this)"` then fires against an undefined `X` and Chrome swallows
  the ReferenceError silently. No "Generating PDF…" appears, no download starts, no
  `alert()` from the catch handler runs.
- **Where it bit us:** `resources/views/service_company/job-applications/showdetail.blade.php`
  PDF block (~line 6144–6563) — the (function(){…})() never closed before
  `window.cdrExportProfilePDF = …` was declared, so `cdrExportProfilePDF` was never
  assigned. Symptom: clicking "Export PDF" silently does nothing.
- **Fix:** put the `})();` **after** the closing `};` of `cdrExportProfilePDF`, so the
  IIFE wraps both helpers and the global assignment. Then the function has closure
  access to `ensurePdfMake` / `buildDef`.

### Other PDF blob never comes back, even when the loader is fine
- **Cause:** `pdfMake.createPdf(heavyDef).getBlob(cb)` may silently never fire `cb`
  for malformed `def` entries (e.g. bad margin arrays, undefined in `widths:['*','*']`)
  or an `svg:` element with unsupported text. The 10s timer in the catch wrapper will
  eventually surface it as `PDF timed out after 10s` — that is the **first** signal
  the `def` (i.e. `buildDef`) is wrong, not the loader.
- **Fix:** on failure, `console.log(JSON.stringify(def))` from inside `cdrExportProfilePDF`
  before the `createPdf` call, paste into a 6-line test page with the same libs, and
  isolate which `content` block fails.

### Fonts: Roboto > Poppins for pdfkit-based exports
- **Cause:** pdfkit's vfs_fonts ships Roboto (Latin coverage of standard text); loading
  Poppins TTF and registering it via `pdfMake.fonts` silently aborts the `getBlob`
  callback if Poppins lacks a glyph for a unicode codepoint present in the doc
  (e.g. `•`, `✓`, the small left-single-quote thing Metronic uses, em-dashes copied
  from rich-text). The error doesn't reach `console.error` — `getBlob(cb)` just never
  fires.
- **Fix:** use **Roboto via vfs_fonts** (already shipped at `public/js/vfs_fonts.min.js`,
  ~780 KB, commit `0d6415c5`). Strip non-Latin glyphs (`•`, `✓`, emoji) from text content
  before they reach pdfMake. Do **not** commit a parallel `public/fonts/poppins/*.ttf`
  bundle — it bloats the repo and was the original root cause of the saga.

### Loader: CSP blocks CDN pdfmake but serve-from-self works
- **Cause:** `script-src 'self'` (per app CSP) blocks `<script src=https://cdn.jsdelivr.net/...>`,
  even when pdfmake would otherwise load. Symptom: `tryList` hangs on the first CDN URL,
  then jobs through fallbacks that all fail with CSP, eventually throwing "All sources
  failed" after ~8s.
- **Fix:** `fetch -> Blob URL -> <script src=blob:...>` trick. The blob URL inherits
  page origin so CSP `script-src 'self'` allows it. Ship `pdfmake.min.js` and
  `vfs_fonts.min.js` same-origin in `public/js/`, fetch them first, fall through to
  CDN if the local file 404s.

---
> Pattern: when something "doesn't show," suspect **ES drift** or **env/cache** before
> assuming a logic bug. When data "leaks," suspect a **raw query without `company_id`**.
