<?php

namespace Tests\Unit;

use App\Services\IndustryTaxonomyCompiler;
use App\Services\IndustryTaxonomyService;
use Tests\TestCase;

/**
 * Class IndustryTaxonomyGodLevelTest
 *
 * Tests the Single Source of Truth (SSOT) Canonical Industry Taxonomy across JERO ATS.
 */
class IndustryTaxonomyGodLevelTest extends TestCase
{
    /**
     * Test that compiled manifest contains canonical count and ID 147.
     */
    public function test_compiled_manifest_contains_canonical_count_and_id_147()
    {
        $manifest = IndustryTaxonomyService::getGlobalManifest();

        $this->assertIsArray($manifest);
        $this->assertArrayHasKey('industries', $manifest);
        $this->assertArrayHasKey('sectors', $manifest);

        $industries = $manifest['industries'];
        $this->assertCount(161, $industries, 'Canonical industry taxonomy must contain exactly 161 industries.');

        // Verify ID 147 is canonical Other / Diversified Services
        $otherIndustry = collect($industries)->firstWhere('id', 147);
        $this->assertNotNull($otherIndustry, 'Industry 147 must exist in the compiled manifest.');
        $this->assertEquals('Other / Diversified Services', $otherIndustry['name']);

        // Verify modern 2026 verticals exist (1001 - 1014)
        $aiMl = collect($industries)->firstWhere('id', 1001);
        $this->assertNotNull($aiMl, 'Vertical 1001 (AI/ML) must exist.');
        $this->assertEquals('Artificial Intelligence & Machine Learning (AI/ML)', $aiMl['name']);

        // Verify all sector_ids are strictly 1 through 10
        foreach ($industries as $ind) {
            $this->assertGreaterThanOrEqual(1, $ind['sector_id']);
            $this->assertLessThanOrEqual(10, $ind['sector_id']);
            $this->assertNotEmpty($ind['name']);
            $this->assertNotEmpty($ind['sector_name']);
        }
    }

    /**
     * Test deterministic sector classification rules.
     */
    public function test_deterministic_sector_classification()
    {
        $techResult = IndustryTaxonomyCompiler::classify('Cloud Engineering & DevOps');
        $this->assertEquals(1, $techResult['sector_id']);

        $finResult = IndustryTaxonomyCompiler::classify('Investment Banking');
        $this->assertEquals(2, $finResult['sector_id']);

        $healthResult = IndustryTaxonomyCompiler::classify('Healthcare & Clinical Research');
        $this->assertEquals(3, $healthResult['sector_id']);
    }

    /**
     * Test detectSector fallback handles unlisted industries gracefully.
     */
    public function test_detect_sector_handles_unlisted_industries()
    {
        $detected = IndustryTaxonomyService::detectSector('Unknown Frontier Enterprise');
        $this->assertIsArray($detected);
        $this->assertArrayHasKey('id', $detected);
        $this->assertArrayHasKey('name', $detected);
        $this->assertGreaterThanOrEqual(1, $detected['id']);
        $this->assertLessThanOrEqual(10, $detected['id']);
    }
}
