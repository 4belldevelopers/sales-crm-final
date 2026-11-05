<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TeamInvitation;
use App\Enums\InvitationStatus;

$inv = TeamInvitation::find(62);
echo "Inv 62 Status: " . $inv->status->value . "\n";
echo "Inv 62 Status isAcceptable: " . ($inv->status->isAcceptable() ? 'YES' : 'NO') . "\n";
echo "Inv 62 isAcceptable(): " . ($inv->isAcceptable() ? 'YES' : 'NO') . "\n";
echo "Inv 62 revoked_at: " . ($inv->revoked_at ? $inv->revoked_at : 'NULL') . "\n";
echo "Inv 62 used_at: " . ($inv->used_at ? $inv->used_at : 'NULL') . "\n";
echo "Inv 62 expires_at: " . $inv->expires_at . " (isPast: " . ($inv->expires_at->isPast() ? 'YES' : 'NO') . ")\n";
