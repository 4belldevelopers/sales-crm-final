# GOD-LEVEL SPECIFICATION & IMPLEMENTATION PLAN: IN-HOUSE BENCH TALENT DOWNLOAD RESUME CENTER

> **Document Type:** Authoritative Architectural Specification & Step-by-Step Implementation Plan  
> **Target Module:** Service Company — In-House Bench Talent (`resources/views/service_company/bench-candidates/`, `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`)  
> **Feature Name:** Dedicated In-House Bench Talent Download Resume Center (`AgyBenchDownloadResumeEngine`)  
> **Concurrency Target:** **10,000 Concurrent Users** with **0% Server/DB Read Strain** (Client-First WASM Architecture)  
> **Status:** APPROVED & READY FOR IMPLEMENTATION  
> **Author:** Antigravity AI Systems & JERO Architecture Council  
> **Timestamp:** October 2026  
> **Storage Path:** `docs/Feautres/BENCH_TALENT_DOWNLOAD_RESUME_CENTER_SPECIFICATION_AND_PLAN.md`  

---

## 1. Executive Summary & Architectural Mandate

### 1.1 The Core Problem
In the Service Company Bench Candidates module (`resources/views/service_company/bench-candidates/showdetail.blade.php`), the current "Download Resume" button is a primitive 2-option dropdown:
1. **Option 1 (Original Resume):** Direct link to `route('download.resume', ...)`.
2. **Option 2 (Resume with Company Logo):** Triggers `hdrDownloadWithLogoBench()`, which programmatically clicks the `#resume_cv` tab in the candidate drawer and polls the DOM for Engine 1 (`AgyResumeRenderer.js`) to click an internal button.

This approach presents critical limitations:
- **No Format Flexibility:** Recruiters cannot download the candidate's **In-House Evaluation Report**, **Highlighted Keyword Resumes**, **Anti-Poaching Blind Profiles (PII Redacted)**, or **Recruiter Packages**.
- **Brittle DOM Dependency:** Relying on automated clicks in the `#resume_cv` viewer tab is fragile, slow (1–3 second polling delay), and fails if the viewer tab hasn't rendered.
- **Data Asymmetry:** Recruiters cannot download the same 15 high-fidelity formats available in the Forward Resume drawer.

### 1.2 The Solution
Build a dedicated, isolated, client-first **In-House Bench Talent Download Resume Center**:
1. **All 15 Formats Available for Instant Download:** Mirrors the exact formats offered in Forward Resume (Original, Branded, In-House Evaluation Report Dossier, Recruiter Packages, Evaluated Skills Highlights, PII Masking, Full Privacy Packages with/without Branding).
2. **100% Client-First WASM Architecture:** All vector stamping, keyword highlighting, PII redaction, dossier generation, and multi-page PDF merging execute **100% in User RAM via WebAssembly**. The server spends **0 MB of PDF compilation RAM** and **0 ms of PDF rendering CPU**.
3. **Instant Direct Download Trigger:** Clicking any format immediately compiles the PDF in browser memory and triggers native browser file download (`URL.createObjectURL(blob)` $\rightarrow$ `a.download = ...; a.click()`) with zero page reload and zero network upload overhead.
4. **Authoritative In-House Bench Dossier Data:** The In-House Evaluation Report Dossier authoritatively uses `bench_candidate_rm_comments` (Relationship Manager comments), `bench_candidate_skill_experience` (rated skills with gold stars and verified experience), city-only location, commercials, and bench availability. Never stale data!
5. **Strict Rule 15 & Rule 17 Isolation:**
   - **Rule 15 (OpenDoc Engine Isolation):** Uses a dedicated isolated engine `AgyBenchDownloadResumeEngine` (`window.AgyBenchDownloadResumeEngine`). It is physically decoupled from `AgyBenchForwardResumeEngine` (Forward Resume) and `AgyResumeRenderer.js` (Job Applications).
   - **Rule 17 (Complete Page/Blade Isolation):** Dedicated drawer container `#kt_bench_download_resume_drawer`, isolated CSS, isolated JS state, and dedicated backend endpoints.

---

## 2. The Tri-Engine Isolation Matrix (Rule 15 Compliance)

