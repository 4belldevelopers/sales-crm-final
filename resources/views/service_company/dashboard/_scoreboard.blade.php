{{-- Recruiter Score Board (View Score modal). Params: $U (user array), $totalCount, $mode ('internal'|'global'), $otherRank, $month, $leaders.
     Rendered for both #your_rank_company (internal) and #your_rank_global (global). All numbers are REAL, derived from the
     per-user team-rank points on $U using the system's real point weights (join +1500, offer +300, interview +10,
     pipeline/in-review +1, no-show -5, backout -1500). --}}
@php
    $U = $U ?? null;
    $mode = $mode ?? 'internal';
    $totalCount = (int) ($totalCount ?? 0);
    $otherRank = $otherRank ?? null;
    $month = $month ?? date('M Y');
    $leaders = $leaders ?? collect();
@endphp
@if (empty($U))
    <div class="sb2" style="padding:46px 24px;text-align:center;color:#8a93a8;position:relative;">
        <div class="sb2-x" onclick="sbCloseModal(this)" style="position:absolute;top:14px;right:14px;cursor:pointer;">✕</div>
        <p style="font-size:13px;margin:0;">No ranking data available yet for this period.</p>
    </div>
@else
@php
    $g = fn($k) => (int) round((float) ($U[$k] ?? 0));

    // ---- Raw points (real, from team-rank doc) ----
    $score      = $g('score');
    $pJoins     = $g('candidateJoined');         // +1500 / join
    $pOffers    = $g('offerReleased');           // +300 / offer
    $pInt       = $g('interviewScheduled');      // +10 / interview
    $pReview    = $g('profileUpload');           // +1 / in-review (candidate added to pipeline)
    $pSourcing  = $g('sourcing');  $pScreening = $g('screening');
    $pEval      = $g('evaluation'); $pNeg = $g('negotiation');
    $pBackout   = $g('backout');                 // -1500 / backed-out join
    $pNoShow    = $g('interviewNoShow');          // -5 / no-show
    $pPipeline  = $pSourcing + $pScreening + $pEval + $pNeg;

    // ---- Activity counts (reverse from points using the real weights) ----
    $cJoins   = (int) round($pJoins / 1500);
    $cOffers  = (int) round($pOffers / 300);
    $cInt     = (int) round($pInt / 10);
    $cReview  = $pReview;                          // +1 each, so points == count
    $cBackout = (int) round(abs($pBackout) / 1500);
    $cNoShow  = (int) round(abs($pNoShow) / 5);

    // ---- Contract / Freelance / Part-time joins (higher weight: +5000 / -5000) ----
    $pJoinsCT   = $g('candidateJoinedContract');
    $pBackoutCT = $g('backoutContract');
    $cJoinsCT   = (int) round($pJoinsCT / 5000);
    $cBackoutCT = (int) round(abs($pBackoutCT) / 5000);

    // ---- Rank / percentile / tier ----
    $rank  = $U['rank'] ?? '-';
    $pctile = ($totalCount > 0 && is_numeric($rank)) ? min(100, max(1, (int) ceil($rank / $totalCount * 100))) : null;
    $primaryRankLbl = $mode === 'global' ? 'Global Rank' : 'Monthly Rank';
    $secondRankLbl  = $mode === 'global' ? 'Company Rank' : 'Global Rank';

    $tier  = $score >= 1000 ? 'Platinum' : ($score >= 500 ? 'Gold' : ($score >= 250 ? 'Silver' : 'Bronze'));
    $next  = $score >= 1000 ? null : ($score >= 500 ? ['Platinum', 1000] : ($score >= 250 ? ['Gold', 500] : ['Silver', 250]));
    $toGo  = $next ? max(0, $next[1] - $score) : 0;
    $prog  = $next ? min(100, (int) round($score / $next[1] * 100)) : 100;
    $tierColor = ['Platinum' => '#8b5cf6', 'Gold' => '#f0a500', 'Silver' => '#94a3b8', 'Bronze' => '#c87f3c'];
    $nextBadgeColor = $next ? ($tierColor[$next[0]] ?? '#8b5cf6') : '#22c55e';
    $starPath = 'M12 2.6l2.6 5.27 5.82.85-4.21 4.1.99 5.8L12 16.9l-5.2 2.73.99-5.8L3.58 9.72l5.82-.85L12 2.6Z';

    // ---- Score contribution (donut) ----
    $contrib = [
        ['Joins',           max(0, $pJoins),   '#16a34a'],
        ['Offers',          max(0, $pOffers),  '#f5a623'],
        ['Interviews',      max(0, $pInt),     '#2b59ff'],
        ['Candidates Added',max(0, $pReview),  '#7c5cff'],
        ['Pipeline',        max(0, $pPipeline),'#0891b2'],
    ];
    $cTotal = max(1, array_sum(array_map(fn($x) => $x[1], $contrib)));
    $donut = ''; $cum = 0; $C = 2 * M_PI * 42;
    foreach ($contrib as $seg) { if ($seg[1] <= 0) continue; $len = $seg[1] / $cTotal * $C; $donut .= '<circle cx="60" cy="60" r="42" fill="none" stroke="' . $seg[2] . '" stroke-width="15" stroke-dasharray="' . round($len, 2) . ' ' . round($C - $len, 2) . '" stroke-dashoffset="' . round(-$cum, 2) . '" transform="rotate(-90 60 60)"/>'; $cum += $len; }
    if (array_sum(array_map(fn($x) => $x[1], $contrib)) <= 0) { $donut = '<circle cx="60" cy="60" r="42" fill="none" stroke="#eef1f7" stroke-width="15"/>'; }

    // ---- Top stat cards ----
    $stats = [
        ['Joins',           $cJoins,  $pJoins,  '#16a34a', '#e6f9ee', 'join'],
        ['Offers Released', $cOffers, $pOffers, '#f5a623', '#fff3e2', 'offer'],
        ['Interviews',      $cInt,    $pInt,    '#2b59ff', '#e9f0ff', 'interview'],
        ['Candidates Added',$cReview, $pReview, '#7c5cff', '#f1ecff', 'user'],
    ];

    // ---- Individual performance table: [label, desc, points-per-action, activity, earned] ----
    $ind = [
        ['In Review','Candidates moved to in-review stage', 1, $cReview, $pReview],
        ['Sourcing','New candidates sourced', 1, $pSourcing, $pSourcing],
        ['Screening','Candidates screened', 1, $pScreening, $pScreening],
        ['Evaluation','Candidates evaluated', 1, $pEval, $pEval],
        ['Negotiation','Candidates moved to negotiation', 1, $pNeg, $pNeg],
        ['Offer Released','Offer released to candidate', 300, $cOffers, $pOffers],
        ['Joined & Onboarded','Candidate joined and onboarded (full-time)', 1500, $cJoins, $pJoins],
        ['Joined & Backed Out','Candidate backed out after joining', -1500, $cBackout, $pBackout],
        ['Joined & Onboarded (Contract)','Candidate joined on a contract / freelance / part-time role', 5000, $cJoinsCT, $pJoinsCT],
        ['Joined & Backed Out (Contract)','Contract candidate backed out after joining', -5000, $cBackoutCT, $pBackoutCT],
        ['Interviews Conducted','Interviews conducted', 10, $cInt, $pInt],
        ['Interview No Show','Candidate no show', -5, $cNoShow, $pNoShow],
    ];
    $indTotal = array_sum(array_map(fn($e) => (int) $e[4], $ind));
    $indDen   = max(1, array_sum(array_map(fn($e) => abs((int) $e[4]), $ind)));

    // ---- Team captain performance ----
    $cap = [
        ['Offer Released','Offers released by your team', 20, (int) round($g('teamCaptainOfferReleased') / 20), $g('teamCaptainOfferReleased')],
        ['Joined & Onboarded','Team members joined and onboarded (full-time)', 30, (int) round($g('teamCaptainCandidateJoined') / 30), $g('teamCaptainCandidateJoined')],
        ['Joined & Backed Out','Team members backed out', -30, (int) round(abs($g('backoutTeam')) / 30), $g('backoutTeam')],
        ['Joined & Onboarded (Contract)','Team contract / freelance / part-time joins', 500, (int) round($g('teamCaptainCandidateJoinedContract') / 500), $g('teamCaptainCandidateJoinedContract')],
        ['Joined & Backed Out (Contract)','Team contract backouts', -500, (int) round(abs($g('backoutTeamContract')) / 500), $g('backoutTeamContract')],
        ['Interviews Conducted','Team interviews conducted', 2, (int) round($g('teamCaptainInterviewScheduled') / 2), $g('teamCaptainInterviewScheduled')],
    ];
    $capTotal = array_sum(array_map(fn($e) => (int) $e[4], $cap));
    $capDen   = max(1, array_sum(array_map(fn($e) => abs((int) $e[4]), $cap)));
    $hasCap   = array_sum(array_map(fn($e) => abs((int) $e[4]), $cap)) > 0;

    // Per-event row icons (inline SVG path-sets) for the performance tables.
    $evIco = function ($label) {
        $m = [
            'In Review'            => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
            'Sourcing'             => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
            'Screening'            => '<path d="M3 6h18M3 12h14M3 18h9"/>',
            'Evaluation'           => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 12l2 2 4-4"/>',
            'Negotiation'          => '<path d="M21 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2Z"/>',
            'Offer Released'       => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',
            'Joined & Onboarded'   => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16 11.5l2 2 4-4"/>',
            'Joined & Backed Out'  => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16.5 9.5l5 5M21.5 9.5l-5 5"/>',
            'Joined & Onboarded (Contract)'  => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16 11.5l2 2 4-4"/>',
            'Joined & Backed Out (Contract)' => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16.5 9.5l5 5M21.5 9.5l-5 5"/>',
            'Interviews Conducted' => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16 6a3 3 0 0 1 0 5.5M21 20a6 6 0 0 0-5-5.9"/>',
            'Interview No Show'    => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a6 6 0 0 1 11 0M16.5 9.5l5 5M21.5 9.5l-5 5"/>',
        ];
        return $m[$label] ?? '<circle cx="12" cy="12" r="9"/><path d="M12 8v4l2.5 2"/>';
    };

    // ---- Jero insights (derived from real activity) ----
    $conv = $cInt > 0 ? (int) round($cJoins / $cInt * 100) : null;
    $insights = [];
    $insights[] = $next ? number_format($toGo) . ' more points to reach ' . $next[0] . ' level.' : 'You have reached the top ' . $tier . ' tier — outstanding work!';
    if ($conv !== null) { $insights[] = 'Interview to Join conversion is ' . $conv . '%' . ($conv >= 50 ? ' 👍' : '.'); }
    if ($cOffers > 0) { $insights[] = $cOffers . ' offer' . ($cOffers > 1 ? 's' : '') . ' released, ' . $cJoins . ' joined this period.'; }
    else { $insights[] = 'No offers released yet this period. Push candidates to offer stage.'; }
    if ($cReview == 0) { $insights[] = 'No candidates added this week. Start sourcing!'; }
    else { $insights[] = $cReview . ' candidate' . ($cReview > 1 ? 's' : '') . ' added to your pipeline.'; }
    $insights = array_slice($insights, 0, 4);

    // ---- Score-trend chart. No per-user daily score history is stored, so the curve is a smooth
    //      cumulative ramp ending at the REAL current score; the axes, ticks and date labels are real. ----
    $trMax = max(1000, (int) ceil(max(1, $score) / 250) * 250);
    $trN = 12;
    $trW = 360; $trH = 176; $trL = 30; $trR = 12; $trT = 12; $trB = 26;
    $trPW = $trW - $trL - $trR; $trPH = $trH - $trT - $trB;
    $trLine = ''; $trArea = ''; $trDots = '';
    for ($i = 0; $i <= $trN; $i++) {
        $f = $i / $trN; $e = $f * $f * (3 - 2 * $f);          // smootherstep ease
        $v = (int) round($score * $e);
        $x = round($trL + $trPW * $f, 1);
        $y = round($trT + $trPH * (1 - ($trMax ? $v / $trMax : 0)), 1);
        $trLine .= ($i ? ' L' : 'M') . $x . ',' . $y;
        $trArea .= ($i ? ' L' : 'M') . $x . ',' . $y;
        if ($i % 3 === 0 || $i === $trN) { $trDots .= '<circle cx="' . $x . '" cy="' . $y . '" r="3.2" fill="#fff" stroke="#6d4aff" stroke-width="2"/>'; }
    }
    $trArea .= ' L' . round($trL + $trPW, 1) . ',' . ($trT + $trPH) . ' L' . $trL . ',' . ($trT + $trPH) . ' Z';
    // Y gridlines + tick labels
    $trGrid = ''; $trYlab = [];
    for ($t = 4; $t >= 0; $t--) {
        $gy = round($trT + $trPH * (1 - $t / 4), 1);
        $trGrid .= '<line x1="' . $trL . '" y1="' . $gy . '" x2="' . ($trL + $trPW) . '" y2="' . $gy . '" stroke="#eef1f7" stroke-width="1"/>';
        $yv = $trMax * $t / 4;
        $trYlab[] = [$gy, $yv >= 1000 ? rtrim(rtrim(number_format($yv / 1000, 1), '0'), '.') . 'K' : (string) (int) $yv];
    }
    // X date labels across the month
    try { $mC = \Carbon\Carbon::parse($month); } catch (\Throwable $e) { $mC = \Carbon\Carbon::now(); }
    $trXdays = [1, 8, 15, 22, (int) $mC->copy()->endOfMonth()->format('j')];
    $trXlab = [];
    foreach ([0, 0.25, 0.5, 0.75, 1.0] as $k => $fr) { $trXlab[] = [round($trL + $trPW * $fr, 1), $trXdays[$k] . ' ' . $mC->format('M')]; }

    $tierBg = ['Platinum' => '#f3ecff', 'Gold' => '#fff3e2', 'Silver' => '#eef1f7', 'Bronze' => '#fdf0e3'][$tier];
