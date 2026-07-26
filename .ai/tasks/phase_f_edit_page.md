# Task Memory — Phase F: Edit Page Resume v2

## Purpose
Bring the EDIT page (`add_job_wise_condidates.id` keyed) onto the same
2-RTT R2-direct upload flow used by create. The existing resume on a row
may be **kept**, **replaced**, or **removed** — all three branches must
be atomic, tenant-scoped, and race-safe across N pods.

Full design: [`docs/future_resume_upload_2.0/00_STEPS_IMPLEMENTATION_PLAN.md`](../../docs/future_resume_upload_2.0/00_STEPS_IMPLEMENTATION_PLAN.md)
**Step 19 — Edit page vertical slice**.

## Where the code lives
- **Controller:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
  - `edit($id)` at line **3981** — DO NOT modify (loads form, no resume logic)
  - `update(Request $request, $id)` at line **5389** — primary edit target
- **Blade:** `resources/views/service_company/job-applications/create.blade.php`
  - Reused for both create AND edit via `$page_event === 'EDIT'` (line 160)
  - Edit-only JS guard on file input handler
- **Model:** `app/Models/JobApplication.php` (uses `documents()` morph relation)
- **Resume session:** `app/Models/ResumeUploadSession.php` (Step 5 model)

## Three branches (the only thing that matters)

| Branch | Trigger | Server action |
|---|---|---|
| **Keep** | User changes scalar fields, no file picked | Existing transaction body runs; `session_uuid` is null; `documents` row untouched |
| **Replace** | User picks a new file, PUTs to R2, submits | Lock session → conditional UPDATE `Linked` → UPDATE-in-place `documents.hashname` → rewrite `job_applications.resume` |
| **Remove** | User checks "Remove existing resume" | `job_applications.resume = ''`; `documents` row stays (audit trail) |

## Critical schema facts (verified against `mysql-schema.sql`)

| Table | Line | Fact |
|---|---|---|
| `documents` | 1331-1342 | **NO `deleted_at` column** — soft-delete impossible without migration |
| `documents` | 1341 | **UNIQUE KEY `(company_id, documentable_id, documentable_type, name)`** — only ONE Resume row per JobApplication |
| `job_applications` | 1936 | HAS `deleted_at` (the application itself can be soft-deleted) |

Because of the UNIQUE constraint on `documents`, Branch A must
**UPDATE-in-place** the existing Resume row's `hashname`, NOT
INSERT-new + tombstone-old. The plan reflects this (Step 19 Branch A,
points 4-5).

## Business rules (verify before changing)
- **Captain-or-submitter auth-guard** at `update()` lines 5401-5422 stays
  first — runs before the transaction opens.
