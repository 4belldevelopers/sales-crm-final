# Jobs Tracker UI/UX Audit — Hidden / Replaced Sections

**Page:** `service_company/jobs` (route: `Route::resource('jobs', ServiceCompanyJobsController::class)` → `index()` → view `resources/views/service_company/jobs/index.blade.php`)
**Persona:** Service Company (in-scope)
**Audit date:** 2026-08-20
**Method:** Reviewed every commit since 2026-07-15 that touches the jobs page view, its hero partial, its filter-bar partial, and the controller's `index()` / `dataCnt()`. Compared each commit's diff to identify sections whose data/visibility changed.

---

## 1. Executive summary

Between **2026-07-23** and **2026-07-29**, the jobs page hero and filter bar were completely rewritten as part of the "job page hero section redesign" project. The redesign **removed three visible data sections** and **relabelled a fourth**, then on **2026-07-29** wrapped the new status tab row in a conditional gate (`@if($tabactive['showTabs'] ?? true)`) whose toggle value is **never set by the controller** — meaning the gate currently defaults to `true` (visible), but a future config flip can hide the tab row silently.

The four data UI sections that were replaced / relabelled:

| # | Section | What it was | What it is now | Commit |
|---|---------|-------------|----------------|--------|
| 1 | Top stat row | 4 stat cards (Total Active Jobs, Client Jobs, Internal Jobs, Job Request Received) | 4 stat cards (Active Jobs, Client Jobs, Internal Jobs, Job Requests) with **hard-coded "12% / 8% / 5% / 0% vs last month" trend strings** | `fd671afc` |
| 2 | Second-row tab strip (All / Custom / Client / Job Request) | Visible link-row under the hero | **Removed entirely** | `fd671afc` |
| 3 | Filter bar — "More Filters" trigger | A `<div class="more-filters-btn">` opening `#apptrackerfilter` modal | **Removed (was already commented, but now fully deleted)** | `fd671afc` |
| 4 | Filter bar — "Add Job" button | A full-width blue button at the right end of the filter row | **Moved out of the filter bar into the hero (`jh-actions`)**; the filter bar no longer contains any primary CTA | `fd671afc` |

Plus two secondary items:

| # | Item | Status | Commit |
|---|------|--------|--------|
| 5 | Status tab row (All / Hiring / On Hold / Closed) | New in redesign, then **wrapped in `@if($tabactive['showTabs'] ?? true)` gate**; controller never sets `showTabs` | `d361e893` |
| 6 | Hero label "Active Jobs" → "Jobs Tracker", "Active Jobs" card → "All Jobs", "Client Jobs" → "Direct Jobs" | Relabelled (semantic drift — `Active Jobs` count actually counts all jobs, including hold/closed) | `9550911a` |

No sections inside the **table area** (rows, columns, drawers) were hidden in this window. The `cf8ddba5 "Improve the performance"` commit on 2026-08-20 only edited controllers (ETag + cache), not views.

---

## 2. The commit timeline (full evidence)

```
2026-07-23  fd671afc  job page hero section redesign fixed           <-- THE rewrite
2026-07-24  4e2acd3b  Client page hero section update and jobs page
                       tab and list active jobs top list               <-- tab row default status
2026-07-29  d361e893  mail issue                                       <-- @if(showTabs) gate added
2026-07-29  9550911a  job filter change                                <-- hero labels renamed
2026-07-29  edaa5146  JD Share                                          <-- unrelated
2026-07-29  2661a67b  Job after update automatically redirects me
                       back to 1st page issue fixed                     <-- unrelated
2026-08-11  ebe1d95f  job add and cv upload                            <-- only CSRF / SweetAlert
2026-08-13  e3693be7  add job click error fixed                        <-- unrelated to layout
2026-08-18  a2f5617a  pagination page-size dropdown was not working
                       (fixed)                                         <-- pagination only
2026-08-20  cf8ddba5  Improve the performance                          <-- controller only (ETag)
```

Only **4 commits** materially changed the page layout. All of them occurred in a **6-day window (Jul 23 – Jul 29)**.

---

## 3. Section-by-section diff

