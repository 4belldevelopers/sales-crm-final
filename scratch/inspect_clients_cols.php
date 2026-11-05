<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$cols = DB::getSchemaBuilder()->getColumnListing('clients');
echo "Clients columns: " . implode(', ', $cols) . "\n";
$client = DB::table('clients')->first();
print_r($client);
