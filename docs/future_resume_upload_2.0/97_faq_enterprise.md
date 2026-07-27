# 97 — Enterprise FAQ (the three reviewer questions, in full)

> *Intent: expand the three reviewer FAQ entries from*
> *[`96_invariants.md`](96_invariants.md) into full enterprise answers.*
> *Each answer covers: the answer + the customer-facing message + the*
> *audit shape + the runbook command + the SLA.*

Companion file: [`96_invariants.md`](96_invariants.md) (the kept-true
invariants). The invariants are the SHAPES the answers must respect;
this file is the EXPERIENCE in production.

---

## FAQ-1 — "What if recruiter re-uploads the same file?"

### Overview

When the recruiter selects the same physical file (same bytes), or
renames it locally and tries again, the system MUST NOT create a
second `resume_uploads` row for the same tenant. The same UUID is
reused while non-terminal; once terminal, the new pick gets a fresh
UUID but the **same idem key will not re-bind**.

### The state-by-state answer

| Session row state on re-pick | Server does | User sees | Audit |
|---|---|---|---|
| `Created` (just presigned, no PUT yet) | **Reuse UUID,** rotate presigned URL only if expired | "Uploading...", no UI flash | `ActionPresignRotated` audit row |
| `Uploading` | **Reuse UUID,** no rotation unless TTL < 60 s | continuous bar | `ActionPresignRotated` |
| `Uploaded` (R2 object present) | **Reuse UUID,** but DO NOT touch R2 object | "Choose a different file" modal | `ActionIdempotentRejected` |
| `QueuedForParsing` / `Parsing` / `Parsed` | **Reuse UUID** — the file is logically "the same" already linked | Banner: "Upload already in progress for this resume" | `ActionIdempotentRejected` |
| `Failed` | **REUSE UUID** (caller has explicit intent to retry) | Retry banner visible, button reuses queue slot | `ActionFailedRetry` |
| `Cancelled` | **NEW UUID** — old row at terminal state | fresh new upload | `ActionNewSession` |
| `Expired` | **NEW UUID** | fresh new upload; old session swept by Reconciler | `ActionNewSession` + `ActionReconcileScored` |
| `Reconciled` | **NEW UUID** | fresh new upload | `ActionNewSession` |

### Customer-facing message (UI copy)

> **English:** *"It looks like you already uploaded this resume. We've*
> *kept your previous session and replaced the file. If you'd like to*
> *start over, please [cancel the previous upload]."*

> **Hindi:** *"ऐसा लगता है कि आपने पहले ही यह रिज्यूमे अपलोड कर दिया है। हमने*
> *आपके पिछले सत्र को रखा है। नए सिरे से शुरू करने के लिए, कृपया पिछला अपलोड रद्द करें।"*

The copy NOT throwing a generic "Error" message is the visibly
out-care feature; the platform cares about the recruiter's trust.

### Audit shape

```php
$auditor->record(session: $row, action: 'ActionIdempotentRejected', [
    'idempotency_key_digest' => substr($idemKey, 0, 16),
    'previous_status' => $row->status,
    'previous_created_at' => $row->created_at,
    'caller_ip_hash' => $request->ip_hash(),  // SHA256(IP + per-day salt for PII)
]);
```

### Runbook

```bash
# Ops: investigate "Why is recruiter seeing double invoice?"
1. Get idem_key from recruiter's session_id:
   SELECT idempotency_key, status, linked_candidate_id, expires_at
   FROM resume_uploads
   WHERE uuid = ?;

2. Confirm cupervisor guest.author then plan from status:

  - Created/Uploading/Uploaded/QueuedForParsing/Parsing/Parsed/Failed: normal,
    no action — INV-1 contract.
  - Cancelled/Expired/Reconciled: this session is terminal; new pick
    expected.
```

### SLA

| Scenario | Resolve time |
|---|---|
| Re-pick same file (normal) | sub-second; no UI flash |
| Re-pick on terminal session | sub-200 ms |
| Re-pick with cross-tenant leak | enterprise: P2 alert, SLA 4 hours |
| Multi-pod idem race | one wins; one logs `ActionIdempotentRejected`; resolver auto-merged |

### What's already enforced vs aspirational

| Behavior | Enforced | Aspirational |
|---|---|---|
| Reuse UUID on non-terminal re-pick | ✅ INV-1 | — |
| Rotate presigned URL on TTL < 60 s | ✅ v2.5 | — |
| Reject reuse on `Uploaded` (UI banner) | ✅ | — |
| Audit row on idempotent rejects | ✅ | — |
| Per-tenant tenant-leak alerting | — | v3 |

---

## FAQ-2 — "What if parser hangs?"

### Overview

