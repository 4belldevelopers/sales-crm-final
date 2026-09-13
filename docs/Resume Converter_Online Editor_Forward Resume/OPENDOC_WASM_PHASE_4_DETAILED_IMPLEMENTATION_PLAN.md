# JERO OpenDoc WASM Engine — Phase 4 Detailed Implementation Plan
## Bench Talent Privacy Shield & Interactive JD Match Auditor with Skill Density Minimap

> **Document Status:** Complete Architecture & Implementation Specification  
> **Target Version:** Laravel 11 / OpenDoc WASM 2.0 / Client-Side Engine  
> **Target Audience:** Core Engineering, Marketplace Architects, Security & Compliance  
> **Master Reference:** [docs/OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md)  
> **Phase 3 Reference:** [docs/OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Rule Adherence:** Multi-Tenancy Scoped (`company_id`), Zero LibreOffice Server Load, Metronic UI Standard, Poppins Font $\le$ 14px

---

## 1. Executive Summary & Core Objectives

Phase 4 elevates JERO from a high-speed ATS document processor into a **secure talent-trading marketplace and interactive recruiter intelligence workbench**:

1. **Bench Talent Privacy Shield (Hard Pixel Burner & Anti-Poaching Architecture):**  
   Empowers staffing firms and consulting agencies to share bench candidates across the JERO Talent Marketplace (`service_company/bench_candidates`) with zero fear of candidate poaching. Unlike superficial CSS/PDF text hiding, OpenDoc WASM permanently burns opaque rectangular pixels directly into the canvas bitmap and excises the text layer entirely, preventing extraction via DevTools or clipboard copying.
2. **Interactive "JD vs. Resume" Live Match Auditor:**  
   Provides hiring managers and senior recruiters with visual evidence of candidate qualifications via an interactive side-by-side workbench. Hovering over any job requirement (e.g. `AWS (5+ Years)`) instantly illuminates all matching sentences on the resume canvas and auto-scrolls directly to the relevant project.
3. **Visual Skill Density Minimap (Gutter Strip):**  
   Mounts a dynamic vertical density strip alongside the resume viewer, color-coding candidate skill distribution across pages (🟢 Core Skills, 🔵 Secondary Skills, 🔴 Missing Mandatory Requirements).
4. **Strict Multi-Tenancy Isolation (`company_id`):**  
   Guarantees that partner companies on the marketplace can only inspect authorized, hard-redacted profiles while owner companies retain full, unredacted access to their candidates.

---

## 2. Feature 1: Bench Talent Privacy Shield (Hard Redaction)

### 2.1 The Business Need in Talent Marketplace Trading
In B2B hiring marketplaces, consulting companies trade available bench talent with other agencies and enterprise clients.
* **The Poaching Threat:** If a partner company or rogue recruiter discovers the candidate’s personal email, phone number, LinkedIn URL, or current consulting firm, they can bypass JERO, contact the candidate directly, and eliminate the originating agency's placement fee.
* **The Legacy Flaw (Superficial Hiding):** Many platforms hide text with CSS (`display: none;`, `opacity: 0`) or overlay black rectangles over a selectable PDF text stream. Any user can right-click $\rightarrow$ **Inspect Element**, copy the underlying text, or download the original file from the browser Network tab.

### 2.2 The OpenDoc WASM Hard-Burn Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       HARD PIXEL BURNER PIPELINE                            │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. Candidate Resume Binary Ingestion (DOCX or PDF)                          │
│       │                                                                     │
│       ▼                                                                     │
│ 2. Automated PII Regex & Entity Scanner:                                    │
│    ▶ Phone Numbers: (\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4} │
│    ▶ Personal Emails: [a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}       │
│    ▶ LinkedIn / Portfolios: (linkedin\.com\/in\/|github\.com\/)[^\s]+       │
│    ▶ Current Employer & Address Entities                                    │
│       │                                                                     │
│       ▼                                                                     │
│ 3. Coordinate Resolution: Exact twip bounding boxes via OpenDoc WASM        │
│       │                                                                     │
│       ▼                                                                     │
│ 4. Hard Canvas Rasterization Burn:                                          │
│    ▶ ctx.fillStyle = '#0f172a'; // Solid opaque dark slate                   │
│    ▶ ctx.fillRect(x - pad, y - pad, w + (pad*2), h + (pad*2));              │
│    ▶ Pixels in the bitmap are overwritten permanently.                      │
│       │                                                                     │
│       ▼                                                                     │
│ 5. Text-Layer Excision:                                                     │
│    ▶ Bypasses text layer generation for redacted coordinate rectangles.     │
│    ▶ PDF clipboard copy over the black box yields 0 characters.             │
│       │                                                                     │
│       ▼                                                                     │
│ 6. Tokenized 1-Click Marketplace Share Link:                                │
│    ▶ Serves exclusively the client-side redacted PDF.                       │
│    ▶ Original Cloudflare R2 file URL is NEVER signed for partner companies. │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.3 Cryptographic Anti-Poaching Guarantee
- **Bitmap Permanence:** The redaction is part of the flattened image raster. There is no underlying layer to "peel back" or toggle off in DevTools.
- **Zero Extractable Text:** Text extraction tools (e.g. `pdftotext`, OCR with low thresholds) see solid black pixel rectangles.

---

## 3. Feature 2: Interactive "JD vs. Resume" Live Match Auditor

### 3.1 The Screening Bottleneck
When an ATS reports *"88% Match"*, hiring managers still spend 10–15 minutes hunting through a 4-page resume to verify whether the candidate truly has the claimed depth of experience or merely keyword-stuffed their summary.

### 3.2 Side-by-Side Split View Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    RECRUITER SCREENING WORKBENCH (SPLIT VIEW)               │
├──────────────────────────────────────┬──────────────────────────────────────┤
│ LEFT COLUMN: JOB REQUIREMENTS AUDIT  │ RIGHT COLUMN: LIVE WASM RESUME VIEWER│
├──────────────────────────────────────┼──────────────────────────────────────┤
│ 🟢 Core Requirements (Weight: 60%)   │ ┌──────────────────────────────────┐ │
│  ☑ AWS Cloud (5+ Years) [17 hits] ────┼─▶ Auto-scrolls to Page 2, Sec 3.    │ │
│  ☑ Docker / Kubernetes [8 hits]      │ │ Sentence illuminated in orange.   │ │
│  ☑ Terraform [4 hits]                │ │                                  │ │
│                                      │ │                                  │ │
│ 🔵 Secondary Skills (Weight: 25%)    │ │ [Canvas Bitmap Page 2]           │ │
│  ☑ Python Scripting [6 hits]         │ │                                  │ │
│  ☑ CI/CD Pipelines [5 hits]          │ │                                  │ │
│                                      │ └──────────────────────────────────┘ │
│ 🔴 Missing Mandatory (Weight: 15%)   │ ── Vertical Minimap Strip ────────── │
│  ☒ SOC2 Compliance Experience        │ [🟢] Page 1: Skills Summary          │
│                                      │ [🟢🟢] Page 2: DevOps Projects (Dense) │
│ Match Summary: 88% Strong Match      │ [🔵] Page 3: Education & Certs       │
└──────────────────────────────────────┴──────────────────────────────────────┘
```

### 3.3 Bi-Directional Hover Sync Mechanics
1. **JD Hover Event:** When the recruiter hovers over `AWS Cloud (5+ Years)` on the left panel:
   - The viewer looks up pre-indexed coordinate hits: `AgyWasmEngine.collectWordHits(doc, ['aws', 'amazon web services'])`.
   - The canvas renders animated highlight pulses around all 17 matches.
   - The viewer smoothly animates scroll position to the highest-density project paragraph.
2. **Resume Hover Event:** When hovering over a highlighted skill inside the resume:
   - The corresponding requirement in the left column highlights and increments its verified hit counter.

### 3.4 Skill Density Minimap (Gutter Strip)
Alongside the vertical scrollbar of the resume viewer, a 24px wide minimap gutter displays color-coded markers:
- **Green Ticks (`#10b981`):** Matches for Core/Mandatory requirements.
- **Blue Ticks (`#3b82f6`):** Matches for Good-to-Have/Secondary skills.
- **Red Alert Banner (`#ef4444`):** Fixed at the top if mandatory requirements are completely missing from the document.
- **Click-to-Jump:** Clicking any tick on the minimap instantly navigates the viewer to that exact page position.

---

## 4. Multi-Tenancy Isolation Architecture (`company_id`)

JERO strictly separates tenant access at every layer of the marketplace:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        TENANT ACCESS BOUNDARY                               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  [Company A: Apex Staffing (company_id: 101)] (Candidate Owner)            │
│    ├── Full Unredacted Original Resume (Cloudflare R2 Direct Access)        │
│    ├── Candidate Direct Phone, Email, Address, LinkedIn                     │
│    └── Internal Recruiter Notes, Margins, Private Evaluation                │
│                                                                             │
│  ══════════════════════════════ TENANCY WALL ══════════════════════════════ │
│                                                                             │
│  [Company B: Global Tech (company_id: 205)] (Marketplace Partner)           │
│    ├── Access strictly gated by is_marketplace_published = true             │
│    ├── Receives ONLY Hard-Redacted WASM Vector Profile                      │
│    ├── Original Cloudflare R2 binary URL: 403 FORBIDDEN                     │
│    └── Candidate direct contact info: 100% NON-EXISTENT in code/DOM         │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 4.1 Database Enforcement
```php
// Marketplace Candidate Query Scoping
$candidate = Candidate::where('id', $candidateId)
    ->where(function ($query) use ($currentCompanyId) {
        $query->where('company_id', $currentCompanyId)
              ->orWhere('is_marketplace_published', true);
    })
    ->firstOrFail();

// If viewed by a partner company, strip private tenant relations
if ($candidate->company_id !== $currentCompanyId) {
    $candidate->makeHidden(['phone', 'email', 'secondary_phone', 'address', 'internal_notes']);
}
```

---

## 5. UI/UX Specifications (Metronic Standard)

- **Layout:** Responsive 2-column split view (`col-lg-5` requirements audit / `col-lg-7` resume viewer) fitting cleanly on 1366×768 laptops up to 4K displays.
- **Typography:** Poppins font family across all labels, titles, and score badges; maximum font size 14px for body text.
- **Iconography:** Clean SVG vector icons for matched/missing states (`<svg>` checkmark and cross); zero 3D elements.
- **Minimap Design:** Sleek 20px semi-transparent track (`background: rgba(241, 245, 249, 0.8)`) on the right margin with rounded 3px heat ticks.

---

## 6. Error Handling & Edge Cases

| Edge Case | Solution / Fallback |
| :--- | :--- |
| **No Skills Found in Resume** | Minimap displays empty state; left column displays prominent alert: *"No core qualifications detected in resume text."* |
| **Non-Standard Phone Formats** | Multi-pattern regex engine matches international, spaced, punctuated, and extension formats (`+1`, `(555)`, `ext. 104`). |
| **Obsolete `.doc` File** | Displays friendly Metronic notification prompting user to convert or review candidate summary. |
| **Window Resize / Responsive Toggle** | Minimap heat ticks recalculate positions automatically via `ResizeObserver` on the canvas container. |

---

## 7. Verification & Acceptance Checklist

- [ ] **Hard Pixel Redaction:** Confirm that redacted areas in exported marketplace PDFs are solid canvas pixels with zero selectable text in the text layer.
- [ ] **Anti-Poaching Security:** Verify that inspecting network payloads or DevTools DOM reveals zero candidate phone numbers or personal emails for partner companies.
- [ ] **Bi-Directional Hover Sync:** Confirm that hovering over a skill requirement on the left illuminates all matching instances in the resume canvas and auto-scrolls smoothly.
- [ ] **Minimap Accuracy:** Verify that green/blue ticks on the vertical minimap match the exact vertical position of keywords on Pages 1, 2, and 3.
- [ ] **Tenancy Scoping:** Verify `company_id` enforcement: partner companies cannot access internal notes, margins, or raw Cloudflare R2 files.
- [ ] **Syntax & Performance:** PHP syntax clean (`php -l`), zero server CPU spikes, 60 FPS client-side hover interactions.
