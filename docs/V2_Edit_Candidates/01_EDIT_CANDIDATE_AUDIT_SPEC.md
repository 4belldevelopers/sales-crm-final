# Candidate Edit V2 — Technical Audit & Architecture Specification

> **Subsystem:** Service Company Candidate Management & ATS Application Tracking  
> **Target Routes:** `job-applications/create/{jobId}/{appId}`, `job-applications/clientcreate/{jobId}/{appId}`  
> **Source Controllers:** `ServiceCompanyJobApplicationController.php`, `GeneralMethod.php`

---

## 1. Problem Description

Recruiters reporting intermittent **404 Not Found** errors or unexpected redirects to the **Dashboard / AppTracker** when clicking the "Edit Candidate" button from candidate lists, application cards, or drawers.

---

## 2. Technical Root Cause Analysis

### Failure Mode 1: Unhandled `DecryptException`
- **File:** `app/Helper/GeneralMethod.php` (Line 6183)
- **Code:**
  ```php
  public static function value_decrypt($data) {
      return decrypt($data);
  }
  ```
- **Analysis:** `Illuminate\Support\Facades\Crypt::decrypt()` throws an unhandled `DecryptException` if `$data` is not a valid Laravel-encrypted payload. In URLs, query strings or route parameters often mutate `+` to spaces or pass raw IDs. The unhandled exception is converted by Laravel into a `404 Not Found` response.

---

### Failure Mode 2: Inactive/Closed Job Hard Abort
- **File:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (Lines 9905–9912)
- **Code:**
  ```php
  $this->job = DB::table('recommendation_relation')
      ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
      ->where('jobs.id', '=', $jobId)
      ->where('jobs.status', '=', 'active')
      ->count();

  if ($this->job == 0) {
      return abort(404);
  }
  ```
- **Analysis:** When a candidate is edited long after submission, the job status may have transitioned to `closed`, `draft`, or `paused`. Filtering strictly by `status = 'active'` causes `$this->job` count to evaluate to `0`, triggering an explicit `abort(404)`.

---

### Failure Mode 3: Misapplied Coin Guard on Edit Operations
- **File:** `ServiceCompanyJobApplicationController.php` (Lines 1532–1537 & Lines 9886–9891)
- **Code:**
  ```php
  $packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
  if ($packageStatus->status) {
      session()->flash('packageStatus', $packageStatus->message);
      $url = url('service_company/apptracker/' . @$jobId . '/all');
      return redirect($url);
  }
  ```
- **Analysis:** Coin validation is designed to prevent creation of *new* candidates when operational coins are zero. However, `create()` and `clientcreate()` handle both Creation (`$appId = 0`) and Editing (`$appId > 0`). Because coin validation executes before checking `$appId`, recruiters attempting to edit existing candidates are redirected out of the form to the AppTracker.

---

### Failure Mode 4: Asymmetric `$appId` Encryption
- **File:** `ServiceCompanyJobApplicationController.php` (Line 1561 & Line 9920)
- **Code:**
  ```php
  $this->jobId = $jobId; // Decrypted at line 1525
  $this->appId = $appId; // NOT decrypted
  ```
- **Analysis:** If frontend templates encrypt `$appId` in Edit links, SQL queries matching raw `$appId` fail silently.

---

## 3. Architecture & Code Changes

### A. `app/Helper/GeneralMethod.php`

```php
/**
 * Decrypt a string safely without throwing unhandled DecryptException.
 * Supports plain numeric IDs, base64 URL space mutations, and encrypted payloads.
 */
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

### B. `ServiceCompanyJobApplicationController.php` — `create()` & `clientcreate()`

```php
// Decrypt both parameters safely
$jobId = GeneralMethod::value_decrypt($jobId);
$appId = GeneralMethod::value_decrypt($appId);

$isEdit = !empty($appId) && $appId !== 0 && $appId !== '0';

// Skip coin validation during edit
if (!$isEdit) {
    $packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
    if ($packageStatus->status) {
        session()->flash('packageStatus', $packageStatus->message);
        $url = url('service_company/apptracker/' . @$jobId . '/all');
        return redirect($url);
    }
}

// In clientcreate(): relax job status constraint for edit flow
$query = DB::table('recommendation_relation')
    ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
    ->where('jobs.id', '=', $jobId)
    ->where('recommendation_relation.to_company_id', '=', $company_id);

if (!$isEdit) {
    $query->where('jobs.status', '=', 'active')
          ->where('recommendation_relation.final_status', '=', 1);
}

$this->job = $query->count();
if ($this->job == 0) {
    return abort(404);
}
```

---

## 4. Verification Matrix

| Test Case | Inputs | Expected Output |
|---|---|---|
| Edit with raw integer `jobId` | `/job-applications/create/123/456` | HTTP 200 (No DecryptException 404) |
| Edit with encrypted `jobId` | `/job-applications/create/eyJpZ.../456` | HTTP 200 |
| Edit with space-mutated URL | `/job-applications/create/eyJpZ%20.../456` | HTTP 200 |
| Edit when company has 0 coins | Company balance = 0, `$appId = 456` | HTTP 200 (No redirect to AppTracker) |
| Edit candidate on closed job | Job status = `closed`, `$appId = 456` | HTTP 200 (No 404 abort) |
| New candidate with 0 coins | Company balance = 0, `$appId = 0` | Redirect to AppTracker with flash message |
| New candidate on closed job | Job status = `closed`, `$appId = 0` | HTTP 404 abort |
