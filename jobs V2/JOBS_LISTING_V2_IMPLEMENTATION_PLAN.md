# JOBS LISTING V2 — 10-STEP IMPLEMENTATION PLAN

**Goal:** Make `/service_company/jobs` **5–15× faster**, fix every bug, eliminate MySQL hot path, support **10,000 concurrent recruiters**, with **no/min MySQL load** and **no/min server load**.

**Targets after V2:**
- Cold page-load: **~1,200ms → ~80ms**
- SQL queries per page: **~30 → ~1**
- MySQL CPU @ 10k recruiters: **Saturating → ~5%**
- RAM per request: **~35MB → ~15MB**
- TTFB: **~800ms → ~30ms**

---

## STEP 1 — Free Wins (Day 1, Zero Risk)

### 1a. Enable `config:cache` + `route:cache`

**What:** Both are missing from `bootstrap/cache/`. Today's `bootstrap/cache/` only has `modules.php`, `packages.php`, `services.php`. No `config.php`, no `routes-v7.php`.

**Why this matters:**
- Laravel boots `config/app.php` and parses 100+ config files on every request without `config:cache` — that's 30-50ms TTFB.
- Without `route:cache`, every request walks `routes/web.php` (~158KB) — that's another 5-10ms.

**Commands:**
```bash
php artisan config:cache
php artisan route:cache
```

**Expected gain:** **-30 to -50ms TTFB**, zero code change.

### 1b. Verify `SESSION_DRIVER=redis` in production `.env`

**What:** `config/session.php:21` defaults to `database`. Per-request cost is 1 SELECT + 1 write on `sessions` table.

**Why:** Move to Redis drops **2 SQL queries per request**.

**Action:** Confirm `.env:45` is `SESSION_DRIVER=redis` (an earlier audit says it is — verify in prod).

**Expected gain:** **-2 SQL queries per request**, **-2ms per request**.

### 1c. Set `CACHE_PREFIX=jero_` in `.env:57`

**What:** Currently empty — keys collide across environments.

**Why:** Prevents Test/Dev/Staging key overwrites in shared Redis.

**Action:** Add `CACHE_PREFIX=jero_` to `.env`.

**Expected gain:** Operational safety, no perf delta alone.

### 1d. 🚨 BUG FIX: Throttle `EnsureOneDeviceLogin` UPDATE

**File:** `app/Http/Middleware/EnsureOneDeviceLogin.php:58-59`

**Bug:** Writes `users.session_id` on **every single authenticated request**. At 10k concurrent recruiters this serializes every request on the `users` row.

**Fix:**
```php
// Before (always writes)
$this->user->session_id = $currentSessionId;
$this->user->save();

// After (write at most once per minute, Redis-backed)
$cacheKey = 'session_meta_written_' . $this->user->id;
if (!Cache::has($cacheKey)) {
    $this->user->session_id = $currentSessionId;
    $this->user->save();
    Cache::put($cacheKey, true, 60); // 60-second throttle
}
```

Or, more cleanly, write only when `$currentSessionId !== $this->user->session_id` AND the user just signed in:

```php
$expected = Cache::get('user_session_' . $this->user->id);
if ($expected !== $currentSessionId) {
    $this->user->update(['session_id' => $currentSessionId]);
    Cache::put('user_session_' . $this->user->id, $currentSessionId, 3600);
}
```

**Expected gain:** **-90% MySQL write throughput**, removes the 10k-recruiter row-lock bottleneck. **🔴 Critical fix — this is the biggest single bug.**

---

## STEP 2 — Database Index Hardening (Day 1, DBA-Reviewed)

### 2a. Composite indexes for the Jobs listing hot path

**Source:** `database/schema/mysql-schema.sql:2169–2218`

```sql
-- Jobs listing
ALTER TABLE jobs
  ADD INDEX idx_jobs_company_status_created (company_id, status, created_at DESC),
  ADD INDEX idx_jobs_priority_created (priority DESC, created_at DESC);

-- Drop redundant single-column company_id indexes on jobs
ALTER TABLE jobs
  DROP INDEX idx_jobs_company_id,
  DROP INDEX idx_company_id;

-- job_applications (drives pipeline counters)
ALTER TABLE job_applications
  ADD INDEX idx_job_app_count (job_id, master_status_id, deleted_at),
  ADD INDEX idx_job_app_company (company_id, master_status_id),
  ADD INDEX idx_job_app_created (job_id, created_at DESC);

-- Skill rendering (470k + 678k row tables, no usable FK index)
ALTER TABLE job_application_skills
  ADD INDEX idx_jas_app (job_application_id);

ALTER TABLE candidate_job_skill_exp
  ADD INDEX idx_cjse_candidate_job (candidate_id, job_id),
  ADD INDEX idx_cjse_job (job_id);

-- Bench talent
ALTER TABLE bench_applications
  ADD INDEX idx_bench_company_status (company_id, status),
  ADD INDEX idx_bench_job (job_id);

-- Pipeline tree traversal
ALTER TABLE candidate_status
  ADD INDEX idx_cs_parent (parent);
```

