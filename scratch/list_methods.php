<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php');
preg_match_all('/public\s+function\s+([a-zA-Z0-9_]+)/', $content, $m);
foreach ($m[1] as $fn) {
    echo $fn . "\n";
}
