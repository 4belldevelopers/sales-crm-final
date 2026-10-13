<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;

$lock = Cache::lock('test_mutation_lock', 5);
$acquired = $lock->get();
echo "Lock acquired: " . ($acquired ? 'YES' : 'NO') . "\n";
if ($acquired) {
    $lock2 = Cache::lock('test_mutation_lock', 5);
    $acquired2 = $lock2->get();
    echo "Simultaneous second lock acquired: " . ($acquired2 ? 'YES (UNSAFE)' : 'NO (SAFE)') . "\n";
    $lock->release();
    echo "First lock released.\n";
    $acquired3 = $lock2->get();
    echo "Lock acquired after release: " . ($acquired3 ? 'YES' : 'NO') . "\n";
    $lock2->release();
}
