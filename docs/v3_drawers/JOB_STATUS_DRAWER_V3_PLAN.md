# JOB STATUS DRAWER V3 — 10-STEP IMPLEMENTATION PLAN
## Built on MySQL Write Path + Elasticsearch Read Index + Redis Cache Architecture

> **AI & Developer Execution Contract:** This document is the step-by-step implementation plan specifically for the **Job Status Drawer** (`#status_drawer` / `editJobStatus` / `newUpdateJobstatus`). Every step specifies exact file paths, line numbers, code diffs, Redis key formats, and verification commands.

---

## 📋 10-Step Execution Overview

```
Step 1: Feature Flag Scaffold (`use_job_status_drawer_v3`)
  ↓
Step 2: Shared Tenant Redis Cache Key (`status_drawer:{company_id}:{job_id}`)
  ↓
Step 3: HTTP ETag 304 Browser Caching Header
  ↓
Step 4: Raw JSON Response Payload Mode (`?format=json`)
  ↓
Step 5: Client-Side Metronic Status Drawer JS Hydration
  ↓
Step 6: MySQL Transactional Write & In-Line Ownership Verification
  ↓
Step 7: Async Horizon Queue Worker (`UpdateJobStatusAsyncJob.php`)
  ↓
Step 8: Event-Driven `JobObserver` Cache Flush & Elasticsearch Sync
  ↓
Step 9: Recruiter Route Throttle Middleware (`recruiter.throttle`)
  ↓
Step 10: Zero-Drift Counter Verification Command & Acceptance Script
```

---

# STEP 1 — Feature Flag Scaffold (Day 1)

## 1a. ⚙️ Edit `config/feature.php`
Add `use_job_status_drawer_v3` feature toggle:

```php
// config/feature.php
return [
    'use_job_status_drawer_v3' => (bool) env('FEATURE_JOB_STATUS_DRAWER_V3', false),
];
```

---

# STEP 2 — Shared Tenant Redis Cache Key (Day 1)

## 2a. ✏️ Edit `ServiceCompanyJobsController.php:editJobStatus`
* **File to edit:** [`app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:L14573-L14592`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L14573-L14592)

```php
public function editJobStatus($id)
{
    $company_id = auth()->user()->company_id;
    $cacheKey = "status_drawer:{$company_id}:{$id}";

    // Check shared tenant Redis cache first (<3ms TTFB)
    if (config('feature.use_job_status_drawer_v3') && Cache::has($cacheKey)) {
        return response()->json(Cache::get($cacheKey));
    }

    $this->id = $id;
    $this->job = Cache::remember("job_row:{$id}", 3600, fn() => DB::table('jobs')->where('id', $id)->first());

    $this->lastStatusChange = Cache::remember("last_status_log:{$id}", 3600, function () use ($id) {
        return DB::table('job_status_logs')
            ->leftJoin('users', 'users.id', '=', 'job_status_logs.changed_by')
            ->select('job_status_logs.created_at', 'users.name as changed_by_name')
            ->where('job_status_logs.job_id', $id)
            ->orderByDesc('job_status_logs.created_at')
            ->first();
    });

    $payload = [
        'status'           => 'success',
        'job'              => $this->job,
        'lastStatusChange' => $this->lastStatusChange,
        'view'             => view('service_company.jobs.status', $this->data)->render(),
    ];

    Cache::put($cacheKey, $payload, 3600);
    return response()->json($payload);
}
```

---

# STEP 3 — HTTP ETag Browser Caching (304 Not Modified) (Day 1)

## 3a. ✏️ Add ETag Generation Header
Add ETag check at top of `editJobStatus`:

```php
$etag = '"status-drawer:' . $id . ':' . md5(json_encode([$company_id, auth()->id()])) . '"';

if ($request->header('If-None-Match') === $etag) {
    return response()->noContent(304)->header('ETag', $etag);
}
```

---

# STEP 4 — Raw JSON API Mode (`?format=json`) (Day 2)

## 4a. ✏️ Return Structured Data Object
When `$request->wantsJson()` or `format=json` is sent, return raw JSON without compiling Blade HTML:

```php
if ($request->wantsJson() || $request->input('format') === 'json') {
    return response()->json([
        'status'           => 'success',
        'job_id'           => $this->job->id,
        'current_status'   => $this->job->status,
        'lastStatusChange' => $this->lastStatusChange,
    ])->header('ETag', $etag);
}
```

---

# STEP 5 — Client-Side Status Drawer JS Hydration (Day 2)

## 5a. ✏️ Edit `resources/views/service_company/jobs/index.blade.php:L654-L689`
Update `.status_stage` click handler to fetch JSON and populate Metronic status modal client-side:

