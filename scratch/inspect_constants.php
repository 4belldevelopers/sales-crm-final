<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "ELASTIC_JOBS: " . (defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS')) . "\n";
echo "ELASTIC_CANDIDATES: " . (defined('ELASTIC_CANDIDATES') ? ELASTIC_CANDIDATES : config('constants.ELASTIC_CANDIDATES')) . "\n";
echo "ELASTIC_TIMELINE: " . (defined('ELASTIC_TIMELINE') ? ELASTIC_TIMELINE : config('constants.ELASTIC_TIMELINE')) . "\n";
echo "ELASTIC_INTERVIEW_TRACKER: " . (defined('ELASTIC_INTERVIEW_TRACKER') ? ELASTIC_INTERVIEW_TRACKER : config('constants.ELASTIC_INTERVIEW_TRACKER')) . "\n";
echo "ELASTIC_CLIENT_LIST: " . (defined('ELASTIC_CLIENT_LIST') ? ELASTIC_CLIENT_LIST : config('constants.ELASTIC_CLIENT_LIST')) . "\n";
