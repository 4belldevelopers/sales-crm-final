<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== INVITATION_REQUEST TABLE COLUMNS ===\n";
print_r(Schema::getColumnListing('invitation_request'));

echo "=== INVITATION_REQUEST ROWS ===\n";
$rows = DB::table('invitation_request')->latest()->limit(20)->get();
echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";
