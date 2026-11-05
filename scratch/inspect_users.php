<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\ElasticService;

$u = User::where('email', 'like', '%vibhanshu%')->first();
if (!$u) {
    echo "User not found\n";
    exit;
}
$companyId = $u->company_id;
echo "Found User: {$u->name} (ID: {$u->id}), Company ID: {$companyId}\n\n";

echo "--- MYSQL USERS FOR COMPANY {$companyId} ---\n";
$users = User::where('company_id', $companyId)->get(['id', 'name', 'email', 'status', 'password', 'email_verification_code', 'root_user']);
foreach ($users as $user) {
    echo sprintf("ID: %d | Name: %s | Email: %s | Status: %s | HasPassword: %s | Code: %s | Root: %d\n",
        $user->id,
        $user->name,
        $user->email,
        $user->status,
        empty($user->password) ? 'NO' : 'YES',
        $user->email_verification_code ?? 'NULL',
        $user->root_user
    );
}

echo "\n--- ELASTICSEARCH USERS FOR COMPANY {$companyId} ---\n";
$elastic = app(ElasticService::class);
$res = $elastic->getClient()->search([
    'index' => ELASTIC_USERS_LIST,
    'body' => [
        'query' => [
            'term' => ['company_id' => $companyId]
        ]
    ]
]);

$hits = $res['hits']['hits'] ?? [];
echo "Total ES hits: " . count($hits) . "\n";
foreach ($hits as $hit) {
    $src = $hit['_source'];
    echo sprintf("ID: %d | Name: %s | Email: %s | Status: %s | HasPassword: %s | Code: %s\n",
        $src['id'] ?? 0,
        $src['name'] ?? '',
        $src['email'] ?? '',
        $src['status'] ?? '',
        empty($src['password']) ? 'NO' : 'YES',
        $src['email_verification_code'] ?? 'NULL'
    );
}
