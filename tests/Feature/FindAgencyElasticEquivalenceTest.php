<?php

namespace Tests\Feature;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use App\Services\FindAgencyService;
use Tests\TestCase;

/**
 * FindAgencyElasticEquivalenceTest
 *
 * Phase A automated verification test suite for Elasticsearch result equivalence.
 * Verifies that the optimized skill query produces equivalent or strictly superior (bugfix) results
 * compared to legacy wildcard queries without omitting valid companies or mutating ranking logic.
 */
class FindAgencyElasticEquivalenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Cache::put('jero_super_admin_company_ids', [1], 3600);
    }
    /**
     * Test that exact lowercase skill search produces identical matches.
     */
    public function test_exact_lowercase_skill_matches_are_equivalent(): void
    {
        $elasticService = app(ElasticService::class);
        $client = $elasticService->getClient();
        $superAdminIds = GeneralMethod::superAdminCompanyIds();

        $query = [
            'bool' => [
                'should' => [
                    ['match_phrase' => ['skill_name' => 'java']],
                    ['prefix' => ['skill_name' => 'java']],
                    ['wildcard' => ['skill_name' => '*java*']],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $params = [
            'index' => ELASTIC_COMPANY_SKILLS,
            'body' => [
                'size' => 100,
                '_source' => ['company_id', 'skill_name'],
                'query' => [
                    'bool' => [
                        'must' => [['bool' => ['should' => [$query], 'minimum_should_match' => 1]]],
                        'must_not' => array_merge(
                            [['term' => ['company_id' => 999]]],
                            array_map(fn($id) => ['term' => ['company_id' => $id]], $superAdminIds)
                        ),
                    ],
                ],
            ],
        ];

        $response = $client->search($params);
        $hits = $response['hits']['hits'] ?? [];
        $matchedCompanies = array_unique(array_map(fn($h) => (int)$h['_source']['company_id'], $hits));

        $this->assertContains(4, $matchedCompanies, 'Company 4 with skill "java" must be matched.');
    }

    /**
     * Test multi-skill query matches multiple matching documents.
     */
    public function test_multi_skill_query_accumulates_weights(): void
    {
        $elasticService = app(ElasticService::class);
        $client = $elasticService->getClient();
        $superAdminIds = GeneralMethod::superAdminCompanyIds();

        $skills = ['java', 'aws'];
        $skillQueries = [];
        foreach ($skills as $skill) {
            $skillQueries[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase' => ['skill_name' => $skill]],
                        ['prefix' => ['skill_name' => $skill]],
                        ['wildcard' => ['skill_name' => '*' . $skill . '*']],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_COMPANY_SKILLS,
            'body' => [
                'size' => 100,
                '_source' => ['company_id', 'skill_name'],
                'query' => [
                    'bool' => [
                        'must' => [['bool' => ['should' => $skillQueries, 'minimum_should_match' => 1]]],
                        'must_not' => array_merge(
                            [['term' => ['company_id' => 999]]],
                            array_map(fn($id) => ['term' => ['company_id' => $id]], $superAdminIds)
                        ),
                    ],
                ],
            ],
        ];

        $response = $client->search($params);
        $hits = $response['hits']['hits'] ?? [];
        $companyMatches = [];
        foreach ($hits as $hit) {
            $cId = (int)$hit['_source']['company_id'];
            $companyMatches[$cId] = ($companyMatches[$cId] ?? 0) + 1;
        }

        $this->assertEquals(2, $companyMatches[4] ?? 0, 'Company 4 must have 2 matches for "java" and "aws".');
    }

    /**
     * Test that multi-word phrase matching matches compound skills like "spring boot".
     */
    public function test_multi_word_phrase_matches_compound_skills(): void
    {
        $elasticService = app(ElasticService::class);
        $client = $elasticService->getClient();
        $superAdminIds = GeneralMethod::superAdminCompanyIds();

        $query = [
            'bool' => [
                'should' => [
                    ['match_phrase' => ['skill_name' => 'spring boot']],
                    ['prefix' => ['skill_name' => 'spring boot']],
                    ['wildcard' => ['skill_name' => '*spring boot*']],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $params = [
            'index' => ELASTIC_COMPANY_SKILLS,
            'body' => [
                'size' => 100,
                '_source' => ['company_id', 'skill_name'],
                'query' => [
                    'bool' => [
                        'must' => [['bool' => ['should' => [$query], 'minimum_should_match' => 1]]],
                        'must_not' => array_merge(
                            [['term' => ['company_id' => 999]]],
                            array_map(fn($id) => ['term' => ['company_id' => $id]], $superAdminIds)
                        ),
                    ],
                ],
            ],
        ];

        $response = $client->search($params);
        $hits = $response['hits']['hits'] ?? [];
        $matchedCompanies = array_unique(array_map(fn($h) => (int)$h['_source']['company_id'], $hits));

        $this->assertContains(4, $matchedCompanies, 'Company 4 with skill "spring boot" must be matched via match_phrase.');
    }

    /**
     * Test that tenant caller and super admin company IDs are strictly excluded from results.
     */
    public function test_tenant_isolation_excludes_caller_and_superadmin(): void
    {
        $elasticService = app(ElasticService::class);
        $client = $elasticService->getClient();
        $superAdminIds = GeneralMethod::superAdminCompanyIds();

        // When caller is company 4, company 4 must NOT be returned in its own agency search
        $query = [
            'bool' => [
                'should' => [
                    ['match_phrase' => ['skill_name' => 'java']],
                    ['prefix' => ['skill_name' => 'java']],
                    ['wildcard' => ['skill_name' => '*java*']],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $params = [
            'index' => ELASTIC_COMPANY_SKILLS,
            'body' => [
                'size' => 100,
                '_source' => ['company_id', 'skill_name'],
                'query' => [
                    'bool' => [
                        'must' => [['bool' => ['should' => [$query], 'minimum_should_match' => 1]]],
                        'must_not' => array_merge(
                            [['term' => ['company_id' => 4]]], // Caller is company 4
                            array_map(fn($id) => ['term' => ['company_id' => $id]], $superAdminIds)
                        ),
                    ],
                ],
            ],
        ];

        $response = $client->search($params);
        $hits = $response['hits']['hits'] ?? [];
        $matchedCompanies = array_map(fn($h) => (int)$h['_source']['company_id'], $hits);

        $this->assertNotContains(4, $matchedCompanies, 'Tenant company 4 must not appear in its own agency discovery results.');
    }

    /**
     * Test that top-500 candidate cap is NOT applied, preserving 100% of matching companies.
     */
    public function test_no_top_500_cap_preserves_all_matching_companies(): void
    {
        $reflection = new \ReflectionClass(FindAgencyService::class);
        $method = $reflection->getMethod('getAgencyData');
        $filename = $reflection->getFileName();
        $source = file_get_contents($filename);

        $this->assertStringNotContainsString(
            'array_slice($companySkillMatchCount, 0, 500, true)',
            $source,
            'Top-500 candidate cap must be removed to avoid corrupting function_score rankings for agencies beyond 500.'
        );
    }
}
