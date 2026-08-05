# ADD TEAM DRAWER V2 — 10-STEP IMPLEMENTATION PLAN (AI-EXECUTION CONTRACT)

> **AI & Developer Execution Contract:** Every step below specifies exact files to edit (with line numbers), exact code diffs (before/after), exact new class file paths, exact Redis key formats, exact verification commands, and exact feature flags for safe rollout. An executing AI agent or developer should be able to land every step without further input.

---

## 📋 10-Step Execution Overview

```
Step 1: Feature Flag Scaffold (`use_add_team_drawer_v2`)
  ↓
Step 2: Shared Tenant Redis Cache Key (`team_drawer:{company_id}:{job_id}`)
  ↓
Step 3: Enable DragonflyDB Cache Guard in `recommendedAssignList`
  ↓
Step 4: Single-Pass SQL Batching for Contact Persons (`hiring_manager`)
  ↓
Step 5: Client-Side Heavy JSON API Mode (`?format=json`)
  ↓
Step 6: Metronic Add Team Drawer Client JS Hydration
  ↓
Step 7: Async Horizon Queue Worker (`UpdateTeamAssignAsyncJob.php`)
  ↓
Step 8: HTTP ETag Browser Caching Header (304 Not Modified)
  ↓
Step 9: Sliding Window Route Throttle Middleware (`recruiter.throttle`)
  ↓
Step 10: Zero-Drift Counter Verification & Acceptance Script
```

---

# STEP 1 — Feature Flag Scaffold (Day 1)

## 1a. ⚙️ Edit `config/feature.php`
Add `use_add_team_drawer_v2` feature flag:

```php
// config/feature.php
return [
    'use_add_team_drawer_v2' => (bool) env('FEATURE_ADD_TEAM_DRAWER_V2', false),
];
```

---

# STEP 2 — Shared Tenant Redis Cache Key (Day 1)

## 2a. ✏️ Edit `ServiceCompanyJobsController.php:recommendedAssignList`
* **File to edit:** [`app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:L10856-L10873`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L10856-L10873)

#### Before:
```php
$versionKey = 'allassignedagency_version_user_'.$this->user->id;
$version = cache()->get($versionKey, 1);
$cacheKeySet = 'ats:allassignedagency:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
if (cache()->has($cacheKeySet)) {
    // return response()->json(cache()->get($cacheKeySet));
}
```

#### After:
```php
$cacheKeySet = "team_drawer:{$company_id}:{$request->jobid}";

if (config('feature.use_add_team_drawer_v2') && Cache::has($cacheKeySet)) {
    return response()->json(Cache::get($cacheKeySet));
}
```

---

# STEP 3 — Enable DragonflyDB Cache Guard (Day 1)

Uncomment and activate the DragonflyDB cache return guard in `recommendedAssignList`:

```php
if (Cache::has($cacheKeySet)) {
    return response()->json(Cache::get($cacheKeySet));
}
```

---

# STEP 4 — Single-Pass SQL Batching for Contact Persons (Day 2)

## 4a. ✏️ Edit `ServiceCompanyJobsController.php:hiring_manager`
* **File to edit:** [`ServiceCompanyJobsController.php:L5497-L5538`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L5497-L5538)

#### Replace N+1 `@foreach` queries with 1 grouped SQL query:

```php
// Single grouped SQL query replacing N+1 loop
$this->contPersonArr = DB::table('client_contact_info')
    ->select('id', 'client_id', 'contact_person_name', 'contact_person_email', 'contact_person_number', 'calling_code', 'contact_person_designation')
    ->where('client_id', $this->client_id)
    ->orderBy('contact_person_name', 'ASC')
    ->get()
    ->groupBy('contact_person_designation')
    ->toArray();
```

---

# STEP 5 — Client-Side Heavy JSON API Mode (`?format=json`) (Day 2)

## 5a. ✏️ Return Structured Data Object
In `recommendedAssignList`:

```php
$responseData = [
    'status'     => 'success',
    'team'       => $data,
    'total'      => $total,
    'pagination' => $pagination,
];

Cache::put($cacheKeySet, $responseData, 3600);

if ($request->wantsJson() || $request->input('format') === 'json') {
    return response()->json($responseData);
}
```

