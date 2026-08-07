# Blocking Scenarios Audit — Login & Session Lifecycle

**Companion to:** [`AUDIT_REPORT.md`](AUDIT_REPORT.md), [`IMPLEMENTATION_PLAN.md`](IMPLEMENTATION_PLAN.md), [`BEFORE_AFTER_COMPARISON.md`](BEFORE_AFTER_COMPARISON.md)
**Date:** 2026-08-06 (initial audit); refined 2026-08-06 after 4 GOD-level architectural refinements.
**Scope:** All code paths that suspend, deactivate, delete, or impersonate a user or company — and the downstream effects on login, sessions, Elasticsearch, and real-time client state.

This audit was triggered by a single observation: a user blocked by their company, or a company blocked by Superadmin, **stays logged in** until they make another HTTP request. There is no real-time kick. We map every blocking path in JERO and quantify the gaps.

### Decisions resolved during review (locked 2026-08-06)

These three architectural choices were resolved during the refinement pass and are baked into §5:

1. **Epoch storage = Redis-only via `Cache::put`.** Keys `company:epoch:{companyId}` and `user:epoch:{userId}`, TTL = 7 days. Loses state on Redis flush — acceptable because Redis already backs sessions, so a flush already logouts everyone.
2. **Action class lives at `app/Actions/Auth/SyncUserStatusAction.php`.** Plain invokable; no `lorisleiva/laravel-actions` needed. Mirrors `SyncUserStatusEsJob` and `BlockCompanyAction`.
3. **Company-block broadcast channel = `company.{companyId}.dashboard`.** Reuses the existing channel declared in `routes/channels.php`; listeners filter on `e.userId === CURRENT_USER_ID` in JS. Single-user block broadcasts on `App.Models.User.{id}`.

The full refinement discussion is captured in [`.claude/plans/memoized-stargazing-sutherland.md`](../../.claude/plans/memoized-stargazing-sutherland.md).

---

## Executive Summary

| # | Scenario | DB write | ES sync | Sessions killed | Real-time kick | Audit log | Severity |
|---|----------|----------|---------|-----------------|----------------|-----------|----------|
| A | User blocked by company (ServiceCo single) | yes | yes (sync, 1 round-trip) | **NO** | **NO** | **NO** | **P0** |
| B | User blocked by company (ProductCo / StaffingCo) | yes | **NO** (drift) | NO | NO | NO | **P0** |
| C | Bulk team-block (`teamDelete`) | yes | **NO** (drift) | NO | NO | NO | **P0** |
| D | Company blocked by Superadmin (`checkActive`) | yes | yes (`updateByQuery`, `refresh:true`) | **NO** | **NO** | **NO** | **P0** |
| E | Company package-block (`checkStatusOperational` / `Sales`) | yes | **NO** (drift) | NO | NO | NO | **P0** |
| F | Coin-depletion user suspension (cron) | yes | yes | NO (soft block via `CustomPackage`) | NO | NO | P1 |
| G | Self-deactivate company (timed) | yes | NO | indirect via `EnsureOneDeviceLogin` next request | NO | NO | P1 |
| H | Profile self-delete (`ProfileController::destroy`) | hard delete | NO (orphans ES doc) | self only | NO | NO | P1 |
| I | Super-admin impersonation (`loginAsCompany`) | implicit session swap | NO | yes (prior admin only) | NO | **NO** | P1 |
| J | Failed-login lockout | none | n/a | n/a | n/a | n/a | **P0** (no protection) |
| K | Email-verification gate | none | n/a | n/a | n/a | n/a | **P0** (no protection) |
| L | Razorpay `payment.failed` | none (stub) | none | no | no | no | P1 |
| M | Stripe `invoice.payment_failed` | none (not handled) | none | no | no | no | P1 |
| N | `DeleteAccount` cron | yes when run | NO | indirect | NO | NO | P1 (not scheduled) |
| O | `LicenceExpire` cron | none (notify-only) | n/a | n/a | n/a | n/a | P2 (dead code) |

**The pattern across all 15 scenarios:** MySQL writes happen; ES is sometimes synced (often inconsistently); **no path kills the user's open session in real time**; **no path broadcasts a kick event**; **no path writes an audit record**. A blocked user keeps working until they reload, hit a middleware redirect, or their session expires naturally.

---

## 1. User blocked by company

### Trigger paths

| Endpoint | File | Method | Notes |
|----------|------|--------|-------|
| `team/statusUpdate` | `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php:2074-2099` | `statusUpdate(Request)` | Flips `users.status` AND `users.working_status` |
| `team/statusUpdate` | `app/Http/Controllers/ProductCompany/ProductCompanyTeamController.php:1854-1882` | `statusUpdate(Request)` | **Only flips `status`** — leaves `working_status` stale. **Does NOT call `userIndexingupload`.** |
| `team/statusUpdate` | `app/Http/Controllers/StaffingCompany/StaffingCompanyTeamController.php:1752-1780` | `statusUpdate(Request)` | Byte-identical to ProductCompany variant |
| `team/teamDelete` (bulk) | `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php:111-142` | `teamDelete(Request)` | Mass update; **no `company_id` scope** (cross-tenant write risk); no ES sync |

Routes:
- `routes/service.php:481` — `team/statusUpdate`
- `routes/product.php:234`, `routes/web.php:755` — ProductCompany variants
- `routes/staffing.php` (analogous) — StaffingCompany variant
- `routes/web.php:1349` — Admin's `AdminTeamController@statusUpdate`
- `routes/superadmin.php:140` — SuperAdmin's `SuperAdminTeamController@statusUpdate`

### Database behavior

**ServiceCompany (most complete) at lines 2080-2087:**
```php
// chkStatus == 'true'  → status='active',  working_status=1, auto_date_team=Carbon::today()
// chkStatus == 'false' → status='inactive', working_status=0
DB::table('users')
    ->where('company_id', auth()->user()->company_id)
    ->where('id', $request->userId)
    ->update([...]);
```

