<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

$rawToken = '647e8fd86748ff06a4e8a053b3c1be1923429d34668d9e33f5bc772bbff23c45';
$req = Request::create('/register?token=' . $rawToken, 'GET');
$req->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$controller = app(RegisterController::class);
$res = $controller->showRegistrationForm($req);

echo "Status Code: " . $res->getStatusCode() . "\n";
$html = $res->getContent();

preg_match('/<form[^>]+id="kt_sign_up_form"[^>]*style="([^"]*)"/', $html, $mSignup);
preg_match('/<form[^>]+id="kt_team_activate_form"[^>]*style="([^"]*)"/', $html, $mTeam);

echo "Signup Style: " . ($mSignup[1] ?? 'none') . "\n";
echo "Team Style:   " . ($mTeam[1] ?? 'none') . "\n";
echo "Contains email kajalverma5057@gmail.com: " . (strpos($html, 'kajalverma5057@gmail.com') !== false ? 'YES' : 'NO') . "\n";
