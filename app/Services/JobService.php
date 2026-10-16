<?php

namespace App\Services;

use App\Enums\BenchSubmissionState;
use App\Enums\ClaimCapacity;
use App\Enums\MarketModState;
use App\Enums\MarketPubState;
use App\Helper\GeneralMethod;
use App\Jobs\ElasticJobsIndexJob;
use App\Jobs\QueueNotificationMailService;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * JobService — Core Business Service for Job Requisitions Management.
 *
 * Intent: Decouples heavy database persistence, batch pivot synchronization, and Elasticsearch indexing
 * from the ServiceCompanyJobsController God controller.
 *
 * Tenancy Note: All writes and lookups are strictly tenant-scoped by company_id to enforce multi-tenant isolation.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class JobService
{
    /**
     * Create a new Job Requisition with batch pivot insertions and asynchronous Elastic indexing.
     *
     * @param  array  $requestData  Input request payload
     * @param  User  $user  Authenticated user instance
     * @return Job Newly created job model
     */
    public function createJob(array $requestData, User $user): Job
    {
        $job = new Job;
        $job->slug = null;
        $job->company_id = $user->company_id;
        $job->status = 'active';

        $job->client_id = $requestData['client_id'] ?? null;
        $job->title = $requestData['title'] ?? null;
        $job->raw_designation = $requestData['raw_designation'] ?? ($requestData['title'] ?? null);
        $job->canonical_designation_id = ! empty($requestData['canonical_designation_id']) ? (int) $requestData['canonical_designation_id'] : null;
        $job->taxonomy_version = $requestData['taxonomy_version'] ?? config('taxonomy.current_version', '2.0.0');
        $job->work_shifts = $requestData['work_shifts'] ?? null;
        $job->category_id = ! empty($requestData['category_id']) && is_numeric($requestData['category_id']) ? (int) $requestData['category_id'] : null;
        if (empty($job->category_id) && (! empty($requestData['custom_department']) || ! empty($requestData['category_id_display']))) {
            $customDept = trim($requestData['custom_department'] ?? $requestData['category_id_display']);
            if ($customDept !== '') {
                $targetCompanyId = $user->company_id ?? (auth()->check() ? auth()->user()->company_id : null);
                $cat = null;
                if ($targetCompanyId) {
                    $cat = \App\Models\JobCategory::where('company_id', $targetCompanyId)
                        ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($customDept)])
                        ->first();
                }
                if (! $cat) {
                    $cat = \App\Models\JobCategory::whereNull('company_id')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($customDept)])
                        ->first();
                }
                if (! $cat && $targetCompanyId) {
                    $cat = \App\Models\JobCategory::create([
                        'company_id' => $targetCompanyId,
                        'name' => $customDept,
                    ]);
                }
                if ($cat) {
                    $job->category_id = $cat->id;
                }
            }
        }
        $job->required_columns = ! empty($requestData['required_columns']) ? $requestData['required_columns'] : 'No Preference';

        if (! empty($requestData['employment_type'])) {
            $job->employment_type = $requestData['employment_type'];
        }
        if (! empty($requestData['total_positions'])) {
            $job->total_positions = $requestData['total_positions'];
        }
        if (! empty($requestData['min_exp'])) {
            $job->min_exp = $requestData['min_exp'];
        }
        if (! empty($requestData['max_exp'])) {
            $job->max_exp = $requestData['max_exp'];
        }

        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime($startDate.' + '.($requestData['end_date'] ?? 0).' days'));
        $job->start_date = $startDate;
        $job->end_date = $endDate;
        $job->priority = 0;
        $job->location_id = 1;

        $metaTitle = $requestData['meta_title'] ?? ($requestData['title'] ?? '');
        $metaDesc = $requestData['meta_description'] ?? strip_tags(Str::substr(html_entity_decode($requestData['job_description'] ?? ''), 0, 150));
        $job->meta_details = [
            'title' => $metaTitle,
            'description' => $metaDesc,
        ];

        $job->min_salary_currency = $requestData['min_salary_currency'] ?? null;
        $job->min_salary = (! empty($requestData['minimum_salary']) && is_numeric($requestData['minimum_salary'])) ? (int) $requestData['minimum_salary'] : 0;
        $job->min_sal_mode = $requestData['min_sal_mode'] ?? null;

        $job->max_salary_currency = $requestData['max_salary_currency'] ?? null;
        $job->max_salary = (! empty($requestData['maximum_salary']) && is_numeric($requestData['maximum_salary'])) ? (int) $requestData['maximum_salary'] : 0;
        $job->max_sal_mode = $requestData['max_sal_mode'] ?? null;
        $job->salary_visibility = \App\Services\SalaryVisibilityPolicy::normalize($requestData['salary_visibility'] ?? null);

        if (! empty($requestData['detail_description'])) {
            $job->job_description = $requestData['detail_description'];
        }

        $isInternalOnly = isset($requestData['visibility_internal']) && (int) $requestData['visibility_internal'] === 1;
        $hasCareer = isset($requestData['carrier_website']) ? ((int) $requestData['carrier_website'] === 1) : (! empty($requestData['visibility_career']));
        $hasMarketplace = ! empty($requestData['visibility_marketplace']);
        $hasBench = ! empty($requestData['visibility_bench_talent']) || (($requestData['bench_submission'] ?? '') === 'enabled');

        if ($isInternalOnly) {
            $job->carrier_website = 0;
            $job->market_pub_state = MarketPubState::PRIVATE->value;
            $job->market_mod_state = MarketModState::NOT_REQUIRED->value;
            $job->bench_submission = BenchSubmissionState::DISABLED->value;
            $job->public_market_status = 0;
        } else {
            $job->carrier_website = $hasCareer ? 1 : 0;
            $job->bench_submission = $hasBench ? BenchSubmissionState::ENABLED->value : BenchSubmissionState::DISABLED->value;

            // Recruiter wants job on marketplace if either Option B (Outsource) or Option C (Bench) is chosen
            if ($hasMarketplace || $hasBench) {
                $moderationService = app(\App\Services\MarketplaceModerationService::class);
                $eval = $moderationService->evaluateRisk([
                    'title' => $job->title,
                    'job_description' => $job->job_description ?? ($requestData['detail_description'] ?? ''),
                    'job_requirement' => $job->job_requirement ?? '',
                    'min_salary' => $requestData['minimum_salary'] ?? 0,
                    'max_salary' => $requestData['maximum_salary'] ?? 0,
                    'company_id' => $job->company_id,
                ]);

                $autoApproveSetting = (bool) DB::table('global_settings')->value('public_market_job_status');
                if (! $eval['hard_blocked'] && $eval['risk_score'] < 0.20 && $autoApproveSetting) {
                    $job->market_pub_state = MarketPubState::PUBLISHED->value;
                    $job->market_mod_state = MarketModState::AUTO_APPROVED->value;
                } else {
                    $job->market_pub_state = MarketPubState::PENDING_REVIEW->value;
                    $job->market_mod_state = MarketModState::PENDING->value;
                }
                $job->claim_capacity = $hasMarketplace ? ClaimCapacity::OPEN->value : ClaimCapacity::LIMIT_REACHED->value;
            } else {
                $job->market_pub_state = MarketPubState::PRIVATE->value;
                $job->market_mod_state = MarketModState::NOT_REQUIRED->value;
                $job->claim_capacity = ClaimCapacity::OPEN->value;
            }
        }
        $job->work_type = $requestData['work_from_home'] ?? ($requestData['work_type'] ?? 'no');
        $job->show_vendor = $requestData['show_client_name'] ?? ($requestData['show_vendor'] ?? 'no');

        $currency = GeneralMethod::currency();

        $maxSalCurr = $requestData['max_salary_currency'] ?? null;
        $ecurrency = $currency[$maxSalCurr] ?? null;
        $econvertedAmount = (! empty($requestData['maximum_salary']) && is_numeric($requestData['maximum_salary'])) ? (float) $requestData['maximum_salary'] : 0;
        if ($maxSalCurr > 0 && $ecurrency && $econvertedAmount > 0) {
            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
        }
        $ectcdivided = GeneralMethod::salaryDividedByMode($requestData['max_sal_mode'] ?? null, $econvertedAmount);
        $job->max_yearly = $ectcdivided->yearly ?? null;
        $job->max_monthly = $ectcdivided->monthly ?? null;
        $job->max_hourly = $ectcdivided->hourly ?? null;

        $minSalCurr = $requestData['min_salary_currency'] ?? null;
        $mcurrency = $currency[$minSalCurr] ?? null;
        $convertedAmount = (! empty($requestData['minimum_salary']) && is_numeric($requestData['minimum_salary'])) ? (float) $requestData['minimum_salary'] : 0;
        if ($minSalCurr > 0 && $mcurrency && $convertedAmount > 0) {
            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $mcurrency);
        }
        $ctcdivided = GeneralMethod::salaryDividedByMode($requestData['min_sal_mode'] ?? null, $convertedAmount);
        $job->min_yearly = $ctcdivided->yearly ?? null;
        $job->min_monthly = $ctcdivided->monthly ?? null;
        $job->min_hourly = $ctcdivided->hourly ?? null;

        $job->auto_date_job = Carbon::today();

        DB::beginTransaction();
        try {
            $job->save();

            // 1. Team Rank Tracking
            $teamRankElastic = [
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'candidate_id' => 0,
                'profileUpload' => 0,
                'interviewScheduled' => 0,
                'interviewNoShow' => 0,
                'offerReleased' => 0,
                'candidateJoined' => 0,
                'jobsPosted' => 2,
                'feedbackGivenOnCVs' => 0,
                'postJobToVendorMarket' => 0,
                'outsourceJobsToVendors' => 0,
                'listBenchCandidates' => 0,
                'respondToBenchRequests' => 0,
            ];
            GeneralMethod::teamRankElastic($teamRankElastic);

            // 2. Concurrency-Safe Coin Deductions via Atomic Redis Lock
            JobConcurrencyLock::runForCompany((int) $user->company_id, function () use ($user, $job) {
                GeneralMethod::coinsDeductions((int) $user->company_id, 2, $user->id, $user->company_id, $job->id, '');
            });

            // 3. Batch Qualification Processing
            $workQualification = $requestData['qualification'] ?? null;
            if (! empty($workQualification)) {
                $workQualificationAll = is_array($workQualification) ? $workQualification : json_decode($workQualification, true);
                if (is_array($workQualificationAll) && count($workQualificationAll) > 0) {
                    $qualNames = array_column($workQualificationAll, 'value');
                    $qualMasterMap = DB::table('qualification_master')
                        ->select('id', 'name')
                        ->whereIn('name', $qualNames)
                        ->get()
                        ->keyBy('name');

                    $batchQualInsert = [];
                    foreach ($workQualificationAll as $idx => $qVal) {
                        $qName = $qVal['value'] ?? null;
                        if (! $qName) {
                            continue;
                        }

                        $qRow = $qualMasterMap->get($qName);
                        if ($idx === 0 && $qRow) {
                            $job->qualification = $qRow->id;
                            $job->save();
                        }

                        if ($qRow) {
                            $batchQualInsert[] = [
                                'job_id' => $job->id,
                                'qualification_id' => $qRow->id,
                                'qualification_name' => $qRow->name,
                            ];
                        }
                    }

                    if (count($batchQualInsert) > 0) {
                        DB::table('job_qualifications')->insert($batchQualInsert);
                    }
                }
            }

            // 4. Client Location Processing
            if (! empty($requestData['address_id'])) {
                DB::table('client_location_jobs')->insert([
                    'company_id' => $user->company_id,
                    'client_location_id' => $requestData['address_id'],
                    'job_id' => $job->id,
                ]);
            }

            // 5. Batch Work City Processing
            $workCity = $requestData['kt_tagify_work_location'] ?? null;
            if (! empty($workCity)) {
                $workCityAll = is_array($workCity) ? $workCity : json_decode($workCity, true);
                if (is_array($workCityAll) && count($workCityAll) > 0) {
                    $cityNames = [];
                    $countryNames = [];
                    $cityPairs = [];

                    foreach ($workCityAll as $cVal) {
                        $rawVal = (string) ($cVal['value'] ?? '');
                        $parts = strpos($rawVal, '|') !== false ? explode('|', $rawVal) : explode(',', $rawVal);
                        if (count($parts) >= 3) {
                            $cName = trim($parts[0]);
                            $coName = trim(end($parts));
                            $cityNames[] = $cName;
                            $countryNames[] = $coName;
                            $cityPairs[] = ['city' => $cName, 'country' => $coName];
                        } elseif (count($parts) >= 1 && trim($parts[0]) !== '') {
                            $cName = trim($parts[0]);
                            $cityNames[] = $cName;
                            $cityPairs[] = ['city' => $cName, 'country' => null];
                        }
                    }

                    if (count($cityNames) > 0) {
                        $cityQuery = DB::table('city_list')
                            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->join('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city_id', 'city_list.city', 'countries.id as country_id', 'countries.country_name')
                            ->whereIn('city_list.city', array_unique($cityNames));
                        $nonEmptyCountries = array_values(array_filter($countryNames));
                        if (! empty($nonEmptyCountries)) {
                            $cityQuery->whereIn('countries.country_name', array_unique($nonEmptyCountries));
                        }
                        $cityRows = $cityQuery->get();

                        $batchCityInsert = [];
                        foreach ($cityPairs as $pair) {
                            $match = $cityRows->first(function ($item) use ($pair) {
                                $itemCity = trim((string) $item->city);
                                $pairCity = trim((string) $pair['city']);

                                if (empty($pair['country'])) {
                                    return strcasecmp($itemCity, $pairCity) === 0;
                                }

                                $itemCountry = trim((string) $item->country_name);
                                $pairCountry = trim((string) $pair['country']);

                                return strcasecmp($itemCity, $pairCity) === 0
                                    && strcasecmp($itemCountry, $pairCountry) === 0;
                            });
                            if ($match) {
                                $batchCityInsert[] = [
                                    'company_id' => $user->company_id,
                                    'city_id' => $match->city_id,
                                    'country_id' => $match->country_id,
                                    'job_id' => $job->id,
                                ];
                            }
                        }

                        if (count($batchCityInsert) > 0) {
                            DB::table('client_city_jobs')->insert($batchCityInsert);
                        }
                    }
                }
            }

            // 6. Job Countries Processing
            if (! empty($requestData['kt_select2_4']) && is_array($requestData['kt_select2_4'])) {
                $batchCountryInsert = [];
                foreach ($requestData['kt_select2_4'] as $ld) {
                    $batchCountryInsert[] = [
                        'company_id' => $user->company_id,
                        'country_id' => $ld,
                        'job_id' => $job->id,
                    ];
                }
                if (count($batchCountryInsert) > 0) {
                    DB::table('job_countries')->insert($batchCountryInsert);
                }
            }

            // 7. Skills Tag Processing (Batch query & insert)
            $tagsArr = [];
            if (! empty($requestData['tags'])) {
                $tags = is_array($requestData['tags']) ? $requestData['tags'] : json_decode($requestData['tags'], true);
                if (is_array($tags) && count($tags) > 0) {
                    $skillsAddElastic = array_column($tags, 'value');
                    if (count($skillsAddElastic) > 0) {
                        GeneralMethod::skillsAddElastic($skillsAddElastic);
                    }
                    $tagsArr = $skillsAddElastic;

                    $existingSkills = DB::table('job_metronics_skills')
                        ->where('company_id', $user->company_id)
                        ->where('job_id', $job->id)
                        ->pluck('skill_name')
                        ->toArray();

                    $batchSkillInsert = [];
                    foreach ($tagsArr as $value) {
                        if (! in_array($value, $existingSkills, true)) {
                            $batchSkillInsert[] = [
                                'company_id' => $user->company_id,
                                'job_id' => $job->id,
                                'skill_name' => $value,
                            ];
                        }
                    }

                    if (count($batchSkillInsert) > 0) {
                        DB::table('job_metronics_skills')->insert($batchSkillInsert);
                    }
                }
            }

            // 8. Legacy jd_details Write Freeze:
            // Under Talent Taxonomy V2 architecture, legacy jd_details is frozen for mutations
            // (enforced by MySQL trigger trg_freeze_jd_details_insert). Recruiter designations
            // are canonically resolved and preserved in jobs.raw_designation & canonical_designation_id.

            // 9. Job Additional Details
            if (! empty($requestData['custom_title']) && is_array($requestData['custom_title'])) {
                $batchCustomInsert = [];
                foreach ($requestData['custom_title'] as $adArr) {
                    if (! empty($adArr)) {
                        $batchCustomInsert[] = [
                            'company_id' => $user->company_id,
                            'job_id' => $job->id,
                            'detail_value' => $adArr,
                        ];
                    }
                }
                if (count($batchCustomInsert) > 0) {
                    DB::table('job_additional_detail')->insert($batchCustomInsert);
                }
            }

            // 10. Batch Recruiter Assignment & Captain Invariant (Section 2.3)
            $cc = [];
            if (! empty($requestData['recruiter_id'])) {
                $rawRecArr = is_array($requestData['recruiter_id'])
                    ? $requestData['recruiter_id']
                    : explode(',', (string) $requestData['recruiter_id']);

                $recruiterArr = array_values(array_unique(array_filter(array_map('trim', $rawRecArr), function ($val) {
                    return $val !== '' && is_numeric($val) && (int) $val > 0;
                })));

                if (count($recruiterArr) > 0) {
                    $recUsers = DB::table('users')
                        ->select('id', 'email')
                        ->where('company_id', $user->company_id)
                        ->whereIn('id', $recruiterArr)
                        ->where('status', 'active')
                        ->get()
                        ->keyBy('id');

                    $batchRecInsert = [];
                    $primaryRecruiterId = null;

                    foreach ($recruiterArr as $recId) {
                        $recRow = $recUsers->get($recId);
                        if ($recRow) {
                            $cc[] = $recRow->email;
                            if ($primaryRecruiterId === null) {
                                $primaryRecruiterId = (int) $recRow->id;
                            }
                            $batchRecInsert[] = [
                                'company_id' => $user->company_id,
                                'user_id' => $recRow->id,
                                'spok_user_id' => null, // Initialized null, atomically updated below
                                'job_id' => $job->id,
                            ];
                            GeneralMethod::cacheClean('alljobslist_version_user_'.$recRow->id);
                        }
                    }

                    if (count($batchRecInsert) > 0) {
                        // 1. Persist recruiter assignment batch
                        DB::table('job_assign')->insert($batchRecInsert);

                        // 2. Clear any conflicting spokesperson records for this company & job
                        DB::table('job_assign')
                            ->where('job_id', $job->id)
                            ->where('company_id', $user->company_id)
                            ->update(['spok_user_id' => null]);

                        // 3. Atomically assign Captain to primary recruiter (earliest assignment order)
                        if ($primaryRecruiterId > 0) {
                            DB::table('job_assign')
                                ->where('job_id', $job->id)
                                ->where('user_id', $primaryRecruiterId)
                                ->where('company_id', $user->company_id)
                                ->update(['spok_user_id' => $primaryRecruiterId]);
                        }

                        app(\App\Services\RelationshipManagerService::class)->invalidateCompanyRoster((int) $user->company_id);
                    }
                }
            }

            // 11. Batch Hiring Manager Assignment with Strict Tenancy & Client Scope
            if ((! empty($requestData['hiring_namager']) || ! empty($requestData['hiring_manager_id'])) && ! empty($requestData['client_id'])) {
                $rawHmArr = ! empty($requestData['hiring_namager'])
                    ? (is_array($requestData['hiring_namager']) ? $requestData['hiring_namager'] : explode(',', (string) $requestData['hiring_namager']))
                    : [];
                $hmNames = array_values(array_unique(array_filter(array_map('trim', $rawHmArr))));

                $rawHmIds = ! empty($requestData['hiring_manager_id'])
                    ? (is_array($requestData['hiring_manager_id']) ? $requestData['hiring_manager_id'] : explode(',', (string) $requestData['hiring_manager_id']))
                    : [];
                $hmIds = array_values(array_unique(array_filter(array_map('intval', $rawHmIds), fn ($id) => $id > 0)));

                if (count($hmNames) > 0 || count($hmIds) > 0) {
                    $clientId = (int) $requestData['client_id'];
                    $hmQuery = DB::table('client_contact_info')
                        ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
                        ->select('client_contact_info.id', 'client_contact_info.contact_person_name')
                        ->where('client_contact_info.client_id', $clientId)
                        ->where('clients.company_id', $user->company_id);

                    if (! empty($hmIds) && ! empty($hmNames)) {
                        $hmQuery->where(function ($q) use ($hmIds, $hmNames) {
                            $q->whereIn('client_contact_info.id', $hmIds)
                                ->orWhereIn('client_contact_info.contact_person_name', $hmNames);
                        });
                    } elseif (! empty($hmIds)) {
                        $hmQuery->whereIn('client_contact_info.id', $hmIds);
                    } else {
                        $hmQuery->whereIn('client_contact_info.contact_person_name', $hmNames);
                    }

                    $availableHmRows = $hmQuery->get();

                    $batchHmInsert = [];
                    $insertedContactIds = [];

                    // 1. Match by ID first
                    foreach ($hmIds as $idVal) {
                        $matched = $availableHmRows->firstWhere('id', $idVal);
                        if ($matched && ! in_array($matched->id, $insertedContactIds)) {
                            $batchHmInsert[] = [
                                'company_id' => $user->company_id,
                                'client_contact_info_id' => $matched->id,
                                'job_id' => $job->id,
                            ];
                            $insertedContactIds[] = $matched->id;
                        }
                    }

                    // 2. Match by Name (case-insensitive for resilient persistence)
                    foreach ($hmNames as $cpName) {
                        $lowerCp = strtolower($cpName);
                        $matched = $availableHmRows->first(function ($r) use ($lowerCp) {
                            return strtolower(trim($r->contact_person_name)) === $lowerCp;
                        });
                        if ($matched && ! in_array($matched->id, $insertedContactIds)) {
                            $batchHmInsert[] = [
                                'company_id' => $user->company_id,
                                'client_contact_info_id' => $matched->id,
                                'job_id' => $job->id,
                            ];
                            $insertedContactIds[] = $matched->id;
                        }
                    }

                    if (count($batchHmInsert) > 0) {
                        DB::table('client_contact_info_jobs')->insert($batchHmInsert);
                    }
                }
            }

            // 12. Notification Email Dispatch
            if (GeneralMethod::mailPermission(10)) {
                $companyId = $user->company_id;
                $mailTypeSubject = GeneralMethod::mailTypeSubject(10, 1);
                $notificationemail = GeneralMethod::notification($companyId, 'Recuitment');

                $strAdded = DB::table('clients')
                    ->select('companies.company_name', 'clients.name', 'clients.self_company')
                    ->join('companies', 'companies.id', '=', 'clients.company_id')
                    ->where('clients.id', $requestData['client_id'] ?? 0)
                    ->first();

                $contactPersonName = $strAdded->company_name ?? '';
                if (! empty($strAdded) && $strAdded->self_company != 1 && $user->company_id == $job->company_id) {
                    $contactPersonName = $strAdded->name ?? '';
                }

                $locationStr = '';
                $clientLoc = DB::table('client_city_jobs')
                    ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                    ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                    ->join('countries', 'countries.id', '=', 'state_list.country_id')
                    ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
                    ->where('client_city_jobs.job_id', $job->id)
                    ->get();

                if (count($clientLoc) > 0) {
                    foreach ($clientLoc as $idx => $cval) {
                        $locationStr .= ($idx + 1).') '.$cval->city_name.' | '.$cval->state_name.' | '.$cval->country_name.'<br>';
                    }
                }

                $oprnlink = url('/service_company/mail_jobs_ajax/'.encrypt($job->id).'/');
                $subject = str_replace(
                    ['[{JOB_TITLE}]', '[{ClientCompany}]', '[{Location}]', '[{JobID}]'],
                    [$job->title, $contactPersonName, $locationStr, $job->id],
                    $mailTypeSubject->subject
                );

                $maillink = "You may access and manage the job posting using the link below:<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>Click here to open the job</a> <br><br> If the above link does not open directly, you may copy and paste the below link it into your browser.<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a>';

                $statusJob = "<strong><span style='color:#27ae60'>Hiring</span></strong>";
                if ($job->status === 'inactive') {
                    $statusJob = "<strong><span style='color:#ff0202'>Closed</span></strong>";
                } elseif ($job->status === 'hold') {
                    $statusJob = "<strong><span style='color:#ffab00'>Hold</span></strong>";
                }

                $salaryMode = GeneralMethod::salaryMode();
                $currencyGet = GeneralMethod::currencySymboleforexcell();
                $maxSalMode = strtoupper(substr($salaryMode[$job->max_sal_mode] ?? '', 0, 1));
                $employmentType = GeneralMethod::employmentType()[(int) $job->employment_type] ?? '';

                $workType = 'Work From Office';
                if ($job->work_type === 'yes') {
                    $workType = 'Remote';
                } elseif ($job->work_type === 'other') {
                    $workType = 'Hybrid';
                }

                $jobAdditional = Job::select('jobs.id', 'job_categories.name as cat_name', 'qualification_master.name as qualification_name')
                    ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
                    ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
                    ->withoutGlobalScopes()->find($job->id);

                $hmTemplateVal = is_array($requestData['hiring_namager'] ?? null)
                    ? implode(', ', array_filter($requestData['hiring_namager']))
                    : (string) ($requestData['hiring_namager'] ?? '');

                $recruiterTemplateVal = is_array($requestData['recruiter'] ?? null)
                    ? implode(', ', array_filter($requestData['recruiter']))
                    : (string) ($requestData['recruiter'] ?? '');

                $templateDescription = str_replace(
                    [
                        '[{status}]', '[{clientCompany}]', '[{job_id}]', '[{job_title}]', '[{experience}]',
                        '[{salaryrange}]', '[{NoofOpenings}]', '[{location}]', '[{remote}]', '[{JobType}]',
                        '[{Department}]', '[{Qualification}]', '[{hiringManager}]', '[{recruiter}]', '[{addedby}]',
                        '[{JobDescriptions}]', '[{maillink}]',
                    ],
                    [
                        ucfirst($statusJob),
                        $contactPersonName,
                        $job->id,
                        $job->title,
                        $job->min_exp.'-'.$job->max_exp.' Years',
                        ($currencyGet[$job->min_salary_currency] ?? '').' '.$job->min_salary.' to '.$job->max_salary.' '.$maxSalMode,
                        $requestData['total_positions'] ?? '',
                        $locationStr,
                        $workType,
                        $employmentType,
                        $jobAdditional->cat_name ?? '',
                        $jobAdditional->qualification_name ?? '',
                        $hmTemplateVal,
                        $recruiterTemplateVal,
                        $user->name,
                        $job->job_description,
                        $maillink,
                    ],
                    $mailTypeSubject->body_description
                );

                $defailsEmail = DB::table('companies')->where('id', $companyId)->first()->company_email ?? '';
                $toMailId = $notificationemail->email ?? $defailsEmail;

                QueueNotificationMailService::dispatch(
                    $toMailId,
                    $subject,
                    'email.common.index',
                    (object) ['message' => $templateDescription],
                    [],
                    $user->email ?? '',
                    $user->name ?? '',
                    $cc,
                    $companyId
                )->onConnection('emails');
            }

            // 13. Canonical Transactional Outbox (Guarantees projection & notification durability)
            try {
                DB::table('sys_outbox_events')->insert([
                    'tenant_id' => (int) $user->company_id,
                    'idempotency_key' => ! empty($requestData['operation_id']) ? (string) $requestData['operation_id'] : null,
                    'aggregate_type' => 'job',
                    'aggregate_id' => (int) $job->id,
                    'aggregate_version' => 1,
                    'event_type' => 'job.created',
                    'payload' => json_encode([
                        'job_id' => (int) $job->id,
                        'company_id' => (int) $user->company_id,
                        'user_id' => (int) $user->id,
                        'title' => (string) $job->title,
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $oe) {
                Log::warning('[JobService@createJob] Outbox persistence error: '.$oe->getMessage());
            }

            GeneralMethod::cacheClean('alljobslist_version_user_'.$user->id);
            GeneralMethod::cacheClean('alljobslist_version_company_'.$user->company_id);
            cache()->increment('alljobslist_version_company_'.$user->company_id);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[JobService@createJob] Transaction failed: '.$e->getMessage(), ['exception' => $e]);
            throw $e;
        }

        // Post-commit Asynchronous Elasticsearch Indexing via Dedicated Queue
        try {
            if (class_exists(ElasticJobsIndexJob::class)) {
                ElasticJobsIndexJob::dispatch($job->id)->onQueue('es-index');
            }
        } catch (\Throwable $e) {
            Log::error('[JobService@createJob] ES Indexing queue dispatch failed: '.$e->getMessage());
        }

        return $job;
    }

    /**
     * Update an existing Job Requisition with batch pivot updates and asynchronous Elastic indexing.
     *
     * @param  Job  $job  Existing job model to update
     * @param  array  $requestData  Input request payload
     * @param  User  $user  Authenticated user instance
     * @return Job Updated job model
     */
    public function updateJob(Job $job, array $requestData, User $user): Job
    {
        $job->client_id = $requestData['client_id'] ?? null;
        $job->title = $requestData['title'] ?? null;
        if (array_key_exists('raw_designation', $requestData)) {
            $job->raw_designation = $requestData['raw_designation'];
        }
        if (array_key_exists('canonical_designation_id', $requestData)) {
            $job->canonical_designation_id = ! empty($requestData['canonical_designation_id']) ? (int) $requestData['canonical_designation_id'] : null;
        }
        if (array_key_exists('taxonomy_version', $requestData)) {
            $job->taxonomy_version = $requestData['taxonomy_version'];
        }
        $job->work_shifts = $requestData['work_shifts'] ?? null;
        if (array_key_exists('category_id', $requestData)) {
            $job->category_id = ! empty($requestData['category_id']) && is_numeric($requestData['category_id']) ? (int) $requestData['category_id'] : null;
        }
        if (empty($job->category_id) && (! empty($requestData['custom_department']) || ! empty($requestData['category_id_display']))) {
            $customDept = trim($requestData['custom_department'] ?? $requestData['category_id_display']);
            if ($customDept !== '') {
                $targetCompanyId = $user->company_id ?? (auth()->check() ? auth()->user()->company_id : null);
                $cat = null;
                if ($targetCompanyId) {
                    $cat = \App\Models\JobCategory::where('company_id', $targetCompanyId)
                        ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($customDept)])
                        ->first();
                }
                if (! $cat) {
                    $cat = \App\Models\JobCategory::whereNull('company_id')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($customDept)])
                        ->first();
                }
                if (! $cat && $targetCompanyId) {
                    $cat = \App\Models\JobCategory::create([
                        'company_id' => $targetCompanyId,
                        'name' => $customDept,
                    ]);
                }
                if ($cat) {
                    $job->category_id = $cat->id;
                }
            }
        }
        $job->required_columns = $requestData['required_columns'] ?? null;

        if (! empty($requestData['employment_type'])) {
            $job->employment_type = $requestData['employment_type'];
        }
        if (! empty($requestData['total_positions'])) {
            $job->total_positions = $requestData['total_positions'];
        }
        if (! empty($requestData['min_exp'])) {
            $job->min_exp = $requestData['min_exp'];
        }
        if (! empty($requestData['max_exp'])) {
            $job->max_exp = $requestData['max_exp'];
        }

        $startDate = $job->start_date;
        $endDate = date('Y-m-d', strtotime($startDate.' + '.($requestData['end_date'] ?? 0).' days'));
        $job->end_date = $endDate;
        $job->priority = 0;
        $job->location_id = 1;

        $metaTitle = $requestData['meta_title'] ?? ($requestData['title'] ?? '');
        $metaDesc = $requestData['meta_description'] ?? strip_tags(Str::substr(html_entity_decode($requestData['job_description'] ?? ''), 0, 150));
        $job->meta_details = [
            'title' => $metaTitle,
            'description' => $metaDesc,
        ];

        $tenantDefaultCurrencyKey = \App\Services\CurrencyService::getTenantCurrencyKey($job->company_id);
        $job->min_salary_currency = (!isset($requestData['min_salary_currency']) || $requestData['min_salary_currency'] === '' || $requestData['min_salary_currency'] === null)
            ? $tenantDefaultCurrencyKey
            : (int) $requestData['min_salary_currency'];
        $job->min_salary = (! empty($requestData['minimum_salary']) && is_numeric($requestData['minimum_salary'])) ? (int) $requestData['minimum_salary'] : 0;
        $job->min_sal_mode = $requestData['min_sal_mode'] ?? null;

        $job->max_salary_currency = (!isset($requestData['max_salary_currency']) || $requestData['max_salary_currency'] === '' || $requestData['max_salary_currency'] === null)
            ? $job->min_salary_currency
            : (int) $requestData['max_salary_currency'];
        $job->max_salary = (! empty($requestData['maximum_salary']) && is_numeric($requestData['maximum_salary'])) ? (int) $requestData['maximum_salary'] : 0;
        $job->max_sal_mode = $requestData['max_sal_mode'] ?? null;
        if (isset($requestData['salary_visibility'])) {
            $job->salary_visibility = \App\Services\SalaryVisibilityPolicy::normalize($requestData['salary_visibility']);
        }

        $isInternalOnly = isset($requestData['visibility_internal']) && (int) $requestData['visibility_internal'] === 1;
        $hasCareer = isset($requestData['carrier_website']) ? ((int) $requestData['carrier_website'] === 1) : (! empty($requestData['visibility_career']));
        $hasMarketplace = ! empty($requestData['visibility_marketplace']);
        $hasBench = ! empty($requestData['visibility_bench_talent']) || (($requestData['bench_submission'] ?? '') === 'enabled');

        if ($isInternalOnly) {
            $job->carrier_website = 0;
            $job->market_pub_state = MarketPubState::UNPUBLISHED->value;
            $job->bench_submission = BenchSubmissionState::DISABLED->value;
            $job->public_market_status = 0;
        } else {
            $job->carrier_website = $hasCareer ? 1 : 0;
            $job->bench_submission = $hasBench ? BenchSubmissionState::ENABLED->value : BenchSubmissionState::DISABLED->value;

            if (isset($requestData['visibility_marketplace']) || isset($requestData['visibility_bench_talent']) || isset($requestData['public_market_status'])) {
                if ($hasMarketplace || $hasBench) {
                    // Check if already approved
                    if (! in_array($job->market_mod_state, [MarketModState::APPROVED->value, MarketModState::AUTO_APPROVED->value])) {
                        $moderationService = app(\App\Services\MarketplaceModerationService::class);
                        $eval = $moderationService->evaluateRisk([
                            'title' => $job->title,
                            'job_description' => $job->job_description ?? ($requestData['detail_description'] ?? ''),
                            'job_requirement' => $job->job_requirement ?? '',
                            'min_salary' => (! empty($requestData['minimum_salary']) && is_numeric($requestData['minimum_salary'])) ? (int) $requestData['minimum_salary'] : 0,
                            'max_salary' => (! empty($requestData['maximum_salary']) && is_numeric($requestData['maximum_salary'])) ? (int) $requestData['maximum_salary'] : 0,
                            'company_id' => $job->company_id,
                            'id' => $job->id,
                        ]);

                        $autoApproveSetting = (bool) DB::table('global_settings')->value('public_market_job_status');
                        if (! $eval['hard_blocked'] && $eval['risk_score'] < 0.20 && $autoApproveSetting) {
                            $job->market_pub_state = MarketPubState::PUBLISHED->value;
                            $job->market_mod_state = MarketModState::AUTO_APPROVED->value;
                        } else {
                            $job->market_pub_state = MarketPubState::PENDING_REVIEW->value;
                            $job->market_mod_state = MarketModState::PENDING->value;
                        }
                    } else {
                        $job->market_pub_state = MarketPubState::PUBLISHED->value;
                    }
                    $job->claim_capacity = $hasMarketplace ? ClaimCapacity::OPEN->value : ClaimCapacity::LIMIT_REACHED->value;
                } else {
                    $job->market_pub_state = MarketPubState::UNPUBLISHED->value;
                }
            }
        }
        $job->work_type = $requestData['work_from_home'] ?? null;
        $job->show_vendor = $requestData['show_client_name'] ?? null;

        if (! empty($requestData['detail_description'])) {
            $job->job_description = $requestData['detail_description'];
        }

        $currency = GeneralMethod::currency();

        $maxSalCurr = $requestData['max_salary_currency'] ?? null;
        $ecurrency = $currency[$maxSalCurr] ?? null;
        $econvertedAmount = (! empty($requestData['maximum_salary']) && is_numeric($requestData['maximum_salary'])) ? (float) $requestData['maximum_salary'] : 0;
        if ($maxSalCurr > 0 && $ecurrency && $econvertedAmount > 0) {
            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
        }
        $ectcdivided = GeneralMethod::salaryDividedByMode($requestData['max_sal_mode'] ?? null, $econvertedAmount);
        $job->max_yearly = $ectcdivided->yearly ?? null;
        $job->max_monthly = $ectcdivided->monthly ?? null;
        $job->max_hourly = $ectcdivided->hourly ?? null;

        $minSalCurr = $requestData['min_salary_currency'] ?? null;
        $mcurrency = $currency[$minSalCurr] ?? null;
        $convertedAmount = (! empty($requestData['minimum_salary']) && is_numeric($requestData['minimum_salary'])) ? (float) $requestData['minimum_salary'] : 0;
        if ($minSalCurr > 0 && $mcurrency && $convertedAmount > 0) {
            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $mcurrency);
        }
        $ctcdivided = GeneralMethod::salaryDividedByMode($requestData['min_sal_mode'] ?? null, $convertedAmount);
        $job->min_yearly = $ctcdivided->yearly ?? null;
        $job->min_monthly = $ctcdivided->monthly ?? null;
        $job->min_hourly = $ctcdivided->hourly ?? null;

        DB::beginTransaction();
        try {
            // Batch qualification update
            $workQualification = $requestData['qualification'] ?? null;
            if (! empty($workQualification)) {
                DB::table('job_qualifications')->where('job_id', $job->id)->delete();
                $workQualificationAll = is_array($workQualification) ? $workQualification : json_decode($workQualification, true);
                if (is_array($workQualificationAll) && count($workQualificationAll) > 0) {
                    $qualNames = array_column($workQualificationAll, 'value');
                    $qualMasterMap = DB::table('qualification_master')
                        ->select('id', 'name')
                        ->whereIn('name', $qualNames)
                        ->get()
                        ->keyBy('name');

                    $batchQualInsert = [];
                    foreach ($workQualificationAll as $idx => $qVal) {
                        $qName = $qVal['value'] ?? null;
                        if (! $qName) {
                            continue;
                        }

                        $qRow = $qualMasterMap->get($qName);
                        if ($idx === 0 && $qRow) {
                            $job->qualification = $qRow->id;
                        }

                        if ($qRow) {
                            $batchQualInsert[] = [
                                'job_id' => $job->id,
                                'qualification_id' => $qRow->id,
                                'qualification_name' => $qRow->name,
                            ];
                        }
                    }

                    if (count($batchQualInsert) > 0) {
                        DB::table('job_qualifications')->insert($batchQualInsert);
                    }
                }
            }

            $job->save();

            // Client Location
            if (! empty($requestData['address_id'])) {
                DB::table('client_location_jobs')->where('job_id', $job->id)->delete();
                DB::table('client_location_jobs')->insert([
                    'company_id' => $user->company_id,
                    'client_location_id' => $requestData['address_id'],
                    'job_id' => $job->id,
                ]);
            }

            // Batch Work City Processing (Replaced wildcard pattern search with exact batch lookup)
            $workCity = $requestData['kt_tagify_work_location'] ?? null;
            if (! empty($workCity)) {
                DB::table('client_city_jobs')->where('job_id', $job->id)->delete();
                $workCityAll = is_array($workCity) ? $workCity : json_decode($workCity, true);

                if (is_array($workCityAll) && count($workCityAll) > 0) {
                    $cityNames = [];
                    $countryNames = [];
                    $cityPairs = [];

                    foreach ($workCityAll as $cVal) {
                        $rawVal = (string) ($cVal['value'] ?? '');
                        $parts = strpos($rawVal, '|') !== false ? explode('|', $rawVal) : explode(',', $rawVal);
                        if (count($parts) >= 3) {
                            $cName = trim($parts[0]);
                            $coName = trim(end($parts));
                            $cityNames[] = $cName;
                            $countryNames[] = $coName;
                            $cityPairs[] = ['city' => $cName, 'country' => $coName];
                        } elseif (count($parts) >= 1 && trim($parts[0]) !== '') {
                            $cName = trim($parts[0]);
                            $cityNames[] = $cName;
                            $cityPairs[] = ['city' => $cName, 'country' => null];
                        }
                    }

                    if (count($cityNames) > 0) {
                        $cityQuery = DB::table('city_list')
                            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->join('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city_id', 'city_list.city', 'countries.id as country_id', 'countries.country_name')
                            ->whereIn('city_list.city', array_unique($cityNames));
                        $nonEmptyCountries = array_values(array_filter($countryNames));
                        if (! empty($nonEmptyCountries)) {
                            $cityQuery->whereIn('countries.country_name', array_unique($nonEmptyCountries));
                        }
                        $cityRows = $cityQuery->get();

                        $batchCityInsert = [];
                        foreach ($cityPairs as $pair) {
                            $match = $cityRows->first(function ($item) use ($pair) {
                                $itemCity = trim((string) $item->city);
                                $pairCity = trim((string) $pair['city']);

                                if (empty($pair['country'])) {
                                    return strcasecmp($itemCity, $pairCity) === 0;
                                }

                                $itemCountry = trim((string) $item->country_name);
                                $pairCountry = trim((string) $pair['country']);

                                return strcasecmp($itemCity, $pairCity) === 0
                                    && strcasecmp($itemCountry, $pairCountry) === 0;
                            });

                            if ($match) {

                                $batchCityInsert[] = [
                                    'company_id' => $user->company_id,
                                    'city_id' => $match->city_id,
                                    'country_id' => $match->country_id,
                                    'job_id' => $job->id,
                                ];
                            }
                        }

                        if (count($batchCityInsert) > 0) {
                            DB::table('client_city_jobs')->insert($batchCityInsert);
                        }
                    }
                }
            }

            // Job Countries
            if (! empty($requestData['kt_select2_4']) && is_array($requestData['kt_select2_4'])) {
                DB::table('job_countries')->where('job_id', $job->id)->delete();
                $batchCountryInsert = [];
                foreach ($requestData['kt_select2_4'] as $ld) {
                    $batchCountryInsert[] = [
                        'company_id' => $user->company_id,
                        'country_id' => $ld,
                        'job_id' => $job->id,
                    ];
                }
                if (count($batchCountryInsert) > 0) {
                    DB::table('job_countries')->insert($batchCountryInsert);
                }
            }

            // Hiring Managers
            if ((! empty($requestData['hiring_namager']) || ! empty($requestData['hiring_manager_id'])) && ! empty($requestData['client_id'])) {
                DB::table('client_contact_info_jobs')->where('job_id', $job->id)->delete();

                $rawHmArr = ! empty($requestData['hiring_namager'])
                    ? (is_array($requestData['hiring_namager']) ? $requestData['hiring_namager'] : explode(',', (string) $requestData['hiring_namager']))
                    : [];
                $hmNames = array_values(array_unique(array_filter(array_map('trim', $rawHmArr))));

                $rawHmIds = ! empty($requestData['hiring_manager_id'])
                    ? (is_array($requestData['hiring_manager_id']) ? $requestData['hiring_manager_id'] : explode(',', (string) $requestData['hiring_manager_id']))
                    : [];
                $hmIds = array_values(array_unique(array_filter(array_map('intval', $rawHmIds), fn ($id) => $id > 0)));

                if (count($hmNames) > 0 || count($hmIds) > 0) {
                    $clientId = (int) $requestData['client_id'];
                    $hmQuery = DB::table('client_contact_info')
                        ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
                        ->select('client_contact_info.id', 'client_contact_info.contact_person_name')
                        ->where('client_contact_info.client_id', $clientId)
                        ->where('clients.company_id', $user->company_id);

                    if (! empty($hmIds) && ! empty($hmNames)) {
                        $hmQuery->where(function ($q) use ($hmIds, $hmNames) {
                            $q->whereIn('client_contact_info.id', $hmIds)
                                ->orWhereIn('client_contact_info.contact_person_name', $hmNames);
                        });
                    } elseif (! empty($hmIds)) {
                        $hmQuery->whereIn('client_contact_info.id', $hmIds);
                    } else {
                        $hmQuery->whereIn('client_contact_info.contact_person_name', $hmNames);
                    }

                    $availableHmRows = $hmQuery->get();

                    $batchHmInsert = [];
                    $insertedContactIds = [];

                    // 1. Match by ID first
                    foreach ($hmIds as $idVal) {
                        $matched = $availableHmRows->firstWhere('id', $idVal);
                        if ($matched && ! in_array($matched->id, $insertedContactIds)) {
                            $batchHmInsert[] = [
                                'company_id' => $user->company_id,
                                'client_contact_info_id' => $matched->id,
                                'job_id' => $job->id,
                            ];
                            $insertedContactIds[] = $matched->id;
                        }
                    }

                    // 2. Match by Name (case-insensitive for resilient persistence)
                    foreach ($hmNames as $cpName) {
                        $lowerCp = strtolower($cpName);
                        $matched = $availableHmRows->first(function ($r) use ($lowerCp) {
                            return strtolower(trim($r->contact_person_name)) === $lowerCp;
                        });
                        if ($matched && ! in_array($matched->id, $insertedContactIds)) {
                            $batchHmInsert[] = [
                                'company_id' => $user->company_id,
                                'client_contact_info_id' => $matched->id,
                                'job_id' => $job->id,
                            ];
                            $insertedContactIds[] = $matched->id;
                        }
                    }

                    if (count($batchHmInsert) > 0) {
                        DB::table('client_contact_info_jobs')->insert($batchHmInsert);
                    }
                }
            }

            // Recruiters
            if (! empty($requestData['recruiter']) && ! empty($requestData['recruiter_id'])) {

                $recruiterArr = array_map('trim', explode(',', $requestData['recruiter_id']));
                // DB::table('job_assign')->where('job_id', $job->id)->where('company_id', $user->company_id)->delete();
                DB::table('job_assign')->where('job_id', $job->id)->where('company_id', $user->company_id)->whereNotIn('user_id', $recruiterArr)->delete();

                $recUsers = DB::table('users')
                    ->select('id')
                    ->where('company_id', $user->company_id)
                    ->whereIn('id', $recruiterArr)
                    ->where('status', 'active')
                    ->get()
                    ->keyBy('id');

                $batchRecInsert = [];
                foreach ($recruiterArr as $key => $recId) {

                    $recRow = $recUsers->get($recId);

                    if (! $recRow) {
                        continue;
                    }

                    $exists = DB::table('job_assign')
                        ->where('job_id', $job->id)
                        ->where('company_id', $user->company_id)
                        ->where('user_id', $recRow->id)
                        ->exists();

                    if (! $exists) {

                        $batchRecInsert[] = [
                            'company_id' => $user->company_id,
                            'user_id' => $recRow->id,
                            'spok_user_id' => ($key === 0) ? $recRow->id : null,
                            'job_id' => $job->id,
                        ];

                        GeneralMethod::cacheClean(
                            'alljobslist_version_user_'.$recRow->id
                        );
                    }
                }
                if (count($batchRecInsert) > 0) {
                    DB::table('job_assign')->insert($batchRecInsert);
                }

                app(\App\Services\RelationshipManagerService::class)->invalidateCompanyRoster((int) $user->company_id);
            }

            // Skills
            $skillsUpdated = false;
            $currentSkillsList = [];
            if (isset($requestData['tags'])) {
                $tags = is_array($requestData['tags']) ? $requestData['tags'] : json_decode($requestData['tags'], true);
                if (is_array($tags) && count($tags) > 0) {
                    $skillsAddElastic = array_column($tags, 'value');
                    if (count($skillsAddElastic) > 0) {
                        GeneralMethod::skillsAddElastic($skillsAddElastic);
                    }
                    $tagsArr = array_values(array_filter(array_map('trim', $skillsAddElastic)));
                    $currentSkillsList = $tagsArr;

                    DB::table('job_metronics_skills')->where('job_id', $job->id)->whereNotIn('skill_name', $tagsArr)->delete();
                    $exitsSkill = DB::table('job_metronics_skills')->where('job_id', $job->id)->pluck('skill_name')->toArray();

                    $notCommon = array_diff($tagsArr, $exitsSkill);

                    if (count($notCommon) > 0) {
                        $batchSkillInsert = [];
                        foreach ($notCommon as $value) {
                            $batchSkillInsert[] = [
                                'company_id' => $user->company_id,
                                'job_id' => $job->id,
                                'skill_name' => $value,
                            ];
                        }
                        if (count($batchSkillInsert) > 0) {
                            DB::table('job_metronics_skills')->insert($batchSkillInsert);
                        }
                    }
                    $skillsUpdated = true;
                } else {
                    DB::table('job_metronics_skills')->where('job_id', $job->id)->delete();
                    $skillsUpdated = true;
                }
            }

            // Additional Details
            if (! empty($requestData['custom_title']) && is_array($requestData['custom_title'])) {
                DB::table('job_additional_detail')->where('job_id', $job->id)->whereNotIn('detail_value', $requestData['custom_title'])->delete();
                $exitsDetails = DB::table('job_additional_detail')->where('job_id', $job->id)->pluck('detail_value')->toArray();

                $notCommonDetails = array_merge(
                    array_diff($requestData['custom_title'], $exitsDetails),
                    array_diff($exitsDetails, $requestData['custom_title'])
                );

                if (count($notCommonDetails) > 0) {
                    $batchDetailInsert = [];
                    foreach ($notCommonDetails as $adArr) {
                        if (! empty($adArr)) {
                            $batchDetailInsert[] = [
                                'company_id' => $user->company_id,
                                'job_id' => $job->id,
                                'detail_value' => $adArr,
                            ];
                        }
                    }
                    if (count($batchDetailInsert) > 0) {
                        DB::table('job_additional_detail')->insert($batchDetailInsert);
                    }
                }
            }

            GeneralMethod::cacheClean('alljobslist_version_user_'.$user->id);
            GeneralMethod::cacheClean('jobDetails_version_user_'.$user->id);
            GeneralMethod::cacheClean('alljobslist_version_company_'.$user->company_id);
            cache()->increment('alljobslist_version_company_'.$user->company_id);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[JobService@updateJob] Transaction failed: '.$e->getMessage(), ['exception' => $e]);
            throw $e;
        }

        // Post-commit Elasticsearch Indexing (Immediate + Async Queue)
        try {
            GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);
            if (class_exists(ElasticJobsIndexJob::class)) {
                ElasticJobsIndexJob::dispatch($job->id)->onQueue('es-index');
            }
        } catch (\Throwable $e) {
            Log::error('[JobService@updateJob] ES Indexing failed: '.$e->getMessage());
        }

        // Multi-Tenant Atomic Cache Invalidation & Reverb Synchronization
        try {
            // A. Bust Job Caches across Redis
            \Illuminate\Support\Facades\Cache::forget('jobDetails_version_company_'.$user->company_id);
            \Illuminate\Support\Facades\Cache::forget('v3:status_job:'.$job->id);
            \Illuminate\Support\Facades\Cache::forget('v3:status_log:'.$job->id);
            \Illuminate\Support\Facades\Cache::forget('job_skills_metronic:'.$job->id);

            // B. Invalidate candidate drawer caches for all candidates attached to this job
            $linkedCandidates = DB::table('add_job_wise_condidates')
                ->where('job_id', $job->id)
                ->select('job_application_id', 'id')
                ->get();

            $candIds = $linkedCandidates->pluck('job_application_id')->filter()->unique()->values()->toArray();
            $wiseIds = $linkedCandidates->pluck('id')->filter()->unique()->values()->toArray();
            $targetIds = array_unique(array_merge($candIds, $wiseIds));

            $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;
            $slugs = ['', 'cvtracker', 'apptracker', 'interview-schedule'];

            foreach ($targetIds as $tid) {
                foreach ($slugs as $slug) {
                    \Illuminate\Support\Facades\Cache::forget('tenant:'.$user->company_id.':candidate_details:'.$tid.':'.md5($slug).':view:'.$drawerViewVersion);
                }
            }

            // C. Broadcast JobSkillsUpdated over Laravel Reverb to synchronize all recruiter browser sessions
            if (class_exists(\App\Events\JobSkillsUpdated::class)) {
                \App\Events\JobSkillsUpdated::dispatch(
                    (int) $user->company_id,
                    (int) $job->id,
                    array_map('intval', $candIds),
                    array_map('intval', $wiseIds),
                    $currentSkillsList,
                    time()
                );
            }
        } catch (\Throwable $cacheEx) {
            Log::warning('[JobService@updateJob] Post-update cache invalidation warning: '.$cacheEx->getMessage());
        }

        return $job;
    }
}
