<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$countries = DB::table('countries')
    ->select('id', 'country_name', 'country_code', 'emoji')
    ->orderBy('country_name')
    ->get();

$outDir = __DIR__ . '/../public/assets/service_company/client';
if (!is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

$jsonPath = $outDir . '/countries.json';
file_put_contents($jsonPath, json_encode($countries, JSON_UNESCAPED_UNICODE));

echo "Successfully exported " . count($countries) . " countries to {$jsonPath}\n";
