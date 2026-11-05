<?php
$files = ['app/Console/Kernel.php', 'routes/console.php'];
foreach ($files as $f) {
    if (file_exists($f)) {
        echo "=== $f ===\n";
        echo file_get_contents($f) . "\n";
    }
}
