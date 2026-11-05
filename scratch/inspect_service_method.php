<?php
$content = file_get_contents('app/Services/CompanyRepresentativeService.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'setUserCommunicationRoles') !== false) {
        for ($i = max(0, $num - 2); $i < min(count($lines), $num + 40); $i++) {
            echo ($i + 1) . ": " . $lines[$i] . "\n";
        }
        break;
    }
}
