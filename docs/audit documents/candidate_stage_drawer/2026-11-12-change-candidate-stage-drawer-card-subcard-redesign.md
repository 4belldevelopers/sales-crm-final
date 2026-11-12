# Change Candidate Stage Drawer — Enterprise Card + Sub-Card Redesign

## 1. Metadata
- **Date:** 2026-11-12
- **Module / Feature:** Change Candidate Stage Drawer (`stage_body_content`, `client_stage_body_content`, `child_stage`)
- **Scope:** Service Company & Super Admin candidate stage drawers (`#csd_drawer` / `#stage_sub_stage`)
- **Compliance:** Rule 7 (Zero business logic mutation), Rule 14 (Metronic + Poppins + 15.6" fit), Rule 17 (Page isolation), Rule 18 (Product & Staffing companies untouched), Rule 20 (Modern Card + Sub-Card Design System), Rule 21 (Audit documentation).

## 2. Executive Summary
Redesigned the Change Candidate Stage drawer body partials (`stage_body_content.blade.php` and `client_stage_body_content.blade.php`) into the **Modern Card + Sub-Card Enterprise Design System** (`#eef2f7` slate canvas, pure white `#ffffff` `.csd-card` containers with `1px solid #e2e8f0` borders and discrete sub-cards):
1. **Crystal-Clear Current vs. Moving Stage Overview:** Added a top **Stage Transition Overview** card with two side-by-side sub-cards (`Current Stage` and `Moving To Stage`), a live directional flow indicator (`→`), step/direction pill (`Staying in Current Stage`, `Advancing +1 Step`, `Moving Back 1 Step`, `Moving to Rejected`, `Reopening`, or multi-step advisory), and live sub-stage status sync.
2. **Pipeline Stage Cards with Persistent Current vs. Target Distinction:** Upgraded stage pills to enterprise sub-cards displaying a persistent `Current` badge on the candidate's active stage and a distinct `Move To` badge + ring highlight on the selected target stage.
3. **Strict Sub-Stage Visibility Guard:** Fixed a PHP `empty($collection)` bug where `Illuminate\Support\Collection` always evaluated as non-empty, causing the `Sub Stage` heading to appear even when 0 sub-stages existed. Updated both SSR (`count($candidate_status_Arr) > 0`) and AJAX (`csdCountSubTiles(html) > 0`) plus `ServiceCompanyJobApplicationController::child_stage()` so the Sub Stage card and heading are activated **only** when at least 1 sub-stage exists and hidden completely otherwise (with dynamic renumbering of the Comments step badge between `2` and `3`).
4. **Stage & Sub-Stage Contextual Comment Suggestions:** Expanded quick comment suggestions across all 8 pipeline stages and 20+ sub-stage patterns (plus dynamic fallback for custom company sub-stage names), tagged by context (`For <Stage> › <Sub-stage>`) with one-click insertion and duplicate-insert prevention.

## 3. Architecture & Data Flow
- **SSR Initial Render (`stage_body_content` / `client_stage_body_content`):**
  - Reads `$application`, `$master_status`, `$candidate_status_Arr`, and `$child_status_name` already prepared by `ServiceCompanyJobApplicationController`.
  - Computes `$csdSubStageCount = count($candidate_status_Arr)` to decide initial `#main_substage` visibility (`display:none` if `0`, or if `InReview(8)` in client-managed flow).
- **Dynamic Stage Switch (`csdSelectStage` / `csdcSelectStage`):**
  - Immediately updates `#csd_summary_card` (`Current Stage` vs `Moving To Stage` sub-cards), submit button label/color, and stage-level comment suggestions.
  - Queries `service_company.job-applications.child_stage` (cached client-side per `master_status_id` in `window.CSD_SUBSTAGE_CACHE`) while keeping `#main_substage` hidden until `csdCountSubTiles(html) > 0` confirms at least 1 `.csd-sub-tile` exists.
- **Sub-Stage Switch (`csdSelectSub`):**
  - Highlights selected `.csd-sub-tile`, updates the target sub-card's sub-stage metadata line, and prepends sub-stage-specific comment chips above stage-level chips.

## 4. File Changes Matrix
| File | Change Summary |
|---|---|
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | Fixed `child_stage($id)` to use `$candidate_status_Arr->count() > 0`, added `.csd-sub-chk` indicator inside `.csd-sub-tile` to match SSR markup, and returned `'count' => $subStageCount` in JSON payload. |
| `resources/views/service_company/job-applications/stage_body_content.blade.php` | Redesigned plain-flow drawer body into Card + Sub-Card layout with Stage Transition Overview, persistent Current vs. Move To indicators, strict `count($candidate_status_Arr) > 0` Sub Stage guard, dynamic step numbering, and stage + sub-stage comment suggestions. |
| `resources/views/service_company/job-applications/client_stage_body_content.blade.php` | Applied matching Card + Sub-Card redesign for client-managed flow while preserving `#int_stage_submit` and `#comment_div` gating (`1/9/7` submit, `7` comment, `8` InReview no-substage). |

## 5. 15.6" Screen Fit (`1366×768` & `1920×1080`)
- Container uses `width: 100%; max-width: 100%; overflow-x: hidden;` with fractional `minmax(0, 1fr)` CSS Grid for the Current/Moving-To sub-cards and `repeat(auto-fill, minmax(210px, 1fr))` for sub-stage tiles.
- All 8 pipeline stage sub-cards (`flex: 1 1 94px; min-width: 88px;`) fit in a single horizontal row inside the 80%-width `#csd_drawer` on `1366×768` (`~1092px`) and `1920×1080`, wrapping cleanly below `768px` with zero horizontal scrollbar.

## 6. Verification Evidence
- `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` — Passed (`No syntax errors detected`).
- Blade compiler + `php -l` on compiled output for both `stage_body_content.blade.php` and `client_stage_body_content.blade.php` — Passed (`No syntax errors detected`).
- SSR render assertion harness (`stage_body_content` & `client_stage_body_content` with empty Laravel `Collection`, populated `Collection`, and `InReview(8)` vs `Rejected(7)`) — Passed (`ALL_RENDER_ASSERTIONS_PASSED`), confirming `#main_substage` is hidden (`display:none;`) when 0 sub-stages exist and visible with dynamic step numbering (`2` vs `3`) when sub-stages exist.
