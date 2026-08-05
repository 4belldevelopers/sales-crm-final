# JOBS LISTING V2 — 10-STEP IMPLEMENTATION PLAN (AI-EXECUTION CONTRACT)

> **AI execution contract.** Every step below specifies: exact files to edit (with `file:line`), exact code diffs (before/after), exact DDL with verification SQL, exact new class file paths ready to be created, exact Redis key formats, exact verification commands, exact backout scripts, exact PHPUnit test skeletons, and exact feature flags for safe rollout. An executing AI agent should be able to land every step without further human input.

**Goal:** Make `/service_company/jobs` **5–15× faster**, fix every bug, eliminate the MySQL hot path, support **10,000 concurrent recruiters**, with **no/min MySQL load** and **no/min server load**.

**Targets after V2:**
- Cold page-load: **~1,200ms → ~80ms**
- SQL queries per page: **~30 → ~1**
- MySQL CPU @ 10k recruiters: **Saturating → ~5%**
- RAM per request: **~35MB → ~15MB**
- TTFB: **~800ms → ~30ms**

---

## Reading order

This document is structured for a single AI agent (or a junior dev) to execute step by step. Each step is **idempotent and gated** by a verifiable test/command. Steps may be parallelised by an agent team but the verification commands at the end of each step are the green light to proceed.

| Symbol | Meaning |
|---|---|
| 🔴 | Must fix before V2 ships |
| 🟠 | High-impact, week-1 |
| 🟡 | Medium-impact, weeks 2-3 |
| 🟢 | Optional, enterprise phase |
| ⚠️ | Bug fix; do not regress |
| 🧪 | Add a PHPUnit / Pest test |
| 🆕 | New file to create |
| ✏️ | Edit existing file |
| ⚙️ | Configuration / env change |

---

## Prerequisites

```bash
# Branch
git checkout -b feature/jobs-listing-v2

# Required CLIs (optional — for online schema changes)
which gh-ost pt-online-schema-change 2>/dev/null

# Backup before any DDL
mysqldump --single-transaction --quick --triggers --events jero > /backup/jero_pre_v2.sql.gz

# Warm up
php artisan optimize:clear
time php artisan tinker --execute="echo 'cold boot ok';"
```

### Feature flag scaffold (apply before any other step)

Create the file `config/feature.php` (NEW, not yet present in the codebase):

```php
<?php
return [
    'throttle_ensure_one_device'           => (bool) env('FEATURE_ENSURE_ONE_DEVICE_THROTTLE', false),
    'use_summary_table_job_counters'       => (bool) env('FEATURE_SUMMARY_JOB_COUNTERS', false),
    'use_summary_table_pipeline_counters'  => (bool) env('FEATURE_SUMMARY_PIPELINE_COUNTERS', false),
    'use_summary_table_application_counters' => (bool) env('FEATURE_SUMMARY_APPLICATION_COUNTERS', false),
    'use_redundant_cardinality_fix'        => (bool) env('FEATURE_REDUNDANT_CARDINALITY_FIX', false),
    'use_career_count_api'                 => (bool) env('FEATURE_CAREER_COUNT_API', false),
    'use_prefetched_row_maps'              => (bool) env('FEATURE_PREFETCHED_ROW_MAPS', false),
    'use_event_driven_counters'            => (bool) env('FEATURE_EVENT_DRIVEN_COUNTERS', false),
    'use_es_search'                        => (bool) env('FEATURE_ES_SEARCH', false),
    'use_dataCnt_count_query'              => (bool) env('FEATURE_DATA_COUNT_QUERY', false),
    'use_rec_where_exists'                 => (bool) env('FEATURE_REC_WHERE_EXISTS', false),
    'use_etag'                             => (bool) env('FEATURE_ETAG', false),
];
```

**Verification:** `php artisan tinker --execute="dump(config('feature.throttle_ensure_one_device'));"`
**Acceptance:** prints `false` (defaults off). Toggle to true per-step after the step's tests pass.

---

# STEP 1 — Free Wins (Day 1, Zero Risk)

## 1a. ⚙️ Enable `config:cache` + `route:cache`

**Bug:** `bootstrap/cache/` only has `modules.php`, `packages.php`, `services.php`. No `config.php`, no `routes-v7.php`.

```bash
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache
```

**Verification:**
```bash
ls -la bootstrap/cache/
# Expected: config.php, routes-v7.php, events.php, views.php, modules.php, packages.php, services.php
test -f bootstrap/cache/config.php && echo "config cached OK"
test -f bootstrap/cache/routes-v7.php && echo "routes cached OK"
```

```bash
php artisan optimize
# Equivalent to: config:cache + route:cache + event:cache + view:cache
```

**Backout:** `php artisan optimize:clear`

**Expected gain:** -30 to -50ms TTFB per request.

## 1b. ⚙️ Verify `SESSION_DRIVER=redis`

**Edit** `.env:45` (if needed):
```env
SESSION_DRIVER=redis
SESSION_CONNECTION=default
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
```

**Verification:**
```bash
php artisan tinker --execute="echo config('session.driver');"
# Expected: redis
```

## 1c. ⚙️ `CACHE_PREFIX=jero_`

**Edit** `.env:57`:
```env
CACHE_PREFIX=jero_
```

**Verification:**
```bash
php artisan tinker --execute="\Cache::put('test_key', 'hello', 60); echo \Cache::get('test_key');"
redis-cli -n 1 KEYS "jero_*"
redis-cli -n 1 DEL "jero_test_key"
```

## 1d. ⚠️🔴 Throttle `EnsureOneDeviceLogin` UPDATE

**File to edit:** `app/Http/Middleware/EnsureOneDeviceLogin.php`
**Lines:** 18-65 (the full middleware body)

**Before (current state):**
```php
public function handle(Request $request, Closure $next)
{
    // ... auth check + token_check logic ...
    if (Session::has('token_check')) {
        session()->forget('super_admin_login_code');
        $user->save();                                     // ← BUG: write each request
    } else {
        // status check
        $user->session_id = $currentSessionId;
        $user->save();                                     // ← BUG: write each request
    }
    return $next($request);
}
```

**After:**
```php
public function handle(Request $request, Closure $next)
{
    if (!auth()->check()) return $next($request);

    $user = auth()->user();
    $currentSessionId = session()->getId();

    $expected = Cache::get('user_session_' . $user->id);
    if ($expected === $currentSessionId) {
        return $next($request);                    // Already in sync — no DB write
    }

    if (!config('feature.throttle_ensure_one_device')) {
        return $next($request);                    // Feature flag off → preserve old behaviour
    }

    $throttleKey = "ensure_one_device_save_{$user->id}";
    if (Cache::has($throttleKey)) {
        return $next($request);                    // Throttled
    }

    // Persist change
    $user->session_id = $currentSessionId;
    $user->save();
    Cache::put('user_session_' . $user->id, $currentSessionId, 3600);
    Cache::put($throttleKey, true, 60);            // 1 write per minute max

    return $next($request);
}
```

**🧪 Add test:** `tests/Feature/EnsureOneDeviceLoginThrottleTest.php`

```php
<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EnsureOneDeviceLoginThrottleTest extends TestCase
{
    public function test_no_users_update_when_session_id_unchanged(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        config(['feature.throttle_ensure_one_device' => true]);

        DB::enableQueryLog();
        DB::flushQueryLog();

        for ($i = 0; $i < 5; $i++) {
            $this->get('/');
        }

        $count = collect(DB::getQueryLog())
            ->filter(fn ($q) => str_contains($q['query'], 'update `users`'))
            ->count();

        $this->assertLessThanOrEqual(1, $count, 'Expected ≤ 1 UPDATE on users across 5 requests');
    }
}
```

**Run:** `php artisan test tests/Feature/EnsureOneDeviceLoginThrottleTest.php`

**Verification:**
```bash
mysql -e "SET GLOBAL general_log = 'ON';" && \
  for i in $(seq 100); do curl -s -o /dev/null http://testing.jero.works/service_company/jobs; done && \
  mysql -e "SELECT COUNT(*) FROM mysql.general_log WHERE argument LIKE '%UPDATE users SET session_id%';" && \
  mysql -e "SET GLOBAL general_log = 'OFF';"
# Expected: ≤ 2
```

**Backout:** git revert the file.

**Expected gain:** -90% MySQL write throughput. Removes the row-lock bottleneck at 10k recruiters.

---

# STEP 2 — Database Index Hardening (Day 1)

## 2a. ⚠️🔴 Composite indexes

**Source:** `database/schema/mysql-schema.sql:2169-2218` (jobs), `:1903-2020` (job_applications).

### Apply with `gh-ost` (online, no lock)

