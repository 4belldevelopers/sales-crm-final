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
    'statusTab' => 'invited',
]);
app()->instance('request', $request);

$response = $controller->data_ajax($request);
$data = $response->getData(true);

echo "Total: " . ($data['total'] ?? 'N/A') . "\n";
echo "Raw data count: " . count($data['raw_data'] ?? []) . "\n";
foreach ($data['raw_data'] ?? [] as $u) {
    echo "ID: " . $u['id'] . ", Name: " . $u['name'] . ", Status: " . $u['status'] . ", Password len: " . strlen($u['password'] ?? '') . "\n";
}
echo "HTML has Secondary Recruiter: " . (strpos($data['html'], 'secondary recruiter') !== false ? 'YES' : 'NO') . "\n";
echo "HTML has Vibhanshu: " . (strpos($data['html'], 'vibhanshu jaiswal') !== false ? 'YES' : 'NO') . "\n";
