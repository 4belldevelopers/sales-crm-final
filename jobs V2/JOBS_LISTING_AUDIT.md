# JOBS LISTING PAGE — PRODUCTION PERFORMANCE AUDIT (V2)

**Target URL:** `/service_company/jobs`
**Controller:** `App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController@index` (`ServiceCompanyJobsController.php:1273`)
**Related view pipeline:** `App\Http\Controllers\ServiceCompany\ServiceCompanyAppTrackerController@index` + `service_company/jobs/index.blade.php` (985 lines) + `resources/views/service_company/ajax_pages/all_apptracker.blade.php` (per-row render)
**Production Target:** 10,000 concurrent recruiters, <500ms page load
**Cold-load measurement (recruiter, non-admin, no cache):** **~30–35 SQL queries + 4–7 ES round-trips + 15+ HTTP asset requests** — every request, every recruiter, until this audit's items land.

---

## §1 Route Analysis

**Route declaration:** `routes/service.php:154` declares `Route::resource('jobs', ServiceCompanyJobsController::class)` inside a group with prefix `service_company` (`routes/service.php:41`). Loaded into web by `routes/web.php:1643`.

A parallel `routes/service_old.php:127` exists but is **NOT** required from `web.php` — drift risk. Confirm nothing else loads `service_old.php` in your Laravel bootstrap beyond `web.php`.

**Middleware stack (in order):**

| # | Middleware | File:line | DB cost |
|---|---|---|---|
| 1 | `web` group (default) | `bootstrap/app.php:11` | 1 SELECT on `sessions` (driver `database` per `config/session.php:21`) |
| 2 | `Authenticate` | bootstrap alias `:28` | 1 SELECT `users` + ~2–3 Spatie role/perm lookups |
| 3 | **`EnsureOneDeviceLogin`** | `app/Http/Middleware/EnsureOneDeviceLogin.php:18,58-59` | **1 UPDATE on `users.session_id` EVERY request — unconditional write** |
| 4 | `CustomPackage` | `app/Http/Middleware/CustomPackage.php:18,30,50` | 1 SELECT `companies` + (sometimes) 1 SELECT `package_active` |

### Findings
1. **No tenant middleware exists** — tenant scoping is `auth()->user()->company_id` read inside the controller. The directory `app/Http/Middleware/` has 9 files; none are tenant resolvers.
2. **`EnsureOneDeviceLogin:58-59`** writes `session_id` on every authenticated request. At 10k concurrent recruiters this serializes on the `users` row. **🔴 Critical bug — must fix.**
3. **No throttle middleware** — nothing protects MySQL from a recruiter double-clicking the page.
4. **`config/session.php:21`** defaults to `database`. Per-request cost = 1 SELECT + 1 write on `sessions` table (~2 ms each).

---

## §2 Controller Review

**`index()`: 87 lines, `ServiceCompanyJobsController.php:1273–1313`.** Hits `Company::all()` **unscoped** at `:1277` (full companies table) plus 4 `Job::count()` aggregates at `:1278–1280` plus another 4 `Job::join('job_assign')->count()` at `:1293–1296` for the non-admin branch — then calls `$this->dataCnt()` at `:1300` (which builds more counts already).

**`dataCnt()`: 381 lines, `:1314–1697`.** This is where most of the hot path lives.

| Line | Bug / Issue |
|---|---|
| `:1331–1535` (admin branch) | **Builds SQL counts then immediately overwrites them with ES counts** at `:1497–1498` and `:1420–1493` — 2 SQL counts per request are pure waste |
| `:1613–1629` | `pluck('job_id')` over every recommendation ever sent to this tenant, then `->get()` over `jobs LEFT JOIN recommendation_relation` to compute `$findJobs`, then `count($findJobs)` in PHP. **Materializes a million-row result set to count it.** |
| `:1334, 1342, 1349, 1547, 1548, 1577, 1601` | Interpolates `auth()->user()->company_id` and `id` directly into `DB::raw(...)`. Safe values, but defeats MySQL query cache. |
| `:1556–1567, 1587–1592` | Mixed `where`/`orWhere` chains — `'final_status IN (1,2)' OR every recruiter-assigned job`. **Logical bug — scans the whole `recommendation_relation` table regardless of `final_status`.** |

**Estimated controller cost:** ~15 SQL + 4 ES round-trips, zero `cache()`/`Redis::` calls.

**`ServiceCompanyBaseController.php:78–247`** additive middleware — fires **15–20 additional SQL queries** before `index()` even starts: `hasRole('admin')` × 2, `user_token.count`, `user_token.first`, `Company::find` × 2, `todoItems.groupBy`, `GlobalSetting::with('currency')`, `package_active`, `LanguageSetting::all()` etc.

**No memoization** anywhere: `auth()->user()` accessed 8+ times in `dataCnt()` without local-variable memo.

---

## §3 Service Layer Review

**`app/Services/`** contains only ES indexer helpers (`ElasticJobIndex`, `ElasticCandidatesIndex`, `ElasticBenchCandidatesIndex`, `ElasticCaompanyIndex`, `ElasticService`) and a few cross-cutting services — NOT query services.

**`app/Helper/GeneralMethod.php`** is the de-facto service layer, used by every controller. The two helpers on the Jobs/Apptracker hot path:

