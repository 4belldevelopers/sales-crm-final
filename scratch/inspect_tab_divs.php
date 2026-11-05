<?php
function findTabDiv($file, $id) {
    $content = file_get_contents($file);
    $pos = strpos($content, 'id="' . $id . '"');
    if ($pos === false) {
        $pos = strpos($content, "id='" . $id . "'");
    }
    if ($pos === false) return "NOT FOUND";
    return substr($content, $pos, 800);
}

$cFile = __DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php';
$aFile = __DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php';

$tabs = ['requisitions', 'candidate_list', 'interview_tracker', 'candidate_joining', 'candidateDashboard'];
foreach ($tabs as $t) {
    echo "========================================================\n";
    echo "TAB: #$t\n";
    echo "========================================================\n";
    echo "--- CLIENT ---\n";
    echo findTabDiv($cFile, $t) . "\n\n";
    echo "--- AGENCY ---\n";
    echo findTabDiv($aFile, $t) . "\n\n";
}
