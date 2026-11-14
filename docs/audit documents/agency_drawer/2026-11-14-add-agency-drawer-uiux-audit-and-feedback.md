# Forensic UI/UX Audit & Design Feedback: Add Agency Drawer

> **Module:** Service Company Jobs Tracker / Add Agency Drawer (`testing.jero.works/service_company/jobs`)  
> **Audited View:** `resources/views/service_company/jobs/spartnerlisting.blade.php`  
> **Commit Hash:** `57ece9be`  
> **Date:** 2026-11-14  
> **Governing Standards:** Rule 14 (15.6" Screen Fit), Rule 20 (Mandatory Card + Sub-Card Enterprise Design System & Header Background Standard), Rule 21 (Module Audit Storage Pattern), Rule 25 (Canonical Scope Pills).

---

## 1. Executive Summary

The transition of the **Add Agency Drawer** from sprawling HTML tables to the **3-Tier Card + Sub-Card Enterprise Architecture** delivers exceptional clarity, clean separation of concerns, and intuitive navigation.

The screen successfully bifurcates the agency acquisition workflow into two parallel, non-competing funnels:
1. **Funnel 1 (Left 34% Column):** Self-service Broadcast to the JERO Agency Marketplace.
2. **Funnel 2 (Right 65% Column):** High-touch Direct Outreach to Recommended, Registered, and Assigned Agencies.

### System Scorecard: **89 / 100 (Enterprise Grade A-)**

- **Rule 20 Compliance:** 94 / 100
- **15.6" Responsiveness (`1366×768` & `1920×1080`):** 90 / 100
- **Typography & Data Ergonomics:** 92 / 100
- **Micro-interactions & Affordances:** 82 / 100

---

## 2. Key Observations & Actionable Recommendations

### 2.1 Sticky Top Job Summary Card
- **Observation:** The card renders clean metadata (scope pill `My Organization`, `HIRING` status, `3-8 Years`, `€ 216.56-389.81 / M`, `5 Openings`, and skill chips). However, its background is currently plain `#ffffff`.
- **Recommendation (Rule 20):** Apply a dedicated header background (`background: #f8fafc; border: 1px solid #e2e8f0;`) to clearly demarcate the sticky entity summary from the interactive canvas underneath. Add tabular numbers formatting to the compensation figure.

### 2.2 Marketplace Broadcast Card (Column 1)
- **Observation:** Provides a clear configuration workflow with `Agency Acceptance Limit` and `Auto-expire` dropdowns.
- **Recommendation:**
  - Convert the static "Open to Agency Applications" sub-card into a **Dynamic Status Card** (showing a gray pill `"Unpublished"` vs. a pulsing green pill `"Live on Marketplace"`).
  - Replace the plain `>` symbol in the CTA button (`> Publish to Agency Marketplace`) with an SVG broadcast/rocket icon (`<svg ...>`).
  - Style the helper `(?)` icons with Metronic tooltips for explanation of vendor limits.

### 2.3 Direct Outreach Toolbar & Sub-Tabs (Column 2)
- **Observation:** The Tagify-powered skill search bar (`Redis`, `PHP 8.2`, `Laravel 11`, `Elasticsearch`) and the sub-tabs (`Recommended (14)`, `My Agencies (4)`, `Assigned (0)`) function smoothly.
- **Recommendation:**
  - Wrap tab counts in soft translucent badge pills (`background: rgba(255,255,255,0.25);`) on the active tab for improved visual pop.
  - Enhance the `Select All` control on the right from a faint gray toggle into an anchored, clear bulk-action pill button.
  - Add an active filter indicator badge on the `Filter` button.

### 2.4 Primary Agency Cards (`.agency-entity-card`)
- **Observation:** Discrete sub-cards 1 to 5 provide comprehensive agency metrics, rates, and contact details.
- **Recommendation:**
  - **Deduplicate Repeated Column Headers:** The uppercase labels `PROFILES`, `RATES & TERMS`, and `LOCATION & TEAM` currently repeat inside every single agency card. Moving them to a single sticky header row above the card stream reduces visual repetition and focuses attention on the data.
  - **Match 0% Styling:** Replace gray `Match 0%` with a neutral `Additional Agency` pill, reserving green for positive matching scores.
  - **Action Button Color:** Switch `Send Requirement` from green to JERO primary blue (`#009ef7`), reserving green for `✓ Request Sent` to maintain consistent semantic color standards.
  - **Tactile Hover Effect:** Add a subtle `transform: translateY(-1px)` and soft shadow on card hover for enhanced desktop interactivity.

---

## 3. Verification & Tenancy Guard

- **15.6" Ergonomics:** Zero horizontal page scroll on `1366×768`. Internal scrolling handles long agency streams cleanly.
- **Tenancy:** All actions strictly scoped to `auth()->user()->company_id`.
- **Code Freeze:** Zero edits to `product_company` or `staffing_company` modules.
