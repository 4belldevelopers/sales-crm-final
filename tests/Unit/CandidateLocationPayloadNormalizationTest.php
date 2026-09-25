<?php

namespace Tests\Unit;

use App\Http\Controllers\ServiceCompany\ServiceCompanyJobApplicationController;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use Tests\TestCase;

/**
 * Prevents candidate submission failures when Current Location arrives in supported UI or API shapes.
 */
class CandidateLocationPayloadNormalizationTest extends TestCase
{
    #[DataProvider('supportedLocationPayloads')]
    public function test_it_normalizes_supported_single_location_payloads(mixed $payload): void
    {
        $this->assertSame([
            'city' => 'Gurgaon/gurugram',
            'state' => 'Haryana',
            'country' => 'India',
        ], $this->normalize($payload));
    }

    public static function supportedLocationPayloads(): array
    {
        return [
            'tagify JSON array' => ['[{"value":"Gurgaon/gurugram | Haryana | India"}]'],
            'plain single-select value' => ['Gurgaon/gurugram | Haryana | India'],
            'JSON string value' => ['"Gurgaon/gurugram | Haryana | India"'],
            'API associative array' => [['value' => 'Gurgaon/gurugram | Haryana | India']],
            'API list array' => [[['value' => 'Gurgaon/gurugram | Haryana | India']]],
            'API string list' => [['Gurgaon/gurugram | Haryana | India']],
            'API object list' => [[(object) ['value' => 'Gurgaon/gurugram | Haryana | India']]],
        ];
    }

    #[DataProvider('emptyOrInvalidLocationPayloads')]
    public function test_it_safely_rejects_empty_or_invalid_location_payloads(mixed $payload): void
    {
        $this->assertNull($this->normalize($payload));
    }

    public static function emptyOrInvalidLocationPayloads(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
            'empty JSON list' => ['[]'],
            'malformed JSON-like value' => ['[{invalid}]'],
            'missing state and country' => ['Gurgaon/gurugram'],
            'missing country' => ['Gurgaon/gurugram | Haryana'],
            'empty component' => ['Gurgaon/gurugram | | India'],
        ];
    }

    private function normalize(mixed $payload): ?array
    {
        $controller = (new ReflectionClass(ServiceCompanyJobApplicationController::class))
            ->newInstanceWithoutConstructor();
        $method = (new ReflectionClass($controller))->getMethod('normalizedCandidateLocation');

        return $method->invoke($controller, $payload);
    }
}
