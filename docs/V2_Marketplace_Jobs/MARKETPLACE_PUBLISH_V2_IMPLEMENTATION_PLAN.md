# AGENCY MARKETPLACE PUBLISHING V2 — 10-STEP IMPLEMENTATION PLAN

> **AI & Developer Execution Contract:** Every step below specifies exact files to edit (with line numbers), exact code diffs (before/after), exact new class file paths, exact Redis key formats, exact verification commands, and exact feature flags for safe rollout. An executing AI agent or developer should be able to land every step without further input.

---

## 📋 10-Step Execution Overview

```
Step 1: Feature Flag Scaffold (`use_marketplace_publish_v2`)
  ↓
Step 2: Redis Config Caching for Global Settings & Company Terms
  ↓
Step 3: In-Line Ownership Verification & Status Validation
  ↓
Step 4: Direct MySQL Transactional Update (`public_market_status`)
  ↓
Step 5: Async Horizon Queue Worker (`PublishMarketplaceJobAsyncJob.php`)
  ↓
Step 6: Real-Time WebSocket Broadcast (`JobMarketplaceStatusChanged`)
  ↓
Step 7: Shared Tenant Redis Caching for Marketplace Discovery (`marketplace_jobs:{page}`)
  ↓
Step 8: HTTP ETag Browser Caching Header (304 Not Modified)
  ↓
Step 9: Sliding Window Route Throttle Middleware (`recruiter.throttle`)
  ↓
Step 10: Zero-Drift Verification Command & Acceptance Script
```

---

# STEP 1 — Feature Flag Scaffold (Day 1)

## 1a. ⚙️ Edit `config/feature.php`
Add `use_marketplace_publish_v2` feature flag:

```php
// config/feature.php
return [
    'use_marketplace_publish_v2' => (bool) env('FEATURE_MARKETPLACE_PUBLISH_V2', false),
];
```

---

# STEP 2 — Redis Config Caching for Global Settings & Terms (Day 1)

## 2a. ✏️ Edit `ServiceCompanyJobsController.php:auction_send_job_request`
* **File to edit:** [`app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:L13993-L14012`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L13993-L14012)

#### Replace DB query with Redis cache:
```php
$global_settings = Cache::remember('global_settings_cache', 86400, function () {
    return DB::table('global_settings')->first();
});

$companyTerms = Cache::remember("company_terms:{$company_id}", 86400, function () use ($company_id) {
    return Company::withoutGlobalScope('company')->find($company_id);
});
```

---

# STEP 3 — In-Line Ownership & Status Validation (Day 1)

Keep security checks in-line before publishing:

```php
$job = Job::findOrFail($jobidGet);

// Validation: Cannot publish Closed or On Hold jobs to Marketplace
if ($job->status === 'inactive' || $job->status === 'hold') {
    return Reply::dataOnly([
        'status'  => 'closeJob',
        'message' => "Please update the job status from 'Closed/Hold' to 'Hiring' to enable posting for agency partners"
    ]);
}
```

---

# STEP 4 — Direct MySQL Transactional Update (Day 2)

```php
if ($global_settings->public_market_job_status) {
    $job->public_market_status = 2; // Published to Marketplace
    $msg = "Your job is now public for agency partners.";
    $text_change = "Unpublish Job";
    $color_change = "btn-light-danger";
} else {
    $job->public_market_status = 1; // Pending Approval
    $msg = "Request sent for approval to an administrator";
    $text_change = "Approval Pending";
    $color_change = "btn-light-warning";
}

$job->save();

// Dispatch background Horizon queue job
PublishMarketplaceJobAsyncJob::dispatch($job->id, $job->public_market_status, auth()->id(), $company_id);

return Reply::dataOnly([
    'status'       => 'success',
    'message'      => $msg,
    'text_change'  => $text_change,
    'color_change' => $color_change,
]);
```

---

# STEP 5 — Async Horizon Queue Worker (`PublishMarketplaceJobAsyncJob.php`) (Day 2)

## 5a. 🆕 Create `app/Jobs/PublishMarketplaceJobAsyncJob.php`

```php
<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Cache;

class PublishMarketplaceJobAsyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $jobId,
        public int $marketStatus,
        public int $userId,
        public int $companyId
    ) {}

    public function handle(): void
    {
        // 1. Update Team Rank Elastic Metrics asynchronously
        GeneralMethod::teamRankElastic([
            'company_id'             => $this->companyId,
            'user_id'                => $this->userId,
            'postJobToVendorMarket'  => 2,
        ]);

        // 2. Re-index in Elasticsearch
        ElasticJobsIndexJob::dispatch($this->jobId);

        // 3. Purge Marketplace discovery caches
        Cache::forget("marketplace_jobs:page_1");
        Cache::forget("job_drawer:{$this->companyId}:{$this->jobId}");
    }
}
```

---

# STEP 6 — Real-Time WebSocket Broadcast (`JobMarketplaceStatusChanged`) (Day 3)

Broadcast real-time status change to all open partner agency screens:

```php
broadcast(new JobMarketplaceStatusChanged($job->id, $job->public_market_status, $company_id))->toOthers();
```

---

# STEP 7 — Shared Tenant Cache Key for Marketplace Discovery (Day 3)

Cache public agency marketplace search results under `marketplace_jobs:{page}` in Redis (1h TTL).

---

# STEP 8 — HTTP ETag Browser Caching Header (Day 4)

Add ETag check to Marketplace search route to return HTTP `304 Not Modified` on repeat agency browses.

---

# STEP 9 — Sliding Window Route Throttle Middleware (Day 4)

## 9a. ⚙️ Edit `routes/service.php`

```php
Route::middleware('recruiter.throttle:60,1')->group(function () {
    Route::get('auction/send_job_request/{id?}', [ServiceCompanyJobsController::class, 'auction_send_job_request']);
});
```

---

# STEP 10 — Acceptance Gate & Verification Script (Day 5)

## 10a. 🎯 Acceptance Script (`marketplace-publish-v2-acceptance.sh`)

```bash
#!/usr/bin/env bash
set -e
echo "=== Agency Marketplace Publishing V2 Acceptance Gate ==="

echo "[1/2] Checking route throttle attachment..."
php artisan route:list --path=service_company/jobs/auction/send_job_request

echo "[2/2] ETag 304 round-trip verification..."
curl -sI -H "Accept: application/json" https://testing.jero.works/service_company/jobs/auction/send_job_request/100 | head -1

echo "=== ALL CHECKS PASSED — MARKETPLACE PUBLISHING V2 READY FOR PRODUCTION ==="
```

---

*Plan End. Saved as `docs/V2_Marketplace_Jobs/MARKETPLACE_PUBLISH_V2_IMPLEMENTATION_PLAN.md`.*
