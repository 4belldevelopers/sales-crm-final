# Perf-10K Plan — make every Add / Edit / Submit fast, low-load, ready for 10K concurrent

> **Date:** 2026-08-16
> **Scope (strictly enforced):** in-scope personas only — Service Co, Super Admin, Admin, Front/SaasFront/Auth. **Product Co and Staffing Co are dormant — not in this plan.** Scope rule: [PERSONA_SCOPE.md](../PERSONA_SCOPE.md).
> **Plan source of truth:** this doc; for the per-controller method/line targets, see the audit at [V2_Add-Edit-Submit/Add-Edit-Submit-Audit.md](../V2_Add-Edit-Submit/Add-Edit-Submit-Audit.md) (which routes/entities this plan targets).
> **What this plan optimizes:** every **Add / Edit / Submit** endpoint — write-path latency, read-path latency, client-side response, query count, server load — and the infra to make it sustainable at **10,000 concurrent users on 3 separate 32 GB VMs** (app, MySQL, ES).

---

## 1. How to read this document

1. **§ 2 — Context & target SLOs.** What "success" means in numbers.
2. **§ 3 — Server-spec mapping.** 3 × 32 GB VMs: app, MySQL, ES — exact tuning.
3. **§ 4 — The four buckets** (write path, read path, screen-by-screen, infra & observability).
4. **§ 5 — Per-Action query budgets.** "Reduce the queries" made concrete per action.
5. **§ 6 — Client-side patterns.** Optimistic, debounced, virtualized.
6. **§ 7 — Phasing.** 8 sequenced phases, ~38 dev-days total.
7. **§ 8 — Critical files.** Where each pattern lands.
8. **§ 9 — Verification.** How to confirm 10K-concurrent readiness.
9. **§ 10 — Risks & non-goals.**

Supporting artifacts:

- [`perf-budget.json`](perf-budget.json) — machine-readable SLO targets per route.
- [`../../tests/load/k6/perf-10k.js`](../../tests/load/k6/perf-10k.js) — load harness (top 30 endpoints, ramp-up to 10K RPS).
- [SCALABILITY.md](../SCALABILITY.md) — long-horizon capacity roadmap; this plan is the **near-term** slice.
- [PERFORMANCE_BUDGET.md](../PERFORMANCE_BUDGET.md) — proposed targets (looser); this plan **tightens** them.

---

## 2. Context & target SLOs

### 2.1 Why this plan exists

The user wants every Add / Edit / Submit endpoint to be:
- **Fast client-side response** (UI feels instant — optimistic, debounced, no full reload).
- **Minimum / zero server load per click** (most work moves client-side or to the queue).
- **Minimum DB / ES queries per click.**
- **Ready for 10,000 concurrent users** on **3 separate 32 GB VMs** (app, MySQL, ES).

### 2.2 Source material

- **Verified (read this turn):** `docs/PERFORMANCE_BUDGET.md`, `docs/SCALABILITY.md`, `docs/ARCHITECTURE.md`, `docs/BAD_PATTERNS.md`, `docs/V2_Add-Edit-Submit/Add-Edit-Submit-Audit.md`, `docs/V2_Add-Edit-Submit/evidence/_Add-Edit-Submit-Audit-evidence.md`.
- **Not verified (3 explore agents failed with HTTP 429 — daily plan limit, resets IST midnight):** per-controller line-level code reads. Where this plan names a specific method/line, **confirm by reading the source before coding**.

### 2.3 Target SLOs

These are **tighter** than the proposed targets in `docs/PERFORMANCE_BUDGET.md` — the user wants "min or no server load," so the read path moves to ES / cache / edge, and the write path returns before side effects complete.