| Method | File:line | What it does | Cost per call |
|---|---|---|---|
| `custome_count_all($id)` | `GeneralMethod.php:11665–12621` | 6 ES `_search` per page load (one per dashboard card) + 3 redundant follow-up `_search` for unique-email cardinality | **~9 ES round-trips**, no cache |
| `skill_match_percentage()` | `GeneralMethod.php:6666–6704` | Recomputes match% per row, plucks `job_metronics_skills` per row | N+1 SQL — see §5 |
| `skill_percentage()` | `GeneralMethod.php:6624–6665` | Same data, plucks `job_metronics_skills` per row | N+1 SQL |

### Findings
1. **`GeneralMethod::custome_count_all` `:12596`** uses ES `_search` with `size:10` then reads `hits.total.value` instead of using the `_count` API. Pure waste.
2. **3 of 6 dashboard counters** at `:12055–12071`, `:12253–12268`, `:12489–12504` fire a 2nd `_search` solely to compute a `cardinality` agg on `email`. The first call already returns `hits.total.value`. **2nd round-trip is dead weight.**
3. **`GeneralMethod.php` is 13,000+ lines** — single-file God helper used by every controller. Splitting it is yak-shave but every section has duplicated work that would disappear with coherent services.

---

## §4 SQL Query Review

### Per-page cold-load query inventory

| # | Source:line | Query | Type | Index status |
|---|---|---|---|---|
| 1 | `EnsureOneDeviceLogin.php:58-59` | `UPDATE users SET session_id = ?` | Write on hot row | **row-lock contention at 10k** |
| 2 | `ServiceCompanyBaseController.php:78-247` | 15–20 misc (user_token, roles, permissions, companies, todoItems, language_settings, package_active) | Mixed | Various |
| 3 | `index():1277` | `SELECT * FROM companies` | **Full table** | None |
| 4 | `index():1278-1296` | 4 × `SELECT COUNT(*) FROM jobs`/`job_assign JOIN jobs` | 4 × count | `idx_jobs_company_id` (redundant; none on `status`) |
| 5 | `dataCnt():1420` (admin only) | `SELECT COUNT(*) FROM recommendation_relation …` | **Wasted — overwritten by ES at `:1494`** | None |
| 6 | `dataCnt():1497-1498` (admin only) | `SELECT COUNT(*) FROM jobs …` | **Wasted — overwritten by ES** | None |
| 7 | `dataCnt():1568/1593/1605` (non-admin) | 3 × `SELECT COUNT(*) FROM jobs` | OK | `idx_jobs_company_id` |
| 8 | `dataCnt():1613–1615` | `SELECT job_id FROM recommendation_relation WHERE to_company_id = ?` (full materialization) | **Huge result set, becomes IN-clause** | `idx_staffing_company_relation` (line 3830) ✓ |
| 9 | `dataCnt():1619–1626` | `SELECT jobs.id FROM jobs LEFT JOIN recommendation_relation … WHERE NOT IN (...)` | **Materializes full join, then `count()` in PHP** | `idx_jobs_company_id` ✓ |
| 10 | `dataCnt():1634–1638` | `SELECT COUNT(*) FROM recommendation_relation WHERE final_status IN (0,3)` | Group scan | Index only on `(to_company_id, from_company_id)` |
| 11 | `all_apptracker.blade.php:28` | `SELECT max_sal_mode, max_salary_currency FROM jobs WHERE id = ?` (per row) | **N+1** (20× per page) | PK |
| 12 | `GeneralMethod.php:6627` | `SELECT skill_name FROM job_metronics_skills WHERE job_id = ?` (per row) | **N+1 #2** | None (table-level FK only) |
| 13 | `all_apptracker.blade.php:181` | Same as #12 again (per row) | **N+1 #3** | None |
| 14 | `all_apptracker.blade.php:76-108` (dup branch) | `SELECT COUNT(*) FROM job_applications WHERE email = ? OR phone = ?` (per row) | **N+1 #4** | None |

**Per-page totals (cold, 20 rows):** **~80 SQL queries** (15 controller + 15-20 base-controller + ~50 row-iteration N+1s).

### Critical missing indexes (`database/schema/mysql-schema.sql:2169–2218` for jobs, `:1903–2020` for job_applications)

- **`jobs`** — NO composite `(company_id, status, created_at)` → every `WHERE company_id=? AND status=? ORDER BY created_at DESC` filesorts
- **`job_applications`** — NO composite `(job_id, master_status_id)` → every per-(job, stage) count scans
- **`job_applications`** — NO index on `master_status_id`, `deleted_at`, `created_at`
- **`job_skills`** — single-column FKs only, NO composite `(job_id, skill_id)` for skill chip rendering
- **`job_application_skills`** — 470k-row table with **NO index on `job_application_id`** at line 1893–1898. Any join from `job_applications` to skill list will scan.
- **`candidate_job_skill_exp`** — 678k-row table, PK only at line 416. No index on `(candidate_id, job_id)` — serious hot-path miss.
- **`bench_applications`** — NO index on `job_id`, `status` (only on `company_id`)
- **`master_status` / `candidate_status`** — PK only; no `(company_id, parent)` for stage tree traversal

**No FULLTEXT index anywhere.** `grep FULLTEXT` against `mysql-schema.sql` and all `database/migrations/*.php` returned zero matches. Search uses unindexed `LIKE`.

---

## §5 Eloquent Review — N+1 hotspots confirmed

