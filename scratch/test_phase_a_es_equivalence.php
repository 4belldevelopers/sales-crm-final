<?php
/**
 * Phase A — Elasticsearch Result Equivalence Audit & Verification Test
 * 
 * Tests original ES queries vs optimized queries on representative test fixtures.
 * Evaluates:
 * 1. Exact skill matches
 * 2. Partial/substring matches
 * 3. Multi-word phrase matches
 * 4. Multi-skill queries
 * 5. Case variations
 * 6. Empty / null / whitespace skills
 * 7. Duplicate skills
 * 8. Tenant isolation (superAdmin & self exclusion)
 * 9. Top-500 candidate cap impact on ranking and eligibility
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helper\GeneralMethod;
use App\Services\ElasticService;

$elasticService = app(ElasticService::class);
$client = $elasticService->getClient();

echo "========================================================================\n";
echo "PHASE A: ELASTICSEARCH RESULT EQUIVALENCE AUDIT & BENCHMARK\n";
echo "========================================================================\n\n";
$company_id = 999;
$superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();
echo "SuperAdmin Company IDs: " . json_encode($superAdminCompanyIds) . "\n\n";

/**
 * Executes Original Query (Wildcard only, no cap)
 */
function runOriginalSkillQuery($client, array $skillsList, int $company_id, array $superAdminCompanyIds): array {
    $skillQueries = [];
    foreach ($skillsList as $skill) {
        if (!empty($skill)) {
            $skillQueries[] = [
                'wildcard' => [
                    'skill_name' => [
                        'value' => '*' . $skill . '*',
                    ],
                ],
            ];
        }
    }

    if (empty($skillQueries)) {
        return ['count' => 0, 'company_matches' => []];
    }

    $params = [
        'index' => ELASTIC_COMPANY_SKILLS,
        'body' => [
            'size' => 10000,
            '_source' => ['company_id', 'skill_name'],
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => $skillQueries,
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                    'must_not' => array_merge(
                        [['term' => ['company_id' => $company_id]]],
                        array_map(fn($saId) => ['term' => ['company_id' => $saId]], $superAdminCompanyIds)
                    ),
                ],
            ],
        ],
    ];

    try {
        $res = $client->search($params);
        $hits = $res['hits']['hits'] ?? [];
        $companySkillMatchCount = [];
        $skillArrCheck = [];
        foreach ($hits as $hit) {
            $source = $hit['_source'];
            $cId = (int)$source['company_id'];
            $sName = strtolower(trim($source['skill_name']));
            $key = "{$cId}_{$sName}";
            if (!in_array($key, $skillArrCheck, true)) {
                $skillArrCheck[] = $key;
                $companySkillMatchCount[$cId] = ($companySkillMatchCount[$cId] ?? 0) + 1;
            }
        }
        return ['count' => count($hits), 'company_matches' => $companySkillMatchCount];
    } catch (\Throwable $e) {
        return ['error' => $e->getMessage()];
    }
}

/**
 * Executes Proposed Query (with match_phrase, prefix, and wildcard)
 */
function runProposedSkillQuery($client, array $skillsList, int $company_id, array $superAdminCompanyIds, bool $applyCap = false): array {
    $skillQueries = [];
    foreach ($skillsList as $skill) {
        $trimmedSkill = trim($skill);
        if (!empty($trimmedSkill)) {
            $lowerSkill = strtolower($trimmedSkill);
            $skillQueries[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase' => ['skill_name' => $lowerSkill]],
                        ['prefix' => ['skill_name' => $lowerSkill]],
                        ['wildcard' => ['skill_name' => '*' . $lowerSkill . '*']],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }
    }

    if (empty($skillQueries)) {
        return ['count' => 0, 'company_matches' => []];
    }

    $params = [
        'index' => ELASTIC_COMPANY_SKILLS,
        'body' => [
            'size' => 10000,
            '_source' => ['company_id', 'skill_name'],
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => $skillQueries,
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                    'must_not' => array_merge(
                        [['term' => ['company_id' => $company_id]]],
                        array_map(fn($saId) => ['term' => ['company_id' => $saId]], $superAdminCompanyIds)
                    ),
                ],
            ],
        ],
    ];

    try {
        $res = $client->search($params);
        $hits = $res['hits']['hits'] ?? [];
        $companySkillMatchCount = [];
        $skillArrCheck = [];
        foreach ($hits as $hit) {
            $source = $hit['_source'];
            $cId = (int)$source['company_id'];
            $sName = strtolower(trim($source['skill_name']));
            $key = "{$cId}_{$sName}";
            if (!in_array($key, $skillArrCheck, true)) {
                $skillArrCheck[] = $key;
                $companySkillMatchCount[$cId] = ($companySkillMatchCount[$cId] ?? 0) + 1;
            }
        }

        if ($applyCap && count($companySkillMatchCount) > 500) {
            arsort($companySkillMatchCount);
            $companySkillMatchCount = array_slice($companySkillMatchCount, 0, 500, true);
        }

        return ['count' => count($hits), 'company_matches' => $companySkillMatchCount];
    } catch (\Throwable $e) {
        return ['error' => $e->getMessage()];
    }
}

