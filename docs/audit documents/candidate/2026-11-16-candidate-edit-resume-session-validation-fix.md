# Candidate Edit: Resume Session Validation Fix (Method A / R2 Integration)

## Metadata
- **Date:** 2026-11-16
- **Module:** Candidate Profile Edit / Job Application (`ServiceCompanyJobApplicationController::update()`, `create.blade.php`, `clientcreate.blade.php`, `JobApplyService.php`)
- **Type:** Architectural Bug Fix & Validation Parity
- **Compliance:** Rule 1 & Rule 2 (CQRS MySQL Writes / ES Reads), Rule 3 (Client-First Execution), Rule 7 & Rule 17 (Zero Business Logic Regressions & Page Isolation), Rule 21 (Audit Standard Layout)

---

## Executive Summary
When candidates were updated on the edit profile screen (`page_event == 'EDIT'`), uploading a resume via **Method A** (direct browser-to-R2 upload via `window.ResumePresign`) succeeded on the client and displayed a green confirmation banner ("Resume uploaded successfully using Method A"). However, upon clicking **"Update Candidate"**, the server responded with a SweetAlert warning modal:
> **"Warning: Upload Resume is required"**

### Root Cause
1. In `create.blade.php` and `clientcreate.blade.php`, once Method A uploaded to R2, `executeCandidateSubmit()` ran `formData.delete('resume')` to strip the redundant multipart binary payload since the file was already in R2 and identified by `resume_session_uuid`.
2. In `ServiceCompanyJobApplicationController::update()` (line 8496), backend validation strictly checked:
   ```php
   $hasResume = $request->hasFile('resume') 
       || $request->filled('uploaded_file_path') 
       || $request->filled('resume_original_name') 
       || $request->input('resume_intent') === 'KEEP' 
       || ! empty($jobApplication->resume) 
       || ! empty($jobApplication->current_resume_id);
   ```
3. Line 8496 was completely unaware of `$request->filled('resume_session_uuid')`, `$request->filled('resumeCheck')`, and `resume_intent === 'ATTACH_IF_AVAILABLE'`. Since `formData.delete('resume')` had stripped the binary file and the candidate's existing record had no prior resume in DB, `$hasResume` evaluated to `false`, causing the controller to return `['status' => 'mailcheck', 'msgType' => 'warning', 'message' => 'Upload Resume is required']`.

---

## Architecture & Data Flow

```
[Candidate Edit Page]
         │
         ├── User uploads file (Method A: direct browser → Cloudflare R2)
         ├── window.ResumePresign.onStateChange(uploaded):
         │     ├── #resume_session_uuid = <uuid>
         │     ├── #resumeCheck = '1'
         │     └── #resume_upload_method = 'Method A'
         │
[Click "Update Candidate"]
         │
         ├── executeCandidateSubmit():
         │     ├── formData.delete('resume') (avoids re-uploading large file bytes)
         │     └── POST /service_company/job-applications/update/{id}
         │
         ▼
[ServiceCompanyJobApplicationController::update()]
         │
         ├── Validates hasResume:
         │     ├── $request->filled('resume_session_uuid')  <-- [FIXED: Recognized!]
         │     ├── $request->filled('resumeCheck')
         │     ├── in_array($request->input('resume_intent'), ['KEEP', 'ATTACH_IF_AVAILABLE'])
         │     └── MySQL SSOT Fallback (job_applications / resume_assets)
         │
         ├── v2 Atomic Claim:
         │     └── ResumeUpload::where('uuid', $sessionUuid)->update(['status' => 'Completed'])
         │
         └── DB Transaction:
               ├── Saves candidate profile details
               ├── Associates new resume asset with candidate
               └── Atomically invalidates tenant cache
```

---

## File Changes Matrix

| File Path | Component | Change Description |
|-----------|-----------|--------------------|
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | `update()` (line 8496) | Added checks for `resume_session_uuid`, `resumeCheck`, `existing_resume_path`, `ATTACH_IF_AVAILABLE` intent, and MySQL SSOT fallback query. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | `checkCandidateSourcingMissingFields()` (line 11884) | Added MySQL SSOT fallback so momentary ES index latency never falsely flags 'Upload Resume'. |
| `app/Services/JobApplyService.php` | `submitApplication()` (line 166) | Added `resumeCheck` and `resume_intent` support to resume validation parity. |
| `resources/views/service_company/job-applications/create.blade.php` | Client Upload & Validation | Populated `resume_original_name` on Method A upload; added `resume_session_uuid` and `resumeCheck` checks to `detectCandidateMissingFields()`. |
| `resources/views/service_company/job-applications/clientcreate.blade.php` | Client Upload & Validation | Populated `resume_original_name` on Method A upload; added `resume_session_uuid` and `resumeCheck` checks to `detectCandidateMissingFields()`. |
| `tests/Feature/StrictSkillRatingValidationTest.php` | Feature Tests | Added `test_check_candidate_sourcing_missing_fields_accepts_resume_from_mysql_fallback()`. |

---

## 15.6" Screen Fit & UI Responsiveness
- The post-upload card (`#upInfo`) displays file name, size, type (DOCX/PDF), and upload timestamp cleanly within its card boundaries without horizontal spill or displacement on standard 1366×768 and 1920×1080 screens.

---

## Verification Evidence

### 1. Feature Test Execution
```bash
php artisan test --filter=StrictSkillRatingValidationTest
```
**Output:**
```
   PASS  Tests\Feature\StrictSkillRatingValidationTest
  ✓ get candidate skill rating status detects unrated skills                                                     1.15s  
  ✓ partial rating fails validation                                                                              0.30s  
  ✓ full rating passes validation                                                                                0.28s  
  ✓ check candidate sourcing missing fields includes skill rating                                                0.29s  
  ✓ salary details pass when yearly ctc is provided                                                              0.28s  
  ✓ salary details fail when both ctc are empty                                                                  0.28s  
  ✓ candidate job skills with zero years experience passes validation when rated                                 0.50s  
  ✓ check candidate sourcing missing fields accepts resume from mysql fallback                                   0.29s  

  Tests:    8 passed (17 assertions)
  Duration: 4.96s
```

### 2. PHP Syntax Lint
```bash
php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php
# No syntax errors detected in app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php

php -l app/Services/JobApplyService.php
# No syntax errors detected in app/Services/JobApplyService.php
```
