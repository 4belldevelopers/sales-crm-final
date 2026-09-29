# V4 Master Playbook: Client-Side Resume Parser & Direct Cloudflare R2 Upload (Method A)

**Document Version:** 4.0  
**Target Systems:** JERO Multi-Tenant B2B SaaS (Service Company, Product Company, Staffing Company, Career Portals)  
**Architecture Rules Compliance:** Rules 1, 2, 3, 4, 5, 7, 10, 14, 17 (CLAUDE.md / AGENTS.md)  
**Primary Modules:** `service_company/bench-candidates/create.blade.php`, `service_company/job-applications/create.blade.php`

---

## Executive Summary & Architecture Overview

The JERO talent acquisition platform implements a **Decoupled Dual-Pipeline Architecture** for resume handling:

```
[User Selects Resume File (PDF / DOC / DOCX)]
              │
              ├──► [PIPELINE 1: 100% Client-Side In-Browser WASM Resume Parser]
              │      ├── Dynamic import: https://parser.jero.works/jero-resume-parser-2tURGwDx.js
              │      ├── In-memory ArrayBuffer snapshot (zero main thread freeze)
              │      ├── WebAssembly PDF.js / Mammoth text & layout extraction
              │      ├── Compromise NLP entity extraction (Name, Email, Phone, Skills, Exp, Education)
              │      └── Non-destructive autofill of form fields & skills table in Browser RAM
              │          (0% MySQL reads, 0% Laravel backend processing, 0% ES strain)
              │
              └──► [PIPELINE 2: Storage Upload Pipeline]
                     ├── PRIMARY: Method A (Direct-to-Cloudflare R2 Browser Upload)
                     │     ├── 1. POST /resume/v2/sessions (with Idempotency-Key)
                     │     ├── 2. Receive Cloudflare R2 pre-signed PUT URL
                     │     ├── 3. Direct browser PUT to R2 via XHR (real progress, zero PHP bytes)
                     │     └── 4. POST /resume/v2/sessions/{uuid}/finalize (marks session Uploaded)
                     │
                     └── FALLBACK: Method B (Circuit-Breaker Server Application Upload)
                           └── ONLY triggered if Method A encounters hard transport failure (CORS/proxy)
                               Streams raw bytes via php://input to R2 with X-Skip-Backend-Parse: 1
```

---

## Phase 1: Header Base UI Crash Audit & Layout Isolation

### 1.1 The Issue
On loading the Add Talent or Add Candidate page, the console threw:
```
Uncaught TypeError: Cannot read properties of null (reading 'offsetWidth')
```
This halted subsequent script initialization in Metronic layout components before form scripts could execute.

### 1.2 Root Cause
Metronic dropdown positioning calculations in `header-base.blade.php` accessed `.offsetWidth` directly on element references without validating if the DOM node was rendered in the current user role layout.

### 1.3 Remediation
In `resources/views/sections/service_company/header-base.blade.php`:
- Added null-checks and safe optional chaining before inspecting layout dimensions:
```javascript
var menuEl = document.querySelector('#kt_header_nav');
if (menuEl && menuEl.offsetWidth) {
    // safe layout calculation
}
```

---

## Phase 2: Client-Side Resume Parsing Integration (`parser.jero.works`)

### 2.1 The Issue
The legacy parser executed on the backend using Python microservices or delayed Horizon queue workers. When workers were congested or offline, candidate autofill hung indefinitely, causing recruiter friction.

### 2.2 Remediation: Standalone In-Browser WASM Parser Bridge
Created isolated partial `resources/views/service_company/bench-candidates/jero-bench-parser.blade.php`:

1. **Dynamic Module Import**:
   ```javascript
   var PRODUCTION_PARSER_URL = 'https://parser.jero.works/jero-resume-parser-2tURGwDx.js';
   import(PRODUCTION_PARSER_URL).then(function (mod) {
       var parseResumeFn = mod.parseResume || (mod.default && mod.default.parseResume);
   });
   ```

2. **In-Memory File Snapshot**:
   Prevents file lock or garbage-collection issues during parallel storage upload:
   ```javascript
   var rawBuf = await file.arrayBuffer();
   var selectedFile = new File([rawBuf], file.name, {
       type: file.type || 'application/pdf',
       lastModified: file.lastModified || Date.now()
   });
   ```

3. **Non-Destructive Form Mapping (`JeroBenchFormBridge`)**:
   Never overwrites fields that the recruiter has already typed:
   ```javascript
   if (parsed.name && !$('#full_name').val()) {
       $('#full_name').val(parsed.name).trigger('change');
   }
   ```

