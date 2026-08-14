# Jobs Module Technical Audit (Add, Edit, Submit Flows)

> **Document Status**: AUDIT ONLY  
> **Target Module**: Jobs Management (`service_company/jobs`)  
> **Environment**: Local / Staging / Production Architecture Assessment  
> **Date**: August 14, 2026  

---

## 1. Executive Summary & Audit Scope

This document provides a comprehensive end-to-end technical audit of the **Jobs Module** in the JERO SaaS platform (`testing.jero.works`). The audit specifically investigates the three core user workflows:

1. **Add Job** (`GET /service_company/jobs/create` and `GET /service_company/jobs/curstomcreate/{id}`)
2. **Submit/Create Job** (`POST /service_company/jobs`)
3. **Edit Job & Submit/Update Job** (`GET /service_company/jobs/{id}/edit` and `POST /service_company/jobs/update/{id}`)

The primary objective is to evaluate latency, query volume, caching efficiency, transaction integrity, safety against concurrent load (up to 10,000+ active recruiters), database index usage, and network resilience.

---

## 2. Architecture & Request Flow Analysis

### 2.1 Route → Controller → Service → Validation → MySQL → Redis → AJAX → Blade/JS Architecture Map

```
[Browser Client] 
       │
       │ (HTTP GET/POST / AJAX FormData)
       ▼
[Middleware Layer] ── (recruiter.throttle:60,1, auth, company_id session)
       │
       ▼
[Route Layer] (routes/service.php)
       │
       ▼
[Controller Layer] (ServiceCompanyJobsController - ~15,450 lines God Controller)
       │
       ├──────────────► [Gate Permissions] (add_jobs, edit_jobs, view_jobs)
       ├──────────────► [Validation Layer] (StoreJob FormRequest / manual inline checking)
       │
       ├──────────────► [Redis Cache Layer] (Cache::remember, GeneralMethod::cacheClean)
       │
       ├──────────────► [MySQL DB Layer] (Jobs, JobCategories, Clients, CityList, JobSkills, etc.)
       │                        ▲
       │                        │ (DB Transactions: beginTransaction / commit / rollBack)
       │                        ▼
       ├──────────────► [Elasticsearch Service] (ElasticCompanySkills, ElasticJobs Indexing)
       │                        ▲
       │                        │ (Synchronous HTTP calls inside DB transaction)
       │                        ▼
       └──────────────► [Queue Layer] (QueueNotificationMailService dispatched to 'emails')
       │
       ▼
[Blade View Layer] (resources/views/service_company/jobs/create.blade.php - ~3,454 lines)
```

---

## 3. Flow-by-Flow Technical Audit

### 3.1 Flow 1: Add Job Page Load (`GET /service_company/jobs/create` & `curstomcreate/{id}`)

#### Primary Route: `GET /service_company/jobs/create`
* **Controller Action**: `ServiceCompanyJobsController@create` ([ServiceCompanyJobsController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L4875-L5002))
* **Permission Gate**: `abort_if(Gate::denies('add_jobs'), 403);`
* **Execution Summary**:
  1. Coin/package check cached for 30s (`coins_valid:{companyId}:event:2`).
  2. User and company profile loaded and cached for 1 hour (`user_profile:{userId}`).
  3. Active job limit check cached for 60s (`company:{companyId}:active_jobs_count`).
  4. Categories and clients cached for 300s (`tenant:{companyId}:categories`, `tenant:{companyId}:clients`).
  5. Master data (`qualification_master`, `countries`) cached for 24h / forever.
  6. Recruiter list cached for 300s per user email md5 hash.
  7. Renders Blade view `service_company.jobs.create`.

