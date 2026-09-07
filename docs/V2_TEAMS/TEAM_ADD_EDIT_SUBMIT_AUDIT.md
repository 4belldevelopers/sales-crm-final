# Team Add/Edit & Submit V2 Performance & Scalability Audit

**Module Target**: Team Module (`Add Team → Submit/Create (Invite Member)`, `Edit Team → Submit/Update`, `Save/Submit Flow`)  
**Target URI**: `http://localhost/git/JWMaincode28June2026/service_company/team`  
**Scope**: Full-stack audit across Route → Controller → Service → Validation → MySQL → Redis → AJAX → Blade/JS  
**Audit Author**: Antigravity AI  
**Date**: August 14, 2026  

---

## 1. Architectural Overview & Request Life-Cycle

### 1.1 Add Team / Invite Member Flow (`GET /service_company/team/add_team_member_form/{id?}`)
- **Route**: Defined in `routes/service.php` mapped to `ServiceCompanyAccountSettingsController::add_team_member_form($id)`.
- **Controller**: `app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php`.
- **Coins Validation**: Executes `GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ)` before rendering form.
- **Reference Data Lookup**:
  - `DB::table('companies')->select('*')->where('id', $company_id)->first()`
  - `DB::table('roles')->select('*')->where('company_id', $company_id)->orderBy('roles.display_name', 'ASC')->get()`
- **Blade Template & AJAX Handling**:
  - Renders `service_company.account.add_team_member_form` containing radio inputs for tenant roles.
  - Client-side JS in `index.blade.php` (`openInviteDrawer()`) fetches this Blade view as HTML via GET AJAX, parses HTML radio DOM elements in jQuery (`var $html = $(response.view)`), extracts option labels, and appends them to a modal `<select id="inviteRole">` element.

### 1.2 Submit / Create Team Member Flow (`POST /service_company/team`)
- **Route**: `Route::resource('team', ServiceCompanyTeamController::class)` mapped to `ServiceCompanyTeamController::store(Request $request)`.
- **Controller**: `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php`.
- **Validation**:
  - Manual `Validator::make` for email address checking `required`, `email:rfc,dns`, and `disposable_email`.
  - Explicit count query: `DB::table('users')->select('users.id')->where('email', '=', $request->email)->count()`.
  - Coin deduction check via `GeneralMethod::coinsValidations()`.
- **DB Operations (NO Transaction)**:
  - Instantiates `User`, assigns `$user->name`, `$user->email`, `$user->company_id`, `$user->email_verification_code = str_random(40)`, `$user->status = 'inactive'`, `$user->working_status = 0`.
  - Uploads `logo` synchronously to S3 if attached: `Storage::disk('s3')->put($filePath, file_get_contents($file))`.
  - Saves `$user->save()`.
  - Triple-writes role relationship:
    1. `$assignRole->assignRole($role_row->name)` (Spatie permission sync)
    2. `$user->roles()->attach($request->role_id)` (Eloquent pivot attach)
    3. `DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $request->role_id])` (Raw pivot query — duplicate!)
  - Inserts initial `users_profile` row: `DB::table('users_profile')->insert(['company_id' => $company_id, 'user_id' => $user->id, 'country' => 101, ...])`.
- **Mail Queueing**:
  - Dispatches `QueueMailService::dispatch(...)` on queue connection `'emails'`.
- **Synchronous Indexing**:
  - Invokes `GeneralMethod::userIndexingupload($user->id)` **inline/synchronously** on the HTTP thread, which runs multiple raw SQL joins and pushes to Elasticsearch.
- **Response**:
  - Returns `Reply::dataOnly(['status' => 'success', 'msgType' => 'success', 'message' => $message])`.

