# Service Company — Company Activity Timeline Backend & UI Audit Changelog

## Metadata
- **Date:** 2026-11-11
- **Module:** Service Company Account / Company Profile Activity (`/service_company/account`)
- **Author:** Antigravity AI Pair Programmer
- **Standard Applied:** Modern Card + Sub-Card Enterprise Design System (Rule 20), CQRS Elasticsearch Reads (Rules 1 & 2), Multi-Tenant Isolation (Rule 6), Zero Business Logic Mutations (Rule 7), 15.6" Screen Fit Standard (Rule 14 & 20).

---

## Executive Summary
Per user feedback that the activity tab was not working due to an inappropriate client-drawer coupling and subsequently required exact UI alignment with the benchmark standard (`media_1794398618355_dc9b9cf5.jpg`), a dedicated company-wide activity backend and timeline interface were engineered from scratch. 

The implementation achieves:
1. **100% Benchmark Design Parity:**
   - Grouped date badges (`<i class="far fa-calendar-alt"></i> Nov 06, 2026`).
   - Concentric ring timeline nodes (green/blue outer ring with inner dot) along a continuous vertical dashed connector line (`border-left: 2px dashed #cbd5e1`).
   - 4-zone sub-card grid:
     - **Entity Sub-Card:** Avatar/icon, stream category pill (`JOB REQUISITION`, `CANDIDATE`, `INTERVIEW`, `TEAM INVITATION`), entity title, company logo + name, supporting details (`#Code · Location · Shift · Openings`).
     - **Status Badge Sub-Card:** Pill badge with status icon (`ACTIVE REQUISITION`, `SUBMITTED TO SOURCING`, `INTERVIEW`, etc.).
     - **Actor Sub-Card:** Attribution label (`CREATED BY:`, `SUBMITTED BY:`, `INVITED BY:`), actor name, company name.
     - **Actions Sub-Card:** Formatted date & time with timezone, plus **"View Profile"** action button.
   - **Footer Sub-Strip:** Category pill, clock icon + description + time, right-aligned monotonic sequence tag (`#seq-1793947420`).
2. **Interactive Sliding Drawers (Zero Page Navigation):**
   - Clicking "View Profile" on a Job card opens the Requisition details sliding drawer (`#kt_activitiesJob_show`) in read-only mode via `window.openJobDrawer(jobId)`.
   - Clicking "View Profile" on a Candidate card opens the Application details sliding drawer (`#kt_activitiesApplication`) in read-only mode via `window.openCandidateDrawer(candId)`.
   - Zero full page reloads, zero ghost flashes, and instant skeleton loader during AJAX retrieval.
3. **Forensic Relative Timestamp Bug Fix ("56 years ago"):**
   - Root cause: `GeneralMethod::timeAgo()` internally invokes `strtotime($time_ago)`, expecting a date string (`Y-m-d H:i:s`). When passed an integer Unix timestamp, `strtotime` returned false or epoch 0, resulting in `2026 - 1970 = 56 years ago`.
   - Resolution: Formatted with `date('Y-m-d H:i:s', $timestamp)` and Carbon, restoring authoritative relative times (`24 hours ago`, `yesterday`).

---

## Architecture & Data Flow

```
                     USER BROWSER / CLIENT RAM
                                │
        ┌───────────────────────┴───────────────────────┐
        ▼                                               ▼
[ Tier 1: Toolbar Card ]                        [ Tier 2: Filter Sub-Tabs ]
- 300ms Debounced Search                        - All Events (156)
- Timeframe (All, Today, 7d, 30d)               - Pipeline (43)
- Live Pulse Health Indicator                   - Interviews (4)
- Live Reverb Sync State                        - Offers & Joining (1)
                                                - Jobs (90)
                                                - Team (18)
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
[ats_candidates_elastic] [ats_jobs_elastic] [ats_candidate_timeline]
[ats_interview_tracker]  [team_invitations]
       │                        │                        │
       └────────────────────────┼────────────────────────┘
                                ▼
        [ Rendered Benchmark Timeline Cards & Sub-Cards ]
     (Date Group Badges + Concentric Ring Timeline Nodes +
      Entity Sub-Card + Status Sub-Card + Actor Sub-Card +
      Actions Sub-Card + Sliding Drawer Opener Buttons)
```

