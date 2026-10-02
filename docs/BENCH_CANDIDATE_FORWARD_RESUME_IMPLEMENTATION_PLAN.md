# Bench Candidate Forward Resume: Architectural Specification & Implementation Plan

> **Authoritative Specification:** `docs/BENCH_CANDIDATE_FORWARD_RESUME_IMPLEMENTATION_PLAN.md`  
> **Status:** APPROVED FOR IMPLEMENTATION  
> **Target Module:** Service Company — Bench Candidates (`resources/views/service_company/bench-candidates/`)  
> **Core Mandate:** Add a "Forward Resume" action button to the Bench Talent Drawer header (`showdetail.blade.php`), opening an isolated Forward Resume drawer cloned from CV Tracker's candidate selection technology, fully compliant with Rule 17 (Complete Page/Blade Isolation) and Rule 15 (OpenDoc WASM Engine Isolation).

---

## 1. Executive Summary & Objective

In JERO's B2B talent marketplace, recruiters viewing a candidate profile in the Bench Talent Drawer frequently need to forward the candidate's resume and comprehensive profile dossier directly to hiring clients or internal stakeholders via email.

Currently, the Bench Talent Drawer (`showdetail.blade.php`) provides:
1. **Edit Profile** (blue action button)
2. **Change Status** (amber action button)
3. **Add Relationship Manager** (purple action button)
4. **Download Resume** (dropdown with Original & Logo options)

Recruiters are forced to download the resume locally, open their external email client, compose an email, attach the file, and send it manually.

This specification details the architecture and implementation to bring **CV Tracker's enterprise Forward Resume technology** directly into the Bench Candidates module with **100% blade, script, and backend isolation**.

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
│ • Message Body: CKEditor rich text with recipient salutation merge tags ([HR_NAME])         │
└─────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Non-Negotiable Architectural Invariants

### 2.1 Rule 17: Complete Page/Blade Isolation
* **Zero Cross-Module Couplings:** All DOM IDs, CSS classes, JavaScript handlers, and endpoints must be strictly namespaced to `bench-candidates`.
* **No Shared Stateful JS:** Bench Forward Resume must never rely on CV Tracker DOM containers (`#kt_test_fwd_resume_drawer`, `#application_test_fwd_resume_body_content`, `#test_form1`, etc.).
* **Dedicated Drawer Shell:** `#kt_bench_fwd_resume_drawer` with body `#bench_fwd_resume_body_content`.

### 2.2 Rule 15: OpenDoc WASM Engine Isolation
* **Tri-Engine Independence:** Forward Resume uses **Engine 2** (`public/js/opendoc/AgyForwardResumeEngine.js`). It must never import or cross-pollinate with Engine 1 (`AgyResumeRenderer.js`) or Engine 3 (`AgyEditorWasmEngine.js`).
* **Client-First Execution:** Document rendering, high-resolution branding headers, contact masking, and report compilation execute **100% client-side in browser RAM** via WebAssembly (yieldToMain cooperative scheduling).

### 2.3 Rule 1 & Rule 2: Universal CQRS & Tenancy Invariant
* **Elasticsearch for Reads:** Email templates are queried from `ELASTIC_EMAIL_TEMPLATE` index, with resilient fallback to DB if ES cluster is offline.
* **Tenant Scoping:** Candidate authorization is strictly validated on backend against `auth()->user()->company_id` and `thirdparty_company_id`.
* **Asynchronous Queue Dispatch:** Mail forwarding is queued via `QueueCandidatesMailAndResumeForwardMailService::dispatch` on the `emails` connection, preventing any synchronous request timeouts or worker blockages.

---

## 3. UI/UX Specification

### 3.1 Top Action Bar Integration (`showdetail.blade.php`)
Located within `.cdr-topbar .cdr-topbar-group`:
* **Button Style:** `.cdr-action-btn.cdr-btn-fwd`
* **Typography:** `font-family: 'Poppins', sans-serif`, `font-size: 12px`, `font-weight: 500`.
* **Palette:**
  * Background: `#ECFDF5` (Soft Emerald)
  * Border: `1px solid #A7F3D0`
  * Text Color: `#059669`
  * Hover Background: `#D1FAE5`
  * Hover Border: `#6EE7B7`
* **Icon:** Clean SVG paper plane (`<svg viewBox="0 0 24 24" ...><path d="M22 2L11 13"/><path d="M22 2L15 22L11 13L2 9L22 2Z"/></svg>`).
* **Tooltip:** `Forward Resume via Email`.

### 3.2 Bench Forward Resume Drawer (`#kt_bench_fwd_resume_drawer`)
* **Position:** Fixed right-side drawer, width `80%` on desktop (`min(1150px, 95vw)`), responsive down to `100vw` on mobile.
* **Z-Index Layering:** `z-index: 10050` (higher than the host Bench Talent Drawer at `z-index: 1000` / `1050`), ensuring smooth overlay and interaction without closing or unmounting the host drawer.
* **Scrollbar Stability:** `scrollbar-gutter: stable` to eliminate any width jitter or layout shifting during tab switches or loading.

### 3.3 Recipient Slide-Out Drawer (`#kt_bench_recipient_drawer`)
* Sub-drawer sliding in from the right (`width: 480px`, `z-index: 10060`).
* Displays two categorized recipient rosters:
  1. **Internal Team:** Active team members and recruiters from the tenant company (`users` joined with `roles`).
  2. **Client Contacts:** Client account managers, hiring managers, and HR stakeholders (`client_contact_info`).
* Clicking "+ Add" appends the contact directly to To or CC as an interactive tag chip; clicking "Added" removes it.
* Supports manual email address typing with instant format validation.

