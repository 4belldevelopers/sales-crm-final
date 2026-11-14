# Forensic UI/UX Audit & Enterprise Design Review: Add Agency Drawer (V2 Post-Implementation)

> **Module:** Service Company Jobs Tracker / Add Agency Drawer (`testing.jero.works/service_company/jobs`)  
> **Audited View:** `resources/views/service_company/jobs/spartnerlisting.blade.php` & AJAX subviews  
> **Commit Hash:** `315ef69c`  
> **Date:** 2026-11-14  
> **Governing Standards:** Rule 14 (15.6" Screen Fit), Rule 18 (Persona Code Freeze), Rule 20 (Mandatory Card + Sub-Card Enterprise Design System & Header Background Standard), Rule 21 (Module Audit Storage Pattern), Rule 25 (Canonical Scope Pills).

---

## 1. Executive Summary & Design System Scorecard

Following the deployment of commit `315ef69c`, the **Add Agency Drawer** was evaluated live in production environment conditions (full-width desktop viewport on `testing.jero.works/service_company/jobs`).

The elevation from the previous iteration is substantial: the drawer now possesses distinct visual hierarchy, anchored section headers, and harmonious token semantics.

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                       ENTERPRISE UI/UX SCORECARD                             │
├────────────────────────────────────────┬───────────────┬─────────────────────┤
│ Evaluation Dimension                   │ V1 Score (89) │ V2 Post-Deploy (94) │
├────────────────────────────────────────┼───────────────┼─────────────────────┤
│ 1. Rule 20 Card + Sub-Card System      │    94 / 100   │       97 / 100      │
│ 2. Rule 20 Header Background Standard  │    78 / 100   │       96 / 100      │
│ 3. Action Token Semantics & Contrast   │    82 / 100   │       96 / 100      │
│ 4. 15.6" Screen Fit & Data Density     │    90 / 100   │       93 / 100      │
│ 5. Micro-interactions & Affordances    │    84 / 100   │       90 / 100      │
├────────────────────────────────────────┼───────────────┼─────────────────────┤
│ TOTAL COMPOSITE GRADE                  │  89 / 100 (A-)│     94 / 100 (A)    │
└────────────────────────────────────────┴───────────────┴─────────────────────┘
```

---

## 2. Confirmed Wins & Verified Enhancements

### 2.1 Anchored Sticky Job Header (Rule 20 & Rule 25 Compliance)
- **Dedicated Background:** Top summary strip is now anchored with `#f8fafc` background and `#e2e8f0` border, creating clear demarcation between the job summary and interactive workspace.
- **Rule 25 Scope Badge:** The `My Organization` pill is rendered in canonical Warm Amber (`#fef3c7` bg / `#92400e` text / `#fcd34d` border) with `fa-id-badge`, completely eliminating ambiguous "Internal" terminology.
- **Data Compactness:** Height is constrained to `52px`, preserving maximal vertical real estate for the agency stream.

### 2.2 Canonical Action Token Semantics
- **Primary Blue Action Button:** The `Send Requirement` button now uses JERO Primary Blue (`#0066ff` / `#0052cc`) with an SVG paper-plane icon.
- **Semantic Separation:** Emerald green is now strictly reserved for confirmed state (`✓ Request Sent` / `.ag-badge-sent`), eliminating the previous visual conflict where active CTAs and confirmed states shared identical green hues.

### 2.3 Sticky Column Header Row
- Injected `.agency-stream-header-row` (`#f8fafc` background, `#e2e8f0` border) directly above the agency card stream with uppercase column labels:
  - `AGENCY & SPECIALIZATION`
  - `PROFILES`
  - `RATES & TERMS`
  - `LOCATION & TEAM`
  - `ACTION`

### 2.4 Marketplace Broadcast Card (Funnel 1)
- **Status Indicator:** Added `Unpublished` badge in soft slate next to `Open to Agency Applications`.
- **CTA Affordance:** Added SVG rocket icon to `Publish to Agency Marketplace` button.

### 2.5 Sub-Tabs & Bulk Select Refinements
- **Translucent Counter Pill:** `Recommended (14)` active tab wraps count in soft translucent badge (`background: rgba(255, 255, 255, 0.25)`).
- **Bordered Pill "Select All":** Replaced faint gray toggle with bordered pill button `.ag-select-all-pill`.

---

## 3. Forensic Analysis & Root-Cause Discoveries

A detailed forensic inspection of the latest rendered state (`media_1794664967327_013a969e.jpg`) identified two micro-issues requiring fine-tuning:

### 3.1 CSS Cascade Bug on Desktop Sub-Card Labels (`.ag-subcard-label`)
- **Symptom:** Although the sticky column header row is active at the top of the stream, uppercase labels (`PROFILES`, `RATES & TERMS`, `LOCATION & TEAM`) are still visible inside each agency card.
- **Root Cause:**
  In `spartnerlisting.blade.php`:
  - Line 692-695:
    ```css
    @media (min-width: 992px) {
        .ag-subcard-label { display: none !important; }
    }
    ```
  - Line 757-767 (declared *later* in stylesheet):
    ```css
    .ag-subcard-label {
        ...
        display: flex !important; /* <--- WINS DUE TO LATER SOURCE ORDER */
    }
    ```
  Because both rules share identical specificity (`.ag-subcard-label`) and both use `!important`, CSS source order dictates that the later `display: flex !important` overrides `display: none !important`.
- **Impact on 15.6" Screens:** Each card remains ~98px tall instead of condensing to ~72px, reducing visible cards on a 768p laptop from 6 cards down to 3.5 cards.
- **Resolution:** Move the `@media (min-width: 992px)` block to the end of the CSS or increase specificity (e.g. `.agency-entity-card .ag-subcard-label`).

### 3.2 Duplicate "Additional Partner" + "Additional Agency" Badges
- **Symptom:** For agencies with 0% skill match that are flagged as additional candidates, cards render two adjacent badges: `[Additional Partner]` and `[Additional Agency]`.
- **Root Cause:** In `find_agency_recom_ajax.blade.php`:
  ```blade
  @if($percentage > 0)
      <span class="ag-badge ag-badge-match-high">Match {{ $percentage }}%</span>
  @else
      <span class="ag-badge ag-badge-additional">Additional Partner</span>
  @endif

  @if(!empty($row->_is_additional))
      <span class="ag-badge ag-badge-additional" title="...">Additional Agency</span>
  @endif
  ```
- **Resolution:** Consolidate the two checks into a single mutually exclusive badge condition.

### 3.3 Column Header Text Sightline Alignment
- **Symptom:** The sticky column header text `AGENCY & SPECIALIZATION` has `padding-left: 28px`, while the agency card content below begins after the checkbox and avatar (~60px).
- **Resolution:** Refine the column header's left padding to align with the agency title's vertical baseline.

---

## 4. Path to 98/100 (Enterprise A+)

Applying these three targeted refinements will achieve complete visual alignment and optimal density for 15.6" displays (`1366×768` and `1920×1080`):

| Micro-Refinement | Scope | Expected Gain |
| :--- | :--- | :--- |
| **Fix CSS Cascade on `.ag-subcard-label`** | `spartnerlisting.blade.php` | 27% vertical space saving; 6 cards in view on 15.6" laptops |
| **Consolidate Duplicate Badges** | `find_agency_recom_ajax.blade.php` | Eliminates redundant badge text; cleaner identity sub-card |
| **Align Column Header Baseline** | `spartnerlisting.blade.php` | Pixel-perfect vertical grid sightlines across all 5 columns |