To guarantee zero regressions and zero blast radius across JERO's document processing features, the platform enforces strict physical decoupling:

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                             JERO DOCUMENT ENGINE ISOLATION MATRIX (RULE 15)                                     │
├─────────────────────────┬─────────────────────────┬─────────────────────────────┬───────────────────────────────────────────────┤
│ Architectural Dimension │ Engine 1 (Job Apps RDC) │ Engine 2 (Bench Forward)    │ Engine 1-B: BENCH DOWNLOAD CENTER (NEW)       │
├─────────────────────────┼─────────────────────────┼─────────────────────────────┼───────────────────────────────────────────────┤
│ **Engine File**         │ AgyResumeRenderer.js    │ _bench_forward_resume_engine│ **_bench_download_resume_engine.blade.php**    │
│ **Global Namespace**    │ window.AgyResumeRenderer│ window.AgyBenchForwardResume│ **window.AgyBenchDownloadResumeEngine**       │
│ **Target Module**       │ Job Applications        │ Bench Talent Forward Email  │ **In-House Bench Talent Download Center**     │
│ **Primary View / Blade**│ job-applications/show...│ bench-candidates/partials...│ **bench-candidates/partials/download_resume...│
│ **Drawer Shell**        │ #rdcDrawer              │ #kt_bench_fwd_resume_drawer │ **#kt_bench_download_resume_drawer**          │
│ **Primary Output**      │ Single Direct Download  │ FormData Multipart Email    │ **Single Direct Instant Download**            │
│ **Dossier Data Model**  │ Job Requisition / Fit   │ Bench Availability / RM     │ **Authoritative In-House Bench Talent Dossier**│
│ **WASM Architecture**   │ Client-Side User RAM    │ Client-Side User RAM        │ **Client-Side User RAM (100% WASM)**          │
│ **Blast Radius Risk**   │ ZERO (Isolated)         │ ZERO (Isolated)             │ **ZERO (100% Isolated Fortress)**             │
└─────────────────────────┴─────────────────────────┴─────────────────────────────┴───────────────────────────────────────────────┘
```

> **Ironclad Directive:** `AgyBenchDownloadResumeEngine` must NEVER import, call, or share state with `AgyBenchForwardResumeEngine` or `AgyResumeRenderer.js`. All utilities, coordinate calculations, vector stamps, and dossier generators are self-contained within the dedicated engine.

---

## 3. High-Altitude Flow of Execution

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                       BENCH DOWNLOAD RESUME CENTER: EXECUTION FLOW (10K READY)                                  │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

[Recruiter Browser (User RAM)]                                                    [JERO Cloud Infrastructure]
  │                                                                                           │
  │ 1. Click "Download Resume" in Bench Talent Header                                         │
  │    Check `BenchDownloadResumeRamStore` in User RAM                                        │
  │    ├── Cache Hit: Open Drawer in 0ms (Instant Hydration)                                  │
  │    └── Cache Miss: POST /bench-candidates/download_center ───────────────────────────────►│ Web Server (FrankenPHP)
  │        ◄── Return Drawer HTML + Authoritative Dossier JSON ───────────────────────────────┤ • Candidate Metadata
  │                                                                                           │ • bench_candidate_rm_comments
  │ 2. Background Pre-Warm (Parallel on Drawer Open)                                          │ • bench_candidate_skill_experience
  │    Fetch raw resume bytes from same-origin proxy                                          │ • Authoritative Fingerprint
  │    GET /bench-candidates/download/{id} ──────────────────────────────────────────────────►│ Cloudflare R2
  │    ◄── Stream PDF / DOCX bytes to User RAM (rsDownloadBytesCache) ────────────────────────┤ (Zero Server Read Load)
  │                                                                                           │
  │ 3. Recruiter Selects Format (Options 1, 5, 41–54)                                         │
  │    Click "Download Format" Button                                                         │
  │    │                                                                                      │
  │    ▼                                                                                      │
  │ 4. CLIENT-SIDE COMPILATION (AgyBenchDownloadResumeEngine in User RAM)                     │
  │    ├── Option 1: Direct Original Resume Blob                                              │
  │    ├── Option 5: In-House Evaluation Report Dossier (pdfMake)                             │
  │    ├── Option 41/45/50: Vector Company Branding Injection (PDF-Lib)                       │
  │    ├── Option 44/46/47/53/54: Turbo Vector Keyword Highlighting (PDF.js + PDF-Lib)        │
  │    ├── Option 48–54: PII Redaction & Contact Masking (Canvas + jsPDF)                     │
  │    └── Option 42/43/46/47/51–54: Multi-Page Concatenation (PDF-Lib)                       │
  │    │                                                                                      │
  │    ▼                                                                                      │
  │ 5. Instant File Trigger                                                                   │
  │    • Generate Blob URL: `URL.createObjectURL(compiledBlob)`                               │
  │    • Dynamic Filename: `Jeroworks_Deepak_Raut_Branded.pdf`                                │
  │    • Trigger native browser download (`a.click()`) in < 500ms                             │
  │    • Revoke Blob URL to free browser RAM (`URL.revokeObjectURL`)                          │
  │                                                                                           │
  │ 6. Asynchronous Download Analytics & Coin Hook (Non-Blocking)                             │
  │    POST /bench-candidates/record_download ───────────────────────────────────────────────►│ Queue Worker (Horizon)
  │    (Candidate ID, Format ID, Coin Ledger)                                                 │ • Record download counter
  │    ◄── { status: 'success' } ─────────────────────────────────────────────────────────────┤ • Atomic coin deduction
```