#### Alternative Route: `GET /service_company/jobs/curstomcreate/{id}`
* **Controller Action**: `ServiceCompanyJobsController@curstomcreate` ([ServiceCompanyJobsController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L4818-L4873))
* **Permission Gate**: `abort_if(Gate::denies('view_jobs'), 403);` (**Inconsistency**: uses `view_jobs` instead of `add_jobs`).
* **Execution Summary & Critical Issues**:
  1. **Tenancy Leak Bug**: `Job::where('status', 'active')->count()` is executed without filtering by `company_id`. It counts ALL active jobs across all tenants in the system.
  2. **Cross-Tenant Data Load**: `JobCategory::all()`, `Question::all()`, `Company::all()`, `JobLocation::all()`, `Skill::all()` are called without tenant scoping (`company_id`). Loads entire multi-tenant datasets into memory.
  3. **Uncached Heavy Queries**: `DB::table('city_list')->get()` loads the entire global city directory (~100,000+ rows in full DB) into PHP memory on every single request. Zero Redis caching used in `curstomcreate`.

---

### 3.2 Flow 2: Submit / Create Job (`POST /service_company/jobs`)

* **Controller Action**: `ServiceCompanyJobsController@store` ([ServiceCompanyJobsController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L5487-L5992))
* **Validation**: Receives `Request $request`. Does **NOT** use `StoreJob` request validation in signature.
* **Database Transaction Boundary**: `DB::beginTransaction()` block from line 5577 to line 5980.
* **Execution Sequence inside Transaction**:
  1. `job->save()` (Inserts main record into `jobs` table).
  2. `GeneralMethod::teamRankElastic(...)` (Updates team rank counters).
  3. `GeneralMethod::coinsDeductions(...)` (Deducts coin balance).
  4. **Qualification Processing (N+1 Query Loop)**: Decodes JSON qualification string, loops through array, queries `qualification_master` by name in every iteration, performs `Job::findOrFail($job->id)` inside the loop, and inserts into `job_qualifications`.
  5. **Client Location & Work City Processing (N+1 Query Loop)**: Decodes JSON location string, loops through cities, executes a 3-table join (`city_list` + `state_list` + `countries`) inside the loop, and inserts into `client_city_jobs`.
  6. **Skills Processing (N+1 Query Loop + Synchronous ES HTTP Call)**: Decodes tags JSON, loops over skills, checks existence in `job_metronics_skills` per item, inserts row, and **calls Elasticsearch update API synchronously via HTTP inside the MySQL transaction loop**.
  7. **JD Details & Additional Details**: Performs existence check on `jd_details`, inserts custom titles into `job_additional_detail`.
  8. **Recruiter Assignment (N+1 Query Loop)**: Explodes recruiter IDs, queries `users` table per ID in a loop, inserts into `job_assign`, and calls `GeneralMethod::cacheClean(...)` inside the loop.
  9. **Hiring Manager Assignment (N+1 Query Loop)**: Explodes manager strings, queries `client_contact_info` table per manager in a loop, inserts into `client_contact_info_jobs`.
  10. **Redundant Post-Processing Queries**: Re-queries `client_location_jobs` and `client_city_jobs` with joins to format an email HTML string; re-queries `clients` and `companies`; re-queries `Job` with joins for category and qualification display names.
  11. **Notification Email Dispatch**: Dispatches `QueueNotificationMailService::dispatch(...)` to the `emails` queue.
  12. **Elasticsearch Indexing**: Executes `GeneralMethod::elasticJobsAddEditUpdateIndex($job->id)` **synchronously** before committing DB transaction.
  13. `DB::commit()` & `Reply::redirect(...)`.

---

### 3.3 Flow 3: Edit Job & Submit / Update Job (`GET jobs/{id}/edit` & `POST jobs/update/{id}`)

