# Implementation Plan: Fix Candidate CV Display Mismatch in Production

## Overview & Objectives
Fix the environment-specific bug where uploading a candidate CV on the live/production server sometimes displays a previous candidate's CV.

The solution ensures:
1. **Zero Non-Unique Database URLs**: The HTTP controller will compute and store the candidate-specific, collision-proof URL (`.../{jobApplicationId}_{safeBaseName}_{uuidSuffix}.{ext}`) synchronously before queue dispatch.
2. **Deterministic Queue Processing**: The background job `UploadResumeToTestRTwo` will use the pre-computed unique object path so R2 moves the temp file to the exact path recorded in the database.
3. **Queue Latency Resilience / Stream Fallback**: In `fetchResumeBytes` / `resumeAsPdf`, if a user views a newly uploaded CV while the job is still queued in Redis, the system will fall back to reading from the candidate's temporary R2 file (`temp/...`) seamlessly instead of returning 404 or a stale cached file.
4. **Fix Update Logic Bug**: Correct `ServiceCompanyJobApplicationController@update` so `resume` is not wiped when `resumeCheck` is omitted during file upload.

---

## Detailed Step-by-Step Changes

### Step 1: Pre-compute Unique Resume Filename & URL in Helper / Service
Create a helper method `GeneralMethod::generateUniqueResumePath($jobApplicationId, $originalFileName, $companyId)` or inline standard deterministic generator:

```php
$baseName = pathinfo($full_name, PATHINFO_FILENAME);
$ext = strtolower(pathinfo($full_name, PATHINFO_EXTENSION));
$safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
$uuidSuffix = \Illuminate\Support\Str::uuid();
$uniqueFileName = "{$jobApplication->id}_{$safeBaseName}_{$uuidSuffix}.{$ext}";
$r2Path = "Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$uniqueFileName}";
$fileUrl = "https://file.jero.works/{$r2Path}";
```

### Step 2: Update Controllers & Services
Update the following places to store `$fileUrl` containing `$uniqueFileName` synchronously:
1. [ServiceCompanyJobApplicationController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php) (`store` & `update`)
2. [ServiceCompanyBenchCandidatesController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php) (`store` & `update`)
3. [JobApplyService.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Services/JobApplyService.php)

Pass `$r2Path` / `$uniqueFileName` to `UploadResumeToTestRTwo` so the queue job moves the temp file to that exact `$r2Path`.

### Step 3: Enhance `UploadResumeToTestRTwo` Job
Ensure `UploadResumeToTestRTwo` accepts the target `$r2Path` or uses the same deterministic logic, moves `tempPath` to `$r2Path`, and verifies `job_applications.resume` is updated to `$dbSave`.

### Step 4: Queue Delay Fallback in `fetchResumeBytes()`
In `fetchResumeBytes($resume)` / `resumeAsPdf`:
If `Storage::disk('r2')->exists($key)` returns false (because the background queue job is still processing in Redis), check if the candidate's `tempPath` / `CandidateResumeUpload` record exists in R2 `temp/` and serve bytes from there.

---

## Verification Plan

### Automated Checks & Syntax
1. PHP Linting:
   - `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
   - `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
   - `php -l app/Services/JobApplyService.php`
   - `php -l app/Jobs/UploadResumeToTestRTwo.php`
2. Code Style:
   - `./vendor/bin/pint --test`

### Manual Verification
1. Upload candidate CV with generic filename `Resume.pdf`.
2. Inspect `job_applications.resume` column immediately after form submission: verify it contains a candidate-specific unique URL (`.../123_Resume_uuid.pdf`), NOT generic `.../Resume.pdf`.
3. Test with async queue (simulate production queue delay) and verify viewing CV returns the newly uploaded file.
4. Upload a second candidate CV also named `Resume.pdf` and verify zero collision/cross-talk between candidate CVs.
