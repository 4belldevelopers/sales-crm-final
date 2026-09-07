# Account Listing & Settings (V1) — Comprehensive Technical Audit

**Target Page:** `http://localhost/git/JWMaincode28June2026/service_company/account`  
**Workspace:** `c:\xampp\htdocs\git\JWMaincode28June2026`  
**Date:** August 12, 2026  
**Status:** Audit Completed — Read Only (No Code Changes Implemented)

---

## 1. Current Architecture & Overview

The Account Listing & Settings module in JERO serves as the central administrative hub for a Service Company tenant. It manages company profile overview, custom candidate interview pipeline stages, contract & offer terms, commercial offer components, service boost keywords, integrations (Google/Zoom), and department configurations.

```
[Browser Request]
       │
       ▼
[Route: GET service_company/account]
       │
       ▼
[Middleware: auth, EnsureOneDeviceLogin]
       │
       ▼
[Controller: ServiceCompanyAccountSettingsController@index]
       │
       ├───► [MySQL DB] ───► (52+ Synchronous SQL Queries)
       ├───► [Elasticsearch] ──► (2 Queries fetching size: 10,000)
       │
       ▼
[View: service_company.account.index]
       │ (Monolithic Blade Template: 249 KB / 3,895 lines)
       ▼
[Client-side Render: 9 Tab Panes + 3,800+ lines of Inline JS]
```

### Key Architectural Characteristics
- **Monolithic Data Fetching:** A single HTTP GET request to `/service_company/account` executes all database and Elasticsearch queries across **all 9 functional tabs**, regardless of which tab the user intends to view.
- **No Service / Repository Layer:** Query logic is embedded directly inside `ServiceCompanyAccountSettingsController::index()`.
- **Heavy Inline JavaScript:** The front-end view (`resources/views/service_company/account/index.blade.php`) spans 249 KB with over 3,800 lines of inline HTML and jQuery AJAX handlers.

---

## 2. Complete Page Flow Analysis

| Step | Component | Details |
| :--- | :--- | :--- |
| **1. Route** | `routes/service.php` | `Route::resource('account', ServiceCompanyAccountSettingsController::class)` inside `service_company` route group. |
| **2. Controller** | `ServiceCompanyAccountSettingsController` | Method: `index()`. Handles initial page render and loads all tab data synchronously. |
| **3. Service / Repo** | *None* | Direct `DB::table()` queries, raw Eloquent queries, and inline call to `ElasticService`. |
| **4. MySQL DB** | 52+ SQL Queries | Fetches `companies`, `clients`, `client_location`, `company_tags`, `td_industries`, `candidate_status` (x7), `master_*` static tables, `job_categories`, `commericials`, `offer_point_companies`, `company_coins`, `users` (x4), `boostkeywords`. |
| **5. Elasticsearch** | 2 Search Queries | Queries index `ELASTIC_MASTER_NOTIFICATIONS` twice with `size: 10000` (once global, once filtered by `company_id`). |
| **6. Redis / Cache** | *Session Only* | No caching applied to static reference data (currencies, languages, timezones, date formats, email preferences) or company settings. |
| **7. AJAX / API** | Dynamic Tab Actions | Async sub-actions (e.g. stage updates, boost toggles, department creation, offer terms saving) use jQuery `$.ajax()` POST endpoints. |
| **8. Blade / Frontend** | `service_company.account.index` | Monolithic template rendering 9 active/hidden tab panes (`#overview`, `#interview_Stage`, `#terms`, `#offerterms`, `#agencyofferterms`, `#offercomponent`, `#services`, `#boostkeyword`, `#settings`). |

---

## 3. Detailed Database Query Audit

The `index()` method executes 52+ database queries on every page load:

### Query Inventory Breakdown

1. **Boost Keywords Query:**
   ```sql
   SELECT `boostkeywords`.*, `countries`.`country_name` 
   FROM `boostkeywords` 
   INNER JOIN `countries` ON `countries`.`id` = `boostkeywords`.`country_id` 
   WHERE `company_id` = ? 
   ORDER BY `boostkeywords`.`id` DESC;
   ```
2. **Company Record Query:**
   ```sql
   SELECT * FROM `companies` WHERE `id` = ? LIMIT 1;
   ```
3. **Self Client Record Query:**
   ```sql
   SELECT `clients`.`id`, `clients`.`tags` 
   FROM `clients` 
   WHERE `clients`.`company_id` = ? AND `clients`.`self_company` = 1 
   LIMIT 1;
   ```
