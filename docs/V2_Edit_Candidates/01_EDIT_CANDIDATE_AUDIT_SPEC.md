# Candidate Edit & Submit V2 — Technical Audit & Architecture Specification

> **Subsystem:** Candidate Profile Editing & ATS Update Pipeline  
> **Routes:** `GET job-applications/create/{jobId}/{appId}`, `POST job-applications/update/{id}`  
> **Source Files:** `ServiceCompanyJobApplicationController.php`, `GeneralMethod.php`, `create.blade.php`

---

## 1. Problem Statement & Symptoms

Recruiters experienced two separate 404 failure modes during candidate editing:
1. **Failure Mode 1**: Clicking "Edit Candidate" resulted in a 404 error or redirected to the dashboard.
2. **Failure Mode 2**: Opening the edit form succeeded, but **clicking Submit on the edit form resulted in a 404 error**.

---

## 2. Technical Audit: Why Submit on Edit Caused 404

### Root Cause Analysis

1. **`$this->application` was never loaded in `create()`**:
   - In `ServiceCompanyJobApplicationController::create($jobId, $appId)`, line 1561 assigned `$this->appId = $appId`, but **failed to fetch the candidate record from MySQL** (`$this->application = JobApplication::find($appId)`).

2. **Empty `{id}` in JavaScript Form Submit Route**:
   - In `create.blade.php` (Line 2593), the AJAX submission URL was generated using Blade syntax:
     ```javascript
     url: '{{ route("service_company.job-applications.update", @$application->id) }}'
     ```
   - Because `$application` was `null`, `@$application->id` evaluated to an empty string.
   - The rendered JavaScript evaluated to:
     ```javascript
     url: 'https://testing.jero.works/service_company/job-applications/update/' // Missing candidate ID!
     ```

3. **Laravel Router Rejected the Request**:
   - The route in `routes/service.php` requires a parameter: `Route::post('job-applications/update/{id}')`.
   - When the browser sent an AJAX POST to `/job-applications/update/` (without `{id}`), Laravel could not match the route and threw **HTTP 404 Not Found**.

4. **Missing Decryption in `update()`**:
   - In `ServiceCompanyJobApplicationController::update($request, $id)`, if `$id` was passed as an encrypted string, `update()` did not call `GeneralMethod::value_decrypt($id)`. The SQL query `ajwc.job_application_id = $id` attempted to match an encrypted string against integer column `job_application_id`, returning 0 rows.

---

## 3. The 2-Stage Architecture Fix

```
STAGE 1: Opening Edit Form (/create/{jobId}/{appId})
   ├── Safe value_decrypt($jobId) & value_decrypt($appId)
   ├── Bypass coin check for edits ($appId > 0)
   └── Load candidate record: $this->application = JobApplication::find($appId)

STAGE 2: Submitting Edit Form (/update/{id})
   ├── Render JS submit URL: url('/service_company/job-applications/update/' + candidateId)
   ├── Decrypt $id in update(): $id = GeneralMethod::value_decrypt($id)
   └── Execute candidate update & ES re-indexing
```

---

## 4. Code Fix Specification

### A. `ServiceCompanyJobApplicationController.php` — `create()` method

```php
$jobId = GeneralMethod::value_decrypt($jobId);
$appId = GeneralMethod::value_decrypt($appId);

$isEdit = !empty($appId) && $appId !== 0 && $appId !== '0';

if ($isEdit) {
    $this->application = JobApplication::find($appId);
}
```

### B. `ServiceCompanyJobApplicationController.php` — `update()` method

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

### C. `create.blade.php` — Form Action JS

```javascript
var candidateId = '{{ @$application->id ?: @$appId }}';
var updateUrl = '{{ url("service_company/job-applications/update") }}/' + candidateId;
```