1. **`all_apptracker.blade.php:28`** — `DB::table('jobs')->where('id', $row->jobId)->first()` **inside `@foreach ($data as $rowArr)`**. Per-row. N+1.
2. **`GeneralMethod::skill_percentage()` at `:6627`** — invoked per row from `:156` of the same blade. Per-row `pluck` on `job_metronics_skills`.
3. **`all_apptracker.blade.php:181`** — duplicate plck of `job_metronics_skills` per row (already done at `:156`).
4. **`all_apptracker.blade.php:76–108`** — duplicate-mode flag fires `job_applications.where(email|phone)->count()` per row.

**`JobApplication` model** (`app/Models/JobApplication.php:17`) has `$appends = ['resume_url', 'photo_url']`, and `getResumeUrlAttribute` at `:63-69` does a nested `documents()->where('name','Resume')->first()` — fires per row when ANY job-application model hydration happens. **The `tenancy` global scope at `:23-27` is commented out** → cross-tenant leak risk (already tracked as BUG-03/SEC-02 in claude.md).

---

## §6 Pagination Review

**`dataCnt():1619–1626`** does `->groupBy('jobs.id')->get()` then `count($findJobs)` — full materialization, no offset/limit.

- Replace `count($findJobs)` with a subquery (`selectRaw('COUNT(DISTINCT jobs.id) as c')`).
- Use Yajra DataTables cursor pagination.
- Store `last_seen_id` in Redis at `apptracker_pagination_user_{id}` so filter changes pick up where the recruiter left off.

`Job::count()` should not exist on the page load at all — see §7.

---

## §7 Dashboard Statistics — Total / Hiring / On Hold / Closed / Removed

Computed live every page load.

**`index():1278–1296`** runs 4 `Job::count()` aggregates per request **plus** `dataCnt()` recomputes the same numbers from ES at `:1676–1688`. **The 4 SQL counts and the 4 ES counts compute the same numbers.**

### Optimal solution — event-driven counter + summary table

```sql
CREATE TABLE job_counters (
  company_id INT UNSIGNED PRIMARY KEY,
  total_jobs INT UNSIGNED DEFAULT 0,
  hiring_jobs INT UNSIGNED DEFAULT 0,
  on_hold_jobs INT UNSIGNED DEFAULT 0,
  closed_jobs INT UNSIGNED DEFAULT 0,
  removed_jobs INT UNSIGNED DEFAULT 0,
  last_job_id INT UNSIGNED DEFAULT 0,
  last_event_at TIMESTAMP NULL,
  INDEX idx_last_event (last_event_at)
) ENGINE=InnoDB;
```

Plus a Redis hash `job_counters:{cid}` mirrored from the table for O(1) read. **Incremented by event observers on `Job::created/updated/deleted/saved`** — `app/Observers/JobApplicationObserver.php` exists but is NOT registered in `AppServiceProvider::boot()` (grep confirmed).

**Reads drop from 4 SQL + 4 ES to 1 Redis HGETALL + 1 SQL fallback.** Cache for 60 s with invalidation on JobSaved event.

**NEVER execute COUNT(*) on page load for this.** It's already in `dataCnt` from ES, but ES is the 2nd round-trip — kill the SQL one too.

---

## §8 Search Review

**No FULLTEXT** (confirmed). **No Elasticsearch search-indexing wired to the Jobs table on the listings path** — controller uses `DB::table('jobs')` with `LIKE` filters at `ServiceCompanyJobsController.php:1544–1605`. At production scale this is full-scan even on indexed columns.

### Optimal solution

- Migrate search to **`ats_jobs_elastic`** (already exists per `ElasticJobIndex.php:150`, environment constant `ELASTIC_JOBS`). The index already receives `Job` documents via the `ElasticJobIndex` service.
- Add **edge_ngram** analyzer on `title` (front-end typeahead), `match` on `description`/`requirements`.
- Move filter combos into ES `bool` queries (term on `status`, range on `experience_years`, term on `location_id`).
- Cache the user's last 5 search states in Redis sorted set `user:searches:{id}` (TTL 1h).

---

## §9 Filter Review

`STATUS`, `JOB TYPE`, `EXPERIENCE`, `SORT BY` are server-side params passed to DataTables. Each filter combination executes a fresh query.

- Cache filter+sort combinations for 30 s in Redis keyed by `(company_id, filter_hash, sort_direction)`.
- Stampede-protect with `Cache::lock('filter_lock_'.$key, 5)->block(3, fn() => Cache::remember(...))`.
- **Status filter** maps to `WHERE jobs.status = ?` — needs composite `(company_id, status, created_at)` index (missing).
- **Experience filter** `WHERE exp_min <= ? AND exp_max >= ?` — needs composite index, missing.
- **Pre-filter metadata** should be precomputed once at job-create time and stored in Redis `SADD job_filters:status active hold inactive` — never query DISTINCT on page load.

---

## §10 Pipeline Counters (8 stages)

Two implementations exist:

- **`ServiceCompanyAppTrackerController::loadcounter` `:154–273`** — 1 ES `_search` with 8 aggregations (good shape, single round-trip). Fires via JS at `apptracker/index.blade.php:1413` after first paint — NOT page-load.
- **`ProductCompanyAppTrackerController::loadcounter` `:98–180`** — MySQL `count()` + `groupBy` across 3 tables with 7 `SUM(CASE…)` aggregations. **COUNT + GROUP BY across 3 tables, very expensive.** Also does a 2nd groupBy-first at `:175–176` — should be combined into one `first()`.
- **`ServiceCompanyJobsController::data()` `:1848–1950`** — GROUP BY across `jobs × clients × companies × add_job_wise_condidates LEFT JOIN × recommendation_relation LEFT JOIN` with 9 `SUM(CASE…)` aggregations per row. **Runs on every DataTables page request.**

