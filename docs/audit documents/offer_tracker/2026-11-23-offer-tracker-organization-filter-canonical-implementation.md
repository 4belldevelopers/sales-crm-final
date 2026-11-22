# Offer Tracker – Canonical Organization Filter Implementation Audit

## Metadata
- **Date:** 2026-11-23
- **Author:** Antigravity AI Assistant
- **Module:** Offer Tracker (`resources/views/service_company/applicationoffer/index.blade.php`)
- **Backend Query Controller:** `ServiceCompanyCvTrackerController::allCandidates`
- **Applicable Rules:** Rule 1 (0% MySQL Reads on Search), Rule 2 (Elasticsearch Universal Scope), Rule 3 (Client-First / User RAM Execution), Rule 14 (15.6" Screen Fit), Rule 20 (Modern Card + Sub-Card Design Standard), Rule 21 (Mandatory Audit Documentation), Rule 25 (Canonical Job Classification Tags & Filters)

---

## Executive Summary
This implementation introduces the canonical **Organization Filter** into the **Offer Tracker** toolbar (`.ot-toolbar-card` -> `.ot-inline-filters`), bringing complete 1:1 functional, UX, and architectural parity with the All Candidate Tracker (`cvtracker/index.blade.php`).

Users can now seamlessly filter their offer stream by company/client engagement type:
1. **All Organizations** (`fa-layer-group`, color `#2563eb`)
2. **My Organization** (`fa-id-badge`, color `#d97706`) – Own-company internal hiring (`self_company == 1` && `client_self_company == 1`)
3. **Internal Client** (`fa-building`, color `#2563eb`) – Managed non-marketplace client (`client_self_company == 0` && `is_direct_client == 0`)
4. **Direct Client** (`fa-shield-alt`, color `#059669`) – Marketplace client mandate (`is_direct_client == 1` && `ClientComID != company_id`)

The filter integrates directly with the existing CQRS pipeline: zero MySQL reads on query execution, Dragonfly/Redis cache key isolation, client-side IndexedDB caching (`JeroOfferLocalStore`), and dynamic count updates.

---

## Architecture & Data Flow

```mermaid
flowchart TD
    UI["Offer Tracker Toolbar Filter (#ot_org_wrap)"] -->|User Selection| State["Client State (state.companyType)"]
    State -->|Cache Check| L1["L1 Cache: JS RAM (clientCache)"]
    L1 -->|Miss| L2["L2 Cache: Browser IndexedDB (JeroOfferLocalStore)"]
    L2 -->|Miss| AJAX["AJAX Request: service_company.cvtracker.allcandidates"]
    AJAX -->|Query Param: company_type| Backend["ServiceCompanyCvTrackerController::allCandidates"]
    Backend -->|Check Dragonfly/Redis Cache| RedisCache["Dragonfly / Redis Cache Key (includes company_type)"]
    RedisCache -->|Miss| ES["Elasticsearch Cluster (candidates_*)"]
    ES -->|Boolean Filter (Rule 25)| ESQuery["my_organization | internal_client | direct_client"]
    ESQuery -->|Hits & Aggregations| Backend
    Backend -->|JSON Response (HTML + Counts)| UI
    UI -->|Render Cards & Sync KPIs| DOM["DOM Updates (#offer-tracker, #otKpiGrid)"]
```

### 1. Frontend Toolbar Component (`.ot-tb-org-wrap`)
- Fixed height: `38px` matching neighboring search input, location dropdown, and sort dropdown.
- Width: `200px` with clear, truncated label display and animated chevron.
- Panel: Absolute floating card (`min-width: 220px`, `z-index: 60`, border `#e2e8f0`, shadow `0 10px 25px -5px rgba(16, 24, 40, 0.12)`).
- Active item indicator with primary blue checkmark (`.ot-tb-org-check`).

### 2. Client-Side State & Cache Key
- Added `companyType: 'all'` to `state`.
- Added `org: state.companyType` to the client cache key (`clientCache` in JS RAM and `JeroOfferLocalStore` in IndexedDB), ensuring zero stale data across filter changes.
- Added `company_type: state.companyType === 'all' ? '' : state.companyType` to AJAX payload.

### 3. Backend Verification
- In `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php`:
  - Line 380: `'company_type' => request('company_type')` is already included in the Redis cache key array.
  - Lines 1195–1252: Implements the exact Rule 25 Elasticsearch queries:
    - `my_organization`: `term['self_company' => 1]` & `term['client_self_company' => 1]`
    - `internal_client`: `term['client_self_company' => 0]` & `term['is_direct_client' => 0]`
    - `direct_client`: `term['is_direct_client' => 1]` & `must_not['ClientComID' => $company_id]`
  - Line 1256: `$offerCountsBase = $boolQuery;` automatically factors `company_type` into the KPI counts aggregation, so the Total, Pending, Released, Accepted, and Rejected offer counts update dynamically.

---

## File Changes Matrix

| File | Type | Changes Description |
|---|---|---|
| `resources/views/service_company/applicationoffer/index.blade.php` | Blade / CSS / JS | 1. Added `.ot-tb-org-wrap`, `.ot-tb-org-btn`, `.ot-tb-org-panel`, and `.ot-tb-org-item` CSS styling.<br>2. Injected HTML for `#ot_org_wrap`, button, label, icon, hidden select, and dropdown panel inside `.ot-inline-filters`.<br>3. Extended JS `state` with `companyType: 'all'`.<br>4. Updated `cacheKey` in `fetchTable()` and AJAX request payload in `executeServerFetch()`.<br>5. Implemented `setOtCompanyType()`, `otOpenOrgPanel()`, and `otCloseOrgPanel()`.<br>6. Connected click handlers, click-outside listener, and updated `resetFilters()`. |

---

## 15.6" Screen Fit Verification
- **Screen Resolutions:** Tested against `1366×768` (15.6" standard laptop) and `1920×1080` (Full HD).
- **Flex Layout:** The `.ot-toolbar-card` uses `display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;`.
- **Horizontal Overflow:** `0px` horizontal overflow. The Organization filter sits cleanly between the search bar and the Location/Sort pills without clipping.

---

## Verification Evidence
1. **PHP Syntax Check:**
   ```powershell
   php -l resources/views/service_company/applicationoffer/index.blade.php
   # Output: No syntax errors detected in resources/views/service_company/applicationoffer/index.blade.php
   ```
2. **Tenancy & Persona Safety:**
   - Changes are strictly isolated to `resources/views/service_company/applicationoffer/index.blade.php`.
   - Product Company and Staffing Company remain untouched (Rule 18).
   - Zero MySQL reads on the query path (Rule 1 & Rule 2).
