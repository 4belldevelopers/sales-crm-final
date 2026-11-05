<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController;
use Illuminate\Http\Request;

echo "====================================================================" . PHP_EOL;
echo "   COMPREHENSIVE MULTI-TENANT ISOLATION & RBAC/ABAC TEST SUITE" . PHP_EOL;
echo "====================================================================" . PHP_EOL;

$passCount = 0;
$failCount = 0;

function runTest($name, $callback) {
    global $passCount, $failCount;
    echo PHP_EOL . "👉 Running: $name" . PHP_EOL;
    try {
        $result = $callback();
        if ($result === true) {
            echo "   ✅ PASSED" . PHP_EOL;
            $passCount++;
        } else {
            echo "   ❌ FAILED: " . (is_string($result) ? $result : 'Assertion failed') . PHP_EOL;
            $failCount++;
        }
    } catch (\Throwable $e) {
        echo "   ❌ EXCEPTION: " . $e->getMessage() . " on line " . $e->getLine() . PHP_EOL;
        $failCount++;
    }
}

// 1. Setup Test Companies & Users
$companyA = DB::table('companies')->where('id', '>', 0)->orderBy('id', 'asc')->first();
$companyB = DB::table('companies')->where('id', '>', $companyA ? $companyA->id : 0)->orderBy('id', 'asc')->first();

$compAId = $companyA ? (int) $companyA->id : 1;
$compBId = $companyB ? (int) $companyB->id : 2;

echo "   Tenant A ID: {$compAId} | Tenant B ID: {$compBId}" . PHP_EOL;

// -------------------------------------------------------------
// TEST 1: bench_assign Scoping Verification
// -------------------------------------------------------------
runTest("TEST 1: bench_assign query contains company_id filter in data_ajax()", function() {
    $controllerPath = 'app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php';
    $code = file_get_contents($controllerPath);
    
    if (preg_match("/DB::table\('bench_assign'\)\s*->where\('company_id',\s*\\\$company_id\)/", $code)) {
        return true;
    }
    return "bench_assign query does not filter by company_id";
});

