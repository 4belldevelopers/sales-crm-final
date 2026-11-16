# Service Company Profile — Offers Tab Addition & Pipeline Data Audit Changelog

## 1. Metadata
- **Module:** Service Company Profile (`service_company/profile`)
- **Author:** Antigravity AI
- **Date:** 2026-11-16
- **Status:** Completed & Verified
- **Standards Applied:** Rule 1 (CQRS Writes), Rule 2 (CQRS Reads via Elasticsearch), Rule 7 (Zero Regressions), Rule 20 (Card + Sub-Card Enterprise Standard & Mandatory Table Header Background), Rule 21 (Audit Documentation Storage Standard), Rule 24 (Default Tenant Timezone Display).

---

## 2. Executive Summary
On the Service Company Recruiter Profile view (`http://localhost/git/JWMaincode28June2026/service_company/profile`), the top summary metrics featured 7 KPI cards: All Jobs, Active Jobs, Candidates, Interviews, Offers, Joining, and Current Rank. However, the secondary tab navigation only contained tabs for *My Details*, *My Jobs*, *Candidates*, *Interviews*, *Joinings*, *My Rank*, *Dashboard*, and *Settings* — completely omitting the **Offers** tab. Additionally, the Offers KPI card was triggering the `#candidate` tab instead of a dedicated Offers view, and joining data required validation against ATS pipeline master statuses (`offer = 5`, `joining = 6`).

This update:
1. Adds the dedicated **Offers** tab (`href="#offers"`) right before the **Joinings** tab (`href="#joinings"`), matching the sequence of the top KPI cards.
2. Directs the Offers KPI card click event directly to `#offers`.
3. Introduces high-performance Elasticsearch-backed endpoint `team.offerdata_ajax` querying `ELASTIC_CANDIDATES` filtered strictly by `job_master_status_id = 5` and `submitted_by = $userId`.
4. Adds dedicated AJAX table renderer `resources/views/service_company/ajax_pages/team-candidate-offer-ajax.blade.php` with 8 columns (Checkbox, Name, Jobs, Job Status, Source, Offer Amount, Offer Date, Stage) and tenant timezone conversion (`GeneralMethod::defaultTimeZone($company_id)`).
5. Enforces Rule 20 mandatory table header row background (`background: #f8fafc; border-bottom: 1px solid #e2e8f0;`) across both `#myTable_offers` and `#myTable_joining`.
6. Verified that joining data correctly corresponds to `job_master_status_id = 6` (`joining = 6`), with live verification confirming candidate retrieval across both statuses.

---

## 3. Architecture & Data Flow
```
User clicks "Offers" tab (or "Offers" KPI card)
  │
  ▼
ProfileTabManager.onTabActivated('offers')
  │
  ▼
fetchTeamCandidateOffers(page)
  │ [HTTP GET /service_company/team_ajax_offerdata/offerdata?userId=X&sortingfilter=Y&country=Z&cus_search=W]
  ▼
ServiceCompanyTeamController::offerdata_ajax()
  │
  ├─► Elasticsearch ELASTIC_CANDIDATES:
  │     Query: must [ submitted_by = $userId, job_master_status_id = 5 ] + search + country filter
  │     Sort: of_created_date / of_offer_amount / wiseId
  │     Result: hits total + hits array
  │
  ▼
Render Blade: service_company.ajax_pages.team-candidate-offer-ajax
  │
  ▼
JSON Response: { html, total, pagination, _telemetry }
  │
  ▼
DOM Insertion:
  - #teamoffersJobsListajax -> <tr> rows with candidate details, job status, offer amount, timezone-converted offer date, and stage badge
  - #paginationteamoffersjobs -> pagination links
```

---

## 4. File Changes Matrix

| File Path | Nature of Change | Summary of Modifications |
| :--- | :--- | :--- |
| `routes/service.php` | Update | Registered `team.offerdata_ajax` endpoint (`Route::get('team_ajax_offerdata/offerdata', [ServiceCompanyTeamController::class, 'offerdata_ajax'])`). |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php` | Update | Implemented `offerdata_ajax` querying `ELASTIC_CANDIDATES` with `job_master_status_id = 5`, sort handling, telemetry, and view rendering. |
| `app/Services/ServiceCompanyProfileService.php` | Update | Synchronized `scpOffersTotal` query in `getKpiMetrics` to strictly check `['term' => ['job_master_status_id' => 5]]` for 100% parity with offer definition. |
| `resources/views/service_company/ajax_pages/team-candidate-offer-ajax.blade.php` | New File | Dedicated table body renderer displaying candidate avatar, designation, job, job status, source, offer amount, Rule 24 tenant timezone offer date, and stage badge. |
| `resources/views/service_company/ajax_pages/team-candidate-join-ajax.blade.php` | Update | Enhanced date and time formatting to resolve tenant timezone (`GeneralMethod::defaultTimeZone($company_id)`) per Rule 24. |
| `resources/views/service_company/profile/index.blade.php` | Update | - Added Offers tab before Joinings in `.scp-tabs-card`.<br>- Linked Offers KPI card click to `#offers`.<br>- Added `#offers` tab pane with search, sort, location dropdowns, and `#myTable_offers`.<br>- Styled `#myTable_offers` and `#myTable_joining` header rows with `#f8fafc` background (Rule 20).<br>- Integrated `fetchTeamCandidateOffers` into `ProfileTabManager`, debounced search, and pagination. |

---

## 5. 15.6" Screen Fit & UI/UX Standards
- **Screen Formats:** Verified on both 1366×768 and 1920×1080 display viewports.
- **Header Background:** Table headers (`#myTable_offers thead tr` and `#myTable_joining thead tr`) employ `#f8fafc` background with crisp `#e2e8f0` border, preventing unanchored floating headers (Rule 20).
- **Tab Bar Elasticity:** The `.scp-tabs-card` retains `overflow-x: auto; scrollbar-width: none;` allowing smooth navigation without page wrapping or horizontal overflow.

---

## 6. Verification Evidence
1. **PHP Syntax Validation:** `php -l` passed with zero errors across all modified PHP and controller files.
2. **Offers Endpoint Direct Test:**
   - User 2: `team_ajax_offerdata/offerdata` returned HTTP 200, Total 1 (`Babji Tadipathri`, `500,000/A`, `06 Oct 26 06:09 PM IST`, `Offered`).
3. **Joinings Endpoint Direct Test:**
   - User 2: `team_ajax_joiningdata/joiningdata` returned HTTP 200, Total 0 ("No data available in table", as User 2 has 0 candidates in Joined status 6).
   - User 5: `team_ajax_joiningdata/joiningdata` returned HTTP 200, Total 1 (`Shared By`, `100000/A`, `25 Sep 26 10:59 AM IST`, `Joined`).
4. **Profile View Full Render:** Tested full rendering of `ServiceCompanyProfileController@index`, confirming presence of `href="#offers"`, `id="offers"`, `id="joinings"`, `offertableLoad`, and `fetchTeamCandidateOffers`.
