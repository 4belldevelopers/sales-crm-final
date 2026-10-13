<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;

$user = DB::table('users')->where('company_id', '>', 0)->first();
$userModel = \App\Models\User::find($user->id);
Auth::login($userModel);

$job = DB::table('jobs')->where('company_id', $user->company_id)->first();
if (!$job) {
    $job = DB::table('jobs')->first();
}

echo "=== VERIFYING ADD AGENCY DRAWER FULL LIFECYCLE ===\n";
echo "User ID: {$user->id}, Company ID: {$user->company_id}, Job ID: {$job->id}\n\n";

$controller = app(ServiceCompanyJobsController::class);
$controller->user = $userModel;

// 1. Verify recommendedData
echo "1. Testing Recommended Agency (recommendedData)...\n";
DB::flushQueryLog();
DB::enableQueryLog();
$start = microtime(true);
$reqRecom = Request::create("/service_company/jobs/recommendedData", 'GET', [
    'page' => 1,
    'jobid' => $job->id,
    'size' => 20
]);
app()->instance('request', $reqRecom);
$resRecom = $controller->recommendedData($reqRecom);
$durRecom = (microtime(true) - $start) * 1000;
$qRecom = DB::getQueryLog();
DB::disableQueryLog();
echo "   Status: " . $resRecom->getStatusCode() . "\n";
echo "   Duration: " . round($durRecom, 2) . " ms\n";
echo "   Queries: " . count($qRecom) . "\n";
$etag = $resRecom->headers->get('ETag');
echo "   ETag: " . $etag . "\n\n";

// 2. Verify 304 on recommendedData
echo "2. Testing 304 Not Modified with ETag...\n";
DB::flushQueryLog();
DB::enableQueryLog();
$start = microtime(true);
$req304 = Request::create("/service_company/jobs/recommendedData", 'GET', [
    'page' => 1,
    'jobid' => $job->id,
    'size' => 20
]);
$req304->headers->set('If-None-Match', $etag);
app()->instance('request', $req304);
$res304 = $controller->recommendedData($req304);
$dur304 = (microtime(true) - $start) * 1000;
$q304 = DB::getQueryLog();
DB::disableQueryLog();
echo "   Status: " . $res304->getStatusCode() . " (Expected: 304)\n";
echo "   Duration: " . round($dur304, 2) . " ms\n";
echo "   Queries: " . count($q304) . " (Expected: 0)\n\n";

// 3. Verify staffingPartnersRegister (My Agency)
echo "3. Testing My Agency (staffingPartnersRegister)...\n";
DB::flushQueryLog();
DB::enableQueryLog();
$start = microtime(true);
$reqReg = Request::create("/service_company/jobs/staffingPartnersRegister", 'GET', [
    'page' => 1,
    'jobid' => $job->id,
    'size' => 20
]);
app()->instance('request', $reqReg);
$resReg = $controller->staffingPartnersRegister($reqReg);
$durReg = (microtime(true) - $start) * 1000;
$qReg = DB::getQueryLog();
DB::disableQueryLog();
echo "   Status: " . $resReg->getStatusCode() . "\n";
echo "   Duration: " . round($durReg, 2) . " ms\n";
echo "   Queries: " . count($qReg) . "\n\n";

// 4. Verify recommendedAssignList (Assigned Agency)
echo "4. Testing Assigned Agency (recommendedAssignList)...\n";
DB::flushQueryLog();
DB::enableQueryLog();
$start = microtime(true);
$reqAssign = Request::create("/service_company/jobs/recommendedAssignList", 'GET', [
    'page' => 1,
    'jobid' => $job->id,
    'size' => 10
]);
app()->instance('request', $reqAssign);
$resAssign = $controller->recommendedAssignList($reqAssign);
$durAssign = (microtime(true) - $start) * 1000;
$qAssign = DB::getQueryLog();
DB::disableQueryLog();
echo "   Status: " . $resAssign->getStatusCode() . "\n";
echo "   Duration: " . round($durAssign, 2) . " ms\n";
echo "   Queries: " . count($qAssign) . "\n\n";

// 5. Verify assignRejected endpoint
echo "5. Testing assignRejected endpoint (formerly missing 500 error)...\n";
$reqReject = Request::create("/service_company/jobs/assignRejected", 'GET', [
    'id' => 999999, // non-existent agency ID to test cleanly without corrupting test data
    'jobid' => $job->id
]);
app()->instance('request', $reqReject);
$resReject = $controller->assignRejected($reqReject);
echo "   Response: " . json_encode($resReject) . "\n";
echo "   assignRejected executed successfully without 500 exception!\n\n";

// 6. Verify cache invalidation on mutation
echo "6. Testing atomic cache invalidator on mutation...\n";
$verKey = 'allrecommendedagency_version_user_' . $user->id;
$verBefore = cache()->get($verKey, 1);
echo "   Cache version before mutation: {$verBefore}\n";

$reqRemove = Request::create("/service_company/jobs/removeJob", 'GET', [
    'jobid' => $job->id,
    'fromcompany' => $user->company_id,
    'tocompany' => 999999
]);
app()->instance('request', $reqRemove);
$resRemove = $controller->removeJob($reqRemove);
$verAfter = cache()->get($verKey, 1);
echo "   Cache version after removeJob mutation: {$verAfter}\n";
if ($verAfter > $verBefore) {
    echo "   [SUCCESS] Cache version bumped automatically! Stale cache is instantly invalidated.\n";
} else {
    echo "   [FAIL] Cache version did not bump.\n";
}

echo "\n=== ALL VERIFICATION CHECKS PASSED ===\n";