### 3.1 Top stat row (4 cards) — was real data, now has hard-coded trend strings

**Before `fd671afc`** (4 cards bound to controller counts):
```blade
<div class="stat-num" id="{{$tabactive['allRequisition_id']}}">{{$tabactive['allRequisition']}}</div>
<div class="stat-label">Total Active Jobs</div>
```
…repeating for `clientRequisition`, `customRequisition`, `jobRequest` (each `<a href="…/client_requisition/0">` / `/custom_requisition/0` / `/job_request/0`).

**After `fd671afc`** — visually redesigned with new CSS class `.jh-card`, but **the trend line is hard-coded**:
```blade
<div class="jh-trend jh-trend-up">
    <svg …>…</svg>
    12% vs last month          {{-- hard-coded, not bound to data --}}
</div>
```
…repeating with `8% vs last month`, `5% vs last month`, `0% vs last month`.

**Hidden / misleading data:**
- The "12% vs last month" / "8% / 5% / 0%" strings are **not computed** — they are static text in the template. A user looking at the hero sees fake trend deltas that have no relationship to the actual ES/MySQL counts. There is no view, controller, or JS calculation behind them.
- The card labels are now misleading (see §3.4).

The 4 cards still link to the correct tabs (`/jobs/custom_requisition/0`, `/jobs/client_requisition/0`, `/jobs/job_request/0`) and still show the controller's `$tabactive['…']` count in the `.jh-num` element, so **the data itself is not hidden** — only the trend text and label semantics.

### 3.2 Old second-row tab strip — REMOVED

**Before `fd671afc`** (the file ended with a *commented-out* tab strip):
```blade
{{-- <div class="d-flex flex-column {{$tabactive['allRequisition_active']}} customDivWidthJobs">
    <h5>… <a href="{{$tabactive['allRequisition_url']}}">All Jobs Openings</a></h5>
    <span class="text-gray-600 existing_data" id="{{$tabactive['allRequisition_id']}}">…</span>
</div>
… <span class="h-20px border-gray-200 border-start mx-4"></span>
… <a href="{{$tabactive['customRequisition_url']}}">Internal Jobs Openings</a>
… <a href="{{$tabactive['clientRequisition_url']}}">Client Jobs Openings</a>
… <a href="{{$tabactive['jobRequest_url']}}">Job Requests</a>
@endif --}}
```

**After `fd671afc`** — the entire commented block is **deleted** (no replacement in the old form). It is superseded by a new tab row further down (see §3.5) but with a different label set (Hiring / On Hold / Closed instead of All / Internal / Client / Job Request).

**Hidden data:**
- "Job Request Received" tab is no longer a first-class visible tab — the count remains on the stat card, but there is no clickable text label "Job Requests" in any row (the new tabs are status-based, not type-based).

### 3.3 "More Filters" trigger — REMOVED

**Before `fd671afc`** (in `jobFilterBar.blade.php`, already commented):
```blade
{{-- <div class="more-filters-btn" data-toggle="modal" data-target="#apptrackerfilter">
    <svg …>…</svg>
    More Filters
</div> --}}
```

**After `fd671afc`** — the commented block is **deleted entirely**. The new filter bar has no equivalent "More Filters" trigger; the advanced filter UI lives only in the apptracker page (`#apptrackerfilter` modal referenced nowhere on the jobs page).

**Hidden data:**
- There is no UI affordance to open the advanced filter modal from the jobs page. Users who relied on the "More Filters" entry point on this page cannot reach it.

### 3.4 Filter bar — "Add Job" button MOVED OUT

**Before `fd671afc`** (end of `jobFilterBar.blade.php`):
```blade
@if(in_array("add_jobs", $searchbar['userPermissions']))
    <button class="" data-toggle="modal" data-target="#add_staffing_partners">
       <a href="javascript:;" type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3 py-4" id="AddjobSection" style="width: 100%">
          <svg …><path d="M12 5v14M5 12h14" stroke="white" …/></svg>
          Add Job
        </a>
    </button>
@endif
```

