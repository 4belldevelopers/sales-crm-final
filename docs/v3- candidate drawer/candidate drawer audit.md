# Candidate Drawer Audit

## Scope

- Page: `service_company/jobs/market-jobs`
- Drawer reviewed: Submit Candidate / Bench Talents drawer opened from Market Jobs
- Primary files reviewed:
  - `resources/views/service_company/jobs/market-jobs.blade.php`
  - `resources/views/service_company/cvtracker/bench-talents.blade.php`
  - `resources/views/service_company/ajax_pages/bench-talents-ajax.blade.php`
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php`
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php`
  - `app/Helper/GeneralMethod.php`
  - `routes/service.php`
  - `database/schema/mysql-schema.sql`

## Executive Summary

The drawer UI correctly limits the displayed bench candidate list to the logged-in Service Company's bench candidates through the Elasticsearch query in `copybenchtable()`. However, the final submit action is weaker than the list view. It trusts request-provided `jobId` and `conId`, uses state-changing `GET` routes, has duplicate click handlers, and lacks an atomic duplicate guard before inserting into `rec_candidate_assign`.

No runtime code was changed for this audit.

## Findings

### P0 - Submit endpoint trusts candidate and job IDs without ownership validation

Location:
- `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php:6891`
- `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php:6896`
- `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php:6909`
- `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php:6922`

`recommandedrequestcondidate()` loads the target job with `Job::withoutGlobalScopes()->findOrFail($request->jobId)` and reads the candidate using raw `DB::table('job_applications')->where('id', $source_application_id)`. It does not verify that the candidate belongs to `auth()->user()->company_id`, that the candidate is a bench candidate, or that the job is eligible for this market submit flow.

Impact:
- A forged request can submit an arbitrary candidate ID if the user can guess IDs.
- A candidate from another company may be linked to the current user's submission.
- A non-market or otherwise hidden job can be targeted because global scopes are bypassed.

Recommended fix:
- Validate `conId` against `job_applications.company_id = auth()->user()->company_id`.
- Validate candidate type/status for bench eligibility.
- Validate `jobId` against the same market visibility rules used by the Market Jobs list.
- Return a clear JSON error if either record is not allowed.

### P1 - One click can trigger two submit handlers

Location:
- `resources/views/service_company/jobs/market-jobs.blade.php:2480`
- `resources/views/service_company/cvtracker/bench-talents.blade.php:1048`

The Market Jobs page binds a global `.request-send` click handler. The injected Bench Talents drawer also binds another `.request-send` handler. When the drawer is injected into the Market Jobs page, the same click can be handled twice.

The Market Jobs handler submits immediately. The drawer handler opens a confirmation dialog and can submit again after confirmation.

Impact:
- Candidate request can be inserted before the user confirms.
- Candidate request can be inserted twice.
- Coin deduction and Elasticsearch indexing can be triggered more than once.
- User feedback can become inconsistent.

Recommended fix:
- Keep one owner for `.request-send`, preferably the parent Market Jobs page or a single namespaced drawer script.
- Remove the duplicate injected handler or bind it with `.off('click.candidateDrawer').on('click.candidateDrawer', ...)`.
- Ensure the chosen handler always shows confirmation before submitting.

### P1 - Duplicate request protection is UI-only, not atomic

Location:
- `resources/views/service_company/ajax_pages/bench-talents-ajax.blade.php:292`
- `app/Helper/GeneralMethod.php:13202`
- `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php:6940`
- `database/schema/mysql-schema.sql:3731`

The drawer uses `GeneralMethod::benchRequestCheck($row->id, $currentJobId)` to hide the "Send Bench Request" button after a request exists. This is useful for UI refresh, but the actual insert path does not repeat the same check before writing.

The schema for `rec_candidate_assign` has only a primary key and no unique constraint across `source_from_company_id`, `source_application_id`, and `source_jobid`.

Impact:
- Double click, retry, multiple tabs, or direct GET requests can create duplicate `rec_candidate_assign` rows.
- Duplicate rows can cause incorrect statuses, duplicate notifications, duplicate ES documents, or duplicate coin events.

Recommended fix:
- Add a server-side duplicate check inside a transaction before insert.
- Prefer a unique database constraint for the natural key if compatible with existing data.
- Return an idempotent success response when the request already exists.

### P2 - State-changing submit action uses GET

Location:
- `routes/service.php:437`
- `resources/views/service_company/jobs/market-jobs.blade.php:2488`
- `resources/views/service_company/cvtracker/bench-talents.blade.php:1072`

`jobs/recommandedrequestcondidate/{jobId}` is a `GET` route, but it inserts into `rec_candidate_assign`, indexes the request into Elasticsearch, invalidates cache, and deducts coins after success.

Impact:
- Browser prefetch, refresh, shared links, or crawlers can trigger writes.
- CSRF protection is weaker than a POST route.
- Side effects are hidden behind a read-style route.

Recommended fix:
- Convert the submit route to `POST`.
- Send `jobId` and `conId` in the request body with CSRF token.
- Keep the old GET route only as a temporary compatibility shim that returns a warning or is removed after callers are updated.

### P2 - Drawer loader can expose job context for any guessed job ID

Location:
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:12989`
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:12995`
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:13046`

`btlisting()` loads a job by ID using `withoutGlobalScopes()` and renders the Bench Talents drawer. The returned candidate list is tenant-scoped to the logged-in company, but the job header/context can be loaded for any guessed job ID unless the job visibility is checked elsewhere.

Impact:
- A Service Company user can potentially load job context that should not be available through the Market Jobs drawer.
- This increases the blast radius of guessed IDs.

Recommended fix:
- Before rendering the drawer, verify the job appears in the current user's allowed Market Jobs visibility set.
- Return `403` or a JSON error when the job is not public/market-visible to the logged-in company.

### P3 - Drawer comment states incorrect tenancy behavior

Location:
- `resources/views/service_company/cvtracker/bench-talents.blade.php:4`
- `resources/views/service_company/cvtracker/bench-talents.blade.php:6`
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:12995`

The drawer Blade comment says `$job` is tenant-scoped from `ServiceCompanyJobsController@btlisting`, but `btlisting()` actually loads the job with `withoutGlobalScopes()`.

Impact:
- Future maintainers may trust a tenancy boundary that does not exist.

Recommended fix:
- Update the comment after the controller enforces market visibility.
- If the controller remains cross-tenant by design, document the explicit visibility check and reason.

## Positive Notes

- `copybenchtable()` filters candidates by `company_id`, `condidate_type = bench_condidate`, and `status = 2`.
- The drawer row rendering checks existing requests with `benchRequestCheck()` and changes the button state after refresh.
- The Market Jobs page button disables the clicked button during AJAX submission, which helps prevent simple same-click repeats in one browser tab.

## Suggested Remediation Order

1. Remove the duplicate `.request-send` handler and keep one confirmation-based submit path.
2. Move submit from GET to POST with CSRF.
3. Add server-side candidate ownership and job market-visibility validation.
4. Add transaction/idempotency protection around `rec_candidate_assign` insert.
5. Update the drawer comment so it documents the real tenancy rule.
6. Run targeted validation: PHP lint for changed files, route list for affected routes, and a manual smoke test of submit, duplicate submit, unauthorized candidate ID, and unauthorized job ID.
