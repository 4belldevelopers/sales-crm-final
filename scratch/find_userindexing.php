<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app'));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'userIndexingupload') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
