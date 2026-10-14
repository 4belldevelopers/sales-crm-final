<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "sys_idempotency_keys: " . (Schema::hasTable('sys_idempotency_keys') ? 'YES' : 'NO') . "\n";
if (Schema::hasTable('sys_idempotency_keys')) {
    echo "Columns: " . json_encode(Schema::getColumnListing('sys_idempotency_keys')) . "\n";
    $indexes = DB::select("SHOW INDEX FROM sys_idempotency_keys");
    echo "Indexes: " . json_encode(array_map(fn($idx) => ['Key_name' => $idx->Key_name, 'Column_name' => $idx->Column_name, 'Non_unique' => $idx->Non_unique], $indexes)) . "\n";
}

echo "sys_outbox_events: " . (Schema::hasTable('sys_outbox_events') ? 'YES' : 'NO') . "\n";
if (Schema::hasTable('sys_outbox_events')) {
    echo "Columns: " . json_encode(Schema::getColumnListing('sys_outbox_events')) . "\n";
    $indexes = DB::select("SHOW INDEX FROM sys_outbox_events");
    echo "Indexes: " . json_encode(array_map(fn($idx) => ['Key_name' => $idx->Key_name, 'Column_name' => $idx->Column_name, 'Non_unique' => $idx->Non_unique], $indexes)) . "\n";
}

echo "recommendation_relation indexes:\n";
$indexes = DB::select("SHOW INDEX FROM recommendation_relation");
echo json_encode(array_map(fn($idx) => ['Key_name' => $idx->Key_name, 'Column_name' => $idx->Column_name, 'Non_unique' => $idx->Non_unique], $indexes)) . "\n";

echo "staffing_company_invitation indexes:\n";
$indexes = DB::select("SHOW INDEX FROM staffing_company_invitation");
echo json_encode(array_map(fn($idx) => ['Key_name' => $idx->Key_name, 'Column_name' => $idx->Column_name, 'Non_unique' => $idx->Non_unique], $indexes)) . "\n";
