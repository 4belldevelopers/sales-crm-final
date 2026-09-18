# SUPER ADMIN GLOBAL EXCLUSION AUDIT

## 1. Executive Summary
This document provides a comprehensive audit of how Super Admin records (`users.is_superadmin = 1` and associated Super Admin company `companies.id = 1`) currently permeate business-facing listings, counts, search results, recommendations, DataTables, and dashboard KPI cards across the JERO SaaS platform.

The objective of this fix is to **100% exclude Super Admin records from all business-facing lists, counts, aggregations, and searches** while strictly preserving Super Admin authentication, impersonation, authorization, and dedicated administrative management tools.

---

## 2. Super Admin Identification Architecture

### Authoritative Super Admin Criteria
- **User Identification**: `users.is_superadmin = 1`
- **Company Identification**: `companies.id IN (SELECT company_id FROM users WHERE is_superadmin = 1)` (Currently resolves to `company_id = 1` named `"Super Admin"`).
- **Name-based fallback avoidance**: Normal companies/users containing "Admin" in their name (e.g., "Admin HR Services") will NOT be excluded unless `is_superadmin = 1` or their `company_id` matches a Super Admin company.

---

## 3. Inventory of Affected Endpoints & Controllers

| Module / Feature | File Path | Method / Endpoint | Current Gap | Required Exclusion |
| :--- | :--- | :--- | :--- | :--- |
| **Find Agency Discovery** | `app/Http/Controllers/ServiceCompany/ServiceCompanyCompanyController.php` | `findAgency()` | Elastic aggregation & DB fallback count only exclude `$company_id` | Exclude `superAdminCompanyIds()` from Elastic query & DB fallback |
| **Find Agency Data (AJAX)** | `app/Http/Controllers/ServiceCompany/ServiceCompanyCompanyController.php` | `findAgencyData()` | `$vendorIds` DB query & Redis cache only exclude `$company_id` | Add `->whereNotIn('id', superAdminCompanyIds())` |
| **Agency Marketplace** | `app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php` | `getAgencyList()` / `marketplace()` | Includes company ID 1 in vendor list | Add `whereNotIn('companies.id', superAdminCompanyIds())` |
| **Recommended Agencies** | `app/Http/Controllers/ServiceCompany/ServiceCompanyCompanyController.php` | `recommendedAgencies()` | Matches all vendor companies including Super Admin | Exclude Super Admin company ID |
| **Bench Candidates Listing** | `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` | `benchCandidatesData()` | Includes candidates added by Super Admin | Filter out candidates where `added_by` or `company_id` belongs to Super Admin |
| **Recruiter & Team Listing** | `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php` | `teamData()` | Includes `is_superadmin = 1` users in team lists | Add `->where('users.is_superadmin', 0)` |
| **Service Dashboard Metrics** | `app/Http/Controllers/ServiceCompany/ServiceCompanyDashboardController.php` | `index()` | Total Agencies & Active Agencies count include Super Admin | Exclude `superAdminCompanyIds()` from count queries |
| **Staffing Dashboard Metrics** | `app/Http/Controllers/StaffingCompany/StaffingCompanyDashboardController.php` | `index()` | Counts include Super Admin company | Exclude `superAdminCompanyIds()` from count queries |
| **Home Controller Elastic Search** | `app/Http/Controllers/HomeController.php` | `searchCompanies()`, `searchCandidates()` | Elastic search queries omit `must_not` for Super Admin | Add `must_not` clause for Super Admin company IDs & `is_superadmin = 1` |
| **Elasticsearch Reindex Commands** | `app/Console/Commands/IndexCompaniesToElasticsearch.php` | `handle()` | Indexes company ID 1 into `ats_companies` index | Exclude Super Admin company during reindexing or filter at query time |

---

## 4. Database Query Audit (MySQL)

### Problematic Patterns Found
1. **Raw `DB::table('companies')` without Tenancy or Super Admin Scopes**:
   `DB::table()` bypasses Eloquent global scopes. Queries fetching agencies (`where('company_type', '!=', 3)`) were missing `whereNotIn('id', [1])`.
2. **`COUNT()` and `COUNT(DISTINCT)` Aggregations**:
   Dashboard cards execute `DB::table('companies')->where('status', 'active')->count()`, resulting in Super Admin being counted in Total Agencies and Verified Agencies.

### Solutions
- Define reusable scopes `scopeExcludeSuperAdmin` in `Company` and `User` Eloquent models.
- Implement helper method `GeneralMethod::superAdminCompanyIds()` to dynamically return cached Super Admin company IDs.
- Update all raw `DB::table('companies')` and `DB::table('users')` queries to explicitly append `whereNotIn('companies.id', GeneralMethod::superAdminCompanyIds())` and `where('users.is_superadmin', 0)`.

---

## 5. Elasticsearch & Redis Audit

### Elasticsearch
- Indices affected: `ELASTIC_COMPANIES_INDEX`, `ELASTIC_CLIENT_LIST`, `ELASTIC_USERS_LIST`, `ELASTIC_CANDIDATES`.
- Search queries now include a strict `must_not` block:
  ```json
  "must_not": [
      { "terms": { "id": [1] } },
      { "term": { "is_superadmin": 1 } }
  ]
  ```

### Redis / Cache Invalidation
- Cached vendor lists (`vendor_ids_for_company_{id}`) and Find Agency cache keys (`ats:allfindagency:*`) will be invalidated by bumping `$versionKey = 'allfindagencyversion_global'` and clearing targeted Redis keys.

---

## 6. Security & Performance Strategy
- **10,000+ Concurrent Recruiters**: Exclusion is performed directly within database SQL `WHERE` clauses and Elasticsearch `bool.must_not` queries before pagination, memory allocation, or count evaluation. No memory-heavy PHP collection filtering.
- **Authentication Integrity**: `is_superadmin = 1` remains untouched in auth middleware (`SuperAdmin.php`, `EnsureOneDeviceLogin.php`, `RedirectIfAuthenticated.php`). Super Admin retains full login, impersonation, and administrative dashboard access.
