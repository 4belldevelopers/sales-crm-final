# SAAS_AUTHORIZATION_IMPLEMENTATION_GUIDE.md

> **For AI coding agents.** This is the authoritative reference for how authorization,
> tenancy, and access control ACTUALLY work in JERO today (2026-08-17). Read this
> before touching any code that decides **who can do what, where, and to which data**.
> Companion documents: `SAAS_ARCHITECTURE_AUDIT.md`, `PERMISSION_MATRIX.md`,
> `TENANT_ISOLATION_AUDIT.md`, `ACTION_INVENTORY.md`, `AUTHORIZATION_GAPS.md`.

---

## 1. How the SaaS Works

JERO is one Laravel 11 codebase serving multiple personas from the same deployment:

| Persona | Route prefix | Middleware chain | Persona scope |
|---|---|---|---|
| Super Admin (platform owner) | `/super-admin/` | `auth` → `super-admin` | **In scope** |
| Admin (tenant-level) | `/admin/` | `auth` (+ legacy base controller) | **In scope** |
| Service Company | `/service_company/` | `auth` → `EnsureOneDeviceLogin` → `custom-package` | **In scope** |
| Product Company | `/product_company/` | `auth` → `custom-package` (NO device login) | **DORMANT — ignore** |
| Staffing Company | inside `web.php` | varies | **DORMANT — ignore** |
| Public Front / SaaS Front | `/` | none (public) | **In scope** |

Writes go to MySQL; reads for search/lists often go to Elasticsearch. Queues run on
Redis via Horizon. Runtime is Octane/FrankenPHP (long-lived workers — no request state
in statics).

## 2. What a Tenant Is

- A **row in `companies`** (`company_type`: 1=Staffing, 2=Service, 3=Product).
- Almost every domain table carries `company_id`.
- There is **no tenant-switching, no subdomain resolution in practice** — tenant
  context is always `auth()->user()->company_id` (implicit, per-request).
- Marketplace relations between tenants use `from_company_id`/`to_company_id`
  (`recommendation_relation`, `staffing_company_relation`) — peer-to-peer, not parent/child.

## 3. What an Account Is

"Account" in this codebase means either:
- **Company account** (the tenant, `companies` row) — has `status` active/inactive,
  `licence_expire_on`, `package_id`, coin balances (`company_coins`), or
- **User account** (`users` row) — has `status`, `working_status`, `is_superadmin`,
  `root_user`.

Key user flags you MUST know:

| Column | Meaning |
|---|---|
| `is_superadmin` | Platform super admin. Bypasses the `JobApplication` global scope. User id 1 always lands on super-admin dashboard. |
| `root_user` | Tenant owner. `1` = company's primary admin; exempt from `working_status` package downgrades. |
| `working_status` | `0` = blocked by package/coins; `1` = active. Only enforced for `root_user == 0`. |
| `status` | `active`/`inactive`. Inactive cannot log in; checked by login + `EnsureOneDeviceLogin`. |

## 4. What a User Is

`users` row belonging to exactly ONE company (`users.company_id`, single value —
a user can never belong to two tenants). Spatie `HasRoles` trait. Sensitive columns
(`super_password`, `super_admin_login_code`) exist for admin impersonation — treat as
legacy landmines, do not extend the pattern.

## 5. What Roles Exist

- Spatie roles stored in `roles` (with `company_id` — **roles are per-tenant**) plus a
  **legacy parallel pivot** `role_user` that the codebase also reads (`User::role()`
  relation maps to `role_user`).
- **No global scope on Role** — querying `Role` returns ALL companies' roles unless you
  filter `company_id` yourself.
- Practical role names observed in code: `admin`, `employee` (checked via
  `hasRole('admin')` hard-coded). Each tenant defines custom roles via the
  ManageRolePermission UI (`roles.manual_add`).
- **Only `roles[0]` is honored**: `AdminBaseController` reads
  `$this->getPermissions->roles[0]->permissions` — effectively ONE role per user.

## 6. What Permissions Exist

- Global `permissions` table (~67 rows, shared across tenants; unique `name`).
- Granted via Spatie (`role_has_permissions` / `model_has_permissions`) or the legacy
  `permission_role` pivot read through `roles.permissions.permission`.
- The full list of permission names actively checked in code is in
  `PERMISSION_MATRIX.md`. Naming convention: `view_X`, `add_X`, `edit_X`, `delete_X`
  (note: **`add_`, not `create_`**).
- `Gate::denies('perm')` works without any `Gate::define()` because Spatie
  auto-registers permissions as abilities.

## 7. How Authorization Works (the real flow)

```
Request
 → route group middleware: auth (session) [→ super-admin | EnsureOneDeviceLogin | custom-package]
 → controller constructor middleware: AdminBaseController loads
     $this->user, $this->userPermissions (from roles[0]), $this->global (Company),
     $this->activePackage (CompanyPackage)
 → controller action: MAYBE abort_if(Gate::denies('perm'), 403)   ← only newer/extracted controllers
 → FormRequest::authorize()  ← ALWAYS returns true (no protection)
 → controller/service body: hand-written company_id filters + coin checks
 → Eloquent (global scope may apply) or raw DB::table (NO scope — manual filter required)
```

