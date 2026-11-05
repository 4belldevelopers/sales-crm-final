<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$compCol = DB::select("SHOW COLUMNS FROM companies WHERE Field = 'id'");
$userCol = DB::select("SHOW COLUMNS FROM users WHERE Field = 'id'");
print_r($compCol);
print_r($userCol);
