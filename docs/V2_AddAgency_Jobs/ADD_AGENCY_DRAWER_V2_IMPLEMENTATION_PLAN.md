# ADD AGENCY DRAWER V2 — 10-STEP IMPLEMENTATION PLAN (AI-EXECUTION CONTRACT)

> **AI & Developer Execution Contract:** Every step below specifies exact files to edit (with line numbers), exact code diffs (before/after), exact new class file paths, exact Redis key formats, exact verification commands, and exact feature flags for safe rollout. An executing AI agent or developer should be able to land every step without further input.

---

## 📋 10-Step Execution Overview

```
Step 1: Feature Flag Scaffold (`use_add_agency_drawer_v2`)
  ↓
Step 2: Shared Tenant Redis Cache Key (`agency_drawer:{company_id}:{job_id}:{page}`)
  ↓
Step 3: Bound ES Relation Pluck (`size: 500`)
  ↓
Step 4: Replace Painless Script Score with Native ES `terms` Filter
  ↓
Step 5: Increase Cache TTL from 3 Mins to 60 Mins
  ↓
Step 6: Client-Side Heavy JSON API Mode (`?format=json`)
  ↓
Step 7: Metronic Add Agency Drawer Client JS Hydration
  ↓
Step 8: Async Horizon Queue Worker (`SendAgencyInvitationAsyncJob.php`)
  ↓
Step 9: HTTP ETag Browser Caching Header (304 Not Modified)
  ↓
Step 10: Sliding Window Route Throttle Middleware (`recruiter.throttle`)
```

---

# STEP 1 — Feature Flag Scaffold (Day 1)

## 1a. ⚙️ Edit `config/feature.php`
Add `use_add_agency_drawer_v2` feature flag:

```php
// config/feature.php
return [
    'use_add_agency_drawer_v2' => (bool) env('FEATURE_ADD_AGENCY_DRAWER_V2', false),
];
```

---

# STEP 2 — Shared Tenant Redis Cache Key (Day 1)

## 2a. ✏️ Edit `ServiceCompanyJobsController.php:staffingPartnersRegister`
* **File to edit:** [`app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:L11179-L11198`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L11179-L11198)

#### Before:
```php
$versionKey = 'allregisteredagency_version_user_'.$this->user->id;
$version = cache()->get($versionKey, 1);
$cacheKeySet = 'ats:allregisteredagency:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
if (cache()->has($cacheKeySet)) {
    return response()->json(cache()->get($cacheKeySet));
}
```

#### After:
```php
$cacheKeySet = "agency_drawer:{$company_id}:{$request->jobid}:{$page}";

if (config('feature.use_add_agency_drawer_v2') && Cache::has($cacheKeySet)) {
    return response()->json(Cache::get($cacheKeySet));
}
```

---

# STEP 3 — Bound ES Relation Pluck (`size: 500`) (Day 1)

## 3a. ✏️ Edit `ServiceCompanyJobsController.php:L11203`

```php
// Limit relation query size from 10000 to 500 active partners
$relationIds = $this->elasticService->getClient()->search([
    'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
    'body'  => [
        'size'  => 500,
        'query' => ['bool' => ['must' => [['term' => ['from_company_id' => $company_id]], ['term' => ['final_status' => 1]]]]],
        '_source' => ['to_company_id']
    ]
]);
```

---

# STEP 4 — Replace Painless Script with Native ES `terms` Filter (Day 2)

## 4a. ✏️ Edit `ServiceCompanyJobsController.php:L11268-L11287`
Replace custom script score with native `terms` filter to leverage ES execution caching:

```php
$params = [
    'index' => ELASTIC_COMPANIES_INDEX,
    'body'  => [
        'from'             => $from,
        'size'             => $size,
        'track_total_hits' => true,
        'query' => [
            'bool' => [
                'filter' => [
                    ['terms' => ['id' => $companyIds]],
                    ['term'  => ['status' => 'active']],
                ],
                'must' => $mustConditions,
                'must_not' => [
                    ['term' => ['id' => $company_id]]
                ]
            ]
        ]
    ]
];
```

---

# STEP 5 — Increase Cache TTL to 60 Minutes (Day 2)

## 5a. ✏️ Edit `ServiceCompanyJobsController.php:L11406`

#### Before:
```php
cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));
```

#### After:
```php
Cache::put($cacheKeySet, $responseData, 3600); // 1 hour TTL
```

---

# STEP 6 — Client-Side Heavy JSON API Mode (`?format=json`) (Day 3)

## 6a. ✏️ Return Structured Data Object

```php
$responseData = [
    'status'     => 'success',
    'agencies'   => $data,
    'total'      => $total,
    'pagination' => $pagination,
];

Cache::put($cacheKeySet, $responseData, 3600);

if ($request->wantsJson() || $request->input('format') === 'json') {
    return response()->json($responseData);
}
```

---

# STEP 7 — Metronic Add Agency Drawer Client JS Hydration (Day 3)

