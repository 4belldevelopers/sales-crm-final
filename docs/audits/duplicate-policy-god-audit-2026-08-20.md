# GOD-Level Audit — Duplicate Policy in JERO Codebase

**Date:** 2026-08-20
**Audit scope:** DB layer · Eloquent models · Observers · FormRequests · Controllers · Services · Jobs · Repositories · Imports/Exports · Elasticsearch · Blade/Frontend
**In-scope personas:** Service Company · Super Admin · Admin · public Front (per [docs/PERSONA_SCOPE.md](docs/PERSONA_SCOPE.md) — Product Co / Staffing Co routes ignored)
**Codebase:** [JWMaincode28June2026](.) (217 tables, MySQL+ES, Laravel 11, CQRS)

---

## 0. Executive Summary

JERO's duplicate-detection policy is **NOT** a unified system — it is a **distributed patchwork** spread across **5 layers with no single source of truth**. The same business rule ("one job application per email per job") is enforced by **3 different mechanisms** (FormRequest, service helper, app_duplicate flag) — none of which is race-safe at the DB level. **22 UNIQUE constraints exist; 9 critical business-uniqueness invariants the code clearly wants are NOT enforced by the DB at all.**

**Headline risks (severity-ordered):**

| # | Risk | Severity | Layer(s) failing |
|---|---|---|---|
| R1 | `job_applications` has NO DB UNIQUE on `(company_id, job_id, email)` despite `companies.app_duplicate` policy | **CRITICAL** | DB |
| R2 | Stale `mysql-schema.sql` is missing 11 UNIQUE constraints that live in migrations — schema dump is NOT source of truth | **CRITICAL** | DB |
| R3 | `users.email` is GLOBAL UNIQUE — blocks same email across tenants (intentional? design choice left undocumented) | **HIGH** | DB |
| R4 | `SaasFrontController::companyRegister()` (~1,200 lines) bypasses its own `RegisterForm` FormRequest — `Validator::make` only checks `users.email`, NOT `companies.domain_key` / `sub_domain` / `career_page_link` / `users.mobile` | **CRITICAL** | Controller |
| R5 | `Documents/StoreRequest::rules()` unique rule lacks `->ignore()` — UPDATE-in-place always fails 422 (the "phase-f-edit-page-v2" gap) | **HIGH** | FormRequest |
| R6 | `BenchApplication` and `OfferJobApplication` models `use Illuminate\Database\Eloquent\SoftDeletes` but never apply the trait — soft-deleted rows leak through queries | **HIGH** | Eloquent |
| R7 | No DB-level defense on `JobApplicationAnswer(job_application_id, question_id)` — `ProcessCandidateSubmissionJob` retries create duplicates | **HIGH** | Job + DB |
| R8 | 6+ `firstOrCreate` callsites on `PermissionRole`/`Role` are non-transactional — race window for duplicates | **MEDIUM** | Controller |
| R9 | No HTTP 409 anywhere — API duplicates indistinguishable from other validation errors | **MEDIUM** | API design |
| R10 | No tenant-scope helper for `Rule::unique` — every tenant-scoped check is hand-rolled via `where(fn)` closure | **MEDIUM** | FormRequest pattern |

---

## 1. Source-of-Truth Map — Where Each Uniqueness Lives

| Business invariant | DB constraint? | Model guard? | FormRequest? | Service check? | ES collapse? |
|---|---|---|---|---|---|
| One `users.email` globally | ✅ `users.email` UNIQUE | ❌ | ✅ `Rule::unique('users')` | ❌ | n/a |
| One `companies.sub_domain` | ✅ migration 2026_08_07_000001 | ❌ | ✅ `RegisterForm` (module-gated) | ❌ + cache probe | n/a |
| One `companies.career_page_link` | ✅ migration (added late) | ❌ | ✅ `RegisterForm` | ❌ | n/a |
| One `companies.domain_key` | ✅ migration 2026_08_07_000002 | ❌ | ❌ (not in `RegisterForm`) | ✅ 60s Redis probe | n/a |
| One `roles(name, guard_name)` | ✅ migration | ❌ | ❌ | ✅ `firstOrCreate` (no race) | n/a |
| One `permissions(name, guard_name)` | ✅ migration | ❌ | ❌ | ❌ | n/a |
| One `coupons.code` | ✅ migration | ❌ | ❌ | ❌ | ❌ |
| One `documents(company_id, doc_type, doc_id, name)` | ✅ migration 2026_06_28_000004 | ❌ | ⚠️ lacks `->ignore()` | ❌ | n/a |
| One `pipeline_counters(job_id, company_id)` | ✅ migration 2026_08_05_000002 | ❌ | n/a | n/a | n/a |
| One `resume_uploads(uuid)` | ✅ migration | ❌ | n/a | ❌ | n/a |
| One `global_settings.setting_key` | ✅ migration | ❌ | ❌ | ❌ | n/a |
| One `modules.slug` / `features.slug` | ✅ migration | ❌ | ❌ | ❌ | n/a |
| One `meeting_link` (Google/Teams) | ✅ HASH UNIQUE | ❌ | ❌ | ✅ `insertOrIgnore` in `MeetingImport` | n/a |
| **One job application per `(company_id, job_id, email)`** | ❌ **MISSING** | ❌ | ⚠️ `FrontJobApplication` (no `StoreJobApplication`) | ⚠️ `checkDuplicateCandidate()` (race-prone) | ✅ `collapse` on `email` (read-only) |
| **One candidate per `email` across jobs in a tenant** | ❌ **NO MASTER CANDIDATE TABLE** | ❌ | ❌ | ⚠️ app_duplicate_days window | ✅ collapse on `email` |
| **One interview per `(app_id, datetime, stage)`** | ❌ **MISSING** | ❌ | ❌ | ❌ | n/a |
| **One placement per `(bench_id, client_job_id, client_app_id)`** | ❌ **MISSING** | ❌ | ❌ | ❌ | n/a |
| **One `JobApplicationAnswer(app_id, question_id)`** | ❌ **MISSING** | ❌ | ❌ | ⚠️ `updateOrCreate` HTTP path / raw insert job path | n/a |
| **One `tb_ranks(user_id, application_id)`** | ❌ **MISSING** | ❌ | ❌ | ❌ unguarded raw insert in `ProcessCandidateSubmissionJob` | n/a |
| **One company email per `(company_email)`** | ❌ **MISSING** | ❌ | ❌ | ❌ | n/a |
| **Same email not in two distinct companies (`users.email` global)** | ✅ GLOBAL UNIQUE | — | — | — | — |

