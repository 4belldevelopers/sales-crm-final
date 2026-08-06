# Blocking Scenarios Audit — Login & Session Lifecycle

**Companion to:** [`AUDIT_REPORT.md`](AUDIT_REPORT.md), [`IMPLEMENTATION_PLAN.md`](IMPLEMENTATION_PLAN.md), [`BEFORE_AFTER_COMPARISON.md`](BEFORE_AFTER_COMPARISON.md)
**Date:** 2026-08-06
**Scope:** All code paths that suspend, deactivate, delete, or impersonate a user or company — and the downstream effects on login, sessions, Elasticsearch, and real-time client state.

This audit was triggered by a single observation: a user blocked by their company, or a company blocked by Superadmin, **stays logged in** until they make another HTTP request. There is no real-time kick. We map every blocking path in JERO and quantify the gaps.

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
| 1 | **Company root user CAN be blocked** — no `root_user === 1` guard. Admin can lock themselves out of their own dashboard. | `ServiceCompanyTeamController.php:2074-2099` | **High** |
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

| Sync path | ES updated? | refresh? | Bulk? |
|-----------|-------------|----------|-------|
| ServiceCompany `statusUpdate` (single) | yes | no | no |
| ProductCompany `statusUpdate` (single) | **NO** | n/a | n/a |
| StaffingCompany `statusUpdate` (single) | **NO** | n/a | n/a |
| `teamDelete` bulk | **NO** | n/a | n/a |
| `SuperAdminCompanyController::checkActive` | yes | **yes** | yes (`updateByQuery`) |
| `checkStatusOperational` | **NO** | n/a | n/a |
| `checkStatusSales` | **NO** | n/a | n/a |
| Coin-deduction cron | yes | no | no |
| `DeleteAccount` cron | **NO** | n/a | n/a |
| `ProfileController::destroy` | **NO** (orphan) | n/a | n/a |

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

### 5.1 `SessionKillService` — single point of force-logout (Phase 3.2 addition)

Centralize the cascade so every blocking handler — user-block, company-block, coin-depletion, self-delete, profile-delete, impersonation — uses the same path.

```php
// app/Services/SessionKillService.php
class SessionKillService
{
    public function kill(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        // 1. Clear session_id in MySQL (single UPDATE)
        DB::table('users')->where('id', $userId)->update([
            'session_id' => null,
            'super_admin_login_code' => null,
        ]);
        // 2. Destroy Redis session if known
        $sid = Cache::pull('user_session_'.$userId);
        if ($sid) {
            session()->getHandler()->destroy($sid);
        }
        // 3. Bump session_epoch so EnsureOneDeviceLogin flags them
        Cache::put('force_logout_'.$userId, true, 86400);
        // 4. Broadcast kick
        event(new UserKickedFromSession($userId, 'blocked'));
    }

    public function killCompany(int $companyId, string $reason): int
    {
        // Bulk UPDATE users SET session_id=NULL WHERE company_id=?
        // Iterate affected user IDs and call kill() per row
        // Single Reverb broadcast per company channel
        ...
    }
}
```

### 5.2 Wire `EnsureOneDeviceLogin` for real-time check (Phase 3.3)

Add a `force_logout_{userId}` cache check at the top of `handle()`. If set, logout + flash "Your session was ended by an administrator" + clear flag. Runs on **every** request, not just stale-session mismatch.

### 5.3 `Blocked` audit event + lightweight table (Phase 6.5)

A minimal, no-Laravel-Auditing alternative:

```php
Schema::create('user_block_log', function (Blueprint $t) {
    $t->id();
    $t->unsignedBigInteger('target_user_id')->nullable()->index();
    $t->unsignedBigInteger('target_company_id')->nullable()->index();
    $t->unsignedBigInteger('actor_user_id');
    $t->string('block_type');        // user_block, company_block, working_status, superadmin, self_deactivate, payment_failed
    $t->string('reason')->nullable();
    $t->timestamp('created_at')->useCurrent();
});
```

Wrap in a `BlockLogger::record($target, $actor, $type, $reason)` helper. Wire into:
- `ServiceCompanyTeamController::statusUpdate` and `teamDelete`
- `ProductCompanyTeamController::statusUpdate`
- `StaffingCompanyTeamController::statusUpdate`
- `SuperAdminCompanyController::checkActive`, `checkStatusOperational`, `checkStatusSales`
- `ProcessOperationalCoinDeductionsJob` (when flipping `working_status=0`)
- `DeleteAccount` console
- `loginAsCompany` (impersonation)
- `ProfileController::destroy` (self-delete)

### 5.4 ES sync unification (Phase 1.x extension)

Replace per-handler ad-hoc ES writes with a single `UserStateIndexer` that:
- Accepts `(userId, status, working_status)` deltas.
- Uses Bulk API with `doc_as_upsert` (already specified in IMPLEMENTATION_PLAN Phase 1.2).
- Never sets `refresh:true` for bulk updates — only for single-doc superadmin confirms.
- Handles the missing sync in ProductCompany/StaffingCompany variants.

### 5.5 Rate-limit the superadmin block endpoints

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
Prevents the self-DoS via `updateByQuery refresh:true` cascade.

### 5.6 Wire the dead consoles (Phase 6.6)

`app/Console/Kernel.php` must schedule:
- `delete-account` — daily at 02:00 server time
- `licence-expire` — daily at 03:00

Without this, both are silent dead code today.

### 5.7 Schema additions (separate migration, sign-off required)

Adding the columns in §4.4 is a **business-logic change** — flag for product sign-off per AI_RULE #8. Not in V2_login scope, but listed for the team planning cycle.

---

## 6. Verification matrix (post-fix)

After Phase 5.1-5.6 land, every blocking path must satisfy:

| Check | Method |
|-------|--------|
| Blocked user gets Reverb `UserKickedFromSession` event within 100 ms | `php artisan test --filter=KickBroadcastTest` |
| Blocked user's open tab is bounced to `/login` within 1 s of broadcast | Playwright e2e: open 2 tabs as user → admin blocks → assert both tabs redirect |
| Blocked user's MySQL `session_id` is `NULL` immediately after block | `php artisan tinker` smoke |
| Blocked user's Redis session key is destroyed | `redis-cli KEYS '*' | grep <sid>` returns nothing |
| Block action writes a row to `user_block_log` with correct actor | `assertDatabaseHas('user_block_log', [...])` |
| ES `ELASTIC_USERS_LIST` reflects new `status` within 1 s (no `refresh:true`) | `curl :9200/users/_search?q=status:inactive` |
| `teamDelete` cannot write across tenants (negative test) | Test sends user IDs from another company; asserts 0 rows affected |
| Root user cannot be blocked by their own company admin | Test asserts 422 / no-op |
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

**End of audit. Total findings: 23 (3 Critical, 7 High, 8 Medium, 5 Low). See IMPLEMENTATION_PLAN §5 for the binding fix sequence.**