**ProductCompany / StaffingCompany (lines 1862-1878 / 1760-1776):** flips only `status`. **Inconsistent with ServiceCompany.**

`UserObserver` does not watch `status` or `working_status` changes (see `app/Observers/UserObserver.php:11-23` — only mutates `company_id` on save and clears old profile image on update). ES sync is therefore **explicit, not automatic**.

### Elasticsearch sync

`GeneralMethod::userIndexingupload($userId)` — `app/Helper/GeneralMethod.php:9235-9339`:
- Called synchronously from `ServiceCompanyTeamController.php:2089` after the MySQL write.
- Uses single-doc `update` API with `doc_as_upsert => true` and `retry_on_conflict => 5`.
- **No `refresh: true`** — so for up to 1 second after the toggle, the blocked user still appears as `active` in app-tracker searches.
- All 28 user fields re-sent inline on the request thread.

**Gaps:**
- ProductCompany / StaffingCompany `statusUpdate` **do not call `userIndexingupload`** — ES drifts.
- `teamDelete` (bulk) **does not call `userIndexingupload`** for any user in the batch — ES drifts wholesale.

### Session invalidation

**None.** The handler updates MySQL only.

- `users.session_id` is **never cleared** by any `statusUpdate` variant.
- No `Auth::logout()`, no `Session::destroy()`.
- The blocked user keeps their active tab working until next request.

**The downstream gate is `EnsureOneDeviceLogin` middleware** (`app/Http/Middleware/EnsureOneDeviceLogin.php:41-46`):
```php
if ($user->status !== 'active') {
    Auth::logout();
    return redirect()->route('login')
        ->withErrors(['device_message' =>
            'Your account has been suspended. To resolve this issue, please contact the administrator or send an email to support@jero.works for further assistance.'
        ]);
}
```
But this only fires on the next request — not the current tab.

### Real-time notification

**None.** Zero broadcasts in `ServiceCompany/`. The `UserBlocked` event does not exist. Compare with `StageMoved` event (`app/Events/StageMoved.php:16`) which has the channel pattern but has not been wired up for suspension.

### Edge cases & footguns

| # | Issue | File:Line | Severity |
|---|-------|-----------|----------|
| 1 | **Company root user CAN be blocked** — no `root_user === 1` guard. Admin can lock themselves out of their own dashboard. *(See "Pending product decision" footnote below this table — the `SyncUserStatusAction` does NOT add a root-user guard; that decision requires product sign-off.)* | `ServiceCompanyTeamController.php:2074-2099` | **High** |
| 2 | **`teamDelete` has no `company_id` WHERE clause** — cross-tenant write: Admin A's checkbox payload can include Admin B's user IDs and they get bulk-inactivated. | `ServiceCompanyTeamController.php:133` | **High** |
| 3 | **ES drift on ProductCompany/StaffingCompany** — `statusUpdate` writes MySQL but skips `userIndexingupload`. Blocked user keeps appearing in app-tracker candidate lists. | `ProductCompanyTeamController.php:1854` / `StaffingCompanyTeamController.php:1752` | **High** |
| 4 | **No ES sync in bulk `teamDelete`** — mass block leaves ES entirely stale. | `ServiceCompanyTeamController.php:133` | Medium |
| 5 | **No audit trail** — no `blocked_by`, `blocked_at`, `block_reason` columns on `users`; no `audit_logs` table. | schema-wide | Medium |
| 6 | **Idempotency on double-click** — AJAX at `service_company/team/index.blade.php:1135-1150` is not debounced. Two rapid clicks fire two ES writes; optimistic UI can flip-flop. | `index.blade.php:1135` | Medium |
| 7 | **No notification to the blocked user** — no email, no Reverb event, only the lazy flash from `EnsureOneDeviceLogin` on next request. | n/a | Medium |
| 8 | **`statusUpdate` vs `WorkingStatus` have different billing semantics** — blocking via `statusUpdate` does NOT decrement a paid seat; toggling via `WorkingStatus` (line 2006-2073) does. A company can block 100 users and still pay for 100 seats. | `ServiceCompanyTeamController.php:2019-2032` | Medium |
| 9 | **`'Avtive'` typo** in `$status_text` (returned in JSON, never rendered today). | `ServiceCompanyTeamController.php:2082, 2086` | Trivial |

---

## 2. Company blocked by Superadmin

### Trigger paths

`app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`:

| Method | Lines | Effect |
|--------|-------|--------|
| `checkActive(Request)` | 11261-11344 | Toggles `users.status` for ALL `working_status=1` users of the company. ES `updateByQuery` on both `ELASTIC_USERS_LIST` and `ELASTIC_COMPANIES_INDEX` with `refresh: true`. |
| `checkStatusOperational(Request)` | 11346-11361 | Flips `company_coins.free_pack_status` for `event_type='operational'`. **No ES sync.** |
| `checkStatusSales(Request)` | 11363-11380 | Same pattern for `event_type='sales'`. Inconsistent type coercion vs. operational variant (L11374 casts `(int)$request->id`, L11354 does not). |

Routes — `routes/superadmin.php`:
- L231 `POST super-admin/check-active` → `checkActive`
- L232 `POST super-admin/check-status-operational` → `checkStatusOperational`
- L233 `POST super-admin/check-status-sales` → `checkStatusSales`

Middleware: parent group is `auth` + `super-admin`. **No `throttle` middleware** on these specific routes.

### Database behavior — `checkActive`

```php
// L11263
$row = DB::table('users')
    ->where('company_id', $request->id)
    ->where('root_user', 1)
    ->first();

// L11267-11269: flip pack_status based on root user
$pack_status = ($row->status === 'active') ? 'inactive' : 'active';

// L11270: BULK write all working users of the company
$result = DB::table('users')
    ->where('company_id', $request->id)
    ->where('working_status', 1)
    ->update(['status' => $pack_status]);
```

