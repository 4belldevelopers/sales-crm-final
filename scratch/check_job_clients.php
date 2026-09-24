<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$jobs = DB::table('jobs')
    ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
    ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
    ->whereIn('jobs.id', [1, 2, 3])
    ->select(
        'jobs.id',
        'jobs.title',
        'jobs.company_id',
        'jobs.client_id',
        'clients.name as client_name',
        'clients.self_company',
        'companies.company_name'
    )
    ->get();

foreach ($jobs as $j) {
    echo "Job ID: {$j->id} | Title: {$j->title} | Company ID: {$j->company_id} ({$j->company_name}) | Client ID: {$j->client_id} ({$j->client_name}) | self_company: {$j->self_company}\n";
}
