# Documentation TODO

Files/sections that most need inline docs (intent comments, docstrings, query
explanations). Ordered by value-to-AI-and-humans. **This is a recommendation list —
no production code was modified.** Add comments only when working in these files for
another reason (per task constraints), unless explicitly asked to document.

Priority: 🟥 do first · 🟧 valuable · 🟨 nice-to-have.

## 🟥 Search / ranking / matching (highest value — business logic, undocumented)
- `app/Services/ElasticCandidatesIndex.php`
  - Document the candidate document shape: what each ES field means, why those 50+
    `job_applications` columns are selected, and the `add_job_wise_condidates` join.
  - Explain `skill_match` / `existing` / matching semantics.
- `app/Services/ElasticBenchCandidatesIndex.php` — bench doc shape + differences from above.
- `app/Services/ElasticJobIndex.php` / `ElasticJobListIndex.php` — job doc fields & ranking.
- Controller ES **query builders** (in `*CvTrackerController`, `*AppTrackerController`,
  `*BenchCandidatesController`) — document filter/boost/sort logic and pagination.
- `boostkeywords` usage — explain how boosting is applied. **[locate first]**

## 🟥 Raw SQL / data integrity
- Every `DB::table(...)` block in `app/Services/*` — add a one-line intent comment and,
  critically, note whether `company_id` is filtered (tenant safety).
- `app/Models/Company.php::isFeatured()` — explain the date logic; note `DB::raw(DATE())`
  is non-sargable.
- `app/Services/AutoCloseJobsService.php` / `AutoRenewFreePackage.php` — document the
  criteria/thresholds (business rules).

## 🟧 Tenancy & model behavior
- `app/Models/Company.php::booted()` — document the global scope and console bypass.
- `app/Models/JobApplication.php` — **document why the company scope is commented out**
  and how tenant isolation is otherwise guaranteed (or flag as a bug).
- `app/Http/Middleware/Package.php` & `CustomPackage.php` — document the feature-gating
  rules (which plan unlocks what).
- `app/Http/Middleware/EnsureOneDeviceLogin.php` — document session/device policy.

## 🟧 Jobs & sync
- `app/Jobs/UploadResumeToS3.php` (+ Test variants) — document which is canonical and
  the S3 path/hashname scheme.
- `app/Jobs/ProcessCandidateRequest.php` — document the vendor request state machine.
- `app/Observers/*` — each observer: state which ES index it keeps in sync and on which
  events (and any re-entrancy guard).

## 🟧 Integrations
- `app/Services/ZoomService.php` + `app/Traits/ZoomJWT.php` — token lifecycle.
- `app/Services/PhonePeService.php` — request/verify signature contract.
- Webhook controllers (`StripeWebhookController`, `RazorpayWebhookController`,
  `PaypalIPNController`, `PhonePePaymentController`) — document event handling +
  idempotency + signature verification + CSRF exemption.

## 🟨 God controllers (add method-level docblocks as you touch them)
- `HomeController.php`, `ServiceCompanyJobsController.php`,
  `ServiceCompanyJobApplicationController.php`, `SuperAdminCompanyController.php`:
  add a short docblock per public method (purpose, route name, returns). Don't attempt
  in one pass — annotate opportunistically.

## 🟨 Repo-level
- A real `README.md` section pointing to `/docs` and `/.ai`.
- `.env.example` — ensure all `ELASTIC_*` / integration keys are represented (without
  secrets) so new envs are reproducible.
- Mark dead files (`OLD_*`, `oldBackUp/`) with a deprecation header or remove (ask first).
