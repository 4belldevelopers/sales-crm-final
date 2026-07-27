# 30 — Workflow diagram (adapted from v1 brief)

> *Intent: copy the v1 user document's ASCII tree into the v2 design.*

The user-supplied v1 ASCII tree lives in
[`../NEW_ Resume Upload_JEROATS_2026-07-26.md`](NEW_Resume_Upload_JEROATS_2026-07-26.md)
(its source DOCX has been archived). The diagram below is **the same
flow** with v2 contracts annotated at each branch.

```
 Recruiter Opens "Add Candidate"
 │
 ├── Page Loads
 │   │
 │   ├── Initialize resume-upload-progress.js
 │   ├── Initialize resume-populate-if-empty.js
 │   ├── Initialize resume-presign.js        ← v2 split module
 │   ├── Register Offline/Online/Storage listeners  ← v2 keeps
 │   ├── Register Retry Manager
 │   ├── Register Upload Progress Events
 │   ├── Register Parser Progress Events  ← v2: via Reverb
 │   ├── Register Auto-fill Listener  ← v2: never overwrites
 │   ├── Restore localStorage Upload Session (if exists)
 │   ├── Restore Idempotency Key (if exists)
 │   ├── Restore Upload UUID (if exists and validate against GET …/{uuid})
 │   └── UI Ready
 │
 └──────────────────────────────────────────────────────────────
 Recruiter Selects Resume
 │
 ├── SHA256(file_bytes)
 ├── Build idem key = sha256(file + company_id + safe_filename)
 │
 ├── Cache File + Metadata (memory)
 ├── Abort previous XHR/parser if any
 ├── Bump epoch token
 │
 └── POST /service_company/api/resume-sessions
      │ (idempotency: If-Match header) ─────── 409 if any conflict
      │
      ├── Server: ResumeUploadSession::createOrPresign()
      │      ├── look up by (company_id, idem key)
      │      ├── if existing & not expired: reuse UUID, rotate URL only if needed
      │      ├── else: insert new row, sign R2 PUT
      │      └── return uuid + presigned PUT URL + final_key
      │
      ├── Save uuid to localStorage + hidden input
      │
      └── PUT <R2 presigned URL> with bytes  ← v2: leaves PHP
           │
           ├── Progress events 0% → 100%
           ├── TTL: 30 min for session
           ├── ETA + speed + cancel button
           │
           └── PARALLEL: parse starts when bytes ≥ 50 KB in stream
                │
                ├── Validate File
                ├── Extract Type
                ├── Extract Text
                ├── Extract Fields (8)
                ├── Emit parseInline ─► fill empty inputs only
                │
                └── Parser Finished

 PUT Complete
 │
 ├── HEAD <final_key>  ← v2: client verifies before posting finalize
 │     │
 │     └── 404? ↳ mark as failed, show retry modal
 │
 ├── POST /service_company/api/resume-sessions/{uuid}/finalize
 │     │
 │     ├── Server: ResumeUploadSession::markUploaded()
 │     │    ├── conditional UPDATE: status=Created → Uploaded
 │     │    ├── not-found → 404 (idempotent: same uuid = same row)
 │     │    └── emit Reverb ResumeUploaded event
 │     │
 │     ├── Hide Cancel button
 │     ├── Show "Uploaded ✓" badge
 │     ├── Hide progress bar
 │     └── Wait for Submit click

 Submit Flow
 │
 ├── Click Submit
 ├── Disable button + show loader  ← v2 also a CSRF-double + locked button
 │
 ├── Run Preflight Ping  (GET /api/resume-preflight)
 │     │
 │     ├── Server reachable? ─── NO: show "Server unavailable" with ETA counted
 │     ├── Upload Completed?    ─── NO: wait until ready (max 10 s)
 │     ├── Parser Completed?    ─── wait, timeout 30 s
 │     │
 │     └── POST /candidates store
           │
           ├── Validate FormRequest
           ├── Verify Session status = Uploaded
           ├── Verify R2 object still exists (HEAD)
           │
           ├── BEGIN TX
           │    ├── insert/update JobApplication
           │    ├── insert documents row (de-dupe on name=Resume + company_id)
           │    ├── update ResumeUploadSession.linked_candidate_id
           │    ├── UploadSessionAudit::record(action=SubmitLinked)
           │    └── COMMIT
           │
           ├── dispatch ParseResumeJob → queue (parse_resumes)
           │    (unconditional — even if pre-fill was rejected by user)
           │
           └── dispatch elestic_aclone_index_candidate (batched bulk index)

 Network Failure Recovery
 │
 ├── Offline detected
 │     ├── badge "Network Lost"
 │     ├── abort XHR/parser
 │     ├── pause progress
 │     ├── preserve uuid + idempotency key in localStorage
 │     └── wait for browser online event
 │
 └── Online detected
       ├── HEAD <final_key> examine server
       ├── if partial upload: resume with `seek-offset` based PUT
       ├── Re-emit finalize if needed (idempotent)
       └── Continue normally

 Refresh Recovery
 │
 ├── Read localStorage uuid
 ├── GET /service_company/api/resume-sessions/{uuid}
 │     │
 │     ├── 200 + status=Uploaded → resume UI to "Uploaded ✓", no upload
 │     ├── 200 + status=Created  → resume upload from where we left off (use R2 multipart upload)
 │     ├── 200 + status=Failed   → restore "Retry" UI, do not retry automatically
 │     └── 404 (expired/wrong/swept) → start fresh
 │
 └── Continue normally

 Cancel
 │
 ├── DELETE /service_company/api/resume-sessions/{uuid}
 │     │
 │     ├── Server: DELETE the R2 object (best effort), mark Cancelled
 │     └── Reverb: emit ResumeUploabCancelledue later for diagnostics
 │
 ├── Clear localStorage uuid
 ├── Reset progress bar
 ├── Reset state machine
 └── Re-upload readiness

 Submit Network Failure
 │
 ├── Detect network failure on POST /candidates
 │
 ├── Verify upload session still exists (GET /api/resume-sessions/{uuid})
 │     │
 │     ├── session.linked_candidate_id NOT NULL:
 │     │     ├── we already inserted — return cached success
 │     │     └── DO NOT double-submit
 │     │
 │     └── session.linked_candidate_id IS NULL:
 │           ├── re-POST /candidates
 │           └── recruiter so doing
 │
 └── Done

 Submit Success Returned But uiutpleUple X乱T Cher Scring
 │
 ├── Cache `last_candidate_id` in localStoret]
 └── On next add-cantoidtr page load:
       ├── if `last_candidate_id` row still exists → OK
       └── else → cleanup key

 Session Cleanup (shecrutionary)
 │
 ├── Scheduler: cpptphalcle  RecincileOrphanlpploaldss
 │     ├── Flash sessessions where expires_isu and uuload=_id != uine
 │     ├──      status = Recised
 │     ├──      DELETE from R2 temp (best-fert)
 │     ├──      inccement metri_recoriciled_total counter (Reulis)
 │     ├──
 │     └── Fletcher metrics
 │
 ├── Scheduler: RepaipStuckReqronon regt
 │     ├── Queue topsessrarequea stukenessmuts
 │     ├── dispatch ParseRessumeJob ag
 │     └── coctiont — no dupe acctione
 │
 └── Metriss
       ├── resume_uploads_created_total
       ├── rmsmffufftp
       ├── resumessparsedpritsts
       ├── resume_uolstatss‎storead
       ├── resume_recon_failed_retry_required_total
       ├── resume_a_all_session_count_total
       ├── resume_reconcil_atte mt_lor forth py()
       └── resumk payload ~size_hloistt
```
