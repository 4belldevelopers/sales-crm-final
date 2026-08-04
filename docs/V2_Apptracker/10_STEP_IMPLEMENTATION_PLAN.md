# Apptracker 10-Step Implementation Plan

> Target: `service_company/apptracker` to serve **10,000 recruiters** on 32 GB RAM (Laravel + MySQL + Elasticsearch + Redis/Horizon + Octane) — **fastest response, minimal SQL, near real-time, client-side dominant, perfect counters, zero business-logic changes**.

## What's already in place (leverage it)

| Asset | Location |
|---|---|
| `JobApplicationObserver` already fires `UpdateApplicationCountersJob`, `UpdatePipelineCountersJob`, + decrements on `counters` queue | `app/Observers/JobApplicationObserver.php` |
| `pipeline_counters` table (per-job stage counts) | `database/schema/mysql-schema.sql` |
| `application_counters`, `job_counters`, `job_agency_counter` summary tables | `database/schema/mysql-schema.sql` |
| Redis hashes: `pipeline:{jobId}`, `app_counters:{jobId}`, `job_counters:{companyId}` | `app/Jobs/UpdatePipelineCountersJob.php` etc. |
| `BulkCounterRefresherJob` does full reconcile on demand | `app/Jobs/BulkCounterRefresherJob.php` |
| `Cache::flexible("custome_count_all:{cid}:{id}", [60, 300], …)` already wraps the header counter | `app/Helper/GeneralMethod.php:11689` |
| Per-tab payload cache (15 s TTL via constants like `ALL_APPTRACKER_TIME`) | controller line 897 etc. |
| ES constants auto-`define()`d by `AppServiceProvider::boot()` | `app/Providers/AppServiceProvider.php:21-25` |
| Horizon + Reverb already installed | `config/horizon.php`, `config/reverb.php` |
| `prepareRowMaps` (kills 4 N+1s) — only currently called by `allDataAjax` | `ServiceCompanyAppTrackerController.php:279-338` |
| Datatables server-side pagination already in place | confirmed in blades |
| Print selected rows already pure-DOM (`#aptk_print_area`) | `index.blade.php` |
| All candidate lists are paginated, in-page live filters | confirmed |

> **The "real-time counter pipeline" already exists on the server side.** What's missing is: (a) the apptracker `loadcounter` endpoint does NOT consume `pipeline_counters` — it hits ES fresh every call, (b) no Reverb broadcast is wired so the browser can subscribe, (c) `prepareRowMaps` is only used by one tab.

## The 10 steps

Each step is **independent** (rollback a single step by reverting its files). Each step has a measurable success metric. **No business logic changes** anywhere — only data-source swaps, caching, render batching, and client-side UX moves.

---

### Step 1 — Database indexes (MySQL hot path)
**Files:** `database/schema/mysql-schema.sql` (annotation only — schema lives in live DB) + a one-time migration runner.

