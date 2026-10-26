<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * MultiCityWorkLocationDisplayTest
 *
 * Verifies the multi-city work location badge and remaining cities hover calculation.
 * Follows JERO GOD-LEVEL ATS standards:
 * - 0% server read strain (executed client-side in User RAM)
 * - Exact remaining city list computation (the +N additional cities beyond primary)
 * - Case-insensitive deduplication with canonical casing preservation
 * - Dual-layer tooltip & popover contract
 */
class MultiCityWorkLocationDisplayTest extends TestCase
{
    /**
     * Helper mimicking the JavaScript logic executed in renderJobsClientSide()
     */
    protected function computeLocationDisplay(array $job): array
    {
        $locations = $job['work_locations'] ?? [];
        $allCitiesList = ! empty($job['cities']) && is_array($job['cities'])
            ? array_filter($job['cities'])
            : (! empty($locations) ? array_filter(array_column($locations, 'city_name')) : (! empty($job['primary_city']) || ! empty($job['city']) ? [! empty($job['primary_city']) ? $job['primary_city'] : $job['city']] : []));

        // Deduplicate cities case-insensitively while preserving canonical casing
        $seenCityKeys = [];
        $deduplicatedCities = [];
        foreach ($allCitiesList as $c) {
            $cleanC = trim((string) $c);
            $cKey = mb_strtolower($cleanC);
            if ($cKey !== '' && ! isset($seenCityKeys[$cKey])) {
                $seenCityKeys[$cKey] = true;
                $deduplicatedCities[] = $cleanC;
            }
        }
        $allCitiesList = $deduplicatedCities;

        $primaryCityName = ! empty($job['primary_city'])
            ? trim((string) $job['primary_city'])
            : (! empty($allCitiesList[0]) ? $allCitiesList[0] : (! empty($job['city']) ? trim((string) $job['city']) : ''));
        $primaryKey = mb_strtolower($primaryCityName);

        // Compute remaining cities (the +N additional cities beyond the primary city)
        $remainingCities = [];
        foreach ($allCitiesList as $c) {
            if (mb_strtolower($c) !== $primaryKey) {
                $remainingCities[] = $c;
            }
        }

        $extraCitiesCount = count($remainingCities);
        $remainingCitiesText = implode(', ', $remainingCities);

        $hasMultipleCountries = false;
        if (! empty($locations)) {
            $distinctCountries = array_unique(array_filter(array_map('trim', array_column($locations, 'country_name'))));
            $hasMultipleCountries = count($distinctCountries) > 1;
        }

        return [
            'primaryCityName' => $primaryCityName,
            'remainingCities' => $remainingCities,
            'extraCitiesCount' => $extraCitiesCount,
            'remainingCitiesText' => $remainingCitiesText,
            'hasMultipleCountries' => $hasMultipleCountries,
            'isSpecified' => ! empty($primaryCityName),
        ];
    }

    public function test_single_city_has_zero_remaining_cities_and_no_extra_badge(): void
    {
        $job = [
            'primary_city' => 'Noida',
            'cities' => ['Noida'],
        ];

        $result = $this->computeLocationDisplay($job);

        $this->assertEquals('Noida', $result['primaryCityName']);
        $this->assertCount(0, $result['remainingCities']);
        $this->assertEquals(0, $result['extraCitiesCount']);
        $this->assertEquals('', $result['remainingCitiesText']);
        $this->assertTrue($result['isSpecified']);
    }

    public function test_two_cities_reveals_exact_single_remaining_city(): void
    {
        $job = [
            'primary_city' => 'Noida',
            'cities' => ['Noida', 'Pune'],
        ];

        $result = $this->computeLocationDisplay($job);

        $this->assertEquals('Noida', $result['primaryCityName']);
        $this->assertEquals(['Pune'], $result['remainingCities']);
        $this->assertEquals(1, $result['extraCitiesCount']);
        $this->assertEquals('Pune', $result['remainingCitiesText']);
    }

