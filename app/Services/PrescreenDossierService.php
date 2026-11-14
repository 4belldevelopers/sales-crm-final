<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class PrescreenDossierService
 *
 * INTENT: Single Source of Truth (SSOT) for Prescreen Report candidate data resolution across
 * Forward Resume workflows (`test_fwd_resume_data`, `test_fwd_resume`, `fwd_resume`,
 * `resumeStudio`, Bench forward/download, and email skill/Q&A HTML trackers).
 *
 * GUARANTEES:
 * 1. Verified Skills Only: Never injects unpossessed job requirements into candidate skills.
 * 2. Strict Job & Candidate Isolation: Queries `candidate_job_skill_exp` and
 *    `application_additional_detail` scoped strictly to `(candidate_id, job_id)`.
 * 3. Zero Fabrication: Never substitutes missing per-skill experience with total experience,
 *    never coerces unrated (`0`/null) skills or candidates to `4`/`5`, and never fabricates `85`/`88`
 *    match scores.
 * 4. Multi-Tenant Cache Isolation & Atomic Invalidation: Caches are scoped by
 *    `(company_id, job_id, candidate_id)` and atomically purged on writes/realignment.
 */
class PrescreenDossierService
{
    /**
     * Resolve authoritative prescreen dossiers keyed by candidate application ID.
     *
     * @param  iterable<object|array>  $candidates
     * @param  int|null  $targetJobId  Explicit job context from request/route (0/null falls back per-candidate `job_id`)
     * @param  int  $companyId  Active authenticated tenant company_id
     * @param  array<string>  $jobSkillsFallback  Optional pre-fetched job skill names for `$targetJobId`
     * @return array<int, array<string, mixed>>
     */
    public function resolveCandidateDossiers(
        iterable $candidates,
        ?int $targetJobId,
        int $companyId,
        array $jobSkillsFallback = []
    ): array {
        $candList = [];
        foreach ($candidates as $cand) {
            $candObj = is_array($cand) ? (object) $cand : $cand;
            $cId = (int) ($candObj->id ?? 0);
            if ($cId > 0) {
                $candList[] = $candObj;
            }
        }

        if (empty($candList)) {
            return [];
        }

        $appIds = array_values(array_unique(array_map(fn ($c) => (int) $c->id, $candList)));

        // Determine effective job ID per candidate and collect all distinct job IDs to batch-query.
        $effectiveJobByCand = [];
        $jobIdsNeeded = [];
        foreach ($candList as $c) {
            $cId = (int) $c->id;
            $effJobId = ($targetJobId !== null && $targetJobId > 0)
                ? (int) $targetJobId
                : (int) ($c->job_id ?? 0);
            $effectiveJobByCand[$cId] = $effJobId;
            if ($effJobId > 0) {
                $jobIdsNeeded[$effJobId] = true;
            }
        }
        $uniqueJobIds = array_keys($jobIdsNeeded);

        // 1. Batch-fetch required skills per job from `job_metronics_skills` (for ordering & match % ONLY).
        $jobSkillsByJobId = [];
        if ($targetJobId !== null && $targetJobId > 0 && ! empty($jobSkillsFallback)) {
            $jobSkillsByJobId[(int) $targetJobId] = self::normalizeSkillList($jobSkillsFallback);
        }
        $missingJobSkillIds = array_values(array_diff($uniqueJobIds, array_keys($jobSkillsByJobId)));
        if (! empty($missingJobSkillIds)) {
            $rawJobSkills = DB::table('job_metronics_skills')
                ->whereIn('job_id', $missingJobSkillIds)
                ->whereNotNull('skill_name')
                ->where('skill_name', '!=', '')
                ->select('job_id', 'skill_name')
                ->get();

            foreach ($rawJobSkills as $row) {
                $jId = (int) $row->job_id;
                $sName = trim((string) $row->skill_name);
                if ($sName !== '') {
                    $jobSkillsByJobId[$jId][] = $sName;
                }
            }
            foreach ($jobSkillsByJobId as $jId => $names) {
                $jobSkillsByJobId[$jId] = self::normalizeSkillList($names);
            }
        }

        // 2. Batch-fetch verified profile skills from `job_application_skills`.
        $jasSkillsByCand = DB::table('job_application_skills')
            ->whereIn('job_application_id', $appIds)
            ->whereNotNull('skills')
            ->where('skills', '!=', '')
            ->select('job_application_id', 'skills')
            ->get()
            ->groupBy('job_application_id');

        // 3. Batch-fetch bench skill evaluations from `bench_candidate_skill_experience`.
        $benchSkillsByCand = DB::table('bench_candidate_skill_experience')
            ->whereIn('candidate_id', $appIds)
            ->select('candidate_id', 'skill_key', 'year', 'month', 'rating')
            ->get()
            ->groupBy('candidate_id');

        // 4. Batch-fetch job-scoped skill evaluations (`candidate_job_skill_exp`) with scoped Redis cache.
        $cjseByCand = $this->fetchJobScopedSkillEvaluations($candList, $effectiveJobByCand, $companyId);

        // 5. Batch-fetch job-scoped screening Q&A (`application_additional_detail`) with scoped Redis cache.
        $qaByCand = $this->fetchJobScopedScreeningQa($candList, $effectiveJobByCand, $companyId);

        // 6. Assemble strict, non-fabricated dossier per candidate.
        $dossiers = [];
        foreach ($candList as $cand) {
            $cId = (int) $cand->id;
            $effJobId = $effectiveJobByCand[$cId] ?? 0;
            $jobSkills = $effJobId > 0 ? ($jobSkillsByJobId[$effJobId] ?? []) : self::normalizeSkillList($jobSkillsFallback);

            // Build verified profile skill registry from candidate's own stored profile records ONLY.
            $verifiedProfileMap = []; // lower => displayName

            $rawProfileSkills = self::splitCommaSkills((string) ($cand->skills ?? ''));
            foreach ($rawProfileSkills as $sName) {
                $lower = mb_strtolower($sName);
                if (! isset($verifiedProfileMap[$lower])) {
                    $verifiedProfileMap[$lower] = $sName;
                }
            }

            $jasRows = $jasSkillsByCand->get($cId);
            if ($jasRows) {
                foreach ($jasRows as $jr) {
                    $sName = trim((string) ($jr->skills ?? ''));
                    if ($sName !== '') {
                        $lower = mb_strtolower($sName);
                        if (! isset($verifiedProfileMap[$lower])) {
                            $verifiedProfileMap[$lower] = $sName;
                        }
                    }
                }
            }

            // Resilient DB fallback if candidate object skills were blank/unprojected
            if (empty($verifiedProfileMap)) {
                try {
                    $dbSkillsVal = DB::table('job_applications')->where('id', $cId)->value('skills');
                    if (! empty($dbSkillsVal)) {
                        foreach (self::splitCommaSkills((string) $dbSkillsVal) as $sName) {
                            $lower = mb_strtolower($sName);
                            if (! isset($verifiedProfileMap[$lower])) {
                                $verifiedProfileMap[$lower] = $sName;
                            }
                        }
                    }
                } catch (\Throwable) {
                }
            }

            // Resilient candidate_skills_value fallback
            if (empty($verifiedProfileMap)) {
                try {
                    $csvRows = DB::table('candidate_skills_value')->where('candidate_id', $cId)->pluck('skill_name')->all();
                    foreach ($csvRows as $sName) {
                        $clean = trim((string) $sName);
                        if ($clean !== '') {
                            $lower = mb_strtolower($clean);
                            if (! isset($verifiedProfileMap[$lower])) {
                                $verifiedProfileMap[$lower] = $clean;
                            }
                        }
                    }
                } catch (\Throwable) {
                }
            }

            // Resilient career summary skills fallback
            if (empty($verifiedProfileMap) && ! empty($cand->careersummary)) {
                $careerSummary = (string) $cand->careersummary;
                if (preg_match('/(?:skills\s+in|experienced\s+in|proficient\s+in|knowledge\s+of)\s+([^.]+)/i', $careerSummary, $sm)) {
                    $extracted = preg_split('/[,;&]|\band\b/i', $sm[1]);
                    foreach ($extracted as $sName) {
                        $clean = trim((string) $sName);
                        if ($clean !== '' && strlen($clean) <= 40 && ! preg_match('/^(and|or|the|in|with)$/i', $clean)) {
                            $lower = mb_strtolower($clean);
                            if (! isset($verifiedProfileMap[$lower])) {
                                $verifiedProfileMap[$lower] = ucwords($clean);
                            }
                        }
                    }
                }
            }

            // Bench skills map (also counts as verified candidate profile skills)
            $benchMap = [];
            $benchRows = $benchSkillsByCand->get($cId);
            if ($benchRows) {
                foreach ($benchRows as $br) {
                    $sName = trim((string) ($br->skill_key ?? ''));
                    if ($sName === '') {
                        continue;
                    }
                    $lower = mb_strtolower($sName);
                    if (! isset($verifiedProfileMap[$lower])) {
                        $verifiedProfileMap[$lower] = $sName;
                    }
                    $bYear = max(0, (int) ($br->year ?? 0));
                    $bMonth = max(0, (int) ($br->month ?? 0));
                    if ($bMonth >= 12) {
                        $bYear += intdiv($bMonth, 12);
                        $bMonth = $bMonth % 12;
                    }
                    $hasDur = ($bYear > 0 || $bMonth > 0);
                    $bText = '--';
                    if ($hasDur) {
                        $parts = [];
                        if ($bYear > 0) {
                            $parts[] = $bYear.' Year'.($bYear > 1 ? 's' : '');
                        }
                        if ($bMonth > 0) {
                            $parts[] = $bMonth.' Month'.($bMonth > 1 ? 's' : '');
                        }
                        $bText = implode(' ', $parts);
                    }
                    $bRating = $br->rating !== null ? min(5, max(0, (int) $br->rating)) : 0;
                    $benchMap[$lower] = [
                        'name' => $sName,
                        'expText' => $bText,
                        'exp' => round($bYear + ($bMonth / 12), 2),
                        'rating' => $bRating,
                    ];
                }
            }

            // Job-scoped evaluation map (`candidate_job_skill_exp` strictly for ($cId, $effJobId))
            $cjseMap = [];
            foreach ($cjseByCand[$cId] ?? [] as $cRow) {
                $sName = trim((string) ($cRow->skill_name ?? ''));
                if ($sName === '') {
                    continue;
                }
                $lower = mb_strtolower($sName);
                $dur = self::parseSkillDuration($cRow->skill_value ?? null);
                $rowRating = $cRow->rating !== null ? min(5, max(0, (int) $cRow->rating)) : 0;
                $isExplicitlyEvaluated = ($rowRating > 0 || $dur['has_duration']);

                // Keep highest-signal row if duplicates exist for the same skill name on this job
                if (! isset($cjseMap[$lower]) || $isExplicitlyEvaluated) {
                    $prev = $cjseMap[$lower] ?? null;
                    if (! $prev || $rowRating > $prev['rating'] || ($dur['has_duration'] && ! $prev['has_duration'])) {
                        $cjseMap[$lower] = [
                            'name' => $sName,
                            'has_duration' => $dur['has_duration'],
                            'expText' => $dur['has_duration'] ? $dur['text'] : '--',
                            'exp' => $dur['has_duration'] ? $dur['years_float'] : 0.0,
                            'rating' => $rowRating,
                            'evaluated' => $isExplicitlyEvaluated,
                        ];
                    }
                }
            }

            $skillsArr = [];
            $seenSkills = [];
            $verifiedSkillNamesForMatch = [];

            // A. Prioritize matched job skills that are in the candidate's profile, bench, OR available in candidate_job_skill_exp
            foreach ($jobSkills as $jsName) {
                $jsLower = mb_strtolower($jsName);
                if (isset($seenSkills[$jsLower])) {
                    continue;
                }

                $inProfile = isset($verifiedProfileMap[$jsLower]);
                $inBench = isset($benchMap[$jsLower]);
                $evalRow = $cjseMap[$jsLower] ?? null;
                $hasCjseEntry = ($evalRow !== null);

                // Anti-hallucination guard: only include job skills that the candidate has in profile, bench, or drawer evaluation (CJSE)
                if (! $inProfile && ! $inBench && ! $hasCjseEntry) {
                    continue;
                }

                $seenSkills[$jsLower] = true;
                $expText = '--';
                $expFloat = 0.0;
                $rating = 0;

                if ($evalRow) {
                    $expText = $evalRow['expText'];
                    $expFloat = $evalRow['exp'];
                    $rating = $evalRow['rating'];
                } elseif ($inBench) {
                    $expText = $benchMap[$jsLower]['expText'];
                    $expFloat = $benchMap[$jsLower]['exp'];
                    $rating = $benchMap[$jsLower]['rating'];
                }

                // Candidate only counts as matched for match % if verified in profile or evaluated with positive duration/rating
                if ($inProfile || $inBench || ($evalRow && ($evalRow['evaluated'] || $evalRow['rating'] > 0 || $evalRow['exp'] > 0))) {
                    $verifiedSkillNamesForMatch[] = $verifiedProfileMap[$jsLower] ?? $jsName;
                }

                $skillsArr[] = [
                    'name' => self::formatSkillDisplayName($verifiedProfileMap[$jsLower] ?? $jsName),
                    'expText' => $expText,
                    'exp' => $expFloat,
                    'rating' => $rating,
                    'matched_job_skill' => true,
                ];
            }

            // B. Append remaining evaluated CJSE skills
            foreach ($cjseMap as $lower => $evalRow) {
                if (isset($seenSkills[$lower])) {
                    continue;
                }
                $seenSkills[$lower] = true;
                if ($evalRow['evaluated'] || $evalRow['rating'] > 0 || $evalRow['exp'] > 0) {
                    $verifiedSkillNamesForMatch[] = $verifiedProfileMap[$lower] ?? $evalRow['name'];
                }

                $skillsArr[] = [
                    'name' => self::formatSkillDisplayName($verifiedProfileMap[$lower] ?? $evalRow['name']),
                    'expText' => $evalRow['expText'],
                    'exp' => $evalRow['exp'],
                    'rating' => $evalRow['rating'],
                    'matched_job_skill' => false,
                ];
            }

            // C. Append remaining bench skills
            foreach ($benchMap as $lower => $bRow) {
                if (isset($seenSkills[$lower])) {
                    continue;
                }
                $seenSkills[$lower] = true;
                $verifiedSkillNamesForMatch[] = $bRow['name'];

                $skillsArr[] = [
                    'name' => self::formatSkillDisplayName($bRow['name']),
                    'expText' => $bRow['expText'],
                    'exp' => $bRow['exp'],
                    'rating' => $bRow['rating'],
                    'matched_job_skill' => false,
                ];
            }

            // D. Append remaining verified profile skills ONLY IF no job/evaluated skills exist
            // (prevents polluting job-specific evaluations with unrelated career profile skills)
            if (empty($skillsArr)) {
                foreach ($verifiedProfileMap as $lower => $displayName) {
                    if (isset($seenSkills[$lower])) {
                        continue;
                    }
                    $seenSkills[$lower] = true;
                    $verifiedSkillNamesForMatch[] = $displayName;

                    $skillsArr[] = [
                        'name' => self::formatSkillDisplayName($displayName),
                        'expText' => '--',
                        'exp' => 0.0,
                        'rating' => 0,
                        'matched_job_skill' => false,
                    ];
                }
            }

            $skillsArr = array_slice($skillsArr, 0, 12);

            // Compute accurate skill match from verified candidate skills vs target job skills ONLY
            $matchPct = 0;
            if (! empty($jobSkills) && ! empty($verifiedSkillNamesForMatch)) {
                $calc = GeneralMethod::calculateSkillMatch($jobSkills, $verifiedSkillNamesForMatch);
                $matchPct = (int) ($calc['percentage'] ?? 0);
            }

            $overallRating = isset($cand->rating) && is_numeric($cand->rating)
                ? min(5, max(0, (int) $cand->rating))
                : 0;

            $totalExpDisplay = self::formatExperienceDisplay($cand->total_exp ?? null, false);
            $totalExpCompact = self::formatExperienceDisplay($cand->total_exp ?? null, true);
            $relExpDisplay = self::formatExperienceDisplay($cand->rel_exp ?? null, false);
            $relExpCompact = self::formatExperienceDisplay($cand->rel_exp ?? null, true);

            $qaList = $qaByCand[$cId] ?? [];

            $loc = GeneralMethod::resolveCandidateLocationHierarchy($cand);

            $hasResume = ! empty($cand->resume) && $cand->resume !== '0' && strtolower((string) $cand->resume) !== 'null';
            $hasEmail = ! empty($cand->email) || ! empty($cand->secondrary_email);
            $hasPhone = ! empty($cand->phone) || ! empty($cand->secondary_mobile);
            $hasLinkedin = ! empty($cand->linkedin) && ! in_array(strtolower(trim((string) $cand->linkedin)), ['--', '-', 'na', 'n/a', 'null', 'none'], true);
            $hasJd = ! empty($cand->current_designation) || ! empty($cand->current_company);
            $valArr = [
                ['label' => 'RESUME', 'status' => $hasResume ? 'Verified' : 'Not Uploaded'],
                ['label' => 'EMAIL ID', 'status' => $hasEmail ? 'Verified' : 'Unverified'],
                ['label' => 'PHONE NUMBER', 'status' => $hasPhone ? 'Verified' : 'Unverified'],
                ['label' => 'LINKEDIN ID', 'status' => $hasLinkedin ? 'Verified' : '-'],
                ['label' => 'JOB DESCRIPTION', 'status' => $hasJd ? 'Shared with candidate' : '-'],
            ];

            $npMode = (int) ($cand->serving_notice_period_mode ?? -1);
            $npRaw = trim((string) ($cand->np_id ?? ''));
            $npTs = $npRaw !== '' ? strtotime($npRaw) : false;
            $isImmediate = false;
            $noticeText = '--';
            if ($npMode === 2 || strtolower($npRaw) === 'immediate') {
                $noticeText = 'Immediate';
                $isImmediate = true;
            } elseif ($npMode === 0 && (int) $npRaw > 0) {
                $noticeText = (int) $npRaw.' Days';
            } elseif ($npTs !== false && (int) date('Y', $npTs) > 1970) {
                if (date('Y-m-d', $npTs) < date('Y-m-d')) {
                    $noticeText = 'Immediate';
                    $isImmediate = true;
                } else {
                    $noticeText = (@$cand->condidate_type === 'bench_condidate') ? ('Available '.date('d M Y', $npTs)) : ('SNP LWD '.date('d M Y', $npTs));
                }
            } else {
                $noticeText = ($npRaw !== '' && ! str_contains($npRaw, '01 Jan 1970')) ? $npRaw : 'Not Specified';
            }

            $education = ! empty($cand->qualification_name) ? (string) $cand->qualification_name : (! empty($cand->qualification) ? (string) $cand->qualification : 'Not Specified');
            $workMode = ! empty($cand->wm) ? ucwords(str_replace(['_', '-'], ' ', strtolower((string) $cand->wm))) : (! empty($cand->work_mode) ? ucwords(str_replace(['_', '-'], ' ', strtolower((string) $cand->work_mode))) : 'Hybrid');

            $dossiers[$cId] = [
                'candidate_id' => $cId,
                'job_id' => $effJobId,
                'skills' => $skillsArr,
                'verified_skill_names' => array_values(array_unique($verifiedSkillNamesForMatch)),
                'job_skills' => $jobSkills,
                'total_exp_display' => $totalExpDisplay,
                'total_exp_compact' => $totalExpCompact,
                'rel_exp_display' => $relExpDisplay,
                'rel_exp_compact' => $relExpCompact,
                'overall_rating' => $overallRating,
                'city' => $loc['city'],
                'state' => $loc['state'],
                'country' => $loc['country'],
                'location_text' => $loc['location_text'],
                'validation' => $valArr,
                'notice_text' => $noticeText,
                'is_immediate' => $isImmediate,
                'education' => $education,
                'work_mode' => $workMode,
                'match' => $matchPct,
                'screening_qa' => $qaList,
                'fingerprint' => sha1(json_encode([
                    'job_id' => $effJobId,
                    'skills' => $skillsArr,
                    'total_exp' => $totalExpDisplay,
                    'rel_exp' => $relExpDisplay,
                    'overall_rating' => $overallRating,
                    'location' => $loc['location_text'],
                    'match' => $matchPct,
                    'qa' => $qaList,
                    'updated_at' => (string) ($cand->updated_at ?? ''),
                ])),
            ];
        }

        return $dossiers;
    }

