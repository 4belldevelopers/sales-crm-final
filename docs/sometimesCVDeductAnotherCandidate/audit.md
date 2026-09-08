# Root Cause Audit: Candidate CV Display Mismatch in Production

## Executive Summary
In live/production environments, uploading a new candidate CV sometimes results in displaying the previous candidate's CV instead of the newly uploaded document. In local environments (`QUEUE_CONNECTION=sync`), this bug never reproduces. 

This audit pinpoints the exact architectural root cause: **a race condition between synchronous HTTP controller database writes of non-unique file URLs and asynchronous queue job execution in production.**

---

## Technical Root Cause Analysis

### 1. Non-Unique URL Saved Synchronously in HTTP Controller
When a user uploads a CV and submits the candidate form, the request lands in `ServiceCompanyJobApplicationController@store` (or `@update`), `ServiceCompanyBenchCandidatesController`, or `JobApplyService`.

In these methods, the controller dispatches an asynchronous background job (`UploadResumeToTestRTwo`) to the `emails` queue. **Immediately after dispatching**, the HTTP controller executes a database update:

```php
// ServiceCompanyJobApplicationController.php (lines 2312-2315)
$fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$full_name}";
DB::table('job_applications')
    ->where('id', $jobApplication->id)
    ->update(['resume' => $fileUrl]);
```

Notice that `$full_name` is the **original filename** uploaded by the user (e.g. `Resume.pdf` or `CV.pdf`).

### 2. Unique Filename Created Asynchronously in Queue Job
Inside `UploadResumeToTestRTwo::handle()`, the background job constructs a candidate-specific, collision-proof filename containing candidate ID and UUID:

```php
// UploadResumeToTestRTwo.php (lines 83-94)
$uniqueFileName = "{$this->jobApplicationId}_{$safeBaseName}_{$uuidSuffix}.{$extension}";
$r2Path = "Companies/{$company}/Resumes/{$year}/{$month}/{$uniqueFileName}";
$dbSave = "https://file.jero.works/".$r2Path;

Storage::disk('r2')->move($tempPath, $r2Path);

DB::table('job_applications')
    ->where('id', $this->jobApplicationId)
    ->update(['resume' => $dbSave]);
```

### 3. Why Local Works vs Production Fails
- **Local Server (`QUEUE_CONNECTION=sync`)**:
  `UploadResumeToTestRTwo` executes **inline synchronously** during the HTTP request. By the time `store()` returns a response, the background job has already completed, moved the R2 file to `$uniqueFileName`, and updated `job_applications.resume` to the unique URL `.../123_Resume_uuid.pdf`. The non-unique URL is overwritten before any user or browser can read it.

- **Live/Production Server (`QUEUE_CONNECTION=redis`/`database`/Horizon)**:
  `UploadResumeToTestRTwo` is pushed to a background Redis queue.
  1. The HTTP request finishes immediately and redirects the user to the candidate page.
  2. The database row `job_applications.resume` is set to `.../Resumes/2026/09/Resume.pdf` (non-unique `$full_name`).
  3. If another candidate in the same company previously uploaded a file named `Resume.pdf` (a generic, highly common filename):
     - The CDN (Cloudflare) or browser HTTP cache serves the **cached file from the previous candidate**.
     - Or if the previous file was still at that static path in R2 before being moved, it reads the previous candidate's physical document.
  4. The recruiter clicks "View CV" and sees Candidate A's CV on Candidate B's profile.

---

## Additional Findings & Edge Cases

### A. Unconditional Resume Wipe in `update()` Method
In `ServiceCompanyJobApplicationController@update` (line 6056):
```php
if (empty($request->resumeCheck)) {
    DB::table('job_applications')
        ->where('id', $jobApplication->id)
        ->update(['resume' => '']);
}
```
If `$request->resumeCheck` is missing or empty, `update()` clears the `resume` column unconditionally, even if a new file was uploaded in the same request.

### B. Inconsistent URL Construction across Controllers
- `ServiceCompanyJobApplicationController`: `.../Companies/{company}/Resumes/{Y}/{m}/{full_name}`
- `JobApplyService`: `https://file.jero.works/{full_name}`
Both write non-unique URLs synchronously before queue processing.

---

## Affected Files
1. [ServiceCompanyJobApplicationController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php) (lines 2300–2325, 6030–6060, 1470–1496)
2. [ServiceCompanyBenchCandidatesController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php) (lines 1690–1738)
3. [JobApplyService.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Services/JobApplyService.php) (lines 445–465)
4. [UploadResumeToTestRTwo.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Jobs/UploadResumeToTestRTwo.php) (lines 80–97)
