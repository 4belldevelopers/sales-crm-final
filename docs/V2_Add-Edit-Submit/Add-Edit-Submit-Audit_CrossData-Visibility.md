# Cross-Account Data Visibility Audit — V2/V3 Readiness

> **Date:** 2026-08-16
> **Companion to:** [`Add-Edit-Submit-Audit.md`](Add-Edit-Submit-Audit.md) (CRUD audit, 196 routes) and [`Add-Edit-Submit-Audit_Transitions.md`](Add-Edit-Submit-Audit_Transitions.md) (transition audit, 196 routes reclassified).
> **Scope (strictly enforced):** the four **in-scope personas** — Service Company, Super Admin, Admin, public Front/SaasFront. **Product Co and Staffing Co are dormant — not in this audit.** Full scope rule: [PERSONA_SCOPE.md](../PERSONA_SCOPE.md).
> **Goal:** produce the single source of truth for **what data each persona sees about which other persona**, and identify every cross-tenant leak surface that must be closed before 10K-concurrent launch and V2/V3 enablement.
>
> **Personas missing from this audit:** Product Company, Staffing Company.

---

## How to read this document

1. **§ 1 — Three-account model.** Defines "agency", "hiring company", and "candidate" — the three legal entities whose data crosses in JERO.
2. **§ 2 — Summary at a glance.** Tenancy columns per table, top 5 cross-data leak surfaces, scope rules per persona.
3. **§ 3 — Data-Visibility Matrix (the heart of this audit).** Every cross-account entity × every persona × every field, with show/hide/mask classification.
4. **§ 4 — Controller tenancy map.** Per-controller SHOW method, the WHERE/ES filter applied, and what it leaks.
5. **§ 5 — Per-entity deep-dive.** Schema tenancy fields, the canonical read path, the cross-tenant gap, and the mask that *should* exist.
6. **§ 6 — UI mask audit.** Where contact data is masked and where it isn't (the `bench-candidates/showdetail.blade.php` `$rcvMaskContact` boundary and 12 sibling views).
7. **§ 7 — Cross-tenant leak vectors — top 10.** The specific `WHERE` clauses missing or commented out, with file:line references.
8. **§ 8 — Remediation roadmap.** Sequenced Phase 8 (Mask & Scope) before 10K launch, ~12 dev-days.
9. **§ 9 — Related materials.**

---

## 1. Three-account model

JERO is a **multi-tenant hiring marketplace**. Three distinct legal entities exchange data inside the same MySQL+ES store:

| Entity | Role in JERO | `companies.company_type` | Identified by |
|---|---|---|---|
| **Agency** (Service Company) | Sources candidates, places them at hiring companies, earns commission/fee on placement. | `2` (`Service Company`) | `companies.id` = `users.company_id` = `job_applications.thirdparty_company_id` |
| **Hiring Company** (Client) | Posts jobs, receives candidates, hires them. May also source from other agencies (via `staffing_company_relation`, `consultancy`). | Same code (also `2`) — there is **no first-class hiring-company persona** in this schema. A hiring company is a `companies` row whose employees are *users* belonging to that company and who interact with agencies through `recommendation_relation`. | `companies.id` = `users.company_id` = `jobs.company_id` |
| **Candidate** | An individual whose PII (name, phone, email, dob, resume, CTC, photo) sits in `job_applications` and `bench_applications`. | n/a — not a tenant | `job_applications.id`, `bench_applications.id` |

> **Important schema fact:** there is **no first-class hiring-company persona**. The "hiring company" is just another `companies` row, often the same `company_type=2` (Service) or (in dormant code) `company_type=3` (Product) — both dormant in scope per [PERSONA_SCOPE.md](../PERSONA_SCOPE.md). A Service tenant that posts its own jobs and sources candidates from itself is the *same* `companies` row used by the cross-tenant "client" view. This means tenancy boundaries inside JERO are **intra-table tenancy splits** (`job_applications.company_id` vs `job_applications.thirdparty_company_id`), not separate-persona separations.

### What each persona sees — the canonical rule

| Persona | Sees its **own** | Sees other **agencies'** | Sees **hiring companies'** | Sees **candidates'** |
|---|---|---|---|---|
| **Service Co (agency)** | All of its own `company_id` rows (jobs, candidates, commercials, billing, team). | Only via explicit cross-tenant bridge (`recommendation_relation.to_company_id`, `staffing_company_relation`, `consultancy`) — i.e. only when a hiring relationship is established. | Same as "other agencies": via `recommendation_relation.to_company_id`. | All `job_applications` rows where `thirdparty_company_id = self` OR `company_id = self` (the dual-tenancy pattern on `job_applications`). |
| **Hiring Co** (a `companies` row of `company_type=2` or dormant `3`) | All its own rows. | Only when invited (`staffing_company_relation.from_company_id`). | n/a (self) | Candidates submitted against its jobs (`job_applications.company_id = self`). |
| **Super Admin** | All rows on the platform. | All rows. | All rows. | All rows. |
| **Admin** | All rows of its own `company_id` only. | None (unless its company is also an agency that invited another — which is allowed in the schema but not exercised in the active code paths). | None (Admin is company-scoped, not platform-scoped). | Its own candidates only. |
| **Front / SaasFront** | Public read only (`featured`, `active`, `show_in_frontend=true`). | Public-read-only via the marketplace. | Public-read-only. | Public profiles only (resume, name, skills); contact masked unless the candidate has accepted an offer. |

---

## 2. Summary at a glance

| Metric | Value |
|---|---:|
| Tables holding cross-account data | **18** (job_applications, add_job_wise_condidates, bench_applications, bench_place, offer_job_applications, interview_schedules, commericials, commericial_details, commericial_countries, recommendation_relation, staffing_company_relation, consultancy, documents, clients, users, coin_orders, coin_transection_logs, on_board_details) |
| Tables with dual tenancy (`company_id` + `thirdparty_company_id`) | 2 (`job_applications`, `add_job_wise_condidates`) |
| Cross-tenant bridge tables (no fixed tenancy) | 5 (`recommendation_relation`, `staffing_company_relation`, `consultancy`, `consultancy_file`, `bench_place`, `conversation_log`) |
| Tables with **no** tenancy column at all | 4 (`application_event`, `coin_events`, `bench_master_status`, `interview_status`) — global lookup only, OK |
| Tables with tenancy that is **ambiguous** (no `company_id`, scoped via FK only) | 5 (`bench_candidate_skill_experience`, `bench_count_view_download`, `comments`, `commericial_details`, `commericial_countries`) — leak risk on FK reuse |
| Controller SHOW methods missing `company_id` filter | **9** (P0 — see §7) |
| Public read paths leaking across tenants | **3** (`HomeController::jobDetails`, `HomeController::jobApply` `?? 2` fallback, `bench-candidates/public_show`) |
| UI views with explicit mask logic | **1** (`bench-candidates/showdetail.blade.php:809` — `$rcvMaskContact`) |
| UI views that *should* have mask logic but don't | **12+** (apptracker showdetail, cvtracker showdetail, bench index, offer index, onboard tracker, etc.) |

