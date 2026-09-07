# Role Permission Listing V2 — Technical Implementation Plan

> **Document Type:** Architectural Blueprint & V2 Implementation Specification  
> **Target Page:** Role Permission Listing (`service_company/role-permission`)  
> **Target URL:** `http://localhost/git/JWMaincode28June2026/service_company/role-permission`  
> **Status:** Draft / Awaiting Approval (DO NOT IMPLEMENT)  
> **Date:** August 12, 2026  

---

## 1. V2 Objective & Architectural Vision

The primary objective of the **Role Permission Listing V2** refactor is to transform the existing un-optimized, query-heavy implementation into a high-performance, enterprise-grade, zero-N+1 module capable of serving **10,000+ concurrent recruiters** with sub-50ms response times.

### Key Goals & Metrics
* **Query Reduction:** Reduce page load database queries from **N+7 queries to exactly 2 queries** (1 for roles, 1 for aggregated user counts).
* **Zero DB Queries in Views:** 100% removal of SQL queries from `index.blade.php`.
* **Sub-50ms Response Latency:** Leverage Redis caching for tenant role-permission structures.
* **10k Concurrency Ready:** Eliminate PHP-FPM memory spikes by removing heavy stringified JSON DOM attributes and optimizing collection transformations.
* **100% Business Logic Preservation:** Maintain strict tenant isolation (`company_id`), system role protections, default 'Recruiter' fallback on role deletion, and requisition permission rules (IDs 58 & 59).

---

## 2. Proposed V2 Architecture

```
[ HTTP Request: GET /service_company/role-permission ]
                         │
                         ▼
[ ServiceCompanyRolesPermissionController@index ]
                         │
                         ▼
[ RolePermissionService ] ◄────► [ Redis Cache Layer ]
                         │       (Key: tenant:{company_id}:roles_permissions)
                         ▼
[ RolePermissionRepository ]
                         │
                         ▼
[ Optimized MySQL Queries ]
  ├─ Query 1: Roles + Permissions (Single Joined/Eager Query)
  └─ Query 2: Aggregated User Counts & Avatars (GROUP BY role_id)
                         │
                         ▼
[ Clean Data Transfer Object (DTO) / View ViewModel ]
                         │
                         ▼
[ Pure Blade View: index.blade.php ] (0 Database Queries)
```

### Layer Separation
1. **Controller (`ServiceCompanyRolesPermissionController`):** Thin HTTP handler. Responsible solely for authorization checks, invoking `RolePermissionService`, and returning the Blade response.
2. **Service (`RolePermissionService`):** Handles business logic, coverage bucket math, Redis cache lookup/invalidation, and transforming raw DB data into a structured ViewModel.
3. **Repository (`RolePermissionRepository`):** Encapsulates all Eloquent / DB Query Builder calls. Ensures 100% tenant-scoped queries (`company_id`).
4. **View (`index.blade.php`):** Pure presentation layer. Rendered strictly from variables passed by the controller.

---

## 3. Query & N+1 Optimization Strategy

### Problem 1: N+1 User Fetching in Blade
* **Current:** In `index.blade.php`, `DB::table('role_user')` runs in a loop for each role.
* **V2 Solution:** Fetch user counts and top 4 user avatars for all tenant roles in a single aggregated query in `RolePermissionRepository`:
  ```php
  // Single aggregated query for user counts & sample avatars per role
  $roleUserStats = DB::table('role_user')
      ->join('users', 'users.id', '=', 'role_user.user_id')
      ->whereIn('role_user.role_id', $roleIds)
      ->where('users.type', 0)
      ->select(
          'role_user.role_id',
          DB::raw('COUNT(users.id) as user_count'),
          DB::raw("GROUP_CONCAT(CONCAT(users.id, ':', users.name) SEPARATOR ';') as user_sample")
      )
      ->groupBy('role_user.role_id')
      ->get()
      ->keyBy('role_id');
  ```
* **Risk:** Low. Standard MySQL aggregation.
* **Testing:** Run `DB::enableQueryLog()` during index load and verify query count drops from N+7 to 2.

### Problem 2: Variable Bug & Redundant Role Queries
* **Current:** `$company_id = '';` mutates company context and triggers redundant queries.
* **V2 Solution:** Remove `$company_id = '';` assignment. Consolidate role fetching into a single repository call.
* **Risk:** Low.
* **Testing:** Verify `$roles` correctly returns company-specific custom and system roles.