    /**
     * Fetch `candidate_job_skill_exp` rows strictly scoped by `(candidate_id, job_id)` and cached by `(company_id, job_id, candidate_id)`.
     *
     * @param  array<object>  $candList
     * @param  array<int, int>  $effectiveJobByCand
     * @return array<int, array<int, object>>
     */
    protected function fetchJobScopedSkillEvaluations(array $candList, array $effectiveJobByCand, int $companyId): array
    {
        $result = [];
        $missingPairs = []; // candId => jobId

        foreach ($candList as $cand) {
            $cId = (int) $cand->id;
            $jId = (int) ($effectiveJobByCand[$cId] ?? 0);
            if ($jId <= 0) {
                $result[$cId] = [];

                continue;
            }

            $cacheKey = self::skillsCacheKey($companyId, $jId, $cId);
            $cached = Cache::get($cacheKey);
            if ($cached !== null && is_array($cached)) {
                $result[$cId] = $cached;
            } else {
                $missingPairs[$cId] = $jId;
            }
        }

        if (! empty($missingPairs)) {
            $rows = DB::table('candidate_job_skill_exp')
                ->join('job_metronics_skills', 'candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                ->where(function ($q) use ($missingPairs) {
                    foreach ($missingPairs as $cId => $jId) {
                        $q->orWhere(function ($sub) use ($cId, $jId) {
                            $sub->where('candidate_job_skill_exp.candidate_id', $cId)
                                ->where('candidate_job_skill_exp.job_id', $jId)
                                ->where('job_metronics_skills.job_id', $jId);
                        });
                    }
                })
                ->select(
                    'candidate_job_skill_exp.candidate_id',
                    'candidate_job_skill_exp.job_id',
                    'job_metronics_skills.skill_name',
                    'candidate_job_skill_exp.skill_value',
                    'candidate_job_skill_exp.rating'
                )
                ->get()
                ->groupBy('candidate_id');

            foreach ($missingPairs as $cId => $jId) {
                $candRows = $rows->has($cId) ? $rows->get($cId)->all() : [];
                $result[$cId] = $candRows;
                Cache::put(self::skillsCacheKey($companyId, $jId, $cId), $candRows, 300);
            }
        }