**Critical:** does NOT write `companies.status` in MySQL. The companies table drifts; ES carries the truth only if `updateByQuery` succeeds.

### Elasticsearch sync — `checkActive`

Two back-to-back `updateByQuery` calls, both with `refresh: true`:

1. **`ELASTIC_USERS_LIST`** (L11272-11304): painless script updates `status` + `updated_at` for all docs matching `company_id=$id AND working_status=1`.
2. **`ELASTIC_COMPANIES_INDEX`** (L11306-11333): painless script updates `status` + `updated_at` for the single company doc matching `id=$id`.

**Performance impact for a 1,000-recruiter company:**
- Two `updateByQuery` calls.
- Each forces a shard refresh (`refresh: true`) — full segment refresh on the touched shard, typically tens to hundreds of ms per shard.
- Painless script execution per matched doc.
- For a 10-shard index with 1k user docs: easily **1-3 seconds of UI freeze** for the superadmin click.
- **At scale (superadmin mass-blocking 50 companies in a UI batch):** ES cluster saturation, search latency spike across all tenants.

### Session invalidation

**None.** `grep 'Auth::logout|Session::destroy|session_id|getHandler' app/Http/Controllers/SuperAdmin/` returns:
- `SuperAdminCompanyController.php:4725` — `Auth::logout();` inside `loginAsCompany()` (impersonation, not blocking).
- `CustomModuleController.php:96-97` — `Session::flush(); Auth::logout();` (unrelated).

`checkActive`, `checkStatusOperational`, `checkStatusSales` touch **none** of these. A blocked company's users keep their PHP session cookies valid until natural expiry. In-flight requests continue to succeed.

**De-facto session kill:** only via `EnsureOneDeviceLogin` middleware on the user's next request — which only runs if they hit a route protected by `auth` middleware. API endpoints not in the auth group remain accessible until session TTL.

### Real-time notification

**None.** `grep 'event(|->broadcast|Reverb|ShouldBroadcast' app/Http/Controllers/SuperAdmin/` returns zero matches. None of the three handlers fires any broadcast. Affected users' clients are silent.

### Root user vs. employees

The root user (`root_user=1`) is included in the bulk write at L11270 because `where('working_status', 1)` matches them too. The root user has no special protection.

### Marketplace implications

Bench candidate documents embed `companies.status` in nested form. `checkActive` does not invalidate them directly — `BenchApplicationObserver` (registered in `AppServiceProvider.php:36`) is the only path that re-indexes bench docs. The marketplace must filter on `companies.status='active'` at query time, otherwise stale bench docs surface for blocked companies.

### Audit / accountability

**None.** Repo-wide search for `audit_logs|activity_log|AuditLog|Auditable|spatie/laravel-activitylog` returns only:
- Unrelated matches in `ServiceCompanyJobApplicationController.php` (function names).
- Two documentation files in `docs/future_resume_upload_2.0/`.

No audit log table, no Laravel Auditing package, no `blocked_by` / `blocked_at` column on `users` or `companies`, no `Log::info` call inside any of the three handlers. A superadmin can block a company and there is **zero forensic record** of who did it, when, or why.

### Summary of defects

| # | Severity | Defect |
|---|----------|--------|
| 1 | **Critical** | No session invalidation when company is blocked — in-flight users stay logged in until natural expiry. |
| 2 | **Critical** | No audit trail — silent, anonymous superadmin action. |
| 3 | **High** | No rate limiting on the block endpoint; ES `refresh:true` on a 1k-doc updateByQuery is a self-DoS vector. |
| 4 | **High** | No real-time broadcast to affected users' clients. |
| 5 | **Medium** | `checkActive` writes `users.status` but never writes `companies.status` in MySQL — relies entirely on ES `updateByQuery` succeeding for marketplace consistency. |
| 6 | **Medium** | Observer-based ES sync is bypassed (`DB::table()` + manual painless). If ES call fails silently, MySQL is updated but ES keeps `status=active` — bench and company searches stay live. |
| 7 | **Low** | Inconsistent type coercion between `checkStatusOperational` and `checkStatusSales`. |

---

## 3. Other blocking scenarios

### 3.1 Package / plan expiration

**`LicenceExpire` console** (`app/Console/Commands/LicenceExpire.php:51-100`):
- Selects `CompanyPackage` rows where `end_date = today±1 day`.
- **DB writes:** none. Only dispatches `LicenseExpire` / `LicenseExpireBefore` notifications.
- **ES / sessions / kick:** none.
- **Scheduler:** **NOT WIRED in `Kernel.php`.** Command exists but will never run automatically.

**`AutoRenewFreePackage`** (`app/Services/AutoRenewFreePackage.php:36-258`):
- Called from `AutoCloseExpiredJobsJob`.
- Inserts fresh `coin_orders`, updates `company_coins.free_pack_status='active'`, ES-upserts `ELASTIC_COMPANY_COINS`.
- Does not flip `companies.status` or `users.status`.

### 3.2 Coin-depletion user suspension (cron)

**`ProcessOperationalCoinDeductionsJob`** (`app/Jobs/ProcessOperationalCoinDeductionsJob.php:88-217`):
- Runs every 5 minutes (scheduled in `Kernel.php`).
- For each user with `status='active'`, deducts from `company_coins.coins`.
- If balance covers → `users.working_status = 1`.
- If balance insufficient → `users.working_status = 0` (excluding `root_user=1`), ES-update user doc.
- **Sessions killed?** NO. `EnsureOneDeviceLogin` only checks `users.status`, not `working_status`. A user with `working_status=0` can navigate freely until hitting `CustomPackage` middleware redirect.
- **Real-time kick?** NO.

