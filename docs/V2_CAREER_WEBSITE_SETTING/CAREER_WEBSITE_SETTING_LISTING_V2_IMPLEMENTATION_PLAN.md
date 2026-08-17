# V2 Career Website Setting Page - Technical Implementation Plan

> **Document Type:** Architecture & Implementation Plan  
> **Target Module:** Service Company - Career Page Settings  
> **Route:** `http://localhost/git/JWMaincode28June2026/service_company/career-page/settings`  
> **Target Concurrency Goal:** 10,000+ Concurrent Recruiters  
> **Date:** August 17, 2026  
> **Author:** Antigravity AI  

---

## 1. V2 Objective & Architectural Vision

The primary objective of the **V2 Career Website Setting Architecture** is to transform the existing career settings module into a **high-concurrency, enterprise-grade, zero-DDL, cached multi-tenant system** capable of supporting 10,000+ concurrent recruiters without performance degradation.

### Key Performance Targets
- **Database Query Latency:** < 2ms (served from Redis cache).
- **HTTP Response Time:** < 50ms (p99).
- **Runtime DDL Operations:** Exactly 0 (all schema management migrated to proper DB migrations).
- **Concurrent Recruiter Support:** 10,000+ recruiters across autoscaling web nodes.
- **Cache Hit Ratio:** > 99.5% for GET settings reads.

---

## 2. Proposed V2 Architecture

```
[ Recruiter Browser ]
         │
         │  GET /service_company/career-page/settings
         ▼
[ Nginx Load Balancer / aaPanel Node Pool ]
         │
         ▼
[ ServiceCompanyAccountSettingsController@careerPageSettings ]
         │  (Delegates 100% data access)
         ▼
[ AccountSettingsService @ getCareerPageSettings ]
         │
         ├──► [ Redis Cache check: "company_career_settings:{company_id}" ]
         │         │
         │         ├─► (Cache Hit) ──► Return Cached Object (< 1ms)
         │         │
         │         └─► (Cache Miss) ─► AccountRepository @ getCareerSettings()
         │                                   │
         │                                   ▼
         │                             [ MySQL DB: company_career_settings ]
         │                                   │
         │                                   └─► Cache Result in Redis (24h)
         ▼
[ Modular Blade View: service_company.career-page.settings ]
   (Renders tab partials from resources/views/service_company/career-page/tabs/*)
```

---

## 3. Query & Schema Optimization Strategy

### 3.1 Migration Strategy (Eliminate Runtime DDL)
1. **Remove Dynamic Schema Code:** Delete all `CREATE TABLE IF NOT EXISTS`, `SHOW COLUMNS FROM`, and `ALTER TABLE` statements from `ServiceCompanyAccountSettingsController.php`.
2. **Create Formal Migration:** Create `database/migrations/2026_08_17_000001_create_company_career_settings_table.php` defining all columns with proper default values and indexes.
3. **Update Schema Master:** Synchronize `database/schema/mysql-schema.sql` to include `company_career_settings`.

