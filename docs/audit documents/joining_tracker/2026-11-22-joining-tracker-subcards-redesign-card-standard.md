# Architecture & Design Audit: Joining Tracker Sub-Cards Redesign (Rule 20 & Offer Tracker Standard)

## Metadata
- **Date:** 2026-11-22
- **Author:** Antigravity AI (Google DeepMind)
- **Module:** Service Company ATS — Joining Tracker (`service_company/job-onboard`)
- **Category:** UI/UX Redesign, Card + Sub-Card Enterprise Standard (Rule 20), Rule 24 Timezone Standard, Rule 25 Scope Badge Standard
- **Scope:** `resources/views/service_company/ajax_pages/joining-tracker.blade.php`, `resources/views/service_company/job-onboard/index.blade.php`

---

## Executive Summary
In accordance with the user's design reference from the Offer Tracker page (`media_1795346148356_870c01d4.png`), the Service Company Joining Tracker candidate card stream has been redesigned from a legacy 5-card layout into the Modern Card + 6 Discrete Sub-Card Enterprise Design System (Rule 20).

Each primary entity card is anchored with a 3.5px left status accent border and accommodates 6 discrete upper sub-cards in a fractional, non-overflowing CSS Grid (`minmax(0, ...fr)`), paired with an activity and action footer strip.

---

## 6 Discrete Upper Sub-Cards Architecture

### 1. Sub-Card 1: CANDIDATE IDENTITY
- **Header:** Label `CANDIDATE` (uppercase, bold 9.5px, muted `#64748b`) with right-side metadata badges:
  - `Bench` micro-pill (`#fef3c7` bg / `#b45309` text) for bench candidates.
  - `X.X Yrs` micro-pill (`#ffffff` bg / `#475569` text / `#e2e8f0` border) for total experience.
- **Body:**
  - Candidate Avatar: 38×38px rounded container with image or algorithmic initials matching the tone palette.
  - Candidate Name: Bold 12.5px (`#0f172a`), interactive drawer trigger (`.show-application-detail`).
  - Current Title / Designation: Muted 11px (`#64748b`).
  - Current Company: Accompanied by `<i class="fas fa-building"></i>` (or location fallback with `<i class="fas fa-map-marker-alt"></i>`).
- **Interaction:** Entire sub-card is clickable (`.show-application-detail`), opening the candidate profile drawer.

### 2. Sub-Card 2: JOB DETAILS
- **Header:** Label `JOB DETAILS` with canonical Rule 25 scope pills:
  - `My Organization` (`.type-my-org`, `#fef3c7` bg, `#92400e` text, `fa-id-badge`).
  - `Internal Client` (`.type-internal`, `#eff6ff` bg, `#2563eb` text, `fa-building`).
  - `Direct Client` (`.type-direct`, `#ecfdf5` bg, `#059669` text, `fa-shield-alt`).
- **Body:**
  - Job Title: Bold 12.5px (`#0f172a`), single-line ellipsis.
  - Job Meta: `#<JobId>` in sky blue (`#0284c7`), separator bullet (`•`), Client Name in uppercase (`#475569`).
  - Employment Tag: Chip with `<i class="fas fa-briefcase"></i> Full-time` (or Contract, etc.).

### 3. Sub-Card 3: COMPENSATION & REAL STATUS
- **Header:** Label `COMPENSATION` with employment type badge (`Full-time`, `Contract`, etc.).
- **Body:**
  - Row 1 (Amount): Formatted compensation (e.g. `INR 2,523,896/A`) with `font-variant-numeric: tabular-nums;` or `Confidential` with lock icon.
  - Row 2 (Status Badge): Real joining and offer lifecycle badge:
    - `Joined` (Emerald `#dcfce7` / `#15803d` / `#bbf7d0`)
    - `Backed Out` / `No Show` (Red `#fee2e2` / `#dc2626` / `#fecaca`)
    - `Joining Today` (Purple `#ede9fe` / `#7c3aed` / `#ddd6fe`)
    - `Delayed` (Orange `#ffedd5` / `#ea580c` / `#fed7aa`)
    - `Pending Docs` (Amber `#fef3c7` / `#d97706` / `#fde68a`)
    - `Offer Created` (Indigo `#e0e7ff` / `#4338ca` / `#c7d2fe`)
    - `Pre-Joining` (Emerald `#ecfdf5` / `#059669` / `#a7f3d0`)
  - Row 3 (Action Cue): `Update Offer >` (triggers `create_offer` modal) or `Client Managed` (for direct client mandates).