**One-sentence verdict:** the schema is correctly tenancy-scoped on the paper — every cross-account table has a `company_id`, plus a `thirdparty_company_id` on the two hottest ones (`job_applications`, `add_job_wise_condidates`). **But 9 controller methods bypass the filter entirely** (direct `JobApplication::find($id)` and friends), and 1 Blade view (`bench-candidates/showdetail.blade.php`) is the *only* place that explicitly masks contact data — and only for bench candidates, only for one viewer-side company. The result is that any Service-tenant user can open `bench-candidates/{id}` or `job-applications/clientshow/{id}` and read another agency's PII by id-probing. The same is true for interview schedules (the `where('interview_schedules.company_id', user()->company_id)` filter is **commented out** at `ServiceCompanyJobApplicationController.php:3399`). Closing these gaps is Phase 8 — ~12 dev-days before 10K-concurrent launch.

---

## 3. Data-Visibility Matrix

> Format: per entity, per persona, what fields the persona sees. **See §6** for the underlying Blade + controller code, **§7** for the specific gaps.
>
> Legend: **✅ visible** · **🔒 masked** (PII redacted; commercial terms redacted; etc.) · **❌ hidden** (not surfaced) · **⚠ partial** (some fields masked, others exposed) · **— N/A**.

### 3.1 `job_applications` (the hottest row — 63619 rows, 30+ write paths)

The `job_applications` table carries **dual tenancy**: `company_id` (the hiring company that owns the job) + `thirdparty_company_id` (the agency that submitted the candidate). The view code (e.g. `ElasticCandidatesIndex::candidateIndexElastic`) joins both sides and exposes both `company_name`s (`apl_company_name`, `post_company_name`, `cur_company_name`).

| Field | Agency (own row) | Agency (other agency's row) | Hiring company (own row) | Super Admin | Admin | Front (public) |
|---|---|---|---|---|---|---|
| **PII** (full_name, email, phone, dob, address, photo, resume, skype_id, linkedin, github, behance, otherLink) | ✅ | ⚠ masked in `bench-candidates/showdetail.blade.php` only; **raw everywhere else** | ✅ | ✅ | ⚠ only own tenant | ❌ |
| **CTC / ECTC / pay_rate / bill_rate** | ✅ | ⚠ same gap as PII | ✅ | ✅ | ⚠ | ❌ |
| **Offer fields** (offer_amount, offer_currency, offer_mode, proposed_ctc) | ✅ | ⚠ | ✅ | ✅ | ⚠ | ❌ |
| **Owning company name** (`company_id` join) | ✅ | ✅ exposed unconditionally via `apl_company_name` | ✅ | ✅ | ⚠ | ❌ |
| **Submitting agency name** (`thirdparty_company_id` join) | ✅ self | ✅ exposed unconditionally via `post_company_name` | ✅ | ✅ | ⚠ | ❌ |
| **Hiring company name** (`jobs.company_id` join) | ✅ self | ✅ exposed unconditionally via `cur_company_name` | ✅ self | ✅ | ⚠ | ❌ |
| **Stage / status** (`master_status_id`, `child_status_id`, `status_id`) | ✅ | ⚠ | ✅ | ✅ | ⚠ | ❌ |
| **Interview dates** (`interviewDateOne..Six`) | ✅ | ⚠ | ✅ | ✅ | ⚠ | ❌ |
| **Recruiter note** (`recruiter_note`, `notice_comments`) | ✅ | ⚠ | ❌ (typically hidden from client) | ✅ | ⚠ | ❌ |
| **Comments** (via `comments` table) | ✅ own | ❌ | ⚠ own (own ticket) | ✅ | ⚠ | ❌ |

### 3.2 `add_job_wise_condidates` (the apptracker pivot — 63804 rows)

Carries `company_id` (hiring co) + `thirdparty_company_id` (agency). Same dual-tenancy pattern.

| Field | Agency | Hiring co | Super Admin | Admin | Front |
|---|---|---|---|---|---|
| `placed_end_date`, `existing`, `skill_match` | ✅ | ✅ | ✅ | ⚠ own | ❌ |
| `status_id`, `master_status_id`, `child_status_id` | ✅ | ✅ | ✅ | ⚠ | ❌ |

### 3.3 `bench_applications` (the marketplace pool)

Single tenancy: `company_id` = the bench-owner agency. Visible cross-tenant only via `bench_place.client_company_id` (placement record).

| Field | Owning agency | Other agency (browsing marketplace) | Hiring company (after placement) | Super Admin | Admin | Front |
|---|---|---|---|---|---|---|
| **PII** (name, email, phone, dob, photo, resume) | ✅ | ⚠ redacted in `bench-candidates/showdetail.blade.php` ONLY for the partial `find-talents` redaction path. **All other read paths return raw PII.** | ✅ (after placement via `bench_place`) | ✅ | ⚠ own | ⚠ public redacted profile (name, skills, exp, location only) |
| **CTC / ECTC / bill_rate / pay_rate** | ✅ | ⚠ — visible in the marketplace browse (`bench-candidates/public_show` and `find-talents`), which is **commercial-sensitive** but is the marketplace's whole point. | ✅ | ✅ | ⚠ | ❌ |
| **Bench owner company name** (`bench_applications.company_id` join) | ✅ self | ✅ exposed unconditionally via `company_name` | ✅ via `bench_place.source_comapny_id` | ✅ | ⚠ | ⚠ public |
| **Placed-at company** (`bench_place.client_company_id`) | ✅ own placement | ❌ | ✅ via `bench_place` | ✅ | ⚠ | ❌ |
| **Placement commercial** (`bench_place.commission/payout`) | ✅ own | ❌ | ❌ (the hiring co sees the offer via `offer_job_applications`, not `bench_place`) | ✅ | ⚠ | ❌ |
| **View/download counts** (`bench_count_view_download`) | ✅ own | ❌ | ❌ | ✅ | ⚠ own | ❌ |
| **Bench skills** (`bench_candidate_skill_experience`) | ✅ own | ❌ | ⚠ placed only | ✅ | ⚠ | ⚠ |

### 3.4 `offer_job_applications` (the offer letter record)

Single tenancy: `company_id` = the hiring company that issued the offer. The **agency is not stored on this table** — the link back to the agency is via `offer_job_applications.application_id → job_applications.id → job_applications.thirdparty_company_id`.

| Field | Hiring co (own offer) | Agency (offer on its placed candidate) | Super Admin | Admin | Front |
|---|---|---|---|---|---|
| **Offer letter text** (`offer_create_main_tds`, `offer_terms_conditions`) | ✅ | ✅ | ✅ | ⚠ | ⚠ via public link |
| **Commercial terms** (`commericial_*` fields added in [2026-08-14 migration](../../database/schema/2026-08-14-add-commercial-policy-fields.sql)) | ✅ | ✅ | ✅ | ⚠ | ❌ |
| **Confidential flag** (`offer_job_applications.confidential`) | ✅ | ✅ | ✅ | ⚠ | ❌ |
| **Offer amount, currency, mode** | ✅ | ✅ | ✅ | ⚠ | ⚠ via offer_code |
| **Acceptance status** | ✅ | ✅ | ✅ | ⚠ | ❌ |
| **Salary offered** (`on_board_details.salary_offered`) | ✅ | ⚠ only via the candidate's onboarding step | ✅ | ⚠ | ❌ |

### 3.5 `interview_schedules` (the interview pipeline)

Single tenancy: `company_id` = the agency that scheduled the interview. The hiring company has no first-class read path.

| Field | Scheduling agency | Hiring co (own candidate, scheduled by agency) | Super Admin | Admin | Front |
|---|---|---|---|---|---|
| **Schedule (date, time, mode, link)** | ✅ | ❌ — no read path in active code | ✅ | ⚠ own | ❌ |
| **Interviewer names** | ✅ | ❌ | ✅ | ⚠ | ❌ |
| **Feedback / ratings** | ✅ | ❌ | ✅ | ⚠ | ❌ |
| **Status / available_status / followup_status** | ✅ | ❌ | ✅ | ⚠ | ❌ |
| **Other candidates' schedules** | ⚠ read via `add_job_wise_condidates.id` (the commented-out filter — see §7) | ❌ | ✅ | ⚠ | ❌ |

### 3.6 `commericials` (the agency's pricing rules) — STRICTLY PRIVATE

Single tenancy: `company_id` = the agency that owns the commercial. **No path from a hiring company to an agency's commercials was found** in the active code — the public career page reads `commericials` only for the company the candidate is applying to (i.e. the hiring company's own commercials). **Verified safe** by `HomeController.php:1159-1171` and `ServiceCompanyJobsController.php:14093-14095`.