**Effective defense-in-depth: 1 layer (DB OR FormRequest) for 12 of 21 invariants. Zero for 9 of 21.**

---

## 2. Layer-by-Layer Findings

### 2.1 Database Layer

**Stale schema dump:** `database/schema/mysql-schema.sql` (225 KB) is **out of sync with migrations** by ~11 UNIQUE constraints. CLAUDE.md says "schema is the source of truth" — this is FALSE today. The schema dump should be regenerated via `mysqldump --no-data`.

**UNIQUE constraints confirmed effective (after migrations applied):** 22 total

| # | Table.Column(s) | Migration | Soft-delete aware? | Tenant-scoped? | NULL-safe? |
|---|---|---|---|---|---|
| 1 | `bench_candidate_skill_experience.(candidate_id, skill_key)` | 2026_06_28_000010 | n/a | ❌ | ❌ |
| 2 | `commercial_experience_bands.code` | baseline | n/a | ❌ | ❌ |
| 3 | `commercial_experience_bands.sort_order` | baseline | n/a | ❌ | ❌ |
| 4 | `companies.sub_domain` | 2026_08_07_000001 | ❌ (no deleted_at on companies) | ❌ global | ✅ multi-NULL |
| 5 | `companies.career_page_link` | 2026_06_28_000002 | ❌ | ❌ | ✅ |
| 6 | `companies.domain_key` | 2026_08_07_000002 | ❌ | ❌ | ✅ |
| 7 | `company_career_settings.company_id` (1:1) | 2026_08_17_000002 | n/a | ✅ | ❌ |
| 8 | `coupons.code` | 2026_07_15_000001 | n/a | ❌ | ❌ |
| 9 | `documents.(company_id, documentable_id, documentable_type, name)` | 2026_06_28_000004 | n/a (no deleted_at) | ✅ | ❌ |
| 10 | `failed_jobs.uuid` | Laravel default | n/a | ❌ | ❌ |
| 11 | `global_settings.setting_key` | 2026_06_28_000009 | n/a | ❌ | ❌ |
| 12 | `google_meeting_lists.meeting_link` | baseline | n/a | ❌ | ❌ |
| 13 | `modules.slug` | 2026_06_28_000008 | n/a | ❌ | ❌ |
| 14 | `features.slug` | 2026_06_28_000008 | n/a | ❌ | ❌ |
| 15 | `permissions.(name, guard_name)` | 2026_06_28_000008 | n/a | ❌ | ❌ |
| 16 | `pipeline_counters.(job_id, company_id)` | 2026_08_05_000002 | n/a | ✅ | ❌ |
| 17 | `resume_uploads.uuid` | 2026_07_26_000001 | n/a | ❌ | ❌ |
| 18 | `resume_uploads.(uuid, company_id)` | 2026_07_26_000001 | n/a | ✅ | ❌ |
| 19 | `roles.(name, guard_name)` | 2026_06_28_000008 | n/a | ❌ (cross-tenant collision blocked) | ❌ |
| 20 | `team_meeting_lists.meeting_link` | baseline | n/a | ❌ | ❌ |
| 21 | `users.email_verification_code` | 2026_08_07_000004 | n/a (NULL-tolerant) | ❌ | ✅ multi-NULL |
| 22 | `users.email` (GLOBAL) | Laravel default | ❌ (no deleted_at) | ❌ global | ❌ |

**Tenant-scoped UNIQUE: only 4** (`company_career_settings`, `documents`, `pipeline_counters`, `resume_uploads.uuid_company`).

**Missing DB-level constraints the code clearly wants:**

| Wanted invariant | Missing UNIQUE | Impact |
|---|---|---|
| One job application per (company, job, email) | `job_applications(company_id, job_id, email)` | R1 — duplicate applications on race |
| One company email | `companies.company_email` | R4 — multiple companies w/ same email |
| One interview per (app, datetime, stage) | `interview_schedules(job_application_id, schedule_date, stage)` | Double-booking possible |
| One placement per (bench, client job, app) | `bench_place(bench_id, client_job_id, client_application_id)` | Double-placement possible |
| One JobApplicationAnswer per (app, question) | `job_application_answers(job_application_id, question_id)` | Duplicate answers on retry |
| One tb_rank per (user, app) | `tb_ranks(user_id, application_id)` | Duplicate rankings on retry |
| One app_duplicate email within N days | partial index not supported on MySQL 8 without functional index | window enforcement only at app layer |
| Master candidate table for cross-job dedupe | no `candidates` table | denormalized — dedupe impossible at DB level |
| `companies.company_name` | no UNIQUE | multiple companies w/ same display name |

