# Service Company / Job Application Show Detail — Performance Audit

**Subject:** `resources/views/service_company/job-applications/showdetail.blade.php` + `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`  
**Target URL:** `http://localhost/git/JWMaincode28June2026/service_company/job-applications/1748?slug=cvtracker&currentPage=undefined`  
**Date:** 2026-08-25  
**Scope constraint:** Service Company persona (`service.php` routes).  
**Mode:** **AUDIT ONLY**. No code edits, no schema changes, no commits.

---

## 0. Executive Summary & TL;DR (P0 → P3)

| Priority | Title | Impact | Line Number / Evidence |
|---|---|---|---|
| **P0** | **N+1 Query Explosion in Timeline Loop** | Executing **12+ DB queries per timeline record** inside Blade `@foreach($updateTimeLine as $tdata)`. 50 timeline items = 625+ DB queries per request. | `showdetail.blade.php:L3230-L4100` |
| **P0** | **Uncached Helper Function DB Queries** | Helpers `companyDetailsByClient()`, `confidentialAmontShow()`, `feedback_status()`, `available_status()` execute raw SQL queries inside loops without caching. | `GeneralMethod.php:L508, L552, L3193, L10286` |
| **P0** | **Misplaced Controller Cache Check** | DragonflyDB cache lookup (`ats:candidateDetails:...`) occurs **after** skill/question DB write inserts and ES index updates. | `ServiceCompanyJobApplicationController.php:L2829` |
| **P0** | **Wasted ES Query Overwritten by MySQL** | ES query `ELASTIC_CANDIDATE_SKILLS_EXP` at L2936 is immediately overwritten by MySQL `job_metronics_skills` LEFT JOIN at L2958. | `ServiceCompanyJobApplicationController.php:L2936-L2958` |
| **P1** | **Massive God Blade File (6,865 Lines)** | Entire drawer HTML + 2,300+ lines inline JS rendered in a single ~450 KB JSON payload. | `showdetail.blade.php:L1-L6865` |
| **P1** | **Unconstrained ES Searches (`size: 10000`)** | 7 out of 10 ES queries request up to 10,000 hits without `_source` field filtering. | `ServiceCompanyJobApplicationController.php:L2875, L2897, L2978, L3004` |
| **P1** | **Embedded Database Queries in Blade** | Raw SQL queries executed inside Blade view (`jobData`, `spokperson`, `company logo`, `submitted_by company`). | `showdetail.blade.php:L411, L448, L1028, L2496` |
| **P2** | **`currentPage=undefined` URL State Bug** | Missing pagination element causes jQuery `.data('page')` to return `undefined`, encrypting string `"undefined"` in navigation parameters. | `service_company_layout.blade.php:L2927` & `Controller.php:L3162` |
| **P2** | **Synchronous PDF.js Canvas Processing** | CV tracker tab triggers synchronous PDF ArrayBuffer download, worker parsing, and canvas page rendering on page load. | `showdetail.blade.php:L1612-L1770` |
| **P3** | **Duplicated Bench Skill Exp Count** | Separate `count()` query executed right after `get()` query on `bench_candidate_skill_experience`. | `ServiceCompanyJobApplicationController.php:L3131-L3139` |

---

## 1. Request Execution Surface Map

### 1.1 Files in Scope

