# Resume Upload Workflow Audit & Race Condition Hardening

## Metadata
- **Date:** 17 November 2026
- **Module:** Candidate Sourcing / Add & Edit Candidate (`service_company.job-applications`)
- **Impact Area:** Method A (Cloudflare R2 Direct PUT), Method B (Same-Origin Fallback Stream), Method C (Session Recovery), Form Submission Handlers (`create.blade.php`, `clientcreate.blade.php`, `bench-candidates/create.blade.php`), Backend Controllers (`ServiceCompanyJobApplicationController`, `JobApplyService`)
- **Author:** JERO Architecture Team
- **Rules Followed:** Rule 1 & 2 (CQRS), Rule 3 (Client-First Execution), Rule 7 & 17 (Zero Business Logic Regressions & Page Isolation), Rule 20 (Card + Sub-Card Design), Rule 21 (Audit Document Storage Standard)

---

## Executive Summary

Following recent enhancements to the **Edit Candidate** and **Candidate Creation** forms, recruiters experienced intermittent submission errors when uploading resumes:
```
Submission Failed: Resume upload is not yet complete. Please wait for the upload to finish and try again.
```
Simultaneously, a green notification banner (`Resume uploaded successfully using Method A`) would appear in the background, confusing users. Subsequent submit attempts within 120 seconds would then fail with a 409 Conflict:
```
Candidate submission is currently processing. Please wait...
```

### Forensic Root Cause Breakdown
1. **Premature Session UUID Exposure (Race Condition in `resume-presign.js`):**
   - In `resume-presign.js`, the hidden input `<input id="resume_session_uuid">` was populated immediately after `_presign` succeeded (~200ms), while the binary PUT to Cloudflare R2 and subsequent backend `_finalize()` API call were still asynchronously transferring data.
   - During this upload window, `resume_uploads.status` in MySQL was still `Pending`.
   - The browser resume card and client-side WebAssembly parser extracted resume fields in <400ms. If a recruiter clicked "Submit Candidate" while the direct R2 PUT or `_finalize()` was still finishing in the background, the form submitted with the `resume_session_uuid`.
   - Backend controllers evaluated `if ($resumeSession->status !== ResumeUploadStatus::Uploaded->value)` and aborted with `"Resume upload is not yet complete..."`.
   - 500ms to 1.5s later, `_finalize()` completed in the background, updating MySQL to `Uploaded` and rendering the green success banner behind the error modal.

2. **Redis Idempotency Lock Leak:**
   - In `ServiceCompanyJobApplicationController::store()`, line 3835 acquired an atomic Redis lock: `Redis::set("idemp:cand:{$company_id}:{$clientSubmissionUuid}", 'PROCESSING', 'EX', 120, 'NX')`.
   - When the pre-flight check failed at line 4210, the controller returned early **without releasing the Redis lock**.
   - On the frontend, `$('#client_submission_uuid').val()` was not rotated on error. When the user clicked "Submit Candidate" again, line 3835 encountered the existing lock and rejected the request as a 409 Conflict.

3. **Missing Submit Guard & State Machine Tracking:**
   - `create.blade.php`, `clientcreate.blade.php`, and `bench-candidates/create.blade.php` did not maintain an authoritative `window._resumeUploadState` machine or await active uploads during submission.
   - `detectCandidateMissingFields()` erroneously relied on `$('#resumeCheck').val() === '1'`, which was marked on raw file selection before upload had even started.

4. **Rigid Backend Status Gate (Zero Self-Healing):**
   - Even when the binary file had successfully landed in Cloudflare R2 (`Storage::disk('r2')->exists(...) === true`), the backend rejected requests if the status was `Pending` or `Uploading` instead of self-healing via `ResumeUploadSession::finalize()`.
   - In re-played or retried candidate submissions, if the session was already `Completed` for that candidate, atomic claim returned 0 and aborted.

---

## Architecture & Data Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       RECRUITER BROWSER CLIENT                              │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │
                         1. processFile(file)
                         ├─► window._resumeUploadState = 'uploading'
                         ├─► showUploadedCard() & WASM client parser
                         └─► ResumePresign.startUpload(file)
                                       │
                      2. POST /resume/v2/sessions
                      ├─► Receives presigned R2 PUT URL
                      ├─► DOES NOT populate #resume_session_uuid yet
                      │
                      3. Direct PUT to Cloudflare R2
                      ├─► Streams raw bytes directly to R2
                      │
                      4. POST /resume/v2/sessions/{uuid}/finalize
                      ├─► DB status transitions: Pending -> Uploaded
                      ├─► Populates #resume_session_uuid
                      └─► window._resumeUploadState = 'uploaded'
                                       │
                         [If Recruiter Clicks Submit Early]
                         ├─► handleCandidateSubmit detects _resumeUploadState === 'uploading'
                         ├─► Button shows: "Uploading Resume..." (disabled + spinner)
                         └─► Awaits ResumePresign.waitForUpload()
                                       │
                         5. executeCandidateSubmit()
                         ├─► Strips binary resume bytes (payload < 4 KB)
                         ├─► Sends resume_session_uuid
                                       │
