<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helper\GeneralMethod;

GeneralMethod::userIndexingupload(99992);
echo "Indexed user 99992 into Elasticsearch successfully\n";
