{{-- 
    Agency Activity Stream Cards (AJAX Partial)
    Renders 3-tier Modern Card + Sub-Card Enterprise Feed.
    Dual-store CQRS Elasticsearch Read Model.
--}}

@if(empty($items))
    <div class="agency-activity-empty-state text-center py-5 px-3 bg-white rounded-3 border" style="border-color: #e2e8f0;">
        <div class="empty-icon-wrapper mb-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; background: #f8fafc; border: 1px dashed #cbd5e1;">
                <i class="fa fa-clock-rotate-left text-muted font-20"></i>
            </div>
        </div>
        <h6 class="fw-bold text-dark font-14 mb-1">No Activity Records Found</h6>
        <p class="text-muted font-12 mb-3 mx-auto" style="max-width: 420px; line-height: 1.5;">
            No historical or real-time activities match your selected category and timeframe filters. You can broaden your filters or log a fresh interaction note.
        </p>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm font-12 px-3" onclick="window.AgencyActivityManager.resetFilters()">
                <i class="fa fa-rotate-left me-1"></i> Reset Filters
            </button>
            <button type="button" class="btn btn-primary btn-sm font-12 px-3" onclick="window.AgencyActivityManager.openNoteModal()">
                <i class="fa fa-pen-to-square me-1"></i> Log Note
            </button>
        </div>
    </div>