### 3.2 Eloquent Model Creation
Create `App\Models\CompanyCareerSetting` with fillable attributes, proper type casts, and tenant scope:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCareerSetting extends Model
{
    protected $table = 'company_career_settings';

    protected $fillable = [
        'company_id', 'primary_color', 'secondary_color', 'font_family',
        'default_language', 'currency', 'page_layout', 'jobs_display',
        'career_page_status', 'td_template', 'td_color_primary',
        'td_color_secondary', 'td_color_accent', 'td_color_bg',
        'td_theme_mode', 'td_layout', 'td_logo_path', 'td_favicon_path',
        'td_hero_img1_path', 'td_hero_img2_path', 'td_opt_life',
        'td_opt_testi', 'td_opt_animation', 'td_custom_css',
        'stat_departments', 'stat_countries', 'stat_employees', 'stat_retention'
    ];

    protected $casts = [
        'career_page_status' => 'boolean',
        'td_opt_life' => 'boolean',
        'td_opt_testi' => 'boolean',
        'td_opt_animation' => 'boolean',
        'stat_departments' => 'integer',
        'stat_countries' => 'integer',
    ];
}
```

---

## 4. Cache, Redis & Invalidation Strategy

### 4.1 Caching Keys
- **Admin Settings Cache Key:** `company_career_settings:{company_id}` (TTL: 86,400s / 24h).
- **Public Career Portal Meta Cache Key:** `tenant_career_meta:{company_id}` (TTL: 86,400s / 24h).

### 4.2 Cache Invalidation Flow (On POST Save)
When a recruiter updates career page settings:
1. Save settings to MySQL database via `CompanyCareerSetting::updateOrCreate()`.
2. Flush backend settings cache: `Cache::forget("company_career_settings:{$company_id}")`.
3. Flush public candidate portal cache: `Cache::forget("tenant_career_meta:{$company_id}")`.
4. Re-prime Redis cache asynchronously via background listener or job.

---

## 5. Media & Asset Storage Optimization Strategy

### 5.1 Cloud Storage & Multi-Node Support
- Replace local `move()` calls with Laravel's unified filesystem disk manager (`Storage::disk(config('filesystems.default'))`).
- Path structure: `uploads/tenants/{company_id}/career/{filename}`.
- Generates CDN-compatible absolute URLs (`Storage::disk('public')->url(...)` or S3 CDN URLs).

### 5.2 Async Background Image Processing Queue
- Create background job `ProcessCareerPageAssetsJob` to handle image resizing, compression, and conversion to WebP format.
- Reduces asset load times on candidate-facing portals by up to 70%.

---

## 6. Frontend & Modular Blade Optimization

### 6.1 Modular Blade Component Structure
Split the 3,904-line single Blade template into clean modular components:
```
resources/views/service_company/career-page/
├── settings.blade.php               (Main layout container ~150 lines)
├── components/
│   ├── topbar.blade.php
│   ├── sidebar.blade.php
│   └── tab_navigation.blade.php
└── tabs/
    ├── general.blade.php
    ├── hero.blade.php
    ├── company.blade.php
    ├── benefits.blade.php
    ├── jobs.blade.php
    ├── seo.blade.php
    ├── integrations.blade.php
    ├── advanced.blade.php
    └── templates.blade.php
