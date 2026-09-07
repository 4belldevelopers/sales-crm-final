# ADR 0007 — Blocking-scenarios architecture (session epoch, Reverb kick, no `refresh:true`, `SyncUserStatusAction`)

## Status

Active (proposed). Code not yet landed. Locked during the 2026-08-06 review pass on `docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md`.

## Context

JERO has 15 distinct code paths that flip `users.status`, `users.working_status`, or `companies.status` — user-block-by-company (3 controllers + 1 bulk path), company-block-by-superadmin (3 methods), coin-depletion (cron), self-deactivate (timed, console command not scheduled), profile self-delete, super-admin impersonation, payment-failure stubs, and more. They all share four problems:

1. **No real-time kick.** Zero broadcasts in any blocking path. Affected users stay logged in until their next HTTP request.
2. **No audit trail.** Zero `audit_logs` table, zero `blocked_by`/`blocked_at` columns, zero `Log::info` calls. Super-admin actions are anonymous.
3. **Inconsistent ES sync.** ServiceCompany `statusUpdate` syncs ES; ProductCompany/StaffingCompany `teamDelete` do not. `checkActive` uses `updateByQuery refresh:true`, which is a self-DoS vector at 5,000-user scale.
4. **Per-user iteration in the hot path.** The "block the company" handler iterates per user, doing N Redis writes + N ES writes + N session destroys — defeats any chance of staying O(1) at 10,000-company / 100M-record scale.

The 2026-08-06 review pass on `BLOCKING_SCENARIOS_AUDIT.md` locked 4 architectural choices that address all four problems.

## Decision

### 1. Session Epoch pattern (replaces per-user iteration)

A session epoch is a single timestamp stored in Redis under one of two keys:

- `Cache::put("user:epoch:{userId}", $ts, 86400 * 7)` — set when a single user is blocked.
- `Cache::put("company:epoch:{companyId}", $ts, 86400 * 7)` — set when an entire company is blocked.

The middleware `EnsureOneDeviceLogin` (already exists; registered but not wired) reads both keys in O(1) and compares to the user's `session('login_timestamp')`. Any session whose `login_timestamp < epoch` is logged out and redirected on next request.

**One Redis write locks all sessions for a 5,000-user company.** No iteration, no per-user Redis writes, no per-user session destruction in the hot path.

TTL is 7 days, which matches the natural `users.session_id` mismatch detection window already in place.

### 2. Reverb broadcast for instant kick (replaces lazy cleanup)

A new `App\Events\UserAccountSuspended` event fires from each state-flip point. Two channel variants:

- **Single-user block:** `broadcast(new UserAccountSuspended(userId, companyId, newStatus, companyBlocked: false))->toOthers()` on `PrivateChannel('App.Models.User.{userId}')`. Reuses the channel already declared in `routes/channels.php`.
- **Company-wide block:** `broadcast(new UserAccountSuspended(userId: null, companyId, newStatus, companyBlocked: true))->toOthers()` on `PrivateChannel('company.{companyId}.dashboard')`. Reuses the existing channel; listeners filter on `e.userId === CURRENT_USER_ID` in JS.

The event class mirrors `App\Events\OperationalCoinUpdated.php:20` (closest precedent in the codebase). Frontend listener wiring is **out of scope for V2_login** — see `BLOCKING_SCENARIOS_AUDIT.md` §9.

The affected user's open tab gets kicked within ~50 ms via WebSocket — no waiting for next HTTP request.

### 3. Drop `refresh:true` everywhere + grep-lint guard

`'refresh' => true` on an Elasticsearch `update` / `updateByQuery` forces an immediate shard refresh — tens to hundreds of ms per shard. For bulk operations under load, this is the leading cause of ES cluster saturation and `SuperAdmin UI` freezes.

**20 sites across 9 files** set `refresh: true` today. All 20 sites drop the flag; natural 1-second background refresh is sufficient. The single exception is the `BlockCompanyAction` company-doc write, which uses `refresh=wait_for` (waits for the next refresh cycle but does not force one) so the marketplace `companies.status` filter reflects within the same request.

A new GUARD 3 in `scripts/ai-guards.sh` fails the build if any new `'refresh' => true` ES call is introduced anywhere except the whitelisted reference comment in `app/Jobs/ProcessOperationalCoinDeductionsJob.php:15,259,282`. The guard runs in pre-commit and CI.

### 4. Consolidated `SyncUserStatusAction` + `BlockCompanyAction`

Two Action classes become the **only** state-flip entry points across the codebase:

```php
// app/Actions/Auth/SyncUserStatusAction.php — single-user block
$action->execute($targetUserId, $status, $workingStatus);
// → 1. DB::table('users')->where('id', $targetUserId)->where('company_id', $actor->company_id)->update([...])
//   abort_if($affected !== 1, 403)  ← cross-tenant guard
// → 2. SyncUserStatusEsJob::dispatch($targetUserId)
// → 3. Cache::put("user:epoch:{$targetUserId}", now()->timestamp, 86400 * 7)
// → 4. BlockLog::create([...])
// → 5. broadcast(new UserAccountSuspended(...))->toOthers()
```

```php
// app/Actions/Auth/BlockCompanyAction.php — bulk company block (static)
BlockCompanyAction::execute($companyId, $newStatus, $actorUserId, $blockType);
// → 1. DB::table('users')->where('company_id', $companyId)->where('working_status', 1)->update([...])
// → 2. Single ELASTIC_USERS_LIST updateByQuery (no refresh:true)
// → 3. Single ELASTIC_COMPANIES_INDEX updateByQuery (refresh=wait_for)
// → 4. Cache::put("company:epoch:{$companyId}", ...)
// → 5. broadcast(new UserAccountSuspended(...))->toOthers()
// → 6. BlockLog::create([...])
```

