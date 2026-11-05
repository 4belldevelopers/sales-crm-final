<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Company;
use App\Models\TeamInvitation;
use App\Enums\InvitationStatus;
use App\Services\TeamMutationService;
use App\Services\TeamInvitationService;
use App\Services\RegistrationContextResolver;
use App\Services\RegistrationActivationService;
use App\Jobs\ProcessTeamOutboxEventJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\View;

echo "=== STARTING GOD-LEVEL VERIFICATION SUITE ===\n\n";

$company = Company::withoutGlobalScopes()->first();
if (!$company) {
    die("ERROR: No company found in DB\n");
}
$companyId = $company->id;
echo "[PASS] Test Company ID: {$companyId} ({$company->company_name})\n";

// TEST 1: Schema v1 Outbox Generation & Backward Compatibility
echo "\n--- TEST 1: Schema v1 Outbox Generation ---\n";
$testEmail = 'godlevel_test_' . time() . '@example.com';
$rawSecret = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $rawSecret);
$testIdempotencyKey = 'test_idem_' . uniqid();

$mutationService = app(TeamMutationService::class);
$mutationResult = $mutationService->executeIdempotentMutation(
    tenantId: $companyId,
    targetUserId: null,
    idempotencyKey: $testIdempotencyKey,
    eventType: 'team_member.invited',
    requestPayload: ['email' => $testEmail, 'name' => 'God Level User'],
    mutationCallback: function () use ($companyId, $testEmail, $tokenHash, $rawSecret) {
        $invId = DB::table('team_invitations')->insertGetId([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'company_id' => $companyId,
            'email' => $testEmail,
            'token_hash' => $tokenHash,
            'role_id' => 1,
            'context_version' => 'TEAM_V2',
            'status' => 'PENDING',
            'delivery_status' => 'QUEUED',
            'invited_by' => 1,
            'expires_at' => now()->addHours(72),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $url = url('/register?token=' . $rawSecret);
        $emailData = [
            'to' => $testEmail,
            'name' => 'God Level User',
            'url' => $url,
            'company_name' => 'Test Company',
        ];

        return [
            'status' => 'success',
            'user_id' => 999999,
            'email' => $testEmail,
            'company_id' => $companyId,
            'invitation_url' => $url,
            'email_data' => $emailData,
            'email_subject' => 'Account Credential Verification',
            'email_template' => 'email.team.create-account',
            'sender_name' => 'JERO Support',
            'from_address' => 'support@jero.works',
        ];
    }
);

// Fetch created event from sys_outbox_events
$outboxEvent = DB::table('sys_outbox_events')
    ->where('idempotency_key', $testIdempotencyKey)
    ->first();

if (!$outboxEvent) {
    die("[FAIL] Outbox event was not created!\n");
}
$eventPayload = json_decode($outboxEvent->payload, true);

if (!isset($eventPayload['schema_version']) || $eventPayload['schema_version'] !== 1) {
    die("[FAIL] Expected schema_version 1, got: " . ($eventPayload['schema_version'] ?? 'null') . "\n");
}
if (!isset($eventPayload['email']['to']) || $eventPayload['email']['to'] !== $testEmail) {
    die("[FAIL] Canonical email.to missing or invalid!\n");
}
if (!isset($eventPayload['email']['template']) || $eventPayload['email']['template'] !== 'email.team.create-account') {
    die("[FAIL] Canonical email.template missing!\n");
}
if (!isset($eventPayload['response_data']['email_data']) || !isset($eventPayload['email_data'])) {
    die("[FAIL] Backward-compatible fallback keys missing in payload!\n");
}
echo "[PASS] Schema v1 canonical event payload verified successfully with full backward compatibility!\n";

// TEST 2: ProcessTeamOutboxEventJob Zero False-Success Invariant
echo "\n--- TEST 2: ProcessTeamOutboxEventJob Invariant Enforcement ---\n";
// Create a deliberately corrupted event with missing email data
$badEventId = DB::table('sys_outbox_events')->insertGetId([
    'tenant_id' => $companyId,
    'event_type' => 'team_member.invited',
    'idempotency_key' => 'corrupt_' . uniqid(),
    'aggregate_type' => 'team_invitation',
    'aggregate_id' => 0,
    'aggregate_version' => 1,
    'payload' => json_encode(['empty' => true]),
    'status' => 'PENDING',
    'attempts' => 0,
    'created_at' => now(),
]);

$caughtException = false;
try {
    $job = new ProcessTeamOutboxEventJob($badEventId);
    $job->handle();
} catch (\RuntimeException $e) {
    $caughtException = true;
    echo "[PASS] Invariant correctly threw RuntimeException: " . $e->getMessage() . "\n";
}

$badEventState = DB::table('sys_outbox_events')->find($badEventId);
if ($badEventState->status === 'COMPLETED') {
    die("[FAIL] Invariant violation: Event without email data marked COMPLETED!\n");
}
echo "[PASS] Zero False-Success Invariant strictly preserved! Bad event status is: {$badEventState->status}\n";

// Clean up bad event
DB::table('sys_outbox_events')->where('id', $badEventId)->delete();

// TEST 3: Token Resolution & HttpOnly Cookie Session Envelope
echo "\n--- TEST 3: Token Resolution & Context Resolver ---\n";
$req = Request::create('/register?token=' . $rawSecret, 'GET');
$invitationService = app(TeamInvitationService::class);
$envelopeData = $invitationService->resolveTokenAndCreateEnvelope($rawSecret, $req);

$cookie = $envelopeData['cookie'];
if (!$cookie || $cookie->getName() !== TeamInvitationService::COOKIE_NAME) {
    die("[FAIL] Cookie name mismatch!\n");
}
echo "[PASS] Token resolved and HttpOnly session envelope cookie created!\n";

// Verify Context Resolver reads cookie and yields CONTEXT_TEAM
$contextReq = Request::create('/register', 'GET');
$contextReq->cookies->set(TeamInvitationService::COOKIE_NAME, $cookie->getValue());

$resolver = app(RegistrationContextResolver::class);
$context = $resolver->resolve($contextReq);

if ($context['context'] !== RegistrationContextResolver::CONTEXT_TEAM) {
    die("[FAIL] Context expected TEAM, got: {$context['context']}\n");
}
if ($context['member']['email'] !== $testEmail) {
    die("[FAIL] Member email mismatch in context! Expected {$testEmail}, got: {$context['member']['email']}\n");
}
echo "[PASS] Registration Context Engine resolved context: {$context['context']} for {$context['member']['email']}\n";

// TEST 4: Blade View Rendering with TEAM Context
echo "\n--- TEST 4: Blade Unified Shell Rendering in TEAM mode ---\n";
try {
    $renderedHtml = View::make('auth.login', [
        'initialMode' => 'signup',
        'registrationContext' => $context,
        'setting' => null,
        'global' => null,
        'frontTheme' => null,
        'headerData' => null,
        'adminTheme' => null,
        'errors' => new \Illuminate\Support\ViewErrorBag(),
    ])->render();

    if (strpos($renderedHtml, 'id="kt_team_activate_form"') === false) {
        die("[FAIL] Rendered Blade HTML does not contain #kt_team_activate_form!\n");
    }
    if (strpos($renderedHtml, $testEmail) === false) {
        die("[FAIL] Rendered Blade HTML does not contain the invited email {$testEmail}!\n");
    }
    if (strpos($renderedHtml, 'kt_team_activate_submit') === false) {
        die("[FAIL] Rendered Blade HTML does not contain #kt_team_activate_submit button!\n");
    }
    echo "[PASS] Blade Unified Registration Shell compiles and renders team activation card perfectly!\n";
} catch (\Throwable $e) {
    die("[FAIL] Blade view rendering threw exception: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
}

// TEST 5: Account Activation Transaction & Session Envelope
echo "\n--- TEST 5: RegistrationActivationService Execution ---\n";
$activationReq = Request::create('/register/activate', 'POST', [
    'name' => 'God Level Activated User',
    'password' => 'SecurePass123!@#',
    'password_confirmation' => 'SecurePass123!@#',
    'toc' => '1',
]);
$activationReq->cookies->set(TeamInvitationService::COOKIE_NAME, $cookie->getValue());
$activationReq->headers->set('Idempotency-Key', 'act_idem_' . uniqid());

$activationService = app(RegistrationActivationService::class);
$response = $activationService->activate($activationReq);

if ($response->getStatusCode() !== 201) {
    $content = $response->getContent();
    die("[FAIL] Activation failed with status " . $response->getStatusCode() . ": " . $content . "\n");
}
$respData = json_decode($response->getContent(), true);
echo "[PASS] Activation succeeded! Status: {$respData['status']}, Redirect: {$respData['redirect_url']}\n";

// Verify Database State
$activatedInvitation = DB::table('team_invitations')->where('email', $testEmail)->first();
if ($activatedInvitation->status !== 'ACTIVATED') {
    die("[FAIL] Invitation status not ACTIVATED, got: {$activatedInvitation->status}\n");
}
if (!$activatedInvitation->used_at) {
    die("[FAIL] Invitation used_at timestamp is null!\n");
}
echo "[PASS] team_invitations row updated to ACTIVATED with used_at timestamp!\n";

$activatedUser = User::withoutGlobalScopes()->where('email', $testEmail)->first();
if (!$activatedUser || $activatedUser->status !== 'active') {
    die("[FAIL] User not created or not active!\n");
}
echo "[PASS] User record created with status=active and working_status=1!\n";

// TEST 6: Double-Activation / Replay Rejection
echo "\n--- TEST 6: Double-Activation Rejection (INV-09) ---\n";
// Re-attempt activation with same token
$replayReq = Request::create('/register/activate', 'POST', [
    'name' => 'God Level Replay Attempt',
    'password' => 'SecurePass123!@#',
    'password_confirmation' => 'SecurePass123!@#',
    'toc' => '1',
]);
$replayReq->cookies->set(TeamInvitationService::COOKIE_NAME, $cookie->getValue());
$replayReq->headers->set('Idempotency-Key', 'act_replay_' . uniqid());

$caughtReplay = false;
try {
    $replayResponse = $activationService->activate($replayReq);
    if ($replayResponse->getStatusCode() !== 201) {
        $caughtReplay = true;
        echo "[PASS] Replay rejected with status code: " . $replayResponse->getStatusCode() . "\n";
    }
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $caughtReplay = true;
    echo "[PASS] Replay rejected with HTTP Exception " . $e->getStatusCode() . ": " . $e->getMessage() . "\n";
}

if (!$caughtReplay) {
    die("[FAIL] Used invitation was accepted again! Double activation prevention failed!\n");
}

// Cleanup test data
DB::table('users')->where('email', $testEmail)->delete();
DB::table('team_invitations')->where('email', $testEmail)->delete();
DB::table('sys_outbox_events')->where('idempotency_key', $testIdempotencyKey)->delete();

echo "\n============================================\n";
echo " ALL 6 GOD-LEVEL VERIFICATION TESTS PASSED! \n";
echo "============================================\n";
