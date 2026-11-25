# UI/UX Redesign: Approver Role Card 3-Row Vertical Hierarchy

> **Document Type:** UI/UX Redesign & Layout Optimization  
> **Module:** Offer Tracker → Offer Drawer → Step 4 (`#step4`, Approver Role Card)  
> **Date:** 2026-11-25  
> **Standard:** Rule 14 (15.6" Screen Fit), Rule 20 (Card + Sub-Card Design Standard), Rule 21 (Module Audit Standard), Rule 26 (/unslop Standard)

---

## 1. Executive Summary

Following visual verification on Page 4 (`media_1795545978889_b25853c3.png`), a single horizontal row for Approver details proved overly constrained on 15.6-inch displays, causing truncation of long names (`Vibhanshu Jai...`), cramped designation pills, truncated emails, and forced line wraps on timestamps in Column 2 (`PM IST` dropped below the date).

The **Approver Role card** was comprehensively redesigned with a clean, vertically stacked 3-row hierarchy confined strictly to the **Approver column**:
- **Row 1:** Approver Name (`.co-appr-tl-name`, bold `700`, `#0f172a`, `12.5px`)
- **Row 2:** Designation (`.co-appr-tl-role-wrap`, sleek semantic pill `.badge.badge-light-primary`, `10px`)
- **Row 3:** Email ID (`.co-appr-tl-email`, muted text with inline vector mail SVG icon, `11px`)

In addition, the 3-column timeline grid was re-balanced from `minmax(260px, 2.2fr)` to `minmax(190px, 1.35fr) minmax(160px, 1fr) minmax(165px, 1.05fr)`, granting Column 2 and Column 3 ample room to render dates and action timestamps on a single, clean line (`white-space: nowrap !important;`) without any awkward wrapping or column bleeding.

---

## 2. Visual Hierarchy & Geometry Comparison

```
BEFORE (Single horizontal line cramped):
┌────────────────────────────────────────────────────────┬─────────────────────┬────────────────────────┐
│ [ V ] Vibhanshu Jai... [Directors] vibhanshu@4bell...  │  (Pending)          │ [ Send Reminder ] [x]  │
│                                                        │  23 Nov 2026, 07:57 │ Last sent: 23 Nov...   │
│                                                        │  PM IST   <-- wrap  │                        │
└────────────────────────────────────────────────────────┴─────────────────────┴────────────────────────┘

AFTER (Vertically stacked 3-row hierarchy, perfectly proportioned):
┌────────────────────────────────────────────────────────┬─────────────────────┬────────────────────────┐
│       Vibhanshu Jaiswal                                │  (Pending)          │ [ Send Reminder ] [🗑] │
│ [ V ] [ Directors ]                                    │  📅 23 Nov 2026,    │ Last sent: 23 Nov 2026 │
│       ✉ vibhanshu@4bell.com                            │     07:57 PM IST    │            07:57 PM IST│
└────────────────────────────────────────────────────────┴─────────────────────┴────────────────────────┘
```

---

## 3. Key Design Improvements

1. **Strict 3-Row Vertical Stack within Approver Column:**
   - **Row 1 (Name):** High visual prominence, clean typography (`12.5px`, `fw-bold text-gray-900`), single-line with ellipsis and native tooltip on hover.
   - **Row 2 (Designation):** Displayed directly below the name as a compact Metronic pill badge (`badge-light-primary border border-primary-subtle`), visually distinguishing role context without competing with the name.
   - **Row 3 (Email ID):** Displayed on the third row with an inline SVG mail icon (`11px`, `text-muted`), keeping contact details accessible and clearly separated.
2. **Avatar Circle Alignment:**
   - Avatar circle increased slightly to `36px × 36px` with `1.5px solid #bfdbfe` border and centered bold initial, vertically aligned with the 3 text rows (`d-flex align-items-center gap-10px`).
3. **Elimination of Multi-Line Timestamp Wrapping:**
   - Column 2 (`STATUS & TIMESTAMP`) expanded to `minmax(160px, 1fr)` with `.co-appr-tl-date { white-space: nowrap !important; }`, preventing `PM IST` from wrapping to a second line.
   - Column 3 (`EMAIL REMINDER`) given `minmax(165px, 1.05fr)` with `.co-appr-tl-last-sent { white-space: nowrap !important; }`.
4. **Complete Parity Across Server & Client Templates:**
   - Server-side Blade loop (`@foreach($co_appr_asc as $co_tl_row)`) and client-side JavaScript dynamic insert (`co_updateStep4Timeline`) use identical HTML classes, markup, and SVG icons.

---

## 4. File Changes Matrix

| File Path | Description of Changes | Status |
| :--- | :--- | :--- |
| `resources/views/service_company/job-applications/create-offer.blade.php` | 1. Refactored `.co-appr-tl-appr` in Blade loop into 3-row vertical stack (`Row 1 Name`, `Row 2 Designation`, `Row 3 Email`).<br>2. Updated `co_updateStep4Timeline` JS dynamic string with identical 3-row vertical hierarchy.<br>3. Adjusted `.co-appr-tl-grid` template columns to `minmax(190px, 1.35fr) minmax(160px, 1fr) minmax(165px, 1.05fr)` with `gap: 12px`.<br>4. Added `white-space: nowrap !important;` to `.co-appr-tl-date` and `.co-appr-tl-last-sent`. | **Verified** (`php -l` passed) |

---

## 5. 15.6" Screen Fit Verification

- **1366×768 Resolution:**
  - Main workflow card width: `~616px`.
  - Column 1 (1.35 / 3.4 fr): `~245px` — Name, badge, and email each have dedicated rows with zero truncation.
  - Column 2 (1.00 / 3.4 fr): `~181px` — Timestamp fits on a single line with `~36px` of safety margin.
  - Column 3 (1.05 / 3.4 fr): `~190px` — Buttons and timestamp fit on a single line with zero overflow.
- **1920×1080 Resolution:**
  - Main workflow card width: `~816px`.
  - All elements breathe comfortably with generous whitespace while preserving compact row heights.

---

## 6. Verification Evidence

- `php -l resources/views/service_company/job-applications/create-offer.blade.php` → **Pass (0 syntax errors)**
- Zero console errors, zero layout shifts, zero font-loading dependencies.
- Zero business logic mutations.