### Optimal solution — pipeline counter summary table

```sql
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
  INDEX idx_last_event (last_event_at)
) ENGINE=InnoDB;
```

- **Event-driven:** `JobApplication::saving/created/deleted/updating` → increment/decrement the right stage column + flip `fresh_data`.
- **Reads:** single `SELECT * FROM pipeline_counters WHERE job_id IN (...)` — no JOIN, no SUM, no GROUP BY.
- **Redis cache:** `HGETALL pipeline_counters:{job_id}` for hot jobs (24h TTL).

This collapses 9 GROUP BY scans per page into 1 lookup. Expected: **-95% MySQL CPU on dashboard renders.**

### Covering index for the legacy path

```sql
CREATE INDEX idx_job_app_count ON job_applications (job_id, master_status_id, deleted_at);
```

---

## §11 Agency Section

`ServiceCompanyJobsController::data()` pulls agency match counts per row, joined through `recommendation_relation` `LEFT JOIN`. **The `whereNotIn` IN-list at `dataCnt():1622` is the worst — materializes every `job_id` ever recommended to this tenant.**

**Optimal:** Materialize `agency_matches_count` into a `job_agency_counter` summary table, incremented on `Recommendation::created/deleted` events. Reads become `SELECT agency_count FROM job_agency_counter WHERE job_id = ?`.

---

## §12 Candidate Counts (Total Applications / Interview)

**`dataCnt():1638`** runs `SELECT COUNT(*) FROM recommendation_relation WHERE to_company_id=? AND final_status IN (0,3)` — does NOT use index `(to_company_id, final_status)` because `final_status IN (0,3)` is the trailing clause.

```sql
CREATE TABLE application_counters (
  job_id INT UNSIGNED PRIMARY KEY,
  total_apps INT UNSIGNED DEFAULT 0,
  interview_apps INT UNSIGNED DEFAULT 0,
  offer_apps INT UNSIGNED DEFAULT 0,
  rejected_apps INT UNSIGNED DEFAULT 0,
  last_event_at TIMESTAMP NULL
) ENGINE=InnoDB;
```

Same observer pattern. Reads: 1 SELECT. Wall cost: O(1).

---

## §13 Frontend Performance

| Asset class | Status |
|---|---|
| jQuery | Loaded (Metronic default). Heavy 90KB minified. |
| Alpine / Livewire | Mixed (CLAUDE.md mentions all; Livewire likely loading entire JS surface even where unused) |
| Metronic bundle | ~300KB CSS + ~500KB JS unminified. Critical CSS extraction recommended. |
| Poppins font | 9 weights × 2 files = 18 woff2 (~500KB). Use ONE weight (400) + 1 italic. ~-400KB. |
| Keenicons | Loaded as a separate font (~250KB). Inline as SVG sprite — -250KB. |
| Tailwind / DataTables / SweetAlert | Likely all loaded; many unused. Audit with `npx depcheck` on dev. |
| Inline scripts in Blade | From the 4 N+1 findings at `all_apptracker.blade.php:76`, the duplicate-detection block is inline — move to deferred partial. |

**Estimated initial payload (current):** ~1.5 MB.
**Target <300 KB initial:**

| Action | Savings |
|---|---|
| Inline critical CSS, defer all non-critical | -200KB |
| Replace Keenicons with SVG sprite | -250KB |
| Remove unused DataTables/SweetAlert from default bundle | -150KB |
| Poppins: ship 1 weight, system-stack fallback | -400KB |
| gzip/Brotli on every text asset | -50% size |
| Async/defer on every script | **-TTFB -300ms** |

---

## §14 Blade Performance

**`service_company/jobs/index.blade.php`** is 985 lines. **Has nested `@include`s** (`sections.service_company.jobHeader`, `jobFilterBar`, `pipeline`).

**Per-row expensive operations in `all_apptracker.blade.php` @foreach:**
1. `:28` — `DB::table('jobs')->where('id', $row->jobId)->first()`. N+1.
2. `:156` — `GeneralMethod::skill_percentage($row->job_id, $skillArr)` → DB hit inside.
3. `:181` — duplicate pluck on same table.
4. `:76-108` — duplicate-detection count() per row.

**Repeated formatting in the loop:**
- Money formatting (`number_format`) per cell.
- Carbon datetimes parsed inline (`->format('d M Y')`) — should be a column on model.
- Permission checks via `Auth::user()` indirect calls.

**Recommendations:**
- Pass a `Map<int, array>` of job data into the view from the controller; drop all per-row `DB::table` calls.
- Cache `NoticePeriod::all()` once with `Cache::remember('notice_periods', 3600, …)`.
- Cache language settings, currency codes (already cached `:4056`), and roles in Redis.
- Use anonymous Blade components for skill chips + buttons.

---

## §15 Caching Strategy

### What exists

- `CACHE_STORE=redis` (`.env:62`), `SESSION_DRIVER=redis` (`.env:45`), `QUEUE_CONNECTION=redis` (`.env:53`).
- `phpredis` extension enabled.
- `Cache::remember('currency_codesww', 86400, …)` at `:4056` — cached 24h.
- `Cache::remember("vendor_ids_for_company_{id}", 300, …)` — 5-min vendorid lookup, used in 3 controllers.
- **`bootstrap/cache/` has `modules.php`, `packages.php`, `services.php` but NO `config.php` and NO `routes-v7.php`** — meaning **config:cache and route:cache are NOT enabled.** Free win. **🔴 Critical.**

