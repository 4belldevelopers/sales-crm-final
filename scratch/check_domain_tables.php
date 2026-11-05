<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = array_map('current', DB::select('SHOW TABLES'));
$matched = array_values(array_filter($tables, fn($t) => str_contains($t, 'block') || str_contains($t, 'domain') || str_contains($t, 'dispos')));
print_r($matched);
