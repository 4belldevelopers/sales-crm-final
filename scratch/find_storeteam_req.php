<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Http/Requests'));
foreach ($files as $f) {
    if ($f->isFile() && stripos($f->getFilename(), 'StoreTeam') !== false) {
        echo $f->getPathname() . "\n";
        echo file_get_contents($f->getPathname()) . "\n";
    }
}