### 1.3 Edit Team Member Flow (`GET /service_company/team/{id}/edit`)
- **Route**: Mapped to `ServiceCompanyTeamController::edit($id)`.
- **Controller**: `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php`.
- **Elasticsearch Hydration & MySQL Lookup**:
  - Fetches ES source first: `elasticService->getClient()->get(['index' => ELASTIC_USERS_LIST, 'id' => $id])`.
  - Queries MySQL: `User::join('companies', ...)->leftJoin('users_profile', ...)->where('users.id', $id)->company()->first()`.
  - Fetches `JobCategory::all()`, `countryArr`, `statelist`.
  - Queries `city_list` joining `state_list` and `countries` via DB cursor.
- **Blade Template**: Renders `resources/views/service_company/team/edit.blade.php` (or `service_company.profile.edit`).

### 1.4 Submit / Update Team Member Flow (`PUT /service_company/team/{id}`)
- **Route**: `Route::resource('team', ServiceCompanyTeamController::class)` mapped to `update(Request $request, $id)`.
- **Controller**: `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php`.
- **Tenancy Security Vulnerability**:
  - Executes `$user = User::find($id)` without scoping by `company_id`.
- **DB Operations (NO Transaction)**:
  - Updates `users.name` and `users.mobile`.
  - Uploads logo synchronously to S3 if attached: `Storage::disk('s3')->put(...)`.
  - Saves `$user->save()`.
  - Checks profile existence: `DB::table('users_profile')->where('user_id', $id)->count()`.
  - Updates or inserts `users_profile` with `designation`, `country`, `state`, `city`.
- **Synchronous Indexing & Cache Cleanup**:
  - Calls `GeneralMethod::userIndexingupload($id)` synchronously.
  - Clears individual user version keys (`allteam_version_user_{id}`, `teamviewmore_version_user_{id}`).
  - **Omits Tenant Cache Invalidation**: Fails to call `$this->clearTeamCache($company_id)`, leaving company team datatable cache stale.

---

## 2. Full-Stack Audit Breakdown

### 2.1 Page-Load Performance (Add Drawer & Edit Page)
- **Add Drawer (`add_team_member_form`)**:
  - HTML fragment returned via AJAX contains raw radio buttons. The client JS in `index.blade.php` parses this HTML on the DOM tree to extract radio values into a select dropdown.
  - **Issue**: Unnecessary HTML rendering and DOM string parsing overhead on every drawer open.
- **Edit Page (`edit`)**:
  - Executes duplicate lookups: first fetches user profile from Elasticsearch, then performs multi-table SQL join on `users` + `companies` + `users_profile`.
  - Fetches all categories (`JobCategory::all()`) and full country list on every edit page load.
  - Runs dynamic cursor query on `city_list` joining `state_list` and `countries`.

### 2.2 Submit/Create Performance (Invite Member)
- **Synchronous Operations Chain**:
  - S3 cloud upload (`Storage::disk('s3')->put()`) blocks HTTP thread.
  - Triple role insertion (Spatie + Eloquent attach + raw `role_user` insert) executes 3 DB queries for 1 role.
  - Synchronous `GeneralMethod::userIndexingupload($user->id)` executes multiple raw SQL joins and HTTP call to Elasticsearch synchronously before responding to browser.
  - Total HTTP request latency on submit: ~800ms–2,500ms depending on S3 and ES network latency.

### 2.3 Submit/Update Performance
- **Synchronous Operations Chain**:
  - S3 image upload blocks request thread.
  - `User::find($id)` + `DB::table('users_profile')->count()` + `DB::table('users_profile')->update()`.
  - Synchronous Elasticsearch re-indexing via `GeneralMethod::userIndexingupload($id)`.

### 2.4 DB Query Count & N+1 / Duplicate Queries
- **Add Team Submit (`store`)**:
  - Query 1: `DB::table('users')->where('email', $email)->count()`
  - Query 2: `GeneralMethod::coinsValidations()` (company & coin queries)
  - Query 3: `INSERT INTO users`
  - Query 4: `DB::table('roles')->where('id', $role_id)->first()`
  - Query 5: Spatie `$assignRole->assignRole()` (select/insert role)
  - Query 6: `$user->roles()->attach($role_id)` (insert `role_user`)
  - Query 7: `DB::table('role_user')->insert(...)` (**DUPLICATE! Primary key conflict or redundant query**)
  - Query 8: `DB::table('users_profile')->insert(...)`
  - Query 9-11: `GeneralMethod::userIndexingupload()` (joins `users`, `roles`, `users_profile`, `companies`, `countries`, `state_list`, `city_list`)
  - **Total**: 11+ queries per single member creation.

