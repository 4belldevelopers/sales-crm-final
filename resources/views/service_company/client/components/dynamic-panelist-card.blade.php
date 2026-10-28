@php
    /**
     * Reusable Dynamic Panelist Component (Blade Server-Side Renderer)
     * Compatible with InterviewReadModel data structure.
     */
    $rawPanelists = $readModel['interviewers'] ?? [];
    $intvsSummary = $readModel['interviewers_summary'] ?? [];

    $list = [];
    if (!empty($rawPanelists) && is_array($rawPanelists)) {
        foreach ($rawPanelists as $idx => $p) {
            $pName = trim(is_array($p) ? ($p['name'] ?? ($p['interviewer_name'] ?? '')) : (string) $p);
            $pComp = trim(is_array($p) ? ($p['company_name'] ?? ($p['company'] ?? ($intvsSummary['lead_company'] ?? ''))) : ($intvsSummary['lead_company'] ?? ''));
            $pImage = is_array($p) ? ($p['image'] ?? ($p['avatar'] ?? null)) : null;
            if (!empty($pName)) {
                $list[] = [
                    'name' => $pName,
                    'company' => $pComp,
                    'image' => $pImage,
                    'initial' => strtoupper(mb_substr($pName, 0, 1)),
                    'is_lead' => ($idx === 0),
                ];
            }
        }
    }

    // Defensive fallback: check lead_name ONLY if it's a real name (not placeholder)
    if (empty($list) && !empty($intvsSummary['lead_name'])) {
        $leadName = trim($intvsSummary['lead_name']);
        if ($leadName !== '' && $leadName !== 'N/A' && strtolower($leadName) !== 'panel assigned') {
            $defaultComp = trim($intvsSummary['lead_company'] ?? '');
            $list[] = [
                'name' => $leadName,
                'company' => $defaultComp,
                'image' => null,
                'initial' => strtoupper(mb_substr($leadName, 0, 1)),
                'is_lead' => true,
            ];
            $extraCount = (int) ($intvsSummary['extra_count'] ?? 0);
            for ($i = 1; $i <= $extraCount; $i++) {
                $list[] = [
                    'name' => 'Panelist #' . ($i + 1),
                    'company' => $defaultComp,
                    'image' => null,
                    'initial' => 'P',
                    'is_lead' => false,
                ];
            }
        }
    }

    $count = count($list);

    // Color palette generator
    $getColor = function($name) {
        $palette = [
            ['bg' => '#eff6ff', 'color' => '#2563eb'],
            ['bg' => '#f3e8ff', 'color' => '#9333ea'],
            ['bg' => '#ecfdf5', 'color' => '#059669'],
            ['bg' => '#fff7ed', 'color' => '#ea580c'],
            ['bg' => '#fef2f2', 'color' => '#dc2626'],
            ['bg' => '#f0fdfa', 'color' => '#0d9488']
        ];
        $h = 0;
        for ($i = 0; $i < strlen($name); $i++) {
            $h = ord($name[$i]) + (($h << 5) - $h);
        }
        return $palette[abs($h) % count($palette)];
    };
@endphp

@if ($count === 0)
    <!-- STATE 1: No Panelist Assigned (Rule 4) -->
    <div class="cd-it-subcard cd-it-panelist-card">
        <div class="cd-subcard-header">
            <span class="cd-subcard-label">PANELISTS</span>
            <span class="cd-it-panel-count-pill cd-it-empty-pill">—</span>
        </div>
        <div class="cd-it-panelist-empty" title="No panelist assigned yet">
            <div class="cd-it-empty-dash">—</div>
            <div class="cd-it-empty-sub">Not Assigned</div>
        </div>
    </div>
