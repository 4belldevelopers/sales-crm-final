@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
        // Support both raw Elasticsearch _source array/object and InterviewReadModel array/object
        $readModel = is_array($rowArr) && isset($rowArr['candidate']['name'])
            ? $rowArr
            : \App\Services\Interview\InterviewReadModel::fromElasticSource((array) $rowArr, $company_id ?? auth()->user()->company_id);

        $cand   = $readModel['candidate'];
        $fu     = $readModel['followup'];
        $job    = $readModel['job'];
        $sch    = $readModel['schedule'];
        $intvs  = $readModel['interviewers_summary'];
        $src    = $readModel['source_info'];
        $stg    = $readModel['stage'];
        $fb     = $readModel['feedback_view'];
        $att    = $readModel['attendance'] ?? [];
        $eval   = $readModel['evaluation'] ?? [];

        $jobTitleInitial = strtoupper(mb_substr($job['title'] ?? 'J', 0, 1) ?: 'J');

        // Panelists list processing
        $panelList = $readModel['interviewers'] ?? [];
        if (empty($panelList)) {
            $leadFallback = !empty($intvs['lead_name']) && $intvs['lead_name'] !== 'N/A' ? $intvs['lead_name'] : 'Panel Assigned';
            $panelList[] = [
                'name' => $leadFallback,
                'company_name' => $intvs['lead_company'] ?? '',
                'is_lead' => true,
            ];
            $extraCount = (int) ($intvs['extra_count'] ?? 0);
            for ($i = 1; $i <= $extraCount; $i++) {
                $panelList[] = [
                    'name' => 'Panelist #' . ($i + 1),
                    'company_name' => $intvs['lead_company'] ?? '',
                    'is_lead' => false,
                ];
            }
        }
        $totalPanelists = count($panelList);
        $leadName = $panelList[0]['name'] ?? 'Panel Assigned';
        $leadInitial = ($leadName && $leadName !== 'Panel Assigned') ? strtoupper(mb_substr($leadName, 0, 1)) : 'P';
        $leadCompany = $panelList[0]['company_name'] ?? ($intvs['lead_company'] ?? '');
        $extraPanelistsCount = max(0, $totalPanelists - 1);
        $panelBadgeText = $totalPanelists === 1 ? '1 Panelist' : "{$totalPanelists} Panelists";
        $panelistTooltipText = implode(', ', array_map(function($p, $idx) {
            return ($idx === 0) ? ($p['name'] . ' (Lead)') : $p['name'];
        }, $panelList, array_keys($panelList)));
    @endphp
        <!-- Master Primary Card for Interview Record -->
        <div class="cd-it-card" data-interview-id="{{ $readModel['id'] }}" data-status="{{ $att['code'] ?? 0 }}" data-feedback="{{ $eval['code'] ?? 0 }}">
            <!-- Upper Discrete 6 Sub-Cards Grid -->
            <div class="cd-it-card-grid">

                <!-- 1. CANDIDATE INFO -->
                <div class="cd-it-subcard">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">CANDIDATE INFO</span>
                        @if (!empty($fu['label']))
                            <span class="cd-followup-pill {{ $fu['style_class'] ?? 'confirmed' }}">
                                {{ $fu['label'] }}
                            </span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="cd-avatar-box {{ $cand['avatar_color_class'] ?? 'avatar-blue' }}">
                            {{ $cand['initial'] ?? 'C' }}
                        </div>
                        <div class="min-w-0 flex-grow-1 overflow-hidden">
                            <a href="javascript:void(0)" class="cd-it-cand-name client-show-application-detail js-view-candidate" data-row-id="{{ $cand['application_id'] ?: $cand['id'] }}" data-id="{{ $cand['id'] }}" title="{{ $cand['name'] }}">
                                {{ $cand['name'] }}
                            </a>
                            <div class="cd-it-cand-role" title="{{ $cand['designation'] }}">
                                {{ $cand['designation'] ?: '—' }}
                            </div>
                            <div class="cd-it-cand-company" title="{{ $cand['current_company'] ?? '' }}">
                                <i class="far fa-building me-1 text-gray-400"></i>
                                <span>{{ $cand['current_company'] ?: '88 Technology' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. TARGET REQUISITION -->
                <div class="cd-it-subcard">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">TARGET REQUISITION</span>
                        <div class="d-flex align-items-center gap-1">
                            <span class="cd-badge-scope {{ $job['job_type_class'] ?? 'scope-direct' }}">
                                {{ $job['job_type_label'] ?? 'Direct Client' }}
                            </span>
                            <span class="cd-status-hiring">Hiring</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="cd-avatar-box avatar-purple">
                            {{ $jobTitleInitial }}
                        </div>
                        <div class="min-w-0 flex-grow-1 overflow-hidden">
                            <a href="{{ url('service_company/jobs/' . $job['id']) }}" target="_blank" class="cd-it-job-title" title="{{ $job['title'] }}">
                                {{ $job['title'] }}
                            </a>
                            <div class="cd-it-job-meta">
                                @if(!empty($job['job_id_display']))
                                    <span class="cd-it-job-id">{{ $job['job_id_display'] }}</span>
                                    <span class="cd-dot-sep">•</span>
                                @endif
                                <span>Technology</span>
                            </div>
                            <div class="cd-it-job-emp" title="{{ $job['display_company_name'] ?? $job['client_name'] }}">
                                {{ $job['display_company_name'] ?: ($job['client_name'] ?: 'Client Account') }}
                                <span class="cd-dot-sep">•</span>
                                <span>💼 Full-Time</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. SCHEDULE & INTERVIEW LINK -->
                <div class="cd-it-subcard">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">SCHEDULE &amp; LINK</span>
                    </div>
                    <div>
                        <div class="cd-it-dt-date">
                            <i class="far fa-calendar-alt me-1 text-primary"></i>
                            <span>{{ $sch['date_formatted'] }}</span>
                        </div>
                        <div class="cd-it-dt-time-mode">
                            @if(!empty($sch['time_formatted']))
                                <i class="far fa-clock me-1 text-primary"></i>
                                <span class="cd-it-dt-time-val">{{ $sch['time_formatted'] }}</span>
                                <span class="cd-dot-sep">|</span>
                            @endif
                            @if (($sch['mode_icon_type'] ?? '') === 'phone')
                                <i class="fas fa-phone-alt me-1"></i>
                            @elseif (($sch['mode_icon_type'] ?? '') === 'map-marker')
                                <i class="fas fa-building me-1"></i>
                            @else
                                <i class="fas fa-video me-1"></i>
                            @endif
                            <span class="cd-it-dt-mode-val">{{ $sch['mode_label'] ?? 'Video Call' }}</span>
                        </div>
                        @if (!empty($sch['meeting_link']))
                            @if (($sch['link_type'] ?? '') === 'address')
                                <span class="cd-it-link-pill" title="{{ $sch['meeting_link'] }}">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <span>{{ $sch['link_label'] ?: 'View Venue Address' }}</span>
                                </span>
                            @else
                                <a href="{{ $sch['meeting_link'] }}" target="_blank" rel="noopener noreferrer" class="cd-it-link-pill">
                                    <i class="fas fa-video me-1"></i>
                                    <span>{{ $sch['link_label'] ?: 'Join Interview Link' }}</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- 4. RECRUITER INFO -->
                <div class="cd-it-subcard">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">RECRUITER INFO</span>
                    </div>
                    <div>
                        <div class="cd-it-recruiter-name" title="{{ $src['name'] }}">
                            {{ $src['name'] ?: '—' }}
                        </div>
                        <div class="cd-it-recruiter-comp" title="{{ $src['company_name'] }}">
                            {{ $src['company_name'] ?: 'Internal Team' }}
                        </div>
                        <div class="cd-it-submitted-at">
                            <i class="far fa-clock me-1"></i>
                            <span>Submitted: {{ $src['submitted_at'] ?? 'Recently' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 5. DEDICATED PANELISTS CARD -->
                <div class="cd-it-subcard cd-it-panelist-card">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">PANELISTS</span>
                        <span class="cd-it-panel-count-pill">{{ $panelBadgeText }}</span>
                    </div>
                    <div class="cd-it-panel-body" title="{{ $panelistTooltipText }}">
                        <div class="cd-panel-avatar-stack">
                            <div class="cd-panel-avatar" title="{{ $leadName }}">
                                {{ $leadInitial }}
                            </div>
                            @if($extraPanelistsCount > 0)
                                <div class="cd-panel-avatar cd-panel-avatar-extra" title="+{{ $extraPanelistsCount }} more panelists">
                                    +{{ $extraPanelistsCount }}
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-grow-1 overflow-hidden">
                            <div class="cd-it-lead-name" title="{{ $leadName }}">
                                {{ $leadName }}
                            </div>
                            <div class="cd-it-lead-sub" title="{{ $leadCompany ?: 'Interview Panel' }}">
                                {{ $extraPanelistsCount > 0 ? "+{$extraPanelistsCount} more panelists" : ($leadCompany ?: 'Interview Panel') }}
                            </div>
                        </div>

                        <!-- Hover Popover -->
                        <div class="cd-it-popover-card">
                            <div class="cd-it-popover-header">
                                <span><i class="fas fa-users text-primary me-1"></i> Interview Panel</span>
                                <span class="badge badge-sm badge-light-primary text-primary">{{ $totalPanelists }}</span>
                            </div>
                            <div class="cd-it-popover-body">
                                @foreach($panelList as $pIdx => $p)
                                    <div class="cd-it-popover-item">
                                        <span class="text-truncate" style="max-width: 150px;" title="{{ $p['name'] }}">
                                            <i class="fas {{ $pIdx === 0 ? 'fa-user-check text-info' : 'fa-user text-muted' }} me-1"></i>
                                            {{ $p['name'] }}
                                        </span>
                                        @if($pIdx === 0)
                                            <span class="item-lead-badge">Lead</span>
                                        @elseif(!empty($p['company_name']))
                                            <span class="text-muted fs-9">{{ $p['company_name'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. STATUS & FEEDBACK -->
                <div class="cd-it-subcard">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">STATUS &amp; FEEDBACK</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <span class="cd-it-att-pill {{ $att['css_class'] ?? 'cit-fb-scheduled' }}">
                                {{ $att['label'] ?? 'Scheduled' }}
                            </span>
                            @if (!empty($fb['recommendation']))
                                <span class="cd-it-rec-pill" style="background: {{ $fb['recommendation']['bg'] }}; color: {{ $fb['recommendation']['color'] }};">
                                    {{ $fb['recommendation']['label'] }}
                                </span>
                            @elseif (!empty($fb['status_text']) && $fb['status_text'] !== 'Pending')
                                <span class="cd-it-rec-pill" style="background: #f1f5f9; color: {{ $fb['status_color'] ?? '#64748b' }};">
                                    {{ $fb['status_text'] }}
                                </span>
                            @endif
                        </div>
                        <div class="cd-it-rating">
                            @if (!empty($eval['star_rating']))
                                ★ {{ $eval['star_rating'] }} / 5.0 Rating
                            @else
                                <span class="text-muted fs-8 font-weight-normal">★ Rating Pending</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Activity Strip (Candidate Stage & Substage) -->
            <div class="cd-it-card-bottom">
                <div class="cd-it-bottom-left">
                    <div class="d-flex align-items-center gap-1">
                        <span class="cd-it-stage-lbl">CANDIDATE STAGE:</span>
                        <span class="cd-it-stage-badge {{ $stg['badge_class'] ?? 'cd-stage-in-review' }}">
                            {{ $stg['master_status_name'] ?: 'In Review' }}
                        </span>
                    </div>
                    <span class="cd-dot-sep">•</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="cd-it-stage-lbl">SUBSTAGE:</span>
                        <span class="cd-it-substage-badge">
                            {{ $stg['child_status_name'] ?: 'Private' }}
                        </span>
                    </div>
                    <span class="cd-dot-sep">•</span>
                    <span class="cd-it-source-subtext">
                        Sourced via <strong>{{ $src['company_name'] ?: 'Internal' }}</strong>
                    </span>
                </div>

                <div class="cd-it-bottom-right">
                    <button type="button" class="btn btn-sm btn-light-primary py-1 px-3 client-show-application-detail js-view-candidate" data-row-id="{{ $cand['application_id'] ?: $cand['id'] }}" data-id="{{ $cand['id'] }}">
                        Candidate Drawer <i class="fas fa-external-link-alt ms-1 fs-9"></i>
                    </button>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="cd-it-empty">
        <i class="fas fa-calendar-times fs-2 text-gray-300 d-block mb-2"></i>
        No interviews scheduled for this client.
    </div>
@endif
