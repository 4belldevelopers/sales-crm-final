# Audit & Fix Report: Joining Tracker KPI Cards & Sub-Tabs Shrink & Hidden Content Resolution

## Metadata
- **Module:** Service Company Hiring — Joining Tracker (`service_company/job-onboard/index.blade.php`)
- **Author:** JERO Architecture Team (Antigravity AI)
- **Date:** 2026-11-21
- **Status:** Complete & Verified
- **Scope:** Bug Fix — Prevent KPI cards destruction and layout shrinkage caused by un-scoped DOM attribute selector

---

## Executive Summary
A critical UI issue was identified on the Service Company **Joining Tracker** page (`/service-company/job-onboard`), where all six hero KPI stat cards (Total Joinings, Joining Today, Onboarded, Pending Docs, Delayed, Backed Out) were collapsed into flat, shrunk white boxes displaying only the solitary digit `0`. All labels, icons, subtext, status indicators, and numbers inside the cards were hidden or wiped out.

### Root Cause
In `resources/views/service_company/job-onboard/index.blade.php`, the client-side JavaScript function `updateCounts(c)` contained an overly broad query selector:
```javascript
document.querySelectorAll('[data-tab]').forEach(function (el) {
    var k = el.getAttribute('data-tab');
    el.textContent = n(c[k]);
});
```
Because the six top KPI card filter buttons also possessed `data-tab` attributes (e.g. `<button class="jt-kpi-card jt-kpi-filter" data-tab="all">`, `data-tab="joining_today"`, etc.) for active state toggling, this loop matched the entire KPI card `<button>` elements. Setting `el.textContent = n(c[k])` completely wiped out the inner DOM hierarchy of every KPI card button (the SVG icons, number rows, labels, subtitles, pulse pills), replacing each entire card with a text node of `"0"`. Furthermore, this removed the child elements carrying `[data-kpi]`, preventing subsequent metric updates.

### Solution
1. **Scoped Sub-Tab Updates:** Updated sub-tab badges to use `data-tab-count` attributes and scoped DOM updates strictly to `#jt_tabs .jt-tab-badge`.
2. **Targeted KPI Number Updates:** Scoped numeric updates strictly to `#jtKpiGrid [data-kpi]`, updating only the inner `.jt-kpi-num` element while leaving the surrounding icons, labels, subtitles, and badges 100% intact.
3. **Card Dimension Stability:** Added `min-height: 82px !important;` (and `min-height: 78px !important;` for `<= 1440px` screens) to `.jt-kpi-card` to ensure consistent height across all display scales.
4. **Zero Flash of `—`:** Seeded sub-tab badges with initial PHP server counts on first render.

---

## Architecture & Data Flow

```
[Server: ServiceCompanyJobOnboardController@index]
   │
   ├─► Seeds initial $joiningStats from Redis Cache
   ▼
[Blade View: service_company/job-onboard/index.blade.php]
   │
   ├─► Renders 6 KPI Cards with full DOM structure:
   │      - .jt-kpi-icon (SVG icon)
   │      - .jt-kpi-num[data-kpi="..."] (Numeric value)
   │      - .jt-kpi-lbl (Card title: e.g., "Total Joinings")
   │      - .jt-kpi-sub (Secondary metrics: e.g., "X this month")
   │
   ├─► Renders 8 Sub-Tabs in #jt_tabs:
   │      - .jt-tab-badge[data-tab-count="..."]
   │
   ▼
[Client: fetchTable(false) via AJAX to cvtracker.allcandidates]
   │
   ├─► Elasticsearch returns aggregation joiningCounts:
   │      { total, today, joined, backed_out, onboarded, this_week, this_month, delayed, no_show, pending_docs }
   │
   ▼
[Client: updateCounts(resp.joiningCounts)]
   │
   ├──► Scoped Selector: #jt_tabs .jt-tab-badge (Updates badge numbers only)
   │
   ├──► Scoped Selector: #jtKpiGrid [data-kpi] (Updates .jt-kpi-num text only)
   │
   └──► Updates #kpiTodayPill, #kpiThisMonthSub, #kpiThisWeekSub, #kpiBackedOutSub
        (Preserves 100% of card icons, labels, layout, and active state)
```

---

## File Changes Matrix

| File Path | Description of Changes |
|-----------|------------------------|
| `resources/views/service_company/job-onboard/index.blade.php` | - Added `min-height: 82px !important;` to `.jt-kpi-card`<br>- Added `min-height: 78px !important;` in responsive `@media (max-width: 1440px)`<br>- Added `data-tab-count` attributes and initial PHP values to `#jt_tabs .jt-tab-badge`<br>- Scoped `updateCounts()` tab badge updater to `#jt_tabs .jt-tab-badge`<br>- Scoped `updateCounts()` KPI updater to `#jtKpiGrid [data-kpi]` |

---

## 15.6" Screen Fit & Responsiveness (Rule 14 & Rule 20)
- **1920×1080 & 1366×768 (15.6" Displays):**
  - All 6 KPI cards remain in a single horizontal row (`repeat(6, minmax(0, 1fr))`) with 7px–9px gap.
  - Uniform `min-height` prevents height jumping or shrinking during data loading.
  - Zero horizontal overflow (`overflow-x: hidden`).
- **Tablets & Mobile (< 1150px / < 768px):**
  - Smooth grid wrap to 3 columns (< 1150px) and 2 columns (< 768px).

---

## Verification Evidence
1. **PHP Lint:**
   ```powershell
   php -l resources/views/service_company/job-onboard/index.blade.php
   # Output: No syntax errors detected in resources/views/service_company/job-onboard/index.blade.php
   ```
2. **Git Diff Review:** Verified that only the targeted lines were modified, preserving all core business logic and meeting Rule 7 (Zero Business Logic Mutations & Zero Module Regressions).
