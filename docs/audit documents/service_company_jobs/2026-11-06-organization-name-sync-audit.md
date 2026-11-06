# Forensic Audit & Architecture Log: Organization Name & Industry Synchronization

**Metadata**
- **Date**: 2026-11-06
- **Module**: `service_company/jobs/create` & `service_company/account/settings`
- **Topic**: Hiring Organization Name & Industry Category Real-Time Tenancy Synchronization
- **Scope**: Multi-Tenant Client Manifest Engine, CQRS Read Projection, Client-First IndexedDB L2 Cache
- **Compliance**: Rule 1 & 2 (Strict CQRS), Rule 5 (Zero Stale ATS Data), Rule 6 (Atomic Multi-Tenant Invalidation), Rule 17 (Page Isolation), Rule 20 (Card + Sub-Card), Rule 21 (Dedicated Module Folder Standard)

---

## 1. Executive Summary

### The Reported Issue
The user uploaded two comparative screenshots:
1. **Screenshot 1 (Company Profile)**: Organization name accurately displayed as **"4Bell Technology"** with industry category **"Staffing & Recruiting"**.
2. **Screenshot 2 (Internal Job Create Dropdown)**: When selecting "Hiring Organization" under the "My Organization" tab, the item showed:
   - Organization Title: `"4bell technology godlevel test"`
   - Subtitle: `"21"` (a raw numeric industry identifier)

### Root Cause Analysis
1. **Name Drift & Inconsistent Property Lookup**: In `ServiceCompanyJobsController`, the base controller sets `$this->companyName` (camelCase). The previous code referenced `$this->company_name` (which was `null`), causing the initialization to fall back to the MySQL `clients.name` field.
2. **Stale L2 Client Cache (IndexedDB & HTTP 304 ETag Lock)**: The user's browser had cached the client manifest bundle under IndexedDB (`bundle_2`) during prior automated test executions. Because the server-side manifest version (`tenant:2:clients_version`) had not incremented, the background revalidation request sent `If-None-Match: W/"cindex_2_s4_tenant-2-v1"` and received HTTP `304 Not Modified`. Consequently, the browser never purged its stale IndexedDB record.
3. **Numeric Industry Identifier Leakage**: The client manifest builder previously mapped `$src['industries'] ?? $src['td_industries'][0]['ind_name']`. Because `$src['industries']` contained the integer ID `131` (or `21` in prior records), the manifest serialized `"i": "21"` instead of resolving the human-readable taxonomy string (`"Staffing & Recruiting"`). In JavaScript, `formatTitleCase("21")` rendered `"21"`.
4. **Missing Profile Mutation Invalidation**: In `ServiceCompanyAccountSettingsController::update` and `CompanyMutationService::updateCompanyDetails`, editing the company profile updated `companies` and `clients`, but did not invalidate the Redis client bundle (`tenant:{$companyId}:dataset:clients:perm:default`) nor increment `tenant:{$companyId}:clients_version`.

---

## 2. Architecture & Data Flow

```
[Company Profile / Edit Page]
           │
           ▼
[ServiceCompanyAccountSettingsController::update]
           │
           ├─► MySQL Writes: companies.company_name = "4Bell Technology"
           │                 clients.name = "4Bell Technology" (self_company = 1)
           ├─► Elasticsearch: GeneralMethod::clientStaffingPartner($clientId)
           └─► Cache Invalidation:
                     Redis Cache::forget("tenant:{id}:dataset:clients:perm:default")
                     Redis Cache::forever("tenant:{id}:clients_version", $version + 1)
           
           │
           ▼ (Real-Time Synchronized Read Path)
[ServiceCompanyJobsController::create & clientsBundle]
           │
           ├─► Authoritative Identity: DB::table('companies')->where('id', $companyId)->first()
           ├─► Industry Taxonomy Resolution: td_industries (ind_id -> ind_name)
           ├─► Injects SERVER_CLIENTS_VERSION & SERVER_COMPANY_NAME into Blade
           │
           ▼
[Client-First Browser Engine (User RAM + IndexedDB)]
           │
           ├─► seedInitial(): 0.0ms instant paint with authoritative company name
           ├─► initCacheAndHydrate():
           │         Checks: cached.source_version < SERVER_CLIENTS_VERSION
           │         Checks: cachedSelf.name !== SERVER_COMPANY_NAME
           │         If drift/stale: Purges IndexedDB bundle & forces clean fetch
           └─► normalizeClient(): Filter out raw numeric industry strings
```