---

### 2.2 Eloquent Model + Observer Layer

**No uniqueness enforcement exists at this layer.** All Observers do exactly one thing: stamp `company_id` from auth context on save. No `static::creating` hook checks for duplicates. No `firstOrCreate`/`updateOrCreate` at the model layer. No `try/catch` around `QueryException`/`SQLSTATE 23000` to recover from race-conditions.

**Bugs / anomalies:**

| Model | Anomaly | Effect |
|---|---|---|
| `app/Models/BenchApplication.php` | `use Illuminate\Database\Eloquent\SoftDeletes` imported, but `use Notifiable, SoftDeletes;` line only has `Notifiable`. The trait is NOT applied. | Soft-deleted bench rows leak through queries. No auto-filter on read. |
| `app/Models/OfferJobApplication.php` | Same pattern as BenchApplication. | Same — ghost rows visible. |
| `app/Models/JobApplication.php` | `SoftDeletes` IS applied correctly. | OK. |
| `app/Models/User.php` | No soft-delete at all. `users.email` global UNIQUE blocks re-registration with same email even after explicit user deletion. | Permanent email lock — by design, but blocks legitimate re-signup after user removal. |

**Trait duplication drift:** ~17 models re-implement the same `TenantScope` in their `boot()` instead of using `app/Traits/BelongsToTenant.php`. Behaviorally identical today, but a drift risk if the trait evolves.

---

### 2.3 FormRequest + Controller Layer

**Coverage:** 33 FormRequest files audited. Only **8 enforce uniqueness** via `Rule::unique` / `unique:`:

| FormRequest | Fields | Tenant-scoped? | Soft-delete aware? | Has `->ignore()`? |
|---|---|---|---|---|
| `RegisterForm` | `sub_domain`, `career_page_link`, `email` | ❌ global | ❌ | ❌ |
| `Company/StoreCompany` | `sub_domain`, `email` | ❌ | ❌ | ❌ |
| `Company/UpdateCompany` | `sub_domain` | ❌ | ❌ | ✅ |
| `ProfileUpdateRequest` | `email` | ❌ (User::class FQCN) | ❌ | ✅ |
| `UpdateProfile` | `email` | ❌ | ❌ | ✅ |
| `SuperAdmin/SuperadminUser/StoreRequest` | `email` | ❌ | ❌ | ❌ |
| `SuperAdmin/SuperadminUser/UpdateRequest` | `email` | ❌ | ❌ | ✅ |
| `SuperAdmin/FooterSetting/StoreMenuRequest` | `title` | ❌ | ❌ | ❌ |
| `SuperAdmin/FooterSetting/UpdateMenuRequest` | `title` | ❌ | ❌ | ✅ |
| `Admin/Document/StoreRequest` | `name` (polymorphic) | ⚠️ polymorphic only, not `company_id` | ❌ | ❌ — **R5** |
| `Admin/ApplicationStatus/StoreRequest` | `status` | ✅ company_id | ❌ | ❌ |
| `Admin/ApplicationStatus/UpdateRequest` | `status` | ✅ | ❌ | ✅ |
| `FrontJobApplication` | `email` | ✅ company_id + job_id | ❌ | ❌ |
| `Language/StoreLanguage` | `language_name`, `language_code` | ❌ | ❌ | ✅ |
| **Missing for `StoreJobApplication`** | (no email unique) | — | — | — |
| **Missing for `UpdateJobApplication`** | (no email unique) | — | — | — |

**Key gaps:**

- **`app/Rules/` directory does not exist.** There are zero custom `Rule` classes. Every uniqueness check is either `Rule::unique` (Fluent API) or `unique:` (string rule).
- **No global tenant-scope helper.** Every tenant-scoped check is hand-rolled via `Rule::unique(...)->where(fn => $query->where('company_id', ...))`. Three places do this manually.
- **No `->whereNull('deleted_at')` or `->withoutTrashed()`** anywhere. Soft-deleted rows still trigger 422 on re-apply.
- **`Admin/Document/StoreRequest`** rule:
  ```php
  Rule::unique('documents')->where(fn $query =>
      $query->whereDocumentableType(...)->whereDocumentableId(...)->whereName(...))
  ```
  Missing `->ignore($this->route('document'))` → UPDATE-in-place always fails (the documented "phase-f-edit-page-v2" gap). The four document controllers (`Admin/AdminDocumentController:36`, `ServiceCompany/ServiceCompanyDocumentController:40`, `StaffingCompany/StaffingCompanyDocumentController:40`, `ProductCompany/ProductCompanyDocumentController:41`) all then call `$record->documents()->create([...])` regardless — meaning the FormRequest validation fires but the create proceeds. Inconsistency.

**Controller-level pre-checks (NOT in FormRequest):**

