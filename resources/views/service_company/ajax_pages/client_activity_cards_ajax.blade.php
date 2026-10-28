@if(empty($items))
    <div class="cd-activity-empty-state">
        <div class="cd-activity-empty-icon">
            <i class="fa fa-history"></i>
        </div>
        <div class="cd-activity-empty-title">No Activity Found</div>
        <div class="cd-activity-empty-text">There are no recorded pipeline events, interviews, or updates for this client under the selected filter criteria.</div>
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

            // Initials helper
            $candidateName = $candidate['name'] ?? 'Candidate';
            $words = preg_split('/\s+/', trim($candidateName));
            $cInitials = strtoupper(substr($words[0] ?? 'C', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            
            $actorName = $actor['name'] ?? 'Technical Recruiter';
            $aWords = preg_split('/\s+/', trim($actorName));
            $aInitials = strtoupper(substr($aWords[0] ?? 'R', 0, 1) . (isset($aWords[1]) ? substr($aWords[1], 0, 1) : ''));

            // Status tokens calculation for standardized badges and markers
            $jobStatus = $job['status'] ?? 'active';
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
        @endphp

        @if($currentGroupDate !== $itemDateStr)
            @php $currentGroupDate = $itemDateStr; @endphp
            <div class="cd-activity-day-group">
                <div class="cd-activity-day-header">
                    <span class="cd-activity-date-badge">
                        <i class="far fa-calendar-alt"></i> {{ $itemDateStr }}
                    </span>
                </div>
            </div>
        @endif

        <div class="cd-activity-card" 
             data-event-id="{{ $eventId }}" 
             data-sequence="{{ $seq }}" 
             data-category="{{ $category }}"
             data-event-type="{{ $eventType }}">
            
            {{-- Connecting Node Dot --}}
            <div class="cd-activity-node-dot {{ $dotStatusClass }} stream-{{ $category }}"></div>

            {{-- Card Body: 4 Discrete Sub-Cards --}}
            <div class="cd-activity-card-body">
                <div class="cd-activity-subcard-grid">

                    {{-- Sub-Card 1: Entity & Subject --}}
                    <div class="cd-activity-subcard-entity">
                        @if($category === 'jobs')
                            @php
                                $jAvatarStyle = match($jobStatus) {
                                    'active' => 'background: #ecfdf5; color: #15803d; border-color: #a7f3d0;',
                                    'hold' => 'background: #fff7ed; color: #c2410c; border-color: #fed7aa;',
                                    'closed', 'removed' => 'background: #fef2f2; color: #b91c1c; border-color: #fecaca;',
                                    default => 'background: #ecfdf5; color: #15803d; border-color: #a7f3d0;'
                                };
                            @endphp
                            <div class="cd-activity-entity-avatar" style="{{ $jAvatarStyle }}">
                                <i class="fa fa-briefcase"></i>
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-entity-name" title="{{ $job['title'] ?? 'Requisition' }}">
                                    {{ $job['title'] ?? 'Requisition' }}
                                </div>
                                <div class="cd-activity-entity-sub">
                                    {{ $job['code'] ?? 'JOB-REQ' }} &bull; {{ $job['total_positions'] ?? 1 }} {{ \Illuminate\Support\Str::plural('Opening', $job['total_positions'] ?? 1) }}
                                </div>
                            </div>
                        @elseif($category === 'crm')
                            <div class="cd-activity-entity-avatar" style="background: #f0fdf4; color: #15803d; border-color: #bbf7d0;">
                                <i class="fa fa-sticky-note"></i>
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-entity-name">
                                    Client Interaction Note
                                </div>
                                <div class="cd-activity-entity-sub">
                                    Logged by {{ $actorName }}
                                </div>
                            </div>
                        @else
                            <div class="cd-activity-entity-avatar" style="background: #eff6ff; color: #009ef7; border-color: #bfdbfe;">
                                {{ $cInitials }}
                            </div>
                            <div class="cd-activity-entity-meta">
                                <div class="cd-activity-entity-name" title="{{ $candidateName }}">
                                    {{ $candidateName }}
                                </div>
                                <div class="cd-activity-entity-sub" title="{{ $candidate['designation'] ?? ($job['title'] ?? '') }}">
                                    {{ $candidate['designation'] ?? ($job['title'] ?? 'Candidate') }}
                                    @if(!empty($job['code']))
                                        &bull; {{ $job['code'] }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Sub-Card 2: Context / Transition / Details --}}
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
                            @endphp
                            <div class="cd-activity-stage-transition">
                                @if(!empty($transition['from_stage_name']))
                                    <span class="cd-activity-stage-badge cd-activity-stage-from">
                                        {{ $transition['from_stage_name'] }}
                                    </span>
                                    <i class="fa fa-arrow-right cd-activity-stage-arrow"></i>
                                @endif
                                <span class="cd-status-badge {{ $transBadgeClass }}">
                                    <i class="fa {{ $transIcon }}" style="font-size: 10px;"></i>
                                    {{ $transition['to_stage_name'] ?? 'Updated' }}
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
                                    <i class="fa fa-video" style="font-size: 10px;"></i>
                                    {{ $details['interview_mode'] ?? 'Video Call' }}
                                </span>
                                @if(!empty($details['scheduled_time']))
                                    <span class="cd-activity-stage-badge cd-activity-stage-from" style="font-size: 10.5px;">
                                        <i class="far fa-clock" style="font-size: 10px;"></i>
                                        {{ \Carbon\Carbon::parse($details['scheduled_time'])->format('M d, h:i A') }}
                                    </span>
                                @endif
                            </div>
                        @elseif($category === 'jobs')
                            @php
                                $statusBadgeClass = match($jobStatus) {
                                    'active' => 'status-active',
                                    'hold' => 'status-hold',
                                    'closed' => 'status-closed',
                                    'removed' => 'status-removed',
                                    default => 'status-active'
                                };
                                $statusIcon = match($jobStatus) {
                                    'active' => 'fa-circle-check',
                                    'hold' => 'fa-pause-circle',
                                    'closed' => 'fa-lock',
                                    'removed' => 'fa-trash-alt',
                                    default => 'fa-circle-check'
                                };
                                $statusLabel = $job['status_label'] ?? ucfirst($jobStatus);
                            @endphp
                            <div class="cd-activity-stage-transition">
                                <span class="cd-status-badge {{ $statusBadgeClass }}">
                                    <i class="fa {{ $statusIcon }}" style="font-size: 10px;"></i>
                                    {{ $statusLabel }} Requisition
                                </span>
                            </div>
                        @elseif($category === 'crm')
                            <div class="cd-activity-detail-note" title="{{ $details['note_excerpt'] ?? '' }}">
                                "{{ $details['note_excerpt'] ?? 'Note recorded.' }}"
                            </div>
                        @endif
                    </div>

                    {{-- Sub-Card 3: Actor & Authority --}}
                    <div class="cd-activity-subcard-actor">
                        <div class="cd-activity-actor-avatar" title="{{ $actorName }}">
                            {{ $aInitials }}
                        </div>
                        <div class="cd-activity-actor-meta">
                            <div class="cd-activity-actor-name" title="{{ $actorName }}">
                                {{ $actorName }}
                            </div>
                            <div class="cd-activity-actor-role">
                                {{ $actor['role'] ?? 'Recruiter' }}
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Card 4: Actions & Kebab Menu --}}
                    <div class="cd-activity-subcard-actions">
                        @if(!empty($candidate['id']))
                            <button type="button" class="cd-activity-btn-action" onclick="if(window.openCandidateDrawer) { window.openCandidateDrawer('{{ $candidate['id'] }}'); } else { console.log('Candidate', '{{ $candidate['id'] }}'); }">
                                <i class="far fa-user"></i> View Profile
                            </button>
                        @elseif(!empty($job['id']))
                            <button type="button" class="cd-activity-btn-action" onclick="if(window.openJobDrawer) { window.openJobDrawer('{{ $job['id'] }}'); } else { console.log('Job', '{{ $job['id'] }}'); }">
                                <i class="far fa-eye"></i> View Job
                            </button>
                        @else
                            <button type="button" class="cd-activity-btn-action" onclick="if(window.openClientNotesModal) { window.openClientNotesModal(); }">
                                <i class="far fa-file-alt"></i> Details
                            </button>
                        @endif

                        {{-- Kebab Menu Wrapper --}}
                        <div class="cd-activity-kebab-wrapper">
                            <button type="button" class="cd-activity-btn-kebab" onclick="window.ClientActivityManager && window.ClientActivityManager.toggleKebabMenu(event, this)">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="cd-activity-kebab-menu">
                                <a href="javascript:void(0)" class="cd-activity-menu-item" onclick="navigator.clipboard.writeText('{{ $eventId }}'); toastr && toastr.success('Event ID copied');">
                                    <i class="far fa-copy"></i> Copy Event ID
                                </a>
                                @if(!empty($job['id']))
                                    <a href="javascript:void(0)" class="cd-activity-menu-item" onclick="if(window.openJobDrawer) window.openJobDrawer('{{ $job['id'] }}');">
                                        <i class="fa fa-briefcase"></i> View Requisition
                                    </a>
                                @endif
                                @if(!empty($candidate['id']))
                                    <a href="javascript:void(0)" class="cd-activity-menu-item" onclick="if(window.openCandidateDrawer) window.openCandidateDrawer('{{ $candidate['id'] }}');">
                                        <i class="fa fa-user-circle"></i> Candidate Details
                                    </a>
                                @endif
                                <a href="javascript:void(0)" class="cd-activity-menu-item" onclick="if(window.ClientActivityManager) window.ClientActivityManager.openNoteModal();">
                                    <i class="fa fa-edit"></i> Add Interaction Note
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer Activity Strip --}}
            <div class="cd-activity-footer-strip">
                <div class="cd-activity-footer-left">
                    <span class="cd-activity-stream-pill stream-{{ $category }}-pill">
                        {{ strtoupper($category) }}
                    </span>
                    <span title="{{ $occurred->toIso8601String() }}">
                        <i class="far fa-clock"></i> {{ $timeAgo }} &bull; {{ $timeFormatted }}
                    </span>
                </div>
                <div class="cd-activity-footer-right">
                    <span class="cd-activity-seq-tag" title="Monotonic Sequence Pointer">
                        #seq-{{ $seq }}
                    </span>
                </div>
            </div>

        </div>
    @endforeach
@endif
