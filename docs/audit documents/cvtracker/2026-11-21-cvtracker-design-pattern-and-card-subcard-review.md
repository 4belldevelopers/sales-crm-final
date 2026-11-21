# Architectural Audit & Design Pattern Review: CV Tracker Modern Card + Sub-Card System

## 1. Document Metadata
- **Module:** Service Company / CV Tracker (`service_company/cvtracker`)
- **Date:** 2026-11-21
- **Author:** JERO Architecture Team (Supergod Mode)
- **Status:** Complete & Certified
- **Applicable Rules:**
  - Rule 1 & 2: Strict CQRS Architecture (0% MySQL Reads, 100% Elasticsearch Reads)
  - Rule 3: Client-First Execution (User RAM + Client NLP + IndexedDB)
  - Rule 7: Zero Business Logic Mutations & Zero Module Regressions
  - Rule 14: Typography & 15.6" Screen Responsive Fit (`1366×768` & `1920×1080`)
  - Rule 17: Complete Page/Blade Isolation
  - Rule 20: Mandatory Card + Sub-Card Enterprise Design System & Header Background Standard
  - Rule 21: Mandatory Audit Location & Module Folder Pattern
  - Rule 24: Default User/Account Timezone with Date & Time Display
  - Rule 25: Canonical Job Classification & Organization Tokens (`My Organization`, `Internal Client`, `Direct Client`)

---

## 2. Executive Summary

A comprehensive architectural and UI/UX review of the **CV Tracker** page (`resources/views/service_company/cvtracker/index.blade.php` and its AJAX card renderer `resources/views/service_company/ajax_pages/all_candidates.blade.php`) was conducted. 

CV Tracker serves as one of the flagship implementations of the **Modern Card + Sub-Card Enterprise Design System (Rule 20)** in JERO. It successfully bridges high-density ATS operational workflows with clean visual hierarchy, replacing legacy sprawling data tables with an interactive 3-tier modular card stream.

---

## 3. Detailed Architecture & Design Pattern Analysis

