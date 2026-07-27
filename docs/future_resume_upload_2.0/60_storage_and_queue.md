# 60 — Storage & queue

> *Intent: size + shape the storage and queue infrastructure so 10K*
> *concurrent users doesn't Bottleneck.*

## Bucket strategy

### `resumes-temp` (R2, lifecycle 30 days)

| Property | Value | Why |
|---|---|---|
| Region | `auto` (Cloudflare) | closest to user |
| Public read | false | never expose blob URLs |
| Default storage class | Standard | no archival for cache |
| Lifecycle | delete after 30 d; prefix=temper/ | automatic clean |
| Multipart support | yes | enables resumption |
| ACL | private + presigned PUT | R2 supports it |

### `resumes-final` (S3)

Granite-cold storage, versioned.

| Property | Value | Why |
|---|---|---|
| Versioning | ENABLED | allow typo-roll-back |
| Default storage class | STANDARD | readable fast |
| Lifecycle | None in v2 | cleanup is by `Reconciled` |

## Object keys

Convention: `temp/{year}/{month}/{c_id}/{uuid_prefix}-{h7}.{ext}`

Example: `temp/2026/07/7/abcdef01234-7f3e9c1.pdf`

The `c_id` makes analytics easy (`r2_du --buckets resumes-temp`
listing per tenant without DB roundtrip).

## Idempotency key

```php
$idem = hash('sha256',
    $fileBytesHash .
    ':' . $companyId .
    ':' . $safeFilename
);
```

Properties:
- Same `bytes + tenant + name` → same key → server reuses UUID.
- Same `tenant + name`, different `bytes` (e.g., a 1 px change) → different key.
- Cross-tenant same hash → different key (different company_id baked in).

## Direct-to-R2 flow (no PHP byte path)

```
[Browser] ─── SHA256(file) ──┐
                              │ idem key
[Browser] ── POST presign ─►  [Server] (Octane, 1 ms)
                              │
                              ├─ check redis: idem exists?
                              │     ├─ yes & not expired → reuse UUID
                              │     └─ no → insert row, sign URL
                              │
                              │  Return presigned PUT (15 min TTL)
                              ▼
[Browser] ── PUT <R2> (presigned, bytes) ──► [R2] (zero PHP bytes)
                              │
                              │  PUT progress 0..100%
                              │  (Reverb not used here; we want minimal broadcast)
                              │
                              │  PUT complete (HEAD returns 200)
                              ▼
[Browser] ── HEAD r2 ─►  (verifies object exists)
[Browser] ── POST finalize ─► [Server]
                              │
                              ├─ HEAD <key> from server (sanity check)
                              ├─ UPDATE row status = Uploaded
                              ├─ emit Reverb ResumeUploaded
                              └─ dispatch ParseResumeJob → parse_resumes queue
```

## Idempotent retry on PUT

R2 supports multipart upload. v2 uses single-PUT for files < 5 MB;
multipart for ≥ 5 MB.

For `< 5 MB`: on Retry, server issues a new presigned URL; client
sends a fresh PUT; no concurrency tuning needed.

For `≥ 5 MB`: client uses R2 multipart; on failure, list parts via R2;
resume from offset; finalize completes multipart. This is 60% of
typical 6 MB+ files.

## Queue sizing

### `parse_resumes`

Each parse = 1 OpenAI call ≈ 8s wall. v2 concurrency cap per pod = 8.
Pods = 2 (scales with HPA). Total throughput = 16 parses/sec sustained.

At peak 1,200 files/sec (10K / 8 s avg upload), 16 parses/sec means
queue depth can hit 1000 fast.

Decision: pod count matches QueueFormula in
`docs/future_resume_upload_2.0/75_capacity_plan.md` — v2 ships 3
parser pods (24 parses/sec). Burst absorbs on Redis rate-limit side.

### `bulkelasticindex`

10K candidates/min ≈ 170/sec ES index writes. Elasticsearch `single-write`
budget at 1,200/sec safely; bulk-writes batch is `1000 per batch, every 5 s`.

Pods: 2. v2 ships 1 pod bench-tested to absorb 300/sec.

### `reconcile_orphans`

Cron-daily; <200 deletes typically. 1 pod.

### `repair_stuck`

Cron every 15 min; 1 pod.

## Scrubber cap

`ResumeMetrics::counter` writes to Redis with TTL=90 d. Scrubber
key-prefix-periodically: keep ≤ 90 d old keys; future `ResumeMetrics:
scrub_old_counters` command.

## Failure recovery

| Failure | Auto-recovery | Manual |
|---|---|---|
| Pod killed mid-parse | Other pod picks up at next retry (timeout) | RerunParse resume job |
| Redis OOM | Rate-limiter fails open with TODO | on-call |
| R2 PUT latency | Client-side backoff already in JS | n/a |
| ES shard unbalance | `/_cluster/reroute` | on-call via health endpoint |
| Reverb backlog | Drain on connect | n/a |

## Health endpoint probe layer

`ResumeHealthController`:
- MySQL: `SELECT 1` + a tiny query against `resume_uploads`
- Redis: `PING`
- R2: `Storage::disk('r2')->exists('__healthprobe')` (write + read + delete)
- Queue: `Queue::size('default')` and `Queue::size('parse_resumes')`
- Scheduler: `Cache::lock('resume_scheduler_leader', 30)->get()`,
  `is_leader=$leader_owner_id === gethostname()`
- Orphan + stuck counts: `ResumeMetrics::currentOrphanUploads()`,
  `currentStuckSessions()`
- Parser backlog: `ResumeUpload::where('status', PARSING)->count()`
