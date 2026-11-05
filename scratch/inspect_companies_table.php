<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

print_r(Schema::getColumnListing('companies'));

$r = DB::table('companies')->select('id', 'company_name', 'logo')->limit(5)->get();
print_r($r);
