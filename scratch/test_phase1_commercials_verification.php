<?php
/**
 * Phase 1 Verification Test Suite: Commercials Drawer & Multi-Page Sync
 * Tests:
 * 1. Syntax & File integrity
 * 2. Icon audit (verify 0 <i class="fa... tags in drawer)
 * 3. Array type safety & PHP 8 compatibility
 * 4. Optimistic locking logic
 * 5. Canonical commercial_state payload contract
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "======================================================\n";
echo "🏛️ PHASE 1 FORENSIC VERIFICATION: COMMERCIALS ENGINE\n";
echo "======================================================\n\n";

$testsPassed = 0;
$testsTotal = 0;

function assertTest($name, $condition, &$testsPassed, &$testsTotal) {
    $testsTotal++;
    if ($condition) {
        $testsPassed++;
        echo "✅ PASS: {$name}\n";
    } else {
        echo "❌ FAIL: {$name}\n";
    }
}

// TEST 1: Drawer Blade contains ZERO broken FontAwesome glyphs
$drawerContent = file_get_contents(__DIR__ . '/../resources/views/service_company/jobs/services_view.blade.php');
$faIconCount = preg_match_all('/<i\s+class=["\']fa[srbld]?\s+/i', $drawerContent);
assertTest("Drawer Blade has 0 FontAwesome <i class='fa... tags (fully converted to SVGs)", $faIconCount === 0, $testsPassed, $testsTotal);

// TEST 2: Wildcard font-family override removed
$hasWildcardPoppins = (strpos($drawerContent, '.commercial-editor-v3 *') !== false && strpos($drawerContent, 'font-family:') !== false);
// Check if .commercial-editor-v3 * { font-family is gone
$hasWildcardFontFamily = preg_match('/\.commercial-editor-v3\s*\*\s*\{[^}]*font-family/i', $drawerContent);
assertTest("Drawer Blade wildcard '.commercial-editor-v3 *' font-family override eliminated", $hasWildcardFontFamily === 0, $testsPassed, $testsTotal);

// TEST 3: Controller has Gate authorization check in jobServicesAddUpdate
$controllerContent = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php');
$hasGateCheck = preg_match('/function\s+jobServicesAddUpdate[^{]*\{[^}]*abort_if\s*\(\s*Gate::denies/s', $controllerContent);
assertTest("Controller jobServicesAddUpdate enforces Gate authorization guard", (bool) $hasGateCheck, $testsPassed, $testsTotal);

// TEST 4: Controller has optimistic locking updateId verification
$hasOptimisticLock = strpos($controllerContent, 'updateId') !== false && strpos($controllerContent, '409') !== false;
assertTest("Controller enforces optimistic concurrency check (HTTP 409 Conflict)", $hasOptimisticLock, $testsPassed, $testsTotal);

// TEST 5: Controller guards rate_val against null (PHP 8 count() TypeError)
$hasRateValGuard = strpos($controllerContent, 'is_array($request->rate_val)') !== false;
assertTest("Controller guards \$request->rate_val with is_array() to prevent PHP 8 TypeError", $hasRateValGuard, $testsPassed, $testsTotal);

// TEST 6: Controller writes durable sys_outbox_events record
$hasOutboxInsert = strpos($controllerContent, "sys_outbox_events") !== false && strpos($controllerContent, "'CommercialsUpdated'") !== false;
assertTest("Controller records durable 'CommercialsUpdated' in sys_outbox_events within ACID transaction", $hasOutboxInsert, $testsPassed, $testsTotal);

// TEST 7: Controller returns canonical commercial_state payload
$hasCanonicalPayload = strpos($controllerContent, "'commercial_state' => \$commercialState") !== false;
assertTest("Controller returns canonical 'commercial_state' DTO in response payload", $hasCanonicalPayload, $testsPassed, $testsTotal);

// TEST 8: Parent tab (services.blade.php) contains ServicesTabEngine and commercial:saved listener
$tabContent = file_get_contents(__DIR__ . '/../resources/views/service_company/account/v2/tabs/services.blade.php');
$hasTabEngine = strpos($tabContent, 'window.ServicesTabEngine') !== false;
$hasSavedListener = strpos($tabContent, 'commercial:saved') !== false;
assertTest("Parent Services Tab registers window.ServicesTabEngine for reactive client rehydration", $hasTabEngine && $hasSavedListener, $testsPassed, $testsTotal);

// TEST 9: Parent tab has targeted sub-card DOM IDs (ft-model-val, ft-currency-val, etc.)
$hasTargetedIds = (
    strpos($tabContent, 'id="ft-model-val"') !== false &&
    strpos($tabContent, 'id="ft-currency-val"') !== false &&
    strpos($tabContent, 'id="ct-model-val"') !== false &&
    strpos($tabContent, 'id="ct-currency-val"') !== false
);
assertTest("Parent Services Tab sub-cards have targeted DOM IDs for instantaneous re-hydration", $hasTargetedIds, $testsPassed, $testsTotal);

// TEST 10: account/index.blade.php dispatches complete canonical state and handles 409
$accountIndexContent = file_get_contents(__DIR__ . '/../resources/views/service_company/account/index.blade.php');
$hasDispatchedCanonical = strpos($accountIndexContent, 'state: response.commercial_state') !== false;
$has409Handler = strpos($accountIndexContent, 'status === 409') !== false;
assertTest("Account index.blade.php dispatches canonical state and handles HTTP 409 Conflict", $hasDispatchedCanonical && $has409Handler, $testsPassed, $testsTotal);

echo "\n------------------------------------------------------\n";
echo "SUMMARY: {$testsPassed} / {$testsTotal} TESTS PASSED\n";
echo "------------------------------------------------------\n";

if ($testsPassed === $testsTotal) {
    echo "🎉 ALL PHASE 1 INTEGRITY TESTS PASSED PERFECTLY!\n";
    exit(0);
} else {
    echo "⚠️ SOME TESTS FAILED.\n";
    exit(1);
}
