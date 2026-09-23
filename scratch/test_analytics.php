<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ServiceCompany\ServiceCompanyClientController;

$user = \App\Models\User::first();
Auth::login($user);
$bell = \DB::table('companies')->where('company_name', 'like', '%4Bell%')->first();
if (!$bell) {
    $bell = \DB::table('clients')->where('name', 'like', '%4Bell%')->first();
}
echo "4BELL RECORD: " . json_encode($bell) . "\n";
$recs = \DB::table('recommendation_relation')->where('from_company_id', 2)->get();
echo "RECOMMENDATIONS FOR 4BELL (id=2): " . json_encode($recs) . "\n";
if ($recs->isNotEmpty()) {
    $toCompId = $recs->first()->to_company_id;
    session(['company_id' => $toCompId]);
    echo "SETTING SESSION company_id TO: " . $toCompId . "\n";
}
$clientId = 2;
try {
    $user->givePermissionTo('view_clients');
} catch (\Throwable $e) {}

$es = app(\App\Services\ElasticService::class);
$user->company_id = 4;
Auth::login($user);
session(['company_id' => 4]);

$controller = app(ServiceCompanyClientController::class);
$response = $controller->dashboardAnalytics(2);
echo "STATUS: " . $response->getStatusCode() . "\n";
echo "PAYLOAD: " . json_encode($response->getData(), JSON_PRETTY_PRINT) . "\n";
