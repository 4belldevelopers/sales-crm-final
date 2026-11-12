# AppTracker KPI Section Redesign (Client Tracker Card Standard)

## Metadata
- **Date:** 2026-11-12
- **Module:** Service Company — AppTracker (`apptracker/index` & `apptracker/clientapp`)
- **Reference Design:** Service Company Clients Tracker (`resources/views/service_company/client/index.blade.php` & `demo/css/clientList.css`)
- **Rule Compliance:** Rule 7 (Zero Business Logic Mutations), Rule 14 (15.6" Screen Fit), Rule 17 (Page Isolation), Rule 18 (Service Company Only Scope), Rule 20 (Card + Sub-Card Design System), Rule 21 (Module Folder Doc Standard)

## Executive Summary
Redesigned the AppTracker Hiring Pipeline KPI bar across both Service Company AppTracker views (`index.blade.php` and `clientapp.blade.php`) from the legacy circular-node (`hp-circle`) strip into an enterprise KPI Stat Card Grid modeled directly after the Clients Tracker `.cl-stat-grid` / `.cl-card` specification.

Every KPI card displays a rounded 34x34px tinted icon tile, bold tabular counter (`tabular-nums`), semantic stage label, and a live contextual sub-caption (`X active` on Total Candidates; `Y% of total` share calculation on each pipeline stage). Clicking any card highlights it with a tinted surface, matching border, `1.5px` ring halo, and elevated white icon box while preserving every existing AJAX filter, route state, counter sync, and WebSocket/BroadcastChannel delta update.

## Architecture & Data Flow
1. **Isolated CSS Grid Layout (`.aptk-kpi-grid` / `.caf-kpi-grid`):**
   - Renders all 8 KPI cards (`Total Candidates` + `Sourcing`, `Screening`, `Evaluation`, `Negotiation`, `Offer`, `Joined`, `Rejected`) in a single horizontal CSS Grid row (`minmax(0, 1.15fr) repeat(7, minmax(0, 1fr))`) on standard desktop and 15.6" laptop viewports.
   - Collapses cleanly to `4 x 2` (`<= 1150px`) and `2 x 4` (`<= 640px`) on narrower screens.
2. **Legacy JS & DOM Contract Preservation (Rule 7):**
   - Total Candidates retains `id="bg_all"`, org palette classes (`hp-org-my`, `hp-org-internal`, `hp-org-direct`), `.bg-total-active` / `.bg-gray`, and count targets `#totalcnt` (`index.blade.php`) / `#bg_alldata` (`clientapp.blade.php`).
   - Stage cards retain `.application_sttus_nav[rel="1..7"]`, `.hp-circle[data-hp="1..7"]`, `#bg_1..#bg_7`, and `#tx_1..#tx_7` (`.hp-label`) so `applyAptkCounts()`, `countder()`, `renderAppTrackerResponse()`, `patchAptkDomCountersFromDelta()`, and `updateStage()`/`updateSubStage()` require zero signature or selector changes.
3. **Live Sub-Line Sync (`syncAptkKpiSublines` / `syncCafKpiSublines`):**
   - Client-side RAM computation calculates active count (`Total - Rejected`) and per-stage share percentage (`round((stage / total) * 100)% of total`) on initial load (`clientapp.blade.php` via PHP + JS sync) and on every AJAX/delta counter update.

## File Changes Matrix
| File | Scope | Summary of Changes |
| :--- | :--- | :--- |
| [index.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/apptracker/index.blade.php) | Service Company (Direct/Own Job AppTracker) | Replaced `.hp-flow` / `.hp-circle` CSS & HTML with `.aptk-kpi-grid` / `.aptk-kpi-card` Client-Tracker-matched KPI cards and added `syncAptkKpiSublines()` in `applyAptkCounts()` / `patchAptkDomCountersFromDelta()`. |
| [clientapp.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/apptracker/clientapp.blade.php) | Service Company (Client Job AppTracker) | Replaced `.hp-flow` / `.hp-circle` CSS & HTML with page-isolated `.caf-kpi-grid` / `.caf-kpi-card` KPI cards, server-rendered initial share sub-lines, and added `syncCafKpiSublines()` across AJAX counter updates. |

## 15.6" Screen Fit (`1366x768` & `1920x1080`)
- Desktop (`> 1440px`, e.g., `1920x1080`): 8 columns (`minmax(0, 1.15fr) repeat(7, minmax(0, 1fr))`), `8px` gap, `10px 11px` card padding, `34x34px` icons, `19px` counters, `11px` labels, `10px` sub-captions.
- 15.6" Laptop (`<= 1440px`, e.g., `1366x768`): 8 columns scaled identically to `clientList.css` breakpoints (`6px` gap, `8px 8px` card padding, `30x30px` icons, `17px` counters, `10px` labels, `9.5px` sub-captions), preventing horizontal page overflow.
- Tablet (`<= 1150px`) and Mobile (`<= 640px`): clean `4`-column and `2`-column wrap without truncation glitches.

## Verification Evidence
- Blade compilation and `php -l` syntax checks executed cleanly via scratch verifier `verify_apptracker_kpi.php` against both modified views (`apptracker/index.blade.php` and `apptracker/clientapp.blade.php`), confirming zero PHP syntax errors and verifying all required KPI IDs (`#bg_all`, `#totalcnt`, `#bg_alldata`, `#bg_1..#bg_7`, `#tx_1..#tx_7`) and JS hooks remain intact.