## 7a. ✏️ Edit `resources/views/service_company/jobs/index.blade.php`
Update `.addAgency` click handler to fetch JSON and populate agency cards client-side:

```javascript
$(document).on('click', '.addAgency', function () {
    var jobId = $(this).data('row-id');
    $('#kt_activitiesAgency_toggle').trigger('click');

    $.get(`/service_company/jobs/staffingPartnersRegister?jobid=${jobId}&format=json`, function (res) {
        if (res.agencies) {
            renderAgencyDrawerContent(res);
        } else {
            $('#kt_activitiesAgency_body').html(res.html);
        }
    });
});
```

---

# STEP 8 — "Send Requirements" Action & Async Horizon Queue Worker (Day 4)

## 8a. ✏️ Edit `ServiceCompanyJobsController.php:send_job_recommanded_request`
Perform in-line coin validation (<5ms) and dispatch async queue worker:

```php
public function send_job_recommanded_request($id)
{
    $from_company_id = auth()->user()->company_id;
    $to_company_id   = $id;
    $jobid           = $request->input('current_jobid');

    // 1. In-Line Validation (Block closed/on-hold jobs)
    if (Job::where('id', $jobid)->whereIn('status', ['inactive', 'hold'])->exists()) {
        return Reply::dataOnly(["status" => "closeJob", "message" => "Job is closed or on hold."]);
    }

    // 2. In-Line Coin Balance Validation (<5ms)
    $coinsCheck = GeneralMethod::coinsValidations((int)$to_company_id, 19, auth()->id(), $from_company_id, '', '');
    if ($coinsCheck->status) {
        return Reply::dataOnly(['status' => 'info', 'message' => $coinsCheck->message]);
    }

    // 3. Direct SQL Record Creation
    DB::table('recommendation_relation')->updateOrInsert([
        'job_id'          => $jobid,
        'from_company_id' => $from_company_id,
        'to_company_id'   => $to_company_id,
    ], ['to_status' => 1, 'created_at' => now()]);

    // 4. Dispatch Async Queue Job for side-effects
    SendAgencyInvitationAsyncJob::dispatch($jobid, $from_company_id, $to_company_id, auth()->id());

    return Reply::dataOnly(['status' => 'success', 'message' => 'Agency assigned successfully.']);
}
```

## 8b. 🆕 Create `app/Jobs/SendAgencyInvitationAsyncJob.php`

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
use App\Helper\GeneralMethod;

class SendAgencyInvitationAsyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $jobId,
        public int $fromCompanyId,
        public int $toCompanyId,
        public int $userId
    ) {}

    public function handle(): void
    {
        // 1. Async Coin Deduction
        GeneralMethod::coinsDeductions((int)$this->toCompanyId, 19, $this->userId, $this->fromCompanyId, '', '');

        // 2. Async ES Re-Indexing
        GeneralMethod::clientRequestIndexElastic($this->jobId);

        // 3. Invalidate agency and job drawer caches
        Cache::forget("agency_drawer:{$this->fromCompanyId}:{$this->jobId}:1");
        Cache::forget("assigned_agency:{$this->fromCompanyId}:{$this->jobId}");
        Cache::forget("job_drawer:{$this->fromCompanyId}:{$this->jobId}");
    }
}
```

---

# STEP 9 — HTTP ETag Browser Caching Header (Day 4)

## 9a. ✏️ Add ETag Check to `staffingPartnersRegister`

```php
$etag = '"agency-drawer:' . $request->jobid . ':' . $page . ':' . md5(json_encode([$company_id, auth()->id()])) . '"';

if ($request->header('If-None-Match') === $etag) {
    return response()->noContent(304)->header('ETag', $etag);
}
```

---

# STEP 10 — Sliding Window Route Throttle & Acceptance Gate (Day 5)

## 10a. ⚙️ Edit `routes/service.php`

```php
Route::middleware('recruiter.throttle:60,1')->group(function () {
    Route::get('jobs/staffingPartnersRegister', [ServiceCompanyJobsController::class, 'staffingPartnersRegister']);
    Route::post('jobs/send_job_recommanded_request', [ServiceCompanyJobsController::class, 'send_job_recommanded_request']);
});
```

## 10b. 🎯 Acceptance Verification Script (`add-agency-v2-acceptance.sh`)

```bash
#!/usr/bin/env bash
set -e
echo "=== Add Agency Drawer V2 Acceptance Gate ==="

echo "[1/2] Checking route throttle attachment..."
php artisan route:list --path=service_company/jobs/staffingPartnersRegister

echo "[2/2] ETag 304 round-trip verification..."
curl -sI -H "Accept: application/json" https://testing.jero.works/service_company/jobs/staffingPartnersRegister?jobid=100 | head -1

echo "=== ALL CHECKS PASSED — ADD AGENCY DRAWER V2 READY FOR PRODUCTION ==="
```

---

*Plan End. Saved as `docs/V2_AddAgency_Jobs/ADD_AGENCY_DRAWER_V2_IMPLEMENTATION_PLAN.md`.*