| File Path | Lines | Size | Primary Purpose |
|---|---|---|---|
| `routes/service.php` | L179 / L195 | — | Route registration: `Route::resource('job-applications')` |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyBaseController.php` | L79-L300 | 19.7 KB | Middleware: Auth, admin role check, user permissions, theme |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | L2642-L3196 | 666 KB | Controller `show($id)` method |
| `resources/views/service_company/job-applications/showdetail.blade.php` | L1-L6865 | 432 KB | Primary Blade view & inline scripts |
| `app/Helper/GeneralMethod.php` | Various | 633 KB | Helper functions used by Controller & Blade |

---

## 2. Comprehensive Database Query Audit

### Initial Request Database Query Table

| # | File | Line # | Table | Selected Columns | Conditions | JOINs | Risk Level |
|---|---|---|---|---|---|---|---|
| 1 | `BaseController` | 93 | `roles` | `roles.*` | `name = 'admin' AND company_id = ?` | None | Medium |
| 2 | `BaseController` | 94 | `role_user` | Exists | `user_id = ?` | None | Low |
| 3 | `BaseController` | 169 | `companies` | `companies.*` | `id = ?` | None | Low |
| 4 | `BaseController` | 291 | `theme_settings` | `theme_settings.*` | `company_id = ?` | None | Medium |
| 5 | `Controller` | 2663 | `add_job_wise_condidates` | `*` | `id = ?` | None | Low |
| 6 | `Controller` | 2711 | `job_metronics_skills` | `id, skill_name` | `job_id = ? AND company_id = ?` | None | Medium |
| 7 | `Controller` | 2718 | `candidate_job_skill_exp` | `job_skill_id` | `job_id = ? AND candidate_id = ?` | None | Medium |
| 8 | `Controller` | 2733 | `candidate_job_skill_exp` | Insert | Per missing skill | None | High (in loop) |
| 9 | `Controller` | 2789 | `job_additional_detail` | `*` | `job_id = ? ORDER BY id ASC` | None | Medium |
| 10 | `Controller` | 2803 | `application_additional_detail` | Insert | Per missing question | None | High (in loop) |
| 11 | `Controller` | 2933 | `jobs` | `jobs.*` | `id = ?` | None | Low |
| 12 | `Controller` | 2958 | `job_metronics_skills` | `id, skill_name...` | `job_id = ?` | LeftJoin `candidate_job_skill_exp` | Medium |
| 13 | `Controller` | 3027 | `rec_candidate_assign` | Count | `source_application_id = ? AND source_jobid = ?` | None | Medium |
| 14 | `Controller` | 3029 | `rec_candidate_assign` | `*` | `source_application_id = ?` | None | Medium |
| 15 | `Controller` | 3055 | `bench_assign` | `users.id...` | `bench_applications_id = ?` | Join `users`, LeftJoin `users_profile` | Medium |
| 16 | `Controller` | 3058 | `bench_additional_detail` | `id, custom_keys...` | `bench_id = ?` | None | Low |
| 17 | `Controller` | 3074 | `candidate_status` | `*` | `id = ?` | None | Low |
| 18 | `Controller` | 3131 | `bench_candidate_skill_experience` | `*` | `candidate_id = ?` | None | Low |
| 19 | `Controller` | 3135 | `bench_candidate_skill_experience` | Count | `candidate_id = ?` | None | Low (Duplicate) |
| 20 | `Controller` | 3187 | `interview_mode` | `*` | `ORDER BY id ASC` | None | Low |
| 21 | `Blade` | 4 | `GeneralMethod::currencyConverter` | `currencies.*` | None | None | **HIGH** |
| 22 | `Blade` | 411 | `jobs` | `max_sal_mode, max_salary_currency` | `id = ?` | None | **HIGH** |
| 23 | `Blade` | 448 | `job_assign` | `users.name` | `job_id = ? AND company_id = ?` | LeftJoin `users` | **HIGH** |
| 24 | `Blade` | 1028 | `companies` | `logo, company_name` | `id = ?` | None | **HIGH** |
| 25 | `Blade` | 2496 | `companies` | `company_name` | `users.id = ?` | Join `users` | **HIGH** |

---

## 3. Timeline N+1 Audit Detail

**Blade File:** `resources/views/service_company/job-applications/showdetail.blade.php` (Line 3230)

For each iteration of `@foreach($updateTimeLine as $tdata)`:
1. `GeneralMethod::companyDetailsByClient($tdata->job_id)` (L3235): 2 SQL queries with 3 JOINs.
2. `GeneralMethod::confidentialAmontShow($tdata->job_id)` (L3268, L3724, L3838, L3954): 1 DB query (`job_assign.exists`) + Spatie permission check (called up to 4 times per loop).
3. `offer_job_applications` query (L3272): 1 DB query (`SELECT * FROM offer_job_applications WHERE id = ?`).
4. `GeneralMethod::feedback_status()` (L3297): 1 DB query on `interview_sub_status`.
5. `GeneralMethod::available_status()` (L3298): 1 DB query on `interview_status`.
6. `$userC` query (L3308): 1 DB query on `users`.
7. `$spokperson` query (L3312): 1 DB query on `job_assign` + `users`.
8. `interview_feedback` query (L3596): 1 DB query on `interview_feedback`.
9. `interview_skills_feedback` count query (L3603): 1 DB query on `interview_skills_feedback`.

**Query Count Projection:** Total = $25 + (N \times 12)$.
- 10 timeline records = **145 DB Queries**
- 50 timeline records = **625 DB Queries**
- 100 timeline records = **1,225 DB Queries**

---

## 4. Helper Implementation Details

- `companyDetailsByClient($jobid)` ([`GeneralMethod.php:L3193`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L3193)): Executed 2 raw SQL queries with 3 JOINs without caching.
- `confidentialAmontShow($jobId)` ([`GeneralMethod.php:L10286`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L10286)): Executed `job_assign` exists check and `hasRole('admin')`.
- `feedback_status()` ([`GeneralMethod.php:L552`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L552)): Executed `DB::table('interview_sub_status')->where('status', 1)->get()`.
- `available_status()` ([`GeneralMethod.php:L508`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L508)): Executed `DB::table('interview_status')->where('status', 1)->get()`.
- `currencyConverter()` ([`GeneralMethod.php:L4318`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L4318)): Executed `DB::table('currencies')->get()`.

---

## 5. Index Audit & Recommendations

```sql
-- High frequency job assignment lookups
CREATE INDEX idx_job_assign_job_user ON job_assign (job_id, user_id);

-- Spokesperson lookup for timeline items
CREATE INDEX idx_job_assign_comp_spok ON job_assign (job_id, company_id, spok_user_id);

-- Skill experience lookup
CREATE INDEX idx_cjs_job_candidate ON candidate_job_skill_exp (job_id, candidate_id);

-- Interview feedback timeline queries
CREATE INDEX idx_if_schedule ON interview_feedback (interview_schedules_id);
CREATE INDEX idx_isf_interview ON interview_skills_feedback (interview_id);
```
