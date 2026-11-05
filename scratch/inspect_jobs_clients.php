<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$jobCols = DB::getSchemaBuilder()->getColumnListing('jobs');
echo "Job columns related to client: " . implode(', ', array_filter($jobCols, function($c) { return str_contains($c, 'client') || str_contains($c, 'comp'); })) . "\n";

$sampleJob = DB::table('jobs')->where('client_id', '>', 0)->first();
if ($sampleJob) {
    echo "Found job with client_id > 0: ID={$sampleJob->id}, client_id={$sampleJob->client_id}, company_id={$sampleJob->company_id}, title={$sampleJob->title}\n";
    $client = DB::table('clients')->where('id', $sampleJob->client_id)->first();
    if ($client) {
        echo "Client record: ID={$client->id}, name={$client->name}, company_name={$client->company_name}, logo={$client->logo}\n";
    }
} else {
    echo "No jobs with client_id > 0 found in sample\n";
    $anyClient = DB::table('clients')->first();
    if ($anyClient) {
        echo "Clients table sample: ID={$anyClient->id}, name={$anyClient->name}, company_name={$anyClient->company_name}\n";
    }
}