### 3.4 Resume Format Selector (15 Enterprise Formats)
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

## 4. Backend & API Endpoints Architecture

### 4.1 Route Declarations (`routes/service.php`)
```php
// Isolated Bench Candidate Forward Resume Endpoints
Route::post('bench-candidates/fwd_resume', [ServiceCompanyBenchCandidatesController::class, 'fwd_resume'])
    ->name('bench-candidates.fwd_resume');
Route::post('bench-candidates/fwd_resume_send', [ServiceCompanyBenchCandidatesController::class, 'fwd_resume_send'])
    ->name('bench-candidates.fwd_resume_send');
```

### 4.2 Endpoint 1: `fwd_resume(Request $request)`
* **HTTP Method:** `POST`
* **Payload:** `{ candidate_id: int }`
* **Responsibilities:**
  1. Multi-tenant ownership check: Candidate must belong to authenticated company or third-party partner.
  2. Candidate profile resolution: Resolves human-readable location names (city, state, country), experience string, notice period, skill ratings from `bench_candidate_skill_experience`, and resume file availability.
  3. Template resolution: Queries active email templates (`temptype = 1`) via Elasticsearch `ELASTIC_EMAIL_TEMPLATE` with fallback to `th_email_template`.
  4. Recipient preparation: Fetches internal company active users and associated client contact persons.
  5. Blade compilation: Renders `service_company.bench-candidates.partials.forward_resume_drawer`.
* **Response:** `{ status: 'success', view: '<rendered_html>' }`.

### 4.3 Endpoint 2: `fwd_resume_send(Request $request)`
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
     - If Option != 1: Validates that client-compiled PDF was delivered via FormData/base64.
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
* **Response:** `{ status: 'success', message: 'Candidate resume forwarded successfully!' }`.

---

## 5. File & Component Breakdown

| File Path | Action | Description |
|-----------|--------|-------------|
| `docs/BENCH_CANDIDATE_FORWARD_RESUME_IMPLEMENTATION_PLAN.md` | Create | This architectural blueprint & implementation specification. |
| `resources/views/service_company/bench-candidates/showdetail.blade.php` | Modify | Add "Forward Resume" button with SVG icon in `.cdr-topbar .cdr-topbar-group` and click event. |
| `resources/views/service_company/bench-candidates/index.blade.php` | Modify | Add `#kt_bench_fwd_resume_drawer` container and client-side drawer trigger function. |
| `resources/views/service_company/bench-candidates/partials/forward_resume_drawer.blade.php` | Create | Isolated Forward Resume form, recipient sub-drawer, format selector, and WASM compilation engine. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` | Modify | Implement isolated `fwd_resume` and `fwd_resume_send` controller methods. |
| `routes/service.php` | Modify | Register routes `bench-candidates.fwd_resume` and `bench-candidates.fwd_resume_send`. |

---

## 6. Detailed Implementation Steps

### Phase 1: Planning & Architectural Document (Complete)
- Author comprehensive specification detailing isolation, workflows, data integrity, and error boundaries.

### Phase 2: Route Registration & Controller Implementation
- Register routes in `routes/service.php` within the Service Company authenticated middleware group.
- Implement `fwd_resume` in `ServiceCompanyBenchCandidatesController.php` with:
  - Tenancy check (`company_id` / `thirdparty_company_id`).
  - Authoritative location hierarchy resolution (never raw numeric IDs).
  - Notice period, skills, experience, and resume URL parsing.
  - Elasticsearch email template loading with resilient DB fallback.
  - Internal users and client contacts loading.
- Implement `fwd_resume_send` with:
  - Validation of recipient emails, subject, and body.
  - Client-compiled attachment processing.
  - Option 1 original resume attachment resolution.
  - Coin deduction check and queued email dispatching.

### Phase 3: Create Isolated Partial Blade View
- Create `resources/views/service_company/bench-candidates/partials/forward_resume_drawer.blade.php`.
- Include Forward Resume WASM Engine (`service_company.cvtracker._forward_resume_engine`).
- Replicate modern Metronic form layout (`.mm-wrap`, `.mm-header`, `.mm-row`).
- Embed recipient slide-out manager (`#kt_bench_recipient_drawer`) for team and client contacts.
- Embed CKEditor initialization scoped to `#bench_fwd_editor1`.
- Wire client-side WASM compilation loop for options 5 and 41–53 with live percentage progress bar.

### Phase 4: Wire Bench Talent Drawer Header & Host Container
- In `resources/views/service_company/bench-candidates/index.blade.php`:
  - Append `#kt_bench_fwd_resume_drawer` container markup at bottom of page.
  - Add `openBenchForwardResume(candidateId)` JavaScript helper to trigger drawer and fetch content via AJAX.
- In `resources/views/service_company/bench-candidates/showdetail.blade.php`:
  - Add "Forward Resume" button to `.cdr-topbar .cdr-topbar-group`.
  - Connect `onclick="openBenchForwardResume({{ $benchApplication->id }})"` handler.

### Phase 5: Verification & Quality Assurance
- Run `php -l` on modified PHP files to ensure zero syntax errors.
- Test drawer launch from Bench Talent Drawer:
  - Click "Forward Resume" in topbar.
  - Verify drawer slides in over host drawer without closing or flickering.
  - Verify recipient picker opens and populates team/client contacts.
  - Verify template selection populates Subject and CKEditor body.
  - Verify format dropdown reflects all 15 options.
  - Test sending with Option 1 (Original Resume) and custom format (WASM compilation).
- Verify complete isolation: Verify CV Tracker pages and endpoints remain 100% unaffected.

### Phase 6: Commit and Push
- Commit cleanly to `staging` with descriptive message following repository conventions.
- Push to `origin staging`.