Authorization is a **combination of**: permission checks (partial), tenant scoping
(partial, model-dependent), status/business checks (login + middleware), and
hard-coded flags (`is_superadmin`, `root_user`, `hasRole('admin')`).

**Most routes have NO permission check at all** — session auth + tenancy is the only
gate. The UI hides buttons via `$this->userPermissions` passed to views, but the
backend often does not re-check.

## 8. How Ownership Works

- Row-level ownership = `company_id` on the row. There is **no per-user ownership
  model** for domain data (any user of a company can act on that company's rows,
  subject to nothing but permission checks that are often absent).
- `submitted_by` / `created_by` / `user_id` columns record who created a row but are
  **not enforced** as access constraints (except display filtering).
- Ownership never "transfers" except super-admin `loginAsCompany` (impersonation).

## 9. How Tenant Isolation Works

- `Company` model global scope: filters `companies.id = auth()->user()->company_id`
  (skips in console; skips when user has no roles).
- `JobApplication` global scope: filters by `company_id`, **bypassed when
  `is_superadmin`**.
- Every other model: **no automatic scope** — controllers/services hand-write
  `where('company_id', ...)`. Raw `DB::table()` queries (6,170 across `app/`) never
  have scopes; ~870 mention `company_id` on the same line — the rest must be audited
  case-by-case.
- ES reads: documents carry `company_id`; controllers must add the term filter
  themselves (see `TENANT_ISOLATION_AUDIT.md`).

## 10. How Frontend Authorization Works

- Permissions are computed per-request in `AdminBaseController::__construct`
  (`$this->userPermissions`) and shared to views.
- Views hide buttons with manual PHP (`in_array(...)`) — **no `@can`/`@role` Blade
  directives exist anywhere**.
- **Frontend checks are cosmetic.** Any hidden action can be invoked by hitting the
  route directly, because most backend routes never re-check the permission.

## 11. How API Authorization Works

- There is **no token API layer**. Everything is the session `web` guard. No Sanctum/
  Passport/JWT guards configured in `config/auth.php` (packages are installed but no
  guard is registered).
- AJAX endpoints (DataTables `*_ajax` pairs) sit inside the same route groups as
  pages — same middleware, same gaps.
- CSRF: session-based, with webhook exemptions in `bootstrap/app.php` for Stripe/
  Razorpay callbacks.

## 12. How Background Jobs Handle Authorization

- Jobs do NOT re-validate permissions. Tenant context is **captured explicitly as
  constructor arguments** (`company_id`, `submitted_by`, …) at dispatch time — e.g.
  `JobApplicationsNewUpload`, `ElasticCompanyIndex`, `BulkCounterRefresherJob`,
  `ProcessCandidateRequest`, `RecordLastLoginEsJob`.
- A job dispatched under one user will happily operate on whatever IDs it was given —
  **authorization is whatever the dispatcher checked** (often nothing beyond session).
- Console commands skip global scopes (`app()->runningInConsole()` early-return), so
  artisan jobs must filter by company explicitly.

## 13. How Files Are Protected

- Resumes/documents on Cloudflare R2 (S3-compatible), public base `file.jero.works`.
- Downloads stream through `HomeController` (`Storage::disk('r2')->download(...)`)
  after rebuilding the path from a URL — **no ownership verification of the requested
  document against the requesting user's company**.
- No signed/temporary URLs for downloads. Upload pipeline has presigned endpoints
  (`service_company/resume/presign`) for candidates.
- Treat any file route as sensitive; ownership checks must be added, not assumed.

## 14. What Must NEVER Be Bypassed

1. Never remove/neutralize `auth` middleware on any non-public route.
2. Never bypass the `Company`/`JobApplication` global scopes without adding an
   explicit, equivalent `company_id` WHERE in the same query.
3. Never write a raw `DB::table()` query on a tenant table without `company_id`
   filtering (or a documented cross-tenant reason: marketplace tables).
4. Never trust `$this->userPermissions` / view-level hiding as the only check on a
   mutating endpoint.
5. Never extend the plaintext `super_password` / numeric `super_admin_login_code`
   impersonation pattern into new code.
6. Never skip the coin deduction (`GeneralMethod::coinsDeductions`) on an action that
   existing code bills for — that's business logic with revenue impact.
7. Never expose ES queries without a `company_id` term filter (except documented
   marketplace reads).

## 15. Known Architectural Inconsistencies

- Dual role pivots: Spatie `model_has_roles` AND legacy `role_user` both in play.
- Only `roles[0]` is honored — multi-role grants silently ignored.
- `Package` middleware alias `active-package` is a **no-op**.
- Product Company group lacks `EnsureOneDeviceLogin` (dormant persona, but the gap is real).
- Permissions table is global while roles are per-company — permission naming drift
  between tenants is possible.
