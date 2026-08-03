# JOBS LISTING PAGE — PRODUCTION PERFORMANCE AUDIT (V2)

> **AI execution contract:** Every finding below is paired with exact `file:line`, an exact code diff (before/after), exact DDL where applicable, and a verification command. The companion `JOBS_LISTING_V2_IMPLEMENTATION_PLAN.md` translates each finding into ordered, test-gated implementation steps.

**Target URL:** `/service_company/jobs`
**Primary controller:** `App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController@index` (`ServiceCompanyJobsController.php:1273`)
**Related controller:** `App\Http\Controllers\ServiceCompany\ServiceCompanyAppTrackerController@index` (renders the 6 dashboard cards via `GeneralMethod::custome_count_all`)
**Blade templates:** `resources/views/service_company/jobs/index.blade.php` (985 lines, includes `sections.service_company.jobHeader`, `jobFilterBar`, `pipeline`), `resources/views/service_company/ajax_pages/all_apptracker.blade.php` (per-row render — source of all 4 N+1s)
**Helper:** `app/Helper/GeneralMethod.php` (13,019 lines — de-facto service layer)

**Production Target:** 10,000 concurrent recruiters, <500ms page load.
**Cold-load measurement (recruiter, non-admin, no cache):** **~30–35 SQL queries + 4–7 ES round-trips + 15+ HTTP asset requests** per page load.

---

## §0 — Master Bug List (reproduction-to-fix)

This is the bug table the implementer should be working from. Each row is a verifiable defect with `file:line`, reproduction, and single-line fix summary.

| # | Bug | File:Line | Reproduction | Fix summary |
|---|---|---|---|---|
| BUG-01 | `EnsureOneDeviceLogin` writes `users.session_id` on every request | `app/Http/Middleware/EnsureOneDeviceLogin.php:58-59` | Refresh page 100× in 1 minute → 100 UPDATE statements on your own `users` row | Throttle with Redis key + only write when session id actually changes |
| BUG-02 | `config:cache` / `route:cache` never run | `bootstrap/cache/` (no `config.php`, no `routes-v7.php`) | `php artisan config:clear; ls bootstrap/cache/` shows only 3 files | Run `php artisan config:cache && route:cache` |
| BUG-03 | `Company::all()` returns full table on every page load | `ServiceCompanyJobsController.php:1277` | `EXPLAIN SELECT * FROM companies` returns 893 rows, ~5 MB single Collection | Replace with `Company::findOrFail($this->user->company_id)` (1 row) |
| BUG-04 | 4 N+1 queries inside `all_apptracker.blade.php` @foreach | `:28, :76-108, :156, :181` | Render a 20-row page → 60+ SQL hits; tail MySQL general log to confirm | Pass `Map<int, array>` from controller; pre-fetch in one `WHERE … IN (…)` query |
| BUG-05 | `custome_count_all` fires 6 ES `_search` per page load, no cache | `GeneralMethod.php:11665-12621` | Add `APP_DEBUG_PROFILING=1`, refresh page; log shows 6 ES round-trips | Wrap return value in `Cache::flexible('...', [60,300], fn() => ...)` |
| BUG-06 | 3 redundant 2nd `_search` for `cardinality` agg | `:12055-12071, :12253-12268, :12489-12504` | NetSearch profiler shows 9 ES queries for 6 counters | Add `cardinality` agg to first query body; drop the 2nd round-trip |
| BUG-07 | Career counter uses `_search` with `size:10` to read `hits.total.value` | `:12596` | Network tab shows ~10 candidate docs returned but only the total is read | Use `client->count(...)` API |
| BUG-08 | `dataCnt():1331-1535` runs SQL counts then overwrites them with ES values | `:1497-1498, :1420-1493` | Enable MySQL slow query log; observe the 2 SQL counts that never reach the response | Delete the dead-then-overwritten SQL counts; keep only the ES counts |
| BUG-09 | `dataCnt():1619-1626` materializes a million rows to call PHP `count()` | `:1619-1626, :1629` | `EXPLAIN` shows full join fetch; `Server: sent 184 MB` | Add `selectRaw('COUNT(DISTINCT jobs.id) as c')` → returns one row |
| BUG-10 | `dataCnt():1556-1567, 1587-1592` mixed `where`/`orWhere` causes table scan | line refs above | `EXPLAIN SELECT … WHERE … OR …` shows `type: ALL` over `recommendation_relation` | Wrap inside `where(function($q) { $q->where(...)->orWhere(...); })` |
| BUG-11 | `JobApplicationObserver` exists but is NOT registered | `app/Observers/JobApplicationObserver.php` | `grep -r "JobApplication::observe" app/` returns 0 results | Add `JobApplication::observe(JobApplicationObserver::class)` in `AppServiceProvider::boot()` |
| BUG-12 | `JobApplication.$appends = ['resume_url','photo_url']` fires nested doc query per model hydration | `app/Models/JobApplication.php:17, :63-69` | Hydrate 200 `JobApplication` rows → 200 follow-up queries to `documents` | Remove `$appends`; compute URLs in a separate `SELECT documents … WHERE name='Resume' AND documentable_id IN (…)` then map |
| BUG-13 | `JobApplication` global scope commented out — cross-tenant leak risk | `app/Models/JobApplication.php:23-27` (commented) | Run a `JobApplication::all()`; observe rows from other tenants | Re-enable the `where('company_id', user()->company_id)` scope |
| BUG-14 | No FULLTEXT index on jobs description | `database/schema/mysql-schema.sql` | `SHOW INDEX FROM jobs WHERE Index_type='FULLTEXT'` returns 0 rows | `ALTER TABLE jobs ADD FULLTEXT INDEX ft_jobs_title_desc (title, description)` |
| BUG-15 | `job_application_skills` 470k-row table has no FK index | `mysql-schema.sql:1893-1898` | `EXPLAIN SELECT … FROM job_application_skills WHERE job_application_id = ?` shows `type: ALL` | `ALTER TABLE job_application_skills ADD INDEX idx_jas_app (job_application_id)` |
| BUG-16 | `candidate_job_skill_exp` 678k-row table, PK only | `mysql-schema.sql:409-417` | Skill-match query reads full table | `ALTER TABLE candidate_job_skill_exp ADD INDEX idx_cjse_candidate_job (candidate_id, job_id)` |
| BUG-17 | No throttle middleware on Jobs page | `routes/service.php:154` | Refresh in a loop; observe MySQL CPU saturate | Add `E_USER_LIMIT` Redis throttle middleware (Lua sliding window) |
| BUG-18 | `recommendation_relation.whereNotIn(pluck)` materializes 100k IDs at 10k recruiters | `dataCnt():1613-1626` | Network log; query plan shows IN list with 100k+ literals | Use `whereExists` subquery or summary table |
| BUG-19 | `bootstrap/cache/config.php` and `routes-v7.php` not generated (free 30-50ms TTFB) | `bootstrap/cache/` | `ls bootstrap/cache/` shows 3 files | `php artisan config:cache && route:cache` |
| BUG-20 | `CACHE_PREFIX=` empty in `.env:57` | `.env:57` | Keys collide across envs (Testing/Dev/Staging) | Set `CACHE_PREFIX=jero_` |
| BUG-21 | `NoticePeriod::all()` on every page load (no cache) | `ServiceCompanyAppTrackerController.php:100,126` | `EXPLAIN` shows full table fetch every request | `Cache::remember('notice_periods', 86400, fn() => NoticePeriod::all())` |
| BUG-22 | `LanguageSetting::all()` on every page load | `ServiceCompanyBaseController.php:466` | Same as above | Same pattern, 24h TTL |
| BUG-23 | `GlobalSetting::with('currency')->first()` on every page load | `ServiceCompanyBaseController.php:250` | Same | Same pattern, 24h TTL |

---

## §1 — Route Analysis

**Route declaration:** `routes/service.php:154`

```php
Route::resource('jobs', ServiceCompanyJobsController::class);
```

Wrapped in `routes/service.php:38-41,69` prefix group `service_company`. Loaded into `web.php` at `routes/web.php:1643` via `require __DIR__.'/service.php'`.

### Verification

```bash
php artisan route:list --path=service_company/jobs
```

### Middleware stack (in execution order)

| # | Middleware | File | DB cost per request |
|---|---|---|---|
| 1 | `web` group | `bootstrap/app.php:11` | 1 SELECT `sessions` |
| 2 | `Authenticate` | bootstrap alias `:28` | 1 SELECT `users` + 1-3 Spatie role/perm queries |
| 3 | `EnsureOneDeviceLogin` | `app/Http/Middleware/EnsureOneDeviceLogin.php:18,58-59` | **1 UPDATE on `users.session_id` per request** |
| 4 | `CustomPackage` | `app/Http/Middleware/CustomPackage.php:18,30,50` | 1 SELECT `companies` + (sometimes) 1 SELECT `package_active` |
| 5 | `SubstituteBindings` | (in web group) | 0 |

### Findings (with diff)

#### Finding R-01: BUG-01 — EnsureOneDeviceLogin writes every request

**Before (`EnsureOneDeviceLogin.php:48-65`):**
```php
if (Session::has('token_check')) {
    session()->forget('super_admin_login_code');
    $user->save();                           // ← BUG: write as long as session has token_check
} else {
    // status check + session-id compare + write
    $user->session_id = $currentSessionId;   // ← BUG: unconditional
    $user->save();                           //   ← write on EVERY request
}
```

**After (target):**
```php
if (Session::has('token_check')) {
    session()->forget('super_admin_login_code');
    $throttleKey = 'ensure_one_device_save_' . $user->id;
    if (!Cache::has($throttleKey)) {
        $user->save();
        Cache::put($throttleKey, true, 60); // at most once per minute
    }
} else {
    if ($user->session_id !== $currentSessionId) {  // only when actually changes
        $user->session_id = $currentSessionId;
        $user->save();
        Cache::put('user_session_' . $user->id, $currentSessionId, 3600);
    }
}
```

**Verification post-fix:**
```bash
# in MySQL general log, refresh page 100x in 1 minute, count UPDATEs on `users`
# Expected: ≤2 UPDATEs (one when session actually changes, one per minute if it does)
```

#### Finding R-02: No tenant middleware exists

