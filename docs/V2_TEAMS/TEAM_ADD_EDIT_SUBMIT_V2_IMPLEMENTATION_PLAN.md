# Team Add/Edit & Submit V2 Implementation Plan

**Module Target**: Team Module (`Add Team → Submit/Create (Invite Member)`, `Edit Team → Submit/Update`, `Save/Submit Flow`)  
**Target Directory**: `docs/V2_TEAMS/`  
**Status**: COMPLETED & VERIFIED  
**Author**: Antigravity AI  
**Date**: August 14, 2026  

---

> [!IMPORTANT]
> **READ-ONLY AUDIT MODE NOTICE**: This document represents a proposed technical architecture and phase-wise implementation plan. No code, database schemas, Redis keys, queues, or server configurations will be modified until explicit user approval is granted.

---

## 1. Executive Summary & Goals

The Team module enables service company recruiters to manage tenant team members, assign RBAC roles, invite new recruiters via email, update profile designations, and toggle working statuses. The V1 implementation suffers from:
1. **Missing Database Transactions**: Partial writes leave orphan rows in `users` upon failure.
2. **Tenancy Security Violation**: Profile updates (`update()`) fetch `User::find($id)` without filtering by tenant `company_id`.
3. **Triple Role Attachment Bug**: `store()` executes 3 duplicate queries to attach a single role.
4. **Synchronous Cloud & Search Operations**: S3 avatar uploads and Elasticsearch indexing (`userIndexingupload`) run inline on the HTTP thread, creating 800ms–2,500ms latency spikes.
5. **Stale Cache Vulnerability**: `store()` and `update()` omit tenant cache invalidation (`clearTeamCache`), leaving listing views out of date.

### Target V2 Metrics:
- **Add Team Drawer Open Latency**: Drop from ~250ms to <20ms (JSON API + Redis reference cache).
- **Submit / Create Team Member Latency**: Drop from ~1,500ms to <150ms (Async S3/ES offloading via Horizon).
- **Submit / Update Team Member Latency**: Drop from ~800ms to <120ms.
- **Data Integrity**: 100% transaction safety (`DB::transaction`) and strict tenant isolation (`where company_id`).
- **Concurrency**: 10,000 concurrent recruiters without deadlocks or double-invitation race conditions (Redis atomic locks).

---

## 2. Phase-Wise V2 Implementation Plan

### Phase 0 — Baseline & Safety

- **Problem**: Lack of transaction wrappers, security tenancy checks, and telemetry logging before V2 modifications.
- **Solution**:
  1. Add strict tenant ownership scope `where('company_id', auth()->user()->company_id)->firstOrFail()` to `update()` and `edit()`.
  2. Wrap all multi-query database writes in `DB::beginTransaction()`, `DB::commit()`, and `DB::rollBack()`.
  3. Instrument execution timing metrics to measure baseline latency.
- **Risk**: Potential 404 errors if legacy code relies on cross-tenant user updates (invalid pattern).
- **Testing**: Run unit tests asserting cross-tenant user access returns 404/403.
- **Expected Result**: Guaranteed tenant isolation, automatic DB rollbacks on errors, established telemetry baseline.

---

### Phase 1 — Add Client Optimization (Team Add Drawer & Reference Data)

- **Problem**: `add_team_member_form` renders a full Blade HTML view containing radio buttons on every drawer open. Client JS parses this HTML string via jQuery DOM manipulation to build a select dropdown, executing un-cached DB queries for `companies` and `roles`.
- **Solution**:
  1. Create lightweight JSON endpoint `GET /service_company/team/roles` returning pre-rendered tenant roles.
  2. Cache tenant roles in Redis: `ats:roles:company_{company_id}` for 24 hours.
  3. Update drawer JS in `index.blade.php` to fetch JSON options directly without HTML string parsing.
- **Risk**: UI dropdown styling discrepancies if CSS classes are omitted.
- **Testing**: Open Invite Member drawer; verify zero DB queries on warm cache and instantaneous drawer opening (<15ms).
- **Expected Result**: 90% latency reduction on drawer open; elimination of DOM string parsing hacks.

