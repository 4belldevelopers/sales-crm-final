<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$companyId = 2;
$controller = app(App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController::class);

// Clear cache via controller
$ref = new ReflectionMethod($controller, 'clearTeamCache');
$ref->setAccessible(true);
$ref->invoke($controller, $companyId);

Cache::forget('ats:teamcounts:'.$companyId);
Cache::forget('ats:company_rep:defaults:'.$companyId);
Cache::forget('ats:company_rep:all_roles:'.$companyId);
Cache::forget('ats:company_rep:all_prios:'.$companyId);

echo "Cleared cache for company {$companyId}.\n";

$counts = $controller->getTeamCounts($companyId);
echo "New getTeamCounts for company {$companyId}:\n";
print_r($counts);
