# Dedicated Location & Coverage Sub-Card and Vertical Talent Supply Specification

**Date:** 15 November 2026  
**Module:** Service Company Marketplace (`/service_company/find-agency`)  
**Standard Compliance:** Rule 14 (15.6" Screen Fit `1366x768` & `1920x1080`), Rule 20 (Card + Sub-Card Enterprise Standard with Dedicated Header Standard), ADR 0008 (Client-First Execution).

---

## 1. Executive Summary & Problem Addressed

Per **User Request & Visual Reference (`media_1794729222342_f5b8f51b.png`)**, the marketplace agency listing cards have undergone a forensic UI refactor to align 100% with the enterprise design standard established in the client tracker and consultancy modules:
1. **Agency Identity (Sub-Card 1):** Header updated to feature clean `[MARKETPLACE]` / `[CONNECTED]` and `[● ACTIVE]` status pills. The body matches the reference Tesla layout: 38px logo on the left, with 3 clean stacked lines on the right: (1) Company Name, (2) Industry (e.g. `Staffing & Recruiting`), and (3) Company Size (e.g. `5,001 - 10,000 employees`). The ambiguous `Accredited` / `Partner` pill has been completely removed.
2. **Location (Sub-Card 2):** Header displays `LOCATION` with country reach tooltip. Body features 3 clean lines: (1) `📍 City` (bold), (2) `State`, and (3) `Country`.
3. **Talent Supply (Sub-Card 4):** Restyled to match the clean 3-line vertical rhythm of the Location card: (1) `💼 X Active Jobs`, (2) `👥 Y Candidate Profiles`, and (3) `⚡ Z Pre-Vetted Bench 👁️` (interactive drawer trigger when $> 0$). The placeholder note `Domain verified candidate pool` has been completely removed.
4. **Decent Enterprise Height Reduction:** Eliminated bulky negative-margin `#f1f5f9` top header bars in favor of a sleek 18px header row. Reduced subcard padding to 7px 9px, locking subcard height to ~80px and overall card height to ~120px.

---

## 2. 5-Sub-Card Grid Architecture (Rule 20)

```
┌─────────────────┬─────────────────┬─────────────────┬───────────────────────────────┬──────────────────────────────────┐
│ SUB-CARD 1:     │ SUB-CARD 2:     │ SUB-CARD 3:     │ SUB-CARD 4:                   │ SUB-CARD 5:                      │
│ Agency Identity │ Location        │ Commercials     │ Talent Supply                 │ Partnership                      │
│ [MKT] [● ACTIVE]│ [Header 18px]   │ [Pre-Agreed]    │ [Live Pool]                   │ [Connect]                        │
│                 │                 │                 │                               │                                  │
│ • Logo Box 38px │ • 📍 City (Bold)│ • Full-Time CTC │ • 💼 5 Active Jobs            │ • [ Send Request ] CTA Button    │
│ • Company Name  │ • State         │ • Contract / Hr │ • 👥 8 Candidate Profiles     │ • ⚡ Instant SLA Alert Footnote  │
│ • Industry      │ • Country       │                 │ • ⚡ 4 Pre-Vetted Bench 👁️    │                                  │
│ • Company Size  │                 │                 │                               │                                  │
├─────────────────┴─────────────────┴─────────────────┴───────────────────────────────┴──────────────────────────────────┤
│ BOTTOM FOOTER ACTIVITY STRIP: ● Active <time> • ● Avg SLA: ~36-48h • 👥 Capacity: 8 Recruiters • Accredited • View →   │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Sub-Card 1: Agency Identity (`.cl-subcard-identity`)
- **Header:** Title `Agency Identity` with `[MARKETPLACE]` (or `[CONNECTED]`) and `[● ACTIVE]` pills.
- **Body:**
  - 38px square logo box with rounded border and subtle shadow.
  - Line 1: Agency Name (bold dark text, linked to client details).
  - Line 2: Industry (`Staffing & Recruiting`).
  - Line 3: Dynamic Company Size (e.g. `5,001 - 10,000 employees`).
  - `Accredited` pill removed.

### Sub-Card 2: Location (`.cl-subcard-loc`)
- **Header:** Title `Location` with interactive `X Countries` serving tooltip badge.
- **Body:**
  - Blue location pin `fas fa-map-marker-alt text-primary`.
  - Line 1: `City` (bold dark text).
  - Line 2: `State` (secondary slate text).
  - Line 3: `Country` (light muted text).

### Sub-Card 3: Commercials (`.cl-subcard-rates`)
- **Header:** Title `Commercials` with `Pre-Agreed` pill.
- **Body:** Compact adaptive dual or single commercial rate boxes.

### Sub-Card 4: Talent Supply (`.cl-subcard-talent`)
- **Header:** Title `Talent Supply` with `Live Pool` pill.
- **Body (Matching Location 3-Line Rhythm):**
  - Line 1: `💼 <count> Active Jobs`
  - Line 2: `👥 <count> Candidate Profiles`
  - Line 3: `⚡ <count> Pre-Vetted Bench 👁️` (interactive drawer trigger when $> 0$, disabled text when 0).
  - `Domain verified candidate pool` note removed.

### Sub-Card 5: Partnership & Action (`.cl-subcard-action-hub`)
- **Header:** Title `Partnership` with `Connect` pill.
- **Body:** Compact CTA button (`Send Request`, `Cancel Request`, `Connected`) + `Instant SLA Alert` footnote.

### Card Bottom Activity Strip (`.cl-card-footer-strip`)
- Tenant timezone-converted last active timestamp (Rule 24).
- Average SLA turnaround (`~36-48h First TAT`).
- Recruiter team capacity (`Capacity: X Active Recruiters`).
- Marketplace accreditation status badge (`Marketplace Accredited` or `Onboarding Partner`).
- Right-anchored dual actions:
  - `📄 Agency Terms` link (triggers `#drawer_agency_terms_view`).
  - `View Full Profile →` link (opens client dossier).

---

## 3. Responsive 15.6" Screen Fit Matrix (Rule 14 & Rule 20)

| Screen Resolution | Available Container Width | Sub-Card Grid Layout | Result / Overflow Check |
| :--- | :--- | :--- | :--- |
| **1920 × 1080 (FHD)** | ~1420px – 1650px | `1.4fr 1.05fr 1.25fr 1.15fr 1.15fr` | Perfectly balanced; 0 overflow; card height ~120px |
| **1366 × 768 (15.6")**| ~1040px – 1100px | `1.35fr 1.05fr 1.25fr 1.15fr 1.15fr` (gap 7px) | Compact, crisp, 0 horizontal scroll |
| **Tablet (≤ 1200px)** | ~880px – 960px | `repeat(3, 1fr)` wrapping | Clean multi-row layout without clipping |
| **Mobile (≤ 992px)**  | ≤ 768px | `repeat(2, 1fr)` | Fully responsive vertical stacking |

---

## 4. Verification & Testing

1. **PHP Syntax Check (`php -l`):**
   - `app/Services/FindAgencyService.php`: Pass (0 errors)
   - `resources/views/service_company/ajax_pages/find_agency_ajax.blade.php`: Pass (0 errors)
   - `resources/views/service_company/common/find-agency.blade.php`: Pass (0 errors)
2. **Pre-Commit AI Guard Check:** Clean; 0 `dd()`, 0 `dump()`, 0 `ray()`, 0 `env()` outside config.
3. **Cache Flush:** `view:clear` and `cache:clear` executed successfully.