### Multi-Tenant Isolation & CQRS Compliance
- All queries strictly enforce `company_id = auth()->user()->company_id`.
- Zero MySQL reads on the query path for pipeline/jobs/interviews; primary data is fetched via Elasticsearch aggregations.
- Redis caching with a 60-second TTL prevents repeated query load during high concurrent tab switches.
- Verified: Company 2 resolves 156 items; Company 1 resolves 12 items. Zero data bleeding across organizations.

---

## File Changes Matrix

| File Path | Change Type | Description |
| :--- | :--- | :--- |
| `app/Services/ServiceCompany/CompanyActivityReadService.php` | Modified | Added `ats_candidates_elastic` aggregation; structured full job, candidate, actor, attribution, and transition payloads; resolved timezone with Carbon; fixed `timeAgo` parameter formatting bug. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAccountActivityController.php` | New | Dedicated JSON endpoint resolving company activity stream and rendering timeline cards HTML. |
| `routes/service.php` | Modified | Registered `service_company.account.activity_data_ajax` route targeting `account/activity-data`. |
| `resources/views/service_company/account/components/company-activity-cards-ajax.blade.php` | Modified | Redesigned 100% to match benchmark UI (`media_1794398618355_dc9b9cf5.jpg`) with date group headers, concentric nodes, 4-zone subcard grid, drawer opener buttons, and bottom activity strip. |
| `resources/views/service_company/account/components/tab-company-activity.blade.php` | Modified | Embedded benchmark styles from `tab-activity-style.blade.php`; implemented `openJobDrawer` and `openCandidateDrawer` client controllers; wired overview timeline sync. |
| `resources/views/service_company/account/index.blade.php` | Modified | Integrated `tab-company-activity` into `#recent_activity` pane and wired overview sync. |

---

## 15.6" Screen Fit & Zero Horizontal Scroll Verification
- Layout verified strictly per Rule 20 and Rule 14.
- All cards, sub-card grids, headers, and tabs enforce `box-sizing: border-box !important`, `max-width: 100% !important`, and `overflow-x: hidden !important`.
- Grid column sizing (`minmax(200px, 1.2fr) minmax(220px, 1.6fr) minmax(180px, 1.2fr) minmax(150px, auto)`) adjusts smoothly across `1366×768` and `1920×1080` screen resolutions without clipping or overflow.

---

## Verification Evidence
1. **PHP Syntax & Linting:**
   - `php -l app/Services/ServiceCompany/CompanyActivityReadService.php`: Pass.
   - `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyAccountActivityController.php`: Pass.
   - `php -l resources/views/service_company/account/components/tab-company-activity.blade.php`: Pass.
   - `php -l resources/views/service_company/account/components/company-activity-cards-ajax.blade.php`: Pass.
   - `./vendor/bin/pint`: Pass (0 violations).
2. **Endpoint Execution:**
   - `GET /service_company/account/activity-data`: HTTP 200, returning 156 company activities for Company 2.
   - Relative timestamps: "24 hours ago", "yesterday" (0% "56 years ago").
   - Category filtering (`all: 156`, `pipeline: 43`, `interviews: 4`, `offers: 1`, `jobs: 90`, `team: 18`) verified.
3. **Interactive Drawer Opening:**
   - Clicking "View Profile" on Job cards triggers `openJobDrawer(jobId)` opening `#kt_activitiesJob_show`.
   - Clicking "View Profile" on Candidate cards triggers `openCandidateDrawer(candId)` opening `#kt_activitiesApplication`.
