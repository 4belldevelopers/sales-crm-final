# JERO OpenDoc WASM Engine — Phase 8 Detailed Implementation Plan
## Download Resume Center: Edit & Optimize Studio, Live ATS Match Optimizer & In-Browser Client-Ready Exporter

> **Document Status:** Complete Architecture & Implementation Specification  
> **Target Version:** Laravel 11 / OpenDoc WASM 2.0 / Client-Side Engine  
> **Target Audience:** Core Engineering, ATS Architects, Recruiter Productivity, Compliance  
> **Master Reference:** [docs/OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md)  
> **Phase 3 Reference:** [docs/OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Phase 7 Reference:** [docs/OPENDOC_WASM_PHASE_7_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_7_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Rule Adherence:** Multi-Tenancy Scoped (`company_id`), Zero LibreOffice Server Load, Metronic UI Standard, Poppins Font $\le$ 14px

---

## 1. Executive Summary & Core Objectives

Phase 8 integrates an **Interactive Edit & Optimize CV Studio directly into the Download Resume Center (`#rdcDrawer`)**. 

In high-velocity recruiting, when recruiters open the Download Resume Center to export a resume for an enterprise client or hiring manager, they frequently need to make quick, critical adjustments:
1. Fix a typo or awkward phrasing in a project bullet point.
2. Insert a specific skill or framework that the candidate confirmed during screening but forgot to include in their original CV.
3. Check the candidate's ATS match score against the target Job Description to maximize interview selection rates.
4. Export a pristine, client-ready PDF in $< 1\text{s}$ without having to download a Word document, edit it in Microsoft Office, re-convert to PDF, and re-upload.

Phase 8 provides this workflow directly inside the Download Resume Center with **0% server CPU load** while leaving **Option 1 (Original Resume Download) 100% untouched**.

---

## 2. Architectural Design & Workflow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│             GROUP 6: DOWNLOAD RESUME CENTER OPTIMIZE & EXPORT               │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. Recruiter opens Download Resume Center (#rdcDrawer)                      │
│       │                                                                     │
│       ▼                                                                     │
│ 2. Modal Header remains clean (no top banner attached to header).           │
│    Format options list displays standard groups 1 through 5, and:           │
│    [ 6. Edit & Optimize Resume ] (3 Options, Teal Theme)                    │
│       ├── Option 61: Edit & Optimize Resume (Clean ATS Standard) - 2 Coins │
│       ├── Option 62: Edit & Optimize Resume + Company Branding - 3 Coins   │
│       └── Option 63: Edit & Optimize Resume + Highlighted Keywords - 3 Coins│
│       │                                                                     │
│       ▼                                                                     │
│ 3. Recruiter selects Option 61, 62, or 63:                                  │
│    - Inline Studio Workbench (#rdcGroup6Studio) expands directly in Group 6 │
│    - Multi-tiered text extractor loads resume text (never blank):           │
│        Tier 1: rcvResumeText from viewer layer                              │
│        Tier 2: DOM span text nodes                                          │
│        Tier 3: Structured candidate profile fallback                        │
│       │                                                                     │
│       ▼                                                                     │
│ 4. Interactive In-Group Workbench:                                          │
│    ├── Top: Live ATS Score Badge (e.g. 85% Match) & Target Job Title        │
│    ├── Missing Skills Inspector: [ + Skill ] 1-click insertion chips        │
│    ├── Editable Resume Textarea: Live debounced ATS recalculation (< 30ms)  │
│    └── Direct Action: [ 📥 Download Optimized Resume ]                      │
│       │                                                                     │
│       ▼                                                                     │
│ 5. Recruiter clicks Download from Group 6 or sticky footer:                 │
│    - Compiles high-fidelity vector PDF via pdfMake in browser memory        │
│    - Option 61: Clean ATS-standard vector PDF                               │
│    - Option 62: Vector PDF + Company Branding (Logo & Header)               │
│    - Option 63: Vector PDF + Highlighted Matching Keywords                  │
│    - Instant download in < 800ms; zero server load                          │
│       │                                                                     │
│       ▼                                                                     │
│ 6. Option 1 (Original Resume) remains completely untouched on Cloudflare R2 │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Real-Time Client-Side ATS Match Algorithm

The scoring engine operates entirely in browser memory (`AgyCvOptimizer.js`):
1. **Target Skills Vectorization:**
   - Extracts required skills from `$rcvJobSkills` and categorized mandatory/good-to-have pools.
2. **Normalized Word Boundary Scanning:**
   - Evaluates resume text against token patterns: `\b(skill)\b` with punctuation normalization (e.g. `node.js` $\leftrightarrow$ `nodejs`, `ci/cd` $\leftrightarrow$ `continuous integration`).
3. **Dynamic Weighting:**
   - Evaluates matched skills against unique job requirements.
   - Computes live match percentage ($0\% \rightarrow 100\%$).
4. **Live Delta Computation:**
   - As characters are typed or keyword chips clicked, `optimizer.calculateAtsScore()` executes within $< 30\text{ms}$ and updates the UI score badge smoothly.

---

## 4. UI/UX Design Specifications (Metronic Standard)

- **Drawer Integration:** Housed cleanly inside Group 6 (`.rdc-section-teal`) in `#rdcDrawer`. Header of `#rdcDrawer` remains completely unencumbered.
- **Color Coding:**
  - High Match ($\ge 80\%$): `#15803d` (Emerald Green)
  - Moderate Match ($50\% - 79\%$): `#b45309` (Amber Gold)
  - Low Match ($< 50\%$): `#b91c1c` (Crimson Red)
- **Typography:** Poppins font family across all labels, buttons, and textareas; maximum body font size 14px.
- **Iconography:** Clean SVG vector icons for edit, spark, chip insertion, and download; zero 3D elements.

---

## 5. Multi-Tenancy Isolation Architecture (`company_id`)

- **Strict Scoping:** All target job skills and candidate text inputs are scoped strictly to the authenticated recruiter's `company_id`.
- **Zero Cross-Company Exposure:** Marketplace partner companies cannot view another company's internal job requirements or customized candidate optimizations.
- **Original File Integrity:** The original Cloudflare R2 resume binary is never modified.

---

## 6. Verification & Acceptance Checklist

- [ ] **Option 1 Untouched:** Confirm that Option 1 in the Download Resume Center continues to download the pristine original file from Cloudflare R2.
- [ ] **Instant Zero-Wait Launch:** Verify that opening the Optimizer Studio uses preloaded assets with zero network delay.
- [ ] **Live Score Recalculation:** Verify that clicking missing skill chips or typing text instantly updates the ATS Match score ring.
- [ ] **Client-Side PDF Generation:** Confirm that clicking "Download Optimized PDF" compiles and downloads a crisp vector PDF in $< 1$s with 0% server CPU.
- [ ] **Drawer Navigation:** Confirm that clicking "Back to All Options" cleanly returns to the standard 15-option Download Resume Center view.