**After `fd671afc`** — the entire Add Job button block is removed from the filter bar and **moved to the hero `jh-actions` slot** (still behind the same `@if(in_array("add_jobs", …))` gate).

**Visual impact:** the filter bar is now strictly utility (search + 4 selects); the only primary CTA on the page lives inside the hero card. This is a deliberate UX choice, not a regression — flagging it because the audit is exhaustive.

### 3.5 Status tab row — NEW but GATED

**After `fd671afc`** — added a `.jh-tabs-card` row with All / Hiring / On Hold / Closed tabs, each with a `<span class="jh-badge">…</span>` showing the live count.

**After `d361e893`** — the row was wrapped in a config gate:
```blade
@if($tabactive['showTabs'] ?? true)
<div class="jh-tabs-card">
    <a href="#" class="jh-tab jh-tab-active" data-status="all" onclick="filterJobsByStatus('all'); return false;">
        …
        <span class="jh-badge jh-badge-blue">{{ $tabactive['allTabCount'] ?? $tabactive['allRequisition'] }}</span>
    </a>
    …Hiring / On Hold / Closed / Removed (Removed added in this same commit)…
</div>
@endif
```

**Controller wiring (verified at `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:1646-1649`):**
```php
$dataCnt['hiringCount']  = $tabStatusCounts['active'];
$dataCnt['holdCount']    = $tabStatusCounts['hold'];
$dataCnt['closedCount']  = $tabStatusCounts['inactive'];
$dataCnt['allTabCount']   = $tabStatusCounts['active'] + $tabStatusCounts['hold'] + $tabStatusCounts['inactive'];
```

`$tabactive['showTabs']` is **never set anywhere** in the controller, in any other blade, or in any JS. So the gate currently evaluates to `true` and the tabs are visible.

**Hidden / latent risk:**
- A future change that sets `$tabactive['showTabs'] = false` (e.g. by tenant tier or feature flag) will silently remove the entire status tab row — the four count badges (Hiring / On Hold / Closed / Removed) would disappear from the UI without any obvious removal. **Recommend either removing the unused gate or wiring it to a documented config.**

### 3.6 Hero label semantic drift (commit `9550911a`)

| Before `9550911a` | After `9550911a` |
|---|---|
| Hero `<div class="jh-title">Active Jobs</div>` | `Jobs Tracker` |
| Card 1 label `Active Jobs` | `All Jobs` |
| Card 2 label `Client Jobs` | `Direct Jobs` |
| Card 3 label `Internal Jobs` | `Internal Jobs` (unchanged) |
| Card 4 label `Job Requests` | `Job Requests` (unchanged) |

`Active Jobs` is misleading because the card's `.jh-num` shows `$tabactive['allRequisition']`, which is the **total** count from `dataCnt()` regardless of status. The label says "Active Jobs" (or "All Jobs" after `9550911a`) but the underlying count is every requisition row the user can see.

---

## 4. What is **not** hidden (verified)

I checked the diff of every commit in §2 against `index.blade.php`. The table area, drawers, legend, modals, and JS handlers were not modified to hide data in this window.

Specifically **unchanged** between Jul 23 → Aug 20:
- Table headers: Job Details / Experience / Hiring & Location / Hiring Manager / Team / Agency / Hiring Pipeline
- Pagination block (`#pagination`)
- Pipeline legend (`@include('sections.service_company.pipeline')`)
- All 6 drawers: `kt_activitiesJob`, `kt_activitiesClientjob`, `kt_activitiesRecruiter`, `kt_activitiesTEAM`, `kt_activitiesCHM`, `status_drawer`
- 3 modals: `scheduleDetailModalStatus`, `JobDetailModalStatus`, `scheduleDetailModal_vis`
- The `fetchCandidates` pipeline (only the page-size branch was fixed on Aug 18 in `a2f5617a`)

---

## 5. Findings, ranked by severity

