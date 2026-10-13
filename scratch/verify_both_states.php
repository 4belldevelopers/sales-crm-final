<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Auth::login($user);

$job = DB::table('jobs')->where('id', 8)->first();
echo "Job 8 in DB: public_market_status = " . $job->public_market_status . PHP_EOL;

$controller = app()->make(App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class);
$controller->user = $user;

// Render when public_market_status is 0
DB::table('jobs')->where('id', 8)->update(['public_market_status' => 0]);
App\Helper\GeneralMethod::invalidateSpartnerlistingCache($user->id);
$res0 = $controller->spartnerlisting(8)->getData(true);
$h0 = $res0['view'];

echo PHP_EOL . "--- State 0 (Unpublished / Not Live) ---" . PHP_EOL;
preg_match('/id="sp_list_one"[^>]*class="([^"]*)"[^>]*style="([^"]*)"/i', $h0, $s1_0);
preg_match('/id="sp_list_two"[^>]*class="([^"]*)"[^>]*style="([^"]*)"/i', $h0, $s2_0);
echo "sp_list_one (Publish): class=[{$s1_0[1]}], style=[{$s1_0[2]}]" . PHP_EOL;
echo "sp_list_two (Unpublish): class=[{$s2_0[1]}], style=[{$s2_0[2]}]" . PHP_EOL;

// Render when public_market_status is 2
DB::table('jobs')->where('id', 8)->update(['public_market_status' => 2]);
App\Helper\GeneralMethod::invalidateSpartnerlistingCache($user->id);
$res2 = $controller->spartnerlisting(8)->getData(true);
$h2 = $res2['view'];

echo PHP_EOL . "--- State 2 (Published / Live on Marketplace) ---" . PHP_EOL;
preg_match('/id="sp_list_one"[^>]*class="([^"]*)"[^>]*style="([^"]*)"/i', $h2, $s1_2);
preg_match('/id="sp_list_two"[^>]*class="([^"]*)"[^>]*style="([^"]*)"/i', $h2, $s2_2);
echo "sp_list_one (Publish): class=[{$s1_2[1]}], style=[{$s1_2[2]}]" . PHP_EOL;
echo "sp_list_two (Unpublish): class=[{$s2_2[1]}], style=[{$s2_2[2]}]" . PHP_EOL;

// Revert back
DB::table('jobs')->where('id', 8)->update(['public_market_status' => 0]);
App\Helper\GeneralMethod::invalidateSpartnerlistingCache($user->id);
echo PHP_EOL . "Reverted Job 8 back to public_market_status = 0 in database." . PHP_EOL;
