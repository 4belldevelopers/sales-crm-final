<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($files as $file) {
    if ($file->isFile() && stripos($file->getFilename(), 'teamreg') !== false) {
        echo $file->getPathname() . "\n";
    }
}
