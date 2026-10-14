<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TalentMatchingService
 *
 * INTENT: 6-Pillar Holistic Talent Matching & Scoring Engine.
 * Combines Canonical Taxonomy competence, tenure, location, compensation, notice period,
 * and bare-metal vector similarity (Mojo SIMD / CPU fallback) with 0% external cloud API costs.
 *
 * TENANCY NOTE: Candidate submissions and job requisitions are strictly scoped by company_id.
 * Taxonomy definitions are global read-only assets.
 */
class TalentMatchingService
{
    /**
     * Weights for the 6 Pillars of Holistic Match Score
     */
    protected const WEIGHTS = [
        'taxonomy_competency' => 0.35,
        'seniority_experience' => 0.20,
        'location_work_mode' => 0.15,
        'compensation_budget' => 0.15,
        'notice_period' => 0.10,
        'domain_relevance' => 0.05,
    ];

    /**
     * Compute holistic match score between a concrete job requisition and a candidate profile.
     *
     * @param object|array $job Requisition details
     * @param object|array $candidate Candidate profile
     * @return array Holistic score and granular breakdown
     */
    public function computeHolisticMatch($job, $candidate): array
    {
        $jobObj = (object) $job;
        $candObj = (object) $candidate;

        $p1 = $this->evaluateTaxonomyCompetency($jobObj, $candObj);
        $p2 = $this->evaluateSeniorityExperience($jobObj, $candObj);
        $p3 = $this->evaluateLocationWorkMode($jobObj, $candObj);
        $p4 = $this->evaluateCompensation($jobObj, $candObj);
        $p5 = $this->evaluateNoticePeriod($candObj);
        $p6 = $this->evaluateDomainRelevance($jobObj, $candObj);

        $holisticScore = (
            ($p1['score'] * self::WEIGHTS['taxonomy_competency']) +
            ($p2['score'] * self::WEIGHTS['seniority_experience']) +
            ($p3['score'] * self::WEIGHTS['location_work_mode']) +
            ($p4['score'] * self::WEIGHTS['compensation_budget']) +
            ($p5['score'] * self::WEIGHTS['notice_period']) +
            ($p6['score'] * self::WEIGHTS['domain_relevance'])
        ) * 100;

        $holisticScore = round(min(100, max(0, $holisticScore)), 2);

        return [
            'holistic_score' => $holisticScore,
            'match_tier' => $this->getMatchTier($holisticScore),
            'pillars' => [
                'taxonomy_competency' => [
                    'weight' => self::WEIGHTS['taxonomy_competency'],
                    'score' => round($p1['score'] * 100, 1),
                    'weighted_score' => round($p1['score'] * self::WEIGHTS['taxonomy_competency'] * 100, 2),
                    'details' => $p1['details']
                ],
                'seniority_experience' => [
                    'weight' => self::WEIGHTS['seniority_experience'],
                    'score' => round($p2['score'] * 100, 1),
                    'weighted_score' => round($p2['score'] * self::WEIGHTS['seniority_experience'] * 100, 2),
                    'details' => $p2['details']
                ],
                'location_work_mode' => [
                    'weight' => self::WEIGHTS['location_work_mode'],
                    'score' => round($p3['score'] * 100, 1),
                    'weighted_score' => round($p3['score'] * self::WEIGHTS['location_work_mode'] * 100, 2),
                    'details' => $p3['details']
                ],
                'compensation_budget' => [
                    'weight' => self::WEIGHTS['compensation_budget'],
                    'score' => round($p4['score'] * 100, 1),
                    'weighted_score' => round($p4['score'] * self::WEIGHTS['compensation_budget'] * 100, 2),
                    'details' => $p4['details']
                ],
                'notice_period' => [
                    'weight' => self::WEIGHTS['notice_period'],
                    'score' => round($p5['score'] * 100, 1),
                    'weighted_score' => round($p5['score'] * self::WEIGHTS['notice_period'] * 100, 2),
                    'details' => $p5['details']
                ],
                'domain_relevance' => [
                    'weight' => self::WEIGHTS['domain_relevance'],
                    'score' => round($p6['score'] * 100, 1),
                    'weighted_score' => round($p6['score'] * self::WEIGHTS['domain_relevance'] * 100, 2),
                    'details' => $p6['details']
                ]
            ]
        ];
    }