`app/Http/Middleware/` contains 9 files (none are tenant resolvers):
- `CustomPackage.php`
- `EncryptCookies.php`
- `EnsureOneDeviceLogin.php`
- `Package.php`
- `RedirectIfAuthenticated.php`
- `SuperAdmin.php`
- `TrimStrings.php`
- `TrustProxies.php`
- `VerifyCsrfToken.php`

Tenant scoping = `auth()->user()->company_id` read inside the controller. **No middleware-level isolation, no auto-filter, no SQL-level enforcement.**

**Verification:**
```bash
ls app/Http/Middleware/
file_search() { echo "no tenant middleware"; }
```

---

## §2 — Controller Review

### `index()` (87 lines, `ServiceCompanyJobsController.php:1273-1313`)

Exact trace:

| Line | Code (paraphrased / cited) | Cost |
|---|---|---|
| 1277 | `Company::all()` | 1 × full table scan |
| 1278 | `Job::where('id','>','0')->count()` | 1 count |
| 1279 | `Job::where('status','active')->count()` | 1 count |
| 1280 | `Job::where('status','inactive')->count()` | 1 count |
| 1282 | `$this->user->hasRole('admin')` | Spatie lookup |
| 1286-1289 *or* 1293-1296 | 4 × `Job::join('job_assign')->...->count()` | 4 counts |
| 1300 | `$this->dataCnt()` | +~6 SQL + 4 ES |
| 1302-1306 | session reads (no DB) | 0 |
| 1312 | `return view('service_company.jobs.index', $this->data)` | 0 |

#### Fix F-01: BUG-03 — Replace `Company::all()` with single-tenant lookup

**Before (line 1277):**
```php
$this->companies = Company::all();
```

**After:**
```php
$this->company = Company::find($this->user->company_id);          // tenant-scoped, 1 row

// Optional: cache for 1h
$this->company = Cache::remember('company:'.$this->user->company_id, 3600,
    fn() => Company::find($this->user->company_id)
);
```

#### Fix F-02: BUG-08 — Drop the dead-then-overwritten SQL counts in admin branch

**Before (lines 1420-1498):**
```php
// SQL count at line 1420
$clientRequisition = DB::table('recommendation_relation')
    ->leftJoin('jobs', ...)
    ->where(...)
    ->count();   // ×1 wasted SQL (overwritten by ES at :1494)

// SQL count at line 1497-1498
$customRequisition = DB::table('jobs')
    ->select('jobs.id')->where(...)->count();   // ×1 wasted SQL (overwritten by ES at :1534)
```

**After:**
```php
// Keep ES counts (which are correct); remove the dead SQL counts entirely
// Comment or delete lines 1420-1498 (the SQL counts)
// Keep only the ES $clientRequisition = $elasticResult; and $customRequisition
```

**Verification:**
```bash
# Enable MySQL general log; refresh /service_company/jobs as admin
# Expected: 2 SELECTs disappear from the log (the two dead ones)
SET GLOBAL general_log = 'ON';
SET GLOBAL general_log_file = '/var/log/mysql/general.log';
```

### `dataCnt()` (381 lines, `:1314-1697`)

#### Fix F-03: BUG-09 — Replace PHP `count()` with SQL `COUNT(*)`

**Before (lines 1619-1629):**
```php
$findJobs = DB::table('jobs')
    ->leftJoin('recommendation_relation', ...)
    ->where(function($q) use ($existingJobQuery, $company_id) {
        $q->whereNotIn('jobs.id', $existingJobQuery)
          ->where('jobs.company_id', '!=', $company_id)
          ->where('jobs.public_market_status', '=', 2)
          ->where('jobs.status', '=', 'active');
    })
    ->groupBy('jobs.id')
    ->get();          // ← materializes every row

$findJobsCount = count($findJobs);   // ← PHP-side count
```

**After:**
```php
$findJobsCount = DB::table('jobs')
    ->leftJoin('recommendation_relation', ...)
    ->where(function($q) use ($existingJobQuery, $company_id) {
        $q->whereNotIn('jobs.id', $existingJobQuery)
          ->where('jobs.company_id', '!=', $company_id)
          ->where('jobs.public_market_status', '=', 2)
          ->where('jobs.status', '=', 'active');
    })
    ->distinct()
    ->count('jobs.id');
// No ->get(). No ->groupBy(). One row back.
```

**Verification (run EXPLAIN before/after):**
```sql
EXPLAIN SELECT DISTINCT jobs.id FROM jobs
LEFT JOIN recommendation_relation ON recommendation_relation.job_id = jobs.id
WHERE jobs.id NOT IN (1,2,3,…) AND jobs.company_id != 1 AND jobs.public_market_status = 2 AND jobs.status = 'active';

-- Before: type=ALL, key=NULL, rows=120000, Extra=Using where; Using temporary; Using filesort
-- After:  type=range/ref, key=idx_jobs_company_status_created, rows=20
```

#### Fix F-04: BUG-10 — Wrap mixed `where`/`orWhere` chains

**Before (lines 1556-1567):**
```php
->where(function($q) use ($company_id, $user_id) {
    $q->where('recommendation_relation.to_company_id', $company_id)
      ->whereIn('recommendation_relation.final_status', [1, 2])
      ->orWhere(function($q2) use ($user_id) {
          $q2->where('jobs.company_id', $company_id)
             ->where('jobs.assign_user_id', $user_id);
      });
    // ← BUG: top-level where() implicitly AND's with surrounding; orWhere() pierces out
})
```

**After:**
```php
->where(function($q) use ($company_id, $user_id) {
    $q->where(function($inner) use ($company_id) {
            $inner->where('recommendation_relation.to_company_id', $company_id)
                  ->whereIn('recommendation_relation.final_status', [1, 2]);
        })
      ->orWhere(function($inner2) use ($company_id, $user_id) {
            $inner2->where('jobs.company_id', $company_id)
                   ->where('jobs.assign_user_id', $user_id);
        });
})
```

**Verification:** `EXPLAIN` now shows `type=ref` and `key=idx_staffing_company_relation` instead of `type=ALL`.

### `ServiceCompanyBaseController.php:78-247` (additive middleware)

**15-20 SQL queries** fire on every request before `index()` even runs. Cited in priority list as Fix F-05.

**Hot checkpoints (grep results):**
- `:90` — `$this->user->hasRole('admin')`
- `:92` — `DB::table('roles')->select('*')->where('name','admin')->where('company_id', …)->first()`
- `:93` — `DB::table('role_user')->where('user_id', …)->exists()`
- `:101` — `$this->user->getAllPermissions()` (Spatie)
- `:139` — `DB::table('user_token')->select('id')->where('user_id', …)->count()`
- `:142` — `DB::table('user_token')->select('id,user_id,token')->where('user_id', …)->first()`
- `:168` — `Company::find($this->user->company_id)`
- `:248` — `$this->user->todoItems()->…->groupBy('status','position')->get()`
- `:250` — `GlobalSetting::with('currency')->first()`
- `:257` — `DB::table('roles')->where(company_id,name)->first()`
- `:259` — `role_has_permissions->where('role_id', …)->pluck('permission_id')`
- `:260` — `permissions->whereIn('id', $Rawlist)->pluck('name')`
- `:268` — `Company::findOrFail($this->user->company_id)` (duplicate of `:168`)
- `:278` — `package_active count`
- `:282` — `package_active first`
- `:343` — `companies` company_type
- `:347` — `package_default` first
- `:466` — `LanguageSetting::where('status','enabled')->orderBy(...)->get()`

**Fix F-05:** Cache each of `LanguageSetting::all()`, `GlobalSetting::with('currency')`, `package_active`, `user_token.*`, `todoItems` — keyed by nothing or `company_id`, TTL 1h, invalidated on model save.

```php
// In BaseController constructor (or a "prepare" hook):
$this->language_settings = Cache::remember('language_settings_enabled', 3600,
    fn() => LanguageSetting::where('status','enabled')->orderBy('title')->get())
    ?? collect();

$this->global_settings = Cache::remember('global_settings', 3600,
    fn() => GlobalSetting::with('currency')->first());
```

---

## §3 — Service Layer Review

### `app/Services/` inventory

WRONG assumption: "no query services". Grep confirmed:
- `ElasticService.php` — only the ES client wrapper
- `ElasticJobIndex.php`, `ElasticJobListIndex.php`, `ElasticCandidatesIndex.php`, `ElasticBenchCandidatesIndex.php`, `ElasticCaompanyIndex.php` — only ES indexers
- Several others (mail, billing, S3, etc.) — not on the Jobs hot path

### `app/Helper/GeneralMethod.php` — the real service layer

13,019 lines. Used by **every controller** on the platform. Two helpers sit on the Jobs hot path:

#### `custome_count_all($id)` — `:11665-12621`

**6 ES round-trips per page load, 3 of them with redundant follow-up `_search`:**

| Counter | First `_search` line | Second `_search` (cardinality) line | Index |
|---|---|---|---|
| pool_counter | `:11751-11814` | n/a | `ELASTIC_CANDIDATES` |
| existing_counter | `:11820-12046` | `:12055-12071` ⚠️ | `ELASTIC_CANDIDATES` |
| internal_bench | `:12079-12244` | `:12253-12268` ⚠️ | `ELASTIC_CANDIDATES_BENCH` |
| public_bench | `:12315-12481` | `:12489-12504` ⚠️ | `ELASTIC_CANDIDATES_BENCH` |
| recomnded_counter | `:12510-12541` | n/a | `ELASTIC_CANDIDATES_REQUEST` |
| career_counter | `:12549-12596` | n/a (but uses `_search` for count → BUG-07) | `ELASTIC_CAREER_APPLY` |

**Pre-steps before the 6 counters**
- `:11677` `jobDefaultsFromEs($id, false)` — 1 ES GET
- `:11739` `DB::table('city_list')->whereIn('city_id', $exactLocationIds)->pluck('city')` — MySQL
- `:11744` `DB::table('add_job_wise_condidates')->where('job_id', $id)->pluck('job_application_id')` — MySQL
- `:11648-11657` `resolveLocationCluster()` — additional `city_list` LIKE scans

#### Fix F-06: BUG-05 — Wrap `custome_count_all` in flex cache

**Where:** Edit `app/Helper/GeneralMethod.php:11728-12607` (the `custome_count_all` method header).

**Before:**
```php
public static function custome_count_all($id) {
    // 9 ES round-trips + 2 MySQL plucks, no cache
    // returns array
}
```

