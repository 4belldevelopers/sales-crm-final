<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== 1. RECOMMENDATION_RELATION ===\n";
$recs = DB::table('recommendation_relation')->get();
foreach ($recs as $r) {
    echo "ID: {$r->id} | job_id: {$r->job_id} | from_company_id: {$r->from_company_id} | to_company_id: {$r->to_company_id} | from_user_id: {$r->from_user_id} | to_user_id: {$r->to_user_id} | final_status: {$r->final_status} | created_at: {$r->created_at}\n";
}

echo "\n=== 2. JOBS TABLE (id 1, 2) ===\n";
$jobs = DB::table('jobs')->whereIn('id', [1, 2])->get();
foreach ($jobs as $j) {
    print_r($j);
}

echo "\n=== 3. USERS INVOLVED ===\n";
$userIds = DB::table('recommendation_relation')->pluck('from_user_id')->merge(DB::table('recommendation_relation')->pluck('to_user_id'))->unique()->filter();
$users = DB::table('users')->whereIn('id', $userIds)->get(['id', 'name', 'email', 'company_id']);
foreach ($users as $u) {
    echo "User ID: {$u->id} | name: '{$u->name}' | email: {$u->email} | company_id: {$u->company_id}\n";
}

echo "\n=== 4. CANDIDATES TABLE FOR JOB 1 and 2 ===\n";
$cands = DB::table('add_job_wise_condidates')->whereIn('job_id', [1, 2])->get();
foreach ($cands as $c) {
    echo "App ID: {$c->id} | job_id: {$c->job_id} | cand_id: {$c->condidates_id} | company_id: {$c->company_id} | thirdparty_company_id: {$c->thirdparty_company_id} | created_at: {$c->created_at}\n";
}
