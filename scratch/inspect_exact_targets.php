<?php
$c = file_get_contents(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');

function showSection($c, $needle, $before = 5, $after = 15) {
    $pos = strpos($c, $needle);
    if ($pos === false) {
        echo "NOT FOUND: $needle\n";
        return;
    }
    $start = max(0, $pos - 200);
    $len = 500;
    echo "=== FOUND: $needle ===\n";
    echo substr($c, $start, $len) . "\n-------------------------\n";
}

showSection($c, "recommendation_relation.final_status' => [1, 2]");
showSection($c, "\$indices = 'ats_candidates_elastic,local_candidates_elastic'");
showSection($c, "\$indexTimeline = ! empty(\$timelineExisting)");
showSection($c, "\$indices = 'ats_jobs_elastic,local_jobs_elastic'");
showSection($c, "\$repUserId = (int) (\$rec['from_user_id'] ?? 0);");
showSection($c, "\$sharedWithAgency = \$isAgencyViewer ? 'Your Agency'");