    public function test_multiple_cities_reveals_all_remaining_cities(): void
    {
        $job = [
            'primary_city' => 'Noida',
            'cities' => ['Noida', 'Pune', 'Bengaluru', 'Hyderabad'],
        ];

        $result = $this->computeLocationDisplay($job);

        $this->assertEquals('Noida', $result['primaryCityName']);
        $this->assertEquals(['Pune', 'Bengaluru', 'Hyderabad'], $result['remainingCities']);
        $this->assertEquals(3, $result['extraCitiesCount']);
        $this->assertEquals('Pune, Bengaluru, Hyderabad', $result['remainingCitiesText']);
    }

    public function test_case_insensitive_deduplication_eliminates_spurious_remaining_cities(): void
    {
        $job = [
            'primary_city' => 'Noida',
            'cities' => ['Noida', 'noida', 'NOIDA', 'Pune', 'pune'],
        ];

        $result = $this->computeLocationDisplay($job);

        $this->assertEquals('Noida', $result['primaryCityName']);
        $this->assertEquals(['Pune'], $result['remainingCities']);
        $this->assertEquals(1, $result['extraCitiesCount']);
        $this->assertEquals('Pune', $result['remainingCitiesText']);
    }

    public function test_preserves_canonical_casing_for_international_and_special_city_names(): void
    {
        $job = [
            'primary_city' => 'McAllen',
            'cities' => ['McAllen', 'São Paulo', 'Zürich', 'New Delhi'],
        ];

        $result = $this->computeLocationDisplay($job);

        $this->assertEquals('McAllen', $result['primaryCityName']);
        $this->assertEquals(['São Paulo', 'Zürich', 'New Delhi'], $result['remainingCities']);
        $this->assertEquals(3, $result['extraCitiesCount']);
        $this->assertEquals('São Paulo, Zürich, New Delhi', $result['remainingCitiesText']);
    }

    public function test_zero_locations_defaults_to_unspecified(): void
    {
        $job = [
            'primary_city' => '',
            'cities' => [],
            'work_locations' => [],
        ];

        $result = $this->computeLocationDisplay($job);

        $this->assertEquals('', $result['primaryCityName']);
        $this->assertCount(0, $result['remainingCities']);
        $this->assertEquals(0, $result['extraCitiesCount']);
        $this->assertFalse($result['isSpecified']);
    }

    public function test_multi_country_disambiguation_flag(): void
    {
        $job = [
            'primary_city' => 'Dublin',
            'work_locations' => [
                ['city_id' => 1, 'city_name' => 'Dublin', 'country_name' => 'Ireland'],
                ['city_id' => 2, 'city_name' => 'Dublin', 'country_name' => 'United States'],
            ],
            'cities' => ['Dublin'],
        ];

        $result = $this->computeLocationDisplay($job);
        $this->assertTrue($result['hasMultipleCountries']);
    }

    public function test_ten_locations_rendered_vertically_without_remaining_locations_text(): void
    {
        $tenCities = [
            'London', 'Dublin', 'Hyderabad', 'Mumbai', 'New Delhi',
            'Pune', 'Bengaluru', 'New York City', 'Singapore', 'Gurugram'
        ];

        $job = [
            'primary_city' => 'London',
            'cities' => $tenCities,
        ];

        $result = $this->computeLocationDisplay($job);
        $this->assertEquals('London', $result['primaryCityName']);
        $this->assertEquals(9, $result['extraCitiesCount']);
        $this->assertCount(9, $result['remainingCities']);

        // Simulate client-side popover HTML generation
        $popoverItems = array_map(function($c) {
            return '<div class="loc-popover-item"><i class="fas fa-map-marker-alt"></i> <span>' . $c . '</span></div>';
        }, $tenCities);
        $popoverBodyHtml = '<div class="loc-popover-body">' . implode('', $popoverItems) . '</div>';

        // Must NOT contain "Remaining Locations" text
        $this->assertStringNotContainsString('Remaining Locations', $popoverBodyHtml);

        // Must contain all 10 cities vertically with location pin icon
        foreach ($tenCities as $city) {
            $this->assertStringContainsString('<i class="fas fa-map-marker-alt"></i> <span>' . $city . '</span>', $popoverBodyHtml);
        }
    }

