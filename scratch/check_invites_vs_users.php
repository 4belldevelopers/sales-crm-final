<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$invs = DB::table('team_invitations')->where('company_id', 2)->get();
foreach ($invs as $inv) {
    $user = DB::table('users')->where('email', $inv->email)->first();
    echo "Invite ID: {$inv->id} | Email: {$inv->email} | Status: {$inv->status} | User in DB: " . ($user ? "YES (ID {$user->id}, status {$user->status})" : "NO") . "\n";
}
