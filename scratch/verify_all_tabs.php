<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(2);
auth()->login($user);

$controller = app(App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController::class);
$controller->user = $user;

$tabs = ['all', 'recruitment', 'client', 'vendor', 'inactive', 'invited'];

echo "=== TESTING SERVER DATA_AJAX RESPONSES FOR ALL TABS ===\n";

foreach ($tabs as $tab) {
    $request = Illuminate\Http\Request::create('/service_company/team/data_ajax', 'GET', [
        'page' => 1,
        'size' => 10,
        'statusTab' => $tab,
    ]);
    app()->instance('request', $request);

    $response = $controller->data_ajax($request);
    $data = $response->getData(true);
    $raw = $data['raw_data'] ?? [];

    $names = array_map(function ($u) { return $u['name'] . " (ID: {$u['id']}, Status: {$u['status']})"; }, $raw);
    echo sprintf(
        "Tab: %-12s | Total: %d | Raw Count: %d | Users: [%s]\n",
        $tab,
        $data['total'] ?? 0,
        count($raw),
        implode(', ', $names)
    );
}
