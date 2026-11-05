<?php
$dir = __DIR__ . '/../app';
$rdi = new RecursiveDirectoryIterator($dir);
$rii = new RecursiveIteratorIterator($rdi);
foreach ($rii as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $c = file_get_contents($file->getPathname());
        if (strpos($c, 'ClientActivityQuery') !== false || strpos($c, 'viewerType') !== false) {
            $lines = explode("\n", $c);
            foreach ($lines as $i => $l) {
                if (strpos($l, 'ClientActivityQuery') !== false || strpos($l, 'viewerType') !== false) {
                    echo $file->getFilename() . " L" . ($i+1) . ": " . trim($l) . "\n";
                }
            }
        }
    }
}
