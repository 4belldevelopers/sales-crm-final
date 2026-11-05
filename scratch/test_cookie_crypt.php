<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\TeamInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

$service = app(TeamInvitationService::class);
$envelope = ['invitation_id' => 1, 'tenant_id' => 1, 'session_nonce' => 'abc', 'expires_at' => time() + 900];
$encrypted = Crypt::encrypt($envelope);

$request = Request::create('/register/activate', 'POST');
$request->cookies->set(TeamInvitationService::COOKIE_NAME, $encrypted);

echo "Cookie in request: " . $request->cookie(TeamInvitationService::COOKIE_NAME) . "\n";
$decrypted = Crypt::decrypt($request->cookie(TeamInvitationService::COOKIE_NAME));
print_r($decrypted);
