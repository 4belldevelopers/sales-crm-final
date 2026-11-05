<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = DB::table('roles')
    ->select('id', 'name', 'display_name', 'description', 'company_id')
    ->limit(15)
    ->get();

echo json_encode($roles, JSON_PRETTY_PRINT);