4. **Overlapping Experience Interval Calculation**:
   Merges date intervals to prevent duplicate month counting when candidates list concurrent roles:
   ```javascript
   function calculateTotalExperience(experienceList) {
       // Intervals merged: (2020-2022) + (2021-2023) = 3 years (not 4)
   }
   ```

5. **Date of Birth & Qualification Normalization**:
   Transforms varied date formats (`DD/MM/YYYY`, `MM-DD-YYYY`, `15th August 1995`) into standard `YYYY-MM-DD` and fuzzy-matches degree strings against qualification `<select>` options.

---

## Phase 3: Decommissioning Legacy Server-Side Parsing

### 3.1 The Issue
Legacy server parser scripts continued to execute in parallel, causing:
- Duplicate uploads to `/uploadresume`
- Unnecessary POST calls to `/extract-resume-applications`
- Redundant polling intervals (`_parseStatusInterval`) hitting `/status` every 4 seconds

### 3.2 Remediation
1. **Decommissioned `re_test()`**:
   Deleted the legacy function that called `service_company.extract.resume-job-applications`.
2. **Decommissioned `handleFileInputChange()` & `normalizeResumeText()`**:
   Removed duplicate file input change listeners that triggered second server uploads.
3. **Decommissioned Polling Variables**:
   Removed `_parseStatusInterval` and `_resumeUploadId`.
4. **Enforced `X-Skip-Backend-Parse: 1`**:
   Ensured all upload routes receive this header so Horizon background jobs (`ParseResumeJob`) are never queued:
   ```javascript
   upXhr.setRequestHeader('X-Skip-Backend-Parse', '1');
   ```

---

## Phase 4: Resolving the PDF Method B Fallback Bug (Phantom `PUT /login 405`)

### 4.1 Forensic Analysis of the Bug
When uploading `.docx` files, Method A (Direct R2 Upload) succeeded. When uploading `.pdf` files, the system silently defaulted to Method B (Server Upload) with the following network sequence:
1. `undefined`
2. `login` (`PUT https://testing.jero.works/login` $\to$ `405 Method Not Allowed`)
3. `uploadresume` (`POST 200 OK`)

#### Why Did This Happen?
1. **MIME Type Mismatch on Client**:
   On Windows/Chromium, PDF files often report `file.type` as `""` (empty string) or `application/x-pdf`.
   The presign call sent `mime_type: file.type || 'application/octet-stream'`.
2. **Validation Failure on Server**:
   `StoreResumeSessionRequest` rejected `application/octet-stream` or `application/x-pdf` because `allowed_mime_types` only permitted `application/pdf`.
3. **Redirect Loop on Validation Failure**:
   Because the initial presign request lacked `Accept: application/json`, Laravel treated it as a web form request and returned a `302 Found` redirect to the previous page or `/login`.
4. **`upload_url` Became Undefined**:
   The browser's `fetch` followed the redirect and received HTML. JSON parsing produced an empty `{}` object, leaving `session.upload_url` as `undefined`.
5. **The Phantom `PUT` Request**:
   `_putToR2Once` called `xhr.open('PUT', uploadUrl)`. The browser converted `undefined` into the string `"undefined"`, issuing a `PUT` to `https://testing.jero.works/.../undefined`.
6. **The 405 Error**:
   The server redirected the non-existent relative URL to `/login`. The browser followed the redirect retaining method `PUT`. Laravel's `/login` route only allows `GET`/`POST`, returning `405 Method Not Allowed`.
7. **Circuit-Breaker Triggered**:
   `shouldUseResumeUploadFallback(error)` saw status `405` and fell back to Method B.

### 4.2 The Fix

#### Step 1: MIME Canonicalization in `StoreResumeSessionRequest.php`
Automatically derives the canonical MIME type from the file extension before validation rules run:
```php
protected function prepareForValidation(): void
{
    $filename = (string) $this->input('original_filename', '');
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $canonicalMimeMap = [
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    if (isset($canonicalMimeMap[$ext])) {
        $this->merge([
            'mime_type' => $canonicalMimeMap[$ext],
        ]);
    }
}
```

#### Step 2: Expanded Allowed MIME Types in `config/resumes.php`
Includes all common browser variants:
```php
'allowed_mime_types' => [
    'application/pdf',
    'application/x-pdf',
    'application/acrobat',
    'applications/vnd.pdf',
    'text/pdf',
    'application/msword',
    'application/vnd.ms-word',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/zip',
    'application/x-zip-compressed',
    'application/octet-stream',
],
```

