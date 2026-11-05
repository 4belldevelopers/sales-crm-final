<?php
$tabAct = file_get_contents(__DIR__ . '/../resources/views/service_company/client/components/tab-activity.blade.php');
preg_match_all('/\$\.ajax\([\s\S]*?\}\);/i', $tabAct, $ajaxMatches);
foreach ($ajaxMatches[0] as $idx => $aj) {
    echo "=== AJAX Call #$idx ===\n" . substr($aj, 0, 800) . "\n\n";
}
