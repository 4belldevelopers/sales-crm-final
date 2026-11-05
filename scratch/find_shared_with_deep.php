<?php
$dirs = [__DIR__ . '/../resources', __DIR__ . '/../app', __DIR__ . '/../public'];
foreach ($dirs as $dir) {
    $rdi = new RecursiveDirectoryIterator($dir);
    $rii = new RecursiveIteratorIterator($rdi);
    foreach ($rii as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade', 'js'])) {
            $c = file_get_contents($file->getPathname());
            if (stripos($c, 'shared with') !== false) {
                $lines = explode("\n", $c);
                foreach ($lines as $i => $l) {
                    if (stripos($l, 'shared with') !== false) {
                        echo $file->getFilename() . " L" . ($i+1) . ": " . trim($l) . "\n";
                    }
                }
            }
        }
    }
}