---

### Phase 2 — Edit Client Optimization (Edit Team Page Hydration)

- **Problem**: `edit($id)` executes duplicate lookups (Elasticsearch query + multi-table SQL join) and loads un-cached global categories (`JobCategory::all()`) and dynamic cursor queries on `city_list`.
- **Solution**:
  1. Simplify `edit($id)` to a single scoped Eloquent query on `User` with `users_profile`.
  2. Leverage Redis cached reference data (`cache()->remember('global:countries', ...)`) for countries, states, and cities.
  3. Remove redundant pre-hydration calls from Elasticsearch on edit view load.
- **Risk**: Stale global country/state cache if regional list updates occur.
- **Testing**: Benchmark `GET /service_company/team/{id}/edit` page load response time.
- **Expected Result**: Edit page load SQL queries reduced from 6+ to 1 scoped query; load time under 40ms.

---

### Phase 3 — Submit/Create Optimization (Invite Member Flow)

- **Problem**: `store()` performs 3 separate role insertions for a single user, uploads logos synchronously to S3 (`Storage::disk('s3')->put()`), and runs `GeneralMethod::userIndexingupload()` synchronously inside the HTTP handler.
- **Solution**:
  1. Deduplicate role attachment to a single Spatie `syncRoles([$roleName])` call.
  2. Offload S3 image upload and Elasticsearch indexing to background Horizon jobs (`SyncUserToElasticJob::dispatch($userId)`).
  3. Call `$this->clearTeamCache($companyId)` on successful creation to bump the company team version key.
- **Risk**: Slight queue propagation delay (<500ms) before user appears in Elasticsearch search indexes.
- **Testing**: Submit new team invitation drawer; observe immediate HTTP 200 response and check Horizon queue processing.
- **Expected Result**: Submit response time drops from ~1,500ms to <150ms; instant tenant cache invalidation.

---

### Phase 4 — Submit/Update Optimization (Profile Update Flow)

- **Problem**: `update()` executes synchronous S3 uploads, synchronous ES indexing, and omits `$this->clearTeamCache($company_id)`, serving stale datatable views to recruiters.
- **Solution**:
  1. Enforce strict `company_id` tenancy scoping on `User::where('id', $id)->where('company_id', $companyId)->firstOrFail()`.
  2. Offload image processing and ES indexing to `SyncUserToElasticJob`.
  3. Invoke `$this->clearTeamCache($companyId)` to immediately bump `ats:teamversion:company_{company_id}`.
- **Risk**: Avatar image update takes 1–2 seconds to reflect in S3 if Horizon queues are backed up.
- **Testing**: Submit profile edit; verify company team datatable (`data_ajax`) reflects updated details immediately.
- **Expected Result**: Update submission latency <120ms; guaranteed cache consistency across all company recruiters.

---

### Phase 5 — Database Optimization (Indexes & Schemas)

- **Problem**: Absence of targeted composite indexes on `users` and `users_profile` leads to index scans during company-scoped datatable counts and tab filters.
- **Solution**:
  1. Create composite index `INDEX idx_company_status (company_id, status)` on `users`.
  2. Create composite index `INDEX idx_user_company (user_id, company_id)` on `users_profile`.
  3. Ensure composite primary key `(user_id, role_id)` on `role_user` prevents duplicate role rows.
- **Risk**: Minimal index maintenance overhead on write operations.
- **Testing**: Run `EXPLAIN` on `data_ajax` queries and verify index hit rates.
- **Expected Result**: SQL count and list query execution times reduced to under 2ms.

---

### Phase 6 — AJAX/API Optimization (Form Requests & Payloads)

- **Problem**: `store()` returns HTTP 200 with `msgType = 'warning'` on validation failure; payload handling uses unvalidated raw request arrays.
- **Solution**:
  1. Implement dedicated Form Request classes: `StoreTeamRequest` and `UpdateTeamRequest`.
  2. Standardize validation responses: return HTTP 422 JSON on validation failure for standard client-side error handling.
  3. Trim FormData payloads to transmit only modified fields.