| Concern | Target p95 | Notes |
|---|---|---|
| Interactive page TTFB | **< 200 ms** | Blade render under Octane; hot path is `cached view + cached user-context` |
| Add / Edit / Submit POST roundtrip (user-perceived) | **< 100 ms** | DB write returns, response sent; ES + mail + Reverb fire-and-forget |
| DataTables AJAX list endpoint | **< 250 ms** | server-side paginated, ES-only, 20 rows/page, no `select *` |
| Elasticsearch query (list/search) | **< 100 ms** | ES round-trip + slim `_source` filter |
| MySQL single-row point lookup | **< 5 ms** | covered by composite index `(company_id, status, …)` |
| Bulk import / mass mail | minutes (background) | never blocks a request |
| Webhook latency (ack only) | **< 50 ms** | verify is queued; ack is immediate |
| Server load at 10K concurrent | **< 60%** CPU sustained across all 3 VMs | Octane `maxRequests` + watchdog |
| Cache hit rate (read endpoints) | **> 90%** | CDN for public + Redis for tenant config + ES request cache |
| **% requests that NEVER touch MySQL** | **> 90%** | ES / cache / edge serves the read |

---

## 3. Server-spec mapping (3 × 32 GB VMs)

| VM | RAM budget | Key tuning |
|---|---|---|
| **App (FrankenPHP + Octane + Reverb)** | 32 GB | Octane workers = `nproc - 1` (likely 7–15); per-worker memory cap 2 GB → `maxRequests: 5000` then recycle. Composer preload enabled. `config:cache` + `route:cache` + `view:cache` ON. |
| **MySQL** | 32 GB | InnoDB **buffer pool = 24 GB** (75% of RAM). `max_connections = 2000` but ProxySQL pools → effective ~250 server-side. `innodb_log_file_size = 4 GB`. Slow log > 50 ms. `performance_schema` ON. |
| **Elasticsearch** | 32 GB | JVM heap **= 16 GB** (≤ 50% of RAM, the rest for OS file cache). `bootstrap.memory_lock: true`. Shard size 20–50 GB. ILM with hot→warm→cold. |

**Plus (separate or co-located small hosts):**

- **Redis** for cache + session + Horizon (8 GB host — sessions must NOT share eviction with cache). Use Redis Cluster or Sentinel.
- **CDN** (CloudFront / Cloudflare) in front of public surface.

**Why split Redis:** cache eviction storm under load will not kill sessions. See [SCALABILITY §5](../SCALABILITY.md).

---

## 4. The four buckets — every fix lands in exactly one

### Bucket A — Write-path (the POST / DELETE itself)

**Goal:** the controller writes MySQL, enqueues ES + mail + Reverb, returns ≤ 100 ms.

