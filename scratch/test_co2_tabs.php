<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController;

$u = User::find(2);
auth()->login($u);
Illuminate\Support\Facades\Gate::before(fn () => true);

$controller = app(ServiceCompanyTeamController::class);

echo "=== TAB: ALL ===\n";
$reqAll = Request::create('/service-company/team/data-ajax', 'GET', ['statusTab' => 'all']);
$resAll = $controller->data_ajax($reqAll)->getData(true);
echo "Total: " . ($resAll['total'] ?? 0) . "\n";
echo "Raw data count: " . count($resAll['raw_data'] ?? []) . "\n";
foreach ($resAll['raw_data'] ?? [] as $r) {
    echo " - User: " . $r['name'] . " (ID: " . $r['id'] . ") | Status: " . $r['status'] . " | HasPassword: " . (!empty($r['password']) ? 'YES' : 'NO') . "\n";
}

echo "\n=== TAB: INVITED ===\n";
$reqInv = Request::create('/service-company/team/data-ajax', 'GET', ['statusTab' => 'invited']);
$resInv = $controller->data_ajax($reqInv)->getData(true);
echo "Total: " . ($resInv['total'] ?? 0) . "\n";
echo "Raw data count: " . count($resInv['raw_data'] ?? []) . "\n";
foreach ($resInv['raw_data'] ?? [] as $r) {
    echo " - User: " . $r['name'] . " (ID: " . $r['id'] . ") | Status: " . $r['status'] . " | HasPassword: " . (!empty($r['password']) ? 'YES' : 'NO') . "\n";
}
