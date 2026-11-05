<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app'));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (stripos($content, 'email_verification_expires_at') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