### Problem 3: Multiple `permissions` Table Scans
* **Current:** `permissions` table is scanned 3 times (controller count, Blade display names, Blade group totals).
* **V2 Solution:** Load active permissions once in `RolePermissionRepository` and store static system permission metadata in Redis or PHP static cache.
* **Risk:** Low.
* **Testing:** Verify total group counts match expected values without secondary queries.

---

## 4. Pagination, Search, Filter & Sort Strategy

* **Pagination:** For tenants with >50 roles, implement server-side pagination (`paginate(15)`). For standard setups (<50 roles), deliver a lightweight cached JSON payload.
* **Search & Filter:** Move filter logic to a clean client-side JS filter engine operating on lightweight dataset attributes (or server-side AJAX filtering if paginated).
* **Sorting:** Client-side sorting on DOM cards by `name`, `users`, or `permissions` without page reloads.

---

## 5. Database Indexing Strategy

Execute schema optimizations via documented migrations / DB scripts:

```sql
-- Composite index for tenant role listing and sorting
ALTER TABLE `roles` ADD INDEX `idx_roles_company_priority_name` (`company_id`, `priority`, `name`);

-- Composite index for active permissions grouping
ALTER TABLE `permissions` ADD INDEX `idx_permissions_status_group` (`status`, `group_name`, `id`);

-- Composite index for user role lookups
ALTER TABLE `role_user` ADD INDEX `idx_role_user_role_user` (`role_id`, `user_id`);
```

### Recommendation Analysis
* **Problem:** In-memory filesort during `ORDER BY priority ASC, name ASC` on tenant roles.
* **Solution:** Composite index `idx_roles_company_priority_name`.
* **Risk:** Low. Non-blocking index creation.
* **Testing:** Run `EXPLAIN SELECT * FROM roles WHERE company_id = 1 ORDER BY priority ASC, name ASC;` and confirm `Using index` without `Using filesort`.

---

## 6. Cache (Redis), Elasticsearch & Queue Strategy

### Redis Caching Strategy
* **Cache Key:** `tenant:{company_id}:roles_permissions_v2`
* **TTL:** 86,400 seconds (24 hours) with automatic cache invalidation on write events.
* **Cache Invalidation Events:**
  - `saveRole()` (Role creation or update)
  - `deleteRole()` / `manualDeleteRole()` (Role deletion)
  - `assignAllPermission()` / `removeAllPermission()`
  - `assignRole()` / `detachRole()`
* **Elasticsearch Usage:** Not required for ACL roles & permissions. MySQL + Redis provides optimal ACID compliance and sub-millisecond cache hits.
* **Queue Strategy:** Fire background job `ProcessRolePermissionAuditLog` when permissions are modified to maintain audit logs without blocking the HTTP response thread.

---

## 7. Server & 10,000+ Concurrency Strategy

To scale to 10,000+ concurrent recruiters on the target server (8 CPU Cores, 31.34 GB RAM, Ubuntu 24.04):

1. **PHP-FPM Pool Configuration:**
   ```ini
   pm = dynamic
   pm.max_children = 120
   pm.start_servers = 20
   pm.min_spare_servers = 10
   pm.max_spare_servers = 30
   pm.max_requests = 1000
   ```
2. **OPcache Optimization:**
   ```ini
   opcache.enable=1
   opcache.memory_consumption=512
   opcache.interned_strings_buffer=64
   opcache.max_accelerated_files=30000
   opcache.validate_timestamps=0
   ```
3. **Nginx Response Compression & Buffering:**
   - Enable `gzip` compression for JSON/HTML payloads to shrink network footprint.

---

## 8. Business Logic Preservation Checklist

To ensure zero regressions, V2 implementation MUST adhere to:

* [x] **Tenant Boundary:** Filter every role and user query by `company_id = auth()->user()->company_id`.
* [x] **System Role Lock:** Prevent editing or deleting roles with `manual_add = 0`.
* [x] **Admin Role Lock:** Prevent editing or deleting role with `name = 'admin'` or `priority = 0`.
* [x] **Role Deletion User Reassignment:** When deleting a role, auto-update all users in `role_user` to the 'Recruiter' system role ID before dropping the role record.
* [x] **Requisition Permission Flags:** Preserve special handling for permission ID 58 (`all_requisition`) and permission ID 59 (`add_team_on_requisition`).

