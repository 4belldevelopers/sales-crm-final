# Job Creation Form to Sales CRM Product Creation Form — Phase 1 Audit & Field Mapping

## Metadata
- **Date:** 2026-11-26
- **Module:** `products` (Service Company Sales CRM)
- **Source Reference:** `http://localhost/sales-crm-final/service_company/jobs/create`
- **Source Files Inspected:**
  - `routes/service.php` (Line 219: `Route::resource('jobs', ServiceCompanyJobsController::class)`)
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` (`create()` L6557–6816, `store()` L8374+, `edit()` L16438+, `update()` L16653+)
  - `app/Http/Requests/StoreJob.php` (L1–60)
  - `resources/views/service_company/jobs/create.blade.php` (16,476 lines)
- **Target UI File:** `resources/views/service_company/products/create.blade.php`
- **Status:** Phase 1 Audit Complete — Pending UI Review & Approval before Backend Development

---

## 1. Executive Summary
The existing Job Creation page (`service_company/jobs/create`) is a 4-step Metronic/Poppins wizard (`#jrwrap`) with 2-column responsive grids, searchable Select2/custom dropdowns, tag/chip inputs, a rich-text description editor, and AJAX-backed master-data bundles.

Per project restrictions:
- The existing Job Management module (`ServiceCompanyJobsController`, `jobs/*` routes, `jobs/create.blade.php`) **remains 100% untouched**.
- The new Sales CRM Product Creation form reuses the `#jrwrap` visual design system, Poppins typography (max 14px), 15.6" screen responsiveness (`1366×768` & `1920×1080`), Rule 20 dedicated header backgrounds (`#f8fafc` / `#f1f5f9`), Rule 26 `/unslop` craft, and Rule 27 GPU-only transitions.

---

## 2. Existing Job Form Inventory

### 2.1 Existing Job Fields & UI Controls
| Step | Section | Field / Input `name` | Control Type | Required |
| :--- | :--- | :--- | :--- | :--- |
| Step 1 | Basic Information | `client_id` | Searchable custom dropdown / Select2 | Yes |
| Step 1 | Basic Information | `address_id` | Select dropdown (dependent on `client_id`) | Yes |
| Step 1 | Basic Information | `title`, `canonical_designation_id`, `raw_designation`, `taxonomy_version` | Autocomplete text + hidden taxonomy fields | Yes |
| Step 1 | Basic Information | `category_id`, `custom_department` | Searchable department dropdown | Yes |
| Step 1 | Experience & Openings | `min_exp`, `max_exp`, `total_positions` | Select dropdowns | Yes |
| Step 1 | Diversity | `required_columns` | Multi-toggle / hidden input | No |
| Step 1 | Compensation | `salary_visibility`, `min_salary_currency`, `max_salary_currency`, `minimum_salary`, `maximum_salary`, `min_sal_mode` | Currency Select2 + number inputs + mode select | Yes |
| Step 1 | Work & Logistics | `kt_tagify_work_location`, `employment_type`, `work_from_home`, `work_shifts`, `qualification`, `end_date` | Tagify / Select / Date | Varies |
| Step 1 | Team & Ownership | `hiring_manager_id`, `recruiter_id`, `operation_id` | Roster picker + hidden IDs | Yes |
| Step 2 | Job Description | `kt_docs_tinymce_hidden` (`#jobDescription`) | ContentEditable rich-text editor with toolbar | Yes |
| Step 2 | Key Skills | `tags` | Searchable chip/tag input with recommended pills | Yes |
| Step 3 | Screening Questions | `customTitleId`, `custom_title[]` | Dynamic question builder modal + templates | No |
| Step 4 | Review & Publish | `visibility_career`, `visibility_marketplace`, `visibility_bench_talent`, `carrier_website` | Checkboxes + summary cards | No |

### 2.2 Existing Dropdowns & Master Data (Controller `create()`)
- **Clients & Locations:** `Client` + `client_location` + `client_contact_info`
- **Categories / Departments:** `JobCategory` + `DepartmentTaxonomyService`
- **Titles / Taxonomy:** `buildTitlesManifest()`
- **Currency:** `GeneralMethod::currency()`, `CurrencyService::getTenantCurrency($companyId)`, `GeneralMethod::currencyEnterpriseMeta()`
- **Qualifications & Countries:** `qualification_master`, `countries`
- **Employment Type / Priority / Salary Mode:** `GeneralMethod::employmentType()`, `priority()`, `salaryMode()`
- **Team Roster:** `RelationshipManagerService::getTeamRoster($companyId)`