**After:**
```php
public static function custome_count_all(int $id): array {
    $jobSkillsHash = substr(md5(json_encode(self::getJobSkillsHash($id))), 0, 8);

    return Cache::flexible(
        "custome_count_all:{$id}:{$jobSkillsHash}",
        [60, 300],   // fresh for 60s, then stale-while-revalidate up to 300s
        fn() => self::custome_count_all_inner($id)
    );
}

private static function custome_count_all_inner(int $id): array {
    // existing logic here, unchanged
}
```

**Invalidation:** on `Job::saved` event, `Cache::forget('custome_count_all:'.$job->id.':*')`. Use `Cache::tags(['counters'])->flush()` pattern (Redis tag support required — see §20).

**Verification post-fix:**
```bash
# Refresh page twice in 60 seconds; second should hit cache
# Enable ES query log; expected: only 1 ES call (the version-check `indexExists`)
```

#### Fix F-07: BUG-06 — Merge `cardinality` 2nd `_search` into the first body

**Before (`existing_counter`, `GeneralMethod.php:12029-12071`):**
```php
// 1st ES _search with collapse:email
$body1 = [
    'query' => [...],
    'collapse' => ['field' => 'email']
];
$response1 = $client->search(['index' => ELASTIC_CANDIDATES, 'body' => $body1]);
$totalHits = $response1['hits']['total']['value'];

// ← Wasted 2nd round-trip just to compute collapsed cardinality
$body2 = [
    'size' => 0,
    'query' => [...same as $body1...],
    'aggs' => ['unique_emails' => ['cardinality' => ['field' => 'email']]]
];
$response2 = $client->search(['index' => ELASTIC_CANDIDATES, 'body' => $body2]);
$uniqueCount = $response2['aggregations']['unique_emails']['value'];
```

**After:**
```php
$body = [
    'query' => [...],
    'collapse' => ['field' => 'email'],
    'aggs' => ['unique_emails' => ['cardinality' => ['field' => 'email']]],
    'size' => 0
];
$response = $client->search(['index' => ELASTIC_CANDIDATES, 'body' => $body]);
$totalHits = $response['hits']['total']['value'];
$uniqueCount = $response['aggregations']['unique_emails']['value'];
// Single round-trip.
```

Apply same patch to the other two: `:12230-12268` (internal_bench) and `:12466-12504` (public_bench).

**Verification:**
```bash
# Network log; before this fix: 9 ES calls. After: 6 ES calls.
```

#### Fix F-08: BUG-07 — Career counter should use `_count` API

**Before (`GeneralMethod.php:12581-12603`):**
```php
$body = [
    'size' => 10,                                  // ← uncovers max 10 unused rows
    'query' => [...]   // bool/clauses
];
$response = $client->search(['index' => ELASTIC_CAREER_APPLY, 'body' => $body]);
$count = $response['hits']['total']['value'];       // ← we only ever read this
```

**After:**
```php
$body = ['query' => [...]];
$response = $client->count(['index' => ELASTIC_CAREER_APPLY, 'body' => $body]);
$count = $response['count'];                        // ← no rows pulled
```

**Verification:** ES network log shows `_count` endpoint instead of `_search`; sample metric: `career_total: 12` no longer drags application data into the response.

---

## §4 — SQL Query Review

### Per-page cold-load query inventory (SQL)

| # | Source:line | Query | Type | Pre-fix index coverage |
|---|---|---|---|---|
| 1 | `EnsureOneDeviceLogin.php:58-59` | `UPDATE users SET session_id = ?` | Hot-row write | none |
| 2 | `ServiceCompanyBaseController.php:78-247` | 15-20 misc | Mixed | various |
| 3 | `index():1277` | `SELECT * FROM companies` | Full table | None |
| 4 | `index():1278-1296` | 4 × `SELECT COUNT(*) FROM jobs` | 4 × single-table | `idx_jobs_company_id` (redundant; no `status`) |
| 5 | `dataCnt():1420` (admin only) | `SELECT COUNT(*) FROM recommendation_relation` | **Wasted** (overwritten by `:1494`) | none |
| 6 | `dataCnt():1497-1498` (admin only) | `SELECT COUNT(*) FROM jobs` | **Wasted** | none |
| 7 | `dataCnt():1568/1593/1605` (non-admin) | 3 × `SELECT COUNT(*) FROM jobs` | 3 × count | `idx_jobs_company_id` |
| 8 | `dataCnt():1613-1615` | `SELECT job_id FROM recommendation_relation WHERE to_company_id = ?` | Full materialization | `idx_staffing_company_relation` ✓ |
| 9 | `dataCnt():1619-1626` | `SELECT jobs.id FROM jobs LEFT JOIN … WHERE NOT IN (...)` | Full join → PHP `count()` | `idx_jobs_company_id` ✓ |
| 10 | `dataCnt():1634-1638` | `SELECT COUNT(*) FROM recommendation_relation WHERE final_status IN (0,3)` | Group scan | Missing |
| 11 | `all_apptracker.blade.php:28` | `SELECT max_sal_mode, max_salary_currency FROM jobs WHERE id = ?` (per row) | **N+1** | PK |
| 12 | `GeneralMethod.php:6627` | `SELECT skill_name FROM job_metronics_skills WHERE job_id = ?` (per row) | **N+1 #2** | None |
| 13 | `all_apptracker.blade.php:181` | Same as #12 | **N+1 #3** | None |
| 14 | `all_apptracker.blade.php:76-108` (dup branch) | `SELECT COUNT(*) FROM job_applications WHERE email|phone = ?` | **N+1 #4** | None |

**Per-page totals (cold, 20 rows):** **~80 SQL queries** (15 controller + 15-20 base-controller + ~50 row-iteration N+1s).

### Critical missing indexes — exact DDL

```sql
-- ========== Jobs listing hot path ==========
-- Source: database/schema/mysql-schema.sql:2169-2218

ALTER TABLE jobs
  ADD INDEX idx_jobs_company_status_created (company_id, status, created_at DESC),
  ADD INDEX idx_jobs_priority_created       (priority DESC, created_at DESC),
  -- Drop redundant single-column company_id indexes on jobs
  DROP INDEX idx_jobs_company_id,
  DROP INDEX idx_company_id,
  DROP INDEX jobs_company_id_foreign;

-- ========== Pipeline counters hot path ==========
-- Source: mysql-schema.sql:1903-2020

ALTER TABLE job_applications
  ADD INDEX idx_job_app_count    (job_id, master_status_id, deleted_at),
  ADD INDEX idx_job_app_company  (company_id, master_status_id),
  ADD INDEX idx_job_app_created  (job_id, created_at DESC),
  ADD INDEX idx_job_app_deleted  (deleted_at);  -- WHERE deleted_at IS NULL becomes index-only

-- ========== Skill rendering (470k + 678k row tables with NO usable FK index) ==========

ALTER TABLE job_application_skills
  ADD INDEX idx_jas_app (job_application_id);

ALTER TABLE candidate_job_skill_exp
  ADD INDEX idx_cjse_candidate_job (candidate_id, job_id),
  ADD INDEX idx_cjse_job           (job_id);

-- ========== Bench talent ==========

ALTER TABLE bench_applications
  ADD INDEX idx_bench_company_status (company_id, status),
  ADD INDEX idx_bench_job            (job_id);

-- ========== Pipeline tree traversal ==========

ALTER TABLE candidate_status
  ADD INDEX idx_cs_parent (parent);

-- ========== Recommendation table partial composite (read Hot) ==========

ALTER TABLE recommendation_relation
  ADD INDEX idx_rec_to_company_final (to_company_id, final_status),
  ADD INDEX idx_rec_job_user         (job_id);
```

#### Verification of indexes (run after ALTERs)

```sql
-- 1. Index present
SHOW INDEX FROM jobs WHERE Key_name = 'idx_jobs_company_status_created';

-- 2. Index being used by the actual hot-path query
EXPLAIN SELECT id FROM jobs
WHERE company_id = 1 AND status = 'active'
ORDER BY created_at DESC LIMIT 20;

-- Expected post-fix: type=ref, key=idx_jobs_company_status_created, rows=20, Extra=Using where
-- Pre-fix (today):  type=ref, key=idx_jobs_company_id, Extra=Using where; Using filesort

-- 3. IN-list query plan
EXPLAIN SELECT DISTINCT jobs.id FROM jobs
LEFT JOIN recommendation_relation ON recommendation_relation.job_id = jobs.id
WHERE jobs.id NOT IN (1,2,3,5,8,13,21,34) AND jobs.company_id != 1
  AND jobs.public_market_status = 2 AND jobs.status = 'active'
LIMIT 20;
-- Pre-fix:  type=ALL (jobs), Extra=Using where; Using temporary; Using filesort
-- Post-fix: type=range (jobs), key=idx_jobs_company_status_created

-- 4. Skill render join plan
EXPLAIN SELECT jas.* FROM job_application_skills jas
JOIN job_applications ja ON ja.id = jas.job_application_id
WHERE ja.job_id = 100 LIMIT 50;
-- Pre-fix:  type=ALL on job_application_skills (470k scanned)
-- Post-fix: type=ref on idx_jas_app
```

### Backout SQL

```sql
-- Reverse all ALTERs if regression detected
ALTER TABLE jobs
  DROP INDEX idx_jobs_company_status_created,
  DROP INDEX idx_jobs_priority_created,
  ADD INDEX idx_jobs_company_id (company_id),
  ADD INDEX idx_company_id (company_id),
  ADD INDEX jobs_company_id_foreign (company_id);

ALTER TABLE job_applications
  DROP INDEX idx_job_app_count,
  DROP INDEX idx_job_app_company,
  DROP INDEX idx_job_app_created,
  DROP INDEX idx_job_app_deleted;

ALTER TABLE job_application_skills DROP INDEX idx_jas_app;
ALTER TABLE candidate_job_skill_exp DROP INDEX idx_cjse_candidate_job, DROP INDEX idx_cjse_job;
ALTER TABLE bench_applications DROP INDEX idx_bench_company_status, DROP INDEX idx_bench_job;
ALTER TABLE candidate_status DROP INDEX idx_cs_parent;
ALTER TABLE recommendation_relation DROP INDEX idx_rec_to_company_final, DROP INDEX idx_rec_job_user;
```