```

### 6.2 Asset Bundling & Performance
- Move CSS block (~800 lines) to compiled CSS stylesheet `resources/css/career-settings.css`.
- Move JS scripts (~400 lines) to compiled JS module `resources/js/career-settings.js`.

---

## 7. Server & Infrastructure Tuning (10,000+ Concurrency)

### 7.1 Server Configuration Tuning (aaPanel / Ubuntu 24 / 31.34 GB RAM / 8 Cores)
1. **PHP 8.2 FPM Worker Pool:**
   - Set `pm = dynamic`, `pm.max_children = 120`, `pm.start_servers = 20`, `pm.min_spare_servers = 10`, `pm.max_spare_servers = 40`.
   - Allocate 128MB memory limit per worker.
2. **PHP OPcache Tuning:**
   - Enable `opcache.enable=1`, `opcache.enable_cli=1`.
   - `opcache.memory_consumption=512`, `opcache.interned_strings_buffer=64`, `opcache.max_accelerated_files=30000`.
   - Set `opcache.validate_timestamps=0` in production for zero disk I/O overhead.
3. **Nginx High-Concurrency Directives:**
   - `worker_processes auto;`, `worker_rlimit_nofile 65535;`.
   - `events { worker_connections 8192; use epoll; multi_accept on; }`.
   - Enable `gzip` and HTTP/2.
4. **MySQL InnoDB Buffer Pool Tuning:**
   - Set `innodb_buffer_pool_size = 20G` (~65% of 31.34 GB RAM).
   - Set `innodb_log_file_size = 2G`, `max_connections = 1000`.
5. **Redis In-Memory Data Store:**
   - `maxmemory 4gb`, `maxmemory-policy allkeys-lru`.

---

## 8. Phase-Wise Implementation Roadmap

| Phase | Task Description | Affected Files | Expected Benefit / Output | Risk Level |
| :--- | :--- | :--- | :--- | :--- |
| **Phase 1** | **Database Migration & Schema Cleanup** | `database/migrations/xxxx_create_company_career_settings_table.php`, `database/schema/mysql-schema.sql`, `app/Models/CompanyCareerSetting.php` | Creates formal DB table migration and Eloquent model; enables clean schema lifecycle. | Low |
| **Phase 2** | **Controller Refactoring & DDL Removal** | `app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php` | Completely removes runtime DDL (`CREATE TABLE`, `ALTER TABLE`, `SHOW COLUMNS`). | Low |
| **Phase 3** | **Service Layer & Redis Caching** | `app/Services/AccountSettingsService.php`, `app/Repositories/AccountRepository.php` | Wraps setting reads in Redis cache (`company_career_settings:{company_id}`). | Low |
| **Phase 4** | **Storage & Media Asset Standardization** | `app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php` | Replaces local `move()` calls with `Storage::disk()`; enables cloud S3 compatibility. | Medium |
| **Phase 5** | **Modular Blade & JS/CSS Extraction** | `resources/views/service_company/career-page/settings.blade.php`, `resources/views/service_company/career-page/tabs/*.blade.php` | Splits 3,904-line Blade into modular tab components and externalized assets. | Low |
| **Phase 6** | **High Concurrency & Load Verification** | Automated tests & server config scripts | Verifies 10,000+ recruiter concurrency performance and zero DDL execution. | Low |

---

## 9. Business Logic & Tenancy Preservation Matrix

| Feature / Logic Element | Current Implementation | V2 Implementation Rule | Verification Method |
| :--- | :--- | :--- | :--- |
| **Tenant Scoping** | `auth()->user()->company_id` | Strict tenant filtering retained in Service/Repository layer. | Automated tenancy test (`company_id` isolation). |
| **Public Career Page URL** | `url('career-jobs/{name}/{company_id}')` | Retained 100% without modification. | Route parameter test. |
| **Candidate Portal Cache Invalidation** | `Cache::forget("tenant_career_meta:{$company_id}")` | Retained on form save inside `AccountSettingsService`. | Redis key inspection after save. |
| **Branding Field Schema** | All 30+ branding attributes (colors, fonts, CSS, stats) | Preserved exact field names for full view compatibility. | Data integrity assertion test. |

---

## 10. Comprehensive Verification & Testing Plan

> **CRITICAL RULE:** Do **NOT** use `RefreshDatabase` trait during testing, as it destroys database state on production/shared development databases.

### 10.1 Unit & Integration Tests (`tests/Feature/CareerPageSettingsV2Test.php`)
1. **GET Settings Route Test:** Verify `GET /service_company/career-page/settings` returns HTTP 200 with correct data structure.
2. **Zero DDL Execution Test:** Assert no DDL queries (`CREATE TABLE`, `ALTER TABLE`) are logged during GET request execution.
3. **Redis Caching Test:** Assert GET request loads data from Redis cache on hit; assert `Cache::forget` invalidates keys on save.
4. **POST Save Route Test:** Submit form payload and verify `company_career_settings` database record updates correctly.
5. **Tenant Security Test:** Verify User from Company A cannot view or mutate career settings of Company B.

### 10.2 Manual & UI Verification Steps
1. Navigate across all 9 tabs (General, Hero, Company Info, Benefits, Job Settings, SEO, Integrations, Advanced, Templates).
2. Change primary color swatch and verify live preview card updates in real time.
3. Upload logo and favicon; verify image previews update correctly.
4. Save settings; verify success toastr message appears and settings persist upon page refresh.
5. Open public candidate career page URL in incognito tab; verify updated brand colors and logos reflect immediately.

---

## 11. Production Rollout & Zero-Downtime Strategy

1. **Pre-Deployment:** Run DB migration `php artisan migrate` to create `company_career_settings` table in production DB.
2. **Deployment:** Deploy refactored controller, service, repository, and modular Blade components.
3. **Post-Deployment Warm-up:** Execute Artisan command to pre-warm Redis cache for active companies.
4. **Monitoring:** Monitor MySQL query log, Nginx access logs, and Horizon queue status.

---

## 12. Rollback & Emergency Fallback Plan

1. **Feature Toggle / Code Fallback:** In case of emergency issues, revert `routes/service.php` to target legacy controller method.
2. **Database Integrity:** Since V2 maintains identical table schema and column names, no database rollback or data loss will occur.
3. **Cache Flush Command:** Run `php artisan cache:clear` to flush stale Redis cache keys if inconsistent state occurs.

---