---

## 4. The 15 Resume Formats Specification

The Bench Download Resume Center organizes all 15 enterprise formats across 5 functional clusters:

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                       BENCH DOWNLOAD RESUME FORMATS: 15-OPTION CLUSTER MATRIX                                   │
├───────┬──────┬──────────────────────────────────────────────────────────────────────┬──────────────────┬──────────────┬─────────┤
│ Clust │ Code │ Format Display Label                                                 │ Transformation   │ Coins / Cost │ Default │
├───────┼──────┼──────────────────────────────────────────────────────────────────────┼──────────────────┼──────────────┼─────────┤
│  1.a  │  1   │ Original Candidate Resume                                            │ Direct Pass-thru │ 1 Coin       │         │
│  1.b  │  41  │ Resume + Company Branding                                            │ Header Stamp     │ 2 Coins      │         │
├───────┼──────┼──────────────────────────────────────────────────────────────────────┼──────────────────┼──────────────┼─────────┤
│  2.a  │  5   │ In-House Evaluation Report (Stand-alone PDF evaluation dossier)      │ pdfMake Dossier  │ 1 Coin       │         │
├───────┼──────┼──────────────────────────────────────────────────────────────────────┼──────────────────┼──────────────┼─────────┤
│  3.a  │  42  │ Recruiter Package + Company Branding (Dossier + Branded Resume)      │ Dossier + Brand  │ 3 Coins      │         │
│  3.b  │  43  │ Recruiter Package (Unbranded) (Dossier + Original Resume)            │ Dossier + Merge  │ 2 Coins      │         │
├───────┼──────┼──────────────────────────────────────────────────────────────────────┼──────────────────┼──────────────┼─────────┤
│  4.a  │  44  │ Resume + Highlighted Keywords (Evaluated skills vector highlights)  │ Vector Highlight │ 2 Coins      │         │
│  4.b  │  45  │ Resume + Highlighted Keywords + Company Branding                    │ Highl + Brand    │ 3 Coins      │         │
│  4.c  │  46* │ In-House Evaluation Report + Resume + Highlighted Keywords + Branding│ Doss+Highl+Brand │ 3 Coins      │   ★     │
│  4.d  │  47  │ Recruiter Package + Highlighted Keywords (Unbranded)                 │ Doss+Highl+Merge │ 2 Coins      │         │
├───────┼──────┼──────────────────────────────────────────────────────────────────────┼──────────────────┼──────────────┼─────────┤
│  5.a  │  48  │ Hide Contact Details (Redacts phone, email, secondary mobile, links) │ Canvas Redaction │ 2 Coins      │         │
│  5.b  │  49  │ Hide Candidate Name & Contact Details (Anti-poaching blind profile)  │ Full Redaction   │ 2 Coins      │         │
│  5.c  │  50  │ Hide Contact Details + Company Branding                              │ Redact + Brand   │ 3 Coins      │         │
│  5.d  │  51  │ Hide Contact Details + In-House Evaluation Report + Company Branding │ Doss+Redact+Brand│ 3 Coins      │         │
│  5.e  │  52  │ Hide Contact Details + In-House Evaluation Report (Unbranded)        │ Doss+Redact+Merge│ 2 Coins      │         │
│  5.f  │  53  │ Full Privacy: Hide Contact + Dossier + Keywords + Company Branding   │ Highl+Red+Br+Dos │ 4 Coins      │         │
│  5.g  │  54  │ Full Privacy (Unbranded): Hide Contact + Dossier + Keywords (No Logo)│ Highl+Red+Dos    │ 3 Coins      │         │
└───────┴──────┴──────────────────────────────────────────────────────────────────────┴──────────────────┴──────────────┴─────────┘
* Format 46 (Option 4.c) is pre-selected by default as the authoritative enterprise presentation standard.
```

### 4.1 Strict Pipeline Ordering Rules
1. **The Vector Text Layer Rule:** For all options combining **Highlighting** and **Privacy Redaction** (Options 53 & 54):
   $$\text{Original Vector PDF} \xrightarrow{\text{Step 1}} \text{Highlight Vector Keywords} \xrightarrow{\text{Step 2}} \text{Rasterize \& Redact PII} \xrightarrow{\text{Step 3}} \text{Stamp Header} \xrightarrow{\text{Step 4}} \text{Merge Dossier}$$
   Highlighting must execute *before* canvas rasterization destroys the PDF text layer.
2. **Dynamic File Naming:** Output filenames must adhere to clean corporate naming conventions:
   - Format 1: `Jeroworks_{CandidateName}_Original.{ext}`
   - Format 41: `Jeroworks_{CandidateName}_Branded.pdf`
   - Format 5: `Jeroworks_{CandidateName}_Evaluation_Report.pdf`
   - Format 46: `Jeroworks_{CandidateName}_Recruiter_Package.pdf`
   - Format 49: `Jeroworks_Confidential_Blind_Profile.pdf`
   - Format 53: `Jeroworks_{CandidateName}_Full_Privacy_Package.pdf`

---

## 5. UI/UX Specification: The Bench Download Center Drawer

### 5.1 Host Container & Top Action Bar (`showdetail.blade.php`)
Replace the current small 2-option dropdown with a polished Metronic button:
* **Button Location:** Top Action Bar (`.cdr-topbar-group`).
* **Visual Style:** `.cdr-action-btn.cdr-btn-dl-center`
  - Typography: `font-family: 'Poppins', sans-serif`, `font-size: 12px`, `font-weight: 500`.
  - Icon: Clean SVG download arrow with document tray.
  - Text: `Download Resume` (with subtle caret).
  - Hover State: Soft emerald/teal background (`#f0fdf4`), border (`#86efac`), text (`#15803d`).