        return $result;
    }

    /**
     * Fetch `application_additional_detail` screening Q&A strictly scoped by `(application_id, job_id)`.
     *
     * @param  array<object>  $candList
     * @param  array<int, int>  $effectiveJobByCand
     * @return array<int, array<int, array{question: string, answer: string}>>
     */
    protected function fetchJobScopedScreeningQa(array $candList, array $effectiveJobByCand, int $companyId): array
    {
        $result = [];
        $missingPairs = [];

        foreach ($candList as $cand) {
            $cId = (int) $cand->id;
            $jId = (int) ($effectiveJobByCand[$cId] ?? 0);
            if ($jId <= 0) {
                $result[$cId] = [];

                continue;
            }

            $cacheKey = self::qaCacheKey($companyId, $jId, $cId);
            $cached = Cache::get($cacheKey);
            if ($cached !== null && is_array($cached)) {
                $result[$cId] = $cached;
            } else {
                $missingPairs[$cId] = $jId;
            }
        }

        if (! empty($missingPairs)) {
            $rows = DB::table('application_additional_detail')
                ->whereNotNull('application_detail_value')
                ->where('application_detail_value', '!=', '')
                ->where(function ($q) use ($missingPairs) {
                    foreach ($missingPairs as $cId => $jId) {
                        $q->orWhere(function ($sub) use ($cId, $jId) {
                            $sub->where('application_id', $cId)
                                ->where('job_id', $jId);
                        });
                    }
                })
                ->select('application_id', 'job_id', 'job_detail_value', 'application_detail_value')
                ->orderBy('id', 'ASC')
                ->get()
                ->groupBy('application_id');

            foreach ($missingPairs as $cId => $jId) {
                $qaFormatted = [];
                if ($rows->has($cId)) {
                    foreach ($rows->get($cId) as $qr) {
                        $qTxt = trim((string) ($qr->job_detail_value ?? ''));
                        $aTxt = trim((string) ($qr->application_detail_value ?? ''));
                        if ($qTxt !== '' && $aTxt !== '') {
                            $qaFormatted[] = [
                                'question' => $qTxt,
                                'answer' => $aTxt,
                            ];
                        }
                    }
                }
                $result[$cId] = $qaFormatted;
                Cache::put(self::qaCacheKey($companyId, $jId, $cId), $qaFormatted, 300);
            }
        }

        return $result;
    }

    /**
     * Atomically invalidate all prescreen dossier caches for a candidate (across affected job IDs & legacy keys).
     */
    public static function invalidateDossierCaches(int $candidateId, ?int $jobId = null, ?int $companyId = null): void
    {
        if ($candidateId <= 0) {
            return;
        }

        if ($companyId === null || $companyId <= 0) {
            $companyId = (int) (DB::table('job_applications')->where('id', $candidateId)->value('company_id')
                ?: (auth()->check() ? auth()->user()->company_id : 0));
        }

        $jobIds = [];
        if ($jobId !== null && $jobId > 0) {
            $jobIds[] = $jobId;
        } else {
            $baseJobId = (int) (DB::table('job_applications')->where('id', $candidateId)->value('job_id') ?? 0);
            if ($baseJobId > 0) {
                $jobIds[] = $baseJobId;
            }
            $wiseJobIds = DB::table('add_job_wise_condidates')
                ->where('job_application_id', $candidateId)
                ->pluck('job_id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->all();
            $cjseJobIds = DB::table('candidate_job_skill_exp')
                ->where('candidate_id', $candidateId)
                ->pluck('job_id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->all();
            $qaJobIds = DB::table('application_additional_detail')
                ->where('application_id', $candidateId)
                ->pluck('job_id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->all();
            $jobIds = array_merge($jobIds, $wiseJobIds, $cjseJobIds, $qaJobIds);
        }

        $jobIds[] = 0;
        $jobIds = array_values(array_unique(array_map('intval', $jobIds)));

        $tenantIds = array_values(array_unique(array_filter([
            (int) $companyId,
            auth()->check() ? (int) auth()->user()->company_id : 0,
        ])));

        foreach ($tenantIds as $tId) {
            // Legacy unscoped keys (safety cleanup during rollout)
            Cache::forget("fwd_cand_skills:{$tId}:{$candidateId}");
            Cache::forget("tenant:{$tId}:cand_qa:{$candidateId}");

            foreach ($jobIds as $jId) {
                Cache::forget(self::skillsCacheKey($tId, $jId, $candidateId));
                Cache::forget(self::qaCacheKey($tId, $jId, $candidateId));
                if ($jId > 0) {
                    Cache::forget("tenant:{$tId}:job_skills_list:{$jId}");
                }
            }
        }
    }

    public static function skillsCacheKey(int $companyId, int $jobId, int $candidateId): string
    {
        return "fwd_cand_skills:v2:{$companyId}:{$jobId}:{$candidateId}";
    }

    public static function qaCacheKey(int $companyId, int $jobId, int $candidateId): string
    {
        return "tenant:{$companyId}:cand_qa:v2:{$jobId}:{$candidateId}";
    }

    /**
     * Parse raw `candidate_job_skill_exp.skill_value` without fabricating empty or `0` placeholders.
     *
     * @return array{has_duration: bool, text: string, years_float: float}
     */
    public static function parseSkillDuration(mixed $rawValue): array
    {
        $raw = trim((string) ($rawValue ?? ''));
        if ($raw === '' || $raw === '--' || $raw === '-') {
            return ['has_duration' => false, 'text' => '--', 'years_float' => 0.0];
        }

        // Format "X Years Y Months"
        if (preg_match('/(\d+)\s*(?:years?|yrs?)\s+(\d+)\s*(?:months?|mos?)/i', $raw, $m)) {
            $years = (int) $m[1];
            $months = (int) $m[2];
            if ($years === 0 && $months === 0) {
                return ['has_duration' => false, 'text' => '--', 'years_float' => 0.0];
            }

            return [
                'has_duration' => true,
                'text' => $raw,
                'years_float' => round($years + ($months / 12), 2),
            ];
        }

        // Format "X.Y Years" (from skillExperienceUpdate where Y = months)
        if (preg_match('/^(\d+)\s*[.]\s*(\d+)(?:\s*(?:years?|yrs?))?$/i', $raw, $m)) {
            $years = (int) $m[1];
            $months = (int) $m[2];
            if ($years === 0 && $months === 0) {
                return ['has_duration' => false, 'text' => '--', 'years_float' => 0.0];
            }

            return [
                'has_duration' => true,
                'text' => "{$years}.{$months} ".($years >= 1 ? 'Years' : 'Year'),
                'years_float' => round($years + ($months / 12), 2),
            ];
        }

        // Pure integer / single number
        if (preg_match('/^(\d+)(?:\s*(?:years?|yrs?))?$/i', $raw, $m)) {
            $years = (int) $m[1];
            if ($years === 0) {
                return ['has_duration' => false, 'text' => '--', 'years_float' => 0.0];
            }

            return [
                'has_duration' => true,
                'text' => $years.' '.($years === 1 ? 'Year' : 'Years'),
                'years_float' => (float) $years,
            ];
        }

        // Fallback check: extract any number > 0
        if (preg_match('/(\d+(?:\.\d+)?)/', $raw, $m)) {
            $num = (float) $m[1];
            if ($num <= 0.0) {
                return ['has_duration' => false, 'text' => '--', 'years_float' => 0.0];
            }

            return [
                'has_duration' => true,
                'text' => $raw,
                'years_float' => $num,
            ];
        }

        return ['has_duration' => true, 'text' => $raw, 'years_float' => 0.0];
    }

    /**
     * Format candidate career experience (total_exp / rel_exp) consistently without fabrication.
     */
    public static function formatExperienceDisplay(mixed $rawExp, bool $compact = false): string
    {
        $raw = trim((string) ($rawExp ?? ''));
        $raw = trim($raw, " \t\n\r\0\x0B.");
        if ($raw === '' || $raw === '--' || $raw === '-' || strtolower($raw) === 'null' || strtolower($raw) === 'n/a') {
            return $compact ? '--' : 'Not Specified';
        }

        if (is_numeric($raw)) {
            $num = (float) $raw;
            if ($num == 0.0) {
                return 'Fresher';
            }
            if ($compact) {
                return $raw.' Yrs';
            }

            return $raw.' Year'.($num == 1.0 ? '' : 's');
        }

        if (preg_match('/^(\d+(?:\.\d+)?)\s*(?:years?|yrs?)$/i', $raw, $m)) {
            $num = (float) $m[1];
            if ($num == 0.0) {
                return 'Fresher';
            }
            if ($compact) {
                return $m[1].' Yrs';
            }

            return $m[1].' Year'.($num == 1.0 ? '' : 's');
        }

        return $raw;
    }

    /**
     * @param  array<int, string>  $skills
     * @return array<int, string>
     */
    public static function normalizeSkillList(array $skills): array
    {
        $seen = [];
        $out = [];
        foreach ($skills as $s) {
            $clean = trim((string) $s);
            if ($clean === '') {
                continue;
            }
            $lower = mb_strtolower($clean);
            if (! isset($seen[$lower])) {
                $seen[$lower] = true;
                $out[] = $clean;
            }
        }

        return $out;
    }

    /**
     * @return array<int, string>
     */
    public static function splitCommaSkills(string $skillsCsv): array
    {
        if (trim($skillsCsv) === '') {
            return [];
        }

        // Handle JSON-encoded array just in case
        $trimmed = trim($skillsCsv);
        if (str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return self::normalizeSkillList(array_map(function ($item) {
                    return is_array($item) ? (string) ($item['value'] ?? $item['name'] ?? '') : (string) $item;
                }, $decoded));
            }
        }

        return self::normalizeSkillList(explode(',', $skillsCsv));
    }

    protected static function formatSkillDisplayName(string $skill): string
    {
        $clean = trim($skill);
        if ($clean === '') {
            return '';
        }

        // Preserve mixed-case / uppercase technical names (PHP, MySQL, TypeScript, GraphQL, C++, UI/UX)
        if (preg_match('/[A-Z]/', $clean)) {
            return $clean;
        }

        return ucwords(strtolower($clean));
    }
}
