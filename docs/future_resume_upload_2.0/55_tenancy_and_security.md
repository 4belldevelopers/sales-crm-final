# 55 — Tenancy & security

> *Intent: encode the rules that prevent tenant leak, file injection,*
> *and rate-limit evasion. Most of this is from CLAUDE.md rules 3 and 5*
> *+ the v1 SECURITY.md. The "parser abuse" subsection was REMOVED on*
> *2026-07-27 — v2 has no server-side parser; the browser-side*
> *`pdf.js` runs entirely from the user's `File` object and never*
> *leaves the browser.*

## Tenancy (CLAUDE.md rule 3)

**Eloquent is tenant-scoped by `company_id`. Raw `DB::table()` is NOT.**

For v2:

1. Every `ResumeUpload` Eloquent query goes through `withGlobalScope`
   patterns or explicit `->where('company_id', $cid)`. NO `Resume::all()`.
2. Every `Redis` key prefixes with `c{company_id}:` BEFORE the business
   key. A line in the contributor songbook: "no company prefix = CLOSED".
3. Every Reverb channel includes company id. The auth callback checks
   `Auth::user()->company_id === $channelName_company_id`. ELSE 403.
4. R2 key: `temp/{year}/{month}/{company_id}/...` and final
   `final/{year}/{month}/{company_id}/...` — the `company_id` is binding
   for analytics + ops sweep.
5. ES index alias: `candidates-{company_id}-v2`. Different `index` setting
   per tenant → no cross-tenant search; respects marketplace v2.

## File validation

### On POST `…/resume-sessions`

| Check | Where | Failure mode |
|---|---|---|
| `idempotency_key` (per-request random UUID via `crypto.randomUUID()`) | server | 400 if missing/malformed |
| `mime_type` ∈ allowed (PDF/DOC/DOCX) | server | 422 |
| `file_size_bytes` ≤ 6 MB | server | 422 |
| `sha256_hex` (sanity-check field; client-stated, server re-verifies at HEAD; NOT used as idem key) | server | mismatch logged, not 422 — idem key is independent (ADR-002, INV-4) |
| filename length 1..128 | server | 422 |

### On R2 PUT

R2 itself can be configured for content-type restrictions per bucket
prefix; we set this on bucket creation (deployment-time concern).

### Defense in depth

| Layer | Purpose |
|---|---|
| Max file size at presign (CAP) | prevent oversized upload |
| SHA256 partial-stream verification | stop scan attacks |
| Filename character sanitizer | strip `..`, control chars, ASCII-only |
| R2 lifecycle rule | temp files expire automatically after 30 d |
| Conditional DELETE on Cancel | no-op if already `Cancelled` |

## Parser security — REMOVED 2026-07-27

> v2 has no `ResumeParserService` and no `ChatGptService`. There is no
> LLM call, no extraction server-side, no `extract-only, not learn-from`
> contract to enforce. The single parser is browser-side (`pdf.js` +
> `re_test()`) and runs entirely from the user's `File` object —
> nothing crosses the network.
>
> The remaining "defense in depth" controls (filename sanitizer,
> length 1..128, MIME allowlist, file size cap, SHA-256 sanity-check,
> R2 lifecycle) are already covered by the **File validation**
> section above.

## Reverb channel auth

```php
Broadcast::channel('resume.{companyId}', function ($user, $companyId) {
    return $user && (int) $user->company_id === (int) $companyId;
});
```

This `int` cast is critical — string matches are a known bypass vector.

If wrong-tenant subscribe attempt: 403 + metric increment.

## Rate limits

Rate-limit middleware (Redis): `throttle:30,1` per user +
custom `company_throttle:120,1` per company. Both keyed by tenant.

`company_id` is REQUIRED in middleware — if missing, 401.

## Logging

Structured logging via `ResumeLogContext` (carried forward from v1
Phase 6) plus Monolog processor reaching into Redis rate-limit
counters via `Log::info('jobs.resume.security.throttled', [...])`.

## Secrets

`/etc/nginx/.../testing.jero.works.conf` keeps server-side
credentials behind chmod 0644 owned by `root:root`. Secrets are
loaded via Laravel's `config/` package; NEVER via raw `env()` outside
of `config/*.php` (CLAUDE.md rule 5).

We adopt `config('services.cloudflare.r2.key')` etc. rather than
`env(...)` in `app/Services/...` so `php artisan config:cache` works
in production.

## Known landmines respected

From `docs/BUG_REPORT.md`:
- 1,227 unescaped `{!! !!}` Blade outputs: v2 audit input (`file_hash`,
  `original_filename`) ever printing must use `e()`. Test file
  `test_view_does_not_use_unbound_blade_for_resume_fields`
- 63 `dd()` debug calls: v2 routes skip these (won't register under
  any path matching `^dd$`).
- `JobApplication` tenant scope commented out: v2 does NOT use
  `JobApplication` directly — only the new `ResumeUpload` model +
  scoped queries.