| Sev | Finding | Source commit |
|---|---|---|
| **HIGH** | Top stat row trend strings ("12% / 8% / 5% / 0% vs last month") are **hard-coded HTML literals**, not bound to any data source — they are decorative misinformation. | `fd671afc` |
| **MEDIUM** | The "Jobs Tracker" hero title and "Active Jobs" / "All Jobs" / "Direct Jobs" card labels are **semantically wrong**: `$tabactive['allRequisition']` is the total requisition count (all statuses), not active-only. | `fd671afc` + `9550911a` |
| **MEDIUM** | The type-based tab row (All Jobs / Internal / Client / Job Request) was removed without replacement; only status-based tabs remain. Users who navigated by type lost their visual affordance. | `fd671afc` |
| **LOW** | "More Filters" trigger (linking to `#apptrackerfilter`) was removed from the filter bar; the modal still exists on other pages but is unreachable from this page. | `fd671afc` |
| **LOW** | Status tab row is wrapped in `@if($tabactive['showTabs'] ?? true)` — the key is **never set by the controller**, so the gate is currently inert. Either remove the gate or wire it to a documented tenant/feature flag. | `d361e893` |
| **INFO** | "Add Job" CTA moved from the filter bar into the hero (`jh-actions`). Intentional consolidation, not a bug — flag only because the audit must be exhaustive. | `fd671afc` |

---

## 6. Recommended fixes (no business-logic change)

1. **HIGH — Replace hard-coded trend strings** in `resources/views/sections/service_company/jobHeader.blade.php`. Either:
   - compute the delta server-side in `dataCnt()` and bind via `$tabactive['activeTrend']`, `…['clientTrend']`, etc., OR
   - drop the trend row entirely (it adds noise and no value if the data is fake).
3. **MEDIUM — Fix label semantics.** Rename the first card back to "Total Jobs" (or split into two cards: "Active" vs "On Hold" vs "Closed") so the count matches the label. The data is already in `dataCnt()` (`hiringCount` / `holdCount` / `closedCount`).
4. **MEDIUM — Restore the type-based tab row** OR add type filters into the existing `jh-tabs-card`. The 4 tab badges already handle status; adding Custom/Client/Job Request as a second tab strip is a 30-line job.
5. **LOW — Re-add the "More Filters" trigger** that opens `#apptrackerfilter` from `jobFilterBar.blade.php`. The modal target already exists; the trigger is one `<button>`.
6. **LOW — Decide on `showTabs`.** Either delete the `@if($tabactive['showTabs'] ?? true)` wrapper in `jobHeader.blade.php:101` and `:157`, or set the key in `index()` so the gate becomes an explicit feature flag.

---

## 7. Provenance / commands used

```bash
# Recent commits to the page
git log --since="2026-07-15" --pretty=format:"%h %ad %s" --date=short \
    -- 'resources/views/service_company/jobs/index.blade.php'

# Hero redesign diff (the rewrite)
git show fd671afc -- \
    'resources/views/sections/service_company/jobHeader.blade.php' \
    'resources/views/sections/service_company/jobFilterBar.blade.php' \
    'resources/views/service_company/jobs/index.blade.php'

# Tabs gated + Removed tab added
git show d361e893 -- \
    'resources/views/sections/service_company/jobHeader.blade.php'

# Hero labels renamed
git show 9550911a -- \
    'resources/views/sections/service_company/jobHeader.blade.php'

# Default-status tweak
git show 4e2acd3b -- \
    'resources/views/sections/service_company/jobFilterBar.blade.php'

# Controller wiring for tab counts (verified)
grep -n "hiringCount\|holdCount\|closedCount\|allTabCount\|removedCount\|showTabs" \
    app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php
```

---

## 8. Files touched in the audit window (for quick grep)

- `resources/views/service_company/jobs/index.blade.php` (layout shell; only pagination + CSRF changes)
- `resources/views/sections/service_company/jobHeader.blade.php` ← **biggest delta**
- `resources/views/sections/service_company/jobFilterBar.blade.php` ← **biggest delta**
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` (`index()` + `dataCnt()`; only added the 4 tab-count keys)
- `demo/css/topCard.css` (+422 / -X lines — only styling, not data)

**No DB, ES, migration, or route changes** were required to hide these sections — every hidden/relabelled element was a pure Blade change inside one partial file (`jobHeader.blade.php`).