#### Page Load: `GET /service_company/jobs/{id}/edit`
* **Controller Action**: `ServiceCompanyJobsController@edit` ([ServiceCompanyJobsController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L13804-L13970))
* **Permission Gate**: `abort_if(Gate::denies('edit_jobs'), 403);`
* **Execution Summary & Critical Issues**:
  1. Decrypts job ID: `$id = GeneralMethod::value_decrypt($id)`.
  2. **Zero Caching**: Unlike `create()`, `edit()` does **NOT** use any Redis caching (`Cache::remember`).
  3. **High Query Count (22–28 queries per page load)**:
     - User/Company profile query (`User::join('companies'...)`).
     - `Job::findOrFail($id)` query.
     - `JobCategory::where('company_id'...)` query.
     - `JobLocation::all()` query (uncached global table).
     - `Skill::all()` query (uncached global table).
     - `Question::all()` query (uncached global table).
     - Consultancies (`type=1`) user query.
     - Team (`type=0`) user query.
     - `client_location` join query.
     - `client_contact_info` join query.
     - `Client::where('company_id'...)` query.
     - **Duplicate Queries**: `qualification_master` is queried **TWICE** identically (lines 13859 and 13865).
     - `job_assign` query.
     - **Duplicate Queries**: `job_additional_detail` is queried **TWICE** (line 13880 for `count()` and line 13900 for `get()`).
     - `client_contact_info_jobs` join query.
     - `job_assign` join `users` query.
     - `job_metronics_skills` query.
     - `GeneralMethod::allCities()` query (loads large city list).
     - `countries` query.
     - `job_countries` pluck query.
     - `client_city_jobs` pluck query.
     - `GeneralMethod::allCitiesSelected(...)` & `GeneralMethod::allQualificationSelected($id)` helper queries.
     - `coin_order_events` (2 separate queries) and `company_coins` query.

#### Submit / Update Job: `POST /service_company/jobs/update/{id}`
* **Controller Action**: `ServiceCompanyJobsController@update` ([ServiceCompanyJobsController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L13971-L14280))
* **Validation**: Typehinted with `StoreJob $request`. However, `StoreJob` only contains `'title' => 'required'`.
* **Database Transaction Boundary**: `DB::beginTransaction()` from line 14084 to line 14280.
* **Execution Sequence & Critical Bottlenecks**:
  1. Performs `Job::findOrFail($id)` outside/inside transaction.
  2. Deletes existing qualifications (`job_qualifications` delete) and loops over new qualification tags with N+1 queries.
  3. `job->save()` updates primary record.
  4. Deletes and re-inserts location records.
  5. **Slow Wildcard SQL inside Loop**: Deletes existing `client_city_jobs`. Loops over input cities and executes:
     ```sql
     SELECT city_list.city_id, countries.id 
     FROM city_list 
     JOIN state_list ON state_list.id = city_list.state_id 
     JOIN countries ON countries.id = state_list.country_id 
     WHERE city_list.city LIKE '%<cityName>%' 
       AND countries.country_name = '<countryName>' 
     LIMIT 1;
     ```
     `LIKE '%<cityName>%'` with leading wildcard forces a full table scan on `city_list` inside an array iteration inside a DB transaction!
  6. Deletes and re-inserts `job_countries`, `client_contact_info_jobs`, `job_assign` with N+1 queries per item.
  7. Calculates skill diff (`not_common`), loops over items, checks `exists()`, inserts into `job_metronics_skills`, and executes **synchronous Elasticsearch HTTP calls** per skill inside the transaction.
  8. Calculates additional detail diff (`not_common`) and inserts items.
  9. Executes `GeneralMethod::elasticJobsAddEditUpdateIndex($job->id)` **synchronously** inside transaction.
  10. `DB::commit()` & `Reply::redirect(...)`.

---

## 4. Detailed Technical Analysis Across Categories

### 4.1 Page-Load Performance
* **Add Job (`create`)**: Acceptable caching applied for static reference data. Latency is dominated by Blade template rendering (3,454 lines of Blade with embedded CSS and asset overhead).
* **Add Job (`curstomcreate`)**: High risk of memory exhaustion and multi-second page load times due to uncached `city_list` fetches (~100,000 rows) and missing tenant filters on core models.
* **Edit Job (`edit`)**: Uncached execution of 22 to 28 separate SQL queries on every request. Significant redundant queries for qualifications, additional details, and global tables.

