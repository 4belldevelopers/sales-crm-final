<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Auth::login($user);

$controller = app()->make(App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class);
$controller->user = $user;

$response = $controller->spartnerlisting(8);

if ($response instanceof \Illuminate\Http\JsonResponse) {
    $data = $response->getData(true);
    echo "Status: " . ($data['status'] ?? 'unknown') . PHP_EOL;
    $html = $data['view'] ?? '';
    echo "View length: " . strlen($html) . PHP_EOL;

    preg_match('/<button[^>]+id="sp_list_one"[^>]*>/i', $html, $m1);
    preg_match('/<button[^>]+id="sp_list_two"[^>]*>/i', $html, $m2);
    echo "sp_list_one: " . ($m1[0] ?? 'NOT FOUND') . PHP_EOL;
    echo "sp_list_two: " . ($m2[0] ?? 'NOT FOUND') . PHP_EOL;

    preg_match('/id="marketplace_unpublished_box"[^>]*class="([^"]*)"[^>]*style="([^"]*)"/i', $html, $mb1);
    preg_match('/id="marketplace_live_box"[^>]*class="([^"]*)"[^>]*style="([^"]*)"/i', $html, $mb2);
    echo "marketplace_unpublished_box class: " . ($mb1[1] ?? 'NOT FOUND') . " | style: " . ($mb1[2] ?? 'NOT FOUND') . PHP_EOL;
    echo "marketplace_live_box class: " . ($mb2[1] ?? 'NOT FOUND') . " | style: " . ($mb2[2] ?? 'NOT FOUND') . PHP_EOL;
} else {
    echo "Response is: " . get_class($response) . PHP_EOL;
}
