<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(2);
auth()->login($user);

$controller = app(App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController::class);
$controller->user = $user;

$request = Illuminate\Http\Request::create('/service_company/team/data_ajax', 'GET', [
    'page' => 1,
    'size' => 10,
    'statusTab' => 'all',
]);

$response = $controller->data_ajax($request);
$data = $response->getData(true);

echo "HTML contains Secondary Recruiter: " . (strpos($data['html'], 'secondary recruiter') !== false ? 'YES' : 'NO') . "\n";
echo "HTML contains Vibhanshu: " . (strpos($data['html'], 'vibhanshu jaiswal') !== false ? 'YES' : 'NO') . "\n";
echo "Raw data items count: " . count($data['raw_data']) . "\n";
foreach ($data['raw_data'] as $idx => $r) {
    echo "Item {$idx}: ID {$r['id']} => {$r['name']}\n";
}
