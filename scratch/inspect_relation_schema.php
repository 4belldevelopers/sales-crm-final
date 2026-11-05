<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== staffing_company_relation columns ===\n";
print_r(Schema::getColumnListing('staffing_company_relation'));

echo "\n=== recommendation_relation columns ===\n";
print_r(Schema::getColumnListing('recommendation_relation'));

echo "\n=== sample staffing_company_relation rows ===\n";
$rows = DB::table('staffing_company_relation')->limit(3)->get();
print_r($rows);
