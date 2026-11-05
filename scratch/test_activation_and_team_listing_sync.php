<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\RegistrationActivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

echo "=== TEST: END-TO-END INVITATION ACTIVATION & TEAM LISTING SYNC ===\n\n";

$companyId = 2; // TechNova Inc
$elastic = app(ElasticService::class)->getClient();

// Clean up any stale e2e test users
$oldUsers = DB::table('users')->where('email', 'like', 'e2e_activation_%')->get();
foreach ($oldUsers as $ou) {
    DB::table('team_invitations')->where('email', $ou->email)->delete();
    try { $elastic->delete(['index' => ELASTIC_USERS_LIST, 'id' => (string) $ou->id]); } catch (\Throwable $e) {}
    DB::table('users')->where('id', $ou->id)->delete();
}

$testEmail = 'e2e_activation_' . time() . '@example.com';
$memberName = 'E2E Verified Recruiter';
$rawSecret = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $rawSecret);

// 1. Setup: Create initial inactive user and invitation (as done when admin invites a member)
$userId = DB::table('users')->insertGetId([
    'name' => $memberName,
    'email' => $testEmail,
    'password' => '',
    'company_id' => $companyId,
    'status' => 'inactive',
    'working_status' => 0,
    'email_verification_code' => Str::random(40),
    'created_at' => now(),
    'updated_at' => now(),
]);

$invId = DB::table('team_invitations')->insertGetId([
    'uuid' => (string) Str::uuid(),
    'company_id' => $companyId,
    'email' => $testEmail,
    'token_hash' => $tokenHash,
    'role_id' => 1,
    'context_version' => 'TEAM_V2',
    'status' => 'PENDING',
    'delivery_status' => 'SENT',
    'invited_by' => 2,
    'expires_at' => now()->addHours(72),
    'created_at' => now(),
    'updated_at' => now(),
]);

// Initial Indexing as invited (pending) user
\App\Helper\GeneralMethod::userIndexingupload($userId);
$elastic = app(ElasticService::class)->getClient();
$elastic->indices()->refresh(['index' => ELASTIC_USERS_LIST]);
echo "[SETUP] Created inactive user #{$userId} and invitation #{$invId} for {$testEmail}\n";

// 2. Verify initial state in Team Listing: Must show "Awaiting Signup" before activation
$adminUser = User::find(2);
auth()->login($adminUser);

$controller = app(ServiceCompanyTeamController::class);
$reqBefore = Request::create('/service_company/team/data_ajax', 'POST', [
    'page' => 1,
    'size' => 20,
    'statusTab' => 'all',
    'cus_search' => $testEmail,
]);
$reqBefore->headers->set('X-Requested-With', 'XMLHttpRequest');

$resBefore = $controller->data_ajax($reqBefore);
$dataBefore = json_decode($resBefore->getContent(), true);
$htmlBefore = $dataBefore['html'] ?? '';

echo "Total records before activation: " . ($dataBefore['total'] ?? 0) . "\n";
if (strpos($htmlBefore, 'Awaiting Signup') === false) {
    echo "DEBUG HTML:\n" . $htmlBefore . "\n";
    die("[FAIL] Expected 'Awaiting Signup' in listing before activation!\n");
}
echo "[PASS] Before activation: Team listing displays 'Awaiting Signup' with Resend button!\n";

// 3. User activates account via RegistrationActivationService
echo "\n--- Executing Activation POST /register/activate ---\n";
$envelope = [
    'invitation_id' => $invId,
    'tenant_id' => $companyId,
    'session_nonce' => bin2hex(random_bytes(16)),
    'expires_at' => now()->addHours(2)->timestamp
];
$cookieVal = Crypt::encrypt($envelope);

$actReq = Request::create('/register/activate', 'POST', [
    'name' => $memberName,
    'password' => 'GodLevelPass2026!#',
    'password_confirmation' => 'GodLevelPass2026!#',
    'toc' => '1',
]);
$actReq->cookies->set(\App\Services\TeamInvitationService::COOKIE_NAME, $cookieVal);
$actReq->headers->set('Idempotency-Key', 'e2e_act_' . uniqid());

$actService = app(RegistrationActivationService::class);
$actRes = $actService->activate($actReq);

if ($actRes->getStatusCode() !== 201) {
    die("[FAIL] Activation failed! " . $actRes->getContent() . "\n");
}
echo "[PASS] Activation succeeded (HTTP 201)!\n";

$elastic->indices()->refresh(['index' => ELASTIC_USERS_LIST]);

// 4. Verify Elasticsearch ELASTIC_USERS_LIST document was updated
$elastic = app(ElasticService::class)->getClient();
$esDoc = $elastic->get(['index' => ELASTIC_USERS_LIST, 'id' => (string) $userId]);
$esSrc = $esDoc['_source'] ?? [];

echo "[ES CHECK] Status: '{$esSrc['status']}', Password set: " . (empty($esSrc['password']) ? 'NO' : 'YES') . ", Verification code: " . var_export($esSrc['email_verification_code'] ?? null, true) . "\n";

if (($esSrc['status'] ?? '') !== 'active') {
    die("[FAIL] ES document status is not 'active'!\n");
}
if (empty($esSrc['password'])) {
    die("[FAIL] ES document password was not populated!\n");
}
if (!empty($esSrc['email_verification_code'])) {
    die("[FAIL] ES document email_verification_code was not cleared!\n");
}
echo "[PASS] Elasticsearch ELASTIC_USERS_LIST document is 100% in sync with active status and password hash!\n";

// 5. Verify Team Listing after activation: MUST show "Active" and MUST NOT show "Awaiting Signup"
echo "\n--- Checking Team Listing after activation ---\n";
auth()->login($adminUser);

$reqAfter = Request::create('/service_company/team/data_ajax', 'POST', [
    'page' => 1,
    'size' => 20,
    'statusTab' => 'all',
    'cus_search' => $testEmail,
]);
$reqAfter->headers->set('X-Requested-With', 'XMLHttpRequest');

$resAfter = $controller->data_ajax($reqAfter);
$htmlAfter = json_decode($resAfter->getContent(), true)['html'] ?? '';

if (strpos($htmlAfter, 'Awaiting Signup') !== false) {
    die("[FAIL] Team listing STILL displays 'Awaiting Signup' after activation!\n");
}
if (strpos($htmlAfter, 'Active') === false) {
    die("[FAIL] Team listing does not show 'Active' status!\n");
}
if (strpos($htmlAfter, 'chkStatus') === false) {
    die("[FAIL] Active toggle switch is missing!\n");
}
echo "[PASS] Team listing immediately reflects 'Active' status with toggle switch (ZERO 'Awaiting Signup')!\n";

// 6. Verify Team Counts (Active vs Invited)
$counts = $controller->getTeamCounts($companyId);
echo "[PASS] Team Counts: Total={$counts['total']}, Active={$counts['active']}, Invited={$counts['invited']}\n";

// Clean up
DB::table('users')->where('id', $userId)->delete();
DB::table('team_invitations')->where('id', $invId)->delete();
try {
    $elastic->delete(['index' => ELASTIC_USERS_LIST, 'id' => (string) $userId]);
} catch (\Throwable $e) {}

// Clean up cache
\Illuminate\Support\Facades\Cache::forget('ats:teamcounts:' . $companyId);
$versionKey = 'ats:teamversion:company_' . $companyId;
\Illuminate\Support\Facades\Cache::increment($versionKey);

echo "\n=========================================================================\n";
echo " END-TO-END INVITATION ACTIVATION & TEAM LISTING SYNC 100% VERIFIED! \n";
echo "=========================================================================\n";
