# Bench Candidate Forward Resume: Architectural Specification & Implementation Plan

> **Authoritative Specification:** `docs/BENCH_CANDIDATE_FORWARD_RESUME_IMPLEMENTATION_PLAN.md`  
> **Status:** UPDATED & CODE-ALIGNED (READY FOR PRODUCTION)  
> **Target Module:** Service Company — Bench Candidates (`resources/views/service_company/bench-candidates/`)  
> **Core Mandate:** Add an isolated "Forward Resume" action button to the Bench Talent Drawer header (`showdetail.blade.php`), opening an isolated Forward Resume drawer cloned from CV Tracker's candidate selection technology, fully compliant with Rule 17 (Complete Page/Blade Isolation) and Rule 15 (OpenDoc WASM Engine Isolation).
> **Special Mandate (Prescreen Dossier):** Authoritatively use **Relationship Manager Comments** from `bench_candidate_rm_comments` and **Talent Skills & Expertise** from `bench_candidate_skill_experience`.

---

## 1. Executive Summary & Objective

In JERO's B2B talent marketplace, recruiters viewing a candidate profile in the Bench Talent Drawer frequently need to forward the candidate's resume and comprehensive profile dossier directly to hiring clients or internal stakeholders via email.

Currently, the Bench Talent Drawer (`showdetail.blade.php`) provides:
1. **Edit Profile** (blue action button)
2. **Change Status** (amber action button)
3. **Add Relationship Manager** (purple action button)
4. **Download Resume** (dropdown with Original & Logo options)
5. **Forward Resume** (emerald action button — newly integrated!)

This specification details the architecture, code alignment, and implementation of **CV Tracker's enterprise Forward Resume technology** into the Bench Candidates module with **100% blade, script, and backend isolation**.

```
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                       BENCH TALENT DRAWER TOP ACTION BAR (showdetail.blade.php)             │
├───────────────┬─────────────────┬──────────────────────────┬──────────────────┬─────────────┤
│ ✏ Edit Profile │ 🔄 Change Status │ 👥 Add Relationship Mgr │ ✉ Forward Resume │ 📥 Download │
└───────────────┴─────────────────┴──────────────────────────┴──────────────────┴─────────────┘
                                                                       │
                                                         (Click triggers isolated AJAX)
                                                                       ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                    ISOLATED BENCH FORWARD RESUME DRAWER (z-index: 10050)                    │
├─────────────────────────────────────────────────────────────────────────────────────────────┤
│ • Header: Title, Subtitle, [Cancel], [Send Email (with Progress Spinner)]                   │
│ • Sender / Reply-To: Authenticated company recruiter email                                  │
│ • To Email & CC Email: Tag/Chip input + "+ Add Email" slide-out recipient manager           │
│ • Template Picker: Loads Elasticsearch email templates (replaces Subject & Body)            │
│ • Subject Line: Input with mandatory validation                                             │
│ • Resume Format Selector: 15 formats (Original, Branded, Pre-Screened Report, Packages)     │
│ • Candidate Roster Card: Live profile badge, resume presence check, dynamic filename        │
│ • Compilation Engine: Client-first WASM compilation in User RAM with 0% server read strain  │
│ • Pre-Screen Dossier: Relationship Manager Comments + Talent Skills & Expertise Matrix      │
│ • Message Body: CKEditor rich text with recipient salutation merge tags ([HR_NAME])         │
└─────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Plan vs Code Review: Key Alignments & Upgrades

| Requirement / Component | Initial Plan / CV Tracker Origin | Actual Code Implementation (Upgraded) | Status |
|-------------------------|----------------------------------|---------------------------------------|:------:|
| **Engine Isolation** | Engine 2 shared with CV Tracker | Dedicated isolated engine `AgyBenchForwardResumeEngine` in `partials/_bench_forward_resume_engine.blade.php` | ✅ Aligned |
| **Pre-Screen Dossier Focus** | Job Requisition fit, client company, match score | **In-House Bench Talent Dossier**: Bench readiness, availability, authoritative location, commercial terms | ✅ Aligned |
| **Dossier Comments Section** | Recruiter Notes from `job_applications.recruiter_note` | **Relationship Manager Comments**: Authoritatively fetched from `bench_candidate_rm_comments` table (with fallback to recruiter notes). Stripped of raw HTML for pdfMake text integrity. | ✅ **UPDATED** |
| **Dossier Skills Matrix** | Generic skills extracted from resume text | **Talent Skills & Expertise**: Real skill matrix from `bench_candidate_skill_experience` with calculated years, months, and 1-5 gold star rating badges. Display headers: `SKILL`, `EXPERIENCE`, `RATINGS`. | ✅ **UPDATED** |
| **Resume Bytes Fetching** | Hardcoded download URL | **Multi-Source Resilient Fetcher**: Tries `cand.resume_url`, public direct uploads, and dedicated streaming endpoint `bench-candidates.download` | ✅ **UPGRADED** |
| **Resume Download Route** | Missing controller method for `bench-candidates.download` | Implemented `download($id)` in `ServiceCompanyBenchCandidatesController` with tenant validation & disk streaming | ✅ **UPGRADED** |
| **Default Message Body** | Generic template | Pre-populated with candidate summary, **Talent Skills & Expertise**, and **Relationship Manager Comments** | ✅ **UPDATED** |

---

## 3. Non-Negotiable Architectural Invariants

### 3.1 Rule 17: Complete Page/Blade Isolation
* **Zero Cross-Module Couplings:** All DOM IDs, CSS classes, JavaScript handlers, and endpoints are strictly namespaced to `bench-candidates`.
* **No Shared Stateful JS:** Bench Forward Resume never relies on CV Tracker DOM containers (`#kt_test_fwd_resume_drawer`, `#application_test_fwd_resume_body_content`, `#test_form1`, etc.).
* **Dedicated Drawer Shell:** `#kt_bench_fwd_resume_drawer` with body `#bench_fwd_resume_body_content`.

