<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$events = DB::table('sys_outbox_events')
    ->where('payload', 'like', '%kajalverma5057%')
    ->orWhere('payload', 'like', '%snehilgupta10m%')
    ->get();

foreach ($events as $e) {
    echo "Event ID: {$e->id} | Status: {$e->status} | Attempts: {$e->attempts}\n";
    $p = json_decode($e->payload, true);
    echo "URL: " . ($p['email']['data']['url'] ?? ($p['response_data']['email_data']['url'] ?? ($p['email_data']['url'] ?? 'none'))) . "\n";
}