* **Trigger Action:** `onclick="openBenchDownloadResumeCenter({{ $benchApplication->id }})"`.

### 5.2 The Dedicated Drawer Shell (`#kt_bench_download_resume_drawer`)
* **Position:** Fixed right-side sliding drawer.
* **Dimensions:** Desktop width `min(720px, 95vw)`, tablet/mobile `100vw`.
* **Z-Index Layering:** `z-index: 10050` (higher than the host Bench Talent drawer at `1000`/`1050`, with backdrop overlay at `10045`).
* **Scrollbar Stability:** `scrollbar-gutter: stable` to eliminate layout jitter.
* **Component Architecture:**
  1. **Sticky Header:**
     - Left: Download icon badge, Title (`Download Resume Center`), Subtitle (`In-House Bench Talent`).
     - Right: Direct Download CTA Button (`Download Selected Format`) with progress spinner, and Cancel/Close (`✕`) button.
  2. **Candidate Summary Card:**
     - Displays candidate avatar/initials, full name, verified designation, city location, availability badge, and primary skills chips.
     - Live document format badge: `PDF Available` (green) or `DOCX (Auto-WASM Conversion)` (amber).
  3. **Categorized Format Selector (15 Formats):**
     - Organized into 5 expandable/sticky cluster cards with clear section headers:
       - *1. Standard Resumes*
       - *2. In-House Evaluation Report*
       - *3. Recruiter Packages*
       - *4. Highlighted Skill Resumes*
       - *5. Privacy & Anonymization Shields*
     - Each card displays radio selector, format title, explanatory description, badges (`DEFAULT`, `CONFIDENTIAL`, `POPULAR`), and estimated coin deduction.
  4. **Compilation & Progress Baseline:**
     - 3px animated progress baseline bar directly beneath the active card showing compilation phases:
       - *Phase 1 (20%): Fetching resume bytes...*
       - *Phase 2 (50%): Compiling In-House Evaluation Report...*
       - *Phase 3 (80%): Injecting company branding banner...*
       - *Phase 4 (100%): File ready! Starting download...*

