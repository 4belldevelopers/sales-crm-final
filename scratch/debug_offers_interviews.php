<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$company_id = 2; // 4Bell
$agency_company_id = 4; // InnovateSoft

echo "=== All Candidate Submissions for Agency 4 in DB ===\n";
$all_cands = DB::table('add_job_wise_condidates')
    ->where('company_id', $company_id)
    ->where('thirdparty_company_id', $agency_company_id)
    ->get();
print_r($all_cands->toArray());

echo "\n=== All Interviews in DB (interview_tracker) ===\n";
$interviews = DB::table('interview_tracker')->get();
print_r($interviews->toArray());