- `Gate::denies()` present in ServiceCompany extracted controllers; older Admin
  controllers use `$user->can()`; many controllers use nothing.
- AdminBaseController dead multi-login code + `user_token` table remnants.

## 16. Known Security Issues (summary)

See `AUTHORIZATION_GAPS.md` for full detail with file:line evidence. Top items:
CRITICAL file-download ownership gap; CRITICAL raw-query tenant gaps; HIGH plaintext
impersonation credentials; HIGH absent permission checks on mutations; MEDIUM no-op
package middleware; MEDIUM Blade/XSS surface.

## 17. Recommended Authorization Architecture (target state, not current)

1. Extract `abort_if(Gate::denies(...))` checks into **Policies** per model
   (app/Policies/ does not exist yet — introducing it is a green-field, additive move).
2. Standardize on Spatie permission checks at **controller entry** for every mutating
   action; keep business rules (coins, package) separate and after the permission gate.
3. Introduce a `BelongsToTenant` trait/global-scope package applied to all tenant
   models — one mechanism instead of per-model `boot()`.
4. Move file downloads behind ownership-verified, signed URLs.
5. Replace numeric `super_admin_login_code` + plaintext `super_password` with a
   proper impersonation table (audited, time-boxed, revocable).

## 18. Safe Implementation Patterns

```php
// SAFE — permission gate + tenant-scoped query
abort_if(Gate::denies('edit_jobs'), 403);
$job = Job::where('company_id', auth()->user()->company_id)->findOrFail($id);

// SAFE — raw query with explicit tenancy note
// Tenancy: filtered by authenticated company (Rule #3 — DB::table has no scope)
$rows = DB::table('jobs')->where('company_id', $user->company_id)->get();

// SAFE — queued job receives tenant explicitly
ProcessCandidateRequest::dispatch($companyId, $jobId, $userId);

// SAFE — ES read with tenant term
'term' => ['company_id' => (int) $user->company_id]
```

## 19. Unsafe Implementation Patterns (do NOT copy)

```php
// UNSAFE — no permission check, no tenant filter, route-model binding alone
public function destroy(Job $job) { $job->delete(); }

// UNSAFE — FormRequest authorize() stub
public function authorize() { return true; }   // every existing request does this

// UNSAFE — raw query without company_id
DB::table('job_applications')->where('status', 1)->get();

// UNSAFE — trusting the view's hidden state
// (view hides button via userPermissions, endpoint itself checks nothing)

// UNSAFE — relying on runningInConsole scope skip in web context
Company::all();  // scope silently no-ops for superadmin-without-roles / console
```

## 20. Testing Requirements

- Every new action must include a test asserting: (a) 403 without the permission,
  (b) 404/403 when the resource belongs to another `company_id`, (c) success inside
  the tenant, (d) coin/package business rules fire when applicable.
- Run `php artisan test` plus targeted feature tests; `php -l` and
  `./vendor/bin/pint` per docs/VERIFY.md.

---

## Rules Every Future AI Agent MUST Follow

1. Read this document before modifying any authorization, permission, or tenancy code.
2. Never assume the current user's tenant — always read `auth()->user()->company_id`
   at query time; never cache tenant across requests (Octane).
3. Never trust frontend authorization — every mutating endpoint needs its own
   `Gate::denies()` / policy check.
4. Every API mutation must have backend authorization independent of UI state.
5. Every tenant-owned query (Eloquent OR raw) must enforce `company_id` isolation.
6. Never bypass Eloquent global scopes without applying an equivalent explicit tenant
   restriction in the same query.
7. Never use hard-coded user IDs, emails, or role names for new authorization logic
   (existing `hasRole('admin')` sites are legacy — don't add more).
8. Never introduce a new permission without documenting it (PERMISSION_MATRIX.md).
9. Never introduce a new role without documenting it (this guide §5).
10. Never expose another tenant's data through search, cache, files, queues, or APIs.
11. Keep authorization (who) and business rules (coins/package/state) as separate
    checks — permission first, business rule second.
12. For every new action identify: Actor → Resource → Action → Permission → Tenant →
    Ownership → Business Rules, and record it in ACTION_INVENTORY.md.
13. Any change affecting access control must ship with authorization tests (§20).
14. Background jobs must receive tenant context explicitly in the constructor and
    filter by it; never rely on `auth()` inside queued code.
15. File URLs must be ownership-verified before streaming; never generate public
    URLs to tenant documents.
16. Preserve baked-in typos (`condidate`, `commericial`, `transection`) — they are
    table/index names.
17. Schema truth is `database/schema/mysql-schema.sql`, not migrations — verify
    columns there before writing queries.
18. Do not touch dormant personas (Product Company, Staffing Company) —
    docs/PERSONA_SCOPE.md.