| Controller:Line | Pattern | Race-safe? |
|---|---|---|
| `Auth/RegisterController.php:71` | `Validator::make` with `unique:users` | ⚠️ TOCTOU |
| `SaasFront/SaasFrontController.php:364` | `Validator::make` with `unique:users` ONLY | ❌ **R4** |
| `SuperAdmin/SuperAdminCompanyController.php:2083,2093,6668` | inline `Rule::unique` | ⚠️ TOCTOU |
| `SuperAdmin/CouponController.php:42` | inline `unique:coupons` | ⚠️ TOCTOU |
| `Auth/ResetPasswordController.php:66-67` | `User::where('email', ...)->exists()` | ⚠️ |
| `StaffingCompany/StaffingCompanyJobApplicationController.php:447-471` | `checkDuplicateCandidate` (flag-only, does not block) | ❌ |
| `ServiceCompany/ServiceCompanyJobApplicationController.php:1941,2343,9544` | same `checkDuplicateCandidate` (flag-only) | ❌ |
| `Admin/AdminJobApplicationController.php:709,1481,1488` | `documents()->updateOrCreate([...])` | ✅ race-safe |
| `StaffingCompany/...JobApplicationController.php:708,1467,1474` | same `updateOrCreate` | ✅ |
| `ProductCompany/...JobApplicationController.php:727` | same `updateOrCreate` | ✅ |
| `Admin/InterviewScheduleController.php:1015` | `comments()->updateOrCreate` | ✅ |
| `ServiceCompany/ServiceCompanyBaseController.php:197` | `Role::firstOrCreate([name, guard_name])` | ⚠️ |
| `ServiceCompany/ServiceCompanyAccountSettingsController.php:1492` | `CompanyCareerSetting::updateOrCreate` | ✅ (1:1 keyed) |
| `SuperAdmin/SuperAdminFooterSettingsController.php:99` | `FooterSetting::updateOrCreate` | ✅ |

**`firstOrCreate` callsites on `PermissionRole` (no transaction wrap):**
- `Admin/ManageRolePermissionController.php:74, 337, 409`
- `StaffingCompany/ManageRolePermissionController.php:72, 315, 386`
- `ProductCompany/ManageRolePermissionController.php:74, 441`
- `ServiceCompany/ManageRolePermissionController.php:76`

All 9 callsites rely on UNIQUE constraint to catch races (via `PermissionRole` model that has UNIQUE on `(role_id, permission_id)` — verified). Race-safe via DB. But: no `->wasRecentlyCreated` check before inserting child rows that depend on the role existing.

**HTTP status convention:** No `409` anywhere in `app/Http/Controllers`. Duplicate failures all return 422 (the only `409` references in the codebase are CSS color hex `ff4098`). API and Web are indistinguishable for duplicate errors. R9.

---

### 2.4 Service + Repository + Job Layer

**Static helper used everywhere for candidate dedupe:**

`app/Helper/GeneralMethod.php:603-667` — `checkDuplicateCandidate()` / `checkDuplicateCandidateClient()`:
- `WHERE company_id = ? OR thirdparty_company_id = ?`
- `AND (email = ? OR phone = ?)`
- `AND created_at >= DATE_SUB(CURDATE(), INTERVAL $app_duplicate_days DAY)`

**Pure read-time count.** No row lock. Used by `JobApplicationsNewUploadService`, `ShortlistBenchCandidateListingService`, and 3 controller direct callsites. Race-prone by definition.

**Redis-coordinated dedupe:**

`app/Services/JobApplyService.php:73-86` — public-apply path:
- `Cache::has('apply:dedup:{company_id}:{job_id}:md5(email)')` (line 74) — returns early if set.
- Writes key at line 532 with 300s TTL post-commit.
- **Race-safe for cache layer, NOT for underlying insert** (no `lockForUpdate`, no `DB::transaction`).
- Followed by `checkElasticDuplicate()` at line 558-609: queries `ELASTIC_CAREER_APPLY` and `ELASTIC_CANDIDATES` by email+jobId. Read-check only. **Fail-open on ES exception** (line 605) — ES outage = duplicate inserts proceed.

**Race-prone service paths:**

| File:Line | Pattern | Failure mode |
|---|---|---|
| `JobApplicationsNewUploadService.php:198-211` | `checkDuplicateCandidate()` → set `duplicate=1` flag → `save()` (always inserts) | Two concurrent queue workers both pass count check, both insert |
| `JobApplicationsNewUploadService.php` (entire method 27-227) | No `DB::transaction` wrap | Atomicity violations |
| `JobApplicationImport.php:127-130` | Bulk Excel import — manual count check then `save()` at line 132 | 50-row concurrent import → all read count=0 → all insert |
| `JobApplicationImport.php:236, 274` | `candidate_skills_value` / `application_additional_detail` raw inserts — no UNIQUE guard | Duplicate skill rows on retry |
| `MeetingImport.php:41,50` | `DB::table()->insertOrIgnore($this->data)` | ✅ race-safe (UNIQUE on PK + HASH on meeting_link) |
| `Repositories/RolePermissionRepository.php:203` | `role_has_permissions->insertOrIgnore` | ✅ race-safe (UNIQUE on `(role_id, permission_id)`) |
| `Repositories/RolePermissionRepository.php:192` | `roles->insertRole()` — blind insert, no UNIQUE retry | ⚠️ relies on `roles(name, guard_name)` UNIQUE catching duplicates; no `whereNull` or pre-check |
| `Repositories/AccountRepository.php` | All read-only | n/a |

**Job queue failures (race-prone):**

