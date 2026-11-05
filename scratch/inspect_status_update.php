<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php');
$lines = explode("\n", $content);
foreach (['statusUpdate', 'reSendmail'] as $method) {
    echo "=== $method ===\n";
    foreach ($lines as $num => $line) {
        if (strpos($line, "function $method") !== false) {
            for ($i = max(0, $num - 1); $i < min(count($lines), $num + 35); $i++) {
                echo ($i + 1) . ": " . $lines[$i] . "\n";
            }
            break;
        }
    }
}
