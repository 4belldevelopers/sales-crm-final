<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = DB::getSchemaBuilder()->getColumnListing('agency_commercial_terms');
dump('agency_commercial_terms columns:', $cols);
$rows = DB::table('agency_commercial_terms')->get();
dump('Rows count in agency_commercial_terms:', $rows->count());
foreach ($rows as $r) {
    dump('Row company_id=' . $r->company_id, $r);
}