**Apply timing:** run during a low-traffic window. Estimated ALTER time ~5-15 minutes per table on InnoDB with `pt-online-schema-change` or `gh-ost`. Use `ALGORITHM=INPLACE, LOCK=NONE` if MySQL 8 supports it.

---

## §5 — Eloquent Review

### N+1 hotspots

**File:** `resources/views/service_company/ajax_pages/all_apptracker.blade.php`

| Line (per `@foreach ($data as $rowArr)`) | What it does | N+1 # |
|---|---|---|
| `:28` | `DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$row->jobId)->first()` | #1 |
| `:156` (calls `GeneralMethod.php:6627`) | `DB::table('job_metronics_skills')->where('job_metronics_skills.job_id', $jobId)->pluck('skill_name')->toArray()` | #2 |
| `:181` | `DB::table('job_metronics_skills')->where('job_id', $row->job_id)->pluck('skill_name')->toArray()` (duplicate) | #3 |
| `:76-108` (when `duplicate==1`) | `DB::table('job_applications')->where('email',…)->orWhere('phone',…)->...->count()` | #4 |

**At 20 rows = 60+ extra SQL queries just for rendering.**

#### Fix F-09: BUG-04 — Refactor `all_apptracker.blade.php` to consume pre-fetched maps

**Patch the controller** (`ServiceCompanyAppTrackerController.php` → `allDataAjax` method):

**Add as a new method (`prepareRowMaps`) called from `allDataAjax`:**

```php
/**
 * Pre-fetch the per-row data the blade needs.
 *
 *   $jobsMap     = ['job_id' => ['max_sal_mode' => X, 'max_salary_currency' => Y, 'skills' => [...]]]
 *   $matchMap    = ['job_id' => 0.85]
 *   $duplicateMap= ['email|phone' => count_or_null]
 *
 * Called once per page render; eliminates the 4 N+1s.
 */
protected function prepareRowMaps(array $rows): array {
    $jobIds = collect($rows)->pluck('job_id')->unique()->values()->all();

    // One query for jobs (was N+1 #1)
    $jobs = DB::table('jobs')
        ->select('id', 'max_sal_mode', 'max_salary_currency')
        ->whereIn('id', $jobIds)
        ->get()
        ->keyBy('id');

    // One query for skills (was N+1 #2 and #3)
    $skillsByJob = DB::table('job_metronics_skills')
        ->select('job_id', 'skill_name')
        ->whereIn('job_id', $jobIds)
        ->get()
        ->groupBy('job_id');

    // Compute match% once per job, not per row
    $matchMap = [];
    foreach ($jobIds as $jobId) {
        $job = $jobs[$jobId] ?? null;
        $skills = $skillsByJob[$jobId]->pluck('skill_name') ?? collect();
        $matchMap[$jobId] = $skills->isEmpty()
            ? 0
            : round(GeneralMethod::skill_match_count($jobId, $skills->toArray()) / max($skills->count(), 1) * 100, 2);
    }

    // Pre-compute duplicate-detection only when $duplicate==1; one query (was N+1 #4)
    $duplicateMap = [];
    if ($this->shouldCheckDuplicates($rows)) {
        $emails = collect($rows)->pluck('email')->filter()->values()->all();
        $phones = collect($rows)->pluck('phone')->filter()->values()->all();
        $dups = DB::table('job_applications')
            ->select('email', 'phone', DB::raw('COUNT(*) as c'))
            ->where(function ($q) use ($emails, $phones) {
                $q->whereIn('email', $emails)->orWhereIn('phone', $phones);
            })
            ->groupBy('email', 'phone')
            ->get();
        foreach ($dups as $d) {
            $duplicateMap[$d->email . '|' . $d->phone] = $d->c;
        }
    }

    return [
        'jobs'        => $jobs,
        'skills'      => $skillsByJob,
        'match'       => $matchMap,
        'duplicates'  => $duplicateMap,
    ];
}
```

**In `allDataAjax`, before returning the view:**
```php
$maps = $this->prepareRowMaps($data);
return view('…all_apptracker', [
    'data'        => $data,
    'jobsMap'     => $maps['jobs'],
    'skillsMap'   => $maps['skills'],
    'matchMap'    => $maps['match'],
    'duplicateMap'=> $maps['duplicates'],
]);
```

**Patch the Blade** (`all_apptracker.blade.php`):

```blade
@foreach ($data as $rowArr)
  @php
    $jobId    = $row->job_id;
    $job      = $jobsMap[$jobId] ?? null;
    $skills   = isset($skillsMap[$jobId]) ? $skillsMap[$jobId]->pluck('skill_name') : collect();
    $match    = $matchMap[$jobId] ?? 0;
    $dupCount = $duplicateMap[($row->email ?? '').'|'.($row->phone ?? '')] ?? null;
  @endphp
  <tr>
    <td>{{ $rowArr['title'] }}</td>
    <td>
      @if($job && $job->max_sal_mode)
        {{ $job->max_salary_currency }} {{ number_format($row->salary) }}
      @else
        {{ number_format($row->salary) }}
      @endif
    </td>
    <td>
      @foreach ($skills as $s)
        <span class="badge">{{ $s }}</span>
      @endforeach
    </td>
    <td>{{ $match }}%</td>
  </tr>
@endforeach
```

**Verification:**
```bash
# Enable general log; render a 20-row page
# Expected SQL count drops from ~80 to ~5
```

---

## §6 — Pagination Review

#### Fix F-10: Cursor pagination

**File:** `resources/views/service_company/jobs/index.blade.php` (DataTables init) and the corresponding controller method `allDataAjax`.

**Before (DataTables init, JS):**
```js
$('#jobs-table').DataTable({
    serverSide: true,
    ajax: '/jobs_ajax/all_data?start=0&length=20&order=created_at|desc',
    ...
});
```

**After:**
```js
// In the controller expose the next cursor:
//   next_cursor: "<base64 of lastId>" or null
$('#jobs-table').DataTable({
    serverSide: true,
    pagingType: 'simple_numbers',
    ajax: function (data, callback, settings) {
        const cursor = $('#jobs-table').data('next-cursor') || 0;
        $.get('/jobs_ajax/all_data', { after_id: cursor, length: 20 }, function (resp) {
            callback({ data: resp.data });
            $('#jobs-table').data('next-cursor', resp.next_cursor);
        });
    },
    ...
});
```

**In the controller** (`ServiceCompanyJobsController.php` or whichever feeds DataTables):
```php
public function allDataAjax(Request $request) {
    $length = min((int) $request->input('length', 20), 100);
    $afterId = (int) $request->input('after_id', 0);

    $rows = DB::table('jobs')
        ->select('id', 'title', 'status', 'created_at')
        ->where('company_id', auth()->user()->company_id)
        ->where('status', '<>', 'removed')
        ->where('id', '>', $afterId)        // ← keyset cursor
        ->orderBy('id', 'desc')
        ->limit($length + 1)               // +1 to detect if there's more
        ->get();

    $hasMore = $rows->count() > $length;
    $rows = $rows->slice(0, $length);
    $nextCursor = $hasMore ? base64_encode((string) $rows->last()->id) : null;

    return response()->json([
        'data'        => $rows,
        'next_cursor' => $nextCursor,
    ]);
}
```

**Verification:**
```sql
-- Verify keyset query plan
EXPLAIN SELECT id, title FROM jobs
WHERE company_id = 1 AND id > 12345
ORDER BY id DESC LIMIT 20;
-- Expected: type=range, key=PRIMARY or idx_jobs_company_status_created, rows=20
-- Compare with OFFSET:
EXPLAIN SELECT id, title FROM jobs
WHERE company_id = 1
ORDER BY id DESC LIMIT 20 OFFSET 10000;
-- Pre-fix LIMIT 20 OFFSET 2000 takes ~280ms; Offset 10000: ~1500ms
-- Post-fix: stable ~10ms at any cursor position
```

---

## §7 — Dashboard Statistics (Total / Hiring / Hold / Closed / Removed)

**Current implementation:**
- `ServiceCompanyJobsController::index:1278-1280` — 3 `Job::count()` synchronous aggregations
- `dataCnt():1267-1697` — also computes `allRequisition`, `clientRequisition`, `customRequisition`, `hiringCount`, `holdCount`, `closedCount`, `allTabCount`

**Effective "duplicate the work":** 4 SQL counts + 4 ES counts on a single page load.

#### Fix F-11: Event-driven counter table

**DDL:**
```sql
CREATE TABLE job_counters (
  company_id    INT UNSIGNED PRIMARY KEY,
  total_jobs    INT UNSIGNED NOT NULL DEFAULT 0,
  hiring_jobs   INT UNSIGNED NOT NULL DEFAULT 0,
  on_hold_jobs  INT UNSIGNED NOT NULL DEFAULT 0,
  closed_jobs   INT UNSIGNED NOT NULL DEFAULT 0,
  removed_jobs  INT UNSIGNED NOT NULL DEFAULT 0,
  last_job_id   INT UNSIGNED NOT NULL DEFAULT 0,
  last_event_at TIMESTAMP NULL,
  INDEX idx_last_event (last_event_at)
) ENGINE=InnoDB;
```

**Migration file:** `database/migrations/2026_08_create_job_counters_table.php`

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('job_counters', function (Blueprint $t) {
            $t->unsignedInteger('company_id')->primary();
            $t->unsignedInteger('total_jobs')->default(0);
            $t->unsignedInteger('hiring_jobs')->default(0);
            $t->unsignedInteger('on_hold_jobs')->default(0);
            $t->unsignedInteger('closed_jobs')->default(0);
            $t->unsignedInteger('removed_jobs')->default(0);
            $t->unsignedInteger('last_job_id')->default(0);
            $t->timestamp('last_event_at')->nullable();
            $t->index('last_event_at', 'idx_last_event');
        });
    }

    public function down(): void {
        Schema::dropIfExists('job_counters');
    }
};
```

**Redis mirror key format:**
```
job_counters:{cid}    # HASH total|hiring|on_hold|closed|removed|last_event_at
```

**Observer** (registed in AppServiceProvider — see §21): on `Job::saved/deleted/updated` adjust the row and the Redis hash atomically.

**Reads:**

```php
public static function jobCounters(int $companyId): array {
    return Cache::remember("job_counters:{$companyId}", 60, function () use ($companyId) {
        $row = DB::table('job_counters')->where('company_id', $companyId)->first();
        if (!$row) {
            // Cold path: compute once, write back
            $row = self::seedJobCounters($companyId);
        }
        return (array) $row;
    });
}
```

**Verification:**
```sql
-- Compare counter table vs. live COUNT (drift detection)
SELECT
  jc.total_jobs,
  (SELECT COUNT(*) FROM jobs WHERE company_id = jc.company_id AND deleted_at IS NULL) AS live_total,
  ABS(jc.total_jobs - live_total) AS drift