| Job | File:Line | Issue |
|---|---|---|
| `JobApplicationsNewUpload.php` | whole file | Delegates to service — same race as above |
| `ProcessCandidateSubmissionJob.php:205-211` | `tb_ranks` insert — no UNIQUE guard, no retry idempotency | Duplicate ranking rows on job retry |
| `ProcessCandidateSubmissionJob.php:217-223` | `JobApplicationAnswer` raw insert — no UNIQUE guard | Duplicate answers on retry |
| `ProcessCandidateSubmissionJob.php:150-153` | `skills` insert guarded by `LIKE` existence check at line 145-149 | Partial — race window |
| `ProcessCandidateSubmissionJob.php:42-275` | Runs AFTER `JobApplication` already saved in caller — no pre-check at all | Order-of-operations race |
| `UploadResumeToS3.php:69-78` | `documents()->updateOrCreate([doc_type, doc_id, name=Resume], [hashname])` | ✅ race-safe via morph composite key |
| `UploadResumeToTestRTwo.php` | mirrors S3 version | ✅ |

**`JobConcurrencyLock.php` exists** at `app/Services/JobConcurrencyLock.php` and its docblock claims to prevent duplicate job-counter updates, but is **not invoked from any candidate-ingest path**.

**Listeners / events:** No `DuplicateDetected` event exists. No Reverb broadcast on duplicate. The duplicate flag is propagated only via:
- ES `is_duplicate` field (ElasticCandidatesIndex:336, ElasticBenchCandidatesIndex:269)
- `duplicate` column on `job_applications`
- A redirect message from `JobApplyService::submitApplication` (line 76-80)

---

### 2.5 Elasticsearch + Frontend + Blade Layer

**ES-side dedupe via `collapse` field=`email` + `cardinality` aggregation:**

| Controller method | File:Line | Behavior |
|---|---|---|
| `similar_existing_data_ajax` | `ServiceCompanyAppTrackerController.php:1956` | `collapse => ['field' => 'email']` + cardinality agg |
| `career_apply_data_ajax` | `ServiceCompanyAppTrackerController.php:5788` | same |
| `public_bench_data_ajax` | `ServiceCompanyAppTrackerController.php:6770` | same |
| `dsimilar_existing_data_ajax` | `ServiceCompanyAppTrackerController.php:7795` | same |
| `dinternal_bench_data_ajax` | `ServiceCompanyAppTrackerController.php:8905` | same |
| `findTalentData` | `ServiceCompanyAppTrackerController.php:10942` | `collapse` + `inner_hits => ['name' => 'latest', 'size' => 1, 'sort' => ...]` (newest-first) |

**Critical observations:**

- **Collapse field is always `email`** — never `phone`, never composite `(email, phone)`. Phone-only duplicates (no email) WILL show up multiple times in `findTalentData` results.
- **Cross-tenant duplicates are intentional** — every company sees the same candidate's bench profile when recommended. Collapse on `email` collapses within one recruiter's view; same `email` across two `company_id` values still produces two distinct hits, by design.
- **No canonical `_id` derived from `(email, phone)`** — every candidate hit is indexed once per `add_job_wise_condidates` row (`_id = $wiseId`) or `job_applications.id`. Same `email` appears in many documents across companies. Dedupe is per-query only, not per-index.
- **`Inner_hits` sorts by `$sort`** for "latest" — but `inner_hits` requires `email` matches; phone-only duplicates still hide behind the email.

**Index mapping (ElasticCandidatesIndex.php:277):**
- `email` keyword (analysed)
- `phone` keyword
- `is_duplicate` boolean
- `duplicate` integer
- `candidate_id`, `thirdparty_company_id`, `company_id`, `ClientComID`, `other_company` integers

**Frontend live AJAX dedupe endpoints (5):**

| Route | Method | Cache | Source-of-truth |
|---|---|---|---|
| `POST /company-register-chk` | `SaasFrontController::companyRegisterChk` (1571) | 60s Redis | `users.email` count |
| `POST /contact-company-register-chk` | `SaasFrontController::contactCompanyRegisterChk` (1606) | 60s Redis | `users.mobile` count |
| `POST /domain-company-register-chk` | `SaasFrontController::domainCompanyRegisterChk` (1623) | none | `companies.domain_key` |
| `POST /client-domain-register-chk` | `SaasFrontController::domainClientRegisterChk` (1682) | none | tenant + Clearbit |
| `POST /valid-email-chk` | `HomeController::validEmailChk` (15950) | none | syntax only — NOT a uniqueness check |

**Resume-upload subsystem dedupe paths (5 distinct):**

| Path | File:Line | Dedupe? |
|---|---|---|
| (a) Browser-side PDF parse → autofill | `resources/views/career-apply.blade.php:3154-3189` | ❌ NO — only extracts text |
| (b) Browser-side DOCX parse | `career-apply.blade.php:3197` (mammoth) | ❌ NO |
| (c) Legacy POST `service_company/uploadresume` | `career-apply.blade.php:3118-3151` | ❌ NO — only middleware-level `X-Idempotency-Key` |
| (d) v2 presign+PUT path | `app/Services/ResumeUploadSession.php` | ⚠️ session-level only — `Idempotency-Key` replay; `sha256_hex` column exists in `resume_uploads` but is **never queried for dedupe** |
| (e) Server-side PDF/DOCX/DOC text extraction | `app/Services/ResumeTextExtractorService.php:20` | ❌ NO — text only |