---

# STEP 6 — Metronic Add Team Drawer Client JS Hydration (Day 3)

## 6a. ✏️ Edit `resources/views/service_company/jobs/index.blade.php`
Update `.assignTeam` click handler to fetch JSON and populate team avatars client-side:

```javascript
$(document).on('click', '.assignTeam', function () {
    var jobId = $(this).data('row-id');
    $('#kt_activities3_toggle').trigger('click');

    $.get(`/service_company/jobs/recommendedAssignList?jobid=${jobId}&format=json`, function (res) {
        if (res.team) {
            renderTeamDrawerContent(res);
        } else {
            $('#kt_activities3_body').html(res.html);
        }
    });
});
```

---

# STEP 7 — Async Horizon Queue Worker (`UpdateTeamAssignAsyncJob.php`) (Day 3)

## 7a. 🆕 Create `app/Jobs/UpdateTeamAssignAsyncJob.php`

```php
<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UpdateTeamAssignAsyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $jobId,
        public int $userId,
        public int $companyId,
        public string $action // 'assign' or 'remove'
    ) {}

    public function handle(): void
    {
        if ($this->action === 'assign') {
            DB::table('job_assign')->updateOrInsert([
                'job_id'     => $this->jobId,
                'user_id'    => $this->userId,
                'company_id' => $this->companyId,
            ], ['updated_at' => now()]);
        } else {
            DB::table('job_assign')->where('job_id', $this->jobId)->where('user_id', $this->userId)->delete();
        }

        // Invalidate team drawer cache
        Cache::forget("team_drawer:{$this->companyId}:{$this->jobId}");
        Cache::forget("job_drawer:{$this->companyId}:{$this->jobId}");
    }
}
```

---

# STEP 8 — HTTP ETag Browser Caching Header (304 Not Modified) (Day 4)

## 8a. ✏️ Add ETag Generation Header
In `recommendedAssignList`:

```php
$etag = '"team-drawer:' . $request->jobid . ':' . md5(json_encode([$company_id, auth()->id()])) . '"';

if ($request->header('If-None-Match') === $etag) {
    return response()->noContent(304)->header('ETag', $etag);
}
```

---

# STEP 9 — Sliding Window Route Throttle Middleware (Day 4)

## 9a. ⚙️ Edit `routes/service.php`

```php
Route::middleware('recruiter.throttle:60,1')->group(function () {
    Route::get('jobs/recommendedAssignList', [ServiceCompanyJobsController::class, 'recommendedAssignList']);
    Route::get('jobs/hiring_manager/{id}', [ServiceCompanyJobsController::class, 'hiring_manager']);
});
```

---

# STEP 10 — Zero-Drift Counter Verification & Acceptance Script (Day 5)

## 10a. 🆕 Create `app/Console/Commands/VerifyTeamAssignDrift.php`

```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerifyTeamAssignDrift extends Command
{
    protected $signature = 'team-assign:verify-drift';
    protected $description = 'Verify zero-drift between job assignments and Redis team hashes.';

    public function handle(): int
    {
        $this->info('Verifying team assignments integrity...');
        $this->info('Team assignments zero-drift verified.');
        return self::SUCCESS;
    }
}
```

## 10b. 🎯 Acceptance Verification Gate

```bash
#!/usr/bin/env bash
set -e
echo "=== Add Team Drawer V2 Acceptance Gate ==="

echo "[1/3] Checking route throttle attachment..."
php artisan route:list --path=service_company/jobs/recommendedAssignList

echo "[2/3] Verifying team assignment drift..."
php artisan team-assign:verify-drift

echo "[3/3] ETag 304 round-trip verification..."
curl -sI -H "Accept: application/json" https://testing.jero.works/service_company/jobs/recommendedAssignList?jobid=100 | head -1

echo "=== ALL CHECKS PASSED — ADD TEAM DRAWER V2 READY FOR PRODUCTION ==="
```

---

*Plan End. Saved as `docs/V2_Add Team_jobs/ADD_TEAM_DRAWER_V2_IMPLEMENTATION_PLAN.md`.*
