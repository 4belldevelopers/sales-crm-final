<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$u = User::withoutGlobalScopes()->where('email', 'snehilgupta10m@gmail.com')->first();
echo "User: " . ($u ? $u->name : 'NOT FOUND') . " | Mobile: " . ($u ? $u->mobile : 'NONE') . "\n";

$u2 = User::withoutGlobalScopes()->where('email', 'kajalverma5057@gmail.com')->first();
echo "User: " . ($u2 ? $u2->name : 'NOT FOUND') . " | Mobile: " . ($u2 ? $u2->mobile : 'NONE') . "\n";
