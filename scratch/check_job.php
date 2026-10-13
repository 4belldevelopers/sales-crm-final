<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jobs = DB::table('jobs')->where('title', 'like', '%Java Security%')->get();
foreach ($jobs as $j) {
    echo "ID: {$j->id}, Title: {$j->title}, public_market_status: {$j->public_market_status}, status: {$j->status}\n";
}
