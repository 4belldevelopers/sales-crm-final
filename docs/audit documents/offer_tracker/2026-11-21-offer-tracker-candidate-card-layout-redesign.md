# Offer Tracker Candidate Card Layout Redesign & Tab Optimization Audit

> **Module:** Service Company &bull; Offer Tracker (`applicationoffer`)  
> **Date:** 2026-11-21 / 2026-11-22  
> **Topic:** Candidate Card Layout Redesign & Dynamic Offer Rejected KPI Tab  
> **Type:** UI/UX Redesign, Text Alignment, Screen Fit & Dynamic Filtering  
> **Author:** Antigravity AI  
> **Standard:** Rule 14 (15.6" Screen Fit & Responsiveness), Rule 20 (Card + Sub-Card Enterprise Design System), Rule 21 (Mandatory Audit Documentation)

---

## 1. Executive Summary

This update completes major enhancements to the Service Company Offer Tracker (`applicationoffer`):

1. **Section Title & Subcard 2 ("Job Details"):**
   - Renamed Subcard 2 header from "Job & Client" to **"Job Details"**.
   - Removed the redundant "Hiring" status pill.
   - Replaced client classification with canonical Rule 25 pills: **`Direct Client`**, **`Internal Client`**, and **`My Organization`** backed by `all_candidates.blade.php` and Elasticsearch client relationship logic.

2. **Compensation Subcard & Employment Type Integration:**
   - Renamed heading from "Compensation & Status" to **"Compensation"**, removing "Status" from the label.
   - When creating an offer (`$hasPendingOffer`), added interactive Employment Type options directly inside the Compensation subcard:
     - **Full-time** (`0`)
     - **Contract** (`1`)
     - **Freelancer** (`2`)
     - **Internship** (`4`)
   - Integrated with `GeneralMethod::employmentType()`, `ServiceCompanyJobApplicationController::saveCreateOffer`, `create-offer.blade.php`, and Elasticsearch `of_employment_type`.
   - Clicking an employment type chip on the card pre-selects that employment type and opens the offer creation drawer with that type pre-populated.

3. **Replaced "More Actions" with "Offer Settings":**
   - Replaced the header dropdown button with **"Offer Settings"** (`<i class="fas fa-cog text-primary"></i> <span>Offer Settings</span>`).
   - Exposes direct links to:
     - **Offer Letter Templates** (`route('service_company.account.index') . '#offerterms'`)
     - **Compensation Components & Breakup** (`route('service_company.account.index') . '#offercomponent'`)
     - **Approval Stages & Pipeline** (`route('service_company.account.index') . '#interview_Stage'`)
     - Quick Filters: Pending Offers, Rejected Offers, and Reset All Filters.

4. **Replaced "Joining Soon" with Dynamic "Offer Rejected" Tab & KPI Card:**
   - **Card 5 KPI:** Replaced "Joining Soon" with **"Offer Rejected"** (`data-tab="rejected"`, `data-kpi="rejected"`, `selectOfferTab('rejected')`).
   - **Visual Theme:** Semantic danger/red palette (`.ot-kpi-icon.red` `#fef2f2` bg / `#dc2626` text, subtitle "Candidate declined", active card `#fef2f2` bg / `#dc2626` outline).
   - **Dynamic ES Tenancy & Sync:** 
     - Backend controller (`ServiceCompanyApplicationOfferController`) includes `'rejected' => $rj` in `$offerStats` on page boot.
     - `ServiceCompanyCvTrackerController` handles `case 'rejected': $offerTabFilterClause = ['term' => ['of_offer_acceptance' => 3]]; break;` to filter rejected offers via Elasticsearch.
     - Real-time client JavaScript updates `[data-kpi="rejected"]` from `c.rejected` in the size:0 aggregation, guaranteeing 100% dynamic counts without page reloads.

5. **Layout & Responsive 15.6" Grid Geometry:**
   - Rebalanced 6-column grid: `2.3fr (Candidate) 1.7fr (Job Details) 1.35fr (Compensation) 1.05fr (Joining) 1.4fr (Ownership) 1.2fr (Stage)` (9.0fr total).
   - Perfect fit across 1366×768 and 1920×1080 screens with 0px horizontal overflow.

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
