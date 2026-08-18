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
    market_jobs_ajax.blade.php — Legacy Blade fallback for the V1 HTML path.

    Intent: Server-rendered HTML cards used when the V2 JSON path is unavailable.
    Updated (2026-08-18): Restructured to 5-column card layout matching the new reference
    design. Company name completely removed from all output (Q4 decision). Inline
    Commercial Terms block added using v2_commercial_terms pre-fetched data.
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

            // ── Avatar colour from first char of title ───────────────────────────
            $bgTextDecore = $strVal = $leftbar = '';
            $str      = trim(@$row->title);
            $str      = $strVal = strtolower($str[0]);
            $strCode  = ord($str);
            if ($strCode >= 97 && $strCode <= 101) {
                $bgTextDecore = 'fs-3 bg-light-danger text-danger';
            } elseif ($strCode >= 102 && $strCode <= 106) {
                $bgTextDecore = 'fs-3 bg-light-warning text-warning';
            } elseif ($strCode >= 107 && $strCode <= 111) {
                $bgTextDecore = 'fs-3 bg-light-primary text-primary';
            } elseif ($strCode >= 112 && $strCode <= 116) {
                $bgTextDecore = 'fs-3 bg-light-success text-success';
            } elseif ($strCode >= 117 && $strCode <= 121) {
                $bgTextDecore = 'fs-3 bg-dark text-secondary';
            } else {
                $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
            }

            // ── Status & type badges (OPEN / HOLD only — FILLING FAST not required) ─
            $rawStatus       = strtolower($row->status ?? 'active');
            $statusLabel     = match ($rawStatus) { 'active' => 'OPEN', 'hold' => 'HOLD', default => strtoupper($rawStatus) };
            $statusBadgeCls  = $rawStatus === 'hold' ? 'jc-badge-hold' : 'jc-badge-open';
            $statusPillCls   = $rawStatus === 'hold' ? 'jc-status-hold' : 'jc-status-open';
            $statusPillText  = $rawStatus === 'hold' ? 'Hold' : 'Active – Open';

            $employment_type  = $row->employment_type == 1 ? 'Contract' : 'Full-Time';
            $typeBadgeCls     = $row->employment_type == 1 ? 'jc-badge-ct' : 'jc-badge-ft';

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
            $locationDisplay = $work_type;
            if ($row->work_type !== 'yes' && $jobCitySelected >= 1) {
                $firstCity      = collect($client_city_jobs)->first();
                $cityName       = $firstCity['city_name'] ?? null;
                $countryName    = $firstCity['country_name'] ?? null;
                $locationDisplay = trim(ucwords(mb_substr($cityName ?? '', 0, 12)) . ', ' . mb_substr($countryName ?? '', 0, 8), ', ');
            }

            // ── Salary ────────────────────────────────────────────────────────────
            $mod             = $sal_mode_set ?? $row->max_sal_mode;
            $cur             = $salary_currency_set ?? $row->max_salary_currency;
            $salModeLabel    = $salaryMode[$mod];
            $salModeShort    = mb_substr($salModeLabel, 0, 1);
            $salary_currency = $currencySymbole[@$cur];
            $currencymain    = GeneralMethod::currency();
            $currencyCode    = $currencymain[$cur];
            $convertedEctc   = GeneralMethod::conversionRates($currencyCode, $row->max_yearly, $mod);
            $convertedCtc    = GeneralMethod::conversionRates($currencyCode, $row->min_yearly, $mod);
            $max_salary      = GeneralMethod::formatK($convertedEctc[$mod]);
            $min_salary      = GeneralMethod::formatK($convertedCtc[$mod]);

            // ── Industry / company size (from pre-fetched ES fields) ──────────────
            $ind_name = '';
            if (isset($row->company_size) || isset($row->industry_name)) {
                $companyDetailsGet = (object)[
                    'industry'          => ['industry_name' => $row->industry_name ?? ''],
                    'company_size_from' => $row->company_size_from ?? 0,
                    'company_size'      => $row->company_size ?? 0,
                ];
            } else {
                $companyDetailsGet = GeneralMethod::companyDetailsGet($row->comId);
            }
            if (is_iterable($companyDetailsGet->industry) && count($companyDetailsGet->industry)) {
                $ind_name = $companyDetailsGet->industry['industry_name'];
            }

            // ── Department / category ─────────────────────────────────────────────
            if (isset($row->category_name) && $row->category_name !== '') {
                $job_categories = (object)['name' => $row->category_name];
            } else {
                $job_categories = DB::table('job_categories')
                    ->select('job_categories.name')
                    ->where('job_categories.id', '=', $row->category_id)
                    ->first();
            }

            // ── Skills ────────────────────────────────────────────────────────────
            $skilltag   = '';
            $totalSkill = 0;
            if (isset($row->job_metronics_skills) && is_array($row->job_metronics_skills)) {
                $skillArr = array_map(fn($s) => (object)$s, $row->job_metronics_skills);
            } else {
                $skillArr = DB::table('job_metronics_skills')
                    ->select('*')
                    ->where('company_id', '=', $row->comId)
                    ->where('job_id', $row->id)
                    ->get();
            }
            if (!empty($skillArr)) {
                foreach ($skillArr as $key => $sval) {
                    if ($key <= 5) {
                        $skilltag .= '<span class="skill-tag">' . ucfirst($sval->skill_name) . '</span>';
                    } else {
                        $totalSkill++;
                    }
                }
            }

            // ── Bench match count ─────────────────────────────────────────────────
            $candidateCount = $row->v2_bench_match_count ?? GeneralMethod::benchCandidateCountSkillBase($row->id);

            // ── Dates ─────────────────────────────────────────────────────────────
            $created_at  = $row->created_at;
            $newDate     = date('d M, Y', strtotime($created_at));
            $daysAgo     = GeneralMethod::timeAgo($created_at);
            $updatedAt   = $row->updated_at ?? $created_at;
            $updatedAgo  = GeneralMethod::timeAgo($updatedAt);
            $endDate     = $row->end_date ?? null;
            $deadlineStr = $endDate ? date('d M Y', strtotime($endDate)) : null;

            // ── Commercial Terms (pre-fetched, zero extra queries) ────────────────
            $ct = $row->v2_commercial_terms ?? null;

            // ── Vendor count / action button ──────────────────────────────────────
            $resultCount  = (int) ($row->vendor_added_count ?? DB::table('market_job_added_count')->where('job_id', $row->id)->where('status', 0)->count());
            $publicMarket = (int) $row->public_market_status;
        @endphp

        {{-- ── JOB CARD ──────────────────────────────────────────────────────── --}}
        <div class="job-card">

            {{-- COL 1: Title + Badges + Skills (NO company name) --}}
            <div class="jc-col1">
                <div class="jc-badges-row">
                    <span class="jc-badge {{ $statusBadgeCls }}">
                        <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                        {{ $statusLabel }}
                    </span>
                    <span class="jc-badge {{ $typeBadgeCls }}">{{ $employment_type }}</span>
                    <span class="jc-req-id">{{ $req_id }}</span>
                </div>

                <div class="job-header-row">
                    <div class="job-icon {{ $bgTextDecore }}">{{ ucwords($strVal) }}</div>
                    <div class="job-details">
                        <h2>{{ ucwords(mb_substr(@$row->title, 0, 25)) }}</h2>
                        {{-- Department only — company name intentionally hidden --}}
                        <div class="job-dept">{{ ucwords(mb_substr(@$job_categories->name, 0, 35)) }}</div>
                    </div>
                </div>

                <div class="skills-row">
                    {!! $skilltag !!}
                    @if($totalSkill)
                        <span class="skill-tag skill-more client-show-detail" data-row-id="{{ $row->id }}">+{{ $totalSkill }}</span>
                    @endif
                </div>
            </div>

            {{-- COL 2: Location & Meta --}}
            <div class="jc-col2">
                <div class="meta-row">
                    <i class="fa-regular fa-location-dot"></i>
                    <div class="meta-content"><p>Location</p><span>{{ $locationDisplay }}</span></div>
                </div>
                <div class="meta-row">
                    <i class="fa-solid fa-laptop-house"></i>
                    <div class="meta-content"><p>Work Mode</p><span>{{ $work_type }}</span></div>
                </div>
                <div class="meta-row">
                    <i class="fa-regular fa-clock"></i>
                    <div class="meta-content"><p>Experience</p>
                        <span>{{ $row->min_exp ? $row->min_exp . ' – ' : '' }}{{ $row->max_exp ? $row->max_exp . ' Years' : '—' }}</span>
                    </div>
                </div>
                <div class="meta-row">
                    <i class="fa-regular fa-circle-user"></i>
                    <div class="meta-content"><p>Openings</p><span>{{ @$row->total_positions }}</span></div>
                </div>
                <div class="meta-row">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                    <div class="meta-content"><p>Salary</p>
                        <span>{!! $salary_currency !!} {{ $min_salary }} – {{ $max_salary }} / {{ $salModeShort }}</span>
                    </div>
                </div>
                <div class="meta-row">
                    <i class="fa-solid fa-industry"></i>
                    <div class="meta-content"><p>Industry</p><span>{{ $ind_name ?: '—' }}</span></div>
                </div>
            </div>

            {{-- COL 3: Dates & Status --}}
            <div class="jc-col3">
                <div class="meta-row">
                    <i class="fa-regular fa-calendar-plus"></i>
                    <div class="meta-content"><p>Posted</p><span>{{ $newDate }}</span></div>
                </div>
                <div class="meta-row">
                    <i class="fa-solid fa-rotate"></i>
                    <div class="meta-content"><p>Last Updated</p><span class="jc-date-warn">{{ $updatedAgo }}</span></div>
                </div>
                <div class="meta-row">
                    <i class="fa-regular fa-calendar-xmark"></i>
                    <div class="meta-content"><p>Submission Deadline</p>
                        @if($deadlineStr)
                            <span class="jc-date-deadline">{{ $deadlineStr }}</span>
                        @else
                            <span>—</span>
                        @endif
                    </div>
                </div>
                <div class="meta-row">
                    <i class="fa-regular fa-circle-dot"></i>
                    <div class="meta-content"><p>Status</p>
                        <span class="jc-status-pill {{ $statusPillCls }}">
                            <span class="jc-status-pill-dot"></span>{{ $statusPillText }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- COL 4: Bench Match --}}
            <div class="jc-col4">
                <div class="jc-bench-box open-bench-talents"
                     data-row-id="{{ $row->id }}" data-mod="{{ $mod }}" data-cur="{{ $cur }}"
                     role="button" tabindex="0" title="View matching bench talents">
                    <div class="jc-bench-header">
                        <i class="fa-solid fa-user-group"></i>
                        <span>Your Bench Match</span>
                    </div>
                    <div class="jc-bench-count">{{ $candidateCount }}</div>
                    <p class="jc-bench-sub">Matching candidates</p>
                    <a class="jc-bench-link">View Matches <i class="fa-solid fa-arrow-right" style="font-size:9px;"></i></a>
                </div>
            </div>

            {{-- COL 5: Commercial Terms + Actions --}}
            <div class="jc-col5">

                {{-- Primary action button --}}
                @php
                    $exceeded = 20;
                @endphp
                @if($publicMarket === 2)
                    @if(($resultCount >= $exceeded || $publicMarket === 6) && ($row->to_company_id != $company_id))
                        <button data-row-status="" data-row-id="{{ $row->id }}"
                                data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}"
                                class="btn-submit btn btn-sm btn-danger w-100">Vendor Fulfilled</button>
                    @elseif($row->recommendation_relation_id == '' || $final_status == 2 || $final_status == 0)
                        <button class="btn-submit btn btn-sm w-100 request_approval"
                                style="background:#7C3AED;"
                                data-row-status="accept" data-row-id="{{ $row->id }}"
                                data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">Submit Candidates</button>
                    @elseif($publicMarket === 3)
                        <button data-row-status="accept" data-row-id="{{ $row->id }}"
                                data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}"
                                class="btn-submit btn btn-sm btn-danger w-100">Reject</button>
                    @elseif($publicMarket === 1)
                        <button data-row-status="accept" data-row-id="{{ $row->id }}"
                                data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}"
                                class="btn-submit btn btn-sm btn-warning w-100">Under Review</button>
                    @elseif($publicMarket === 4)
                        <button data-row-status="accept" data-row-id="{{ $row->id }}"
                                data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}"
                                class="btn-submit btn btn-sm btn-danger w-100">Vendor Limit</button>
                    @elseif($row->to_company_id == $company_id)
                        @php
                            $url_path = url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($row->id) . '/all');
                        @endphp
                        <a href="{{ $url_path }}" class="btn-submit btn btn-sm btn-info w-100 text-center">View Job</a>
                    @endif
                @else
                    @if($employment_type === 'Contract')
                        <button data-row-id="{{ $row->id }}" data-mod="{{ $mod }}" data-cur="{{ $cur }}"
                                class="btn-submit btn btn-sm w-100 open-bench-talents" style="background:#7C3AED;">Submit Contract Candidates</button>
                    @else
                        <button data-row-id="{{ $row->id }}" data-mod="{{ $mod }}" data-cur="{{ $cur }}"
                                class="btn-submit btn btn-sm btn-primary w-100 open-bench-talents">Match from Your Bench</button>
                    @endif
                @endif

                <button class="btn-view market-show-detail" data-row-id="{{ $row->id }}">View Requirement</button>
                <button class="btn-view client-terms-listing-find-job"
                        data-row-id="{{ $row->id }}"
                        data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">View Terms</button>

                {{-- Commercial Terms block (inline, from pre-fetched Redis cache) --}}
                <div class="jc-commercial-block">
                    <div class="jc-commercial-title">Commercial Terms</div>
                    @if($ct)
                        @if($employment_type === 'Contract')
                            <div class="jc-ct-row">
                                <span class="jc-ct-label">Billing Model</span>
                                <span class="jc-ct-value">{{ ucfirst($ct['billing_model'] ?? 'Monthly') }}</span>
                            </div>
                            <div class="jc-ct-row">
                                <span class="jc-ct-label">Billing Rate</span>
                                <span class="jc-ct-value">
                                    @if(!empty($ct['billing_rate']))
                                        ₹{{ number_format($ct['billing_rate'] / 1000, 0) }}K/Mo
                                    @else
                                        Per Contract
                                    @endif
                                </span>
                            </div>
                            <div class="jc-ct-row">
                                <span class="jc-ct-label">Payment Terms</span>
                                <span class="jc-ct-value">{{ $ct['payment_days'] ?? 30 }} Days</span>
                            </div>
                        @else
                            <div class="jc-ct-row">
                                <span class="jc-ct-label">Hiring Fee</span>
                                <span class="jc-ct-value">{{ $ct['hiring_fee_pct'] ?? 8.33 }}% of CTC</span>
                            </div>
                            <div class="jc-ct-row">
                                <span class="jc-ct-label">Replacement</span>
                                <span class="jc-ct-value">{{ $ct['replacement_days'] ?? 90 }} Days</span>
                            </div>
                            <div class="jc-ct-row">
                                <span class="jc-ct-label">Payment Terms</span>
                                <span class="jc-ct-value">{{ $ct['payment_days'] ?? 30 }} Days</span>
                            </div>
                        @endif
                    @endif
                    <a class="jc-view-terms-link client-terms-listing-find-job"
                       data-row-id="{{ $row->id }}"
                       data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">View Full Terms →</a>
                </div>

                <div class="jc-card-footer">
                    <i class="fa-regular fa-bookmark" title="Save job"></i>
                    <i class="fa-solid fa-ellipsis" title="More options"></i>
                </div>

            </div>{{-- /jc-col5 --}}
        </div>{{-- /job-card --}}

    @endforeach
@else
    <div class="text-center py-5 text-muted" style="font-size:13px;">No jobs available matching your criteria.</div>
@endif
