<?php
$lines = file('resources/views/saas-front/teamReg.blade.php');
foreach ($lines as $i => $l) {
    if (stripos($l, '<form') !== false || stripos($l, 'action') !== false || stripos($l, 'ajax') !== false || stripos($l, 'route(') !== false || stripos($l, 'url(') !== false) {
        echo ($i + 1) . ": " . trim($l) . "\n";
    }
}
