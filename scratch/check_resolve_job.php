<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$s = app(App\Services\ServiceCompany\ClientActivityReadService::class);
$ref = new ReflectionClass($s);
$m = $ref->getMethod('resolveJobProfiles');
$m->setAccessible(true);
$res = $m->invoke($s, [2], 2, 2, 'agency');
print_r($res);
