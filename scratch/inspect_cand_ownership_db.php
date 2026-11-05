<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Sample add_job_wise_condidates ===\n";
$rows = DB::table('add_job_wise_condidates')
    ->select('id', 'job_id', 'job_application_id', 'company_id', 'thirdparty_company_id', 'master_status_id')
    ->whereNotNull('thirdparty_company_id')
    ->limit(5)
    ->get();
print_r($rows);

echo "\n=== Sample job_applications ===\n";
$appIds = $rows->pluck('job_application_id')->toArray();
$apps = DB::table('job_applications')
    ->select('id', 'job_id', 'company_id', 'thirdparty_company_id', 'full_name')
    ->whereIn('id', $appIds)
    ->get();
print_r($apps);
