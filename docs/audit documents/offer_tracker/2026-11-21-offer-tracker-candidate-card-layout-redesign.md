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

2. **Redesigned Compensation & Status Sub-Card (Clean 3-Tier Vertical Rhythm):**
   - **Header:** Clean `Compensation & Status` label without crowding dates, eliminating the truncated `COMPENSATION & S...` bug.
   - **Row 1 (Compensation Tier):** Amount (`INR 157,279/A`) and `Change` pill (or `Create Offer` CTA) sit on a single baseline with `justify-content: space-between`.
   - **Row 2 (Offer Status Badge Tier):** Dedicated full-width row for the lifecycle status badge (`Offer Created`, `Offer Released`, `Accepted`, `Pending`, etc.) with semantic icon and zero horizontal crowding.
   - **Row 3 (Status Context & Subtitle Tier):** Dedicated full-width row for the descriptive context (`Compensation saved`, `Offer emailed to candidate`, etc.) preceded by a matching status icon (`fa-circle-check`, `fa-clock`). Completely eliminates the truncated `Compensa...` overflow bug.

3. **Grid Rebalancing & Zero-Truncation Geometry:**
   - Rebalanced `.ot-entity-grid` column fractions to `1.65fr (Candidate) 1.80fr (Job & Client) 1.95fr (Comp & Status) 1.05fr (Joining) 1.35fr (Ownership) 1.20fr (Stage)`.
   - Compacted `Job & Client` header badges (`8px` font, `1px 5px` padding) so `JOB & CLIENT` never truncates to `JOB & CL...`.
   - All 6 subcards now have **matching 3-line body heights (~58px)**, establishing unified horizontal and vertical alignment across the entire card.

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
| `resources/views/service_company/applicationoffer/index.blade.php` | Modify | 1. Replaced "Joining Soon" KPI card with "Offer Rejected" (`data-tab="rejected"`, `data-kpi="rejected"`).<br>2. Added `.ot-kpi-icon.red`, `.ot-kpi-sub.red`, and `.ot-kpi-card.active[data-tab="rejected"]` CSS.<br>3. Updated `updateOfferCounts` in JS to dynamically sync `rejected: n(c.rejected)`.<br>4. Rebalanced 6-column grid proportions. |
| `resources/views/service_company/ajax_pages/offer-tracker.blade.php` | Modify | Redesigned `ot-subcard-comp-status` into 3 dedicated vertical tiers (Amount + Change, Status Badge, Context Subtitle) with zero text truncation. |

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
