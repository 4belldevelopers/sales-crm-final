<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;
use App\Helper\GeneralMethod;

echo "====================================================================\n";
echo "   REPRODUCTION OF REPORTED & DISCOVERED ADD AGENCY DRAWER GAPS\n";
echo "====================================================================\n\n";

$user = App\Models\User::first();
Auth::login($user);
$controller = app(ServiceCompanyJobsController::class);
$controller->user = $user;

// GAP 1: staffingPartnersRegister cache key missing jobid!
echo "[GAP 1] Testing staffingPartnersRegister cache key isolation across jobs:\n";
$reqJob1 = Request::create('/service_company/jobs/staffingPartnersRegister', 'GET', [
    'page' => 1,
    'jobid' => 1,
    'cus_search' => ''
]);
app()->instance('request', $reqJob1);
$resJob1 = $controller->staffingPartnersRegister($reqJob1);
$dataJob1 = json_decode($resJob1->getContent(), true);

$reqJob2 = Request::create('/service_company/jobs/staffingPartnersRegister', 'GET', [
    'page' => 1,
    'jobid' => 8,
    'cus_search' => ''
]);
app()->instance('request', $reqJob2);
$resJob2 = $controller->staffingPartnersRegister($reqJob2);
$dataJob2 = json_decode($resJob2->getContent(), true);

// Check if Job 2 returned Job 1's cached HTML:
preg_match('/data-row-jobid="([^"]*)"/', $dataJob2['html'] ?? '', $mJob);
$returnedJobId = $mJob[1] ?? 'NONE';
echo "  Requested Job ID: 8\n";
echo "  Returned HTML contained Job ID in buttons: " . $returnedJobId . "\n";
if ($returnedJobId == '1') {
    echo "  >> CONFIRMED CRITICAL BUG: staffingPartnersRegister served Job 1 HTML for Job 2 request!\n";
} else {
    echo "  >> Job ID matched: " . $returnedJobId . "\n";
}

// GAP 2: recommendedData cache key ignores salary and experience filters
echo "\n[GAP 2] Testing recommendedData cache key sensitivity to salary/experience filters:\n";
$reqFilterA = Request::create('/service_company/jobs/recommendedData', 'GET', [
    'page' => 1,
    'jobid' => 1,
    'min_sal' => 1000,
    'max_sal' => 5000,
    'min_exp' => 2,
    'max_exp' => 5,
]);
app()->instance('request', $reqFilterA);
$resFilterA = $controller->recommendedData($reqFilterA);
$etagA = $resFilterA->headers->get('ETag');

$reqFilterB = Request::create('/service_company/jobs/recommendedData', 'GET', [
    'page' => 1,
    'jobid' => 1,
    'min_sal' => 999999,
    'max_sal' => 9999999,
    'min_exp' => 15,
    'max_exp' => 20,
]);
app()->instance('request', $reqFilterB);
$resFilterB = $controller->recommendedData($reqFilterB);
$etagB = $resFilterB->headers->get('ETag');

echo "  ETag with normal salary (1000-5000): " . $etagA . "\n";
echo "  ETag with extreme salary (999999+):  " . $etagB . "\n";
if ($etagA === $etagB) {
    echo "  >> CONFIRMED BUG: Different salary/experience filters generated identical ETag/cache keys!\n";
} else {
    echo "  >> ETag correctly differs.\n";
}

// GAP 3: resend_job_recommanded_request lacks atomic lock and transaction
echo "\n[GAP 3] Testing resend_job_recommanded_request concurrency and locking:\n";
$reflector = new ReflectionClass(ServiceCompanyJobsController::class);
$method = $reflector->getMethod('resend_job_recommanded_request');
$source = file_get_contents($reflector->getFileName());
$startLine = $method->getStartLine();
$endLine = $method->getEndLine();
$lines = array_slice(explode("\n", $source), $startLine - 1, $endLine - $startLine + 1);
$methodCode = implode("\n", $lines);

$hasLock = strpos($methodCode, 'Cache::lock') !== false;
$hasTransaction = strpos($methodCode, 'DB::transaction') !== false || strpos($methodCode, 'beginTransaction') !== false;
echo "  resend_job_recommanded_request has Cache::lock: " . ($hasLock ? 'YES' : 'NO (VULNERABLE)') . "\n";
echo "  resend_job_recommanded_request has DB::transaction: " . ($hasTransaction ? 'YES' : 'NO (VULNERABLE)') . "\n";

// GAP 4: Multi-tenant cache invalidation
echo "\n[GAP 4] Testing tenant-wide version invalidation:\n";
$invalidateMethod = $reflector->getMethod('invalidateAgencyDrawerCaches');
$invLines = array_slice(explode("\n", $source), $invalidateMethod->getStartLine() - 1, $invalidateMethod->getEndLine() - $invalidateMethod->getStartLine() + 1);
$invCode = implode("\n", $invLines);
$hasCompanyVersion = strpos($invCode, 'company') !== false;
echo "  invalidateAgencyDrawerCaches bumps tenant company version: " . ($hasCompanyVersion ? 'YES' : 'NO (User-only, violates Rule 6)') . "\n";

echo "\n====================================================================\n";
