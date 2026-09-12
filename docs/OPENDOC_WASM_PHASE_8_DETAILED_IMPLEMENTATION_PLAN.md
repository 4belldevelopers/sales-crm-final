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
│              DOWNLOAD RESUME CENTER: OPTIMIZE & EXPORT WORKFLOW             │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. Recruiter opens Download Resume Center (#rdcDrawer)                      │
│       │                                                                     │
│       ▼                                                                     │
│ 2. Featured Studio Card Displays at Top:                                    │
│    [ ⚡ EDIT & OPTIMIZE RESUME BEFORE DOWNLOAD ]                             │
│    "Current ATS Match: 76% • Target Job: Senior Backend Engineer"           │
│    [ ✏️ Open Optimizer Studio ]                                              │
│       │                                                                     │
│       ▼                                                                     │
│ 3. Recruiter clicks "Open Optimizer Studio":                                │
│    - Options list collapses into #rdcOptimizerPanel                         │
│    - Pre-loads candidate resume text & job skills from on-demand cache      │
│       │                                                                     │
│       ▼                                                                     │
│ 4. Split Optimizer Workbench:                                               │
│    ├── Left Panel: ATS Score Gauge & Missing Skills Inspector               │
│    │     - Circular Progress Ring (e.g. 76% -> 92%)                         │
│    │     - Missing Keywords: [ + Docker ] [ + CI/CD ] [ + Kubernetes ]      │
│    │     - Clicking a chip inserts skill into the editor in real-time       │
│    │                                                                        │
│    └── Right Panel: Interactive Resume Text Editor                          │
│          - Editable sections: Summary, Experience, Skills                   │
│          - Live debounced score recalculator (< 50ms)                       │
│       │                                                                     │
│       ▼                                                                     │
│ 5. Recruiter clicks "Download Optimized PDF":                               │
│    - Compiles clean, ATS-standard vector PDF client-side                    │
│    - Zero server roundtrip; instant download in < 800ms                     │
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
   - Mandatory Core Skills: $60\%$ of total score weight.
   - Secondary / Good-to-Have Skills: $30\%$ of total score weight.
   - Structural Density (Experience bullet count, tenure formatting): $10\%$ of total score weight.
4. **Live Delta Computation:**
   - As characters are typed or keyword chips clicked, `optimizer.recalculateScore()` executes within $15\text{ms}$ and updates the UI progress ring smoothly.

---

## 4. UI/UX Design Specifications (Metronic Standard)

- **Drawer Integration:** Seamlessly mounted inside `#rdcDrawer` above Group 1.
- **Color Coding:**
  - High Match ($\ge 80\%$): `#10b981` (Emerald Green)
  - Moderate Match ($60\% - 79\%$): `#f59e0b` (Amber Gold)
  - Low Match ($< 60\%$): `#ef4444` (Crimson Red)
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
