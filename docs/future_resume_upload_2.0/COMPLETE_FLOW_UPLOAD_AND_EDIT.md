# Complete Flow — Resume Upload v2: From File Pick to ATS Row, and the Edit Path

> **Update 2026-07-27 — server-side parser removed.** This document was
> rewritten without `ParseResumeJob`, `ResumeParserService`,
> `resume_parse_results`, `parse_resumes` queue, `ResumeParsed` /
> `ResumeParseStarted` / `ResumeParseProgress` / `ResumeParseFailed`
> Reverb events, `parser_attempts`/`parser_last_error` columns, or any
> "why both parsers" justification. The single parser layer in v2 runs
> in the browser from the local `File` object via `pdf.js` +
> `re_test()`, parallel with the R2 PUT. The candidate row is the only
> canonical record — there is no parser-stage durable table.

> **Intent:** Walk through the **end-to-end lifecycle of a resume** in v2.
> Two journeys, one truth:
>
> 1. **CREATE flow** — recruiter picks a file, sees parser auto-fill
>    (browser-side, parallel with upload), submits, candidate appears
>    in the ATS view, with a "possible duplicate" badge if the Duplicate
>    Ownership Engine sees a fingerprint match.
> 2. **EDIT flow** — recruiter opens an existing candidate, keeps /
>    replaces / removes the resume, submits, row updates atomically.
>
> Both flows respect the 4 invariants (INV-1..4), share the same
> `resume_uploads` table, and never violate Step 20's no-merge contract.

## TL;DR — the parser works parallel with the upload

This is the most-asked question about v2, so the answer is up top:

**There is ONE parser layer running in parallel with the upload (not
sequential, not queued):**

| Where | Runs | When | Cost |
|---|---|---|---|
| **Browser-side** (`pdf.js` + `mammoth` + `re_test()`) | From the local `File` object (0 bytes through the server) | Starts as soon as bytes stream into the `FileReader` (typically ≥50 KB) | 0 bytes through PHP |

The browser-side parser is the **only** parser in v2. Form fields fill
**while the upload progress bar is still moving**. By the time the
upload progress bar hits 100% and `finalize` lands, the form fields
are already populated (or left empty if the user types first). The user
clicks Submit, the candidate row is inserted with the values that are
already on the form.

There is **no** server-side parser, no `parse_resumes` queue, no LLM
call, no `resume_parse_results` row, no Retry-After semantics, no
`parser_fallback_mode`, no `RepairStuckSessionsJob` parser sweep, and
no `extractor_version`. INV-2's `data-parser-populated="false"`
attribute still gates the auto-fill so manual values always win.

---

## Table of Contents