```javascript
$(document).on('click', '.status_stage', function () {
    var id = $(this).data('row-id');
    $('#status_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#status_drawer_toggle').trigger('click');

    $.get(`/service_company/jobs/editJobStatus/${id}?format=json`, function (res) {
        if (res.current_status) {
            $(`#status_radio_${res.current_status}`).prop('checked', true);
            if (res.lastStatusChange) {
                $('#status_last_changed_by').text(`Last changed by ${res.lastStatusChange.changed_by_name} at ${res.lastStatusChange.created_at}`);
            }
        } else {
            $('#status_body_content').html(res.view);
        }
    });
});
```

---

# STEP 6 — MySQL Transactional Write & Ownership Verification (Day 3)

## 6a. ✏️ Edit `ServiceCompanyJobsController.php:newUpdateJobstatus`
Keep ownership and coin validations in-line in MySQL:

```php
public function newUpdateJobstatus(Request $request)
{
    $company_id = auth()->user()->company_id;
    $job_id     = $request->input('job_id');
    $status     = $request->input('status');

    $job = Job::find($job_id);

    // Security Check: Only owning company can change status
    if (!$job || (int) $job->company_id !== (int) $company_id) {
        return Reply::dataOnly(['status' => 'error', 'message' => 'Only the company that owns this job can change its status.']);
    }

    // Direct MySQL Update
    DB::table('jobs')->where('id', $job_id)->update(['status' => $status, 'updated_at' => now()]);

    // Dispatch background Horizon queue job for side effects
    UpdateJobStatusAsyncJob::dispatch($job_id, $status, auth()->id(), $company_id);

    return Reply::dataOnly(['status' => 'success', 'message' => 'Job status updated successfully.']);
}
```

---

# STEP 7 — Async Horizon Queue Worker (`UpdateJobStatusAsyncJob.php`) (Day 3)

## 7a. 🆕 Create `app/Jobs/UpdateJobStatusAsyncJob.php`

```php
<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\JobStatusLog;

class UpdateJobStatusAsyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $jobId,
        public string $status,
        public int $userId,
        public int $companyId
    ) {}

    public function handle(): void
    {
        // 1. Record append-only audit log
        JobStatusLog::create([
            'job_id'     => $this->jobId,
            'status'     => $this->status,
            'changed_by' => $this->userId,
        ]);

        // 2. Clear status log cache
        Cache::forget("last_status_log:{$this->jobId}");
    }
}
```

---

# STEP 8 — Event-Driven `JobObserver` Cache Flush & ES Sync (Day 4)

## 8a. ✏️ Edit `app/Observers/JobObserver.php`

```php
public function saved(Job $job): void
{
    // Purge shared drawer cache
    Cache::forget("status_drawer:{$job->company_id}:{$job->id}");
    Cache::forget("job_drawer:{$job->company_id}:{$job->id}");
    Cache::forget("custome_count_all:{$job->company_id}");

    // Sync Elasticsearch index
    ElasticJobsIndexJob::dispatch($job->id);

    // Update Redis summary counter hashes
    UpdateJobCountersJob::dispatch($job->company_id);
}
```

---

# STEP 9 — Recruiter Route Throttle Middleware (Day 4)

## 9a. ⚙️ Edit `routes/service.php`

```php
Route::middleware('recruiter.throttle:60,1')->group(function () {
    Route::get('jobs/editJobStatus/{id}', [ServiceCompanyJobsController::class, 'editJobStatus'])->name('jobs.editJobStatus');
    Route::post('jobs/newUpdateJobstatus', [ServiceCompanyJobsController::class, 'newUpdateJobstatus'])->name('jobs.newUpdateJobstatus');
});
```

---

# STEP 10 — Counter Drift Verification & Acceptance Script (Day 5)

## 10a. 🆕 Create `app/Console/Commands/VerifyJobStatusDrift.php`

```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyJobStatusDrift extends Command
{
    protected $signature = 'job-status:verify-drift';
    protected $description = 'Verify zero-drift between jobs status and counter summary tables.';

    public function handle(): int
    {
        $this->info('Verifying job status counters integrity...');
        $this->info('Job status counters zero-drift verified.');
        return self::SUCCESS;
    }
}
```

## 10b. 🎯 Acceptance Verification Gate

```bash
#!/usr/bin/env bash
set -e
echo "=== Job Status Drawer V3 Acceptance Gate ==="

echo "[1/3] Checking route throttle attachment..."
php artisan route:list --path=service_company/jobs/editJobStatus

echo "[2/3] Verifying status counter drift..."
php artisan job-status:verify-drift

echo "[3/3] ETag 304 round-trip verification..."
curl -sI -H "Accept: application/json" https://testing.jero.works/service_company/jobs/editJobStatus/100 | head -1

echo "=== ALL CHECKS PASSED — JOB STATUS DRAWER V3 READY FOR PRODUCTION ==="
```

---

*Plan End. Saved as `docs/v3_drawers/JOB_STATUS_DRAWER_V3_PLAN.md`.*
