@if(empty($items))
    <div class="cd-activity-empty-state">
        <div class="cd-activity-empty-icon">
            <i class="fas fa-clock-rotate-left"></i>
        </div>
        <div class="cd-activity-empty-title">No Activity Found</div>
        <div class="cd-activity-empty-text">There are no recorded audit events matching the selected filter criteria.</div>
    </div>
@else
    @php
        $currentGroupDate = null;
    @endphp

    @foreach($items as $item)
        @php
            $occurred = \Carbon\Carbon::parse($item['occurred_at'] ?? now());
            $itemDateStr = $occurred->isToday() ? 'Today' : ($occurred->isYesterday() ? 'Yesterday' : $occurred->format('M d, Y'));
            $timeFormatted = $occurred->format('h:i A');
            $timeAgo = $occurred->diffForHumans();
            $category = $item['category'] ?? 'pipeline';
            $eventType = $item['event_type'] ?? '';
            $candidate = $item['candidate'] ?? null;
            $job = $item['job'] ?? null;
            $actor = $item['actor'] ?? null;
            $transition = $item['transition'] ?? null;
            $details = $item['details'] ?? [];
            $seq = $item['client_activity_sequence'] ?? 0;
            $eventId = $item['event_id'] ?? '';

            // Candidate Name & Initials
            $candidateName = $candidate['name'] ?? 'Candidate';
            $words = preg_split('/\s+/', trim($candidateName));
            $cInitials = strtoupper(substr($words[0] ?? 'C', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            $candidateId = $candidate['wise_id'] ?? ($candidate['id'] ?? ($candidate['application_id'] ?? ''));

            // Job Details
            $jobStatus = $job['status'] ?? 'active';
            $jobTitle = $job['title'] ?? 'Requisition';
            $jobId = $job['id'] ?? '';
            $rawJobCode = $job['code'] ?? '';
            if (!empty($jobId)) {
                $jobCode = '#' . $jobId;
            } elseif (!empty($rawJobCode)) {
                $jobCode = str_starts_with($rawJobCode, '#') ? $rawJobCode : ('#' . preg_replace('/^jero-?/i', '', $rawJobCode));
            } else {
                $jobCode = '';
            }
            $jobOpenings = (int) ($job['total_positions'] ?? 1);

            // Attribution & Actor Company Resolution (NEVER recruiter role)
            $attribution = $item['attribution'] ?? [];
            $isSharedJob = ($eventType === 'job.shared') || !empty($job['shared_by_name']);
            $attributionLabel = $attribution['label'] ?? ($isSharedJob ? 'Shared by' : ($category === 'jobs' ? 'Created by' : ($category === 'crm' ? 'Logged by' : 'Uploaded by')));
            $attributionName = $attribution['name'] ?? ($actor['name'] ?? ($isSharedJob ? ($job['shared_by_name'] ?? 'Vibhanshu Jaiswal') : ($category === 'jobs' ? 'Account Manager' : 'Technical Recruiter')));
            if ($isSharedJob && (empty($attributionName) || strtolower($attributionName) === 'account lead')) {
                $attributionName = !empty($job['shared_by_name']) && strtolower($job['shared_by_name']) !== 'account lead'
                    ? $job['shared_by_name']
                    : (!empty($actor['name']) && strtolower($actor['name']) !== 'account lead' ? $actor['name'] : 'Vibhanshu Jaiswal');
            }
            $companyName = $attribution['company_name'] ?? ($actor['company_name'] ?? ($isSharedJob ? ($job['shared_by_company'] ?? '4Bell Technology') : ($category === 'jobs' ? ($job['creator_company'] ?? '4Bell Technology') : ($candidate['uploader_company'] ?? '4Bell Technology'))));
            if (empty($companyName) || strtolower($companyName) === 'account lead' || strtolower($companyName) === 'technical recruiter') {
                $companyName = '4Bell Technology';
            }

            // Standardize Stage & Status Colors:
            // 🟢 Active Requisition / Active / Offer / Joined -> Green
            // 🟠 Hold -> Orange
            // 🔴 Closed / Removed / Rejected -> Red
            // 🔵 Pipeline / Interview -> Blue / Violet
            $dotStatusClass = 'dot-status-pipeline';
            if ($category === 'jobs') {
                $dotStatusClass = 'dot-status-' . $jobStatus;
            } elseif ($category === 'interviews') {
                $dotStatusClass = 'dot-status-interview';
            } elseif ($category === 'offers') {
                $dotStatusClass = 'dot-status-active';
            } elseif ($category === 'crm') {
                $dotStatusClass = 'dot-status-crm';
            } else {
                $transStatus = $transition['status'] ?? 'pipeline';
                $dotStatusClass = 'dot-status-' . $transStatus;
            }

            // Activity Type Label
            $activityLabel = match($eventType) {
                'job.shared' => 'Job Shared',
                'job.created' => 'Job Requisition Created',
                'job.updated' => 'Job Requisition Updated',
                'candidate.joined' => 'Candidate Joined',
                'offer.sent' => 'Offer Extended',
                'interview.scheduled' => 'Interview Scheduled',
                'client.note_added' => 'Client Note Logged',
                default => ($category === 'jobs' ? ($isSharedJob ? 'Job Shared' : 'Job Requisition Created') : ($category === 'interviews' ? 'Interview Scheduled' : 'Candidate Stage Changed'))
            };
        @endphp

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

        <div class="cd-activity-card cd-activity-audit-card" 
             data-event-id="{{ $eventId }}" 
             data-sequence="{{ $seq }}" 
             data-category="{{ $category }}"
             data-event-type="{{ $eventType }}"
             data-search-terms="{{ strtolower($candidateName . ' ' . ($candidate['designation'] ?? '') . ' ' . $jobTitle . ' ' . $jobCode . ' ' . $attributionName . ' ' . $companyName . ' ' . ($transition['to_stage_name'] ?? '') . ' ' . ($job['shared_with_agency'] ?? '') . ' ' . ($job['shared_by_name'] ?? '')) }}">
            
            {{-- Connecting Node Dot --}}
            <div class="cd-activity-node-dot {{ $dotStatusClass }} stream-{{ $category }}"></div>

            {{-- Audit Timeline Row: Activity -> Entity -> What Changed -> Who -> Company -> Time --}}
            <div class="cd-activity-card-body">
                <div class="cd-activity-subcard-grid">

                    {{-- 1. Activity & Entity Sub-Card --}}
                    <div class="cd-activity-subcard-entity">
                        @if($category === 'jobs')
                            @php
                                $jAvatarStyle = $isSharedJob
                                    ? 'background: #eff6ff; color: #009ef7; border-color: #bfdbfe;'
                                    : match($jobStatus) {
                                        'active' => 'background: #ecfdf5; color: #15803d; border-color: #a7f3d0;',
                                        'hold' => 'background: #fff7ed; color: #c2410c; border-color: #fed7aa;',
                                        'closed', 'removed' => 'background: #fef2f2; color: #b91c1c; border-color: #fecaca;',
                                        default => 'background: #ecfdf5; color: #15803d; border-color: #a7f3d0;'
                                    };
                                $sharedWithText = $job['shared_with_agency'] ?? ($job['agency_name'] ?? '');
                            @endphp
                            <div class="cd-activity-entity-avatar" style="{{ $jAvatarStyle }}">
                                <i class="fas {{ $isSharedJob ? 'fa-share-nodes' : 'fa-briefcase' }}"></i>
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-pill-label">
                                    <span class="cd-activity-stream-pill stream-jobs-pill">{{ $isSharedJob ? 'JOB SHARED' : 'JOB REQUISITION' }}</span>
                                </div>
                                <div class="cd-activity-entity-name" title="{{ $jobTitle }}">
                                    @if($isSharedJob && !empty($sharedWithText))
                                        {{ $jobTitle }} &rarr; Shared with {{ $sharedWithText }}
                                    @else
                                        {{ $jobTitle }}
                                    @endif
                                </div>
                                <div class="cd-activity-entity-sub">
                                    <span class="fw-semibold text-gray-800">{{ $jobCode }}</span> &bull; {{ $jobOpenings }} {{ \Illuminate\Support\Str::plural('Opening', $jobOpenings) }}
                                </div>
                            </div>
                        @elseif($category === 'crm')
                            <div class="cd-activity-entity-avatar" style="background: #f0fdf4; color: #15803d; border-color: #bbf7d0;">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-pill-label">
                                    <span class="cd-activity-stream-pill stream-crm-pill">CRM NOTE</span>
                                </div>
                                <div class="cd-activity-entity-name">Client Interaction</div>
                                <div class="cd-activity-entity-sub">Logged Note</div>
                            </div>
                        @elseif($category === 'interviews')
                            <div class="cd-activity-entity-avatar" style="background: #f5f3ff; color: #7c3aed; border-color: #ddd6fe;">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-pill-label">
                                    <span class="cd-activity-stream-pill stream-interviews-pill">INTERVIEW</span>
                                </div>
                                <div class="cd-activity-entity-name" title="{{ $candidateName }}">
                                    {{ $candidateName }}
                                </div>
                                <div class="cd-activity-entity-sub" title="{{ $candidate['designation'] ?? $jobTitle }}">
                                    {{ $candidate['designation'] ?? $jobTitle }}
                                    @if(!empty($jobCode))
                                        &bull; {{ $jobCode }}
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="cd-activity-entity-avatar" style="background: #eff6ff; color: #009ef7; border-color: #bfdbfe;">
                                {{ $cInitials }}
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-pill-label">
                                    <span class="cd-activity-stream-pill stream-pipeline-pill">CANDIDATE</span>
                                </div>
                                <div class="cd-activity-entity-name" title="{{ $candidateName }}">
                                    {{ $candidateName }}
                                </div>
                                <div class="cd-activity-entity-sub" title="{{ $candidate['designation'] ?? $jobTitle }}">
                                    <span class="fw-semibold text-gray-800">{{ $candidate['designation'] ?? $jobTitle }}</span>
                                    @if(!empty($candidateId))
                                        &bull; #{{ $candidateId }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 2. What Changed Sub-Card (Transition & Standard Status Badges) --}}
                    <div class="cd-activity-subcard-detail">
                        @if($category === 'pipeline' || $category === 'offers')
                            @php
                                $transStatus = $transition['status'] ?? ($category === 'offers' ? 'active' : 'pipeline');
                                $transBadgeClass = match($transStatus) {
                                    'active' => 'status-active',
                                    'hold' => 'status-hold',
                                    'removed' => 'status-removed',
                                    'closed' => 'status-closed',
                                    default => 'status-pipeline'
                                };
                                $transIcon = match($transStatus) {
                                    'active' => 'fa-check-double',
                                    'hold' => 'fa-pause-circle',
                                    'removed', 'closed' => 'fa-times-circle',
                                    default => 'fa-check-circle'
                                };
                                $fromStage = $transition['from_stage_name'] ?? '';
                                $toStage = $transition['to_stage_name'] ?? 'Screening';
                            @endphp
                            <div class="cd-activity-stage-transition">
                                @if(!empty($fromStage) && strtolower($fromStage) !== strtolower($toStage))
                                    <span class="cd-activity-stage-badge cd-activity-stage-from">
                                        {{ $fromStage }}
                                    </span>
                                    <i class="fas fa-arrow-right cd-activity-stage-arrow"></i>
                                @endif
                                <span class="cd-status-badge {{ $transBadgeClass }}">
                                    <i class="fas {{ $transIcon }}" style="font-size: 10px;"></i>
                                    {{ $toStage }}
                                </span>

                                @if(!empty($details['compensation']['amount']) && empty($details['compensation']['is_confidential']))
                                    <span class="cd-status-badge status-active">
                                        {{ $details['compensation']['currency'] ?? 'INR' }} {{ number_format($details['compensation']['amount']) }}
                                    </span>
                                @endif
                            </div>
                        @elseif($category === 'interviews')
                            <div class="cd-activity-stage-transition">
                                <span class="cd-status-badge status-interview">
                                    <i class="fas fa-video" style="font-size: 10px;"></i>
                                    {{ $details['interview_mode'] ?? 'Video Call' }}
                                </span>
                                @if(!empty($details['scheduled_time']))
                                    <span class="cd-activity-stage-badge cd-activity-stage-from" style="font-size: 10.5px;">
                                        <i class="far fa-clock me-1" style="font-size: 10px;"></i>
                                        {{ \Carbon\Carbon::parse($details['scheduled_time'])->format('M d, h:i A') }}
                                    </span>
                                @endif
                            </div>
                        @elseif($category === 'jobs')
                            @php
                                $statusBadgeClass = $isSharedJob
                                    ? 'status-active'
                                    : match($jobStatus) {
                                        'active' => 'status-active',
                                        'hold' => 'status-hold',
                                        'closed' => 'status-closed',
                                        'removed' => 'status-removed',
                                        default => 'status-active'
                                    };
                                $statusIcon = $isSharedJob
                                    ? 'fa-share-nodes'
                                    : match($jobStatus) {
                                        'active' => 'fa-circle-check',
                                        'hold' => 'fa-pause-circle',
                                        'closed' => 'fa-lock',
                                        'removed' => 'fa-trash-alt',
                                        default => 'fa-circle-check'
                                    };
                                $statusLabel = $isSharedJob
                                    ? ($transition['to_stage_name'] ?? ('Shared with ' . ($job['shared_with_agency'] ?? 'Agency')))
                                    : (($job['status_label'] ?? ucfirst($jobStatus)) . ' Requisition');
                            @endphp
                            <div class="cd-activity-stage-transition">
                                <span class="cd-status-badge {{ $statusBadgeClass }}">
                                    <i class="fas {{ $statusIcon }}" style="font-size: 10px;"></i>
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        @elseif($category === 'crm')
                            <div class="cd-activity-detail-note" title="{{ $details['note_excerpt'] ?? '' }}">
                                "{{ $details['note_excerpt'] ?? 'Note recorded.' }}"
                            </div>
                        @endif
                    </div>

                    {{-- 3. Who & Company Sub-Card (Actual Owner & Company - NEVER Recruiter Role) --}}
                    <div class="cd-activity-subcard-actor">
                        <div class="cd-activity-actor-meta">
                            <div class="cd-activity-actor-attribution">
                                {{ $attributionLabel }}:
                            </div>
                            @if($isSharedJob || str_contains(strtolower($attributionLabel), 'shared') || $eventType === 'job.shared')
                                <div class="cd-activity-actor-name fw-bold text-dark" title="{{ $companyName }} — {{ $attributionName }}">
                                    {{ $companyName }} — {{ $attributionName }}
                                </div>
                            @else
                                <div class="cd-activity-actor-name" title="{{ $attributionName }}">
                                    {{ $attributionName }}
                                </div>
                                <div class="cd-activity-actor-company" title="{{ $companyName }}">
                                    <i class="far fa-building me-1" style="font-size: 10px; color: #64748b;"></i>{{ $companyName }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 4. Time & Action Sub-Card (Strictly Read-Only, ZERO Job Drawer Trigger) --}}
                    <div class="cd-activity-subcard-actions">
                        <div class="cd-activity-time-wrap">
                            <div class="cd-activity-time-ago" title="{{ $occurred->toIso8601String() }}">
                                {{ $timeAgo }}
                            </div>
                            <div class="cd-activity-time-clock">
                                {{ $timeFormatted }}
                            </div>
                        </div>

                        {{-- Action Button: Strictly Read-Only View Profile for Candidate and Job --}}
                        @if($category === 'jobs' && !empty($job['id']))
                            <button type="button" 
                                    class="cd-activity-btn-action" 
                                    onclick="if(window.openJobDrawer) { window.openJobDrawer('{{ $job['id'] }}', { readOnly: true }); }">
                                <i class="far fa-eye me-1"></i> View Profile
                            </button>
                        @elseif(!empty($candidate['id']) || !empty($candidate['wise_id']))
                            @php
                                $targetCandDrawerId = $candidate['wise_id'] ?? $candidate['id'];
                            @endphp
                            <button type="button" 
                                    class="cd-activity-btn-action" 
                                    onclick="if(window.openCandidateDrawer) { window.openCandidateDrawer('{{ $targetCandDrawerId }}', { readOnly: true }); }">
                                <i class="far fa-user me-1"></i> View Profile
                            </button>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Footer Activity Strip --}}
            <div class="cd-activity-footer-strip">
                <div class="cd-activity-footer-left">
                    <span class="cd-activity-stream-pill stream-{{ $category }}-pill">
                        {{ ($category === 'jobs' && $isSharedJob) ? 'JOB SHARED' : strtoupper($category) }}
                    </span>
                    <span>
                        <i class="far fa-clock me-1"></i> {{ $activityLabel }} &bull; {{ $timeFormatted }}
                    </span>
                </div>
                <div class="cd-activity-footer-right">
                    <span class="cd-activity-seq-tag" title="Monotonic Audit Sequence">
                        #seq-{{ $seq }}
                    </span>
                </div>
            </div>

        </div>
    @endforeach
@endif
