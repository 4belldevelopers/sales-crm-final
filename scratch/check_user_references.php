<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$userId = 99992;
$tables = ['jobs', 'job_assign', 'job_applications', 'cv_assign_job', 'interviews', 'clients'];
foreach ($tables as $t) {
    if (\Illuminate\Support\Facades\Schema::hasTable($t)) {
        $cols = \Illuminate\Support\Facades\Schema::getColumnListing($t);
        foreach ($cols as $c) {
            if (strpos($c, 'user') !== false || strpos($c, 'recruiter') !== false || strpos($c, 'created_by') !== false || strpos($c, 'spok') !== false) {
                $count = DB::table($t)->where($c, $userId)->count();
                if ($count > 0) {
                    echo "Table $t.$c has $count rows for user $userId\n";
                }
            }
        }
    }
}
