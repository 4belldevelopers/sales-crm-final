<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('recommendation_relation')
    ->where(function($q) {
        $q->where('from_company_id', 3)->orWhere('to_company_id', 3);
    })
    ->get();

echo "Total recommendation_relation involving company 3: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "ID: {$r->id}, Job: {$r->job_id}, From: {$r->from_company_id}, To: {$r->to_company_id}, final_status: {$r->final_status}, created_at: {$r->created_at}\n";
}
