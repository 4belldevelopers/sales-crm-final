# Database Schema

> ✅ **AUTHORITATIVE SCHEMA NOW AVAILABLE:** the real, exact structure has been exported
> to **[`database/schema/mysql-schema.sql`](../database/schema/mysql-schema.sql)**
> (MySQL 8.0.45, DB `dbmaster`, **217 tables + 1 view**, structure only — no data, no
> secrets). **That file is the source of truth — read it for exact columns/types/indexes.**
> Generated read-only via `SHOW CREATE TABLE`. Regenerate it whenever the live schema
> changes (see [VERIFY.md](VERIFY.md)).
>
> **[CAVEAT — the prose below is still partly [INFERRED]]**
> This document was written *before* the dump existed, so its column lists were deduced
> from models/queries and may be incomplete or slightly off. When they disagree with
> `mysql-schema.sql`, **the SQL file wins.** Schema is still **not** in
> `database/migrations/` (only 3 stock migrations) — do **not** auto-generate migrations;
> see [adr/0005-schema-in-live-db.md](adr/0005-schema-in-live-db.md).

To inspect the real schema: open `database/schema/mysql-schema.sql`, or run
`php artisan db:table <name>` / `SHOW CREATE TABLE <name>;` (see [VERIFY.md](VERIFY.md)).

---

## Tenancy

Almost every business table carries **`company_id`** (the tenant key). Eloquent
queries are auto-filtered by the `company` global scope (`Models/Company.php`), but
**raw `DB::table()` queries are NOT** — filter `company_id` manually.

---

## Core entities & key columns (inferred)

### `companies` (tenant root)
`id`, `name`, `sub_domain` (lowercased), `package_id`, `logo`, `login_background`,
`status` (active/…), `featured`, `featured_start_date`, `featured_end_date`,
`licence_expire_on`, `industries`, `timezone`, `stripe_id`/Cashier columns.
`app_duplicate` (0=None/1=Job-wise/2=Overall System) + `app_duplicate_days` — existing
per-company duplicate-detection settings (`GeneralMethod::checkDuplicateCandidate()`).
`enable_cross_agency_duplicate` (tinyint, default 0) — **added by
`database/migrations/2026_07_19_140013_add_duplicate_detection_columns.php`, pending on
the live DB until that migration runs.** Opt-in for the cross-agency, job-wide
duplicate-ownership arbitration (`GeneralMethod::resolveDuplicateOwnership()` — Company
Recruiter reservation vs. first-agency-to-Sourcing); scope (single job vs. every job this
company owns) is governed by this same company's own `app_duplicate` value, not a
separate setting.
Relations: hasMany `clients`, `application` (JobApplication), `packages`
(CompanyPackage); belongsTo `package`.

### `users`
`id`, `company_id`, `name`, `email`, `password`, `reports_to_id`, `created_by`,
roles via spatie (`model_has_roles`). Relations: belongsTo Company; Spatie roles.

### `jobs` (requisitions) — **NOTE:** collides conceptually with Laravel's queue
`jobs` table; this app uses `custom_queue_jobs` for the queue, so `jobs` here = job
postings.
`id`, `company_id`, `category_id`, `location_id`, `client_id` **[INFERRED]**,
`status`, title/description, skills. Relations: belongsTo `JobCategory`(category_id),
`JobLocation`(location_id); hasMany `JobSkill`(job_id); belongsToMany `Question`
(via `job_questions`); hasMany `JobApplication`.

### `job_applications` (candidate-in-pipeline — **central entity**)
Observed columns (from `ElasticCandidatesIndex`): `id`, `company_id`,
`thirdparty_company_id`, `job_id`, `full_name`, `email`, `phone`, `phone_code`,
`second_phone_code`, `secondary_mobile`, `np_id`, `serving_notice_period_mode`,
`country`, `state`, `city`, `city_id`, `skills`, `photo`, `total_exp`, `rel_exp`,
`ctc`/`ectc` (+ `_cur`,`_mode`,`_hourly`,`_monthly`,`_yearly` variants),
`current_designation`, `current_company`, `bill_rate_currency/amount/mode`,
`condidate_type` (sic), `master_status_id`, `status`, `is_viewed`,
`column_priority`, `resume`, `rating`, `skype_id`, `cover_letter`, `passport`,
`process_id`, `qualification`, `duplicate`.
Relations: belongsTo `Job`(job_id); belongsToMany `Job`; morphMany/morphOne
`Document` (Resume); hasMany `JobApplicationAnswer`.
**SoftDeletes imported but the `company` global scope is commented out** ⚠️.

### `bench_applications` — bench (un-placed/available) candidates. Model `BenchApplication`.
ES mirror: `ats_candidates_bench_elastic`.

### `offer_job_applications` — placement/offer stage. Model `OfferJobApplication`.

