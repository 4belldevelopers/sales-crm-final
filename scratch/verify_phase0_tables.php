<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "team_invitations exists: " . (Schema::hasTable('team_invitations') ? 'YES' : 'NO') . "\n";
echo "registration_idempotency_keys exists: " . (Schema::hasTable('registration_idempotency_keys') ? 'YES' : 'NO') . "\n";
echo "team_invitation_audit_logs exists: " . (Schema::hasTable('team_invitation_audit_logs') ? 'YES' : 'NO') . "\n";
echo "tenant_state_versions exists: " . (Schema::hasTable('tenant_state_versions') ? 'YES' : 'NO') . "\n";

$indexes = DB::select('SHOW INDEXES FROM team_invitations');
echo "\nteam_invitations indexes:\n";
foreach ($indexes as $idx) {
    echo "- " . $idx->Key_name . " on (" . $idx->Column_name . ")\n";
}

$tsvCount = DB::table('tenant_state_versions')->count();
echo "\ntenant_state_versions rows seeded: " . $tsvCount . "\n";
