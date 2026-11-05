<?php
$content = file_get_contents('app/Services/Interview/InterviewReadModel.php');
$pos = strpos($content, 'company');
while ($pos !== false) {
    echo substr($content, $pos - 30, 200) . "\n---\n";
    $pos = strpos($content, 'company', $pos + 1);
}