---

## 9. Phase-Wise Implementation Roadmap

The V2 implementation will be executed strictly in ordered phases:

| Phase | Component / Module | Scope of Work | Estimated Risk | Verification Criteria |
|-------|--------------------|---------------|----------------|-----------------------|
| **Phase 1** | Service & Repository | Create `RolePermissionRepository` and `RolePermissionService`. Move data aggregation out of Controller & Blade. | Low | Unit tests pass for repository queries; zero logic in Blade. |
| **Phase 2** | N+1 Query Removal | Replace Blade loop `role_user` queries with single `GROUP BY role_id` query. Fix `$company_id = ''` bug. | Low | `DB::enableQueryLog()` confirms <= 2 queries on page load. |
| **Phase 3** | Blade View Refactoring | Clean up `index.blade.php`. Remove inline `@php` DB calls. Remove heavy `data-role-detail` DOM JSON. | Low | Page renders cleanly; DOM size reduced by 60%. |
| **Phase 4** | Redis Caching | Implement `tenant:{company_id}:roles_permissions_v2` Redis cache with invalidation hooks on write actions. | Medium | Response latency drops under 50ms on warm cache. |
| **Phase 5** | Database Indexing | Execute migration adding composite indexes on `roles`, `permissions`, and `role_user`. | Low | `EXPLAIN` query plan shows `Using index` without `filesort`. |
| **Phase 6** | Automated Testing & Load Audit | Run end-to-end unit/feature tests and 1,000+ request concurrency smoke test. | Low | `php artisan test` green; zero HTTP 500 errors under load. |

---

## 10. Structured Recommendations Matrix

### Recommendation 1: Service/Repository Layer Extraction
* **Problem:** Database queries and grouping logic are embedded inside `index.blade.php` and `ServiceCompanyRolesPermissionController.php`.
* **Solution:** Create `RolePermissionRepository` and `RolePermissionService` to encapsulate all ACL queries and data transformations.
* **Risk:** Low. Preserves exact data structures.
* **Testing:** Run `php artisan test --filter=RolePermissionTest` and verify ViewModel matches expected schema.

### Recommendation 2: Single-Query User Aggregation (N+1 Elimination)
* **Problem:** `DB::table('role_user')` runs in a loop for each role inside Blade template.
* **Solution:** Execute a single `GROUP BY role_id` query joining `role_user` and `users` to fetch count and top user avatars.
* **Risk:** Low.
* **Testing:** Count executed SQL statements using `DB::getQueryLog()`; verify query count is exactly 2.

### Recommendation 3: Redis Tenant-Level Cache Layer
* **Problem:** Role permission cards and coverage stats are recomputed on every page request.
* **Solution:** Cache full processed ViewModel in Redis under `tenant:{company_id}:roles_permissions_v2`. Invalidate cache on role mutations.
* **Risk:** Medium. Requires comprehensive cache invalidation coverage across all role write endpoints.
* **Testing:** Verify cache hit on second page load; verify cache flush on role creation/update/deletion.

### Recommendation 4: Composite Database Indexing
* **Problem:** In-memory filesort on `roles` table during `ORDER BY priority ASC, name ASC`.
* **Solution:** Add composite index `idx_roles_company_priority_name` on `roles(company_id, priority, name)`.
* **Risk:** Low.
* **Testing:** Execute `EXPLAIN SELECT * FROM roles WHERE company_id = X ORDER BY priority, name;` and confirm index usage.

---

## 11. Testing, Rollout & Rollback Plan

### Automated Testing Plan
1. **Linting & Syntax Check:** Run `php -l` on all modified files.
2. **Code Style Check:** Run `./vendor/bin/pint --test` on modified files.
3. **Feature Test:** Execute `php artisan test --filter=RolePermissionTest`.

### Rollout Strategy (Zero Downtime)
1. Deploy V2 Service, Repository, and refactored Blade view.
2. Apply database indexes concurrently (`ALTER TABLE ...`).
3. Warm up Redis caches for active tenants via a background Artisan command.

### Rollback Strategy
1. If issues arise, clear Redis tenant keys (`php artisan cache:clear`).
2. Revert git commit to restore V1 controller and view files.
3. Database schema changes (indexes) are additive and do not require immediate rollback.