┌──────────────────────────────────────▼──────────────────────────────────────┐
│                    FRANKENPHP / LARAVEL BACKEND                             │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │
       6. Pre-flight Session Validation:
          ├─► Status is Uploaded or Completed? -> PASS
          ├─► Status is Pending/Uploading BUT Storage::disk('r2')->exists() == true?
          │    └─► JIT Self-Healing: Calls ResumeUploadSession::finalize() -> PASS
          └─► If ANY pre-flight fails -> Releases Redis Lock immediately!
                                       │
       7. Atomic DB Transaction:
          ├─► Claim session: UPDATE resume_uploads SET status = 'Completed'
          │    (allows re-play if already completed for this candidate)
          ├─► Move session to company CDN storage path
          └─► Persist Candidate & Sync Elasticsearch (CQRS)
```

---

## File Changes Matrix

| File Path | Description of Changes |
| :--- | :--- |
| `public/js/resume-presign.js` & `js/resume-presign.js` | 1. Wrapped upload lifecycle in `_activeUploadPromise`.<br>2. Deferred `#resume_session_uuid` DOM assignment until **after** both R2 PUT and `_finalize()` succeed.<br>3. Cleared session UUID on cancellation/failure.<br>4. Added and exported `isUploading()`, `waitForUpload()`, and `getState()`. |
| `resources/views/service_company/job-applications/create.blade.php` | 1. Initialized and tracked `window._resumeUploadState` across Method A, Method B, and dropzone resets.<br>2. Added submit guard in `handleCandidateSubmit()` to show `"Uploading Resume..."` spinner and `await window.ResumePresign.waitForUpload()` / `_methodBUploadPromise`.<br>3. Updated `detectCandidateMissingFields()` to provide precise guidance when uploads are in progress or failed.<br>4. Rotated `client_submission_uuid` and `client_update_uuid` on any error response to prevent Redis idempotency lockouts. |
| `resources/views/service_company/job-applications/clientcreate.blade.php` | Applied complete parity with `create.blade.php`: state machine tracking, submit await guard, missing fields upload state check, and idempotency key rotation on error. |
| `resources/views/service_company/bench-candidates/create.blade.php` | 1. Added `await window.ResumePresign.waitForUpload()` to candidate submit handlers.<br>2. Added `#resume_session_uuid` existence check to `detectMissingFields()`. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | 1. Added JIT self-healing in `store()`, `update()`, and `clientappstore()`: checks R2 existence and finalizes pending sessions automatically.<br>2. Accepted both `Uploaded` and `Completed` statuses.<br>3. Released `$redisLockKey` in all early returns before database transactions.<br>4. In atomic claim, allowed replayed requests for the same candidate without throwing a false invalid session error. |
| `app/Services/JobApplyService.php` | 1. Added JIT self-healing check prior to candidate persistence.<br>2. Handled already `Completed` sessions for candidate apply flows. |

---

## 15.6" Screen Fit Verification

- **Resolutions:** `1366×768` and `1920×1080` (Rule 14 & Rule 20).
- **Submit Button State:** While waiting for resume upload, the action button dynamically displays:
  `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading Resume...`
  within its fixed bounds with zero horizontal shifting, overflow, or container displacement.
- **Card + Sub-Card Integration:** Resume card and dropzone transitions adhere strictly to Rule 20 without layout shifts or raw unstyled text.

---

## Verification Evidence

1. **PHP Syntax Validation (`php -l`):**
   ```
   No syntax errors detected in app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php
   No syntax errors detected in app/Services/JobApplyService.php
   ```

2. **Automated Unit & Feature Test Execution:**
   - `Tests\Feature\CandidateResumeUploadFallbackTest`: **4 passed** (35 assertions)
   - `Tests\Unit\ResumeUploadStatusTest`: **10 passed** (59 assertions)
   - `Tests\Feature\StrictSkillRatingValidationTest`: **8 passed** (17 assertions)
   - `Tests\Feature\CandidateEditWorkflowTest`: **13 passed** (17 assertions, 0 failed)

3. **End-to-End Resilience:**
   - Premature submit clicks while uploading smoothly wait for completion instead of failing.
   - Upload errors immediately unlock the form for retry without Redis 409 conflict lockouts.
   - Network delays during client finalization are automatically self-healed on the backend through R2 existence verification.
