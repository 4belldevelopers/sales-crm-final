<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "sys_outbox_events exists: " . (Schema::hasTable('sys_outbox_events') ? 'YES' : 'NO') . "\n";
echo "transactional_outboxes exists: " . (Schema::hasTable('transactional_outboxes') ? 'YES' : 'NO') . "\n";
echo "team_outbox exists: " . (Schema::hasTable('team_outbox') ? 'YES' : 'NO') . "\n";
