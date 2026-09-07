# Role Permission Listing — Technical & Architectural Audit Report (V1)

> **Document Type:** Audit & Technical Analysis  
> **Target Page:** Role Permission Listing (`service_company/role-permission`)  
> **Target URL:** `http://localhost/git/JWMaincode28June2026/service_company/role-permission`  
> **Status:** Completed Audit (No Code Modified)  
> **Date:** August 12, 2026  

---

## 1. Executive Summary & Page Flow Overview

The **Role Permission Listing** page is a core security and access control dashboard within the JERO B2B hiring SaaS platform. It allows service company administrators to view, create, edit, and delete user roles, assign permission groups, and monitor role distribution across company team members.

### Complete Page Flow Architecture
```
[ Browser Request: GET /service_company/role-permission ]
                         │
                         ▼
[ Route Definition: routes/service.php ]
  └─ Route::resource('role-permission', ServiceCompanyRolesPermissionController::class)
                         │
                         ▼
[ Controller: ServiceCompanyRolesPermissionController@index ]
  ├─ 1. Gate Authorization Check: 'view_roles_permission'
  ├─ 2. Fetch Roles via Eloquent: Role::company($company_id)->with(['permissions'])
  ├─ 3. Group permissions in memory per role
  ├─ 4. Query total permissions count & fetch all modules
  └─ 5. Execute DB::table('roles') where company_id = '' (BUG: $company_id reset to '')
                         │
                         ▼
[ View Template: resources/views/service_company/role-permission/index.blade.php ]
  ├─ 1. [CRITICAL BUG] Execute raw DB queries inside Blade PHP block:
  │      ├─ DB::table('permissions')->pluck('display_name', 'name')
  │      ├─ DB::table('permissions')->where('status', 1)->groupBy('group_name')...
  │      └─ [N+1 QUERY LOOP] DB::table('role_user')->join('users')... for EACH role!
  ├─ 2. Compute coverage metrics & format role metadata into JSON strings
  └─ 3. Render HTML grid cards with data-role-detail DOM attributes
                         │
                         ▼
[ Client-Side Browser Execution ]
  ├─ Vanilla JS / jQuery handles search input, filters (Type, Access), and sorting
  └─ Card click / Edit click opens Role Form Drawer via AJAX (loadEditRole) or DOM JSON
```

---

## 2. File & Architectural Audit

### Primary Codebase Artifacts
1. **Route File:** `routes/service.php` (Lines 495–512)
   - Active Route: `Route::resource('role-permission', ServiceCompanyRolesPermissionController::class);`
   - Auxiliary AJAX Routes: `loadRole/{id?}`, `loadEditRole/{id?}`, `saveRole`, `assignAllPermission`, `removeAllPermission`, `storeRole`, `deleteRole`, `manualDeleteRole`.
2. **Controller File:** [ServiceCompanyRolesPermissionController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyRolesPermissionController.php)
   - Class: `App\Http\Controllers\ServiceCompany\ServiceCompanyRolesPermissionController`
   - Extending: `ServiceCompanyBaseController`
3. **Legacy/Inactive Controller:** [ManageRolePermissionController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ManageRolePermissionController.php)
   - Contained commented-out resource route definitions under `settings/role-permission`.
4. **Primary View File:** [index.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/role-permission/index.blade.php)
   - Extends layout `layouts.service_company_layout`.
   - Contains 1,765 lines of combined Blade, inline PHP database queries, CSS styles, and client-side JavaScript.
5. **Partial View File:** `resources/views/service_company/role-permission/loadEditRole.blade.php`
   - Rendered asynchronously when editing a role drawer.

### Key Architectural Antipatterns Identified
* **Database Queries inside Blade View:** Logic for querying assigned users and calculating permission coverage totals is embedded directly inside `@php` / `<?php ?>` blocks in `index.blade.php`.
* **Missing Repository/Service Layer:** Business logic and database operations are executed directly inside the controller and Blade template.
* **Variable Scope Mutation Bug:** In `ServiceCompanyRolesPermissionController@index`, `$company_id` is retrieved from `auth()->user()->company_id` on line 94, used on line 96, and then explicitly overwritten with `$company_id = '';` on line 111 right before querying `$this->roleslist` on line 116.
* **HTML Payload Inflation:** Detail payloads for all roles (including assigned user list, coverage percentages, and permissions) are stringified to JSON and injected into `data-role-detail` attributes on every HTML card element.

---

## 3. Database Query & Performance Audit

### Complete Execution Trace on Page Load (`index()`)