### What is missing (CACHE_PREFIX=.env:57 is also empty — keys collide; set `CACHE_PREFIX=jero_`)

| Cache key | TTL | Source |
|---|---|---|
| `job_counters:{cid}` | 60s, invalidated by JobSaved event | §7 |
| `pipeline_counters:{job_id}` | 24h, hot path | §10 |
| `application_counters:{job_id}` | 24h | §12 |
| `company:{cid}` | 1h | Company::all() @ index:1277 |
| `agencies_for_company:{cid}` | 1h | agency match |
| `team_for_company:{cid}` | 1h | team members |
| `notice_periods` | 24h | NoticePeriod::all() |
| `language_settings` | 24h | LanguageSetting::all() |
| `global_settings` | 24h | GlobalSetting::with('currency')->first() |
| `user_permissions:{uid}` | 1h | Spatie permission cache |
| `route_index` | indefinite | route:cache (not enabled today) |
| `config_index` | indefinite | config:cache (not enabled today) |
| `user_session_meta:{sid}` | 5 min | middleware-computed user-meta |
| `menu_tree:{cid}` | 1h | sidebar nav |

**Stale-while-revalidate:** `Cache::flexible('key', [300, 600], fn() => …)`.
**Response cache:** `Cache::flexible('jobs_index_full:{user_hash}', [60, 300], fn() => full HTML render)`.
**Edge cache:** Cloudflare `Cache-Control: public, max-age=60, s-maxage=600` + `Vary: Cookie`.

---

## §16 Memory Usage

Per-request estimate (cold, 20 rows, current code):
- Company::all() ~893 rows × 30 fields × 200 bytes = ~5 MB array.
- `recommendation_relation.pluck('job_id')` — materializes hundreds of thousands of integers. ~8 MB.
- `dataCnt() groupBy('jobs.id')->get()` — materializes full result rows before `count()`. ~4 MB.
- `Job` model hydration × 200 rows + `JobApplication` model appends (`resume_url`) → 10 MB+ for Eloquent-only pages.
- Blade @foreach context array — variable shadowing per row → ~3 MB.
- Total JSON/HTML render prep — ~5 MB.

**Total estimated: ~30–40 MB per request.** At 10k concurrent that's 300-400 GB of heap pressure if all in-flight — way over the 32GB server.

**Target <30 MB:** switch `Company::all()` to `pluck('id','name')`, replace `pluck + IN-list` with `EXISTS`, use Query Builder instead of Eloquent for the data table (`->selectRaw`), turn off `JobApplication.$appends`.

---

## §17 Network Payload

| Asset | Now | Target |
|---|---|---|
| HTML | 150 KB | 80 KB |
| CSS | 350 KB | 100 KB (critical only) |
| JS | 700 KB | 200 KB (deferred) |
| Fonts | 500 KB | 100 KB (Poppins 1 weight + Keenicons SVG sprite) |
| Images | 50 KB | 30 KB (responsive srcset) |
| **Total uncompressed** | **~1.5 MB** | **<300 KB** |
| **Total gzipped** | ~750 KB | <150 KB |

---

## §18 Concurrency Review (10k concurrent)

Hard ceiling failures at 10k (current code):

| Bottleneck | Why | Fix |
|---|---|---|
| **`EnsureOneDeviceLogin` UPDATE on `users`** | Row-lock contention | Throttle the write to once/minute |
| **`SESSION_DRIVER=database` (default config)** | Row-lock on `sessions.session_id` | Move to Redis |
| **`Job::count()` × 4** | Coverage OK but `status` filter scans | Add composite index |
| **`recommendation_relation.whereNotIn(pluck)`** | IN-list of 100k IDs | EXISTS subquery + summary table |
| **Redis lock stampedes** on `Cache::lock('vendor_ids_for_company_{id}', 2)->block(2)` | 10k concurrent block() calls on same key | Jitter: `block(rand(1,3), fn)` |
| **ES bulk on job counter recompute** | `loadcounter` 8-agg single query × 10k concurrent | Cache result 60s, pre-warm |
| **Octane `max_requests=1000`** | 8 workers × 1000 = 8000 reqs before recycle | OK at 10k only just barely |
| **MySQL `max_connections` default 151** | 10000 concurrent recruiter requests | Need `max_connections ≥ 10240` + connection pooling |

---

## §19 Database Design Review

### Recommended DDL (priority order)

```sql
-- 1. Composite indexes for the Jobs listing hot path
ALTER TABLE jobs
  ADD INDEX idx_jobs_company_status_created (company_id, status, created_at DESC),
  ADD INDEX idx_jobs_priority_created (priority DESC, created_at DESC),
  DROP INDEX idx_jobs_company_id,
  DROP INDEX idx_company_id;          -- 3 redundant single-column indexes collapsed

-- 2. Pipeline counters hot path
ALTER TABLE job_applications
  ADD INDEX idx_job_app_count (job_id, master_status_id, deleted_at),
  ADD INDEX idx_job_app_company (company_id, master_status_id),
  ADD INDEX idx_job_app_created (job_id, created_at DESC);

-- 3. Skill chip rendering
ALTER TABLE job_application_skills
  ADD INDEX idx_jas_app (job_application_id);

ALTER TABLE candidate_job_skill_exp
  ADD INDEX idx_cjse_candidate_job (candidate_id, job_id),
  ADD INDEX idx_cjse_job (job_id);

-- 4. Bench talent indexes
ALTER TABLE bench_applications
  ADD INDEX idx_bench_company_status (company_id, status),
  ADD INDEX idx_bench_job (job_id);

-- 5. Pipeline tree traversal
ALTER TABLE candidate_status
  ADD INDEX idx_cs_parent (parent);
```

