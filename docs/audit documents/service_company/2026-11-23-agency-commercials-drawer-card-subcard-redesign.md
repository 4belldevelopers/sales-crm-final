# Forensic Audit & Implementation Record: Agency Commercials Drawer Redesign (Rule 20 & Header Actions)

**Document ID:** `AUDIT-SC-2026-11-23-AGENCY-COMMERCIALS-DRAWER-RESTRUCTURE`  
**Date:** 2026-11-23  
**Module:** Service Company Account Settings (`/service_company/account#terms`)  
**Feature:** Edit Fulltime & Contract Commercials for Agencies Sliding Drawer  
**Governing Standard:** Rule 20 (Mandatory Card + Sub-Card Enterprise Standard & Header Background Standard), Rule 14 (15.6" Screen Fit: 1366×768 & 1920×1080), Rule 7 (Zero Business Logic Mutations), Rule 26 (/unslop Craft Standard)

---

## 1. Executive Summary

The agency commercial terms configuration drawer allows service companies to define recruitment commercials (Full-Time placement fee model, default pricing, experience bands, guarantee replacement periods, billing timelines, and contract staffing rates) for agency partners.

### Core Problems Identified
1. **Unanchored Bottom Action Bar:** The "Save & Update" and "Cancel" buttons were previously located in a floating/sticky drawer footer at the bottom of the screen (`.aat-drawer-footer`). Users on 15.6" laptop screens (`1366×768` and `1920×1080`) had to scroll to the very bottom past multiple lengthy sections or experience tables to commit their changes.
2. **AI Slop & Visual Inconsistencies:** The drawer contained purple/violet gradients (`linear-gradient(135deg,#fbfcff,#f8f7ff)`), glowing borders, unanchored section headers, and legacy Metronic styling that violated Rule 26 (/unslop) and Rule 20.
3. **Screen Fit & Padding Issues:** Fixed bottom padding (82px) reserved for the old footer was redundant once buttons moved to the header.

### Implemented Solutions
1. **Header Action Placement:** Relocated both the `#cancelAgencyCommercialTerms` (`.cd-btn-outline`) and `#saveAgencyCommercialTerms` (`.cd-btn-primary.save`) buttons into the sticky drawer topbar (`.aat-drawer-head > .aat-drawer-actions`) directly alongside `#closeAgencyCommercialDrawer`. Users can now save or dismiss changes instantly from any scroll position.
2. **Modern Card + Sub-Card System (Rule 20):** Every section is now wrapped as a distinct container card with mandatory `#f8fafc` header background, `#e2e8f0` crisp hairline border, and 10px rounded corners. Inside, structured subcards house commercial model selection options, default pricing fields, experience pricing tables, replacement policy rules, and payment timeline schedules.
3. **15.6" Screen Optimization (Rule 14):** Drawer width is dynamically bounded via `width: min(1180px, calc((100vw - 220px) * 0.94)) !important; max-width: calc(100vw - 220px) !important; overflow-x: hidden !important; overflow-y: auto !important;`. Grids are defensively wrapped with `minmax(0, 1fr)` ensuring zero horizontal scrolling on standard laptop displays.
4. **Zero Business Logic Mutations (Rule 7):** All form input names, IDs (`#agency_commercial_terms_form`, `#saveAgencyCommercialTerms`, `#cancelAgencyCommercialTerms`), hidden inputs, data attributes (`[data-agency-section]`, `[data-aat-*]`), and AJAX request payloads remain 100% intact.

---

## 2. Architecture & Data Flow

```
[Service Company User: Account Settings #terms Subtab]
                    │
                    ▼ Click "Edit Fulltime Commercials" or "Edit Contract Commercials"
   window.openAgencyCommercialDrawer(mode)
                    │
                    ├─ Sets title, subtitle, and dynamic alert context
                    ├─ Toggles [data-agency-section="fulltime" / "contract"]
                    ├─ Syncs fee model, replacement summaries, and range defaults
                    │
                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│ .aat-drawer.agency-commercial-mode (Sticky Header + Internal Scroll)   │
│                                                                        │
│ ┌─ HEADER: .aat-drawer-head (#f8fafc background, border #e2e8f0) ────┐ │
│ │  • Icon + Title ("Edit Fulltime / Contract Commercials") + Subtitle │ │
│ │  • ACTIONS: [Cancel] + [Save & Update] + [×] Close                 │ │
│ └────────────────────────────────────────────────────────────────────┘ │
│                                                                        │
│ ┌─ BODY: .aat-drawer-body (Rule 20 Card + Sub-Card Stream) ──────────┐ │
│ │  • Alert Banner with contextual SVG icon                           │ │
│ │  • Card 1: Commercial Model (Percentage vs Fixed Subcards)         │ │
│ │  • Card 2: Default Pricing Subcard + Calculation Summary Box       │ │
│ │  • Card 3: Experience-Based Pricing Matrix (Mandatory #f8fafc thead)│ │
│ │  • Card 4: Replacement Policy Subcard + Dynamic Policy Note        │ │
│ │  • Card 5: Payment Timeline Subcard                                │ │
│ │  • Card 6: General Commercial Settings (Currency, Tax, TDS)        │ │
│ └────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────┘
                    │
                    ▼ Click "Save & Update" in Header
   $('body').on('click', '#saveAgencyCommercialTerms', ...)
                    │
                    ├─ Validates form & triggers .jero-btn-loading spinner
                    ├─ Serializes #agency_commercial_terms_form via FormData
                    ├─ POST /service_company/account/update/{id}
                    │     (ServiceCompanyAccountSettingsController)
                    ├─ Persists to `agency_commercial_terms` table
                    ├─ Flushes multi-tenant cache via AgencyCommercialTermsService
                    └─ Displays toastr success & reloads with fresh data
```

---

## 3. File Changes Matrix

| File Path | Nature of Change | Purpose |
|:---|:---|:---|
| `resources/views/service_company/account/agency_commercials_drawer.blade.php` | Markup Restructure | Moved Save & Update and Cancel buttons into `.aat-drawer-head`; removed `.aat-drawer-footer`; added clean SVG icon to `.aat-edit-alert`. |
| `resources/views/service_company/account/index.blade.php` | CSS Modernization | Refactored drawer styles (lines 480–607) for Rule 20 Card + Sub-Card layout, sticky `#f8fafc` topbar, tabular numerals, unslop craft standard, and 15.6" screen bounds. |
| `docs/audit documents/service_company/2026-11-23-agency-commercials-drawer-card-subcard-redesign.md` | Audit Documentation | Documented architecture, DOM elements, responsive layout metrics, and verification steps per Rule 21. |

---

## 4. 15.6" Screen Fit Verification (`1366×768` & `1920×1080`)

| Display Dimension | Metric | Implementation Guarantee |
|:---|:---|:---|
| **Viewport Width** | `1366px` | Left navigation = `220px`. Available space = `1146px`. Drawer width = `min(1180px, 1077px)`. Zero window overflow (`overflow-x: hidden`). |
| **Drawer Topbar** | `min-height: 64px` | Title + Subtitle on left (~750px space), Cancel + Save + Close on right (~280px). No wrapping, perfectly aligned. |
| **Default Pricing Grid** | `repeat(3, minmax(0, 1fr))` / `repeat(5, minmax(0, 1fr))` | 3-column in percentage mode (~320px/col); 5-column in fixed mode (~190px/col). Inputs and dropdowns fit comfortably. |
| **Experience Range Matrix** | `grid-template-columns` | Fixed column widths with `minmax(..., 1fr)` flexible rate column. Fits within ~1000px content area without horizontal scrollbar. |
| **Calculation Subcards** | `min-height: 44px` | Compact flex row with centered mathematical operators (`×`, `=`) and tabular numerals (`font-variant-numeric: tabular-nums`). |

---

## 5. Pre-Commit Verification & AI Guard Scan

1. **PHP Syntax Check (`php -l`):**
   - `resources/views/service_company/account/agency_commercials_drawer.blade.php`: Pass (0 syntax errors).
   - `resources/views/service_company/account/index.blade.php`: Pass (0 syntax errors).
2. **AI Guard Enforcement:**
   - Debug statements (`dd()`, `dump()`, `ray()`, `var_dump()`): 0 found.
   - `env()` calls outside `config/`: 0 found.
   - Unescaped `{!! !!}` statements: 0 added.
3. **Multi-Tenancy Guard:**
   - All backend persistence operations in `ServiceCompanyAccountSettingsController` remain strictly scoped by `company_id`.
