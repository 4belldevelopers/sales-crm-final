<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "disposable_email_domains exists: " . (Schema::hasTable('disposable_email_domains') ? 'YES' : 'NO') . "\n";
echo "companies exists: " . (Schema::hasTable('companies') ? 'YES' : 'NO') . "\n";
echo "users exists: " . (Schema::hasTable('users') ? 'YES' : 'NO') . "\n";
