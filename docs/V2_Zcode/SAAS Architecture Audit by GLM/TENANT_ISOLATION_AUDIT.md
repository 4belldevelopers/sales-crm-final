# TENANT_ISOLATION_AUDIT.md

> Deep audit of SaaS tenant isolation in JERO (2026-08-17). Analysis-only.
> Tenant = `companies.id`; tenant column = `company_id` (plus `from_company_id`/
> `to_company_id` on marketplace tables, `thirdparty_company_id` on applications).
> Scale reference: 217 tables; 6,170 raw `DB::table/DB::select` call-sites in `app/`
  of which only ~870 mention `company_id` on the same line.

---

## 1. Isolation Mechanisms Inventory

### 1.1 Eloquent global scopes (CONFIRMED)

| Model | Scope | Behavior | Gaps |
|---|---|---|---|
| `Company` (`app/Models/Company.php:57-70`) | `company` | `where companies.id = auth()->user()->company_id` | Skipped when `runningInConsole()`; **skipped when user has NO roles** (`$user->roles->count() > 0` guard) — a roleless user sees ALL companies; superadmin with roles IS scoped (must use `withoutGlobalScope`) |
| `JobApplication` (`app/Models/JobApplication.php:24-31`) | `company` | `where job_applications.company_id = user->company_id` unless `is_superadmin` | Third-party company dimension (`thirdparty_company_id`) handled ad-hoc in raw queries, not by scope |
| All other models (Job, Client, Bench, Document, Interview, Offer…) | **NONE** | — | Isolation depends entirely on hand-written WHERE clauses |

**Note:** docs/ARCHITECTURE.md still says the JobApplication scope is commented out
(BUG-03); the code now shows it ACTIVE with a superadmin bypass — the doc is stale,
and the superadmin bypass is itself a drift from the Company scope's logic.

### 1.2 Middleware

| Middleware | Tenant effect |
|---|---|
| `auth` | Establishes the user (and therefore implicit tenant context) |
| `EnsureOneDeviceLogin` | Kills sessions on suspend/block (epoch gate) — protects stale tenants |
| `custom-package` | Redirects `working_status=0` non-root users to package screen |
| `SuperAdmin` | `is_superadmin` gate for platform routes |
| **No tenant-resolution middleware exists** | Tenant is never switched or re-validated per request |

### 1.3 Raw queries (the primary risk surface)

- `DB::table(...)` / `DB::select(...)` bypass ALL global scopes. 6,170 call-sites
  across `app/` (5,375 in controllers alone). ~870 lines co-mention `company_id`.
- Many are legitimately global (masters, countries, ES helpers) or cross-tenant by
  design (marketplace), but a large fraction operate on tenant tables
  (`jobs`, `job_applications`, `users`, `clients`, `bench_applications`) with no
  company filter. Representative CONFIRMED examples:

```php
// app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php (myContactPerson)
$companyContact = DB::table('users')->select(...)->join('users_profile', ...);
// filters by company ONLY if request('company_id') != ""  ← attacker-controlled

// app/Actions/Auth/AttemptLoginAction.php:190 handleSuperCode()
Auth::loginUsingId($users->id);   // id from a users-table lookup by email+code

// app/Http/Controllers/HomeController.php:13382
DB::table('users')->where('password','=','')->get();  // global maintenance sweep (ok)

// routes/service.php /search-cities inline route
DB::table('city_list')->where('city','LIKE',"%$query%")  // master table (ok) but dd() left in
```

- IDOR pattern: numerous controllers take `{id}` route params and fetch rows via
  raw query or route-model binding on **unscoped** models (Job, Client, Bench…),
  then mutate. With no scope and no WHERE company_id, tenant A can address tenant
  B's rows by guessing/enumerating IDs. (CONFIRMED pattern; per-route exploitation
  catalog deferred to remediation Track A.)

### 1.4 Elasticsearch

- Documents embed `company_id` (+ `thirdparty_company_id`,
  `from_company_id/to_company_id` on relation indices) —
  `ElasticJobIndex.php:48,63-67,89,123,141`, `ElasticCandidatesIndex.php:94-97`,
  `ElasticBenchCandidatesIndex.php:199-201`.
- Read paths add `term company_id` filters in controllers/services manually.
  **No shared query-builder enforces it** — each of the 60+ index read paths must
  remember the filter; any omission silently returns cross-tenant hits because ES
  is a separate store unaware of Eloquent scopes.
- `candidates_view` MySQL VIEW joins across companies (by design for marketplace
  reads) — any raw consumer must still constrain by company.

### 1.5 Redis / cache keys

- Keys observed: `user:epoch:{id}`, `company:epoch:{id}`,
  `throttle:jobs:{userId}`, `rslide:jobs:{userId}`, `token_check_save_{id}`,
  `idempotency:{key}` — all user-scoped, none leak cross-tenant data by key design.
- Dashboard KPI cache (V2 dashboard API) keys are company-scoped (channel
  authorization confirms company dimension) — INVALIDATION must include company.

### 1.6 Queue payloads

- Jobs carry explicit tenant IDs (see §4 of architecture audit). Risk: none from
  key leakage; risk is *dispatch-time trust* — jobs re-use company_id params with
  no re-authorization (acceptable pattern, but mutations inside jobs are unbilled/
  unchecked if dispatcher was fooled).

### 1.7 Broadcast channels