### `add_job_wise_condidates` (pivot, sic) — links candidates to jobs with match data.
Columns: `id`, `job_id`, `skill_match`, `existing`, `placed_end_date`,
`rec_candidate_assign_id`. Central to candidate↔job matching & ES candidate indexing.
`duplicate` (tinyint, nullable) — **added by
`database/migrations/2026_07_19_140013_add_duplicate_detection_columns.php`, pending on
the live DB until that migration runs.** Per-job-addition duplicate flag, set when a
candidate is added via Similar Candidates/Internal Bench/Career Page/Agency Bench/Copy-to-
Other-Job — deliberately separate from `job_applications.duplicate`, which is one column
shared across every job the same candidate profile is later added to and would otherwise
bleed a duplicate result across unrelated jobs. Indexed into ES as `wise_duplicate`.

### `documents` (polymorphic) — `documentable_type`, `documentable_id`, `name`
(e.g. "Resume"), `hashname`. Files stored on S3.

---

## Module tables (inferred names, from `DB::table()` usage)

| Domain | Tables |
|---|---|
| Jobs | `jobs`, `job_assign`, `job_questions`, `job_qualifications`, `job_metronics_skills`, `job_application_skills`, `job_skills` **[INFERRED]** |
| Candidates | `job_applications`, `bench_applications`, `add_job_wise_condidates`, `rec_candidate_assign`, `applicant_notes` |
| Clients/Vendor | `clients`, `client_location`, `client_location_jobs`, `client_contact_info`, `client_contact_info_jobs`, `client_city_jobs`, `recommendation_relation`, `staffing_company_relation`, `commericials`, `commericial_details`, `commericial_countries` |
| Interviews | `interview_schedules`, `interview_schedule_employees` **[INFERRED]**, `meeting_schedules`, `meeting_comments`, `schedule_comments` |
| Coins/Billing | `company_coins`, `coin_orders`, `coin_order_events`, `coin_events`, `coin_packages`, `coin_transection_logs` (sic), `packages`, `package_details`, `package_features`, `company_packages`, `orders`, `package_order_items`, `subscriptions`, `stripe_invoices`, `razorpay_invoices`, `razorpay_subscriptions`, `paypal_invoices`, `payment_settings` |
| Mail/Notify | `smtp_settings`, `email_settings`, `email_count_companies`, `sms_settings` |
| RBAC | `roles`, `permissions`, `role_user`, `permission_role`, `model_has_roles` (spatie) |
| Geo/Master | `countries`, `city_list`, `td_industries`, `skills`, `currencies`, `notice_periods`, `boostkeywords` |
| Misc | `bench_assign`, `bench_count_view_download`, `documents`, `onboards`, `onboard_files`, `tickets`, `todo_items`, `sticky_notes`, `reports`, `comments` |

---

## Key relationships (confirmed in models)

```
Company 1──* User
Company 1──* Client
Company 1──* JobApplication
Company 1──* CompanyPackage        Company *──1 Package
Job *──1 JobCategory (category_id)
Job *──1 JobLocation (location_id)
Job 1──* JobSkill (job_id)
Job *──* Question (pivot job_questions)
Job 1──* JobApplication (job_id)
JobApplication *──* Job (pivot)
JobApplication 1──* JobApplicationAnswer
JobApplication 1──* Document (morph: documentable)
InterviewSchedule 1──* InterviewScheduleEmployee
InterviewSchedule *──* User (via InterviewScheduleEmployee)
Onboard 1──* OnboardFiles (on_board_detail_id)
Role *──* User (spatie model_has_roles)
add_job_wise_condidates  ──>  job_applications + jobs (matching pivot)
```

---

## Indexes — **[UNCERTAIN]**

No migration definitions to read indexes from. **Verify against the live DB.**
High-probability hot columns that **should** be indexed (confirm before assuming):
- `job_applications.company_id`, `.job_id`, `.master_status_id`, `.status`, `.email`, `.duplicate`
- `jobs.company_id`, `.client_id`, `.status`
- `add_job_wise_condidates.job_id`, `.id`, `.thirdparty_company_id`
- `timeline.application_id` + `.job_id` + `.master_process_name` (composite) —
  `GeneralMethod::resolveDuplicateOwnership()` does a `MIN(create_at)` lookup filtered on
  exactly these three per candidate/job pair; this table has no indexes listed at all
  today beyond the primary key, so this query currently does a full scan.
- all `*_id` foreign keys, all `company_id` columns

---

## Bottlenecks / hotspots (DB)

1. **`job_applications`** is the busiest table (read on every tracker/dashboard,
   re-indexed to ES on change). Wide row (many CTC/exp/bill-rate columns). Most
   likely N+1 + full-scan source. **[BOTTLENECK]**
2. **Raw `DB::table()` with explicit column lists** scattered in services — easy to
   miss `company_id` filter (tenant-leak risk) and easy to drop an index.
3. **Dual writes** to MySQL + Elasticsearch via observers/jobs — write amplification
   on candidate/job mutations.
4. **No FK/migration enforcement** — referential integrity is application-level only.
5. **`smtp_settings` queried 7×** in mail services — candidate for caching.

See [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md).