| Fix | Where | Impact |
|---|---|---|
| **A1. Decouple ES from the request.** Move every ES write from the controller body to a queued `Elastic*Index` job (already exists in `app/Jobs/`). | All controllers — `ServiceCompanyJobsController`, `ServiceCompanyJobApplicationController`, `SuperAdminCompanyController` | Removes 1 sync HTTP call per write (50–200 ms saved). |
| **A2. Decouple mail from the request.** `Mail::send()` is already queued via `app/Jobs/Queue*MailService.php` — verify and remove any inline `Mail::raw()` calls. | Service + SuperAdmin mail paths | 200–800 ms saved per mail. |
| **A3. Decouple Reverb broadcasts.** `event(new ...)` is fine for local, but at 10K concurrent, batched / queued broadcasts are cheaper. Use Reverb's queue driver for outbound events. | All `ShouldBroadcast` events | Reduces request blocking on websocket fan-out. |
| **A4. Single-transaction writes.** `DB::transaction(function() { ... })` for any endpoint that touches > 1 table. | All multi-table writes (`job-applications/store-schedule`, `save-create-offer`, `apptracker/store`, etc.) | Eliminates partial-write drift; halves DB round-trips via the single TX. |
| **A5. Bulk-insert with `insert()` / `upsert()`** instead of per-row `::create()` loops. | `apptracker/store`, `talent-add`, `importJobCondidate` | 50–200 ms saved per bulk. |
| **A6. Split the single `default` Horizon queue into 5.** `indexing`, `mail`, `uploads`, `webhooks`, `default`. Update `config/horizon.php` with per-queue supervisors and `tries`, `timeout`, `backoff` per queue. | `config/horizon.php` | Stops heavy jobs (resume parse, ES bulk, mass mail) from starving interactive index jobs. |
| **A7. Webhook pattern: ack < 50 ms, verify async.** Add `webhook.verify` middleware that enqueues the verify + write. | `PaypalIPNController`, `StripeWebhookController`, `RazorpayWebhookController`, `PhonePePaymentController` | Removes sync external HTTP from the request. |
| **A8. Idempotency-key middleware on webhooks.** Cache the key for 24h; if seen, return 200 with the cached result. | Same | No duplicate invoice writes on gateway retry. |
| **A9. Webhook signature verify BEFORE DB write.** Use `WebhookSignature` middleware; reject on mismatch with 401. | All 4 webhook controllers | Stops unauth POSTs from writing. |
| **A10. Cache `smtp_settings` per tenant.** ~7 reads per mail send (`BAD_PATTERNS.md` 🟨). Move to `Cache::remember('smtp_settings:'.$companyId, …)`. | All mail services | Saves 6+ DB hits per send. |
| **A11. Role / permission cache.** spatie/laravel-permission re-reads `roles`, `permissions`, `role_user` on every request when using `auth()->user()->can(...)`. Wrap in a per-request memo. | `ServiceCompanyRolesPermissionController`, `ManageRolePermissionController` | -2 DB hits per request per authenticated call. |
| **A12. De-duplicate role / permission controllers** (parallel stack — audit §5.C). | `routes/service.php:502-511` vs `:547-567` | Maintenance win; perf win because one Observer-driven path replaces two bypassing paths. |

### Bucket B — Read-path (the GETs that render the page)

**Goal:** **90% of read requests never touch MySQL.** ES + Redis + edge handle them.

| Fix | Where | Impact |
|---|---|---|
| **B1. Slim ES `_source`** to only columns the table renders. Drop 50+ `select *` columns from `ElasticCandidatesIndex` and friends. Audit `app/Services/Elastic*Index.php`. | All ES index services | 30–60% ES bandwidth drop, faster serialization. |
| **B2. ES request cache.** For repeat filter sets in the same tenant within N seconds, cache the full response in Redis (`tags: ['tenant:'+id, 'filter_hash']`). | `app/Services/ElasticService.php` | Cache hit returns < 5 ms. |
| **B3. Tag-based invalidation.** When a write happens, invalidate the cache tags for that tenant. | New `CacheInvalidateOnWrite` listener or Observer hook | Cache stays fresh. |
| **B4. Composite indexes** on the hot read tables: `(company_id, status, updated_at)`, `(company_id, master_status_id, deleted_at)`, `(company_id, job_id, stage_id)`. | `job_applications`, `add_job_wise_condidates`, `interview_schedules`, `bench_applications` | 5–50 ms query → < 5 ms. |
| **B5. `select()` discipline** in every hot DataTables controller — column-restricted, no `select *`. | All `*_ajax` controllers | Network + ES round-trip cut. |
| **B6. `chunkById` / `cursor` for any > 10K-row export.** Never load full result set. | `applicationoffer/export`, `cvtracker/export`, `apptracker/export` | Memory flat, query stable. |
| **B7. Eager-load all belongsTo / hasMany used in the view.** Use `Model::preventLazyLoading(! app()->isProduction)` to N+1-detect in dev. | All list controllers | -1 query per relation per row. |
| **B8. Move `Jobs::COUNT(...)` dashboard queries to a precomputed `dashboard_metrics` Redis hash**, refreshed every 60 s by a scheduled command. | `ServiceCompanyDashboardController`, `SuperAdmin*` dashboards | Dashboard goes from 30 queries → 1 Redis GET. |
| **B9. View caching for static sections** (`@cache('key', 3600)` for footer / sidebar / global settings per tenant). | `resources/views/service_company/layouts/*.blade.php` | Saves repeated Blade compiles under Octane. |
| **B10. CDN for public surface.** Cloudflare in front of `saas-front/*`, `front/*`, `career-page`, static assets. SWR (stale-while-revalidate) for 60 s on `GET /jobs/{id}`. | Public pages | Cuts app-tier load for unauth reads. |