| Field | Owning agency | Other agency | Hiring co | Super Admin | Front |
|---|---|---|---|---|---|
| `rateType`, `commtype`, `status` | ✅ | ❌ | ❌ | ✅ | ⚠ own only |
| `sal_mode`, `salary_currency`, `payment_terms_days`, `billing_*`, `replacement_*` | ✅ | ❌ | ❌ | ✅ | ⚠ |
| `commericial_details[].rate` per experience band | ✅ | ❌ | ❌ | ✅ | ⚠ |
| `commericial_countries[]` | ✅ | ❌ | ❌ | ✅ | ⚠ |

### 3.7 `clients` (the hiring company's CRM — its own customers)

`clients.company_id` = the hiring company that added this client. **A `clients` row IS one of the hiring company's customers.** This means an agency's `clients` list and a hiring company's `clients` list are independent of each other.

| Field | Owning hiring co | Other hiring co | Agency | Super Admin | Admin | Front |
|---|---|---|---|---|---|---|
| **Client name, contact, email, phone** | ✅ | ❌ | ❌ | ✅ | ⚠ own | ❌ |
| **Commission % and payout** (these are commercial terms) | ✅ own | ❌ | ⚠ — agency can see `clients` only for jobs where they're the recommending agency | ✅ | ⚠ | ❌ |
| **Logo, website, industry, size, founded, description, tags** | ✅ | ❌ | ⚠ via the job they're attached to | ✅ | ⚠ | ⚠ public if `show_in_frontend` |

### 3.8 `users` (team members) — STRICTLY TENANT

`users.company_id` = the tenant that owns the user. `is_superadmin=1` rows have `company_id IS NULL`.

| Field | Own tenant | Other tenant | Super Admin | Admin | Front |
|---|---|---|---|---|---|
| **Name, email, phone, image, role** | ✅ | ❌ | ✅ | ⚠ own | ❌ |
| **Active session, session_time, session_id** | ✅ own | ❌ | ✅ | ⚠ own | ❌ |
| **`super_password`, `super_admin_login_code`** | ❌ (never shown even to the user) | ❌ | ✅ (own backdoor only) | ❌ | ❌ |
| **Recruiter display name** (`applicant_notes.user_id`) | ✅ | ❌ | ✅ | ⚠ | ❌ |

### 3.9 `coin_orders` / `coin_balances` / `top_up_logs` / `coin_transection_logs` — STRICTLY TENANT

`coin_orders.company_id` = the buyer. `coin_transection_logs` has BOTH `company_id` and `from_company_id` (inter-tenant transfers are possible but no controller exercises them in the active code). **`coin_events`, `coin_packages`, `top_up_coin_packages`, `coin_package_types`, `coin_package_points` are GLOBAL** (no tenancy) — they're catalog tables, safe.

| Field | Own tenant | Other tenant | Super Admin | Admin | Front |
|---|---|---|---|---|---|
| **Coin balance / package quota** (`company_coins`) | ✅ own | ❌ | ✅ | ⚠ own | ❌ |
| **Coin orders** (`coin_orders`) | ✅ own | ❌ | ✅ | ⚠ own | ❌ |
| **Top-up transactions** | ✅ own | ❌ | ✅ | ⚠ own | ❌ |
| **Transfer logs** (between companies) | ✅ own side | ❌ | ✅ | ⚠ own | ❌ |

### 3.10 `subscriptions` / `company_packages` / `package_invoice` — STRICTLY TENANT

Single tenancy: `company_id`. **No cross-tenant exposure.**

### 3.11 `notifications` — POLYMORPHIC RECIPIENT

`notifiable_type + notifiable_id` is the recipient. No `company_id` column. Cross-tenant only when the recipient is Super Admin (`is_superadmin=1, company_id IS NULL`).

### 3.12 `tickets` / `comments` — TICKET-PARENTED, COMMENT AMBIGUOUS

`tickets.company_id` exists; **`comments` has no `company_id`**, only `ticket_id` + `user_id`. A comment on a Service tenant's ticket, when the ticket is moved across companies, travels with the ticket. This is the canonical "ambiguous tenancy" pattern.

### 3.13 `on_board_details` / `on_board_files` — HIRING-CO TENANT

`company_id` = the hiring company that ran the onboarding. **Agency is not stored on this table** — link via `on_board_details.job_application_id → job_applications.thirdparty_company_id`.

| Field | Hiring co (own onboarding) | Agency (placed candidate) | Super Admin | Admin | Front |
|---|---|---|---|---|---|
| **Salary offered, joining date, accept-by date, offer code** | ✅ | ⚠ via offer_code URL flow only | ✅ | ⚠ | ⚠ |
| **Department / designation / reports_to** | ✅ | ❌ | ✅ | ⚠ | ❌ |
| **Signature image** (`sign`) | ✅ own | ✅ via offer_code URL | ✅ | ⚠ | ⚠ |
| **Reject reason** | ✅ | ✅ via offer_code URL | ✅ | ⚠ | ❌ |

---

## 4. Controller tenancy map

> Per-controller SHOW/GET method, the WHERE / Eloquent scope / ES filter, and what it leaks. Cross-references §7 for the specific gaps.

### 4.1 `ServiceCompanyJobsController.php`

| Method | Line | Filter | Leak |
|---|---|---|---|
| `index` | 1277 | cache key `company:'.$this->user->company_id` (1287); counts branch on `job_assign.user_id` for non-admin role | none cross-tenant |
| `show($id)` | 4994 | **`Job::find($id)` — no filter** | any Service user with a valid id gets the job |
| `viewMore(Request, $id)` | 5475 | ES direct GET by id; computes `agencyView` for view-rendering only | cross-tenant read by id is permitted by design |
| `serviceviewmore` | 14078 | `where('company_id', $company_id)` (14095) — **strict** | none |