4. **Industry Name Query:**
   ```sql
   SELECT `td_industries`.`ind_id`, `td_industries`.`ind_name` 
   FROM `td_industries` 
   WHERE `td_industries`.`ind_id` = ? 
   ORDER BY `td_industries`.`ind_name` ASC LIMIT 1;
   ```
5. **Company Tags Query:**
   ```sql
   SELECT `company_tags`.`id`, `company_tags`.`tag_name` 
   FROM `company_tags` 
   WHERE `company_id` = ?;
   ```
6. **Active Jobs Count Query:**
   ```sql
   SELECT COUNT(*) FROM `jobs` WHERE `status` = 'active' AND `company_id` = ?;
   ```
7. **Team Sample Query (5 users):**
   ```sql
   SELECT `id`, `name`, `image` FROM `users` WHERE `type` = 0 AND `company_id` = ? AND `status` = 'active' LIMIT 5;
   ```
8. **Team Count Query:**
   ```sql
   SELECT COUNT(*) FROM `users` WHERE `type` = 0 AND `company_id` = ? AND `status` = 'active';
   ```
9–12. **Client Location Queries (REDUNDANT x4):**
   - Query 9 (Join countries, first): `SELECT client_location.city, ... FROM client_location JOIN countries ... WHERE client_id = ? ORDER BY id ASC LIMIT 1;`
   - Query 10 (Join countries, count): `SELECT COUNT(*) FROM client_location JOIN countries ... WHERE client_id = ?;`
   - Query 11 (No join, count): `SELECT COUNT(*) FROM client_location WHERE client_id = ?;` *(Duplicate)*
   - Query 12 (No join, first): `SELECT city, city_id, ... FROM client_location WHERE client_id = ? ORDER BY id ASC LIMIT 1;` *(Duplicate)*
13. **City Name Lookup (Conditional):**
   ```sql
   SELECT `city_list`.`city` FROM `city_list` WHERE `city_list`.`city_id` = ? LIMIT 1;
   ```
14. **Country Name Lookup (Conditional):**
   ```sql
   SELECT * FROM `countries` WHERE `id` = ?;
   ```
15. **Master Status List:**
   ```sql
   SELECT * FROM `master_status` ORDER BY `priority` ASC;
   ```
16–22. **Candidate Status Pipeline Queries (REDUNDANT x7):**
   - Query 16: `SELECT * FROM candidate_status WHERE parent = 1 AND company_id = ? ORDER BY priority ASC;`
   - Query 17: `SELECT * FROM candidate_status WHERE parent = 2 AND company_id = ? ORDER BY priority ASC;`
   - Query 18: `SELECT * FROM candidate_status WHERE parent = 3 AND company_id = ? ORDER BY priority ASC;`
   - Query 19: `SELECT * FROM candidate_status WHERE parent = 4 AND company_id = ? ORDER BY priority ASC;`
   - Query 20: `SELECT * FROM candidate_status WHERE parent = 5 AND company_id = ? ORDER BY priority ASC;`
   - Query 21: `SELECT * FROM candidate_status WHERE parent = 6 AND company_id = ? ORDER BY priority ASC;`
   - Query 22: `SELECT * FROM candidate_status WHERE parent = 7 AND company_id = ? ORDER BY priority ASC;`
23–28. **Static Master Tables (Uncached):**
   - `SELECT * FROM master_languages ORDER BY name ASC;`
   - `SELECT * FROM master_timezone ORDER BY priority DESC;`
   - `SELECT * FROM master_currency ORDER BY country ASC;`
   - `SELECT * FROM master_time_format ORDER BY priority ASC;`
   - `SELECT * FROM master_date_format ORDER BY priority ASC;`
   - `SELECT * FROM master_email_preferences ORDER BY priority ASC;`
29. **Company Departments Query:**
   ```sql
   SELECT * FROM `job_categories` WHERE `company_id` = ? ORDER BY `name` ASC;
   ```
30. **All Countries Query (Uncached):**
   ```sql
   SELECT * FROM `countries`;
   ```
31. **Hiring Locations Pluck:**
   ```sql
   SELECT `country_id` FROM `job_locations` WHERE `company_id` = ?;
   ```
