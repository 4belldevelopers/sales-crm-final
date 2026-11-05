<?php
require __DIR__ . '/../vendor/autoload.php';
$ref = new ReflectionMethod('App\Helper\GeneralMethod', 'companyAsVendor');
$start = $ref->getStartLine();
$end = $ref->getEndLine();
$file = $ref->getFileName();

$lines = file($file);
for ($i = $start - 1; $i < $end; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