### 3.3 Self-deactivate company (timed)

**4 namespace copies** of `CompanySettingsController::deleteAccountStore` — `Admin`, `StaffingCompany`, `ServiceCompany`, `ProductCompany`:
- Writes `companies.delete_account_at = Carbon::now()->addDays($superSettings->delete_account_in)`.
- **Bug at L63/L67:** uses `=` instead of `==` — the `addHours(...)` branch is dead; always adds days.

**`DeleteAccount` console** (`app/Console/Commands/DeleteAccount.php:51-62`):
```php
$companies = Company::where('status', 'active')
    ->whereRaw("delete_account_at < '$currentDate'")
    ->whereNotNull('delete_account_at')->get();
foreach ($companies as $company) {
    $company->status = 'inactive';
    $company->save();
}
```
- **ES sync:** NONE.
- **Sessions killed:** indirectly via `EnsureOneDeviceLogin` next request.
- **Real-time kick:** NONE.
- **Scheduler:** **NOT WIRED.** The timed-delete feature is silently broken.

### 3.4 Profile self-delete

`ProfileController::destroy` (lines 43-59):
- Hard delete via `User::delete()` (no `deleted_at` column on `users`).
- Self session invalidate + regenerate.
- **ES orphan:** candidate / application ES docs remain with no link back to user.

### 3.5 Super-admin impersonation

`SuperAdminCompanyController::loginAsCompany` (L4721-4730):
```php
$admin = User::frontAllAdmins($companyId)->first();
Auth::logout();
Auth::loginUsingId($admin->id);
```
- Bumps `users.session_id` implicitly via `EnsureOneDeviceLogin` next request.
- Logs out the prior admin via `Auth::logout()`.
- **Audit trail:** NONE. No `impersonation_log` table, no `impersonated_by` column.
- **Reversible:** No "exit impersonation" route.

**Code generation:** `users.super_admin_login_code = company_id . rand(1111,9999)` set during registration at `SaasFront\SaasFrontController:1081, 1867, 2965, 3577`.

**Code consumption:** `LoginController.php:104-174, 586-666` — user types their code as "password"; bypasses standard auth; `Auth::loginUsingId`.

### 3.6 Failed-login lockout

**Schema check** (`mysql-schema.sql:4265-4296`): **NO** `failed_login_attempts`, **NO** `locked_until`, **NO** `banned_at` columns.

`LoginRequest` (`app/Http/Requests/Auth/LoginRequest.php:60-76`) has working `RateLimiter::tooManyAttempts($key, 5)` with 1-minute backoff — **but is not wired into the live `Auth\LoginController::login()`**.

`LoginController::login` (L70-256) does NOT check or increment any counter. Bad password just returns `redirect()->back()->withErrors(['email' => 'These credentials do not match our records.'])`. **No DB write, no session-side delay, no IP block.**

**Conclusion:** No real failed-login lockout exists today.

### 3.7 Email-verification gate

**Schema:** only `users.email_verification_code varchar(40)`. **NO `email_verified_at` column** (despite `ProfileController.php:32` writing to it — fails silently on save due to mass-assignment).

**Enforcement:** NONE. No controller checks `email_verified_at` before allowing login.

### 3.8 Razorpay / Stripe payment failure

**RazorpayWebhookController** (`app/Http/Controllers/Razorpay/RazorpayWebhookController.php`):
- L75 `paymentFailed` → `return;` — **no-op stub**.
- L83 `subscriptionCancelled` → `return;` — **no-op stub**.
- Only `subscription.charged` does work.

**StripeWebhookController** (`app/Http/Controllers/Stripe/StripeWebhookController.php`):
- L52-101 handles `invoice.payment_succeeded`.
- **No `invoice.payment_failed` / `customer.subscription.deleted` handler.**

Failed payments do NOT suspend companies. They retain `status='active'` until manual admin intervention or coin-balance drain.

---

## 4. Cross-cutting findings

### 4.1 Zero real-time kicks across all 15 scenarios

Not a single blocking path in JERO broadcasts a Reverb event to the affected user. The "kick" is always lazy — fired on the user's next HTTP request by `EnsureOneDeviceLogin`.

### 4.2 Zero audit trails across all 15 scenarios

No `audit_logs` table, no Laravel Auditing, no `blocked_by` / `blocked_at` columns. Super-admin actions and company-admin actions are anonymous.

### 4.3 Inconsistent ES sync

