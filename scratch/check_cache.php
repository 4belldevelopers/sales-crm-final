<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$companyId = 2;
echo "ats:teamversion:company_2 = " . var_export(cache()->get('ats:teamversion:company_2'), true) . "\n";
echo "ats:teamcounts:2 = " . var_export(cache()->get('ats:teamcounts:2'), true) . "\n";
