<?php
$content = file_get_contents('app/Services/ElasticService.php');
preg_match_all('/public function ([a-zA-Z0-9_]+)\b/', $content, $m);
print_r($m[1]);
