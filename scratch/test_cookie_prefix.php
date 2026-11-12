<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cookieVal = "eyJpdiI6IjkwTEY2YnAvL01CY1c2WHJvd1hhd3c9PSIsInZhbHVlIjoiMFo3cEtIK1NMTzNTZ1U5NEo4TnZjT2NkSnJiWE9vRDFkSTNCQVFQQzhsWVI2MC9GNUhDWWMvK2RzRkxZeUhoU2swZ21Za2d6Kzh5dDNDUFBGNmY0QkdKRDRtOW5JK1dyVVFNcHJYQ3RGWVRydWxGVEJqV3dyZnNuVG1HSjlPN0QiLCJtYWMiOiI2OWY3YWI5ZWQ3NjNiMzc0MTdiZDg4YmNiNzMwMzMyNmMzNWM3OGM2MGIwMmU4ZTNhMjg0NTNkYmQ2MjUzNzY4IiwidGFnIjoiIn0=";

$encrypter = app('encrypter');
$cookiePrefix = \Illuminate\Cookie\CookieValuePrefix::create('jero_session', $encrypter->getKey());
echo "Prefix: " . $cookiePrefix . "\n";

// Let's create an authenticated session and format the cookie properly!
$user = App\Models\User::where('is_superadmin', 1)->first();
$sessionHandler = app('session.store');
$sessionHandler->start();
Auth::guard('web')->setUser($user);
$guardKey = Auth::guard('web')->getName();
$sessionHandler->put($guardKey, $user->id);
$sessionHandler->save();

$sid = $sessionHandler->getId();
echo "Created session id: " . $sid . "\n";

// Format cookie with prefix:
$rawCookieVal = \Illuminate\Cookie\CookieValuePrefix::create('jero_session', $encrypter->getKey()) . $sid;
$signedEncryptedCookie = $encrypter->encrypt($rawCookieVal, false);

echo "Signed Encrypted Cookie: " . $signedEncryptedCookie . "\n";

// Now test with curl!
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/git/JWMaincode28June2026/super-admin/countaccountviewmore/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Cookie: jero_session=' . urlencode($signedEncryptedCookie),
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
$resp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
echo "Response: " . substr($resp, 0, 300) . "\n";
