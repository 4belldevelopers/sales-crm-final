<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cookieVal = "eyJpdiI6IjkwTEY2YnAvL01CY1c2WHJvd1hhd3c9PSIsInZhbHVlIjoiMFo3cEtIK1NMTzNTZ1U5NEo4TnZjT2NkSnJiWE9vRDFkSTNCQVFQQzhsWVI2MC9GNUhDWWMvK2RzRkxZeUhoU2swZ21Za2d6Kzh5dDNDUFBGNmY0QkdKRDRtOW5JK1dyVVFNcHJYQ3RGWVRydWxGVEJqV3dyZnNuVG1HSjlPN0QiLCJtYWMiOiI2OWY3YWI5ZWQ3NjNiMzc0MTdiZDg4YmNiNzMwMzMyNmMzNWM3OGM2MGIwMmU4ZTNhMjg0NTNkYmQ2MjUzNzY4IiwidGFnIjoiIn0=";

$decrypted = \Illuminate\Support\Facades\Crypt::decrypt($cookieVal, false);
echo "Decrypted session ID: " . $decrypted . "\n";

// Let's see if this session exists in Redis
$redis = \Illuminate\Support\Facades\Redis::connection();
$keys = $redis->keys('*' . $decrypted . '*');
echo "Redis keys matching session: " . json_encode($keys) . "\n";
