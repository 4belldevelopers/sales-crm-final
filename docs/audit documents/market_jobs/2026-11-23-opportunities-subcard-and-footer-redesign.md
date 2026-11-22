# Opportunities Listing Page UI/UX Redesign — Job Tracker Parity Standard

## Metadata
- **Date:** 2026-11-23
- **Module:** Service Company — Opportunities / Agency Job Marketplace
- **Route:** `GET /service_company/jobs/market-jobs/{id}` (`https://testing.jero.works/service_company/jobs/market-jobs/0`)
- **Author:** Antigravity AI Agent
- **Compliance Standards:** Rule 1 & 2 (Strict CQRS), Rule 14 (15.6" Screen Fit), Rule 20 (Card + Sub-Card Design Standard), Rule 25 (Canonical Classification), Rule 26 (/unslop Craft Standard)

---

## Executive Summary
This update delivers the second phase of the Opportunities Listing Page redesign, elevating visual hierarchy, layout proportionality, and element alignment to achieve 100% parity with the **Job Tracker** page (`resources/views/service_company/jobs/index.blade.php`).

Key enhancements include:
1. **Sort & Filter Positioning:** Interchanged the positions of Sort and Filter controls within `.cl-filter-bar`. The Metronic Sort combobox popover now directly follows the micro-search bar, followed by the Advanced Filter button.
2. **Removed Unnecessary Clutter:**
   - Completely removed the redundant `#marketRefreshBtn` from the page header.
   - Completely removed the `#marketResetAllBtn` from the page header.
   - Removed the `"X Opportunities"` pill (`#span_jobMarket_header`) from the header card title row.
3. **Opportunity Subcard Redesign (Job Tracker Parity):** Redesigned Subcard 1 (`.cd-subcard-role`) to follow the exact 3-row layout and visual structure established on the Job Tracker page:
   - **Row 1:** Position / Job Title (12.5px, bold 700 `#0f172a`, truncated with ellipsis).
   - **Row 2:** Job ID (`#<req_id>` in cyan `#0284c7`, font-weight 700) `·` Department (`#64748b`, 10.5px, font-weight 500).
   - **Row 3:** Direct Client label (`displayClientName`) `·` Briefcase icon + Normalized Employment Type (`Contract`, `Full-time`, `Freelancer`, `Internship`, `Part-time` in `#2563eb`).
4. **Footer Strip Redesign:**
   - **Left side:** Relevant Skills Tags (`.cd-bottom-skills.cd-bottom-left-skills`).
   - **Right side:** Posted Date (`📅 Posted: <date>`) `·` Number of Openings (`👥 <count> Openings`).
   - **Removed All Unmentioned Elements:** Deadlines, updated ago timestamps, bookmark save icons, and ellipsis options menus were completely eliminated to maximize clarity and whitespace.
5. **Enterprise-Level Card Height & Proportions:**
   - Subcards enforce uniform `min-height: 105px` and stretch vertically to match sibling subcards.
   - Total master card height reduced to ~140px–150px, providing dense, readable enterprise information density without scrolling fatigue.
   - Responsive grid ensures zero horizontal overflow on standard 15.6" screens (`1366×768` and `1920×1080`).

---

## Architectural & UI/UX Structure

### 1. Control Hierarchy in Tier 3 Filter Bar
```
┌───────────────────────────────────────────────┬────────────────────────────┬──────────────────┐
│ 🔍 Search jobs by title, skills, ID, client... │  Sort: Newest to Oldest ▾  │  ⚙️ Filter (0)   │
└───────────────────────────────────────────────┴────────────────────────────┴──────────────────┘
```

### 2. Subcard 1 Three-Row Structure (Job Tracker Parity)
```
┌─────────────────────────────────────────────────────────────────────────────────┐
│ JOB DETAILS                                    [ 🛡️ DIRECT CLIENT ] [ 👥 OPEN ]  │
├─────────┬───────────────────────────────────────────────────────────────────────┤
│         │ Row 1: Sales Manager Required                                         │
│ [LOGO]  │ Row 2: #120 · Information Technology                                  │
│         │ Row 3: 4BELL TECHNOLOGY · 💼 Contract                                │
└─────────┴───────────────────────────────────────────────────────────────────────┘
```

### 3. Footer Strip Layout
```
┌───────────────────────────────────────────────┬─────────────────────────────────┐
│ [React] [Node.js] [TypeScript] [+2]           │ 📅 Posted: 22 Nov 26 · 👥 3 Openings │
└───────────────────────────────────────────────┴─────────────────────────────────┘
```

---

## File Changes Matrix

| File Path | Type | Key Changes |
| :--- | :--- | :--- |
| `resources/views/service_company/jobs/market-jobs.blade.php` | Blade View | Removed `#marketRefreshBtn`, `#marketResetAllBtn`, `#span_jobMarket_header`; interchanged Sort and Filter controls; implemented 3-row layout in `renderMarketJobCard` with normalized employment types; redesigned footer strip with left skills tags and right telemetry. |
| `demo/css/find-market-jobs.css` | Stylesheet | Updated `.cd-market-card-bottom`, `.cd-bottom-left-skills`, `.cd-bottom-right-meta`, `.cd-job-client-name`; set `.cd-bottom-actions` to `display: none !important`. |
| `tests/Feature/MarketJobV2Test.php` | Feature Test | Added `test_market_jobs_redesign_controls_subcards_and_footer_strip_parity` asserting DOM order (Sort before Filter), removal of deprecated buttons, 3-row subcard classes, and footer strip classes. |

---

## 15.6" Screen Fit & Responsiveness (Rule 14)
- **1920×1080:** Full 5-column subcard grid (`2.5fr 1.4fr 0.95fr 1.35fr 1.25fr`), zero horizontal scroll.
- **1366×768:** Compact 5-column subcard grid (`2.3fr 1.35fr 1fr 1.35fr 1.25fr`), subcard text truncates with ellipsis.
- **Tablet / 1100px:** Responsive grid wraps into 3 columns with actions spanning 2 columns.
- **Mobile / 768px:** Single-column stacked cards.

---

## Verification Evidence
```bash
# 1. PHP Syntax Check
$ php -l resources/views/service_company/jobs/market-jobs.blade.php
No syntax errors detected in resources/views/service_company/jobs/market-jobs.blade.php

# 2. Automated Feature Test Suite
$ php artisan test --filter=MarketJobV2Test
PASS  Tests\Feature\MarketJobV2Test
✓ market job v2 service class exists
✓ market job v2 resource class exists
✓ reindex ats jobs v2 command exists
✓ v2 route is registered
✓ v2 service get cache key format
✓ tenant cache invalidation increments version
✓ market job v2 resource dto transformation
✓ location only filter parsing
✓ market jobs blade implements l1 ram and l2 indexeddb architecture
✓ market job delta updated event broadcast contracts
✓ market job mutation service and canonical hashing
✓ process market job outbox event job contracts
✓ phase4 blade idempotency and schema indexing
✓ phase5 claim route and outbox wiring
✓ phase7 production rollout and observability readiness
✓ market jobs tabs and filter bar client tracker parity
✓ market jobs redesign controls subcards and footer strip parity

Tests: 17 passed (140 assertions)
Duration: 11.11s
```
