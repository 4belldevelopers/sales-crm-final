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

### 3. Sub-Card 3: COMPENSATION (View-Only)
- **Header:** Label `COMPENSATION` with employment type badge (`Full-time`, `Contract`, etc.).
- **Body:**
  - Row 1 (Amount): Formatted compensation (e.g. `INR 2,523,896/A`) with `font-variant-numeric: tabular-nums;` or `Confidential` with lock icon.
  - Row 2 (Status Badge): `Offer Accepted` (`#ecfdf5` bg / `#059669` text / `#a7f3d0` border) confirming the agreed salary.
  - Row 3 (Package Detail): View-only package mode (e.g. `Yearly Package`).
- **Interaction:** Strictly view-only. Zero `Update Offer` buttons or modal triggers (offer drawer is exclusively scoped to the Offer Stage).

### 4. Sub-Card 4: JOINING SCHEDULE
- **Header:** Label `JOINING` with dynamic relative countdown badge:
  - `Joined` (green `#dcfce7` / `#15803d`): Displayed when candidate onboarding is completed (`of_joined == 1` or master stage is `Joined`/`Onboarded`).
  - `No Show` / `Backed Out` (red `#fee2e2` / `#dc2626`): Candidate declined after offer acceptance.
  - `Today` (purple `#ede9fe` / `#7c3aed`): Candidate scheduled to join on the current date.
  - `in X days` (blue/amber): Future scheduled joining countdown.
  - `Xd ago` (neutral `#f1f5f9` / `#64748b` with subtle `#e2e8f0` border): Neutral relative elapsed time for past dates (zero "Delayed" or "Reschedule" warning pills).
  - `Not Scheduled`: Unscheduled fallback.
- **Body:**
  - Date: Calendar icon with bold date string (e.g. `📅 17 Nov 2026`).
  - Time: Always displays the scheduled time formatted in the tenant's configured default timezone per Rule 24 (e.g. `12:18 PM IST`). Zero warning states, zero exclamation icons, and zero "Reschedule Pending" text.

---

### Removal of "Delayed" Options & Reschedule Pending
Per explicit user directive:
1. **Sub-Card 4 Badge:** Replaced orange "Delayed" pill badge with neutral elapsed time badge (e.g. `4d ago` in `#f1f5f9`/`#64748b`) or `Joined` if master stage is `Joined`/`Onboarded`.
2. **Sub-Card 4 Time:** Completely removed `Reschedule Pending` label and orange warning icon. The second line now cleanly renders the tenant-formatted joining time (`12:18 PM IST`).
3. **Card Left Accent Border:** Removed orange `#ea580c` for overdue dates. Accent colors now strictly map to authoritative states: `#10b981` (Joined), `#ef4444` (Backed Out), `#7c3aed` (Today), `#d97706` (Pending Docs), `#6366f1` (Offer Created), or `#009ef7` (Pre-Joining).
4. **More Actions Dropdown:** Removed legacy "Review Delayed Joinings / Reschedule & follow-up queue" item and replaced with direct filters for "Joined & Onboarded" (`selectJoiningTab('onboarded')`) and "Joined & Backedout" (`selectJoiningTab('backed_out')`).

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
  - `Candidate Profile ↗` primary action button (opening candidate profile drawer).
  - (Offer drawer actions are omitted in Joining Tracker as offer generation is restricted to the Offer stage).

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
| `resources/views/service_company/ajax_pages/joining-tracker.blade.php` | Complete redesign into Modern Card + 6 Discrete Sub-Cards + Bottom Strip matching Offer Tracker design. Removed `Delayed` badge, `Reschedule Pending` label, and orange accent border. Corrected `Joined` state detection via `$isCandidateJoined`. Tenancy-batched `offerMeta` lookup. Rule 24 and Rule 25 compliant. |
| `resources/views/service_company/job-onboard/index.blade.php` | Added complete `.jt-*` subcard CSS classes, updated `.jt-entity-grid` to 6 fractional columns. Removed `.badge-delayed` and replaced "Review Delayed Joinings" dropdown item with "Joined & Onboarded" and "Joined & Backedout". |

---

## Verification
- `php -l` checks on both modified blade files passed with zero errors.
- Pre-commit AST invariants and secret checks verified.
