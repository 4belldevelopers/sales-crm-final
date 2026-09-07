# JERO ATS V2 Clients Module — Add & Edit Comprehensive Performance & Architecture Audit

> **Document Status:** Complete Audit Report (V1 Baseline)  
> **Target Path:** `docs/V2_CLIENTS/CLIENT_ADD_EDIT_AUDIT.md`  
> **Scope:** Add Client & Edit Client Workflows (`http://localhost/git/JWMaincode28June2026/service_company/clients`)  
> **Audited Components:** Routes, Controllers, Helpers, MySQL Database, Elasticsearch, Redis, Email Queues, AJAX endpoints, Blade Templates, and JavaScript assets.

---

## 1. Executive Summary & Scope

The Clients module handles B2B client management for recruiters and service companies within the JERO multi-tenant ATS SaaS platform. This audit covers the complete end-to-end flow for:
1. **Add Client (`Clients → + Add Client → Create Client`)**: Route `GET/POST /service_company/clients/create` & `/service_company/clients`
2. **Edit Client (`Clients → Edit/View Client → Update Client`)**: Route `GET/POST /service_company/clients/{id}/edit` & `/service_company/clients/update/{id}`

### Key Architecture Findings
- **Shared View Architecture**: Both Add and Edit Client workflows utilize a single, heavy Blade template ([create.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/create.blade.php) - ~155 KB) containing 4 wizard tabs, inline scripts, TinyMCE, Tagify, and Metronic component logic.
- **Data Source Desync in Edit Page**: The `edit()` controller method attempts to fetch the primary client object from Elasticsearch (`ELASTIC_CLIENT_LIST`) rather than MySQL relational source of truth, creating data stale risk if ES indexing lags or fails.
- **Synchronous Elasticsearch Indexing**: Both `store()` and `update()` execute `GeneralMethod::clientStaffingPartner($client_id)` synchronously before completing the HTTP response, which runs a multi-table JOIN query and blocks the HTTP thread on Elasticsearch HTTP writes.
- **Missing MySQL Database Indexes**: Critical foreign key and scoping columns across `client_contact_info`, `client_file`, `client_location`, and `clients` lack indexes on `company_id` and `client_id`, forcing full table scans during multi-tenant queries.
- **Redundant & Unnecessary Reference Queries**: Controller methods execute duplicate reference data queries (e.g. `Country::all()`, `statelist` overwritten immediately, and unnecessary `allCities()` cursor queries).

---

## 2. ADD CLIENT AUDIT (`Clients → + Add Client → Create Client`)

### 2.1 Current Flow
1. User clicks **+ Add Client** from Clients Index (`/service_company/clients`).
2. Browser issues `GET /service_company/clients/create`.
3. Route dispatches to `ServiceCompanyClientController@create`.
4. Authorization checked via `Gate::denies('add_clients')` (returns 403 HTTP error if denied).
5. Controller fetches global reference data (Countries, Job Categories, Company Types, Industries, State lists, Cities).
6. View `service_company.client.create` renders 4 wizard tabs:
   - **Tab 1: Basic Information** (Client Name, Website, Industry, Company Size Range, Company Type, Year Founded, Logo).
   - **Tab 2: Details & Tags** (Description via TinyMCE editor, Tags via Tagify).
   - **Tab 3: Locations** (Address, Tagify Work Locations with Country | State | City auto-suggestions).
   - **Tab 4: Contact Persons** (Contact Name, Email, Calling Code, Phone, Designation).
