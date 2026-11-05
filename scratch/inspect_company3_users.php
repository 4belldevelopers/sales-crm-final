<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = DB::table('users')->where('company_id', 3)->get();
echo "Users for Company 3 (count: " . count($users) . "):\n";
foreach ($users as $u) {
    echo "ID: {$u->id} | name: '{$u->name}' | email: {$u->email}\n";
}