The parser runs on a neighbor pod and bounded by HTTP timeout
to the parser service. When it hangs (network drop, parser pod
crash, response buffer overflow), the orchestrator MUST surface a
defined terminal state, NEVER leave the row in `Parsing` indefinitely.

### Behavior contract

| Wait time | Server behavior | Frontend | Audit |
|---|---|---|---|
| 0-10 s | normal parse | progress 0..100% by 25% thresholds | `ActionParseStarted`, `ActionParseProgress` |
| 10-30 s | parser nearing deadline | thinner progress | `ActionParseProgress` |
| 30-60 s (timeout) | `parser_last_error = PAR_TIMEOUT`, status **= `Failed`** | "Resume parsing failed. We didn't see a response from the parser. Try again or paste the content." | `ActionParseFailed{reason='timeout'}` |
| > 60 s (cron sweep) | RepairStuckSessionsJob marks stuck as `Failed` and re-queues if attempt<1 | already-`Failed`; UI displays last-error message | `ActionRepairStuckSwept` |

### Customer-facing message

> **English:** *"We're having trouble parsing your resume right now. We've*
> *saved what you've typed, so please try again or manually fill the*
> *remaining fields. Nothing you've entered has been lost."*

> **Hindi:** *"अभी आपके रिज्यूमे के सारांश को पढ़ने में मुश्किल हो रही है। आपने जो*
> *भी भरा है, हमने उसे सुरक्षित रखा है। कृपया पुनः प्रयास करें या शेष फ़ील्ड्स*
> *मैन्युअल रूप से भरें। आपकी जानकारी खोई नहीं है।"*

The "Nothing you've entered has been lost" sentence is the most
important sentence — it affirms the v1 product rule of data safety.

### UI behavior

| Element | Behavior on `Failed` |
|---|---|
| Progress bar | stops, hides |
| Cancel button | hides (already terminal) |
| Retry button | visible — re-issues presigned PUT (if status=Cancelled) OR recreates session (NEW UUID) |
| Manual fields | recruiter can still edit + hit Submit; the parse is no longer required for submit |
| Submit button | enabled — recruiter can submit even without a `Parsed` status; system parses in background |

### Audit shape

```php
$auditor->record(session: $row, action: 'ActionParseFailed', [
    'reason_code' => 'PAR_TIMEOUT',
    'reason_detail_safe' => Str::limit($e->getMessage(), 255, ''),
    'total_attempts' => $row->parser_attempts,
    'time_to_ms' => $tookMs,
    'last_parser_pod' => $row->last_parser_pod_id,
]);
```

### Runbook

```bash
# Ops: high parser failure rate
1. /api/internal/health/resume: parser_backlog_count > 50?
  - yes: alert already firing (P3 in 80_observability.md).
  - no: small number, check individual session ids.

2. SELECT * FROM resume_uploads
   WHERE status IN ('Parsing', 'QueuedForParsing')
     AND parser_started_at < NOW() - INTERVAL 60 SECOND;

3. If root cause is pod crash:
   - Restart pod.
   - Recent ParseResumeJob on the pod re-queued by RepairStuckSessionsJob.
   - Metric: resume_repair_dispatched_total increments.

4. If root cause is dependency outage (OpenAI down):
   - Trigger parser_fallback_mode=True (admin-only flag) — pure heuristic mode
     until dependency back up.
```

### SLA

| Scenario | Resolve time |
|---|---|
| Single ParseResumeJob timeout → Failed | 30 s end-to-end |
| Bulk parser pod outage | RepairStuckSessionsJob runs every 15 min; P2 within 15 min |
| Parser vendor (OpenAI/etc) 429 streak | parser_fallback_mode admin toggle, P1 within 1 hour |

### What's already enforced vs aspirational

| Behavior | Enforced | Aspirational |
|---|---|---|
| `parser_attempts` capped at 1 | ✅ INV-5 + ParseResumeJob guard |
| `Failed` status surfaces to UI | ✅ |
| `RepairStuckSessionsJob` cron | ✅ scheduler 15 min |
| Manual submit works on `Failed` | ✅ |
| parser_fallback_mode flag | — | v2.5 only if vendor reaches persistent outage |
| Per-pod parser telemetry | ✅ |
| Auto-retry capped at 1 (never loop) | ✅ INV-5 |

---

## FAQ-3 — "What if two tabs submit concurrently?"

### Overview

It is normal for recruiters to have multiple tabs open during a long
application flow: tab A for resuming edit, tab B for verifying the
other side. When both tabs hit Submit within milliseconds, the
system MUST keep DB state correct AND visible to both tabs.

### Behavior contract

