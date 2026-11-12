# Coins Activity (`transection-log`) — Modern Card + Sub-Card Redesign

## Metadata
- **Date:** 2026-11-12
- **Module:** `service_company_subscription`
- **Route / URL:** `/service_company/transection-log` (`service_company.transection-log`)
- **Controller Action:** `App\Http\Controllers\ServiceCompany\ManageSubscriptionController@transectionLog` & `transectionLogData`
- **Primary View:** `resources/views/service_company/subscription/transection-logs.blade.php`
- **Standards Enforced:** Rule 7 (Zero Business Logic Mutations), Rule 14 (15.6" Screen Fit `1366×768` & `1920×1080`, Poppins font, max 14px body, no 3D), Rule 17 (Page/Blade Isolation), Rule 18 (Service Company only), Rule 20 (Modern Card + Sub-Card Enterprise Design System), Rule 21 (Audit Document Standard)

## Executive Summary
The Service Company **Coins Activity** page (`resources/views/service_company/subscription/transection-logs.blade.php`) has been redesigned from a legacy tabular ledger into the **Rule 20 3-Tier Modern Card + Sub-Card Enterprise Architecture** while keeping 100% of the existing backend endpoint contracts, query-parameter synchronization, client-side LRU page caching, `AbortController` request cancellation, and Laravel Echo / Reverb real-time updates intact.

## Architecture & Data Flow
1. **Strict Page Isolation (`.txact-wrapper.coins-activity-card-system`):** All CSS rules and layout classes are scoped inside `.txact-wrapper` so no styles leak to other Service Company pages (Rule 17), and Font Awesome 5/6 glyphs are explicitly bound with `font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;` over `Poppins` typography.
2. **Tier 1 — Pure White Toolbar Card (`.tier1-toolbar-card` / `.txact-toolbar-card`):**
   - Left identity header (`Coins Activity`) with live wallet context pill and real-time sync badge.
   - Right-corner live search bar (`#txlog-search`) with 300ms debounce, inline clear button (`#txlog-search-clear`), and `#txlog-reset` reset control.
3. **Tier 2 — White Sub-Tabs & Filter Control Card (`.tier2-subtabs-card` / `.txact-subtabs-card`):**
   - Wallet ledger sub-tabs (`.txlog-tab`: `#tab-operational` for **Operational Coins**, `#tab-sales` for **Sales Coins**) with single active primary blue highlight (`#009ef7`).
   - Quick date presets (`.txlog-chip`), custom date range inputs (`#txlog-date-from`, `#txlog-date-to`), transaction type filter (`#txlog-type`), and page-size selector (`#txlog-per-page`: 10 / 20 / 50 / 100).
4. **Tier 3 — Telemetry Sub-Cards & Card Stream (`.tier3-cards-stream`):**
   - **4-KPI Telemetry Grid (`.txact-kpi-grid` / `.txact-kpi-subcard`):** Total Credits (`#kpi-credit-val` / `#stat-credit`), Total Debits (`#kpi-debit-val` / `#stat-debit`), Net Coin Flow (`#kpi-net-val`), and Matching Entries (`#kpi-total-val` / `#stat-total`), formatted with `tabular-nums`.
   - **Reverb Live Reload Banner (`#txlog-live-notice` & `#btn-txlog-live-reload`):** Preserved non-blocking notification strip triggered by wallet WebSocket events.
   - **Modern 6-Part Activity Entry Cards (`.txact-entry-card`):** Zero `<table` markup. Each ledger record renders 5 discrete upper sub-cards inside `.txact-entry-grid` (Entry & Type badge, Coin Amount Delta, Wallet Balance After, Description/Reference, Timestamp) plus a bottom activity strip (`.txact-entry-bottom`) showing actor/module context and transaction metadata, with `#txlog-pagination` below `#txlog-content`.
5. **Preserved JS & API Contracts:** `fetchData` (`transection-log/data`), `pageCache` (LRU RAM cache), `AbortController`, `syncBrowserUrl`, Echo channel listeners, and `window.JWTransactionLog` remain functionally unchanged.

## File Changes Matrix
| File | Change Type | Summary |
| :--- | :--- | :--- |
| `resources/views/service_company/subscription/transection-logs.blade.php` | Modified | Replaced legacy table layout with scoped Rule 20 3-tier Card + Sub-Card markup, KPI telemetry sub-cards, and 6-part activity entry cards while preserving all DOM IDs and JS/Reverb contracts. |
| `tests/Feature/ServiceCompanySubscriptionTest.php` | Modified | Added `test_transection_logs_template_enforces_three_tier_architecture_and_preserves_contracts` verifying Blade resolution, 3-tier classes, KPI/entry cards, zero `<table` elements, and preserved IDs/API contracts. |
| `docs/audit documents/service_company_subscription/2026-11-12-coins-activity-card-subcard-redesign.md` | Created | Rule 21 audit & architecture changelog for the Coins Activity redesign. |

## 15.6" Screen Fit (`1366×768` & `1920×1080`)
- Fluid responsive grid (`txact-kpi-grid` collapses cleanly across breakpoints; `txact-entry-grid` wraps cleanly without horizontal overflow).
- Compact enterprise spacing and `<=14px` Poppins body typography ensure high-density legibility on `1366×768` laptops and `1920×1080` monitors with zero horizontal scrollbar.

## Verification Evidence
- `php -l resources/views/service_company/subscription/transection-logs.blade.php`
- `php -l tests/Feature/ServiceCompanySubscriptionTest.php`
- `./vendor/bin/pint --test tests/Feature/ServiceCompanySubscriptionTest.php`
- `php artisan test tests/Feature/ServiceCompanySubscriptionTest.php`
