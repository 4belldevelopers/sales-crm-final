<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

Cache::flush();
echo "Cache flushed successfully.\n";

$company_id = 2; // 4Bell Technology
$jobIds = [1, 2, 3];

$clientRows = DB::table('jobs')
    ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
    ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
    ->whereIn('jobs.id', $jobIds)
    ->select(
        'jobs.id as job_id',
        DB::raw("COALESCE(clients.name, companies.company_name, '') as client_title"),
        DB::raw("COALESCE(clients.self_company, 1) as self_company")
    )
    ->get();

foreach ($clientRows as $cRow) {
    $isSelf = ((int)$cRow->self_company === 1);
    $label = $isSelf ? 'Internal' : 'Internal Client';
    echo "Job {$cRow->job_id}: Title='{$cRow->client_title}', self_company={$cRow->self_company} => Label='{$label}'\n";
}