---

## 6. Client-Side vs Server-Side Responsibilities

```
┌─────────────────────────────────────────────────────────────┬─────────────────────────────────────────────────────────────┐
│                    CLIENT-SIDE (User Browser RAM)           │                  SERVER-SIDE (FrankenPHP & Horizon)         │
├─────────────────────────────────────────────────────────────┼─────────────────────────────────────────────────────────────┤
│ 1. 100% Vector PDF Document Transformation & Compilation    │ 1. Multi-Tenant Authorization & Candidate Tenancy Guard     │
│ 2. Vector Keyword Highlighting (PDF.js coordinate extraction│ 2. Authoritative Location, Notice Period & RM Comments Query│
│ 3. Pre-Screen In-House Evaluation Report Dossier (pdfMake)  │ 3. Cloudflare R2 Resume Document Streaming Proxy            │
│ 4. PII Redaction & Contact Masking (Canvas + jsPDF)         │ 4. Fast SWR Version Fingerprint Computation                 │
│ 5. Company Branding Header Injection (PDF-Lib)              │ 5. Asynchronous Coin Deductions & Ledger Updates (Event 44) │
│ 6. Direct Client-Side Download Trigger (URL.createObjectURL)│ 6. Download Analytics Logging (Queue Worker)                │
│ 7. Browser RAM Store & Local State (0ms Drawer Hydration)   │ 7. Zero PDF Generation Server Workload (0% CPU / 0 MB RAM)  │
└─────────────────────────────────────────────────────────────┴─────────────────────────────────────────────────────────────┘
```

---

## 7. Complete File Architecture & Implementation Breakdown

```
JWMaincode28June2026/
├── app/
│   └── Http/
│       └── Controllers/
│           └── ServiceCompany/
│               └── ServiceCompanyBenchCandidatesController.php  <-- Added download_center() & record_download()
├── resources/
│   └── views/
│       └── layouts/
│           └── service_company_layout.blade.php                 <-- Host container #kt_bench_download_resume_drawer & global trigger
│       └── service_company/
│           └── bench-candidates/
│               ├── index.blade.php                              <-- Table row download trigger hook
│               ├── showdetail.blade.php                         <-- Upgraded Download Resume action button in topbar
│               └── partials/
│                   ├── _bench_download_resume_engine.blade.php  <-- NEW: Dedicated Engine 1-B (window.AgyBenchDownloadResumeEngine)
│                   └── download_resume_drawer.blade.php         <-- NEW: Isolated Download Resume Center Drawer Blade
└── routes/
    └── service.php                                              <-- Registered download_center and record_download routes
```

### 7.1 Component 1: `_bench_download_resume_engine.blade.php` (Dedicated Engine)
* **Namespace:** `window.AgyBenchDownloadResumeEngine`
* **Dependencies:** Asynchronously loaded via multi-CDN/local fallback loader (`rsTryList`):
  - `PDFLib` (`pdf-lib.min.js`)
  - `pdfjsLib` (`pdf.min.js`)
  - `pdfMake` (`pdfmake.min.js` + `vfs_fonts.js`)
  - `jspdf` (`jspdf.umd.min.js`)
  - `AgyForwardResumeEngine.js` (for client-side WASM DOCX to PDF conversion)
* **Core Public Methods:**
  - `processDownloadOption(candidate, optId, companyData, onProgress)`: Compiles the target PDF in RAM and returns `{ blob, filename }`.
  - `downloadCandidateFormat(candidate, optId, companyData)`: Single-call helper that executes compilation, creates an object URL, triggers browser download, and revokes memory.

