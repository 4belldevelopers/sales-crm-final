# 99 — Decision log (ADR-style)

> *Intent: record trade-offs so future AIs don't re-litigate them. Each*
> *ADR follows the pattern: Context → Decision → Why now → Trade-offs →*
> *Reversible?*

---

## ADR-001 — Direct browser-to-R2 presigned PUT, NOT through PHP

**Context.** v1 PHP receives every byte. At 10K concurrent × 6 MB avg,
60 GB/s enters PHP, multiplied by Nginx/Cloudflare bandwidth round-trips.

**Decision.** v2 PUT is browser→R2 with presigned URL; PHP sees only
metadata.

**Why now.** Single biggest bandwidth sink; cannot scale past 1K users
without it.

**Trade-offs.** Slightly more complex client (mTLS URL signing, R2
multipart upload). Bugs in URL signing would expose presigned URL misuse.
Mitigated by short URL TTL (15 min), single-use idempotency key.

**Reversible?** Yes — multipart fallback is the legacy v1 path; v2
flips back via feature flag.

---

## ADR-002 — HTTP `Idempotency-Key` header is a per-request random token, NOT a content hash

**Context.** v1 used a server-computed hash of `(file bytes + tenant +
filename)` as the idempotency key, with a `UNIQUE(company_id,
idempotency_key)` constraint. That is content-addressed deduplication,
which conflicts with the product's hard requirement that *the upload
subsystem performs no business duplicate check*. Two recruiters picking
the same bytes must always get two different sessions; the candidate-
level ownership check lives in the Duplicate Ownership Engine at
submit time, not in the upload subsystem.