### Bucket C — Screen-by-screen (the 9 heaviest user surfaces)

Per audit §1b + read-path bottlenecks agent (inferred — confirm by reading each view file):

| Screen | Top 3 fixes | Estimated p95 improvement |
|---|---|---|
| **Service Apptracker** (the hot test of all) | (1) Convert to fetch-on-mount + virtualized rows (Alpine + `IntersectionObserver`). (2) Server returns slim JSON (B1 + B5). (3) Pre-computed filter facets in Redis (B8). | 1.2 s → **< 300 ms** |
| **Service CV-Tracker / candidates list** | (1) Same fetch-on-mount pattern. (2) Local filter cache (last 50 filter combos in `localStorage`). (3) Slim ES `_source` (B1). | 1.0 s → **< 250 ms** |
| **Service Bench candidates list** | (1) Virtualize. (2) ES count-only facet query first, then rows on demand. (3) Tag-based cache (B3). | 900 ms → **< 200 ms** |
| **Service Job edit** | (1) Form prefilled via single ES GET on `job_id`. (2) Auto-save draft to `localStorage` every keystroke (debounced 1 s). (3) Submit POST returns 100 ms; full ES re-index is queued. | Page load 800 ms → **< 200 ms**; Save feels **instant** |
| **Service Job-Application edit (the hottest screen)** | (1) Split into tabs (Overview / Stage / Offer / Resume / Interview); each tab lazy-loads via ES GET. (2) Optimistic stage move (A3 + Reverb later confirms). (3) Submit POST acks < 100 ms; bulk side effects queued. | Page load 1.5 s → **< 400 ms**; Stage move feels **instant** |
| **Super Admin Company list** | (1) DataTables server-side mode (verify it's on). (2) Slim `_source` (B1). (3) Covering index on `(status, created_at)`. | 1.1 s → **< 200 ms** |
| **Super Admin Company edit** | (1) Single ES GET to load. (2) Cache `company_settings` per company in Redis (10 min). (3) Invalidate on write (B3). | 1.0 s → **< 200 ms** |
| **Super Admin Coupons list** | (1) Server-side paginated, slim. (2) Add/edit uses the same `add-edit` endpoint — no separate edit page. | 500 ms → **< 150 ms** |
| **Auth login** | (1) Cache user + role + permissions in session-on-login (single bulk fetch). (2) `env()`→`config()` (BUG-02) so Octane `config:cache` holds. (3) Move password-reset / OTP send to a queued job. | 600 ms → **< 150 ms** |

### Bucket D — Infra & observability

| Fix | Why |
|---|---|
| **D1. ProxySQL in front of MySQL** (or RDS Proxy). | Octane workers open persistent connections; without pooling, `max_connections` (151 default) is exhausted at 10K. |
| **D2. Read replicas.** Writes → primary, reads (DataTables, dashboard) → replica. | Halves primary load. |
| **D3. ES ILM + aliases.** Indices behind aliases (`ats_jobs_read`, `ats_candidates_read`); ILM rolls over by size. | 60+ indices → manageable. |
| **D4. ES `_bulk` for backfills + reconciliation.** Existing artisan commands should batch in 1–5 K batches, not per row. | Backfill time -90%. |
| **D5. Reconciliation command** (`app/Console/Commands/Index*ToElasticsearch`) — schedule hourly; compare MySQL count to ES count per index, enqueue drift jobs. | Drift → 0. |
| **D6. Horizon supervisors per queue.** `indexing` (8 workers, 300 s timeout), `mail` (4 workers, 600 s), `uploads` (4 workers, 1800 s), `webhooks` (4 workers, 60 s), `default` (8 workers). | Throughput 3–5× vs single queue. |
| **D7. APM** (Telescope dev, OpenTelemetry / Sentry prod). Tag every request with `route`, `tenant_id`, `user_id`. Slow query log > 50 ms. | Can't tune what you can't see. |
| **D8. SLO dashboards.** p50 / p95 / p99 per route in Grafana. Alert on p95 > target for 5 min, on Horizon queue depth > 1 K, on ES drift > 0.5%. | Catch regressions before users do. |
| **D9. Load test harness.** k6 scripts in `tests/load/` exercising the top 30 endpoints at 10 K RPS. Gate releases on green. | Confidence at each growth milestone. |
| **D10. Circuit breakers** on every external call (`Http::timeout(3)->retry(2, 100, …)`). | Third-party slowness does not cascade. |
| **D11. CDN edge + `Cache-Control: public, max-age=60, stale-while-revalidate=600`** on public pages. | 80%+ of public traffic never reaches origin. |
| **D12. Rate-limit per-tenant writes** (`RateLimiter::for('tenant-writes', fn($r) => Limit::perMinute(500)->by($r->user()->company_id))`). | One noisy tenant cannot DoS the platform. |

---

## 5. Per-Action query budget (the "reduce the queries" rule, made concrete)

| Action | Current query count (estimated — verify) | Target | How |
|---|---|---|---|
| **Open Apptracker page (initial)** | 30–60 (Eloquent + raw) | **3** | 1 ES list + 1 ES count + 1 Redis GET for facets. |
| **Apply Apptracker filter** | 5–15 | **1** | 1 ES search. |
| **Move stage (stage_submit)** | 4–8 + ES sync + Reverb | **1 write** | 1 `UPDATE job_applications`; ES, Reverb, mail → queue. |
| **Save job** | 6–10 + ES sync | **1 transaction** | `DB::transaction { insert/update jobs, insert/update service_on_jobs }`; ES + side effects → queue. |
| **Bulk save (apptracker.store)** | N creates + ES sync | **1 bulk insert + 1 bulk ES** | `Model::insert([...])` + queued `_bulk`. |
| **Add team member** | 4–8 + mail | **1 transaction** | Insert `user` + `role_user` + `company_user` in TX; mail → queue. |
| **Login** | 5–10 (auth + roles + permissions + company) | **1** | Bulk fetch + session cache; re-fetch only on role change. |
| **Register (company-register)** | ~70 inserts + ~30 ES syncs + 3 external HTTP | **1 ES verify, 1 mail queue** | Move ALL inserts + ES to a single queued job after the user-facing ack. |

---

## 6. Client-side patterns (medium aggressiveness)

| Pattern | Where |
|---|---|
| **Optimistic UI on stage moves** (UI updates immediately, server confirms via Reverb). | Stage Submit, Status Update, Recruiter reassign. |
| **Debounced auto-save draft** on long forms (1 s debounce, write to `localStorage`). | Job edit, Job-Application edit, Offer letter. |
| **Fetch-on-mount for tabs** (only the active tab loads; tabs lazy-load when clicked). | Job-Application edit (Overview / Stage / Offer / Resume / Interview tabs). |
| **Virtualized lists** for > 200-row DataTables (Alpine + `IntersectionObserver` windowing). | Apptracker, CV-Tracker, Bench list, Super Admin Company list. |
| **Local filter cache** (last 50 filter combos in `localStorage` per user). | All list screens. |
| **`requestAnimationFrame` for dropdowns / autocomplete** (do not re-render on every keystroke). | All client-job pickers, recruiter pickers, contact pickers. |
| **Skeleton + `content-visibility: auto`** on off-screen rows. | All list screens. |
| **Form partial-render on submit error** (do not re-render the whole form on validation fail — return only the failing field errors). | All Add / Edit forms. |

---

## 7. Phasing (sequenced, dependency-ordered)

**Combined estimate: ~38 dev-days** (audit's 31 + ~7 for the new client-side + infra additions).

### Phase 1 — Foundation (3 days) [no business-logic change]
- A4 single-transaction writes.
- A6 split queues.
- A11 role / permission cache.
- D1 ProxySQL + D2 read replica.
- D7 APM + D8 SLO dashboards (gates every later phase).

### Phase 2 — Decouple ES + mail + Reverb from requests (5 days)
- A1, A2, A3 across the top 20 write endpoints.
- A5 bulk-insert.
- A10 `smtp_settings` cache.
- D5 reconciliation command.

### Phase 3 — Read path (cache + index + ES _source) (6 days)
- B1, B5, B7 across all `*_ajax` + dashboard controllers.
- B2, B3 ES request cache with tag-based invalidation.
- B4 composite indexes.
- B8 dashboard precomputed metrics.
- B9 view caching for static sections.

### Phase 4 — Webhook hardening (2 days)
- A7, A8, A9 across all 4 webhook controllers.
- D10 circuit breakers.

### Phase 5 — Per-screen client-side rewrite (8 days) [medium UI]
- Apptracker, CV-Tracker, Bench list — fetch-on-mount + virtualize + local filter cache.
- Job edit + Job-Application edit — optimistic + auto-save + lazy tabs.
- Auth login — bulk fetch on login.

### Phase 6 — Slice refactors (10 days, parallel with above)
- Slice `ServiceCompanyJobsController` + `ServiceCompanyJobApplicationController` + `SuperAdminCompanyController` (the audit's 7-phase plan).
- A12 role / permission dedup.
- Move ES indexing logic out of controllers into `Elastic*Index` Action classes (the slice pattern).

### Phase 7 — Public surface + infra polish (4 days)
- B6 export `chunkById` migration.
- B10 CDN + cache headers.
- D3 ES ILM + aliases (consolidate the 60+ indices).
- D11 CDN.
- D12 per-tenant rate limit.

### Phase 8 — Verify (3 days, runs in parallel with each phase gate)
- D9 k6 load harness ([`tests/load/k6/perf-10k.js`](../../tests/load/k6/perf-10k.js)).
- Synthetic monitoring (Datadog / Prometheus) for SLOs.
- Run the 12-objective compliance check (memory `12-objective-compliance-review-aug-2026.md`).

---

## 8. Critical files to modify (representative — pattern repeats)

**Write path:**
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` (slice refactor + A1 / A4)
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (slice refactor + A1 / A3 / A4)
- `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` (A5 bulk insert)
- `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` (A1 / A5)
- `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` (A1 / A4)
- `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` (slice refactor + A1 / A4)
- `app/Http/Controllers/SuperAdmin/ManageRolePermissionController.php` (A11)
- `app/Http/Controllers/ServiceCompany/ServiceCompanyRolesPermissionController.php` (A12 merge)
- `app/Http/Controllers/Webhooks/{PaypalIPNController,StripeWebhookController,RazorpayWebhookController,PhonePePaymentController}.php` (A7 / A8 / A9)
- `app/Services/QueueMail*.php`, `app/Services/SmtpService.php` (A2 / A10)
- `app/Observers/JobApplicationObserver.php`, `app/Observers/JobObserver.php`, etc. (verify they queue, not sync)

**Read path:**
- `app/Services/ElasticService.php`, `app/Services/Elastic*Index.php` (B1 / B2 / B3 — slim `_source`, request cache, tag invalidation)
- All `*_ajax` controllers in `ServiceCompany/` and `SuperAdmin/` (B5 select discipline, B7 eager loading)
- `app/Models/JobApplication.php`, `app/Models/Job.php`, `app/Models/InterviewSchedule.php`, `app/Models/BenchApplication.php` (B4 composite index hints — see `database/schema/mysql-schema.sql` for current state; rule 2 says schema is not in migrations)

**Infra:**
- `config/horizon.php` (A6 split queues)
- `config/database.php` (D2 read replica)
- `config/cache.php`, `config/elasticsearch.php` (B3 tag invalidation hooks)
- `config/queue.php` (new queue definitions)
- `database/schema/mysql-schema.sql` (B4 add composite indexes — schema lives here, not migrations, per CLAUDE.md rule 2)
- `routes/web.php` debug route gating (audit §5.D)
- `bootstrap/app.php` (custom webhook middleware registration)

**Frontend:**
- `resources/views/service_company/apptracker/*.blade.php` (Phase 5 fetch-on-mount)
- `resources/views/service_company/cvtracker/*.blade.php` (Phase 5)
- `resources/views/service_company/bench-candidates/*.blade.php` (Phase 5)
- `resources/views/service_company/job-applications/edit.blade.php` (Phase 5 lazy tabs + auto-save)
- `resources/views/auth/login.blade.php` (Phase 5)
- `resources/js/*` (Alpine + `IntersectionObserver` patterns — add a `useVirtualList` composable)

---

## 9. Verification (how to confirm 10K-concurrent readiness)

1. **Per-route p95** < target ([`perf-budget.json`](perf-budget.json) + D8 dashboard). Top 30 routes must hit the SLOs in §2.3.
2. **Apptracker page TTFB** < 300 ms p95 on k6 1 K RPS sustained.
3. **Horizon queue depth** < 100 for `indexing` and `default` under 10 K RPS; `mail` < 1 K; `webhooks` < 50.
4. **ES drift** = 0 (D5 reconciliation command).
5. **MySQL slow log** = 0 queries > 50 ms on hot tables.
6. **Cache hit rate** > 90% on read endpoints (B2).
7. **% requests that touch MySQL** < 10% (per the SCALABILITY §4 target).
8. **Server CPU** < 60% sustained across all 3 VMs at 10 K concurrent.
9. **Tenant-isolation regression test** still passes (BUG-03 — `JobApplication` company scope).
10. **Load test script** in `tests/load/k6/perf-10k.js` reproducible; CI runs it on every release candidate.

---

## 10. Risks & non-goals

- **Risk: ES becomes the bottleneck** at 10K reads/sec. Mitigation: ILM + replicas + read-only cache. If still hot → add a 4th VM or move to OpenSearch managed.
- **Risk: Octane worker memory leak** at long uptime. Mitigation: `maxRequests: 5000` per worker + watchdog.
- **Risk: Webhook verify async could miss a payment.** Mitigation: ack only after enqueue; Horizon's at-least-once + idempotency-key dedup (A8).
- **Risk: Slice refactor changes controller surface** — could regress the audit's existing UI. Mitigation: feature-flag the new Action class behind a per-tenant toggle; run in shadow for 1 week before cutover.
- **Risk: ES slim `_source` breaks a downstream view** that expects a column. Mitigation: feature-flag per index; ship as additive first.
- **Non-goal: no SPA migration.** No Next.js / Inertia / Livewire rewrite — only Alpine + small composables for the top 3 screens. (Per user's "medium UI aggressiveness" choice.)
- **Non-goal: no schema redesign.** Schema stays in `database/schema/mysql-schema.sql` (CLAUDE.md rule 2). Indexes are added, columns are not renamed.
- **Out of scope:** Product Co + Staffing Co controllers and routes are dormant — not in this plan. Same scope rule as the audit ([PERSONA_SCOPE.md](../PERSONA_SCOPE.md)).