#### Step 3: Absolute URL Validation & MIME Handling in `resume-presign.js`
In `public/js/resume-presign.js` and `js/resume-presign.js`:
```javascript
// 1. Strict URL validation — prevents relative/undefined PUT requests
if (!uploadUrl || typeof uploadUrl !== 'string' || !/^https?:\/\//i.test(uploadUrl)) {
    var invalidUrlErr = new Error('Invalid upload URL: ' + uploadUrl);
    invalidUrlErr.retryable = false;
    reject(invalidUrlErr);
    return;
}

// 2. Canonical Content-Type header on R2 PUT
var mimeMap = {
    pdf:  'application/pdf',
    doc:  'application/msword',
    docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
};
var ext = (file.name.split('.').pop() || '').toLowerCase();
var putContentType = mimeMap[ext] || file.type || 'application/octet-stream';

xhr.open('PUT', uploadUrl, true);
xhr.setRequestHeader('Content-Type', putContentType);
```

#### Step 4: Cache-Busting Script Inclusion
In `create.blade.php`:
```html
<script src="{{ asset('js/resume-presign.js') }}?v=20260929-godlevel"></script>
```
Forces every browser to fetch the updated script, eliminating stale cached versions.

---

## Phase 5: Implementation Guide for Any Other Module

To apply this pattern to any other module (e.g., `job-applications/create.blade.php`, `career-apply.blade.php`):

### 1. In the Blade View
1. Include the engine scripts with cache-busting:
   ```html
   <script src="{{ asset('js/resume-presign.js') }}?v=20260929-godlevel"></script>
   @include('service_company.bench-candidates.jero-bench-parser')
   ```
2. Initialize the feature flag and presign URL:
   ```javascript
   @if(config('resumes.features.browser_upload') && \Illuminate\Support\Facades\Schema::hasTable('resume_uploads'))
   window.RESUME_PRESIGN_URL = @json(route('service_company.resume.v2.sessions.store'));
   @endif
   var RESUMES_FEATURE_BROWSER_UPLOAD = !!(window.ResumePresign && window.RESUME_PRESIGN_URL);
   ```
3. Wire the parallel execution in `processFile(file)`:
   - Call `window.JeroBenchClientParser.parseAndPopulate(file, { ... })` for in-browser parsing.
   - Concurrently call `window.ResumePresign.startUpload(file, contextKey)` for Method A storage upload.
   - Include the circuit-breaker fallback to Method B inside `.catch(error)`.

### 2. In the Controller / FormRequest
1. Ensure the store request extends `CoreRequest` and implements `prepareForValidation()` as demonstrated in Section 4.2.
2. In the submit controller handler:
   ```php
   if ($request->filled('resume_session_uuid')) {
       // Method A: finalize R2 session
       $resumeSession = ResumeUpload::byUuid($request->resume_session_uuid)
           ->forCompany($companyId)
           ->first();
       if ($resumeSession) {
           $finalResumeUrl = app(ResumeUploadSession::class)->moveToCompanyPath($resumeSession);
           $application->resume_path = $finalResumeUrl;
       }
   } elseif ($request->filled('uploaded_file_path')) {
       // Method B: fallback file path
       $application->resume_path = $request->uploaded_file_path;
   }
   ```

---

## Phase 6: Diagnostic & Troubleshooting Runbook

If any resume fails to upload via Method A in the future, follow this checklist:

| Check | Expected Value | If Failed |
|---|---|---|
| **Console Network Filter** | Check `sessions` POST request | If 422: verify `prepareForValidation` in `StoreResumeSessionRequest`. |
| **`Accept` Header** | Must be `application/json` | Prevents Laravel 302 redirects on validation failures. |
| **R2 PUT Request** | Host: `*.r2.cloudflarestorage.com`, Method: `PUT` | If host is `testing.jero.works`: script cache issue or invalid URL guard bypassed. |
| **R2 PUT Status** | `200 OK` | If 403: verify R2 bucket CORS settings (AllowedOrigins, AllowedMethods PUT, AllowedHeaders *). |
| **Script Tag Version** | `?v=20260929-godlevel` | Bump version tag if changes made to `public/js/resume-presign.js`. |
| **File Size Limit** | $\le 6 \text{ MB}$ (6,291,456 bytes) | Handled by frontend guard and backend validator. |

---

## Verification Test Commands

Run the following test suites to verify pipeline integrity:

```bash
# 1. Add Talent & Parser Remediation Test Suite (9 tests, 77 assertions)
php artisan test --filter=V4AddTalentRemediationTest

# 2. Resume Upload Session Lifecycle Service Test Suite (14 tests, 25 assertions)
php artisan test --filter=ResumeUploadSessionServiceTest

# 3. Resume Upload Fallback Circuit-Breaker Test Suite (4 tests, 35 assertions)
php artisan test --filter=CandidateResumeUploadFallbackTest
```
