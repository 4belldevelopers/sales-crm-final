<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Jobs\ProcessTeamOutboxEventJob;
use App\Models\TeamInvitation;
use App\Services\MailService;
use Illuminate\Support\Facades\DB;

echo "=== TEST: ProcessTeamOutboxEventJob End-to-End Delivery ===\n";

$testEmail = 'delivery_test_' . time() . '@example.com';
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

// Mock MailService so we don't attempt external network SMTP during unit test
$mailServiceMock = Mockery::mock(MailService::class);
$mailServiceMock->shouldReceive('sendEmail')
    ->once()
    ->andReturn(true);

app()->instance(MailService::class, $mailServiceMock);

// Insert Schema v1 outbox event
$eventId = DB::table('sys_outbox_events')->insertGetId([
    'tenant_id' => 1,
    'event_type' => 'team_member.invited',
    'idempotency_key' => 'delivery_test_' . uniqid(),
    'aggregate_type' => 'team_invitation',
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
                'name' => 'Delivery User',
                'url' => url('/register?token=' . $rawSecret),
            ],
        ],
        'email_data' => [
            'to' => $testEmail,
            'name' => 'Delivery User',
            'url' => url('/register?token=' . $rawSecret),
        ],
    ]),
    'status' => 'PENDING',
    'attempts' => 0,
    'created_at' => now(),
]);

$job = new ProcessTeamOutboxEventJob($eventId);
$job->handle();

// Check sys_outbox_events status
$eventAfter = DB::table('sys_outbox_events')->find($eventId);
echo "Event Status after processing: " . $eventAfter->status . "\n";
if ($eventAfter->status !== 'COMPLETED') {
    die("[FAIL] Expected COMPLETED, got: {$eventAfter->status}\n");
}
echo "[PASS] sys_outbox_events transitioned to COMPLETED!\n";

// Check team_invitations delivery_status
$invAfter = DB::table('team_invitations')->find($invId);
echo "Invitation Delivery Status: " . $invAfter->delivery_status . "\n";
if ($invAfter->delivery_status !== 'SENT') {
    die("[FAIL] Expected SENT, got: {$invAfter->delivery_status}\n");
}
echo "[PASS] team_invitations.delivery_status updated to SENT!\n";

// Clean up
DB::table('team_invitations')->where('id', $invId)->delete();
DB::table('sys_outbox_events')->where('id', $eventId)->delete();

echo "\n[SUCCESS] Outbox Email Processing End-to-End Verified!\n";
