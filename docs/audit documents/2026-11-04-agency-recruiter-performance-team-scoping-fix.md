# Agency Drawer Recruiter Performance Team Scoping Fix

**Date:** 2026-11-04  
**Scope:** Staffing Partner / Agency Drawer (`service_company/consultancy`), Client Performance Analytics  
**Affected Files:**
- `app/Services/Analytics/ClientPerformanceAnalyticsService.php`
- `app/Services/Analytics/ClientPerformanceScopeResolver.php`
- `tests/Feature/AgencyDrawerRecruiterPerformanceTeamScopingTest.php`

---

## 1. Problem Statement & Root Cause

### Reported Issue
In the Staffing Partner / Agency Drawer (`service_company/consultancy`), under the **Recruiter Performance** card, the viewer (User #2: Vibhanshu Jaiswal, belonging to Company #2 - 4Bell Technology) was listed in the table with 9 candidate submissions.
The user requested: **"onli list here to agency team"** with a screenshot highlighting and circling Vibhanshu Jaiswal.

### Forensic Root Cause
1. **Unscoped Recruiter Construction:**
   In `ClientPerformanceAnalyticsService::getPerformanceDashboard()`, `buildRecruiters($cAggs['recruiters_bucket']['buckets'] ?? [], $scope['auth_company_id'])` passed `$authCompanyId` (Company #2, the viewer company), but `$companyId` was never evaluated inside `buildRecruiters`.
   When Elasticsearch returned term buckets for `submitted_by` (User #2 with 9 submissions, User #5 with 1 submission), all buckets were added to `$items` without checking the user's company membership.
2. **Missing Shared Job Constraint in Agency Candidate Filter:**
   In `ClientPerformanceScopeResolver.php`, the agency candidate filter lacked the `['terms' => ['jobId' => $sharedJobIds]]` constraint present in direct client scoping. Consequently, candidates belonging to non-shared jobs (Jobs 1, 6, 7) were matching due to legacy `ClientComID = 4` test data, which inflated candidate counts and populated User #2's submissions.
3. **Agency Team Identity:**
   The Partner Agency is InnovateSoft Technologies (Company #4). Its team members in Elasticsearch (`local_users_list_elastic`) are Daksh Sharma (User #5, Recruiter) and Varsha Sharma (User #4, Admin). Vibhanshu Jaiswal belongs to 4Bell Technology (Company #2, the viewer/service company) and must never appear in the Agency's Recruiter Performance card.

---

## 2. Architectural Solution & Implementation

### 1. Scoped Agency Candidate Filter (`ClientPerformanceScopeResolver.php`)
Added the strict shared job IDs constraint to the agency `candidate_filter`:
```php
[
    'bool' => [
        'should' => [
            ['terms' => ['jobId' => ! empty($sharedJobIds) ? array_values($sharedJobIds) : [-999999]]],
            ['terms' => ['job_id' => ! empty($sharedJobIds) ? array_values($sharedJobIds) : [-999999]]],
        ],
        'minimum_should_match' => 1,
    ],
],
```
This guarantees that only candidates submitted on genuinely shared jobs between the Service Company and the Agency are aggregated.

### 2. Strict Agency Team Resolution & Population (`ClientPerformanceAnalyticsService.php`)
1. **Dynamic Target Company Identification:**
   ```php
   $isAgency = ($scope['client_type'] ?? '') === 'agency';
   $targetCompanyId = $isAgency ? (int) ($scope['client_id'] ?? 0) : (int) ($scope['auth_company_id'] ?? 0);
   $recruiters = $this->buildRecruiters($cAggs['recruiters_bucket']['buckets'] ?? [], $targetCompanyId, $isAgency);
   ```
2. **0% MySQL User Profile Resolution (`resolveUserProfile`):**
   - Checks DragonflyDB/Redis key `ats:user_brief:{$userId}`.
   - On cache miss, queries Elasticsearch index `local_users_list_elastic` (with fallback to `DB::table('users')` for safety).
   - Resolves `name`, `company_id`, `company_name`, and `avatar`.
   - Caches profile in Redis with 24-hour TTL (`86400s`).
3. **Strict Company Tenancy Guard in `buildRecruiters`:**
   - In agency mode (`$isAgency === true`), any recruiter whose `company_id !== $targetCompanyId` (e.g. Vibhanshu Jaiswal, Company #2) is strictly skipped (`continue;`).
4. **Agency Team Member Population (`fetchAgencyTeamMembers`):**
   - If active recruiter items are fewer than the limit (5), queries `local_users_list_elastic` for all team members belonging to `$targetCompanyId`.
   - Populates remaining agency team members (e.g. Varsha Sharma) with baseline counts (`submissions: 0`, `joins: 0`, `join_rate_pct: 0.0%`), ensuring the full agency team is visible without displaying host company users.

---

## 3. Verification & Test Coverage

### Automated Tests
- `tests/Feature/AgencyDrawerRecruiterPerformanceTeamScopingTest.php`:
  1. `test_agency_drawer_recruiter_performance_strictly_lists_agency_team`: PASSED
  2. `test_agency_candidate_filter_strictly_scopes_to_shared_job_ids`: PASSED
  3. `test_agency_top_roles_only_lists_shared_jobs`: PASSED
- `tests/Feature/ClientDrawerCandidateVisibilityRuleTest.php`: 11 PASSED
- `tests/Feature/DirectClientDrawerAttributionSecurityTest.php`: 39 PASSED
- **Total:** 53 passed (163 assertions) with 0 regressions.

### Live Verified Output for Agency #4:
- **Roles:** Total 1 (Job #2: junior java developer, 1 opening, 5 submissions, 1 join, 20.0% conversion).
- **Recruiters:** Total 2
  - **Daksh Sharma** (User #5, InnovateSoft Technologies): 1 submission, 0 interviews, 0 offers, 0 joins.
  - **Varsha Sharma** (User #4, InnovateSoft Technologies): 0 submissions, 0 interviews, 0 offers, 0 joins.
- **Host Company Users (e.g. Vibhanshu Jaiswal):** 0 (100% excluded).
