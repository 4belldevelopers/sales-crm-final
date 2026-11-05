<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;

echo "=== SIMULATING EXACT USER CLICK ===\n\n";

$rawToken = '647e8fd86748ff06a4e8a053b3c1be1923429d34668d9e33f5bc772bbff23c45';
$url = 'https://testing.jero.works/register?token=' . $rawToken;

// Step 1: User clicks link
$req1 = Request::create($url, 'GET');
$req1->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36');

$controller = app(RegisterController::class);
$res1 = $controller->showRegistrationForm($req1);

echo "Step 1 Response Class: " . get_class($res1) . "\n";
echo "Step 1 Status Code: " . $res1->getStatusCode() . "\n";

$cookies = $res1->headers->getCookies();
echo "Cookies attached in Step 1: " . count($cookies) . "\n";
$envelopeCookie = null;
foreach ($cookies as $c) {
    echo "Cookie: " . $c->getName() . " | Value: " . substr($c->getValue(), 0, 20) . "... | Domain: " . ($c->getDomain() ?: 'null') . " | Path: " . $c->getPath() . " | Secure: " . ($c->isSecure() ? 'YES' : 'NO') . " | HttpOnly: " . ($c->isHttpOnly() ? 'YES' : 'NO') . " | SameSite: " . $c->getSameSite() . "\n";
    if ($c->getName() === \App\Services\TeamInvitationService::COOKIE_NAME) {
        $envelopeCookie = $c;
    }
}

if (!$envelopeCookie) {
    die("ERROR: No envelope cookie set!\n");
}

// Step 2: Browser follows 302 redirect to /register
echo "\n--- STEP 2: Browser GET /register with Cookie ---\n";
$req2 = Request::create('https://testing.jero.works/register', 'GET');
$req2->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36');
$req2->cookies->set(\App\Services\TeamInvitationService::COOKIE_NAME, $envelopeCookie->getValue());

$res2 = $controller->showRegistrationForm($req2);
echo "Step 2 Response Class: " . get_class($res2) . "\n";

if ($res2 instanceof \Illuminate\View\View) {
    $viewData = $res2->getData();
    echo "View Data 'registrationContext':\n";
    print_r($viewData['registrationContext'] ?? 'NOT FOUND');
    
    $html = $res2->render();
    if (strpos($html, 'id="kt_team_activate_form"') !== false) {
        echo "\n[FOUND] #kt_team_activate_form is present in HTML!\n";
    } else {
        echo "\n[MISSING] #kt_team_activate_form is NOT in HTML!\n";
    }
    
    // Check if teamStyle or signupStyle was active
    preg_match('/<form[^>]+id="kt_sign_up_form"[^>]*style="([^"]*)"/', $html, $mSignup);
    preg_match('/<form[^>]+id="kt_team_activate_form"[^>]*style="([^"]*)"/', $html, $mTeam);
    echo "Signup Form Style: " . ($mSignup[1] ?? 'none') . "\n";
    echo "Team Form Style: " . ($mTeam[1] ?? 'none') . "\n";
}
