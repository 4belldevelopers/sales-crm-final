<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== USERS FOR COMPANY 2 ===\n";
$users = User::where('company_id', 2)->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Status: {$u->status} | Working: {$u->working_status} | PassLen: " . strlen($u->password ?? '') . " | VerifiedAt: " . ($u->email_verified_at ?? 'NULL') . " | Code: " . ($u->email_verification_code ?? 'NULL') . "\n";
}

echo "\n=== TEAM INVITATIONS (ALL RECENT) ===\n";
$invites = DB::table('team_invitations')->latest()->limit(10)->get();
foreach ($invites as $inv) {
    echo json_encode($inv) . "\n";
}

echo "\n=== ALL USERS WITH EMPTY PASSWORD OR STATUS INVITED/PENDING ===\n";
$unverified = User::where(function($q) {
    $q->whereNull('password')
      ->orWhere('password', '')
      ->orWhere('status', 'invited')
      ->orWhere('status', 'pending');
})->get(['id', 'company_id', 'name', 'email', 'status', 'email_verified_at', 'email_verification_code', 'created_at']);
foreach ($unverified as $u) {
    echo "ID: {$u->id} | Company: {$u->company_id} | Name: {$u->name} | Email: {$u->email} | Status: {$u->status} | Code: {$u->email_verification_code} | Created: {$u->created_at}\n";
}

echo "\n=== RECENT SYS_OUTBOX_EVENTS ===\n";
$events = DB::table('sys_outbox_events')->latest()->limit(10)->get();
foreach ($events as $ev) {
    echo "Event: {$ev->event_type} | AggregateID: {$ev->aggregate_id} | Status: {$ev->status} | Payload: {$ev->payload}\n";
}