// 1. Check existing documents in index
$existingSkills = $client->search([
    'index' => ELASTIC_COMPANY_SKILLS,
    'body' => ['size' => 20, 'query' => ['match_all' => (object)[]]]
]);
echo "Existing skills in index:\n";
foreach ($existingSkills['hits']['hits'] ?? [] as $hit) {
    echo "  - Company ID: {$hit['_source']['company_id']}, Skill: '{$hit['_source']['skill_name']}'\n";
}
echo "\n";

// Test Scenarios with actual skills in index ('java', 'aws', 'node', 'spring boot')
$scenarios = [
    'Scenario 1: Exact Match (lowercase: java)' => ['java'],
    'Scenario 2: Exact Match (uppercase: JAVA)' => ['JAVA'],
    'Scenario 3: Partial substring (jav in java)' => ['jav'],
    'Scenario 4: Multi-skill query (java, aws)' => ['java', 'aws'],
    'Scenario 5: Multi-word skill with spaces (spring boot)' => ['spring boot'],
    'Scenario 6: Substring of multi-word skill (boot in spring boot)' => ['boot'],
    'Scenario 7: Mixed Case & Whitespace (  SPRING BOOT  )' => ['  SPRING BOOT  '],
    'Scenario 8: Empty and whitespace skills' => ['', '   '],
    'Scenario 9: Duplicate skills in input (java, JAVA, java)' => ['java', 'JAVA', 'java'],
    'Scenario 10: Non-existent skill (zero match)' => ['nonexistent_skill_xyz_123'],
];

$comparisonResults = [];

foreach ($scenarios as $name => $skills) {
    $cleanSkills = array_filter($skills, fn($s) => $s !== null);
    $orig = runOriginalSkillQuery($client, $cleanSkills, $company_id, $superAdminCompanyIds);
    $prop = runProposedSkillQuery($client, $cleanSkills, $company_id, $superAdminCompanyIds, false);

    $origMatches = $orig['company_matches'] ?? [];
    $propMatches = $prop['company_matches'] ?? [];

    $isEqual = ($origMatches == $propMatches);
    $status = $isEqual ? "PASS (100% IDENTICAL)" : "DIFF DETECTED";

    echo "[$status] $name\n";
    echo "  Original companies matched: " . json_encode($origMatches) . "\n";
    echo "  Proposed companies matched: " . json_encode($propMatches) . "\n";

    if (!$isEqual) {
        echo "  --> NOTE: Diff reason analyzed: " . ($orig['count'] !== $prop['count'] ? "Hit count diff (case sensitivity/multi-word)" : "Company count diff") . "\n";
    }
    echo "\n";

    $comparisonResults[] = [
        'scenario' => $name,
        'input' => $skills,
        'original_matches' => $origMatches,
        'proposed_matches' => $propMatches,
        'equivalent' => $isEqual,
    ];
}

// 2. Synthetic Test: 600 Agencies with Top-500 Cap Equivalence Audit
echo "------------------------------------------------------------------------\n";
echo "AUDITING TOP-500 CAP IMPACT ON 600-AGENCY DATASET\n";
echo "------------------------------------------------------------------------\n";
$syntheticMatches = [];
for ($i = 1; $i <= 600; $i++) {
    // Agencies 1-100: 3 matches, 101-300: 2 matches, 301-600: 1 match
    $matches = ($i <= 100) ? 3 : (($i <= 300) ? 2 : 1);
    $syntheticMatches[$i] = $matches;
}

$withoutCap = $syntheticMatches;
arsort($syntheticMatches);
$withCap = array_slice($syntheticMatches, 0, 500, true);

$omittedCount = count($withoutCap) - count($withCap);
$omittedAgencyIds = array_keys(array_diff_key($withoutCap, $withCap));

echo "Total agencies matching: " . count($withoutCap) . "\n";
echo "Agencies retained under cap: " . count($withCap) . "\n";
echo "Agencies OMITTED from function score boost: " . $omittedCount . "\n";
echo "Sample of omitted agency IDs: " . json_encode(array_slice($omittedAgencyIds, 0, 10)) . "\n";

$capEquivalence = ($omittedCount === 0);
echo "Cap Result Equivalence: " . ($capEquivalence ? "EQUIVALENT" : "VIOLATION (Top-500 cap alters scores for $omittedCount valid agencies)") . "\n";

file_put_contents(
    'scratch/phase_a_es_equivalence_report.json',
    json_encode([
        'timestamp' => date('c'),
        'scenarios' => $comparisonResults,
        'top_500_cap_audit' => [
            'total_tested' => 600,
            'retained' => count($withCap),
            'omitted' => $omittedCount,
            'omitted_agency_ids' => $omittedAgencyIds,
            'decision' => 'REMOVE_CAP_TO_PRESERVE_100_PERCENT_BUSINESS_LOGIC_EQUIVALENCE'
        ]
    ], JSON_PRETTY_PRINT)
);
echo "\nSaved report to scratch/phase_a_es_equivalence_report.json\n";
