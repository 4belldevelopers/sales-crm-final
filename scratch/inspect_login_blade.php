<?php
$lines = file('resources/views/auth/login.blade.php');
foreach ($lines as $i => $l) {
    if (stripos($l, 'initialMode') !== false || stripos($l, 'signup') !== false || stripos($l, 'action=') !== false || stripos($l, 'company-register') !== false || stripos($l, 'CompanyRegistration') !== false) {
        echo ($i + 1) . ": " . trim($l) . "\n";
    }
}