```bash
gh-ost --host=db.jero.works --port=3306 --user=migrator --password=*** \
  --database=jero --table=jobs \
  --alter="ADD INDEX idx_jobs_company_status_created (company_id, status, created_at DESC)" \
  --allow-on-master --execute

gh-ost --host=db.jero.works --port=3306 --user=migrator --password=*** \
  --database=jero --table=jobs \
  --alter="ADD INDEX idx_jobs_priority_created (priority DESC, created_at DESC)" \
  --allow-on-master --execute

gh-ost --host=db.jero.works --port=3306 --user=migrator --password=*** \
  --database=jero --table=jobs \
  --alter="DROP INDEX idx_jobs_company_id, DROP INDEX idx_company_id, DROP INDEX jobs_company_id_foreign" \
  --allow-on-master --execute

gh-ost --host=db.jero.works --port=3306 --user=migrator --password=*** \
  --database=jero --table=job_applications \
  --alter="ADD INDEX idx_job_app_count (job_id, master_status_id, deleted_at), \
           ADD INDEX idx_job_app_company (company_id, master_status_id), \
           ADD INDEX idx_job_app_created (job_id, created_at DESC), \
           ADD INDEX idx_job_app_deleted (deleted_at)" \
  --allow-on-master --execute

gh-ost --host=db.jero.works --port=3306 --user=migrator --password=*** \
  --database=jero --table=job_application_skills \
  --alter="ADD INDEX idx_jas_app (job_application_id)" \
  --allow-on-master --execute

gh-ost --host=db.jero.works --port=3306 --user=migrator --password=*** \
  --database=jero --table=candidate_job_skill_exp \
  --alter="ADD INDEX idx_cjse_candidate_job (candidate_id, job_id), \
           ADD INDEX idx_cjse_job (job_id)" \
  --allow-on-master --execute

gh-ost --host=db.jero.works --port=3306 --user=migrator --password=*** \
  --database=jero --table=bench_applications \
  --alter="ADD INDEX idx_bench_company_status (company_id, status), \
           ADD INDEX idx_bench_job (job_id)" \
  --allow-on-master --execute

gh-ost --host=db.jero.works --port=3306 --user=migrator --password=*** \
  --database=jero --table=candidate_status \
  --alter="ADD INDEX idx_cs_parent (parent)" \
  --allow-on-master --execute

gh-ost --host=db.jero.works --port=3306 --user=migrator --password=*** \
  --database=jero --table=recommendation_relation \
  --alter="ADD INDEX idx_rec_to_company_final (to_company_id, final_status), \
           ADD INDEX idx_rec_job_user (job_id)" \
  --allow-on-master --execute
```

### Plain DDL (apply in low-traffic window, ~5-15 min per table)

```sql
ALTER TABLE jobs
  ADD INDEX idx_jobs_company_status_created (company_id, status, created_at DESC),
  ADD INDEX idx_jobs_priority_created       (priority DESC, created_at DESC),
  DROP INDEX idx_jobs_company_id,
  DROP INDEX idx_company_id,
  DROP INDEX jobs_company_id_foreign;

ALTER TABLE job_applications
  ADD INDEX idx_job_app_count    (job_id, master_status_id, deleted_at),
  ADD INDEX idx_job_app_company  (company_id, master_status_id),
  ADD INDEX idx_job_app_created  (job_id, created_at DESC),
  ADD INDEX idx_job_app_deleted  (deleted_at);

ALTER TABLE job_application_skills
  ADD INDEX idx_jas_app (job_application_id);

ALTER TABLE candidate_job_skill_exp
  ADD INDEX idx_cjse_candidate_job (candidate_id, job_id),
  ADD INDEX idx_cjse_job           (job_id);

ALTER TABLE bench_applications
  ADD INDEX idx_bench_company_status (company_id, status),
  ADD INDEX idx_bench_job            (job_id);

ALTER TABLE candidate_status
  ADD INDEX idx_cs_parent (parent);

ALTER TABLE recommendation_relation
  ADD INDEX idx_rec_to_company_final (to_company_id, final_status),
  ADD INDEX idx_rec_job_user         (job_id);
```

### 🧪 Migration (records the change in source control)

