@php
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
            $ind_name = $getClientIdCompany->main_ind_name ?? '';

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

            /* ── Status badge ──────────────────────────────────── */
            $finalStatus = intval(@$row->final_status ?? 1);
            switch ($finalStatus) {
                case 1:  $badgeClass = 'aa-badge-open';    $badgeLabel = 'Open';    break;
                case 2:  $badgeClass = 'aa-badge-paused';  $badgeLabel = 'Paused';  break;
                case 3:  $badgeClass = 'aa-badge-expired'; $badgeLabel = 'Expired'; break;
                default: $badgeClass = 'aa-badge-closed';  $badgeLabel = 'Closed';  break;
            }

            /* ── Relation ID for Terms ─────────────────────────── */
            $relationId = @$row->id ?? '';
            $jobId      = @$row->job_id ?? '';
        @endphp

        <div class="aa-row">

            {{-- ── Agency ──────────────────────────────────────── --}}
            <div class="aa-col-agency">
                <div class="aa-logo" style="background:{{ $bgColor }};">
                    @if($logo_path)
                        <img src="{{ $logo_path }}" alt="{{ $companyName }}">
                    @else
                        {{ strtoupper($strVal) }}
                    @endif
                </div>
                <div style="min-width:0;">
                    <div class="aa-name">{{ substr(ucwords($companyName), 0, 22) }}</div>
                    @if($ind_name)
                        <div class="aa-cat">{{ substr(ucwords($ind_name), 0, 22) }}</div>
                    @endif
                </div>
            </div>

            {{-- ── Contact ──────────────────────────────────────── --}}
            <div class="aa-col-contact">
                @if($contact)
                    <div class="aa-cnt-name">{{ ucwords($contact->name) }}</div>
                    <div class="aa-cnt-btns">
                        @if(!empty($contact->email))
                            <button class="aa-cnt-btn email-icon"
                                    data-email="{{ $contact->email }}"
                                    data-name="{{ $contact->name }}"
                                    title="Send email">
                                <i class="fa-regular fa-envelope"></i>
                            </button>
                        @endif
                        @if(!empty($contact->mobile))
                            <button class="aa-cnt-btn phone-icon"
                                    data-phone="{{ $contact->mobile }}"
                                    data-country="IN"
                                    data-name="{{ $contact->name }}"
                                    title="Call contact">
                                <i class="fa-solid fa-phone"></i>
                            </button>
                        @endif
                    </div>
                @else
                    <span class="aa-cnt-dash">—</span>
                @endif
            </div>

            {{-- ── Performance KPIs (5 inline, no-wrap) ────────────── --}}
            <div class="aa-col-perf">
                <div class="aa-kpi">
                    <span class="aa-kpi-num">{{ $sourced }}</span>
                    <span class="aa-kpi-label">Sourced</span>
                </div>
                <div class="aa-kpi">
                    <span class="aa-kpi-num">0</span>
                    <span class="aa-kpi-label">Evaluated</span>
                </div>
                <div class="aa-kpi">
                    <span class="aa-kpi-num">{{ $interviews }}</span>
                    <span class="aa-kpi-label">Interviews</span>
                </div>
                <div class="aa-kpi">
                    <span class="aa-kpi-num">0</span>
                    <span class="aa-kpi-label">Offers</span>
                </div>
                <div class="aa-kpi">
                    <span class="aa-kpi-num">{{ $joined }}</span>
                    <span class="aa-kpi-label">Joined</span>
                </div>
            </div>

            {{-- ── Job Shared ────────────────────────────────────── --}}
            <div class="aa-col-shared">
                <div class="aa-shared-rel">{{ ucwords($sharedRel) }}</div>
                @if($sharedAbs)
                    <div class="aa-shared-abs">{{ $sharedAbs }}</div>
                @endif
            </div>

            {{-- ── Status ────────────────────────────────────────── --}}
            <div class="aa-col-status">
                <span class="aa-badge {{ $badgeClass }}">
                    <span class="aa-badge-dot"></span>{{ $badgeLabel }}
                </span>
            </div>

            {{-- ── Actions ───────────────────────────────────────── --}}
            <div class="aa-col-actions">
                {{-- View Terms --}}
                <button class="aa-terms-btn client-terms-accept"
                        data-row-id="{{ $relationId }}"
                        data-row-relation_id="{{ $relationId }}"
                        title="View agency terms &amp; conditions">
                    View Terms
                </button>

                {{-- Three-dot menu --}}
                <button class="aa-dots-btn" title="More actions" type="button">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <div class="aa-ddmenu">
                    <button class="aa-ddi remove_job"
                            data-row-tabname="Assigned"
                            data-row-ename="remove"
                            data-row-jobid="{{ $jobId }}"
                            data-row-fromcompany="{{ @$row->from_company_id }}"
                            data-row-tocompany="{{ @$row->to_company_id }}"
                            type="button">
                        <i class="fa-regular fa-trash-can" style="width:14px;"></i> Remove Agency
                    </button>
                </div>
            </div>

        </div>
    @endforeach
@else
    {{-- Empty state --}}
    <div class="aa-empty">
        <div class="aa-empty-icon">
            <i class="fa-regular fa-building"></i>
        </div>
        <div class="aa-empty-title">No Agencies Assigned</div>
        <div class="aa-empty-sub">Share this job with agencies to receive candidate submissions.</div>
    </div>
@endif
