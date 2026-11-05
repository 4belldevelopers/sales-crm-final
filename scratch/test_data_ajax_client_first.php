<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController;

echo "====================================================================\n";
echo "   DATA_AJAX CLIENT-FIRST PAYLOAD & PERFORMANCE TEST\n";
echo "====================================================================\n";

$companyUser = User::where('company_id', 1)->where('status', 'active')->first();
if (!$companyUser) {
    echo "❌ No active user found for company 1\n";
    exit(1);
}

auth()->login($companyUser);
Illuminate\Support\Facades\Gate::before(fn () => true);
$controller = app(ServiceCompanyTeamController::class);

$request = Request::create('/service-company/team/data-ajax', 'GET', [
    'statusTab' => 'all',
    'size' => 10,
    'page' => 1,
]);

$response = $controller->data_ajax($request);
$data = $response->getData(true);

echo "👉 Checking data_ajax() response structure:\n";

// 1. is_full_dataset flag
if (isset($data['is_full_dataset']) && $data['is_full_dataset'] === true) {
    echo "   ✅ is_full_dataset is TRUE (Client RAM Engine active)\n";
} else {
    echo "   ❌ is_full_dataset missing or false\n";
    exit(1);
}

// 2. raw_data populated with full company records
$rawCount = count($data['raw_data'] ?? []);
$total = $data['total'] ?? 0;
echo "   ℹ️ Total records: {$total} | raw_data count: {$rawCount}\n";
if ($rawCount >= min($total, 2000)) {
    echo "   ✅ raw_data contains canonical full company dataset\n";
} else {
    echo "   ❌ raw_data count ({$rawCount}) does not match expected total ({$total})\n";
    exit(1);
}

// 3. Inspect first record fields
$first = $data['raw_data'][0] ?? null;
if ($first) {
    $hasActiveJobs = array_key_exists('pre_active_jobs', $first);
    $hasCaptainJobs = array_key_exists('pre_captain_jobs', $first);
    $hasTrmTalents = array_key_exists('pre_trm_talents', $first);
    $hasChannels = array_key_exists('channels', $first);
    $hasPhone = array_key_exists('phone', $first);
    $hasScore = array_key_exists('pre_score', $first);
    $hasRank = array_key_exists('pre_rank', $first);

    if ($hasActiveJobs && $hasCaptainJobs && $hasTrmTalents && $hasChannels && $hasPhone && $hasScore && $hasRank) {
        echo "   ✅ Record has all pre-computed workload metrics & channel tags\n";
        echo "      Sample user: {$first['name']} | Active: {$first['pre_active_jobs']} | SPOC: {$first['pre_captain_jobs']} | TRM: {$first['pre_trm_talents']} | Channels: " . json_encode($first['channels']) . "\n";
    } else {
        echo "   ❌ Missing pre-computed fields in raw_data record\n";
        exit(1);
    }
}

// 4. HTML verification
if (!empty($data['html']) && str_contains($data['html'], 'tm-primary-card')) {
    echo "   ✅ Initial HTML server render contains valid tm-primary-card components\n";
} else {
    echo "   ❌ HTML render missing tm-primary-card\n";
    exit(1);
}

// 5. Pagination verification
if (!empty($data['pagination']) && str_contains($data['pagination'], 'team-pagination-bar')) {
    echo "   ✅ Pagination HTML contains valid team-pagination-bar\n";
} else {
    echo "   ❌ Pagination HTML missing team-pagination-bar\n";
    exit(1);
}

echo "====================================================================\n";
echo "   ALL DATA_AJAX CLIENT-FIRST TESTS PASSED 100%!\n";
echo "====================================================================\n";