| # | Query Type | Source Location | SQL / Logic Description | Latency Impact |
|---|------------|-----------------|-------------------------|----------------|
| 1 | SELECT | Controller (Line 96) | `SELECT * FROM roles WHERE company_id = ?` | Moderate |
| 2 | SELECT | Controller (Line 97) | `SELECT id, name, group_name FROM permissions WHERE id IN (...)` (eager loaded via Spatie) | Moderate |
| 3 | SELECT | Controller (Line 114) | `SELECT COUNT(*) FROM permissions` | Low |
| 4 | SELECT | Controller (Line 115) | `SELECT * FROM modules` | Low |
| 5 | SELECT | Controller (Line 116) | `SELECT id, display_name, priority, name, manual_add FROM roles WHERE company_id = '' ORDER BY priority ASC, name ASC` (**BUG: Queries empty string company_id**) | Low (Wasted) |
| 6 | SELECT | Blade View (Line 667) | `SELECT name, display_name FROM permissions` | Moderate |
| 7 | SELECT | Blade View (Line 686) | `SELECT group_name, COUNT(*) as cnt FROM permissions WHERE status = 1 AND id NOT IN (58,59,14,15,16,17) GROUP BY group_name` | Moderate |
| 8..N | SELECT | Blade View (Line 705) | **[N+1 LOOP]** `SELECT users.id, users.name, users.email FROM role_user JOIN users ON users.id = role_user.user_id WHERE role_user.role_id = ? AND users.type = 0` (Executed for EVERY role) | **CRITICAL (N+1)** |

### N+1 Query Problem Breakdown
In `resources/views/service_company/role-permission/index.blade.php`:
```php
// Executed inside foreach ($roles as $rlist) loop:
$roleUsers = DB::table('role_user')
    ->join('users', 'users.id', '=', 'role_user.user_id')
    ->where('role_user.role_id', $roleId)
    ->where('users.type', 0)
    ->select('users.id', 'users.name', 'users.email')
    ->get();
```
* **Impact:** For a tenant with 15 roles, 15 database round-trips are executed during Blade rendering.
* **Root Cause:** Eager loading was not performed in `ServiceCompanyRolesPermissionController` for the `users` relationship.

### Duplicate & Redundant Queries
1. **`$this->roleslist` Query:** Redundant with `$this->roles`. It returns incorrect/empty datasets because `$company_id` is set to `''` before invocation.
2. **`permissions` Table Scans:** The `permissions` table is queried in the controller (`Permission::count()`), once in Blade for display names (`pluck('display_name', 'name')`), and once in Blade for group totals (`groupTotals`).

---

## 4. Relevant vs. Unused Data & Memory Audit

* **Unused Data:** 
  - `$this->roleslist` loaded in controller is never rendered in `index.blade.php` (the view iterates over `$roles`).
  - `$this->modules` is fetched (`Module::all()`) but unused on the primary listing page.
  - `$this->totalPermissions` is calculated but replaced by `$groupTotals` inside Blade.
* **PHP Memory Footprint:**
  - `Role::company($company_id)->with('permissions')->get()` loads full Eloquent model instances into memory.
  - In-memory collection grouping (`$roles->each(...)`) duplicates permission string arrays in memory.
  - Blade template constructs heavy multi-dimensional associative arrays (`$allRoleStats`) holding full user objects and coverage metrics for all roles simultaneously.
* **HTML Payload Size:**
  - Storing stringified JSON (`data-role-detail`) on every card DOM element adds ~2–5 KB of JSON overhead per card. For 20 roles, this adds ~100 KB of HTML string bloat.

---

## 5. Pagination, Search, Filter & Sort Audit

| Feature | Current Implementation | Status / Finding |
|---------|------------------------|------------------|
| **Pagination** | None. All roles fetched at once. | **Missing.** Unscalable for large tenant setups. |
| **Search** | Client-side DOM filtering on `#roleSearch` input event. | Works for <50 cards; slow on large DOMs. |
| **Filtering** | Client-side DOM toggle on `#filterRoleType` and `#filterAccessLevel`. | Pure JS; no server-side SQL filtering. |
| **Sorting** | Client-side DOM element reordering on `#sortRoles` change event. | Re-sorts `.rp-role-card` elements in JS. |

---

## 6. Database Indexes & Schema Audit

### Related Database Tables
Based on `database/schema/mysql-schema.sql`:

1. **`roles` Table:**
   - Primary Key: `id` (`int unsigned AUTO_INCREMENT`)
   - Foreign Keys / Indexes: `KEY roles_company_id_foreign (company_id)`
   - Columns: `id`, `company_id`, `name`, `guard_name`, `display_name`, `description`, `created_at`, `updated_at`, `priority`, `manual_add`.