| Tab state | Tab A submit | Tab B submit | Outcome |
|---|---|---|---|
| Both have idem key | T1: server INSERT IGNORE on `(uuid)` wins | T2: server SELECT sees T1's row, returns same UUID | one UUID, both tabs show success banner; tabs reconcile via `BroadcastChannel('resume-sync')` |
| Different idem keys (different files) | T1: own session, own candidate | T2: own session, own candidate | two different runs, two different candidates — feedback prompt: "Did you mean to upload two versions?" |
| `Submitted` state set in T1, but T2 hasn't refreshed | — | T2 already in `Submitted` cache | T2 polls `GET …/{uuid}`, immediately sees `Parsed` state; UI shows the prior success |

### Tab-to-tab sync

```js
// resume-broadcast-sync.js
const chan = new BroadcastChannel('resume-sync-' + companyId);
chan.onmessage = (e) => {
  const data = JSON.parse(e.data);
  if (!data.uuid || data.uuid !== localResumeUUID) return;
  applyUIFromCrossTab(data);  // status, pct, candidate_id, etc.
};

function emitCrossTab(data) {
  chan.postMessage(JSON.stringify(data));
}

// On submit success:
emitCrossTab({ uuid, status: 'submitted', candidate_id });

// On error:
emitCrossTab({ uuid, status: 'error', visible_error: 'friendly' });
```

### Customer-facing message

If a recruiter complains "the same candidate was created twice" — which
the system prevents — it could be a perception issue: tabs may show
the same candidate banner simultaneously. UX consensus:

> **English:** *"Both tabs have your upload in sync. You can close one*
> *tab now without losing anything."*

This is a confirmation, not an error message.

### Audit shape

```php
$auditor->record(session: $row, action: 'ActionConcurrentSubmit', [
    'pod_id_a' => $aPodId,
    'pod_id_b' => $bPodId,
    'winner_pod_id' => $winnerPodId,
    'resolution' => 'INSERT_IGNORE_BOTH_RECEIVED',
    'time_diff_ms' => $timeDiffMs,
    'company_id' => $companyId,
]);
```

### Runbook

```bash
# Ops: "candidate_counter inflation check"
1. Compare application submission count to add_candidate table INSERT count
   for the same window:
SELECT date_trunc('hour', created_at) as bucket,
       count(*) as candidates
FROM add_job_wise_condidates
WHERE created_at > NOW() - INTERVAL '24 hour'
GROUP BY 1;

2. Expected: linear at some-defined-rate. Spikes in `candidates - submitted`
   metric: "resume_submit_duplicate_total" — investigate.

3. Common false positives (not real duplicates):
  - Cache stale in tab — UI counts but DB didn't.
  - Two browser windows opened moments apart.
  - Refresh-of-submit triggered re-fetch.

4. Real duplicate:
  - email + phone matches a candidate from <2 minutes ago.
  - composite idx (company_id, email, phone) returns the existing.
  - UI merges them.
```

### SLA

| Scenario | Resolve time |
|---|---|
| Two-tab concurrent submit | sub-second; one INSERT, second SELECT |
| Repo auto-reconciliation | sub-15-min via RepairStuckSessionsJob |
| Multi-pod idem race | one INSERT wins, others no-op |
| Cross-tenant Reverb leak attempt | 403 + alert + SLA 4h |

### What's already enforced vs aspirational

| Behavior | Enforced | Aspirational |
|---|---|---|
| `UNIQUE (company_id, idempotency_key)` | ✅ INV-4 |
| `linked_candidate_id` UNIQUE behavior on duplicate | ✅ INV-3 |
| BroadcastChannel for same-browser tabs | ✅ v2.5 (ADR-018) |
| Reverb cross-device cross-tab | ✅ |
| 403 on cross-tenant Reverb | ✅ INV-3 implicit |
| Schema composite index `(company_id, email, phone)` | — v3 (Candidate dedup) |

---

## The 3 takeaways at enterprise level

| Idiom | Enterprise-level answer |
|---|---|
| Re-upload same file | **Same UUID reused; rotated presign; non-terminal states re-use.** Customer sees continuity, not duplicate; "trust" copy, not error. |
| Parser hangs | **`Failed` status immediately; manual fields survive; auto-repair cron sweeps. Customer sees "Nothing you've entered has been lost" affirmation.** |
| Two tabs in parallel submit | **One INSERT IGNORE wins; BROADCAST keeps tabs in sync; customer sees "two tabs reconciled" not "two applicants".** |

The pattern across all three answers: **state of truth = the DB row**, customer copy emphasizes **continuity and trust**, failures are **immediately visible to ops**, and **no silent data loss**.

A line in the contributor songbook: *"if a customer ever asks 'where is my data?' and the answer is anything but a single MySQL row with status='Parsed' and linked_candidate_id, we have a P1 bug."*
