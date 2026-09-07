# JERO SaaS Architecture Audit

> **Analysis-only document.** No code was modified. Generated 2026-08-17.
> Evidence-based: every conclusion backed by file path, line number, or code fragment.
> Confidence labels: **CONFIRMED** (directly verified), **INFERRED** (strongly inferred), **UNKNOWN**.

---

## Executive Summary

| Field | Value |
|---|---|
| **Application Type** | Multi-tenant B2B Hiring SaaS (ATS + Talent Marketplace) |
| **Framework** | Laravel 11, PHP 8.2+, FrankenPHP/Octane |
| **Authentication** | Session auth (Laravel default `web` guard). No JWT/Sanctum/Passport guard configured. Captcha-based login. Super-code impersonation for admin. |
| **Tenant Model** | Company-scoped (`company_id` column). Global Eloquent scope on `Company` model. ~217 tables, most have `company_id`. |
| **Account Model** | A `Company` row = one tenant. `company_type` enum: 1=Staffing, 2=Service, 3=Product. |
| **User Model** | `users` table with `company_id` FK. Uses Spatie `HasRoles`. Flags: `is_superadmin`, `root_user`, `status`, `working_status`. |
| **Role Model** | Spatie `Role` with custom `company_id`, `display_name`, `priority`. Stored in `roles` + `role_user` pivot + `model_has_roles` (Spatie). |
| **Permission Model** | Spatie `Permission` with custom `group_name`, `module_id`, `product_based`. 67 permissions seeded. 38 actively checked via `Gate::denies()`. |
| **Authorization Strategy** | Mixed: Spatie permission checks (`Gate::denies()`, `$user->can()`) + hard-coded `is_superadmin`/`root_user` checks + hard-coded `hasRole('admin')` checks. NO Policy classes exist. NO custom `Gate::define()` (only Horizon's `viewHorizon`). |
| **Tenant Isolation Strategy** | Eloquent global scope on `Company` model (skips CLI/console). `JobApplication` has its own scope. Most other models rely on ad-hoc `company_id` filtering in controllers/services. |
| **Subscription Strategy** | Coins-based economy + `CustomPackage` middleware checking `working_status`. `package_active` table drives feature gating. Coins deducted per action via `GeneralMethod::coinsDeductions()`. |
| **Frontend Authorization** | Almost entirely absent at the Blade level. No `@can`, `@cannot`, `@role`, `@permission` directives found in views. UI hiding done via `$this->userPermissions` array in `AdminBaseController` (computed per-request). |
| **API Authorization** | No dedicated API guard. All routes use session `web` guard. No `api.php` authorization patterns. |
| **Background Job Authorization** | Tenant context passed explicitly via constructor parameters (`company_id`). Jobs re-filter queries. ES sync jobs accept `company_id`. |
| **File Authorization** | Public/guessable URLs via `asset_url()` helper → `file.jero.works/{path}`. No signed/presigned URLs observed for downloads. R2 storage accessed directly. |
| **Search Authorization** | ES queries filter by `company_id` in index documents. Read queries use `term` filters on `company_id`. |
| **Overall Authorization Maturity** | **LOW** — inconsistent, partially implemented, heavily hard-coded. |
| **Overall SaaS Architecture Maturity** | **MEDIUM** — functional multi-tenancy but with significant isolation gaps. |
| **Critical Security Findings** | 5 (see AUTHORIZATION_GAPS.md) |

---

## 1. Tenancy Model

### 1.1 What Represents a Tenant?

**CONFIRMED:** A `companies` row is the tenant unit. Every business entity (Service Company, Product Company, Staffing Company, Super Admin) maps to a `companies` record.

**File:** `database/schema/mysql-schema.sql` — `TABLE: companies` (line ~990)
**File:** `app/Models/Company.php` — class definition

Key columns:
- `id` (PK, auto-increment)
- `company_name`, `company_email`
- `company_type` — `1`=Staffing, `2`=Service, `3`=Product (ENUM comment)
- `status` — `active`|`inactive`
- `package_id` — FK to `packages`
- `sub_domain` — subdomain-based tenant resolution (partially implemented)
- `licence_expire_on` — license/subscription expiry date

### 1.2 Can One User Belong to Multiple Tenants?

**CONFIRMED:** No. `users.company_id` is a single INT column. Each user belongs to exactly one company.

**File:** `database/schema/mysql-schema.sql` — `users.company_id int unsigned DEFAULT NULL`

### 1.3 Can One Tenant Have Multiple Users?

**CONFIRMED:** Yes. Multiple users per `company_id`. Roles are per-user within a tenant.

### 1.4 Can One User Have Multiple Roles?

**CONFIRMED:** No in practice. `AdminBaseController` line 153: `$this->getPermissions->roles[0]->permissions` — only reads `roles[0]`. The `role_user` pivot has composite PK `(user_id, role_id)`, but the application only uses the first role.

### 1.5 Are Agencies Separate Tenants?

**CONFIRMED:** Agencies are companies in the same `companies` table with `company_type=1`. They participate in the marketplace via `recommendation_relation` and `staffing_company_relation` tables.

### 1.6 Parent/Child Tenant Relationships?

**CONFIRMED:** No formal parent/child. Multi-tenant marketplace uses `from_company_id`/`to_company_id` patterns in `recommendation_relation` and `staffing_company_relation` — peer-to-peer, not hierarchical.

### 1.7 How is Tenant Context Determined?

**CONFIRMED:** Tenant context is implicit — derived from `auth()->user()->company_id` on every authenticated request. There is no explicit tenant-switching mechanism (no subdomain routing, no tenant selector middleware).

**File:** `app/Models/Company.php:57-70` — global scope reads `auth()->user()->company_id`

### 1.8 Global Scope Implementation

**CONFIRMED:** `Company` model has a global scope that filters by authenticated user's `company_id`:

```php
// app/Models/Company.php:57-70
protected static function booted(){
    static::addGlobalScope('company', function (Builder $builder) {
        if (app()->runningInConsole()) {
            return; // Skip in CLI
        }
        $user = auth()->user();
        if ($user && $user->roles->count() > 0) {
            $builder->where('companies.id', $user->company_id);
        }
    });
}
```

**CRITICAL GAP:** This scope only applies to `Company` model queries. Other models (Job, JobApplication, Client, etc.) do NOT automatically inherit this scope unless they define their own.

**File:** `app/Models/JobApplication.php:24-31` — `JobApplication` has its own scope:
```php
static::addGlobalScope('company', function (Builder $builder) {
    if (auth()->check() && !auth()->user()->is_superadmin) {
        $builder->where('job_applications.company_id', auth()->user()->company_id);
    }
});
```

---

## 2. Authentication System

### 2.1 Login Flow

**CONFIRMED:** Session-based authentication via Laravel's `web` guard. Custom `AttemptLoginAction` handles login POST.

**File:** `app/Actions/Auth/AttemptLoginAction.php`
**Config:** `config/auth.php` — single guard: `web` (session driver, eloquent provider)

Login steps:
1. Captcha validation (4-digit code stored in session)
2. Team-invite first-login activation (if `userId` + `companyid` + `toc` present)
3. Super-code impersonation check (if `super_code` present)
4. `Auth::attempt($credentials)` with email/password
5. Status check (`active` only)
6. `DeviceSessionManager::recordLogin()` — Redis single-device enforcement
7. Session seeding: `login_at` epoch timestamp
8. Async ES last_login update via `RecordLastLoginEsJob`

### 2.2 Super-Admin Impersonation

**CONFIRMED:** Super-admin can impersonate as any company user via `super_admin_login_code`:

```php
// app/Actions/Auth/AttemptLoginAction.php:175-206
private function handleSuperCode(Request $request, float $startAt, bool $captchaOk): RedirectResponse
{
    $users = DB::table('users')
        ->where('email', '=', $request->email)
        ->where('super_admin_login_code', '=', $request->password)
        ->first();
    // ... Auth::loginUsingId($users->id)
    Session::put('token_check', 'SUPERCODE');
}
```

**File:** `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php:4723` — `loginAsCompany($companyId)` — logs out admin, logs in as company admin via `Auth::loginUsingId()`.

**SECURITY CONCERN:** `super_admin_login_code` is a plaintext numeric code (`companyid.rand(1111,9999)`). Stored in `users.super_admin_login_code`. Additionally, `super_password` stores the user's plaintext password (SEC-DEBT comments in code acknowledge this).

### 2.3 User Status: `is_superadmin` vs `root_user`

**CONFIRMED:** Two privilege elevation mechanisms:

| Column | Type | Purpose |
|---|---|---|
| `is_superadmin` | `tinyint(1)` | Platform-level super admin. User ID 1 is always redirected to super-admin dashboard. Bypasses Company global scope. |
| `root_user` | `int` | Tenant-level "owner" user. `root_user=1` = company's primary admin. Excluded from `working_status` downgrade. Used throughout controllers for admin-only operations. |
| `working_status` | `int` | `0`=inactive/disabled, `1`=active. Non-root users get `working_status=0` when package expires. |
| `status` | `enum('active','inactive')` | Account-level status. Inactive users cannot log in. |

### 2.4 Single-Device Enforcement

**CONFIRMED:** `EnsureOneDeviceLogin` middleware (applied to all persona routes):

**File:** `app/Http/Middleware/EnsureOneDeviceLogin.php`

Guards applied in order:
1. Super-admin `token_check` bypass (throttled code clear)
2. Account status check (`active` only)
3. Epoch gate — kicks sessions created before latest block/suspend action
4. Single-device enforcement via `DeviceSessionManager` (Redis)

### 2.5 Password Reset

**CONFIRMED:** Standard Laravel password reset. Super-admin has custom reset flow.

**File:** `app/Http/Controllers/Auth/ResetPasswordController.php:203` — super-admin password reset bypass.

### 2.6 Social Login

**INFERRED:** Google OAuth configured at the Company level (`companies.google_client_id`, `google_client_secret`, etc.) but no dedicated social login controller found in the codebase during this audit. May be implemented in the large god controllers.

---

## 3. User / Role / Permission Model

### 3.1 Entity Relationship Map

```
companies (tenant)
 ├── users (company_id FK)
 │    ├── users_profile (user_id, company_id)
 │    ├── role_user (user_id, role_id) ← Spatie pivot
 │    ├── model_has_roles (model_id=User, role_id) ← Spatie polymorphic
 │    ├── model_has_permissions (model_id=User, permission_id) ← Spatie polymorphic
 │    └── todo_items (user_id)
 │
 ├── roles (company_id)
 │    ├── role_user → users
 │    └── model_has_roles → users
 │
 ├── permissions (global, NOT per-company)
 │    ├── model_has_permissions → users/roles
 │    └── permission_role (via Spatie) → roles
 │
 ├── clients (company_id)
 ├── jobs (company_id)
 ├── job_applications (company_id, thirdparty_company_id)
 ├── bench_applications (company_id)
 ├── recommendation_relation (from_company_id, to_company_id)
 ├── staffing_company_relation (from_company_id, to_company_id)
 ├── company_coins (company_id, event_type)
 ├── company_packages (company_id, package_id)
 └── ... (most tables have company_id)
```

### 3.2 Role System

**CONFIRMED:** Uses Spatie `laravel-permission` package with custom extensions.

**File:** `app/Models/Role.php` — extends Spatie's Role
**File:** `app/Models/Permission.php` — extends Spatie's Permission
**File:** `app/Models/RoleUser.php` — maps to `role_user` table

Key facts:
- Roles have `company_id` — each tenant creates its own roles
- Spatie's `model_has_roles` polymorphic table also used
- **No global scope on `Role` model** — the commented-out scope in `Role.php` would have filtered by company_id
- Role names are custom per company (e.g., "admin", "employee", "recruiter", etc.)

### 3.3 Permission System

**CONFIRMED:** 67 permissions in `permissions` table. Permissions are **global** (no `company_id` column).

**File:** `database/schema/mysql-schema.sql` — `TABLE: permissions`

Permission structure:
```sql
permissions (
  id, name (unique), guard_name, group_name, display_name,
  description, module_id, priority, status, product_based
)
```

**Active permissions checked via `Gate::denies()`** (38 unique permissions):
- `view_job_applications`, `add_job_applications`, `edit_job_applications`, `delete_job_applications`
- `view_jobs`, `add_jobs`, `edit_jobs`, `delete_jobs`
- `view_company`, `add_company`, `edit_company`, `delete_company`
- `view_clients`, `add_clients`, `edit_clients`, `delete_clients`
- `view_category`, `add_category`, `edit_category`, `delete_category`
- `view_skills`, `add_skills`, `edit_skills`, `delete_skills`
- `view_schedule`, `add_schedule`, `edit_schedule`
- `view_team`, `add_team`, `edit_team`
- `view_locations`, `add_locations`, `edit_locations`, `delete_locations`
- `view_report`, `view_joining`
- `view_question`, `edit_question`, `add_question`
- `view_roles_permission`, `add_roles_permission`
- `manage_settings`, `edit_offer`
- Additional via `$user->can()`: `edit_settings`, `delete_client`, `edit_schedule`

### 3.4 Hard-Coded Authorization Checks

**CONFIRMED:** Extensive hard-coded privilege checks alongside Spatie:

**Pattern 1: `is_superadmin` flag**
```php
// app/Http/Controllers/Controller.php:33
if ($this->user && $this->user->is_superadmin) {
    config(['froiden_envato.allow_users_id' => true]);
}

// app/Http/Models/Company.php:60 (global scope bypass)
if ($user && $user->roles->count() > 0) { ... }
// Note: superadmin with no roles would BYPASS the scope

// app/Http/Models/JobApplication.php:27
if (auth()->check() && !auth()->user()->is_superadmin) { ... }
```

**Pattern 2: `root_user` flag**
```php
// Throughout controllers — 60+ instances
// root_user=1 = company owner/admin
// Non-root users (root_user=0) subject to working_status/package checks
```

**Pattern 3: `hasRole('admin')` checks**
```php
// app/Http/Controllers/Admin/AdminCustomreportController.php:66
$chkAdminRole = $this->user->hasRole('admin');

// app/Http/Controllers/Admin/AdminJobApplicationController.php:1272
$chkAdminRole = $this->user->hasRole('admin');
```

**Pattern 4: Direct `$user->status` checks**
```php
// app/Actions/Auth/AttemptLoginAction.php:72
if ($user->status !== 'active') { Auth::logout(); ... }
```

---

## 4. Authorization Architecture

### 4.1 Mechanisms Used

| Mechanism | Used? | Where | Notes |
|---|---|---|---|
| Laravel Policies | **NO** | — | No `app/Policies/` directory exists. No `Policy` classes. |
| `Gate::define()` | **Almost NO** | Only `viewHorizon` in `HorizonServiceProvider` | No custom gates defined. |
| `Gate::denies()` | **YES** | ServiceCompany controllers (40+ uses) | Primary permission check in newer controllers. |
| `$user->can()` | **YES** | Admin controllers (20+ uses) | Spatie permission checks. |
| `$user->hasRole()` | **YES** | Admin controllers (10+ uses) | Hard-coded role name checks. |
| `abort_if(Gate::denies(...))` | **YES** | ServiceCompany extracted controllers | 40+ instances. |
| Middleware `role` | **Registered** | `bootstrap/app.php:56` | Spatie RoleMiddleware registered but usage unknown. |
| Middleware `permission` | **Registered** | `bootstrap/app.php:57` | Spatie PermissionMiddleware registered but usage unknown. |
| Blade `@can` / `@role` | **NO** | — | Zero Blade authorization directives found. |
| FormRequest `authorize()` | **Always `true`** | All 55 FormRequests | Authorization is NEVER enforced in FormRequests. |

### 4.2 How Gates Work (Spatie Default)

Since no custom `Gate::define()` calls exist, `Gate::denies('view_jobs')` resolves through Spatie's default mechanism:
1. Spatie registers permissions from `model_has_permissions` and `role_has_permissions`
2. Gate checks if the authenticated user (via `model_id`) has the named permission
3. User must have role → role must have permission, OR user must have direct permission

**File:** `AdminBaseController.php:151-156` — permissions loaded per request:
```php
$this->getPermissions = User::with('roles.permissions.permission')->find($this->user->id);
$userPermissions = array();
foreach ($this->getPermissions->roles[0]->permissions as $key => $value) {
    $userPermissions[] = $value->permission->name;
}
$this->userPermissions = $userPermissions;
```

**INFERRED:** `$this->userPermissions` array is shared to all views and used for UI hiding. But since Blade `@can` directives are absent, UI hiding is done via manual PHP in views (`if (in_array('view_jobs', $this->userPermissions))`).

---

## 5. Subscription / Plan / Feature Access

### 5.1 Package System

**CONFIRMED:** Three-tier access control:
1. **Packages** (`packages` table) — subscription plans with durations
2. **Custom Packages** — company-specific negotiated packages
3. **Coins** (`company_coins`, `coin_orders`, `coin_transection_logs`) — per-action billing

### 5.2 CustomPackage Middleware

**File:** `app/Http/Middleware/CustomPackage.php`

Checks:
- If `root_user == 0` (non-owner), checks `working_status`
- If `working_status == 0` (inactive), redirects to custom package page based on `company_type`
- If `working_status == 1`, checks `package_active` table for user limits, CV access limits, bench client access limits

### 5.3 Package Middleware

**File:** `app/Http/Middleware/Package.php`

**NO-OP:** This middleware does nothing. It just calls `$next($request)`.

### 5.4 Coins Economy

**CONFIRMED:** Actions deduct coins via `GeneralMethod::coinsDeductions()`.

```php
// app/Helper/GeneralMethod.php:5035
public static function coinsDeductions($companyid, $eventId, $userId, $from_company_id, $jobId, $teamId)
```

Called before:
- Adding job applications
- Adding team members
- Various bench candidate operations

Coin validation flow:
1. `coinsValidationsAllcompany()` — checks if company has sufficient balance
2. `coinsDeductions()` — deducts and logs the transaction
3. If insufficient, action is blocked with error response

---

## 6. Route Architecture & Middleware Stack

### 6.1 Middleware Registration

**File:** `bootstrap/app.php:43-61`

```
auth                → Authenticate (session)
super-admin         → SuperAdmin (is_superadmin check)
EnsureOneDeviceLogin → Device enforcement + epoch gate + account status
active-package      → Package (NO-OP)
custom-package      → CustomPackage (working_status/package check)
role                → Spatie RoleMiddleware
permission          → Spatie PermissionMiddleware
role_or_permission  → Spatie RoleOrPermissionMiddleware
recruiter.throttle  → 30 req/60s per user
recruiter.sliding   → 60 req/60s sliding window per user
```

### 6.2 Route Group Patterns

**Super Admin:**
```
auth → super-admin (prefix: super-admin/)
```

**Service Company:**
```
auth → EnsureOneDeviceLogin → custom-package (prefix: service_company/)
```

**Product Company:**
```
auth → custom-package (prefix: product_company/)
NOTE: No EnsureOneDeviceLogin!
```

### 6.3 Product Company Missing EnsureOneDeviceLogin

**CONFIRMED — CRITICAL GAP:** Product Company routes do NOT include `EnsureOneDeviceLogin` middleware. Product company users can have concurrent sessions without enforcement.

**File:** `routes/product.php:14-15`
```php
Route::group(['middleware' => ['custom-package']], function () {
```

---

## 7. Background Jobs & Async Authorization

### 7.1 Tenant Context in Jobs

**CONFIRMED:** Jobs explicitly receive `company_id` via constructor.

**File:** `app/Jobs/JobApplicationsNewUpload.php:17-36`
```php
public $company_id;
public function __construct($requestAll, $company_id, $submitted_by, ...)
```

### 7.2 Jobs That Pass company_id

| Job | company_id Source |
|---|---|
| `JobApplicationsNewUpload` | Explicit param |
| `ElasticCompanyIndex` | Explicit param (`$this->comid`) |
| `IndexCompanyBulkJob` | Explicit param |
| `BulkCounterRefresherJob` | Explicit param |
| `AutoCloseExpiredJobsJob` | Loop over active companies |
| `ProcessCandidateRequest` | Explicit param (`$this->companyId`) |
| `RecordLastLoginEsJob` | Explicit param |

### 7.3 Jobs That DON'T Pass company_id

**INFERRED:** Jobs that rely on Eloquent global scopes (Company model) would inherit `auth()->user()->company_id` if they run within a request context. But queued jobs run without `auth()` context, so any job that uses Eloquent WITHOUT explicit company_id would query ALL companies.

---

## 8. File & Storage Authorization

### 8.1 Storage Backend

**CONFIRMED:** Cloudflare R2 (S3-compatible). Files stored at `file.jero.works/{path}`.

**File:** `app/Helper/start.php:37` — `asset_url()` helper generates public URLs.

### 8.2 Resume Downloads

**File:** `app/Http/Controllers/HomeController.php:797-819`

```php
$s3Path = str_replace('https://file.jero.works/', '', $resumeUrl);
if (!Storage::disk('r2')->exists($s3Path)) { ... }
return Storage::disk('r2')->download($s3Path, $customName);
```

**CRITICAL GAP:** Resume download controller does NOT verify that the requesting user's `company_id` matches the resume owner's `company_id`. The URL path structure is predictable (`documents/{application_id}/{hashname}`).

### 8.3 No Signed URLs

**CONFIRMED:** No use of `Storage::temporaryUrl()`, `Storage::signedUrl()`, or `createPresignedRequest()` found. All file access uses public/guessable URLs.

---

## 9. Search & External Data Stores

### 9.1 Elasticsearch Tenant Filtering

**CONFIRMED:** ES documents include `company_id` field. Read queries use `term` filters.

**File:** `app/Services/ElasticJobIndex.php:48` — `'company_id' => $s->company_id`
**File:** `app/Services/ElasticCandidateIndex.php:94` — `'job_applications.company_id as other_company'`

### 9.2 Redis

**CONFIRMED:** Used for:
- Sessions (`SESSION_DRIVER=redis`)
- Cache (`CACHE_STORE=redis`)
- Horizon queue management
- Device session enforcement (`DeviceSessionManager`)
- Idempotency keys
- Rate limiting keys
- Epoch gates for session invalidation

---

## 10. Events, Listeners & Observers

### 10.1 Events (10 total)

| Event | Purpose |
|---|---|
| `CandidateStatusChanged` | Candidate pipeline stage changed |
| `CompanyRegistered` | New company signed up |
| `CompanyUrlEvent` | Company URL operation |
| `InterviewReminderEvent` | Interview reminder triggered |
| `InterviewScheduled` | Interview scheduled |
| `MessageSent` | Chat message sent |
| `OperationalCoinUpdated` | Coins deducted/credited |
| `StageMoved` | Pipeline stage transition |
| `UserAccountSuspended` | Account suspended |
| `UserRegistered` | User registered |

### 10.2 Observers (28 total)

Key observers for authorization-relevant entities:
- `JobApplicationObserver` — ES sync on CRUD
- `JobObserver` — ES sync on CRUD
- `CompanyObserver` — ES sync
- `UserObserver` — ES sync
- `ClientObserver` — ES sync
- `RecommendationRelationObserver` — marketplace ES sync

---

## 11. Database-Level Security

### 11.1 Foreign Keys

**CONFIRMED:** Some FKs exist but NOT consistently enforced:

Examples WITH FK:
- `application_settings.company_id → companies.id` (ON DELETE CASCADE)
- `client_file.company_id` (indexed, no FK constraint)

Many tables have `company_id` indexed but WITHOUT foreign key constraints.

### 11.2 Soft Deletes

**CONFIRMED:** `JobApplication` uses `SoftDeletes` trait. Most other models do NOT.

### 11.3 Database-Level Authorization

**CONFIRMED:** The database itself enforces NO authorization boundaries. All access control is application-layer only. A direct SQL query could read any company's data regardless of tenancy.

---

## 12. Broadcasting / WebSocket Authorization

**File:** `routes/channels.php`

```php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('company.{companyId}.dashboard', function ($user, int $companyId) {
    return (int) $user->company_id === (int) $companyId;
});
```

**CONFIRMED:** Channel authorization is properly implemented — user can only subscribe to their own user channel and their company's dashboard channel.

---

## 13. Key Architecture Gaps

1. **No Policy classes** — All authorization is inline in controllers
2. **No Gate definitions** — Relies entirely on Spatie's automatic resolution
3. **FormRequests always authorize=true** — No authorization at form validation layer
4. **No Blade authorization directives** — UI hiding done via manual PHP
5. **No API guard** — No JWT/Sanctum/Passport; all routes use session auth
6. **Package middleware is a no-op** — Registered but does nothing
7. **Product Company missing EnsureOneDeviceLogin** — Inconsistent session enforcement
8. **Global scope only on Company model** — Most models have no automatic tenant filtering
9. **Permissions are global** — Same 67 permissions for all tenants (no tenant-specific permissions)
10. **Dual role systems** — Spatie `model_has_roles` AND legacy `role_user` coexist
