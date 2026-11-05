<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('routes'));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (stripos($content, 'teamReg') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
$filesApp = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app'));
foreach ($filesApp as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (stripos($content, 'teamReg') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
