<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = DB::table('users')->where('is_superadmin', 1)->first();
echo json_encode([
    'id' => $u->id,
    'email' => $u->email,
    'name' => $u->name,
]);