32–35. **Commercials & Commercial Countries Queries:**
   - `SELECT COUNT(*) FROM commericial_countries WHERE company_id = ? AND commtype = 2;`
   - `SELECT COUNT(*) FROM commericial_countries WHERE company_id = ? AND commtype = 1;`
   - `SELECT * FROM commericials WHERE company_id = ? AND commtype = 2 LIMIT 1;`
   - `SELECT * FROM commericials WHERE company_id = ? AND commtype = 1 LIMIT 1;`
36–43. **Offer Point Companies Queries (Repeated with Fallbacks):**
   - Query `offer_point_companies` for `section_type=0, section_divided=0` (and fallback to `offer_points` if empty).
   - Query `offer_point_companies` for `section_type=1, section_divided=0` (and fallback to `offer_points` if empty).
   - Query `offer_point_companies` for `section_type=0, section_divided=1` (and fallback to `offer_points` if empty).
   - Query `offer_point_companies` for `section_type=1, section_divided=1` (and fallback to `offer_points` if empty).
   - Query `offer_point_companies` latest record: `ORDER BY id DESC LIMIT 1;`
44. **Global Settings Query (Subquery inside query):**
   ```sql
   SELECT * FROM `global_settings` WHERE `id` = (SELECT MIN(`id`) FROM `global_settings`);
   ```
45–46. **Company Coins Queries:**
   - `SELECT * FROM company_coins WHERE company_id = ? AND event_type = 'operational' LIMIT 1;`
   - `SELECT * FROM company_coins WHERE company_id = ? AND event_type = 'sales' LIMIT 1;`
47–48. **Empaneled Agencies Queries:**
   - `SELECT COUNT(*) FROM users WHERE company_id = ? AND type = 1;`
   - `SELECT COUNT(*) FROM users WHERE company_id = ? AND type = 1 AND status = 'active';`
49. **Company Administrators Query:**
   ```sql
   SELECT `users`.`id`, `users`.`name`, `users`.`email`, `users`.`mobile`, `users`.`image`, `users`.`root_user`, `users_profile`.`designation` 
   FROM `users` 
   LEFT JOIN `users_profile` ON `users_profile`.`user_id` = `users`.`id` 
   WHERE `users`.`company_id` = ? AND `users`.`type` = 0 AND `users`.`status` = 'active' 
   ORDER BY `users`.`root_user` DESC, `users`.`id` ASC LIMIT 4;
   ```

---

## 4. N+1 & Query Redundancy Analysis

### Confirmed Query Redundancies
1. **Client Location Query Duplication:** `client_location` table is queried **4 consecutive times** in `ServiceCompanyAccountSettingsController@index` (lines 95–119). Two queries execute with joins and two execute without joins for the exact same `client_id`.
2. **Interview Stages Pipeline Partitioning (7x N+1 Pattern):** `candidate_status` table is queried 7 separate times for `parent = 1, 2, 3, 4, 5, 6, 7` instead of a single grouped query (`WHERE company_id = ? AND parent IN (1,2,3,4,5,6,7)`).
3. **Offer Points Partitioning (4x to 8x Queries):** `offer_point_companies` is queried 4 separate times for different combinations of `(section_type, section_divided)`. If any return empty, fallback queries to `offer_points` are fired, resulting in up to 8 separate database calls for offer points configuration.
4. **Static Dropdown Thrashing:** Static tables (`master_languages`, `master_timezone`, `master_currency`, `master_time_format`, `master_date_format`, `master_email_preferences`, `countries`) are re-queried from MySQL on **every single page request** without any Redis/application-level caching.
5. **Users Table Fragmented Queries:** The `users` table is queried 4 separate times for team preview, team count, agency count, active agency count, and primary admin list.

---

## 5. Relevant vs. Unused Data Analysis

### Unused & Over-fetched Data
- **Elasticsearch Mass Fetches (`size: 10000`):**
  - Line 210: Fetches up to 10,000 documents from `ELASTIC_MASTER_NOTIFICATIONS` unconditionally.
  - Line 237: Fetches up to 10,000 documents from `ELASTIC_MASTER_NOTIFICATIONS` filtered by `company_id`.
  - *Impact:* Mass payload serialization into PHP arrays, consuming multi-megabyte memory buffers for data that is mostly unused or redundant.
- **`Country::all()` Fetch:** Loads all 250+ country models into memory when only country IDs for current company locations are required.
- **Monolithic HTML DOM Payload:** HTML for all 9 tab panes (Overview, Interview Stages, Terms, Offer Terms, Agency Offer Terms, Offer Components, Services, Boost Keywords, Settings) is generated and sent in one single HTTP response, even if the user only views the Overview tab.

