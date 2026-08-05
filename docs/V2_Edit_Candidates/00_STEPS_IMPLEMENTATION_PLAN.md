# Candidate Edit V2 — Multi-Step Implementation Plan

> **Goal:** Eliminate candidate edit 404 errors and unwanted redirects to the dashboard by fixing decryption handling, skipping coin checks during edit operations, supporting closed/paused job edits, and normalizing `$appId` parameters.

---

## Overview & Executive Summary

When a recruiter attempts to edit an existing candidate or job application (`/service_company/job-applications/create/{jobId}/{appId}` or `/service_company/job-applications/clientcreate/{jobId}/{appId}`), the request currently fails with a **404 Not Found** or **redirects back to AppTracker / Dashboard**.

### Root Cause Summary
1. **Decryption Crash (`DecryptException`)**: Unhandled `decrypt($jobId)` in `GeneralMethod::value_decrypt()` throws when given unencrypted IDs or URL-mutated string parameters (`+` converted to spaces).
2. **Coin Validation Guard on Edit**: Coin check runs before checking if `$appId` is present, redirecting recruiters out of coins away from editing existing candidate data.
3. **Closed/Inactive Job Hard Constraint**: `clientcreate()` requires `jobs.status = 'active'`, returning a hard `abort(404)` if the underlying job is closed or paused.
4. **`$appId` Parameter Encryption Mismatch**: `$jobId` is decrypted while `$appId` is passed directly without safe decryption/numeric handling.

---

## Phase 1: Core Helper Fix — Safe Decryption

### Step 1: Update `GeneralMethod::value_decrypt()` in `app/Helper/GeneralMethod.php`
- **Intent**: Prevent unhandled `DecryptException` crashes across the entire platform.
- **Action**: Wrap `decrypt()` in a `try-catch` block with fallback for numeric values and URL-decoded space-to-plus conversion.

```php
public static function value_decrypt($data)
{
    if (empty($data)) {
        return null;
    }
    if (is_numeric($data)) {
        return (int) $data;
    }
    try {
        return decrypt($data);
    } catch (\Throwable $e) {
        try {
            return decrypt(str_replace(' ', '+', $data));
        } catch (\Throwable) {
            return null;
        }
    }
}
```

---

## Phase 2: Controller Fixes — `ServiceCompanyJobApplicationController.php`

### Step 2: Skip Coin Check for Edit Requests in `create()` and `clientcreate()`
- **Intent**: Allow recruiters to edit existing candidate records even when their operational coin balance is zero.
- **Action**: Wrap the coin validation call with `if (empty($appId) || $appId === '0')`.

```php
$isEdit = !empty($appId) && $appId !== '0';

if (!$isEdit) {
    $packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
    if ($packageStatus->status) {
        session()->flash('packageStatus', $packageStatus->message);
        $url = url('service_company/apptracker/' . @$jobId . '/all');
        return redirect($url);
    }
}
```

### Step 3: Relax Inactive Job Constraint for Edit Flow in `clientcreate()`
- **Intent**: Prevent 404 errors when editing candidates whose job status has changed to closed, draft, or paused.
- **Action**: Conditionally enforce `jobs.status = 'active'` only during new candidate creation.

```php
$query = DB::table('recommendation_relation')
    ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
    ->where('jobs.id', '=', $jobId)
    ->where('recommendation_relation.to_company_id', '=', $company_id);

if (empty($appId) || $appId === '0') {
    $query->where('jobs.status', '=', 'active')
          ->where('recommendation_relation.final_status', '=', 1);
}

$this->job = $query->count();
if ($this->job == 0) {
    return abort(404);
}
```

### Step 4: Normalize `$appId` Parameter Handling
- **Intent**: Ensure `$appId` is safely decrypted whether passed as an integer or an encrypted hash string.
- **Action**: Apply `GeneralMethod::value_decrypt($appId)` inside `create()` and `clientcreate()`.

---

## Phase 3: Automated Testing & Verification

### Step 5: Unit Test for `GeneralMethod::value_decrypt()`
- **Test File**: `tests/Unit/GeneralMethodDecryptTest.php`
- **Scenarios**:
  - Test valid encrypted payload returns original string/integer.
  - Test raw integer input returns integer directly (no crash).
  - Test invalid base64 / corrupted string returns `null` instead of throwing `DecryptException`.
  - Test URL space-mutated base64 string decrypts successfully.

### Step 6: Feature Test for Edit Candidate Flow
- **Test File**: `tests/Feature/CandidateEditWorkflowTest.php`
- **Scenarios**:
  - GET `/service_company/job-applications/create/{encryptedJobId}/{appId}` returns HTTP 200.
  - GET `/service_company/job-applications/create/{rawJobId}/{appId}` returns HTTP 200 (no 404/500).
  - Recruiter with 0 coins editing an existing candidate is NOT redirected to dashboard.
  - Editing candidate on a closed job returns HTTP 200 (no 404).

---

## Phase 4: Staging Verification & Rollout

### Step 7: Deployment & Verification Checklist
1. Run `php artisan test --filter=CandidateEdit` locally.
2. Deploy changes to `testing.jero.works`.
3. Run `php artisan config:cache` and `php artisan octane:reload`.
4. Verify clicking Edit on AppTracker and Candidate drawer opens the Edit page with HTTP 200.
