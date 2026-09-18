# DIRECT CLIENT PARENT COMPANY DISPLAY — PROJECT-WIDE IMPLEMENTATION PLAN

> **Goal**: Deploy project-wide resolution of Parent/Main Company details for Direct Client jobs across all listing, tracker, drawer, export, and details components without altering Internal Job behavior or introducing N+1 queries.

---

## 1. Centralized Component Architecture

### Method: `GeneralMethod::resolveJobDisplayCompany($job, $viewerCompanyId = null, array $companiesMap = [])`
**Location**: `app/Helper/GeneralMethod.php`

**Logic**:
```php
/**
 * INTENT: Project-Wide Centralized Job Display Company Resolver.
 * Resolves Parent/Main Company Name and job classification for Direct Client vs Internal jobs.
 * TENANCY: Scoped by $viewerCompanyId (defaults to auth()->user()->company_id).
 */
public static function resolveJobDisplayCompany($job, $viewerCompanyId = null, array $companiesMap = [])
{
    $viewerCompanyId = $viewerCompanyId ?: (auth()->user()->company_id ?? 0);
    $row = (object) $job;

    $recommendation_id = (int) ($row->recommendation_id ?? $row->to_company_id ?? 0);
    $to_company_id = (int) ($row->to_company_id ?? 0);
    $from_company_id = (int) ($row->from_company_id ?? $row->comId ?? $row->company_id ?? 0);

    $isDirectClientJob = ($recommendation_id > 0 && $to_company_id === (int) $viewerCompanyId);

    $parentCompanyName = '';
    if ($isDirectClientJob) {
        $parentCompanyName = $companiesMap[$from_company_id]
            ?? ($row->company_name ?? ($row->clients_name ?? ($row->client_name ?? '')));
    }

    $clientTypeJob = $isDirectClientJob ? 'Direct' : 'Client';
    $tagText = $isDirectClientJob
        ? (! empty($parentCompanyName) ? $parentCompanyName : 'Direct Client')
        : (__('messages.internal_tag').' '.$clientTypeJob);

    $badgeClass = $isDirectClientJob ? 'badge-client' : 'badge-internal';

    return [
        'is_direct_client' => $isDirectClientJob,
        'display_name' => $parentCompanyName,
        'tag_text' => $tagText,
        'badge_class' => $badgeClass,
        'from_company_id' => $from_company_id,
    ];
}
```

---

## 2. Implementation Rollout Across Modules

### Phase 1: Core Controller & Helper Layer
- Add `GeneralMethod::resolveJobDisplayCompany()` in `app/Helper/GeneralMethod.php`.
- Integrate into `ServiceCompanyJobsController@all_data_ajax()` and drawer methods.

### Phase 2: Tracker AJAX Views & Renderers
- Update:
  - `resources/views/service_company/ajax_pages/interview-tracker-ajax.blade.php`
  - `resources/views/service_company/ajax_pages/offer-tracker.blade.php`
  - `resources/views/service_company/ajax_pages/bench-candidate-interview-schedule-ajax.blade.php`
  - `resources/views/service_company/ajax_pages/bench-candidate-offer-ajax.blade.php`
  - `resources/views/service_company/ajax_pages/bench-explore-list-ajax.blade.php`
  - `resources/views/service_company/ajax_pages/bench-recommend-req-ajax.blade.php`

### Phase 3: Export Classes
- Update `AppTrackerExport.php`, `ApplicationListExport.php`, `CvTrackerExport.php`, `InterviewExcelExport.php`, `JobOnBoardExport.php` to format `client_name` / `company_name` via `GeneralMethod::resolveJobDisplayCompany()`.

---

## 3. Verification & Performance Criteria

1. **Zero N+1 Queries**: All parent company names mapped via pre-fetched `$companiesMap` arrays.
2. **Zero Blade Database Queries**: No `DB::table()` calls in `.blade.php` files.
3. **Internal Job Preservation**: Internal Jobs retain `"Internal Client"` badge and behavior.
4. **Verification**: `php -l` and Pint code formatting.