It also caused the v1.5 orphan carousel: a user picking the same file
twice and a user picking the same file three times produced only 1 row
+ 2 R2 orphans — the inverse of what INV-1 ("every file pick is a
brand-new session") requires.

**Decision.** The idempotency key is now generated on the **client**, at
the moment of presign-request construction, using `crypto.randomUUID()`.
It travels as the HTTP `Idempotency-Key` request header (standard
RFC: draft-ietf-httpapi-idempotency-key-header). The server stores it
on the `resume_uploads.idempotency_key` column with an **INDEX, not a
UNIQUE constraint**, and replays the most recent non-terminal session
matching `(company_id, idempotency_key)` on a duplicate header.

- Same header on a retried POST → server returns the previously-issued
  session (transport retry safety).
- Different header on a new POST → new session, always (INV-1).
- Two pods racing on the same header → the second INSERT proceeds but
  the first is returned to the second caller via the lookup (INV-4);
  the `INDEX` makes the lookup O(1) for the rare race.

**Why now.** The Duplicate Ownership Engine is the owner of "is this a
duplicate candidate?" — the upload subsystem MUST NOT compete with it
by accident, and any UNIQUE constraint here would silently reintroduce
content-based dedupe the moment a developer added a new caller.

**Trade-offs.**
- A browser that retries with a fresh `crypto.randomUUID()` after a
  successful presign would create a second session on the same bytes.
  Mitigated by client discipline: the key is held in module closure
  for the duration of the `fetch()` retries; a *user* re-pick (different
  file, or same file again) intentionally emits a new key.
- Race-window "double-charge" of storage at the OBJECT level is still
  prevented by R2's atomic PUT — see INV-2 in the storage domain.
- A truly identical concurrent POST from the *same* JS closure (e.g.
  user double-clicks submit twice before first response) → two
  sessions, two R2 objects, one wins at submit time per INV-3
  ("submit verifies state before any insert").

**Reversible?** Yes — additive column, no UNIQUE to drop. Drop the
INDEX, drop the header lookup. Forward-compat: a future "file
fingerprint" path belongs in the Duplicate Ownership Engine, not here.

---

## ADR-003 — Reverb same-tenant private channel auth from session, NOT URL or localStorage

**Context.** Reverb channel auth in v1 reads from URL path or
localStorage and is tenant-leak-prone.

**Decision.** Auth callback: `(int) Auth::user()->company_id === (int) {channel_company_id}`. The `int` cast is critical.

**Why now.** Cross-tenant progress visibility is a hard security
violation in a multi-tenant ATS/marketplace.

**Trade-offs.** User must re-authenticate when their auth/session
ticket expires; the broadcast stops there.

**Reversible?** Yes.

---

## ADR-004 — State machine on `resume_uploads.status`, NOT a separate `resume_upload_state_transitions` table

**Context.** v1 already uses a CHECK-and-UPDATE pattern; an audit table
plus a transitions table is two-phase and easy to confuse.

**Decision.** Stay with the existing conditional UPDATE pattern; use
`upload_session_audits` for *audit*, but the state machine is
implicit in the `resume_uploads.status` enum + conditional UPDATEs.

**Why now.** Adding a transitions table adds latency for state reads
with no correctness gain.

**Trade-offs.** Transitions table gives explicit "who/when" — partly
replaced by `upload_session_audits.action_at`.

**Reversible?** Yes.

---

## ADR-005 — ~~ONE Retry-After attempt in parser, NO automatic retry loop~~ — REMOVED 2026-07-27

**Context (historic).** Provider-side 429 has a `Retry-After`. v1 had
no honor; blast retry pattern likely.

**Original decision.** v2 honors `Retry-After` once, then fails.

**REMOVAL reason.** v2 has no server-side parser. There is no parser
queue to retry, no `Retry-After` surface to honor. Browser-side
`pdf.js` is deterministic; failures degrade to "empty fields" only,
which INV-2 handles fine. ADR-005 is kept here as a tombstone to
preserve the ADR numbering for downstream cross-references; the
content is no longer in force.

**Reversible?** N/A — concept removed, not deferred.

---

## ADR-006 — Pre-existing backups: legacy `POST /uploadresume` kept as a fallback

**Context.** A JS-side failure to load v2 modules must not lose data.

**Decision.** The v1 multipart path is preserved behind fallback
`data-nojs` HTML. Kept for 6 months.

**Why now.** Defense in depth — on JS module load failure (CDN, CSP,
label, etc.) we don't lose bytes.

**Trade-offs.** Two upload code paths increase surface for bugs.
Mitigated by feature flag `resumes.legacy_uploadresume_compat`.

**Reversible?** Yes — v2.1.0 deletes the legacy path.

---

## ADR-007 — No `chmod 0777` on `bootstrap/cache` in v2

**Context.** The v1 /login 500 chain had a sub-bug where Octane's
first-request bootstrap wrote to `bootstrap/cache` and failed because
the directory wasn't writable. The fix was `chmod 0777` which is
production-unsafe.

**Decision.** v2 sets `bootstrap/cache` to `0755` and uses a
deployment-time write privilege via container UID-match with FPM.

**Why now.** Octane `octane:reload` should not rely on world-writable
permissions in production.

**Trade-offs.** Off-the-shelf FPM images may need UID adjustment.

**Reversible?** Yes.

---

## ADR-008 — Health endpoint ON by default at v2.0.0a — even if no other step shipped

**Context.** Without health endpoint, ops cannot decide whether the
system is "partially serving" or "completely down" — it's the same
status before v2 rolls out.

**Decision.** Health endpoint ships at v2.0.0a, even before the
upload pipeline changes.

**Why now.** Lives as a small dependency-free surface; risk =
near zero.

**Trade-offs.** None.

**Reversible?** Yes.

---

## ADR-009 — INDEX (not UNIQUE) on `(company_id, idempotency_key)` for replay-lookup, INSERT-then-SELECT on miss

**Context.** Two pods receiving the same `Idempotency-Key` header
concurrently — must produce exactly one session for that header, not
two. ADR-002 dropped the UNIQUE constraint because it conflated
transport-retry replay with content dedupe; but the multi-pod race
window has not gone away.

**Decision.** The idempotency_key column now has an `INDEX
(company_id, idempotency_key)` only. Replay uses an explicit
INSERT-then-SELECT pattern:

```
SELECT uuid, status FROM resume_uploads
  WHERE company_id = ?
    AND idempotency_key = ?
    AND status NOT IN (terminal)
  ORDER BY id DESC
  LIMIT 1;

-- if not found:
INSERT INTO resume_uploads (… idempotency_key …)
  VALUES (…);
```

**Why now.** Race-window correctness still has to hold; the `INDEX`
gives a fast replay-lookup without a uniqueness gate that would
re-introduce the ADR-002 anti-pattern. INSERT-then-SELECT is the same
pattern v1 used; multi-pod correctness comes from the explicit
transaction + the `orderByDesc('id')` lookup that resolves two
concurrent inserts to the same winner (the second caller sees the
first caller's row via the lookup).

**Trade-offs.**
- Two concurrent inserts on a cold key produce two rows; the second
  request's lookup returns the first row, but its own INSERT also
  exists. **Mitigation:** the second INSERT is harmless — it has the
  same `(company_id, idempotency_key)` value, the same terminal
  status (Uploaded/Pending) once both writers race-ahead, and at
  submit-time each session is independent. The user picks one tab to
  submit with (INV-1 + INV-3); the other session is reconciled by
  `ReconcileOrphanUploadsJob` (Phase 5) within 24 h.
- This is intentional — race-on-cold-key is a real but bounded
  scenario; the alternative (UNIQUE) is forbidden by ADR-002.

**Reversible?** Yes — drop the INDEX, accept a slower replay-lookup,
keep the INSERT-then-SELECT pattern.

---

## ADR-010 — File cap = 6 MB, NOT negotiated per request

**Context.** Some files are 8 MB, some 4 MB. Negotiating is
UX-bad.

**Decision.** Hard 6 MB cap.

**Why now.** Server's `max:6048` KB has been the contract for v1.
v2 keeps it.

**Trade-offs.** Some users with long resumes may complain.

**Reversible?** Yes — bump cap = discuss with product first.

---

## ADR-011 — Public career-apply keeps HTTP polling (1 Hz) for parse progress, NOT Reverb

**Context.** Public candidates are anonymous. Reverb has 1 channel per
user; public-side blast fan-out is unbounded.

**Decision.** Public progress: HTTP polling at 1 Hz.

**Why now.** Saves Reverb pod budget; cost on the public side is
not worth the complexity.

**Trade-offs.** Public candidates get 1-second-late progress.

**Reversible?** Yes.

---

## ADR-012 — Multipart R2 PUT for files ≥ 5 MB, single PUT for < 5 MB

**Context.** A single 6 MB PUT has too low a success rate on
small-loss networks. Multipart uploads allow resumption from offset.

**Decision.** ≥ 5 MB = multipart (>1 part); < 5 MB = single PUT.

**Why now.** 5 MB is empirically the boundary where R2 multipart
dominates single PUT for success rate.

**Trade-offs.** Two client code paths.

**Reversible?** Yes.

---

## ADR-013 — Explicit "Manual Values Always Win" test (`test_manual_values_always_win_over_parser`)

**Context.** Without a single test asserting this rule, the JS will
eventually overwrite manual values, especially during state-machine
resets.

**Decision.** One explicit test; UI rule reinforced by a
`data-parser-populated` attribute on inputs that the JS reads to
refuse overwriting on re-render.

**Why now.** This is the v1 product brief's most important rule.

**Trade-offs.** None.

**Reversible?** Yes.

---

## ADR-014 — Vertical slices extraction from `HomeController` and `ServiceCompanyJobApplicationController`

**Context.** These two controllers are 16,633 and ~813 LOC.
The v1 500-chain bug was cross-cutting concerns in their init paths.

**Decision.** v2 extracts thin delegating wrappers; the heavier logic
moves into vertical slices (Route + FormRequest + Action + ES sync +
Observer + Reverb + test).

**Why now.** `HomeController` boot-time deps are a hot path; cuts
happen in v2.0.0f/m.

**Trade-offs.** Time investment for low wins in the v1-era code.
Mitigated by leaving the legacy path during v2.

**Reversible?** Yes.

---

## ADR-015 — ~~One parser pod per "tenant hotness", Redis-locked per-pod concurrency~~ — REMOVED 2026-07-27

**Context (historic).** One tenant spiking triggers throttling on the parser.

**Original decision.** Per-pod concurrency cap via Redis semaphore keyed by
`tenant_id`; max is configurable.

**REMOVAL reason.** v2 has no parser pods. The single parser layer runs
in the browser from the local `File` object; browser memory is bounded
per-tab, and per-tab throttling is the browser's responsibility, not
the server's. ADR-015 is kept here as a tombstone to preserve the ADR
numbering for downstream cross-references; the content is no longer
in force.

**Reversible?** N/A — concept removed, not deferred.

---

## ADR-016 — `documents()` row de-dup on `(name='Resume', company_id, candidate_id)` in v2

**Context.** v1 de-dupes on `(name='Resume')` only — cross-candidate
collision.

**Decision.** Compound unique on `(name='Resume', company_id, candidate_id)`
goes into MySQL additive migration.

**Why now.** Cross-candidate smoke showed v1 had collisions on certain
recruiter flows.

**Trade-offs.** Migration is additive; no rewriting.

**Reversible?** Yes.

---

## ADR-017 — Move presign to a Cloudflare Worker at v3.0, NOT in v2

**Context.** Today presign is an Octane round-trip per upload. At 10K
concurrent that's ~3,000 RPS on Octane for sign-only work. Cloudflare
Workers can sign R2 URLs at the edge with bucket-prefix-token binding,
removing the round-trip.

**Decision.** v2 keeps presign on Octane (proven pattern, low risk).
v3 considers moving presign to a Cloudflare Worker.

**Why now (the "later" part):**
- Worker auth requires a token-bucket-rotation pipeline we don't yet
  have.
- Edge secret storage adds operational tail.
- Harder to debug.
- v2.5 already halves Octane's per-recruiter load by merging
  `finalize`+`submit` and adopting `BroadcastChannel`. That gets us
  the 90% of the value with 10% of the operational risk.

**Why NOT now (v2.0)**:
- v2.0 is the ship-it-for-10K milestone.
- A second rate-limit path requires separate CI, separate deploy,
  separate observability. We don't have those pipelines.

**Trade-offs.** If we ship this in v3, we add:
- Cloudflare account isolation (separate runbook, separate alerting)
- TS code (departure from PHP-everywhere)
- A second secret-rotation loop

We **avoid** by deferring until v2.5 traffic numbers are real.

**Reversible?** Yes — Octane presign stays as fallback when Worker
returns 5xx or is unavailable.

---

## ADR-018 — `BroadcastChannel` for same-origin tab sync, NOT Reverb, in v2.5

**Context.** Multi-tab recruiters (recruiter opens two tabs to edit +
verify) are 95% of multi-tab traffic. They share a browser process.
Reverb sends those events over WebSockets — paying server bandwidth
+ bandwidth in + broadcast-fan-out cost for what could be a local IPC
bus.

**Decision.** v2.5 introduces `resume-broadcast-sync.js` (~1 KB) that
opens a `BroadcastChannel('resume-sync-{companyId}')` per tab. All
upload/parse progress events that originate in the same browser
process are dispatched on this channel first. Reverb is still used
_only_ for cross-machine sync (recruiter on phone + laptop).

**Why now.** Zero server load for the common case; once tab B in the
same browser rehydrates from `localStorage` it picks up fan-out via
BroadcastChannel in <5 ms. No WebSocket frame for that case.

**Trade-offs.**
- Safari < 15.4 has no BroadcastChannel — fallback to localStorage
  `storage` events (still zero-server, slightly more code).
- Different browsers don't see each other's events — that's
  intentional, and Reverb picks up that 5%.

**Fallback safety:** If `BroadcastChannel` API not present in
browser, JS module falls back to `localStorage`-driven `storage`
events; same semantics. Production-grade, no risk-bearing code on
either side.

**Reversible?** Yes — feature flag `resumes.use_broadcast_channel` (default ON at v2.5).
