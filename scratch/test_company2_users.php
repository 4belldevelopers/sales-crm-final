<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::where('company_id', 2)->get();
foreach ($users as $u) {
    echo "User #{$u->id} ({$u->name} - {$u->email}): status='{$u->status}', pwd=" . (empty($u->password) ? 'EMPTY' : 'SET') . "\n";
}