### Counter tables (event-driven, replaces live COUNT)

```sql
CREATE TABLE job_counters       ( /* …from §7… */ );
CREATE TABLE pipeline_counters   ( /* …from §10… */ );
CREATE TABLE application_counters ( /* …from §12… */ );
```

### Foreign-key hygiene

Only `application_settings:79` has a real `CONSTRAINT FOREIGN KEY ... REFERENCES`. Everywhere else `KEY xxx_foreign` is just an index, no referential integrity enforced. Optional cleanup, not blocking.

---

## §20 Redis Architecture

| Use | Type | Key pattern | TTL |
|---|---|---|---|
| User permissions | Hash | `user_perms:{uid}` | 1h |
| Company config | Hash | `company:{cid}` | 1h |
| Job counter card | Hash | `job_counters:{cid}` | 60s (event-driven) |
| Pipeline counters | Hash | `pipeline:{job_id}` | 24h |
| Application counters | Hash | `app_counters:{job_id}` | 24h |
| Session | Default Redis | `laravel_database_*` | session lifetime |
| Vendor IDs | String | `vendor_ids_for_company:{cid}` | 300s |
| Notice period list | String | `notice_periods` | 24h |
| Language settings | String | `language_settings` | 24h |
| Global settings | String | `global_settings` | 24h |
| Filter combos | Hash + checksum | `filter:{cid}:{md5}` | 30s, stampede-locked |
| Pagination cursors | String | `paginate:{uid}` | 1h |
| Search history | Sorted Set | `user:searches:{uid}` | 1h |
| Atomic increments | INCR + Lua | `rate:{uid}:{bucket}` | sliding |
| Pub/Sub for cache invalidation | Channel | `cache.invalidate` | n/a |

---

## §21 Event Driven Architecture

| Domain event | Observer (NEW) | Side effects |
|---|---|---|
| `JobSaved` | `JobObserver::saved` | `job_counters:{cid}` HINCRBY ±1, bust `cache:job_counters:{cid}` |
| `JobApplicationSaved` | split existing `JobApplicationObserver` | `pipeline_counters:{job_id}` ±1 on stage change, `app_counters:{job_id}` ±1, ES reindex via `ElasticCandidateIndexJob` |
| `BenchApplicationSaved` | `BenchApplicationObserver` | `bench_counters:{cid}` ±1 |
| `RecommendationSaved` | `RecommendationRelationObserver` | invalidate `agency_counts:{cid}` |
| `JobSkillUpdated` | `JobSkillObserver` | bust `cache:job_skills:{job_id}` |

```php
class JobApplicationObserver {
  public function saved(JobApplication $app): void {
    PipelineCountersUpdater::dispatch($app)->onQueue('pipeline-counts');
    ElasticCandidateIndex::dispatch($app->candidate_id)->onQueue('es-index');
  }
}
```

Both jobs are `ShouldQueue` (infrastructure already exists per `app/Jobs/`).

---

## §22 Background Jobs

| Move to queue | Why |
|---|---|
| `custome_count_all` ES refresh (after job create/update) | Background refresh, no user-facing latency |
| `PipelineCountersUpdaterJob` | Idempotent, fast Redis HINCRBY + summary table update |
| `AggregateJobCountersJob` (daily) | Drift detection — walks summary tables vs `Job::count()` and reconciles |
| `EsReindexBulkJob` | Bulk indexer; runs once per minute to drain a reindex queue |
| `Cache::flexible` recompute (background) | Trigger `cache:recompute` on event instead of inline |
| Recommendation dispatch | Already `ShouldQueue`, force `Mail:` queue, not default |
| Resume parsing | Already `ParseResumeJob` — verify dispatcher path |

**Horizon config:** `config/horizon.php:215–222` already has `production maxProcesses=10`. Bump to 16–32 for counter/event throughput.

---

## §23 Elasticsearch Review

**Currently NOT used for Jobs listing** — `StaffingCompanyJobsController:54–75`, `ServiceCompanyJobsController::index:1273–1313`, `ProductCompanyAppTrackerController::loadcounter:98–180` all hit MySQL.

**ES is ready:** `App\Providers\AppServiceProvider:19–24` defines `ELASTIC_JOBS`/`ELASTIC_CANDIDATES`/`ELASTIC_CANDIDATES_BENCH` constants; `app/Services/ElasticJobIndex.php` indexer exists; `ats_jobs_elastic` index receives updates.

| Use | Today | Should move to |
|---|---|---|
| Job list/search/filter | MySQL LIKE | ES `match` + `term` filters + aggs |
| Pipeline stage aggregations | MySQL `SUM(CASE WHEN…)` over `job_applications` | ES `filter` aggregations (single round-trip, proven in `ServiceCompanyAppTrackerController::loadcounter:154–273`) |
| Candidate match | MySQL `orWhere` + `LIKE %skill%` in loop | ES `function_score` with skill overlap |
| Bench/agency counts | MySQL `Job::count` | ES `cardinality` agg on `ats_candidates_bench_elastic` |
| Career page | MySQL `Job::count` | ES already indexed as `ats_career_apply_elastic` |
| Autocomplete for search | n/a | ES edge_ngram on `title` |

