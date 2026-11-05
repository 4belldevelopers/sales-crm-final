<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = DB::table('roles')->where(function($q) {
    $q->where('company_id', 2)->orWhereNull('company_id');
})->get();

echo "Roles for Company 2 (or null company_id):\n";
foreach ($roles as $r) {
    echo "- ID: {$r->id}, Name: {$r->name}, Display: {$r->display_name}, Company: " . var_export($r->company_id, true) . "\n";
}

$rolesOnlyCompany = DB::table('roles')->where('company_id', 2)->get();
echo "\nRoles with strict company_id = 2:\n";
foreach ($rolesOnlyCompany as $r) {
    echo "- ID: {$r->id}, Name: {$r->name}, Display: {$r->display_name}\n";
}
