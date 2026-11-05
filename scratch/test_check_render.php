<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bands = \App\Services\CommercialReadModelService::getCommercialExperienceBands();
$countries = \App\Services\CommercialReadModelService::getMasterCountries();
$data = [
    'sectionCheck' => 2,
    'commercialExperienceBands' => $bands,
    'countries' => $countries,
    'commericials' => collect([[
        'id' => 1,
        'rateType' => 1, // Fixed fee
        'sal_mode' => 0,
        'commericial_details' => [
            ['exp' => 0, 'rate' => 8.33],
            ['exp' => 1, 'rate' => 8.33],
        ],
        'commericial_countries' => [],
    ]]),
    'currencyList' => \App\Services\CurrencyService::getSettingCurrenciesList(),
    'defaultCurrencyKey' => 4,
];

$html = view('service_company.jobs.services_view', $data)->render();
file_put_contents('scratch/test_fixed_render.html', $html);

// Check Card 1 and Card 2 options and values
preg_match_all('/<select[^>]*data-range-applied-on[^>]*>(.*?)<\/select>/s', $html, $matches);
echo "Range applied on selects count: " . count($matches[0]) . PHP_EOL;
foreach ($matches[1] as $idx => $opts) {
    echo "Tier $idx options:\n" . trim($opts) . PHP_EOL;
}

preg_match_all('/<input[^>]*data-range-rate[^>]*value="([^"]*)"/s', $html, $rateMatches);
echo "Range rate values: " . implode(', ', $rateMatches[1]) . PHP_EOL;

preg_match('/<input[^>]*id="commercial_default_rate"[^>]*value="([^"]*)"/', $html, $defaultRateMatch);
echo "Default rate value: " . ($defaultRateMatch[1] ?? 'NOT FOUND') . PHP_EOL;