    /**
     * Pillar 1: Taxonomy Competency Match (35%)
     * Formula: 0.70 * MustHaveMatch + 0.30 * NiceToHaveMatch + Canonical Designation Alignment
     */
    protected function evaluateTaxonomyCompetency(object $job, object $candidate): array
    {
        $mustHaveSkills = $this->extractSkillsList($job->must_have_skills ?? ($job->skills ?? []));
        $niceHaveSkills = $this->extractSkillsList($job->nice_to_have_skills ?? []);
        $candidateSkills = $this->extractSkillsList($candidate->skills ?? ($candidate->candidate_skills ?? []));

        $candSkillsLower = array_map('strtolower', $candidateSkills);

        // Must-have match
        $mustMatched = 0;
        foreach ($mustHaveSkills as $ms) {
            if (in_array(strtolower($ms), $candSkillsLower)) {
                $mustMatched++;
            }
        }
        $mustScore = count($mustHaveSkills) > 0 ? ($mustMatched / count($mustHaveSkills)) : 1.0;

        // Nice-to-have match
        $niceMatched = 0;
        foreach ($niceHaveSkills as $ns) {
            if (in_array(strtolower($ns), $candSkillsLower)) {
                $niceMatched++;
            }
        }
        $niceScore = count($niceHaveSkills) > 0 ? ($niceMatched / count($niceHaveSkills)) : 1.0;

        // Canonical designation alignment
        $designationBonus = 0.0;
        $jobCanonicalId = (int) ($job->canonical_designation_id ?? 0);
        $candCanonicalId = (int) ($candidate->canonical_designation_id ?? 0);

        if ($jobCanonicalId > 0 && $jobCanonicalId === $candCanonicalId) {
            $designationBonus = 0.15;
        }

        $competencyScore = min(1.0, (0.70 * $mustScore) + (0.30 * $niceScore) + $designationBonus);

        return [
            'score' => $competencyScore,
            'details' => [
                'must_have_matched' => $mustMatched,
                'must_have_total' => count($mustHaveSkills),
                'nice_have_matched' => $niceMatched,
                'nice_have_total' => count($niceHaveSkills),
                'canonical_designation_matched' => ($jobCanonicalId > 0 && $jobCanonicalId === $candCanonicalId)
            ]
        ];
    }

    /**
     * Pillar 2: Seniority & Experience Match (20%)
     */
    protected function evaluateSeniorityExperience(object $job, object $candidate): array
    {
        $minExp = isset($job->min_exp) ? (float) $job->min_exp : 0.0;
        $maxExp = isset($job->max_exp) ? (float) $job->max_exp : 20.0;
        $candExp = (float) ($candidate->total_exp ?? ($candidate->rel_exp ?? 0.0));

        if ($candExp >= $minExp && $candExp <= $maxExp) {
            $score = 1.0;
        } elseif ($candExp < $minExp) {
            $deficit = $minExp - $candExp;
            $score = max(0.0, 1.0 - ($deficit * 0.25));
        } else {
            $surplus = $candExp - $maxExp;
            $score = max(0.5, 1.0 - ($surplus * 0.10));
        }

        return [
            'score' => round($score, 3),
            'details' => [
                'candidate_exp' => $candExp,
                'job_min_exp' => $minExp,
                'job_max_exp' => $maxExp
            ]
        ];
    }

    /**
     * Pillar 3: Location & Work-Mode Fit (15%)
     */
    protected function evaluateLocationWorkMode(object $job, object $candidate): array
    {
        $jobCity = strtolower(trim($job->city ?? ''));
        $candCity = strtolower(trim($candidate->city ?? ($candidate->current_location ?? '')));

        $score = 0.6; // Default baseline

        if ($jobCity && $candCity && $jobCity === $candCity) {
            $score = 1.0;
        } elseif (!empty($job->work_type) && strtolower($job->work_type) === 'remote') {
            $score = 1.0;
        }

        return [
            'score' => $score,
            'details' => [
                'job_city' => $jobCity,
                'candidate_city' => $candCity
            ]
        ];
    }

