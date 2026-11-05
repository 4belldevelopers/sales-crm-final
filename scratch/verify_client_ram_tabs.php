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
app()->instance('request', $request);

$response = $controller->data_ajax($request);
$data = $response->getData(true);
$fullDataset = $data['raw_data'] ?? [];

echo "=== TESTING CLIENT-SIDE RAM FILTERING ON FULL CANONICAL DATASET ===\n";

$tabs = ['all', 'recruitment', 'client', 'vendor', 'inactive', 'invited', 'active'];

foreach ($tabs as $statusTab) {
    $filtered = array_filter($fullDataset, function ($r) use ($statusTab) {
        $channels = is_array($r['channels'] ?? null) ? $r['channels'] : [];
        $status = strtolower($r['status'] ?? '');
        $isInvited = empty($r['password']) || $status === 'invited';

        if ($statusTab === 'client') {
            return in_array('client', $channels, true) || !empty($r['is_client_rep']);
        } elseif ($statusTab === 'vendor' || $statusTab === 'agency') {
            return in_array('agency', $channels, true) || in_array('vendor', $channels, true) || !empty($r['is_agency_rep']) || (!empty($r['pre_agency_shares']));
        } elseif ($statusTab === 'recruitment' || $statusTab === 'bench') {
            return in_array('bench', $channels, true) || in_array('recruitment', $channels, true) || ((int)($r['root_user'] ?? 0) !== 1 && $status === 'active' && !$isInvited) || ((int)($r['root_user'] ?? 0) === 1 && empty($channels));
        } elseif ($statusTab === 'inactive') {
            return $status === 'inactive' && !$isInvited;
        } elseif ($statusTab === 'invited') {
            return $isInvited;
        } elseif ($statusTab === 'active') {
            return $status === 'active' && !$isInvited;
        }
        return true;
    });

    $names = array_map(function ($u) { return $u['name']; }, $filtered);
    echo sprintf(
        "Client RAM Tab: %-12s | Count: %d | Users: [%s]\n",
        $statusTab,
        count($filtered),
        implode(', ', $names)
    );
}
