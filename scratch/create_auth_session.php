<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

$user = User::where('is_superadmin', 1)->first();

$session = app('session.store');
$session->start();
Auth::guard('web')->setUser($user);
$guardName = Auth::guard('web')->getName(); // e.g. login_web_...
$session->put($guardName, $user->getAuthIdentifier());
$session->save();

$sessionId = $session->getId();
$encryptedCookie = Crypt::encrypt($sessionId, false);

echo "Guard key: {$guardName} = " . $user->getAuthIdentifier() . "\n";
echo "Session ID: {$sessionId}\n";
echo "Encrypted Cookie: {$encryptedCookie}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/git/JWMaincode28June2026/super-admin/countaccountviewmore/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Cookie: ' . config('session.cookie') . '=' . urlencode($encryptedCookie),
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
$resp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: {$httpCode}\n";
echo "Response: " . substr($resp, 0, 300) . "\n";
