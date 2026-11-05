<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('sys_outbox_events')
    ->whereIn('id', [187, 188])
    ->get();

foreach ($rows as $r) {
    echo "ID: {$r->id}\n";
    echo "Status: {$r->status}\n";
    echo "Created: {$r->created_at}\n";
    echo "Locked: {$r->locked_at}\n";
    echo "Processed: {$r->processed_at}\n";
    echo "--------------------------\n";
}
