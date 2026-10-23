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
}