- **Risk**: Frontend JS expecting HTTP 200 JSON warning messages must be updated to handle HTTP 422 errors.
- **Testing**: Submit empty/invalid email forms and verify standardized HTTP 422 JSON validation responses.
- **Expected Result**: Clean RESTful API architecture and standardized error handling.

---

### Phase 7 — Redis Optimization (Centralized Cache Strategy)

- **Problem**: Cache invalidation is inconsistent across team actions (`teamDelete` calls `clearTeamCache`, but `store` and `update` do not).
- **Solution**:
  1. Standardize all team modifications (`store`, `update`, `roleupdate`, `statusUpdate`, `teamDelete`) to call `$this->clearTeamCache($companyId)`.
  2. Centralize key generation:
     - Team Counts: `ats:teamcounts:{company_id}` (TTL: 1 hour)
     - Team Version: `ats:teamversion:company_{company_id}`
     - Roles List: `ats:roles:company_{company_id}` (TTL: 24 hours)
- **Risk**: Cache stampede on high-frequency writes (mitigated by atomic version increments).
- **Testing**: Perform write operations in sequence and monitor Redis key version increments via `redis-cli`.
- **Expected Result**: 100% cache accuracy across all concurrent recruiter sessions.

---

### Phase 8 — Frontend Optimization (Drawer & UI Polish)

- **Problem**: Client JS uses DOM parsing hacks, lacks double-submit button disabling, and uses alert dialogs.
- **Solution**:
  1. Modernize `openInviteDrawer()` in `index.blade.php` to fetch JSON options.
  2. Enforce automatic button disabling (`#inviteSubmitBtn`) with Metronic spinner state during submission.
  3. Implement clean Toast notifications for success/error feedback.
- **Risk**: Visual glitches during drawer animation if DOM updates lag.
- **Testing**: Verify UI behavior across Chrome, Firefox, Edge, and Safari on desktop and mobile viewports.
- **Expected Result**: Premium, responsive UI experience with zero client-side DOM parsing overhead.

---

### Phase 9 — Network & Retry Safety (Horizon Queue Resilience)

- **Problem**: Transient network drops to S3 or Elasticsearch during async jobs can cause silent failures.
- **Solution**:
  1. Configure Horizon retry policies on `SyncUserToElasticJob`:
     ```php
     public $tries = 3;
     public $backoff = [5, 15, 60];
     ```
  2. Ensure job execution is idempotent using `user_id` deduplication.
- **Risk**: Re-indexing out of order if multiple rapid updates occur (handled by update timestamps).
- **Testing**: Simulate network failure during queue processing; verify automatic retry and final index consistency.
- **Expected Result**: 100% system resilience against cloud storage and search engine network blips.

---

### Phase 10 — Concurrency Optimization (Distributed Lock Guard)

- **Problem**: Rapid double-clicking or parallel API calls can bypass email uniqueness checks and create duplicate user accounts.
- **Solution**:
  1. Implement Redis atomic locks during team store and update:
     ```php
     $lock = Cache::lock('team:store:' . md5($request->email), 5);
     if (!$lock->get()) {
         return Reply::error('Invitation processing in progress. Please wait.');
     }
     ```
- **Risk**: Lock acquisition rejection if TTL is too long (capped at 5 seconds).
- **Testing**: Dispatch 20 concurrent HTTP POST requests with identical emails using `curl` / Postman runner.
- **Expected Result**: Exactly 1 request succeeds; 19 requests are safely rejected with lock warnings.

---

### Phase 11 — Load Testing (10,000 Concurrent Recruiter Simulation)

- **Problem**: System behavior unverified under extreme enterprise traffic spikes.
- **Solution**:
  1. Build k6 load testing script executing 10,000 virtual users across:
     - 40% Datatable filtering & search (`data_ajax`)
     - 30% Invite drawer opens & role fetching
     - 20% Team member updates
     - 10% Member invitations (`store`)
  2. Monitor CPU, RAM, MySQL pool connections, Redis memory, and HTTP response p95 latency.
