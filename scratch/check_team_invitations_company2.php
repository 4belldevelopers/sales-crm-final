<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('team_invitations')->where('company_id', 2)->get();
echo "Total team_invitations for company 2: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "ID: {$r->id} | Email: {$r->email} | Status: {$r->status} | Delivery: {$r->delivery_status} | Created: {$r->created_at} | Used: {$r->used_at} | Revoked: {$r->revoked_at}\n";
}
