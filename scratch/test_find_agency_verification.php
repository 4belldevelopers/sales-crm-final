<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\FindAgencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$user = User::where('company_id', '>', 0)->first();
if (!$user) {
    echo "No user found in MySQL database for testing.\n";
    exit(0);
}
Auth::login($user);

$service = app(FindAgencyService::class);

echo "=== FIND AGENCY V2 FORENSIC VERIFICATION ===\n";
echo "Testing Tenant ID: {$user->company_id} | User ID: {$user->id}\n\n";

// 1. Initial Request (Populates Cache & Generates ETag)
$t0 = microtime(true);
$request1 = Request::create('/service_company/find-agency-data', 'GET', [
    'page' => 1,
    'per_page' => 10,
    'force_refresh' => 1
]);
$request1->setUserResolver(fn() => $user);
$response1 = $service->getAgencyData($request1, $user);
$t1 = microtime(true);

$etag = $response1->headers->get('ETag');
$status1 = $response1->getStatusCode();
$bodyLen1 = strlen($response1->getContent());
$dur1 = round(($t1 - $t0) * 1000, 2);

echo "1. Fresh Query (force_refresh=1):\n";
echo "   Status: {$status1}\n";
echo "   ETag:   {$etag}\n";
echo "   Size:   {$bodyLen1} bytes\n";
echo "   Time:   {$dur1} ms\n\n";

// 2. Warm Request (Served from Redis Cache)
$t2 = microtime(true);
$request2 = Request::create('/service_company/find-agency-data', 'GET', [
    'page' => 1,
    'per_page' => 10,
]);
$request2->setUserResolver(fn() => $user);
$response2 = $service->getAgencyData($request2, $user);
$t3 = microtime(true);

$status2 = $response2->getStatusCode();
$dur2 = round(($t3 - $t2) * 1000, 2);

echo "2. Warm Query (Served from Redis):\n";
echo "   Status: {$status2}\n";
echo "   Time:   {$dur2} ms\n\n";

// 3. Conditional HTTP 304 Revalidation (Browser sends If-None-Match + X-Has-Representation: 1)
$t4 = microtime(true);
$request3 = Request::create('/service_company/find-agency-data', 'GET', [
    'page' => 1,
    'per_page' => 10,
]);
$request3->headers->set('If-None-Match', $etag);
$request3->headers->set('X-Has-Representation', '1');
$request3->setUserResolver(fn() => $user);
$response3 = $service->getAgencyData($request3, $user);
$t5 = microtime(true);

$status3 = $response3->getStatusCode();
$bodyLen3 = strlen($response3->getContent());
$dur3 = round(($t5 - $t4) * 1000, 2);

echo "3. Conditional HTTP 304 Revalidation:\n";
echo "   Status: {$status3} (Expected: 304)\n";
echo "   Body:   {$bodyLen3} bytes (Payload eliminated)\n";
echo "   Time:   {$dur3} ms\n\n";

if ($status3 === 304 && $bodyLen3 === 0) {
    echo ">>> VERIFICATION PASSED: HTTP 304 conditional caching working perfectly with 0-byte payload! <<<\n";
} else {
    echo ">>> VERIFICATION FAILED: Status {$status3}, expected 304. <<<\n";
}
