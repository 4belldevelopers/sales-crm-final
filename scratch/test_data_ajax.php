<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(2); // Vibhanshu Jaiswal, company_id = 2
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

echo "Total: " . ($data['total'] ?? 'N/A') . "\n";
echo "is_full_dataset: " . (json_encode($data['is_full_dataset'] ?? null)) . "\n";
echo "raw_data count: " . (isset($data['raw_data']) ? count($data['raw_data']) : 'N/A') . "\n";
echo "Counts:\n";
print_r($data['counts'] ?? []);

echo "\nRaw data users:\n";
foreach ($data['raw_data'] ?? [] as $u) {
    echo "ID: " . $u['id'] . ", Name: " . $u['name'] . ", Status: " . $u['status'] . ", Password len: " . strlen($u['password'] ?? '') . ", verification_code: " . ($u['email_verification_code'] ?? 'null') . "\n";
}
