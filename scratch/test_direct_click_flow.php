<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Auth\RegisterController;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

echo "=== TEST: ENTERPRISE TEAM MEMBER ACTIVATION CARD & SUBMIT FLOW ===\n\n";

$testEmail = 'audit_team_member_' . time() . '@example.com';
$memberName = 'Audit Test Specialist';
$rawSecret = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $rawSecret);

// Create an inactive user (as created by ServiceCompanyTeamController::store)
$userId = DB::table('users')->insertGetId([
    'name' => $memberName,
    'email' => $testEmail,
    'password' => bcrypt('TempDummyPass!23'),
    'company_id' => 1,
    'status' => 'inactive',
    'working_status' => 0,
    'created_at' => now(),
    'updated_at' => now(),
]);

// Create test invitation
$invId = DB::table('team_invitations')->insertGetId([
    'uuid' => (string) Str::uuid(),
    'company_id' => 1,
    'email' => $testEmail,
    'token_hash' => $tokenHash,
    'role_id' => 1,
    'context_version' => 'TEAM_V2',
    'status' => 'PENDING',
    'delivery_status' => 'SENT',
    'invited_by' => 1,
    'expires_at' => now()->addHours(72),
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "[SETUP] Created inactive user #{$userId} and invitation #{$invId} for {$testEmail} (Name: {$memberName})\n";

// TEST 1: User clicks link in email: GET /register?token=<rawSecret>
echo "\n--- TEST 1: Link click GET /register?token=... ---\n";
$req1 = Request::create('/register?token=' . $rawSecret, 'GET');
$req1->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$controller = app(RegisterController::class);
$res1 = $controller->showRegistrationForm($req1);

if ($res1->getStatusCode() !== 200) {
    die("[FAIL] Expected HTTP 200 directly, got: " . $res1->getStatusCode() . "\n");
}
echo "[PASS] Single HTTP 200 response returned directly!\n";

$html1 = $res1->getContent();

// Check 1: Dedicated Enterprise Card Class & Form
if (strpos($html1, 'id="kt_team_activate_form"') === false) {
    die("[FAIL] #kt_team_activate_form is missing from HTML!\n");
}
if (strpos($html1, 'jw-card-team-invite') === false) {
    die("[FAIL] .jw-card-team-invite class missing from HTML!\n");
}
echo "[PASS] Form has .jw-card-team-invite class!\n";

// Check 2: Distinct background and top accent styling
if (strpos($html1, 'linear-gradient(180deg, #f0f6ff 0%') === false) {
    die("[FAIL] Custom background gradient CSS missing!\n");
}
if (strpos($html1, 'linear-gradient(90deg, #2563eb 0%') === false) {
    die("[FAIL] Custom top accent border gradient CSS missing!\n");
}
echo "[PASS] Distinct background gradient and top accent styling verified in CSS!\n";

// Check 3: Reordered fields (1: Full Name, 2: Work Email, 3: Phone Number, 4: Create Password, 5: Confirm Password)
$posName = strpos($html1, 'id="jw-team-name"');
$posEmail = strpos($html1, 'id="jw-team-email"');
$posPhone = strpos($html1, 'id="team_mobile"');
$posPwd = strpos($html1, 'id="jw-team-pwd"');
$posConfirm = strpos($html1, 'id="jw-team-pwd-confirm"');

if (!$posName || !$posEmail || !$posPhone || !$posPwd || !$posConfirm) {
    die("[FAIL] One or more fields are missing! Name: $posName, Email: $posEmail, Phone: $posPhone, Pwd: $posPwd, Confirm: $posConfirm\n");
}

if (!($posName < $posEmail && $posEmail < $posPhone && $posPhone < $posPwd && $posPwd < $posConfirm)) {
    die("[FAIL] Fields are NOT in the required order! Expected Name < Email < Phone < Password < Confirm.\n" .
        "Order indices: Name={$posName}, Email={$posEmail}, Phone={$posPhone}, Pwd={$posPwd}, Confirm={$posConfirm}\n");
}
echo "[PASS] Strict Field Ordering Verified: 1. Full Name -> 2. Work Email -> 3. Phone Number -> 4. Create Password -> 5. Confirm Password!\n";

// Check 4: Full Name is Read-Only and Pre-filled
if (!preg_match('/<input[^>]+id="jw-team-name"[^>]+readonly/i', $html1)) {
    die("[FAIL] Full Name input is NOT readonly!\n");
}
if (strpos($html1, 'value="' . $memberName . '"') === false) {
    die("[FAIL] Full Name input is NOT prefilled with '$memberName'!\n");
}
echo "[PASS] Full Name is read-only and prefilled with '$memberName'!\n";

// Check 5: Work Email is Read-Only and Pre-filled
if (!preg_match('/<input[^>]+id="jw-team-email"[^>]+readonly/i', $html1)) {
    die("[FAIL] Work Email input is NOT readonly!\n");
}
if (strpos($html1, 'value="' . $testEmail . '"') === false) {
    die("[FAIL] Work Email input is NOT prefilled with '$testEmail'!\n");
}
echo "[PASS] Work Email is read-only and prefilled with '$testEmail'!\n";

// Check 6: Phone Component is Present and Optional
if (strpos($html1, 'team_calling_code') === false) {
    die("[FAIL] team_calling_code country code selector missing!\n");
}
if (strpos($html1, 'Phone Number (Optional)') === false) {
    die("[FAIL] Phone Number (Optional) label missing!\n");
}
echo "[PASS] Optional Phone Number component (<x-jero-phone-input>) rendered cleanly!\n";

// Check 7: Auto-accept Terms (No checkbox, hidden input toc=1, legal copy rendered)
if (strpos($html1, 'type="checkbox" name="toc"') !== false) {
    die("[FAIL] Terms checkbox is still present! Should be auto-accepted!\n");
}
if (strpos($html1, '<input type="hidden" name="toc" value="1">') === false) {
    die("[FAIL] Auto-accepted hidden toc input is missing!\n");
}
if (strpos($html1, 'Terms & Conditions') === false || strpos($html1, 'Privacy Policy') === false) {
    die("[FAIL] Legal copy for auto-accepted terms missing!\n");
}
echo "[PASS] Terms & Conditions auto-accepted cleanly (checkbox removed, hidden toc=1 added, legal copy displayed)!\n";

// Extract Cookie for Activation Test
$cookies = $res1->headers->getCookies();
$attachedCookie = null;
foreach ($cookies as $c) {
    if ($c->getName() === \App\Services\TeamInvitationService::COOKIE_NAME) {
        $attachedCookie = $c;
    }
}
if (!$attachedCookie) {
    die("[FAIL] Envelope cookie missing from response!\n");
}

// TEST 2: Submit Activation with Optional Phone Number
echo "\n--- TEST 2: Submit Activation with Phone Number ---\n";
$actReq = Request::create('/register/activate', 'POST', [
    'password' => 'UltraSecurePass2026!#',
    'password_confirmation' => 'UltraSecurePass2026!#',
    'mobile' => '9876543210',
    'calling_code' => '+91',
    'toc' => '1',
]);
$actReq->cookies->set(\App\Services\TeamInvitationService::COOKIE_NAME, $attachedCookie->getValue());
$actReq->headers->set('Idempotency-Key', 'act_test_' . uniqid());

$actService = app(\App\Services\RegistrationActivationService::class);
$actRes = $actService->activate($actReq);

echo "Activation status code: " . $actRes->getStatusCode() . "\n";
if ($actRes->getStatusCode() !== 201) {
    die("[FAIL] Activation returned: " . $actRes->getContent() . "\n");
}
$actData = json_decode($actRes->getContent(), true);
echo "[PASS] Activation succeeded! Redirect URL: " . $actData['redirect_url'] . "\n";

// Verify DB: User activated, mobile & calling_code updated, name preserved
$userAfter = DB::table('users')->find($userId);
echo "[CHECK] User Name: {$userAfter->name}\n";
echo "[CHECK] User Status: {$userAfter->status} (working_status: {$userAfter->working_status})\n";
echo "[CHECK] User Mobile: {$userAfter->calling_code} {$userAfter->mobile}\n";

if ($userAfter->name !== $memberName) {
    die("[FAIL] User name was altered! Expected '$memberName', got '{$userAfter->name}'\n");
}
if ($userAfter->status !== 'active' || (int)$userAfter->working_status !== 1) {
    die("[FAIL] User was not activated! Status: {$userAfter->status}\n");
}
if ($userAfter->mobile !== '9876543210' || $userAfter->calling_code !== '+91') {
    die("[FAIL] User mobile/calling_code was not updated! Got '{$userAfter->calling_code}' '{$userAfter->mobile}'\n");
}
echo "[PASS] User record 100% correctly activated with preserved name, status=active, and optional phone saved!\n";

// Verify DB: Invitation status is ACTIVATED
$invAfter = DB::table('team_invitations')->find($invId);
if ($invAfter->status !== 'ACTIVATED') {
    die("[FAIL] Expected invitation status ACTIVATED, got: " . $invAfter->status . "\n");
}
echo "[PASS] Invitation status transitioned to ACTIVATED!\n";

// Clean up Test 1 & 2
DB::table('users')->where('id', $userId)->delete();
DB::table('team_invitations')->where('id', $invId)->delete();

// TEST 3: Submit Activation WITHOUT Optional Phone Number
echo "\n--- TEST 3: Submit Activation WITHOUT Phone Number (Optional field check) ---\n";
$testEmail3 = 'opt_phone_' . time() . '@example.com';
$rawSecret3 = bin2hex(random_bytes(32));
$tokenHash3 = hash('sha256', $rawSecret3);

$userId3 = DB::table('users')->insertGetId([
    'name' => 'Optional Phone Specialist',
    'email' => $testEmail3,
    'password' => bcrypt('TempDummyPass!23'),
    'company_id' => 1,
    'status' => 'inactive',
    'working_status' => 0,
    'created_at' => now(),
    'updated_at' => now(),
]);

$invId3 = DB::table('team_invitations')->insertGetId([
    'uuid' => (string) Str::uuid(),
    'company_id' => 1,
    'email' => $testEmail3,
    'token_hash' => $tokenHash3,
    'role_id' => 1,
    'context_version' => 'TEAM_V2',
    'status' => 'PENDING',
    'delivery_status' => 'SENT',
    'invited_by' => 1,
    'expires_at' => now()->addHours(72),
    'created_at' => now(),
    'updated_at' => now(),
]);

$envelope3 = [
    'invitation_id' => $invId3,
    'tenant_id' => 1,
    'session_nonce' => bin2hex(random_bytes(16)),
    'expires_at' => now()->addHours(2)->timestamp
];
$cookieVal3 = Illuminate\Support\Facades\Crypt::encrypt($envelope3);

$actReq3 = Request::create('/register/activate', 'POST', [
    'password' => 'UltraSecurePass2026!#',
    'password_confirmation' => 'UltraSecurePass2026!#',
    'toc' => '1',
]);
$actReq3->cookies->set(\App\Services\TeamInvitationService::COOKIE_NAME, $cookieVal3);
$actReq3->headers->set('Idempotency-Key', 'act_test3_' . uniqid());

$actRes3 = $actService->activate($actReq3);

if ($actRes3->getStatusCode() !== 201) {
    die("[FAIL] Activation without phone failed! " . $actRes3->getContent() . "\n");
}
$userAfter3 = DB::table('users')->find($userId3);
if ($userAfter3->status !== 'active') {
    die("[FAIL] User 3 status is not active!\n");
}
echo "[PASS] Activation without phone succeeded cleanly! User status: active\n";

// Clean up Test 3
DB::table('users')->where('id', $userId3)->delete();
DB::table('team_invitations')->where('id', $invId3)->delete();

echo "\n=========================================================================\n";
echo " ALL TESTS PASSED! ENTERPRISE REDESIGN & ACTIVATION FLOW 100% VERIFIED! \n";
echo "=========================================================================\n";
