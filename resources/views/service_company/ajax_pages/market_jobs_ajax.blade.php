@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode      = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency        = GeneralMethod::currency();
    $job_status_arr  = GeneralMethod::job_status();
    $job_status      = GeneralMethod::job_status();
@endphp

{{--
    market_jobs_ajax.blade.php — Fallback Blade template for Market Jobs.
    Structure: 6-column Grid with Sub-cards and Icon-only Meta (NO headings).
--}}

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $targetJobId = $row->id;
            $company_id  = auth()->user()->company_id;

            // ── Recommendation relation ──────────────────────────────────────────
            if (isset($row->v2_relation)) {
                $relationget = $row->v2_relation;
            } else {
                $relationget = GeneralMethod::getrRcommendationRelationData($company_id, $targetJobId);
            }
            $from_company_id           = $relationget->from_company_id ?? null;
            $to_company_id             = $relationget->to_company_id ?? null;
            $recommendation_id         = $relationget->id ?? null;
            $final_status              = $relationget->final_status ?? null;
            $row->final_status                = $final_status;
            $row->recommendation_id           = $recommendation_id;
            $row->recommendation_relation_id  = $recommendation_id;
            $row->from_company_id             = $from_company_id;
            $row->to_company_id               = $to_company_id;
            $row->companyidcount              = $row->comId;

            // ── Status & type badges ──────────────────────────────────────────────
            $rawStatus       = strtolower($row->status ?? 'active');
            $statusLabel     = match ($rawStatus) { 'active' => 'OPEN', 'hold' => 'HOLD', default => strtoupper($rawStatus) };
            $statusBadgeCls  = $rawStatus === 'hold' ? 'jc-badge-hold' : 'jc-badge-open';
            $statusDotCls    = $rawStatus === 'hold' ? 'jc-dot-hold' : 'jc-dot-open';
            $statusText      = $rawStatus === 'hold' ? 'Hold' : 'Active · Open';

            $employment_type = $row->employment_type == 1 ? 'Contract' : 'Full-Time';
            $typeBadgeCls    = $row->employment_type == 1 ? 'jc-badge-ct' : 'jc-badge-ft';
            if ($row->public_market_status == 2) {
                $typeBadgeCls = 'jc-badge-bench';
                $employment_type = 'Bench Talent';
            }

            // ── REQ-ID ────────────────────────────────────────────────────────────
            $req_id = 'REQ-JERO-' . $row->id;

            // ── Location ──────────────────────────────────────────────────────────
            $client_city_jobs  = $row->client_city_jobs;
            $jobCitySelected   = is_array($client_city_jobs) ? count($client_city_jobs) : 0;
            $work_type         = match ($row->work_type ?? 'no') {
                'yes'   => 'Remote',
                'other' => 'Hybrid',
                default => 'Work From Office',
            };
            $locationDisplay = $work_type === 'Remote' ? 'Remote' : 'Noida, India';
            if ($row->work_type !== 'yes' && $jobCitySelected >= 1) {
                $firstCity      = collect($client_city_jobs)->first();
                $cityName       = $firstCity['city_name'] ?? null;
                $countryName    = $firstCity['country_name'] ?? null;
                $locationDisplay = trim(ucwords(mb_substr($cityName ?? '', 0, 12)) . ', ' . mb_substr($countryName ?? '', 0, 8), ', ');
            }

            // ── Salary ────────────────────────────────────────────────────────────
            $mod             = $sal_mode_set ?? $row->max_sal_mode;
            $cur             = $salary_currency_set ?? $row->max_salary_currency;
            $salModeLabel    = $salaryMode[$mod] ?? 'Annual';
            $salModeShort    = mb_substr($salModeLabel, 0, 1);
            $salary_currency = $currencySymbole[@$cur] ?? '₹';
            $currencymain    = GeneralMethod::currency();
            $currencyCode    = $currencymain[$cur] ?? 'INR';
            $convertedEctc   = GeneralMethod::conversionRates($currencyCode, $row->max_yearly ?? 0, $mod);
            $convertedCtc    = GeneralMethod::conversionRates($currencyCode, $row->min_yearly ?? 0, $mod);
            $max_salary      = GeneralMethod::formatK($convertedEctc[$mod] ?? 0);
            $min_salary      = GeneralMethod::formatK($convertedCtc[$mod] ?? 0);

            // ── Industry / company size ───────────────────────────────────────────
            $ind_name = $row->industry_name ?? '';
            if (empty($ind_name)) {
                $companyDetailsGet = GeneralMethod::companyDetailsGet($row->comId);
                if (is_iterable($companyDetailsGet->industry) && count($companyDetailsGet->industry)) {
                    $ind_name = $companyDetailsGet->industry['industry_name'];
                }
            }

            // ── Department / category ─────────────────────────────────────────────
            $deptName = $row->category_name ?? '';
            if (empty($deptName) && !empty($row->category_id)) {
                $cat = DB::table('job_categories')->select('name')->where('id', '=', $row->category_id)->first();
                $deptName = $cat->name ?? '';
            }

            // ── Skills ────────────────────────────────────────────────────────────
            $skilltag   = '';
            $totalSkill = 0;
            if (isset($row->job_metronics_skills) && is_array($row->job_metronics_skills)) {
                $skillArr = array_map(fn($s) => (object)$s, $row->job_metronics_skills);
            } else {
                $skillArr = DB::table('job_metronics_skills')->where('company_id', '=', $row->comId)->where('job_id', $row->id)->get();
            }
            if (!empty($skillArr)) {
                foreach ($skillArr as $key => $sval) {
                    if ($key < 5) {
                        $skilltag .= '<span class="skill-tag">' . ucfirst($sval->skill_name) . '</span>';
                    } else {
                        $totalSkill++;
                    }
                }
            }

            // ── Bench match count ─────────────────────────────────────────────────
            $candidateCount = $row->v2_bench_match_count ?? GeneralMethod::benchCandidateCountSkillBase($row->id);

            // ── Dates ─────────────────────────────────────────────────────────────
            $created_at  = $row->created_at ?? date('Y-m-d H:i:s');
            $newDate     = date('d M Y', strtotime($created_at));
            $updatedAt   = $row->updated_at ?? $created_at;
            $updatedAgo  = GeneralMethod::timeAgo($updatedAt);
            $endDate     = $row->end_date ?? null;
            $deadlineStr = $endDate ? date('d M Y', strtotime($endDate)) : null;

            // ── Commercial Terms ──────────────────────────────────────────────────
            $ct = $row->v2_commercial_terms ?? null;

            // ── Vendor count / action button ──────────────────────────────────────
            $resultCount  = (int) ($row->vendor_added_count ?? 0);
            $publicMarket = (int) ($row->public_market_status ?? 0);

            // ── Card left border highlight accent (Green / Yellow) ──
            $cardAccentCls = 'jc-accent-green';
            if ($rawStatus === 'hold' || $employment_type === 'Contract') {
                $cardAccentCls = 'jc-accent-yellow';
            } elseif ($publicMarket === 2 || $employment_type === 'Bench Talent') {
                $cardAccentCls = 'jc-accent-green';
            }
        @endphp

        {{-- ── JOB CARD ──────────────────────────────────────────────────────── --}}
        <div class="job-card {{ $cardAccentCls }}">

            {{-- COL 1: Title, Badges, Subtitle, Skills (NO company name) --}}
            <div class="jc-col-main">
                <div class="jc-badges-row">
                    <span class="jc-badge {{ $statusBadgeCls }}">
                        <span class="jc-dot-mini"></span>{{ $statusLabel }}
                    </span>
                    <span class="jc-badge {{ $typeBadgeCls }}">{{ $employment_type }}</span>
                    <span class="jc-req-id">{{ $req_id }}</span>
                </div>

                <h2 class="jc-job-title" title="{{ $row->title }}">{{ ucwords(mb_substr(@$row->title, 0, 28)) }}</h2>
                <div class="jc-job-dept" title="{{ $deptName }}">{{ $deptName ?: '—' }}</div>

                <div class="skills-row">
                    {!! $skilltag !!}
                    @if($totalSkill)
                        <span class="skill-tag skill-more client-show-detail" data-row-id="{{ $row->id }}">+{{ $totalSkill }}</span>
                    @endif
                </div>
            </div>

            {{-- COL 2: Meta Info (Icons + Data ONLY, NO Headings) --}}
            <div class="jc-col-meta">
                <div class="jc-meta-item" title="Location">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ $locationDisplay }}</span>
                </div>
                <div class="jc-meta-item" title="Work Mode">
                    <i class="fa-solid fa-laptop"></i>
                    <span>{{ $work_type }}</span>
                </div>
                <div class="jc-meta-item" title="Experience">
                    <i class="fa-regular fa-user"></i>
                    <span>{{ $row->min_exp ? $row->min_exp . ' – ' : '' }}{{ $row->max_exp ? $row->max_exp . ' Years' : '—' }}</span>
                </div>
                <div class="jc-meta-item" title="Openings">
                    <i class="fa-solid fa-users"></i>
                    <span>{{ @$row->total_positions ?: 1 }} Openings</span>
                </div>
                <div class="jc-meta-item" title="Salary">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                    <span>{!! $salary_currency !!}{{ $min_salary }} – {{ $max_salary }} / {{ $salModeShort }}</span>
                </div>
                <div class="jc-meta-item" title="Industry">
                    <i class="fa-regular fa-building"></i>
                    <span>{{ $ind_name ?: ($deptName ?: 'IT Services') }}</span>
                </div>
            </div>

            {{-- COL 3: Dates & Status --}}
            <div class="jc-col-dates">
                <div class="jc-date-item">
                    <i class="fa-regular fa-calendar"></i>
                    <div class="jc-date-content">
                        <span class="jc-date-label">Posted</span>
                        <span class="jc-date-val">{{ $newDate }}</span>
                    </div>
                </div>
                <div class="jc-date-item">
                    <i class="fa-solid fa-rotate"></i>
                    <div class="jc-date-content">
                        <span class="jc-date-label">Last Updated</span>
                        <span class="jc-date-val jc-text-green">{{ $updatedAgo }}</span>
                    </div>
                </div>
                <div class="jc-date-item">
                    <i class="fa-regular fa-clock"></i>
                    <div class="jc-date-content">
                        <span class="jc-date-label">Submission Deadline</span>
                        <span class="jc-date-val {{ $deadlineStr ? 'jc-text-red' : 'jc-text-green' }}">
                            {{ $deadlineStr ?: 'Open till filled' }}
                        </span>
                    </div>
                </div>
                <div class="jc-status-row">
                    <span class="jc-date-label">Status</span>
                    <span class="jc-status-indicator">
                        <span class="{{ $statusDotCls }}"></span>
                        {{ $statusText }}
                    </span>
                </div>
            </div>

            {{-- COL 4: Sub-card 1 (Your Bench Match) --}}
            <div class="jc-subcard jc-bench-subcard open-bench-talents"
                 data-row-id="{{ $row->id }}" data-mod="{{ $mod }}" data-cur="{{ $cur }}"
                 role="button" tabindex="0" title="View matching bench talents">
                <div class="jc-bench-header">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Your Bench Match</span>
                </div>
                <div class="jc-bench-num">{{ $candidateCount }}</div>
                <div class="jc-bench-desc">Matching candidates</div>
                <div class="jc-subcard-link">View Matches &rarr;</div>
            </div>

            {{-- COL 5: Sub-card 2 (Commercial Terms / Details) --}}
            <div class="jc-subcard jc-terms-subcard">
                <div class="jc-subcard-title">{{ $publicMarket === 2 ? 'Requirement Details' : 'Commercial Terms' }}</div>
                <div class="jc-terms-rows">
                    @if($employment_type === 'Contract')
                        <div class="jc-ct-row">
                            <span class="jc-ct-label">Billing Model</span>
                            <span class="jc-ct-val">{{ ucfirst($ct['billing_model'] ?? 'Monthly') }}</span>
                        </div>
                        <div class="jc-ct-row">
                            <span class="jc-ct-label">Billing Rate</span>
                            <span class="jc-ct-val">{{ !empty($ct['billing_rate']) ? '₹' . number_format($ct['billing_rate']/1000,0) . 'K/Mo' : '₹80K – ₹1.2L / Month' }}</span>
                        </div>
                        <div class="jc-ct-row">
                            <span class="jc-ct-label">Payment Terms</span>
                            <span class="jc-ct-val">{{ $ct['payment_days'] ?? 30 }} Days</span>
                        </div>
                    @elseif($publicMarket === 2)
                        <div class="jc-ct-row">
                            <span class="jc-ct-label">Deployment Type</span>
                            <span class="jc-ct-val">Bench</span>
                        </div>
                        <div class="jc-ct-row">
                            <span class="jc-ct-label">Work Mode</span>
                            <span class="jc-ct-val">{{ $work_type }}</span>
                        </div>
                        <div class="jc-ct-row">
                            <span class="jc-ct-label">Availability</span>
                            <span class="jc-ct-val">Immediate</span>
                        </div>
                    @else
                        <div class="jc-ct-row">
                            <span class="jc-ct-label">Hiring Fee</span>
                            <span class="jc-ct-val">{{ $ct['hiring_fee_pct'] ?? 8.33 }}% of CTC</span>
                        </div>
                        <div class="jc-ct-row">
                            <span class="jc-ct-label">Replacement</span>
                            <span class="jc-ct-val">{{ $ct['replacement_days'] ?? 90 }} Days</span>
                        </div>
                        <div class="jc-ct-row">
                            <span class="jc-ct-label">Payment Terms</span>
                            <span class="jc-ct-val">{{ $ct['payment_days'] ?? 30 }} Days</span>
                        </div>
                    @endif
                </div>
                <a class="jc-subcard-link client-terms-listing-find-job"
                   data-row-id="{{ $row->id }}"
                   data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">View Full Terms &rarr;</a>
            </div>

            {{-- COL 6: Action Buttons Column --}}
            <div class="jc-col-actions">
                @php
                    $exceeded = 20;
                @endphp
                @if($publicMarket === 2)
                    @if(($resultCount >= $exceeded || $publicMarket === 6) && ($row->to_company_id != $company_id))
                        <button class="btn-cta-primary" style="background:#EF4444;">Vendor Fulfilled</button>
                    @elseif($row->recommendation_relation_id == '' || $final_status == 2 || $final_status == 0)
                        <button class="btn-cta-primary request_approval"
                                data-row-status="accept" data-row-id="{{ $row->id }}"
                                data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">Submit Candidates</button>
                    @elseif($publicMarket === 3)
                        <button class="btn-cta-primary" style="background:#EF4444;">Reject</button>
                    @elseif($publicMarket === 1)
                        <button class="btn-cta-primary" style="background:#F59E0B;">Under Review</button>
                    @elseif($publicMarket === 4)
                        <button class="btn-cta-primary" style="background:#EF4444;">Vendor Limit</button>
                    @elseif($row->to_company_id == $company_id)
                        @php
                            $url_path = url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($row->id) . '/all');
                        @endphp
                        <a href="{{ $url_path }}" class="btn-cta-primary text-center">View Job</a>
                    @endif
                @else
                    @if($employment_type === 'Contract')
                        <button data-row-id="{{ $row->id }}" data-mod="{{ $mod }}" data-cur="{{ $cur }}"
                                class="btn-cta-primary btn-cta-purple open-bench-talents">Submit Contract Candidates</button>
                    @else
                        <button data-row-id="{{ $row->id }}" data-mod="{{ $mod }}" data-cur="{{ $cur }}"
                                class="btn-cta-primary open-bench-talents">Submit Candidates</button>
                    @endif
                @endif

                <button class="btn-cta-secondary market-show-detail" data-row-id="{{ $row->id }}">View Requirement</button>
                <button class="btn-cta-secondary client-terms-listing-find-job"
                        data-row-id="{{ $row->id }}"
                        data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">View Terms</button>

                <div class="jc-actions-footer">
                    <i class="fa-regular fa-bookmark jc-icon-btn" title="Save Job"></i>
                    <i class="fa-solid fa-ellipsis-vertical jc-icon-btn" title="More Options"></i>
                </div>
            </div>

        </div>{{-- /job-card --}}

    @endforeach
@else
    <div class="text-center py-5 text-muted" style="font-size:13px;">No jobs available matching your criteria.</div>
@endif