**SQL:**
```sql
ALTER TABLE add_job_wise_condidates
  ADD INDEX idx_awjc_job_status      (job_id, master_status_id),
  ADD INDEX idx_awjc_company_job     (company_id, job_id),
  ADD INDEX idx_awjc_thirdparty_job  (thirdparty_company_id, job_id),
  ADD INDEX idx_awjc_app             (job_application_id);
ALTER TABLE clients               ADD INDEX idx_clients_company (company_id);
ALTER TABLE tickets               ADD INDEX idx_tickets_company (company_id);
ALTER TABLE interview_schedules   ADD INDEX idx_isched_company  (company_id);
ALTER TABLE job_applications      ADD INDEX idx_japp_status     (job_id, master_status_id);
ALTER TABLE job_applications      ADD INDEX idx_japp_email      (company_id, email(40));
ALTER TABLE job_applications      ADD INDEX idx_japp_phone      (company_id, phone(20));
```
**Run:** via the existing `php artisan` + DB console. Not a Laravel migration (per CLAUDE.md rule #2).

**What it changes:** nothing. Pure DDL on the table that powers `custome_count_all` and `loadcounter`.

**Server→client load change:** zero on the app server; MySQL writes drop from full scans to index seeks (~50× faster on 100 M rows).

**Success metric:** `EXPLAIN` on the `add_job_wise_condidates` GROUP BY query goes from `type=ALL, rows=63K` to `type=ref, rows=<100`. `idx_japp_status` makes the per-job/per-stage breakdown indexed.

**Rollback:** `DROP INDEX …`.

**Why first:** single biggest win, zero risk, applied in production during off-hours.

---

### Step 2 — Cache the static reference data
**Files:**
- `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` (lines 95–125 in `index`, lines 988 / 4965 / 6045 / 7065 / 8178 / 9360 / 10628 across page-renders, line 355 `interviewStatus`, line 10573 `TicketStatus::all()`)
- `app/Helper/GeneralMethod.php:534` (`feedback_status`)
- A new `app/Services/ReferenceDataCache.php` (one tiny helper class with `get($key, $ttl, fn)`)

**What it changes:** wrap each `Model::all()` and the `qualification_master SELECT *` and `feedback_status()` in `Cache::remember('ref:master_status', 86400, fn() => …)`. Nothing visible changes. The reference lists ship from memory on subsequent renders.

**Server→client load change:** page-render SQL drops from ~7 queries to 1 (the `Job::findOrFail`); everything else is memory.

**Success metric:** `index()` GET produces 1 SQL query on hot path (verified via `DB::enableQueryLog()`).

**Rollback:** remove the `Cache::remember` wrappers.

---

### Step 3 — De-duplicate per-row tag lookups (kill the N+1 across the 8 tabs)
**Files:**
- `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` — add a `protected function resolveTagifyFilters(array $custom_location, array $custom_education, int $company_id): array` method.
- Call it at the top of each `_data_ajax` (lines ~1161/1184 similar, 4224/4228 candidates, 5098/5112 internal_bench, 6178/6192 career_apply, 7202/7216 public_bench, 8351/8373 dsimilar, 9546/9556 dinternal_bench, 10712/10723 findTalent).

**What it changes:** a single batch lookup per request:
```php
$cities = collect();
if (!empty($custom_location)) {
    $cities = DB::table('city_list')->whereIn('city', $custom_location)->pluck('city_id', 'city');
}
$quals = collect();
if (!empty($custom_education)) {
    $quals = DB::table('qualification_master')->whereIn('name', $custom_education)->pluck('id', 'name');
}
return ['cities' => $cities, 'qualifications' => $quals, 'location_ids' => $cities->values(), 'qual_ids' => $quals->values()];
```
Wrap each in `Cache::remember('ref:city_lookup:'.md5(implode(',', $custom_location)), 300, fn() => …)` keyed by the input set. 8 tabs go from N+M queries each to 2 total per request.

**Server→client load change:** MySQL drops from 8–80 queries per filter chip to 2. ES load unchanged.

**Success metric:** the COUNT(`city_list`) and COUNT(`qualification_master`) hits in `tail storage general_log` drop to 0 during normal traffic.

**Rollback:** revert the foreach+DB bodies to inline lookups.

---

### Step 4 — `loadcounter` reads from `pipeline_counters` + Redis (no ES hot path)
**Files:**
- `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php:155-273` (full `loadcounter` rewrite)
- `app/Jobs/UpdatePipelineCountersJob.php:34-43` (already does `Redis::hincrby("pipeline:{jobId}", $stage, 1)` + the `app_counters:{jobId}` total)
- `app/Http/Controllers/ProductCompany/ProductCompanyAppTrackerController.php` + `Staffing/Admin…` — same change in the parallel controllers (4 files)

**What it changes (the meat of this step):**
```php
public function loadcounter(Request $request)
{
    $cid   = auth()->user()->company_id;
    $jobid = (int) $request->jobid;
    $key   = "pipeline:{$jobid}";

    // 1. Redis primary (1 round-trip, in-memory on a 32 GB Redis box)
    $hash = Redis::hgetall($key);
    if (!empty($hash) && !($hash['fresh_data'] ?? 1)) {
        return Reply::dataOnly([
            'status' => 'success',
            'data'   => $this->shapeCounter($hash),
            'allCounter' => (int) ($hash['total'] ?? 0),
        ]);
    }

    // 2. pipeline_counters table (1 SELECT, indexed by PK)
    $row = DB::table('pipeline_counters')->where('job_id', $jobid)->first();
    if ($row) {
        Redis::pipeline(fn($p) => $p->hmset($key, (array) $row));
        return Reply::dataOnly(['status' => 'success', 'data' => $this->shapeCounter($row), 'allCounter' => (int) ($row->total_apps ?? 0)]);
    }

    // 3. Fallback: ES aggregation (existing code, only on first visit per job)
    return /* existing ES-based body */;
}
```
Mirror in the other 3 persona controllers.

**Server→client load change:** counter reads drop from **1 ES round-trip** to **1 Redis GET**. ES load on `loadcounter` drops to zero in the steady state. With 10 K recruiters × 5 tabs/hour saved on every hit, we eliminate ~50 K ES searches/hour.

**Success metric:** Apache access logs show `apptracker/loadcounter` no longer correlates with ES request spikes (compare `/_cat/requests` ES endpoint before/after).

**Rollback:** restore the ES body; flip a feature flag if needed.

**Why this matters most for "real-time":** `JobApplicationObserver` already bumps Redis on every save. So the moment a recruiter moves a stage, **the next `loadcounter` call** sees the new count. No polling, no delay.

---

### Step 5 — `prepareRowMaps` for all 8 tabs (kill N+1 across sibling listings)
**Files:** `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php`

Currently called only by `allDataAjax` (line ~891). Add the same call at the top of:
- `similar_existing_data_ajax` (after line 1060)
- `clientappdata_ajax` (after line 2495)
- `candidates_request_data_ajax` (after line 4287)
- `internal_bench_data_ajax` (after line 5257)
- `career_apply_data_ajax` (after line 6313)
- `public_bench_data_ajax` (after line 7361)
- `dsimilar_existing_data_ajax` (after line 8465)
- `dinternal_bench_data_ajax` (after line 9644)

**What it changes:** injects the pre-fetched `$rowMaps['jobs']`, `$rowMaps['skills']`, `$rowMaps['duplicates']` into the cached payload that the Blade partials already consume. Same call signature as `allDataAjax`.

**No logic change.** Same data, same view, just stops re-querying per row.

**Server→client load change:** SQL per request drops from `(N+M+1)` to 4.

**Success metric:** `DB::getQueryLog()` on a 50-row listing goes from 50+ entries to 4.

**Rollback:** remove the call site.

---

### Step 6 — Frontend weight: kill 400 KB FA6.js, add `defer`, lazy-load tables
**Files:** every Blade in `resources/views/service_company/apptracker/`. The 30-iteration year-dropdown lives in 9 files — extract to a partial.

**Changes:**
1. Replace `<script src="…/font-awesome/6.0.0-beta2/js/all.min.js">` (line 3 of `index.blade.php` and applied elsewhere) with `<link rel="stylesheet" href="…/font-awesome/6.0.0-beta2/css/all.min.css">`.
2. Add `defer` to all plugin `<script src>` tags.
3. New partial `resources/views/service_company/apptracker/_partials/years_dropdown.blade.php` containing the two `@for ($i = 1; $i <= 30; $i++)` loops; `@include` it from all 9 blade files.
4. Replace hardcoded `https://servicetesting.4bell.com/…` in `find-talent.blade.php` + `find-talent_old.blade.php` with `{{ asset('…') }}`.
5. Cache the Blade view via `php artisan view:cache` (built into Laravel 11).

**Server→client load change:** first-paint network drops by ~500 KB on every tab. No SQL effect.

**Success metric:** Chrome DevTools Network tab shows total transferred for first page load ≤ 250 KB.

**Rollback:** revert individual files.

---

### Step 7 — Make `loadcounter` real-time via Reverb
**Files:**
- New event: `app/Events/StageMoved.php` (broadcast on private channel `pipeline.{jobId}`)
- New listener: `app/Listeners/BroadcastStageChange.php` — emits to Redis pub/sub over Reverb
- Existing: `app/Observers/JobApplicationObserver.php` — already calls `UpdatePipelineCountersJob::dispatch(...)`; add `event(new StageMoved($app))`
- `resources/views/sections/service_company/apptrackerHeader.blade.php` — add an inline `<script>` that `Echo.private('pipeline.{jobId}').listen('StageMoved', e => { /* update badge DOM */ })`

**What it changes:** when a stage moves, observers already update Redis + DB. Add a Reverb broadcast so the browser badge updates without polling. The server side already paid for the work — Reverb just informs the listening browser tab.

**No backend logic changes.** Only an event class, listener registration, and ~30 lines of JS.

**Server→client load change:** polling on the candidate list (if any) goes to zero; badge updates within ~100 ms of the action.

**Success metric:** in DevTools Network tab the `loadcounter` AJAX call doesn't fire after a stage move — the badge updates via WS push.

**Rollback:** comment out the `event(new StageMoved(...))` line; fallback to current poll-on-tab-open.

---

### Step 8 — Push more work to the client (render, sort, paginate, filter)
**Files:** the 8 `_data_ajax` payloads — add `X-Lite: 1` mode where only the columns needed for the current view are returned.

**What it changes:** define a column → ES `_source` include list per tab (probably 12–18 of the ~80 fields in `ELASTIC_CANDIDATES`). Reduce the over-the-wire JSON to ~30 % of current size. Server stops serializing fields the recruiter never sees.

```php
$columns = [
    'id','wiseId','full_name','email','phone','skills','ectc','ctc',
    'np_id','total_exp','qualification_name','current_company','current_designation',
    'current_location','city_name','country_name','job_master_status_id',
    'master_status_name','childStatus','created_at','duplicate','client_name',
    'recommendation_id','photo','submitted_by_company_name'
];
$params['body']['_source'] = $columns;
```

Same principle for the secondary ES round-trip on counters — when reading from Redis (step 4), it returns only the 8 numbers; no _source at all.

**Server→client load change:** ES CPU halves on listing requests. Bandwidth per recruiter per session drops materially.

**Success metric:** MTU per `_data_ajax` response ≤ 80 KB at p95 (up from current ~150 KB).

**Rollback:** remove the `_source` array.

---

### Step 9 — Repair the broken routes and harden tenancy
**Files:**
- `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` — add the missing methods (or move shared logic to `BaseController`):
  - `showInterviews`, `similar_existing_data`, `internal_bench_data`, `public_bench_data`, `candidates_request_data`, `sub_stage`, `export`, `data`, `openRejFrm`, `store`, `getchild`, `ticketUpdateStatus`
  - Replace the hardcoded `Job::findOrFail(48)` in `findTalent` (line 10606) with the user's most-recent job
  - Add `where('company_id', auth()->user()->company_id)` to `ticketshow`, `updateStatus`, `storetComment`, `benchReqViewMore`'s `updatetimeline` lookup
- `routes/service.php:265, 273` — name the two unnamed routes (`->name('apptracker.index')`, `->name('apptracker.clientapp')`)

**What it changes:** fixes 12 routes that currently 500; eliminates the cross-tenant display bug in `findTalent`; plugs tenant leaks on tickets and `benchReqViewMore`; unlocks `php artisan route:cache`.

**No business-logic change** — same endpoints, same response shapes, just routing/scoping corrections.

**Server→client load change:** none at runtime (this is correctness). But once `route:cache` runs, route resolution is ~2 ms faster per request.

**Success metric:** `php artisan route:list --path=apptracker` shows all routes resolve; `route:cache` exits 0; `grep -n "Job::findOrFail(48)" app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` returns empty.

**Rollback:** revert each route/method pair.

---

### Step 10 — Move 9 stale `clientappdata_ajax` counters etc. onto `request_cache=true`
**Files:** `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` at lines 1990, 2960, 5938, 6963, 8037, 9213, 10321, 11324 (plus Product/Admin/Staffing mirrors).

**What it changes:** add `'request_cache' => true` to each `$aggParams` body. Elasticsearch will dedupe identical concurrent aggregations across the cluster within the shard request cache TTL (default 1 % heap, ~256 MB at 32 GB heap). Identical-tab-opens from different recruiters will hit ES once.

```php
$counterParams['body']['request_cache'] = true;
$response = $this->elasticService->getClient()->search($counterParams);
```

**Server→client load change:** ES CPU drops materially when many recruiters are on the same job. With 32 GB ES heap, ~256 MB is dedicated to request cache — enough for the working set of all hot jobs.

**Success metric:** ES `nodes.stats()` shows `indices.requests.cache.hit_count` increasing over the day; total counter query count drops 50–80 %.

**Rollback:** remove the `request_cache` flag.

---

## After all 10 steps — what changes for the recruiter

| UX touchpoint | Before | After |
|---|---|---|
| Open /similar_candidates page | ~13 SQL queries + 2 ES round-trips | 1 SQL (job fetch) + 1 ES data search + Redis hash GET for counters |
| Move a candidate from Sourcing → Screening | page refetches all 5 tabs' counters | counter updates within 100 ms via Reverb; the page badge flashes |
| Apply a city filter on bench view | N+1 lookups on `city_list` per selected chip | 1 batched `whereIn` (cached) |
| Print selected candidates | already client-side ✅ | unchanged |
| Search by name / email on dashboard | per-keystroke server hit | already debounced via DataTables; step 3 adds a Redis-TTL cache |

## Final 32 GB server budget

| Server | Before | After (steady-state at 10 K recruiters) |
|---|---|---|
| MySQL | hot path = 13 SQL/render; counter queries = full scans | ≤ 1 SQL/render; counters bypass MySQL |
| Elasticsearch | 2 searches/render × every tab × every user | 1 search/render with 75 % cache hit; counter searches zero |
| Redis | tiny | 5 GB (counter hashes + queues + cache) |
| PHP-FPM/Octane | god controller = ~5 MB/worker × 200 = 1 GB | same today (this plan doesn't refactor — that's a separate, longer roadmap) |

## Verification (per step)

Each step has a runbook in `docs/VERIFY.md` style:
1. **Before:** `php artisan tinker --execute='DB::enableQueryLog(); …'` to log query count.
2. **Apply:** the change for that step.
3. **After:** same script; compare numbers.
4. **Confirm:** the page still renders identically; CSRF + tenancy still scoped; `php -d memory_limit=512M artisan test` (where tests exist).

## What this plan does NOT touch

- No ranking/matching algorithm changes (rule #8).
- No business-logic refactor of the god controller (separate, larger task).
- No rename of typos like `condidate`, `commericial`, `transection` (rule #7).
- No migration files (rule #2 — schema changes are direct DDL on live DB).
- No new layer (Repository/Action) — just consolidating existing helpers (rule #9).

## Order rationale

- Steps 1–2 are **DB-only** — zero app-code risk.
- Steps 3–4 are **read-path changes** — same data, faster delivery.
- Steps 5–6 are **rendering** — same data, slimmer wire/payload.
- Step 7 is **real-time** — only adds new event/broadcast (additive).
- Step 8 is **wire compactness** — cuts data, not logic.
- Step 9 is **correctness** — fixes broken routes.
- Step 10 is **final ES tuning** — relies on steps 4 having shifted most counter reads to Redis, so ES request cache hits are uncontested.

## Estimated effort

| Step | Time | Risk |
|---|---|---|
| 1 | 30 min + maintenance window | low (DDL only) |
| 2 | 2 hours (touch 7 controllers + helper) | low |
| 3 | 4 hours (8 tabs × 1 method) | low |
| 4 | 4 hours (3 controllers, 1 job tweak) | medium — testing ES→Redis fallback chain |
| 5 | 2 hours | low |
| 6 | 4 hours | low |
| 7 | 6 hours | medium — Reverb + JS coordination |
| 8 | 2 hours | low |
| 9 | 8 hours (12 methods + tenancy audit) | medium — multiple repos |
| 10 | 1 hour | low |
| **Total** | **~3 dev-days** | |

## Roll-forward

Once all 10 are merged, the next natural step (separate PR / plan) is to **slice the 11 K-line god-controller** into `CandidateListingService` + `CounterService` per the audit. That's a refactor-only change with no behaviour delta — but it's the gate to running hundreds of Octane workers without memory pressure.

See [[apptracker-god-level-audit]] for the full evidence base.