| Sync path | ES updated? | refresh? | Bulk? | Fixed in plan |
|-----------|-------------|----------|-------|---------------|
| ServiceCompany `statusUpdate` (single) | yes | no | no | Phase 3.5 → `SyncUserStatusAction` |
| ProductCompany `statusUpdate` (single) | **NO** | n/a | n/a | Phase 3.5 → `SyncUserStatusAction` (closes drift) |
| StaffingCompany `statusUpdate` (single) | **NO** | n/a | n/a | Phase 3.5 → `SyncUserStatusAction` (closes drift) |
| `teamDelete` bulk | **NO** | n/a | n/a | Phase 3.5 (Action's cross-tenant guard rejects cross-tenant IDs) |
| `SuperAdminCompanyController::checkActive` | yes | **yes** | yes (`updateByQuery`) | Phase 3.6 → `BlockCompanyAction` (drops `refresh:true`) |
| `checkStatusOperational` | **NO** | n/a | n/a | Phase 3.6 → `BlockCompanyAction` (no ES sync — only company_coins row + epoch) |
| `checkStatusSales` | **NO** | n/a | n/a | Phase 3.6 → `BlockCompanyAction` (same as operational) |
| Coin-deduction cron | yes | no | no | Already correct (no change) |
| `DeleteAccount` cron | **NO** | n/a | n/a | Phase 6.5 wires scheduler, but ES sync remains a separate task (out of V2_login scope) |
| `ProfileController::destroy` | **NO** (orphan) | n/a | n/a | Out of scope (self-delete path; needs separate ADR) |

After Phase 3.5 and Phase 3.6 land, all 5 user-blocking paths (3 statusUpdate + teamDelete + checkActive) reduce to **one Action class** with **one ES sync path** (`SyncUserStatusEsJob` → `GeneralMethod::userIndexingupload()`), one audit-log row per event, one Redis `Cache::put` per blocking event, and one Reverb broadcast.

### 4.4 Schema gaps

The `users` table is missing every column that would support the blocking scenarios properly:

| Missing column | What it would enable |
|----------------|---------------------|
| `users.deleted_at` | Soft-delete team members instead of cascading hard-deletes |
| `users.failed_login_attempts` | Real lockout after N bad passwords |
| `users.locked_until` | Time-bound lockout with auto-expiry |
| `users.banned_at` | Hard ban flag distinct from `status='inactive'` |
| `users.blocked_by` | Audit trail for who blocked |
| `users.blocked_at` | Audit trail for when |
| `users.block_reason` | Compliance / support context |
| `users.last_active_at` | Real-time online indicator |
| `users.email_verified_at` | Verified-email gate |

---

## 5. Recommended remediation (login-version scope)

These tie directly into the existing [`IMPLEMENTATION_PLAN.md`](IMPLEMENTATION_PLAN.md). Each is sized for the V2_login slice; larger audit / observability investments belong in a separate ADR.

### 5.1 `SyncUserStatusAction` — single source of truth for status flips (Phase 3.2)

Centralize the cascade so every blocking handler — user-block, company-block, coin-depletion, self-delete, profile-delete, impersonation — uses the same path. Replaces the older `SessionKillService` iteration with an O(1) epoch-key write plus a single Reverb broadcast.

```php
// app/Actions/Auth/SyncUserStatusAction.php
namespace App\Actions\Auth;

use App\Events\UserAccountSuspended;
use App\Jobs\SyncUserStatusEsJob;
use App\Models\BlockLog;
use Illuminate\Http\Request;

class SyncUserStatusAction
{
    public function __construct(private Request $request) {}

    public function execute(int $targetUserId, string $status, int $workingStatus): void
    {
        $actor = $this->request->user();
        abort_unless($actor, 401);

        // 1. Cross-tenant-scoped DB write — single UPDATE, asserts exactly 1 row affected.
        $affected = DB::table('users')
            ->where('id', $targetUserId)
            ->where('company_id', $actor->company_id)
            ->update([
                'status'         => $status,
                'working_status' => $workingStatus,
                'updated_at'     => now(),
            ]);
        abort_if($affected !== 1, 403, 'cross_tenant_or_missing_user');

        // 2. Async ES sync to ELASTIC_USERS_LIST — Horizon queue handles retry/backoff.
        SyncUserStatusEsJob::dispatch($targetUserId);

        // 3. Bump the session-epoch in Redis (single O(1) write).
        Cache::put("user:epoch:{$targetUserId}", now()->timestamp, 86400 * 7);

        // 4. Audit-log row (minimal schema, no Laravel-Auditing dep).
        BlockLog::create([
            'target_user_id'     => $targetUserId,
            'target_company_id'  => $actor->company_id,
            'actor_user_id'      => $actor->id,
            'block_type'         => 'user_block',
            'created_at'         => now(),
        ]);

        // 5. Fire Reverb event — frontend listener redirects within 50 ms.
        broadcast(new UserAccountSuspended(
            userId:         $targetUserId,
            companyId:      $actor->company_id,
            newStatus:      $status,
            companyBlocked: false,
        ))->toOthers();
    }
}
```

**Wired into** (replaces the per-handler `DB::table('users')->update(...)` block in each):

- `ServiceCompanyTeamController::statusUpdate` at `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php:2074-2099`
- `ProductCompanyTeamController::statusUpdate` at `:1854-1882`
- `StaffingCompanyTeamController::statusUpdate` at `:1752-1780`
- `ServiceCompanyTeamController::teamDelete` at `:111-142` (called in a loop; the action's cross-tenant guard handles each row individually and rejects non-matching IDs with a 403)

The cross-tenant guard inside the Action (`abort_if($affected !== 1, 403, ...)`) replaces the unguarded `whereIn('id', $arr)->update(...)` call in the bulk `teamDelete` path — fixing the audit's "no `company_id` scope" footgun in one place.

#### §5.1.1 Company-block companion: `BlockCompanyAction`

For the SuperAdmin `checkActive`, `checkStatusOperational`, `checkStatusSales` paths in `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php:11261-11380`, a parallel static helper handles the bulk-user case without per-user iteration:

```php
// app/Actions/Auth/BlockCompanyAction.php
namespace App\Actions\Auth;

use App\Events\UserAccountSuspended;
use App\Models\BlockLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BlockCompanyAction
{
    public static function execute(int $companyId, string $newStatus, int $actorUserId, string $blockType): void
    {
        // 1. Single DB update for all working users of the company.
        DB::table('users')
            ->where('company_id', $companyId)
            ->where('working_status', 1)
            ->update(['status' => $newStatus, 'updated_at' => now()]);

        // 2. Single ELASTIC_USERS_LIST updateByQuery (NO refresh:true; see §5.5).
        // 3. Single ELASTIC_COMPANIES_INDEX updateByQuery — refresh:wait_for only here.
        //    (omitted for brevity; see §5.5 for the exact shape)

        // 4. ONE Cache::put for the company epoch — kills 5,000 users with 1 Redis write.
        Cache::put("company:epoch:{$companyId}", now()->timestamp, 86400 * 7);

        // 5. ONE broadcast on company.{companyId}.dashboard.
        broadcast(new UserAccountSuspended(
            userId:         null,
            companyId:      $companyId,
            newStatus:      $newStatus,
            companyBlocked: true,
        ))->toOthers();

        // 6. Single audit log row.
        BlockLog::create([
            'target_company_id' => $companyId,
            'actor_user_id'     => $actorUserId,
            'block_type'        => $blockType,  // 'company_block_active', 'company_block_operational', 'company_block_sales'
            'created_at'        => now(),
        ]);
    }
}
```

`checkActive` becomes a 6-line call. The superadmin UI does not freeze on a 5,000-user company because the per-user iteration loop is gone.

### 5.2 Epoch-gated session check in `EnsureOneDeviceLogin` (Phase 3.3)

Replace the older `force_logout_{userId}` cache-flag idea with an epoch comparison — a single timestamp rather than a separate flag key. Add to `app/Http/Middleware/EnsureOneDeviceLogin.php` immediately after the existing `$user->status !== 'active'` check at L41:

```php
// Existing line 41 area:
if ($user->status !== 'active') {
    Auth::logout();
    return redirect()->route('login')
        ->withErrors(['device_message' =>
            'Your account has been suspended. To resolve this issue, please contact the administrator or send an email to support@jero.works for further assistance.'
        ]);
}

// NEW — single O(1) Redis read per request; rejects any session that logged in before the company or user was blocked.
$companyEpoch = Cache::get("company:epoch:{$user->company_id}");
$userEpoch    = Cache::get("user:epoch:{$user->id}");
$sessionEpoch = $user->session_epoch ?? session('login_timestamp', 0);

if (($companyEpoch && $sessionEpoch < $companyEpoch)
    || ($userEpoch && $sessionEpoch < $userEpoch)) {
    Auth::logout();
    session()->invalidate();
    return redirect()->route('login')
        ->withErrors(['account' => 'Company access suspended.']);
}
```

`session('login_timestamp')` is set by the login flow on successful auth — a 2-line change in `LoginController::authenticated()` (called out as a prerequisite when the implementation phase begins; not in scope of this audit edit).

**Why an epoch over a force-logout flag:**
- One key per blocking event (`company:epoch:42` or `user:epoch:7`) instead of two (`force_logout_*` and the value carrier).
- Idempotent — repeat calls just overwrite the timestamp; no dangling flag to clear.
- TTL-based cleanup — keys auto-expire after 7 days, no manual invalidation needed.
- Lower middleware overhead — one read on `company:epoch:*` covers all users of the company; the older flag would have required reading a per-user key for every request anyway.

### 5.3 `block_log` audit table + `BlockLog` model (Phase 6.5)

A minimal, no-Laravel-Auditing alternative. The `BlockLog::create(...)` calls baked into `SyncUserStatusAction` (§5.1) and `BlockCompanyAction` (§5.1.1) make this table the canonical record of every blocking action:

```php
Schema::create('block_log', function (Blueprint $t) {
    $t->id();
    $t->unsignedBigInteger('target_user_id')->nullable()->index();
    $t->unsignedBigInteger('target_company_id')->nullable()->index();
    $t->unsignedBigInteger('actor_user_id');
    $t->string('block_type');        // user_block, company_block_active, company_block_operational,
                                      // company_block_sales, working_status_zero, self_deactivate,
                                      // payment_failed, impersonation, self_delete
    $t->string('reason')->nullable();
    $t->timestamp('created_at')->useCurrent();
});
```

Wired automatically once `SyncUserStatusAction` and `BlockCompanyAction` are the only state-flip paths. Additional wire-up required for:
- `ProcessOperationalCoinDeductionsJob` (when flipping `working_status=0`) — `app/Jobs/ProcessOperationalCoinDeductionsJob.php:121`
- `DeleteAccount` console — `app/Console/Commands/DeleteAccount.php:51-62`
- `SuperAdminCompanyController::loginAsCompany` (impersonation) — `:4721-4730`
- `ProfileController::destroy` (self-delete) — `:43-59`

### 5.4 ES sync unification (Phase 1.x extension)

All three controller paths (`statusUpdate` in `ServiceCompany/`, `ProductCompany/`, `StaffingCompany/`) now delegate to `App\Actions\Auth\SyncUserStatusAction` (§5.1). ES sync is automatic via `SyncUserStatusEsJob` — a queue-wrapped version of the existing `app/Helper/GeneralMethod.php:9235` `userIndexingupload()` helper using single-doc `update` with `doc_as_upsert=true` (already specified in IMPLEMENTATION_PLAN Phase 1.2). The Product/StaffingCompany ES-drift footgun resolves implicitly, because the Action is the **only** state-flip path and it always syncs. The bulk `teamDelete` path loops, calling the Action per row — the cross-tenant guard inside the Action rejects any non-matching `company_id` with a 403.

### 5.5 Remove `refresh:true` everywhere + grep-lint guard

`refresh:true` on Elasticsearch `update` / `updateByQuery` forces an immediate shard refresh — tens to hundreds of ms per shard. For bulk operations under load, this is the leading cause of ES cluster saturation and `SuperAdmin UI` freezes.

**Verified site list (2026-08-06 grep across `app/`):** 20 ES calls set `refresh=>true`. They are distributed across 9 files:

| File | Lines | Count |
|------|-------|-------|
| `app/Services/AutoCloseJobsService.php` | 236 | 1 |
| `app/Jobs/ProcessCandidateUpdateJob.php` | 341 | 1 |
| `app/Http/Controllers/HomeController.php` | 13578, 13624, 13671, 13882, 13914, 14805, 14860 | 7 |
| `app/Http/Controllers/SaasFront/SaasFrontController.php` | 305 | 1 |
| `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` | 11216, 11299, 11328 | 3 |
| `app/Http/Controllers/Auth/LoginController.php` | 225, 247, 374 | 3 |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | 13089, 13340, 15425 | 3 |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` | 4090 | 1 |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | 548 | 1 |
| **Total** | | **20** |

**Negative reference** (already correct): `app/Jobs/ProcessOperationalCoinDeductionsJob.php:15,259,282` — explicit comments stating "deliberately omit 'refresh' => true — 1-second natural refresh is sufficient."

**Recommended implementation:**

1. Drop `'refresh' => true` from all 20 sites — natural 1-second background refresh is sufficient.
2. Add a `scripts/ai-guards.sh` lint rule to keep `refresh:true` from creeping back in:

```bash
# scripts/ai-guards.sh — addition
! grep -rnE "'refresh'\s*=>\s*true" app/ \
    | grep -v "ProcessOperationalCoinDeductionsJob" \
    | grep -v "^Binary file" \
    | grep -vE "docs/|\.md:" \
    && { echo "ERROR: 'refresh' => true found in app/ (see §5.5 of docs/V2_login/BLOCKING_SCENARIOS_AUDIT.md)"; exit 1; }
```

3. For the single-company doc write in `BlockCompanyAction` (§5.1.1) **only**, use `refresh=wait_for` so the marketplace `companies.status` filter reflects within the same request — never `refresh=true`.
4. Pre-commit + CI: `bash scripts/ai-guards.sh` runs before commit (per AI_RULE §13). The grep rule fails the build if any new `refresh:true` site is introduced anywhere except the whitelisted comment in `ProcessOperationalCoinDeductionsJob`.

### 5.6 Rate-limit the superadmin block endpoints

Add to `routes/superadmin.php` parent group:
```php
->middleware(['throttle:superadmin-bulk-ops'])
```
And in `AppServiceProvider::configureRateLimiters()`:
```php
RateLimiter::for('superadmin-bulk-ops', function (Request $request) {
    return Limit::perMinute(10)->by('superadmin:'.$request->user()->id);
});
```
Prevents the self-DoS via `updateByQuery` cascade even after `refresh:true` is removed (the bulk operation itself is still heavy on a 5,000-user company).

### 5.7 Wire the dead consoles (Phase 6.6)

`app/Console/Kernel.php` must schedule:
- `delete-account` — daily at 02:00 server time
- `licence-expire` — daily at 03:00

Without this, both are silent dead code today. The cron dispatcher is referenced from `app/Console/Kernel.php:13-58` — `process-operational-coin-deductions` and other jobs are wired there, so it's the canonical place for these registrations.

### 5.8 Schema additions (separate migration, sign-off required)

Adding the columns in §4.4 is a **business-logic change** — flag for product sign-off per AI_RULE #8. Not in V2_login scope, but listed for the team planning cycle.

---

## 6. Verification matrix (post-fix)

After §5.1–5.7 land, every blocking path must satisfy:

| Check | Method |
|-------|--------|
| **Epoch gate (company-block):** a user logged in at `t=100` is rejected after `Cache::put("company:epoch:42", t=200)`, even if their PHP session is still valid | Unit test seeds `company:epoch:42 = 200`, dispatches a request through `EnsureOneDeviceLogin::handle()`, asserts 302 → `/login` |
| **Epoch gate (user-block):** a user is rejected after `Cache::put("user:epoch:7", t=300)` regardless of any `company:epoch` value | Same shape as above, with `user:epoch` instead of `company:epoch` |
| **Epoch TTL:** epochs auto-expire after 7 days (no dangling Redis keys) | Assert `Cache::get('company:epoch:42')` returns null after 7 days (use `Carbon::setTestNow`) |
| **`SyncUserStatusAction` cross-tenant guard** rejects non-matching `company_id` | Negative test: action called with target user from another tenant returns 403 via `abort_if($affected !== 1, ...)` |
| **Reverb `UserAccountSuspended` event** fires on company-block with `companyBlocked=true` | `Event::fake()` test asserts broadcast was dispatched on `PrivateChannel('company.{companyId}.dashboard')` with correct payload |
| **Reverb `UserAccountSuspended` event** fires on single-user block with `userId` set | Same shape as above, channel becomes `PrivateChannel('App.Models.User.{id}')` |
| **`BlockCompanyAction()` is O(1) Redis writes** regardless of company size | Load test: 5,000-member company → assert `Cache::put` called exactly once on `company:epoch:*`, broadcast fired exactly once, audit log has 1 row |
| All 20 `refresh:true` ES sites are removed | `bash scripts/ai-guards.sh` exits 0; `grep -rnE "'refresh'\s*=>\s*true" app/` returns only the whitelisted `ProcessOperationalCoinDeductionsJob` comment lines |
| Blocked user gets Reverb event within 100 ms | `php artisan test --filter=KickBroadcastTest` |
| Blocked user's open tab is bounced to `/login` within 1 s of broadcast | Playwright e2e: open 2 tabs as user → admin blocks → assert both tabs redirect |
| Block action writes a row to `block_log` with correct actor | `assertDatabaseHas('block_log', ['actor_user_id' => ...])` |
| ES `ELASTIC_USERS_LIST` reflects new `status` within 1 s (no `refresh:true`) | `curl :9200/users/_search?q=status:inactive` |
| `teamDelete` cannot write across tenants (negative test) | Test sends user IDs from another company; asserts `abort(403)` from inside `SyncUserStatusAction` |
| Root user cannot be blocked by their own company admin | Test asserts `affected === 1` only when root user is omitted; negative test confirms 403 on root-user block attempt |
| Superadmin `checkActive` capped at 10 / min | Load test; assert 11th request returns 429 |

---

## 7. Open questions for product sign-off

1. Should blocking a company hard-logout all current users, or soft-redirect to a "suspended" page? (Different UX, different compliance posture.)
2. Should blocked users see a real-time "you've been suspended" notification, or should the kick be silent? (Privacy / phishing surface.)
3. Should the audit log retain `reason` text from admin, or be limited to typed categories?
4. Should Razorpay `payment.failed` auto-suspend, or stay a manual superadmin action? (Risk: false-positive card declines.)
5. Should failed-login lockout be 5 / 15 / 30 minutes? (Current `LoginRequest` default is 5 attempts / 1 minute.)

These are **business decisions** — flagging them per AI_RULE #8. Not implemented in the V2_login slice.

---

## 8. `refresh:true` ES site inventory (2026-08-06)

Full grep result for `'refresh'\s*=>\s*true` across `app/` — kept here as a scannable list separate from §5.5's narrative.

| # | File:Line | Context |
|---|-----------|---------|
| 1 | `app/Services/AutoCloseJobsService.php:236` | ES update on job close |
| 2 | `app/Jobs/ProcessCandidateUpdateJob.php:341` | ES candidate update |
| 3 | `app/Http/Controllers/HomeController.php:13578` | ES update (god controller) |
| 4 | `app/Http/Controllers/HomeController.php:13624` | ES update (god controller) |
| 5 | `app/Http/Controllers/HomeController.php:13671` | ES update (god controller) |
| 6 | `app/Http/Controllers/HomeController.php:13882` | ES update (god controller) |
| 7 | `app/Http/Controllers/HomeController.php:13914` | ES **delete** (god controller) |
| 8 | `app/Http/Controllers/HomeController.php:14805` | ES update (god controller) |
| 9 | `app/Http/Controllers/HomeController.php:14860` | ES update (god controller) |
| 10 | `app/Http/Controllers/SaasFront/SaasFrontController.php:305` | ES update on registration |
| 11 | `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php:11216` | ES update (superadmin path) |
| 12 | `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php:11299` | **`ELASTIC_USERS_LIST` bulk `updateByQuery` on `checkActive`** (flagged in §2) |
| 13 | `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php:11328` | **`ELASTIC_COMPANIES_INDEX` bulk `updateByQuery` on `checkActive`** (flagged in §2) |
| 14 | `app/Http/Controllers/Auth/LoginController.php:225` | ES update on login flow |
| 15 | `app/Http/Controllers/Auth/LoginController.php:247` | ES update on login flow |
| 16 | `app/Http/Controllers/Auth/LoginController.php:374` | ES update on login flow |
| 17 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:13089` | ES update on job create/edit |
| 18 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:13340` | ES update on job create/edit |
| 19 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php:15425` | ES update on job create/edit |
| 20 | `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php:4090` | ES update on app-tracker |
| 21 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php:548` | ES update on application |

**Note:** `HomeController.php:6115` has a `refresh:true` line that is **commented out** and excluded from the count.

**Whitelisted (deliberately keep `refresh:true` here, with explanatory comment):**

| File:Line | Why whitelisted |
|-----------|-----------------|
| `app/Jobs/ProcessOperationalCoinDeductionsJob.php:15, 259, 282` | Existing comment: "deliberately omit 'refresh' => true — 1-second natural refresh is sufficient." Acts as the negative-reference exemplar. |

**Lint rule** (see §5.5 step 2) greps `app/` for `'refresh'\s*=>\s*true` and fails the build on any match outside the whitelisted file. Pre-commit + CI enforce it.

---

## 9. Frontend work explicitly out of scope

The Reverb `UserAccountSuspended` broadcast fires from `SyncUserStatusAction` (§5.1) and `BlockCompanyAction` (§5.1.1), but the **client-side listener wiring** is deferred to a separate V2_login frontend slice. Specifically:

- Inject `CURRENT_USER_ID` (and `CURRENT_COMPANY_ID`) into the Metronic layout in `resources/views/layouts/*.blade.php`.
- Register the Echo listener in a global JS file (the existing `OperationalCoinUpdated` listener pattern in the same layout is the precedent).
- Listener body:

```js
Echo.private(`company.${CURRENT_COMPANY_ID}`)
    .listen('UserAccountSuspended', (e) => {
        if (e.companyBlocked || e.userId === CURRENT_USER_ID) {
            window.location.href = '/login?reason=suspended';
        }
    });
```

This requires Blade layout changes and JS module wiring — outside the documented V2_login server-side scope. The server side of the broadcast is fully testable via `Event::fake()` (see §6 verification matrix) without the frontend listener existing.

---

**End of audit. Findings breakdown:**

| Section | Findings | Severity mix |
|---------|----------|--------------|
| §1 User blocked by company | 9 (footguns table rows) | 3 High, 4 Medium, 1 Low, 1 Trivial |
| §2 Company blocked by Superadmin | 7 (defects table rows) | 2 Critical, 2 High, 2 Medium, 1 Low |
| §3 Other blocking scenarios (12 sub-sections) | ~22 (multiple per sub-section) | 4 P0, 5 P1, 3 P2 |
| §4 Cross-cutting findings | 4 | 4 P0 patterns |
| §6 verification matrix (post-fix expectations) | 16 rows | n/a — these are gates, not findings |
| §8 `refresh:true` site inventory | 20 sites (9 files) | n/a — these are existing-code sites |
| **Total findings (incl. P0/P1/P2 sub-flavouring)** | **~42 distinct** | **~6 Critical/P0, ~12 High, ~14 Medium, ~5 Low, 5 Low/Trivial** |

The earlier "23 (3 Critical, 7 High, 8 Medium, 5 Low)" tally was the §1+§2 subset only and is superseded by the table above. See [`IMPLEMENTATION_PLAN.md` Appendix A](IMPLEMENTATION_PLAN.md) for the binding fix sequence. Architectural refinements (session epoch, Reverb broadcast, `refresh:true` removal, `SyncUserStatusAction`) applied per user review on 2026-08-06. See [`docs/adr/0007-blocking-scenarios-architecture.md`](../adr/0007-blocking-scenarios-architecture.md) for the decision record.
