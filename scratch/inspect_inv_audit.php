<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$logs = DB::table('team_invitation_audit_logs')->whereIn('invitation_id', [61, 62])->get();
foreach ($logs as $l) {
    echo "ID: {$l->id} | InvID: {$l->invitation_id} | Action: {$l->action} | IP: {$l->ip_address} | UA: {$l->user_agent} | Created: {$l->created_at}\n";
}

$inv = DB::table('team_invitations')->find(62);
print_r($inv);