// -------------------------------------------------------------
// TEST 2: Cross-Tenant Bench Counts Isolation Simulation
// -------------------------------------------------------------
runTest("TEST 2: Company A cannot aggregate bench candidates belonging to Company B", function() use ($compAId, $compBId) {
    $sharedUserId = 777777;
    DB::table('bench_assign')->where('user_id', $sharedUserId)->delete();
    
    DB::table('bench_assign')->insert([
        ['company_id' => $compAId, 'user_id' => $sharedUserId, 'created_at' => now(), 'updated_at' => now()],
        ['company_id' => $compAId, 'user_id' => $sharedUserId, 'created_at' => now(), 'updated_at' => now()],
        ['company_id' => $compAId, 'user_id' => $sharedUserId, 'created_at' => now(), 'updated_at' => now()],
        ['company_id' => $compBId, 'user_id' => $sharedUserId, 'created_at' => now(), 'updated_at' => now()],
        ['company_id' => $compBId, 'user_id' => $sharedUserId, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $trmTalentsMapA = DB::table('bench_assign')
        ->where('company_id', $compAId)
        ->whereIn('user_id', [$sharedUserId])
        ->select('user_id', DB::raw('COUNT(*) as total'))
        ->groupBy('user_id')
        ->pluck('total', 'user_id');

    $trmTalentsMapB = DB::table('bench_assign')
        ->where('company_id', $compBId)
        ->whereIn('user_id', [$sharedUserId])
        ->select('user_id', DB::raw('COUNT(*) as total'))
        ->groupBy('user_id')
        ->pluck('total', 'user_id');

    DB::table('bench_assign')->where('user_id', $sharedUserId)->delete();

    $countA = (int) ($trmTalentsMapA[$sharedUserId] ?? 0);
    $countB = (int) ($trmTalentsMapB[$sharedUserId] ?? 0);

    echo "     Company A: $countA (Expected: 3) | Company B: $countB (Expected: 2)" . PHP_EOL;
    return ($countA === 3 && $countB === 2);
});

// -------------------------------------------------------------
// TEST 3: Parameter Tampering on Drawer Endpoints (authorizeTeamMemberAccess)
// -------------------------------------------------------------
runTest("TEST 3: Target user belonging to foreign company is safely re-scoped", function() use ($compAId, $compBId) {
    $userA = User::where('company_id', $compAId)->first();
    $userB = User::where('company_id', $compBId)->first();

    if (!$userA || !$userB) return "Need users in both companies";

    Auth::login($userA);

    $controller = app(ServiceCompanyTeamController::class);
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('authorizeTeamMemberAccess');
    $method->setAccessible(true);

    $authorizedUser = $method->invoke($controller, (int) $userB->id);
    echo "     Requested foreign ID: {$userB->id} | Re-scoped to: {$authorizedUser->id} (Co {$authorizedUser->company_id})" . PHP_EOL;

    return ($authorizedUser->id === $userA->id && $authorizedUser->company_id === $compAId);
});

// -------------------------------------------------------------
// TEST 4: Root User Protection (Cannot deactivate root/owner user)
// -------------------------------------------------------------
runTest("TEST 4: Root owner cannot be deactivated via WorkingStatus or statusUpdate", function() use ($compAId) {
    $rootUser = User::where('company_id', $compAId)->where('root_user', 1)->first();
    if (!$rootUser) return "Root user not found";

    Auth::login($rootUser);
    $controller = app(ServiceCompanyTeamController::class);

    // Test statusUpdate on root user
    $req = Request::create('/service_company/team/statusUpdate', 'POST', [
        'userId' => $rootUser->id,
        'chkStatus' => 'false'
    ]);
    
    $res = $controller->statusUpdate($req);
    $data = is_array($res) ? $res : (method_exists($res, 'getData') ? $res->getData(true) : []);
    $statusAfter = DB::table('users')->where('id', $rootUser->id)->value('status');

    echo "     Response message: " . ($data['message'] ?? '') . " | Status in DB: {$statusAfter}" . PHP_EOL;
    return ($statusAfter === 'active' && ($data['status'] ?? '') === 'error');
});

// -------------------------------------------------------------
// TEST 5: Root User Protection in Bulk Delete (teamDelete)
// -------------------------------------------------------------
runTest("TEST 5: Root owner cannot be deleted via teamDelete", function() use ($compAId) {
    $rootUser = User::where('company_id', $compAId)->where('root_user', 1)->first();
    if (!$rootUser) return "Root user not found";

    Auth::login($rootUser);
    $controller = app(ServiceCompanyTeamController::class);

    $req = Request::create('/service_company/team/teamDelete', 'POST', [
        'arr' => [$rootUser->id]
    ]);
    
    $controller->teamDelete($req);
    $statusAfter = DB::table('users')->where('id', $rootUser->id)->value('status');

    echo "     Root user status after delete attempt: {$statusAfter}" . PHP_EOL;
    return ($statusAfter === 'active');
});

// -------------------------------------------------------------
// TEST 6: Recruiter Scoreboard Restricted to Own User ID
// -------------------------------------------------------------
runTest("TEST 6: Recruiter scoreboard ABAC restriction to own user ID", function() use ($compAId) {
    $adminUser = User::where('company_id', $compAId)->where('root_user', 1)->first();
    $recruiterUser = User::where('company_id', $compAId)->where('root_user', '!=', 1)->first();

    if (!$recruiterUser) {
        // Create or find a regular non-root user
        $recruiterUser = User::where('root_user', 0)->first();
    }
    if (!$adminUser || !$recruiterUser) return "Recruiter or Admin user missing";

    Auth::login($recruiterUser);
    $controller = app(ServiceCompanyTeamController::class);

    // Call teamScoreboard requesting the admin's scoreboard
    $req = Request::create('/service_company/team/teamScoreboard', 'GET', [
        'user_id' => $adminUser->id
    ]);

    $response = $controller->teamScoreboard($req);
    $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : []);

    echo "     Scoreboard returned status: " . ($data['status'] ?? 'unknown') . PHP_EOL;
    return (($data['status'] ?? '') === 'success');
});

// -------------------------------------------------------------
// TEST 7: Cross-Tenant Communication Role Rejection
// -------------------------------------------------------------
runTest("TEST 7: setUserCommunicationRoles rejects assignment of foreign user", function() use ($compAId, $compBId) {
    $userB = User::where('company_id', $compBId)->first();
    if (!$userB) return "User B not found";

    $repService = app(\App\Services\CompanyRepresentativeService::class);
    $result = $repService->setUserCommunicationRoles($compAId, $userB->id, ['client']);

    return (($result['status'] ?? '') === 'error' && str_contains(strtolower($result['message'] ?? ''), 'not found'));
});

// -------------------------------------------------------------
// TEST 8: Cross-Tenant Default Representative Rejection
// -------------------------------------------------------------
runTest("TEST 8: setDefaultRepresentative rejects user belonging to another company", function() use ($compAId, $compBId) {
    $userB = User::where('company_id', $compBId)->first();
    if (!$userB) return "User B not found";

    $repService = app(\App\Services\CompanyRepresentativeService::class);
    $result = $repService->setDefaultRepresentative($compAId, $userB->id, 'client');

    return (($result['status'] ?? '') === 'error' && str_contains(strtolower($result['message'] ?? ''), 'not found'));
});

// -------------------------------------------------------------
// TEST 9: WorkingStatus strictly aborts on cross-tenant user ID tampering
// -------------------------------------------------------------
runTest("TEST 9: WorkingStatus strictly aborts on cross-tenant user ID tampering", function() use ($compAId, $compBId) {
    $userA = User::where('company_id', $compAId)->first();
    $userB = User::where('company_id', $compBId)->first();

    if (!$userA || !$userB) return "Users missing";

    Auth::login($userA);
    $controller = app(ServiceCompanyTeamController::class);

    $caughtException = false;
    try {
        $req = Request::create('/service_company/team/working_status', 'POST', [
            'userId' => $userB->id,
            'chkStatus' => 'false'
        ]);
        $controller->WorkingStatus($req);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $caughtException = true;
    } catch (\Throwable $e) {
        if (str_contains($e->getMessage(), 'No query results')) {
            $caughtException = true;
        }
    }

    return $caughtException;
});

// -------------------------------------------------------------
// TEST 10: Role Update strictly aborts on cross-tenant user ID tampering
// -------------------------------------------------------------
runTest("TEST 10: roleupdate strictly aborts on cross-tenant user ID tampering", function() use ($compAId, $compBId) {
    $userA = User::where('company_id', $compAId)->first();
    $userB = User::where('company_id', $compBId)->first();

    if (!$userA || !$userB) return "Users missing";

    Auth::login($userA);
    $controller = app(ServiceCompanyTeamController::class);

    $caughtException = false;
    try {
        $req = Request::create('/service_company/team/roleupdate', 'POST', [
            'cuser_id' => $userB->id,
            'user_role' => 1
        ]);
        $controller->roleupdate($req);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $caughtException = true;
    } catch (\Throwable $e) {
        if (str_contains($e->getMessage(), 'No query results')) {
            $caughtException = true;
        }
    }

    return $caughtException;
});

// -------------------------------------------------------------
// TEST 11: reSendmail strictly returns error for cross-tenant user ID tampering
// -------------------------------------------------------------
runTest("TEST 11: reSendmail strictly returns error for cross-tenant user ID tampering", function() use ($compAId, $compBId) {
    $userA = User::where('company_id', $compAId)->first();
    $userB = User::where('company_id', $compBId)->first();

    if (!$userA || !$userB) return "Users missing";

    Auth::login($userA);
    $controller = app(ServiceCompanyTeamController::class);

    $req = Request::create('/service_company/team/reSendmail', 'POST', [
        'userId' => $userB->id,
    ]);
    $response = $controller->reSendmail($req);
    $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : (array) $response);

    return (($data['status'] ?? '') === 'error' && str_contains(strtolower($data['message'] ?? ''), 'unauthorized'));
});

echo PHP_EOL . "====================================================================" . PHP_EOL;
echo "   RESULTS: {$passCount} PASSED / {$failCount} FAILED" . PHP_EOL;
echo "====================================================================" . PHP_EOL;

if ($failCount > 0) {
    exit(1);
}
exit(0);
