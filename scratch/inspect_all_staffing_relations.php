<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('staffing_company_relation')->get();
echo "Total relations: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "ID: {$r->id} | from_company_id: {$r->from_company_id} (client_id: {$r->from_client_id}) -> to_company_id: {$r->to_company_id} (client_id: {$r->to_client_id}) | final_status: {$r->final_status}\n";
}