```php
// routes/channels.php
Broadcast::channel('App.Models.User.{id}', fn($user,$id) => (int)$user->id === (int)$id);
Broadcast::channel('company.{companyId}.dashboard', fn($user,$cid) => (int)$user->company_id === (int)$cid);
```
CONFIRMED correct.

### 1.8 File storage (R2/S3)

- Public URL base `file.jero.works`; `asset_url()` (`app/Helper/start.php:37`)
  builds deterministic paths (`documents/{applicationId}/{hashname}`,
  `candidate-photos/{photo}`, `company-logo/{logo}`).
- Download route (`HomeController:797-819`) rebuilds the R2 key from a supplied
  resume URL and streams it — **no verification that the document belongs to the
  requester's company** → cross-tenant resume retrieval if IDs are guessable.
- No signed URLs; object names partially hashed (`hashname`) but photo/logo names
  are raw filenames.

---

## 2. Cross-Tenant Access Paths (attack/bypass scenarios)

| # | Path | Severity | Mechanics |
|---|---|---|---|
| X1 | Resume/document download | **CRITICAL** | Authenticated user of company A requests `documents/{id}/{hash}` of company B; controller streams from R2 without ownership check (HomeController:797-819). |
| X2 | Roleless-user Company scope bypass | **CRITICAL** | `Company::booted()` requires `roles->count() > 0`; any authenticated user without a role reads ALL companies (fallback path `SaasFrontController` login flows can create such users). |
| X3 | Unscoped model + IDOR | **HIGH** | Job/Client/Bench/Offer rows fetched by bare ID on unscoped models; mutating endpoints (status updates, deletes, exports) operate cross-tenant. |
| X4 | Raw-query IDOR | **HIGH** | 5,375 controller raw queries; sub-set keyed by request-controlled IDs without company filter (e.g., `myContactPerson` optional company_id). |
| X5 | Superadmin impersonation primitives | **HIGH** | `loginAsCompany` (no audit trail beyond logs), numeric `super_admin_login_code`, plaintext `super_password` stored on users. |
| X6 | ES read omission | **MEDIUM** | Any list/search endpoint forgetting the `company_id` term returns foreign docs; no central guard exists. |
| X7 | `user_token` legacy table | **LOW** | Dead multi-login code (AdminBaseController:79-111) — informational. |
| X8 | Public-URL file guessing | **MEDIUM** | Photo/logo paths un-hashed under public base. |

---

## 3. Tables With / Without Tenancy Columns (schema-level)

- With `company_id` (indexed or not): `companies` (self), `users`, `users_profile`,
  `roles`, `application_settings`, `application_status`, `bench_applications`,
  `bench_assign`, `client_file`, `client_location`, `clients`, `client_contact_info`
  (via client), `jobs` (+ `client_city_jobs`, `client_location_jobs`,
  `client_contact_info_jobs`), `commericials` (+details/countries),
  `company_packages`, `company_coins`, `coin_orders`, `coin_transection_logs`,
  `carts`, `cart_transaction_history`, `candidate_status`, `boostkeywords`,
  `add_job_wise_condidates` (+ `thirdparty_company_id`) …
- Marketplace cross-tenant: `recommendation_relation`, `staffing_company_relation`,
  `conversations` (`from/to_company_id`), `bench_place` (`source_comapny_id`,
  `client_company_id`).
- **No tenancy column at all** (global by design): `countries`, `city_list`,
  `state_list`, `currencies`, `qualification_master`, `master_status`,
  `bench_master_status`, `modules`, `permissions`, `packages` (platform),
  `coin_packages` (platform), `coupons` (platform), `cache`, `custom_queue_jobs`…

### DB-enforced boundaries

- Only a minority of `company_id` columns have real FOREIGN KEYs to `companies`
  (e.g., `application_settings.company_id` FK CASCADE). Most are plain indexed
  ints — **the database enforces NO tenant boundary**; isolation is 100%
  application-layer.

---

## 4. What IS Correctly Isolated (credit where due)

- Broadcast channels (user + company) — correct.
- `JobApplication` scope now active with superadmin carve-out.
- Queue jobs carry explicit company context.
- Webhooks (Stripe/Razorpay) CSRF-exempted but signature-authenticated.
- Newer extracted ServiceCompany controllers pair `abort_if(Gate::denies())` with
  scoped queries — the pattern to replicate.
- `EnsureOneDeviceLogin` epoch gate invalidates sessions company-wide on
  suspend/block (company:epoch keys).

---

## 5. Remediation Priorities (recommendations only — NOT implemented)

1. **P0 X1:** Add ownership verification (document → application → company_id ==
   user company_id) to every file-download route; move to signed URLs.
2. **P0 X2:** Fix `Company::booted()` — scope by `company_id` whenever an
   authenticated non-superadmin exists, regardless of roles.
3. **P1 X3/X4:** Roll a `BelongsToTenant` scope/trait across tenant models; sweep
   IDOR-style raw fetches; require `where company_id` on every raw tenant query
   (enforce via PHPStan custom rule or ai-guards).
4. **P1 X5:** Replace super-code impersonation with an audited, revocable
   impersonation mechanism; drop `super_password`.
5. **P2 X6:** Central ES query builder injecting company filter by default.
6. **P2:** Add FKs on tenant columns where feasible (schema is out-of-band; needs
   DBA coordination per AI_RULES — never via migrations).
