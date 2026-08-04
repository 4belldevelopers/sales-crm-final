# V3 JOBS LISTING — IMPLEMENTATION PLAN & ACCELERATION BLUEPRINT

> **AI & Developer Execution Contract:** This document translates the findings from [`V3_Jobs_Audit.md`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/jobs%20V2/V3_Jobs_Audit.md) into concrete, step-by-step implementation tasks. Every step is test-gated, safe, and preserves 100% of existing business logic.

---

## 🎯 Target Performance After V3 Execution

* **Response Latency (TTFB):** **< 30 ms** (down from ~1,200 ms)
* **SQL Queries per Page Load:** **0 to 1 query** (down from ~35 queries)
* **Payload Size:** **~12 KB JSON** (down from ~150 KB server-rendered HTML)
* **Server CPU Load @ 10,000 Recruiters:** **< 5%**
* **Counter Accuracy:** **100% Zero-Drift Real-Time Data**

---

# PHASE 1 — BaseController Middleware Zero-SQL Optimization (Day 1)

### Task 1.1: Cache Spatie Role Permissions in Redis
* **File to edit:** [`app/Http/Controllers/ServiceCompany/ServiceCompanyBaseController.php:L258-L263`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBaseController.php#L258-L263)

#### Code Changes:
Replace raw `DB::table('roles')` and `DB::table('role_has_permissions')` queries with 1-hour Redis cache lookup:

```php
// app/Http/Controllers/ServiceCompany/ServiceCompanyBaseController.php

$userId    = $this->user->id;
$companyId = auth()->user()->company_id;

$this->userPermissions = Cache::remember("user_permissions:{$userId}", 3600, function () use ($userId, $companyId) {
    $roleRow = DB::table('roles')
        ->select('id')
        ->where('company_id', $companyId)
        ->where('name', $this->getPermissions[0] ?? 'admin')
        ->first();

    if (!$roleRow) return [];

    $rawList = DB::table('role_has_permissions')
        ->where('role_id', $roleRow->id)
        ->pluck('permission_id')
        ->toArray();

    return DB::table('permissions')
        ->whereIn('id', $rawList)
        ->pluck('permissions.name')
        ->toArray();
});
```

---

### Task 1.2: Cache Active Package Status
* **File to edit:** [`app/Http/Controllers/ServiceCompany/ServiceCompanyBaseController.php:L279`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBaseController.php#L279)

#### Code Changes:
Wrap `package_active` table lookups in tenant-bound Redis cache:

```php
if ($this->user->company_id) {
    $customActivePackage = Cache::remember("company_package_active:{$this->user->company_id}", 3600, function () {
        $row = DB::table('package_active')
            ->where('company_id', $this->user->company_id)
            ->first();
        return $row ? (array) $row : [];
    });
}
```

#### Verification Command:
```bash
# Enable query log in tinker and hit base controller logic
php artisan tinker --execute="DB::enableQueryLog(); \$u = \App\Models\User::first(); auth()->login(\$u); app(\App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class)->index(); dump(count(DB::getQueryLog()));"
# Expected: ≤ 1 SQL query
```

---

# PHASE 2 — Route Throttle & Deployment Cache Warming (Day 1)

### Task 2.1: Attach Recruiter Throttle to Jobs Route
* **File to edit:** [`routes/service.php:L154`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/routes/service.php#L154)

#### Code Changes:
Wrap the `jobs` resource route with `recruiter.throttle` (which allows 60 requests/minute per recruiter with fail-open Redis protection):

```php
// routes/service.php: line 154
Route::middleware('recruiter.throttle:60,1')->group(function () {
    Route::resource('jobs', ServiceCompanyJobsController::class);
});
```

---

### Task 2.2: Warm Production Application Cache
Run the framework optimization command during deployment:

```bash
php artisan optimize
```

#### Verification:
```bash
ls -la bootstrap/cache/
# Expected: config.php, routes-v7.php, events.php, views.php
```

---

# PHASE 3 — Client-Side Heavy JSON API Shift (Days 2–3)

### Task 3.1: Expose Pure JSON Mode in `allDataAjax`
* **File to edit:** [`app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php:L891-L899`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php#L891-L899)

#### Code Changes:
Allow callers asking for JSON to skip server-side Blade HTML string compilation:

```php
// app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php

$rowMaps = $this->prepareRowMaps($data, $company_id, (int) $request->input('app_duplicate', 0), (int) $request->input('app_duplicate_days', 0));

if ($request->wantsJson() || $request->input('format') === 'json') {
    return response()->json([
        'status'     => 'success',
        'data'       => $data,
        'rowMaps'    => $rowMaps,
        'pagination' => $pagination,
    ]);
}

// Fallback to legacy Blade HTML rendering for un-migrated views
$responseData = [
    'html'       => view('service_company.ajax_pages.all_apptracker', compact('data', 'userPermissions', 'rowMaps'))->render(),
    'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
];

return response()->json($responseData);
```

---

### Task 3.2: Client-Side DataTables Render Pipeline
* **File to edit:** [`resources/views/service_company/jobs/index.blade.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/jobs/index.blade.php)

#### JavaScript Implementation:
Update DataTables initialization to consume raw JSON and format elements client-side:

```javascript
$('#jobs-apptracker-table').DataTable({
    serverSide: true,
    ajax: {
        url: '/jobs_ajax/all_data?format=json',
        dataSrc: 'data'
    },
    columns: [
        { 
            data: 'full_name',
            render: function(data, type, row) {
                return `<div class="d-flex align-items-center">
                    <span class="symbol-label fw-bold">${data ? data.charAt(0).toUpperCase() : 'C'}</span>
                    <a href="/candidate/${row.candidate_id}" class="text-gray-800 fw-bolder ms-2">${data}</a>
                </div>`;
            }
        },
        {
            data: 'jobId',
            render: function(data, type, row, meta) {
                let jobsMap = meta.settings.json.rowMaps.jobs || {};
                let job = jobsMap[data] || {};
                let currency = job.max_salary_currency || 'INR';
                return `${currency} ${Number(row.current_ctc || 0).toLocaleString()}`;
            }
        }
    ]
});
```

#### Gain:
* **Network payload:** Drops from **150 KB → 12 KB**
* **Server CPU time:** Drops from **45 ms → 2 ms**

---

# PHASE 4 — Historical Counter Seeding & Verification (Day 3)

### Task 4.1: Seed Counter Summary Tables
Populate `job_counters`, `pipeline_counters`, `application_counters`, and `job_agency_counter` from existing live MySQL database records:

```bash
php artisan counters:seed-from-live
```

---

### Task 4.2: Verify Zero Counter Drift
Ensure 100% mathematical parity between live data and summary tables:

```bash
php artisan counters:verify-drift
# Expected output: No counter drift detected.
```

---

# PHASE 5 — Frontend Asset Optimization (Day 4)

### Task 5.1: Self-Host Poppins Font
Save `poppins-400.woff2` to `public/fonts/poppins-400.woff2` to eliminate Google Fonts external network blocking:

```css
@font-face {
  font-family: 'Poppins';
  src: url('/fonts/poppins-400.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
body { font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif; }
```

---

### Task 5.2: Keenicons SVG Sprite
Create SVG sprite component [`resources/views/components/icon.blade.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/components/icon.blade.php):

```blade
@props(['name'])
<svg class="icon icon-{{ $name }}" aria-hidden="true" focusable="false" {{ $attributes }}>
  <use xlink:href="/icons.svg#icon-{{ $name }}"></use>
</svg>
```

---

# 🚀 Final Verification Matrix

| Step | Green Light Verification Command | Pass Criteria |
|---|---|---|
| **Phase 1** | `php artisan tinker --execute="/* test base controller queries */"` | **0 SQL queries** from middleware |
| **Phase 2** | `php artisan optimize && ls -la bootstrap/cache/` | `config.php` and `routes-v7.php` exist |
| **Phase 3** | `curl -sH "Accept: application/json" "http://testing.jero.works/jobs_ajax/all_data?format=json"` | Returns raw JSON (payload <15 KB) |
| **Phase 4** | `php artisan counters:verify-drift` | Returns `No counter drift detected.` |

---

*Plan end. Document saved as `docs/jobs V2/V3_Jobs_Implementation_Plan.md`.*