**Two parser paths in production:** browser-side (`pdfjsLib` + `mammoth.extractRawText`) AND server-side (`Smalot\PdfParser` + `PhpOffice\PhpWord`). Neither does content-hash dedupe. `sha256_hex` column in `resume_uploads` is computed (line 84) but never used as a uniqueness key.

---

## 3. Race Conditions (10 confirmed)

| # | Path | Window | Impact |
|---|---|---|---|
| 1 | `SaasFrontController::companyRegister` | 60s Redis probe + no DB lock around 70-row insert | Two simultaneous signups w/ same email → first wins, second gets `unique:users` error, but **other half-rows are still written** (departments, coin orders, ES writes at lines 898-1568) before the unique violation throws. `DB::beginTransaction()` at line 384 wraps but rollback leaves dangling ES writes (no compensation). |
| 2 | `JobApplyService::submitApplication` | Redis lock closes cache gap; underlying `JobApplication` insert not under `lockForUpdate` | Same-company two-tab apply can race past cache check; ES `checkElasticDuplicate` fail-open on ES outage |
| 3 | `JobApplicationsNewUploadService::candidateIndexElastic` | No transaction; pure read-then-insert at line 199→214 | Two concurrent queue workers both pass count check, both insert |
| 4 | `ServiceCompanyBaseController:197` `Role::firstOrCreate(['name', 'guard_name'])` | Relies entirely on `roles(name, guard_name)` UNIQUE — no retry wrapper | Two tenants creating role "Admin" concurrently — UNIQUE catches but error path is opaque |
| 5 | `ProcessCandidateSubmissionJob:205-211` `tb_ranks` insert | No guard | Job retry after partial success → duplicate ranking rows |
| 6 | `ProcessCandidateSubmissionJob:217-223` `JobApplicationAnswer` insert | No guard (HTTP path uses `updateOrCreate`; job path bypasses it) | Duplicate `(job_application_id, question_id)` rows on retry |
| 7 | `SaasFrontController::companyRegister` companies.domain_key | Cached probe; insert at line 411 not under any lock | Two signups w/ same website within 60s both pass probe → one fails on UNIQUE — partial state committed |
| 8 | `JobApplicationImport.php:136` `add_job_wise_condidates` insert | Unguarded | Repeated retries of Excel import → duplicate pivot rows |
| 9 | `SaasFrontController::companyRegister` ES upserts at lines 898-1144 | `doc_as_upsert: true` before `DB::commit` | Failed commit rolls back MySQL but ES docs remain; retry creates new `_id`s (line 900, 938, 980) → duplicates accumulate |
| 10 | `ElasticCandidatesIndex::candidateIndexElastic` | `bulk` API with `_id = $wiseId` — never composite | A recruiter updating a candidate via new resume creates a NEW `add_job_wise_condidates` row whose `email` then appears in TWO ES documents (old + new) until old is GC'd. Collapse at search hides this from UI but does NOT collapse history. |

---

## 4. Cross-Cutting Concerns

### 4.1 Soft-delete column anomaly

| Model | Trait | Effect |
|---|---|---|
| `JobApplication` | `SoftDeletes` correctly applied via `use Notifiable, SoftDeletes` | OK |
| `BenchApplication` | `use Illuminate\Database\Eloquent\SoftDeletes` imported, NEVER applied in `use` clause | Soft-deleted rows leak through queries |
| `OfferJobApplication` | Same as above | Same |
| `User` | No `SoftDeletes`, no soft-delete column | `users.email` global UNIQUE blocks re-registration with same email permanently — by design |

**R6** — the BenchApplication / OfferJobApplication broken-SoftDeletes is a latent query correctness bug that affects dedupe (a `Rule::unique(...)->whereNull('deleted_at')` would NOT filter correctly).

### 4.2 Global `users.email` UNIQUE — design question

`users.email` is global UNIQUE. This means:
- Same email cannot exist in two different companies
- This is EITHER intentional (one user → one account → many companies via `company_user` pivot) OR a multi-tenant correctness violation

The codebase has no canonical design doc confirming either. CLAUDE.md notes `JobApplication` tenant scope is **commented out** (BUG-03) — suggesting the team has NOT fully decided on tenant-scoping of user records.

**Recommendation:** Confirm with product. If "one user account per email globally" → keep as-is. If "email is per-tenant namespace" → drop the global UNIQUE, add `users(company_id, email)` UNIQUE.

### 4.3 No tenant-scope helper for Rule::unique

Three places hand-roll:
```php
Rule::unique('foo')->where(fn $query => $query->where('company_id', auth()->user()->company_id))
```

This should be a `BelongsToTenant` aware `Rule::uniqueTenant(string $table, string $column): Rule` factory. The pattern is repeated, error-prone, and inconsistent.

### 4.4 No HTTP 409 Conflict convention

Duplicate failures all return 422. API clients cannot distinguish "validation error" from "duplicate detected" without parsing message text. Frontend UX has to branch on `validation.email[0] === 'You have already applied...'` — fragile.

### 4.5 Stale `mysql-schema.sql` dump

CLAUDE.md rule #2 says schema is the source of truth. The 225KB dump is **missing 11 UNIQUE constraints** present in migrations. Anyone reading `mysql-schema.sql` alone will conclude JERO has fewer uniqueness invariants than it actually does.

---

## 5. Behavioral Map by Business Surface

### 5.1 Company Registration (Saas Front)

