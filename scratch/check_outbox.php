<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ANY EVENTS / EMAILS FOR recruiter2 ===\n";
$events = DB::table('sys_outbox_events')->where('payload', 'like', '%recruiter2%')->get();
echo "Outbox events: " . count($events) . "\n";
foreach ($events as $ev) {
    echo json_encode($ev) . "\n";
}

$allEmails = DB::table('sys_outbox_events')->latest()->limit(5)->get();
foreach ($allEmails as $ev) {
    echo "Recent: {$ev->event_type} -> {$ev->payload}\n";
}
