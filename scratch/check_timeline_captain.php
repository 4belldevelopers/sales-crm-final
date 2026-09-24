<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$jobId = 3;
$company_id = 2; // current logged-in company (4Bell Technology)

$job = DB::table('jobs')->where('id', $jobId)->first();
echo "Job 3 Company ID: " . ($job->company_id ?? 'N/A') . "\n";

$spokpersonName = DB::table('job_assign')
    ->join('users', 'users.id', '=', 'job_assign.spok_user_id')
    ->where('job_assign.job_id', $jobId)
    ->where('job_assign.company_id', $job->company_id)
    ->where('job_assign.spok_user_id', '>', 0)
    ->value('users.name');

echo "SPOC/Captain Name for Job 3 (Company {$job->company_id}): " . ($spokpersonName ?? 'None') . "\n";

$compName = DB::table('companies')->where('id', $job->company_id)->value('company_name');
echo "Company Name for Job 3: " . ($compName ?? 'None') . "\n";
