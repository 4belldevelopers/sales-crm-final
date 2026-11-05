<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$invs = DB::table('team_invitations')->where('email', 'snehilgupta10m@gmail.com')->get();
foreach ($invs as $i) {
    echo "ID: {$i->id} | Status: {$i->status} | Delivery: {$i->delivery_status} | Expires: {$i->expires_at}\n";
}
