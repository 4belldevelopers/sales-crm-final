# Clients Listing V2 Implementation Plan

## Goal
Optimize the Clients Listing page (`/service_company/clients`) to achieve sub-60ms response times, eliminate N+1 queries, minimize memory footprint, and support 10,000+ concurrent recruiters while strictly preserving tenant security and business logic.

---

## Current Problems Summary
1. **N+1 Queries in Blade Loop**: 40+ DB and ES sub-queries executed per page request inside `all-client-ajax.blade.php`.
2. **Memory Overhead in Hiring Filter**: `hiring=1` query loads all active client IDs into PHP memory arrays.
3. **Uncached Country Dropdown**: `DB::table('countries')->get()` executes on every page load.
4. **Missing Debounce on Search Input**: Typing triggers an AJAX request per keystroke.
5. **Missing MySQL Indexes**: `clients(company_id, self_company)` lacks composite index.

---

## V2 Architecture & Data Strategy

### 1. Batch Pre-Aggregation in Controller
Instead of querying job and candidate counts per row inside the Blade template, `ServiceCompanyClientController` will:
- Extract all `client_id`s and relation `company_id`s for the current page payload (10 items).
- Run 2 batch aggregation queries (1 ES batch query for jobs/candidates, 1 batch MySQL relation query).
- Pass pre-aggregated arrays (`$jobsCountsMap`, `$candidateCountsMap`, `$relationMap`) to the Blade view.

### 2. Batch Relation Fetching
Replace `GeneralMethod::getRelationData()` loop calls with a single batch query in controller:
```php
$relationMap = DB::table('staffing_company_relation')
    ->where('to_company_id', $company_id)
    ->whereIn('from_company_id', $pageCompanyIds)
    ->get()
    ->keyBy('from_company_id');
```

### 3. Country Dropdown Caching
Cache the static country list for 24 hours:
```php
$this->countries = cache()->remember('global_countries_list', 86400, function () {
    return DB::table('countries')->select('id', 'country_name')->get();
});
```

---

## Index & Schema Enhancements

Recommend applying the following non-blocking indexes in MySQL (`mysql-schema.sql`):
```sql
-- Composite index for tenant client lookups
ALTER TABLE `clients` ADD INDEX `idx_clients_company_self` (`company_id`, `self_company`);

-- Composite index for relation lookups
ALTER TABLE `staffing_company_relation` ADD INDEX `idx_rel_to_status_from` (`to_company_id`, `final_status`, `from_company_id`);
```

---

## Frontend AJAX & Debounce Optimization

In [resources/views/service_company/client/index.blade.php](file:///C:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/index.blade.php):
- Wrap `fetchCandidates()` in a 300ms JS debounce timer:
```javascript
let searchTimer = null;
$('#kt_tagify_2').on('keyup', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        fetchCandidates(1);
    }, 300);
});
```

---

## Proposed Code Changes

### Files to be Modified
1. `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php`
   - Update `index()` to cache country query.
   - Update `all_clients_data_ajax()` to batch fetch relations, job counts, and candidate funnel metrics before rendering view.
2. `resources/views/service_company/ajax_pages/all-client-ajax.blade.php`
   - Remove per-row `GeneralMethod::getRelationData()`, `InternalClientJobsCountAllStatus()`, `InternalClientCandidatesCountList()` calls.
   - Access pre-computed `$jobsCountsMap[$clientId]` and `$relationMap[$companyId]`.
3. `resources/views/service_company/client/index.blade.php`
   - Add 300ms debounce to search input keyup handler.

---

## Security & Permission Preservation
- **Tenancy Scoping**: All database and Elasticsearch queries strictly scope by `auth()->user()->company_id`.
- **Permission Guards**: `abort_if(Gate::denies('view_clients'), 403)` is enforced on all client data endpoints.
- **Business Logic Rules**: Status values (`final_status = 1`), self vs. direct client classifications, and count definitions remain identical.

---

## Verification Plan

### Automated Tests
- Run `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php`
- Run `php -l resources/views/service_company/ajax_pages/all-client-ajax.blade.php`
- Run `php -l resources/views/service_company/client/index.blade.php`

### Manual Verification
- Test client listing rendering (verify counts, names, locations, job counts, performance funnels).
- Test search with debouncing.
- Test location and sort filters.
- Test hiring filter.
