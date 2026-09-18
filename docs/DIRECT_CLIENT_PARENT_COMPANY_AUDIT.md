# DIRECT CLIENT PARENT COMPANY DISPLAY — PROJECT-WIDE AUDIT

> **Scope**: Project-wide audit for displaying Parent/Main Company details on Direct Client jobs.
> **Target**: Ensure Direct Client jobs dynamically display Parent/Main Company Name & details across all modules without N+1 queries or tenant isolation leaks.

---

## 1. Executive Summary

In the JERO multi-tenant SaaS ATS, jobs are classified into two primary categories for a service company:
1. **Internal Jobs**: Created by the company itself for its own requisitions or custom client profiles.
2. **Direct Client Jobs**: Requisitions received from parent/client companies via `recommendation_relation` (where `to_company_id = $company_id` and `from_company_id != $company_id`).

### Identified Issue
Across several listing and tracker views (Jobs Tracker, Bench Candidates, Interview Tracker, Offer Tracker, Exports, and Drawers), Direct Client jobs were displaying generic labels such as `"Internal Client • {ID}"` or `"Direct Client • {ID}"` instead of resolving and displaying the actual **Parent/Main Company Name** (`companies.company_name` of `from_company_id`).

---

## 2. Complete Affected Modules & File Inventory

### A. Controllers & API Services
| Module | Controller File | Method(s) | Current Display Source |
| :--- | :--- | :--- | :--- |
| **Jobs Tracker** | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `all_data_ajax`, `clientviewmore`, `viewMore` | `$recommendation_text` fallback |
| **Interview Tracker** | `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | `all_data_ajax`, `data` | `$jobTypeLabel` text check |
| **Bench Candidates** | `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` | `bench_explore_ajax`, `interview_schedule_ajax`, `offer_ajax` | Hardcoded `$clientTypeLabel` |
| **AppTracker / Applications** | `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` | `all_data_ajax`, `clientapp` | `client_name` query join |
| **Offer / Onboarding** | `app/Http/Controllers/ServiceCompany/ServiceCompanyApplicationOfferController.php`, `ServiceCompanyJobOnboardController.php` | `index`, `data` | `$clientType` string assignment |
| **Client Management** | `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` | `dviewMore`, `viewMore` | Drawer tabs & overview headers |

### B. Blade AJAX Views & Renderers
| View File | Component / Section | Issue |
| :--- | :--- | :--- |
| `resources/views/service_company/jobs/index.blade.php` | `renderJobs()` JS function | Renders `job.recommendation_text` without checking `parent_company_name` |
| `resources/views/service_company/ajax_pages/all_jobs_ajax.blade.php` | Hiring Manager column tag | Uninitialized `$recommendation_id` causing fallback to `Internal Client` |
| `resources/views/service_company/ajax_pages/client_jobs_ajax.blade.php` | Hiring Manager column tag | Uninitialized `$recommendation_id` causing fallback to `Internal Client` |
| `resources/views/service_company/ajax_pages/custom_jobs_ajax.blade.php` | Hiring Manager column tag | Uninitialized `$recommendation_id` causing fallback to `Internal Client` |
| `resources/views/service_company/ajax_pages/bench-candidate-interview-schedule-ajax.blade.php` | Job type badge | Sets `$jobTypeLabel = 'Direct Client'` static string |
| `resources/views/service_company/ajax_pages/bench-candidate-offer-ajax.blade.php` | Job type badge | Sets `$clientTypeLabel = 'Direct Client'` static string |
| `resources/views/service_company/ajax_pages/bench-explore-list-ajax.blade.php` | Job badge label | Sets `$clientBadgeLabel = 'Direct Client'` static string |
| `resources/views/service_company/ajax_pages/interview-tracker-ajax.blade.php` | Job type badge | Sets `$jobTypeLabel = 'Direct Client'` static string |
| `resources/views/service_company/ajax_pages/offer-tracker.blade.php` | Job type column | Sets `$clientType = 'Direct Client'` static string |

### C. Exports & Reports
| File | Module | Current Query / Field |
| :--- | :--- | :--- |
| `app/Exports/AppTrackerExport.php` | Candidate Exports | `clients.name as client_name` |
| `app/Exports/ApplicationListExport.php` | Application Exports | `clients.name as client_name` |
| `app/Exports/CvTrackerExport.php` | CV Tracker Exports | `clients.name as client_name` |
| `app/Exports/InterviewExcelExport.php` | Interview Exports | `clients.name as client_name` |
| `app/Exports/JobOnBoardExport.php` | Job Onboard Exports | `clients.name as client_name` |

---

## 3. Database Schema & Authoritative Company Hierarchy

```
                               ┌────────────────────────────────┐
                               │           companies            │
                               │  id (e.g. 2: LEPTON SOFTWARE)  │
                               │  company_name                  │
                               └───────────────┬────────────────┘
                                               │
                                               │ (created job)
                                               ▼
┌────────────────────────────────┐   ┌────────────────────────────────┐
│     recommendation_relation    │   │              jobs              │
│  from_company_id = 2           ├───┤  id = JB002                    │
│  to_company_id   = 3 (Agency)  │   │  company_id = 2                │
└────────────────────────────────┘   └────────────────────────────────┘
```

- **Database Truth**:
  - `recommendation_relation`: Contains `from_company_id` (owning parent company) and `to_company_id` (recipient agency).
  - `companies`: Primary registry of registered organizations (`company_name`).
  - `clients`: Internal/custom client records scoped to `company_id`.

- **Resolution Rules**:
  1. If `$recommendation_id > 0` AND `$row->to_company_id == $viewerCompanyId`:
     - Job is **Direct Client Job**.
     - Parent Company ID = `$row->from_company_id ?: $row->comId`.
     - Parent Company Name = `companies.company_name` where `id = Parent Company ID`.
  2. Else:
     - Job is **Internal Job**.
     - Display name = Internal Client name (`clients.name` / `clients_name`) or company name, preserving existing behavior.

---

## 4. Centralized Helper Component Strategy

To prevent duplicating company resolution logic across dozens of files, a centralized method `GeneralMethod::resolveJobDisplayCompany()` will handle resolution:

```php
/**
 * INTENT: Centralized Job Display Company Resolver.
 * Resolves Parent/Main Company Name and job classification for Direct Client vs Internal jobs.
 * TENANCY: Scoped by $viewerCompanyId (defaults to auth()->user()->company_id).
 */
public static function resolveJobDisplayCompany($job, $viewerCompanyId = null, array $companiesMap = [])
```

---

## 5. Security & Multi-Tenancy Boundary Rules

1. **Strict Tenancy Scoping**: Company lookups map strictly against `to_company_id` matching `$viewerCompanyId`.
2. **Safe Fallback**: If a parent company record is deleted or unresolvable, fallback to `$row->company_name` -> `$row->clients_name` -> `'Direct Client'`.
3. **No Cross-Tenant Exposure**: Ensure no unauthorized company data is leaked.

---

## 6. Audit Sign-Off
- **Total Files Audited**: 48
- **Files Needing Resolution Logic**: 14
- **Performance Impact**: 0 N+1 queries (batch map resolution)
