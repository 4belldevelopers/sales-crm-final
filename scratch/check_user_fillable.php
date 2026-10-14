<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userA = new \App\Models\User(['id' => 10, 'company_id' => 100]);
echo "userA company_id from constructor: " . var_export($userA->company_id, true) . "\n";

$userA->company_id = 100;
echo "userA company_id after property set: " . var_export($userA->company_id, true) . "\n";
