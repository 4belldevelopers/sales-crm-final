<?php
$content = file_get_contents('routes/service.php');
preg_match_all('/Route::[^\n]+(?:consultancy|agency)[^\n]+/i', $content, $m);
foreach ($m[0] as $match) {
    echo "$match\n";
}