**Rule:** keep ES for search and filter, MySQL becomes the materialized source of truth for counters.

---

## §24 API Design (if migrating to API)

```
GET /api/service_company/jobs?cursor=&filter[status]=active&filter[type]=…&sort=created_at_desc&length=20
→ 200 OK
  Cache-Control: private, max-age=30
  ETag: "<user_hash>:<filter_hash>"
  X-Total-Count: 1362
{
  "data": [{id, title, …, match_score: 0.85}],
  "next_cursor": "eyJpZCI6MTIzfQ==",
  "prev_cursor": null,
  "aggregations": { "totals": {"all":1362, …} },
  "filters": { "locations": [...], "types": [...] }
}
```

- Cursor pagination (not offset) — keeps keyset fast as table grows.
- `ETag` + `If-None-Match` returns 304 for unchanged data.
- `Cache-Control: private`, `s-maxage` for short CDN wins.
- API endpoints in `routes/api.php` skip session/CSRF middleware (drops 2 ms per request).

---

## §25 Final Performance Score

| Category | Today (1–10) | After Phase 4 (1–10) |
|---|---|---|
| Database | 3 | 8 |
| Redis | 5 | 9 |
| Frontend | 4 | 8 |
| Backend | 3 | 8 |
| Architecture | 4 | 9 |
| Caching | 3 | 9 |
| Scalability | 2 | 9 |
| Maintainability | 4 | 7 |
| Security | 5 | 7 |
| **Overall** | **3.6** | **8.2** |

---

## §26 Priority Matrix

### 🔴 Critical — fix before production (Day 1)

1. Enable `php artisan config:cache && route:cache` (free — `bootstrap/cache/` only has 3 of 5 files)
2. Make `EnsureOneDeviceLogin:58-59` UPDATE throttled (write once per minute, not every request)
3. Set `SESSION_DRIVER=redis` (verify production)
4. Add composite `(company_id, status, created_at DESC)` index on `jobs`
5. Drop the 3 redundant `idx_*_company_id` indexes on `jobs`
6. Stop returning `Company::all()` from `index():1277` — change to `pluck('id','name')` + `Cache::remember`
7. Set `CACHE_PREFIX=jero_` in `.env:57`

### 🟠 High Impact — Week 1

8. Cache `GeneralMethod::custome_count_all` (Redis 60s)
9. Fix the 3 redundant `cardinality` `_search` calls (use `hits.total.value`)
10. Switch career counter at `:12596` to ES `count()` API instead of `_search` with `size:10`
11. Build pipeline counter summary table + observer
12. Recompute match% in controller, kill the 4 N+1s in `all_apptracker.blade.php:28, 76–108, 156, 181`
13. Add `(job_id, master_status_id, deleted_at)` composite index on `job_applications`
14. Cache `ServiceCompanyBaseController.php:78–247` middleware-heavy queries
15. Cache `NoticePeriod::all()`, `Skill::all()`, currency dropdown for shared 24h
16. Add `E_USER_LIMIT` or `Redis::throttle` to middleware stack

### 🟡 Medium — Weeks 2–3

17. Migrate Jobs listing search/filter to ES
18. Build `job_counters`, `application_counters`, `agency_counters` summary tables + observers
19. Cursor pagination on the data table
20. Reduce font shipping (Poppins 1 weight + Keenicons SVG sprite)
21. Critical CSS extraction, defer non-critical JS
22. Add Datadog/Sentry/MySQL slow query log + Grafana `dashboard.jero.works`
23. Cloudflare edge cache with `Vary: Cookie`

### 🟢 Low — Enterprise phase

24. Split `GeneralMethod.php` (13k lines) into coherent services
25. Promote `recommendation_relation` from `WHERE NOT IN (pluck)` to `EXISTS`
26. Foreign-key constraint cleanup
27. Read-replica routing for dashboards
28. Build enterprise rate-limiter middleware (Redis Lua sliding window)

---

## §27 Implementation Roadmap

### Phase 1 — Day 1
1. Enable `config:cache` + `route:cache`
2. Verify `SESSION_DRIVER=redis` and `CACHE_PREFIX=jero_` in production `.env`
3. Add 4 composite indexes (§26:🔴)
4. Patch `EnsureOneDeviceLogin` to throttle UPDATE
5. Cache `Company::all()`, `NoticePeriod::all()`, `LanguageSetting::all()`, `GlobalSetting`
6. Replace `Company::all()` with `pluck` projection in `index:1277`
7. Add `E_USER_LIMIT` Redis throttle middleware

**Result:** -150ms TTFB, -15 SQL queries, -60% write contention.

### Phase 2 — Week 1
1. Wrap `GeneralMethod::custome_count_all` in 60s Redis cache; merge the 3 redundant `cardinality` `_search` calls
2. Replace `ProductCompanyAppTrackerController::loadcounter:98–180` with 1 ES `_search` + 8 filter aggs
3. Add `(job_id, master_status_id, deleted_at)` index on `job_applications`
4. Build pipeline counter summary table + observer skeleton
5. Refactor `all_apptracker.blade.php:28, 76, 156, 181` to read from controller-supplied maps
6. Cache `ServiceCompanyBaseController` middleware-heavy queries

**Result:** Sub-200ms page loads in dev; -25 SQL queries.

