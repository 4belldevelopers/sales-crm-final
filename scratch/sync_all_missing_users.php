<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(App\Services\ElasticService::class);
$users = DB::table('users')->select('id', 'name', 'email', 'company_id')->get();
echo "Total users in MySQL: " . $users->count() . "\n";

$missing = [];
foreach ($users as $u) {
    try {
        $res = $elastic->getClient()->get([
            'index' => ELASTIC_USERS_LIST,
            'id' => (string) $u->id,
        ]);
    } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
        $missing[] = $u;
    } catch (\Throwable $e) {
        $missing[] = $u;
    }
}

echo "Missing users count in ES: " . count($missing) . "\n";
foreach ($missing as $m) {
    echo "Missing: ID {$m->id}, {$m->name}, {$m->email}, Company: {$m->company_id}\n";
    // Sync immediately!
    App\Helper\GeneralMethod::userIndexingupload((int) $m->id);
    echo "  -> Synced to ES!\n";
}
