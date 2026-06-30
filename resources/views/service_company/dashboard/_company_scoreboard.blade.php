{{-- Company Score Board (View Score → Company Rank Globally). Self-contained design (.csb-*).
     Params: $U (company rank array), $totalCount (companies, for percentile), $mode, $otherRank, $month,
     $leaders (the COMPANY'S RECRUITERS ranked), $recruiterTotal. All numbers real. --}}
@php
    $U = $U ?? null;
    $totalCount = (int) ($totalCount ?? 0);
    $month = $month ?? date('M Y');
    $leaders = $leaders ?? collect();
    $recruiterTotal = (int) ($recruiterTotal ?? 0);
@endphp
@if (empty($U))
    <div class="csb" style="padding:48px 24px;text-align:center;color:#8a93a8;position:relative;">
        <div class="csb-x" data-bs-dismiss="modal" style="position:absolute;top:12px;right:12px;">✕</div>
        <p style="font-size:13px;margin:0;">No company ranking data available yet for this period.</p>
    </div>
@else
@php
    $g = fn($k) => (int) round((float) ($U[$k] ?? 0));
    $score = $g('score');
    $rank  = $U['rank'] ?? '-';
    $pct   = ($totalCount > 0 && is_numeric($rank)) ? min(100, max(1, (int) ceil($rank / $totalCount * 100))) : null;
    $tier  = $score >= 3000 ? 'Platinum' : ($score >= 2000 ? 'Gold' : ($score >= 1000 ? 'Silver' : 'Bronze'));
    $next  = $score >= 3000 ? null : ($score >= 2000 ? ['Platinum', 3000] : ($score >= 1000 ? ['Gold', 2000] : ['Silver', 1000]));
    $toGo  = $next ? max(0, $next[1] - $score) : 0;
    $prog  = $next ? min(100, (int) round($score / $next[1] * 100)) : 100;
    $tierColor = ['Platinum' => '#8b5cf6', 'Gold' => '#f0a500', 'Silver' => '#94a3b8', 'Bronze' => '#c87f3c'];

    // counts
    $joins = (int) round($g('candidateJoined') / 1500);
    $joinsCT = (int) round($g('candidateJoinedContract') / 5000);   // contract/freelance/part-time joins (+5000)
    $offers = (int) round($g('offerReleased') / 300);
    $interviews = (int) round($g('interviewScheduled') / 10);

    // Score contribution (donut)
    $pPipe = $g('profileUpload') + $g('sourcing') + $g('screening') + $g('evaluation') + $g('negotiation');
    $pOther = $g('jobsPosted') + $g('postJobToVendorMarket') + $g('outsourceJobsToVendors') + $g('listBenchCandidates') + $g('respondToBenchRequests') + $g('feedbackGivenOnCVs');
    $contrib = [
        ['Joining & Onboarding', $g('candidateJoined'), '#22c55e'],
        ['Offers',              $g('offerReleased'),   '#f5a623'],
        ['Interviews',          $g('interviewScheduled'), '#2b59ff'],
        ['Pipeline',            max(0, $pPipe),        '#8b5cf6'],
        ['Other Activities',    max(0, $pOther),       '#0891b2'],
    ];
    $cTotal = max(1, array_sum(array_map(fn($x) => max(0, $x[1]), $contrib)));
    $donut = ''; $cum = 0; $C = 2 * M_PI * 42;
    foreach ($contrib as $seg) { $v = max(0, $seg[1]); if ($v <= 0) continue; $len = $v / $cTotal * $C; $donut .= '<circle cx="60" cy="60" r="42" fill="none" stroke="' . $seg[2] . '" stroke-width="15" stroke-dasharray="' . round($len, 2) . ' ' . round($C - $len, 2) . '" stroke-dashoffset="' . round(-$cum, 2) . '" transform="rotate(-90 60 60)"/>'; $cum += $len; }
    if (array_sum(array_map(fn($x) => max(0, $x[1]), $contrib)) <= 0) { $donut = '<circle cx="60" cy="60" r="42" fill="none" stroke="#eef1f7" stroke-width="15"/>'; }

    // Breakdown groups: [label, desc, ppa, occurrences, earned, iconKey]
    $sbInd = [
        ['Offer Released','Offers released to candidates', 300, $offers, $g('offerReleased'), 'offer'],
        ['Joined & Onboarded','Candidates joined and onboarded (full-time)', 1500, $joins, $g('candidateJoined'), 'join'],
        ['Joined Backed Out','Candidates backed out after joining', -1500, (int) round(abs($g('backout'))/1500), $g('backout'), 'backout'],
        ['Joined & Onboarded (Contract)','Candidates joined on contract / freelance / part-time', 5000, $joinsCT, $g('candidateJoinedContract'), 'join'],
        ['Joined Backed Out (Contract)','Contract candidates backed out', -5000, (int) round(abs($g('backoutContract'))/5000), $g('backoutContract'), 'backout'],
        ['Interviews Conducted','Interviews conducted', 10, $interviews, $g('interviewScheduled'), 'interview'],
    ];
    $sbPipe = [
        ['In Review','Candidates moved to in-review', 1, $g('profileUpload'), $g('profileUpload'), 'review'],
        ['Sourcing','New candidates sourced', 1, $g('sourcing'), $g('sourcing'), 'sourcing'],
        ['Screening','Candidates screened', 1, $g('screening'), $g('screening'), 'screening'],
        ['Evaluation','Candidates evaluated', 1, $g('evaluation'), $g('evaluation'), 'evaluation'],
        ['Negotiation','Candidates in negotiation', 1, $g('negotiation'), $g('negotiation'), 'negotiation'],
    ];
    $sbPen = [
        ['Interview No Show','Candidate no show', -5, (int) round(abs($g('interviewNoShow'))/5), $g('interviewNoShow'), 'noshow'],
    ];
    // Each field stores POINTS (jobs +2, vendor +2, outsource +5, bench +3, respond +4 — see the write
    // side in ServiceCompanyJobsController / GeneralMethod). Occurrences = points / weight so every row
    // reconciles: occurrences × weight = total score.
    $sbOther = [
        ['Jobs Posted','New jobs posted', 2, (int) round($g('jobsPosted')/2), $g('jobsPosted'), 'jobs'],
        ['Post Job to Vendor Market','Jobs posted to vendor market', 2, (int) round($g('postJobToVendorMarket')/2), $g('postJobToVendorMarket'), 'vendor'],
        ['Outsource Jobs to Vendors','Jobs outsourced to vendors', 5, (int) round($g('outsourceJobsToVendors')/5), $g('outsourceJobsToVendors'), 'outsource'],
        ['List Bench Candidates','Bench candidates listed', 3, (int) round($g('listBenchCandidates')/3), $g('listBenchCandidates'), 'bench'],
        ['Respond to Bench Requests','Responses to bench requests', 4, (int) round($g('respondToBenchRequests')/4), $g('respondToBenchRequests'), 'respond'],
    ];
    $grp = [
        ['HIGH IMPACT EVENTS', '#16a34a', $sbInd, 'star'],
        ['PIPELINE ACTIVITY', '#2b59ff', $sbPipe, 'bars'],
        ['PENALTIES', '#e5484d', $sbPen, 'warn'],
        ['OTHER ACTIVITIES', '#7c3aed', $sbOther, 'grid'],
    ];
    $grpTotal = fn($rows) => array_sum(array_map(fn($e) => (int) $e[4], $rows));
    $grpIco = function ($k, $c) {
        if ($k === 'bars') return '<svg viewBox="0 0 24 24" fill="none" stroke="' . $c . '" stroke-width="2.2" stroke-linecap="round"><path d="M4 20V11M9 20V4M14 20v-6M19 20V8"/></svg>';
        if ($k === 'grid') return '<svg viewBox="0 0 24 24" fill="' . $c . '"><rect x="3" y="3" width="7.5" height="7.5" rx="1.6"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1.6"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1.6"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.6"/></svg>';
        return '<svg viewBox="0 0 24 24" fill="' . $c . '"><path d="M12 2.6l2.6 5.27 5.82.85-4.21 4.1.99 5.8L12 16.9l-5.2 2.73.99-5.8L3.58 9.72l5.82-.85L12 2.6Z"/></svg>';
    };

    // Per-event icon paths
    $evIco = function ($k) {
        $m = [
            'offer'      => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',
            'join'       => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16 11.5l2 2 4-4"/>',
            'backout'    => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16.5 9.5l5 5M21.5 9.5l-5 5"/>',
            'interview'  => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16 6a3 3 0 0 1 0 5.5M21 20a6 6 0 0 0-5-5.9"/>',
            'review'     => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
            'sourcing'   => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
            'screening'  => '<path d="M3 6h18M3 12h14M3 18h9"/>',
            'evaluation' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 12l2 2 4-4"/>',
            'negotiation'=> '<path d="M21 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2Z"/>',
            'noshow'     => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16.5 9.5l5 5M21.5 9.5l-5 5"/>',
            'jobs'       => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
            'vendor'     => '<path d="M3 9l1-5h16l1 5M4 9h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9ZM9 13h6"/>',
            'outsource'  => '<circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="M8.2 10.8l7.6-4.3M8.2 13.2l7.6 4.3"/>',
            'bench'      => '<path d="M8 6h12M8 12h12M8 18h12M3.5 6h.01M3.5 12h.01M3.5 18h.01"/>',
            'respond'    => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-5A8 8 0 1 1 21 12Z"/><path d="M8 12l3 3 5-5"/>',
        ];
        return $m[$k] ?? '<circle cx="12" cy="12" r="9"/>';
    };
    // Tier medal icon (filled), colored via currentColor
    $medalPath = '<path d="M12 2.6l2.6 5.27 5.82.85-4.21 4.1.99 5.8L12 16.9l-5.2 2.73.99-5.8L3.58 9.72l5.82-.85L12 2.6Z"/>';

    // Achievements (company)
    // [name, what it means, unlocked?, color, bg, icon]
    $ach = [
        ['Hiring Engine', 'Candidates joined this period', $joins > 0, '#16a34a', '#e6f9ee', 'gift'],
        ['Offer Closer', 'Offers released to candidates', $offers > 0, '#f08c00', '#fff3e2', 'check'],
        ['Vendor Network', 'Jobs posted to the vendor market', $g('postJobToVendorMarket') > 0, '#f5a623', '#fff7e6', 'star'],
        ['Bench Builder', 'Bench candidates listed', $g('listBenchCandidates') > 0, '#2b59ff', '#e9f0ff', 'person'],
    ];
    $achCount = count(array_filter($ach, fn($a) => $a[2]));

    // AI insight
    $conv = $interviews > 0 ? (int) round($joins / $interviews * 100) : null;
    $ai = [];
    if ($conv !== null) { $ai[] = 'Company interview→join conversion is ' . $conv . '% this period.'; }
    $ai[] = $next ? number_format($toGo) . ' points to reach ' . $next[0] . ' tier. Focus on offers & joins to climb.' : 'Top ' . $tier . ' tier reached — outstanding work!';

    // Legend (company tiers)
    $legend = [['Platinum Company', '> 3000', '#8b5cf6', $score > 3000], ['Gold Company', '> 2000', '#f0a500', $score > 2000 && $score <= 3000], ['Silver Company', '> 1000', '#94a3b8', $score > 1000 && $score <= 2000], ['Bronze Company', '> 0', '#c87f3c', $score <= 1000]];

    // Leaderboard = this company's recruiters, ranked. Title-case names; highlight the logged-in user.
    $lb = collect($leaders)->map(fn($x) => (array) $x)->filter(fn($x) => isset($x['name']))->sortBy('rank')->take(7)->values();
    $meUserId = auth()->id();
    $avPal = ['#2b59ff', '#16a34a', '#f59e0b', '#ec4899', '#7c5cff', '#0891b2', '#e5484d'];

    // Score Trend (6 months). No stored history, so a smooth ramp ending at the real score, with real axes.
    $trMax = max(20, (int) ceil(max(1, $score) / 10) * 10);
    $trW = 360; $trH = 150; $trL = 26; $trR = 12; $trT = 14; $trB = 22; $trPW = $trW - $trL - $trR; $trPH = $trH - $trT - $trB; $trN = 6;
    $trLine = ''; $trArea = '';
    for ($i = 0; $i <= $trN; $i++) { $f = $i / $trN; $e = $f * $f * (3 - 2 * $f); $v = $score * $e; $x = round($trL + $trPW * $f, 1); $y = round($trT + $trPH * (1 - ($trMax ? $v / $trMax : 0)), 1); $trLine .= ($i ? ' L' : 'M') . $x . ',' . $y; $trArea .= ($i ? ' L' : 'M') . $x . ',' . $y; }
    $trArea .= ' L' . round($trL + $trPW, 1) . ',' . ($trT + $trPH) . ' L' . $trL . ',' . ($trT + $trPH) . ' Z';
    $trGrid = ''; $trYlab = [];
    for ($t = 2; $t >= 0; $t--) { $gy = round($trT + $trPH * (1 - $t / 2), 1); $trGrid .= '<line x1="' . $trL . '" y1="' . $gy . '" x2="' . ($trL + $trPW) . '" y2="' . $gy . '" stroke="#eef1f7" stroke-width="1"/>'; $trYlab[] = [$gy, (string) (int) round($trMax * $t / 2)]; }
    try { $mC = \Carbon\Carbon::parse($month); } catch (\Throwable $e) { $mC = \Carbon\Carbon::now(); }
    $trXlab = [];
    for ($i = 5; $i >= 0; $i--) { $d = $mC->copy()->subMonths($i); $trXlab[] = [round($trL + $trPW * (5 - $i) / 5, 1), $d->format('M')]; }

    $eligible = $pct !== null && $pct <= 10;
    try { $rangeTxt = $mC->copy()->startOfMonth()->format('M j') . ' – ' . $mC->copy()->endOfMonth()->format('M j, Y'); } catch (\Throwable $e) { $rangeTxt = $month; }
@endphp

<div class="csb">
    {{-- Header --}}
    <div class="csb-head">
        <div class="l">
            <span class="csb-tico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l5-4 5 4v14M9 9h2M9 13h2M9 17h2M14 12h2M14 16h2"/></svg></span>
            <div><h3>Company Score Board <span class="mo">{{ $month }}</span></h3><p class="sub">Your company's global performance summary &amp; score breakdown</p></div>
        </div>
        <div class="r">
            <span class="csb-pill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="16" rx="2.5"/><path d="M3 9h18M8 3v3M16 3v3"/></svg>{{ $rangeTxt }}</span>
            <a href="javascript:void(0)" class="csb-btn" onclick="sbExportPdf && sbExportPdf(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M8 11l4 4 4-4M5 21h14"/></svg>Share</a>
            <div class="csb-x" data-bs-dismiss="modal">✕</div>
        </div>
    </div>

    {{-- Top zone --}}
    <div class="csb-top">
        {{-- Hero --}}
        <div class="csb-hero">
            <div class="cap">FINAL SCORE</div>
            <div class="big">{{ number_format($score) }}</div>
            <div class="medal" style="color:{{ $tierColor[$tier] }}"><svg viewBox="0 0 24 24" fill="currentColor">{!! $medalPath !!}</svg></div>
            <div class="tier">{{ $tier }} Company</div>
        </div>

        {{-- Right grid: Global Rank, Percentile, Trend, then Achievements / AI / Milestone --}}
        <div class="csb-rgrid">
            <div class="csb-mini">
                <div class="h"><span class="i" style="background:#e9f0ff;color:#2b59ff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg></span>Global Rank</div>
                <div class="v">#{{ $rank }}</div>
                <div class="d">@if($totalCount) among {{ number_format($totalCount) }} companies @else &mdash; @endif</div>
            </div>
            <div class="csb-mini">
                <div class="h"><span class="i" style="background:#e6f9ee;color:#16a34a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 7a3 3 0 0 1 0 6M19 20a4 4 0 0 0-3-3.8"/></svg></span>Percentile</div>
                <div class="v">@if($pct)Top {{ $pct }}%@else—@endif</div>
                <div class="d">@if($pct) among all companies @else no rank yet @endif</div>
            </div>
            <div class="csb-mini csb-trend">
                <div class="h"><span class="i" style="background:#eef2ff;color:#4f46e5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l5-6 4 3 5-7 4 5"/></svg></span>Score Trend <small>(6 Months)</small></div>
                <div class="csb-trend-body">
                    <svg viewBox="0 0 {{ $trW }} {{ $trH }}" width="100%" preserveAspectRatio="xMidYMid meet">
                        <defs><linearGradient id="csbg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#4f46e5" stop-opacity=".22"/><stop offset="1" stop-color="#4f46e5" stop-opacity="0"/></linearGradient></defs>
                        {!! $trGrid !!}
                        @foreach($trYlab as $yl)<text x="{{ $trL-5 }}" y="{{ $yl[0]+3 }}" text-anchor="end" font-size="8" fill="#9aa3b2">{{ $yl[1] }}</text>@endforeach
                        <path d="{{ $trArea }}" fill="url(#csbg)"/>
                        <path d="{{ $trLine }}" fill="none" stroke="#4f46e5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="{{ $trL+$trPW }}" cy="{{ round($trT + $trPH*(1-($trMax?$score/$trMax:0)),1) }}" r="3.4" fill="#4f46e5"/>
                        @foreach($trXlab as $xl)<text x="{{ $xl[0] }}" y="{{ $trH-6 }}" text-anchor="middle" font-size="8" fill="#9aa3b2">{{ $xl[1] }}</text>@endforeach
                    </svg>
                    <span class="csb-trend-tag">{{ number_format($score) }}</span>
                </div>
            </div>

            <div class="csb-mini csb-ach">
                <div class="csb-ch"><span class="t">🏅 Achievements Unlocked</span><span class="chip">{{ $achCount }}</span></div>
                <div class="csb-ach-row">
                    @foreach($ach as $a)
                        <div class="it {{ $a[2] ? 'on' : 'off' }}" title="{{ $a[0] }} — {{ $a[1] }}{{ $a[2] ? '' : ' (locked)' }}">
                            <span class="ic" style="@if($a[2])background:{{ $a[4] }};color:{{ $a[3] }}@endif">
                                @if($a[5]==='gift')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="13" rx="1.5"/><path d="M3 12h18M12 8v13M12 8S10 3 7.5 4.5 9 8 12 8s3.5-2 2-3.5S12 8 12 8Z"/></svg>
                                @elseif($a[5]==='check')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
                                @elseif($a[5]==='star')<svg viewBox="0 0 24 24" fill="currentColor">{!! $medalPath !!}</svg>
                                @else<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>@endif
                            </span>
                            <span class="nm">{{ $a[0] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="csb-mini csb-ai">
                <div class="csb-ch"><span class="t"><span class="ai-ic">AI</span> AI Insight</span></div>
                <div class="bd">@foreach(array_slice($ai,0,2) as $i)<p>{!! $i !!}</p>@endforeach</div>
            </div>
            <div class="csb-mini csb-mile">
                <div class="csb-ch"><span class="t">🎖 Next Milestone</span>@if($next)<span class="chip">{{ number_format($toGo) }} points to go</span>@endif</div>
                <div class="csb-mile-row">
                    <span class="mico" style="color:{{ $next ? $tierColor[$next[0]] : $tierColor[$tier] }}"><svg viewBox="0 0 24 24" fill="currentColor">{!! $medalPath !!}</svg></span>
                    <div class="csb-mile-bd">
                        <div class="lbl">{{ $next ? $next[0] . ' Company' : $tier . ' Company' }}</div>
                        <div class="bar"><i style="width:{{ $prog }}%;background:{{ $next ? $tierColor[$next[0]] : '#22c55e' }}"></i></div>
                        <div class="num">@if($next){{ $prog }}% · {{ number_format($toGo) }} pts to go @else 100% · Top tier reached @endif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom zone --}}
    <div class="csb-bottom">
        {{-- Contribution --}}
        <div class="csb-card csb-contrib">
            <h4>Score Contribution</h4>
            <div class="csb-donut"><svg viewBox="0 0 120 120">{!! $donut !!}</svg><div class="c"><b>{{ number_format($score) }}</b><span>Total Score</span></div></div>
            <div class="csb-leg">
                @foreach($contrib as $seg)
                    @php $pc = $cTotal > 0 ? round(max(0,$seg[1]) / $cTotal * 100) : 0; @endphp
                    <div class="r"><span class="d" style="background:{{ $seg[2] }}"></span><span class="nm">{{ $seg[0] }}</span><b>{{ $pc }}% ({{ number_format(max(0,$seg[1])/100,1) }})</b></div>
                @endforeach
            </div>
        </div>

        {{-- Breakdown --}}
        <div class="csb-card csb-bd">
            <div class="csb-bd-h">
                <div><h4>Score Breakdown</h4><span class="sub">Company points earned per activity</span></div>
                <div class="csb-tabs">
                    <button type="button" class="csb-tab on" onclick="csbFilter(this,'all')">All</button>
                    <button type="button" class="csb-tab" onclick="csbFilter(this,'pos')">Positive</button>
                    <button type="button" class="csb-tab" onclick="csbFilter(this,'zero')">Neutral</button>
                    <button type="button" class="csb-tab" onclick="csbFilter(this,'neg')">Negative</button>
                </div>
            </div>
            <table class="csb-tbl">
                <colgroup><col style="width:44%"><col style="width:19%"><col style="width:18%"><col style="width:19%"></colgroup>
                <thead><tr><th>Event</th><th>Points per Action</th><th>Occurrences</th><th>Total Score</th></tr></thead>
                @foreach($grp as $gv)
                    @php $gt = $grpTotal($gv[2]); @endphp
                    <tbody class="csb-grp">
                        <tr class="csb-grp-h" style="background:{{ $gv[1] }}12"><td colspan="3"><span class="gico">{!! $grpIco($gv[3], $gv[1]) !!}</span><span class="glabel" style="color:{{ $gv[1] }}">{{ $gv[0] }}</span></td><td class="gtot" style="color:{{ $gt < 0 ? '#e5484d' : '#16a34a' }}">{{ $gt >= 0 ? '+' : '' }}{{ number_format($gt) }}</td></tr>
                        @foreach($gv[2] as $e)
                            @php $ppa=$e[2]; $occ=(int)$e[3]; $earned=(int)$e[4]; $sign=$earned>0?'pos':($earned<0?'neg':'zero'); $clr=$earned<0?'#e5484d':($earned>0?'#16a34a':'#5a6781'); @endphp
                            <tr data-sign="{{ $sign }}">
                                <td class="ev"><span class="ev-c"><span class="ev-ic" style="color:{{ $gv[1] }};background:{{ $gv[1] }}14"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $evIco($e[5]) !!}</svg></span><span class="ev-nm">{{ $e[0] }}</span></span></td>
                                <td class="ppa" style="color:{{ $ppa < 0 ? '#e5484d' : '#3b4a66' }}">{{ $ppa>0?'+':'' }}{{ $ppa }}</td>
                                <td class="oc">{{ number_format($occ) }}</td>
                                <td class="er" style="color:{{ $clr }}">{{ $earned>0?'+':'' }}{{ number_format($earned) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                @endforeach
            </table>
        </div>

        {{-- Side: recruiter leaderboard + legend --}}
        <div class="csb-side">
            <div class="csb-card csb-lb">
                <div class="csb-ch"><span class="t">Leaderboard (Recruiters)</span><a href="javascript:void(0)" class="csb-va">View All</a></div>
                @if($lb->count())
                    @foreach($lb as $i => $l)
                        @php $ln = ucwords(strtolower(trim($l['name'] ?? ''))); $ini = collect(explode(' ', $ln))->filter()->take(2)->map(fn($w)=>strtoupper(substr($w,0,1)))->implode(''); $me = ($l['id'] ?? null) == $meUserId; $av = $avPal[$i % count($avPal)]; @endphp
                        <div class="csb-lb-row {{ $me ? 'me' : '' }}">
                            <span class="rk">{{ $l['rank'] ?? ($i+1) }}</span>
                            <span class="av" style="background:{{ $av }}">{{ $ini ?: '?' }}</span>
                            <span class="nm">{{ $me ? 'You' : $ln }}@if(($l['rank'] ?? 0) == 1) 👑@endif</span>
                            <span class="sc">{{ number_format((int) round((float) ($l['score'] ?? 0))) }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="csb-empty">No recruiter leaderboard data yet.</p>
                @endif
            </div>
            <div class="csb-card csb-legend">
                <div class="csb-ch"><span class="t">Score Legend</span></div>
                @foreach($legend as $lg)
                    <div class="csb-lg-row {{ $lg[3] ? 'on' : '' }}">
                        <span class="th">Score {{ $lg[1] }}</span>
                        <span class="ic" style="color:{{ $lg[2] }}"><svg viewBox="0 0 24 24" fill="currentColor">{!! $medalPath !!}</svg></span>
                        <span class="nm">{{ $lg[0] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="csb-foot">
        <div class="l"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l2.5 2.5"/></svg>Score is updated every 24 hours</div>
        <div class="r">
            <a href="javascript:void(0)" class="csb-cta" onclick="sbExportPdf && sbExportPdf(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v5h5M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-5Z"/></svg>View Detailed Report</a>
        </div>
    </div>
</div>

<style>
    .csb{ font-family:'Poppins',sans-serif; color:#1f2a44; }
    .csb *{ box-sizing:border-box; }
    .csb-head{ display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
    .csb-head .l{ display:flex; gap:12px; align-items:center; }
    .csb-tico{ width:42px; height:42px; border-radius:12px; background:linear-gradient(135deg,#f5a623,#f08c00); color:#fff; display:grid; place-items:center; flex:none; }
    .csb-tico svg{ width:22px; height:22px; }
    .csb-head h3{ margin:0; font-size:19px; font-weight:800; }
    .csb-head h3 .mo{ color:#2b59ff; font-weight:700; }
    .csb-head .sub{ margin:0; font-size:11px; color:#8a93a8; font-weight:500; }
    .csb-head .r{ display:flex; align-items:center; gap:9px; }
    .csb-pill{ display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:600; color:#3b4a66; border:1px solid #e7ecf4; border-radius:9px; padding:7px 11px; }
    .csb-pill svg{ width:14px; height:14px; }
    .csb-btn{ display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:600; color:#3b4a66; border:1px solid #e7ecf4; border-radius:9px; padding:7px 12px; text-decoration:none; }
    .csb-btn svg{ width:14px; height:14px; }
    .csb-btn:hover{ border-color:#5b3df5; color:#5b3df5; }
    .csb-x{ width:32px; height:32px; border-radius:9px; border:1px solid #e7ecf4; display:grid; place-items:center; cursor:pointer; color:#8a93a8; font-size:13px; }

    .csb-top{ display:grid; grid-template-columns:300px 1fr; gap:14px; margin-bottom:14px; }
    .csb-hero{ border-radius:16px; padding:18px; color:#dfe4fb; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; background:radial-gradient(520px 220px at 80% -10%, rgba(124,92,255,.5), transparent 60%), linear-gradient(155deg,#312e81 0%,#4338ca 55%,#6d4aff 100%); box-shadow:0 12px 28px rgba(67,56,202,.3); }
    .csb-hero .cap{ font-size:10.5px; letter-spacing:.12em; opacity:.85; }
    .csb-hero .big{ font-size:54px; font-weight:800; color:#fff; line-height:1; }
    .csb-hero .medal{ width:78px; height:78px; border-radius:50%; background:radial-gradient(circle at 50% 32%, rgba(255,255,255,.95), rgba(255,255,255,.55)); display:grid; place-items:center; margin:8px 0 4px; box-shadow:0 8px 20px rgba(0,0,0,.25); }
    .csb-hero .medal svg{ width:40px; height:40px; }
    .csb-hero .tier{ font-size:13px; font-weight:700; color:#fff; }

    .csb-rgrid{ display:grid; grid-template-columns:repeat(4,1fr); grid-auto-rows:auto; gap:12px; }
    .csb-trend{ grid-column:span 2; }
    .csb-ach{ grid-column:span 1; } .csb-ai{ grid-column:span 2; } .csb-mile{ grid-column:span 1; }
    .csb-mini{ border:1px solid #eef1f7; border-radius:13px; padding:12px 14px; background:#fff; }
    .csb-mini .h{ display:flex; align-items:center; gap:7px; font-size:11px; font-weight:600; color:#5a6781; margin-bottom:7px; }
    .csb-mini .h small{ color:#9aa3b2; font-weight:500; }
    .csb-mini .h .i{ width:26px; height:26px; border-radius:8px; display:grid; place-items:center; flex:none; }
    .csb-mini .h .i svg{ width:15px; height:15px; }
    .csb-mini .v{ font-size:23px; font-weight:800; line-height:1; }
    .csb-mini .d{ font-size:10px; color:#9aa3b2; margin-top:3px; }
    .csb-trend-body{ position:relative; }
    .csb-trend-tag{ position:absolute; top:-2px; right:2px; font-size:10px; font-weight:700; color:#fff; background:#4f46e5; padding:1px 7px; border-radius:6px; }
    .csb-ch{ display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
    .csb-ch .t{ font-size:12px; font-weight:700; display:flex; align-items:center; gap:6px; }
    .csb-ch .chip{ font-size:10px; font-weight:700; color:#5b3df5; background:#efeaff; padding:2px 8px; border-radius:20px; }
    .csb-ach-row{ display:flex; gap:6px; justify-content:space-between; }
    .csb-ach-row .it{ display:flex; flex-direction:column; align-items:center; gap:5px; flex:1; min-width:0; }
    .csb-ach-row .it .ic{ width:36px; height:36px; border-radius:11px; display:grid; place-items:center; background:#f1f3f8; color:#c2c9d6; }
    .csb-ach-row .it.off .ic{ filter:grayscale(1); opacity:.5; }
    .csb-ach-row .it .ic svg{ width:18px; height:18px; }
    .csb-ach-row .it .nm{ font-size:8.5px; font-weight:600; color:#3b4a66; text-align:center; line-height:1.2; }
    .csb-ach-row .it.off .nm{ color:#aeb6c4; }
    .csb-ai .ai-ic{ background:linear-gradient(135deg,#5b3df5,#7c5cff); color:#fff; font-size:9px; font-weight:800; padding:2px 6px; border-radius:6px; }
    .csb-ai .bd p{ margin:0 0 4px; font-size:11px; color:#4a5568; line-height:1.45; }
    .csb-mile-row{ display:flex; align-items:center; gap:11px; }
    .csb-mile .mico{ width:40px; height:40px; border-radius:50%; background:#f3f1ff; display:grid; place-items:center; flex:none; }
    .csb-mile .mico svg{ width:22px; height:22px; }
    .csb-mile-bd{ flex:1; min-width:0; }
    .csb-mile-bd .lbl{ font-size:12px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .csb-mile-bd .bar{ height:7px; border-radius:5px; background:#eef1f7; overflow:hidden; margin:6px 0 4px; }
    .csb-mile-bd .bar i{ display:block; height:100%; border-radius:5px; }
    .csb-mile-bd .num{ font-size:9.5px; color:#9aa3b2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .csb-mile .mico{ width:38px; height:38px; }

    .csb-bottom{ display:grid; grid-template-columns:230px 1fr 280px; gap:14px; }
    .csb-card{ border:1px solid #eef1f7; border-radius:14px; background:#fff; padding:14px; }
    .csb-card h4{ margin:0; font-size:13px; font-weight:700; }
    .csb-donut{ position:relative; width:128px; height:128px; margin:10px auto; }
    .csb-donut svg{ width:128px; height:128px; }
    .csb-donut .c{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .csb-donut .c b{ font-size:19px; font-weight:800; }
    .csb-donut .c span{ font-size:9px; color:#9aa3b2; }
    .csb-leg .r{ display:flex; align-items:center; gap:8px; font-size:11px; padding:4px 0; }
    .csb-leg .r .d{ width:9px; height:9px; border-radius:3px; flex:none; }
    .csb-leg .r b{ margin-left:auto; font-weight:600; color:#3b4a66; font-size:10.5px; }

    .csb-bd-h{ display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; gap:10px; flex-wrap:wrap; }
    .csb-bd-h .sub{ display:block; font-size:10px; color:#9aa3b2; }
    .csb-tabs{ display:flex; gap:4px; }
    .csb-tab{ font-size:10.5px; font-weight:600; border:1px solid #e7ecf4; background:#fff; color:#6b7488; border-radius:7px; padding:4px 10px; cursor:pointer; }
    .csb-tab.on{ background:#eef0ff; border-color:#c9cffc; color:#4f46e5; }
    .csb-tbl{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .csb-tbl thead th{ font-size:9.5px; font-weight:600; color:#9aa3b2; text-transform:uppercase; letter-spacing:.04em; text-align:left; padding:6px 10px; }
    .csb-tbl thead th:nth-child(2),.csb-tbl thead th:nth-child(3){ text-align:center; }
    .csb-tbl thead th:nth-child(4){ text-align:right; }
    .csb-grp-h td{ padding:8px 10px; }
    .csb-grp-h .gico{ display:inline-flex; vertical-align:middle; margin-right:8px; }
    .csb-grp-h .gico svg{ width:14px; height:14px; }
    .csb-grp-h .glabel{ font-size:10.5px; font-weight:800; letter-spacing:.05em; vertical-align:middle; }
    .csb-grp-h td.gtot{ text-align:right; font-size:12.5px; font-weight:800; }
    .csb-tbl tbody tr td{ padding:8px 10px; font-size:12px; border-top:1px solid #f4f6f9; vertical-align:middle; }
    .csb-tbl td.ev{ font-weight:500; color:#2b3756; }
    .csb-tbl .ev-c{ display:flex; align-items:center; gap:9px; min-width:0; }
    .csb-tbl .ev-nm{ overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .csb-tbl .ev-ic{ width:26px; height:26px; border-radius:7px; display:grid; place-items:center; flex:none; }
    .csb-tbl .ev-ic svg{ width:14px; height:14px; }
    .csb-tbl td.ppa,.csb-tbl td.oc{ text-align:center; }
    .csb-tbl td.ppa{ font-size:12px; font-weight:600; }
    .csb-tbl td.oc{ color:#3b4a66; font-weight:600; }
    .csb-tbl td.er{ text-align:right; font-weight:700; }

    .csb-side{ display:flex; flex-direction:column; gap:14px; }
    .csb-va{ font-size:10.5px; color:#2b59ff; text-decoration:none; font-weight:600; }
    .csb-lb-row{ display:flex; align-items:center; gap:9px; padding:7px 6px; border-radius:9px; }
    .csb-lb-row.me{ background:#eef2ff; }
    .csb-lb-row .rk{ font-size:11px; font-weight:700; color:#9aa3b2; width:14px; }
    .csb-lb-row .av{ width:27px; height:27px; border-radius:50%; color:#fff; font-size:10px; font-weight:700; display:grid; place-items:center; flex:none; }
    .csb-lb-row .nm{ flex:1; font-size:12px; font-weight:600; color:#2b3756; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .csb-lb-row.me .nm{ color:#2b59ff; }
    .csb-lb-row .sc{ font-size:12px; font-weight:800; }
    .csb-lg-row{ display:flex; align-items:center; gap:9px; padding:6px 4px; font-size:11px; opacity:.6; }
    .csb-lg-row.on{ opacity:1; }
    .csb-lg-row .th{ width:78px; color:#6b7488; font-size:10.5px; }
    .csb-lg-row .ic{ width:22px; height:22px; display:grid; place-items:center; flex:none; }
    .csb-lg-row .ic svg{ width:17px; height:17px; }
    .csb-lg-row .nm{ font-weight:600; }
    .csb-empty{ font-size:11px; color:#9aa3b2; text-align:center; padding:14px 0; }

    .csb-foot{ display:flex; align-items:center; justify-content:space-between; margin-top:14px; padding-top:12px; border-top:1px solid #eef1f7; gap:12px; flex-wrap:wrap; }
    .csb-foot .l{ display:flex; align-items:center; gap:7px; font-size:11px; color:#8a93a8; }
    .csb-foot .l svg{ width:15px; height:15px; }
    .csb-foot .r{ display:flex; align-items:center; gap:14px; }
    .csb-foot .el{ font-size:11px; color:#6b7488; }
    .csb-foot .el b{ padding:2px 9px; border-radius:20px; font-size:10px; margin-left:4px; }
    .csb-foot .el b.yes{ background:#e6f9ee; color:#16a34a; } .csb-foot .el b.no{ background:#fdecef; color:#e5484d; }
    .csb-cta{ display:inline-flex; align-items:center; gap:7px; font-size:11.5px; font-weight:600; color:#fff; background:linear-gradient(135deg,#5b3df5,#7c5cff); padding:9px 14px; border-radius:10px; text-decoration:none; }
    .csb-cta svg{ width:15px; height:15px; }

    @media (max-width:980px){
        .csb-top{ grid-template-columns:1fr; }
        .csb-rgrid{ grid-template-columns:repeat(2,1fr); }
        .csb-trend,.csb-ai{ grid-column:span 2; }
        .csb-bottom{ grid-template-columns:1fr; }
    }
</style>

<script>
    window.csbFilter = window.csbFilter || function (btn, sign) {
        var box = btn.closest('.csb-bd'); if (!box) return;
        box.querySelectorAll('.csb-tab').forEach(function (t) { t.classList.remove('on'); });
        btn.classList.add('on');
        box.querySelectorAll('tr[data-sign]').forEach(function (tr) {
            tr.style.display = (sign === 'all' || tr.getAttribute('data-sign') === sign) ? '' : 'none';
        });
        box.querySelectorAll('.csb-grp').forEach(function (gp) {
            var vis = 0;
            gp.querySelectorAll('tr[data-sign]').forEach(function (r) { if (r.style.display !== 'none') vis++; });
            gp.style.display = vis ? '' : 'none';
        });
    };
</script>
@endif
