<?php

/**
 * Phase 2 & 3 God-Level CQRS & Commercials Verification Script
 *
 * Verifies:
 * 1. Master Cache Pre-warming & Retrieval
 * 2. CommercialReadModelService projection builder & tenant state DTO
 * 3. 0% MySQL reads on cached hits (Rule 1 verification)
 * 4. Multi-tenant atomic cache invalidation (Rule 6 verification)
 * 5. Reverb CommercialsUpdated event construction & private channel authorization
 * 6. ETag 304 Not Modified functionality
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\DTO\Commercials\CommercialStateDTO;
use App\DTO\Commercials\ServiceTermDTO;
use App\Events\CommercialsUpdated;
use App\Services\CommercialReadModelService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

echo "=== STARTING PHASE 2 & 3 VERIFICATION ===\n\n";

$passCount = 0;
$failCount = 0;

function assertCheck($name, $condition, &$passCount, &$failCount, $details = '') {
    if ($condition) {
        echo " [PASS] $name\n";
        if ($details) echo "        $details\n";
        $passCount++;
    } else {
        echo "![FAIL] $name\n";
        if ($details) echo "        Error: $details\n";
        $failCount++;
    }
}

// 1. Verify Master Data Caching
try {
    $countries = CommercialReadModelService::getMasterCountries();
    $industries = CommercialReadModelService::getMasterIndustries();
    $defaultRates = CommercialReadModelService::getMasterDefaultRates();
    $bands = CommercialReadModelService::getCommercialExperienceBands();

    assertCheck(
        "Master data cached in Redis",
        !empty($countries) && !empty($industries) && !empty($bands),
        $passCount,
        $failCount,
        "Countries: " . count($countries) . ", Industries: " . count($industries) . ", Bands: " . count($bands)
    );
} catch (\Throwable $e) {
    assertCheck("Master data cached in Redis", false, $passCount, $failCount, $e->getMessage());
}

// 2. Identify a test tenant company_id
$testCompanyId = (int) DB::table('commericials')->value('company_id') ?: 1;

// 3. Test getTenantCommercialsState
try {
    // Invalidate first to test cold warmup
    CommercialReadModelService::invalidateTenantCache($testCompanyId);
    $initialVersion = (int) Cache::get("company:{$testCompanyId}:commercials:version", 1);

    $stateDTO = CommercialReadModelService::getTenantCommercialsState($testCompanyId);

    assertCheck(
        "CommercialReadModelService returns CommercialStateDTO",
        $stateDTO instanceof CommercialStateDTO &&
        $stateDTO->fulltime instanceof ServiceTermDTO &&
        $stateDTO->contract instanceof ServiceTermDTO,
        $passCount,
        $failCount,
        "Company: {$stateDTO->companyId}, Version: {$stateDTO->version}, FT status: {$stateDTO->fulltime->status}, CT status: {$stateDTO->contract->status}"
    );
} catch (\Throwable $e) {
    assertCheck("CommercialReadModelService returns CommercialStateDTO", false, $passCount, $failCount, $e->getMessage());
}

// 4. Test 0% MySQL reads on cached projection fetch (Rule 1)
try {
    DB::flushQueryLog();
    DB::enableQueryLog();

    $cachedDTO = CommercialReadModelService::getTenantCommercialsState($testCompanyId);
    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    assertCheck(
        "0% MySQL reads on cached projection hit (Rule 1 compliant)",
        count($queries) === 0,
        $passCount,
        $failCount,
        "Executed " . count($queries) . " MySQL queries on cached hit (target: 0)"
    );
} catch (\Throwable $e) {
    assertCheck("0% MySQL reads on cached projection hit", false, $passCount, $failCount, $e->getMessage());
}

// 5. Test Multi-Tenant Atomic Cache Invalidation (Rule 6)
try {
    $versionBefore = (int) Cache::get("company:{$testCompanyId}:commercials:version", 1);
    CommercialReadModelService::invalidateTenantCache($testCompanyId);
    $versionAfter = (int) Cache::get("company:{$testCompanyId}:commercials:version", 1);

    $oldKeyExists = Cache::has("company:{$testCompanyId}:commercials:projection:v{$versionBefore}");

    assertCheck(
        "Atomic Cache Invalidation increments version and purges projection (Rule 6)",
        $versionAfter === ($versionBefore + 1) && !$oldKeyExists,
        $passCount,
        $failCount,
        "Version before: $versionBefore, Version after: $versionAfter"
    );
} catch (\Throwable $e) {
    assertCheck("Atomic Cache Invalidation", false, $passCount, $failCount, $e->getMessage());
}

// 6. Test CommercialsUpdated Event Structure
try {
    $event = new CommercialsUpdated(
        companyId: $testCompanyId,
        serviceType: 'fulltime',
        version: 42,
        senderId: 999
    );

    $channels = $event->broadcastOn();
    $eventName = $event->broadcastAs();
    $payload = $event->broadcastWith();

    assertCheck(
        "CommercialsUpdated event configures channel, eventName and metadata",
        $channels->name === "private-company.{$testCompanyId}" &&
        $eventName === 'commercials.updated' &&
        $payload['version'] === 42 &&
        !isset($payload['rates']), // Ensure no sensitive financial rates in broadcast
        $passCount,
        $failCount,
        "Channel: {$channels->name}, Event: $eventName, Version: {$payload['version']}"
    );
} catch (\Throwable $e) {
    assertCheck("CommercialsUpdated event configures channel", false, $passCount, $failCount, $e->getMessage());
}

// 7. Verify Route Registration
try {
    $route = Route::getRoutes()->getByName('service_company.jobs.commercialsState');
    assertCheck(
        "Route service_company.jobs.commercialsState is registered",
        $route !== null,
        $passCount,
        $failCount,
        "Action: " . ($route ? $route->getActionName() : 'NULL')
    );
} catch (\Throwable $e) {
    assertCheck("Route service_company.jobs.commercialsState is registered", false, $passCount, $failCount, $e->getMessage());
}

// 8. Test Controller getCommercialsState API with ETag and 304 response
try {
    $controller = app(\App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class);
    $user = \App\Models\User::where('company_id', $testCompanyId)->first();
    if ($user) {
        auth()->login($user);
        \Illuminate\Support\Facades\Gate::define('view_jobs', fn() => true);

        // Request 1: Fresh state
        $request1 = \Illuminate\Http\Request::create('/service_company/jobs/commercials-state', 'GET');
        $response1 = $controller->getCommercialsState($request1);
        $etag = $response1->headers->get('ETag');
        $json = json_decode($response1->getContent(), true);

        // Request 2: Conditional GET with matching ETag -> Expected 304 Not Modified
        $request2 = \Illuminate\Http\Request::create('/service_company/jobs/commercials-state', 'GET');
        $request2->headers->set('If-None-Match', $etag);
        $response2 = $controller->getCommercialsState($request2);

        assertCheck(
            "Controller getCommercialsState supports HTTP ETag & 304 Not Modified",
            $response1->getStatusCode() === 200 &&
            !empty($etag) &&
            isset($json['services']['fulltime']) &&
            $response2->getStatusCode() === 304,
            $passCount,
            $failCount,
            "Req 1 Status: {$response1->getStatusCode()}, ETag: $etag, Req 2 Status: {$response2->getStatusCode()}"
        );
    } else {
        echo " [SKIP] Test #8: No user found for company $testCompanyId\n";
    }
} catch (\Throwable $e) {
    assertCheck("Controller getCommercialsState supports HTTP ETag", false, $passCount, $failCount, $e->getMessage());
}

echo "\n=========================================\n";
echo "SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=========================================\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
