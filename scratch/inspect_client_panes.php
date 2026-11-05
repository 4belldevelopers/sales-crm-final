<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
foreach (['recent_activity', 'requisitions', 'candidate_list', 'interview_tracker', 'candidate_joining', 'candidateDashboard'] as $tab) {
    preg_match('/<div[^>]+id=["\']' . $tab . '["\'][^>]*>(.*?)<\/div>\s*(?:<\!--|\s*<div class=["\']tab-pane)/s', $content, $m);
    if ($m) {
        echo "=== Tab: $tab ===\n";
        echo substr(trim($m[1]), 0, 300) . "\n...\n";
    }
}
