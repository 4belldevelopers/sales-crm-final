<?php
$lines = file('routes/web.php');
for ($i = 1050; $i < 1110; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