### 4.2 Submit/Create & Submit/Update Performance
* Both `store()` and `update()` execute between 30 and 60 SQL statements sequentially inside a single `DB::beginTransaction()` block.
* **Transaction Duration**: Prolonged by synchronous external HTTP network requests to Elasticsearch (`$this->elasticService->getClient()->update(...)` and `elasticJobsAddEditUpdateIndex`).
* **Database Row Locking**: Holding open MySQL InnoDB transactions while performing network I/O to external Elasticsearch clusters introduces lock escalation and thread starvation under concurrent traffic.

### 4.3 DB Query Count & Slow Queries
* **N+1 Query Loops**:
  - `qualification_master` queried 1x per qualification tag.
  - `city_list` joined query executed 1x per city tag.
  - `users` queried 1x per assigned recruiter.
  - `client_contact_info` queried 1x per hiring manager.
  - `job_metronics_skills` checked 1x per skill.
* **Unindexed / Slow Query Patterns**:
  - `WHERE city_list.city LIKE '%cityName%'` in `update()` forces a full table scan on `city_list` for every city tag updated.

### 4.4 Redis Operations
* Redis caching is used in `create()` using 30s to 86400s expiry windows.
* Zero Redis caching in `edit()` and `curstomcreate()`.
* **Cache Invalidation Strategy**: Relies on manual calls to `GeneralMethod::cacheClean('alljobslist_version_user_' . $id)`. Does not utilize Redis tags or key pattern invalidation.

### 4.5 Database Indexes & Schema Integrity
* `mysql-schema.sql` defines 217 tables.
* Pivot tables (`client_city_jobs`, `job_qualifications`, `client_contact_info_jobs`, `job_assign`, `job_metronics_skills`, `job_countries`) lack compound composite unique indexes on `(job_id, <entity_id>)`.
* Deleting and re-inserting all rows on `update()` creates auto-increment ID gaps and index fragmentation on pivot tables.

### 4.6 Network Failure & Retry Handling
* AJAX submit handler (`saveJob()`) in `create.blade.php` checks `navigator.onLine`.
* If a network drop or TCP timeout occurs mid-request, jQuery `$.ajax` invokes the `error` callback with a basic toast ("Server error while saving the job").
* **No Exponential Backoff / Automatic Retry**: Retrying requires the user to manually click "Publish Job" again.

### 4.7 Duplicate Submission Protection
* Front-end disables the publish button (`$btn.prop('disabled', true)`) upon click.
* **No Server-Side Idempotency Key**: If a user submits, experiences a network delay, reloads, or sends parallel requests via API tools, the backend will execute duplicate `store()` calls, leading to double coin deductions and duplicate job creation.

### 4.8 Transactions & Rollback Safety
* `store()` and `update()` wrap core DB operations in `DB::beginTransaction()` and `DB::commit()` with `try-catch` blocks calling `DB::rollBack()`.
* **Rollback Limitations**:
  - `GeneralMethod::teamRankElastic(...)` and synchronous Elasticsearch HTTP updates cannot be rolled back by MySQL `DB::rollBack()`. If MySQL fails on line 5970, Elasticsearch already contains the updated skill document.

### 4.9 Error Handling & Logging
* Exceptions caught in `store()` and `update()` log errors via `Log::error(...)` and return JSON responses (`Reply::dataOnly(...)`).
* `StoreJob` request class does not enforce proper validation error structures. Frontend handles errors via generic toast notifications.

### 4.10 Permissions & Business Logic
* Inconsistent Gate authorization across actions:
  - `create()` -> `Gate::denies('add_jobs')`
  - `curstomcreate()` -> `Gate::denies('view_jobs')` (**Security Bug**)
  - `edit()` & `update()` -> `Gate::denies('edit_jobs')`
* Multi-tenancy check in `curstomcreate()` fails to filter `Job::where('status', 'active')->count()`, creating a cross-tenant data visibility risk.

