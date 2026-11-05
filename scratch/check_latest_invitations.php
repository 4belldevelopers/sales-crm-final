<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$invs = DB::table('team_invitations')->orderBy('id', 'desc')->limit(10)->get();
foreach ($invs as $i) {
    echo "ID: {$i->id} | Email: {$i->email} | Status: {$i->status} | Delivery: {$i->delivery_status} | Created: {$i->created_at} | TokenHash: " . substr($i->token_hash, 0, 12) . "...\n";
}
