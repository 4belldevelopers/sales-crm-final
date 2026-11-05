<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

$req = Request::create('http://testing.jero.works/register?token=test', 'GET');
echo "Is secure (http): " . ($req->isSecure() ? 'YES' : 'NO') . "\n";

$reqHttps = Request::create('https://testing.jero.works/register?token=test', 'GET');
echo "Is secure (https): " . ($reqHttps->isSecure() ? 'YES' : 'NO') . "\n";

echo "App URL: " . config('app.url') . "\n";
echo "Session secure: " . (config('session.secure') ? 'YES' : 'NO') . "\n";
echo "Session same_site: " . config('session.same_site') . "\n";