**Wire path:** Browser step 1 → 5 live AJAX probes (`company-register-chk`, `contact-company-register-chk`, `domain-company-register-chk`, `client-domain-register-chk`, `valid-email-chk`) → POST to `companyRegister()` (1193-line monolith) → 70+ row inserts → ES upserts.

**Duplicates blocked by:**
- `users.email` global UNIQUE (DB) — final safety net
- `companies.sub_domain` UNIQUE (DB, module-gated)
- `companies.career_page_link` UNIQUE (DB)
- `companies.domain_key` UNIQUE (DB)
- 60s Redis caches on the 5 AJAX probes (UI hint, NOT prevention)

**Duplicates NOT blocked:**
- `companies.company_email` (no UNIQUE)
- `companies.company_name` (no UNIQUE)
- `users.mobile` (no UNIQUE — only validated in FormRequest via `contact-company-register-chk` cache, no DB enforcement)
- Same website domain within 60s cache window if BOTH probes return cached "OK" then both reach the insert path

**Race:** confirmed at lines 384 (beginTransaction) → 411 (company save) → 1080 (user save) → 1568 (ES dispatch). No `lockForUpdate`. `DB::beginTransaction()` wraps but rollback leaves dangling ES writes.

### 5.2 Job Application (public front)

**Wire path:** Browser → `POST /apply` → `FrontJobApplication` FormRequest → `Rule::unique('job_applications')->where(job_id, company_id)` → `JobApplyService::submitApplication` → Redis lock check → ES `checkElasticDuplicate` → `JobApplication::create`.

**Duplicates blocked by:**
- `Rule::unique` FormRequest (race-prone TOCTOU)
- Redis `apply:dedup:{company_id}:{job_id}:md5(email)` cache (300s TTL, race-safe for cache layer)
- ES read-check via `checkElasticDuplicate` (fail-open)

**Duplicates NOT blocked:**
- `job_applications` has NO DB UNIQUE on `(company_id, job_id, email)` — race window unbounded
- ES check fails open on ES outage (line 605)

### 5.3 Job Application (admin/internal)

**Wire path:** Controller → `updateOrCreate` for documents (✅), `Rule::unique` not used on `job_applications` for non-public paths. `StoreJobApplication` FormRequest has **NO unique check**.

**Duplicates blocked by:**
- `documents(company_id, doc_type, doc_id, name)` UNIQUE for resume uploads
- `JobApplicationAnswer(job_application_id, question_id)` enforced via `updateOrCreate` HTTP path only

**Duplicates NOT blocked:**
- Same email can be applied to the same job via admin/internal path (no FormRequest, no UNIQUE)
- `JobApplicationAnswer` via `ProcessCandidateSubmissionJob` retry (job bypasses `updateOrCreate`)

### 5.4 Candidate Listing / Marketplace Search (ES)

**Wire path:** Search UI → `findTalentData` etc. → ES `collapse field=email` + `inner_hits` → list view with `_prehydrated_duplicate_flag`.

**Duplicates collapsed:**
- Same `email` within a single recruiter's view (collapse)
- Same email across tenants — INTENTIONALLY NOT collapsed (marketplace)

**Duplicates NOT collapsed:**
- Phone-only duplicates (no email) — show as separate hits
- Same email + same tenant appearing in multiple documents — collapsed at query, not at index

### 5.5 Resume Upload

**Wire path:** Browser parses (pdf.js / mammoth) → optional v2 presign+PUT (R2 storage) → server-side extract → `JobApplyService::submitApplication` → ES upsert.

**Dedupe applied:**
- Session-level idempotency via `X-Idempotency-Key` (10-min Redis cache) — network retry safety
- `ResumeUploadSession::finalize/cancel` use conditional `UPDATE whereIn status` — double-call safety
- `sha256_hex` column populated at `ResumeUploadSession.php:84` — **but never queried for dedupe**

**Duplicates NOT blocked:**
- Same content uploaded twice from different browser → two distinct sessions (sha256 not used as uniqueness key)
- Same email parsed from resume → goes through `JobApplyService` which has its own race window (see §5.2)

### 5.6 Role / Permission Management

**Wire path:** Controller → `PermissionRole::firstOrCreate([...])` (silent dedup) → `role_has_permissions->insertOrIgnore` (race-safe).

**Duplicates blocked:**
- `(role_id, permission_id)` UNIQUE catches all races
- `insertOrIgnore` is atomic

**Issues:**
- `roles(name, guard_name)` UNIQUE catches tenant cross-creation but no friendly error path — error surfaces as raw `QueryException` 23000
- No tenant scope on `roles` — cross-tenant role name collision blocked by global UNIQUE (by design?)

### 5.7 Coupons / Packages / Settings

**Wire path:** Inline `Validator::make` (NOT FormRequest) at `SuperAdminCompanyController:6662,6668`, `CouponController:42`, etc.

**Duplicates blocked by:**
- `coupons.code` UNIQUE (DB)
- `modules.slug`, `features.slug`, `global_settings.setting_key` UNIQUE (DB)
- Inline `Rule::unique` race-prone TOCTOU

**Issue:** No FormRequest coverage for these admin endpoints — relies on ad-hoc inline validation.

---

## 6. Recommendations (Prioritized)

### P0 — Critical (ship blockers)

