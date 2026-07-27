# 45 — Failure modes (every way 10K breaks + our response)

> *Intent: name every failure we know about, with the user-facing behavior +*
> *the on-call-friendly diagnostics.*

## FAIL-1 — Browser offline mid-upload

**Surface:** badge "Network Lost"; progress bar frozen; parser paused.
**Backend:** session row remains `Created`/`Uploading`. No partial finalize.
**Recovery:** online event → validate server with HEAD/R2; resume PUT
from offset (multipart upload ≥5 MB case) or restart (single-PUT case).
**Diagnose:** `tail storage/logs/laravel.log` for `jobs.resume.upload_recovered`.

## FAIL-2 — Browser offline mid-finalize

**Surface:** "Upload completed" badge shown, but submit preflight 404s.
**Backend:** session row STILL `Uploaded` post-HEAD check. Reverb kept
the state, but `finalize` was never POSTed.
**Recovery:** online event → re-POST `finalize` (server is idempotent.
**Diagnose:** metric `resume_finalize_retried_total` increments.

## FAIL-3 — Refresh after online-event but `finalize` was lost in transit

**Surface:** user has to refresh; refresh recovery re-reads localStorage.
**Backend:** session row may have been set to `Uploaded` by a later pod's
re-POST or may still be `Created`. Either is OK.
**Recovery:** GET `/api/resume-sessions/{uuid}` returns truth; UI re-syncs.
**Diagnose:** metric `resume_session_refresh_total{outcome}`.

## FAIL-4 — Two pods both move Created → Uploaded

**Surface:** none (correctness preserved).
**Backend:** second pod's UPDATE is filtered by status guard
(`status='Created'`). One wins.
**Diagnose:** log line `jobs.resume.upload_transition_skipped_pod=2`.

## FAIL-5 — R2 PUT 4xx on partial upload

**Surface:** "Upload failed" badge, retry button.
**Backend:** session row stays `Created`. **Rotate presigned URL**: client
retries with a fresh URL (server issues new one on next presign attempt).
Same UUID holds for idempotency.
**Diagnose:** metric `resume_presign_rotations_total`.

## FAIL-6 — R2 PUT 5xx (server-side)

**Surface:** retry modal, exponential backoff, max 3.
**Backend:** same UUID holds. After 3, session row → `Failed` with
`last_error='R2 503'`. UI shows "Upload unsuccessful" with retry CTA.
**Diagnose:** metric `resume_upload_failed_total{reason}`.

## FAIL-7 — Parser fails (network)

**Surface:** "Parsing failed — We'll try once more in a moment" modal.
**Backend:** parses_lines attempt=1. Once succeeded, status = `Parsed`.
Once attempt=1 failed with no Retry-After, status = `Failed`.
**Diagnose:** metric `resume_parse_attempts_total{outcome}`.

## FAIL-8 — Parser 429 with `Retry-After`

**Surface:** "Resume parse paused — server is busy; we'll continue automatically".
**Backend:** `ResumeParserService` honors `Retry-After`
(`sleep(min(seconds, cap=10))`); one attempt only.
**Diagnose:** metric `resume_parser_429_total`.

## FAIL-9 — Parser exceeds timeout

**Surface:** resubmit wait; do not auto-retry.
**Backend:** one attempt only; status = `Failed` after timeout.
**Diagnose:** `last_error='PAR_TIMEOUT'` on the row.

## FAIL-10 — Submit POST times out from browser; verify success

**Surface:** did it go through, or not?
**Backend:** server state survives. Server returns 201 with `{session_linked_candidate_id}`.
GET `/api/resume-sessions/{uuid}` returns the linked_candidate_id;
UI shows green "We received your submission".
**Diagnose:** metric `resume_submit_recovered_total`.

## FAIL-11 — Submit POST returned 5xx, then browser retries

**Surface:** user double-clicks.
**Backend:** server check on candidate uniqueness (composite idx:
company_id, email, phone) — second insert returns 200 with the existing
candidate_id; UI surfaces "you already submitted — here is the prior
candidate row" banner. The Duplicate Ownership Engine flags a "possible
duplicate" badge on the candidate; **the engine NEVER merges or
deletes candidates** (Step 20). Recruiter decides whether to archive
one manually.
**Diagnose:** metric `resume_submit_recovered_total`.

## FAIL-12 — Session expires (presign → upload-complete > 30 min)

**Surface:** PUT returns 403; UI shows "Resume expired, please pick again".
**Server:** `CleanupOrphanUploads` after 24h marks `EXPIRED`; DELETE'd from R2.
**Diagnose:** metric `resume_sessions_expired_total`.

## FAIL-13 — Reverb pod crash mid-broadcast

**Surface:** "Parser progress" stuck at last-seen %.
**Backend:** session state still advances server-side. Recovery: refresh
logic re-reads `/api/resume-sessions/{uuid}` and resyncs.
**Diagnose:** Reverb pod health (separate endpoint).

## FAIL-14 — Pod OOM / scale-out saturation

**Surface:** 502s on presign/finalize.
**Backend:** HPA at 70% mem; rate-limit /presign to 120/min/company + 30/min/user.
**Diagnose:** 5xx rate per pod.

## FAIL-15 — Cross-tenant Reverb subscribe attempt

**Surface:** silent — wrong-tenant sends/receives never happen.
**Backend:** channel auth checks `Auth::user()->company_id === channel_company_id`,
else 403. URL smuggling can't bypass it.
**Diagnose:** metric `resume_reverb_auth_failed_total{reason}`.

## FAIL-16 — File < 1 KB (empty/malicious tiny file)

**Surface:** "File too small, please re-pick".
**Backend:** server check `file_size_bytes >= 1 KB`.
**Diagnose:** metric `resume_rejected_files_total{reason=too_small}`.

## FAIL-17 — Filename injection attempts

**Surface:** silently sanitized server-side; client logs to console.
**Backend:** filename = `Illuminate\Support\Str::ascii(strip_control($origFilename))`
+ length 1..128 enforced. Original persisted up to 128 chars.
**Diagnose:** metric `resume_filename_sanitized_total`.

## FAIL-18 — Stale finalize (modal open while session moved to `Parsed`)

**Surface:** UI shows "Parser finished", but submit is misaligned.
**Backend:** GET `/api/resume-sessions/{uuid}` returns `Parsed`; UI
disables Update Candidate button (read-only inspection).
**Diagnose:** metric `resume_stale_finalize_total`.

## FAIL-19 — Resume submitted, but ES index write fails

**Surface:** recruiter sees "We received your submission" (DB truth);
candidates search misses them.
**Backend:** elastic_index_candidate dispatched to `bulkelasticindex` queue,
retries up to 5 times with exponential backoff; on terminal fail, audit
row `ActionElasticIndexFailed` written; ops follow-up.
**Diagnose:** metric `resume_es_index_failed_total`.

## FAIL-20 — Browser retries presign POST with a different `Idempotency-Key` (intentional new pick)

**Surface:** server returns a NEW session (different UUID, different
`Idempotency-Key`) for the second request. The first session's row is
left in its current state; the second is a brand-new session.
**Backend:** idem key is `crypto.randomUUID()` per request, NOT a
content hash (ADR-002, INV-4). A user re-picking the same file or
renaming the file before upload always emits a new key → always gets
a new session. This is INV-1: every file pick = brand-new session.
The Duplicate Ownership Engine flags the dup at submit time (Step 20);
it NEVER merges.
**Diagnose:** metric `resume_idempotency_key_rotations_total`.
