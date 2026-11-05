<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Jobs\ProcessTeamOutboxEventJob;
use App\Models\TeamInvitation;
use App\Services\MailService;
use Illuminate\Support\Facades\DB;

echo "=== TEST: ProcessTeamOutboxEventJob Failure Handling ===\n";

$testEmail = 'failure_test_' . time() . '@example.com';
$rawSecret = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $rawSecret);

// Insert invitation
$invId = DB::table('team_invitations')->insertGetId([
    'uuid' => (string) \Illuminate\Support\Str::uuid(),
    'company_id' => 1,
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

// Mock MailService to throw an exception
$mailServiceMock = Mockery::mock(MailService::class);
$mailServiceMock->shouldReceive('sendEmail')
    ->once()
    ->andThrow(new \Exception('SMTP Connection Timed Out (Simulated Failure)'));

app()->instance(MailService::class, $mailServiceMock);

// Insert Schema v1 outbox event
$eventId = DB::table('sys_outbox_events')->insertGetId([
    'tenant_id' => 1,
    'event_type' => 'team_member.invited',
    'idempotency_key' => 'fail_test_' . uniqid(),
    'aggregate_type' => 'TeamMember',
    'aggregate_id' => $invId,
    'aggregate_version' => 1,
    'payload' => json_encode([
        'schema_version' => 1,
        'event_type' => 'team_member.invited',
        'email' => [
            'to' => $testEmail,
            'subject' => 'Account Credential',
            'template' => 'email.team.create-account',
            'sender_name' => 'JERO Admin',
            'from_address' => 'admin@jero.works',
            'data' => [
                'to' => $testEmail,
                'name' => 'Failure User',
                'url' => url('/register?token=' . $rawSecret),
            ],
        ],
        'email_data' => [
            'to' => $testEmail,
            'name' => 'Failure User',
            'url' => url('/register?token=' . $rawSecret),
        ],
    ]),
    'status' => 'PENDING',
    'attempts' => 0,
    'created_at' => now(),
]);

$caught = false;
try {
    $job = new ProcessTeamOutboxEventJob($eventId);
    $job->handle();
} catch (\Throwable $e) {
    $caught = true;
    echo "Caught expected job exception: " . $e->getMessage() . "\n";
}

// Check sys_outbox_events status
$eventAfter = DB::table('sys_outbox_events')->find($eventId);
echo "Event Status after failure: " . $eventAfter->status . "\n";
if ($eventAfter->status !== 'FAILED') {
    die("[FAIL] Expected FAILED, got: {$eventAfter->status}\n");
}
echo "[PASS] sys_outbox_events transitioned to FAILED (ready for retry / recovery)!\n";

// Check team_invitations delivery_status
$invAfter = DB::table('team_invitations')->find($invId);
echo "Invitation Delivery Status: " . $invAfter->delivery_status . "\n";
if ($invAfter->delivery_status !== 'FAILED') {
    die("[FAIL] Expected FAILED, got: {$invAfter->delivery_status}\n");
}
echo "[PASS] team_invitations.delivery_status updated to FAILED!\n";

// Clean up
DB::table('team_invitations')->where('id', $invId)->delete();
DB::table('sys_outbox_events')->where('id', $eventId)->delete();

echo "\n[SUCCESS] Failure Handling & Zero False-Success Verified!\n";
