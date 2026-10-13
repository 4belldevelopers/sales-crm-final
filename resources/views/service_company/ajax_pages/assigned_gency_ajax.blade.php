@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode      = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $currency        = GeneralMethod::currency();
@endphp

<style>
    /* ── Assigned Agencies AJAX Rows ── */
    .aa-row {
        display: flex !important;
        align-items: center !important;
        background: #ffffff !important;
        border: 1px solid #e8ecf1 !important;
        border-radius: 10px !important;
        padding: 10px 14px !important;
        margin-bottom: 6px !important;
        transition: box-shadow 0.15s ease, border-color 0.15s ease !important;
        gap: 0 !important;
        min-height: 56px !important;
        font-family: 'Poppins', sans-serif !important;
        width: 100% !important;
    }
    .aa-row:hover {
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05) !important;
        border-color: #cbd5e1 !important;
    }
    .aa-col-agency {
        flex: 0 0 22% !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        min-width: 0 !important;
        padding-right: 8px !important;
    }
    .aa-col-contact {
        flex: 0 0 15% !important;
        min-width: 0 !important;
        padding-right: 8px !important;
    }
    .aa-col-perf {
        flex: 0 0 29% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0 !important;
        flex-wrap: nowrap !important;
    }
    .aa-col-shared {
        flex: 0 0 11% !important;
        min-width: 0 !important;
        padding-right: 6px !important;
    }
    .aa-col-status {
        flex: 0 0 9% !important;
        text-align: center !important;
    }
    .aa-col-actions {
        flex: 1 1 auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 6px !important;
        position: relative !important;
        min-width: 120px !important;
    }
    .aa-logo {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        max-width: 38px !important;
        max-height: 38px !important;
        border-radius: 50% !important;
        flex-shrink: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        color: #ffffff !important;
        overflow: hidden !important;
        border: 1.5px solid #f1f5f9 !important;
    }
    .aa-logo img {
        width: 100% !important;
        height: 100% !important;
        max-width: 38px !important;
        max-height: 38px !important;
        object-fit: cover !important;
        border-radius: 50% !important;
        display: block !important;
    }
    .aa-name {
        font-weight: 600 !important;
        font-size: 12px !important;
        color: #0f172a !important;
        line-height: 1.3 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .aa-cat {
        font-size: 10.5px !important;
        color: #64748b !important;
        margin-top: 1px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .aa-cnt-name {
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .aa-cnt-btns {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        margin-top: 3px !important;
    }
    .aa-cnt-btn {
        width: 24px !important;
        height: 24px !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        background: #f8fafc !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 11px !important;
        color: #64748b !important;
        cursor: pointer !important;
        transition: background 0.12s, color 0.12s, border-color 0.12s !important;
    }
    .aa-cnt-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }
    .aa-cnt-dash {
        color: #94a3b8 !important;
        font-size: 13px !important;
    }
    .aa-kpi {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 1px !important;
        flex: 1 1 0 !important;
        min-width: 0 !important;
        padding: 0 2px !important;
        border-right: 1px solid #f1f5f9 !important;
    }
    .aa-kpi:last-child {
        border-right: none !important;
    }
    .aa-kpi-num {
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.1 !important;
    }
    .aa-kpi-label {
        font-size: 8.5px !important;
        color: #64748b !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.2px !important;
        white-space: nowrap !important;
    }
    .aa-shared-rel {
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        line-height: 1.2 !important;
    }
    .aa-shared-abs {
        font-size: 10px !important;
        color: #64748b !important;
        margin-top: 1px !important;
    }
    .aa-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 3px 8px !important;
        border-radius: 6px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }
    .aa-badge-dot {
        width: 6px !important;
        height: 6px !important;
        border-radius: 50% !important;
        background: currentColor !important;
    }
    .aa-badge-open    { background: #dcfce7 !important; color: #16a34a !important; }
    .aa-badge-closed  { background: #f1f5f9 !important; color: #64748b !important; }
    .aa-badge-paused  { background: #fef3c7 !important; color: #d97706 !important; }
    .aa-badge-expired { background: #fee2e2 !important; color: #dc2626 !important; }
    .aa-terms-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 5px 10px !important;
        border-radius: 7px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        cursor: pointer !important;
        white-space: nowrap !important;
        transition: border-color 0.12s, color 0.12s, background 0.12s !important;
    }
    .aa-terms-btn:hover {
        border-color: #0066ff !important;
        color: #0066ff !important;
        background: #eff6ff !important;
    }
    .aa-dots-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        cursor: pointer !important;
        color: #64748b !important;
        font-size: 13px !important;
        transition: background 0.12s, border-color 0.12s !important;
    }
    .aa-dots-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #1e293b !important;
    }
    .aa-ddmenu {
        position: absolute !important;
        right: 0 !important;
        top: calc(100% + 4px) !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08) !important;
        z-index: 100 !important;
        min-width: 155px !important;
        padding: 4px 0 !important;
        display: none !important;
    }
    .aa-ddmenu.open {
        display: block !important;
    }
    .aa-ddi {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 7px 12px !important;
        font-size: 11.5px !important;
        color: #ef4444 !important;
        cursor: pointer !important;
        border: none !important;
        background: none !important;
        width: 100% !important;
        text-align: left !important;
        white-space: nowrap !important;
        transition: background 0.1s !important;
    }
    .aa-ddi:hover {
        background: #fef2f2 !important;
    }
    .aa-empty {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 48px 20px !important;
        text-align: center !important;
    }
    .aa-empty-icon {
        width: 56px !important;
        height: 56px !important;
        border-radius: 50% !important;
        background: #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-bottom: 12px !important;
        font-size: 22px !important;
        color: #94a3b8 !important;
    }
    .aa-empty-title {
        font-size: 13.5px !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        margin-bottom: 4px !important;
    }
    .aa-empty-sub {
        font-size: 12px !important;
        color: #94a3b8 !important;
        max-width: 280px !important;
        line-height: 1.5 !important;
    }
</style>

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
                <div class="aa-logo" style="width: 38px; height: 38px; min-width: 38px; min-height: 38px; max-width: 38px; max-height: 38px; border-radius: 50%; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: {{ $bgColor }};">
                    @if($logo_path)
                        <img src="{{ $logo_path }}" alt="{{ $companyName }}" style="width: 100%; height: 100%; max-width: 38px; max-height: 38px; object-fit: cover; border-radius: 50%; display: block;" />
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
