# AppTracker Header Tabs Redesign (Client Tracker KPI Card Standard)

## Metadata
- **Date:** 2026-11-12
- **Module:** Service Company — AppTracker Header Tabs (`apptrackerHeader` & `clientappHeader`) + Pipeline KPI Rollback (`apptracker/index`, `apptracker/clientapp`)
- **Reference Design:** Service Company Clients Tracker (`resources/views/service_company/client/index.blade.php` & `demo/css/clientList.css`)
- **Rule Compliance:** Rule 7 (Zero Business Logic Mutations), Rule 14 (15.6" Screen Fit), Rule 17 (Page Isolation), Rule 18 (Service Company Only Scope), Rule 20 (Card + Sub-Card Design System), Rule 21 (Module Folder Doc Standard)

## Executive Summary
1. **Reverted Pipeline Stage KPI Section (`apptracker/index.blade.php`, `apptracker/clientapp.blade.php`):** Restored the `.hp-card` / `.hp-flow` Hiring Pipeline stage strip (`Sourcing`–`Joined` + `Rejected`) back to its original layout (`7ff7e237~1`).
2. **Redesigned AppTracker Navigation Tabs (`apptrackerHeader.blade.php` & `clientappHeader.blade.php`):** Transformed the 6 AppTracker section tabs (`Total Candidates In Job`, `Similar Candidates`, `In-house Bench Talents`, `Agency Bench Talents`, `Bench Talent Requests`, `Career Page`) and 3 client-job sibling tabs into enterprise `.cl-stat-grid` / `.cl-card`-style horizontal KPI tab cards modeled directly after Clients Tracker.

## Architecture & Data Flow
1. **Card Anatomy & Visual Fidelity (`apptrackerHeader.blade.php` / `clientappHeader.blade.php`):**
   - Pure white elevated cards (`#ffffff`, border `#e2e8f0`, radius `11px`, subtle hover elevation) arranged in a single horizontal CSS Grid row (`repeat(6, minmax(0, 1fr))` in `apptrackerHeader.blade.php`; `repeat(3, minmax(0, 1fr))` in `clientappHeader.blade.php`).
   - Left `34x34px` rounded-`8px` tinted icon tile (`--apth-bg`, `--apth-color`). When active, the card adopts `--apth-tint` background, `--apth-border`, a `1.5px` `--apth-color` halo ring (`--apth-glow`), and an elevated pure white `#ffffff` icon box — matching `.cl-card.active` in `demo/css/clientList.css`.
   - Right `.apth-meta` stacks bold `19px` `tabular-nums` counter + `Active` pill badge (on the current route's active tab) above the `11px` weight-`600` single-line title and preserved tooltip trigger `#apth_hint_<id>`.
   - Bottom `.apth-sub` micro-row (`10px`, colored per tab) pairs a semantic `11px` Feather icon + source caption (`Active job pipeline`, `Matched to job profile`, `Internal bench pool`, `Partner agency talent`, `Vendor submissions`, `Direct job applicants`) with a subtle right navigation arrow (`->`).
2. **Legacy DOM & JS Contract Preservation (Rule 7):**
   - Preserves all route URLs (`$tabactive[..._url]`), server-side active flags (`$tabactive[..._active]`), counter DOM IDs (`$tabactive[..._id]`, e.g., `#pool_counter`, `#similar_counter`, `#inhouseBench_counter`, `#agencyBench_counter`, `#benchTalentReq_counter`, `#career_counter`), and `#apth_hint_similar_counter` for dynamic tooltip updates in `similar_candidates.blade.php` and `dsimilar_candidates.blade.php`.

## File Changes Matrix
| File | Scope | Summary of Changes |
| :--- | :--- | :--- |
| [apptrackerHeader.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/sections/service_company/apptrackerHeader.blade.php) | Service Company AppTracker (All 6 tabs) | Redesigned `.apth-grid` / `.apth-card` layout to match Clients Tracker `.cl-stat-grid` cards with 34x34px icon boxes, active ring halos, `Active` pill, and semantic `.apth-sub` captions. |
| [clientappHeader.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/sections/service_company/clientappHeader.blade.php) | Service Company Client-Job AppTracker (3 tabs) | Synchronized visual design tokens and responsive scaling across the 3 client-job tabs (`Total Candidates In Job`, `Similar Candidates`, `In-house Bench Talents`). |
| [index.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/apptracker/index.blade.php) | Service Company AppTracker Pipeline KPI | Reverted previous `.hp-flow` -> `.aptk-kpi-grid` edit back to `7ff7e237~1`. |
| [clientapp.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/apptracker/clientapp.blade.php) | Service Company ClientApp Pipeline KPI | Reverted previous `.hp-flow` -> `.caf-kpi-grid` edit back to `7ff7e237~1`. |

## 15.6" Screen Fit (`1366x768` & `1920x1080`)
- Desktop (`> 1440px`, e.g. `1920x1080`): 6-column grid (`8px` gap, `10px 11px` padding, `34x34px` icons, `19px` counters, `11px` titles, `10px` captions).
- 15.6" Laptop (`<= 1440px`, e.g. `1366x768`): 6 columns scaled identically to `clientList.css` (`6px` gap, `8px 8px` padding, `30x30px` icons, `17px` counters, `10px` titles, `9.5px` captions), preventing horizontal page overflow.
- Narrow (`<= 1150px` -> `26px` icons / `15.5px` counters; `<= 900px` -> 3 columns; `<= 576px` -> 2 columns; `<= 400px` -> 1 column).

## Verification Evidence
- Blade compilation, `php -l`, and mock SSR render assertions executed via `verify_apptracker_header_tabs.php` on `apptrackerHeader.blade.php`, `clientappHeader.blade.php`, `apptracker/index.blade.php`, and `apptracker/clientapp.blade.php`.
