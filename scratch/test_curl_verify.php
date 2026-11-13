<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::where('is_superadmin', 1)->first();
$s = app('session.store');
$s->start();
Auth::guard('web')->setUser($user);
$s->put(Auth::guard('web')->getName(), $user->id);
$csrf = $s->token();
$s->save();

$sid = $s->getId();
$enc = app('encrypter');
$val = \Illuminate\Cookie\CookieValuePrefix::create('jero_session', $enc->getKey()) . $sid;
$cookie = $enc->encrypt($val, false);

$targetUrl = 'http://127.0.0.1/git/JWMaincode28June2026/super-admin/company/3/toggle-verify';
$ch = curl_init($targetUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Cookie: jero_session=' . urlencode($cookie),
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
        'X-CSRF-TOKEN: ' . $csrf,
    ],
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => $csrf,
        'idempotency_key' => 'test_cli_' . time(),
    ]),
]);

$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP CODE: {$code}\n";
echo "RESPONSE: {$res}\n";
