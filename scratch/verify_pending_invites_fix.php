<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "=== 1. VERIFYING getTeamCounts(2) ===\n";
$controller = app(ServiceCompanyTeamController::class);
$counts = $controller->getTeamCounts(2);
echo "Total: " . $counts['total'] . "\n";
echo "Active: " . $counts['active'] . "\n";
echo "Inactive: " . $counts['inactive'] . "\n";
echo "Invited: " . $counts['invited'] . "\n";

assert($counts['invited'] === 0, "Invited count should be 0");
assert($counts['active'] === 4, "Active count should be 4");

echo "=== 2. VERIFYING data_ajax for company 2 ===\n";
// Log in as company 2 admin (user ID 2)
$admin = User::find(2);
Auth::login($admin);

// Request statusTab = 'invited'
$reqInvited = Request::create('/service_company/team/data_ajax', 'GET', [
    'statusTab' => 'invited',
    'size' => 10,
    'per_page' => 10
]);
$resInvited = $controller->data_ajax($reqInvited)->getData(true);
$invitedListCount = count($resInvited['raw_data'] ?? []);
echo "Invited tab returned records count: $invitedListCount\n";
assert($invitedListCount === 0, "Invited list should be 0 records");

// Request statusTab = 'all'
$reqAll = Request::create('/service_company/team/data_ajax', 'GET', [
    'statusTab' => 'all',
    'size' => 10,
    'per_page' => 10
]);
$resAll = $controller->data_ajax($reqAll)->getData(true);
$allListCount = count($resAll['raw_data'] ?? []);
echo "All tab returned records count: $allListCount\n";
assert($allListCount === 4, "All list should be 4 records");

// Verify that for all returned records, isInvited logic evaluates correctly
foreach ($resAll['raw_data'] as $rec) {
    $status = strtolower($rec['status'] ?? '');
    $isInvited = ($status !== 'active') && (empty($rec['password']) || $status === 'invited' || !empty($rec['email_verification_code']));
    echo "Member: {$rec['name']} | Status: $status | isInvited: " . ($isInvited ? 'YES' : 'NO') . "\n";
    assert($isInvited === false, "Active members must not be classified as invited");
}

echo "\n>>> ALL VERIFICATION CHECKS PASSED PERFECTLY! <<<\n";