Both classes use `Illuminate\Http\Request` for the actor and `Illuminate\Support\Facades\DB`, `Cache`, `Illuminate\Support\Facades\Broadcast` directly. The Actions live at `app/Actions/Auth/` (plain invokables, no `lorisleiva/laravel-actions` package).

The Action's `abort_if($affected !== 1, 403, 'cross_tenant_or_missing_user')` closes the unguarded bulk-`teamDelete` write path that allowed cross-tenant mass-inactivation.

The `SyncUserStatusEsJob` wraps the existing `app/Helper/GeneralMethod.php:9235` `userIndexingupload()` helper with `ShouldQueue` + `$tries = 5` + `$backoff = [10,30,60,120,300]` on the `es-index` queue — same shape as `ElasticCandidateIndexJob`.

## Alternatives considered

- **Iteration-based force-logout (rejected):** for each affected user, call `Auth::logout()` + `session()->getHandler()->destroy()`. At 5,000 users this is 5,000 Redis ops + 5,000 destroys — defeats the O(1) goal.
- **Per-user Reverb broadcast on company-block (rejected):** loop through affected users and fire one event each. More precise but N broadcasts for an N-user company.
- **MySQL column `companies.session_epoch` (rejected):** durable across Redis flush, but every middleware check now does a MySQL read per request — defeats the O(1) read goal.
- **`UserBlocked` / `UserKickedFromSession` event name (rejected):** duplicates `StageMoved` and `OperationalCoinUpdated` patterns; picked `UserAccountSuspended` because it accurately describes both single-user and company-block variants.
- **Drop `block_log` in favor of `activity_log` (rejected):** no existing audit package in the codebase. Spatie's `laravel-activitylog` would add a dependency for one table; minimal `BlockLog` model is cheaper.
- **External Action library (rejected):** `lorisleiva/laravel-actions` not installed; plain invokables are simpler.
- **Cascade session destroy in `EnsureOneDeviceLogin` (rejected):** the middleware should detect and redirect, not destroy. Cleanup is lazy via existing `users.session_id` mismatch logic.

## Consequences

### Positive

- One Redis write per blocking event, regardless of company size.
- One Reverb broadcast per blocking event.
- One audit-log row per blocking event.
- Cross-tenant guard closes the bulk-`teamDelete` footgun.
- ES cluster stops being saturated by `refresh:true` cascades.
- All blocking paths are auditable for the first time.
- The Action class deduplicates 4 near-identical `statusUpdate` methods.

### Negative

- `EnsureOneDeviceLogin` middleware becomes load-bearing for every authenticated request. Any bug in the epoch comparison has high blast radius. Mitigation: keep the existing `$user->status !== 'active'` check first; epoch comparison runs second.
- Epoch keys live in Redis. A Redis flush loses them — but Redis already backs sessions, so a flush already logouts everyone. No new failure mode.
- Frontend Echo listener wiring is deferred (audit §9). Until then, users don't see the 50-ms kick — they still see the lazy cleanup on next request.
- Two pending product questions remain (`IMPLEMENTATION_PLAN.md` Appendix A): hard-logout vs. soft-redirect (Q1) and free-text vs. typed `reason` (Q2). Defaults documented.

### Neutral

- `users.session_id` semantics unchanged. The epoch is an additional layer, not a replacement.
- Existing broadcast events (`StageMoved`, `OperationalCoinUpdated`, etc.) untouched.
- Channel declarations in `routes/channels.php` reused, not added to.

## Implementation order

Per `IMPLEMENTATION_PLAN.md` Phase 3:

1. **3.3** Foundations — `SyncUserStatusAction`, `BlockCompanyAction`, `UserAccountSuspended` event, `SyncUserStatusEsJob`, `block_log` migration, `BlockLog` model.
2. **3.4** Epoch gate in `EnsureOneDeviceLogin` + `login_timestamp` in `LoginController`.
3. **3.5** Wire `SyncUserStatusAction` into ServiceCo + ProductCo + StaffingCo `statusUpdate` and `teamDelete`.
4. **3.6** Wire `BlockCompanyAction` into `SuperAdminCompanyController::checkActive`, `checkStatusOperational`, `checkStatusSales`.
5. **3.7** `refresh:true` lint guard in `scripts/ai-guards.sh`.
6. **3.8** `superadmin-bulk-ops` rate limiter.

Plus **Phase 6.4** (`block_log`) and **Phase 6.5** (scheduler entries for `delete-account` and `licence-expire`).

## References

- `docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md` — companion audit (42+ findings, §1–§9)
- `docs/V2_login/IMPLEMENTATION_PLAN.md` — Phase 3.3–3.8, Phase 6.4/6.5, Appendix A
- `docs/V2_login/BEFORE_AFTER_COMPARISON.md` — §16 blocking-scenarios delta
- `docs/adr/README.md` — ADR index
- `app/Helper/GeneralMethod.php:9235` — `userIndexingupload()` helper (existing reuse)
- `app/Events/OperationalCoinUpdated.php:20` — closest event-class precedent
- `app/Jobs/ElasticCandidateIndexJob.php:16` — closest job-class precedent
- `routes/channels.php` — channel registry (`App.Models.User.{id}`, `company.{companyId}.dashboard`)