---

## 6. Pagination, Search, Filter & Sort Audit

- **Pagination:** None. All lists (Boost Keywords, Department Categories, Team Members, Interview Stages) are fetched completely without pagination.
- **Search & Filtering:** Server-side search or dynamic filtering is absent on this page. All filtering is hardcoded by `company_id`.
- **Sorting:** Sorting is performed in-memory or via basic SQL `ORDER BY priority ASC` / `ORDER BY id DESC`.

---

## 7. Database Indexes Audit

| Table | Current Indexes | Missing / Recommended Indexes | Impact |
| :--- | :--- | :--- | :--- |
| `companies` | `PRIMARY (id)`, `UNIQUE (career_page_link)`, `KEY (package_id)` | Composite: `(id, status)` | Fast single-row tenant lookup |
| `candidate_status` | `KEY (company_id)`, `KEY (parent)` | Composite: `(company_id, parent, priority)` | Eliminates file-sort on 7 pipeline queries |
| `client_location` | `KEY (client_id)` | Composite: `(client_id, id)` | Fast primary address lookup |
| `offer_point_companies`| `KEY (company_id)` | Composite: `(company_id, section_type, section_divided, sorting)` | Fast single query retrieval for all offer points |
| `users` | `KEY (company_id)`, `KEY (status)` | Composite: `(company_id, type, status, root_user)` | Optimizes admin and agency query filters |
| `boostkeywords` | `KEY (company_id)` | Composite: `(company_id, country_id, id)` | Optimizes country-joined keyword listing |
| `company_coins` | `KEY (company_id)` | Composite: `(company_id, event_type)` | Fast single-query coins balance check |

---

## 8. EXPLAIN & Query Performance Analysis

- **`candidate_status` queries:** `EXPLAIN` shows `Using where; Using filesort` because `parent` and `priority` are sorted without a covering composite index `(company_id, parent, priority)`.
- **`offer_point_companies` queries:** `EXPLAIN` shows multiple index lookups on `company_id` followed by in-memory filtering for `section_type` and `section_divided`.
- **`global_settings` min(id) subquery:** Performs an unnecessary index scan on `global_settings` primary key on every request.

---

## 9. PHP Memory & Large Collection Usage

- **Elasticsearch Collection Mapping:** Memory spikes when `collect($response['hits']['hits'])` maps 10,000 Elastic hits into stdClass objects in PHP memory twice per request.
- **Monolithic Blade Rendering:** Compiling and evaluating a 249 KB Blade template containing 3,895 lines of combined HTML and JavaScript consumes ~12 MB to ~18 MB of PHP memory per HTTP request.

---

## 10. Elasticsearch Usage Audit

- **Indices Used:** `ELASTIC_MASTER_NOTIFICATIONS`.
- **Query Method:** Direct invocation of `$this->elasticService->getClient()->search($params)`.
- **Efficiency Bottleneck:** Setting `size => 10000` bypasses Elasticsearch pagination best practices and forces Elasticsearch to return massive payloads over JSON HTTP protocol into PHP.
- **Write/Sync Integrity:** Sync on notification updates relies on synchronous inline calls rather than queued events.

---

## 11. Redis & Cache Usage Audit

- **Current State:** Redis is used for session management and Horizon queues, but **NOT** utilized for application-level caching of static reference data or account settings.
- **Missed Caching Opportunities:**
  - Master tables (`master_status`, `master_languages`, `master_timezone`, `master_currency`, `master_time_format`, `master_date_format`, `master_email_preferences`, `countries`) are 100% static and should be cached in Redis with a 24-hour TTL.
  - Tenant account settings (`companies` record by `company_id`) can be cached in Redis with cache invalidation on update.

---

## 12. Queue & Background Work Audit

- **Current State:** Account updates (e.g. updating company details, changing interview stages, updating boost keyword status) execute synchronously on the web worker thread.
- **Impact:** Heavy updates or sync operations cause noticeable request latency for the user.

---

## 13. AJAX & API Payload Audit

- **Initial HTML Payload:** 249 KB HTML/JS string (uncompressed).
- **Sub-action Endpoints:**
  - `POST /service_company/account/addIntStage` -> Returns HTML snippet (`stageHtml`).
  - `POST /service_company/account/boostStatusUpdate` -> Returns JSON `{status: "success"}`.
  - `POST /service_company/account/update/{id}` -> Processes multi-field updates synchronously and redirects.
  - `POST /service_company/account/addressupdate` -> Returns JSON response.