Create `database/migrations/2026_08_01_010000_jobs_listing_v2_indexes.php`:

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE jobs
            ADD INDEX idx_jobs_company_status_created (company_id, status, created_at DESC),
            ADD INDEX idx_jobs_priority_created (priority DESC, created_at DESC),
            DROP INDEX idx_jobs_company_id,
            DROP INDEX idx_company_id,
            DROP INDEX jobs_company_id_foreign');

        DB::statement('ALTER TABLE job_applications
            ADD INDEX idx_job_app_count (job_id, master_status_id, deleted_at),
            ADD INDEX idx_job_app_company (company_id, master_status_id),
            ADD INDEX idx_job_app_created (job_id, created_at DESC),
            ADD INDEX idx_job_app_deleted (deleted_at)');

        DB::statement('ALTER TABLE job_application_skills
            ADD INDEX idx_jas_app (job_application_id)');

        DB::statement('ALTER TABLE candidate_job_skill_exp
            ADD INDEX idx_cjse_candidate_job (candidate_id, job_id),
            ADD INDEX idx_cjse_job (job_id)');

        DB::statement('ALTER TABLE bench_applications
            ADD INDEX idx_bench_company_status (company_id, status),
            ADD INDEX idx_bench_job (job_id)');

        DB::statement('ALTER TABLE candidate_status
            ADD INDEX idx_cs_parent (parent)');

        DB::statement('ALTER TABLE recommendation_relation
            ADD INDEX idx_rec_to_company_final (to_company_id, final_status),
            ADD INDEX idx_rec_job_user (job_id)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE jobs
            DROP INDEX idx_jobs_company_status_created,
            DROP INDEX idx_jobs_priority_created,
            ADD INDEX idx_jobs_company_id (company_id),
            ADD INDEX idx_company_id (company_id),
            ADD INDEX jobs_company_id_foreign (company_id)');

        DB::statement('ALTER TABLE job_applications
            DROP INDEX idx_job_app_count,
            DROP INDEX idx_job_app_company,
            DROP INDEX idx_job_app_created,
            DROP INDEX idx_job_app_deleted');

        DB::statement('ALTER TABLE job_application_skills DROP INDEX idx_jas_app');
        DB::statement('ALTER TABLE candidate_job_skill_exp
            DROP INDEX idx_cjse_candidate_job,
            DROP INDEX idx_cjse_job');
        DB::statement('ALTER TABLE bench_applications
            DROP INDEX idx_bench_company_status,
            DROP INDEX idx_bench_job');
        DB::statement('ALTER TABLE candidate_status DROP INDEX idx_cs_parent');
        DB::statement('ALTER TABLE recommendation_relation
            DROP INDEX idx_rec_to_company_final,
            DROP INDEX idx_rec_job_user');
    }
};
```

**Verification post-apply:**
```sql
SHOW INDEX FROM jobs WHERE Key_name IN ('idx_jobs_company_status_created','idx_jobs_priority_created');
SHOW INDEX FROM job_applications WHERE Key_name = 'idx_job_app_count';

EXPLAIN SELECT id FROM jobs
WHERE company_id = 1 AND status = 'active'
ORDER BY created_at DESC LIMIT 20;
-- Post-fix: type=ref, key=idx_jobs_company_status_created, rows=20, Extra=Using where (no filesort)

EXPLAIN SELECT jas.* FROM job_application_skills jas
JOIN job_applications ja ON ja.id = jas.job_application_id
WHERE ja.job_id = 100 LIMIT 50;
-- Post-fix: type=ref, key=idx_jas_app, rows=50

EXPLAIN SELECT job_id FROM recommendation_relation
WHERE to_company_id = 5 AND final_status IN (1, 2) LIMIT 50;
-- Post-fix: type=range, key=idx_rec_to_company_final, rows=12
```

**Expected gain:** -60% scanner time on list/sort, -95% on stage counts.

## 2b. ⚙️🔴 Raise `max_connections` and pool

**On MySQL host** (`.my.cnf` or `my.cnf`):
```ini
[mysqld]
max_connections = 2048
max_user_connections = 1984
innodb_buffer_pool_size = 24G
table_open_cache = 8192
thread_cache_size = 256
```

**Cloud (recommended):**
- AWS RDS: use RDS Proxy in front; set `max_connections=2048` in DB parameter group.
- Self-hosted: ProxySQL with `mysql-default_server_version` set.

**Verification:**
```sql
SHOW VARIABLES LIKE 'max_connections';
SHOW STATUS LIKE 'Max_used_connections%';
```

**Expected gain:** No more "Too many connections" at 10k recruiters.

---

# STEP 3 — Fix 4 N+1 Queries in the Blade (Days 2-3, ⚠️🔴)

## 3a. 🆕 Pre-fetch maps in the controller

**File to create:** `app/Traits/PreparesJobsRowMaps.php`

```php
<?php
namespace App\Traits;

use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\DB;

trait PreparesJobsRowMaps
{
    /**
     * Pre-fetch the per-row data the `all_apptracker.blade.php` Blade needs.
     * Used to eliminate 4 N+1 queries.
     */
    protected function prepareRowMaps(array $rows, bool $checkDuplicates = false): array
    {
        $jobIds = collect($rows)->pluck('job_id')->filter()->unique()->values()->all();
        if (empty($jobIds)) {
            return ['jobs' => collect(), 'skills' => collect(), 'match' => [], 'duplicates' => []];
        }

        // 1) Job-level columns the view needs (was N+1 #1 from all_apptracker.blade.php:28)
        $jobs = DB::table('jobs')
            ->select('id', 'max_sal_mode', 'max_salary_currency')
            ->whereIn('id', $jobIds)
            ->get()
            ->keyBy('id');

        // 2) Skills (was N+1 #2 and #3 from all_apptracker.blade.php:156 & :181)
        $skillsByJob = DB::table('job_metronics_skills')
            ->select('job_id', 'skill_name')
            ->whereIn('job_id', $jobIds)
            ->get()
            ->groupBy('job_id');

        // 3) Match% — computed once per job_id, not per row
        $matchMap = [];
        foreach ($jobIds as $jobId) {
            $skills = $skillsByJob[$jobId] ?? null;
            $count = $skills ? $skills->count() : 0;
            $matchCount = $count
                ? GeneralMethod::skill_match_count($jobId, $skills->pluck('skill_name')->toArray())
                : 0;
            $matchMap[$jobId] = $count ? round(($matchCount / $count) * 100, 2) : 0;
        }

        // 4) Duplicates — single query (was N+1 #4 from all_apptracker.blade.php:76-108)
        $duplicateMap = [];
        if ($checkDuplicates) {
            $emails = collect($rows)->pluck('email')->filter()->unique()->values()->all();
            $phones = collect($rows)->pluck('phone')->filter()->unique()->values()->all();
            if ($emails || $phones) {
                $dups = DB::table('job_applications')
                    ->select('email', 'phone', DB::raw('COUNT(*) c'))
                    ->where(function ($q) use ($emails, $phones) {
                        if ($emails) $q->whereIn('email', $emails);
                        if ($emails && $phones) $q->orWhereIn('phone', $phones);
                        elseif ($phones) $q->whereIn('phone', $phones);
                    })
                    ->groupBy('email', 'phone')
                    ->get();
                foreach ($dups as $d) {
                    $duplicateMap[($d->email ?? '') . '|' . ($d->phone ?? '')] = $d->c;
                }
            }
        }

        return [
            'jobs'       => $jobs,
            'skills'     => $skillsByJob,
            'match'      => $matchMap,
            'duplicates' => $duplicateMap,
        ];
    }
}
```

## 3b. 🆕 Helper method `skill_match_count`

**File to edit:** `app/Helper/GeneralMethod.php`

Add a NEW pure (no DB) method (keep `skill_match_percentage`/`skill_percentage` until old call-sites are migrated — backward-compatible):

```php
/**
 * Compute skill match count WITHOUT issuing a DB query.
 */
public static function skill_match_count(int $jobId, array $candidateSkills): int
{
    $jobSkills = self::getJobSkillsCache($jobId);
    $matched = 0;
    foreach ($jobSkills as $js) {
        foreach ($candidateSkills as $cs) {
            if (self::skillMatchesPhrasePrefix($cs, $js)) {
                $matched++;
                break;
            }
        }
    }
    return $matched;
}

private static array $jobSkillsCache = [];
protected static function getJobSkillsCache(int $jobId): array
{
    if (isset(self::$jobSkillsCache[$jobId])) {
        return self::$jobSkillsCache[$jobId];
    }
    return self::$jobSkillsCache[$jobId] = Cache::remember(
        "job_skills:{$jobId}", 86400,
        fn () => DB::table('job_metronics_skills')
            ->where('job_id', $jobId)
            ->pluck('skill_name')
            ->map(fn ($s) => strtolower(trim($s)))
            ->all()
    );
}
```

## 3c. ✏️ Wire `prepareRowMaps` into `ServiceCompanyAppTrackerController::allDataAjax`

**File:** `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php`

Add `use App\Traits\PreparesJobsRowMaps;` and `use PreparesJobsRowMaps;` in the class body.

```php
class ServiceCompanyAppTrackerController extends Controller
{
    use PreparesJobsRowMaps;
    // ...
    public function allDataAjax(Request $request)
    {
        // … existing logic populating $data …

        if (!config('feature.use_prefetched_row_maps')) {
            return view('service_company.ajax_pages.all_apptracker', $data);  // Legacy path
        }

        $maps = $this->prepareRowMaps($data, $request->boolean('duplicate_check'));

        return view('service_company.ajax_pages.all_apptracker', array_merge($data, [
            'jobsMap'      => $maps['jobs'],
            'skillsMap'    => $maps['skills'],
            'matchMap'     => $maps['match'],
            'duplicateMap' => $maps['duplicates'],
        ]));
    }
}
```

## 3d. ✏️ Patch `all_apptracker.blade.php` to read from maps

**File:** `resources/views/service_company/ajax_pages/all_apptracker.blade.php`
**Lines:** :23-220 (the entire `@foreach ($data as $rowArr)` block).

| Line | Current code | Replace with |
|---|---|---|
| `:28` | `$jobMode = DB::table('jobs')->where('id','=',$row->jobId)->first()->max_sal_mode ?? '';` | `$jobMode = optional($jobsMap->get($row->job_id))->max_sal_mode;` |
| `:76-108` | `if ($duplicate == 1) { $cnt = DB::table('job_applications')->where('email', …)->count(); … }` | `$cnt = $duplicateMap[($row->email ?? '').'|'.($row->phone ?? '')] ?? 0;` |
| `:156` | `$percentage = GeneralMethod::skill_percentage($row->job_id, $skillArr);` | `$percentage = $matchMap[$row->job_id] ?? 0;` |
| `:181` | `$jobRequiredSkills = DB::table('job_metronics_skills')->where('job_id', $row->job_id)->pluck('skill_name');` | `$jobRequiredSkills = $skillsMap->get($row->job_id, collect())->pluck('skill_name');` |

**Verification:**
```bash
mysql -e "SET GLOBAL general_log = 'ON';" && \
  curl -s http://testing.jero.works/jobs_ajax/all_data > /dev/null && \
  mysql -e "SET GLOBAL general_log_file = '/tmp/gl.log';" && \
  curl -s http://testing.jero.works/jobs_ajax/all_data > /dev/null && \
  grep -c "job_metronics_skills" /tmp/gl.log
# Post-fix: 1 (not 21+)
```

**🧪 Add test:** `tests/Feature/NoNPlusOneInJobsAjaxTest.php`

```php
<?php
namespace Tests\Feature;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobMetronicsSkill;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NoNPlusOneInJobsAjaxTest extends TestCase
{
    public function test_jobs_data_ajax_does_not_n_plus_one(): void
    {
        $company = Company::factory()->create();
        $user    = User::factory()->for($company)->create();
        $job     = Job::factory()->for($company)->create();
        JobMetronicsSkill::factory()->for($job)->count(5)->create();

        foreach (range(1, 20) as $i) {
            JobApplication::factory()->for($job)->for($company)->create();
        }

        $this->actingAs($user);
        config(['feature.use_prefetched_row_maps' => true]);

        DB::enableQueryLog();
        DB::flushQueryLog();
        $resp = $this->get('/jobs_ajax/all_data?job_id=' . $job->id);
        $log = DB::getQueryLog();

        $jobSkillHits = collect($log)
            ->filter(fn ($q) => str_contains($q['query'], 'job_metronics_skills'))
            ->count();

        $resp->assertOk();
        $this->assertLessThanOrEqual(1, $jobSkillHits, 'Expected ≤ 1 query on job_metronics_skills, got ' . $jobSkillHits);
    }
}
```

**Expected gain:** -50 SQL queries per page. From ~80 → ~5 SQL queries per page.

---

# STEP 4 — Replace `Company::all()` + Cache Static Dropdowns (Day 2)

## 4a. ⚠️🟠 Replace `Company::all()` with single-tenant lookup

**File to edit:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php`
**Line:** 1277

**Before:**
```php
$this->companies = Company::all();
```

**After:**
```php
$this->company = Cache::remember('company:'.$this->user->company_id, 3600,
    fn () => Company::find($this->user->company_id)
);
```

Audit any view uses of `$companies`:

```bash
grep -n "companies" resources/views/service_company/jobs/*.blade.php
```

**Verification:**
```bash
php artisan tinker --execute="echo \Cache::has('company:1') ? 'cached' : 'uncached';"
```

## 4b. ✏️ Cache `NoticePeriod`, `LanguageSetting`, `GlobalSetting`

**File to edit:** `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php`
**Lines:** :100, :126 (NoticePeriod::all()) and equivalents for LanguageSetting/GlobalSetting.

```php
use Illuminate\Support\Facades\Cache;

class ServiceCompanyAppTrackerController extends Controller
{
    protected function noticePeriods()
    {
        return Cache::remember('notice_periods', 86400,
            fn () => \App\Models\NoticePeriod::orderBy('days')->get()
        );
    }

    protected function languageSettings()
    {
        return Cache::remember('language_settings_enabled', 86400,
            fn () => \App\Models\LanguageSetting::where('status','enabled')->orderBy('title')->get()
        );
    }

    protected function globalSetting()
    {
        return Cache::remember('global_settings', 86400,
            fn () => \App\Models\GlobalSetting::with('currency')->first()
        );
    }
}
```

**Replace every call site:** `NoticePeriod::all()` → `$this->noticePeriods()`.

### Cache-invalidation observers (NEW)

🆕 `app/Observers/NoticePeriodCacheInvalidateObserver.php`:

```php
<?php
namespace App\Observers;

use App\Models\NoticePeriod;
use App\Models\LanguageSetting;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Cache;

class NoticePeriodCacheInvalidateObserver
{
    public function saved(NoticePeriod $m): void { Cache::forget('notice_periods'); }
    public function deleted(NoticePeriod $m): void { Cache::forget('notice_periods'); }
}

class LanguageSettingCacheInvalidateObserver
{
    public function saved(LanguageSetting $m): void { Cache::forget('language_settings_enabled'); }
    public function deleted(LanguageSetting $m): void { Cache::forget('language_settings_enabled'); }
}

class GlobalSettingCacheInvalidateObserver
{
    public function saved(GlobalSetting $m): void { Cache::forget('global_settings'); }
    public function deleted(GlobalSetting $m): void { Cache::forget('global_settings'); }
}
```

🆕 In `app/Providers/AppServiceProvider.php::boot()` (append):

```php
\App\Models\NoticePeriod::observe(\App\Observers\NoticePeriodCacheInvalidateObserver::class);
\App\Models\LanguageSetting::observe(\App\Observers\LanguageSettingCacheInvalidateObserver::class);
\App\Models\GlobalSetting::observe(\App\Observers\GlobalSettingCacheInvalidateObserver::class);
```

**Verification:**
```bash
for i in 1 2 3 4 5; do curl -s http://testing.jero.works/service_company/apptracker > /dev/null; done
grep -c "notice_period" /tmp/gl.log
# Expected: 1
```

**Expected gain:** -3 SQL queries per page; -15 SQL when BaseController middleware is also cached.

---

# STEP 5 — Cache + Refactor `custome_count_all` ES Calls (Days 3-4, ⚠️🟠)

## 5a. ⚠️🔴 Wrap `custome_count_all` in `Cache::flexible`

**File to edit:** `app/Helper/GeneralMethod.php:11665-12621`
**Lines:** The function header is at `:11665`.

**Before:**
```php
public static function custome_count_all($id) {
    // 6 cards × 1 ES each + 3 redundant `_search` follow-ups + 2 MySQL plucks
    // Returns: ['pool_counter' => int, 'existing_counter' => int, …]
}
```

**After — extract to two functions; cache only the outer one:**

```php
public static function custome_count_all(int $id): array
{
    if (!config('feature.use_summary_table_job_counters')) {
        return self::custome_count_all_inner($id);
    }

    $hash = substr(md5(json_encode([
        self::jobSkillsHash($id),
    ])), 0, 8);

    return Cache::flexible(
        "custome_count_all:{$id}:{$hash}",
        [60, 300],
        fn () => self::custome_count_all_inner($id)
    );
}

private static function custome_count_all_inner(int $id): array
{
    // All the existing logic goes here verbatim, unchanged
    return [
        'pool_counter' => …,
        'existing_counter' => …,
        'internal_bench_counter' => …,
        'public_bench_counter' => …,
        'recomnded_counter' => …,
        'career_counter' => …,
    ];
}

private static function jobSkillsHash(int $id): string
{
    return Cache::remember("job_skills_hash:{$id}", 86400, function () use ($id) {
        $skills = DB::table('job_metronics_skills')
            ->where('job_id', $id)
            ->pluck('skill_name')
            ->map(fn ($s) => strtolower(trim($s)))
            ->all();
        return json_encode($skills);
    });
}
```

**Verification:**
```bash
redis-cli -n 1 GET "jero_custome_count_all:1390:…"
# Then refresh; expect cache hit
```

**Expected gain:** -9 ES round-trips → 0 on hot reads.

## 5b. ⚠️🟠 Merge redundant `cardinality` 2nd `_search` calls

**File to edit:** `app/Helper/GeneralMethod.php`
**Lines:** :12029-12071 (existing_counter), :12230-12268 (internal_bench), :12466-12504 (public_bench)

**Before:**
```php
$params1 = [
    'index' => ELASTIC_CANDIDATES,
    'body' => [
        'query' => [...],
        'collapse' => ['field' => 'email'],
    ],
];
$resp1 = $client->search($params1);

$params2 = [
    'size' => 0,
    'index' => ELASTIC_CANDIDATES,
    'body' => [
        'aggs' => ['unique_emails' => ['cardinality' => ['field' => 'email']]],
        'query' => [...same as $params1['body']['query']...]
    ],
];
$resp2 = $client->search($params2);
$totalCollapsed = $resp2['aggregations']['unique_emails']['value'];
```

**After (gated by feature flag):**
```php
if (!config('feature.use_redundant_cardinality_fix')) {
    return self::custome_count_all_legacy($id);
}

$params = [
    'index' => ELASTIC_CANDIDATES,
    'body' => [
        'query' => [...],
        'collapse' => ['field' => 'email'],
        'aggs'   => ['unique_emails' => ['cardinality' => ['field' => 'email']]],
        'size'   => 0,
    ],
];
$resp = $client->search($params);

$total = $resp['hits']['total']['value'];
$totalCollapsed = $resp['aggregations']['unique_emails']['value'];
```

## 5c. ⚠️🟠 Career counter: use ES `_count` API

**File:** `app/Helper/GeneralMethod.php:12581-12603`

**Before:**
```php
$body = ['size' => 10, 'query' => [...career bool clauses...]];
$response = $client->search(['index' => ELASTIC_CAREER_APPLY, 'body' => $body]);
$career = $response['hits']['total']['value'];
```

**After (gated):**
```php
if (!config('feature.use_career_count_api')) {
    return self::careerLegacy($id);
}

$body = ['query' => [...career bool clauses...]];
$response = $client->count(['index' => ELASTIC_CAREER_APPLY, 'body' => $body]);
$career = $response['count'];
```

**Verification:**
```bash
# ES query log: 0 _search on ELASTIC_CAREER_APPLY; 1 _count per page
```

**Expected gain:** -3 ES round-trips per page.

---

# STEP 6 — Event-Driven Counter Tables + Observers (Days 4-7, ⚠️🔴)

This is the largest code-block-heavy step.

## 6a-d. 🆕 Migrations (4 files)

`database/migrations/2026_08_01_020000_create_job_counters_table.php`:
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
    public function down(): void { Schema::dropIfExists('job_counters'); }
};
```

`database/migrations/2026_08_01_020001_create_pipeline_counters_table.php`:
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pipeline_counters', function (Blueprint $t) {
            $t->unsignedInteger('job_id')->primary();
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('sourcing')->default(0);
            $t->unsignedInteger('screening')->default(0);
            $t->unsignedInteger('evaluation')->default(0);
            $t->unsignedInteger('negotiation')->default(0);
            $t->unsignedInteger('offered')->default(0);
            $t->unsignedInteger('joined')->default(0);
            $t->unsignedInteger('rejected')->default(0);
            $t->boolean('fresh_data')->default(1);
            $t->timestamp('last_event_at')->nullable();
            $t->index(['job_id', 'company_id'], 'idx_job_company');
            $t->index(['fresh_data', 'last_event_at'], 'idx_fresh');
        });
    }
    public function down(): void { Schema::dropIfExists('pipeline_counters'); }
};
```

`database/migrations/2026_08_01_020002_create_application_counters_table.php`:
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('application_counters', function (Blueprint $t) {
            $t->unsignedInteger('job_id')->primary();
            $t->unsignedInteger('total_apps')->default(0);
            $t->unsignedInteger('interview_apps')->default(0);
            $t->unsignedInteger('offer_apps')->default(0);
            $t->unsignedInteger('rejected_apps')->default(0);
            $t->timestamp('last_event_at')->nullable();
            $t->index('last_event_at', 'idx_last');
        });
    }
    public function down(): void { Schema::dropIfExists('application_counters'); }
};
```

`database/migrations/2026_08_01_020003_create_job_agency_counter_table.php`:
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('job_agency_counter', function (Blueprint $t) {
            $t->unsignedInteger('job_id');
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('matching_count')->default(0);
            $t->unsignedInteger('assigned_count')->default(0);
            $t->timestamp('last_event_at')->nullable();
            $t->primary(['job_id', 'company_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('job_agency_counter'); }
};
```

## 6e. 🆕 Seed initial counters from production data

`app/Console/Commands/SeedCounterTablesFromLive.php` (NEW):
```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedCounterTablesFromLive extends Command
{
    protected $signature = 'counters:seed-from-live {--dry-run}';
    protected $description = 'One-shot bootstrap: build counter tables from current live state.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        // job_counters
        $rows = DB::select("
            SELECT company_id,
                   COUNT(*) AS total_jobs,
                   SUM(status = 'active')  AS hiring_jobs,
                   SUM(status = 'hold')    AS on_hold_jobs,
                   SUM(status = 'inactive' AND deleted_at IS NULL) AS closed_jobs,
                   SUM(deleted_at IS NOT NULL) AS removed_jobs,
                   MAX(id) AS last_job_id
            FROM jobs
            GROUP BY company_id
        ");
        foreach ($rows as $r) {
            if ($dry) { $this->line("[dry] job_counters cid={$r->company_id}"); continue; }
            DB::table('job_counters')->updateOrInsert(
                ['company_id' => $r->company_id],
                [
                    'total_jobs'    => (int) $r->total_jobs,
                    'hiring_jobs'   => (int) $r->hiring_jobs,
                    'on_hold_jobs'  => (int) $r->on_hold_jobs,
                    'closed_jobs'   => (int) $r->closed_jobs,
                    'removed_jobs'  => (int) $r->removed_jobs,
                    'last_job_id'   => (int) $r->last_job_id,
                    'last_event_at' => now(),
                ]
            );
        }

        // pipeline_counters
        $rows = DB::select("
            SELECT job_id, company_id,
                   SUM(master_status_id = 1) AS sourcing,
                   SUM(master_status_id = 2) AS screening,
                   SUM(master_status_id = 3) AS evaluation,
                   SUM(master_status_id = 4) AS negotiation,
                   SUM(master_status_id = 5) AS offered,
                   SUM(master_status_id = 6) AS joined,
                   SUM(master_status_id IN (7,9)) AS rejected
            FROM job_applications
            WHERE deleted_at IS NULL
            GROUP BY job_id, company_id
        ");
        foreach ($rows as $r) {
            if ($dry) { $this->line("[dry] pipeline_counters job_id={$r->job_id}"); continue; }
            DB::table('pipeline_counters')->updateOrInsert(
                ['job_id' => $r->job_id, 'company_id' => $r->company_id],
                [
                    'sourcing'    => (int) $r->sourcing,
                    'screening'   => (int) $r->screening,
                    'evaluation'  => (int) $r->evaluation,
                    'negotiation' => (int) $r->negotiation,
                    'offered'     => (int) $r->offered,
                    'joined'      => (int) $r->joined,
                    'rejected'    => (int) $r->rejected,
                    'fresh_data'  => 0,
                    'last_event_at' => now(),
                ]
            );
        }

        // application_counters
        $rows = DB::select("
            SELECT job_id,
                   COUNT(*) AS total_apps,
                   SUM(child_status_id IN (1,2,3)) AS interview_apps,
                   SUM(master_status_id IN (5,6)) AS offer_apps,
                   SUM(master_status_id IN (7,9)) AS rejected_apps
            FROM job_applications
            WHERE deleted_at IS NULL
            GROUP BY job_id
        ");
        foreach ($rows as $r) {
            if ($dry) { $this->line("[dry] application_counters job_id={$r->job_id}"); continue; }
            DB::table('application_counters')->updateOrInsert(
                ['job_id' => $r->job_id],
                [
                    'total_apps'     => (int) $r->total_apps,
                    'interview_apps' => (int) $r->interview_apps,
                    'offer_apps'     => (int) $r->offer_apps,
                    'rejected_apps'  => (int) $r->rejected_apps,
                    'last_event_at'  => now(),
                ]
            );
        }

        $this->info('Counter tables seeded.');
        return self::SUCCESS;
    }
}
```

**Run dry first, then live:**
```bash
php artisan counters:seed-from-live --dry-run
php artisan counters:seed-from-live
```

## 6f. 🆕 Observers + Job classes

🆕 `app/Observers/JobObserver.php`:
```php
<?php
namespace App\Observers;

use App\Jobs\UpdateJobCountersJob;
use App\Models\Job;
use Illuminate\Support\Facades\Cache;

class JobObserver {
    public function saved(Job $job): void {
        Cache::forget("custome_count_all:{$job->id}:" . '*');
        UpdateJobCountersJob::dispatch($job->id)->onQueue('counters');
    }
    public function deleted(Job $job): void {
        Cache::forget("custome_count_all:{$job->id}:" . '*');
        UpdateJobCountersJob::dispatch($job->id, isDelete: true)->onQueue('counters');
    }
}
```

🆕 `app/Observers/JobApplicationObserver.php`:
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
        if ($app->isDirty('photo')) {
            $oldPhoto = $app->getOriginal('photo');
            if ($oldPhoto) @unlink(storage_path('app/' . $oldPhoto));
        }
        if (!$app->company_id) {
            $app->company_id = $app->job?->company_id;
            $app->saveQuietly();
        }

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

🆕 `app/Observers/BenchApplicationObserver.php`:
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

🆕 `app/Observers/RecommendationRelationObserver.php`:
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

🆕 `app/Observers/JobSkillObserver.php`:
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

🆕 `app/Jobs/UpdateJobCountersJob.php`:
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
        $delta = $this->isDelete ? -1 : 1;
        $key = "job_counters:{$companyId}";

        Redis::pipeline(function ($pipe) use ($key, $status, $delta) {
            $pipe->hincrby($key, 'total_jobs', $delta);
            $pipe->hincrby($key, "{$status}_jobs", $delta);
            $pipe->hset($key, 'last_event_at', now()->toIso8601String());
        });

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

🆕 `app/Jobs/UpdatePipelineCountersJob.php`:
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

    public function __construct(public int $jobApplicationId) {}

    public function handle(): void {
        $app = JobApplication::withTrashed()->find($this->jobApplicationId);
        if (!$app || $app->trashed()) return;

        $stage = $this->stageFromMasterStatus($app->master_status_id);
        $jobId = $app->job_id;

        Redis::pipeline(function ($pipe) use ($jobId, $stage) {
            $pipe->hincrby("pipeline:{$jobId}", $stage, 1);
            $pipe->hset("pipeline:{$jobId}", 'last_event_at', now()->toIso8601String());
        });

        DB::table('pipeline_counters')->updateOrInsert(
            ['job_id' => $jobId, 'company_id' => $app->company_id],
            [
                $stage        => DB::raw("{$stage} + 1"),
                'last_event_at' => now(),
                'fresh_data'  => 0,
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

(Decrement variants — `DecrementPipelineCountersJob`, `DecrementApplicationCountersJob` — same shape with delta=-1.)

`UpdateApplicationCountersJob`, `UpdateBenchCountersJob`, `UpdateJobAgencyCounterJob`, `ElasticCandidateIndexJob` — same skeleton with appropriate fields.

## 6g. ✏️ Register observers in `AppServiceProvider::boot()`

**File:** `app/Providers/AppServiceProvider.php`

```php
public function boot(): void
{
    // … existing code …

    $observerRegistry = config('feature.use_event_driven_counters') ? [
        \App\Models\Job::class                    => \App\Observers\JobObserver::class,
        \App\Models\JobApplication::class         => \App\Observers\JobApplicationObserver::class,
        \App\Models\BenchApplication::class       => \App\Observers\BenchApplicationObserver::class,
        \App\Models\RecommendationRelation::class => \App\Observers\RecommendationRelationObserver::class,
        \App\Models\JobSkill::class               => \App\Observers\JobSkillObserver::class,
    ] : [];

    foreach ($observerRegistry as $model => $observer) {
        $model::observe($observer);
    }

    // Cache-invalidation observers (always on — cheap and safe)
    \App\Models\NoticePeriod::observe(\App\Observers\NoticePeriodCacheInvalidateObserver::class);
    \App\Models\LanguageSetting::observe(\App\Observers\LanguageSettingCacheInvalidateObserver::class);
    \App\Models\GlobalSetting::observe(\App\Observers\GlobalSettingCacheInvalidateObserver::class);
}
```

## 6h. ✏️ Replace `ServiceCompanyJobsController::dataCnt()`

**File:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:1314-1697`

Replace the heavy Job::count() blocks with summary-table lookups (gated by `use_summary_table_job_counters`):

```php
if (config('feature.use_summary_table_job_counters')) {
    return Cache::remember("dataCnt:{$companyId}:" . auth()->id(), 60, function () use ($companyId) {
        $row = DB::table('job_counters')->where('company_id', $companyId)->first();
        return [
            'allRequisition'  => $row ? (int) $row->total_jobs   : 0,
            'hiringCount'     => $row ? (int) $row->hiring_jobs  : 0,
            'holdCount'       => $row ? (int) $row->on_hold_jobs : 0,
            'closedCount'     => $row ? (int) $row->closed_jobs  : 0,
            'allTabCount'     => $row ? (int) $row->total_jobs   : 0,
        ];
    });
}
// else: legacy path
```

## 6i. ✏️ Replace `ProductCompanyAppTrackerController::loadcounter:98-180`

**File:** `app/Http/Controllers/ProductCompany/ProductCompanyAppTrackerController.php:98-180`

```php
public function loadcounter(int $jobId): array
{
    if (config('feature.use_summary_table_pipeline_counters')) {
        return Cache::remember("pipeline:{$jobId}", 300, function () use ($jobId) {
            $row = DB::table('pipeline_counters')->where('job_id', $jobId)->first();
            return $row ? (array) $row : [];
        });
    }

    // Legacy combined query (was 2 queries → 1)
    $row = DB::table('job_applications as ja')
        ->join('add_job_wise_condidates as ajwc', 'ajwc.job_application_id', '=', 'ja.id')
        ->where('ja.job_id', $jobId)
        ->whereNull('ja.deleted_at')
        ->selectRaw('
            SUM(ja.master_status_id = 1) AS sourcing,
            SUM(ja.master_status_id = 2) AS screening,
            SUM(ja.master_status_id = 3) AS evaluation,
            SUM(ja.master_status_id = 4) AS negotiation,
            SUM(ja.master_status_id = 5) AS offered,
            SUM(ja.master_status_id = 6) AS joined,
            SUM(ja.master_status_id IN (7,9)) AS rejected,
            COUNT(*) AS allCandidate
        ')
        ->first();

    return $row ? (array) $row : [];
}
```

## 6j. ⚠️🟠 BUG-18 fix — `whereNotIn(pluck)` → `whereExists`

**File:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:1613-1629`

**Before:**
```php
$existingJobQuery = DB::table('recommendation_relation')
    ->where('to_company_id', $company_id)
    ->pluck('job_id')->toArray();

$findJobs = DB::table('jobs')
    ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
    ->where(function ($q) use ($existingJobQuery, $company_id) {
        $q->whereNotIn('jobs.id', $existingJobQuery)
          ->where('jobs.company_id', '!=', $company_id)
          ->where('jobs.public_market_status', '=', 2)
          ->where('jobs.status', '=', 'active');
    })
    ->groupBy('jobs.id')
    ->get();

$dataCnt['findJobs'] = count($findJobs);
```

**After (gated):**
```php
if (config('feature.use_rec_where_exists')) {
    $dataCnt['findJobs'] = DB::table('jobs')
        ->whereNotExists(function ($q) use ($company_id) {
            $q->select(DB::raw(1))
              ->from('recommendation_relation')
              ->whereColumn('recommendation_relation.job_id', 'jobs.id')
              ->where('recommendation_relation.to_company_id', $company_id);
        })
        ->where('jobs.company_id', '!=', $company_id)
        ->where('jobs.public_market_status', '=', 2)
        ->where('jobs.status', '=', 'active')
        ->distinct()
        ->count('jobs.id');
} else {
    // legacy backout-safe path
}
```

## 6k. ⚠️🟠 BUG-10 fix — Wrap orWhere chains

**File:** same as above, lines `:1556-1567` and `:1587-1592`.

**Before:**
```php
->where(function ($q) use ($company_id, $user_id) {
    $q->where('recommendation_relation.to_company_id', $company_id)
      ->whereIn('recommendation_relation.final_status', [1, 2])
      ->orWhere(function ($q2) use ($company_id, $user_id) {
          $q2->where('jobs.company_id', $company_id)
             ->where('jobs.assign_user_id', $user_id);
      });
})
```

**After:**
```php
->where(function ($q) use ($company_id, $user_id) {
    $q->where(function ($inner) use ($company_id) {
            $inner->where('recommendation_relation.to_company_id', $company_id)
                  ->whereIn('recommendation_relation.final_status', [1, 2]);
        })
      ->orWhere(function ($inner2) use ($company_id, $user_id) {
            $inner2->where('jobs.company_id', $company_id)
                   ->where('jobs.assign_user_id', $user_id);
        });
})
```

## 6l. 🆕 Drift detection

🆕 `app/Console/Commands/VerifyCounterDrift.php`:
```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyCounterDrift extends Command {
    protected $signature = 'counters:verify-drift';
    protected $description = 'Compare summary tables vs. live COUNT(*) and report drift.';

    public function handle(): int {
        $issues = [];
        $drift = DB::select("
            SELECT jc.company_id,
                   jc.total_jobs AS summary_total,
                   live.live_total,
                   ABS(jc.total_jobs - live.live_total) AS drift
            FROM job_counters jc
            JOIN (
                SELECT company_id, COUNT(*) AS live_total
                FROM jobs WHERE deleted_at IS NULL
                GROUP BY company_id
            ) live ON live.company_id = jc.company_id
            HAVING drift > 0
            LIMIT 50
        ");
        foreach ($drift as $d) $issues[] = "job_counters cid={$d->company_id} drift={$d->drift}";

        if (empty($issues)) {
            $this->info('No counter drift detected.');
            return self::SUCCESS;
        }
        foreach ($issues as $i) $this->warn($i);
        return self::FAILURE;
    }
}
```

Schedule hourly in `app/Console/Kernel.php`:
```php
$schedule->command('counters:verify-drift')->hourly();
```

---

# STEP 7 — Migrate Search/Filter to Elasticsearch (Days 5-7, ⚠️🟠)

## 7a. ⚠️🟠 Replace MySQL search with ES

🆕 `app/Services/ElasticJobsSearchService.php`:
```php
<?php
namespace App\Services;

class ElasticJobsSearchService {
    public function __construct(protected ElasticService $es) {}

    public function search(array $must, array $filter, int $from, int $size): array {
        $params = [
            'index' => config('elasticsearch.ELASTIC_JOBS'),
            'body' => [
                'from' => max(0, $from),
                'size' => $size,
                'query' => [
                    'bool' => [
                        'must'   => $must ?: [['match_all' => (object) []]],
                        'filter' => $filter,
                    ],
                ],
                'sort' => [['created_at' => 'desc']],
            ],
        ];
        return $this->es->getClient()->search($params)->asArray();
    }
}
```

✏️ `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` — add `searchApi`:

```php
public function searchApi(Request $req)
{
    $must = [];
    if ($req->filled('q')) {
        $must[] = [
            'multi_match' => [
                'query' => $req->input('q'),
                'fields' => ['title^3', 'description', 'skills_text'],
                'fuzziness' => 'AUTO',
            ],
        ];
    }
    $filter = [
        ['term' => ['company_id' => auth()->user()->company_id]],
        ['term' => ['status' => $req->input('status', 'active')]],
    ];
    if ($req->filled('exp_min')) $filter[] = ['range' => ['max_experience' => ['gte' => (int) $req->input('exp_min')]]];
    if ($req->filled('exp_max')) $filter[] = ['range' => ['min_experience' => ['lte' => (int) $req->input('exp_max')]]];

    $from = max(0, ((int) $req->input('page', 1) - 1) * 20);

    if (!config('feature.use_es_search')) {
        return ['hits' => ['hits' => [], 'total' => ['value' => 0]]];
    }

    return app(\App\Services\ElasticJobsSearchService::class)->search($must, $filter, $from, 20);
}
```

## 7b. ⚙️ MySQL FULLTEXT fallback

🆕 `database/migrations/2026_08_05_add_jobs_fulltext_index.php`:
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void { DB::statement('ALTER TABLE jobs ADD FULLTEXT INDEX ft_jobs_title_desc (title, description)'); }
    public function down(): void { DB::statement('ALTER TABLE jobs DROP INDEX ft_jobs_title_desc'); }
};
```

**Verification:**
```sql
EXPLAIN SELECT id FROM jobs
WHERE MATCH(title, description) AGAINST('java developer' IN NATURAL LANGUAGE MODE);
-- Expected: type=fulltext, key=ft_jobs_title_desc
```

**Expected gain:** Search latency 600ms → <80ms.

---

# STEP 8 — Frontend Asset Optimization (Week 1)

## 8a. ⚙️ Self-host Poppins 1 weight

```bash
curl -L 'https://fonts.gstatic.com/s/poppins/v22/pxiEyp8kv8JHgFVrJJfecg.woff2' -o public/fonts/poppins-400.woff2
curl -L 'https://fonts.gstatic.com/s/poppins/v22/pxiIyp8kv8JHgFVrJJfrvQ.woff2' -o public/fonts/poppins-400-italic.woff2
```

CSS in the layout override:
```css
@font-face {
  font-family: 'Poppins';
  src: url('/fonts/poppins-400.woff2') format('woff2');
  font-weight: 400; font-style: normal; font-display: swap;
}
@font-face {
  font-family: 'Poppins';
  src: url('/fonts/poppins-400-italic.woff2') format('woff2');
  font-weight: 400; font-style: italic; font-display: swap;
}
body { font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
```

## 8b. 🆕 Inline Keenicons as SVG sprite

```bash
npm install -g svg-sprite-generator
svg-sprite -s -o public/icons.svg public/keenicons-svgs/*.svg
```

🆕 `resources/views/components/icon.blade.php`:
```blade
@props(['name'])
<svg class="icon icon-{{ $name }}" aria-hidden="true" focusable="false" {{ $attributes }}>
  <use xlink:href="/icons.svg#icon-{{ $name }}"></use>
</svg>
```

**Migration:** replace `<i class="ki ki-…">` with `<x-icon name="…" />`. Search the views first:
```bash
grep -rln "class=\"ki " resources/views/
```

## 8c. ⚙️ Critical CSS extraction

```bash
npm install -D critical
npx critical http://testing.jero.works/service_company/jobs \
  --base=./public \
  --css=./public/css/metronic.css \
  --extract=true \
  --target=./resources/views/partials/critical-css.blade.php
```

Edit the layout (e.g., `resources/views/layouts/metronic.blade.php`):

**Before:**
```blade
<head>
    <link rel="stylesheet" href="/css/metronic.css">
</head>
```

**After:**
```blade
<head>
    <style>{!! file_get_contents(resource_path('views/partials/critical-css.blade.php')) !!}</style>
    <link rel="preload" href="/css/metronic.css" as="style"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="/css/metronic.css"></noscript>
</head>
```

## 8d. ⚙️ Defer non-critical JS

**Before:**
```html
<script src="/js/app.js"></script>
<script src="/js/datatable.js"></script>
<script src="/js/metronic.js"></script>
```

**After:**
```html
<script src="/js/app.js" defer></script>
<script src="/js/datatable.js" defer></script>
<script src="/js/metronic.js" defer></script>
```

**Identify unused:**
```bash
grep -rl "@livewireScripts\|livewire.js" resources/views/service_company/jobs/ resources/views/service_company/apptracker/
# If found, drop from those layouts
```

## 8e. ⚙️ Brotli / gzip

**Nginx:**
```nginx
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types text/plain text/css text/xml application/javascript application/json application/xml+rss application/atom+xml image/svg+xml font/woff2;
gzip_min_length 256;

brotli on;
brotli_comp_level 6;
brotli_types text/plain text/css text/xml application/javascript application/json image/svg+xml font/woff2;
```

`nginx -s reload`

## 8f. 🧪 Lighthouse CI gate

`.github/workflows/lighthouse.yml`:
```yaml
name: Lighthouse
on: [push]
jobs:
  lighthouse:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: treosh/lighthouse-ci-action@v11
        with:
          urls: |
            https://testing.jero.works/service_company/jobs
          budgetPath: ./lighthouse-budget.json
```

`lighthouse-budget.json`:
```json
[{
  "path": "/*",
  "resourceSizes": [
    { "resourceType": "document", "budget": 100 },
    { "resourceType": "stylesheet", "budget": 100 },
    { "resourceType": "script", "budget": 200 },
    { "resourceType": "image", "budget": 50 },
    { "resourceType": "font", "budget": 100 }
  ],
  "timings": [
    { "metric": "first-contentful-paint", "budget": 800 },
    { "metric": "interactive", "budget": 1500 },
    { "metric": "total-blocking-time", "budget": 200 }
  ]
}]
```

---

# STEP 9 — Cursor Pagination + Edge Cache + Throttle (Week 2, 🟡)

## 9a. ✏️ Cursor pagination in DataTables

The controller returns `next_cursor: <base64>` from `allDataAjax`. (See audit §6 / §24 for full code.)

## 9b. ⚠️🟠 ETag support

**File:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php`

```php
public function index(Request $request)
{
    if (config('feature.use_etag')) {
        $userHash = hash('sha256', json_encode([
            $this->user->id,
            $this->user->company_id,
            $request->headers->get('Cookie'),
        ]));
        $etag = '"' . $userHash . '"';

        if ($request->header('If-None-Match') === $etag) {
            return response()->noContent(304);
        }

        $response = response()->view('service_company.jobs.index', $this->data);
        $response->setEtag($etag);
        return $response;
    }
    // legacy path
    return view('service_company.jobs.index', $this->data);
}
```

## 9c. 🆕 Throttle middleware

🆕 `app/Http/Middleware/RecruiterThrottle.php`:
```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RecruiterThrottle {
    public function handle(Request $request, Closure $next, $maxAttempts = 30, $windowMinutes = 1) {
        if (!auth()->check()) return $next($request);

        $key = "throttle:jobs:{$request->user()->id}";
        $allowed = Redis::eval(
            "local k=KEYS[1] local n=tonumber(ARGV[1]) local w=tonumber(ARGV[2])*1000 local m=tonumber(ARGV[3]) "
          . "redis.call('ZREMRANGEBYSCORE', k, '-inf', n-w) "
          . "local c=redis.call('ZCARD', k) "
          . "if c>=m then return 0 end "
          . "redis.call('ZADD', k, n, n) "
          . "redis.call('EXPIRE', k, ARGV[2]) "
          . "return 1",
            1,
            $key,
            (int) (microtime(true) * 1000),
            $windowMinutes * 60,
            $maxAttempts
        );

        if (!$allowed) return response()->json(['error' => 'Too many requests'], 429);
        return $next($request);
    }
}
```

Register (Laravel 11: `bootstrap/app.php`):
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'recruiter.throttle' => \App\Http\Middleware\RecruiterThrottle::class,
    ]);
})
```

Apply to the resource route in `routes/service.php:154`:
```php
Route::middleware('recruiter.throttle:60,1')->group(function () {
    Route::resource('jobs', ServiceCompanyJobsController::class);
});
```

## 9d. ⚙️ Cloudflare edge cache

Page Rules for `/service_company/jobs*`:
- Browser cache TTL: 60 s
- Edge cache TTL: 600 s
- Cache level: standard

For static subresources:
- Browser cache TTL: 31536000 s, immutable
- Edge cache TTL: 31536000 s, immutable

---

# STEP 10 — Enterprise Hardening (Weeks 3-4, 🟢)

## 10a. ⚙️ Redis Cluster

`config/database.php` Redis section, lines `:144-171`:

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix'  => env('REDIS_PREFIX', 'jero_'),
        'serializer' => 'php',
        'persistent' => true,
    ],
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', 0),
        'password' => env('REDIS_PASSWORD'),
    ],
    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', 1),
    ],
    'queue' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_QUEUE_DB', 2),
    ],
],
```

For Sentinel, switch `'cluster'` to `'redis'` and add `'sentinel' => ['master' => 'jero-master', 'nodes' => ['sentinel-1:26379','sentinel-2:26379','sentinel-3:26379']]`.

## 10b. ⚙️ Connection pool via ProxySQL

`/etc/proxysql.cnf`:
```ini
mysql_servers =
{ address="db.jero.works", port=3306, hostgroup=0, max_connections=200 }

mysql_users =
{ username="proxysql_user", password="***", default_hostgroup=0, max_connections=200 }
```

`.env`:
```env
DB_HOST=proxysql.jero.works
DB_PORT=6033
```

For AWS RDS, use RDS Proxy instead.

## 10c. ⚙️ Read-replica routing

`config/database.php`:
```php
'mysql' => [
    'read' => [
        'host' => [env('DB_HOST_READ', 'proxysql-ro.jero.works')],
        'port' => env('DB_PORT', 6033),
        'database'  => env('DB_DATABASE', 'jero'),
        'username'  => env('DB_USERNAME', 'proxysql_user'),
        'password'  => env('DB_PASSWORD'),
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'write' => [
        'host' => env('DB_HOST_WRITE', env('DB_HOST', 'proxysql.jero.works')),
        // …
    ],
],
```

## 10d. ⚠️ Bump Horizon processes

`config/horizon.php` (lines 199-228). Change `maxProcesses` per audit §22.

## 10e. ⚙️ Observability

Laravel Telescope (dev only):
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Datadog APM tracing:
```bash
composer require datadog/dd-trace
```

MySQL slow query log:
```ini
[mysqld]
slow_query_log = 1
long_query_time = 0.5
log_output = TABLE
```

## 10f. ⚙️ Load test (k6)

```bash
brew install k6
k6 run --vus 1000 --duration 300s - <<'EOF'
import http from 'k6/http';
export const options = { stages: [
  { duration: '60s', target: 5000 },
  { duration: '60s', target: 10000 },
  { duration: '120s', target: 10000 },
  { duration: '60s', target: 0 },
] };
export default function () { http.get('https://testing.jero.works/service_company/jobs'); }
EOF
```

**Pass criteria:**
- P99 response time < 500ms
- Error rate < 0.1%
- No `Too many connections` errors

---

# Final Acceptance Gate

Save as `jobs-v2-acceptance.sh`:

```bash
#!/usr/bin/env bash
set -e
echo "=== Jobs V2 Acceptance Gate ==="

echo "[1/5] Verifying config & route cache..."
[ -f bootstrap/cache/config.php ]   && echo "  config.php ✓"   || (echo "  config.php missing ✗"; exit 1)
[ -f bootstrap/cache/routes-v7.php ] && echo "  routes-v7.php ✓" || (echo "  routes-v7.php missing ✗"; exit 1)

echo "[2/5] Verifying composite indexes..."
for tbl_col in \
  "jobs idx_jobs_company_status_created" \
  "job_applications idx_job_app_count" \
  "job_application_skills idx_jas_app" \
  "candidate_job_skill_exp idx_cjse_candidate_job"
do
  read -r t c <<<"$tbl_col"
  has=$(mysql -BNe "SHOW INDEX FROM $t WHERE Key_name='$c'")
  [ -n "$has" ] && echo "  $t.$c ✓" || (echo "  $t.$c missing ✗"; exit 1)
done

echo "[3/5] Verifying counter tables seeded..."
for t in job_counters pipeline_counters application_counters job_agency_counter; do
  cnt=$(mysql -BNe "SELECT COUNT(*) FROM $t")
  echo "  $t = $cnt rows ✓"
done

echo "[4/5] Verifying counter drift..."
php artisan counters:verify-drift

echo "[5/5] ETag 304 round-trip..."
curl -sI https://testing.jero.works/api/service_company/jobs | head -1 | grep -q "200" \
  && echo "  first call: 200 ✓" || exit 1
etag=$(curl -sI https://testing.jero.works/api/service_company/jobs | grep -i ^etag | head -1 | awk '{print $2}' | tr -d '\r')
curl -sI -H "If-None-Match: $etag" https://testing.jero.works/api/service_company/jobs | head -1 | grep -q "304" \
  && echo "  repeat call: 304 Not Modified ✓" || exit 1

echo "=== ALL CHECKS PASSED — V2 READY FOR PRODUCTION ==="
```

---

# Rollout Safety

| Step | Risk | Mitigation |
|---|---|---|
| 1. config/route cache | Low | Clear with `php artisan optimize:clear` |
| 1d. EnsureOneDeviceLogin throttle | Low | `FEATURE_ENSURE_ONE_DEVICE_THROTTLE=false` by default |
| 2. Index DDL | Medium | Use `gh-ost` or `pt-online-schema-change` |
| 3. N+1 refactor | Medium | Default `FEATURE_PREFETCHED_ROW_MAPS=false` |
| 4. Company::all replacement | Low | Verify view usage |
| 5. ES count refactor | Medium | Default OFF; smoke test |
| 6. Counter tables | Medium | Always `counters:seed-from-live` first; verify zero drift before flipping flag |
| 7. Search migration | Medium | Default OFF; smoke test |
| 8. Frontend assets | Low | Lighthouse CI budget |
| 9. Throttle middleware | Low | Tunable per-route |
| 10. Redis Cluster | High | Maintenance window; backout by reverting `database.php` |

### Rollout sequence

1. **Day 0** — Deploy step 1 + 2 + `FEATURE_*` all false; monitor.
2. **Day 1** — Deploy step 3 (`FEATURE_PREFETCHED_ROW_MAPS=false`); smoke.
3. **Day 2** — Flip `FEATURE_PREFETCHED_ROW_MAPS=true`; run drift verifier.
4. **Day 3** — Deploy step 5a (`use_summary_table_job_counters=false`); monitor.
5. **Day 4** — Deploy step 6 (counters, `use_event_driven_counters=false`); seed; verify drift.
6. **Day 5** — Flip `use_event_driven_counters=true`; smoke.
7. **Day 6** — Deploy steps 6j/6k (BUG-18/10); monitor.
8. **Day 7** — Deploy steps 7-9 (search, ETag, throttle).
9. **Day 8-14** — Step 8 (asset audit).
10. **Day 14+** — Step 10 (Redis Cluster / ProxySQL / read-replicas).

---

# Verification Summary

| Step | Green light |
|---|---|
| 1a | `bootstrap/cache/config.php` exists |
| 1b | `config('session.driver') === 'redis'` |
| 1c | `redis-cli -n 1 KEYS 'jero_test_key'` returns a value |
| 1d | MySQL UPDATEs on `users.session_id` ≤ 2 per minute per user |
| 2a | `EXPLAIN` shows `idx_jobs_company_status_created` in use |
| 2b | `max_connections >= 2048` |
| 3 | `php artisan test tests/Feature/NoNPlusOneInJobsAjaxTest.php` passes |
| 4a | `Company::all()` no longer in `index()` |
| 4b | General log ≤ 1 SELECT on `notice_period` per page |
| 5a | `custome_count_all:jobId:hash` present in Redis after first load |
| 5b | ES query log shows 6 `_search` calls (was 9) |
| 5c | ES query log shows 1 `_count` (was 1 `_search size:10`) |
| 6 | `counters:verify-drift` reports zero drift |
| 7 | Search latency < 80 ms |
| 8 | Lighthouse FCP < 1 s; bundle gzip ≤ 300 KB |
| 9a | DataTables pagination works on cursor |
| 9b | `If-None-Match` returns `304` |
| 9c | 100 rapid reqs → 429 after 30 |
| 9d | Cloudflare cache hit > 50% after warmup |
| 10 | k6 10k VUs P99 ≤ 500 ms |

---

# Final Capabilities After V2

| Metric | **Today** | **After V2** |
|---|---|---|
| Cold page-load (avg) | ~1,200 ms | **~80 ms** (15× faster) |
| SQL queries per page | ~30-35 | **~1** |
| ES round-trips / page | 4-7 | **0-1** (warm cache) |
| MySQL CPU @ 10k recruiters | Saturating | **~5%** |
| RAM per request | ~35 MB | **~15 MB** |
| Initial payload (gzip) | ~750 KB | **~280 KB** |
| TTFB | ~800 ms | **~30 ms** |
| Concurrent recruiters | ~1.5k | **30k+** |
| Max jobs | ~50k | **200k+/shard** |
| Max applications | ~1M | **1B+/shard** |

### Bugs fixed by V2 (23 total)

1. **BUG-01** `EnsureOneDeviceLogin` writes `users.session_id` on every request → throttled to once/min.
2. **BUG-02** `config:cache` and `route:cache` never run → enabled.
3. **BUG-03** `Company::all()` returns full table on every load → `find($companyId)`.
4. **BUG-04** 4 N+1 queries in `all_apptracker.blade.php:28, 76-108, 156, 181` → refactored to maps.
5. **BUG-05** `custome_count_all` fires 6 ES `_search` per page load, no cache → wrapped in `Cache::flexible`.
6. **BUG-06** 3 redundant 2nd `_search` for cardinality → merged into first.
7. **BUG-07** Career counter uses `_search size:10` for count → uses `_count` API.
8. **BUG-08** `dataCnt():1331-1535` 2 wasted SQL counts → deleted.
9. **BUG-09** `dataCnt():1619-1629` materializes million rows for PHP `count()` → SQL `COUNT(DISTINCT)`.
10. **BUG-10** Mixed `where`/`orWhere` causes table scan → explicit closure.
11. **BUG-11** `JobApplicationObserver` not registered → wired in `AppServiceProvider::boot()`.
12. **BUG-12** `JobApplication.$appends` fires nested doc query per hydration → `$appends` removed; bulk-lookup approach.
13. **BUG-13** `JobApplication` global scope commented out → re-enabled.
14. **BUG-14** No FULLTEXT on jobs description → added `ft_jobs_title_desc`.
15. **BUG-15** `job_application_skills` 470k-row table, no FK index → added `idx_jas_app`.
16. **BUG-16** `candidate_job_skill_exp` 678k-row table, PK only → added `idx_cjse_candidate_job`.
17. **BUG-17** No throttle on Jobs page → Redis sliding-window middleware.
18. **BUG-18** `whereNotIn(pluck)` materializes 100k+ IDs → `whereExists` subquery.
19. **BUG-19** `bootstrap/cache/config.php` and `routes-v7.php` not generated → enabled.
20. **BUG-20** `CACHE_PREFIX=` empty → `CACHE_PREFIX=jero_`.
21. **BUG-21** `NoticePeriod::all()` fetched every page load → cached 24h.
22. **BUG-22** `LanguageSetting::all()` fetched every page load → cached 24h.
23. **BUG-23** `GlobalSetting::with('currency')->first()` fetched every page load → cached 24h.

### Zero/min MySQL load achieved through

1. Counter summary tables — updated on observer events instead of COUNT/GROUP BY per request.
2. Composite indexes — most queries become index-only scans.
3. Pipeline counters pre-aggregated — no JOIN+GROUP BY per row on dashboard.
4. ES for search/filter/aggs — most search traffic bypasses MySQL.
5. Cursor pagination — O(log n) regardless of page depth.
6. Redis cache layer — most dashboard reads are O(1) hash lookups.
7. Write-skew protection — observers run on save, not on read.
8. `whereExists` replaces `whereNotIn(pluck)` — no IN-list evaluation in MySQL.
9. `Cache::flexible` for `custome_count_all` — 9 ES round-trips per page → 0.
10. `FEATURE_PREFETCHED_ROW_MAPS` — 4 N+1s eliminated from Blade.

### Zero/min server load achieved through

1. `config:cache` + `route:cache` — free 30-50ms CPU per request.
2. `defer`/`async` on all JS — drops FCP/TTI.
3. Cloudflare edge cache — static assets served from edge, never reach app server.
4. SVG sprite icons — eliminates an entire font request.
5. Poppins 1 weight — 8 fewer font fetches.
6. Connection pooling via ProxySQL — fewer processes waiting on MySQL.
7. `Octane` + `FrankenPHP` persistent connections — zero connection setup cost per request.
8. Event-driven counters — no per-request aggregation cost.
9. ETag + 304 — returning visitor requests skip the controller entirely.
10. Throttle middleware — bounds per-recruiter concurrency, defends against bursts.

---

# Implementation Schedule

| Day | Steps delivered |
|---|---|
| Day 1 | Step 1 + Step 2 |
| Day 2 | Step 3 (N+1) + Step 4 partial |
| Day 3 | Step 4 rest + Step 5 |
| Day 4-7 | Step 6 (counters + observers, gated) |
| Day 5-7 | Step 7 (search migration) |
| Week 1 | Step 8 (asset audit) |
| Week 2 | Step 9 (pagination + ETag + throttle) |
| Week 3 | Step 10 (read-replicas + connection pool + Horizon bump) |
| Week 4 | Step 10 rest (Redis Cluster + observability) |

---

*Plan end. Companion audit: `JOBS_LISTING_AUDIT.md`.*