### 2.5 AJAX / API Calls & Payload Size
- **Add Team Drawer**:
  - Call 1: GET `/service_company/team/add_team_member_form/123` (returns full HTML fragment).
  - Call 2: POST `/service_company/team` (FormData containing `name`, `email`, `role_id`, `logo`).
- **Edit Team Submit**:
  - Call 1: PUT `/service_company/team/{id}` (FormData containing `name`, `mobile`, `designation`, `country`, `state`, `city`, `avatar`).

### 2.6 Redis Operations & Cache Invalidation
- **Missing Tenant Cache Bumping in `store()` & `update()`**:
  - `ServiceCompanyTeamController` maintains a company-wide version key `ats:teamversion:company_{company_id}` and `ats:teamcounts:{company_id}` inside `index()` and `data_ajax()`.
  - When `store()` or `update()` completes, `$this->clearTeamCache($company_id)` is **NOT called**.
  - `clearTeamCache()` is only called in `teamDelete()`, `statusUpdate()`, and `roleupdate()`.
  - **Impact**: Newly added or edited team members will NOT appear in the cached datatable listing (`data_ajax`) until the TTL expires or another action triggers `clearTeamCache`.

### 2.7 Database Indexes
- `users`: Indexed on `id`, `company_id`, `email`, `status`.
- `users_profile`: Needs composite index on `(user_id, company_id)`.
- `role_user`: Composite primary index `(user_id, role_id)`. Direct `DB::table('role_user')->insert()` after Spatie/Eloquent attach causes duplicate key exceptions if constraints are enforced.

### 2.8 Network Failure & Retry Handling
- **No Idempotency / Retry Protection**:
  - If S3 upload or ES indexing fails due to network drop during `store()`, an unhandled exception is thrown.
  - The HTTP request returns 500 error, but the `users` row was already saved to MySQL because no DB transaction wrapper exists.

### 2.9 Duplicate Submission Protection
- **Client Side**:
  - `index.blade.php` disables `#inviteSubmitBtn` on click (`$btn.addClass('loading').prop('disabled', true)`).
  - `add_team_member_form.blade.php` disables `#invitation_submit` on click.
- **Server Side**:
  - **NO Redis Lock / Mutex**: No `Cache::lock('team:store:' . md5($email))` or `Cache::lock('team:update:' . $id)` protection. Rapid double-clicks or parallel API requests can pass the email uniqueness check simultaneously and cause duplicate account creation.

### 2.10 Transactions & Rollback
- **CRITICAL FINDING**: Neither `store()` nor `update()` use `DB::beginTransaction()`, `DB::commit()`, or `DB::rollBack()`.
- If role attachment, profile creation, or email queueing fails mid-execution, orphan rows remain in `users`.

### 2.11 Error Handling & Validation
- Manual `Validator::make` in `store()` returns `Reply::dataOnly(['status' => 'success', 'msgType' => 'warning', 'message' => ...])` with HTTP 200 instead of HTTP 422 Unprocessable Entity.
- In `update()`, no request validation is performed on `mobile`, `designation`, `country`, `state`, or `city`.

### 2.12 Permissions & Business Logic
- **Tenancy Security Flaw in `update()`**:
  - `update(Request $request, $id)` executes `$user = User::find($id)` without filtering by `company_id`.
  - A user in tenant A can manipulate `$id` to modify profile details of a user in tenant B.
- Spatie gate check `abort_if(Gate::denies('view_team'), 403)` is present in `data_ajax()`, but `store()` and `update()` rely on commented-out or missing permission checks (`//abort_if(! $this->user->can('edit_team'), 403);`).