2. **`role_user` Table:**
   - Primary Key: `(user_id, role_id)`
   - Indexes: `KEY role_user_role_id_foreign (role_id)`

3. **`permissions` Table:**
   - Primary Key: `id` (`int unsigned AUTO_INCREMENT`)
   - Unique Keys: `UNIQUE KEY permissions_name_unique (name)`
   - Indexes: `KEY permissions_module_id_foreign (module_id)`

4. **`role_has_permissions` Table:**
   - Primary Key: `(permission_id, role_id)`
   - Indexes: `KEY role_has_permissions_role_id_foreign (role_id)`

### Missing Composite Indexes Analysis
* `role_user`: Current primary key is `(user_id, role_id)`. Querying `WHERE role_id = ? AND users.type = 0` uses `role_user_role_id_foreign` index, which requires a secondary table lookup on `users`.
* `roles`: Querying `WHERE company_id = ? ORDER BY priority ASC, name ASC` uses `roles_company_id_foreign`, but requires an `in-memory filesort` for ordering by `priority` and `name`.
* **Missing Index Recommendation:**
  - `roles(company_id, priority, name)` -> Eliminates filesort for tenant role listing.
  - `permissions(status, group_name, id)` -> Accelerates group total counts and filtering.

---

## 7. Infrastructure & Server Environment Audit

### Server Specifications (Production Baseline)
* **OS:** Ubuntu 24.04 LTS (Control Panel: aaPanel 8.0.3)
* **CPU Cores:** 8 CPU Cores
* **System RAM:** 31.34 GB
* **Disk Space:** 386.4 GB SSD/NVMe

### Runtime Component Stack

| Component | Status / Local Dev / Target | Audit Observation |
|-----------|-----------------------------|--------------------|
| **Web Server** | Nginx / Apache | Managed via aaPanel on production |
| **PHP Runtime** | PHP 8.2.12 (CLI ZTS Visual C++ 2019 x64) | Production utilizes PHP-FPM 8.x |
| **OPcache** | Configured on production; Not loaded on CLI | Must ensure `opcache.validate_timestamps=0` in production |
| **MySQL / MariaDB** | MySQL 8.x / MariaDB | InnoDB engine used across all role tables |
| **Redis** | `redis` PHP extension active | **Not utilized** for role/permission caching |
| **Elasticsearch** | Configured in codebase | **Not utilized** for role/permission listing |
| **Queue Workers** | Laravel Horizon / Redis Queue | **Not utilized** for role updates or audit logs |

---

## 8. 10,000+ Concurrent Recruiters Scaling Risks

1. **Database Connection Saturation:**
   - If 1,000 recruiters access role management simultaneously, the N+1 queries executed in Blade will spawn 15,000+ SQL queries, instantly exhausting the MySQL connection pool.
2. **PHP-FPM Memory Exhaustion:**
   - Eager-loading permissions and rendering massive inline JSON strings into Blade cards consumes significant memory per FPM worker (~15–25 MB per request). At high concurrency, FPM worker memory limit will be breached.
3. **CPU Overhead from Uncached Blade Views:**
   - Re-computing permission coverage buckets and querying the database inside Blade templates on every request wastes CPU cycles on the web server.

---

## 9. Categorized Findings & Vulnerabilities

### Confirmed Problems
1. **Critical N+1 Queries in Blade:** `DB::table('role_user')` executed in a loop inside `index.blade.php`.
2. **Variable Overwrite Bug:** `$company_id` reset to `''` before `$this->roleslist` query in `ServiceCompanyRolesPermissionController@index`.
3. **Database Operations in View Layer:** SQL queries (`permissions` pluck, group totals, assigned users) executed directly inside `index.blade.php`.
4. **Duplicate Queries:** `permissions` table queried 3 separate times during a single page render.
5. **No Caching Layer:** Zero Redis caching implemented for static role and permission definitions.

### Potential Problems
1. **Filesort Overhead:** `roles` table lacks a composite index on `(company_id, priority, name)`.
2. **DOM Bloat:** Large number of custom roles will slow down client-side DOM rendering due to embedded `data-role-detail` JSON strings.

### Not Measured
1. Production Redis cache hit/miss ratio for ACL lookups.
2. Exact query execution latency under 10,000 concurrent HTTP requests (requires load test environment).

---

## 10. Security & Business Logic Preservation Requirements