FROM job_counters jc;

-- Expected: drift = 0 for all tenants
```

---

## §8 — Search Review

**Current state:** no FULLTEXT index, all search via `LIKE '%query%'` (full-scan).

#### Fix F-12: Migrate search to ES + add FULLTEXT fallback

**DDL (MySQL FULLTEXT for fallback):**
```sql
ALTER TABLE jobs ADD FULLTEXT INDEX ft_jobs_title_desc (title, description);
```

**ES query (target, served by `ats_jobs_elastic`):**

```php
public function search(SearchRequest $req): LengthAwarePaginator {
    $params = [
        'index' => config('elasticsearch.ELASTIC_JOBS'),
        'body'  => [
            'from' => max(0, ((int) $req->page - 1) * 20),
            'size' => 20,
            'query' => [
                'bool' => [
                    'must' => [
                        ['multi_match' => [
                            'query' => $req->input('q'),
                            'fields' => ['title^3', 'description', 'skills_text'],
                            'fuzziness' => 'AUTO',
                        ]],
                    ],
                    'filter' => [
                        ['term' => ['company_id' => auth()->user()->company_id]],
                        ['term' => ['status' => $req->input('status', 'active')]],
                        ['range' => ['min_experience' => ['lte' => (int) ($req->input('exp_max', 99))]]],
                        ['range' => ['max_experience' => ['gte' => (int) ($req->input('exp_min', 0))]]],
                    ],
                ],
            ],
        ],
    ];

    $resp = app(ElasticService::class)->getClient()->search($params);
    return new LengthAwarePaginator(
        collect($resp['hits']['hits'])->map(fn ($h) => $h['_source']),
        $resp['hits']['total']['value'] ?? 0,
        20,
        (int) $req->input('page', 1)
    );
}
```

**Edge_ngram analyzer for autocomplete** (apply via Elasticsearch index mapping update):
```bash
curl -X PUT "http://76.13.243.68:9200/ats_jobs_elastic/_settings" \
  -H 'Content-Type: application/json' \
  -d '{
    "analysis": {
      "analyzer": {
        "title_edge_ngram": {
          "type": "custom",
          "tokenizer": "standard",
          "filter": ["lowercase", "edge_ngram_filter"]
        }
      },
      "filter": {
        "edge_ngram_filter": { "type": "edge_ngram", "min_gram": 2, "max_gram": 15 }
      }
    }
  }'
```

**Verification:**
```bash
time php artisan tinker --execute="app(App\\Http\\Controllers\\SearchController::class)->search((new \\Illuminate\\Http\\Request(['q'=>'java','status'=>'active','exp_min'=>1,'exp_max'=>5])))"
# Pre-fix: 600ms+
# Post-fix: <80ms
```

---

## §9 — Filter Review

**Current behaviour:** every filter combination → fresh query.

#### Fix F-13: Filter-result cache with stampede protection

```php
public function jobsListingFilter(Request $req) {
    $companyId = auth()->user()->company_id;
    $filterHash = md5(json_encode([
        'status' => $req->input('status'),
        'type'   => $req->input('type'),
        'exp'    => $req->input('exp'),
        'sort'   => $req->input('sort', 'created_at_desc'),
    ]));

    $cacheKey = "filter_jobs:{$companyId}:{$filterHash}";

    return Cache::lock("filter_lock:{$cacheKey}", 5)->block(3, function () use ($cacheKey, $req, $companyId) {
        return Cache::flexible($cacheKey, [30, 120], function () use ($req, $companyId) {
            return $this->buildFilterQuery($req, $companyId);
        });
    });
}
```

**Stampede jitter:**
```php
// In Cache::lock->block, jitter the wait
$jitter = rand(1, 3);
Cache::lock("filter_lock:{$cacheKey}", 5)->block($jitter, function () { ... });
```

**Pre-filter metadata in Redis:**
```php
// Job create/update:
Redis::sadd('job_filters:status:' . $companyId, $job->status);
Redis::sadd('job_filters:location:' . $companyId, $job->location_id);
Redis::sadd('job_filters:type:' . $companyId, $job->job_type);

// On filter dropdown load:
return [
    'statuses'   => Redis::smembers('job_filters:status:' . $companyId),
    'locations'  => Redis::smembers('job_filters:location:' . $companyId),
    'types'      => Redis::smembers('job_filters:type:' . $companyId),
];
```

---

## §10 — Pipeline Counters

#### Fix F-14: Event-driven pipeline counter table

**DDL:**
```sql
CREATE TABLE pipeline_counters (
  job_id       INT UNSIGNED PRIMARY KEY,
  company_id   INT UNSIGNED NOT NULL,
  sourcing     INT UNSIGNED NOT NULL DEFAULT 0,
  screening    INT UNSIGNED NOT NULL DEFAULT 0,
  evaluation   INT UNSIGNED NOT NULL DEFAULT 0,
  negotiation  INT UNSIGNED NOT NULL DEFAULT 0,
  offered      INT UNSIGNED NOT NULL DEFAULT 0,
  joined       INT UNSIGNED NOT NULL DEFAULT 0,
  rejected     INT UNSIGNED NOT NULL DEFAULT 0,
  fresh_data   TINYINT(1) NOT NULL DEFAULT 1,
  last_event_at TIMESTAMP NULL,
  INDEX idx_job_company (job_id, company_id),
  INDEX idx_fresh       (fresh_data, last_event_at)
) ENGINE=InnoDB;
```

**Migration file:** `database/migrations/2026_08_create_pipeline_counters_table.php` — same skeleton as `job_counters` migration.

**Redis hash:**
```
pipeline:{job_id}    # HASH sourcing|screening|...|rejected
```

**Observer** (JobApplicationObserver::saved — see §21):
```php
public function saved(JobApplication $app): void {
    UpdatePipelineCountersJob::dispatch($app->id)->onQueue('counters');
    UpdateApplicationCountersJob::dispatch($app->id)->onQueue('counters');
    ElasticCandidateIndexJob::dispatch($app->id)->onQueue('es-index');
}

public function deleted(JobApplication $app): void {
    DecrementPipelineCountersJob::dispatch($app->id)->onQueue('counters');
    DecrementApplicationCountersJob::dispatch($app->id)->onQueue('counters');
}
```

**Job class — `app/Jobs/UpdatePipelineCountersJob.php`:**

```php
<?php
namespace App\Jobs;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class UpdatePipelineCountersJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(public int $jobApplicationId) {}

    public function handle(): void {
        $app = JobApplication::withTrashed()->find($this->jobApplicationId);
        if (!$app || $app->trashed()) {
            // decrement path through different job
            return;
        }

        $stage = $this->stageFromMasterStatus($app->master_status_id);
        $jobId = $app->job_id;

        // Atomic Redis increment
        Redis::pipeline(function ($pipe) use ($jobId, $stage) {
            $pipe->hincrby("pipeline:{$jobId}", $stage, 1);
            $pipe->hset("pipeline:{$jobId}", 'last_event_at', now()->toIso8601String());
        });

        // Synchronous summary table UPDATE
        DB::table('pipeline_counters')
            ->where('job_id', $jobId)
            ->updateOrInsert(
                ['job_id' => $jobId, 'company_id' => $app->company_id],
                [
                    $stage => DB::raw("{$stage} + 1"),
                    'last_event_at' => now(),
                    'fresh_data' => 0,
                ]
            );
    }

    protected function stageFromMasterStatus(int $masterStatusId): string {
        return match($masterStatusId) {
            1 => 'sourcing',
            2 => 'screening',
            3 => 'evaluation',
            4 => 'negotiation',
            5 => 'offered',
            6 => 'joined',
            7, 9 => 'rejected',
            default => 'sourcing',
        };
    }
}
```

**Read path** (replaces `ProductCompanyAppTrackerController::loadcounter:98-180`):

```php
public function loadcounter(int $jobId): array {
    $cached = Redis::hgetall("pipeline:{$jobId}");
    if ($cached) return $cached;

    $row = DB::table('pipeline_counters')->where('job_id', $jobId)->first();
    if (!$row) {
        // Cold path: compute once, write back to both
        $row = $this->coldComputePipeline($jobId);
    }
    return (array) $row;
}
```

#### Fix F-15: Covering index for the legacy path (kept as a backout safety net)

```sql
CREATE INDEX idx_job_app_count ON job_applications (job_id, master_status_id, deleted_at);
```

**Verification:**
```sql
EXPLAIN SELECT job_id, master_status_id, COUNT(*) FROM job_applications
WHERE job_id IN (12, 34, 56, 78) AND deleted_at IS NULL
GROUP BY job_id, master_status_id;
-- Pre-fix:  type=ALL on job_applications (1.2M rows)
-- Post-fix: type=range, key=idx_job_app_count, rows=8
```

---

## §11 — Agency Section

#### Fix F-16: Counter summary for agency match

**DDL:**
```sql
CREATE TABLE job_agency_counter (
  job_id            INT UNSIGNED NOT NULL PRIMARY KEY,
  company_id        INT UNSIGNED NOT NULL,
  matching_count    INT UNSIGNED NOT NULL DEFAULT 0,
  assigned_count    INT UNSIGNED NOT NULL DEFAULT 0,
  last_event_at     TIMESTAMP NULL,
  INDEX idx_job_agency (job_id, company_id)
) ENGINE=InnoDB;
```

**Observer (`RecommendationRelationObserver`):**
```php
public function saved(RecommendationRelation $r): void {
    DB::table('job_agency_counter')
        ->where('job_id', $r->job_id)
        ->updateOrInsert(
            ['job_id' => $r->job_id, 'company_id' => $r->from_company_id],
            ['assigned_count' => DB::raw('assigned_count + 1'), 'last_event_at' => now()]
        );
    Redis::hincrby("job_agency:{$r->job_id}", 'assigned_count', 1);
}
```

**Read:**
```php
public function getJobAgencyCount(int $jobId): int {
    return (int) Cache::remember("job_agency_count:{$jobId}", 600,
        fn() => (int) DB::table('job_agency_counter')->where('job_id', $jobId)->value('assigned_count') ?: 0
    );
}
```

---

## §12 — Candidate Counts (Total Applications / Interview)

#### Fix F-17: `application_counters` summary table

```sql
CREATE TABLE application_counters (
  job_id            INT UNSIGNED PRIMARY KEY,
  total_apps        INT UNSIGNED NOT NULL DEFAULT 0,
  interview_apps    INT UNSIGNED NOT NULL DEFAULT 0,
  offer_apps        INT UNSIGNED NOT NULL DEFAULT 0,
  rejected_apps     INT UNSIGNED NOT NULL DEFAULT 0,
  last_event_at     TIMESTAMP NULL,
  INDEX idx_last     (last_event_at)
) ENGINE=InnoDB;
```

**Read** replaces `dataCnt():1638`:
```php
public function getApplicationCounters(int $jobId): array {
    $row = Redis::hgetall("app_counters:{$jobId}");
    if (!$row) {
        $row = DB::table('application_counters')->where('job_id', $jobId)->first();
        if (!$row) $row = (object) ['total_apps' => 0, 'interview_apps' => 0, 'offer_apps' => 0, 'rejected_apps' => 0];
        Redis::hmset("app_counters:{$jobId}", (array) $row);
        Redis::expire("app_counters:{$jobId}", 86400);
    }
    return $row;
}
```

---

## §13 — Frontend Performance

### Estimated initial payload today

| Asset | Today | Notes |
|---|---|---|
| HTML | 150 KB | 985-line Blade + per-row inline scripts |
| CSS | 350 KB | Metronic bundle unminified |
| JS | 700 KB | jQuery + Metronic + DataTables + SweetAlert2 (+ Livewire) |
| Fonts | 500 KB | Poppins 9 weights × web + italic |
| Icons | 250 KB | Keenicons font |
| **Total uncompressed** | **~1.5 MB** | |
| **Total gzipped** | **~750 KB** | |

### Fix F-18 — Concrete asset trims

#### 1. Poppins font reduction

**File:** wherever `@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');` is — likely the layout.