### 3.2 Rule 15: OpenDoc WASM Engine Isolation
* **Tri-Engine Independence:** Forward Resume uses **Engine 2** (`window.AgyBenchForwardResumeEngine`). It never imports or cross-pollinates with Engine 1 (`AgyResumeRenderer.js`) or Engine 3 (`AgyEditorWasmEngine.js`).
* **Client-First Execution:** Document rendering, high-resolution branding headers, contact masking, and report compilation execute **100% client-side in browser RAM** via WebAssembly (cooperative scheduling).

### 3.3 Universal CQRS & Tenancy Invariant
* **Elasticsearch for Reads:** Email templates are queried from `ELASTIC_EMAIL_TEMPLATE` index, with resilient fallback to `th_email_template` DB table if ES cluster is offline.
* **Tenant Scoping:** Candidate authorization is strictly validated on backend against `auth()->user()->company_id` and `thirdparty_company_id`.
* **Asynchronous Queue Dispatch:** Mail forwarding is queued via `QueueCandidatesMailAndResumeForwardMailService::dispatch` on the `emails` connection, preventing any synchronous request timeouts or worker blockages.

---

## 4. Pre-Screen Report Dossier Specifications

### 4.1 Relationship Manager Comments
* **Source of Truth:** MySQL table `bench_candidate_rm_comments` (`candidate_id`, `company_id`, `relationship_manager_id`, `comments`).
* **Fallback Hierarchy:** If `bench_candidate_rm_comments` is empty, check `job_applications.recruiter_note`, then `job_applications.comment`. If none exist, display `"No Relationship Manager comments added yet."`.
* **Text Formatting:** Rich-text tags from CKEditor are sanitized and stripped (`<p>`, `<strong>`, `<br>` converted into clean whitespace and line breaks) before being passed to pdfMake, ensuring clean vector typography without parsing errors.
* **Visual Display:** Section Title: `RELATIONSHIP MANAGER COMMENTS` with 13.5px bold title, 1.4 line height, and blue accent bar.

### 4.2 Talent Skills & Expertise
* **Source of Truth:** MySQL table `bench_candidate_skill_experience` (`candidate_id`, `skill_key`, `year`, `month`, `total_months`, `rating`).
* **Calculation:** Experience string derived from `total_months` (years = `intdiv(months, 12)`, remaining months = `months % 12`), with fallback to `year` and `month` columns.
* **Rating Display:** Gold star icons (`★`) for active rating (1–5) and muted star icons (`☆`) for unrated stars, followed by text `X / 5`.
* **Table Column Headers:**
  - `SKILL` (left-aligned, bold)
  - `EXPERIENCE` (center-aligned, e.g. "4 Years 6 Months")
  - `RATINGS` (star icons + numeric rating)
* **Capacity:** Displays up to 10–12 curated evaluated skills, sorted by highest rating first. If `bench_candidate_skill_experience` has no entries, dynamically falls back to comma-separated tags in `job_applications.skills`.

---

## 5. UI/UX Specification

