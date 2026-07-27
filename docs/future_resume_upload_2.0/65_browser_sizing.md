# 65 — Browser sizing

> *Intent: name exactly what the BROWSER does for 10K concurrent users, so*
> *we don't blame the server when the issue is local.*

## 10K concurrent = 10K tabs (worst case)

Most recruiters have 1-3 tabs. Power-user: 8 tabs. Pathological 10K
concurrent = ~1.25K browsers each with up to 8 tabs.

Per-tab footprint:

| Resource | v2 budget |
|---|---|
| DOM nodes | ≤ 500 on apply page (existing constraint) |
| localStorage | ≤ 4 KB |
| WebSocket connections | 1 (Reverb) |
| Event listeners | ≤ 25 |
| Memory | ≤ 8 MB sustained |
| CPU while idle | ≤ 0.5% |
| Memory under upload + parse | ≤ 30 MB |

## localStorage key budget (cross-tab ok)

| Key | Tenant | Size budget | TTL |
|---|---|---|---|
| `resume_session_uuid_{company_user_id}` | per (company, user) | 64 bytes | session |
| `resume_idempotency_key_{check_name}` | per (company, user) | 256 bytes | session |
| `resume_epoch_token_{file_hash}` | per file | 16 bytes | until upload done |
| `last_candidate_id` | per user | 16 bytes | until next add |

Total ≤ 4 KB. Cross-tab storage events drive UI re-sync.

## WebSocket budget

Each tab holds 1 Reverb connection. Server-side: 1 connection per
`(user, company, tab)` triple. Per-tenant cap: 5 outstanding
connections (config-driven). On approaching: oldest connection FCFS
gracefully pops.

## Bootstrap time

The four v2 JS modules (`resume-upload-progress.js`,
`resume-populate-if-empty.js`, `resume-presign.js`,
`resume-retry-manager.js`) are loaded:

| Module | When | Bytes (gzipped target) |
|---|---|---|
| `resume-presign.js`       | before "Choose File"  | ≤ 4 KB |
| `resume-upload-progress.js` | on file drop         | ≤ 8 KB |
| `resume-populate-if-empty.js` | on parser broadcast | ≤ 6 KB |
| `resume-retry-manager.js`  | on submit            | ≤ 3 KB |

Total scratch ≤ 21 KB gz (excluding pdfjs/mammoth, which already
plumb and are not added by v2).

## Failed-build fallback

If a module fails to load (CDN outage), Blade renders `data-nojs` on
the form, applies fallback synchronous multipart upload to v1
`/uploadresume`. **No data loss.** v1 path is retained for a
maximum of 6 months after v2 GA, then deprecated.

## Browser compatibility

| Browser | Min version |
|---|---|
| Chrome | 88 (SubtleCrypto support) |
| Firefox | 78 |
| Edge | 88 |
| Safari | 14 |

Polyfill: `@oddbird/css-has-selectors` and `crypto.subtle.digest`
fallback (≤ 1 KB).

## Mobile

Mobile (iOS 14+, Android 9+): Reverb fallback to HTTP polling at
1 Hz. Mobile is long-tail; doesn't drive 10K.

## Telemetry

Telemetry is scoped per the privacy contract in
[`55_tenancy_and_security.md`](55_tenancy_and_security.md). We do
**not** track DOM-level metrics; only upload/parse progress and
network events.

## Performance budget

| Metric | Target | Hard cap |
|---|---|---|
| Time-to-Interactive (apply page) | ≤ 1.5 s | 3 s |
| Click-to-ProgressBar (after file pick) | ≤ 250 ms | 500 ms |
| Upload complete → UI ready | ≤ 1 s | 2 s |
| Parser prefill completion | ≤ 10 s | 30 s |
| Submit click → success response | ≤ 1 s | 3 s |
