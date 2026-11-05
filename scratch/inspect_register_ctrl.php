<?php
$lines = file('app/Http/Controllers/Auth/RegisterController.php');
for ($i = 0; $i < min(count($lines), 100); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
