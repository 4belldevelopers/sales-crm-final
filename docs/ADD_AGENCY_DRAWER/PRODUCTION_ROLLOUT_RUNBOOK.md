# Production Rollout & Operational Runbook: Add Agency Drawer

**Project:** `C:\xampp\htdocs\git\JWMaincode28June2026`  
**Page:** Service Company → Jobs → Add Agency Drawer  
**Feature Scope:** Jobs Listing → Add Agency Drawer (`spartnerlisting.blade.php`), Recommended Agency, My Agency, Assigned Agency, Search & Skill Filter, Send Requirement, Ignore / Remove Agency.  

---

## 1. Pre-Deployment Checklist

Before merging or rolling out to staging/production:

1. **Syntax & Static Analysis:**
   ```powershell
   php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php
   php -l resources/views/service_company/ajax_pages/find_agency_recom_ajax.blade.php
   php -l resources/views/service_company/ajax_pages/rec_register_agency_ajax.blade.php
   php -l resources/views/service_company/jobs/index.blade.php
   php -l resources/views/service_company/jobs/spartnerlisting.blade.php
   ```
   *Expectation: 0 syntax errors detected.*

2. **Automated Verification Harness:**
   ```powershell
   php scratch/verify_all_drawer_features.php
   ```
   *Expectation: All 6 lifecycle assertions pass.*

3. **Verify Git File Isolation:**
   ```powershell
   git status -s
   ```
   Ensure NO files under `resources/views/product_company/` or `resources/views/staffing_company/` are modified.

---

## 2. Deployment Steps

### Step 1: Deploy Application Code
Pull/deploy the changes onto web/application servers running FrankenPHP / Octane / Apache.

### Step 2: Clear and Warm Application Caches
Run standard Laravel maintenance commands:
```powershell
php artisan view:clear
php artisan route:clear
php artisan config:clear
```
*(If route caching is enabled in production, run `php artisan route:cache`)*.

### Step 3: Atomic Tenant Cache Invalidation (Optional Safety Flush)
If you wish to force all logged-in recruiters to pull fresh drawer HTML immediately upon deployment:
```powershell
php artisan cache:forget ats:job_summary_meta:*
```
Or flush the relevant Redis cache tags/keys. Note that any agency mutation (`send_requirement`, `remove_job`, `assign_rejected`) will automatically bump version keys on its own.

### Step 4: Verify Elasticsearch Indices
Ensure Elasticsearch is healthy and reachable:
- Index `companies` (`ELASTIC_COMPANIES_INDEX`) must be green/yellow.
- Index `client_and_agency_relation` (`ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX`) must be green/yellow.
- Index `job_requests` (`ELASTIC_JOB_REQUESTS_INDEX`) must be green/yellow.

---

## 3. Post-Deployment Smoke Testing Runbook

Perform the following manual smoke tests on the production/staging UI:

1. **Drawer Opening:**
   - Navigate to `http://localhost/git/JWMaincode28June2026/service_company/jobs`.
   - Click the "Add Agency" button on any active job row.
   - **Verification:** Drawer slides out immediately without freezing the page. Loading spinner displays briefly, followed by recommended agency cards.

2. **Filter & Search:**
   - Type a keyword into the skill tagify / search input.
   - **Verification:** Tagify filters asynchronously; results re-render without page reload or table blanking.

3. **Multi-Job Switch Test:**
   - Close the drawer.
   - Click "Add Agency" on a *different* job requisition.
   - **Verification:** Drawer opens immediately and loads the new job's recommended agencies. Table does NOT remain blank or stuck.

4. **Tab Switching:**
   - Switch to "My Agency" tab → Verify registered agency listing renders in < 500ms.
   - Switch to "Assigned Agency" tab → Verify assigned cards render with KPIs and 3-dot menus.

5. **Send Requirement Mutation:**
   - Click "Send Requirement" on a recommended agency.
   - **Verification:** SweetAlert confirms success; button state changes to "Request Sent"; reopening the drawer displays the updated state.

6. **Assigned Agency "Ignore" / "Remove":**
   - Click "Ignore" on an assigned agency card.
   - **Verification:** Request succeeds with HTTP 200 (no 500 error); agency is ignored and card is removed from view.

---

## 4. Monitoring & Telemetry Thresholds

Monitor application performance using APM (e.g., Laravel Pulse, Horizon, Sentry, Datadog):

| Telemetry Point | Normal Healthy Range | Warning Threshold | Critical Incident Threshold |
|---|---|---|---|
| `recommendedData` Response Time | 2 ms – 50 ms | > 150 ms | > 500 ms |
| `recommendedData` MySQL Queries | 0 – 3 queries | > 6 queries | > 15 queries (Indicates N+1 regression) |
| Conditional Request ETag Duration | 1 ms – 5 ms | > 20 ms | > 50 ms |
| External cURL HTTP Calls | **0 calls** | > 0 calls | > 5 calls |
| Browser Script Parsing Time | < 25 ms | > 100 ms | > 300 ms (Check for external SVG scripts) |

---

## 5. Rollback Plan

If any critical issue arises during deployment:
1. Revert Git commit:
   ```powershell
   git revert HEAD --no-edit
   ```
2. Clear view caches:
   ```powershell
   php artisan view:clear
   ```
3. Restart workers / Octane:
   ```powershell
   php artisan octane:reload
   ```
All changes are fully backward-compatible and introduce zero schema changes, so rollback is instantaneous and safe.