- **Tenancy on `session_uuid`:** always
  `ResumeUploadSession::where('company_id', auth()->user()->company_id)`.
  Cross-tenant guess → 404 (never expose other tenants' session existence).
- **Race-safety:** `lockForUpdate()` on the session row + conditional
  UPDATE on `Linked` (status IN 'Uploaded','Pending'). Loser gets 422
  `RESUME_SESSION_ALREADY_LINKED`.
- **Manual-edit-always-wins** UX rule (carry-over from create): when
  the user replaces a resume, the parser re-runs (Step 9 fan-out) but
  only **populates-if-empty** — fields the user already typed stay
  untouched.
- **Same R2 bucket, same idempotency key formula** as create
  (sha256 of file_bytes + company_id + safe_filename) — replaces
  reuse Step 5's `createOrPresign`.

## Race conditions covered (each is a test in `JobApplicationEditResumeTest`)

| Race | Detection |
|------|-----------|
| Two concurrent edits replace the same resume | Conditional UPDATE on `Linked` returns 0 rows for loser → 422 |
| Replace while previous session still `Pending` | Conditional UPDATE returns 0 rows → 422 |
| Object reaped by `uploads:cleanup-orphans` between PUT and submit | `Storage::disk('r2')->exists()` false → 422 `RESUME_NOT_IN_R2` |
| Cross-tenant `session_uuid` | `where('company_id', …)` returns null → 404 |
| Double-submit (double-click Update) | Conditional UPDATE on `Linked` returns 0 on 2nd call → 422 |
| Captain-not-assigned opens edit | Existing redirect with `candidate_edit_denied` (v1 unchanged) |

## Feature flag

| Config key | Env var | Default | Effect |
|---|---|---|---|
| `features.edit_page_v2` | `RESUMES_FEATURE_EDIT_PAGE_V2` | `false` | When ON: edit form uses 2-RTT presign flow + Branch A/B logic |

OFF = today's v1 multipart upload path. ON = atomic transaction path.
Same rollout pattern as create — staging first, then production.

## Files inventory

### NEW (1 file)
1. `tests/Feature/JobApplicationEditResumeTest.php` — 11 tests covering
   the 3 branches + the 5 race tests + 1 idempotency test + 2 manual-
   edit-always-wins tests.

### MODIFIED (4 files)
1. `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
   — wrap resume branches in `DB::transaction(...)`; add Branch A/B
   inside `update()`. **Do NOT** touch `edit()` (line 3981).
2. `resources/views/service_company/job-applications/create.blade.php`
   — edit-page Blade block (`@if($page_event === 'EDIT')`) +
   `isEditPage` JS guard on the file-input handler.
3. `config/resumes.php` — add `'edit_page_v2' => (bool) env('RESUMES_FEATURE_EDIT_PAGE_V2', false)`.
4. **NO route change** — `PATCH /job-applications/{id}` already exists;
   the same endpoint branches on request shape (`session_uuid` /
   `remove_existing_resume` presence).

## Verification

### Automated
```bash
php artisan test --filter=JobApplicationEditResumeTest   # 11/11
php artisan test --filter=ResumeV2                       # no regressions
bash scripts/ai-guards.sh                                # clean
```

### Manual staging (7-step)
1. Flag ON: `RESUMES_FEATURE_EDIT_PAGE_V2=true php artisan config:cache`
2. Open an existing edit page — verify "View current resume" + "Replace" + "Remove" controls render.
3. Replace flow — pick new file → DevTools shows presign + direct R2 PUT → submit → MySQL: 1 active Resume row, `ResumeUploadSession.status='Linked'`.
4. Remove flow — check "Remove existing resume" → submit → `resume=''`, Resume documents row stays for audit.
5. No-change flow — change phone only, submit → no R2 PUT in network panel.
6. Cross-tenant `session_uuid` — submit with another company's session_uuid → 404.
7. Auth guard — log in as a non-captain non-submitter → redirected with `candidate_edit_denied`.

## Gotchas
- ⚠️ `documents` UNIQUE KEY means you **cannot** add a `deleted_at`
  + INSERT-new + tombstone-old in the same transaction. UPDATE-in-
  place is the only way.
- ⚠️ Edit form reuses `create.blade.php` — any change there affects
  BOTH flows. Guard new code with `@if($page_event === 'EDIT')` (and
  `@if($page_event !== 'EDIT')` on the inverse).
- ⚠️ The parser fan-out (Step 9) re-runs on every file pick on edit
  too. That's intentional: replacing a resume should re-extract
  name/email/phone. The populate-if-empty rule keeps the manual-
  edit-always-wins guarantee.
- ⚠️ `update()` is ~500 lines (entire controller is 500KB+). Grep
  for `function update` before editing — do not read the file whole.
- ⚠️ `add_job_wise_condidates.id` (the `$id` in the URL) is the
  pivot row; `job_applications.id` (the row that gets the resume
  UPDATE) is **different**. The auth-guard resolves pivot → job_app
  through the `job_assign` join at lines 5403-5414.

## Out of scope (deferred)
- A `document_history` audit table — current UPDATE-in-place loses
  the old `hashname`. If compliance demands full history, add
  Step 19.1 with the migration.
- Public career-edit pages — same controller code path; defer until
  Step 17's extraction is complete.
- Multipart chunked upload on EDIT (Step 10 covers CREATE; EDIT
  reuses the same presign endpoint, chunks work for free — no code
  change required).