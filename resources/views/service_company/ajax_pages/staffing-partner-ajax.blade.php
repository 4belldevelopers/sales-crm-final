@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            
            // Batch pre-hydrated relation lookup or fallback
            $relationget = isset($batchRelations[$row->company_id]) 
                ? $batchRelations[$row->company_id] 
                : GeneralMethod::getRelationData($row->company_id, $company_id);

            $from_company_id   = $relationget->from_company_id ?? null;
            $recommendation_id = $relationget->to_company_id ?? null;
            $final_status      = $relationget->final_status ?? null;
            $to_created_at     = $relationget->to_created_at ?? null;
            $created_at        = $relationget->created_at ?? null;

            $row->final_status = $final_status;
            $row->staffing_company_relation_id = $recommendation_id;
            $row->to_company_id = $recommendation_id;
            $row->from_company_id = $from_company_id;
            $row->to_created_at = ($to_created_at) ? $to_created_at : $created_at;

            // Batch pre-hydrated industry lookup or fallback
            $td_industries = isset($batchIndustries[$row->industries]) 
                ? (object) $batchIndustries[$row->industries] 
                : DB::table('td_industries')->where('ind_id', '=', $row->industries)->first();

            $logo_path = '';
            if (!empty(@$row->website) && empty(@$row->logo)){
                $domainGet = GeneralMethod::getDomain($row->website);
                $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
                $google = GeneralMethod::getDomainIcon($googleIcon, $row->id, true);
                $logo_path  = ($google) ? $googleIcon : '';
            }
            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = @$row->logo;
            }

            $company_name = ucwords($row->company_name);
            $ind_name = $td_industries ? $td_industries->ind_name : 'Staffing & Recruitment';
            $client_type_css = 'show-detail-direct';
                
            $bgTextDecore = GeneralMethod::icoinText($row->company_name);
            $initials = strtoupper(implode('', array_map(fn($word) => mb_substr($word, 0, 1), explode(' ', trim($row->company_name)))));
            if (strlen($initials) > 3) {
                $initials = substr($initials, 0, 2);
            }

            $client_id = $row->client_id;
            
            // Batch pre-hydrated location lookup or fallback
            $locInfo = isset($batchLocations[$client_id]) 
                ? $batchLocations[$client_id] 
                : DB::table('client_location')
                    ->select(
                        'client_location.id',
                        'client_location.city',
                        'client_location.address',
                        'client_location.postcode',
                        'client_location.country_id',
                        'city_list.city as city_name',
                        'state_list.state'
                    )
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                    ->where('client_id', $client_id)
                    ->first();

            $country_name = '';
            $locInfostate = '';
            if ($locInfo) {
                if (@$locInfo->country_id > 0) {
                    $countryArr = GeneralMethod::getCountryName($locInfo->country_id);
                    if (is_array($countryArr) && count($countryArr) > 0) {
                        foreach ($countryArr as $cval) {
                            $country_name = ucwords($cval['name']);
                        }
                        if (isset($locInfo->state_name)) {
                            $locInfostate = $locInfo->state_name;
                        } elseif (isset($locInfo->state)) {
                            $locInfostate = $locInfo->state;
                        }
                    }
                }
            }

            $city = @$locInfo->city_name ?: @$locInfo->city;

            $to_created_at = $row->to_created_at;
            $newDate = $to_created_at ? date('d M, Y', strtotime($to_created_at)) : '';
            $timeAgo = $to_created_at ? GeneralMethod::timeAgo($to_created_at) : 'Recently';

            // Batch pre-hydrated job stats lookup or fallback
            $RowJobs = isset($batchAgencyStats[$row->company_id])
                ? $batchAgencyStats[$row->company_id]
                : GeneralMethod::agencyJobsCountAllstatus($row->company_id, $company_id);

            $Active = $RowJobs->job_active ?? 0;
            $Hold   = $RowJobs->job_hold ?? 0;
            $Closed = $RowJobs->job_inactive ?? 0;

            // Batch pre-hydrated candidate stats lookup or fallback
            $candidatesProfiles = isset($batchAgencyStats[$row->company_id])
                ? $batchAgencyStats[$row->company_id]
                : GeneralMethod::agencyCandidatesCountList($row->company_id, $company_id);

            $all_candidates = $candidatesProfiles->all_candidates ?? 0;
            $Joined         = $candidatesProfiles->Joined ?? 0;
            $offers         = $candidatesProfiles->offers ?? 0;
            $sourced        = $candidatesProfiles->sourced ?? 0;
            $evaluated      = $candidatesProfiles->evaluated ?? 0;
            $all_interview  = $candidatesProfiles->all_interview ?? 0;

            $isDirect = ($final_status == 1);
            $scopeLabel = $isDirect ? 'Direct Agency' : 'Marketplace Partner';
            $scopeClass = $isDirect ? 'pill-direct type-direct' : 'pill-internal type-internal';

            /* ── company size (Employee Strength) ── */
            $compSizeObj = $batchCompanySizes[$row->company_id] ?? null;
            $fromSize = $row->company_size_from ?? ($compSizeObj->company_size_from ?? null);
            $toSize = $row->company_sizes ?? $row->company_size ?? ($compSizeObj->company_size ?? null);
            $companySizeText = '';
            if (!empty($fromSize) && !empty($toSize)) {
                $companySizeText = number_format((int)$fromSize) . ' - ' . number_format((int)$toSize) . ' employees';
            } elseif (!empty($toSize)) {
                $companySizeText = is_numeric($toSize) ? number_format((int)$toSize) . ' employees' : $toSize . ' employees';
            }
        @endphp

        <!-- ── Tier 3 Primary Card Stream (Rule 20 Enterprise Card + Sub-Card Design) ── -->
        <div class="cl-primary-card tbl-r {{ $Active > 0 ? 'is-active-client' : 'is-inactive-client' }}" 
             data-row-id="{{ $row->client_id }}" 
             data-client-type="direct" 
             data-active-jobs="{{ $Active }}" 
             data-agency-name="{{ e($company_name) }}">
            
            <div class="cl-subcards-grid">
                <!-- SUB-CARD 1: Agency Identity -->
                <div class="cl-subcard cl-subcard-identity">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Agency Identity</span>
                        <div class="cl-subcard-badges">
                            <span class="cl-scope-pill {{ $scopeClass }} {{ $client_type_css }}" data-row-id="{{ $row->client_id }}">
                                {{ $scopeLabel }}
                            </span>
                            <span class="cl-status-pill {{ $Active > 0 ? 'pill-active' : 'pill-inactive' }}">
                                <span class="cl-status-pill-dot"></span>
                                <span>{{ $Active > 0 ? 'Active' : 'Inactive' }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="cl-identity-body">
                        <div class="cl-logo-box">
                            @if (!empty($logo_path))
                                <img src="{{ $logo_path }}" alt="{{ e($initials) }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                                <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                            @else
                                <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="cl-details-box">
                            <div class="cl-name {{ $client_type_css }}" data-row-id="{{ $row->client_id }}">{{ $company_name }}</div>
                            <div class="cl-ind" title="{{ e($ind_name) }}">{{ $ind_name }}</div>
                            @if(!empty($companySizeText))
                                <div class="cl-size">{{ $companySizeText }}</div>
                            @else
                                <div class="cl-size text-muted">—</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: Location -->
                <div class="cl-subcard cl-subcard-loc">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Location</span>
                    </div>
                    <div class="cl-loc-body">
                        <svg class="cl-loc-pin" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <div class="cl-loc-lines">
                            @if ($city)
                                <div class="cl-loc-city" title="{{ e($city) }}">{{ $city }}</div>
                            @endif
                            @if ($locInfostate)
                                <div class="cl-loc-state" title="{{ e($locInfostate) }}">{{ $locInfostate }}</div>
                            @endif
                            @if ($country_name)
                                <div class="cl-loc-country" title="{{ e($country_name) }}">{{ $country_name }}</div>
                            @endif
                            @if (!$city && !$locInfostate && !$country_name)
                                <div class="cl-loc-empty">—</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 3: Shared Jobs Status -->
                <div class="cl-subcard cl-subcard-jobs">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Shared Jobs</span>
                    </div>
                    <div class="cl-jobs-body tbl-cell-jobs">
                        <div class="cl-job-line {{ $Active > 0 ? 'has-count cl-job-hiring' : 'no-count' }}">
                            <span class="cl-job-bullet {{ $Active > 0 ? 'bullet-hiring' : 'bullet-neutral' }}"></span>
                            <span class="cl-job-num">{{ $Active }}</span>
                            <span class="cl-job-label">Active</span>
                        </div>
                        <div class="cl-job-line {{ $Hold > 0 ? 'has-count cl-job-hold' : 'no-count' }}">
                            <span class="cl-job-bullet {{ $Hold > 0 ? 'bullet-hold' : 'bullet-neutral' }}"></span>
                            <span class="cl-job-num">{{ $Hold }}</span>
                            <span class="cl-job-label">Hold</span>
                        </div>
                        <div class="cl-job-line {{ $Closed > 0 ? 'has-count cl-job-closed' : 'no-count' }}">
                            <span class="cl-job-bullet {{ $Closed > 0 ? 'bullet-closed' : 'bullet-neutral' }}"></span>
                            <span class="cl-job-num">{{ $Closed }}</span>
                            <span class="cl-job-label">Closed</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: Candidate Pipeline Flow -->
                <div class="cl-subcard cl-subcard-pipeline">
                    <div class="cl-subcard-head">
                        <div class="cl-subcard-title-wrap">
                            <svg class="cl-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                            </svg>
                            <span class="cl-subcard-title">Candidate Pipeline</span>
                        </div>
                    </div>
                    <div class="cl-pipe-body tbl-cell-pipeline" title="Total Submissions: {{ $all_candidates }} | Interviews: {{ $all_interview }} | Offers: {{ $offers }} | Joined: {{ $Joined }}">
                        <div class="cl-pipeline-flow">
                            <div class="cl-pipe-step step-sourcing {{ $sourced > 0 ? 'has-count' : '' }}" title="Sourcing: {{ $sourced }}">
                                <span class="cl-pipe-pill pill-sourcing">{{ $sourced }}</span>
                                <span class="cl-pipe-label">Sourcing</span>
                            </div>
                            <div class="cl-pipe-step step-screening {{ $evaluated > 0 ? 'has-count' : '' }}" title="Screening: {{ $evaluated }}">
                                <span class="cl-pipe-pill pill-screening">{{ $evaluated }}</span>
                                <span class="cl-pipe-label">Screening</span>
                            </div>
                            <div class="cl-pipe-step step-evaluation {{ $evaluated > 0 ? 'has-count' : '' }}" title="Evaluation: {{ $evaluated }}">
                                <span class="cl-pipe-pill pill-evaluation">{{ $evaluated }}</span>
                                <span class="cl-pipe-label">Evaluation</span>
                            </div>
                            <div class="cl-pipe-step step-negotiations no-count" title="Negotiation: 0">
                                <span class="cl-pipe-pill pill-negotiations">0</span>
                                <span class="cl-pipe-label">Negotiation</span>
                            </div>
                            <div class="cl-pipe-step step-offer {{ $offers > 0 ? 'has-count' : '' }}" title="Offer: {{ $offers }}">
                                <span class="cl-pipe-pill pill-offer">{{ $offers }}</span>
                                <span class="cl-pipe-label">Offer</span>
                            </div>
                            <div class="cl-pipe-step step-joined {{ $Joined > 0 ? 'has-count' : '' }}" title="Joined: {{ $Joined }}">
                                <span class="cl-pipe-pill pill-joined">{{ $Joined }}</span>
                                <span class="cl-pipe-label">Joined</span>
                            </div>
                            <div class="cl-pipe-step step-rejected no-count" title="Rejected: 0">
                                <span class="cl-pipe-pill pill-rejected">0</span>
                                <span class="cl-pipe-label">Rejected</span>
                            </div>
                            <div class="cl-pipe-divider" aria-hidden="true"></div>
                            <div class="cl-pipe-step step-interview {{ $all_interview > 0 ? 'has-count' : '' }}" title="Interview: {{ $all_interview }}">
                                <span class="cl-pipe-pill pill-interview">{{ $all_interview }}</span>
                                <span class="cl-pipe-label">Interview</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM STRIP: Connected Date, Last Activity, Submissions Badge & Actions -->
            <div class="cl-card-bottom-strip">
                <div class="cl-bottom-meta">
                    <span class="cl-meta-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span>Partnered: <strong>{{ $newDate ?: 'Recently' }}</strong></span>
                    </span>
                    <span class="cl-meta-dot">•</span>
                    <span class="cl-meta-item">
                        <span class="cl-dot cl-dot-green"></span>
                        <span>Activity: <strong>{{ $timeAgo ?: 'Active this week' }}</strong></span>
                    </span>
                    <span class="cl-meta-dot">•</span>
                    <span class="cl-meta-item cl-meta-apps">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Submissions:</span>
                        <span class="cl-apps-badge {{ $all_candidates > 0 ? 'has-apps' : '' }}">{{ $all_candidates }}</span>
                    </span>
                </div>
                <div class="cl-bottom-actions">
                    <button type="button" class="btn btn-sm btn-light-primary fw-bold px-3 py-1.5 {{ $client_type_css }}" data-row-id="{{ $row->client_id }}" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; white-space: nowrap !important; line-height: 1 !important; font-size: 11px; border-radius: 6px;">
                        <span>View Agency</span>
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display: inline-block; vertical-align: middle; flex-shrink: 0;"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="cl-empty-state">
        <div class="cl-empty-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
        </div>
        <div class="cl-empty-title">No agencies found</div>
        <div class="cl-empty-desc">No agencies match your selected filters or search query.</div>
    </div>
@endif