### 5.1 Top Action Bar Integration (`showdetail.blade.php`)
Located within `.cdr-topbar .cdr-topbar-group`:
* **Button Style:** `.cdr-action-btn.cdr-btn-fwd`
* **Typography:** `font-family: 'Poppins', sans-serif`, `font-size: 12px`, `font-weight: 500`.
* **Palette:**
  - Background: `#eff6ff` (Soft Blue)
  - Border: `1px solid #bfdbfe`
  - Text Color: `#1d4ed8`
  - Hover Background: `#dbeafe`
  - Hover Border: `#93c5fd`
* **Icon:** Clean SVG paper plane (`<svg viewBox="0 0 24 24" ...><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>`).
* **Tooltip:** `Forward Resume via Email`.

### 5.2 Bench Forward Resume Drawer (`#kt_bench_fwd_resume_drawer`)
* **Position:** Fixed right-side drawer, width `80%` on desktop (`min(1150px, 95vw)`), responsive down to `100vw` on mobile.
* **Z-Index Layering:** `z-index: 10050` (higher than the host Bench Talent Drawer at `z-index: 1000` / `1050`), ensuring smooth overlay and interaction without closing or unmounting the host drawer.
* **Scrollbar Stability:** `scrollbar-gutter: stable` to eliminate any width jitter or layout shifting during tab switches or loading.

### 5.3 Recipient Slide-Out Drawer (`#kt_bench_recipient_drawer`)
* Sub-drawer sliding in from the right (`width: 480px`, `z-index: 10060`).
* Displays two categorized recipient rosters:
  1. **Internal Team:** Active team members and recruiters from the tenant company (`users` joined with `roles`).
  2. **Client Contacts:** Client account managers, hiring managers, and HR stakeholders (`client_contact_info`).
* Clicking "+ Add" appends the contact directly to To or CC as an interactive tag chip; clicking "Added" removes it.
* Supports manual email address typing with instant format validation.

### 5.4 Resume Format Selector (15 Enterprise Formats)
Fully integrated dropdown supporting all 5 format clusters:
1. **Standard Options:**
   - Option 1: Original Candidate Resume
   - Option 41: Resume + Company Branding
2. **Pre-Screened Report:**
   - Option 5: Pre-Screened Report PDF Dossier
3. **Recruiter Packages:**
   - Option 42: Pre-Screened Report + Branded Resume
   - Option 43: Pre-Screened Report + Unbranded Resume
4. **Highlighted Skills:**
   - Option 44: Resume + Highlighted Keywords
   - Option 45: Resume + Highlighted Keywords + Company Branding
   - Option 46: Recruiter Package + Highlighted Keywords + Company Branding
   - Option 47: Recruiter Package + Highlighted Keywords (Unbranded)
5. **Privacy & Anonymization (Anti-Poaching Shields):**
   - Option 48: Hide Contact Details
   - Option 49: Hide Candidate Name & Contact Details
   - Option 50: Hide Contact Details + Company Branding
   - Option 51: Hide Contact Details + Pre-Screened Report (Branded)
   - Option 52: Hide Contact Details + Pre-Screened Report (Unbranded)
   - Option 53: Hide Contact Details + Pre-Screened Report + Highlighted Keywords

---

## 6. Backend & API Endpoints Architecture

### 6.1 Route Declarations (`routes/service.php`)
```php
// Isolated Bench Candidate Forward Resume & Stream Endpoints
Route::get('bench-candidates/download/{id}', [ServiceCompanyBenchCandidatesController::class, 'download'])
    ->name('bench-candidates.download');
Route::post('bench-candidates/fwd_resume', [ServiceCompanyBenchCandidatesController::class, 'fwd_resume'])
    ->name('bench-candidates.fwd_resume');
Route::post('bench-candidates/fwd_resume_send', [ServiceCompanyBenchCandidatesController::class, 'fwd_resume_send'])
    ->name('bench-candidates.fwd_resume_send');
```

### 6.2 Endpoint 1: `fwd_resume(Request $request)`
* **HTTP Method:** `POST`
* **Payload:** `{ candidate_id: int }`
* **Responsibilities:**
  1. Multi-tenant ownership check: Candidate must belong to authenticated company or third-party partner.
  2. Candidate profile resolution: Resolves authoritative location strings (`city_name`, `state_name`, `country_name`), experience string, notice period, and resume URLs.
  3. **Relationship Manager Comments:** Fetches comments from `bench_candidate_rm_comments` table.
  4. **Talent Skills & Expertise:** Queries `bench_candidate_skill_experience`, orders by rating descending, and formats years/months for each skill.
  5. Template resolution: Queries active email templates (`temptype = 1`) via Elasticsearch `ELASTIC_EMAIL_TEMPLATE` with fallback to `th_email_template`.
  6. Recipient preparation: Fetches internal company active users and associated client contact persons.
  7. Blade compilation: Renders `service_company.bench-candidates.partials.forward_resume_drawer`.
