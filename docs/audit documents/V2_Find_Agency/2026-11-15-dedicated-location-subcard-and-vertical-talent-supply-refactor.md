# Dedicated Location & Coverage Sub-Card and Vertical Talent Supply Specification

**Date:** 15 November 2026  
**Module:** Service Company Marketplace (`/service_company/find-agency`)  
**Standard Compliance:** Rule 14 (15.6" Screen Fit `1366x768` & `1920x1080`), Rule 20 (Card + Sub-Card Enterprise Standard with Dedicated Header Backgrounds), ADR 0008 (Client-First Execution).

---

## 1. Executive Summary & Problem Addressed

In the previous iteration of the Service Company Find Agency page, the primary agency card featured 4 sub-cards where:
1. **Sub-Card 1 (Identity & Location)** combined the agency logo, company name, industry specialization, geographic location (City, State, Country), and serving countries badge into a single column. This compressed the text hierarchy on standard enterprise screens (`1366x768`).
2. **Sub-Card 3 (Jobs & Talent Supply)** presented the pipeline metrics (`Jobs`, `Profiles`, `Bench 👁️`) in a 3-column horizontal mini-grid, which squeezed numbers and labels into tight boxes.

To achieve maximum enterprise clarity, readability, and visual balance per **User Feedback #10**, this refactor implements:
1. **Separate Dedicated Sub-Card for Location & Countries Serving (`.cl-subcard-location`)**: Isolates physical location hierarchy (`City`, `State`, `Country`) and global reach (`Serving X Countries` with interactive hover tooltips) into its own dedicated sub-card.
2. **Vertical Listings for Jobs & Talent Supply (`.cl-talent-vertical-list`)**: Transforms pipeline metrics into clean, full-width vertical stacked rows with distinct semantic colored badges and interactive triggers.

---

## 2. 5-Sub-Card Grid Architecture (Rule 20)

The primary agency card stream (`.cl-primary-card.tbl-r`) now features a perfectly balanced 5-tier sub-card layout:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TOP TRUST & SYNERGY RIBBON: Specialization / Skill Chips | 🛡️ 90-Day Free Replacement | 🎯 100% Contingent             │
├─────────────────┬─────────────────┬─────────────────┬───────────────────────────────┬──────────────────────────────────┤
│ SUB-CARD 1:     │ SUB-CARD 2:     │ SUB-CARD 3:     │ SUB-CARD 4:                   │ SUB-CARD 5:                      │
│ Agency Identity │ Location & Reach│ Commercials     │ Jobs & Talent Supply          │ Partnership & Action             │
│ [Header #f1f5f9]│ [Header #f1f5f9]│ [Header #f1f5f9]│ [Header #f1f5f9]              │ [Header #f1f5f9]                 │
│                 │                 │                 │ (Vertical Stacked Rows)       │                                  │
│ • Logo/Initials │ • City (Bold)   │ • Full-Time CTC │ • 💼 Active Jobs: 5           │ • Capacity: 8 Recruiters         │
│ • Company Name  │ • State, Country│ • Contract / Hr │ • 👥 Talent Profiles: 12      │ • [ Send Request ] Button        │
│ • Industry Pill │ • 🌐 Serving X  │ • View Agency   │ • ⚡ Pre-Vetted Bench: 2 👁️   │ • Instant SLA Alert Footnote     │
│ • Accredited Tag│   Countries (i) │   Terms Drawer  │ • Domain verified pool        │                                  │
├─────────────────┴─────────────────┴─────────────────┴───────────────────────────────┴──────────────────────────────────┤
│ BOTTOM FOOTER ACTIVITY STRIP: ● Active <timestamp>  •  ● Avg SLA: ~36-48h First TAT  •  Marketplace Accredited  • View →│
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Sub-Card 1: Agency Identity (`.cl-subcard-identity`)
- **Header:** `<i class="fas fa-shield-alt text-primary me-1"></i>Agency Identity` with Superadmin Verified badge (`companies.accountVerify == 1`).
- **Body:** Rounded company logo (with dynamic fallback colored initials), company name linked to client details, industry taxonomy pill, and partnership status pill (`Accredited Partner` or `Direct Partner`).

### Sub-Card 2: Location & Coverage (`.cl-subcard-location`)
- **Header:** `<i class="fas fa-map-marker-alt text-danger me-1"></i>Location & Coverage` with 2-letter country code pill (`IN`, `US`, etc.).
- **Body:**
  - City highlighted in bold dark font (`font-weight-700`).
  - State and Country displayed clearly underneath.
  - Interactive serving countries pill with Bootstrap hover tooltip (`Serving X Countries`) listing all serving territories.

### Sub-Card 3: Commercials & Terms (`.cl-subcard-rates`)
- **Header:** `<i class="fas fa-file-invoice-dollar text-success me-1"></i>Commercials` with `Pre-Agreed` pill.
- **Body:** Adaptive commercial cards supporting:
  - Dual Mode (Full-Time % CTC + Contract Rate / Hr or / Mo).
  - Permanent Only Mode.
  - Contract Only Mode.
  - Interactive sliding drawer trigger: `📄 View Agency Terms →` opening `#drawer_agency_terms_view`.

### Sub-Card 4: Jobs & Talent Supply (`.cl-subcard-talent`)
- **Header:** `<i class="fas fa-users-cog text-warning me-1"></i>Jobs & Talent Supply` with `Live Pool` pill.
- **Body (Vertical Stacked Rows):**
  - Row 1: `💼 Active Jobs: <count>`
  - Row 2: `👥 Talent Profiles: <count>`
  - Row 3: `⚡ Pre-Vetted Bench: <count> Bench 👁️` (Context-aware: highlighted and clickable to open `#drawer_agency_bench_preview` when bench $> 0$, disabled when 0).
  - Sub-note: `✓ Domain verified candidate pool`.

### Sub-Card 5: Partnership & Action Hub (`.cl-subcard-action-hub`)
- **Header:** `<i class="fas fa-handshake text-info me-1"></i>Partnership & Action` with `Connect` pill.
- **Body:**
  - Recruiter team capacity: `Capacity: X Active Recruiters`.
  - Primary context-aware CTA button: `Send Request` (Primary Blue), `Cancel Request` (Light Danger), `Connected` (Light Success), or `Rejected`.
  - SLA footnote: `Instant SLA Alert to Lead Partner`.

### Card Bottom Activity Strip (`.cl-card-footer-strip`)
- Tenant timezone-converted last active timestamp (Rule 24).
- Average SLA turnaround (~36-48h First TAT).
- Marketplace accreditation status.
- Right-anchored `View Full Profile →` link.

---

## 3. Responsive 15.6" Screen Fit Matrix (Rule 14 & Rule 20)

| Screen Resolution | Available Container Width | Sub-Card Grid Layout | Result / Overflow Check |
| :--- | :--- | :--- | :--- |
| **1920 × 1080 (FHD)** | ~1420px – 1650px | `1.35fr 1.15fr 1.35fr 1.15fr 1.15fr` | Perfectly balanced; 0 overflow |
| **1366 × 768 (15.6")**| ~1040px – 1100px | `1.3fr 1.1fr 1.3fr 1.1fr 1.1fr` (gap 6px) | Compact, crisp, 0 horizontal scroll |
| **Tablet (≤ 1200px)** | ~880px – 960px | `repeat(3, 1fr)` wrapping | Clean 2-row layout without clipping |
| **Mobile (≤ 992px)**  | ≤ 768px | `repeat(2, 1fr)` | Fully responsive vertical stacking |

---

## 4. Verification & Testing

1. **PHP Syntax Check (`php -l`):**
   - `resources/views/service_company/ajax_pages/find_agency_ajax.blade.php`: Pass (0 errors)
   - `resources/views/service_company/common/find-agency.blade.php`: Pass (0 errors)
2. **Pre-Commit AI Guard Check:** Clean; 0 `dd()`, 0 `dump()`, 0 `ray()`, 0 `env()` outside config.
3. **Cache Flush:** `view:clear` and `cache:clear` executed successfully.
