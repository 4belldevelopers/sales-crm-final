# Jobs Tracker: Swap Skills and Posted Date in Job Card Footer Strip

## 1. Document Metadata
- **Date:** 2026-11-25
- **Module:** Service Company Jobs Tracker (`resources/views/service_company/jobs/index.blade.php`)
- **Standard Reference:** Rule 7 (Zero Business Logic Mutations), Rule 14 (15.6" Screen Fit), Rule 17 (Complete Page/Blade Isolation), Rule 20 (Card + Sub-Card Design), Rule 21 (Module Subfolder Storage)
- **Target Screens:** 15.6" Displays (`1366×768` and `1920×1080`) and smaller responsive viewports
- **Isolation Scope:** Strictly isolated to `#allJobList` inside `resources/views/service_company/jobs/index.blade.php`; shared stylesheets (`tab-jobs-style.blade.php`) and other modules remain completely untouched.

---

## 2. Executive Summary
Updated the bottom footer strip (`.cd-job-card-bottom`) of the job cards rendered on the **Service Company Jobs Tracker** page so that:
1. **Skills** (`.cd-bottom-skills.cd-bottom-left-skills`) now renders on the **left side**, starting cleanly from the left edge (`flex: 1 1 auto`, `justify-content: flex-start`, horizontal overflow scroll preserved).
2. **Posted Date** (`.cd-bottom-left.cd-bottom-right-meta`) now renders at the **extreme right corner** (`margin-left: auto`, `justify-content: flex-end`, `flex-shrink: 0`, `text-align: right`).
3. Both **Hiring Job cards** (`renderJobsClientSide`) and **Pending Job Request cards** (`renderJobRequestsClientSide`) within Jobs Tracker are updated consistently.
4. All existing typography, pill styling, colors, card geometry, click handlers, filters, and CQRS/ES data flows remain 100% preserved.

---

## 3. Architecture & Layout Behavior

```
Before:
┌───────────────────────────────────────────────────────────────────────────────┐
│ 📅 Posted: 08 Nov 26, 07:30 PM IST                     [Sales] [Lead gen...] │
└───────────────────────────────────────────────────────────────────────────────┘

After:
┌───────────────────────────────────────────────────────────────────────────────┐
│ [Sales] [Lead generation]                     📅 Posted: 08 Nov 26, 07:30 PM │
└───────────────────────────────────────────────────────────────────────────────┘
```

- **Shared Stylesheet Isolation (Rule 17):** `resources/views/service_company/client/components/tab-jobs-style.blade.php` is shared with Client Drawers (`dviewMore.blade.php`, `client/show.blade.php`, `consultancy/dviewMore.blade.php`, `client_jobs_side_bar_ajax.blade.php`), so it was left untouched. All CSS overrides for the swapped layout are scoped strictly under `#allJobList .cd-job-card-bottom` inside `resources/views/service_company/jobs/index.blade.php`.
- **Responsive Breakpoints:**
  - **Desktop / 15.6" (`1366×768` & `1920×1080`, `> 1100px`):** Single horizontal flex row with `justify-content: space-between` and `gap: 12px`; overflowing skills scroll cleanly within the left container (`min-width: 0`) without pushing Posted Date off the right corner.
  - **Smaller Screens (`<= 1100px`):** When `.cd-job-card-bottom` stacks vertically via `tab-jobs-style.blade.php`, Skills occupies full width starting from the left edge and Posted Date remains right-aligned with clean wrapping (`flex-wrap: wrap`).

---

## 4. File Changes Matrix

| File Path | Changes Applied | Verification |
|---|---|---|
| `resources/views/service_company/jobs/index.blade.php` | 1. Added page-isolated CSS rules under `#allJobList .cd-job-card-bottom` for left-aligned `.cd-bottom-skills.cd-bottom-left-skills` and right-aligned `.cd-bottom-right-meta` (plus `<= 1100px` responsive alignment).<br>2. Swapped footer child order in `renderJobRequestsClientSide` (Skills left, Posted Date & Requested By right).<br>3. Swapped footer child order in `renderJobsClientSide` (Skills left, Posted Date & HM right, with bullet guarded on `formattedDate`). | `php -l` passed; Blade compiled (`196025` bytes); Bun DOM check passed; AI guards clean. |

---

## 5. Verification Evidence
- **PHP Syntax Check:** `php -l resources/views/service_company/jobs/index.blade.php` → `No syntax errors detected`.
- **Blade Compilation Check:** `blade.compiler->compileString(...)` → `Blade compiled OK: 196025 bytes`.
- **Client Card Render Order Check (Bun v1.4.4):** Executed `renderJobsClientSide` (with and without `hiring_manager`) and `renderJobRequestsClientSide`; verified `.cd-bottom-skills.cd-bottom-left-skills` renders before `.cd-bottom-left.cd-bottom-right-meta` in `.cd-job-card-bottom`.
- **AI Guard Scan:** Zero occurrences of `dd()`, `dump()`, `ray()`, `var_dump()`, or `env()` in `resources/views/service_company/jobs/index.blade.php`; zero new `{!! !!}` outputs.
- **Unit Test Check:** `php artisan test tests/Unit/MultiCityWorkLocationDisplayTest.php` → 10 passed (50 assertions).