### 7.2 Component 2: `download_resume_drawer.blade.php` (Drawer UI)
* Self-contained Blade partial rendered inside `#bench_download_resume_body_content`.
* Pre-loaded with candidate JSON metadata, authoritative RM comments from `bench_candidate_rm_comments`, evaluated skills matrix from `bench_candidate_skill_experience`, and company logo base64 Data URI.
* Includes instant format card selection, live preview trigger, and direct download dispatch.

### 7.3 Component 3: Controller Endpoints (`ServiceCompanyBenchCandidatesController.php`)
* **Endpoint 1: `POST /bench-candidates/download_center` (`download_center`)**
  - Input: `{ candidate_id: int, fingerprint: string }`
  - Responsibilities:
    1. Tenant validation: Ensures candidate belongs to `auth()->user()->company_id` or authorized third-party partner.
    2. Resolves authoritative city-only location, notice period, RM comments, and skills matrix.
    3. Computes SWR fingerprint. If client fingerprint matches, returns `{ status: 'unmodified' }` in < 5ms.
    4. Renders `service_company.bench-candidates.partials.download_resume_drawer`.
* **Endpoint 2: `POST /bench-candidates/record_download` (`record_download`)**
  - Input: `{ candidate_id: int, format_id: int }`
  - Responsibilities:
    1. Validates company coins (Event 44 / Permission check).
    2. Dispatches asynchronous queue job to record analytics and deduct coins without blocking client download.

---

## 8. Detailed 6-Phase Implementation Plan

### Phase 1: Engine Construction (`_bench_download_resume_engine.blade.php`)
- [ ] Create `resources/views/service_company/bench-candidates/partials/_bench_download_resume_engine.blade.php`.
- [ ] Implement self-contained AMD/RequireJS isolation guard (`window.define.amd = false`).
- [ ] Implement multi-CDN dynamic script loader for `pdf-lib`, `pdfjs-dist`, `pdfmake`, and `jspdf`.
- [ ] Implement `normalizeBenchCandidateData` prioritizing `candidate.skills_evaluated` from `bench_candidate_skill_experience`.
- [ ] Implement `buildBenchDefinition` for the In-House Evaluation Report dossier with gold star ratings and sanitized RM comments.
- [ ] Implement vector keyword highlighting (`buildTurboVectorHighlightedResume`).
- [ ] Implement PII contact redaction with un-transformed coordinate bounding boxes (`renderAnonymizedPdfBlob`).
- [ ] Implement company branding header injection (`addCompanyBrandingHeader`).
- [ ] Implement multi-page document concatenation (`mergeDossierAndResume`).
- [ ] Expose public API: `window.AgyBenchDownloadResumeEngine`.

### Phase 2: Controller & Route Setup
- [ ] Register routes in `routes/service.php`:
  ```php
  Route::post('bench-candidates/download_center', [ServiceCompanyBenchCandidatesController::class, 'download_center'])
      ->name('bench-candidates.download_center');
  Route::post('bench-candidates/record_download', [ServiceCompanyBenchCandidatesController::class, 'record_download'])
      ->name('bench-candidates.record_download');
  ```
- [ ] Implement `download_center(Request $request)` in `ServiceCompanyBenchCandidatesController`:
  - Multi-tenant candidate ownership verification.
  - Authoritative location normalization (city-only).
  - RM comments fetch from `bench_candidate_rm_comments`.
  - Skills matrix fetch from `bench_candidate_skill_experience`.
  - Authoritative SWR fingerprint calculation.
  - Return `{ status: 'unmodified' }` or rendered Blade drawer HTML.
- [ ] Implement `record_download(Request $request)` in `ServiceCompanyBenchCandidatesController`:
  - Atomic coin deduction check (Event 44).
  - Queue job dispatch for download audit logging.

