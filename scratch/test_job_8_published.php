<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Auth::login($user);

// Temporarily set public_market_status = 2 for job 8
DB::table('jobs')->where('id', 8)->update(['public_market_status' => 2]);
App\Helper\GeneralMethod::invalidateSpartnerlistingCache($user->id);

$controller = app()->make(App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class);
$controller->user = $user;

$response = $controller->spartnerlisting(8);
$data = $response->getData(true);
$html = $data['view'] ?? '';

echo "=== WHEN PUBLISHED (public_market_status = 2) ===" . PHP_EOL;
preg_match('/<button[^>]+id="sp_list_one"[^>]*>/i', $html, $m1);
preg_match('/<button[^>]+id="sp_list_two"[^>]*>/i', $html, $m2);
echo "sp_list_one: " . ($m1[0] ?? 'NOT FOUND') . PHP_EOL;
echo "sp_list_two: " . ($m2[0] ?? 'NOT FOUND') . PHP_EOL;

preg_match('/id="marketplace_unpublished_box"[^>]*class="([^"]*)"[^>]*style="([^"]*)"/i', $html, $mb1);
preg_match('/id="marketplace_live_box"[^>]*class="([^"]*)"[^>]*style="([^"]*)"/i', $html, $mb2);
echo "marketplace_unpublished_box class: " . ($mb1[1] ?? 'NOT FOUND') . " | style: " . ($mb1[2] ?? 'NOT FOUND') . PHP_EOL;
echo "marketplace_live_box class: " . ($mb2[1] ?? 'NOT FOUND') . " | style: " . ($mb2[2] ?? 'NOT FOUND') . PHP_EOL;

// Revert job 8 back to public_market_status = 0
DB::table('jobs')->where('id', 8)->update(['public_market_status' => 0]);
App\Helper\GeneralMethod::invalidateSpartnerlistingCache($user->id);
echo "Reverted job 8 back to public_market_status = 0." . PHP_EOL;
