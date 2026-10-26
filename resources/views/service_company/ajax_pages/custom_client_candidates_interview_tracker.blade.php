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
    @endphp
        <!-- Primary Job Card for Interview Record -->
        <div class="it-primary-card" data-interview-id="{{ $readModel['id'] }}">
            <!-- 1. Candidate Sub-Card (2.2fr - Proportional Content Width) -->
            <div class="it-subcard it-subcard-cand">
                <div class="it-cand-avatar {{ $cand['avatar_color_class'] ?? 'it-avatar-blue' }}">
                    {{ $cand['initial'] ?? 'C' }}
                </div>
                <div class="it-cand-info">
                    <div class="it-cand-name" title="{{ $cand['name'] }}">
                        {{ $cand['name'] }}
                    </div>
                    <div class="it-cand-role" title="{{ $cand['designation'] }}">
                        {{ $cand['designation'] ?: '—' }}
                    </div>
                    @if (!empty($fu['label']))
                        <div class="it-cand-meta">
                            <span class="it-meta-tag {{ $fu['style_class'] }}">
                                {{ $fu['label'] }}
                            </span>
                            @if (!empty($fu['formatted_at']))
                                <span class="it-meta-date">{{ $fu['formatted_at'] }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. Job Requisition Sub-Card (2.3fr - No Artificial Truncation) -->
            <div class="it-subcard it-subcard-job">
                <div class="it-job-title" title="{{ $job['title'] }}">
                    {{ $job['title'] }}
                </div>
                <div class="it-job-company" title="{{ $job['display_company_name'] }}">
                    {{ $job['display_company_name'] ?: 'Client Account' }}
                </div>
                <div class="it-job-tags">
                    <span class="it-job-type {{ $job['job_type_class'] }}">{{ $job['job_type_label'] }}</span>
                    @if (!empty($job['job_id_display']))
                        <span class="it-dot-sep"></span>
                        <span class="it-job-id">{{ $job['job_id_display'] }}</span>
                    @endif
                </div>
            </div>

            <!-- 3. Interview Schedule Sub-Card (1.75fr) -->
            <div class="it-subcard it-subcard-datetime">
                <div class="it-dt-row it-dt-date">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>{{ $sch['date_formatted'] }}</span>
                </div>
                @if (!empty($sch['time_formatted']))
                    <div class="it-dt-row it-dt-time">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>{{ $sch['time_formatted'] }}</span>
                    </div>
                @endif
                <div class="it-dt-row it-dt-mode">
                    @if ($sch['mode_icon_type'] === 'phone')
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    @elseif ($sch['mode_icon_type'] === 'map-marker')
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    @else
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                    @endif
                    <span>{{ $sch['mode_label'] }}</span>
                </div>
                @if (!empty($sch['meeting_link']))
                    @if ($sch['link_type'] === 'address')
                        <span class="it-dt-link" title="{{ $sch['meeting_link'] }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>{{ $sch['link_label'] ?: 'View Address' }}</span>
                        </span>
                    @elseif ($sch['link_type'] === 'link')
                        <a href="{{ $sch['meeting_link'] }}" target="_blank" rel="noopener noreferrer" class="it-dt-link">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            <span>{{ $sch['link_label'] ?: 'Interview Link' }}</span>
                        </a>
                    @endif
                @endif
            </div>

            <!-- 4. Interviewers Sub-Card (1.1fr - Compact) -->
            <div class="it-subcard it-subcard-interviewers">
                <div class="it-lead-name">{{ $intvs['lead_name'] }}</div>
                <div class="it-lead-comp">{{ $intvs['lead_company'] }}</div>
                @if ($intvs['extra_count'] > 0)
                    <div class="it-extra-wrap">
                        <span class="it-extra-pill">{{ $intvs['extra_label'] }}</span>
                        <span class="it-extra-lbl">{{ $intvs['extra_count'] + 1 }} Interviewers</span>
                    </div>
                @endif
            </div>

            <!-- 5. Source Sub-Card (1.1fr - Compact) -->
            <div class="it-subcard it-subcard-source">
                <div class="it-src-name" title="{{ $src['name'] }}">
                    {{ $src['name'] }}
                </div>
                <div class="it-src-comp" title="{{ $src['company_name'] }}">
                    {{ $src['company_name'] }}
                </div>
            </div>

            <!-- 6. Stage Sub-Card (0.75fr - Compact) -->
            <div class="it-subcard it-subcard-stage">
                <span class="it-badge {{ $stg['badge_class'] ?? 'it-badge-blue' }}">
                    {{ $stg['master_status_name'] }}
                </span>
                @if (!empty($stg['child_status_name']))
                    <span class="it-stage-sub">
                        {{ $stg['child_status_name'] }}
                    </span>
                @endif
            </div>

            <!-- 7. Feedback Sub-Card (0.8fr - Pure View Only) -->
            <div class="it-subcard it-subcard-feedback">
                <span class="it-fb-status" style="color: {{ $fb['status_color'] }};">
                    @if ($fb['status_icon'] === 'check')
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @elseif ($fb['status_icon'] === 'times')
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    @elseif ($fb['status_icon'] === 'exclamation-triangle')
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    @elseif ($fb['status_icon'] === 'exclamation-circle')
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    @else
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    @endif
                    <span>{{ $fb['status_text'] }}</span>
                </span>
                @if (!empty($fb['recommendation']))
                    <span class="it-rec-pill" style="background: {{ $fb['recommendation']['bg'] }}; color: {{ $fb['recommendation']['color'] }};">
                        {{ $fb['recommendation']['label'] }}
                    </span>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="it-empty-card">
        <i class="fas fa-calendar-times fs-2 text-gray-300 d-block mb-2"></i>
        No interviews scheduled for this client.
    </div>
@endif
