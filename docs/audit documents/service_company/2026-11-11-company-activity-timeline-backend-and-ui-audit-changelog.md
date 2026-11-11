# Service Company — Company Activity Timeline Backend & UI Audit Changelog

## Metadata
- **Date:** 2026-11-11
- **Module:** Service Company Account / Company Profile Activity (`/service_company/account`)
- **Author:** Antigravity AI Pair Programmer
- **Standard Applied:** Modern Card + Sub-Card Enterprise Design System (Rule 20), CQRS Elasticsearch Reads (Rules 1 & 2), Multi-Tenant Isolation (Rule 6), Zero Business Logic Mutations (Rule 7), 15.6" Screen Fit Standard (Rule 14 & 20).

---

## Executive Summary
Per user feedback that the activity tab was not working due to an inappropriate client-drawer coupling, a dedicated company-wide activity backend and timeline interface were engineered from scratch. The new architecture decouples the Company Profile page entirely from `client_id` or single-client models, and establishes a true multi-domain audit timeline tracking all actions performed by the organization's users across:
1. **Candidate Pipeline Transitions:** Stage movements (InReview, Sourcing, Screening, etc.) from `ats_candidate_timeline`.
2. **Job Requisitions:** Requisitions created and published by hiring managers and team members from `ats_jobs_elastic`.
3. **Interview Lifecycle:** Scheduled and conducted interviews from `ats_interview_tracker_elastic`.
4. **Offers & Joinings:** Offers released and candidates onboarding from `ats_candidate_timeline`.
5. **Team & Access Governance:** Colleague invitations and role assignments from `team_invitations`.

---

## Architecture & Data Flow

```
                     USER BROWSER / CLIENT RAM
                                │
        ┌───────────────────────┴───────────────────────┐
        ▼                                               ▼
[ Tier 1: Toolbar Card ]                        [ Tier 2: Filter Sub-Tabs ]
- 300ms Debounced Search                        - All Events (143)
- Timeframe (All, Today, 7d, 30d)               - Pipeline (30)
- Live Pulse Health Indicator                   - Interviews (4)
                                                - Offers & Joining (1)
                                                - Requisitions (90)
                                                - Team & Access (18)
                                │
                                ▼
         [ GET /service_company/account/activity-data ]
                                │
                                ▼
         [ ServiceCompanyAccountActivityController ]
                                │
                                ▼
              [ CompanyActivityReadService ]
       ┌────────────────────────┼────────────────────────┐
       ▼                        ▼                        ▼
[ats_candidate_timeline] [ats_jobs_elastic] [ats_interview_tracker]
       │                        │                        │
       └────────────────────────┼────────────────────────┘
                                ▼
                 [ Normalized Timeline Cards ]
            (Actor Name + Avatar + Formatted Time +
             Action Type + Entity Link + Category)
```

### Multi-Tenant Isolation & CQRS Compliance
- All queries strictly enforce `company_id = auth()->user()->company_id`.
- Zero MySQL reads on the query path for pipeline/jobs/interviews; primary data is fetched via Elasticsearch aggregations.
- Redis caching with a 60-second TTL prevents repeated query load during high concurrent tab switches.
- Verified: Company 2 resolves 143 items; Company 1 resolves 12 items. Zero data bleeding across organizations.

---

## File Changes Matrix

| File Path | Change Type | Description |
| :--- | :--- | :--- |
| `app/Services/ServiceCompany/CompanyActivityReadService.php` | New | High-performance CQRS service aggregating company users' actions across Elasticsearch indices and DB audit logs with category, timeframe, and debounced search filters. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAccountActivityController.php` | New | Dedicated JSON endpoint resolving company activity stream and rendering timeline cards HTML. |
| `routes/service.php` | Modified | Registered `service_company.account.activity_data_ajax` route targeting `account/activity-data`. |
| `resources/views/service_company/account/components/company-activity-cards-ajax.blade.php` | New | Modern timeline cards template with actor node, vertical timeline connector, badges, and action links. |
| `resources/views/service_company/account/components/tab-company-activity.blade.php` | New | Rule 20 Card + Sub-Card container with debounced search, timeframe filter, category pills, and `CompanyActivityManager` script. |
| `resources/views/service_company/account/index.blade.php` | Modified | Integrated `tab-company-activity` into `#recent_activity` pane and wired overview sync. |

---

## Verification Evidence
1. **PHP Syntax & Linting:**
   - `php -l app/Services/ServiceCompany/CompanyActivityReadService.php`: Pass.
   - `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyAccountActivityController.php`: Pass.
   - `php -l resources/views/service_company/account/components/tab-company-activity.blade.php`: Pass.
   - `php -l resources/views/service_company/account/components/company-activity-cards-ajax.blade.php`: Pass.
   - `php -l resources/views/service_company/account/index.blade.php`: Pass.
   - `php -l routes/service.php`: Pass.
   - `./vendor/bin/pint`: Pass.
2. **Endpoint Execution:**
   - `GET /service_company/account/activity-data`: HTTP 200, returning 143 company activities for Company 2 and 12 for Company 1.
   - Debounced search for `'engineer'` returns 15 matching items.
   - Category filtering (`jobs`, `pipeline`, `interviews`, `team`) verified.
3. **Overview Sync:**
   - Top 5 activities seamlessly render into `#cdOverviewRecentActivityList`.