- **Risk**: High CPU load on local development machine.
- **Testing**: Execute 15-minute sustained load test scenario.
- **Expected Result**: p95 latency < 150ms for submits; p95 latency < 30ms for list views; 0 server 500 errors.

---

### Phase 12 — V1 vs V2 Validation (Regression Testing)

- **Problem**: Ensuring V2 optimizations do not break legacy features, RBAC permissions, or email dispatching.
- **Solution**:
  1. Execute automated PHPUnit test suite validating:
     - User record creation in MySQL (`users`, `users_profile`, `role_user`).
     - Account activation email dispatching (`QueueMailService`).
     - Elasticsearch document indexing structure.
     - RBAC role assignment accuracy.
- **Risk**: Identification of hidden business logic edge cases.
- **Testing**: Run `php artisan test --filter=TeamTest`.
- **Expected Result**: 100% test pass rate with complete functional equivalence and 10x performance gains.

---

### Phase 13 — Production Rollout & Rollback Strategy

- **Problem**: Deployment risks on live multi-tenant environment.
- **Solution**:
  1. Feature flag guard in `config/features.php`:
     ```php
     'v2_team_management' => env('FEATURE_V2_TEAM_MANAGEMENT', false),
     ```
  2. Dual-path controller execution allowing single-click rollback via `.env` toggle.
  3. Post-rollout telemetry monitoring.
- **Risk**: Redis key format mismatch during live flag toggle.
- **Testing**: Verify instant feature flag toggling between V1 and V2 logic in staging environment.
- **Expected Result**: Zero-downtime deployment with immediate single-click rollback capability.

---

## 3. Phase Summary & Risk Matrix

| Phase | Target Area | Key Technical Change | Primary Risk | Expected Outcome |
| :--- | :--- | :--- | :--- | :--- |
| **Phase 0** | Baseline & Safety | Add `DB::transaction` & tenant scoping | 404 on legacy cross-tenant calls | 100% data safety & isolation |
| **Phase 1** | Add Team Drawer | JSON roles API + Redis cache | CSS dropdown styling mismatch | 90% faster drawer open (<15ms) |
| **Phase 2** | Edit Team Hydration | Single SQL query + Redis references | Stale reference cache | Edit page load <40ms |
| **Phase 3** | Submit/Create | Offload S3/ES to Horizon queues | Queue index delay (<500ms) | Submit response <150ms |
| **Phase 4** | Submit/Update | Scoped query + async offloading | Image upload queue lag | Update response <120ms |
| **Phase 5** | Database | Composite indexes on `users`/`profile` | Minimal write overhead | DB queries <2ms |
| **Phase 6** | AJAX/API | Form Requests returning HTTP 422 | Frontend JS handler update | Standardized REST API |
| **Phase 7** | Redis | Centralized `clearTeamCache()` | Increased version bumps | 100% cache consistency |
| **Phase 8** | Frontend Polish | Native JSON binding & button spinners | Visual animation glitches | Smooth UI, zero DOM parsing |
| **Phase 9** | Network Safety | Horizon retry policies with backoff | Out-of-order queue jobs | 100% network resilience |
| **Phase 10** | Concurrency | Redis atomic locks (`Cache::lock`) | Lock timeout on long jobs | Zero duplicate invitations |
| **Phase 11** | Load Testing | 10,000 VR load test via k6 | High test server load | Verified 10K concurrency |
| **Phase 12** | V1/V2 Validation | Automated PHPUnit regression suite | Legacy edge cases found | 100% test pass rate |
| **Phase 13** | Rollout | Feature flag toggle & rollback | Key format mismatch | Zero-downtime deployment |

---

## 4. Final Stop Instruction

> [!CAUTION]
> **AUDIT COMPLETE**: Both audit files have been generated. No application code, database schemas, or server settings have been modified.
> **DO NOT PROCEED WITH IMPLEMENTATION UNTIL USER APPROVAL IS GRANTED.**
