# Service Company / Job Application Show Detail — Implementation Plan

**Subject:** Implementation Plan for Performance Optimization of Job Application Detail Page  
**Primary Files Target:**  
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`  
- `resources/views/service_company/job-applications/showdetail.blade.php`  
- `app/Helper/GeneralMethod.php`  
- `resources/views/layouts/service_company_layout.blade.php`  
**Date:** 2026-08-25  

> [!NOTE]  
> **STATUS:** **IMPLEMENTATION COMPLETED AND VERIFIED.** All priorities (P0-1 through P3) have been fully executed, tested, and verified.

---

## 1. User Review Required

> [!WARNING]  
> **Order of Cache Execution Fix:** Moving DragonflyDB cache check to the top of `show($id)` will bypass database write queries (skills and questions sync) on cache HITs. If an administrator edits job skills or questions, the cache key must be explicitly invalidated (`ats:candidateDetails:...`).

> [!IMPORTANT]  
> **Lazy Loading Drawers:** Extracting inline JavaScript and drawers from `showdetail.blade.php` into separate components will change how drawer HTML is loaded. Content will be fetched asynchronously on tab/modal open.

---

## 2. Proposed Architectural Changes

### Phase 1: Controller & Cache Optimizations (Quick Wins)

#### [MODIFY] [ServiceCompanyJobApplicationController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php)
1. **Move Cache Lookup to Line 2643:** Check DragonflyDB cache key `ats:candidateDetails:{userId}:{version}:{hash}` at the very start of `show($id)`. Return cached response immediately on hit.
2. **Remove Redundant ES Skills Search (L2936):** Delete `$params` search for `ELASTIC_CANDIDATE_SKILLS_EXP` as it is immediately overwritten by MySQL.
3. **Validate and Sanitize `currentPage` (L3162):** Ensure `$this->currentPage` is cast to an integer: `$this->currentPage = filter_var(request('currentPage'), FILTER_VALIDATE_INT) ?: 1;`.
4. **Constrain ES Queries:** Reduce `size: 10000` to `size: 50` across all ES searches and specify explicit `_source` projections.

#### [MODIFY] [GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php)
1. **Cache `available_status()` (L508):** Wrap `interview_status` DB query with `Cache::rememberForever('ref:interview_status:all', ...)`.
2. **Cache `feedback_status()` (L552):** Wrap `interview_sub_status` DB query with `Cache::rememberForever('ref:interview_sub_status:all', ...)`.
3. **Cache `currencyConverter()` (L4318):** Wrap `currencies` DB query with `Cache::remember('ref:currencies:all', 86400, ...)`.

---

### Phase 2: Timeline Bulk Loading & N+1 Elimination

#### [MODIFY] [ServiceCompanyJobApplicationController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php)
1. Extract distinct IDs from `$updateTimeLine`: `$jobIds`, `$userIds`, `$interviewIds`, `$offerIds`.
2. Execute **4 single bulk queries**:
   - `bulkCompanyDetailsByClient($jobIds)` (1 query)
   - `bulkSpokespersons($jobIds)` (1 query)
   - `bulkOfferApplications($offerIds)` (1 query)
   - `bulkInterviewFeedback($interviewIds)` (1 query)
3. Pass prepared arrays/maps in `$this->data` to `showdetail.blade.php`.

#### [MODIFY] [showdetail.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/job-applications/showdetail.blade.php)
1. Update `@foreach($updateTimeLine as $tdata)` loop to read pre-loaded arrays instead of calling `GeneralMethod::companyDetailsByClient()`, `GeneralMethod::confidentialAmontShow()`, `DB::table('users')`, `DB::table('interview_feedback')`, etc.
2. Remove embedded Blade DB queries at lines 411, 448, 1028, and 2496, replacing them with variables prepared in the Controller.

---

### Phase 3: Frontend Modularization & JS Clean Up

#### [NEW] `resources/js/candidate-detail.js`
- Extract 2,301 lines of inline JavaScript from `showdetail.blade.php` into a compiled asset file.

#### [NEW] `resources/views/components/candidate/x-candidate-header.blade.php`
- Componentize the top action bar and candidate summary strip.

#### [NEW] `resources/views/components/candidate/x-candidate-timeline.blade.php`
- Componentize the candidate activity timeline panel.

---

## 3. Verification Plan

### Automated Verification
```bash
# 1. Verify syntax of modified PHP files
php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php
php -l app/Helper/GeneralMethod.php

# 2. Run PHP Code Sniffer / Pint formatting check
./vendor/bin/pint --test

# 3. Check route list
php artisan route:list --path=job-applications
```

### Manual & Performance Verification
1. Enable Laravel Query Log (`DB::enableQueryLog()`) on `GET service_company/job-applications/1748`.
2. Verify total query count drops from **~625 queries** to **< 10 queries**.
3. Verify controller execution time drops from **~850ms** to **< 60ms**.
4. Test URL parameters with `currentPage=undefined` and verify parameter gracefully defaults to `1`.
5. Test candidate timeline, interview feedback drawer, and PDF download center to confirm 100% feature preservation.