@elseif ($count === 1)
    @php
        $p = $list[0];
        $col = $getColor($p['name']);
    @endphp
    <!-- STATE 2: Single Panelist (Rule 1: Avatar on top, Name below, Company below) -->
    <div class="cd-it-subcard cd-it-panelist-card">
        <div class="cd-subcard-header">
            <span class="cd-subcard-label">PANELISTS</span>
            <span class="cd-it-panel-count-pill">1 Panelist</span>
        </div>
        <div class="cd-it-panelist-single" title="{{ $p['name'] }} ({{ $p['company'] ?: '—' }})">
            <div class="cd-panel-avatar cd-panel-avatar-single" style="background: {{ $col['bg'] }} !important; color: {{ $col['color'] }} !important;">
                @if(!empty($p['image']))
                    <img src="{{ $p['image'] }}" alt="{{ $p['name'] }}" class="cd-panel-avatar-img" onerror="this.style.display='none';this.parentElement.innerText='{{ $p['initial'] }}';">
                @else
                    {{ $p['initial'] }}
                @endif
            </div>
            <div class="cd-it-panelist-meta">
                <div class="cd-it-panelist-name" title="{{ $p['name'] }}">
                    {{ $p['name'] }}
                </div>
                <div class="cd-it-panelist-company" title="{{ $p['company'] ?: '—' }}">
                    {{ $p['company'] ?: '—' }}
                </div>
            </div>
        </div>
    </div>
@else
    @php
        $maxVisible = 3;
        $visibleList = array_slice($list, 0, $maxVisible);
        $remainingCount = $count - $maxVisible;
        $lead = $list[0];
        $extraCount = $count - 1;
    @endphp
    <!-- STATE 3: Multiple Panelists (Rule 2: Overlapping circular avatars, +N red indicator, hover popover) -->
    <div class="cd-it-subcard cd-it-panelist-card">
        <div class="cd-subcard-header">
            <span class="cd-subcard-label">PANELISTS</span>
            <span class="cd-it-panel-count-pill">{{ $count }} Panelists</span>
        </div>
        <div class="cd-it-panelist-multiple">
            <div class="cd-panel-avatar-stack">
                @foreach ($visibleList as $p)
                    @php $col = $getColor($p['name']); @endphp
                    <div class="cd-panel-avatar" style="background: {{ $col['bg'] }} !important; color: {{ $col['color'] }} !important;" title="{{ $p['name'] }}">
                        @if(!empty($p['image']))
                            <img src="{{ $p['image'] }}" alt="{{ $p['name'] }}" class="cd-panel-avatar-img" onerror="this.style.display='none';this.parentElement.innerText='{{ $p['initial'] }}';">
                        @else
                            {{ $p['initial'] }}
                        @endif
                    </div>
                @endforeach
                @if ($remainingCount > 0)
                    <div class="cd-panel-avatar cd-panel-avatar-extra" title="+{{ $remainingCount }} more panelists">
                        +{{ $remainingCount }}
                    </div>
                @endif
            </div>
            <div class="cd-it-panelist-meta">
                <div class="cd-it-panelist-name" title="{{ $lead['name'] }} + {{ $extraCount }} more">
                    {{ $lead['name'] }} <span class="cd-it-panelist-more-text">+{{ $extraCount }} more</span>
                </div>
                <div class="cd-it-panelist-company" title="{{ $lead['company'] ?: '—' }}">
                    {{ $lead['company'] ?: '—' }}
                </div>
            </div>

            <!-- Hover Popover with Complete Panelist List -->
            <div class="cd-it-popover-card">
                <div class="cd-it-popover-header">
                    <span><i class="fas fa-users text-primary me-1"></i> Interview Panel</span>
                    <span class="badge" style="background:rgba(56,189,248,0.2); color:#38bdf8; font-size:9.5px; padding:1px 6px; border-radius:4px;">{{ $count }}</span>
                </div>
                <div class="cd-it-popover-body">
                    @foreach ($list as $idx => $p)
                        @php $col = $getColor($p['name']); @endphp
                        <div class="cd-it-popover-row">
                            <div class="cd-popover-avatar" style="background: {{ $col['bg'] }}; color: {{ $col['color'] }};">
                                @if(!empty($p['image']))
                                    <img src="{{ $p['image'] }}" alt="{{ $p['name'] }}">
                                @else
                                    {{ $p['initial'] }}
                                @endif
                            </div>
                            <div class="cd-popover-info">
                                <div class="cd-popover-name" title="{{ $p['name'] }}">
                                    <span>{{ $p['name'] }}</span>
                                    @if ($idx === 0)
                                        <span class="item-lead-badge">Lead</span>
                                    @endif
                                </div>
                                <div class="cd-popover-company" title="{{ $p['company'] }}">
                                    {{ $p['company'] ?: '—' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
