<?php
require __DIR__ . '/../vendor/autoload.php';
$ref = new ReflectionClass(\App\Services\ElasticJobIndex::class);
foreach ($ref->getMethods() as $m) {
    echo $m->getName() . "\n";
}