The following core business logic **MUST remain 100% unchanged** in V2:

1. **Tenant Security Boundary:** All role queries must strictly filter by `company_id = auth()->user()->company_id`.
2. **System vs. Custom Role Distinction:**
   - `manual_add = 0` indicates built-in System Roles.
   - `manual_add = 1` indicates tenant-created Custom Roles.
3. **Admin & Priority System Protection:**
   - Role with `name = 'admin'` or `priority = 0` **cannot be edited or deleted**.
   - Custom roles cannot overwrite system role names.
4. **Role Deletion Safety Net:**
   - Before deleting any role, all assigned users in `role_user` **must be reassigned** to the default 'Recruiter' system role.
5. **Special Requisition Permissions:**
   - Permission ID `58` (`all_requisition`) and Permission ID `59` (`add_team_on_requisition`) have mandatory conditional handling in `saveRole()`.

---

## 11. Comprehensive Audit Recommendations

### Recommendation 1: Move Data Aggregation from Blade to Service Layer
* **Problem:** Database queries and data mapping logic are embedded inside `index.blade.php`.
* **Solution:** Create `RolePermissionService` and `RolePermissionRepository`. Perform all queries, user count aggregations, and coverage calculations in PHP before passing a clean Data Transfer Object (DTO) or array to Blade.
* **Risk:** Low. No change to rendered data structure.
* **Testing:** Verify view receives identical data variables; confirm zero DB queries executed inside Blade view.

### Recommendation 2: Eliminate N+1 Queries via Single SQL Join/Group Query
* **Problem:** `role_user` queried N times in Blade loop for user counts and avatars.
* **Solution:** Fetch user counts and top 4 user avatars for all roles in a single aggregated query grouped by `role_id`:
  ```sql
  SELECT role_id, COUNT(user_id) as user_count 
  FROM role_user 
  JOIN users ON users.id = role_user.user_id 
  WHERE users.type = 0 
  GROUP BY role_id;
  ```
* **Risk:** Low.
* **Testing:** Log database query count using `DB::enableQueryLog()`; verify query count drops from N+7 to exactly 3 queries.

### Recommendation 3: Fix `$company_id` Variable Bug & Remove Redundant Queries
* **Problem:** `$company_id = '';` line breaks `$this->roleslist` and triggers redundant queries.
* **Solution:** Remove line 111 in `ServiceCompanyRolesPermissionController.php` and consolidate role queries.
* **Risk:** Low.
* **Testing:** Inspect controller output; verify no queries with `company_id = ''`.

### Recommendation 4: Implement Tenant-Level Redis Caching
* **Problem:** Role permission structures and group totals are re-calculated on every page load.
* **Solution:** Cache company role structures in Redis using key `tenant:{company_id}:role_permissions`. Invalidate cache upon role create, edit, or delete events.
* **Risk:** Medium (requires cache invalidation triggers on all write routes).
* **Testing:** Benchmark response time on cached vs uncached requests; verify cache invalidates when creating a new role.

### Recommendation 5: Add Composite Database Indexes
* **Problem:** In-memory filesort when ordering roles by priority and name.
* **Solution:** Add composite index `idx_roles_company_priority_name` on `roles(company_id, priority, name)`.
* **Risk:** Low. Standard index addition.
* **Testing:** Run `EXPLAIN SELECT * FROM roles WHERE company_id = 1 ORDER BY priority ASC, name ASC;` and confirm `Using index` without `Using filesort`.

---

## 12. Summary Table of Audit Findings

| Category | Finding / Problem | Current Impact | Recommended V2 Action |
|----------|-------------------|----------------|-----------------------|
| **Architecture** | DB queries inside Blade template | Poor separation of concerns, untestable code | Extract logic to `RolePermissionService` |
| **Database** | N+1 queries in role loop (`role_user`) | High DB round-trips (N+7 queries per request) | Aggregate assigned users in 1 query |
| **Database Bug** | `$company_id = ''` mutation in controller | `$this->roleslist` returns invalid empty results | Remove bug, clean up variable assignments |
| **Database** | Missing composite index on `roles` table | In-memory filesort on role sorting | Add `(company_id, priority, name)` index |
| **Caching** | Zero Redis caching for role structures | Wasted CPU & DB queries on every view load | Implement Redis cache with auto-invalidation |
| **Payload** | Heavy JSON stringified in HTML DOM cards | DOM bloat and slow client rendering | Streamline payload / server-side view models |
| **Security** | Role deletion user reassignment check | Critical business logic must be preserved | Maintain auto-reassignment to 'Recruiter' |

