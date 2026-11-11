@if(empty($items))
    <div class="cd-activity-empty-state">
        <div class="cd-activity-empty-icon">
            <i class="fas fa-clock-rotate-left"></i>
        </div>
        <div class="cd-activity-empty-title">No Company Activity Recorded</div>
        <div class="cd-activity-empty-text">Activities performed by your team across job requisitions, candidate pipeline, interviews, offers, and team invitations will appear here dynamically in real time.</div>
    </div>
@else
    @php
        $currentGroupDate = null;
    @endphp

    @foreach($items as $act)
        @php
            $itemDateStr = $act['formatted_date'] ?? date('M d, Y', $act['timestamp'] ?? time());
            $timeFormatted = $act['time_formatted'] ?? date('h:i A T', $act['timestamp'] ?? time());
            $category = $act['category'] ?? 'pipeline';
            $job = $act['job'] ?? null;
            $candidate = $act['candidate'] ?? null;
            $actor = $act['actor'] ?? null;
            $attribution = $act['attribution'] ?? [];
            $transition = $act['transition'] ?? null;
            $seq = $act['sequence'] ?? ($act['timestamp'] ?? time());

            // Concentric node dot color class
            $dotStatusClass = match($category) {
                'jobs' => 'dot-status-active',
                'interviews' => 'dot-status-interview',
                'offers' => 'dot-status-active',
                'team' => 'dot-status-crm',
                default => 'dot-status-pipeline'
            };
        @endphp

        {{-- Date Group Divider --}}
        @if($currentGroupDate !== $itemDateStr)
            @php $currentGroupDate = $itemDateStr; @endphp
            <div class="cd-activity-day-group">
                <div class="cd-activity-day-header">
                    <span class="cd-activity-date-badge">
                        <i class="far fa-calendar-alt me-1"></i> {{ $itemDateStr }}
                    </span>
                </div>
            </div>
        @endif

        {{-- Activity Card --}}
        <div class="cd-activity-card cd-activity-audit-card" 
             data-event-id="{{ $act['id'] }}" 
             data-sequence="{{ $seq }}" 
             data-category="{{ $category }}">

            {{-- Concentric Ring Timeline Node Dot --}}
            <div class="cd-activity-node-dot {{ $dotStatusClass }} stream-{{ $category }}"></div>

            <div class="cd-activity-card-body">
                <div class="cd-activity-subcard-grid">

                    {{-- 1. Entity Sub-Card (Avatar/Initial Box, Pill, Title, Company, Details) --}}
                    <div class="cd-activity-subcard-entity">
                        @if($category === 'jobs')
                            <div class="cd-activity-entity-avatar" style="background: #ecfdf5; color: #15803d; border-color: #a7f3d0;">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-pill-label">
                                    <span class="cd-activity-stream-pill stream-jobs-pill">JOB REQUISITION</span>
                                </div>
                                <div class="cd-activity-entity-name" title="{{ $job['title'] ?? 'Requisition' }}">
                                    {{ $job['title'] ?? 'Requisition' }}
                                </div>
                                <div class="cd-activity-company-context" title="{{ $job['company_name'] ?? 'Company' }}">
                                    @if(!empty($job['company_logo']))
                                        <img src="{{ $job['company_logo'] }}" class="cd-activity-company-logo" alt="{{ $job['company_name'] ?? '' }}" onerror="this.style.display='none';">
                                    @else
                                        <i class="far fa-building me-1 text-muted" style="font-size: 11px;"></i>
                                    @endif
                                    <span class="cd-activity-company-name">{{ $job['company_name'] ?? 'Company' }}</span>
                                </div>
                                <div class="cd-activity-entity-sub">
                                    <span class="fw-semibold text-gray-800">{{ $job['code'] ?? '' }}</span>
                                    @if(!empty($job['location'])) &bull; {{ $job['location'] }} @endif
                                    @if(!empty($job['job_type'])) &bull; {{ $job['job_type'] }} @endif
                                    @if(!empty($job['total_positions']))
                                        &bull; {{ $job['total_positions'] }} {{ \Illuminate\Support\Str::plural('Opening', (int)$job['total_positions']) }}
                                    @endif
                                </div>
                            </div>
                        @elseif($category === 'team')
                            <div class="cd-activity-entity-avatar" style="background: #ecfeff; color: #0891b2; border-color: #a5f3fc;">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-pill-label">
                                    <span class="cd-activity-stream-pill stream-crm-pill">TEAM INVITATION</span>
                                </div>
                                <div class="cd-activity-entity-name" title="{{ $candidate['name'] ?? 'Team Member' }}">
                                    {{ $candidate['name'] ?? 'Team Member' }}
                                </div>
                                <div class="cd-activity-company-context">
                                    <i class="far fa-building me-1 text-muted" style="font-size: 11px;"></i>
                                    <span class="cd-activity-company-name">{{ $actor['company_name'] ?? 'Company' }}</span>
                                </div>
                                <div class="cd-activity-entity-sub">
                                    <span>Colleague Onboarding &bull; Internal Team</span>
                                </div>
                            </div>
                        @elseif($category === 'interviews')
                            <div class="cd-activity-entity-avatar" style="background: #f5f3ff; color: #7c3aed; border-color: #ddd6fe;">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-pill-label">
                                    <span class="cd-activity-stream-pill stream-interviews-pill">INTERVIEW</span>
                                </div>
                                <div class="cd-activity-entity-name" title="{{ $candidate['name'] ?? 'Candidate' }}">
                                    {{ $candidate['name'] ?? 'Candidate' }}
                                </div>
                                <div class="cd-activity-company-context">
                                    <i class="far fa-building me-1 text-muted" style="font-size: 11px;"></i>
                                    <span class="cd-activity-company-name">{{ $job['company_name'] ?? 'Company' }}</span>
                                </div>
                                <div class="cd-activity-entity-sub" title="{{ $candidate['designation'] ?? '' }}">
                                    <span class="fw-semibold text-gray-800">{{ $job['title'] ?? ($candidate['designation'] ?? 'Candidate') }}</span>
                                    @if(!empty($job['code'])) &bull; {{ $job['code'] }} @endif
                                </div>
                            </div>
                        @else
                            {{-- Pipeline / Offers Candidate --}}
                            <div class="cd-activity-entity-avatar" style="background: #eff6ff; color: #009ef7; border-color: #bfdbfe;">
                                {{ $candidate['initials'] ?? 'CD' }}
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-pill-label">
                                    <span class="cd-activity-stream-pill stream-pipeline-pill">CANDIDATE</span>
                                </div>
                                <div class="cd-activity-entity-name" title="{{ $candidate['name'] ?? 'Candidate' }}">
                                    {{ $candidate['name'] ?? 'Candidate' }}
                                    @if(!empty($candidate['wise_id']) || !empty($candidate['id']))
                                        <span class="text-muted fw-normal fs-8 ms-1">#{{ $candidate['wise_id'] ?? $candidate['id'] }}</span>
                                    @endif
                                </div>
                                <div class="cd-activity-company-context">
                                    @if(!empty($job['company_logo']))
                                        <img src="{{ $job['company_logo'] }}" class="cd-activity-company-logo" alt="{{ $job['company_name'] ?? '' }}" onerror="this.style.display='none';">
                                    @else
                                        <i class="far fa-building me-1 text-muted" style="font-size: 11px;"></i>
                                    @endif
                                    <span class="cd-activity-company-name">{{ $job['company_name'] ?? 'Company' }}</span>
                                </div>
                                <div class="cd-activity-entity-sub" title="{{ $candidate['designation'] ?? ($job['title'] ?? '') }}">
                                    <span class="fw-semibold text-gray-800">{{ $candidate['designation'] ?? ($job['title'] ?? 'Candidate') }}</span>
                                    @if(!empty($job['code'])) &bull; {{ $job['code'] }} @endif
                                    @if(!empty($job['location'])) &bull; {{ $job['location'] }} @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 2. Status Badge Sub-Card --}}
                    <div class="cd-activity-subcard-detail">
                        @if($category === 'jobs')
                            <div class="cd-activity-stage-transition">
                                <span class="cd-status-badge {{ $act['status_class'] ?? 'status-active' }}">
                                    <i class="fas fa-circle-check" style="font-size: 10px;"></i>
                                    {{ $act['status_text'] ?? 'Active Requisition' }}
                                </span>
                            </div>
                        @elseif($category === 'team')
                            <div class="cd-activity-stage-transition">
                                <span class="cd-status-badge status-active">
                                    <i class="fas fa-user-plus" style="font-size: 10px;"></i>
                                    {{ $act['status_text'] ?? 'Team Member Invited' }}
                                </span>
                            </div>
                        @elseif($category === 'interviews')
                            <div class="cd-activity-stage-transition">
                                <span class="cd-status-badge status-interview">
                                    <i class="fas fa-video" style="font-size: 10px;"></i>
                                    {{ $act['status_text'] ?? 'Interview Scheduled' }}
                                </span>
                            </div>
                        @else
                            {{-- Pipeline / Offers --}}
                            <div class="cd-activity-stage-transition">
                                @if(!empty($transition['from_stage_name']) && strtolower($transition['from_stage_name']) !== strtolower($transition['to_stage_name']))
                                    <span class="cd-activity-stage-badge cd-activity-stage-from">
                                        {{ $transition['from_stage_name'] }}
                                    </span>
                                    <i class="fas fa-arrow-right cd-activity-stage-arrow"></i>
                                @endif
                                <span class="cd-status-badge {{ $act['status_class'] ?? 'status-active' }}">
                                    <i class="fas fa-user-check" style="font-size: 10px;"></i>
                                    {{ $act['status_text'] ?? ($transition['to_stage_name'] ?? 'Pipeline') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- 3. Actor & Attribution Sub-Card --}}
                    <div class="cd-activity-subcard-actor">
                        <div class="cd-activity-actor-meta">
                            <div class="cd-activity-actor-attribution">
                                {{ strtoupper($attribution['label'] ?? 'CREATED BY') }}:
                            </div>
                            <div class="cd-activity-actor-name" title="{{ $attribution['name'] ?? ($actor['name'] ?? 'Team Member') }}">
                                {{ $attribution['name'] ?? ($actor['name'] ?? 'Team Member') }}
                            </div>
                            <div class="cd-activity-actor-company" title="{{ $attribution['company_name'] ?? ($actor['company_name'] ?? '') }}">
                                <i class="far fa-building me-1" style="font-size: 10px; color: #64748b;"></i>
                                {{ $attribution['company_name'] ?? ($actor['company_name'] ?? 'Company') }}
                            </div>
                        </div>
                    </div>

                    {{-- 4. Date, Time & View Profile Action Sub-Card --}}
                    <div class="cd-activity-subcard-actions">
                        <div class="cd-activity-time-wrap">
                            <div class="cd-activity-time-ago" title="{{ $act['time_ago'] ?? '' }}">
                                {{ $itemDateStr }}
                            </div>
                            <div class="cd-activity-time-clock">
                                {{ $timeFormatted }}
                            </div>
                        </div>

                        {{-- Action Opener Buttons --}}
                        @if($category === 'jobs' && !empty($job['id']))
                            <button type="button" 
                                    class="cd-activity-btn-action show-detail-open" 
                                    data-id="{{ $job['id'] }}"
                                    title="View Requisition in Drawer"
                                    onclick="if(window.openJobDrawer){ window.openJobDrawer('{{ $job['id'] }}', { readOnly: true }); }">
                                <i class="far fa-eye me-1"></i> View Profile
                            </button>
                        @elseif(!empty($candidate['id']) || !empty($candidate['wise_id']))
                            @php
                                $targetCandId = $candidate['wise_id'] ?? $candidate['id'];
                            @endphp
                            <button type="button" 
                                    class="cd-activity-btn-action show-application-detail" 
                                    data-row-id="{{ $targetCandId }}"
                                    data-id="{{ $targetCandId }}"
                                    title="View Candidate in Drawer"
                                    onclick="if(window.openCandidateDrawer){ window.openCandidateDrawer('{{ $targetCandId }}', { readOnly: true }); }">
                                <i class="far fa-user me-1"></i> View Profile
                            </button>
                        @elseif($category === 'team')
                            <a href="{{ url('service_company/team') }}" class="cd-activity-btn-action" title="Manage Team Members">
                                <i class="fas fa-users me-1"></i> Manage Team
                            </a>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Footer Activity Strip --}}
            <div class="cd-activity-footer-strip">
                <div class="cd-activity-footer-left">
                    <span class="cd-activity-stream-pill stream-{{ $category }}-pill">
                        {{ strtoupper($category === 'jobs' ? 'JOB REQUISITION' : ($category === 'pipeline' ? 'PIPELINE' : $category)) }}
                    </span>
                    <span>
                        <i class="far fa-clock me-1"></i> {{ $act['activity_label'] ?? 'Activity Recorded' }} &bull; {{ $timeFormatted }}
                    </span>
                </div>
                <div class="cd-activity-footer-right">
                    <span class="cd-activity-seq-tag" title="Audit Sequence">
                        #seq-{{ $seq }}
                    </span>
                </div>
            </div>

        </div>
    @endforeach
@endif