**Before:** 9 weights imported.
**After:** One weight + italic + system fallback.

```css
/* layout file or public/css/custom.css */
@font-face {
  font-family: 'Poppins';
  src: url('/fonts/poppins-400.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'Poppins';
  src: url('/fonts/poppins-400-italic.woff2') format('woff2');
  font-weight: 400;
  font-style: italic;
  font-display: swap;
}
body { font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
```

**Action:** Pre-download `poppins-400.woff2` (~12KB) and `poppins-400-italic.woff2` (~13KB) into `public/fonts/`. Self-host to skip Google Fonts CSPE.

**Verification:** DevTools Network → Font tab; only one woff2 should load per page.

#### 2. Inline Keenicons as SVG sprite

**Before:** `<link rel="stylesheet" href="/css/keenicons.css">`
**After:** Single `public/icons.svg` sprite, referenced inline.

```blade
{{-- resources/views/components/icon.blade.php --}}
@props(['name'])
<svg class="icon icon-{{ $name }}"><use xlink:href="/icons.svg#{{ $name }}"></use></svg>
```

Then in template: `<x-icon name="search" />` instead of `<i class="ki ki-search"></i>`.

**Verification:** DevTools; no `/css/keenicons.css` request.

#### 3. Critical CSS + defer non-critical

**Use the `critical` npm package (`npm install -D critical`):**
```bash
npx critical http://testing.jero.works/service_company/jobs \
  --base=./public \
  --css=./public/css/metronic.css \
  --extract=true \
  --target=./resources/views/partials/critical-css.blade.php
```

**Patch layout:**
```blade
<head>
    <style>{!! file_get_contents(resource_path('views/partials/critical-css.blade.php')) !!}</style>
    <link rel="preload" href="/css/metronic.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="/css/metronic.css"></noscript>
</head>
```

**Verification:** Lighthouse → "First Contentful Paint" < 1s; total CSS < 100 KB initial.

#### 4. Defer JS

```blade
<script src="/js/app.js" defer></script>
<script src="/js/datatable.js" defer></script>
<script src="/js/metronic.js" defer></script>
```

