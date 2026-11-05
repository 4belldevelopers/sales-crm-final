<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use Illuminate\Support\Facades\Cache;

echo "Syncing Kajal (999139)...\n";
GeneralMethod::userIndexingupload(999139);

echo "Syncing Snehil (999131)...\n";
GeneralMethod::userIndexingupload(999131);

// Clear Team Cache for Company 2
Cache::forget('ats:teamcounts:2');
Cache::forget('ats:company_rep:defaults:2');
$versionKey = 'ats:teamversion:company_2';
if (Cache::has($versionKey)) {
    Cache::increment($versionKey);
} else {
    Cache::put($versionKey, 2, now()->addDays(30));
}

echo "Cache cleared & version bumped for Company 2.\n";

$elastic = app(ElasticService::class)->getClient();

foreach ([999139, 999131] as $uid) {
    $doc = $elastic->get(['index' => ELASTIC_USERS_LIST, 'id' => (string) $uid]);
    $src = $doc['_source'];
    echo "User {$uid} ({$src['name']}): Status='{$src['status']}', Password=" . (empty($src['password']) ? 'EMPTY' : 'SET') . ", Code=" . var_export($src['email_verification_code'], true) . "\n";
}