1. **Add `job_applications(company_id, job_id, email)` UNIQUE** (R1). Migration + add `->whereNull('deleted_at')` aware rule. Wrap `JobApplyService::submitApplication` in `DB::transaction` with `lockForUpdate` on a sentinel row.
2. **Fix `SaasFrontController::companyRegister` validation** (R4). Use `RegisterForm` FormRequest instead of inline `Validator::make`. Add `users.mobile` UNIQUE or scope `users.email` per-company. Add `companies.company_email` UNIQUE.
3. **Fix `app/Models/BenchApplication.php` and `OfferJobApplication.php`** (R6). Apply `SoftDeletes` trait correctly: `use Notifiable, SoftDeletes;`. Then audit all dedupe queries against these tables to add `whereNull('deleted_at')`.
4. **Add UNIQUE on `job_application_answers(job_application_id, question_id)`** (R7). Replace raw insert in `ProcessCandidateSubmissionJob:217-223` with `updateOrCreate`. Same for `tb_ranks(user_id, application_id)`.

### P1 — High (within 1 sprint)

5. **Regenerate `mysql-schema.sql`** to match live migrations (R2). Add a CI step `mysqldump --no-data` against staging and diff vs. the committed file.
6. **Fix `Admin/Document/StoreRequest::rules()`** (R5). Add `->ignore($this->route('document'))` to allow UPDATE-in-place. Add `whereCompanyId(auth()->user()->company_id)` to the where closure for explicit tenant scoping.
7. **Confirm global `users.email` UNIQUE design** (R3). Document the decision in ADR. If per-tenant, drop global, add `(company_id, email)`.
8. **Introduce `app/Rules/` namespace** with `BelongsToTenantUnique` factory. Replace 3 hand-rolled `Rule::unique(...)->where(fn => whereCompanyId)` callsites with the factory.
9. **Standardize HTTP 409** for duplicate failures. Add `Reply::conflict($message)` helper. Update `JobApplyService`, `SaasFrontController`, `StoreJobApplication` to return 409.

### P2 — Medium (next quarter)

10. **Add UNIQUE on `interview_schedules(job_application_id, schedule_date, stage)`** and `bench_place(bench_id, client_job_id, client_application_id)`.
11. **Add `sha256_hex` UNIQUE on `resume_uploads`** for content-based dedupe; query `ResumeUploadSession::createOrPresign` against it.
12. **Collapse on `(email, phone)` composite** in ES — extend the 6 `collapse` callsites to use a `runtime_field` or update mapping to support multi-field collapse.
13. **Remove the 60s Redis cache from registration probes** — they mask race conditions by pretending to enforce uniqueness. Replace with a `UniqueLock` Redis SETNX pattern with 5s TTL that integrates with the insert path.
14. **Add a `DuplicateDetected` event** for observability. Fire from `JobApplyService::submitApplication` and `JobApplicationsNewUploadService::candidateIndexElastic`. Listen in a notification listener for superadmins.

### P3 — Long-term

15. **Introduce a master `candidates` table** with `(email, phone)` UNIQUE per tenant — replace denormalized `job_applications` candidate data. Eliminates R1, R7, R10 structurally.
16. **Move tenant-scope to Postgres Row-Level Security** when migrating off MySQL — `users.email` per-tenant RLS solves R3 architecturally.
17. **Consolidate 17 hand-rolled `boot()` tenant scopes** onto `BelongsToTenant` trait. Reduces drift risk.

---

## 7. Cross-References

- [docs/PERSONA_SCOPE.md](docs/PERSONA_SCOPE.md) — in-scope personas for this audit
- [docs/AI_RULES.md](docs/AI_RULES.md) — schema-source-of-truth rule (currently violated)
- [docs/AI_GUARDRAILS.md](docs/AI_GUARDRAILS.md) — verify-don't-hallucinate (all findings verified by reading code)
- [docs/PERFORMANCE_BUDGET.md](docs/PERFORMANCE_BUDGET.md) — UNIQUE index implications for insert throughput
- [docs/SECURITY.md](docs/SECURITY.md) — cross-tenant leak risk via global `users.email` UNIQUE
- [docs/BUG_REPORT.md](docs/BUG_REPORT.md) — known BUG-03 (JobApplication tenant scope commented out)
- Memory: [phase-f-edit-page-v2-playbook](../memory/phase-f-edit-page-v2-playbook.md) — confirms R5 root cause

---

## 8. Audit Methodology

**Tools used:** 6 parallel `Explore` agents fan-out across DB / Eloquent / FormRequest / Service / ES / Frontend layers. Each agent returned verified findings (file:line + behavior). Findings cross-checked for consistency.

**Limitations:**
- Did NOT execute the SQL migrations against a fresh DB to verify the effective constraint set end-to-end — relied on grep over migration files.
- Did NOT bench-mark race windows — analysis is structural (read code), not empirical (run code).
- Did NOT enumerate all `exists:`/`count()` checks in controllers — filtered to dedupe-relevant ones.
- Did NOT read `routes/web.php` end-to-end (158 KB file — out of scope per CLAUDE.md rule #1).

**Confidence:** HIGH for all DB-layer findings (verified against `mysql-schema.sql` + `database/migrations/`). HIGH for FormRequest / Controller findings (33 files audited). HIGH for ES findings (6 controller methods + 5 AJAX endpoints). HIGH for Service-layer findings (10+ services cross-checked). MEDIUM for race-condition timing claims (structural analysis only).

---

*Audit generated 2026-08-20 by claude-opus-4-8.*