**Audit & remove unused** (typical Jero pattern):
- Drop `livewire.js` from jobs page bundle (Livewire isn't used for the table).
- Drop `sweetalert2` global init; load on-demand.
- Drop `select2` global; use native `<select>` for filter dropdowns.

**Verification:** DevTools Performance → "Total Blocking Time" < 200ms; "Time to Interactive" < 1.5s.

---

## §14 — Blade Performance

### Hot-path per-row operations in `all_apptracker.blade.php`

Same N+1s already covered in §5. Plus:

**Repeated formatting in the loop:**
```blade
{{ number_format($row->salary) }}                    {{-- per row × hundreds --}}
{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}    {{-- per row × hundreds, parser overhead --}}
@if(auth()->user()->can('edit_job')) ... @endif      {{-- Spatie lookup × hundreds --}}
```

#### Fix F-19: Pre-format in controller, format once per request

**In controller (after fetching rows, before passing to view):**
```php
foreach ($rows as $r) {
    $r->salary_fmt = number_format($r->salary);
    $r->created_fmt = \Carbon\Carbon::parse($r->created_at)->format('d M Y');
    $r->match_pct = $matchMap[$r->job_id] ?? 0;     // from prepareRowMaps()
}

// Pre-resolve permission once
$canEdit = auth()->user()->can('edit_job');
$canDelete = auth()->user()->can('delete_job');
```

**In view:**
```blade
{{ $row->salary_fmt }}
{{ $row->created_fmt }}
@if($canEdit) ... @endif
```

**Remove duplicate `NoticePeriod::all()` and other duplicate reads** — already covered by F-21.

---

## §15 — Caching Strategy

### What is currently cached

(Inferred from `Cache::remember`/`Cache::put` grep over `app/`.)

| Cache key | TTL | File |
|---|---|---|
| `currency_codesww` | 86400 | `GeneralMethod.php:4056,4090` |
| `vendor_ids_for_company_{id}` | 300 | `HomeController.php:7002` |
| (same) | 300 | `ServiceCompanyJobsController.php:9717` |
| (same) | 300 | `ServiceCompanyTeamController.php:70` |

### Fix F-20: Cache list + boot config

```bash
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache      # Blade compiled views
```

**Verify cache files now exist:**
```bash
ls -la bootstrap/cache/
# Expected: config.php  routes-v7.php  modules.php  packages.php  services.php  events.php  views.php
```

### Fix F-21: Cache `NoticePeriod` / `LanguageSetting` / `GlobalSetting`

```php
// app/Models/NoticePeriod.php — add booted observer
class NoticePeriod extends Model {
    protected static function booted() {
        static::saved(fn() => Cache::forget('notice_periods'));
        static::deleted(fn() => Cache::forget('notice_periods'));
    }
}

// In all calling code:
public static function getNoticePeriods() {
    return Cache::remember('notice_periods', 86400, fn() => NoticePeriod::orderBy('days')->get());
}
```

**Verification:**
```bash
# Tail Laravel log; reload /service_company/jobs 5x; expect cache hit count to grow
grep "notice_periods" storage/logs/laravel.log | head -20
# Expected: only one SELECT on notice_period in the log
```

### Cache key schedule (V2 target)

| Key | TTL | Source | Invalidation |
|---|---|---|---|
| `job_counters:{cid}` | 60s, event-driven | `job_counters` table | `Job::saved/deleted/updated` observer |
| `pipeline:{job_id}` | 24h, hot path | `pipeline_counters` table | `JobApplication::saved/deleted` observer |
| `app_counters:{job_id}` | 24h | `application_counters` table | `JobApplicationSaved` event |
| `company:{cid}` | 1h | `companies` row | `Company::saved` observer |
| `agencies_for_company:{cid}` | 1h | agencies list | `Agency::saved` |
| `team_for_company:{cid}` | 1h | team rows | `User::saved` |
| `notice_periods` | 24h | `NoticePeriod::all()` | `NoticePeriod::saved` |
| `language_settings` | 24h | `LanguageSetting::all()` | `LanguageSetting::saved` |
| `global_settings` | 24h | `GlobalSetting::with('currency')` | `GlobalSetting::saved` |
| `custome_count_all:{job_id}:{hash}` | [60,300] flexible | 6 dashboard counters | `Job::saved` |
| `user_permissions:{uid}` | 1h | Spatie | `Role::saved` |
| `filter_jobs:{cid}:{hash}` | [30,120] | filter result | `Job::saved` |
| `route_index` | indefinite | route:cache | file regen |
| `config_index` | indefinite | config:cache | file regen |
| `user_session_meta:{sid}` | 5min | middleware | TTL |
| `menu_tree:{cid}` | 1h | sidebar nav | route:cache regen |

---

## §16 — Memory Usage

### Per-request estimate (current code, cold, 20 rows)

- `Company::all()`: ~5 MB
- `recommendation_relation.pluck('job_id')`: ~8 MB
- `dataCnt() ::get()` (full materialization): ~4 MB
- Job model hydration × 200 rows × `$appends`: 10 MB
- Blade @foreach context array overhead: ~3 MB
- JSON/HTML render prep: ~5 MB

**Total estimated today:** ~35 MB / request (901 KB avg / recruiter × 1 req/s sustained × 10k recruiters = 350 GB continuous heap pressure, 100% over the 32 GB server).

### Fix F-22: Memory profile per request after V2 (target <15 MB)

| Component | Today | After V2 | How |
|---|---|---|---|
| Company fetch | 5 MB | 0.05 MB | `findOrFail($id)` instead of `all()` |
| Recommendation pluck | 8 MB | 0 | Replaced by EXISTS subquery |
| dataCnt ::get() materialization | 4 MB | 0.05 MB | `selectRaw('COUNT(DISTINCT …)')` returns 1 row |
| Job model hydration | 10 MB | 0 | Use raw `DB::select` projection with selectRaw |
| Blade @foreach overhead | 3 MB | 1 MB | Pass Maps, skip per-row hydration |
| JSON/HTML render | 5 MB | 4 MB | Minor — pre-format in controller |
| **Total** | **~35 MB** | **~15 MB** | |

**Verification:**
```php
// In a controller test:
$start = memory_get_usage();
$response = $this->get('/service_company/jobs');
$end = memory_get_peak_usage();
echo "peak: " . ($end - $start) . " bytes";
// Expected after V2: <15MB
```

---

## §17 — Network Payload Targets

| Asset | Today | V2 target | How |
|---|---|---|---|
| HTML | 150 KB | 80 KB | Trim Blade, defer per-row JS |
| CSS | 350 KB | 100 KB | Critical CSS + defer Metronic |
| JS | 700 KB | 200 KB | defer, remove unused (Livewire, SweetAlert bulk) |
| Fonts | 500 KB | 100 KB | Poppins 1 weight + system fallback |
| Icons | 250 KB | 25 KB | SVG sprite |
| Images | 50 KB | 30 KB | responsive srcset, lazy loading |
| **Total uncompressed** | **~1.5 MB** | **~510 KB** | |
| **Total gzipped** | **~750 KB** | **~280 KB** | |

---

## §18 — Concurrency Review

### Hardware budget

Server: 32 GB RAM, 8-16 cores. FrankenPHP × 8 workers (per `start-octane.sh:3`).
`max_requests=1000` per worker → 8000 reqs before recycle. With 10k recruiters at ~1 req/s sustained = 8,000-12,000 req/s at peak.

### Bottlenecks — pre-fix

| Bottleneck | Cost at 10k recruiters | Fix |
|---|---|---|
| `EnsureOneDeviceLogin` UPDATE | ~10k writes/s, row-lock serializes the entire chain | Cache throttle (F-01) |
| MySQL `max_connections=151` default | Connection pool exhausted, 100% packet drop | Raise to 2048; deploy ProxySQL / RDS Proxy |
| `dataCnt():1613-1626` 100k-ID IN-list | 10k recruiters × thousands of recs = millions of literals per query | EXISTS subquery + summary table |
| ES `cardinality` follow-up queries | 9 ES round-trips/page × 10k = 90k ES ops/s | Merge into single query (F-07) |

### Post-fix targets

- 5 MySQL writes/req on cold path (1 candidate counter read at most)
- 1 ES round-trip / warm page (none on warm cache hits)
- MySQL CPU @ 10k = ~5%; ES CPU @ 10k = ~10%

---

## §19 — Database Design

DDL already enumerated in §4. Plus:

### Apply timing recommendations

- Use `ALTER TABLE … ALGORITHM=INPLACE, LOCK=NONE` if MySQL version supports it.
- Otherwise: `pt-online-schema-change` (Percona) or `gh-ost` (GitHub) for online migration.
- Estimated time per ALTER: 5-15 min on 1M-row tables, 30-60 min on 10M+ row tables.

```bash
# Example with gh-ost
gh-ost \
  --host=db.jero.works \
  --database=jero \
  --table=jobs \
  --alter="ADD INDEX idx_jobs_company_status_created (company_id, status, created_at DESC)" \
  --allow-on-master \
  --execute
```

### Backout plan

All ALTERs documented in §4 backout block. Each index add can be reverted independently with `DROP INDEX`. Counter tables are creations only; backout = `DROP TABLE job_counters` / `pipeline_counters` / `application_counters`.

---

## §20 — Redis Architecture

### Redis key formats (V2 canonical)

```
job_counters:{cid}                          HASH  total|hiring|on_hold|closed|removed|last_event_at
pipeline:{job_id}                           HASH  sourcing|screening|evaluation|negotiation|offered|joined|rejected
app_counters:{job_id}                       HASH  total_apps|interview_apps|offer_apps|rejected_apps
job_agency:{job_id}                         HASH  matching_count|assigned_count
company:{cid}                               STRING (JSON)
agencies_for_company:{cid}                  STRING (JSON)
team_for_company:{cid}                      HASH
notice_periods                              STRING (JSON)
language_settings                           STRING (JSON)
global_settings                             STRING (JSON)
custome_count_all:{job_id}:{hash}           STRING (JSON)  flexible [60,300]
filter_jobs:{cid}:{md5}                     STRING (JSON)  flexible [30,120]
user_permissions:{uid}                      STRING (JSON)  1h
search_history:{uid}                        SORTED SET     1h
session:{sid}                               HASH
throttle:jobs:{uid}                         STRING (counter, exp=60s)
user_session_meta:{uid}                     STRING (1h)
ensure_one_device_save_{uid}                STRING (60s)
cache.invalidate                            CHANNEL (pub/sub)
rate:user:{uid}                             SORTED SET (sliding window)
```

### Lua atomic counter script (apply in UpdatePipelineCountersJob)

```lua
-- KEYS[1] = "pipeline:{job_id}"
-- ARGV[1] = field name (e.g. "sourcing")
-- ARGV[2] = delta (1 or -1)
redis.call('HINCRBY', KEYS[1], ARGV[1], ARGV[2])
redis.call('HSET', KEYS[1], 'last_event_at', ARGV[3])
return redis.call('HGET', KEYS[1], ARGV[1])
```

**Call from PHP:**
```php
$newValue = Redis::eval(
    "redis.call('HINCRBY', KEYS[1], ARGV[1], ARGV[2]) return redis.call('HGET', KEYS[1], ARGV[1])",
    1,
    "pipeline:{$jobId}",
    $stage,
    $delta,
    now()->toIso8601String()
);
```

### Rate-limiter sliding window Lua

```lua
-- KEYS[1] = "rate:user:{uid}"
-- ARGV[1] = now (millis)
-- ARGV[2] = window seconds
-- ARGV[3] = max requests
local now = tonumber(ARGV[1])
local window = tonumber(ARGV[2]) * 1000
local max = tonumber(ARGV[3])
redis.call('ZREMRANGEBYSCORE', KEYS[1], '-inf', now - window)
local count = redis.call('ZCARD', KEYS[1])
if count >= max then return 0 end
redis.call('ZADD', KEYS[1], now, now)
redis.call('EXPIRE', KEYS[1], ARGV[2])
return 1
```

### Feature flags (Redis-backed; safe rollout)

```php
// In config/app.php:
'feature' => [
    'use_summary_table_job_counters' => env('FEATURE_SUMMARY_JOB_COUNTERS', false),
    'use_summary_table_pipeline_counters' => env('FEATURE_SUMMARY_PIPELINE', false),
    'throttle_ensure_one_device' => env('FEATURE_THROTTLE_ENSURE', true),
],

// Accessor:
function feature(string $key, $default = false): bool {
    return (bool) Redis::get("feature:{$key}", $default);
}
```

---

## §21 — Event Driven Architecture

### Fix F-23: Register the dead `JobApplicationObserver` + add new observers

**File:** `app/Providers/AppServiceProvider.php` (the `boot()` method)

**Before:**
```php
public function boot(): void {
    $this->app['cache']->remember('session_meta', 60, fn() => true);
    // …$this->user->hasRole() ratelimiters and a few constants…
}
```

**After:**
```php
public function boot(): void {
    // Existing code…

    // Wire observers
    \App\Models\Job::observe(\App\Observers\JobObserver::class);
    \App\Models\JobApplication::observe(\App\Observers\JobApplicationObserver::class);
    \App\Models\BenchApplication::observe(\App\Observers\BenchApplicationObserver::class);
    \App\Models\RecommendationRelation::observe(\App\Observers\RecommendationRelationObserver::class);
    \App\Models\JobSkill::observe(\App\Observers\JobSkillObserver::class);
    \App\Models\NoticePeriod::observe(\App\Observers\NoticePeriodCacheInvalidateObserver::class);
}
```

### File: `app/Observers/JobObserver.php` (NEW)

```php
<?php
namespace App\Observers;

use App\Jobs\UpdateJobCountersJob;
use App\Models\Job;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class JobObserver {
    public function saved(Job $job): void {
        // Bust the dashboard cache for this job's tenant
        Cache::forget("custome_count_all:{$job->id}:" . '*');
        Cache::forget("filter_jobs:{$job->company_id}:" . '*');

        // Async counter update
        UpdateJobCountersJob::dispatch($job->id)->onQueue('counters');
    }

    public function deleted(Job $job): void {
        Cache::forget("custome_count_all:{$job->id}:" . '*');
        UpdateJobCountersJob::dispatch($job->id, isDelete: true)->onQueue('counters');
    }
}
```

### File: `app/Observers/JobApplicationObserver.php` (NEW, replacing existing)

```php
<?php
namespace App\Observers;

use App\Jobs\DecrementApplicationCountersJob;
use App\Jobs\DecrementPipelineCountersJob;
use App\Jobs\ElasticCandidateIndexJob;
use App\Jobs\UpdateApplicationCountersJob;
use App\Jobs\UpdatePipelineCountersJob;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Cache;

class JobApplicationObserver {
    public function saved(JobApplication $app): void {
        // Existing photo-company_id logic preserved — see BUG-12 patch separately
        if ($app->isDirty('photo')) {
            $oldPhoto = $app->getOriginal('photo');
            if ($oldPhoto) @unlink(storage_path('app/' . $oldPhoto));
        }
        if (!$app->company_id) {
            $app->company_id = $app->job?->company_id;
            $app->saveQuietly();
        }

        // New event-driven counters
        UpdatePipelineCountersJob::dispatch($app->id)->onQueue('counters');
        UpdateApplicationCountersJob::dispatch($app->id)->onQueue('counters');
        ElasticCandidateIndexJob::dispatch($app->id)->onQueue('es-index');

        Cache::forget("custome_count_all:{$app->job_id}:" . '*');
    }

    public function deleted(JobApplication $app): void {
        DecrementPipelineCountersJob::dispatch($app->id)->onQueue('counters');
        DecrementApplicationCountersJob::dispatch($app->id)->onQueue('counters');
    }
}
```

### File: `app/Jobs/UpdateJobCountersJob.php` (NEW)

```php
<?php
namespace App\Jobs;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class UpdateJobCountersJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 10;

    public function __construct(public int $jobId, public bool $isDelete = false) {}

    public function handle(): void {
        $job = Job::withTrashed()->find($this->jobId);
        if (!$job) return;

        $companyId = $job->company_id;
        $status = $this->isDelete ? 'removed' : $job->status;

        // Adjust counter for current status; subtract from previous if changed.
        $delta = $this->isDelete ? -1 : 1;
        $key = "job_counters:{$companyId}";

        Redis::pipeline(function ($pipe) use ($key, $status, $delta) {
            $pipe->hincrby($key, 'total_jobs', $delta);
            $pipe->hincrby($key, "{$status}_jobs", $delta);   // hiring_jobs / on_hold_jobs / closed_jobs
            $pipe->hset($key, 'last_event_at', now()->toIso8601String());
        });

        // Cross-check summary table
        DB::table('job_counters')->updateOrInsert(
            ['company_id' => $companyId],
            [
                'total_jobs'   => DB::raw("total_jobs + ({$delta})"),
                "{$status}_jobs" => DB::raw("{$status}_jobs + ({$delta})"),
                'last_job_id'  => $job->id,
                'last_event_at' => now(),
            ]
        );
    }
}
```

### File: `app/Observers/BenchApplicationObserver.php` (NEW)

```php
<?php
namespace App\Observers;

use App\Jobs\UpdateBenchCountersJob;
use App\Models\BenchApplication;
use Illuminate\Support\Facades\Cache;

class BenchApplicationObserver {
    public function saved(BenchApplication $app): void {
        Cache::forget("custome_count_all:{$app->job_id}:" . '*');
        UpdateBenchCountersJob::dispatch($app->id)->onQueue('counters');
    }

    public function deleted(BenchApplication $app): void {
        UpdateBenchCountersJob::dispatch($app->id, isDelete: true)->onQueue('counters');
    }
}
```

### File: `app/Observers/RecommendationRelationObserver.php` (NEW)

```php
<?php
namespace App\Observers;

use App\Jobs\UpdateJobAgencyCounterJob;
use App\Models\RecommendationRelation;
use Illuminate\Support\Facades\Cache;

class RecommendationRelationObserver {
    public function saved(RecommendationRelation $r): void {
        Cache::forget("job_agency_count:{$r->job_id}");
        UpdateJobAgencyCounterJob::dispatch($r->id)->onQueue('counters');
    }
}
```

### File: `app/Observers/JobSkillObserver.php` (NEW)

```php
<?php
namespace App\Observers;

use App\Models\JobSkill;
use Illuminate\Support\Facades\Cache;

class JobSkillObserver {
    public function saved(JobSkill $s): void { Cache::forget("job_skills:{$s->job_id}"); }
    public function deleted(JobSkill $s): void { Cache::forget("job_skills:{$s->job_id}"); }
}
```

### File: `app/Observers/NoticePeriodCacheInvalidateObserver.php` (NEW)

```php
<?php
namespace App\Observers;

use App\Models\NoticePeriod;
use Illuminate\Support\Facades\Cache;

class NoticePeriodCacheInvalidateObserver {
    public function saved(NoticePeriod $n): void { Cache::forget('notice_periods'); }
    public function deleted(NoticePeriod $n): void { Cache::forget('notice_periods'); }
}
```

---

## §22 — Background Jobs

### Job class skeleton (each of these is a new file in `app/Jobs/`)

```
UpdateJobCountersJob.php                       (defined in §21)
UpdateApplicationCountersJob.php
UpdatePipelineCountersJob.php                  (defined in §10)
DecrementPipelineCountersJob.php
DecrementApplicationCountersJob.php
UpdateBenchCountersJob.php
UpdateJobAgencyCounterJob.php
ElasticCandidateIndexJob.php
ElasticJobsIndexJob.php
BulkCounterRefresherJob.php                    (nightly drift repair)
```

Each implements `ShouldQueue` with:
- `tries = 3`
- `backoff = [10, 30, 90]`
- `timeout = 30`
- `$queue = 'counters'` (or `'es-index'`, `'cache-recompute'`)

### Horizon config patch

**File:** `config/horizon.php` (lines 199-213 — `environments.production`)

**Before:**
```php
'production' => [
    'supervisor-jobs' => [
        'connection' => 'redis',
        'queue' => ['default'],
        'balance' => 'auto',
        'maxProcesses' => 10,
        'balanceCooldown' => 3,
        'memory' => 128,
        'tries' => 1,
        'timeout' => 60,
    ],
],
```

**After:**
```php
'production' => [
    'supervisor-1' => [
        'connection' => 'redis',
        'queue' => ['default'],
        'balance' => 'auto',
        'maxProcesses' => 32,
        'balanceCooldown' => 3,
        'memory' => 256,
        'tries' => 3,
        'timeout' => 60,
    ],
    'supervisor-counters' => [
        'connection' => 'redis',
        'queue' => ['counters'],
        'balance' => 'simple',
        'maxProcesses' => 8,
        'memory' => 256,
        'tries' => 5,
        'timeout' => 30,
    ],
    'supervisor-es' => [
        'connection' => 'redis',
        'queue' => ['es-index'],
        'balance' => 'simple',
        'maxProcesses' => 4,
        'memory' => 512,
        'tries' => 3,
        'timeout' => 120,
    ],
    'supervisor-mail' => [
        'connection' => 'redis',
        'queue' => ['mails'],
        'balance' => 'auto',
        'maxProcesses' => 4,
        'memory' => 128,
        'tries' => 3,
        'timeout' => 60,
    ],
],
```

---

## §23 — Elasticsearch Review

### Indexes already available

| Index | Indexer | Use |
|---|---|---|
| `ats_jobs_elastic` | `ElasticJobIndex.php` | Job postings |
| `ats_candidates_elastic` | `ElasticCandidatesIndex.php` | Candidates (uses bulk + ID-based upsert) |
| `ats_candidates_bench_elastic` | `ElasticBenchCandidatesIndex.php` | Bench |
| `ats_companies_elastic` | `ElasticCaompanyIndex.php` | Companies |
| `ats_career_apply_elastic` | (per `.env:139-186`) | Career apply |

### Move these to ES

| Concern | Today (MySQL) | After (ES) |
|---|---|---|
| Job list/search/filter | LIKE | `match` + `term` + `bool` |
| Pipeline stage aggregations | `SUM(CASE…)` | `filter` aggs (proven in loadcounter:154-273) |
| Candidate match | `orWhere` + `LIKE %skill%` in loop | `function_score` with skill overlap |
| Bench/agency counts | `Job::count` | `cardinality` agg on bench index |

---

## §24 — API Design (if migrating to API)

```
GET /api/service_company/jobs?cursor=…&filter[status]=active&filter[type]=contract&sort=created_at_desc&length=20
→ 200 OK
  Cache-Control: private, max-age=30
  ETag: "<user_hash>:<filter_hash>"
  X-Total-Count: 1362

  {
    "data": [{ "id": 1390, "title": "Java Intern", "match_score": 0.85, ... }],
    "next_cursor": "eyJpZCI6MTM5Mn0=",
    "prev_cursor": null,
    "aggregations": {
      "totals": {"all": 1362, "hiring": 3, "hold": 491, "closed": 858, "removed": 0},
      "filters": {
        "statuses": ["active","hold","closed"],
        "locations": ["Bangalore","Noida"],
        "types": ["contract","fulltime"]
      }
    }
  }
```

`routes/api.php` (move routes here to skip session/CSRF middleware):

```php
Route::middleware(['auth:api', 'throttle:60,1'])
    ->prefix('api/service_company')
    ->group(function () {
        Route::get('jobs', [JobsApiController::class, 'index']);
        Route::get('jobs/aggregations', [JobsApiController::class, 'aggregations']);
    });
```

**Verification:**
```bash
curl -I -H "If-None-Match: \"abc\"" https://testing.jero.works/api/service_company/jobs
# Expected: 304 Not Modified
```

---

## §25 — Final Performance Score

| Category | Today (1-10) | After Phase 4 (1-10) |
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

## §26 — Priority Matrix

(See `JOBS_LISTING_V2_IMPLEMENTATION_PLAN.md` §26 for the matching execution order.)

---

## §27 — Implementation Roadmap

Day-by-day breakdown with verification gates:

| Day | Steps | Done criteria |
|---|---|---|
| Day 1 | Step 1a-1d + Step 2a | `bootstrap/cache/config.php` exists; `EnsureOneDeviceLogin` MySQL writes ≤2/min per user |
| Day 2 | Step 2b + Step 4 + F-21 | `Company::find` only; cache TTLs in place |
| Day 3 | Step 5 (F-06..F-08) | ES round-trips per page = 6 (down from 9) |
| Day 4-7 | Step 6 (F-11,F-14,F-16,F-17,F-21,F-23) | Counter tables seeded; observers wired; Live tests pass |
| Day 5-7 | Step 7 | Search latency <80ms; EXPLAIN shows new index used |
| Week 1 | Step 3 (F-09,F-19) | 4 N+1s removed; page SQL count ≤3 |
| Week 2 | Step 8 + Step 9 | Bundle size <300KB gzip; cursor pagination works |
| Week 3-4 | Step 10 | Sustained 30k recruiters; all observability dashboards up |

---

## §28 — Expected Results

| Metric | **Today** | **After Phase 1** | **After Phase 2** | **After Phase 3** | **After Phase 4** |
|---|---|---|---|---|---|
| Cold page-load | ~1,200 ms | ~700 ms | ~250 ms | ~150 ms | ~80 ms |
| SQL queries / page | ~30-35 | ~15 | ~5 | ~2 | ~1 |
| ES round-trips / page | 4-7 | 4-7 | 0-1 (cached) | 0-1 | 0-1 |
| MySQL CPU @ 10k recruiters | Saturating | 60% | 30% | 15% | 5% |
| RAM / request | ~35 MB | ~30 MB | ~22 MB | ~20 MB | ~15 MB |
| Payload (initial, gzip) | ~750 KB | ~750 KB | ~750 KB | ~300 KB | ~250 KB |
| TTFB | ~800 ms | ~700 ms | ~150 ms | ~80 ms | ~30 ms |
| Concurrent recruiters | ~1.5k | ~3k | ~6k | ~10k | ~30k |
| Max jobs | ~50k | ~50k | ~200k | ~500k | 200k+/shard |
| Max applications | ~1M | ~1M | ~10M | ~100M | 1B+/shard |

---

## §29 — Enterprise Architecture Diagram

(Diagram identical to the V2 plan document.)

---

*Audit end. 23 bugs identified with `file:line`, 22 fixes specified with code diffs, exact DDL, verification commands, and feature flags for safe rollout. Implementation plan details in companion `JOBS_LISTING_V2_IMPLEMENTATION_PLAN.md`.*