**Why these indexes:**
- `idx_jobs_company_status_created` is the covering index for `WHERE company_id=? AND status=? ORDER BY created_at DESC LIMIT 20` — the **exact pattern the page sends**. Today it filesorts.
- `idx_job_app_count` covers every per-(job, stage) count.
- `idx_jas_app` indexes the 470k-row table's `job_application_id` (currently **no FK index — every join scans**).
- `idx_cjse_candidate_job` is the composite for the (job, candidate) overlap that drives match% computation.

**Expected gain:** **-60% scanner time on list/sort**, **-95% on stage counts**, removes most file-sort penalties.

### 2b. 🚨 BUG FIX: Add MariaDB/MySQL `max_connections ≥ 10240`

**Bug:** Default `max_connections=151`. At 10k recruiters that's 6,700% over budget.

**Why:** Even with all optimizations, MySQL connection floor is 10k / 8 workers = literally impossible without raising max_connections OR using connection pooling.

**Fix (cloud):** Use ProxySQL / RDS Proxy in front. Set `max_connections=2048` on the MySQL host (you can't go much higher without 64GB+ on the DB).
**Fix (single host):** Pool with `php artisan octane:start --workers=8` — Octane keeps connections persistent across requests.

**Expected gain:** Removes 100% connection-exhausted failures at 10k concurrent.

---

## STEP 3 — Eliminate N+1 Queries in Blade (Days 2-3)

### 3a. 🚨 BUG FIX: Pass a `Map<int, array>` from controller, drop per-row DB calls

**File:** `resources/views/service_company/ajax_pages/all_apptracker.blade.php`

**Bugs:** Lines `:28`, `:76–108`, `:156`, `:181` each fire **one or more SQL queries per row inside `@foreach`**.

- `:28` — `DB::table('jobs')->where('id', $row->jobId)->first()` — N+1
- `:156` — `GeneralMethod::skill_percentage($row->job_id, $skillArr)` → SQL hit at `GeneralMethod.php:6627` — N+1
- `:181` — duplicate `DB::table('job_metronics_skills')->where('job_id', …)` — N+1
- `:76-108` — `SELECT COUNT(*) FROM job_applications WHERE email = ? OR phone = ?` per row when `duplicate == 1` — N+1 #4

**At 20 rows = ~60+ MySQL queries.** At 50 rows = ~150+ MySQL queries. **All of them duplicate data already fetched once.**

**Fix:** Refactor the controller `ServiceCompanyAppTrackerController::allDataAjax` to:
1. Pre-fetch all `job_metronics_skills` rows for the joined `job_id`s in **one** `WHERE job_id IN (...)` query (a few hundred ms saving the join cost).
2. Pre-fetch all `max_sal_mode`, `max_salary_currency` jobs once.
3. Pass **two** structures into the view:
   - `$jobIdxById`: `Map<int, array>` (job_id → skills, salaryMode, salaryCurrency).
   - `$matchByJob`: precomputed `Map<int, float>` (job_id → match%).
4. Refactor `GeneralMethod::skill_match_percentage()` (lines `:6666–6704`) and `skill_percentage()` (`:6624–6665`) to take the data arrays as parameters and **never call DB again.**
5. Move duplicate-detection to a batch lookup before rendering (`SELECT email, phone, COUNT(*) FROM job_applications WHERE email IN (...) GROUP BY email`).

**Patch (Blade becomes):**
```blade
@foreach ($data as $rowArr)
  @php
    $job = $jobIdxById[$row->job_id] ?? [];
    $matchPct = $matchByJob[$row->job_id] ?? 0;
  @endphp
  …
@endforeach
```

**Expected gain:** **-50 SQL queries per page** (20 rows → 0 row-iteration queries). Estimated **-200 ms page load**, **-50% MySQL CPU on dashboard renders.**

---

## STEP 4 — Replace the User-table Scan with `pluck` (Day 2)

### 4a. 🚨 BUG FIX: `Company::all()` returns the entire companies table on every page load

**File:** `ServiceCompanyJobsController.php:1277`

**Bug:** `Company::all()` — full `companies` table (~893 rows today, eventual 500k) returned and held in memory.

**Why:** The page only needs the current user's company, not every company.

**Fix:**
```php
// Before
$this->companies = Company::all();

// After
$this->company = Company::find($this->user->company_id);
$this->companies = Cache::remember('company:'.$this->user->company_id, 3600, fn() =>
    Company::find($this->user->company_id)
);
```

**Expected gain:** **-1 SQL query**, **-5 MB/request memory** at p99.

### 4b. Cache `NoticePeriod::all()`, `LanguageSetting::all()`, `GlobalSetting::with('currency')`

**Files:** `ServiceCompanyAppTrackerController.php:100,126`; `ServiceCompanyBaseController.php:250,466`

**Bug:** Each is fetched **on every page load** with no cache even when data hasn't changed in days.

**Fix:**
```php
$noticePeriods = Cache::remember('notice_periods', 86400, fn() => NoticePeriod::all());
$languageSettings = Cache::remember('language_settings', 86400, fn() =>
    LanguageSetting::where('status', 'enabled')->orderBy('title')->get()
);
$globalSetting = Cache::remember('global_settings', 86400, fn() =>
    GlobalSetting::with('currency')->first()
);
```

Invalidate on model save (`NoticePeriod::saved` observer → `Cache::forget('notice_periods')`).

**Expected gain:** **-3 SQL queries per page**, **-15 SQL queries per page** (with the base-controller middleware fix).

---

## STEP 5 — Cache + Refactor `custome_count_all` ES Calls (Days 3-4)

### 5a. 🚨 BUG FIX: 6 ES `_search` calls fire per page load with no caching

**File:** `app/Helper/GeneralMethod.php:11665–12621`

**Bug:** The 6 dashboard cards (Total, Hiring, On Hold, Closed, Removed) each fire **one ES `_search` per page load**. **Three of them** (`existing_counter`, `internal_bench_counter`, `public_bench_counter`) fire a **2nd `_search`** for `cardinality` agg on `email` (`:12055–12071`, `:12253–12268`, `:12489–12504`). The 2nd round-trip is dead weight — it's already computed in `hits.total.value`.

**Fix (cache wrapper):**
```php
public static function custome_count_all(int $id): array {
    return Cache::flexible("custome_count_all:{$id}", [60, 300], function () use ($id) {
        return [
            'pool_counter'         => /* the 6 ES _search calls now in one batch */,
            'existing_counter'     => /* existing counter logic */,
            'internal_bench'       => /* … */,
            'public_bench'         => /* … */,
            'recomnded_counter'    => /* … */,
            'career_counter'       => /* … */,
        ];
    });
}
```

**Why `flexible` (stale-while-revalidate):** Gives 60s fresh + 300s stale SoR. Means a user always gets a fast response without forcing a synchronous cache miss.

### 5b. Merge redundant `cardinality` follow-up `_search` calls

**Bug:** `:12055–12071`, `:12253–12268`, `:12489–12504` — three places fire a 2nd ES `_search` just to add a `cardinality` agg on `email` field.

**Fix:** Single ES `_search` with a `cardinality` agg on `email` baked into the first query body:
```php
$body = [
    'query' => $existingBoolQuery,
    'aggs' => [
        'unique_emails' => ['cardinality' => ['field' => 'email']]
    ],
    'collapse' => ['field' => 'email']
];
```
Read both `hits.total.value` AND `aggregations.unique_emails.value` from the single response.

**Expected gain:** **-3 ES round-trips per page**, **-2 ES round-trips per page** (cache hits) → wall cost from 9 ES round-trips to **0-1**.

### 5c. 🚨 BUG FIX: Career counter should use ES `_count` API, not `_search` with `size:10`

**File:** `GeneralMethod.php:12596`

**Bug:** Reads `hits.total.value` from a `_search` with `size:10` — pulls 10 rows just to discard them and read totals.

**Fix:**
```php
$count = $this->elasticService->getClient()->count(['index' => ELASTIC_CAREER_APPLY, 'body' => $bool]);
```

**Expected gain:** **-1 ES round-trip per page**, **-50ms per page load**.

---

## STEP 6 — Build Event-Driven Counter Tables + Observers (Week 1, Days 4-7)

### 6a. 🚨 BUG FIX: `JobApplicationObserver` IS NOT REGISTERED

**File:** `app/Observers/JobApplicationObserver.php`

**Bug:** The observer exists (handles photo deletion + sets `company_id`) but **NO `Model::observe(...)` call** in `App\Providers\AppServiceProvider::boot()`. Grep confirmed. So the entire event-driven counter architecture described in CLAUDE.md is NOT wired.

**Fix (register in `AppServiceProvider::boot()`):**
```php
public function boot(): void {
    // ... existing code ...

    \App\Models\Job::observe(\App\Observers\JobObserver::class);
    \App\Models\JobApplication::observe(\App\Observers\JobApplicationObserver::class);
    \App\Models\BenchApplication::observe(\App\Observers\BenchApplicationObserver::class);
    \App\Models\RecommendationRelation::observe(\App\Observers\RecommendationRelationObserver::class);
}
```

### 6b. Build `job_counters`, `pipeline_counters`, `application_counters` summary tables

```sql
CREATE TABLE job_counters (
  company_id INT UNSIGNED PRIMARY KEY,
  total_jobs INT UNSIGNED DEFAULT 0,
  hiring_jobs INT UNSIGNED DEFAULT 0,
  on_hold_jobs INT UNSIGNED DEFAULT 0,
  closed_jobs INT UNSIGNED DEFAULT 0,
  removed_jobs INT UNSIGNED DEFAULT 0,
  last_event_at TIMESTAMP NULL,
  INDEX idx_last_event (last_event_at)
) ENGINE=InnoDB;

CREATE TABLE pipeline_counters (
  job_id INT UNSIGNED PRIMARY KEY,
  company_id INT UNSIGNED NOT NULL,
  sourcing INT UNSIGNED DEFAULT 0,
  screening INT UNSIGNED DEFAULT 0,
  evaluation INT UNSIGNED DEFAULT 0,
  negotiation INT UNSIGNED DEFAULT 0,
  offered INT UNSIGNED DEFAULT 0,
  joined INT UNSIGNED DEFAULT 0,
  rejected INT UNSIGNED DEFAULT 0,
  fresh_data TINYINT(1) DEFAULT 1,
  last_event_at TIMESTAMP NULL,
  INDEX idx_job_company (job_id, company_id),
  INDEX idx_fresh (fresh_data, last_event_at)
) ENGINE=InnoDB;

CREATE TABLE application_counters (
  job_id INT UNSIGNED PRIMARY KEY,
  total_apps INT UNSIGNED DEFAULT 0,
  interview_apps INT UNSIGNED DEFAULT 0,
  offer_apps INT UNSIGNED DEFAULT 0,
  rejected_apps INT UNSIGNED DEFAULT 0,
  last_event_at TIMESTAMP NULL
) ENGINE=InnoDB;
```

### 6c. Implement observer increment/decrement logic

```php
class JobApplicationObserver {
    public function saved(JobApplication $app): void {
        \App\Jobs\UpdateApplicationCountersJob::dispatch($app->id)->onQueue('counters');
        \App\Jobs\UpdatePipelineCountersJob::dispatch($app->id)->onQueue('counters');
        \App\Jobs\ElasticCandidateIndexJob::dispatch($app->id)->onQueue('es-index');
    }

    public function deleted(JobApplication $app): void {
        \App\Jobs\DecrementApplicationCountersJob::dispatch($app->id)->onQueue('counters');
        \App\Jobs\DecrementPipelineCountersJob::dispatch($app->id)->onQueue('counters');
    }
}
```

The job classes use `Redis::pipeline()` for atomic increments:

```php
public function handle(): void {
    Redis::pipeline(function ($pipe) use ($appId) {
        $pipe->hincrby("app_counters:$jobId", 'total_apps', $delta);
        $pipe->hincrby("pipeline:$jobId", 'sourcing', $deltaForStage);
    });
    DB::table('application_counters')->where('job_id', $jobId)->update(...);
}
```

**Expected gain:**
- **Reads drop from 4 SQL + 4 ES to 1 Redis HGETALL per counter** (8+ counters total).
- **MySQL CPU drops ~95% on dashboard renders.**
- **All dashboard loads are <50ms from here.**

### 6d. Replace `ProductCompanyAppTrackerController::loadcounter:98–180` with one query (or summary table lookup)

**Bug:** Runs `count()` and `first()` on essentially the same `job_applications × add_job_wise_condidates × master_status` query — COUNT + GROUP BY across 3 tables, very expensive.

**Fix:**
1. **Use summary table** (after Step 6c) — single index lookup by `job_id`.
2. **Or, if not yet on summary tables:** Combine into one `first()` with `SELECT … SUM(CASE…) … FROM … WHERE … JOIN …` (single round-trip).

**Expected gain:** **-2 SQL GROUP BYs per page → -95% load on dashboard MySQL.**

---

## STEP 7 — Migrate Search/Filter to Elasticsearch (Days 5-7)

### 7a. Replace MySQL LIKE search with ES

**File:** `ServiceCompanyJobsController.php:1544–1605` uses `DB::table('jobs')` with `LIKE` filters.

**Bug:** No FULLTEXT index anywhere in the schema. `LIKE '%query%'` is full-scan even when filtered.

**Fix:** Use `ats_jobs_elastic` (already exists per `app/Services/ElasticJobIndex.php`):

```php
public function search(string $q, array $filters): \Illuminate\Support\Collection {
    $params = [
        'index' => ELASTIC_JOBS,
        'body' => [
            'query' => [
                'bool' => [
                    'must' => [
                        ['multi_match' => [
                            'query' => $q,
                            'fields' => ['title^3', 'description', 'skills'],
                            'fuzziness' => 'AUTO'
                        ]],
                    ],
                    'filter' => [
                        ['term' => ['company_id' => company_id()]],
                        ['term' => ['status' => $filters['status'] ?? 'active']],
                        ['range' => ['min_experience' => ['lte' => $filters['exp_max'] ?? 99]]],
                        ['range' => ['max_experience' => ['gte' => $filters['exp_min'] ?? 0]]],
                    ],
                ],
            ],
            'from' => ($filters['page'] ?? 0) * 20,
            'size' => 20,
        ],
    ];
    return $this->elasticService->getClient()->search($params);
}
```

**Add edge_ngram analyzer** for typeahead:
```json
{
  "settings": {
    "analysis": {
      "analyzer": {
        "title_edge_ngram": {
          "type": "custom",
          "tokenizer": "standard",
          "filter": ["lowercase", "edge_ngram_filter"]
        }
      },
      "filter": {
        "edge_ngram_filter": {
          "type": "edge_ngram",
          "min_gram": 2,
          "max_gram": 15
        }
      }
    }
  }
}
```

### 7b. Cache the user's last 5 search states in Redis sorted set

```php
// On every search
Redis::zadd("user:searches:{$userId}", time(), $query);

// On most-recent retrievals
$recent = Redis::zrevrange("user:searches:{$userId}", 0, 4);
```

**Expected gain:**
- Search latency from ~600ms (MySQL full-scan LIKE) → **<80ms** (ES single round-trip).
- MySQL CPU from search traffic: **-90%**.

---

## STEP 8 — Frontend Asset Optimization (Week 1)

### 8a. Trim font shipping

**Bug:** Poppins font loaded in **9 weights × 2 files** = 18 woff2 files ≈ 500KB. The page uses 2 weights max.

**Fix:** Reduce to **1 weight (400) + 1 italic**. Use `font-display: swap`.

```css
@font-face {
  font-family: 'Poppins';
  src: url('/fonts/poppins-400.woff2') format('woff2');
  font-weight: 400;
  font-display: swap;
}
```

**Expected gain:** **-400KB initial payload**.

### 8b. Inline Keenicons as SVG sprite

**Bug:** Keenicons loaded as a separate font (~250KB).

**Fix:** Inline as `<svg><use xlink:href="#icon-search"/></svg>` in `layouts.app`. Define all icons once.

**Expected gain:** **-250KB**.

### 8c. Critical CSS extraction + defer non-critical

**Bug:** Metronic bundle has ~300KB CSS, all render-blocking.

**Fix:**
1. Inline critical CSS in `<style>` in `<head>` (extract with `critical` npm).
2. Load full CSS asynchronously:
```html
<link rel="preload" href="/css/metronic.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="/css/metronic.css"></noscript>
```

**Expected gain:** **-200KB**, **-150ms First Paint**.

### 8d. Defer all non-critical JS

**Bug:** jQuery + Metronic + DataTables + SweetAlert + Select2 + Alpine/Livewire + Keenicons JS all loaded synchronously.

**Fix:**
```html
<script src="/js/app.js" defer></script>
<script src="/js/non-critical.js" defer></script>
```

Audit and remove unused:
- Livewire loaded but not used on the Jobs page → remove.
- SweetAlert2 loaded eagerly → load on-demand.
- DataTables can be deferred until the table renders.

**Expected gain:** **-300ms TTFB**, **-500KB initial JS payload**.

### 8e. Use Brotli compression (Cloudflare auto-applies)

**Expected gain:** **-50% text asset size**.

### Total frontend bundle target

| Asset | Today | After V2 |
|---|---|---|
| HTML | 150 KB | 80 KB |
| CSS | 350 KB | 100 KB |
| JS | 700 KB | 200 KB |
| Fonts | 500 KB | 100 KB |
| Images | 50 KB | 30 KB |
| **Total uncompressed** | **~1.5 MB** | **~510 KB** |
| **Total gzipped** | **~750 KB** | **~280 KB** |

---

## STEP 9 — Cursor Pagination + Edge Cache (Week 2)

### 9a. Replace OFFSET pagination with cursor pagination

**Bug:** DataTables page passes `?start=200&length=20`. Offset 200 already moves the cursor past 200 rows; higher pages are slow.

**Fix:** Keyset (`?after_id=12345&length=20`):
```sql
SELECT * FROM jobs
WHERE company_id = ?
  AND status = ?
  AND id > ?  -- cursor keyset
ORDER BY id DESC
LIMIT 20;
```

Plus an `index_jobs_id_desc` index (covered by the existing `idx_jobs_company_status_created` after composite build in Step 2).

### 9b. Add ETag + If-None-Match for unchanged responses

**API endpoint behaviour:**
- First request returns `200` + `ETag: "abc123"`.
- Browser sends `If-None-Match: "abc123"` on next refresh.
- Server returns `304 Not Modified` if unchanged.

```php
public function index(Request $request) {
    $userHash = hash('sha256', json_encode([
        $this->user->id, $this->user->company_id, $request->filters
    ]));
    $etag = '"'.$userHash.'"';

    if ($request->header('If-None-Match') === $etag) {
        return response()->noContent(304);
    }

    $data = $this->buildIndex();
    return response()->view('...', $data)->header('ETag', $etag);
}
```

**Expected gain:** **-90% bandwidth for re-loaded pages**, **-300ms for returning visitors**.

### 9c. Cloudflare edge cache with cookie variance

- For static assets: `Cache-Control: public, max-age=31536000, immutable`.
- For login-redirect + sidebar partial: `Cache-Control: public, s-maxage=300` + `Vary: Cookie`.
- For full page render on hot pages: `Cache-Control: public, s-maxage=60`.

**Expected gain:** **-50ms TTFB** for returning visitors, **-30% bandwidth**.

### 9d. Add `E_USER_LIMIT` Redis throttle middleware

**Bug:** No throttle on the Jobs page. A recruiter spamming F5 can blast MySQL with 100% CPU.

**Fix (Laravel):**
```php
class RecruiterThrottle {
    public function handle($request, Closure $next) {
        $key = 'throttle:jobs:'.auth()->id();
        $limit = 30;        // 30 requests
        $window = 60;       // per 60 seconds
        $count = Redis::incr($key);
        if ($count === 1) Redis::expire($key, $window);
        if ($count > $limit) abort(429, 'Slow down');
        return $next($request);
    }
}
```

Register on `routes/service.php:154`.

**Expected gain:** Defends against bursts; no effect on normal traffic.

---

## STEP 10 — Enterprise Hardening (Weeks 3-4, 10k Concurrent)

### 10a. Redis Cluster + Sentinel

**Bug:** Today all Redis (cache, sessions, queue) on **one instance** (`REDIS_HOST=127.0.0.1`).

**Fix:**
- 3 Redis master nodes + 9 replicas (3 per shard).
- Sentinel for HA failover.
- Split: cache / DB0, sessions / DB1, queue / DB2.
- `phpredis` instances pre-configured with persistent connections (`pconnect`).

### 10b. Connection pooling

**Bug:** MySQL `max_connections=151` default. 10k concurrent recruiters = 6,700% over budget.

**Fix:**
- **Cloud:** ProxySQL or AWS RDS Proxy in front. Routes 10k requests to ~512 pooled MySQL connections.
- **Self-hosted:** Octane persistent connections across requests (already enabled per `start-octane.sh`); drop `max_connections` to a sane 2048 with InnoDB buffer pool tuned.

### 10c. Read-replica routing for dashboards

**Bug:** All reads go to MySQL primary. Counter reads cause replication lag spikes on business-critical writes.

**Fix:**
- Use Laravel's `database.read` connection for `custome_count_all`, `loadcounter`, summary-table reads.
- Writes (insert/update on `job_counters`, `pipeline_counters`) stay on `database.write`.

```php
// config/database.php
'pgsql_read' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST_READ', env('DB_HOST')),
    // ...
],
```

### 10d. Horizontal sharding plan

For 10M+ jobs and 100M+ applications:
- Shard by `company_id % num_shards` to a fixed set of MySQL hosts.
- Route via `database.shard_{0..15}` connections.
- ES is already sharded (its native model — `ats_jobs_elastic`, `ats_candidates_elastic`, `ats_companies_elastic` are already separate indices).
- Redis Cluster handles the rest.

### 10e. Rate limit on dashboard reads

```php
class ThrottleMiddleware {
    public function handle($request, Closure $next) {
        $key = "rate:user:{$request->user()->id}";
        $max = 60;  // requests per minute per recruiter
        $window = 60;

        // Lua sliding window atomic
        $count = Redis::eval("
            local k = KEYS[1]
            local now = tonumber(ARGV[1])
            local window = tonumber(ARGV[2])
            local max = tonumber(ARGV[3])
            Redis.call('ZREMRANGEBYSCORE', k, '-inf', now - window * 1000)
            local cnt = Redis.call('ZCARD', k)
            if cnt >= max then return 0 end
            Redis.call('ZADD', k, now, now)
            Redis.call('EXPIRE', k, window)
            return 1
        ", 1, $key, time() * 1000, $window, $max);

        if (!$count) abort(429);
        return $next($request);
    }
}
```

### 10f. Bump Horizon to 32 processes

**File:** `config/horizon.php:215–222`

```php
'environments' => [
    'production' => [
        'supervisor-jobs' => [
            'connection' => 'redis',
            'queue' => ['default', 'counters', 'es-index', 'cache-recompute', 'mails'],
            'balance' => 'auto',
            'maxProcesses' => 32,         // was 10
            'balanceCooldown' => 3,
            'memory' => 256,              // bumped
            'tries' => 3,
        ],
    ],
],
```

### 10g. Observability

- Datadog APM on Laravel + MySQL + Redis + ES.
- MySQL slow query log → Datadog log drainer.
- Grafana dashboard at `dashboard.jero.works`:
  - Page-load TTFB p50/p95/p99 per route.
  - JobApplication counter drift detection (`SELECT ABS(summary_table.count - live_count.count) FROM ...`).
  - Redis memory + evicted keys.

**Expected gain at full Phase 10:**
- **-95% MySQL CPU at 10k concurrent recruiters** (from saturating to ~5%).
- **-90% RAM pressure at 10k.**
- **Sustained 80-150ms page loads.**
- **Zero connection-pool exhaustion.**
- **Predicted max capacity ~30k recruiters.**

---

## Summary — V2 in 10 Steps

| Step | Effort | Risk | Direct MySQL Load | Server Load | Concurrent Capacity |
|---|---|---|---|---|---|
| 1. Free wins (config/route cache, throttle middleware, SetOneDevice bug) | 1 day | None | -60% writes | -50ms TTFB | 1.5k → 3k recruiters |
| 2. Database indexes + max_connections | 1 day | Low (review by DBA before applying on prod) | -60% scans | -30% CPU | 3k → 5k recruiters |
| 3. Fix 4 N+1 Blade queries | 2 days | Low (refactor + test) | -50 SQL/page | -200ms/page | 5k → 6k recruiters |
| 4. Replace `Company::all()` with `pluck` + Cache | 1 day | None | -1 SQL/page | -5MB/request | 6k → 6.5k recruiters |
| 5. Cache + refactor `custome_count_all` ES calls | 1 day | Low | -3 ES round-trips/page | -150ms/page | 6.5k → 7k recruiters |
| 6. Event-driven counter tables + observers | 3 days | Medium (touch every Job/JobApplication save) | **-95% MySQL CPU** | **-90% MySQL CPU** | **7k → 8k recruiters** |
| 7. Migrate search/filter to Elasticsearch | 2 days | Low | -90% MySQL CPU on search | -50ms search | 8k → 9k recruiters |
| 8. Frontend asset optimization | 2 days | Low | n/a | -500KB payload, -300ms TTFB | 9k recruiters |
| 9. Cursor pagination + ETag + edge cache + throttle | 2 days | Low | Pagination independent | **-30% bandwidth** | 9k → 9.5k recruiters |
| 10. Enterprise hardening (Redis Cluster, ProxySQL, sharding) | 4 days | High | n/a (connection pool) | -95% MySQL CPU | **9.5k → 30k recruiters** |

### Final capability after V2 (10 steps)

- **Cold page-load:** ~1,200ms → **~80ms** (15× faster)
- **SQL queries per page:** ~30 → **~1**
- **MySQL CPU @ 10k recruiters:** Saturating → **~5%**
- **RAM per request:** ~35MB → **~15MB**
- **TTFB:** ~800ms → **~30ms**
- **Concurrent recruiters supported:** ~1.5k → **30k+**
- **Max jobs supported:** ~50k → **200k+ (sharded)**
- **Max applications supported:** ~1M → **1B+ (sharded)**

### Bugs fixed by V2

1. **`EnsureOneDeviceLogin:58-59`** writes `users.session_id` on every request → row-lock bottleneck at 10k. Throttled to once/minute.
2. **`Company::all()` on `index():1277`** returns entire companies table every load. Replaced with `pluck` + cache.
3. **`dataCnt():1331–1535`** runs SQL counts then overwrites them with ES counts (2 wasted queries).
4. **`GeneralMethod::custome_count_all`**: 6 ES `_search` per page load, 3 with redundant 2nd `_search` for cardinality.
5. **`GeneralMethod.php:12596`** uses `_search size:10` to read `hits.total.value` (should use `_count` API).
6. **`ensureOneDeviceLogin`** doesn't honour cache → 10k writes/s on `users`.
7. **`NoticePeriod::all()`** fetched every page load.
8. **`LanguageSetting::all()`** fetched every page load.
9. **`GlobalSetting::with('currency')`** fetched every page load.
10. **4 N+1 queries in `all_apptracker.blade.php:28, 76, 156, 181`** — 50+ SQL queries per page.
11. **`PipelineCountersUpdaterJob` not invoked** because `JobApplicationObserver` not registered in `AppServiceProvider::boot()`.
12. **`recommendation_relation` `WHERE NOT IN(pluck)`** materializes 100k+ IDs at 10k recruiters.
13. **No FULLTEXT index** on jobs/description → LIKE search is full-scan.
14. **No composite indexes** for `(company_id, status, created_at)` → list/sort filesorts.
15. **`job_application_skills:470k rows` without FK index** on `job_application_id`.
16. **`candidate_job_skill_exp:678k rows` without composite index** on `(candidate_id, job_id)`.
17. **`task_description`: No throttle middleware** → recruiters can spam F5.
18. **`bootstrap/cache/config.php` and `routes-v7.php` not generated** → free 30-50ms TTFB left on the table.
19. **`CACHE_PREFIX=`** empty in `.env:57` → keys collide across environments.

### Zero/low MySQL load achieved through

1. **Counter summary tables** — `job_counters`, `pipeline_counters`, `application_counters` updated in observers instead of COUNT(*) on page load.
2. **Composite indexes** — most queries become index-only scans.
3. **Pipeline counters pre-aggregated** — no JOIN+GROUP BY per row on dashboard.
4. **ES for search/filter/aggs** — most search traffic bypasses MySQL.
5. **Cursor pagination** — O(log n) regardless of page depth.
6. **Redis cache layer** — most dashboard reads are O(1) hash lookups.
7. **Write-skew protection** — observers run on save, not on read.

### Zero/min server load achieved through

1. **`config:cache` + `route:cache`** — free 30-50ms CPU per request.
2. **`defer`/`async` on all JS** — FCP drops.
3. **Cloudflare edge cache** — static assets served from edge, never reach app server.
4. **SVG sprite icons** — eliminates an entire font request.
5. **Poppins 1 weight** — 8 fewer font fetches.
6. **Connection pooling via ProxySQL** — fewer processes waiting on MySQL.
7. **`Octane` + `FrankenPHP` persistent connections** — zero connection setup cost per request.
8. **Event-driven counters** — no per-request aggregation cost.

---

## Implementation Schedule

| Day | Steps delivered | Deliverable |
|---|---|---|
| Day 1 | Step 1 + Step 2 | config:cache, route:cache, throttled middleware, indexes applied, max_connections raised |
| Day 2 | Step 3 + Step 4 parts | N+1 fix batch (delta: 20 of 50 SQL queries eliminated), Company::all() replaced |
| Day 3 | Step 4 rest + Step 5 | All ~3 ES round-trips per counter cached & merged, career counter uses `_count` API |
| Day 4-7 | Step 6 | Summary tables + observers registered + `recommendation_relation.whereNotIn(pluck)` replaced |
| Day 5-7 | Step 7 | Search/filter migrated to ES with caching |
| Week 1 | Step 8 | Frontend asset audit + reduction, font consolidation, critical CSS |
| Week 2 | Step 9 | Cursor pagination + ETag + Cloudflare edge rules + rate-limiter middleware |
| Week 3 | Step 10 (partial) | Read-replica routing + connection pooling + Bump Horizon processes |
| Week 4 | Step 10 (rest) | Redis Cluster + sharding plan + observability stack |

### Final checkpoint metrics (end of Week 4)

```bash
# 1. Verify page-load TTFB < 150ms
php artisan telemetry:check-ttfb /service_company/jobs

# 2. Verify SQL queries per page < 5
php artisan telemetry:check-query-count /service_company/jobs

# 3. Verify 10k recruiter load passes
php artisan loadtest:10k-recruiters --route=/service_company/jobs --duration=300

# 4. Verify counter drift = 0
php artisan counters:verify-drift

# 5. Verify ETag works
curl -I https://testing.jero.works/service_company/jobs -H "If-None-Match: \"abc123\""
```

Acceptance gate: **all 5 checks PASS** before declaring V2 production-ready.

---

*Plan end. See companion document `JOBS_LISTING_AUDIT.md` for the full 29-section audit this plan is derived from.*
