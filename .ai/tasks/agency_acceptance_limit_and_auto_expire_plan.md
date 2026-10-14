# Implementation Plan & Technical Architecture: Agency Acceptance Limit & Auto-Expire Bug Fix

## 1. Overview & Objective
This document outlines the root causes, architecture, implementation details, database schema mapping, and verification suite for the fix applied to the **Agency Acceptance Limit** and **Auto-expire** dropdown controls within the **Publish to Agency Marketplace** drawer on the JERO ATS Service Company Jobs page (`http://localhost/git/JWMaincode28June2026/service_company/jobs`).

---

## 2. Root Cause Analysis

### A. Frontend Disconnection (`spartnerlisting.blade.php`)
- **Missing Event Handlers:** The HTML `<select>` elements (`#marketplace_vendor_limit` and `#marketplace_auto_expire`) had no jQuery/JavaScript `change` listeners attached. Selecting an option did not trigger any network request.
- **Hardcoded Selection Markup:** The Blade template hardcoded the `selected` attribute on `value="20"` (for vendor limit) and `value="7"` (for auto-expire duration), completely ignoring existing values in the database.

### B. Controller Processing Bypass (`ServiceCompanyJobsController.php`)
- **Ignored Parameters in `auction_send_job_request`:** The existing endpoint only inspected publication state transitions (`job_cancel`). Incoming values for `vendor_limit` and `auto_expire` were completely unread and never written to the database.
- **Premature Return on Published Jobs:** If a job was already published (`public_market_status == 2`), the endpoint exited early without saving any configuration changes.
- **Hardcoded Drawer Values in `spartnerlisting`:** In the method rendering the drawer, `$this->vendor_limit` was hardcoded to `20`, preventing any saved database values from reflecting in the view.
- **Hardcoded Threshold in `marketrequestaadded`:** The check that halts invitations when limits are reached hardcoded `$exceeded = 20;` instead of reading the job's configured limit.

---

## 3. Implementation Details & Architecture

### A. Database Storage (No Migrations / Schema Integrity)
In accordance with `database/schema/mysql-schema.sql` (217 tables truth rule):
- **Storage Location:** `jobs.meta_details` (JSON cast array).
- **Auto-expire Calculation:** Updated `jobs.end_date` using `Carbon::now()->addDays($auto_expire)->format('Y-m-d 23:59:59')` and set `jobs.closeType = 1` (automated expiry).
- **Zero Schema Alterations:** Avoided adding unnecessary columns; preserved full backward compatibility with all job queries.

### B. Controller Changes (`ServiceCompanyJobsController.php`)
1. **`spartnerlisting($id)`:**
   - Reads `vendor_limit` and `auto_expire` dynamically from the job's `meta_details` (with validation fallbacks).
   - Passes `$vendor_limit` and `$auto_expire` to the Blade view and JSON payload.
   - Enforces true multi-tenant isolation (`$isSuperAdmin` check).
2. **`auction_send_job_request(Request $request)`:**
   - Validates input values:
     - `vendor_limit`: `[5, 10, 20, 50]`
     - `auto_expire`: `[7, 14, 30]`
   - Handles standalone setting updates (`action === 'update_settings'`) for both published and unpublished jobs without altering the publication status.
   - Atomically updates `jobs.meta_details`, `jobs.end_date`, and `jobs.closeType`.
   - Invalidates Redis/Dragonfly caches and purges client RAM caches.
   - Synchronizes changes to Elasticsearch via Laravel Scout.
3. **`marketrequestaadded(Request $request)`:**
   - Replaced hardcoded `$exceeded = 20` with dynamic limit from `jobs.meta_details['vendor_limit']`.

### C. Eloquent Model (`app/Models/Job.php`)
- Added accessor `getVendorLimitAttribute(): int`.
- Added accessor `getAutoExpireAttribute(): int`.
- Made `getActiveAttribute()` safe for date string formats using `Carbon::parse()`.

### D. Frontend Drawer Integration (`spartnerlisting.blade.php`)
- Rendered `selected` attributes dynamically based on saved job values.
- Attached AJAX change listeners to `#marketplace_vendor_limit` and `#marketplace_auto_expire`.
- Added request debouncing, mutex locking (`data-is-updating`), and aborting of in-flight requests.
- Added visual rollbacks to restore previous values if a request fails or is rejected.
- Busts client RAM cache `window.jobDrawerCache['spartner_' + jobId]`.
- Displays real-time SweetAlert Metronic toasts on successful saves.

---

## 4. Multi-Tenant Security & CQRS Compliance
- **Strict Tenancy Scoping:** Non-superadmin users can only view and update jobs belonging to their own `company_id`.
- **CQRS Read/Write Separation:** Updates write to MySQL and trigger immediate index sync to Elasticsearch.
- **Cache Invalidation:** Multi-tenant cache keys (`allsendinvitationtoagency_v5_version_*`) are atomically incremented.

---

## 5. Automated Verification Suites

The implementation is verified by an automated PHPUnit feature test suite:
**File:** `tests/Feature/AgencyAcceptanceLimitAndAutoExpireTest.php`

### Verified Test Cases (100% Green, 10/10 Passed)
1. `test_change_agency_acceptance_limit_persists_correctly`
2. `test_change_auto_expire_duration_calculates_and_persists_end_date`
3. `test_change_both_settings_sequentially_preserves_both`
4. `test_drawer_renders_saved_settings_authoritatively`
5. `test_update_settings_on_published_job_preserves_published_state`
6. `test_invalid_setting_values_are_rejected`
7. `test_unauthorized_cross_tenant_update_is_rejected`
8. `test_rapid_successive_updates_retain_latest_value`
9. `test_publish_operation_saves_dropdown_settings`
10. `test_unpublish_operation_preserves_configured_settings`

### Regression Suites Passing:
- `AgencyMarketplacePublishTest`: 6/6 passed.
- `MarketJobV2Test`: 15/15 passed.
