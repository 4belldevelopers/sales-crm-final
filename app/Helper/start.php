<?php

use App\Models\Company;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

<<<<<<< HEAD
// Universal fallback autoloader for App\ namespace:
// Guarantees newly created classes (Services, Presenters, Actions, Models) resolve
// seamlessly on servers running authoritative/optimized Composer classmaps before 'composer dump-autoload'.
spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'App\\')) {
        $file = dirname(__DIR__).'/'.str_replace('\\', '/', substr($class, 4)).'.php';
        if (file_exists($file)) {
            require_once $file;

            return true;
        }
    }

    return false;
});

if (! class_exists(\App\Services\AppTrackerCardPresenter::class, false)) {
    $presenterPath = dirname(__DIR__).'/Services/AppTrackerCardPresenter.php';
=======
if (! class_exists(\App\Services\AppTrackerCardPresenter::class, false)) {
    $presenterPath = __DIR__ . '/../Services/AppTrackerCardPresenter.php';
>>>>>>> b668fffd0a7bcf8f6ee0aa21cf002288fc4a6910
    if (file_exists($presenterPath)) {
        require_once $presenterPath;
    }
}

<<<<<<< HEAD
if (! class_exists(\App\Services\CompanyProfileRestrictionService::class, false)) {
    $restrictionServicePath = dirname(__DIR__).'/Services/CompanyProfileRestrictionService.php';
    if (file_exists($restrictionServicePath)) {
        require_once $restrictionServicePath;
    }
}

if (! function_exists('superAdmin')) {
=======
if (!function_exists('superAdmin')) {
>>>>>>> b668fffd0a7bcf8f6ee0aa21cf002288fc4a6910
    function superAdmin()
    {
        return auth()->user();
    }
}
if (! function_exists('company')) {
    function company()
    {
        if (auth()->user()) {
            $company = Company::find(auth()->user()->company_id);

            return $company;
        }

        // return false;
    }
}

if (! function_exists('user')) {
    function user()
    {
        if (auth()->check()) {
            return auth()->user();
        }
        // return null;
    }
}

if (! function_exists('asset_url')) {

    // @codingStandardsIgnoreLine
    function asset_url($path)
    {
        $path = 'user-uploads/'.$path;
        $storageUrl = $path;

        if (! Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;

    }

}
if (! function_exists('check_migrate_status')) {

    function check_migrate_status()
    {
        if (! session()->has('check_migrate_status')) {

            $status = Artisan::call('migrate:check');

            if ($status && ! request()->ajax()) {
                Artisan::call('migrate', ['--force' => true]); // migrate database
                Artisan::call('optimize:clear');
            }
            session(['check_migrate_status' => true]);
        }

        return session('check_migrate_status');
    }
}

if (! function_exists('module_enabled')) {
    function module_enabled($moduleName)
    {
        return \Nwidart\Modules\Facades\Module::collections()->has($moduleName);
    }
}

if (! function_exists('getDomainSpecificUrl')) {
    function getDomainSpecificUrl($url, $company = false)
    {
        if (module_enabled('Subdomain')) {
            // If company specific

            if ($company) {
                $url = str_replace(request()->getHost(), $company->sub_domain, $url);
                $url = str_replace('www.', '', $url);
                // Only downgrade to http for subdomain in non-production (local dev may lack subdomain certs).
                // In production, preserve HTTPS to prevent token exposure over plaintext. (audit M4)
                if (! app()->isProduction()) {
                    return $url = str_replace('https', 'http', $url);
                }

                return $url;
            }

            // If there is no company and url has login means
            // New superadmin is created
            return $url = str_replace('login', 'super-admin-login', $url);
        }

        return $url;
    }
}

if (! function_exists('get_domain')) {

    function get_domain($host = false)
    {
        if (! $host) {
            $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'; // $_SERVER['HTTP_HOST'];
        }
        $myhost = strtolower(trim($host));
        $count = substr_count($myhost, '.');
        if ($count === 2) {
            if (strlen(explode('.', $myhost)[1]) >= 3) {
                $myhost = explode('.', $myhost, 2)[1];
            }
        } elseif ($count > 2) {
            $myhost = get_domain(explode('.', $myhost, 2)[1]);
        }

        return $myhost;
    }
}

if (! function_exists('recruit_plugins')) {

    function recruit_plugins()
    {

        if (! session()->has('recruit_plugins')) {
            $plugins = \Nwidart\Modules\Facades\Module::allEnabled();
            // dd(array_keys($plugins));

            // foreach ($plugins as $plugin) {
            //     Artisan::call('module:migrate', array($plugin, '--force' => true));
            // }

            session(['recruit_plugins' => array_keys($plugins)]);
        }

        return session('recruit_plugins');
    }
}

if (! function_exists('tenant_table')) {
    function tenant_table(string $table): \Illuminate\Database\Query\Builder
    {
        return \App\Helper\TenantDB::table($table);
    }
}
