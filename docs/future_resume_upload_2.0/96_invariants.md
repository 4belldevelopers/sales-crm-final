# 96 — Invariants (kept-true across the resume upload subsystem)

> *Intent: lock down the few rules that MUST hold across the system. These are*
> *non-negotiable design rules. If a PR breaks one of them, the PR is closed*
> *on review, regardless of how good the rest of it looks.*

## Reading order

1. INV-1 — session UUID lifecycle
2. INV-2 — manual values always win
3. INV-3 — submit idempotency via server state
4. INV-4 — overall upload idempotency
5. INV-5 — parser result belongs to one session

Each invariant below shows: **canonical statement**, **where it lives**,
**what enforces it**, **the test that proves it holds**.

## INV-1 — Session UUID has a 1:1 lifecycle with one user-initiated flow

**Canonical:** A `upload_session_uuid` has a 1:1 lifecycle with one
user-initiated "pick → upload → finalize" flow. The state machine is
monotone from `Created` until terminal (`Parsed`, `Cancelled`, `Expired`,
or `Reconciled`). The same `idempotency_key` on a re-pick of the same
file REUSES the UUID — and is permitted only while the row is
non-terminal. Once terminal, the UUID is dead: no further uploads.

**Where it lives:**
- DB: `resume_uploads` row keyed by `uuid` + `company_id`.
- Server: `ResumeUploadSession` service guard around every state
  transition.
- API: `EP-3 finalize` rejects on terminal state with HTTP 409.

**Test:** `tests/Feature/ResumeV2Invariant1Test.php`
- `test_repick_same_idem_reuses_uuid_when_nonterminal`
- `test_repick_after_parse_gets_new_uuid` (terminal → new UUID)
- `test_finalize_rejects_when_session_cancelled_terminal`

## INV-2 — Parser may populate empty fields only

**Canonical:** Parser prefill never overwrites a non-empty input. The
recruiter's typed value wins; the parser's value goes into the input
only if the input was empty **and** the parser's value is non-empty.

**Where it lives:**
- Blade: `<input … data-parser-populated="false">` attribute.
- JS: `resume-populate-if-empty.js` checks
  `!input.value && input.dataset.parserPopulated === 'false'`
  before assigning.
- Server: `ResumeParserService` does not write back into the form; the
  JS read is independent. Server writes only to
  `resume_parse_results.payload`.

**Test:** `tests/Feature/ResumeV2Invariant2Test.php`
- `test_manual_values_always_win_over_parser` (the test name is fixed;
  see ADR-013)
- `test_parser_payload_writes_to_parse_results_table_not_applicant_fields`

## INV-3 — Submission may retry, but server verifies state before any insert

**Canonical:** The browser MAY auto-retry a `POST /candidates` that
failed at the network layer. The server MUST verify the session state
on every attempt BEFORE inserting. If `linked_candidate_id` is already
set, server returns the existing candidate (HTTP 200) instead of
inserting a duplicate.

**Where it lives:**
- Controller: `ServiceCompanyJobApplicationController::store` (and
  the v2.5 merged variant) reads session first.
- DB: `resume_uploads.linked_candidate_id` UNIQUE behavior at
  application level (FK + DB constraint via `unique(company_id, session_uuid, linked_candidate_id)`).
- API: `EP-candidate-store` returns 200 with `note=duplicate_merged`
  on dedup path.

**Test:** `tests/Feature/ResumeV2Invariant3Test.php`
- `test_submit_retry_when_already_linked_returns_existing_candidate`
- `test_submit_retry_does_not_duplicate_candidate_row`
- `test_submit_retry_after_501_returns_same_candidate_id`

## INV-4 — Every upload IS idempotent (same idem key = same UUID)

**Canonical:** Two recruiters (or the same recruiter) uploading the
same `idempotency_key` against the same `company_id` always resolve to
the same `upload_session_uuid`. Concurrent pods hitting `INSERT` race
produce one row, not two. Idem key is `sha256(file_bytes + company_id + safe_filename)`.

**Where it lives:**
- DB: `UNIQUE (company_id, idempotency_key)` on `resume_uploads`.
- Service: `ResumeUploadSession::createOrPresign(...)` — INSERT IGNORE
  + SELECT lookup pattern.
- Browser: presign POST sends the same key on retry; server refuses
  to issue a new UUID.

**Test:** `tests/Feature/ResumeV2Invariant4Test.php`
- `test_same_idem_key_returns_same_uuid_under_concurrent_pods`
- `test_session_reused_when_idem_key_matches_existing_nonterminal`
- `test_new_uuid_when_idem_key_differs_by_one_byte`

## INV-5 — One parser result belongs to one upload session

**Canonical:** There is at most one `resume_parse_results` row per
`resume_upload_id`. Parser dispatch is bounded: Laravel's
`ShouldBeUnique(60s)` AND `parser_attempts` is incremented BEFORE the
parse and capped at 1 server-side.

**Where it lives:**
- DB: `resume_parse_results.resume_upload_id` FK with `UNIQUE` per row.
- Job: `ParseResumeJob::uniqueId()` returns the `resumeUploadId`;
  `uniqueFor = 60`.
- Server: `ParseResumeJob::handle()` reads `parser_attempts` first
  and short-circuits if already attempted.

**Test:** `tests/Feature/ResumeV2Invariant5Test.php`
- `test_parser_dispatched_twice_within_60s_runs_only_once`
- `test_parser_attempts_incremented_before_parse_call`
- `test_parser_second_attempt_within_window_short_circuits`

## Why each invariant maps to a measurable test

| Invariant | Tied to ADR / Goal / Failure Mode |
|---|---|
| INV-1 | ADR-002, ADR-009, FAIL-12, G-5 |
| INV-2 | G-3, ADR-013 |
| INV-3 | FAIL-10, FAIL-11, ADR-013 fallback |
| INV-4 | ADR-002, ADR-009, G-5, C5 (current state gap) |
| INV-5 | ADR-005, G-4, C7 (current state gap) |

## Original draft — what was kept, what was sharpened

| # | User-supplied invariant | Verdict |
|---|---|---|
| 1 | "One upload_session_uuid owns exactly one upload" | Sharpened into INV-1 — the original reads as forbidding UUID reuse on re-pick, which breaks ADR-002. |
| 2 | "Parser may populate empty fields only" | Kept as INV-2 — matches G-3 verbatim. |
| 3 | "Submission must never replay automatically. Always verify server state before retry." | Sharpened into INV-3 — the original self-contradicts; we keep half (verify-first) and correct the other (browser may replay; server deduplicates). |
| 4 | "Every upload is idempotent" | Kept as INV-4 — the canonical statement. |
| 5 | "One parser result belongs to one upload session" | Kept as INV-5 — verbatim. |

## Frequently-asked review questions

**Q: "What if the recruiter really did want to re-upload the same file?"**
A: They will get the same UUID reused (INV-1 + ADR-002). If they want
to start over, they explicitly cancel first (`DELETE …/{uuid}`) → UUID
goes terminal → next presign issues a new one.

**Q: "What if the parser takes >30 s?"**
A: Status flips to `Failed` with `last_error='PAR_TIMEOUT'`. A retry
attempt by the recruiter re-runs the upload flow but the parser
itself never re-runs automatically — it's bounded to one attempt.

**Q: "What if the same browser opens two tabs at the same time?"**
A: BroadcastChannel keeps them in sync (v2.5, ADR-018). If they
both POST `/api/resume-sessions` with the same idem key concurrently,
INV-4 says one UUID wins; the other gets the same UUID via SELECT.