### 4.11 Server / PHP-FPM / MySQL / Redis Bottlenecks
* Long-running PHP-FPM worker threads caused by synchronous ES HTTP network calls inside DB transactions.
* High MySQL pool usage during peak job creation hours due to N+1 query loops.

### 4.12 10K+ Concurrent Recruiter Scalability Bottlenecks
* Under 10,000 concurrent recruiters:
  1. `edit()` page loads will overload MySQL with 220,000–280,000 uncached queries.
  2. `store()` and `update()` endpoints will exhaust PHP-FPM workers while waiting for synchronous Elasticsearch network roundtrips.
  3. `curstomcreate()` will cause memory exhaustion on web nodes due to uncached 100k-row city table reads.

---

## 5. Audit Findings Classification

### 5.1 Confirmed Problems

| ID | Category | Description | Source Reference |
|---|---|---|---|
| **CP-01** | Transaction & Network | Synchronous Elasticsearch HTTP update calls executed inside MySQL `DB::beginTransaction()` block. | [ServiceCompanyJobsController.php:L5700](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L5700) |
| **CP-02** | Database Query | Severe N+1 query loops in `store()` and `update()` for qualifications, cities, skills, recruiters, and hiring managers. | [ServiceCompanyJobsController.php:L5609](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L5609) |
| **CP-03** | Cache & Performance | Zero Redis caching on Edit Job page load (`edit()`), resulting in 22–28 SQL queries per request. | [ServiceCompanyJobsController.php:L13804](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L13804) |
| **CP-04** | Security & Tenancy | `curstomcreate()` active jobs count query lacks `company_id` scope; loads uncached global datasets into memory. | [ServiceCompanyJobsController.php:L4837](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L4837) |
| **CP-05** | Validation | `StoreJob` FormRequest class only validates `'title' => 'required'`. All numeric, date, and relational validations are missing. | [StoreJob.php:L30](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Requests/StoreJob.php#L30) |
| **CP-06** | Query Efficiency | Duplicate identical queries executed in `edit()` for `qualification_master` and `job_additional_detail`. | [ServiceCompanyJobsController.php:L13859](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L13859) |
| **CP-07** | Query Efficiency | `LIKE '%cityName%'` leading wildcard SQL query executed inside a loop during `update()`. | [ServiceCompanyJobsController.php:L14115](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L14115) |
| **CP-08** | Security & Auth | `curstomcreate()` uses `view_jobs` gate instead of `add_jobs`. | [ServiceCompanyJobsController.php:L4822](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L4822) |

---

### 5.2 Potential Problems

| ID | Category | Description | Impact |
|---|---|---|---|
| **PP-01** | Concurrency | PHP-FPM worker thread pool exhaustion under high concurrent job creation due to synchronous network I/O. | Service instability during bulk recruiting drives |
| **PP-02** | Data Integrity | Inability to roll back Elasticsearch mutations if MySQL transaction fails late in execution. | Index out of sync with MySQL database |
| **PP-03** | Network Safety | Lack of client-side idempotency keys allows duplicate job records and coin deductions on network retries. | Double coin deduction for user |
| **PP-04** | Database Locks | InnoDB row and table lock contention during long-running `store()` and `update()` transactions. | Database request queue backup |

---

### 5.3 Not Measured

| ID | Metric | Status | Reason |
|---|---|---|---|
| **NM-01** | Exact Page Load Latency (ms) | Not Measured | Requires synthetic benchmark suite and live APM instrumentation. |
| **NM-02** | Exact DB Query Latency (ms) | Not Measured | Dependent on DB server hardware, dataset size, and concurrent load. |
| **NM-03** | Max RPS under 10K Users | Not Measured | Demands formal Phase 11 load testing using Locust / k6. |
| **NM-04** | Peak Memory Consumption | Not Measured | Requires profiling under multi-tenant production payload simulation. |

---
