# JERO OpenDoc WASM Engine — Phase 5 Detailed Implementation Plan
## Advanced Document Intelligence, Resume Diff Comparator, Pre-Upload Sanitizer & Agency Presentation Mode

> **Document Status:** Complete Architecture & Implementation Specification  
> **Target Version:** Laravel 11 / OpenDoc WASM 2.0 / Client-Side Engine  
> **Target Audience:** Core Engineering, ATS Architects, Agency Solutions, Security & Compliance  
> **Master Reference:** [docs/OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md)  
> **Phase 3 Reference:** [docs/OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Phase 4 Reference:** [docs/OPENDOC_WASM_PHASE_4_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_4_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Phase 6 Reference:** [docs/OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Rule Adherence:** Multi-Tenancy Scoped (`company_id`), Zero LibreOffice Server Load, Metronic UI Standard, Poppins Font $\le$ 14px

---

## 1. Executive Summary & Core Objectives

Phase 5 transitions JERO from a high-speed document viewer and exporter into an **active client-side document intelligence and screening suite**. By utilizing WebAssembly to parse, inspect, and diff document abstract syntax trees (ASTs) directly inside the browser, JERO automates candidate quality control and verification while keeping server CPU consumption at **0%**.

### The 3 Core Pillars of Phase 5:
1. **Client-Side Pre-Upload Resume Sanitizer & Anti-Cheat Engine:**  
   Inspects candidate resumes in browser memory the instant a file is selected in `<input type="file">`. Detects white-on-white text cheats, micro-fonts ($< 4\text{pt}$), hidden table cells, and trailing page overflow (2.1-page resumes), prompting candidates to auto-optimize formatting *before* uploading to Cloudflare R2.
2. **Multi-Version Resume Visual Comparator (Diff Tool):**  
   Empowers recruiters reviewing re-applicants (e.g. candidate re-applying after 6–12 months) to instantly compare old vs. new resumes side-by-side or in a unified overlay with green vector additions and red strikethrough deletions.
3. **White-Label Agency Presentation Mode (Instant Vendor Rebranding):**  
   Enables staffing agencies to prepend stylized vector cover sheets, candidate scorecards, interview ratings, rate cards, and standardized headers/footers to candidate resumes in under 1 second.

> [!NOTE]
> **Dedicated Phase 6 Promotion:** The *In-Browser Dynamic Offer Letter & Consulting Contract Generator* has been promoted into its own dedicated specification. See [docs/OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md).

---

## 2. Feature 1: Pre-Upload Resume Sanitizer & Anti-Cheat Engine

### 2.1 The Problem in Modern ATS Ingestion
Job seekers and rogue staffing agencies frequently attempt to game keyword-matching ATS algorithms using deceptive tactics:
- **Invisible Text Injection:** Stuffer paragraphs rendered in white font on white backgrounds (`w:color w:val="FFFFFF"`).
- **Micro-Font Keyword Stacking:** Thousands of keywords packed into 1pt font in document headers, footers, or margins.
- **Hidden Tables & Overlaps:** Text boxes positioned behind images or opaque shapes.
- **Sloppy Page Budget Spills:** A resume that is 2 pages and 3 lines (2.1 pages), causing awkward formatting, wasted paper upon printing, and recruiter frustration.

### 2.2 Client-Side Pre-Flight Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    PRE-UPLOAD WASM SANITIZER PIPELINE                       │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. User selects resume file in <input type="file" id="resume_upload">       │
│       │                                                                     │
│       ▼                                                                     │
│ 2. FileReader reads ArrayBuffer directly into browser RAM                   │
│       │                                                                     │
│       ▼                                                                     │
│ 3. AgyWasmEngine.inspectUpload(arrayBuffer) in WebAssembly Memory:          │
│    ├── A. Cheat Detector:                                                   │
│    │     - Check: w:sz < 8 (font size < 4pt)                                │
│    │     - Check: font color == background color (e.g. white on white)      │
│    │     - Check: high repetition ratio (> 15 identical niche skill tokens) │
│    │                                                                        │
│    ├── B. Page Budget Calculator:                                           │
│    │     - Compute page count & trailing page line count                    │
│    │     - If Page Count > 1 AND Last Page Lines <= 4:                      │
│    │       Prompt: "Auto-fit trailing 3 lines to 2 pages?"                  │
│    │                                                                        │
│    └── C. Pre-Parsed Clean Text Extraction:                                 │
│          - Extracts clean structured plain text in browser memory           │
│          - Eliminates server-side Apache Tika / Poppler OCR load            │
│       │                                                                     │
│       ▼                                                                     │
│ 4. If Cheats Detected: Display Modal Alert with exact flagged elements      │
│ 5. If Clean: Send { file_blob, extracted_text } to Laravel API              │
│       │                                                                     │
│       ▼                                                                     │
│ 6. Laravel persists binary to R2 and writes extracted_text to Elasticsearch │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.3 Page Budget Auto-Fitter Mechanics
When a resume spills onto an extra page by only 1–4 lines, the client-side engine offers a 1-click **"Fit to Page"** optimization:
- Reduces line spacing (`w:line`) by 5% to 10%.
- Tightens paragraph spacing before/after (`w:spacing w:after="40"` instead of `120`).
- Adjusts page margins from 1.0 inch to 0.75 inch (`w:pgMar`).
- Recalculates page breaks in memory and displays an instant side-by-side preview of the clean 2-page document.

---

## 3. Feature 2: Multi-Version Resume Visual Comparator (Diff Tool)

### 3.1 The Re-Application Challenge
Candidates frequently re-apply for new openings months or years after their initial rejection or placement. Recruiters must quickly answer:
- *What new skills or certifications did the candidate acquire?*
- *Did their job title or company tenure change?*
- *Were any previous skills, projects, or dates altered or removed?*

Manually reading two 3-page resumes takes 15+ minutes and invites human error.

### 3.2 Dual-Document WASM Diff Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                   MULTI-VERSION RESUME COMPARATOR (DIFF)                    │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. Recruiter opens Candidate History Timeline -> Selects "Compare Versions" │
│       │                                                                     │
│       ▼                                                                     │
│ 2. Fetch v1 (Original, e.g. 2024) and v2 (New, e.g. 2026) Binaries          │
│       │                                                                     │
│       ▼                                                                     │
│ 3. Ingest into Parallel WASM Instances:                                     │
│    const docV1 = await AgyWasmEngine.openDocument(v1Bytes);                 │
│    const docV2 = await AgyWasmEngine.openDocument(v2Bytes);                 │
│       │                                                                     │
│       ▼                                                                     │
│ 4. Token & Paragraph AST Diffing Algorithm (Myers / Levenshtein Vector):     │
│    - Compute paragraph hash map & fuzzy sentence alignments                 │
│    - Classify diff items: ADDED, REMOVED, MODIFIED, UNCHANGED                │
│       │                                                                     │
│       ▼                                                                     │
│ 5. Visual Vector Rendering:                                                 │
│    ├── 🟩 Green Highlight Box: Newly added skills, projects, certifications │
│    ├── 🟥 Red Strikethrough Box: Removed positions or altered dates         │
│    └── 🟧 Orange Highlight Box: Modified job titles or revised bullets      │
│       │                                                                     │
│       ▼                                                                     │
│ 6. Recruiter Views: Side-by-Side Synced View OR Unified Changelog Drawer    │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 3.3 Side-by-Side Synchronized Scrolling
- The UI mounts two `<canvas>` viewports side by side: Left = **Version 1 (Previous)**, Right = **Version 2 (Current)**.
- Scrolling either viewport triggers a proportional scroll lock using normalized document height percentages:
  ```javascript
  const scrollRatio = leftViewer.scrollTop / (leftViewer.scrollHeight - leftViewer.clientHeight);
  rightViewer.scrollTop = scrollRatio * (rightViewer.scrollHeight - rightViewer.clientHeight);
  ```
- A sticky summary pill at the top displays quick stats:  
  `+4 New Skills` · `+1 New Role (Senior Tech Lead)` · `-1 Removed Tool (AngularJS)`.

---

## 4. Feature 3: White-Label Agency Presentation Mode

### 4.1 The Agency Value Proposition
Recruiting agencies on JERO present vetted candidates to enterprise corporate clients. Standard practice requires agencies to strip personal contact information and prepend an **Agency Candidate Presentation Sheet** featuring:
- Agency logo and contact details.
- Recruiter rating and summary notes.
- Verified skill breakdown and assessment scores.
- Candidate availability, notice period, and hourly/annual rate.
- Standard legal representation disclaimer and confidentiality notice.

### 4.2 Dynamic Vector Cover Sheet & Running Header Injection

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                  AGENCY PRESENTATION MODE COMPOSITOR                        │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. Recruiter clicks "Export Agency Presentation" in Candidate Drawer        │
│       │                                                                     │
│       ▼                                                                     │
│ 2. Pull Agency Metadata from Company Settings:                              │
│    - Agency Logo PNG/SVG Data URI                                           │
│    - Recruiter Evaluation (Rating, Notes, Bill Rate, Availability)          │
│       │                                                                     │
│       ▼                                                                     │
│ 3. Client-Side WASM Document Prepend:                                       │
│    - Injects Vector Page 1: Stylized Metronic Cover Sheet                   │
│    - Renders clean grid: Candidate Code (e.g. JERO-CAND-8402), Skills, Rate │
│    - Renders legal NDA banner at bottom                                     │
│       │                                                                     │
│       ▼                                                                     │
│ 4. Sub-Page Running Header & Footer:                                        │
│    - Inserts running header on Pages 2+: Agency Logo (Top Right)            │
│    - Inserts running footer: "Represented exclusively by Apex Staffing"     │
│       │                                                                     │
│       ▼                                                                     │
│ 5. Redaction Guard: Auto-redacts candidate personal email and phone         │
│       │                                                                     │
│       ▼                                                                     │
│ 6. High-Res Vector PDF Export in < 800ms directly to recruiter desktop      │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 5. Promotion of Offer Letter Generator to Phase 6

To maintain clean engineering modularity and support deep e-sign contract workflows, the **Dynamic Offer Letter & Contract Generator** is fully specified as **Phase 6**:
- **Reference:** [docs/OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md)
- **Scope:** Microsoft Word `.docx` template management, split-run XML substitution, interactive canvas preview, digital signature capture, and tamper-evident SHA-256 audit logging.

---

## 6. Multi-Tenancy Isolation Architecture (`company_id`)

All Phase 5 modules strictly adhere to JERO multi-tenant data boundaries:

```php
// Strict Company Scoping on Candidate Resumes and Historical Versions
public function getCandidateVersions(Request $request, $candidateId)
{
    $companyId = auth()->user()->company_id;
    
    // Tenancy Note: Ensure candidate history belongs strictly to current tenant
    $candidate = DB::table('candidates')
        ->where('id', $candidateId)
        ->where('company_id', $companyId)
        ->firstOrFail();

    $versions = DB::table('candidate_resumes')
        ->where('candidate_id', $candidate->id)
        ->where('company_id', $companyId)
        ->orderBy('created_at', 'desc')
        ->get(['id', 'file_name', 'file_url', 'created_at']);

    return response()->json(['versions' => $versions]);
}
```

- **Candidate Diff Privacy:** Version comparison is strictly limited to candidate records owned by the authenticated recruiter's `company_id`.
- **Agency Branding:** Agency cover sheets draw strictly from the logged-in user's corporate profile assets.

---

## 7. UI/UX Specifications (Metronic Standard)

- **Design System:** Metronic Clean Card System (`card card-custom`, `card-header`, `card-body`).
- **Typography:** Poppins font family across all modals, tabs, and canvas labels; maximum body font size 14px.
- **Iconography:** Clean SVG vector icons for diff additions (`+`), removals (`-`), cheat flags (`!`), and downloads; zero 3D elements.
- **Diff Color Coding:**
  - Added text: `#10b981` (Emerald green, 15% opacity background, solid green border).
  - Removed text: `#ef4444` (Crimson red, 15% opacity background, red strikethrough line).
  - Modified text: `#f59e0b` (Amber yellow, 15% opacity background).

---

## 8. Performance Budget & Memory Management

| Metric | Target Budget | Enforcement Mechanism |
| :--- | :--- | :--- |
| **Pre-Upload Sanitizer Inspection** | $< 350\text{ms}$ | Pure in-memory XML scanning; stops at first page break for cheat analysis |
| **Dual-Document Diff Memory** | $< 70\text{MB}$ peak | Serial document loading; deterministic `.free()` cleanup upon modal dismissal |
| **Cover Sheet Prepend & PDF Export** | $< 900\text{ms}$ | Vector element injection directly into canvas compositor |

---

## 9. Error Handling & Edge Cases

| Edge Case | Solution / Fallback |
| :--- | :--- |
| **Diffing Structurally Mismatched Files** | If candidate uploads DOCX for v1 and scanned PDF for v2, engine falls back to structured OCR text diff with a notification banner. |
| **Obsolete `.doc` Binary Upload** | Sanitizer displays standard Metronic notification prompting conversion to `.docx` or `.pdf`. |
| **Extremely Large Resume (> 20 pages)** | Diff engine samples headings and body sections progressively to maintain 60 FPS responsiveness. |

---

## 10. Verification & Acceptance Checklist

- [ ] **Pre-Upload Cheat Detection:** Verify that a test resume containing 1pt white-on-white text triggers the anti-cheat warning banner before file upload.
- [ ] **2.1-Page Budget Optimizer:** Confirm that resumes with 2 lines on page 3 display the "Fit to Page" prompt and cleanly adjust margins and spacing.
- [ ] **Side-by-Side Diff Synchronization:** Verify that scrolling v1 or v2 resumes maintains accurate line alignment and correctly highlights added/removed sections.
- [ ] **Agency Presentation Export:** Confirm that prepending an agency cover sheet generates a high-resolution vector PDF with candidate contact info automatically redacted.
- [ ] **Multi-Tenancy Security:** Verify that candidate version histories cannot be accessed by unauthorized `company_id` accounts.
- [ ] **Zero Server CPU:** Confirm that all sanitization, diffing, and presentation generation runs 100% on the client with zero LibreOffice/Puppeteer backend load.
