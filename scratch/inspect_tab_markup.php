<?php
$clientDrawer = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$agencyDrawer = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');

function getDivById($html, $id) {
    $pos = strpos($html, 'id="' . $id . '"');
    if ($pos === false) $pos = strpos($html, "id='$id'");
    if ($pos === false) return "NOT FOUND";
    return substr($html, $pos, 2500);
}

$tabs = ['requisitions', 'candidate_list', 'interview_tracker', 'candidate_joining', 'candidateDashboard'];

$out = "";
foreach ($tabs as $t) {
    $out .= "========================================================\n";
    $out .= "TAB: #$t\n";
    $out .= "========================================================\n";
    $out .= "--- CLIENT ---\n";
    $out .= getDivById($clientDrawer, $t) . "\n\n";
    $out .= "--- AGENCY ---\n";
    $out .= getDivById($agencyDrawer, $t) . "\n\n";
}

file_put_contents(__DIR__ . '/tabs_summary.txt', $out);
echo "Written " . strlen($out) . " bytes\n";
