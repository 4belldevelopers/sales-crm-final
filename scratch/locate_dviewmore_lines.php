<?php
foreach (['ServiceCompanyClientController.php', 'ServiceCompanyConsultancyController.php'] as $f) {
    $path = __DIR__ . '/../app/Http/Controllers/ServiceCompany/' . $f;
    $lines = file($path);
    echo "=== $f ===\n";
    foreach ($lines as $num => $line) {
        if (stripos($line, 'dviewMore') !== false) {
            echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
            // print 10 lines around
            for ($k = max(0, $num - 5); $k <= min(count($lines) - 1, $num + 10); $k++) {
                echo "   [" . ($k + 1) . "] " . $lines[$k];
            }
        }
    }
}
