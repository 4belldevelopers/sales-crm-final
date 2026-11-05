<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = DB::table('users')->where('id', 3)->first();
print_r($user);
$profile = DB::table('users_profile')->where('user_id', 3)->first();
print_r($profile);