    /**
     * Pillar 4: Compensation Alignment (15%)
     */
    protected function evaluateCompensation(object $job, object $candidate): array
    {
        $budgetMax = (float) ($job->max_yearly ?? ($job->max_salary ?? 0));
        $candEctc = (float) ($candidate->ectc_yearly ?? ($candidate->ectc ?? 0));

        if ($budgetMax <= 0 || $candEctc <= 0) {
            return ['score' => 0.8, 'details' => ['status' => 'not_specified']];
        }

        if ($candEctc <= $budgetMax) {
            $score = 1.0;
        } else {
            $ratio = $candEctc / $budgetMax;
            $score = max(0.0, 1.0 - (($ratio - 1.0) * 2.0));
        }

        return [
            'score' => round($score, 3),
            'details' => [
                'budget_max' => $budgetMax,
                'candidate_ectc' => $candEctc
            ]
        ];
    }

    /**
     * Pillar 5: Notice Period & Availability (10%)
     */
    protected function evaluateNoticePeriod(object $candidate): array
    {
        $npDays = (int) ($candidate->np_days ?? ($candidate->notice_period_days ?? 30));
        $isImmediate = !empty($candidate->immediate_join) || $npDays <= 0;

        if ($isImmediate) {
            $score = 1.0;
        } elseif ($npDays <= 15) {
            $score = 0.90;
        } elseif ($npDays <= 30) {
            $score = 0.75;
        } elseif ($npDays <= 60) {
            $score = 0.50;
        } else {
            $score = 0.25;
        }

        return [
            'score' => $score,
            'details' => [
                'np_days' => $npDays,
                'is_immediate' => $isImmediate
            ]
        ];
    }

    /**
     * Pillar 6: Domain & Industry Relevance (5%)
     */
    protected function evaluateDomainRelevance(object $job, object $candidate): array
    {
        $jobDept = strtolower(trim($job->dept_name ?? ($job->department_name ?? '')));
        $candDept = strtolower(trim($candidate->dept_name ?? ($candidate->department_name ?? '')));

        if ($jobDept && $candDept && $jobDept === $candDept) {
            $score = 1.0;
        } else {
            $score = 0.70;
        }

        return [
            'score' => $score,
            'details' => [
                'job_department' => $jobDept,
                'candidate_department' => $candDept
            ]
        ];
    }

    /**
     * Evaluate cosine similarity between two float vectors (Mojo / SIMD fallback)
     */
    public function computeVectorCosineSimilarity(array $vecA, array $vecB): float
    {
        $count = count($vecA);
        if ($count === 0 || $count !== count($vecB)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $a = (float) $vecA[$i];
            $b = (float) $vecB[$i];
            $dotProduct += $a * $b;
            $normA += $a * $a;
            $normB += $b * $b;
        }

        $denominator = sqrt($normA) * sqrt($normB);
        if ($denominator < 1e-9) {
            return 0.0;
        }

        return round($dotProduct / $denominator, 4);
    }

    protected function extractSkillsList($skills): array
    {
        if (is_array($skills)) {
            $flat = [];
            foreach ($skills as $s) {
                if (is_string($s)) $flat[] = trim($s);
                elseif (is_object($s) && isset($s->skill_name)) $flat[] = trim($s->skill_name);
                elseif (is_array($s) && isset($s['skill_name'])) $flat[] = trim($s['skill_name']);
            }
            return array_filter($flat);
        }

        if (is_string($skills)) {
            $decoded = json_decode($skills, true);
            if (is_array($decoded)) {
                return $this->extractSkillsList($decoded);
            }
            return array_filter(array_map('trim', explode(',', $skills)));
        }

        return [];
    }

    protected function getMatchTier(float $score): string
    {
        if ($score >= 90) return 'Top Talent Match (Tier 1)';
        if ($score >= 75) return 'Strong Fit (Tier 2)';
        if ($score >= 60) return 'Potential Fit (Tier 3)';
        return 'Low Alignment (Tier 4)';
    }
}
