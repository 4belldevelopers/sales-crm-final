<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('add_job_wise_condidates')->where('created_at', 'like', '2026-09%')->get();
echo "September candidates in add_job_wise_condidates: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "App ID: {$r->id} | job_id: {$r->job_id} | company_id: {$r->company_id} | thirdparty_company_id: {$r->thirdparty_company_id} | created_at: {$r->created_at}\n";
}