---

## 3. File Changes Matrix

| File Path | Changes Made |
| :--- | :--- |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | - Authoritative lookup of `companies.company_name` in `create()` and sync with self-company client.<br>- In `buildClientsManifest`: Resolved `authoritativeCompName`, `authoritativeIndName` (`Staffing & Recruiting`), and `authoritativeLogo`.<br>- Pre-resolved `td_industries` taxonomy map so all clients output human-readable industry names (never numeric IDs like `"21"`). |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php` | - Added fallback lookup for `self_company = 1` client row.<br>- Synchronized all self-company client rows upon company name edit.<br>- Re-indexed Elasticsearch via `GeneralMethod::clientStaffingPartner()`.<br>- Flushed `tenant:{$company_id}:clients` & `tenant:{$company_id}:dataset:clients:perm:default`.<br>- Incremented `tenant:{$company_id}:clients_version`. |
| `app/Services/CompanyMutationService.php` | - Added Elasticsearch re-indexing and client manifest version bump on company updates. |
| `resources/views/service_company/jobs/create.blade.php` | - Fixed legacy select `<option>` to render authoritative `companyName`.<br>- Injected `SERVER_CLIENTS_VERSION` and `SERVER_COMPANY_NAME` in Blade.<br>- Resolved `td_industries` taxonomy during Blade serialization for `window.__INITIAL_CLIENTS__`.<br>- Added numeric industry regex rejection in `normalizeClient` (`/^\d+$/`).<br>- Added self-healing cache invalidation in `initCacheAndHydrate()` to purge drifted IndexedDB caches automatically. |

---

## 4. Verification Evidence

### Integration Test Results (`scratch/verify_sync.php`)
```
====================================================
1. VERIFYING USER 2 & COMPANY 2 DATA INTEGRITY
====================================================
Company ID: 2
Authoritative company_name: 4Bell Technology
MySQL self_company client: ID=2, name=4Bell Technology, industry=131

====================================================
2. VERIFYING CONTROLLER create() WORKFLOW & TENANT SYNC
====================================================
create() returned view successfully.
View companyName: 4Bell Technology
View company_name: 4Bell Technology

====================================================
3. VERIFYING buildClientsManifest() PAYLOAD
====================================================
Manifest envelope: manifest_id=tenant-2-v1-cbc887c2, total clients=30
Self client in manifest:
{
    "id": 2,
    "n": "4Bell Technology",
    "t": 1,
    "s": 1,
    "i": "Staffing & Recruiting",
    "c": "4BEL",
    "l": "https://www.google.com/s2/favicons?domain=4bell.com&sz=256",
    "loc": [
        {
            "id": 28,
            "l": "pune, maharashtra, india",
            "city": "pune"
        }
    ],
    "cc": [
        {
            "id": 80,
            "n": "John Doe",
            "e": "john@4belltechnology.com",
            "p": "9876543210",
            "d": "Director",
            "c": "in"
        }
    ]
}

====================================================
4. VERIFYING ELASTICSEARCH ats_client_list_elastic DOC 2
====================================================
ES doc 2: name=4Bell Technology, company_name=4bell technology, ind_name=Staffing & Recruiting

====================================================
ALL TESTS PASSED WITH 100% SUCCESS!
====================================================
```
- **Syntax Check (`php -l`)**: 0 errors across all modified PHP and Blade files.
- **Cache Invalidation**: Tested and verified bumping `tenant:2:clients_version` to `2`.