@else
    @foreach($groupedItems as $dateGroupLabel => $groupEvents)
        <div class="agency-date-group mb-3">
            <div class="agency-date-group-divider d-flex align-items-center my-2.5">
                <div class="flex-grow-1 border-top" style="border-color: #e2e8f0 !important;"></div>
                <span class="badge bg-white text-secondary border px-3 py-1 font-11 fw-semibold text-uppercase mx-2 shadow-xs" style="border-radius: 20px; letter-spacing: 0.5px; border-color: #cbd5e1 !important;">
                    <i class="fa fa-calendar-day me-1 text-primary"></i> {{ $dateGroupLabel }}
                </span>
                <div class="flex-grow-1 border-top" style="border-color: #e2e8f0 !important;"></div>
            </div>

            <div class="agency-date-group-items">
                @foreach($groupEvents as $event)
                    <div class="agency-activity-card mb-2.5 p-3 bg-white rounded-3 shadow-xs border transition-all position-relative" 
                         id="activity-card-{{ $event['event_uuid'] }}"
                         data-event-uuid="{{ $event['event_uuid'] }}" 
                         data-category="{{ strtolower($event['category']) }}"
                         style="border-color: #e2e8f0; transition: border-color 0.15s ease, box-shadow 0.15s ease;">
                        
                        <!-- Header Row: Category Badge + Time + Relative Time -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge font-11 px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1.5" 
                                      style="background-color: {{ $event['category_color'] }}15; color: {{ $event['category_color'] }}; font-weight: 600; border: 1px solid {{ $event['category_color'] }}30;">
                                    <i class="fa {{ $event['icon_class'] }}"></i>
                                    <span>{{ $event['category_badge'] }}</span>
                                </span>
                                <span class="text-secondary font-11 d-inline-flex align-items-center gap-1" title="{{ $event['date_formatted'] }} {{ $event['time_formatted'] }} IST">
                                    <i class="fa fa-clock text-muted"></i>
                                    <span>{{ $event['time_formatted'] }}</span>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-muted font-11 px-2 py-0.5 border" style="border-radius: 12px; border-color: #e2e8f0 !important;">
                                    {{ $event['relative_time'] }}
                                </span>
                                <button type="button" class="btn btn-link text-muted p-0 font-12" title="View details" 
                                        onclick="window.AgencyActivityManager.viewEventDetails('{{ $event['event_uuid'] }}')">
                                    <i class="fa fa-circle-info"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Body: Event Title & Description -->
                        <div class="event-body mb-2">
                            <h6 class="fw-bold font-13 mb-1 text-dark d-flex align-items-center gap-1.5">
                                {{ $event['event_title'] }}
                            </h6>
                            @if(!empty($event['event_description']))
                                <p class="text-secondary font-12 mb-2 line-height-normal" style="color: #475569 !important; white-space: pre-line;">
                                    {{ $event['event_description'] }}
                                </p>
                            @endif
                        </div>

                        <!-- Linked Entity Chips -->
                        @if(!empty($event['related_candidate_name']) || !empty($event['related_job_title']) || !empty($event['related_client_name']))
                            <div class="entity-chips d-flex flex-wrap gap-1.5 mb-2">
                                @if(!empty($event['related_candidate_name']))
                                    <span class="badge font-11 px-2 py-1 bg-light text-dark border d-inline-flex align-items-center gap-1 cursor-pointer hover-shadow" 
                                          onclick="window.AgencyActivityManager.switchTab('candidate_list')"
                                          title="Switch to Candidates Tab"
                                          style="border-color: #cbd5e1 !important; cursor: pointer;">
                                        <i class="fa fa-user text-purple" style="color: #8b5cf6;"></i>
                                        <span>Candidate: <strong>{{ $event['related_candidate_name'] }}</strong></span>
                                        <i class="fa fa-arrow-up-right-from-square font-9 text-muted ms-1"></i>
                                    </span>
                                @endif
                                @if(!empty($event['related_job_title']))
                                    <span class="badge font-11 px-2 py-1 bg-light text-dark border d-inline-flex align-items-center gap-1 cursor-pointer hover-shadow"
                                          onclick="window.AgencyActivityManager.switchTab('requisitions')"
                                          title="Switch to Requisitions Tab"
                                          style="border-color: #cbd5e1 !important; cursor: pointer;">
                                        <i class="fa fa-briefcase text-primary"></i>
                                        <span>Req: <strong>{{ $event['related_job_title'] }}</strong></span>
                                        <i class="fa fa-arrow-up-right-from-square font-9 text-muted ms-1"></i>
                                    </span>
                                @endif
                                @if(!empty($event['related_client_name']))
                                    <span class="badge font-11 px-2 py-1 bg-light text-secondary border d-inline-flex align-items-center gap-1"
                                          style="border-color: #cbd5e1 !important;">
                                        <i class="fa fa-building text-info"></i>
                                        <span>Client: <strong>{{ $event['related_client_name'] }}</strong></span>
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- Diff / State Change Sub-Card (if present) -->
                        @if($event['has_diff'])
                            <div class="diff-card rounded-2 p-2.5 mb-2 border" style="background: #f8fafc; border-color: #e2e8f0 !important; font-size: 11.5px;">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="fw-bold text-dark font-11 text-uppercase tracking-wider">
                                        <i class="fa fa-code-compare text-primary me-1"></i> State Change / Terms
                                    </span>
                                    <button type="button" class="btn btn-xs btn-link text-primary p-0 font-11 text-decoration-none" 
                                            onclick="jQuery('#diff-{{ $event['event_uuid'] }}').slideToggle(150)">
                                        Toggle Details
                                    </button>
                                </div>
                                <div class="diff-details" id="diff-{{ $event['event_uuid'] }}">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="p-2 rounded bg-white border" style="border-color: #fee2e2 !important;">
                                                <span class="text-danger fw-semibold d-block font-10 text-uppercase mb-0.5">Previous Value</span>
                                                <div class="text-dark font-11 text-truncate">
                                                    @if(is_array($event['previous_value']))
                                                        @foreach($event['previous_value'] as $pk => $pv)
                                                            <div><span class="text-muted">{{ $pk }}:</span> {{ is_array($pv) ? json_encode($pv) : $pv }}</div>
                                                        @endforeach
                                                    @else
                                                        {{ $event['previous_value'] ?? 'None' }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 rounded bg-white border" style="border-color: #dcfce7 !important;">
                                                <span class="text-success fw-semibold d-block font-10 text-uppercase mb-0.5">New Value</span>
                                                <div class="text-dark font-11 text-truncate">
                                                    @if(is_array($event['new_value']))
                                                        @foreach($event['new_value'] as $nk => $nv)
                                                            <div><span class="text-muted">{{ $nk }}:</span> {{ is_array($nv) ? json_encode($nv) : $nv }}</div>
                                                        @endforeach
                                                    @else
                                                        {{ $event['new_value'] ?? 'None' }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Footer Attribution Strip -->
                        <div class="card-footer-strip d-flex align-items-center justify-content-between pt-2 border-top font-11 text-muted" style="border-color: #f1f5f9 !important;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="d-inline-flex align-items-center gap-1 text-dark fw-medium">
                                    <i class="fa fa-user-circle text-muted"></i>
                                    <span>{{ $event['actor_name'] }}</span>
                                </span>
                                <span class="badge bg-light text-secondary border font-10" style="padding: 2px 6px; border-color: #e2e8f0 !important;">
                                    {{ $event['actor_role'] }}
                                </span>
                                <span class="text-muted font-10">via {{ $event['source_label'] }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="font-10 text-muted font-monospace" title="Event UUID: {{ $event['event_uuid'] }}">
                                    #{{ substr($event['event_uuid'], 0, 8) }}
                                </span>
                            </div>
                        </div>

                        {{-- Hidden JSON storage for instant slide-in details modal --}}
                        <script type="application/json" class="raw-event-payload">
                            {!! json_encode($event, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
                        </script>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endif
