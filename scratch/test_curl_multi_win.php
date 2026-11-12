<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

$user = User::where('is_superadmin', 1)->first();
$sessionHandler = app('session.store');
$sessionHandler->start();
Auth::guard('web')->setUser($user);
$guardKey = Auth::guard('web')->getName();
$sessionHandler->put($guardKey, $user->id);
$sessionHandler->save();

$sid = $sessionHandler->getId();
$encrypter = app('encrypter');
$rawCookieVal = \Illuminate\Cookie\CookieValuePrefix::create('jero_session', $encrypter->getKey()) . $sid;
$signedCookie = $encrypter->encrypt($rawCookieVal, false);

$cookieHeader = 'Cookie: jero_session=' . urlencode($signedCookie);
$url = 'http://127.0.0.1/git/JWMaincode28June2026/super-admin/countaccountviewmore/1';

echo "Testing 20 concurrent HTTP requests with Windows curl_multi fix...\n";
$mh = curl_multi_init();
$handles = [];

for ($i = 0; $i < 20; $i++) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [$cookieHeader, 'Accept: application/json', 'X-Requested-With: XMLHttpRequest'],
        CURLOPT_TIMEOUT => 10,
    ]);
    curl_multi_add_handle($mh, $ch);
    $handles[] = $ch;
}

$t0 = microtime(true);
$stillRunning = null;
do {
    $mrc = curl_multi_exec($mh, $stillRunning);
    if ($stillRunning > 0) {
        if (curl_multi_select($mh, 0.05) === -1) {
            usleep(2000);
        }
    }
} while ($stillRunning > 0 && $mrc == CURLM_OK);

$elapsed = microtime(true) - $t0;
echo sprintf("Finished 20 concurrent requests in %.3f seconds (%.1f req/sec)!\n", $elapsed, 20 / $elapsed);

foreach ($handles as $ch) {
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Status: {$code} | ";
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);
echo "\nDone!\n";