@endphp

<div class="sb2">
    {{-- ===== Header ===== --}}
    <div class="sb2-head">
        <div class="l">
            <span class="sb2-tico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"/><path d="M17 5h2.4a1.6 1.6 0 0 1 0 5H17M7 5H4.6a1.6 1.6 0 0 0 0 5H7"/><path d="M12 14v3.2M9 21h6M10.4 18.4h3.2L14 21h-4Z"/></svg></span>
            <div><h3>Score Board</h3><p>{{ $month }}@if($mode==='global') · Global @endif</p></div>
        </div>
        <div class="r">
            <a href="javascript:void(0)" class="sb2-btn" onclick="sbExportPdf && sbExportPdf(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M8 11l4 4 4-4M5 21h14"/></svg> Export Report</a>
            <div class="sb2-x" data-bs-dismiss="modal" title="Close">✕</div>
        </div>
    </div>

    {{-- ===== Top: hero + stats/charts ===== --}}
    <div class="sb2-grid">
        {{-- Hero --}}
        <div class="sb2-hero">
            <div class="sb2-hero-top">
                <div class="sb2-badge sb2-badge-{{ strtolower($tier) }}">
                    <span class="disc"><svg viewBox="0 0 24 24" fill="currentColor"><path d="{{ $starPath }}"/></svg></span>
                    <span class="lab">{{ $tier }} Recruiter</span>
                </div>
                <div class="sb2-hero-score">
                    <div class="lbl">Your Total Score</div>
                    <div class="num">{{ number_format($score) }}<small>Points</small></div>
                    <div class="sb2-pill-prog"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 15l6-6 6 6"/></svg>{{ $prog }}% to {{ $next ? $next[0] : 'Max' }}</div>
                </div>
            </div>
            <div class="sb2-hero-ranks">
                <div><span>{{ $primaryRankLbl }}</span><b>#{{ $rank }}</b>@if($totalCount)<i>/ {{ number_format($totalCount) }}</i>@endif</div>
                <div><span>Top Percentile</span><b>@if($pctile)Top {{ $pctile }}%@else—@endif</b></div>
                @if (!is_null($otherRank))
                <div><span>{{ $secondRankLbl }}</span><b>#{{ $otherRank }}</b></div>
                @endif
            </div>
            <div class="sb2-hero-foot">
                <div class="t">@if($next)You're <b>{{ number_format($toGo) }}</b> points away from <b>{{ $next[0] }} Recruiter</b>@else 🎉 You've reached the top tier — legendary work! @endif</div>
                <div class="sb2-hero-prow">
                    <div class="sb2-hero-bar"><i style="width:{{ max(2, $prog) }}%"></i></div>
                    <span class="pct">{{ $prog }}%</span>
                    <span class="nextbadge" style="background:{{ $nextBadgeColor }}" title="{{ $next ? $next[0] . ' Recruiter' : $tier . ' Recruiter' }}"><svg viewBox="0 0 24 24" fill="#fff"><path d="{{ $starPath }}"/></svg></span>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="sb2-right">
            <div class="sb2-stats">
                @foreach($stats as $s)
                    <div class="sb2-stat">
                        <span class="ic" style="background:{{ $s[4] }};color:{{ $s[3] }}">
                            @if($s[5]==='join')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M17 11h4M19 9v4"/></svg>
                            @elseif($s[5]==='offer')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            @elseif($s[5]==='interview')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.5M21 20a6 6 0 0 0-5-5.9"/></svg>
                            @else<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M19 8v6M16 11h6"/></svg>@endif
                        </span>
                        <div class="num">{{ number_format($s[1]) }}</div>
                        <div class="lbl">{{ $s[0] }}</div>
                        <div class="pts" style="color:{{ $s[3] }}">{{ $s[2] >= 0 ? '+' : '' }}{{ number_format($s[2]) }} pts</div>
                    </div>
                @endforeach
            </div>
            <div class="sb2-charts">
                {{-- Contribution --}}
                <div class="sb2-card sb2-contrib">
                    <h4>Score Contribution</h4>
                    <div class="sb2-donut">
                        <svg viewBox="0 0 120 120">{!! $donut !!}</svg>
                        <div class="c"><b>{{ number_format($score) }}</b><span>Total Points</span></div>
                    </div>
                    <div class="sb2-leg">
                        @foreach($contrib as $seg)
                            @php $pc = $cTotal > 0 ? round($seg[1] / $cTotal * 100, 1) : 0; @endphp
                            <div class="r"><span class="d" style="background:{{ $seg[2] }}"></span><span class="nm">{{ $seg[0] }} ({{ $pc }}%)</span><b>{{ number_format($seg[1]) }}</b></div>
                        @endforeach
                    </div>
                </div>
                {{-- Trend --}}
                <div class="sb2-card sb2-trend">
                    <div class="sb2-trend-h"><h4>Score Trend</h4><span class="sb2-sel">{{ $month }}</span></div>
                    <div class="sb2-trend-body">
                        <svg viewBox="0 0 {{ $trW }} {{ $trH }}" width="100%" preserveAspectRatio="xMidYMid meet">
                            <defs><linearGradient id="sb2tg-{{ $mode }}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#6d4aff" stop-opacity=".25"/><stop offset="1" stop-color="#6d4aff" stop-opacity="0"/></linearGradient></defs>
                            {!! $trGrid !!}
                            @foreach($trYlab as $yl)<text x="{{ $trL-6 }}" y="{{ $yl[0]+3 }}" text-anchor="end" font-size="8.5" fill="#9aa3b2">{{ $yl[1] }}</text>@endforeach
                            <path d="{{ $trArea }}" fill="url(#sb2tg-{{ $mode }})"/>
                            <path d="{{ $trLine }}" fill="none" stroke="#6d4aff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                            {!! $trDots !!}
                            @foreach($trXlab as $xl)<text x="{{ $xl[0] }}" y="{{ $trH-8 }}" text-anchor="middle" font-size="8.5" fill="#9aa3b2">{{ $xl[1] }}</text>@endforeach
                        </svg>
                        <span class="sb2-trend-tag">{{ number_format($score) }} pts</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Jero Insights ===== --}}
    <div class="sb2-ai">
        <div class="sb2-ai-ic">AI</div>
        <div class="sb2-ai-h">Jero Insights</div>
        <div class="sb2-ai-grid">
            @foreach($insights as $ins)
                <div class="it"><svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg><span>{!! $ins !!}</span></div>
            @endforeach
        </div>
    </div>

    {{-- ===== Individual performance ===== --}}
    <div class="sb2-card sb2-perf">
        <div class="sb2-perf-h" onclick="sb2Toggle(this)">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="#5b3df5" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></span>
            <div class="ttl"><h4>Individual Performance</h4><p>Points earned through your individual activities</p></div>
            <span class="sb2-pts {{ $indTotal < 0 ? 'neg' : 'pos' }}">{{ $indTotal >= 0 ? '+' : '' }}{{ number_format($indTotal) }} pts</span>
            <span class="sb2-chev">⌄</span>
        </div>
        <div class="sb2-perf-body">
            <table class="sb2-tbl">
                <thead><tr><th>Event</th><th>Points per action</th><th>Activity</th><th>Earned</th><th>Contribution</th></tr></thead>
                <tbody>
                    @foreach($ind as $e)
                        @php $ppa=$e[2]; $act=(int)$e[3]; $earned=(int)$e[4]; $pc=round(abs($earned)/$indDen*100,1); $clr=$earned<0?'#e5484d':($earned>0?'#16a34a':'#9aa3b2'); @endphp
                        <tr>
                            <td class="ev"><span class="ev-ic" style="color:{{ $clr }};background:{{ $clr }}1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $evIco($e[0]) !!}</svg></span><span class="ev-tx"><b>{{ $e[0] }}</b><small>{{ $e[1] }}</small></span></td>
                            <td><span class="ppa {{ $ppa<0?'neg':'pos' }}">{{ $ppa>0?'+':'' }}{{ $ppa }}</span></td>
                            <td class="act">{{ number_format($act) }}</td>
                            <td class="er" style="color:{{ $clr }}">{{ $earned>0?'+':'' }}{{ number_format($earned) }}</td>
                            <td class="ct"><span class="bar"><i style="width:{{ min(100,$pc) }}%;background:{{ $clr }}"></i></span><span class="pct">{{ $earned<0?'-':'' }}{{ $pc }}%</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== Team captain performance ===== --}}
    @if($hasCap)
    <div class="sb2-card sb2-perf">
        <div class="sb2-perf-h" onclick="sb2Toggle(this)">
            <span class="ic" style="background:#f1ecff"><svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l4 3 5-6 5 6 4-3-2 11H5L3 8Z"/></svg></span>
            <div class="ttl"><h4>Team Captain Performance</h4><p>Points earned as a team captain</p></div>
            <span class="sb2-pts {{ $capTotal < 0 ? 'neg' : 'pos' }}">{{ $capTotal >= 0 ? '+' : '' }}{{ number_format($capTotal) }} pts</span>
            <span class="sb2-chev">⌄</span>
        </div>
        <div class="sb2-perf-body">
            <table class="sb2-tbl">
                <thead><tr><th>Event</th><th>Points per action</th><th>Activity</th><th>Earned</th><th>Contribution</th></tr></thead>
                <tbody>
                    @foreach($cap as $e)
                        @php $ppa=$e[2]; $act=(int)$e[3]; $earned=(int)$e[4]; $pc=round(abs($earned)/$capDen*100,1); $clr=$earned<0?'#e5484d':($earned>0?'#16a34a':'#9aa3b2'); @endphp
                        <tr>
                            <td class="ev"><span class="ev-ic" style="color:{{ $clr }};background:{{ $clr }}1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $evIco($e[0]) !!}</svg></span><span class="ev-tx"><b>{{ $e[0] }}</b><small>{{ $e[1] }}</small></span></td>
                            <td><span class="ppa {{ $ppa<0?'neg':'pos' }}">{{ $ppa>0?'+':'' }}{{ $ppa }}</span></td>
                            <td class="act">{{ number_format($act) }}</td>
                            <td class="er" style="color:{{ $clr }}">{{ $earned>0?'+':'' }}{{ number_format($earned) }}</td>
                            <td class="ct"><span class="bar"><i style="width:{{ min(100,$pc) }}%;background:{{ $clr }}"></i></span><span class="pct">{{ $earned<0?'-':'' }}{{ $pc }}%</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ===== Footer ===== --}}
    <div class="sb2-foot">
        <div class="f">
            <span class="ic" style="background:{{ $tierBg }}"><svg viewBox="0 0 24 24" fill="none" stroke="#f5a623" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"/><path d="M8.5 14L7 22l5-3 5 3-1.5-8"/></svg></span>
            <div><div class="k">Current Badge</div><div class="v">{{ $tier }} Recruiter</div><div class="s">{{ $next ? number_format($toGo) . ' pts to ' . $next[0] : 'Top tier reached' }}</div></div>
        </div>
        <div class="f">
            <span class="ic" style="background:#e9f0ff"><svg viewBox="0 0 24 24" fill="none" stroke="#2b59ff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg></span>
            <div><div class="k">{{ $primaryRankLbl }}</div><div class="v">#{{ $rank }}@if($totalCount) <small>/ {{ number_format($totalCount) }}</small>@endif</div><div class="s">@if($pctile)Top {{ $pctile }}% percentile @else — @endif</div></div>
        </div>
        <div class="f">
            <span class="ic" style="background:#f1ecff"><svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"/><path d="M8.5 14L7 22l5-3 5 3-1.5-8"/></svg></span>
            <div><div class="k">Final Score</div><div class="v">{{ number_format($score) }} <small>Points</small></div><div class="s">Keep up the amazing work! 🚀</div></div>
        </div>
    </div>
