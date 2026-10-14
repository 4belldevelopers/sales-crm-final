<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $client = app(App\Services\ElasticService::class)->getClient();
    $health = $client->cluster()->health();
    echo "ES Health: " . json_encode($health) . "\n";
    
    $skillsMapping = $client->indices()->getMapping(['index' => ELASTIC_COMPANY_SKILLS]);
    echo "Skills Mapping: " . json_encode($skillsMapping) . "\n";
    
    $companiesCount = $client->count(['index' => ELASTIC_COMPANIES_INDEX]);
    echo "Companies Count: " . json_encode($companiesCount) . "\n";
    
    $skillsCount = $client->count(['index' => ELASTIC_COMPANY_SKILLS]);
    echo "Skills Count: " . json_encode($skillsCount) . "\n";
} catch (\Throwable $e) {
    echo "ES Exception: " . $e->getMessage() . "\n";
}
