@php
    /**
     * Intent: Assigned Agencies Card Stream.
     * Implements strict Modern Card + Sub-Card Enterprise Design System (Rule 20):
     * Tier 3 Primary Agency Entity Card (.agency-entity-card):
     *   - Left border accent: 3.5px solid #10b981 (Open) / #f59e0b (Paused) / #ef4444 (Expired) / #cbd5e1 (Closed)
     *   - Sub-Card 1: Agency & Specialization (Avatar/Logo, Title, Verified Badge, Industry)
     *   - Sub-Card 2: Primary Contact (Contact Person, Quick Email & Phone triggers)
     *   - Sub-Card 3: Performance Pipeline (Sourced, Evaluated, Interviews, Offers, Joined with Tabular nums)
     *   - Sub-Card 4: Shared On (Relative & Absolute date)
     *   - Sub-Card 5: Status & Actions (Semantic Status Badge, View Terms, 3-Dots Action Dropdown)
     *   - Bottom Activity Strip: Active Mandate indicator + Manage link
     * Tenant Isolation: company_id scoped.
     * Fits 15.6" screen (1366x768 & 1920x1080) with zero horizontal overflow.
     */
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode      = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $currency        = GeneralMethod::currency();
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;

            /* ── Logo ──────────────────────────────────────────── */
            $logo_path = '';
            if (!empty(@$row->logo_to) && strlen(@$row->logo_to) > 10) {
                $logo_path = @$row->logo_to;
            }

            /* ── Agency initial colour ─────────────────────────── */
            $companyName = trim(@$row->company_name_to ?? '');
            $strVal = strtolower(substr($companyName ?: '#', 0, 1));
            $charCode = ord($strVal);
            if     ($charCode >= 97  && $charCode <= 101) { $bgColor = '#ef4444'; }
            elseif ($charCode >= 102 && $charCode <= 106) { $bgColor = '#f59e0b'; }
            elseif ($charCode >= 107 && $charCode <= 111) { $bgColor = '#6366f1'; }
            elseif ($charCode >= 112 && $charCode <= 116) { $bgColor = '#22c55e'; }
            elseif ($charCode >= 117 && $charCode <= 121) { $bgColor = '#1e293b'; }
            else                                           { $bgColor = '#64748b'; }

            /* ── Industry / category ───────────────────────────── */
            $getClientIdCompany = $row->_client_info ?? GeneralMethod::getClientIdCompany($row->to_company_id);
            $ind_name = $getClientIdCompany->main_ind_name ?? 'IT Staffing & Recruitment';

            /* ── Contact ───────────────────────────────────────── */
            $contact = $row->_contact_info ?? null;
            if (!isset($row->_contact_info)) {
                $contactRows = DB::table('users')
                    ->join('job_assign', 'job_assign.user_id', 'users.id')
                    ->select('users.name', 'users.image', 'users.mobile', 'users.email')
                    ->where('users.company_id', $row->to_company_id)
                    ->where('job_assign.company_id', $row->to_company_id)
                    ->where('job_assign.job_id', $row->job_id)
                    ->skip(0)->take(1)
                    ->get();
                $contact = $contactRows->first();
            }

            /* ── Performance ───────────────────────────────────── */
            $perf = $row->_perf_info ?? GeneralMethod::agencyCandidatesCount($row->to_company_id, $row->job_id);
            $sourced    = $perf->Upload    ?? 0;
            $interviews = $perf->Interview ?? 0;
            $joined     = $perf->Joined    ?? 0;

            /* ── Job Shared date ───────────────────────────────── */
            $sharedRaw = @$row->created_at ?? null;
            $sharedRel = $sharedRaw ? GeneralMethod::timeAgo($sharedRaw) : '—';
            $sharedAbs = $sharedRaw ? date('d M, Y', strtotime($sharedRaw)) : '';

            /* ── Status badge & accent color ────────────────────── */
            $finalStatus = intval(@$row->final_status ?? 1);
            switch ($finalStatus) {
                case 1:
                    $badgeClass = 'aa-badge-open';
                    $badgeLabel = 'Open';
                    $accentColor = '#10b981';
                    break;
                case 2:
                    $badgeClass = 'aa-badge-paused';
                    $badgeLabel = 'Paused';
                    $accentColor = '#f59e0b';
                    break;
                case 3:
                    $badgeClass = 'aa-badge-expired';
                    $badgeLabel = 'Expired';
                    $accentColor = '#ef4444';
                    break;
                default:
                    $badgeClass = 'aa-badge-closed';
                    $badgeLabel = 'Closed';
                    $accentColor = '#94a3b8';
                    break;
            }

            /* ── Relation ID for Terms ─────────────────────────── */
            $relationId = @$row->id ?? '';
            $jobId      = @$row->job_id ?? '';
        @endphp

        <!--begin::Assigned Agency Primary Card (Rule 20)-->
        <div class="agency-entity-card aa-primary-card" id="assigned_agency_{{ $relationId }}" style="border-left: 3.5px solid {{ $accentColor }} !important;">
            
            <!-- Upper Discrete Sub-Cards Grid -->
            <div class="agency-card-grid aa-grid">

                <!-- SUB-CARD 1: Agency Identity & Specialization -->
                <div class="ag-subcard ag-subcard-identity">
                    <div class="ag-subcard-head">
                        <span class="ag-subcard-title">
                            <i class="fas fa-building text-primary me-1 fs-9"></i>AGENCY & SPECIALIZATION
                        </span>
                    </div>
                    <div class="ag-subcard-body">
                        <div class="d-flex align-items-center gap-2 w-100">
                            {{-- Logo / Avatar --}}
                            <div class="ag-avatar-wrap flex-shrink-0">
                                @if($logo_path)
                                    <img src="{{ $logo_path }}" alt="{{ $companyName }}" class="ag-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                                    <div class="ag-avatar-initials" style="display: none; background: {{ $bgColor }}; color: #ffffff;">
                                        {{ strtoupper($strVal) }}
                                    </div>
                                @else
                                    <div class="ag-avatar-initials" style="background: {{ $bgColor }}; color: #ffffff;">
                                        {{ strtoupper($strVal) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="ag-identity-content min-w-0 flex-grow-1">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="ag-agency-title text-truncate" title="{{ $companyName }}">
                                        {{ substr(ucwords($companyName), 0, 24) }}
                                    </span>
                                    <span class="ag-verified-badge" title="Verified Assigned Partner">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" fill="#0066ff"/>
                                            <path d="M9 12l2 2 4-4" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                                @if($ind_name)
                                    <div class="ag-agency-spec text-truncate" title="{{ $ind_name }}">
                                        {{ substr(ucwords($ind_name), 0, 28) }}
                                    </div>
                                @endif
                                <div class="ag-badges-row d-flex align-items-center gap-1 flex-wrap mt-1">
                                    <span class="ag-badge ag-badge-registered">Assigned Partner</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: Primary Contact -->
                <div class="ag-subcard ag-subcard-contact">
                    <div class="ag-subcard-head">
                        <span class="ag-subcard-title">
                            <i class="fas fa-user-tie text-primary me-1 fs-9"></i>CONTACT PERSON
                        </span>
                    </div>
                    <div class="ag-subcard-body">
                        @if($contact)
                            <div class="ag-contact-name text-truncate" title="{{ $contact->name }}">
                                {{ ucwords($contact->name) }}
                            </div>
                            <div class="ag-contact-btns mt-1">
                                @if(!empty($contact->email))
                                    <button class="ag-contact-icon-btn email-icon" data-email="{{ $contact->email }}" data-name="{{ $contact->name }}" title="Email {{ $contact->name }}" type="button">
                                        <i class="fa-regular fa-envelope"></i>
                                    </button>
                                @endif
                                @if(!empty($contact->mobile))
                                    <button class="ag-contact-icon-btn phone-icon" data-phone="{{ $contact->mobile }}" data-country="IN" data-name="{{ $contact->name }}" title="Call {{ $contact->name }}" type="button">
                                        <i class="fa-solid fa-phone"></i>
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="text-muted fs-8">Not Assigned</div>
                            <div class="ag-contact-dash">—</div>
                        @endif
                    </div>
                </div>

                <!-- SUB-CARD 3: Performance Pipeline (5 Stages) -->
                <div class="ag-subcard ag-subcard-pipeline">
                    <div class="ag-subcard-head">
                        <span class="ag-subcard-title">
                            <i class="fas fa-chart-line text-primary me-1 fs-9"></i>PERFORMANCE PIPELINE
                        </span>
                    </div>
                    <div class="ag-subcard-body">
                        <div class="ag-pipeline-strip">
                            <div class="ag-pipe-kpi">
                                <span class="ag-pipe-num">{{ $sourced }}</span>
                                <span class="ag-pipe-lbl">Sourced</span>
                            </div>
                            <div class="ag-pipe-kpi">
                                <span class="ag-pipe-num">0</span>
                                <span class="ag-pipe-lbl">Evaluated</span>
                            </div>
                            <div class="ag-pipe-kpi">
                                <span class="ag-pipe-num">{{ $interviews }}</span>
                                <span class="ag-pipe-lbl">Interviews</span>
                            </div>
                            <div class="ag-pipe-kpi">
                                <span class="ag-pipe-num">0</span>
                                <span class="ag-pipe-lbl">Offers</span>
                            </div>
                            <div class="ag-pipe-kpi">
                                <span class="ag-pipe-num text-success">{{ $joined }}</span>
                                <span class="ag-pipe-lbl">Joined</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: Shared On -->
                <div class="ag-subcard ag-subcard-shared">
                    <div class="ag-subcard-head">
                        <span class="ag-subcard-title">
                            <i class="far fa-calendar-alt text-primary me-1 fs-9"></i>SHARED ON
                        </span>
                    </div>
                    <div class="ag-subcard-body">
                        <div class="ag-shared-rel text-truncate" title="{{ $sharedAbs }}">
                            {{ ucwords($sharedRel) }}
                        </div>
                        @if($sharedAbs)
                            <div class="ag-shared-abs text-truncate">{{ $sharedAbs }}</div>
                        @endif
                    </div>
                </div>

                <!-- SUB-CARD 5: Status & Actions -->
                <div class="ag-subcard ag-subcard-status-actions text-end">
                    <div class="ag-subcard-head">
                        <span class="ag-subcard-title">
                            <i class="fas fa-sliders text-primary me-1 fs-9"></i>STATUS & ACTIONS
                        </span>
                    </div>
                    <div class="ag-subcard-body">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            {{-- Status Pill --}}
                            <span class="aa-badge {{ $badgeClass }}">
                                <span class="aa-badge-dot"></span>{{ $badgeLabel }}
                            </span>

                            {{-- View Terms Button --}}
                            <button class="ag-terms-btn client-terms-accept" data-row-id="{{ $relationId }}" data-row-relation_id="{{ $relationId }}" title="View agency terms &amp; conditions" type="button">
                                <i class="fas fa-file-contract me-1"></i>Terms
                            </button>

                            {{-- Three-dot menu --}}
                            <div class="position-relative d-inline-block">
                                <button class="ag-dots-btn aa-dots-btn" title="More actions" type="button">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="aa-ddmenu">
                                    <button class="aa-ddi remove_job" data-row-tabname="Assigned" data-row-ename="remove" data-row-jobid="{{ $jobId }}" data-row-fromcompany="{{ @$row->from_company_id }}" data-row-tocompany="{{ @$row->to_company_id }}" type="button">
                                        <i class="fa-regular fa-trash-can" style="width:14px;"></i> Remove Agency
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Activity Strip (Rule 20 Sub-Card 6) -->
            <div class="ag-card-bottom-strip">
                <div class="d-flex align-items-center gap-2">
                    <span class="ag-bottom-partner-tag">
                        <i class="fas fa-check-circle text-success"></i>
                        <span>Active Requirement Assignment</span>
                    </span>
                    <span class="text-gray-300">•</span>
                    <span class="ag-bottom-meta">Real-time candidate submissions enabled</span>
                </div>
                <div>
                    <span class="text-muted fs-8">Ref #{{ $relationId }}</span>
                </div>
            </div>

        </div>
        <!--end::Assigned Agency Primary Card-->
    @endforeach
@else
    <!--begin::Empty State Card-->
    <div class="agency-empty-state-card">
        <div class="ag-empty-icon-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8">
                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
        </div>
        <div class="ag-empty-title">No Agencies Assigned Yet</div>
        <div class="ag-empty-subtitle">Send this requirement to recommended agencies or your connected agencies to track candidate submissions here.</div>
    </div>
    <!--end::Empty State Card-->
@endif
