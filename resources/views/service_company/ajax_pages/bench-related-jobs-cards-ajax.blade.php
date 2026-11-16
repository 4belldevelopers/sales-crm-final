@php
    use Carbon\Carbon;
    use App\Helper\GeneralMethod;
@endphp

@if (!empty($jobs) && count($jobs) > 0)
    <div class="rj-cards-stream d-flex flex-column gap-3">
        @foreach ($jobs as $job)
            @php
                $jobId = (int) ($job['id'] ?? 0);
                $jobTitle = trim((string) ($job['title'] ?? 'Untitled Position'));
                $clientName = trim((string) ($job['client_name'] ?? ''));
                $companyName = trim((string) ($job['company_name'] ?? ''));
                $isSelfCompany = ((int) ($job['self_company'] ?? 0) === 1);

                // Status & Badges
                $rawStatus = strtolower((string) ($job['status'] ?? 'active'));
                $statusLabel = $rawStatus === 'hold' ? 'HOLD' : 'OPEN';
                $statusBadgeCls = $rawStatus === 'hold' ? 'jc-badge-hold' : 'jc-badge-open';
                $reqId = 'REQ-JERO-' . $jobId;

                // Employment Type
                $empTypeKey = (int) ($job['employment_type'] ?? 0);
                $employment_type = match ($empTypeKey) {
                    1 => 'Contract',
                    2 => 'Freelance',
                    3 => 'Part-Time',
                    default => 'Full-Time',
                };
                $typeBadgeCls = match ($employment_type) {
                    'Contract' => 'jc-badge-ct',
                    'Freelance' => 'jc-badge-fl',
                    'Part-Time' => 'jc-badge-pt',
                    default => 'jc-badge-ft',
                };

                // Department / Category Name
                $deptName = $job['v2_category_name'] ?? ($job['category_name'] ?? ($job['department'] ?? ''));
                if (empty($deptName)) {
                    $deptName = !empty($clientName) ? ucwords($clientName) : (!empty($companyName) ? ucwords($companyName) : 'Cloud & DevOps Infrastructure');
                }

                // Mandate Tag & Openings
                $totalPositions = @$job['total_positions'] ?: (@$job['vacancies'] ?: 1);
                $mandateStyle = '';
                $mandateIconStyle = '';
                if ($category === 'my_org' || ($category === 'internal' && $isSelfCompany)) {
                    $mandateLabel = 'My Organization';
                    $mandateIcon = 'fa-id-badge';
                    $mandateStyle = 'background:#fef3c7; border:1px solid #fcd34d; color:#92400e;';
                    $mandateIconStyle = 'color:#d97706;';
                } elseif ($category === 'internal') {
                    $mandateLabel = 'Internal Client';
                    $mandateIcon = 'fa-building text-primary';
                    $mandateStyle = 'background:#eff6ff; border:1px solid #bfdbfe; color:#2563eb;';
                } else {
                    $mandateLabel = 'Client Mandate';
                    $mandateIcon = 'fa-shield-alt text-success';
                }

                // Salary formatting (Clean Unicode currency symbol, never raw HTML <i> tags)
                $currKey = $job['min_salary_currency'] ?? 0;
                $currencyList = GeneralMethod::currency();
                $currencyCode = $currencyList[$currKey] ?? (is_string($currKey) ? strtoupper($currKey) : 'INR');
                $symbolMap = [
                    'INR' => '₹',
                    'USD' => '$',
                    'EUR' => '€',
                    'GBP' => '£',
                    'JPY' => '¥',
                    'CAD' => 'CA$',
                    'AUD' => 'A$',
                    'SGD' => 'S$',
                    'AED' => 'AED ',
                    'CHF' => 'CHF ',
                    'NZD' => 'NZ$',
                    'ZAR' => 'R ',
                ];
                $currencySymbol = $symbolMap[$currencyCode] ?? '₹';
                $minSal = isset($job['min_salary']) && is_numeric($job['min_salary']) ? (float) $job['min_salary'] : 0;
                $maxSal = isset($job['max_salary']) && is_numeric($job['max_salary']) ? (float) $job['max_salary'] : 0;
                $salModeKey = (int) ($job['min_sal_mode'] ?? 0);
                $salModeShort = [0 => 'A', 1 => 'M', 2 => 'H'][$salModeKey] ?? 'A';

                $formatAmount = function($amt) {
                    if ($amt >= 100000) {
                        return ($amt / 100000 == (int)($amt / 100000) ? (int)($amt / 100000) : number_format($amt / 100000, 1)) . 'L';
                    } elseif ($amt >= 1000) {
                        return ($amt / 1000 == (int)($amt / 1000) ? (int)($amt / 1000) : number_format($amt / 1000, 1)) . 'K';
                    }
                    return number_format($amt);
                };

                if ($minSal > 0 || $maxSal > 0) {
                    if ($minSal > 0 && $maxSal > 0 && $minSal != $maxSal) {
                        $salaryDisplay = $currencySymbol . $formatAmount($minSal) . ' – ' . $formatAmount($maxSal) . ' / ' . $salModeShort;
                    } elseif ($maxSal > 0) {
                        $salaryDisplay = 'Up to ' . $currencySymbol . $formatAmount($maxSal) . ' / ' . $salModeShort;
                    } else {
                        $salaryDisplay = $currencySymbol . $formatAmount($minSal) . '+ / ' . $salModeShort;
                    }
                } else {
                    $salaryDisplay = 'As per Market Norms';
                }

                // Experience
                $minExp = isset($job['min_exp']) && is_numeric($job['min_exp']) ? (float) $job['min_exp'] : null;
                $maxExp = isset($job['max_exp']) && is_numeric($job['max_exp']) ? (float) $job['max_exp'] : null;
                if ($minExp !== null && $maxExp !== null && ($minExp > 0 || $maxExp > 0)) {
                    if ($minExp == $maxExp) {
                        $expFormatted = ($minExp == (int) $minExp ? (int) $minExp : $minExp) . ' Years Exp';
                    } elseif ($minExp == 0) {
                        $expFormatted = 'Up to ' . ($maxExp == (int) $maxExp ? (int) $maxExp : $maxExp) . ' Years Exp';
                    } else {
                        $expFormatted = ($minExp == (int) $minExp ? (int) $minExp : $minExp) . ' – ' . ($maxExp == (int) $maxExp ? (int) $maxExp : $maxExp) . ' Years Exp';
                    }
                } elseif ($minExp !== null && $minExp > 0) {
                    $expFormatted = ($minExp == (int) $minExp ? (int) $minExp : $minExp) . '+ Years Exp';
                } else {
                    $expFormatted = '1 – 3 Years Exp';
                }

                // Work Mode & Location
                $workType = strtolower(trim((string) ($job['work_type'] ?? 'no')));
                $workModeLabel = 'Work From Office';
                if ($workType === 'yes') {
                    $workModeLabel = 'Remote';
                } elseif ($workType === 'other') {
                    $workModeLabel = 'Hybrid';
                }

                $cities = [];
                if (!empty($job['client_city_jobs']) && is_array($job['client_city_jobs'])) {
                    foreach ($job['client_city_jobs'] as $c) {
                        if (!empty($c['city_name'])) {
                            $cities[] = ucwords(trim($c['city_name']));
                        }
                    }
                }
                $cities = array_unique($cities);
                $locationDisplay = !empty($cities) ? implode(', ', array_slice($cities, 0, 1)) : ($workType === 'yes' ? 'Remote' : 'Work From Office');

                // Skill Match Calculation
                $matchResult = $job['match_result'] ?? GeneralMethod::calculateSkillMatch($job['job_metronics_skills'] ?? [], $candidateSkills);
                $matchPercentage = (int) ($job['match_percentage'] ?? $matchResult['percentage'] ?? 0);
                $matchedCount = (int) ($matchResult['matched_count'] ?? 0);
                $benchMatchNumber = $matchPercentage > 0 ? ($matchPercentage . '%') : ($matchedCount > 0 ? $matchedCount : '0%');
                $benchMatchSub = $matchedCount > 0 ? ($matchedCount . ' Matching Skills') : 'Matching Candidates';

                // Commercial Terms
                $ct = $job['v2_commercial_terms'] ?? ($job['commercial_terms'] ?? null);
                if ($employment_type === 'Contract') {
                    $billingModel = ucfirst($ct['billing_model'] ?? 'Monthly');
                    $billingRate = !empty($ct['billing_rate']) ? $currencySymbol . number_format($ct['billing_rate']/1000, 0) . 'K/Mo' : $salaryDisplay;
                    $payTerms = ($ct['payment_days'] ?? 30) . ' Days';
                    $term1Label = 'Billing Model'; $term1Val = $billingModel;
                    $term2Label = 'Billing Rate';  $term2Val = $billingRate;
                    $term3Label = 'Payment Terms'; $term3Val = $payTerms;
                } else {
                    $term1Label = 'Hiring Fee';
                    $term1Val = !empty($ct['hiring_fee_pct']) ? ($ct['hiring_fee_pct'] . '% of CTC') : '8.33% of CTC';
                    $term2Label = 'Replacement';
                    $term2Val = !empty($ct['replacement_days']) ? ($ct['replacement_days'] . ' Days') : '90 Days';
                    $term3Label = 'Payment Terms';
                    $term3Val = !empty($ct['payment_days']) ? ($ct['payment_days'] . ' Days') : '30 Days';
                }

                // Bench candidate request status & relation resolution
                $candidateId = (int) ($candidate->id ?? 0);
                $benchRequestCheck = $benchReqMap[$jobId] ?? null;
                $relationget = $relationMap[$jobId] ?? (object) [];
                $recommendation_relation_id = $relationget->id ?? '';

                $candidateStatus = $candidate->status ?? $candidate->statusCheck ?? null;
                $requestsend = ((int) $candidateStatus === 2) ? 'request-send' : 'checkrequestsend';

                $parentCompName = !empty($job['company_name']) ? $job['company_name'] : (!empty($job['client_name']) ? $job['client_name'] : '');

                // Dates (Rule 24: formatted in tenant timezone)
                $postedDateRaw = $job['created_at'] ?? $job['auto_date_job'] ?? null;
                if ($postedDateRaw) {
                    try {
                        $dt = Carbon::parse($postedDateRaw)->setTimezone($tenantTimezone);
                        $postedDateStr = $dt->format('d M, Y');
                        $postedRelative = $dt->diffForHumans();
                    } catch (\Throwable $e) {
                        $postedDateStr = '06 Nov, 2026';
                        $postedRelative = 'Recently';
                    }
                } else {
                    $postedDateStr = '06 Nov, 2026';
                    $postedRelative = 'Recently';
                }

                $updatedRaw = $job['updated_at'] ?? $postedDateRaw;
                $updatedRelative = $updatedRaw ? Carbon::parse($updatedRaw)->diffForHumans() : 'Recently';

                $deadlineDateRaw = $job['end_date'] ?? null;
                if ($deadlineDateRaw) {
                    try {
                        $deadlineStr = Carbon::parse($deadlineDateRaw)->setTimezone($tenantTimezone)->format('d M Y');
                    } catch (\Throwable $e) {
                        $deadlineStr = date('d M Y', strtotime('+30 days', strtotime($postedDateRaw ?? 'now')));
                    }
                } else {
                    $deadlineStr = date('d M Y', strtotime('+30 days', strtotime($postedDateRaw ?? 'now')));
                }

                // Left accent border highlight (Green / Yellow / Orange)
                $cardAccentClass = 'jc-accent-green';
                if ($rawStatus === 'hold' || $employment_type === 'Contract') {
                    $cardAccentClass = 'jc-accent-yellow';
                } elseif ($matchPercentage === 0) {
                    $cardAccentClass = 'jc-accent-orange';
                }

                // Job Skills List
                $jobSkills = [];
                if (!empty($job['job_metronics_skills']) && is_array($job['job_metronics_skills'])) {
                    foreach ($job['job_metronics_skills'] as $js) {
                        if (!empty($js['skill_name'])) {
                            $cleanSkill = trim($js['skill_name']);
                            if ($cleanSkill !== '') {
                                $jobSkills[] = $cleanSkill;
                            }
                        }
                    }
                }
                $jobSkills = array_unique($jobSkills);
            @endphp

            <!-- Modern Card + Sub-Card Enterprise Layout (Rule 20 / market-jobs design) -->
            <div class="job-card cd-market-card {{ $cardAccentClass }}" data-job-id="{{ $jobId }}">
                <!-- UPPER DISCRETE SUB-CARDS GRID -->
                <div class="cd-market-card-grid">

                    <!-- SUB-CARD 1: Role Profile -->
                    <div class="cd-market-subcard cd-subcard-role">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fas fa-briefcase text-primary me-1"></i>ROLE PROFILE</span>
                            <div class="cd-badges-group">
                                <span class="jc-badge {{ $statusBadgeCls }}">
                                    <span class="jc-dot-mini"></span>{{ $statusLabel }}
                                </span>
                                <span class="jc-badge {{ $typeBadgeCls }}">{{ $employment_type }}</span>
                                <span class="jc-req-id">{{ $reqId }}</span>
                            </div>
                        </div>
                        <div class="cd-subcard-body">
                            <h2 class="cd-market-job-title btn-view-job" data-job-id="{{ $jobId }}" title="{{ $jobTitle }}">{{ $jobTitle }}</h2>
                            <div class="cd-market-job-dept" title="{{ $deptName }}"><i class="far fa-folder-open text-primary me-1"></i>{{ $deptName }}</div>
                            <div class="cd-role-mandate-row">
                                <span class="cd-mandate-tag" style="{{ $mandateStyle }}">
                                    <i class="fas {{ $mandateIcon }} me-1" style="{{ $mandateIconStyle }}"></i>{{ $mandateLabel }}
                                </span>
                                <span class="cd-openings-count"><i class="fas fa-users text-muted me-1"></i>{{ $totalPositions }} Openings</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 2: Compensation & Work Mode -->
                    <div class="cd-market-subcard cd-subcard-comp">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fas fa-coins text-warning me-1"></i>COMPENSATION &amp; WORK</span>
                        </div>
                        <div class="cd-subcard-body">
                            <div class="cd-comp-highlight">
                                <strong>{{ $salaryDisplay }}</strong>
                            </div>
                            <div class="cd-comp-meta-row">
                                <i class="far fa-user text-muted me-1"></i>
                                <span>{{ $expFormatted }}</span>
                            </div>
                            <div class="cd-comp-meta-row" title="{{ $locationDisplay }}">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                <span class="cd-location-name">{{ $locationDisplay }}</span> • <span class="badge badge-light-primary px-1.5 py-0 fs-8">{{ $workModeLabel }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 3: Your Bench Match -->
                    <div class="cd-market-subcard cd-subcard-bench btn-view-job"
                         data-job-id="{{ $jobId }}"
                         role="button" tabindex="0" title="View matching bench talents">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fas fa-user-check text-primary me-1"></i>BENCH MATCH</span>
                        </div>
                        <div class="cd-subcard-body text-center">
                            <div class="cd-bench-kpi-num {{ $matchPercentage > 0 ? 'text-primary' : 'text-gray-700' }}">{{ $benchMatchNumber }}</div>
                            <div class="cd-bench-kpi-sub">{{ $benchMatchSub }}</div>
                            <div class="cd-subcard-link justify-content-center">View Matches &rarr;</div>
                        </div>
                    </div>

                    <!-- SUB-CARD 4: Commercial Terms -->
                    <div class="cd-market-subcard cd-subcard-terms">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fas fa-file-invoice-dollar text-success me-1"></i>COMMERCIAL TERMS</span>
                        </div>
                        <div class="cd-subcard-body">
                            <div class="cd-terms-rows">
                                <div class="jc-ct-row"><span class="jc-ct-label">{{ $term1Label }}</span><span class="jc-ct-val">{{ $term1Val }}</span></div>
                                <div class="jc-ct-row"><span class="jc-ct-label">{{ $term2Label }}</span><span class="jc-ct-val">{{ $term2Val }}</span></div>
                                <div class="jc-ct-row"><span class="jc-ct-label">{{ $term3Label }}</span><span class="jc-ct-val">{{ $term3Val }}</span></div>
                            </div>
                            <a href="javascript:void(0);" class="cd-subcard-link client-terms-listing-find-job" data-row-id="{{ $jobId }}" data-row-recommendation_relation_id="{{ $recommendation_relation_id }}">
                                View Full Terms &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- SUB-CARD 5: Submission & Actions -->
                    <div class="cd-market-subcard cd-subcard-actions">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fas fa-paper-plane text-info me-1"></i>SUBMISSION</span>
                        </div>
                        <div class="cd-subcard-body">
                            @if(!empty($category) && $category === 'direct' && !empty($parentCompName) && !in_array($parentCompName, ['Internal Client', 'Direct Client']))
                                <div class="fw-bold text-dark text-truncate text-center" style="font-size:11px; color:#0F172A;" title="{{ $parentCompName }}">
                                    {{ ucwords(mb_substr($parentCompName, 0, 16)) }}
                                </div>
                            @else
                                <div class="text-muted text-center" style="font-size:12px; line-height: 1.2;">—</div>
                            @endif

                            <div class="w-100 my-1">
                                @if ($benchRequestCheck == null)
                                    <button type="button" data-jobId="{{ $jobId }}" data-conId="{{ $candidateId }}" class="btn btn-sm btn-primary py-1.5 px-2 w-100 {{ $requestsend }}" style="font-size:11px; font-weight:700; border-radius:6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s ease;">
                                        <i class="fa fa-paper-plane me-1"></i> Submit Candidate
                                    </button>
                                @else
                                    @if ($benchRequestCheck->ac_status == 1)
                                        <span class="badge w-100 text-center py-1.5 px-2" style="font-size:11px; font-weight:600; background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; border-radius:6px; display:block;">
                                            <i class="fa-solid fa-check me-1"></i> Added
                                        </span>
                                    @else
                                        <span class="badge w-100 text-center py-1.5 px-2" style="font-size:11px; font-weight:600; background:#fef3c7; color:#b45309; border:1px solid #fde68a; border-radius:6px; display:block;">
                                            <i class="fa-regular fa-clock me-1"></i> Request Sent
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between w-100 px-1 mt-auto pt-1" style="border-top: 1px dashed #e2e8f0;">
                                <a href="javascript:void(0);" class="btn-view-job text-muted" data-job-id="{{ $jobId }}" style="font-size:10.5px; font-weight:500; text-decoration:none;" title="View Requirements">
                                    <i class="far fa-eye text-primary me-1"></i>Details
                                </a>
                                <a href="javascript:void(0);" class="client-terms-listing-find-job text-muted" data-row-id="{{ $jobId }}" data-row-recommendation_relation_id="{{ $recommendation_relation_id }}" style="font-size:10.5px; font-weight:500; text-decoration:none;" title="View Terms">
                                    <i class="far fa-file-alt text-success me-1"></i>Terms
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- SUB-CARD 6: BOTTOM ACTIVITY STRIP -->
                <div class="cd-market-card-bottom">
                    <div class="cd-bottom-telemetry">
                        <span><i class="far fa-calendar-alt text-muted me-1"></i>Posted: <strong>{{ $postedDateStr }} ({{ $postedRelative }})</strong></span>
                        <span><i class="fas fa-history text-success me-1"></i>Updated: <strong class="text-success">{{ $updatedRelative }}</strong></span>
                        <span><i class="far fa-clock text-warning me-1"></i>Deadline: <strong class="text-danger">{{ $deadlineStr }}</strong></span>
                    </div>

                    @if (!empty($jobSkills))
                        <div class="cd-bottom-skills">
                            <div class="skills-row">
                                @foreach ($jobSkills as $sIndex => $skName)
                                    @php
                                        $isMatched = in_array(strtolower($skName), $candidateSkills, true);
                                    @endphp
                                    @if ($sIndex < 4 || $isMatched)
                                        <span class="skill-tag {{ $isMatched ? 'skill-matched' : '' }}">
                                            @if ($isMatched)
                                                <i class="fas fa-check text-success me-1" style="font-size: 8px;"></i>
                                            @endif
                                            {{ ucfirst($skName) }}
                                        </span>
                                    @endif
                                @endforeach
                                @if (count($jobSkills) > 4)
                                    <span class="skill-tag skill-more btn-view-job" data-job-id="{{ $jobId }}">+{{ count($jobSkills) - 4 }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="cd-bottom-actions">
                        <button type="button" class="btn btn-sm btn-primary py-1 px-3 fs-8 fw-bold btn-view-job" data-job-id="{{ $jobId }}">
                            <i class="fas fa-eye me-1"></i> View Job
                        </button>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
@else
    <!-- Clean Modern Empty State -->
    <div class="mjh-empty-state text-center py-12 px-4">
        <div class="mjh-empty-icon-wrap mx-auto">
            <i class="fa fa-briefcase"></i>
        </div>
        <h5 class="mjh-empty-title">No Matching Opportunities Found</h5>
        @if (!empty($search))
            <p class="mjh-empty-desc">
                No opportunities match your search <strong>"{{ e($search) }}"</strong>. Try searching with different keywords.
            </p>
            <div class="mjh-empty-actions">
                <button type="button" class="mjh-empty-btn-primary" id="sc_btn_clear_search_empty">
                    <i class="fa fa-times me-1"></i> Clear Search Filter
                </button>
            </div>
        @else
            <p class="mjh-empty-desc">
                There are currently no active job requisitions matching this candidate's core skills in this category.
            </p>
        @endif
    </div>
@endif
