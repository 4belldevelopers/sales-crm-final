<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\TeamInvitation;
use App\Services\ElasticService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

$emails = ['kajalverma5057@gmail.com', 'snehilgupta10m@gmail.com'];

echo "=== DIAGNOSTIC FOR KAJAL & SNEHIL ===\n\n";

$elastic = app(ElasticService::class)->getClient();

foreach ($emails as $email) {
    echo "----------------------------------------\n";
    echo "EMAIL: {$email}\n";
    echo "----------------------------------------\n";

    // 1. MySQL User Record
    $user = DB::table('users')->where('email', $email)->first();
    if (!$user) {
        echo "[MySQL Users] NOT FOUND!\n";
    } else {
        echo "[MySQL Users] ID: {$user->id}, Name: {$user->name}, Company ID: {$user->company_id}\n";
        echo "  Status: '{$user->status}', Working Status: '{$user->working_status}'\n";
        echo "  Password: " . (empty($user->password) ? 'EMPTY/NULL' : 'SET (Length: ' . strlen($user->password) . ')') . "\n";
        echo "  Email Verification Code: " . var_export($user->email_verification_code, true) . "\n";
        echo "  Last Login: {$user->last_login}\n";
    }

    // 2. MySQL Team Invitations Record
    $invs = DB::table('team_invitations')->where('email', $email)->get();
    echo "[MySQL Invitations] Found " . count($invs) . " invitation records:\n";
    foreach ($invs as $inv) {
        echo "  Invitation #{$inv->id}: Status='{$inv->status}', Delivery='{$inv->delivery_status}', UsedAt='{$inv->used_at}', ExpiresAt='{$inv->expires_at}', Company ID={$inv->company_id}\n";
    }

    // 3. Elasticsearch Document
    if ($user) {
        try {
            $esDoc = $elastic->get([
                'index' => ELASTIC_USERS_LIST,
                'id' => (string) $user->id,
            ]);
            $src = $esDoc['_source'] ?? [];
            echo "[Elasticsearch ELASTIC_USERS_LIST] ID: {$user->id} FOUND!\n";
            echo "  Name: " . ($src['name'] ?? 'N/A') . "\n";
            echo "  Status: " . ($src['status'] ?? 'N/A') . "\n";
            echo "  Password: " . (isset($src['password']) ? (empty($src['password']) ? 'EMPTY' : 'SET (Length: ' . strlen($src['password']) . ')') : 'NOT IN DOC') . "\n";
            echo "  Email Verification Code: " . var_export($src['email_verification_code'] ?? null, true) . "\n";
            echo "  Display Name: " . ($src['display_name'] ?? 'N/A') . "\n";
            echo "  Role Name: " . ($src['role_name'] ?? 'N/A') . "\n";
        } catch (\Throwable $e) {
            echo "[Elasticsearch ELASTIC_USERS_LIST] NOT FOUND or ERROR: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

if ($user) {
    $cid = $user->company_id;
    echo "=== CACHE KEYS FOR COMPANY #{$cid} ===\n";
    echo "Version Key (ats:teamversion:company_{$cid}): " . var_export(Cache::get('ats:teamversion:company_' . $cid), true) . "\n";
    echo "Team Counts (ats:teamcounts:{$cid}): " . var_export(Cache::get('ats:teamcounts:' . $cid), true) . "\n";
}