### 2.3 Existing Form Validations (`StoreJob.php` & `store()`)
- `title`: `required|string|max:255`
- `client_id`: `nullable|integer` (tenant-scoped via `company_id`)
- `category_id`: `nullable|integer`
- `total_positions`: `nullable|integer|min:1`
- `minimum_salary`, `maximum_salary`: `nullable|numeric|min:0`
- `min_exp`, `max_exp`: `nullable|integer|min:0`
- `carrier_website`, `salary_visibility`: `nullable|string|max:500` / `max:50`
- Client-side JS `validateStep()` checks required fields per step before advancing or calling `publishJob()`.

### 2.4 Existing AJAX Requests & JS Events
- `service_company.jobs.clientsBundle`, `clientDetails`, `getContactPerson`, `getlocationCity`
- `service_company.jobs.jobTitlesBundle`, `departmentsBundle`, `jdDetails`
- `service_company.jobs.store` (POST) / `service_company.jobs.update` (POST/PUT)
- JS events: `goToStep(n)`, `formatText(cmd)` for rich editor, skill chip add/remove/clear, `publishJob()` with CSRF token refresh and `operation_id` idempotency.

### 2.5 Existing Database Relationships
- `jobs.company_id` → `companies.id` (tenant isolation)
- `jobs.client_id` → `clients.id`
- `jobs.category_id` → `job_categories.id`
- `job_skills`, `job_questions`, `job_locations`, `job_recruiters`

---

## 3. Job-to-Product Field Mapping

| Existing Job Field / Component | Disposition | New Product Field / Component |
| :--- | :--- | :--- |
| `title` (Job Title / Designation) | **Mapped** | `name` (Product Name `*`) |
| `operation_id` / Job Code | **Mapped** | `sku` (Product SKU / Code `*` — unique per `company_id`) |
| `category_id` (Job Department) | **Mapped** | `category_id` (Product Category `*`) + `subcategory_id` (Product Subcategory) |
| `employment_type` | **Mapped** | `product_type` (`Physical` / `Digital` / `Service`) |
| `client_id` (Hiring Organization) | **Mapped** | `brand` (Brand / Manufacturer) |
| `kt_docs_tinymce_hidden` (Job Description) | **Mapped** | `description` (Product Description — rich editor reused) |
| *(New)* | **Added** | `image` (Product Image with live preview) |
| `minimum_salary`, `maximum_salary` | **Mapped** | `purchase_price`, `selling_price` `*`, `mrp` |
| `min_salary_currency` | **Reused** | `currency` (from `GeneralMethod::currency()` + `CurrencyService`) |
| *(New)* | **Added** | `discount_type` (`percentage`/`fixed`), `discount_value`, `tax_percentage` (GST %), `hsn_sac_code`, auto-calculated `final_selling_price` & `profit_margin` |
| `total_positions` | **Mapped** | `opening_stock`, `available_quantity`, `min_stock_alert`, `unit`, `warehouse_location`, `stock_status` (conditional on `product_type`) |
| `tags` (Key Skills chip input) | **Mapped** | `tags` (Product Tags chip input) |
| *(New)* | **Added** | `model_number`, `barcode`, `warranty_period`, `weight`, `dimensions`, `features`, `specifications` |
| `visibility_career`, `visibility_marketplace` | **Mapped** | `status` (`draft` / `active` / `inactive`), `visibility`, `is_featured` (`Yes` / `No`), `availability` |
| `end_date` | **Mapped** | `start_date`, `end_date` (Product Start Date / End Date) |
| `min_exp`, `max_exp`, `work_from_home`, `work_shifts`, `qualification`, `required_columns` (gender), `hiring_manager_id`, `recruiter_id`, `customTitleId` (screening questions), `carrier_website`, `salary_visibility` | **Removed** | Not applicable to Sales CRM Products — removed cleanly |

---

## 4. UI & Architecture Notes for Phase 2–4
1. **UI Isolation:** Created in `resources/views/service_company/products/create.blade.php` with zero shared mutable state with `jobs/create.blade.php`.
2. **Dynamic Calculations (Client-First):** Final Selling Price and Profit Margin compute instantaneously in browser RAM on input without server round-trips.
3. **Conditional Inventory & Physical Specs:** Selecting `Digital` or `Service` automatically adapts/hides physical-only fields (Weight, Dimensions, Warehouse/Stock where inapplicable).
4. **Backend (Pending UI Approval):** Will be implemented in Phase 4 with dedicated `products`, `product_categories`, `product_subcategories` tables (tenant-scoped by `company_id`), `StoreProductRequest`, transactions, and Elasticsearch sync.