7. On website field change/blur, AJAX `POST /client-domain-register-chk` fires to check domain duplication and fetch clearbit suggestions.
8. On form submit, AJAX `POST /service_company/clients` dispatches to `ServiceCompanyClientController@store`.
9. Controller executes MySQL transaction (`DB::beginTransaction()`):
   - Inserts `clients` record.
   - Loops through location cards and Tagify values, resolves `city_list` / `state_list` IDs, inserts `client_location` records.
   - Loops through contact cards, inserts `client_contact_info` records.
   - Dispatches `QueueNotificationMailService` email notification to queued connection `emails`.
   - Handles logo upload to Cloud storage (`r2` disk) and updates physical/logical paths on `clients` row.
   - Synchronously calls `GeneralMethod::clientStaffingPartner($client_id)` to re-query relational tables and index to Elasticsearch `ELASTIC_CLIENT_LIST`.
   - Clears user-level list cache keys via `GeneralMethod::cacheClean`.
   - Commits transaction (`DB::commit()`).
10. AJAX returns JSON response `{'status': 'success'}` and browser redirects to `service_company.clients.custom_client/0`.

### 2.2 Queries Executed (Add Client Page Load & Save)

#### Page Load (`GET /service_company/clients/create`):
1. `SELECT * FROM countries` via `Country::all()`
2. `SELECT * FROM job_categories` via `JobCategory::all()`
3. `SELECT ind_id, ind_name FROM td_industries ORDER BY ind_name ASC`
4. `SELECT * FROM countries ORDER BY country_name` via `GeneralMethod::getAllCountryList()`
5. `SELECT id, state as name, country_id FROM state_list WHERE country_id = 101 ORDER BY state` via `GeneralMethod::statelist(101)`
6. **DUPLICATE QUERY:** `SELECT id, state as name, 101 as country_id FROM state_list ORDER BY state` (immediately overwrites query #5!)
7. `SELECT city_list.city as city_name, state_list.state as state_name, countries.country_name FROM city_list JOIN state_list ... WHERE city_list.city_id = '1' ORDER BY city_list.city LIMIT 10` via `GeneralMethod::allCities()`

#### Page Interactive AJAX Calls:
- `POST /client-domain-register-chk`:
  - `SELECT id FROM clients WHERE website LIKE '%<domain>%' AND company_id = <company_id>` (**Full Table Scan** due to leading wildcard `%`)
  - `SELECT id FROM clients WHERE website LIKE '%<domain>%' AND company_id != <company_id>` (**Full Table Scan**)
  - External HTTP call to Clearbit API (`https://autocomplete.clearbit.com/v1/companies/suggest?query=<domain>`) with 1s timeout, cached in Redis for 24h.
  - If duplicate found: `ES GET /client_list/_doc/<client_id>` to fetch logo URL.
- `GET /city-job-suggestions?q=<query>`: Tagify location autocomplete search.
- `POST /valid-email-chk`: Contact email validation.
- `GET /service_company/clients/addresslist/<cid>`: Dynamic address row Blade partial snippet.
- `GET /service_company/job-applications/statelist/<cid>`: State option list HTML.
- `GET /service_company/clients/citylist/<state_id>`: City option list HTML.

#### Form Submission (`POST /service_company/clients`):
1. `INSERT INTO clients (company_id, name, website, industry, company_size_from, company_sizes, company_type, founded, description, tags, created_at, updated_at) VALUES (...)`
2. **N Queries inside Location loop**: For each Tagify city entry:
   - `SELECT city_list.city_id, countries.id as country_id FROM city_list JOIN state_list ON ... JOIN countries ON ... WHERE city_list.city != '' AND city_list.city = <cityName> AND countries.country_name = <countryName> LIMIT 1`
   - `SELECT state_id FROM city_list WHERE city_id = <city_id>` via `GeneralMethod::stateId()`
   - `INSERT INTO client_location (client_id, country_id, city, city_id, address, postcode, state) VALUES (...)`
3. **N Queries inside Contact Persons loop**:
   - `INSERT INTO client_contact_info (client_id, contact_person_name, contact_person_email, contact_person_number, calling_code, contact_person_designation) VALUES (...)`
4. `SELECT * FROM companies WHERE id = <company_id> LIMIT 1`
5. `SELECT * FROM companies JOIN users ON users.company_id = companies.id WHERE companies.id = <company_id> LIMIT 1`
6. `SELECT company_name FROM companies WHERE id = <company_id> LIMIT 1` (For logo directory formatting)
7. `UPDATE clients SET logo = ..., logo_physical_path = ..., logo_logical_path = ... WHERE id = <client_id>` (If logo uploaded)
8. **Synchronous Elasticsearch Indexing Query (`clientStaffingPartner`)**:
   - `SELECT companies.*, clients.*, mainCompany.ind_name, td_industries.ind_name ... FROM companies LEFT JOIN staffing_company_relation ... JOIN clients ... LEFT JOIN client_location ... LEFT JOIN client_contact_info ... WHERE clients.id = <client_id> GROUP BY clients.id`
   - `SELECT client_location.*, state_list.state as state_name, city_list.city as city_name, countries.country_name FROM client_location LEFT JOIN countries ... LEFT JOIN state_list ... LEFT JOIN city_list ... WHERE client_location.client_id = <client_id>`
   - `SELECT * FROM client_contact_info WHERE client_id = <client_id>`
   - Synchronous HTTP call to Elasticsearch (`PUT /client_list/_doc/<client_id>`)

### 2.3 Confirmed Problems
1. **Synchronous ES Indexing in Web Request**: `GeneralMethod::clientStaffingPartner($client_id)` is invoked inside `store()` prior to sending the HTTP response. If Elasticsearch experiences network latency or high write load, the HTTP request hangs.
2. **Duplicate Query on Page Load**: `GeneralMethod::statelist(101)` is executed and immediately overwritten by a raw `DB::table('state_list')` query 3 lines later in `create()`.
3. **Unindexed Full Table Scan on Domain Duplicate Check**: `SaasFrontController::domainClientRegisterChk()` runs `WHERE clients.website LIKE '%<domain>%'`. The leading wildcard `%` makes B-tree indexes unusable, causing full table scans across the entire `clients` table.
4. **Repeated City/State Lookups in Location Loop**: In `store()`, for every location item in the array, a 3-table JOIN (`city_list` JOIN `state_list` JOIN `countries`) plus a separate query for `GeneralMethod::stateId()` is run inside a PHP `foreach` loop.
5. **No Database Indexes on Foreign Keys**: Table `client_contact_info` lacks indexes on `client_id` and `company_id`. Table `clients` lacks an index on `company_id`.
6. **Massive HTML Payload**: `create.blade.php` is 155 KB of mixed Blade logic, inline JavaScript, and stylesheet rules.

### 2.4 Potential Problems
1. **Unbounded Location / Contact Person Rows**: No hard server-side limit on maximum locations or contact persons submitted, creating potential memory spike during loop execution.
2. **Synchronous R2 File Upload**: Uploading logo directly to R2 cloud storage (`$file->storeAs(..., 'r2')`) occurs inline during request handling, adding cloud network latency.
3. **Clearbit External Dependency**: Domain autocomplete depends on external clearbit HTTP call. If Clearbit times out or fails, response time degrades despite 1s timeout.

### 2.5 Not Measured
- Memory footprint of `create.blade.php` DOM tree rendering in browser.
- Network transfer latency for R2 cloud logo uploads.
- Exact millisecond execution breakdown for `QueueNotificationMailService::dispatch()`.

### 2.6 Required Data
- `company_id` (Auth user tenancy scope)
- Client basic information (`name`, `website`, `industry`, `company_sizes`, `company_type`, `founded`)
- Client description (`description` - HTML from TinyMCE editor)
- Client tags (`tags` - comma-separated string)
- Array of locations (`address`, `kt_tagify_work_location`)
- Array of contact persons (`cp_name`, `cp_email`, `cp_number`, `calling_code`, `cp_desigination`)
- Client logo file (Optional binary upload)

### 2.7 Business Logic
- Multi-tenant boundary enforced by assigning `$client->company_id = auth()->user()->company_id`.
- Company size range string (e.g. `"10-50"`) is parsed into `$from_size = 10` and `$to_size = 50`.
- Tags stored as comma-separated values converted from Tagify JSON array.
- Work locations mapped from Tagify string (`"City | State | Country"`) into relational records in `client_location`.
- Automatic staffing partner association initialized via `GeneralMethod::clientStaffingPartner()`.

### 2.8 Validation
- Client Name cannot be empty (`trim($request->name) == ''`).
- Website domain duplicate check checked per-company via `client-domain-register-chk`.
- Contact Email checked via `valid-email-chk` (`email:rfc,dns`, `disposable_email`).
- Logo allowed extensions: `png`, `jpeg`, `jpg`. Max size: 5 MB (`5242880` bytes).

### 2.9 Permissions
- Controlled by `Gate::denies('add_clients')`. Throws 403 Forbidden response if check fails.

### 2.10 Database Writes/Updates
- `INSERT INTO clients`
- `INSERT INTO client_location` (1 row per city/location)
- `INSERT INTO client_contact_info` (1 row per contact person)
- `UPDATE clients` (Updates `logo`, `logo_physical_path`, `logo_logical_path` if logo uploaded)

### 2.11 Elasticsearch Indexing
- Target Index: `ELASTIC_CLIENT_LIST` (`client_list`)
- Triggered by: `GeneralMethod::clientStaffingPartner($client_id)`
- Mode: `doc_as_upsert => true`, `retry_on_conflict => 5`

### 2.12 Redis/Cache
- `global_countries_list` cached for 86,400s (24 hours).
- Clearbit domain cache: `clearbit:company:<md5>` cached for 86,400s (24 hours).
- Invalidation: `cacheClean()` flushes user-level client list cache keys (`allclients_version_user_*`, `allcustomclient_version_user_*`, `clientsviewmore_version_user_*`).

### 2.13 Notifications/Emails
- Email Template: `email.client.add-client`
- Queue Connection: `emails`
- Dispatched via `QueueNotificationMailService::dispatch(...)`

### 2.14 Redirect Behavior
- Returns JSON payload: `Reply::redirect(route('service_company.clients.custom_client') . '/0', __('menu.clients') . ' ' . __('messages.createdSuccessfully'))`
- Browser JS performs `window.location = ...` to custom clients list.

---

## 3. EDIT CLIENT AUDIT (`Clients → Edit/View Client → Update Client`)

### 3.1 Current Flow
1. User clicks **Edit** on a client in Clients list.
2. Browser issues `GET /service_company/clients/{id}/edit` with encrypted `$id`.
3. Route dispatches to `ServiceCompanyClientController@edit`.
4. Authorization checked via `Gate::denies('edit_clients')` (returns 403 HTTP error if denied).
5. `$id` decrypted using `GeneralMethod::value_decrypt($id)`.
6. Controller attempts to fetch `$client` from Elasticsearch `ELASTIC_CLIENT_LIST` via `$this->elasticService->getClient()->get(['index' => ELASTIC_CLIENT_LIST, 'id' => $id])`.
7. Queries DB for `$client_file`, `$client_location` (JOIN `countries`), `$client_contact_info`.
8. Controller fetches all standard dropdown reference lists (Countries, Categories, Company Types, Industries, State lists, Cities).
9. Renders shared view `service_company.client.create` with `$page_event = 'EDIT'`, pre-filling form controls.
10. On form update submit, AJAX `POST /service_company/clients/update/{id}` dispatches to `ServiceCompanyClientController@update`.
11. Controller executes MySQL transaction (`DB::beginTransaction()`):
    - `Client::find($id)` and updates modified attributes.
    - **Location Sync Loop 1**: Parses Tagify locations, queries `city_list` JOIN `state_list` JOIN `countries`, checks existing `client_location` rows, gathers valid IDs in `$addIdGet`.
    - Deletes removed locations: `DELETE FROM client_location WHERE id NOT IN ($addIdGet) AND client_id = $id`.
    - **Location Sync Loop 2**: Re-queries `city_list` JOIN `state_list` JOIN `countries` (duplicate!), updates existing location rows or inserts new location rows.
    - **Contact Persons Sync**: Deletes all existing contacts (`DELETE FROM client_contact_info WHERE client_id = $id`) and re-inserts current contact cards.
    - Logo update: Handles file upload to R2 and updates physical/logical paths on `clients` row if new logo uploaded.
    - Calls `GeneralMethod::clientStaffingPartner($id)` to update Elasticsearch index synchronously.
    - Clears user-level list cache keys via `GeneralMethod::cacheClean`.
    - Commits transaction (`DB::commit()`).
12. AJAX returns JSON response and browser redirects to `service_company.clients.index`.

### 3.2 Queries Executed (Edit Client Page Load & Update)

#### Page Load (`GET /service_company/clients/{id}/edit`):
1. **Elasticsearch Get Query**: `GET /client_list/_doc/<id>` (Fetches primary client model from ES!)
2. `SELECT * FROM client_file WHERE client_id = <id>`
3. `SELECT * FROM countries` via `Country::all()`
4. `SELECT client_location.city, client_location.city_id, client_location.address, client_location.postcode, countries.country_name, client_location.country_id, client_location.state FROM client_location JOIN countries ON countries.id = client_location.country_id WHERE client_location.client_id = <id>`
5. `SELECT * FROM client_contact_info WHERE client_id = <id>`
6. **DUPLICATE QUERY:** `SELECT * FROM countries` via `Country::all()` (2nd time!)
7. `SELECT * FROM job_categories` via `JobCategory::all()`
8. `SELECT ind_id, ind_name FROM td_industries ORDER BY ind_name ASC`
9. `SELECT * FROM countries ORDER BY country_name` via `GeneralMethod::getAllCountryList()`
10. `SELECT id, state as name, country_id FROM state_list WHERE country_id = 101 ORDER BY state` via `GeneralMethod::statelist(101)`
11. **DUPLICATE QUERY:** `SELECT id, state as name, 101 as country_id FROM state_list WHERE state_list.country_id = 101 ORDER BY state_list.state` (Immediately overwrites query #10!)
12. `SELECT city_list.city as city_name, state_list.state as state_name, countries.country_name FROM city_list JOIN state_list ... WHERE city_list.city_id = '1' ORDER BY city_list.city LIMIT 10` via `GeneralMethod::allCities()`

#### Form Submission (`POST /service_company/clients/update/{id}`):
1. `SELECT * FROM clients WHERE id = <id> LIMIT 1`
2. `UPDATE clients SET name = ..., website = ..., industry = ..., company_size_from = ..., company_sizes = ..., company_type = ..., founded = ..., description = ..., tags = ..., updated_at = ... WHERE id = <id>`
3. **N Queries in Location Sync Loop 1**:
   - `SELECT city_list.city_id, countries.id as country_id FROM city_list JOIN state_list ON ... JOIN countries ON ... WHERE city_list.city != '' AND city_list.city = <cityName> AND countries.country_name = <countryName> LIMIT 1`
   - `SELECT state_id FROM city_list WHERE city_id = <city_id>`
   - `SELECT * FROM client_location WHERE city_id = <city_id> AND address = <address> AND client_id = <id> LIMIT 1`
4. `DELETE FROM client_location WHERE id NOT IN (<addIdGet>) AND client_id = <id>`
5. **N Queries in Location Sync Loop 2 (DUPLICATE SEARCHES)**:
   - Re-runs `SELECT city_list.city_id, countries.id as country_id FROM city_list JOIN state_list ...` for each location.
   - Re-runs `SELECT * FROM client_location WHERE city_id = <city_id> AND address = <address> AND client_id = <id> LIMIT 1`
   - `UPDATE client_location SET country_id = ..., city = ..., city_id = ..., address = ..., postcode = ..., state = ... WHERE id = <location_id>` (OR `INSERT INTO client_location ...`)
6. `DELETE FROM client_contact_info WHERE client_id = <id>`
7. **N Queries in Contact Sync Loop**:
   - `INSERT INTO client_contact_info (client_id, contact_person_name, contact_person_email, contact_person_number, calling_code, contact_person_designation) VALUES (...)`
8. `SELECT company_name FROM companies WHERE id = <company_id> LIMIT 1` (If logo updated)
9. `UPDATE clients SET logo = ..., logo_physical_path = ..., logo_logical_path = ... WHERE id = <id>` (If logo updated)
10. **Synchronous Elasticsearch Indexing Query (`clientStaffingPartner`)**:
    - `SELECT companies.*, clients.* ... WHERE clients.id = <id> GROUP BY clients.id`
    - `SELECT client_location.* ... WHERE client_location.client_id = <id>`
    - `SELECT * FROM client_contact_info WHERE client_id = <id>`
    - Synchronous HTTP call to Elasticsearch (`PUT /client_list/_doc/<id>`)

### 3.3 Confirmed Problems
1. **Primary Entity Read from Elasticsearch**: In `edit($id)`, `$this->client` is assigned from Elasticsearch (`$this->elasticService->getClient()->get($params)`). If an edit occurred in MySQL but ES sync failed or was delayed, the edit view displays out-of-date data. MySQL must always be the primary source of truth for read/write on single entity operations.
2. **Duplicate Location Loop Queries**: `update()` loops over `$request->addressId` TWICE. Both loops perform the exact same 3-table JOIN query (`city_list` JOIN `state_list` JOIN `countries`) and existence check (`client_location` where city_id & address match) for the exact same input arrays.
3. **Hard Delete & Re-insert Strategy for Contacts**: `update()` executes `DELETE FROM client_contact_info WHERE client_id = $id` and then inserts all submitted contacts. This destroys auto-increment primary IDs (`id`), resets candidate/job relations tied to contact IDs, and creates index fragmentation under frequent edits.
4. **Duplicate Reference Data Queries**: `Country::all()` is queried twice during `edit()`. `statelist` is queried twice during `edit()`.
5. **Synchronous ES Update in Web Request**: `GeneralMethod::clientStaffingPartner($id)` runs synchronously inside `update()`, blocking response completion on Elasticsearch response.

### 3.4 Potential Problems
1. **Race Conditions on Concurrent Client Updates**: Two recruiters updating the same client simultaneously will overwrite each other's location and contact records due to global `DELETE WHERE client_id = X` without optimistic locking or version tracking.
2. **Orphaned File References**: If a logo is replaced, the old logo file remains on R2 storage without cleanup.

### 3.5 Not Measured
- Total network latency of dual R2 upload + synchronous ES document update during client edit.
- Browser memory footprint during TinyMCE editor re-initialization on Edit form load.

### 3.6 Required Data
- Existing Client model (from MySQL `clients` table)
- Existing client files (`client_file`)
- Existing client locations (`client_location`)
- Existing client contacts (`client_contact_info`)
- Reference data (Countries, Job Categories, Company Types, Industries, State lists, City suggestions)

### 3.7 Business Logic
- Decrypts URL parameter `$id` before database querying.
- Preserves existing location records matching `city_id` and `address`; deletes locations no longer present in request.
- Re-populates client contacts.
- Updates staffing partner relationship index via `clientStaffingPartner($id)`.

### 3.8 Validation
- `edit_clients` permission check via `Gate::denies('edit_clients')`.
- Website domain uniqueness check via `clientDomainRegisterChk`.
- Contact person email validation via `valid-email-chk`.

### 3.9 Permissions
- `Gate::denies('edit_clients')` enforces RBAC access control.

### 3.10 Database Writes/Updates
- `UPDATE clients`
- `DELETE FROM client_location` & `UPDATE/INSERT client_location`
- `DELETE FROM client_contact_info` & `INSERT INTO client_contact_info`

### 3.11 Elasticsearch Indexing
- Index: `ELASTIC_CLIENT_LIST` (`client_list`)
- Updated synchronously via `GeneralMethod::clientStaffingPartner($id)`.

### 3.12 Redis/Cache
- Invalidates user list caches: `allclients_version_user_*`, `allcustomclient_version_user_*`, `clientsviewmore_version_user_*`.

### 3.13 Notifications/Emails
- No email notification dispatched on Client Edit/Update in current V1 flow.

### 3.14 Redirect Behavior
- Returns JSON payload: `Reply::redirect(route('service_company.clients.index'), __('menu.clients') . ' ' . __('messages.updatedSuccessfully'))`
- Browser JS redirects to Clients Index.

---

## 4. CROSS-CUTTING DATABASE & INFRASTRUCTURE ANALYSIS

### 4.1 Missing Database Indexes (`database/schema/mysql-schema.sql`)

| Table Name | Column(s) | Current Index Status | Impact of Missing Index |
| :--- | :--- | :--- | :--- |
| `clients` | `company_id` | **No Index** | Multi-tenant filtering `WHERE company_id = X` performs full table scan on `clients`. |
| `clients` | `website` | **No Index** | Domain check `WHERE website LIKE '%domain%'` performs full table scan. |
| `client_contact_info` | `client_id` | **No Index** | `WHERE client_id = X` scans entire table (1,800+ rows in dev, millions in prod). |
| `client_contact_info` | `user_id` | **No Index** | User relation checks cause unindexed table scans. |
| `client_file` | `client_id` | **No Index** | `WHERE client_id = X` scans entire table. |
| `client_file` | `company_id` | **No Index** | Company file listing causes full table scan. |
| `client_location` | `company_id` | **No Index** | Tenant scoping filtering on location performs full table scan. |

### 4.2 Scalability Bottlenecks at 10K+ Concurrent Recruiters
1. **DB Lock Contention on Auto-Increment Tables**: Frequent deletes/inserts on `client_contact_info` and `client_location` during client updates cause auto-increment lock (`AUTO-INC`) contention in InnoDB engine.
2. **Elasticsearch Connection Pool Exhaustion**: Synchronous ES updates from 10,000 HTTP threads will exhaust Elasticsearch REST client connection pools and cause HTTP 503 / connection refusal.
3. **Leading Wildcard Queries (`LIKE '%domain%'`)**: Under 10K concurrent recruiters, unindexed wildcard website queries will saturate MySQL CPU at 100%.
4. **Heavy Blade Payload (155 KB per page load)**: Transmitting 155 KB raw HTML per request across 10K recruiters consumes ~1.5 GB bandwidth per page view wave, exhausting web server network throughput.

---

## 5. SUMMARY COMPARISON: ADD VS EDIT WORKFLOW

| Metric / Aspect | Add Client Flow | Edit Client Flow |
| :--- | :--- | :--- |
| **Primary Route** | `GET/POST /service_company/clients/create` | `GET/POST /service_company/clients/{id}/edit` |
| **Primary Data Source** | Form Input / Default Values | Elasticsearch (`client_list` index) — **Data Risk** |
| **Controller Methods** | `create()`, `store()` | `edit()`, `update()` |
| **Blade View** | `service_company.client.create` (155 KB) | `service_company.client.create` (Shared View) |
| **MySQL Insert/Update Ops** | 1 Client Insert + N Location Inserts + N Contact Inserts | 1 Client Update + Location Sync + Contact Delete/Re-insert |
| **Elasticsearch Sync** | Synchronous (`clientStaffingPartner`) | Synchronous (`clientStaffingPartner`) |
| **Duplicate DB Queries** | 2 (`Country::all()`, `statelist`) | 4 (`Country::all()` x2, `statelist` x2, Location Loop x2) |
| **Email Notification** | Yes (`QueueNotificationMailService`) | No |
| **Redirect Destination** | Custom Clients (`/custom_client/0`) | Clients Index (`/clients`) |

---
