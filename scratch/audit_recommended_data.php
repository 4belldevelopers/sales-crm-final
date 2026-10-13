<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;

// Find a service company user and a job
$user = DB::table('users')
    ->join('companies', 'companies.id', '=', 'users.company_id')
    ->where('companies.company_type', 2) // Service company
    ->select('users.id', 'users.company_id', 'users.email')
    ->first();

if (!$user) {
    // Try any active user
    $user = DB::table('users')->where('company_id', '>', 0)->first();
}

echo "Authenticated User ID: {$user->id}, Company ID: {$user->company_id}\n";
$userModel = \App\Models\User::find($user->id);
Auth::login($userModel);

$job = DB::table('jobs')->where('company_id', $user->company_id)->first();
if (!$job) {
    $job = DB::table('jobs')->first();
}
echo "Testing with Job ID: {$job->id}, Title: {$job->title}\n\n";

$controller = app(ServiceCompanyJobsController::class);
$controller->user = $userModel;

// 1. Audit spartnerlisting($job->id)
echo "=== 1. AUDITING spartnerlisting({$job->id}) ===\n";
DB::flushQueryLog();
DB::enableQueryLog();
$start = microtime(true);

$req1 = Request::create("/service_company/jobs/spartnerlisting/{$job->id}", 'GET');
app()->instance('request', $req1);
$res1 = $controller->spartnerlisting($job->id);

$duration1 = (microtime(true) - $start) * 1000;
$queries1 = DB::getQueryLog();
DB::disableQueryLog();

echo "Status Code: " . $res1->getStatusCode() . "\n";
echo "Duration: " . round($duration1, 2) . " ms\n";
echo "MySQL Query Count: " . count($queries1) . "\n";
$totalSqlTime1 = array_sum(array_column($queries1, 'time'));
echo "Total MySQL Time: " . round($totalSqlTime1, 2) . " ms\n";
$content1 = $res1->getContent();
echo "Payload Size: " . strlen($content1) . " bytes\n\n";

// 2. Audit recommendedData for Job
echo "=== 2. AUDITING recommendedData for Job {$job->id} ===\n";
DB::flushQueryLog();
DB::enableQueryLog();
$start = microtime(true);

$req2 = Request::create("/service_company/jobs/recommendedData", 'GET', [
    'page' => 1,
    'jobid' => $job->id,
    'size' => 20
]);
app()->instance('request', $req2);
$res2 = $controller->recommendedData($req2);

$duration2 = (microtime(true) - $start) * 1000;
$queries2 = DB::getQueryLog();
DB::disableQueryLog();

echo "Status Code: " . $res2->getStatusCode() . "\n";
echo "Duration: " . round($duration2, 2) . " ms\n";
echo "MySQL Query Count: " . count($queries2) . "\n";
$totalSqlTime2 = array_sum(array_column($queries2, 'time'));
echo "Total MySQL Time: " . round($totalSqlTime2, 2) . " ms\n";
$content2 = $res2->getContent();
echo "Payload Size: " . strlen($content2) . " bytes\n";
$decoded2 = json_decode($content2, true);
echo "Total Records Reported: " . ($decoded2['total'] ?? 'N/A') . "\n";

// Check headers
echo "ETag: " . $res2->headers->get('ETag') . "\n";
echo "Cache-Control: " . $res2->headers->get('Cache-Control') . "\n";

// 3. Test conditional request (ETag 304)
echo "\n=== 3. AUDITING conditional request with ETag (expecting 304) ===\n";
DB::flushQueryLog();
DB::enableQueryLog();
$start = microtime(true);

$req3 = Request::create("/service_company/jobs/recommendedData", 'GET', [
    'page' => 1,
    'jobid' => $job->id,
    'size' => 20
]);
$req3->headers->set('If-None-Match', $res2->headers->get('ETag'));
app()->instance('request', $req3);
$res3 = $controller->recommendedData($req3);

$duration3 = (microtime(true) - $start) * 1000;
$queries3 = DB::getQueryLog();
DB::disableQueryLog();

echo "Status Code: " . $res3->getStatusCode() . "\n";
echo "Duration: " . round($duration3, 2) . " ms\n";
echo "MySQL Query Count: " . count($queries3) . "\n";
$totalSqlTime3 = array_sum(array_column($queries3, 'time'));
echo "Total MySQL Time: " . round($totalSqlTime3, 2) . " ms\n";
echo "Body Content Length: " . strlen($res3->getContent()) . " bytes\n";
foreach ($queries3 as $idx => $q) {
    echo "  [" . ($idx + 1) . "] (" . $q['time'] . "ms) " . substr($q['query'], 0, 100) . "...\n";
}
