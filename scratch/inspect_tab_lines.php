<?php
$content = file_get_contents('resources/views/service_company/consultancy/dviewMore.blade.php');
$tabs = ['overview', 'recent_activity', 'contacts', 'requisitions', 'candidate_list', 'interview_tracker', 'candidate_joining', 'candidateDashboard'];

foreach ($tabs as $t) {
    $pos = strpos($content, 'id="' . $t . '"');
    if ($pos !== false) {
        $line = substr_count(substr($content, 0, $pos), "\n") + 1;
        echo "Tab '$t' begins around line $line\n";
    }
}
