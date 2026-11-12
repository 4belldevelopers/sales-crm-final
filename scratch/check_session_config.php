<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Cookie: " . config('session.cookie') . "\n";
