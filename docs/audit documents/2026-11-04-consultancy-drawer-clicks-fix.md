# Audit & Fix Report: Partner Agency Drawer Click & Tab Switch Issue

**Date:** 2026-11-04  
**Module:** Service Company / Consultancy (`service_company/consultancy`)  
**Drawer View:** `resources/views/service_company/consultancy/dviewMore.blade.php`  
**Controller:** `app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php`  

---

## 1. Problem Description
The user reported that clicking any KPI card or any navigation tab pill inside the Partner Agency Drawer (`service_company/consultancy`, e.g., InnovateSoft Technologies `#AG-4`) was not switching the tab panes:
- All 6 Hero KPI cards (All Shared Jobs, All Active Jobs, All Candidates, All Interviews, All Offers, All Joined) were unresponsive.
- In the user's screenshot, clicking the `Performance` tab pill highlighted the pill blue, but the view remained stuck on the `Overview` tab content ("About InnovateSoft Technologies").
- Clicking on the candidates KPI navigated the browser URL to `.../service_company/consultancy#candidate_list`.

---

## 2. Root Cause Analysis
1. **Undeclared Function ReferenceError (`agencyPerfFetchData`)**:
   - In `loadAgencyTabContent`, clicking the Performance tab invoked `agencyPerfFetchData(...)` directly.
   - `agencyPerfFetchData` was never declared with a `function` statement; it was only assigned later as `window.agencyPerfFetchData = cdPerfFetchData;`.
   - In strict evaluation mode, this triggered `Uncaught ReferenceError: agencyPerfFetchData is not defined`, interrupting script execution.
2. **Missing Tab Identity Mapping**:
   - Tab pills and callers used mixed casing and identifiers (`candidateDashboard`, `requisitions`, `candidate_list`, `interview_tracker`, `candidate_offers`, `candidate_joined`, `candidatedashboard`, `performance`, `jobs`, `interviews`).
   - If a caller passed a target like `'performance'`, `$('#performance')` failed to find `#candidateDashboard`.
3. **CSS Display Override Flaw on `.tab-pane.show`**:
   - The CSS previously contained `.tab-pane.active, .tab-pane.show { display: block !important; }`.
   - In Bootstrap 5, if `.show` remained on the Overview pane after `.active` was removed, `.tab-pane.show` forced the Overview pane to stay visible with `display: block !important`, obscuring lower panes.
4. **Anchor Navigation Bubbling (`href="#..."`)**:
   - KPI cards previously used `href="#requisitions"`, `href="#candidate_list"`, etc. When an unhandled error occurred, the browser executed default anchor navigation, changing the URL to `#candidate_list`.
   - Replaced all action links with `href="javascript:void(0);"` and explicit `data-target-tab` attributes.

---

## 3. Implemented Solutions
1. **Added `mapAgencyTabIdentity(rawTarget)`**:
   - Normalizes any raw tab identifier (`'performance'`, `'candidatedashboard'`, `'jobs'`, `'requisitions'`, `'candidates'`, `'candidate_list'`, `'interviews'`, `'interview_tracker'`, `'offers'`, `'candidate_offers'`, `'joined'`, `'candidate_joined'`, `'activity'`, `'overview'`) into:
     - `normTab`: for authoritative store fetching.
     - `domPaneId`: for DOM element selection (`#candidateDashboard`, `#requisitions`, `#candidate_list`, `#interview_tracker`, `#candidate_offers`, `#candidate_joined`, `#recent_activity`, `#contacts`, `#overview`).
2. **Robust `triggerAgencyDrawerTab(target, e, force)`**:
   - Prevents default anchor action and stops event propagation.
   - Removes `.active` from all tab pills and applies `.active` strictly to the target tab pill.
   - Explicitly hides all `.tab-pane` elements with `removeClass('active show').css('display', 'none')`.
   - Explicitly activates the target pane with `addClass('active show').css('display', 'block')`.
   - Wraps `loadAgencyTabContent` in a `try...catch` block to guarantee tab switching never halts on async fetch errors.
   - Aliased `window.triggerDrawerTab = window.triggerAgencyDrawerTab` for complete backward compatibility.
3. **Pre-declared `agencyPerfFetchData`**:
   - Hoisted `function agencyPerfFetchData(range, compare, force, onComplete)` so it is safe to invoke anywhere.
4. **Scoped CSS Rule Enforcement**:
   - Scoped tab panes to `.cd-scope .tab-pane { display: none !important; }` and `.cd-scope .tab-pane.active { display: block !important; }`.
   - Removed `.tab-pane.show { display: block !important; }`.
5. **Clean Delegated Event Handlers**:
   - Bound namespaced click listeners `click.agencyDrawerTab` and `click.agencyDrawerKpi` to prevent duplicate bindings or race conditions with Bootstrap's native tab handler.
6. **Cache Invalidation**:
   - Bumped cache key to `consultancyviewmore_v32` in `ServiceCompanyConsultancyController.php`.
   - Cleared Laravel view and application cache.

---

## 4. Verification Results
- Tested all 9 tab clicks sequentially via Node VM runtime (`overview`, `recent_activity`, `contacts`, `requisitions`, `candidate_list`, `interview_tracker`, `candidate_offers`, `candidate_joined`, `candidateDashboard`):
  - Result: **All 9 tabs switched with 0 errors**.
- PHP lint check (`php -l`): **Pass (0 syntax errors)**.
- Laravel view and application cache cleared successfully.
