<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Helper\GeneralMethod;
use App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

echo "=== FIXING USER 99992 PASSWORD ===\n";
$user = User::withoutGlobalScopes()->find(99992);
if ($user) {
    $user->password = bcrypt('Password123!');
    $user->status = 'active';
    $user->working_status = 1;
    $user->save();
    echo "User 99992 updated in MySQL. Password length: " . strlen($user->password) . "\n";
} else {
    echo "User 99992 not found in MySQL!\n";
}

echo "=== SYNCING USER 99992 TO ELASTICSEARCH ===\n";
try {
    GeneralMethod::userIndexingupload(99992);
    echo "userIndexingupload(99992) completed.\n";
} catch (\Throwable $e) {
    echo "ES sync error: " . $e->getMessage() . "\n";
}

echo "=== INVALIDATING CACHES FOR COMPANY 2 ===\n";
$keysToForget = [
    'ats:teamcounts:2',
    'ats:teamcounts:v3:2',
    'ats:company_rep:defaults:2',
];
foreach ($keysToForget as $k) {
    Cache::forget($k);
}
// Clear versioned keys
$versionKey = 'ats:teamversion:company_2';
Cache::forget($versionKey);

echo "=== VERIFYING getTeamCounts(2) FROM CONTROLLER ===\n";
$controller = app(ServiceCompanyTeamController::class);
$counts = $controller->getTeamCounts(2);
echo "Resulting teamCounts for company 2:\n";
print_r($counts);