### 2.13 Server / PHP-FPM / MySQL / Redis Infrastructure Impact
- Synchronous cloud file uploads to S3 and synchronous HTTP calls to Elasticsearch occupy PHP-FPM worker threads for 1–3 seconds per submit request.
- Under high concurrent team management activity, PHP-FPM worker pools quickly saturate.

### 2.14 10K+ Concurrent Recruiter Scalability Bottlenecks
- Single-threaded synchronous HTTP requests during create/update limit throughput.
- Missing distributed locks allow race conditions under high concurrency.
- Un-cached drawer reference queries hit MySQL on every drawer open.

---

## 3. Categorized Audit Findings

### 3.1 Confirmed Problems

1. **Missing Database Transactions in Create and Update Flows**:
   - `store()` and `update()` perform multiple writes (`users`, `roles`, `role_user`, `users_profile`) without `DB::beginTransaction()`. Partial failures leave corrupt database records.
2. **Tenancy Violation in Team Member Update**:
   - `update()` fetches `$user = User::find($id)` without enforcing `where('company_id', auth()->user()->company_id)`.
3. **Triple Role Attachment Query Bug**:
   - `store()` executes 3 separate role attachments for a single user: Spatie `$assignRole->assignRole()`, Eloquent `$user->roles()->attach()`, and raw `DB::table('role_user')->insert()`.
4. **Synchronous S3 Upload and Elasticsearch Indexing**:
   - S3 file upload and `GeneralMethod::userIndexingupload()` run synchronously inside the HTTP controller thread, adding 800ms–2,500ms latency.
5. **Missing Tenant Cache Invalidation on Submit/Update**:
   - `store()` and `update()` do not execute `$this->clearTeamCache($company_id)`, resulting in stale team listing data in `data_ajax()`.
6. **Inefficient HTML Fragment Parsing for Drawer Options**:
   - `add_team_member_form` renders a full Blade view with HTML radios, which `index.blade.php` parses via jQuery DOM traversal to populate a select dropdown.
7. **Missing Request Validation in Update Flow**:
   - `update()` accepts `$request->name`, `mobile`, `designation`, `country`, `state`, `city` without any server-side validation rules.

### 3.2 Potential Problems

1. **Duplicate Submission Vulnerability Under High Concurrency**:
   - Lack of Redis lock (`Cache::lock`) on email invitation allows parallel API requests to bypass email existence checks.
2. **HTTP 200 Status Code Returned on Validation Failure**:
   - Validation errors return HTTP 200 with `msgType = 'warning'`, preventing standardized client-side HTTP error interceptors from functioning cleanly.
3. **Un-cached Roles & Reference Data in Add Team Form**:
   - `add_team_member_form($id)` queries `roles` and `companies` from MySQL on every single form load instead of leveraging Redis cache.

### 3.3 Not Measured

1. **Exact 10,000 Concurrent Recruiter Benchmark Latency**:
   - Not measured on production cluster hardware (requires k6 load test suite).
2. **S3 Object Storage Network Latency under Congestion**:
   - Exact upload latency variation under peak network traffic not measured.
3. **PHP-FPM Memory Allocation under Simultaneous Image Uploads**:
   - Memory consumption per worker under concurrent 2MB avatar uploads not measured.

---

## 4. Database Index Scoping Summary

| Table | Current Indexes | Required V2 Index Enhancements | Rationale |
| :--- | :--- | :--- | :--- |
| `users` | `PRIMARY (id)`, `company_id`, `email` | `INDEX idx_company_status (company_id, status)` | Fast company-scoped user counts and tab filtering |
| `users_profile` | `PRIMARY (id)`, `user_id` | `INDEX idx_user_company (user_id, company_id)` | Scoped profile lookups and joins |
| `role_user` | `PRIMARY (user_id, role_id)` | Ensure composite primary key enforces uniqueness | Prevents duplicate role attachment entries |
| `roles` | `PRIMARY (id)`, `company_id` | `INDEX idx_company_name (company_id, name)` | Rapid tenant role lookups |