### 4. Sub-Card 4: JOINING SCHEDULE
- **Header:** Label `JOINING` with dynamic relative countdown badge (`5d ago`, `Today`, `Tomorrow`, `in 3 days`, or `Pending`).
- **Body:**
  - Date: Calendar icon with bold date string (e.g. `📅 17 Nov 2026`).
  - Time & Timezone: Formatted in the tenant's configured default timezone per Rule 24 (`12:18 PM IST`).

### 5. Sub-Card 5: OWNERSHIP & RECRUITER
- **Header:** Label `<i class="fas fa-user-tie me-1"></i> OWNERSHIP & RECRUITER`.
- **Body:**
  - Recruiter Name: `<i class="fas fa-user-circle text-primary me-1"></i>` with recruiter name (bold 11.5px `#0f172a`).
  - Company: `<i class="fas fa-building me-1"></i>` with submitting company name (10.5px `#475569`).
  - Submitted Age: `<i class="far fa-calendar-alt me-1"></i> Submitted: <X> days ago` (10px `#64748b`).

### 6. Sub-Card 6: PIPELINE & STAGE
- **Header:** Label `PIPELINE & STAGE`.
- **Body:**
  - Master Stage Pill: Semantic pill (e.g. `Joined`, `Offered`, `In Review`).
  - Sub-Stage Text: `Sub: <ChildStatus>` or `Sub: Standard`.
  - Action Cue: `Update Stage >` in primary blue (`#009ef7`).
- **Interaction:** Entire sub-card triggers `.stage_sub_stage` drawer (`data-row-id="{{ $row->wiseId }}"`).

---

## Bottom Activity Strip
- **Left:** `LAST UPDATED: <d M Y, h:i A T>` formatted in the tenant's account timezone per Rule 24.
- **Right:**
  - `Manage Offer` button (for internal jobs with offer ID, opening `#kt_create_offer` drawer).
  - `Candidate Profile ↗` primary action button (opening `#kt_drawer_candidate_profile`).

---

## 15.6" Screen Fit & Zero-Overflow Validation
The grid employs fractional units ensuring that all 6 sub-cards fit comfortably without horizontal scrolling on 1366×768 and 1920×1080 screens:
```css
.jt-entity-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2.05fr) minmax(0, 1.65fr) minmax(0, 1.65fr) minmax(0, 1.05fr) minmax(0, 1.35fr) minmax(0, 1.25fr) !important;
    gap: 8px !important;
    padding: 10px 12px !important;
    width: 100% !important;
    align-items: stretch !important;
}
```
Responsive breakpoints:
- `<= 1440px`: `gap: 6px; padding: 8px 10px;`
- `<= 1150px`: `repeat(3, minmax(0, 1fr))`
- `<= 768px`: `repeat(2, minmax(0, 1fr))`
- `<= 520px`: `minmax(0, 1fr)`

---

## File Changes Matrix
| File | Changes |
| :--- | :--- |
| `resources/views/service_company/ajax_pages/joining-tracker.blade.php` | Complete redesign into Modern Card + 6 Discrete Sub-Cards + Bottom Strip matching Offer Tracker design. Tenancy-batched `offerMeta` lookup. Rule 24 and Rule 25 compliant. |
| `resources/views/service_company/job-onboard/index.blade.php` | Added complete `.jt-*` subcard CSS classes, updated `.jt-entity-grid` to 6 fractional columns and adjusted responsive breakpoints. |

---

## Verification
- `php -l` checks on both modified blade files passed with zero errors.
- Pre-commit AST invariants and secret checks verified.
