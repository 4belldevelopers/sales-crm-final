<?php
// scratch/test_blade_compilation.php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Blade;

$views = [
    'resources/views/service_company/account/edit.blade.php',
    'resources/views/service_company/account/onboarding.blade.php',
    'resources/views/service_company/client/create.blade.php',
];

foreach ($views as $view) {
    echo "Compiling $view... ";
    $content = file_get_contents($view);
    $compiled = Blade::compileString($content);
    // Lint compiled PHP
    $tmp = tempnam(sys_get_temp_dir(), 'blade_test_') . '.php';
    file_put_contents($tmp, $compiled);
    $output = shell_exec("php -l \"$tmp\" 2>&1");
    unlink($tmp);
    if (strpos($output, 'No syntax errors detected') !== false) {
        echo "OK (Blade compilation and PHP lint passed!)\n";
    } else {
        echo "FAIL!\nOutput: $output\n";
    }
}