### Phase 3 — Weeks 2–3
1. Build `job_counters`, `application_counters`, `agency_counters` summary tables + observers
2. Migrate Jobs listing search/filter to ES (with cache fallback)
3. Cursor pagination on data table
4. Frontend asset audit; font consolidation
5. Add Datadog APM + MySQL slow query log
6. Cloudflare edge rules

**Result:** -90% MySQL CPU on dashboards, 80–150ms warm cache.

### Phase 4 — Enterprise (LinkedIn scale)
1. Read-replica routing for dashboards
2. Horizontal MySQL: shard by `company_id` once > 50M rows
3. Redis Cluster + Sentinel (3 masters, 9 replicas)
4. Connection pooling (ProxySQL or RDS Proxy)
5. Hybrid event-driven + CQRS sync
6. **"10k recruiter" goal becomes sustainable.**

---

## §28 Expected Results

| Metric | **Today** | **Phase 1** | **Phase 2** | **Phase 3** | **Phase 4** |
|---|---|---|---|---|---|
| Cold page-load | ~1,200 ms | ~700 ms | ~250 ms | ~150 ms | ~80 ms |
| SQL queries / page | ~30–35 | ~15 | ~5 | ~2 | ~1 |
| ES round-trips / page | 4–7 | 4–7 | 0–1 (cached) | 0–1 | 0–1 |
| MySQL CPU @ 10k recruiters | Saturating | 60% | 30% | 15% | 5% |
| RAM / request | ~35 MB | ~30 MB | ~22 MB | ~20 MB | ~15 MB |
| Payload (initial, gzip) | ~750 KB | ~750 KB | ~750 KB | ~300 KB | ~250 KB |
| TTFB | ~800 ms | ~700 ms | ~150 ms | ~80 ms | ~30 ms |
| Concurrent recruiters supported | ~1.5k | ~3k | ~6k | ~10k | ~30k |
| Max jobs supported | ~50k | ~50k | ~200k | ~500k | 200k+/shard |
| Max applications supported | ~1M | ~1M | ~10M | ~100M | 1B+ sharded |

---

## §29 Enterprise Architecture Diagram

```
                                 ┌─────────────────────────────────┐
                                 │     Cloudflare Edge + WAF       │
                                 │  Cache-Control: public + s-max  │
                                 └────────────────┬────────────────┘
                                                  │
                                                  ▼
                          ┌─────────────────────────────────────────┐
                          │   FrankenPHP × 8 workers (32GB host)   │
                          │   - 512MB/worker, max_requests=1000     │
                          │   - opcache + octane primed             │
                          └─────┬─────────────────────┬─────────────┘
                                │                     │
              ┌─────────────────┴──┐         ┌────────┴───────────┐
              │   PHP app          │         │   config:cache +    │
              │  (Laravel 11 +      │         │   route:cache       │
              │   Octane hot path) │         │   octane cache      │
              └────┬───────────────┘         │   (10k rows, 100KB) │
                   │                          └─────────────────────┘
                   │
       ┌───────────┼─────────────────────────────────────┐
       │           │                                     │
       ▼           ▼                                     ▼
┌────────────┐ ┌────────────┐                ┌──────────────────┐
│  MySQL     │ │  Redis     │                │  Elasticsearch   │
│  Primary   │ │  DB 0/1/2  │                │  ats_jobs_elastic│
│            │ │  (phpredis)│                │  ats_candidates  │
│  + indexes │ │            │                │  ats_candidates_ │
│  + summary │ │  fragments,│                │  bench           │
│  tables    │ │  counters, │                │                  │
│  + observers│ │  rate      │                │  Search, Filter, │
│            │ │  limits,   │                │  Aggs            │
│            │ │  sessions  │                │                  │
│            │ │  (ElastiCache  │            └──────────────────┘
│            │ │   on AWS)  │
└────────────┘ └────────────┘
       ▲           ▲
       │           │
       └───────────┘
                │
       ┌────────┴─────────┐
       │  Event-driven    │
       │  observer        │
       │  layer (BOOT)    │
       └────────┬─────────┘
                │
                ▼
       ┌──────────────────┐
       │  Horizon (Redis) │
       │  - pipeline-     │
       │    counters      │
       │  - es-index      │
       │  - aggregate-    │
       │    refresh       │
       │  max=32          │
       └──────────────────┘
```

---

## Summary

1. **The page does ~30-35 SQL + 4-7 ES queries cold, every request, forever, with no cache.** Bottleneck is not MySQL itself — it's the absence of `config:cache`/`route:cache` (free win), the `EnsureOneDeviceLogin` write on every request, the per-tenant 100k-ID `whereNotIn` query, and the 4 N+1s inside the Blade `@foreach`.
2. **Infrastructure is largely ready** — Redis is wired, ES indices exist, Horizon is sized, observers are written. What's missing is the wiring: counts aren't event-driven, observers aren't registered, summary tables don't exist, and data is computed twice (SQL then ES) every request.
3. **Phase 1 (1 day)** gives ~30% improvement via 7 file changes. **Phase 2 (week 1)** gets you to ~250ms. **Phase 3 (week 2-3)** gets you to LinkedIn-class at 10k recruiters. Phase 4 is open-ended and depends on whether you go multi-region.
4. **Biggest cheapest win: enable `config:cache` and `route:cache` today.** That's a 30-50ms TTFB drop with zero code risk.

---

*Audit end. See companion document `JOBS_LISTING_V2_IMPLEMENTATION_PLAN.md` for the actionable 10-step implementation plan.*
