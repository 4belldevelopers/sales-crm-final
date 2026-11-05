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
$records = $data['raw_data'] ?? [];

echo "Records count from data_ajax: " . count($records) . "\n";
foreach ($records as $r) {
    echo "- ID: {$r['id']}, Name: {$r['name']}, Status: {$r['status']}, Password len: " . strlen($r['password'] ?? '') . ", email_verification_code: " . ($r['email_verification_code'] ?? 'null') . "\n";
}

$tabs = ['all', 'recruitment', 'client', 'vendor', 'inactive', 'invited', 'active'];

foreach ($tabs as $tabName) {
    $matched = array_filter($records, function ($r) use ($tabName) {
        $channels = is_array($r['channels'] ?? null) ? $r['channels'] : [];
        $status = strtolower($r['status'] ?? '');
        $isInvited = empty($r['password']) || $status === 'invited';

        if ($tabName === 'all') {
            return true;
        } elseif ($tabName === 'client') {
            return in_array('client', $channels, true) || !empty($r['is_client_rep']);
        } elseif ($tabName === 'vendor' || $tabName === 'agency') {
            return in_array('agency', $channels, true) || in_array('vendor', $channels, true) || !empty($r['is_agency_rep']) || (!empty($r['pre_agency_shares']));
        } elseif ($tabName === 'recruitment' || $tabName === 'bench') {
            return in_array('bench', $channels, true) || in_array('recruitment', $channels, true) || ((int)($r['root_user'] ?? 0) !== 1 && $status === 'active' && !$isInvited);
        } elseif ($tabName === 'inactive') {
            return $status === 'inactive';
        } elseif ($tabName === 'invited') {
            return $isInvited;
        } elseif ($tabName === 'active') {
            return $status === 'active' && !$isInvited;
        }
        return true;
    });

    $names = array_map(function ($u) { return $u['name']; }, $matched);
    echo "Tab [{$tabName}] matched: " . count($matched) . " users -> [" . implode(', ', $names) . "]\n";
}
