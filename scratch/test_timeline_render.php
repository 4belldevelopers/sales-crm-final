<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$cols = DB::getSchemaBuilder()->getColumnListing('updatetimeline');
echo "updatetimeline columns: " . implode(', ', $cols) . "\n";

$rows = DB::table('updatetimeline')->where('job_id', 3)->get();
echo "Rows count for job_id 3: " . count($rows) . "\n";
foreach ($rows as $r) {
    print_r($r);
}
