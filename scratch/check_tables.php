<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = array_map('current', DB::select('SHOW TABLES'));
$matched = array_values(array_filter($tables, fn($t) => str_contains($t, 'invite') || str_contains($t, 'team')));

echo "Matched tables:\n";
print_r($matched);
