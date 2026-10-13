<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Auth::login($user);
$job = App\Models\Job::first();

echo "=== TEST 1: UNPUBLISHED STATE ===" . PHP_EOL;
$viewUnpublished = view('service_company.jobs.spartnerlisting', [
    'job' => $job,
    'job_id' => $job->id,
    'text_change' => '',
    'jobLocationCountselect' => (object)['city' => 'Noida'],
    'skillArr' => collect([]),
    'td_industries' => collect([]),
    'cities' => [],
    'default_skill' => 'Java',
    'registeredTotalCount' => 0,
    'assignedTotalCount' => 0,
    'activeCompanies' => 10,
    'allCompanies' => 10
])->render();

// Check presence of d-none on buttons in unpublished state
preg_match('/<button[^>]+id="sp_list_one"[^>]*>/i', $viewUnpublished, $m1);
preg_match('/<button[^>]+id="sp_list_two"[^>]*>/i', $viewUnpublished, $m2);
echo "Unpublished - sp_list_one: " . ($m1[0] ?? 'NOT FOUND') . PHP_EOL;
echo "Unpublished - sp_list_two: " . ($m2[0] ?? 'NOT FOUND') . PHP_EOL;

echo PHP_EOL . "=== TEST 2: LIVE STATE ===" . PHP_EOL;
$jobLive = clone $job;
$jobLive->public_market_status = 2;
$viewLive = view('service_company.jobs.spartnerlisting', [
    'job' => $jobLive,
    'job_id' => $jobLive->id,
    'text_change' => 'Published',
    'jobLocationCountselect' => (object)['city' => 'Noida'],
    'skillArr' => collect([]),
    'td_industries' => collect([]),
    'cities' => [],
    'default_skill' => 'Java',
    'registeredTotalCount' => 0,
    'assignedTotalCount' => 0,
    'activeCompanies' => 10,
    'allCompanies' => 10
])->render();

preg_match('/<button[^>]+id="sp_list_one"[^>]*>/i', $viewLive, $mL1);
preg_match('/<button[^>]+id="sp_list_two"[^>]*>/i', $viewLive, $mL2);
echo "Live - sp_list_one: " . ($mL1[0] ?? 'NOT FOUND') . PHP_EOL;
echo "Live - sp_list_two: " . ($mL2[0] ?? 'NOT FOUND') . PHP_EOL;

echo PHP_EOL . "ALL RENDER TESTS COMPLETED SUCCESSFULLY." . PHP_EOL;