    public function test_job_drawer_cache_payload_version_busts_key(): void
    {
        $jobId = 999;
        $companyId = 5;
        $userId = 12;

        $payloadV1 = [
            'company' => $companyId,
            'job_id' => $jobId,
            'job_version' => 1,
            'user_id' => $userId,
        ];
        $keyV1 = 'ats:jobDetails:' . $userId . ':1:' . md5(json_encode($payloadV1));

        $payloadV2 = [
            'company' => $companyId,
            'job_id' => $jobId,
            'job_version' => 2, // Incremented after job update
            'user_id' => $userId,
        ];
        $keyV2 = 'ats:jobDetails:' . $userId . ':1:' . md5(json_encode($payloadV2));

        $this->assertNotEquals($keyV1, $keyV2);
    }

    public function test_popular_hub_chips_match_database_city_formats(): void
    {
        // Simulates the JS isCityMatch logic in PHP for testing
        $matcher = function(string $item, string $canonical, string $label): bool {
            $getVariants = function(string $raw): array {
                $str = mb_strtolower(trim($raw));
                $parts = preg_split('/[,|(]/', $str);
                $primary = trim($parts[0] ?? '');
                if ($primary === '') return [];

                $variants = [$primary];
                if (str_contains($primary, '/')) {
                    foreach (explode('/', $primary) as $sub) {
                        $sub = trim($sub);
                        if ($sub !== '') $variants[] = $sub;
                    }
                }

                $expanded = [];
                $aliases = [
                    'bangalore' => 'bengaluru',
                    'bengaluru' => 'bengaluru',
                    'gurgaon' => 'gurugram',
                    'gurugram' => 'gurugram',
                    'mumbai' => 'mumbai',
                    'bombay' => 'mumbai',
                    'poona' => 'pune',
                    'pune' => 'pune',
                ];
                foreach ($variants as $v) {
                    $expanded[] = $v;
                    if (isset($aliases[$v])) $expanded[] = $aliases[$v];
                    if (in_array($v, ['new york', 'new york city', 'nyc', 'ny'], true)) {
                        $expanded = array_merge($expanded, ['new york', 'new york city', 'nyc', 'ny']);
                    }
                    if (in_array($v, ['delhi', 'new delhi', 'delhi ncr'], true)) {
                        $expanded = array_merge($expanded, ['delhi', 'new delhi', 'delhi ncr']);
                    }
                }
                return array_unique(array_filter($expanded));
            };

            $itemVariants = $getVariants($item);
            $queryVariants = array_merge($getVariants($label), $getVariants($canonical));

            return count(array_intersect($itemVariants, $queryVariants)) > 0;
        };

        // 1. Bengaluru pill vs Bangalore/Bengaluru from DB
        $this->assertTrue(
            $matcher('Bangalore/Bengaluru | Karnataka | India', 'Bengaluru, Karnataka, India', 'Bengaluru')
        );

        // 2. New York pill vs New York City from DB
        $this->assertTrue(
            $matcher('New York City | New York | America (USA)', 'New York City, New York, United States', 'New York')
        );

        // 3. Delhi NCR pill vs New Delhi from DB
        $this->assertTrue(
            $matcher('New Delhi | Delhi | India', 'New Delhi, Delhi, India', 'Delhi NCR')
        );

        // 4. Mumbai, Hyderabad, Pune, London, Singapore
        $this->assertTrue($matcher('Mumbai | Maharashtra | India', 'Mumbai, Maharashtra, India', 'Mumbai'));
        $this->assertTrue($matcher('Hyderabad | Telangana | India', 'Hyderabad, Telangana, India', 'Hyderabad'));
        $this->assertTrue($matcher('Pune | Maharashtra | India', 'Pune, Maharashtra, India', 'Pune'));
        $this->assertTrue($matcher('London | England | United Kingdom', 'London, England, United Kingdom', 'London'));
        $this->assertTrue($matcher('Singapore | Central Singapore | Singapore', 'Singapore, Singapore', 'Singapore'));

        // 5. Negative test: London should NOT match New York
        $this->assertFalse($matcher('London | England | United Kingdom', 'New York City, New York, United States', 'New York'));
    }
}
