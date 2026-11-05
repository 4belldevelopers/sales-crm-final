<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$anomalousUsers = User::where('status', 'active')
    ->where(function ($q) {
        $q->whereNull('password')->orWhere('password', '');
    })
    ->get(['id', 'company_id', 'name', 'email', 'status', 'created_at']);

echo "Anomalous active users with empty password: " . $anomalousUsers->count() . "\n";
foreach ($anomalousUsers as $u) {
    echo "ID: {$u->id} | Company: {$u->company_id} | Name: {$u->name} | Email: {$u->email}\n";
}