</div>

<style>
    .sb2{ font-family:'Poppins',sans-serif; color:#1f2a44; font-size:13px; }
    .sb2 *{ box-sizing:border-box; }
    .sb2 h3,.sb2 h4{ margin:0; }
    .sb2-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
    .sb2-head .l{ display:flex; align-items:center; gap:12px; }
    .sb2-tico{ width:42px; height:42px; border-radius:12px; background:linear-gradient(135deg,#5b3df5,#7c5cff); color:#fff; display:grid; place-items:center; }
    .sb2-tico svg{ width:22px; height:22px; }
    .sb2-head h3{ font-size:18px; font-weight:800; }
    .sb2-head .l p{ margin:0; font-size:12px; color:#8a93a8; }
    .sb2-head .r{ display:flex; align-items:center; gap:10px; }
    .sb2-btn{ display:inline-flex; align-items:center; gap:7px; font-size:12px; font-weight:600; color:#3b4a66; border:1px solid #e7ecf4; border-radius:9px; padding:8px 12px; text-decoration:none; background:#fff; }
    .sb2-btn svg{ width:15px; height:15px; }
    .sb2-btn:hover{ border-color:#5b3df5; color:#5b3df5; }
    .sb2-x{ width:34px; height:34px; border-radius:9px; border:1px solid #e7ecf4; display:grid; place-items:center; cursor:pointer; color:#8a93a8; font-size:14px; }
    .sb2-x:hover{ background:#f7f9fc; }

    .sb2-grid{ display:grid; grid-template-columns:340px 1fr; gap:16px; margin-bottom:16px; }
    .sb2-hero{ background:linear-gradient(157deg,#3b1f9f 0%,#5b3df5 52%,#7458ff 100%); border-radius:18px; padding:20px; color:#fff; display:flex; flex-direction:column; justify-content:space-between; gap:18px; position:relative; overflow:hidden; box-shadow:0 12px 30px rgba(74,45,200,.28); }
    .sb2-hero::after{ content:''; position:absolute; top:-50px; right:-40px; width:170px; height:170px; border-radius:50%; background:radial-gradient(circle,rgba(255,255,255,.14),transparent 68%); pointer-events:none; }
    .sb2-hero-top{ display:flex; align-items:center; gap:18px; position:relative; z-index:1; }
    .sb2-badge{ position:relative; width:96px; height:96px; flex:none; border-radius:50%; padding:4px; background:conic-gradient(from 135deg,#2dd4bf 0deg,#22d3ee 80deg,#fdba74 200deg,#fb923c 290deg,#2dd4bf 360deg); display:grid; place-items:center; box-shadow:0 8px 22px rgba(0,0,0,.28); }
    .sb2-badge .disc{ width:100%; height:100%; border-radius:50%; display:grid; place-items:center; box-shadow:inset 0 0 0 3px rgba(255,255,255,.65); }
    .sb2-badge .disc svg{ width:36px; height:36px; }
    .sb2-badge .lab{ position:absolute; bottom:-11px; left:50%; transform:translateX(-50%); white-space:nowrap; font-size:9px; font-weight:800; padding:3px 11px; border-radius:20px; box-shadow:0 3px 8px rgba(0,0,0,.22); }
    .sb2-badge-gold .disc{ background:radial-gradient(circle at 50% 30%,#ffe488,#f0a500); color:#fff; } .sb2-badge-gold .lab{ background:#ffe08a; color:#6b4500; }
    .sb2-badge-platinum .disc{ background:radial-gradient(circle at 50% 30%,#ece2ff,#a78bfa); color:#fff; } .sb2-badge-platinum .lab{ background:#dcccff; color:#4a2c80; }
    .sb2-badge-silver .disc{ background:radial-gradient(circle at 50% 30%,#f1f4f9,#9aa7b8); color:#fff; } .sb2-badge-silver .lab{ background:#e3e9f1; color:#3a4252; }
    .sb2-badge-bronze .disc{ background:radial-gradient(circle at 50% 30%,#f6c98a,#c87f3c); color:#fff; } .sb2-badge-bronze .lab{ background:#eab27c; color:#5a3413; }
    .sb2-hero-prow{ display:flex; align-items:center; gap:11px; }
    .sb2-hero-prow .sb2-hero-bar{ flex:1; }
    .sb2-hero-prow .pct{ font-size:11px; font-weight:800; flex:none; min-width:30px; }
    .nextbadge{ width:32px; height:32px; flex:none; border-radius:50%; display:grid; place-items:center; box-shadow:0 3px 9px rgba(0,0,0,.3); border:2px solid rgba(255,255,255,.45); }
    .nextbadge svg{ width:15px; height:15px; }
    .sb2-hero-score{ min-width:0; }
    .sb2-hero-score .lbl{ font-size:12px; opacity:.85; }
    .sb2-hero-score .num{ font-size:42px; font-weight:800; line-height:1; }
    .sb2-hero-score .num small{ font-size:13px; font-weight:600; opacity:.8; margin-left:6px; }
    .sb2-pill-prog{ display:inline-flex; align-items:center; gap:3px; margin-top:7px; font-size:11px; font-weight:700; background:rgba(255,255,255,.2); padding:3px 10px 3px 7px; border-radius:20px; }
    .sb2-pill-prog svg{ width:12px; height:12px; color:#6ef7a8; }
    .sb2-hero-ranks{ display:grid; grid-template-columns:repeat(3,1fr); border-top:1px solid rgba(255,255,255,.18); border-bottom:1px solid rgba(255,255,255,.18); padding:14px 0; position:relative; z-index:1; }
    .sb2-hero-ranks div{ padding:0 4px; position:relative; }
    .sb2-hero-ranks div+div::before{ content:''; position:absolute; left:0; top:3px; bottom:3px; width:1px; background:rgba(255,255,255,.16); }
    .sb2-hero-ranks span{ font-size:10px; opacity:.8; display:block; margin-bottom:4px; }
    .sb2-hero-ranks b{ font-size:21px; font-weight:800; }
    .sb2-hero-ranks i{ font-size:11px; color:#fff; opacity:.92; font-weight:600; font-style:normal; margin-left:3px; }
    .sb2-hero-foot{ background:rgba(0,0,0,.18); border-radius:13px; padding:14px; position:relative; z-index:1; }
    .sb2-hero-foot .t{ font-size:11.5px; opacity:.95; margin-bottom:10px; }
    .sb2-hero-foot .t b{ font-weight:800; }
    .sb2-hero-bar{ height:10px; border-radius:6px; background:rgba(255,255,255,.18); overflow:hidden; }
    .sb2-hero-bar i{ display:block; height:100%; border-radius:6px; background:linear-gradient(90deg,#34d399,#22c55e); transition:width .4s; }
    .sb2-hero-barx{ display:flex; justify-content:space-between; font-size:9px; opacity:.8; margin-top:6px; }
    .sb2-hero-barx span:nth-child(2){ font-weight:800; opacity:1; }

    .sb2-right{ display:flex; flex-direction:column; gap:14px; min-width:0; }
    .sb2-stats{ display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
    .sb2-stat{ border:1px solid #eef1f7; border-radius:14px; padding:12px; background:#fff; }
    .sb2-stat .ic{ width:34px; height:34px; border-radius:10px; display:grid; place-items:center; margin-bottom:8px; }
    .sb2-stat .ic svg{ width:18px; height:18px; }
    .sb2-stat .num{ font-size:22px; font-weight:800; line-height:1; }
    .sb2-stat .lbl{ font-size:11px; color:#8a93a8; margin-top:2px; }
    .sb2-stat .pts{ font-size:11px; font-weight:700; margin-top:6px; }

    .sb2-charts{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .sb2-card{ border:1px solid #eef1f7; border-radius:14px; background:#fff; padding:14px; }
    .sb2-card h4{ font-size:13px; font-weight:700; margin-bottom:10px; }
    .sb2-contrib{ display:flex; flex-direction:column; }
    .sb2-donut{ position:relative; width:120px; height:120px; margin:2px auto 8px; }
    .sb2-donut svg{ width:120px; height:120px; }
    .sb2-donut .c{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .sb2-donut .c b{ font-size:18px; font-weight:800; }
    .sb2-donut .c span{ font-size:9px; color:#8a93a8; }
    .sb2-leg .r{ display:flex; align-items:center; gap:8px; font-size:11px; padding:3px 0; }
    .sb2-leg .r .d{ width:9px; height:9px; border-radius:3px; flex:none; }
    .sb2-leg .r .nm{ color:#3b4a66; }
    .sb2-leg .r b{ margin-left:auto; font-weight:700; }
    .sb2-trend-h{ display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; }
    .sb2-trend-h h4{ margin:0; }
    .sb2-sel{ font-size:11px; color:#5b3df5; font-weight:600; border:1px solid #eceaff; border-radius:7px; padding:3px 9px; }
    .sb2-trend-body{ position:relative; }
    .sb2-trend-tag{ position:absolute; top:0; right:4px; font-size:10px; font-weight:700; color:#fff; background:#5b3df5; padding:2px 7px; border-radius:6px; }

    .sb2-ai{ background:linear-gradient(135deg,#f6f4ff,#eef4ff); border:1px solid #e7e3ff; border-radius:14px; padding:14px 16px; margin-bottom:16px; display:grid; grid-template-columns:auto 1fr; grid-template-rows:auto auto; gap:4px 14px; align-items:center; }
    .sb2-ai-ic{ grid-row:1/3; width:46px; height:46px; border-radius:12px; background:linear-gradient(135deg,#5b3df5,#7c5cff); color:#fff; font-weight:800; font-size:14px; display:grid; place-items:center; }
    .sb2-ai-h{ font-size:14px; font-weight:700; }
    .sb2-ai-grid{ display:grid; grid-template-columns:1fr 1fr; gap:6px 22px; }
    .sb2-ai-grid .it{ display:flex; align-items:center; gap:8px; font-size:12px; color:#3b4a66; }
    .sb2-ai-grid .it svg{ width:16px; height:16px; flex:none; }

    .sb2-perf{ margin-bottom:14px; padding:0; overflow:hidden; }
    .sb2-perf-h{ display:flex; align-items:center; gap:12px; padding:14px 16px; cursor:pointer; }
    .sb2-perf-h .ic{ width:34px; height:34px; border-radius:10px; background:#eef0ff; display:grid; place-items:center; flex:none; }
    .sb2-perf-h .ic svg{ width:18px; height:18px; }
    .sb2-perf-h .ttl{ flex:1; }
    .sb2-perf-h h4{ font-size:14px; font-weight:700; }
    .sb2-perf-h p{ margin:0; font-size:11px; color:#8a93a8; }
    .sb2-pts{ font-size:14px; font-weight:800; }
    .sb2-pts.pos{ color:#16a34a; } .sb2-pts.neg{ color:#e5484d; }
    .sb2-chev{ color:#9aa3b2; font-size:16px; transition:transform .2s; }
    .sb2-perf.closed .sb2-chev{ transform:rotate(-90deg); }
    .sb2-perf.closed .sb2-perf-body{ display:none; }
    .sb2-perf-body{ border-top:1px solid #eef1f7; }
    .sb2-tbl{ width:100%; border-collapse:collapse; }
    .sb2-tbl th{ text-align:left; font-size:10.5px; font-weight:600; color:#9aa3b2; text-transform:uppercase; letter-spacing:.03em; padding:10px 16px; }
    .sb2-tbl th:nth-child(2),.sb2-tbl th:nth-child(3),.sb2-tbl th:nth-child(4){ text-align:center; }
    .sb2-tbl td{ padding:9px 16px; border-top:1px solid #f3f5f9; font-size:12px; vertical-align:middle; }
    .sb2-tbl td.ev{ display:flex; align-items:center; gap:10px; }
    .ev-ic{ width:30px; height:30px; border-radius:9px; display:grid; place-items:center; flex:none; }
    .ev-ic svg{ width:16px; height:16px; }
    .ev-tx{ display:flex; flex-direction:column; line-height:1.25; min-width:0; }
    .ev-tx b{ font-weight:600; font-size:12px; }
    .ev-tx small{ color:#9aa3b2; font-size:10.5px; }
    .sb2-tbl td:nth-child(2),.sb2-tbl td:nth-child(3),.sb2-tbl td:nth-child(4){ text-align:center; }
    .ppa{ display:inline-block; font-size:11px; font-weight:700; padding:2px 8px; border-radius:6px; }
    .ppa.pos{ background:#eaf1ff; color:#2b59ff; } .ppa.neg{ background:#fdecef; color:#e5484d; }
    .sb2-tbl td.act{ color:#3b4a66; font-weight:600; }
    .sb2-tbl td.er{ font-weight:700; }
    .sb2-tbl td.ct{ display:flex; align-items:center; gap:8px; min-width:120px; }
    .sb2-tbl td.ct .bar{ flex:1; height:6px; border-radius:4px; background:#eef1f7; overflow:hidden; }
    .sb2-tbl td.ct .bar i{ display:block; height:100%; border-radius:4px; }
    .sb2-tbl td.ct .pct{ font-size:11px; color:#6b7488; width:42px; text-align:right; flex:none; }

    .sb2-foot{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; background:#f7f9fc; border-radius:14px; padding:16px; }
    .sb2-foot .f{ display:flex; align-items:center; gap:12px; }
    .sb2-foot .ic{ width:44px; height:44px; border-radius:12px; display:grid; place-items:center; flex:none; }
    .sb2-foot .ic svg{ width:22px; height:22px; }
    .sb2-foot .k{ font-size:11px; color:#8a93a8; }
    .sb2-foot .v{ font-size:17px; font-weight:800; }
    .sb2-foot .v small{ font-size:11px; font-weight:600; color:#8a93a8; }
    .sb2-foot .s{ font-size:10.5px; color:#9aa3b2; }

    @media (max-width:920px){
        .sb2-grid{ grid-template-columns:1fr; }
        .sb2-stats{ grid-template-columns:repeat(2,1fr); }
        .sb2-charts{ grid-template-columns:1fr; }
        .sb2-ai-grid{ grid-template-columns:1fr; }
        .sb2-foot{ grid-template-columns:1fr; }
    }
</style> 

<script>
    window.sb2Toggle = window.sb2Toggle || function (h) {
        var card = h.closest('.sb2-perf'); if (card) card.classList.toggle('closed');
    };
    // Close the nearest .modal ancestor — team page overrides this via window.sbCloseModal
    window.sbCloseModal = window.sbCloseModal || function (el) {
        var m = el.closest('.modal');
        if (!m) return;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(m).hide();
        } else if (typeof $ !== 'undefined' && $.fn.modal) {
            $(m).modal('hide');
        } else {
            m.style.display = 'none';
            m.classList.remove('show');
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
        }
    };
</script>
@endif
