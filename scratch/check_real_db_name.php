<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Default DB connection: " . config('database.default') . "\n";
echo "MySQL DB config database: " . config('database.connections.mysql.database') . "\n";
echo "Active DB name: " . DB::connection()->getDatabaseName() . "\n";
