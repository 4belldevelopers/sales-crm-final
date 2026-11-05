<?php
$routes = [
    'direct-client-candidate-list',
    'direct-client-candidate-join-list',
    'direct-client-candidate-interview-tracker',
    'jobs_side_bar_job_ajax',
    'performance-analytics'
];

$routeFiles = glob('routes/*.php');
foreach ($routeFiles as $rf) {
    $content = file_get_contents($rf);
    foreach ($routes as $r) {
        if (strpos($content, $r) !== false) {
            echo "Found '$r' in $rf\n";
            preg_match_all('/Route::[^\n]+' . preg_quote($r, '/') . '[^\n]+/i', $content, $m);
            foreach ($m[0] as $match) {
                echo "  $match\n";
            }
        }
    }
}