1. [Actors and tables](#actors-and-tables)
2. [CREATE flow — 11 stages](#create-flow--11-stages)
3. [EDIT flow — 4 branches](#edit-flow--4-branches)
4. [Parser, Reverb, and observer pipeline](#parser-reverb-and-observer-pipeline)
5. [Validation, security, and rate-limit checks](#validation-security-and-rate-limit-checks)
6. [Reconciliation, retention, and cleanup](#reconciliation-retention-and-cleanup)
7. [What the ATS view sees](#what-the-ats-view-sees)
8. [End-to-end sequence diagram](#end-to-end-sequence-diagram)

---

## Actors and tables

### Personas
- **Recruiter** — service-company / staffing user. Authenticates via the
  existing session guard; tenant = `Auth::user()->company_id`.
- **Candidate** — the person whose resume was uploaded. Has zero direct
  interaction with v2 (the public career-apply path is v1, deferred).
- **Reverb pod** — websocket broadcaster for status-only progress
  (uploaded/cancelled/failed). There is no parser pod in v2.
- **Scheduler** — single leader pod (per `Schedule::onOneServer()`) running
  cleanup / reconcile.

### Tables involved (and the baked-in typos)
| Table | Purpose | Owned by |
|---|---|---|
| `resume_uploads` | **v2 source of truth** — every file pick = 1 row | upload subsystem |
| ~~`resume_parse_results`~~ | **REMOVED** — v2 has no canonical parser-output row. Parsed fields live on the candidate row + browser memory. | n/a |
| `upload_session_audits` | every state transition + auth context | upload subsystem |
| `documents` | resume file linked to a candidate row (UNIQUE per candidate) | upload subsystem |
| `add_job_wise_condidates` | the candidate row visible in ATS view | upload subsystem + ATS read-path |
| `candidates` | normalized candidate identity (if separate from above) | candidate subsystem |
| `es_index` (`condidate_v1` index — typo preserved) | search index; observer keeps in sync | search subsystem |

**Cross-tenant isolation** is enforced at every read by `WHERE company_id = ?`
on `resume_uploads` + `UNIQUE(uuid, company_id)` structurally prevents probing
across tenants.

---

## CREATE flow — 11 stages

> Browser → server → R2 → parser → ATS row.

### Stage 1 — Recruiter opens "Add Candidate" page

**Route:** `GET /service_company/job-applications/create`

**What runs:**
1. `ServiceCompanyJobApplicationController::create()` (~line 3981)
2. Loads jobs list (for the dropdown) — wrapped in `forCompany($cid)`.
3. Renders `create.blade.php` with:
   - Empty form fields.
   - Empty resume file input.
   - JS modules registered (in order — Metronic, then `resume-presign.js`,
     `resume-upload-progress.js`, `resume-populate-if-empty.js`,
     `resume-broadcast-sync.js`).

**Initializes (browser-side):**
- `Idempotency-Key` closure is empty.
- `localStorage['resume_uuid']` may be set from a prior refresh.
- `BroadcastChannel('resume-sync-{companyId}')` opens.

---

### Stage 2 — Recruiter picks a file

**User action:** clicks the file input, selects `jane_doe.pdf` (4 MB).

**Browser-side (`resume-presign.js`):**
1. Read file bytes; compute `sha256_hex` (sanity check — NOT idem key).
2. **Generate `Idempotency-Key`** via `crypto.randomUUID()` — kept in module
   closure for the duration of this presign flow.
3. **Abort** any in-flight `XHR` / `parser` work from a prior pick.
4. **Bump epoch token** (UI label: "Uploading resume (2 of 3)").
5. **POST** `/service_company/api/resume-sessions` with header
   `Idempotency-Key: <uuid>` and body:
   ```json
   {
     "original_filename": "jane_doe.pdf",
     "file_size_bytes": 4194304,
     "mime_type": "application/pdf",
     "sha256_hex": "abc123..."
   }
   ```

**Validation at server (FormRequest + service-layer):**
- `Idempotency-Key` is a UUID → else **400**.
- `mime_type ∈ {application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document}` → else **422**.
- `file_size_bytes ≤ 6 MB` → else **422**.
- `filename length 1..128` → else **422**.
- `sha256_hex` is recorded but NOT used as idem key; mismatch logged, not 422.

**Rate limit check (Redis fail-open):**
- `throttle:30,1` per user → else **429** `X-RateLimit-Reason=user`.
- `company_throttle:120,1` per company → else **429** `X-RateLimit-Reason=company`.

**Auth guard (tenant):**
- `Auth::user()->company_id` is captured by middleware; never trusted from body.

---

### Stage 3 — Server creates the session row

**Server route:** `POST /service_company/api/resume-sessions` →
`ResumeUploadSessionController::store` →
`ResumeUploadSessionService::createOrPresign($idemKey, $payload)`

**Algorithm (the seam of INV-4):**
```php
// 1. Look up by (company_id, idempotency_key) WHERE status NOT IN terminal
$existing = DB::table('resume_uploads')
    ->where('company_id', $cid)
    ->where('idempotency_key', $idemKey)
    ->whereNotIn('status', ['Failed','Cancelled','Expired','Reconciled'])
    ->orderByDesc('id')
    ->limit(1)
    ->first();

if ($existing) {
    // Replay path (INV-4) — same header, transport retry
    $session = $existing;
    $replayed = true;
} else {
    // New path (INV-1) — different key, always new
    $uuid = (string) Str::uuid();
    $r2TempKey = sprintf('temp/%s/%s/%d/%s-%s.%s',
        now()->year, now()->format('m'), $cid,
        substr($uuid, 0, 12), substr(hash('sha256', random_bytes(7)), 0, 7),
        pathinfo($payload['original_filename'], PATHINFO_EXTENSION));

    $session = DB::table('resume_uploads')->insertGetId([
        'uuid'              => $uuid,
        'company_id'        => $cid,
        'original_filename' => $payload['original_filename'],
        'file_size_bytes'   => $payload['file_size_bytes'],
        'mime_type'         => $payload['mime_type'],
        'sha256_hex'        => $payload['sha256_hex'],
        'idempotency_key'   => $idemKey,
        'status'            => 'Created',
        'r2_temp_key'       => $r2TempKey,
        'expires_at'        => now()->addMinutes(30),
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);
    $replayed = false;

    UploadSessionAudit::record($uuid, 'ActionPresignIssued', [
        'r2_temp_key' => $r2TempKey,
        'actor_ip_hash' => sha256($request->ip() . $dailySalt),
    ]);
}

$presignedUrl = Storage::disk('r2')->temporaryUrl(
    $session->r2_temp_key,
    now()->addMinutes(30),
    ['method' => 'PUT']
);

return [
    'session' => [
        'uuid'       => $session->uuid,
        'company_id' => $cid,
        'expires_at' => $session->expires_at,
        'r2_temp_key'=> $session->r2_temp_key,
    ],
    'upload' => [
        'url'         => $presignedUrl,
        'headers'     => ['x-amz-acl' => 'private'],
        'method'      => 'PUT',
        'expires_at'  => $session->expires_at,
    ],
    'replayed' => $replayed,
];
```

**Response 201 / 200:**
```json
{
  "session": { "uuid": "...", "company_id": 7, "expires_at": "...", "r2_temp_key": "..." },
  "upload":  { "url": "https://r2.cloudflarestorage.com/...", "method": "PUT", "expires_at": "..." },
  "replayed": false
}
```

**Cross-pod race on a cold key:** two pods concurrently INSERTing on the
same `(company_id, idempotency_key)` produce two rows. The second pod's
lookup returns the first pod's row (ADR-009 INSERT-then-SELECT); both rows
are reconciled within 24 h by `ReconcileOrphanUploadsJob` (orphan rows
that never reach `Uploaded` are reaped).

---

### Stage 4 — Browser PUTs the file directly to R2 **while running the browser-side parser in parallel**

> This is the only parsing stage in v2. There is **one** parser layer,
> running in the browser from the local `File` object via
> `pdf.js` + `mammoth.js` + `re_test()`. It starts as soon as bytes
> stream into the `FileReader` (typically at ≥50 KB; see v1 brief:
> *"PARALLEL: parse starts when bytes ≥ 50 KB in stream"*). The parser
> is the v1 brief's original UX — no LLM call, no server round-trip.

**Browser-side, two paths running concurrently (`resume-upload-progress.js`
+ `resume-populate-if-empty.js`):**
1. Hide the native file input.
2. Show progress bar + cancel button + ETA.
3. **Start two paths simultaneously:**
   - **Path A — R2 upload**: PUT file bytes directly to the presigned URL.
   - **Path B — Browser-side parse**: stream bytes into `pdf.js` (PDF) /
     `mammoth.js` (DOCX) / `re_test()` regex extractors. On ≥50 KB streamed
     (or on the first `FileReader.onload` chunk) → emit `parseInline` event
     with extracted fields. Zero network, instant.
4. As `parseInline` fires, run `resume-populate-if-empty.js`: for each input
   with `data-parser-populated="false"` AND empty value, assign the parser's
   value. The UI sees form fields auto-fill **while the upload progress bar
   is still moving**.
5. Path A: emit progress events 0% → 100% via `BroadcastChannel`
   (same-browser tabs receive them zero-server).
6. Path A: on ≥5 MB files, switch to **multipart upload** — initiate,
   upload parts in parallel, complete multipart, then PUT completes.
7. **Cancel button**: calls `DELETE /service_company/api/resume-sessions/{uuid}`.

**R2 state (Path A):**
- Object exists at `temp/{year}/{month}/{cid}/{uuid-prefix}-{h7}.pdf`.
- Lifecycle rule will auto-expire after 30 days if the object is never
  promoted to `final/`.

**Browser parser state (Path B):**
- The form fields are populated with parsed values.
- `data-parser-populated="false"` is flipped to `"true"` on inputs the parser
  filled.
- The user can now type over them; INV-2 (manual values always win) is
  preserved for submit.

---

### Stage 5 — Browser calls finalize (no server-side parser dispatch)

**User action:** progress bar hits 100%; JS auto-POSTs
`/service_company/api/resume-sessions/{uuid}/finalize` (no user click).

**Server-side algorithm (`ResumeUploadSessionService::finalize`):**
```php
// 1. Verify session in tenant scope
$session = ResumeUploadSession::byUuid($uuid)->forCompany($cid)->firstOrFail();

// 2. Conditional UPDATE: Created/Uploading → Uploaded
$updated = DB::table('resume_uploads')
    ->where('id', $session->id)
    ->whereIn('status', ['Created','Uploading'])
    ->update([
        'status' => 'Uploaded',
        'upload_completed_at' => now(),
        'updated_at' => now(),
    ]);
abort_unless($updated, 409, 'RESUME_ALREADY_FINALIZED');

// 3. HEAD-check R2 (proves object exists with the right size)
$exists = Storage::disk('r2')->exists($session->r2_temp_key);
abort_unless($exists, 422, 'RESUME_NOT_IN_R2');

// 4. Audit row
UploadSessionAudit::record($uuid, 'ActionFinalizeCompleted', [
    'upload_completed_at' => now(),
]);

// 5. NO ParseResumeJob::dispatch() here — there is no server-side
//    parser in v2. The browser-side pdf.js + re_test() in Stage 4
//    has already populated the form fields while the file streamed
//    to R2. By the time finalize lands, parsing is done (or the
//    form is empty, which INV-2 handles fine).

// 6. Emit Reverb event for same-tab UI sync (status only — no payload)
broadcast(new ResumeUploaded($session))->toOthers();
```

**Idempotent:** A second `finalize` POST hits zero rows in step 2 → 409
but the session is already `Uploaded`. The client treats 409 as success.

**Response 200:**
```json
{ "status": "Uploaded", "upload_completed_at": "...", "uuid": "..." }
```

**Renumbered note:** what used to be Stage 6 (server-side parser job)
and Stage 7 (server-side parser refine) is GONE. Stage 4's Pipeline B
(client-side parse) is the only parser. Submit (now Stage 6) sees the
already-populated form, locks the session, inserts the candidate row.

---

### Stage 6 (was Stage 8) — Recruiter clicks Submit

**User action:** clicks "Add Candidate" submit button.

**Browser-side preflight:**
1. GET `/api/resume-preflight` — server reachability check (returns 200 +
   server time).
2. Verify session status via GET `/api/resume-sessions/{uuid}`.
3. If `status=Failed`: show modal "Parser failed; manual fields survive;
   submit anyway?"
4. Disable button + show loader + apply CSRF-double-submit guard.

**Submit POST:** `POST /candidates` (or
`POST /service_company/job-applications` — the actual route).

**Server-side algorithm (`ServiceCompanyJobApplicationController::store`
+ `JobApplicationCandidateStoreAction`):**

```php
DB::transaction(function () use ($request) {

    // 1. Verify session state (INV-3)
    $session = ResumeUploadSession::byUuid($request->input('session_uuid'))
        ->forCompany(auth()->user()->company_id)
        ->lockForUpdate()
        ->first();

    // 2. Race-safe: if already linked, replay (INV-3 disambiguation)
    if ($session->linked_candidate_id) {
        return response()->json([
            'status' => 'ok',
            'note'   => 'session_replay_winner_found',
            'candidate_id' => $session->linked_candidate_id,
        ], 200);
    }

    abort_unless(
        in_array($session->status, ['Uploaded'], true),
        422,
        'RESUME_SESSION_NOT_READY'
    );

    // 3. Validate FormRequest (StoreJobApplication rules)
    $data = $request->validated();

    // 4. Insert candidate row (add_job_wise_condidates — typo preserved)
    $candidateId = DB::table('add_job_wise_condidates')->insertGetId([
        'company_id'     => auth()->user()->company_id,
        'job_id'         => $data['job_id'],
        'name'           => $data['name'],
        'email'          => $data['email'],
        'phone'          => $data['phone'],
        'canonical_resume' => $session->canonical_url,
        'status'         => 'Active',
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);

    // 5. Insert documents row (UNIQUE on documentable_id+type+name+company_id)
    DB::table('documents')->insertOrIgnore([
        'company_id'        => auth()->user()->company_id,
        'documentable_id'   => $candidateId,
        'documentable_type' => AddJobWiseCondidate::class,
        'name'              => 'Resume',
        'hashname'          => $session->canonical_url,
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);

    // 6. Conditional UPDATE: link session → candidate (terminal link state)
    DB::table('resume_uploads')
        ->where('id', $session->id)
        ->whereIn('status', ['Uploaded'])
        ->update([
            'linked_candidate_id'   => $candidateId,
            'linked_application_id' => $candidateId,
            'linked_at'             => now(),
            'status'                => 'Completed',
        ]);

    // 7. Audit row
    UploadSessionAudit::record($session->uuid, 'ActionSubmitLinked', [
        'candidate_id' => $candidateId,
        'documents_id' => $documentsId,
    ]);

    // 8. Move R2 object: temp/ → final/ (best-effort, idempotent)
    Storage::disk('r2')->copy(
        $session->r2_temp_key,
        str_replace('temp/', 'final/', $session->r2_temp_key)
    );

    return $candidateId;
});

// 9. AFTER commit: dispatch ES reindex (queue: bulkelasticindex)
ElasticIndexCandidateJob::dispatch($candidateId)->onQueue('bulkelasticindex');
```

**Note — no-merge contract (Step 20):** the engine NEVER merges candidates.
If two recruiters uploaded the same file, both have already inserted
candidates; both persist forever. The engine observes and badges them.

---

### Stage 7 (was Stage 9) — Observer syncs Elasticsearch

**Trigger:** `CandidateObserver` (or ES sync job dispatched after the
transaction commits).

**Index:** `condidate_v1` (typo preserved per `mysql-schema.sql`) alias
`candidates-{company_id}-v2`.

**What runs:**
```php
public function created($candidate): void
{
    ElasticIndexCandidateJob::dispatch($candidate->id)
        ->onQueue('bulkelasticindex');
}
```

**Failure handling:** retries 5× with exponential backoff; on terminal
fail, audit row `ActionElasticIndexFailed`; ops follow-up.

---

### Stage 8 (was Stage 10) — Duplicate Ownership Engine observes (no write)

> **Out of the upload subsystem's code path.** Engine runs as a separate
> scheduled job / event listener.

**What it does:**
1. Read all candidates for `company_id = ?`.
2. Compute fingerprint per candidate (normalized name + email + phone).
3. Detect pairwise matches within the same tenant.
4. Compute badge status.
5. **Write only to `candidate_duplicate_flags`** (a separate engine-owned
   table, NOT to `candidates` / `documents` / `resume_uploads`).

**What it does NOT do:**
- DELETE any candidate row.
- UPDATE any candidate row.
- SOFT-DELETE any `documents` row.
- Combine two `resume_uploads` rows.
- Move `linked_candidate_id` from one candidate to another.
- Touch coin balances, audit logs, or financial state.

**Test coverage (engine repo, not upload repo):**
- `DuplicateOwnershipEngineTest::test_two_candidates_same_fingerprint_both_get_badge`
- `DuplicateOwnershipEngineTest::test_engine_does_not_cross_tenants_by_default`
- `DuplicateOwnershipEngineTest::test_engine_read_only_under_load`
- `DuplicateOwnershipEngineTest::test_engine_does_not_delete_candidates`
- `DuplicateOwnershipEngineTest::test_engine_does_not_touch_resume_uploads`

---

### Stage 9 (was Stage 11) — ATS view loads with the candidate row

**Route:** `GET /service_company/candidates` (or equivalent list page).

**Read query (Eloquent, scoped):**
```php
$candidates = AddJobWiseCondidate::query()
    ->forCompany(auth()->user()->company_id)  // global scope
    ->with(['documents', 'duplicateFlags'])    // eager load
    ->paginate(50);
```

**Display:**
- `Name`, `Email`, `Phone`, `Applied For (job title)`, `Resume link`,
  `Status: Active`, **`Possible duplicate` badge** (if engine flagged).
- Resume link opens the candidate's R2 object via signed URL.

**Index `condidate_v1`:** search results come from this index, kept in sync
via the observer.

---

## EDIT flow — 4 branches

> The same `add_job_wise_condidates.id` keyed form. The `update()` method
> at line 5389 has a 3-branch resume handler; Step 19 brings it onto the
> same 2-RTT R2-direct flow.

**Route:** `PATCH /service_company/job-applications/{id}`
**Controller:** `ServiceCompanyJobApplicationController::update()` (line 5389)
**Feature flag:** `features.edit_page_v2` → `RESUMES_FEATURE_EDIT_PAGE_V2`

### Pre-flight — same guards as Create

1. Captain-or-submitter auth check (current lines 5401-5422).
2. Feature-flag guard: if the request carries `session_uuid` or
   `remove_existing_resume=1` and flag is OFF → return `flag_off` error.

### Branch A — Replace resume

**Trigger:** recruiter clicks "Replace resume" → file input → picks new file
→ 2-RTT presign completes → `session_uuid` populated in form → Submit.

**Algorithm:**
```php
DB::transaction(function () use ($id, $request) {

    // 1. Scalar UPDATEs (existing lines 5478-5890 minus resume branch)
    $jobApp = JobApplication::findOrFail($id);
    $jobApp->fill($request->only(['name','email','phone',...]));
    $jobApp->save();

    if ($request->filled('session_uuid')) {
        $sessionUuid = $request->input('session_uuid');

        // 2. Verify session in tenant scope, lock for update
        $session = ResumeUploadSession::byUuid($sessionUuid)
            ->forCompany(auth()->user()->company_id)
            ->lockForUpdate()
            ->firstOrFail();

        abort_unless(
            in_array($session->status, ['Uploaded','Pending'], true),
            422,
            'RESUME_SESSION_NOT_UPLOADED'
        );

        // 3. HEAD-check R2
        abort_unless(
            Storage::disk('r2')->exists($session->object_key),
            422,
            'RESUME_NOT_IN_R2'
        );

        // 4. Conditional UPDATE session → Linked (race-safe across N pods)
        $linked = DB::table('resume_uploads')
            ->where('id', $session->id)
            ->whereIn('status', ['Uploaded','Pending'])
            ->update([
                'status' => 'Linked',
                'linked_application_id' => $id,
                'linked_at' => now(),
            ]);

        abort_unless($linked, 422, 'RESUME_SESSION_ALREADY_LINKED');

        // 5. UPDATE-in-place the existing Resume documents row
        //    (UNIQUE on company_id+documentable_id+documentable_type+name
        //    means we cannot INSERT a second Resume row for the same
        //    JobApplication — we UPDATE the existing one's hashname).
        $newHashname = $session->canonical_url ?? $session->presigned_url_path;

        $docUpdated = DB::table('documents')
            ->where('documentable_id', $id)
            ->where('documentable_type', JobApplication::class)
            ->where('company_id', auth()->user()->company_id)
            ->where('name', 'Resume')
            ->update([
                'hashname'   => $newHashname,
                'updated_at' => now(),
            ]);

        // 6. If no Resume row existed yet (edge case), INSERT one
        if (! $docUpdated) {
            DB::table('documents')->insert([
                'company_id'        => auth()->user()->company_id,
                'documentable_id'   => $id,
                'documentable_type' => JobApplication::class,
                'name'              => 'Resume',
                'hashname'          => $newHashname,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        // 7. Rewrite canonical resume column on JobApplication
        $jobApp->resume = $newHashname;
        $jobApp->save();
    }
});
```

**Why UPDATE-in-place not INSERT+DELETE?** The `documents` table has
`UNIQUE KEY (company_id, documentable_id, documentable_type, name)` —
only ONE Resume row per JobApplication is allowed. UPDATE rewrites the
`hashname`; the old R2 object is reaped by the retention job after 90 d.

**Audit:** `ActionResumeReplaced` with `old_hashname`, `new_hashname`,
`session_uuid`.

### Branch B — Remove resume

**Trigger:** recruiter checks "Remove existing resume" → Submit.

**Algorithm:**
```php
DB::transaction(function () use ($id) {
    JobApplication::where('id', $id)->update(['resume' => '']);

    // No `deleted_at` on documents table — keep the row as audit trail.
    // canonical resume column is now ''.
    // R2 object may be reaped by uploads:reconcile if Linked session
    // has no other referrer.
});
```

**Audit:** `ActionResumeRemoved` with `application_id`, `actor_user_id`.

### Branch C — Keep resume, change scalar fields only

**Trigger:** recruiter changes name/email/phone/notes, leaves resume alone.

**Algorithm:**
```php
// 1. Scalar UPDATE only
$jobApp = JobApplication::findOrFail($id);
$jobApp->fill($request->only(['name','email','phone',...]));
$jobApp->save();

// 2. No session_uuid, no remove_existing_resume — fall through
// 3. documents row untouched
// 4. Fast path: no presign, no R2 round-trip
```

**Audit:** `ActionApplicationUpdated` (scalar-only change).

### Branch D — Cancel the edit (no submit)

**Trigger:** recruiter opens edit, changes nothing, closes tab.

**Behavior:**
- The existing `documents` row is preserved (no change).
- No new `resume_uploads` row was created (no presign POST).
- If a temp row WAS created (recruiter picked a file then closed tab),
  `uploads:cleanup-orphans` reaps it after 24 h.

---

## Parser, Reverb, and observer pipeline

### Parser pipeline (ONE layer — browser only)

| Step | What runs | Where |
|---|---|---|
| 1. Trigger | File picked; `FileReader.onloadstart` or ≥50 KB streamed | browser |
| 2. Library | `pdf.js` (PDF) / `mammoth.js` (DOCX) / `re_test()` regex extractors | browser |
| 3. Extract | Text → name/email/phone/skills/education | browser |
| 4. Emit | `parseInline` event with extracted fields | browser |
| 5. Apply | `resume-populate-if-empty.js` (INV-2 — manual wins) | browser |
| 6. Network | **0 bytes** — runs entirely from the local `File` object | — |

**Sanitization (CLAUDE.md + SECURITY.md):**
- Strip `<>{}` from `raw_text` before stuffing into form inputs.
- Strip emails beyond the FIRST match.
- Strip URLs beyond the FIRST 3.
- Cap raw_text length at 25 KB before regex extraction
  (~6 MB compressed DOCX → ~150 KB raw, then trim).

**Why no server-side parser? (2026-07-27 decision recap.)**
The v2 contract is: **the candidate row is the only canonical record**.
A second, durable parser pipeline would create a side-channel of
"ground truth" that competes with the user-visible form. Browser memory
is bounded but transient — that's sufficient for the form-fill UX. Any
downstream consumer (search, ES, engine fingerprint) reads the candidate
row after submit. There is no LLM call, no queue, no pod — the server
subsystem never sees the resume text.

### Reverb pipeline

| Event | Channel | Auth | Payload |
|---|---|---|---|
| `ResumeUploaded` | `private-resume.{companyId}` | `(int) Auth::user()->company_id === (int) $cid` | `{ uuid, status: "Uploaded", upload_completed_at }` |
| `ResumeCancelled` | same | same | `{ uuid }` |
| `ResumeFailed` | same | same | `{ uuid, last_error }` (only on cross-pod race, not parser-related) |

> **Removed events:** `ResumeParsed`, `ResumeParseStarted`,
> `ResumeParseProgress`, `ResumeParseFailed`. There is no server-side
> parser; there is no parse progress to broadcast.

**Same-browser shortcut:** `BroadcastChannel('resume-sync-{companyId}')`
fires the same event locally first; Reverb is the fallback for
cross-machine sync (recruiter on phone + laptop).

**Auth security:** the `(int)` cast on `company_id` is critical — string
matches are a known bypass vector.

### Observer pipeline

**`CandidateObserver`** (on `add_job_wise_condidates`):

| Trigger | Action |
|---|---|
| `created` | `ElasticIndexCandidateJob::dispatch($id)->onQueue('bulkelasticindex')` |
| `updated` (status change, resume replacement) | re-dispatch ES sync |
| `deleted` | re-dispatch ES sync with `deleted=true` |

**Failure handling:** 5× retries with exponential backoff; terminal fail
writes audit `ActionElasticIndexFailed`.

---

## Validation, security, and rate-limit checks

### Per-request validation (server-side)

| Check | Where | Failure |
|---|---|---|
| `Idempotency-Key` is UUID | FormRequest | 400 |
| `mime_type` ∈ {PDF, DOC, DOCX} | FormRequest | 422 |
| `file_size_bytes ≤ 6 MB` | FormRequest | 422 |
| `filename length 1..128` | FormRequest | 422 |
| `sha256_hex` matches file | HEAD-R2 at finalize | 422 `RESUME_NOT_IN_R2` |
| session belongs to tenant | `forCompany()` scope | 404 (no tenant enumeration) |
| session not terminal | status check | 409 (with hint to use new idem key) |

### Rate limits (Redis fail-open)

| Endpoint | Per-user | Per-company | Window |
|---|---|---|---|
| `POST /resume-sessions` | 30/min | 120/min | 60 s |
| `GET /resume-sessions/{uuid}` | 60/min | 240/min | 60 s |
| `POST /resume-sessions/{uuid}/finalize` | 30/min | 120/min | 60 s |
| `DELETE /resume-sessions/{uuid}` | 30/min | 120/min | 60 s |
| `GET /resume-preflight` | 60/min | 600/min | 60 s |
| `POST /candidates` (submit) | 20/min | n/a | 60 s |

If Redis is down: `try/catch` logs `jobs.resume.throttle.fail_open` and
allows the request (degraded mode). Production startup fails loud if
`resumes.metrics_require_redis=true` and `cache.default != redis`.

### Security checks

| Vector | Defense |
|---|---|
| Cross-tenant probe via session_uuid | `UNIQUE(uuid, company_id)` + `WHERE company_id = ?` |
| Cross-tenant Reverb subscribe | `(int)` cast on `company_id` in `Broadcast::channel()` callback |
| Filename injection | `Illuminate\Support\Str::ascii(strip_control($orig))` + length 1..128 |
| Path traversal in R2 keys | UUID + 7-char hash prefix, no user input in path |
| XSS in resume auto-fill | `e()` on all Blade outputs (NEVER `{!! !!}` for user-supplied data) |
| ~~LLM prompt injection~~ | REMOVED — no LLM call in v2 |
| R2 bucket policy | bucket-prefix token binding (Worker presign is v3; ADR-017) |

---

## Reconciliation, retention, and cleanup

### `uploads:cleanup-orphans` — daily at 03:30 (Asia/Kolkata)

**What it does:**
1. Find `resume_uploads` rows where `status='Created'` AND `expires_at < NOW() - INTERVAL 24 HOUR`.
2. For each:
   - Conditional UPDATE → `Reconciled` (status guard prevents re-running).
   - DELETE the R2 temp object (best-effort).
   - Audit `ActionReconcileScored`.
   - Increment `resume_orphan_reconciled_total` (Redis counter).

**Leader election:** `Schedule::onOneServer()->withoutOverlapping(300)` —
only one pod runs; the other pods skip.

### `uploads:reconcile` — weekly Sunday at 04:30

**What it does:**
1. Find rows stuck in non-terminal states for too long.
2. Apply retention policy per status.

### `uploads:repair` — every 15 minutes

**What it does (in v2, server-side-parser-removed build):**
1. Find sessions stuck in `Created`/`Uploading` with `expires_at <
   NOW() - INTERVAL 1 HOUR` and no `upload_completed_at`.
2. Conditional UPDATE → `Reconciled` (status guard prevents re-running).
3. DELETE the R2 temp object (best-effort).
4. Audit `ActionReconcileScored`.
5. Increment `resume_orphan_reconciled_total` (Redis counter).

> The original spec for `uploads:repair` covered parser-stuck-session
> recovery (`status IN ('QueuedForParsing','Parsing') AND
> parser_started_at < …`). Those states no longer exist in v2; the
> job's parser-half is removed and only the orphan-reaper half remains.

### R2 lifecycle rules (bucket policy)

| Prefix | Lifecycle | Reason |
|---|---|---|
| `temp/{year}/{month}/{cid}/...` | 30 days | If never promoted, it's an orphan |
| `final/{year}/{month}/{cid}/...` | none (90-day optional) | Persistent storage for linked resumes |
| After 90 days unreferenced | reaped by retention | Cleanup if candidate archived |

### Audit log retention

| Event | Retention |
|---|---|
| `ActionPresignIssued` | 365 days |
| `ActionFinalizeCompleted` | 365 days |
| ~~`ActionParsed` / `ActionParseFailed`~~ | REMOVED — no server-side parser |
| `ActionSubmitLinked` | 365 days |
| `ActionSubmitReplayWinnerFound` | 365 days |
| `ActionResumeReplaced` / `ActionResumeRemoved` | 365 days |
| `ActionReconcileScored` | 90 days |
| `ActionElasticIndexFailed` | 90 days |

---

## What the ATS view sees

### List page (`/service_company/candidates`)

**Read query:**
```php
$candidates = AddJobWiseCondidate::query()
    ->forCompany(auth()->user()->company_id)
    ->with([
        'documents' => fn($q) => $q->where('name', 'Resume'),
        'duplicateFlags',
    ])
    ->paginate(50);
```

**Columns shown:**

| Column | Source | Notes |
|---|---|---|
| Name | `add_job_wise_condidates.name` | searchable |
| Email | `add_job_wise_condidates.email` | searchable |
| Phone | `add_job_wise_condidates.phone` | searchable |
| Applied For | `add_job_wise_condidates.job_id` → joined | searchable |
| Resume | `documents.hashname` (where name='Resume') | clickable signed URL |
| Status | `add_job_wise_condidates.status` | Active / Archived / Hired |
| Duplicate badge | `candidate_duplicate_flags.flagged = true` | if engine flagged |
| Created at | `add_job_wise_condidates.created_at` | sortable |
| Updated at | `add_job_wise_condidates.updated_at` | sortable |

### Detail page (`/service_company/candidates/{id}`)

- Full candidate data.
- Resume preview (iframe).
- Parsed fields (read-only — `data-parser-populated` display).
- Audit timeline (from `upload_session_audits` for this candidate_id).
- Edit button → EDIT flow Branch A/B/C.

### Search page

ES query → `condidate_v1` index alias `candidates-{company_id}-v2`.

**Filter by `possible_duplicate = true`** → shows only flagged candidates.
**Filter by `has_resume = true`** → shows only candidates with a `documents` row.

---

## End-to-end sequence diagram

### CREATE flow (browser-side parser runs in parallel with R2 PUT)

```
Browser                  Server (Octane)              R2                              ATS View
   │  [client parser]            │                       │                                │
   │   ║                          │                       │                                │
   │   ╠═══════════════════════════════════════════════════════════════════════════════════►│
   │   ║  [R2 upload PUT]          │                       │                                │
   │   ║                          │                       │                                │
   │  GET /create                 │                       │                                │
   │ ──────────────────────────► │                       │                                │
   │  200 + form + JS             │                       │                                │
   │ ◄────────────────────────── │                       │                                │
   │                             │                       │                                │
   │  (user picks file)          │                       │                                │
   │                             │                       │                                │
   │  POST /resume-sessions      │                       │                                │
   │  Idempotency-Key: uuid      │                       │                                │
   │ ──────────────────────────► │                       │                                │
   │                             │ INSERT resume_uploads │                                │
   │                             │ (Created, r2_temp_key)│                                │
   │  201 { session, upload }    │                       │                                │
   │ ◄────────────────────────── │                       │                                │
   │                             │                       │                                │
   │ ╔══════════════════════════════════════════════════════════════════════════════════════╗ │
   │ ║ START TWO PARALLEL PATHS (both browser-side):                                         ║ │
   │ ║                                                                                        ║ │
   │ ║ Path A — R2 PUT (network)                                                              ║ │
   │ ║ Path B — pdf.js + re_test (local File object, 0 bytes through server)                 ║ │
   │ ╚══════════════════════════════════════════════════════════════════════════════════════╝ │
   │                             │                       │                                │
   │  PUT bytes ════════════════════════════════════════►│  (object written)              │
   │  (Path A)                   │                       │                                │
   │                             │                       │                                │
   │  pdf.js extracts ════════►  │                       │                                │
   │  (Path B, no network)       │                       │                                │
   │  parseInline event ─────────────────────────────────────────────────────────────────► │
   │  populate-if-empty.js       │                       │                                │
   │  (form fields fill)         │                       │                                │
   │                             │                       │                                │
   │  BroadcastChannel           │                       │                                │
   │  (same-browser tabs sync)   │                       │                                │
   │                             │                       │                                │
   │  POST /finalize             │                       │                                │
   │ ──────────────────────────► │ HEAD r2_temp_key      │                                │
   │                             │ ─────────────────────►│                                │
   │                             │  200 OK               │                                │
   │                             │ ◄─────────────────────│                                │
   │                             │ UPDATE Created→Uploaded (conditional)                  │
   │                             │ — NO ParseResumeJob dispatch —                          │
   │  200 { Uploaded }           │                       │                                │
   │ ◄────────────────────────── │                       │                                │
   │                             │                       │                                │
   │  (user clicks Submit)       │                       │                                │
   │  POST /candidates           │                       │                                │
   │ ──────────────────────────► │ TX:                   │                                │
   │                             │   verify session      │                                │
   │                             │   INSERT candidate    │                                │
   │                             │   INSERT documents    │                                │
   │                             │   UPDATE session→Completed                           │
   │                             │   audit ActionSubmitLinked                            │
   │                             │ COMMIT                                                │
   │                             │ dispatch ES reindex (bulkelasticindex)                │
   │  201 { candidate_id }       │                       │                                │
   │ ◄────────────────────────── │                       │                                │
   │                             │                       │                                │
   │                             │                       │  (observer fires)              │
   │                             │                       │  ES index updated              │
   │                             │                       │                                │
   │                             │               (Duplicate Ownership Engine observes; never writes) │
   │                             │                       │                                │
   │  GET /candidates            │                       │                                │
   │ ─────────────────────────────────────────────────────────────────────────────────────────►│
   │  200 [candidates, possible_dup badge]                                                   │
   │ ◄─────────────────────────────────────────────────────────────────────────────────────────│
```

**The single-parser parallelism in plain English:**

- **t=0**: file picked → `crypto.randomUUID()` generated
- **t=100ms**: presign POST returns → two paths begin **simultaneously**
  (Path A: R2 PUT, Path B: pdf.js + re_test from local `File`)
- **t=200ms**: pdf.js finishes extracting fields → `parseInline` event →
  form fields fill while upload progress bar still moves
- **t=2-8s** (depends on file size + connection): R2 PUT completes
- **t=8s**: `finalize` POST → conditional UPDATE Created→Uploaded;
  **no parser dispatch**
- **t=anytime**: user clicks Submit → TX inserts candidate row + documents
  row + ES reindex; Duplicate Ownership Engine observes; ATS view updates

### EDIT flow (Branch A — Replace resume)

```
Browser                Server                  R2                Audit
   │                       │                     │                  │
   │  GET /edit/{id}       │                     │                  │
   │ ────────────────────► │                     │                  │
   │  200 { form, current resume preview }        │                  │
   │ ◄──────────────────── │                     │                  │
   │                       │                     │                  │
   │  (user picks new file)│                     │                  │
   │  POST /resume-sessions (2-RTT)              │                  │
   │  Idempotency-Key: uuid│                     │                  │
   │ ────────────────────► │ INSERT row          │                  │
   │  201 { upload url }   │                     │                  │
   │ ◄──────────────────── │                     │                  │
   │  PUT bytes ─────────────────────────────────►│                  │
   │  POST /finalize       │ HEAD ok             │                  │
   │ ────────────────────► │ UPDATE →Uploaded    │                  │
   │                       │ — no ParseResumeJob —                  │
   │  200 { Uploaded }     │                     │                  │
   │ ◄──────────────────── │                     │                  │
   │  (form already filled │                     │                  │
   │   by browser parser)  │                     │                  │
   │                       │                     │                  │
   │  PATCH /job-applications/{id}                │                  │
   │  session_uuid=...     │                     │                  │
   │  scalar fields        │                     │                  │
   │ ────────────────────► │ TX:                 │                  │
   │                       │   find session, lock │                  │
   │                       │   abort unless Uploaded                │
   │                       │   HEAD r2            │                  │
   │                       │   conditional UPDATE session→Linked    │
   │                       │   UPDATE documents.hashname (in place) │
   │                       │   rewrite JobApplication.resume        │
   │                       │   audit ActionResumeReplaced           │
   │                       │ COMMIT                                  │
   │  200 { updated }      │                     │                  │
   │ ◄──────────────────── │                     │                  │
```

---

## Quick reference — what runs where

| Concern | Layer | File (typical) |
|---|---|---|
| Form layout, file input | Blade | `resources/views/service_company/job-applications/create.blade.php` |
| 2-RTT presign client | JS | `resources/js/resume-presign.js` |
| Progress bar, ETA, cancel | JS | `resources/js/resume-upload-progress.js` |
| Auto-fill parser output | JS | `resources/js/resume-populate-if-empty.js` |
| Same-tab sync | JS | `resources/js/resume-broadcast-sync.js` |
| Submit preflight | JS | inline in create.blade.php |
| Presign endpoint | Controller | `app/Http/Controllers/Api/ResumeSessionController.php` |
| Finalize endpoint | Controller | same file |
| Cancel endpoint | Controller | same file |
| Preflight endpoint | Controller | `app/Http/Controllers/Api/ResumePreflightController.php` |
| Health endpoint | Controller | `app/Http/Controllers/Api/ResumeHealthController.php` |
| Create flow | Controller | `ServiceCompanyJobApplicationController::create()` + `store()` |
| Edit flow | Controller | `ServiceCompanyJobApplicationController::edit()` + `update()` |
| Session service | Service | `app/Services/ResumeUploadSessionService.php` |
| ~~Parser service~~ | REMOVED | n/a — no server-side parser |
| Metrics service | Service | `app/Services/ResumeMetrics.php` |
| Log context | Helper | `app/Logging/ResumeLogContext.php` |
| ~~Parser job~~ | REMOVED | n/a — no server-side parser |
| ES reindex job | Job | `app/Jobs/ElasticIndexCandidateJob.php` |
| Reconcile job | Job | `app/Jobs/ReconcileOrphanUploadsJob.php` |
| ~~Repair job (parser-half)~~ | REMOVED | parser-half gone; only the orphan-reaper half remains in `uploads:repair` |
| Reverb events | Event | `app/Events/ResumeUploaded.php` (status only) |
| Reverb auth | Channel | `routes/channels.php` |
| ES observer | Observer | `app/Observers/CandidateObserver.php` |
| Rate-limit middleware | Middleware | `app/Http/Middleware/CompanyThrottleMiddleware.php` |
| Health-token middleware | Middleware | `app/Http/Middleware/InternalTokenMiddleware.php` |
| Request-context middleware | Middleware | `app/Http/Middleware/RequestContextMiddleware.php` |

---

**End of complete flow doc. Pair with [`RESUME_UPLOAD_V2_COMPLETE.md`](RESUME_UPLOAD_V2_COMPLETE.md) for the executive view and the canonical contracts.**