### A. 3-Tier Layered Architecture

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TIER 1: HEADER & 9-KPI STATUS PIPELINE CARDS                                                                     │
│ • Header Card (.cl-header-card): Icon Box (44×44px, #eff6ff), Title (19px, 700), Subtitle, Export CTA           │
│ • 9 Status KPI Cards (cv-kpi-grid): All (Blue) | InReview (Yellow) | Sourcing (Slate) | Screening (Purple) |      │
│   Evaluation (Sky) | Negotiation (Pink) | Offer (Orange) | Joined (Green) | Rejected (Red)                      │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                       ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TIER 2: TOOLBAR CONTROLS CARD (.cvh-tb-card)                                                                    │
│ [ 1. Search Bar (flex-grow) 🔍 ]  [ 2. Sort Dropdown (220px) ]  [ 3. Org Dropdown (215px) ]  [ 4. Filters Toggle ]│
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                       ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TIER 3: PRIMARY CANDIDATE CARDS STREAM (.simf-cand-card / .aptk-cand-card)                                      │
│ Left Stage Rail: 3.5px solid <StageColor> | Duplicate Border: #ef4444 | Selected Tint: #f8fbff                 │
│ ┌──────────────────┬─────────────────┬──────────────────┬──────────────────┬─────────────────┬────────────────┐ │
│ │ SUB-CARD 1:      │ SUB-CARD 2:     │ SUB-CARD 3:      │ SUB-CARD 4:      │ SUB-CARD 5:     │ SUB-CARD 6:    │ │
│ │ Candidate Info   │ Comp & Exp      │ Location/Degree  │ Job Details      │ Ownership/Added │ Stage & Action │ │
│ │ • Checkbox       │ • Expected CTC  │ • City, Country  │ • Job Title      │ • Recruiter     │ • Stage Badge  │ │
│ │ • Avatar Box     │ • Current CTC   │ • Work Mode      │ • #ID • Cat      │ • Company Name  │ • Substage     │ │
│ │ • Full Name      │ • Total Exp     │ • Qualification  │ • Client Name    │ • Upload Date   │ • Update Cue   │ │
│ │ • Designation    │ • Rel Exp       │   (Uppercase)    │ • Emp Type       │                 │ • Add to Other │ │
│ │ • Current Org    │ • Notice Period │                  │ • Rule 25 Pill   │                 │   Job (CTA)    │ │
│ └──────────────────┴─────────────────┴──────────────────┴──────────────────┴─────────────────┴────────────────┘ │
│ ┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ SUB-CARD 7 (BOTTOM STRIP): [Match 85%] • [Skill] [Skill] (Scrollable) • [Resume ✓] [LinkedIn] [Visa]        │ │
│ └─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Comprehensive Design Tokens & Color Palette

### 1. Stage Rail & Accent Palette
CV Tracker dynamically assigns the primary left-border rail color (`3.5px solid`) based on the candidate's active stage:
| Master Stage | Stage ID | Rail Hex | Pill Background | Pill Text | Pill Border |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **In Review** | 8 | `#eab308` (Yellow) | `#fefce8` | `#854d0e` | `#fef08a` |
| **Sourcing** | 1 | `#0891b2` (Cyan/Slate) | `#ecfeff` | `#0e7490` | `#a5f3fc` |
| **Screening** | 2 | `#a855f7` (Purple) | `#faf5ff` | `#7e22ce` | `#e9d5ff` |
| **Evaluation** | 3 | `#009ef7` (Primary Blue) | `#eff6ff` | `#1d4ed8` | `#bfdbfe` |
| **Negotiation** | 4 | `#ec4899` (Pink) | `#fdf2f8` | `#be185d` | `#fbcfe8` |
| **Offered** | 5 | `#f97316` (Orange) | `#fff7ed` | `#c2410c` | `#fed7aa` |
| **Joined** | 6 | `#10b981` (Emerald) | `#dcfce7` | `#15803d` | `#bbf7d0` |
| **Rejected** | 7, 9 | `#ef4444` (Red) | `#fee2e2` | `#b91c1c` | `#fecaca` |

### 2. Rule 25 Organization Classification Tokens
Sub-Card 4 (`Job Details`) strictly isolates color highlighting to the badge pill while keeping the sub-card container neutral `#f8fafc`:
| Classification | Badge Class | Background | Text Color | Border | Icon |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **My Organization** | `.type-my-org` | `#fef3c7` | `#92400e` | `1px solid #fcd34d` | `fa-id-badge` (`#d97706`) |
| **Internal Client** | `.type-internal` | `#eff6ff` | `#2563eb` | `1px solid #bfdbfe` | `fa-building` (`#2563eb`) |
| **Direct Client** | `.type-direct` | `#ecfdf5` | `#059669` | `1px solid #a7f3d0` | `fa-shield-alt` (`#059669`) |

### 3. Toolbar & Control Tokens
- **Search Bar:** Container flex-grow `1`, min-width `240px`, height `44px`, border `1px solid #e4e6ef`, radius `10px`.
- **Sort Dropdown:** Width `220px`, height `44px`, font size `13px`, font-weight `600`.
- **Organization Dropdown:** Width `215px`, height `44px`, custom dropdown panel with instant checkmarks.
- **Filter Button:** Height `44px`, primary blue badge counter for active filter predicates.

### 4. Mathematical Grid Units (15.6" Screen Fit)
The 6 discrete sub-cards utilize fractional CSS Grid units with `minmax(0, ...)` ensuring mathematical immunity to horizontal overflow:
```css
display: grid;
grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.25fr) minmax(0, 1.1fr) minmax(0, 1.75fr) minmax(0, 1.1fr) minmax(0, 1.15fr);
gap: 8px;
padding: 10px 12px;
width: 100%;
max-width: 100%;
align-items: stretch;
```

---

## 5. Forensic Review Findings & Recommendations

### Key Strengths of CV Tracker
1. **Exceptional Information Density:** Presents over 20 discrete candidate and requisition attributes without visual overwhelm or horizontal scrolling.
2. **Interactive 9-KPI Status Bar:** Acts as an instant pipeline filter with real-time numeric badges and active states.
3. **Multi-Tenant CQRS Isolation:** Fully powered by Elasticsearch read queries (`ServiceCompanyCvTrackerController::allcandidates()`), ensuring 0% MySQL reads on page requests.
4. **Actionable Sub-Card 6:** Seamlessly pairs stage updating with the primary "Add to Other Job" action drawer.

### Identified Improvement & Harmonization Opportunities
1. **Toolbar Header Background Alignment (Rule 20):** 
   - *Current:* `.cl-header.cl-header-card` has `background: #ffffff`.
   - *Standard:* Rule 20 specifies that Tier 1 toolbar cards and drawer headers should use a dedicated `#f8fafc` or `#f1f5f9` header background with border `#e2e8f0` to anchor the top section clearly against the card body.
2. **Date & Timezone Standard (Rule 24):**
   - *Current:* Sub-Card 5 displays `date('d M , Y', strtotime($row->created_at))`.
   - *Standard:* Rule 24 mandates converting timestamps into the account setting default timezone via `GeneralMethod::defaultTimeZone($company_id)` and rendering date, time, and timezone abbreviation (e.g. `21 Nov 26, 08:35 PM IST`).
3. **Skill Chip Wrapping vs Scroll Protection:**
   - Sub-Card 7 uses `overflow-x: auto; scrollbar-width: none;` which keeps the strip clean and 1-line compact.

---

## 6. Conclusion
The CV Tracker page pattern represents the gold standard for candidate listing views across JERO. Its color schemes, 9-status KPI integration, 6 discrete sub-cards, and bottom intelligence strip provide a repeatable blueprint for all future ATS listing refactors.