* **Response:** `{ status: 'success', view: '<rendered_html>' }`.

### 6.3 Endpoint 2: `fwd_resume_send(Request $request)`
* **HTTP Method:** `POST`
* **Payload:** `FormData` containing:
  - `to_email`: comma-separated recipient emails
  - `cc_email`: comma-separated CC emails
  - `from_email`: authenticated sender email
  - `subject`: email subject line
  - `attached_cv_format`: integer (1, 5, 41..53)
  - `editor1`: CKEditor HTML body
  - `arr`: array with candidate ID `[candId]`
  - `custom_attachments`: uploaded compiled PDF files (from client WASM engine)
* **Responsibilities:**
  1. Strict input validation: Required To email, valid email syntax, required Subject, non-blank body, valid format.
  2. Candidate tenancy re-verification.
  3. Attachment resolution:
     - If Option != 1: Validates that client-compiled PDF was delivered via FormData or base64 map.
     - If Option == 1: Resolves candidate's original resume document path from local storage or cloud storage.
  4. Coin & Permission Check: Verifies company coin balance and email permission (Event ID 43 / Permission 13).
  5. Mail Queue Dispatch:
     ```php
     QueueCandidatesMailAndResumeForwardMailService::dispatch(
         $mailto,
         $subject,
         'email.resume-forward.forward-resume',
         (object) $emailPayload,
         $attachments,
         $fromAddress,
         $senderName,
         $ccList,
         $companyId
     )->onConnection('emails');
     ```
* **Response:** `{ status: 'success', message: 'Candidate resume forwarded successfully via email!' }`.

### 6.4 Endpoint 3: `download($id)`
* **HTTP Method:** `GET`
* **Route:** `/service_company/bench-candidates/download/{id}`
* **Responsibilities:**
  1. Validates candidate tenancy (`company_id` or `thirdparty_company_id`).
  2. Resolves resume hashname from `job_applications` or `documents` table.
  3. Streams file binary with `Content-Type: application/pdf` directly to the client WASM engine.

---

## 7. File & Component Breakdown

| File Path | Action | Description |
|-----------|--------|-------------|
| `docs/BENCH_CANDIDATE_FORWARD_RESUME_IMPLEMENTATION_PLAN.md` | Update | Authoritative blueprint and plan document. |
| `resources/views/service_company/bench-candidates/partials/_bench_forward_resume_engine.blade.php` | Update | Isolated Engine 2 instance with Relationship Manager Comments, Talent Skills & Expertise, and multi-source resume byte fetching. |
| `resources/views/service_company/bench-candidates/partials/forward_resume_drawer.blade.php` | Update | Drawer UI with recipient picker, 15 resume formats, WASM progress bar, and updated message template. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` | Update | Added `download($id)`, updated `fwd_resume()` with RM comments & full skill matrix, and `fwd_resume_send()`. |
| `resources/views/service_company/bench-candidates/showdetail.blade.php` | Update | Header "Forward Resume" action button (`.cdr-btn-fwd`) triggering drawer. |
| `resources/views/layouts/service_company_layout.blade.php` | Update | Host container `#kt_bench_fwd_resume_drawer` and global trigger function. |
| `routes/service.php` | Verified | Routes registered for `bench-candidates.download`, `fwd_resume`, and `fwd_resume_send`. |

---

## 8. Verification & Quality Assurance Results

1. **PHP Syntax Integrity:**
   - `php -l routes/service.php` → Passed (0 errors).
   - `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` → Passed (0 errors).
2. **Dossier Data Verification:**
   - Pre-screen report header displays `IN-HOUSE BENCH TALENT DOSSIER`.
   - Section 1 displays `PROFESSIONAL SUMMARY` & `RELATIONSHIP MANAGER COMMENTS`.
   - Section 2 displays `TALENT SKILLS & EXPERTISE` with columns `SKILL`, `EXPERIENCE`, `RATINGS`.
   - Availability reflects bench notice period ("Immediate" / "Available DD MMM YYYY").
   - Authoritative location reflects resolved strings (`City, State, Country`).
3. **Module Isolation Verification:**
   - CV Tracker views, controllers, and engines remain 100% untouched.
   - App Tracker views, controllers, and engines remain 100% untouched.
   - Product Company and Staffing Company directories are 100% untouched (Rule 18 compliant).
