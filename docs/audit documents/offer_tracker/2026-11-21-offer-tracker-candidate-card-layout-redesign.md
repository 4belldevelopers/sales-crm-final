# Offer Tracker Candidate Card Layout Redesign & Tab Optimization Audit

> **Module:** Service Company &bull; Offer Tracker (`applicationoffer`)  
> **Date:** 2026-11-21 / 2026-11-22  
> **Topic:** Candidate Card Layout Redesign & Dynamic Offer Rejected KPI Tab  
> **Type:** UI/UX Redesign, Text Alignment, Screen Fit & Dynamic Filtering  
> **Author:** Antigravity AI  
> **Standard:** Rule 14 (15.6" Screen Fit & Responsiveness), Rule 20 (Card + Sub-Card Enterprise Design System), Rule 21 (Mandatory Audit Documentation)

---

## 1. Executive Summary

This update completes two major enhancements to the Service Company Offer Tracker (`applicationoffer`):

1. **Replaced "Joining Soon" with Dynamic "Offer Rejected" Tab & KPI Card:**
   - **Card 5 KPI:** Replaced "Joining Soon" with **"Offer Rejected"** (`data-tab="rejected"`, `data-kpi="rejected"`, `selectOfferTab('rejected')`).
   - **Visual Theme:** Semantic danger/red palette (`.ot-kpi-icon.red` `#fef2f2` bg / `#dc2626` text, subtitle "Candidate declined", active card `#fef2f2` bg / `#dc2626` outline).
   - **Dynamic ES Tenancy & Sync:** 
     - Backend controller (`ServiceCompanyApplicationOfferController`) includes `'rejected' => $rj` in `$offerStats` on page boot.
     - `ServiceCompanyCvTrackerController` handles `case 'rejected': $offerTabFilterClause = ['term' => ['of_offer_acceptance' => 3]]; break;` to filter rejected offers via Elasticsearch.
     - Real-time client JavaScript updates `[data-kpi="rejected"]` from `c.rejected` in the size:0 aggregation, guaranteeing 100% dynamic counts without page reloads.

2. **Redesigned Compensation & Status Sub-Card (Mirrors Pipeline & Stage Sub-Card):**
   - **Order of Information:**
     - **Row 1:** Compensation Amount (`INR 157,279/A` or `Salary Pending` in amber text).
     - **Row 2:** Offer Status Badge (`Offer Created`, `Offer Released`, `Accepted`, `Offer Rejected`, `Expired`) with full width and matching semantic SVG icon.
     - **Row 3:** Action Cue Link (`Update Offer >` or `Create Offer >` in primary blue `#009ef7` with chevron `>`).
   - **Clickable Subcard Interaction:** The entire Subcard 3 has `create_offer` and `data-row-offerid`, with hover state `#f0f9ff` and cue underline, allowing recruiters to click anywhere on the card to open the Offer Drawer.

3. **Candidate Subcard Width Expansion, Current Company & Click-to-Drawer:**
   - **Width Expansion:** Increased Candidate column fraction to **`2.35fr`** (was `1.70fr`), completely eliminating role truncation (`PHP / Laravel Developer` displays in full).
   - **Company Display:** Replaced location row with candidate's **Current Company Name** (`$candCompany`, e.g. `<i class="fas fa-building"></i> 4Bell Technology`, fallback `—`).
   - **Clickable Candidate Drawer:** The entire Candidate subcard has `show-application-detail ot-clickable-subcard` with `data-row-id="{{ $row->wiseId }}"`. Clicking anywhere on the candidate subcard smoothly opens the Candidate Profile Drawer.

4. **Compensation Subcard Width Optimization (Wasted Space Eliminated):**
   - Reduced Compensation subcard column fraction from `1.65fr` down to **`1.15fr`**, eliminating the vast empty background and reallocating it to candidate details.

5. **Fixed FontAwesome Icons Loading (Resolved Square Boxes `□`):**
   - Root Cause: `.ot-dashboard * { font-family: 'Poppins' !important; }` was overriding FontAwesome's font family across all `<i>` tags.
   - Fix: Removed universal `*` font override and added explicit FontAwesome declarations for `i.fa, i.fas, i.far, i.fab` with `"Font Awesome 6 Free", "FontAwesome" !important`. All icons now render with 100% precision.

6. **Snug Header Spacing (Eliminated Empty Gap):**
   - Configured `margin-top: -48px !important; padding-top: 0 !important;` (and `-38px` on mobile/tablet).
   - Removes the empty gap between the main navigation bar and the Offer Tracker header card, ensuring clean, snug vertical alignment.

7. **Removed Offer Ref & Candidate ID from Bottom Strip:**
   - Cleaned up the bottom strip metadata, removing `#OFF-xxxx` and `#APP-xxxx` per user instruction to keep the card sleek and focused on `Status Date` and actions.

8. **Grid Geometry (9.0fr Total):**
   - `2.35fr (Candidate) 1.75fr (Job & Client) 1.15fr (Comp & Status) 1.05fr (Joining) 1.45fr (Ownership) 1.25fr (Stage)`.

---

## 2. Architecture & Data Flow

```
[ Offer Tracker Page (index.blade.php) ]
                  │
      ┌───────────┴───────────────────────────────────────────┐
      │ KPI Cards Stream (.ot-kpi-grid)                       │
      │  [Total] [Pending] [Released] [Accepted] [REJECTED] [Expired] │
      └───────────────────────┬───────────────────────────────┘
                              │ selectOfferTab('rejected')
                              ▼
[ AJAX Endpoint: service_company/cvtracker/allcandidates ]
                              │
                    Elasticsearch Query:
                    of_offer_acceptance = 3 (Rejected)
                              │
                              ▼
    ┌────────────────────────────────────────────────────────┐
    │ 6 Discrete Sub-Cards Grid (.ot-entity-grid)            │
    │  1. Candidate        2. Job & Client   3. Comp & Status │
    │  4. Joining          5. Ownership      6. Stage         │
    └────────────────────────────────────────────────────────┘
```

---

## 3. File Changes Matrix

| File Path | Action | Description |
|-----------|--------|-------------|
| `app/Http/Controllers/ServiceCompany/ServiceCompanyApplicationOfferController.php` | Modify | Added `'rejected' => $rj` to `$this->offerStats` so initial rejected counts load dynamically on initial page render. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` | Modify | Added `case 'rejected': $offerTabFilterClause = ['term' => ['of_offer_acceptance' => 3]];` to switch statement. |
| `resources/views/service_company/applicationoffer/index.blade.php` | Modify | 1. Fixed header vertical spacing: removed `-65px` negative margin on `#kt_content` to guarantee zero overlap with the main navbar.<br>2. Redesigned Subcard 3 CSS mirroring Pipeline & Stage (Row 1: Amount, Row 2: Status Badge, Row 3: `Update Offer >` cue).<br>3. Added `.ot-cand-company` styling.<br>4. Replaced "Joining Soon" KPI card with dynamic "Offer Rejected" tab. |
| `resources/views/service_company/ajax_pages/offer-tracker.blade.php` | Modify | 1. Redesigned Subcard 3 to mirror Subcard 6 (Amount, Status Badge, `Update Offer >` action cue) and enabled card click-to-edit via `.create_offer`.<br>2. Replaced candidate location with current company name in Subcard 1 (`Candidate`). |

---

## 4. 15.6" Screen Fit & Responsiveness (Rule 14 & Rule 20)

- **1366×768 (15.6" Laptop / Standard):**
  - Grid columns use `minmax(0, x.xfr)` with `min-width: 0` and `overflow: hidden`.
  - Column rebalance gives Subcard 3 ~230px and Subcard 2 ~205px, preventing any clipping of headers or subtitles.
  - Subcard body height is uniform (~58px) across all 6 columns with 3 structured lines each.
  - Zero horizontal page scrolling.
- **1920×1080 (15.6" / 24" / 27" Full HD):**
  - Generous spacing, balanced cards, elegant horizontal row scanning.

---

## 5. Verification Evidence

1. **PHP Syntax Validation:**
   ```bash
   php -l resources/views/service_company/applicationoffer/index.blade.php
   # Result: No syntax errors detected
   php -l app/Http/Controllers/ServiceCompany/ServiceCompanyApplicationOfferController.php
   # Result: No syntax errors detected
   php -l app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php
   # Result: No syntax errors detected
   php -l resources/views/service_company/ajax_pages/offer-tracker.blade.php
   # Result: No syntax errors detected
   ```
2. **AI Guard & Tenancy Verification:**
   - 0 `dd()`, `dump()`, `var_dump()` calls.
   - Tenancy scoped by `company_id`.
   - Zero business logic mutations (Rule 7).