### 4.2 `ServiceCompanyJobApplicationController.php` + `ServiceCompanyAppTrackerController.php`

| Method | Line | Filter | Leak |
|---|---|---|---|
| `AppTracker::index($id,$mstatus_id)` | 80 | `Job::findOrFail($id)` — **none on the Job lookup** (106, 138) | any Service user can open any job's apptracker board by id |
| `AppTracker::showInterviews($id)` | 11392 | `where('company_id', $companyId)` (11399) — **strict** | none |
| `JobApplication::show($id)` | 2624 | `AddJobWiseCondidate::find($id)` then ES direct GET — **none** | full candidate PII to anyone with the id |
| `JobApplication::show` → `interview_schedules` join | 3396 | **`where('interview_schedules.company_id', user()->company_id)` is commented out** (3399) | **P0** — any tenant can read any interview schedule |
| `JobApplication::clientshow($id)` | 9704 | `AddJobWiseCondidate::find($id)` — **none** | **P0** — the canonical cross-tenant read path (hiring co reads agency's candidate) |
| `JobApplication::websiteShow($id)` | 3180 | ES direct GET by id; auth-walled to the candidate's own session | intended (self view) |
| `JobApplication::clientcreate` | 9060 | form renderer | n/a |

### 4.3 `ServiceCompanyBenchCandidatesController.php`

| Method | Line | Filter | Leak |
|---|---|---|---|
| `index` | 64 | ES `term: company_id = self` + `condidate_type = bench_condidate` (77-78) — **strict** | none |
| `show($id)` | 875 | `JobApplication::...->where('job_applications.id', $id)->first()` (971) — **none** | **P0** — any Service user can read another agency's bench record |
| `bench_application_status` | 2391 | `JobApplication::...->where('id', $id)->first()` (2451) — **none** | PII including rates, dob |
| `bench_explore` | 4934 | `JobApplication::find($id)` (4940-4960) — **none** | **P0** — cross-tenant bench read |
| `public_show($id)` | 5817 | `where('job_applications.id', $id)->first()` (5904) — **none**. Redaction at 5906-5917 conditional on `previousUrl` containing `find-talents`. | **P0** — when redaction branch misses, full PII returned. Always returns owning agency's company_name/website/company_size/founded/company_type and the candidate's CTC/bill_rate/pay_rate/photo/resume/employee_number. |
| `hmlisting($id)` | 2330 | `where('users.company_id', $company_id)` (2353) — **strict** | none |

### 4.4 `ServiceCompanyCvTrackerController.php`

| Method | Line | Filter | Leak |
|---|---|---|---|
| `index` | 81 | counters branch on `add_job_wise_condidates.company_id = self` OR `add_job_wise_condidates.thirdparty_company_id = self` | agency sees own + any client-submitted-back |
| `allcandidates` | 179 | ES filter branches on `company_id` and `thirdparty_company_id` | OK by design |
| `fwd_resume` | 2689 | tenant scope at 2725 | OK |
| `offerSendMail` | 2834 | tenant scope at 2871 | OK |

### 4.5 `SuperAdminCompanyController.php`

| Method | Line | Filter | Leak |
|---|---|---|---|
| `index` | 121 | `$must = []` (size 10000) — intentionally unscoped (SuperAdmin) | by design |
| `data()` | 1626 | DataTables over all rows | by design |
| `show($id)` | 1098 | `Company::withoutGlobalScope('company')->findOrFail($id)` (1101) — explicit | by design |
| `viewMore($id)` | 2622 | `Company::withoutGlobalScope('company')` + `MeetingSchedule` query **without any `where`** (2672-2678) + `coin_orders`/`company_coins` (2695-2720) | by design, but `MeetingSchedule` is **global** — every meeting between every agency and every client is on this page |
| `candidateApproval` | 5874 | POST | n/a |
| `jobApproval` | 11494 | POST | n/a |
| `showfeedback` | 11109 | `where('conversations.id', $request->conversations_id)` (11117) — **no `company_id` filter on `conversation_log`** | SuperAdmin cross-tenant by design |

### 4.6 `AdminAccountSettingsController.php`

| Method | Line | Filter | Leak |
|---|---|---|---|
| `index` | 48 | Strictly `auth()->user()->company_id` (60, 61, 87, 89, 90, 158, 166, 174, 181, 188, 195, 210) | none cross-tenant |
| `data1` | 972 | `Skill::all()` (no company_id) | OK — skills are global |
| `add_team_member_form` / `sendinvitation` | 1091 / routed in `web.php:1130` | **`sendinvitation` does NOT exist on this controller** — the route 404s. Cross-persona misuse flagged in [Add-Edit-Submit-Audit.md §5.B](Add-Edit-Submit-Audit.md). | n/a |

### 4.7 `SaasFrontController.php` / `HomeController.php`

| Method | Line | Filter | Leak |
|---|---|---|---|
| `index` (SaasFront) | 52 | Language-scoped only; `Company::where('featured', 1)` — intended | public landing page |
| `benchCandidates` (HomeController) | 822 | ES `term: company_id = $company_id` (975) + `condidate_type = bench_condidate` (977) | **P1** — only the publicly-listed company's bench |
| `careerJobPage` (HomeController) | 1110 | `where('companies.id', $company_id)` + `where('commericials.company_id', $company_id)` (1170-1171) — **strict** | none |
| `jobApply` (HomeController / SaasFront) | 1448 | `Job::where('jobs.company_id', $company_id)` (1484); **`$companyId = (int) ($company_id ?? 2)` at 1458** — falls back to company 2 | **P0** — coin check + candidate count attributed to company 2 when `company_id` is missing |
| `jobDetails` (HomeController) | 1555 | `Job::where('jobs.id', $jobId)` with status filter only (1495-1501, 1579-1600) — **no `company_id` on the job itself** | **P1** — public unauthenticated user can read any active job by id |
| `downloadResume($id, $encodedPath)` | 791 | path is encoded | see Phase 7.4 webhook hardening for related P1 |

### 4.8 Front / FrontJobOfferController

| Method | Line | Filter | Leak |
|---|---|---|---|
| `index($offerCode)` | 43 | `Onboard::where('offer_code', $offerCode)->first()` then `Job::where('id', $this->offer->applications->job->id)->first()` — **no company_id filter** | by design (offer code is the secret), but a leaked code = full PII |
| `jobDetail` | 177 | `where('company_id', $companyId)` (193) — **strict** | none |
| `jobApply` | 262 | `where('company_id', $companyId)` | OK |

---

## 5. Per-entity deep-dive

### 5.1 `job_applications`

**Schema tenancy fields:** `company_id` (hiring co) + `thirdparty_company_id` (agency). Indexes: `idx_ja_company_status`, `idx_ja_company_type_status`, `idx_ja_submitted_by`, `idx_ja_city_id`.

**Canonical read path:** `ElasticCandidatesIndex::candidateIndexElastic($comid)` (line 20) — fetches one `add_job_wise_condidates` row by id, then joins EVERYTHING (`master_status`, `jobs`, `recommendation_relation`, `companies` ×3 aliases, `offer_job_applications`, `clients`, `job_locations`, `users`, `documents`, `qualification_master`, `countries`, `state_list`, `city_list`). **Both** `job_applications.company_id as other_company` (line 94) **and** `add_job_wise_condidates.thirdparty_company_id as ClientComID` (line 147) are selected — the view renders both as `apl_company_name`, `post_company_name`, `cur_company_name`.

**Cross-tenant gap:** the `show`/`clientshow`/`public_show` methods that drive this code path have no `company_id` filter at the controller layer. Any Service-tenant user with a valid id gets the full payload. The Blade view (`service_company.apptracker.showdetail`) does not mask any of the PII fields.

**Mask that *should* exist:** the view should respect the same `$rcvMaskContact` boundary that `bench-candidates/showdetail.blade.php:809` implements. When `auth()->user()->company_id !== job_application.thirdparty_company_id` AND `auth()->user()->company_id !== job_application.company_id`, mask: full_name (keep first name + initial), email, phone (mobile + secondary), dob, photo, secondary_email, skype_id, social links (linkedin/github/behance), resume download URL, CTC/ECTC/pay_rate/bill_rate, proposed_ctc, offer_amount.

### 5.2 `bench_applications`

**Schema tenancy fields:** `company_id` (bench-owner agency). Indexes: only `job_applications_company_id_foreign` (the name is wrong — this is the bench table's company_id).

**Canonical read path:** `ServiceCompanyBenchCandidates::show($id)` (line 875) — `JobApplication::...->where('job_applications.id', $id)->first()` (971). **No `where('company_id', ...)`**.

**Cross-tenant gap:** the only mask in the codebase is `bench-candidates/showdetail.blade.php:809` — `$rcvMaskContact = ((int) @$benchApplication->company_id !== (int) (auth()->user()->company_id ?? 0))`. This is correct, but it only fires inside the `if(!empty($benchApplication->resume) && in_array(...))` block at line 774. The view header (lines 1-770) renders name, designation, full address, CTC, bill rate, pay rate, photo, employee_number, and the **owning agency's** company_name, website, company_size, founded, company_type (lines 5889-5895) **without any mask**.

**Public marketplace read path:** `bench-candidates/public_show` (line 5817) — the partial redaction at 5906-5917 only fires when `previousUrl` contains `find-talents`. Otherwise full PII is returned and the **owning agency's identity is leaked** via `companies` join. This is the marketplace's whole point for browsing, but it's a P0 for an id-probing attacker.

**Mask that *should* exist:**
- Header rows 1-770: same `$rcvMaskContact` boundary should apply (currently it does NOT).
- `public_show` redaction should be unconditional on `previousUrl`, not conditional.
- The owning agency's `company_name` should only be exposed if the viewer is the agency itself or the hiring company after placement.

### 5.3 `interview_schedules`

**Schema tenancy fields:** `company_id` (scheduling agency). Index: `idx_comp_sched_status`.

**Canonical read path:** `ServiceCompanyJobApplicationController::show` (line 2624) → at line 3396-3404:

```php
$intRow = DB::table('interview_schedules')
    ->select('*')
    ->where('job_application_id', $application->id)
    ->where('interview_job_id', $application->jobId)
    ->orderBy('interview_schedules.id', 'DESC')
    ->first();
```

**Cross-tenant gap:** the `->where('interview_schedules.company_id', user()->company_id)` filter is **commented out** at line 3399. This is the canonical P0 for `interview_schedules` — the hiring company has no business reading another agency's schedule, and the agency shouldn't read another agency's schedule either. The 30+ read paths into `show`/`clientshow` all reach this query.

**Mask that *should* exist:** add `->where('interview_schedules.company_id', auth()->user()->company_id)` back. For `clientshow` (the hiring-co viewer), a separate tenancy rule applies — the hiring co can read interviews only for its own candidates (`job_application.company_id = self`), not for any agency. This means `clientshow` should ALSO gate by `job_applications.company_id = self`, which it does NOT today.

### 5.4 `commericials` (commercials)

**Schema tenancy fields:** `company_id` (owning agency). Index: `idx_commericials_company_id`.

**Canonical read path:** `ServiceCompanyJobsController::serviceviewmore` (line 14078) — `where('company_id', $company_id)` (14095), **strict**.

**Cross-tenant gap:** **none in the active code**. The public career page reads `commericials` only for the company the candidate is applying to. The agency's `services_view.blade.php` is only rendered for the owning agency (the "Preview for Agencies" button at line 62 renders a *preview*, not a different tenant's view).

**Why this matters for V2:** the `2026-08-14-add-commercial-policy-fields.sql` migration added `replacement_*`, `billing_*`, `payment_terms_days` to `commericials`. These are commercial terms that the hiring company SHOULD see when the agency submits a candidate (to validate fee/replacement policy before approving an offer). Today the hiring company has NO read path to an agency's commercials — even when the agency's candidate is being hired by them. **This is a V2/V3 functional gap, not a leak.** Phase 8 step 4 should add a public-read projection that exposes only the agency's commercials for a placed candidate.

### 5.5 `offer_job_applications`

**Schema tenancy fields:** `company_id` (hiring co). Index: `idx_comp_created_status`.

**Canonical read path:** `ServiceCompanyApplicationOfferController::export`, `update_application`, `update_salary` — all scoped to `auth()->user()->company_id` by the controller's tenancy scope.

**Cross-tenant gap:** **none in the controller**. But the schema does NOT store `thirdparty_company_id` on this table — the agency is reached only via `offer_job_applications.application_id → job_applications.id → job_applications.thirdparty_company_id`. If the offer is moved to a different hiring co, the agency link stays with the original application.

**Mask that *should* exist:** none required — offer letters are private to the issuing hiring co, with public access via `offer_code` URL (the `FrontJobOfferController`).

### 5.6 `users` (team)

**Schema tenancy fields:** `company_id` (owning tenant); `is_superadmin=1` means cross-tenant.

**Canonical read path:** `ServiceCompanyTeamController::index` etc. — scoped by `company_id`. The `users.company_id` index supports this.

**Cross-tenant gap:** **none in the controller**. But the `bench_candidate_skill_experience.candidate_id` join, the `applicant_notes.user_id` join, and the `comments.user_id` join all carry the user's `company_id` through the parent — without an explicit tenancy check, a moved user can leak. Phase 8 step 2.

### 5.7 `coin_orders` / `coin_transection_logs` / `top_up_logs`

**Schema tenancy fields:** `company_id` (buyer); `coin_transection_logs` has BOTH `company_id` and `from_company_id`.

**Canonical read path:** `SuperAdminCompanyController::viewMore` (line 2622) reads `coin_orders`/`company_coins` (line 2695-2720) for a single company — strict.

**Cross-tenant gap:** **none**. `coin_events`, `coin_packages`, `top_up_coin_packages`, `coin_package_types`, `coin_package_points` are GLOBAL by design (catalog tables). `coin_transection_logs` has dual columns but no controller exercises inter-tenant transfers.

### 5.8 `tickets` / `comments`

**Schema tenancy fields:** `tickets.company_id` exists; **`comments` has no `company_id`** — only `ticket_id + user_id`.

**Canonical read path:** `ServiceCompanyAppTrackerController::ticketUpdate`, `SuperAdminCompanyController::ticketUpdateStatus`.

**Cross-tenant gap:** comment tenancy is **inherited from the ticket**. If a ticket is re-assigned across companies (no path exists today, but the schema permits), comments travel with the ticket, not with the company. **Phase 8 step 5** should add `comments.company_id` and backfill from `comments.user_id → users.company_id` at the time of the parent ticket.

### 5.9 `on_board_details` / `on_board_files`

**Schema tenancy fields:** `company_id` (hiring co). FKs: `job_application_id`, `designation_id`, `department_id`, `reports_to_id`.

**Canonical read path:** `ServiceCompanyJobOnboardController::*` — scoped to `auth()->user()->company_id`.

**Cross-tenant gap:** **none in the controller**. The agency is reached via `on_board_details.job_application_id → job_applications.id → job_applications.thirdparty_company_id`. Phase 8 step 6 should ensure the agency sees its placed-candidate onboarding only via that link, never by id.

---

## 6. UI mask audit

> The visibility model is enforced at two layers: (a) the controller `WHERE` clause (which rows are returned) and (b) the Blade view (which fields of those rows are rendered). This section audits the Blade layer.

### 6.1 The ONE existing mask — `bench-candidates/showdetail.blade.php:809`

```php
$rcvMaskContact = ((int) @$benchApplication->company_id !== (int) (auth()->user()->company_id ?? 0));
```

This is the only explicit cross-tenant mask in the entire `resources/views/service_company/` tree. It is passed to `service_company.partials.resume_cv_viewer` (line 811-824) which uses it to redact phone, email, secondary_mobile, dob, photo, social links, and the resume download URL.

**Limitation:** the mask only fires inside the `if(!empty($benchApplication->resume) && in_array(...['pdf','doc','docx']))` block at line 774. The view header (lines 1-770) renders `company_name` (line 409), `current_designation`, full address, `pay_rate_amount`, `bill_rate_amount`, CTC, ECTC, photo, employee_number, and the **owning agency's** company_name, website, company_size, founded, company_type — all **without any mask**.

**Limitation:** the mask is binary (own vs not-own). It does not differentiate between (a) the owning agency, (b) the hiring company that has placed this candidate, (c) any other agency browsing the marketplace. The 3-way distinction matters for `bench-candidates/public_show`.

### 6.2 Views that should have mask logic but don't

| View path | Driver entity | What should be masked | Severity |
|---|---|---|---|
| `resources/views/service_company/apptracker/showdetail.blade.php` (referenced via `service_company.apptracker.clientshow` controller) | `job_applications` | full_name, email, phone, dob, photo, secondary_email, secondary_mobile, skype_id, linkedin, github, behance, otherLink, CTC, ECTC, pay_rate_amount, bill_rate_amount, offer_amount | **P0** |
| `resources/views/service_company/apptracker/benchReqViewMore.blade.php` | `job_applications` (bench candidates inside apptracker) | same as above | **P0** |
| `resources/views/service_company/cvtracker/index.blade.php` | `add_job_wise_condidates` + `job_applications` | CTC, ECTC, pay_rate, bill_rate when the viewer is neither the owning agency nor the hiring co | **P0** |
| `resources/views/service_company/bench-candidates/index.blade.php` | `bench_applications` (via ES `ELASTIC_CANDIDATES_BENCH`) | none on the listing — only on the detail page | P2 |
| `resources/views/service_company/bench-candidates/public_show.blade.php` | `job_applications` | depends on viewer type | **P0** |
| `resources/views/service_company/applicationoffer/index.blade.php` | `offer_job_applications` | full candidate PII when viewer is an agency (not the issuing hiring co) | **P1** |
| `resources/views/service_company/applicationoffer/tracker.blade.php` | `offer_job_applications` + `on_board_details` | salary_offered, joining_date when viewer is not the issuing co | **P1** |
| `resources/views/service_company/job-applications/showdetail.blade.php` | `job_applications` | same as apptracker/showdetail | **P0** |
| `resources/views/service_company/job-onboard/offerListing.blade.php` | `offer_job_applications` | salary_offered, employee_number, sign | **P1** |
| `resources/views/superadmin/companies/view_more.blade.php` (referenced via `SuperAdminCompanyController::viewMore`) | `company_coins`, `coin_orders`, `MeetingSchedule` (global) | none — SuperAdmin by design | n/a |
| `resources/views/superadmin/apptracker/*` | `job_applications` | full PII — SuperAdmin by design | n/a |
| `resources/views/front/job-detail.blade.php` | `Job`, `Company`, `commericials` | none — public by design, but check that commercials are the hiring co's only | P3 |
| `resources/views/front/career-apply.blade.php` | `Job`, `JobApplication` insert | coin balance + candidate count for the applying company — vulnerable to the `?? 2` fallback (§7.6) | **P0** |

### 6.3 Blade view: `services_view.blade.php`

This is the commercials editor. Rendered only by the owning agency (`ServiceCompanyJobsController::serviceviewmore` line 14078). No cross-tenant exposure. The "Preview for Agencies" button (line 62) renders a preview of how the agency's commercials will appear to hiring companies browsing the agency's marketplace profile — it does not actually fetch another tenant's data.

---

## 7. Cross-tenant leak vectors — top 10

> Each row: vector, where, severity, why, fix.

| # | Vector | Where | Severity | Why | Fix |
|---|---|---|---|---|---|
| 1 | `JobApplication::clientshow($id)` has no `company_id` filter and is the route used by hiring companies to read an agency's candidate | `ServiceCompanyJobApplicationController.php:9704-10240` | **P0** | A hiring-co user authenticates with their own `company_id` but reads any agency's `add_job_wise_condidates` row by id. Full PII (email, phone, dob, CTC, resume) returned. | Add `where('add_job_wise_condidates.company_id', auth()->user()->company_id)` AND `orWhere('add_job_wise_condidates.thirdparty_company_id', auth()->user()->company_id)` (the dual-tenancy pattern). Gate the controller by `candidateRead` permission. |
| 2 | `JobApplication::show` → `interview_schedules` query has its `where('interview_schedules.company_id', user()->company_id)` filter **commented out** | `ServiceCompanyJobApplicationController.php:3396-3404` | **P0** | Any Service-company user can fetch any agency's interview schedule by id. | Re-enable the commented-out filter. Add the same `where('job_applications.company_id', self)` AND `orWhere('job_applications.thirdparty_company_id', self)` gate to the outer `show`. |
| 3 | `bench-candidates/public_show/{id}` reads any bench by id with no `company_id` filter. PII redaction is conditional on `previousUrl` containing `find-talents` | `ServiceCompanyBenchCandidatesController.php:5817-5949` | **P0** | When the redaction branch misses (i.e. `previousUrl` is anything other than `find-talents`), full PII returned. The owning agency's company_name, website, company_size, founded, company_type and the candidate's CTC, bill_rate, pay_rate, photo, resume, employee_number are **always** returned unconditionally. | Move the redaction logic out of the `previousUrl` branch. Apply `$rcvMaskContact`-style boundary unconditionally based on `(viewer.company_id, bench.company_id, bench_place.client_company_id)`. |
| 4 | `ServiceCompanyBenchCandidates::show($id)` and `bench_application_status` and `bench_explore` use `JobApplication::...->where('id', $id)->first()` with no `company_id` filter | `ServiceCompanyBenchCandidatesController.php:875, 2391, 4934` | **P0** | The Service-side `show` reads any agency's bench record. The same template (`service_company.bench-candidates.showdetail`) is used — no mask on the header rows. | Add `where('job_applications.company_id', self)` for the owning agency. For marketplace browsing, use a separate `benchBrowse` controller method that explicitly allows read with the `$rcvMaskContact` boundary enforced. |
| 5 | `AppTracker::index($id, $mstatus_id)` does `Job::findOrFail($id)` with no tenant filter | `ServiceCompanyAppTrackerController.php:80-158` | **P1** | A Service user with `view_job_applications` can open any job's apptracker board by guessing job ids. The candidate counters are tenant-scoped, but the job metadata is global. | Add `where('jobs.company_id', self)` AND `orWhereExists(staffing_company_relation)` for jobs shared with this tenant. |
| 6 | `HomeController::jobApply` and `SaasFrontController::jobApply` hard-code `$companyId = (int) ($company_id ?? 2)` (line 1458) and use `JobApplication::where('company_id', $company_id)->count()` (line 1479). The `?? 2` falls back to **company 2** | `HomeController.php:1448-1484`, `SaasFrontController.php:1448-1484` | **P0** | Coin-billing and candidate-count are charged/attributed to company 2 when `company_id` is missing. | Replace `?? 2` with `abort(400, 'company_id required')`. Add a FormRequest validation. |
| 7 | `HomeController::jobDetails($jobId, $company_id)` and `SaasFrontController::jobDetails` query `Job::where('jobs.id', $jobId)` with no `company_id` filter on the job itself | `HomeController.php:1555-1600` | **P1** | A public unauthenticated user can read any active job by id, including jobs owned by tenants that haven't enabled public career pages. | Add `where('jobs.company_id', $company_id)` (the same `company_id` from the URL). Or use `Job::whereHas('companies', fn($q) => $q->where('show_in_frontend', 'true'))`. |
| 8 | `application_event` is globally keyed (no `company_id` column) and is joined into the `updatetimeline` timeline in `show` and `clientshow` | `ServiceCompanyJobApplicationController.php:3559-3574, 9957-9966` | **P2** | Not a leak by itself (the table holds global event-type labels), but it travels inside the `clientshow` payload which is cross-tenant. | No fix needed — `application_event` is a lookup, not a tenant record. Flag as informational. |
| 9 | `SuperAdminCompanyController::viewMore` loads `MeetingSchedule` with **no `where`** at all (line 2672-2678), then `company_coins` and `coin_orders` for the same company | `SuperAdminCompanyController.php:2622-2720` | **P2** | By design for SuperAdmin. The `MeetingSchedule` query is **global** — every meeting between every agency and every client is on this page. | Confirm only the `superadmin` middleware hits this route (verify in `routes/superadmin.php`). If the route is ever exposed to a Service tenant, this is a P0. Add a regression test. |
| 10 | `comments` has no `company_id` column — tenancy is inherited from the parent `tickets.company_id` via `ticket_id` + `user_id`. A user moved across companies takes their comments with them. | `database/schema/mysql-schema.sql:914-970` | **P2** | If a user is moved across companies, comments travel with the user. The `tickets` row stays, but its comments can become orphaned. | Add `comments.company_id` and backfill from `comments.user_id → users.company_id` at the time of insertion. Add a Spatie Observer to keep it in sync. |
| (bonus) | `bench_count_view_download` has no `company_id` — view/download analytics on a bench candidate are recorded with only `user_id` + `candidate_id`. | `database/schema/mysql-schema.sql:226-236` | **P2** | The bench-owner agency cannot reliably attribute a view/download to a specific company; cross-tenant attribution is lost. | Add `bench_count_view_download.company_id` (denormalized from the viewer's `users.company_id` at insert time). Index by `(company_id, candidate_id, type)`. |
| (bonus) | `bench_candidate_skill_experience` has no `company_id` — scoped only via `candidate_id`. If a bench candidate is "shared" across agencies (no path exists today but the schema permits), skills travel with the candidate, not the agency. | `database/schema/mysql-schema.sql:207-221` | **P3** | Cross-tenant skill attribution is fragile. | Add `company_id` and backfill. |

---

## 8. Remediation roadmap — Phase 8 (Mask & Scope)

> Combined ~12 dev-days before 10K-concurrent launch. Each step targets a specific §7 vector or §6 UI gap.

### P8.1. Re-enable the commented-out `interview_schedules` filter (½ dev-day) — Targets §7.2

1. Uncomment `->where('interview_schedules.company_id', user()->company_id)` at `ServiceCompanyJobApplicationController.php:3399`.
2. Add the dual-tenancy outer gate (`where('job_applications.company_id', self)` OR `orWhere('job_applications.thirdparty_company_id', self)`) to the `show` method.
3. Run regression: open `job-applications/show/{id}` from 2 tenants; verify only own-tenant schedules are returned.

### P8.2. Gate `JobApplication::clientshow` by dual tenancy (1 dev-day) — Targets §7.1

1. Add `where('add_job_wise_condidates.company_id', auth()->user()->company_id)` OR `orWhere('add_job_wise_condidates.thirdparty_company_id', auth()->user()->company_id)` to the `AddJobWiseCondidate::find($id)` follow-up query.
2. Add a `candidateRead` permission check at the top of the method.
3. Run regression: open `job-applications/clientshow/{id}` from agency A and hiring co B; verify each sees only the candidates they own.

### P8.3. Move the `bench-candidates/public_show` redaction unconditional (1 dev-day) — Targets §7.3

1. Replace the `previousUrl`-conditional redaction with the `$rcvMaskContact` pattern from `bench-candidates/showdetail.blade.php:809`.
2. Compute the boundary from `(viewer.company_id, bench.company_id, bench_place.client_company_id)` — three states: owning-agency (no mask), placed-hiring-co (limited mask — no salary), marketplace-browse (full mask).
3. Run regression: load `bench-candidates/public_show/{id}` from 3 personas; verify the right fields are masked.

### P8.4. Add `where('company_id', self)` to `bench-candidates/show`, `bench_application_status`, `bench_explore` (1 dev-day) — Targets §7.4

1. Add the filter to the three methods.
2. For the marketplace-browse path, route to a new `benchBrowseShow` method that explicitly allows read with the mask from P8.3.
3. Run regression.

### P8.5. Fix `HomeController::jobApply` `?? 2` fallback (½ dev-day) — Targets §7.6

1. Replace `?? 2` with `abort(400, 'company_id required')` at `HomeController.php:1458` and `SaasFrontController.php:1458`.
2. Add a FormRequest validation for the public apply endpoint.
3. Run regression: hit `jobApply` with a missing `company_id`; verify 400 not 200.

### P8.6. Fix `HomeController::jobDetails` missing `company_id` (½ dev-day) — Targets §7.7

1. Add `where('jobs.company_id', $company_id)` at `HomeController.php:1495-1501` and `:1579-1600`.
2. Verify the public-facing `Company::where('show_in_frontend', 'true')` lookup still works.
3. Run regression.

### P8.7. Add `company_id` to `comments` and `bench_count_view_download` (2 dev-days) — Targets §7.10 + bonus

1. Add the columns + indexes via a schema migration.
2. Backfill from `comments.user_id → users.company_id` and from `bench_count_view_download.user_id → users.company_id`.
3. Add an Observer on `users` to re-stamp `comments.company_id` when a user moves companies (or, more correctly, prevent the move — see P8.10).

### P8.8. Extend the `$rcvMaskContact` pattern to 12 sibling views (3 dev-days) — Targets §6.2

1. Extract `$rcvMaskContact` into a Blade directive `@maskBoundary($entity, $viewer, $placement)` so all 12 views use the same boundary logic.
2. Apply to: apptracker showdetail, benchReqViewMore, cvtracker index, bench-candidates index, bench-candidates/public_show, applicationoffer index, applicationoffer/tracker, job-applications/showdetail, job-onboard/offerListing, superadmin/companies/view_more (SuperAdmin by design — no mask), superadmin/apptracker/* (SuperAdmin by design — no mask), front/career-apply (validation only).
3. Run regression: open every page from 2 personas; verify the masked fields are redacted.

### P8.9. Add a `CandidateVisibilityPolicy` and unit tests (1 dev-day) — Targets §3, §6

1. Implement `App\Policies\CandidateVisibilityPolicy` with three methods: `view(viewer, candidate)`, `viewContact(viewer, candidate)`, `viewCommercial(viewer, candidate)`.
2. Cover all 3 personas × 5 candidate states (own agency, other agency, hiring co not yet placed, hiring co after placement, public) × 3 visibility layers (header rows, contact fields, commercial terms).
3. Run `php artisan test --filter=CandidateVisibilityPolicy`.

### P8.10. Lock user-move-across-companies + add `RoleObserver` cache invalidation (1 dev-day) — Targets §7.10

1. Block the `users.company_id` update path (only SuperAdmin can move a user; requires a separate `userMove` form).
2. On the rare move, walk `comments.user_id = old_user_id` and re-stamp `comments.company_id`.
3. Fire `UserMovedAcrossCompanies` event for downstream listeners.

### P8.11. Add the V2 commercial-policy public read projection (1 dev-day) — Targets §5.4

1. Add `App\Http\Resources\CommericialPublicResource` that exposes only the agency's commercials for a placed candidate (no PII, only rateType/commtype/status/sal_mode/salary_currency/replacement_*/billing_*/payment_terms_days).
2. Add a route `GET /api/v1/job-applications/{id}/commericial` returning this resource.
3. Add to the offer tracker UI so the hiring co can validate fee/replacement policy before approving.

### P8.12. Test the cross-tenant invariants (1 dev-day) — Targets §3

1. Add a `tests/Feature/CrossTenantVisibilityTest.php` suite that creates 2 tenants (Agency A, Hiring Co B) and verifies:
   - Agency A can read its own candidates (✅), cannot read Agency C's candidates (❌ masked), cannot read Agency B's commercials (❌ hidden).
   - Hiring Co B can read candidates submitted by Agency A against its own jobs (✅), cannot read candidates submitted by Agency A against Hiring Co D's jobs (❌ masked), cannot read any agency's commercials without a placement (❌ hidden).
   - Super Admin can read everything (✅, by design).
   - Front public user can read only `show_in_frontend=true` rows (✅), cannot read PII fields (✅ masked).

### Combined effort

| Phase step | Effort | Cumulative |
|---|---:|---:|
| P8.1 — re-enable interview_schedules filter | 0.5 dev-day | 0.5 |
| P8.2 — gate `JobApplication::clientshow` | 1 dev-day | 1.5 |
| P8.3 — unconditional `public_show` redaction | 1 dev-day | 2.5 |
| P8.4 — bench show/status/explore filter | 1 dev-day | 3.5 |
| P8.5 — fix `?? 2` fallback | 0.5 dev-day | 4 |
| P8.6 — fix `jobDetails` missing `company_id` | 0.5 dev-day | 4.5 |
| P8.7 — schema migration for `comments` + `bench_count_view_download` | 2 dev-days | 6.5 |
| P8.8 — extend `$rcvMaskContact` directive | 3 dev-days | 9.5 |
| P8.9 — `CandidateVisibilityPolicy` + unit tests | 1 dev-day | 10.5 |
| P8.10 — user-move lock + `RoleObserver` | 1 dev-day | 11.5 |
| P8.11 — public commercial read projection | 1 dev-day | 12.5 |
| P8.12 — cross-tenant invariant tests | 1 dev-day | 13.5 |

**Total: ~13.5 dev-days** before the system can claim cross-tenant visibility is closed for the 10K launch.

---

## 9. Related materials

- [PERSONA_SCOPE.md](../PERSONA_SCOPE.md) — scope rule this audit follows.
- [AI_RULES.md](../AI_RULES.md) — rule 3 (tenancy), rule 7 (preserved typos `condidate`, `commericial`), rule 11 (vertical slices).
- [Add-Edit-Submit-Audit.md](Add-Edit-Submit-Audit.md) — narrow CRUD audit, hotspots H1–H10, Phases 1–6.
- [Add-Edit-Submit-Audit_Transitions.md](Add-Edit-Submit-Audit_Transitions.md) — transition audit, hotspots T1–T7, Phase 7.
- [SECURITY.md](../SECURITY.md) — webhook signature, XSS (BUG-04/SEC-03), tenant leak (BUG-03/SEC-02).
- [BUG_REPORT.md](../BUG_REPORT.md) — `JobApplication` tenant scope commented out (BUG-03), 1,227 unescaped `{!! !!}` (BUG-04).
- [MODULE_MAP.md](../MODULE_MAP.md) — file-to-module ownership.
- [DB_SCHEMA.md](../DB_SCHEMA.md) — schema overview; this audit supersedes it on tenancy.
- [database/schema/mysql-schema.sql](../../database/schema/mysql-schema.sql) — 217-table source of truth (read first per AI_RULES rule 2).
- [database/schema/2026-08-14-add-commercial-policy-fields.sql](../../database/schema/2026-08-14-add-commercial-policy-fields.sql) — the latest migration adding replacement/billing/payment fields to `commericials`.
- `database/schema/candidates_view` (line 487 in mysql-schema.sql) — the denormalized view that joins `add_job_wise_condidates` + `job_applications` + `companies` + `users` + `recommendation_relation` + `clients` + `master_status` + `qualification_master` + `city_list` + `state_list` + `countries`. **This is the public read-shape that drives 30+ controllers** — every PII field on it must be gated before any cross-tenant view.