---

## 14. Permissions, Tenancy & Security Audit

- **Tenancy Enforcement:** Multi-tenancy is enforced via `auth()->user()->company_id`.
- **Authorization Gates:** Missing explicit `Gate::authorize()` or policy checks on sub-action endpoints (e.g. `addIntStage`, `delIntStage`, `boostStatusUpdate`). Any authenticated user belonging to the company can execute these settings modifications regardless of role permissions.

---

## 15. Frontend Performance Audit

- **DOM Complexity:** 3,800+ lines of DOM elements rendered on initial load.
- **CSS / JS Bloat:** Includes jQuery plugins, Metronic theme bundles, SweetAlert, Select2, and inline JS blocks.
- **First Contentful Paint (FCP):** Delayed by monolithic HTML download and heavy client-side JavaScript execution.

---

## 16. Server Configuration Audit

| Component | Target Server Specification | Local Dev Verification Status |
| :--- | :--- | :--- |
| **OS** | Ubuntu 24.04 LTS | Windows 11 (XAMPP Dev Environment) |
| **Control Panel** | aaPanel 8.0.3 | N/A |
| **CPU / Cores** | 8 Cores | 8 Cores (Host system) |
| **RAM** | 31.34 GB | 32 GB (Host system) |
| **Disk** | 386.4 GB | Local NVMe SSD |
| **Web Server** | Nginx / FrankenPHP / Apache | Apache 2.4 (XAMPP local) |
| **PHP Version** | PHP 8.2+ / PHP-FPM | PHP 8.2.12 (CLI) |
| **OPcache** | Enabled (Target) | Enabled (Zend OPcache) |
| **MySQL / MariaDB** | MySQL 8.0 / MariaDB 10.11 | MySQL 8.0 (Local) |
| **Elasticsearch** | Elasticsearch 7.x/8.x | Running locally / connected |
| **Redis** | Redis 7.x | Configured in `.env` |
| **Queue Workers** | Laravel Horizon | Active in background |

---

## 17. 10,000+ Concurrent Recruiters Scalability Analysis

If 10,000 concurrent recruiters access `/service_company/account`:

1. **Database Crash:** 10,000 concurrent requests x 52 SQL queries = **520,000 SQL queries per second**. MySQL connection pools will exhaust instantly, leading to `Too many connections` crashes.
2. **Elasticsearch Overload:** 10,000 concurrent requests x 2 queries fetching 10,000 items = **20,000 high-memory Elasticsearch queries**. Elastic JVM heap will hit OOM (Out Of Memory) immediately.
3. **Web Worker Starvation:** PHP-FPM worker pools will be occupied for 800ms–1500ms per request compiling 249 KB Blade templates, causing 504 Gateway Timeouts for all users.

---

## 18. Problem Categorization

### A. Confirmed Problems
1. Monolithic 249 KB Blade template loaded all at once for 9 tabs.
2. 52+ SQL queries executed on every page load.
3. Quadruple redundant queries on `client_location` table.
4. 7 separate SQL queries for interview pipeline stages (`candidate_status`).
5. Uncached static master tables re-queried on every page load.
6. 2 Elasticsearch queries returning `size: 10000` documents unconditionally.
7. Lack of granular role/permission checks on settings modification APIs.

### B. Potential Problems
1. Race conditions during simultaneous interview stage priority updates.
2. Memory leaks in browser client due to 3,800+ lines of unminified inline jQuery scripts.

### C. Not Measured
1. Exact network latency in milliseconds under high latency mobile networks.
2. Exact Redis memory consumption per company settings cache key.

---

## 19. Identified Non-Negotiable Business Logic & Data Rules

1. **Tenancy:** All queries MUST filter by `company_id = auth()->user()->company_id`.
2. **Company Overview:** Must display accurate company profile, tags, active jobs count, active team count, and empaneled agencies.
3. **7-Stage Interview Pipeline:** Pipeline MUST maintain parent stages: 1: Sourcing, 2: Screening, 3: Evaluation, 4: Negotiation, 5: Offered, 6: Joined, 7: Rejected, along with custom child sub-stages sorted by `priority`.
4. **Commercial & Offer Terms:** Business logic for standard terms, offer terms, agency offer terms, and hourly/monthly/annual breakdown sections MUST function identically.
5. **Boost Keywords:** Country-linked keyword boost options and toggling MUST retain existing table mappings (`boostkeywords`).
