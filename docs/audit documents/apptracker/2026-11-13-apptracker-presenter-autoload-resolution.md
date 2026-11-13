# App Services Autoload Resolution & Server Synchronization Audit

- **Date:** 2026-11-13
- **Author:** JERO Architecture Team
- **Component:** `App\Services\AppTrackerCardPresenter`, `App\Services\CompanyProfileRestrictionService`, `app/Helper/start.php`, `AppServiceProvider`, Account Settings Controller, AppTracker Views
- **Scope:** Root cause forensic investigation, permanent in-code universal PSR-4 fallback autoloader, and production server deployment synchronization commands.

---

## 1. Executive Summary

During production deployment testing on `hire.jero.works`, two separate fatal class resolution errors occurred on newly introduced service classes:
1. `Illuminate\View\ViewException`: `Class "App\Services\AppTrackerCardPresenter" not found` in `all_apptracker.blade.php` and `all_candidates.blade.php`.
2. `Illuminate\Contracts\Container\BindingResolutionException`: `Target class [App\Services\CompanyProfileRestrictionService] does not exist` in `ServiceCompanyAccountSettingsController.php:799`.

A forensic audit revealed that both files exist and are syntactically sound. The error occurs because production environments deploy with Composer optimized/authoritative classmaps (`"optimize-autoloader": true`) and persistent Laravel Octane/FrankenPHP workers in RAM. When new classes are pulled, running workers and static classmaps fail to resolve the class until Composer dumps the classmap and Octane workers are reloaded.

To permanently eliminate this vulnerability without relying solely on manual server commands, a universal fallback PSR-4 autoloader has been implemented across the early autoload lifecycle (`app/Helper/start.php` via Composer's `files` array), accompanied by explicit service provider bindings in `AppServiceProvider::register`.

---

## 2. Root Cause Analysis

1. **Composer Authoritative / Optimized Classmap:**
   In production (`composer.json` has `"optimize-autoloader": true`), Composer generates a static map in `vendor/composer/autoload_classmap.php`. In authoritative mode, if a newly added class is missing from this file, Composer's `ClassLoader::findFile()` immediately returns `false` without performing a disk scan.
2. **Octane / FrankenPHP Worker In-Memory Caching:**
   Octane keeps persistent worker processes alive in memory. Once a worker records a class lookup failure (`$missingClasses`), it will never re-attempt to find that class on disk during the worker's lifespan until `php artisan octane:reload` or `php artisan octane:restart` is issued.
3. **Laravel Service Container Resolution:**
   `app(CompanyProfileRestrictionService::class)` invokes `Illuminate\Container\Container::make()`, which relies on `class_exists()`. Because the authoritative classmap fails, the container throws `BindingResolutionException: Target class does not exist`.

---

## 3. Implemented Multi-Tier Universal Solution

To make the application 100% resilient regardless of server autoloader state:

1. **Universal Fallback PSR-4 Autoloader (`app/Helper/start.php`):**
   `app/Helper/start.php` is explicitly listed under Composer's `"files"` array, executing synchronously on every PHP request and worker boot before routing. It registers an `spl_autoload_register` fallback:
   ```php
   spl_autoload_register(function ($class) {
       if (str_starts_with($class, 'App\\')) {
           $file = dirname(__DIR__) . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
           if (file_exists($file)) {
               require_once $file;
               return true;
           }
       }
       return false;
   });
   ```
   If Composer's static classmap has a cache miss for ANY class in `App\`, this fallback seamlessly checks disk and requires the file, preventing fatal crashes.
2. **Explicit Early Preload Safeguards:**
   Added explicit `class_exists()` and `require_once` checks in `start.php` for `AppTrackerCardPresenter` and `CompanyProfileRestrictionService`.
3. **Framework Service Provider (`App\Providers\AppServiceProvider::register`):**
   Registers `CompanyProfileRestrictionService` as a singleton in the Laravel container with explicit disk fallback.
4. **Defensive View-Level Requiring:**
   Added fallback file checks in `all_apptracker.blade.php`, `all_candidates.blade.php`, and `clientappdata_ajax.blade.php`.

---

## 4. File Changes Matrix

| File | Change Description |
|---|---|
| `app/Helper/start.php` | Registered universal `App\` fallback autoloader via `spl_autoload_register` + explicit checks for `AppTrackerCardPresenter` and `CompanyProfileRestrictionService`. |
| `app/Providers/AppServiceProvider.php` | Added `register()` safeguard to guarantee `AppTrackerCardPresenter` and `CompanyProfileRestrictionService` are loaded into worker memory and container. |
| `resources/views/service_company/ajax_pages/all_apptracker.blade.php` | Added defensive view-level fallback before class invocation. |
| `resources/views/service_company/ajax_pages/all_candidates.blade.php` | Added defensive view-level fallback before class invocation. |
| `resources/views/service_company/ajax_pages/clientappdata_ajax.blade.php` | Added defensive view-level fallback before class invocation. |

---

## 5. Server Deployment Synchronization Commands

On `/www/wwwroot/hire.jero.works` (or any live environment), run the following commands to ensure Composer classmaps and Octane workers are fully synchronized:

```bash
# 1. Navigate to application root
cd /www/wwwroot/hire.jero.works

# 2. Pull the latest commits from git
git pull origin <branch_name>

# 3. Regenerate optimized Composer classmap
composer dump-autoload -o

# 4. Clear application, route, and compiled view caches
php artisan optimize:clear

# 5. Reload Octane FrankenPHP workers (or restart systemd service)
php artisan octane:reload
# or if running as a systemd service:
systemctl reload octane-staging.service || systemctl restart octane-staging.service
```

---

## 6. Verification Evidence

- `php -l app/Helper/start.php`: Syntax OK
- `php -l app/Providers/AppServiceProvider.php`: Syntax OK
- `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php`: Syntax OK
- `php -l app/Services/CompanyProfileRestrictionService.php`: Syntax OK
- `php vendor/bin/pint app/Helper/start.php app/Providers/AppServiceProvider.php`: Style OK
- `scratch/test_company_profile_7day_restriction_comprehensive.php`: **18 passed, 0 failed**
- `scratch/test_controller_http_and_view_rendering.php`: **All integration checks passed**
- `php artisan test --filter AppTrackerCardSubCardRedesignTest`: **13 passed, 344 assertions (0 failures)**
