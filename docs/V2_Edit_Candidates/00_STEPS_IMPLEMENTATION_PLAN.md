# Candidate Edit V2 — Multi-Step Implementation Plan (Updated)

> **Goal:** Fix both stages of the candidate edit workflow: (1) Opening the Edit Form without 404/redirects, and (2) Submitting the Edit Form (`POST /job-applications/update/{id}`) without 404 or authorization failures.

---

## Executive Summary & Bug Tracing

### Issue 1: Opening the Edit Page (Stage 1)
- **Root Cause**: `value_decrypt($jobId)` throws `DecryptException` when given unencrypted IDs, and zero-coin check redirects recruiters out of coins away from editing existing candidate data.
- **Fix**: Safe `value_decrypt()` helper and bypass coin check when `$appId` is present.

### Issue 2: Submitting the Edit Form (Stage 2)
- **Root Cause**: In `ServiceCompanyJobApplicationController::create()`, `$this->appId` was set but `$this->application` was never queried from MySQL (`JobApplication::find($appId)`).
- As a result, `create.blade.php` rendered the form action URL as `route('service_company.job-applications.update', @$application->id)`, which resolved to `/service_company/job-applications/update/` (missing the `{id}` route parameter).
- When the recruiter clicked **Submit**, the browser posted to `/update/`, which failed Laravel route matching and returned **HTTP 404 Not Found**.

---

## Phase 1: Fix `create()` & `clientcreate()` Candidate Loading (Stage 1 & 2)

### Step 1: Load `$this->application` in `create()` & `clientcreate()`
- **Location**: `ServiceCompanyJobApplicationController.php` (Lines 1561 & 9920)
- **Action**: When `$appId` is present, load the candidate record so `$this->application` is available in Blade templates:

```php
$this->jobId = $jobId;
$this->appId = $appId;

if (!empty($appId) && $appId !== '0') {
    $this->application = JobApplication::find($appId);
}
```

### Step 2: Update `create.blade.php` & `clientcreate.blade.php` Form Submit URL
- **Location**: `resources/views/service_company/job-applications/create.blade.php` (Line 2593)
- **Action**: Fallback to `appId` if `$application->id` is not set:

```javascript
var candidateId = '{{ @$application->id ?: @$appId }}';
var updateUrl = '{{ url("service_company/job-applications/update") }}/' + candidateId;
```

---

## Phase 2: Fix `update()` Method in `ServiceCompanyJobApplicationController.php`

### Step 3: Decrypt `$id` in `update()`
- **Location**: `ServiceCompanyJobApplicationController.php` (Line 5240)
- **Action**: Apply `GeneralMethod::value_decrypt($id)` at the start of `update()`:

```php
public function update(Request $request, $id)
{
    $id = GeneralMethod::value_decrypt($id);
    if (!$id) {
        return Reply::dataOnly(['status' => 'error', 'message' => 'Invalid candidate ID']);
    }
    // ...
}
```

---

## Phase 3: Helper & Security Layer Updates

### Step 4: Safe Decryption Helper in `GeneralMethod.php`
- **Location**: `app/Helper/GeneralMethod.php` (Line 6182)
- **Action**: Add `try-catch` wrapper with numeric passthrough:

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

## Phase 4: Verification & Automated Tests

### Step 5: Integration Tests for Edit & Submit
- **File**: `tests/Feature/CandidateEditSubmitTest.php`
- **Tests**:
  - Test GET `/service_company/job-applications/create/{jobId}/{appId}` loads form with `$application->id` populated.
  - Test POST `/service_company/job-applications/update/{appId}` updates candidate and returns HTTP 200 success.
  - Test POST with encrypted `$appId` parameter decrypts correctly.
