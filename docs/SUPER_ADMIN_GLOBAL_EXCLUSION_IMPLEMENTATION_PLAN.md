# SUPER ADMIN GLOBAL EXCLUSION IMPLEMENTATION PLAN

## 1. Objectives
- Completely exclude Super Admin records from all business-facing lists, counts, KPIs, search results, DataTables, recommendations, and analytics.
- Ensure 0% visual or numerical leakage of Super Admin in `/service_company/find-agency` and all other agency/candidate/recruiter/company listings.
- Maintain high performance (10,000+ concurrent users) via query-level DB/Elasticsearch filtering.
- Maintain 100% functionality for Super Admin login, auth middleware, and administrative control panels (`/super-admin/*`).

---

## 2. Technical Implementation Phases

### Phase 1: Authoritative Helper & Model Scopes
- Add `GeneralMethod::superAdminCompanyIds()` returning an array of company IDs owned by super admin users (`users.is_superadmin = 1`). Cache for 300s.
- Add `GeneralMethod::superAdminUserIds()` returning an array of user IDs where `is_superadmin = 1`. Cache for 300s.
- Add `scopeExcludeSuperAdmin` to `App\Models\Company` and `App\Models\User`.

### Phase 2: MySQL Query Updates
- **Find Agency (`ServiceCompanyCompanyController.php`)**:
  - Update `findAgency()` fallback counts: filter `whereNotIn('id', GeneralMethod::superAdminCompanyIds())`.
  - Update `findAgencyData()` `$vendorIds` query: filter `whereNotIn('id', GeneralMethod::superAdminCompanyIds())`.
- **Marketplace & Agency Lists (`ServiceCompanyConsultancyController.php`, `StaffingCompanyConsultancyController.php`)**:
  - Exclude Super Admin company IDs from company query builders.
- **Candidate & Recruiter Lists (`ServiceCompanyBenchCandidatesController.php`, `ServiceCompanyTeamController.php`)**:
  - Exclude Super Admin users (`where('is_superadmin', 0)`) and Super Admin companies.
- **Dashboard Counters (`ServiceCompanyDashboardController.php`, `StaffingCompanyDashboardController.php`)**:
  - Apply `superAdminCompanyIds()` exclusion to all agency/company/recruiter `count()` queries.

### Phase 3: Elasticsearch Query Updates
- Update search and aggregation payloads in `ServiceCompanyCompanyController.php` and `HomeController.php` to add `must_not` terms:
  ```php
  'must_not' => [
      ['terms' => ['id' => GeneralMethod::superAdminCompanyIds()]],
      ['term' => ['is_superadmin' => 1]]
  ]
  ```

### Phase 4: Redis & Cache Clearing
- Increment global version key `allfindagencyversion_global`.
- Flush vendor ID caches (`vendor_ids_for_company_*`).

---

## 3. Verification Plan
- **Find Agency Page Test**: Open `/service_company/find-agency`, verify total count drops from 3 to 2, and "Super Admin" is not rendered.
- **Search Test**: Query "Super Admin" in Find Agency search bar; verify zero results returned.
- **Dashboard KPI Test**: Verify agency, candidate, and recruiter KPI card totals exclude Super Admin.
- **Auth & Permission Test**: Login as Super Admin; verify administrative functionality remains 100% operational.
- **Regression Test**: Ensure regular companies and candidates are displayed accurately without side effects.