### Phase 3: Drawer UI Component (`download_resume_drawer.blade.php`)
- [ ] Create `resources/views/service_company/bench-candidates/partials/download_resume_drawer.blade.php`.
- [ ] Build sticky header with download title, subtitle, close button, and primary download CTA.
- [ ] Build candidate summary profile card with live status and skills preview.
- [ ] Build 5 format clusters (15 formats total) with custom radio cards, badges, and cost indicators.
- [ ] Pre-select Option 4.c (Format 46) by default.
- [ ] Build 3px compilation baseline progress bar.
- [ ] Implement download button click handler:
  - Disables button and displays phase spinner (`Compiling...`).
  - Calls `AgyBenchDownloadResumeEngine.processDownloadOption()`.
  - Triggers native browser download via `URL.createObjectURL(blob)`.
  - Asynchronously pings `record_download` endpoint.
  - Restores button state and displays success toast.

### Phase 4: Layout & Host Integration
- [ ] Update `resources/views/layouts/service_company_layout.blade.php`:
  - Add drawer shell container `#kt_bench_download_resume_drawer` with body container `#bench_download_resume_body_content`.
  - Implement `BenchDownloadResumeRamStore` for 0ms in-memory drawer hydration.
  - Implement `window.openBenchDownloadResumeCenter(candidateId)` with SWR background revalidation.
- [ ] Update `resources/views/service_company/bench-candidates/showdetail.blade.php`:
  - Replace the current `#hdrDlWrapBench` 2-option dropdown with the new `.cdr-btn-dl-center` action button.
  - Wire button to `openBenchDownloadResumeCenter({{ $benchApplication->id }})`.
  - Remove obsolete DOM-clicking script `hdrDownloadWithLogoBench()`.

### Phase 5: Concurrency, Caching & Performance Verification
- [ ] Verify that opening the drawer uses **0% server PDF CPU/RAM**.
- [ ] Verify that opening a previously viewed candidate resolves in **0ms** from `BenchDownloadResumeRamStore`.
- [ ] Verify that background revalidation sends `fingerprint` and receives `{ status: 'unmodified' }` in under 5ms.
- [ ] Verify that changing candidate status or RM comments immediately invalidates `BenchDownloadResumeRamStore`.
- [ ] Verify that Cloudflare R2 resume bytes are cached in browser RAM (`rsDownloadBytesCache`) so format switches require zero network re-fetches.

### Phase 6: Multi-Dimensional Quality Assurance
- [ ] **PHP Syntax Check:** `php -l` on all modified controllers and routes.
- [ ] **Format Validation:** Verify that all 15 formats download with correct vector fonts, layout margins, and clean file names.
- [ ] **Dossier Data Accuracy:** Verify that the In-House Evaluation Report contains exact RM comments from `bench_candidate_rm_comments` and ratings from `bench_candidate_skill_experience`.
- [ ] **Privacy Redaction Verification:** Confirm that phone, email, and private URLs are permanently flattened on canvas in Options 48–54, with zero selectable text in redacted areas.
- [ ] **DOCX Source Resilience:** Verify that candidates with `.docx` resumes convert cleanly via client WASM without server timeout.
- [ ] **Responsive Design:** Confirm drawer fits seamlessly on 15.6-inch screens (1366×768 & 1920×1080) with full internal scroll isolation.

---

## 9. Verification & Code Alignment Checklist

| Verification Requirement | Target Condition | Status |
|---|---|:---:|
| **Rule 15 Isolation (WASM)** | `AgyBenchDownloadResumeEngine` has zero dependencies on Engine 2 or Engine 1 | Target: 100% Isolated |
| **Rule 17 Isolation (Blade)** | `#kt_bench_download_resume_drawer` namespaced strictly to bench candidates | Target: 100% Isolated |
| **Rule 18 Isolation (Freeze)** | `resources/views/product_company/` and `staffing_company/` completely untouched | Target: 100% Frozen |
| **Rule 3 & Concurrency Target**| 100% of PDF compiling executes in User RAM; 0% server read strain | Target: 10K Ready |
| **Rule 5 Zero Stale Data** | Authoritative SWR fingerprinting syncs RM comments and skills within 5ms | Target: Zero Stale Data |
| **Option Coverage** | All 15 formats (Options 1, 5, 41–54) available for instant direct download | Target: Complete |

---

> **Summary:**  
> This specification establishes a unified, world-class standard for downloading In-House Bench Talent resumes. By combining complete Rule 15 WASM engine isolation, instant client-first compilation, authoritative bench dossier data synchronization, and a high-density Metronic drawer, JERO recruiters can export candidate presentations with zero server strain and zero latency.
