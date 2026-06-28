{{-- Reusable Recruiter Score Board. Params: $U (user array), $totalCount (active recruiters), $mode ('internal'|'global'), $otherRank, $month, $leaders --}}
@php
    $U = $U ?? null;
    $mode = $mode ?? 'internal';
    $totalCount = (int) ($totalCount ?? 0);
    $otherRank = $otherRank ?? '-';
    $month = $month ?? date('M Y');
    $leaders = $leaders ?? collect();
@endphp
@if (empty($U))
    <div class="rsb" style="padding:46px 24px;text-align:center;color:#8a93a8;position:relative;">
        <div class="rsb-x" data-bs-dismiss="modal" style="position:absolute;top:14px;right:14px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"><rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="#0f1b3d"/><rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="#0f1b3d"/></svg></div>
        <p style="font-size:13px;margin:0;">No ranking data available yet for this period.</p>
    </div>
@else
@php
    $g = fn($k) => (int) round((float) ($U[$k] ?? 0));
    $sbScore = $g('score');
    $sbJoins = (int) round($g('candidateJoined') / 1500);
    $sbOffers = (int) round($g('offerReleased') / 300);
    $sbInterviews = (int) round($g('interviewScheduled') / 10);
    $sbInReview = $g('profileUpload');
    $sbRank = $U['rank'] ?? '-';
    $sbTotal = $totalCount;
    $sbPct = ($sbTotal > 0 && is_numeric($sbRank)) ? min(100, max(1, (int) ceil($sbRank / $sbTotal * 100))) : null;
    $sbTier = $sbScore >= 1000 ? 'Platinum' : ($sbScore >= 500 ? 'Gold' : ($sbScore >= 250 ? 'Silver' : 'Bronze'));
    $sbNext = $sbScore >= 1000 ? null : ($sbScore >= 500 ? ['Platinum', 1000] : ($sbScore >= 250 ? ['Gold', 500] : ['Silver', 250]));
    $sbProg = $sbNext ? min(100, (int) round($sbScore / $sbNext[1] * 100)) : 100;
    $primaryLbl = $mode === 'global' ? 'Global Rank' : 'Company Rank';
    $thirdLbl = $mode === 'global' ? 'Company Rank' : 'Global Rank';
    $thirdVal = $otherRank;

    // Score contribution buckets (donut)
    $cJoins = $g('candidateJoined'); $cOffers = $g('offerReleased'); $cInt = $g('interviewScheduled');
    $cPipe = $g('profileUpload') + $g('sourcing') + $g('screening') + $g('evaluation') + $g('negotiation');
    $sbContrib = [['Joining & Onboarding', $cJoins, '#22c55e'], ['Offers', $cOffers, '#2b59ff'], ['Interviews', $cInt, '#f5a623'], ['Others', $cPipe, '#8b5cf6']];
    $sbCT = max(1, array_sum(array_map(fn($x) => max(0, $x[1]), $sbContrib)));
    $sbDonut = ''; $cum = 0; $C = 2 * M_PI * 42;
    foreach ($sbContrib as $seg) { $val = max(0, $seg[1]); if ($val <= 0) continue; $len = $val / $sbCT * $C; $sbDonut .= '<circle cx="60" cy="60" r="42" fill="none" stroke="' . $seg[2] . '" stroke-width="16" stroke-dasharray="' . round($len, 2) . ' ' . round($C - $len, 2) . '" stroke-dashoffset="' . round(-$cum, 2) . '"/>'; $cum += $len; }
    if ($sbCT <= 1) { $sbDonut = '<circle cx="60" cy="60" r="42" fill="none" stroke="#eef1f7" stroke-width="16"/>'; }

    // Individual score-breakdown events: [label, description, points-per-action, earned-points]
    $sbInd = [['In Review','Candidates moved to in-review stage',1,$g('profileUpload')],['Sourcing','New candidates sourced',1,$g('sourcing')],['Screening','Candidates screened',1,$g('screening')],['Evaluation','Candidates evaluated',1,$g('evaluation')],['Negotiation','Candidates in negotiation',1,$g('negotiation')],['Offer Released','Offer released to candidate',300,$g('offerReleased')],['Joined & Onboarded','Candidate joined and onboarded',1500,$g('candidateJoined')],['Joined & Backed Out','Candidate backed out after joining',-1500,$g('backout')],['Interviews Conducted','Interviews conducted',10,$g('interviewScheduled')],['Interview No Show','Candidate no show',-5,$g('interviewNoShow')]];
    // Team-captain events (kept – real data, do not delete)
    $sbCap = [['Offer Released','Offers released by your team',20,$g('teamCaptainOfferReleased')],['Joined & Onboarded','Team members joined and onboarded',30,$g('teamCaptainCandidateJoined')],['Joined & Backed Out','Team members backed out',-30,$g('backoutTeam')],['Interviews Conducted','Team interviews conducted',2,$g('teamCaptainInterviewScheduled')]];

    // Group individual events into High Impact / Pipeline / Penalties for the breakdown card
    $grp = ['high' => ['High Impact Events', '#16a34a', []], 'pipe' => ['Pipeline Activity', '#2b59ff', []], 'pen' => ['Penalties', '#e5484d', []]];
    foreach ($sbInd as $e) { $ppa = $e[2]; if ($ppa < 0 && abs($ppa) < 10) { $grp['pen'][2][] = $e; } elseif (abs($ppa) >= 10) { $grp['high'][2][] = $e; } else { $grp['pipe'][2][] = $e; } }
    $grpTotal = fn($rows) => array_sum(array_map(fn($e) => (int) $e[3], $rows));

    $sbIndTotal = array_sum(array_map(fn($e) => (int) $e[3], $sbInd));
    $sbCapTotal = array_sum(array_map(fn($e) => (int) $e[3], $sbCap));

    // Achievements (derived from real activity)
    $sbAch = [
        ['Interview Machine','Conducted interviews', $cInt > 0, '#f08c00','#fff3e2'],
        ['Offer Closer','Released offers', $cOffers > 0, '#16a34a','#e6f9ee'],
        ['Rising Star','Earned score points', $sbScore > 0, '#f5a623','#fff7e6'],
        ['Active Recruiter','Moved the pipeline', $cPipe > 0, '#2b59ff','#e9f0ff'],
    ];
    $sbAchCount = count(array_filter($sbAch, fn($a) => $a[2]));

    // AI insights
    $sbConv = $sbInterviews > 0 ? (int) round($sbJoins / $sbInterviews * 100) : null;
    $sbBackout = (int) round(abs($g('backout')) / 1500);
    $sbAi = [];
    if ($sbConv !== null) { $sbAi[] = 'Your interview-to-join conversion is ' . $sbConv . '%' . ($sbConv >= 50 ? ' &mdash; excellent!' : '.'); }
    if ($sbNext) { $sbAi[] = number_format(max(0, $sbNext[1] - $sbScore)) . ' more points to reach ' . $sbNext[0] . ' level. Focus on offers &amp; joins to climb faster.'; }
    else { $sbAi[] = 'You have reached the top ' . $sbTier . ' tier &mdash; outstanding work! &#127881;'; }
    if (empty($sbAi)) { $sbAi[] = 'Start moving candidates through your pipeline to begin earning points.'; }

    // Score legend (matches the real tier thresholds)
    $sbLegend = [['Platinum Recruiter', 1000, '#a78bfa', $sbScore >= 1000], ['Gold Recruiter', 500, '#f59e0b', $sbScore >= 500 && $sbScore < 1000], ['Silver Recruiter', 250, '#94a3b8', $sbScore >= 250 && $sbScore < 500], ['Bronze Recruiter', 0, '#d97706', $sbScore < 250]];

    // Bonus eligibility (top 10% heuristic)
    $sbEligible = $sbPct !== null && $sbPct <= 10;

    // Leaderboard rows
    $sbLeaders = collect($leaders)->map(fn($x) => (array) $x)->filter(fn($x) => isset($x['name']))->sortBy('rank')->take(7)->values();
    $myId = $U['id'] ?? null;

    // Date range label
    try { $mc = \Carbon\Carbon::parse($month); $rangeTxt = $mc->copy()->startOfMonth()->format('M j') . ' – ' . $mc->copy()->endOfMonth()->format('M j, Y'); } catch (\Throwable $e) { $rangeTxt = $month; }
@endphp

<div class="rsb">
    {{-- ===== Header ===== --}}
    <div class="rsb-top">
        <div class="l">
            <span class="rsb-tico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"/><path d="M17 5h2.4a1.6 1.6 0 0 1 0 5H17M7 5H4.6a1.6 1.6 0 0 0 0 5H7"/><path d="M12 14v3.2M9 21h6M10.4 18.4h3.2L14 21h-4Z"/></svg></span>
            <div>
                <h3 class="rsb-ttl">Recruiter Score Board <span class="mo">{{ $month }}</span>@if($mode==='global')<span class="badge-g">Global</span>@endif</h3>
                <p class="rsb-sub">Your performance summary and score breakdown</p>
            </div>
        </div>
        <div class="r">
            <span class="rsb-pill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="16" rx="2.5"/><path d="M3 9h18M8 3v3M16 3v3"/></svg>{{ $rangeTxt }}</span>
            <a href="javascript:void(0)" class="rsb-btn" onclick="sbExportPdf(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M8 11l4 4 4-4M5 21h14"/></svg>Export</a>
            <div class="rsb-x" data-bs-dismiss="modal"><svg viewBox="0 0 24 24" fill="none"><rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"/><rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"/></svg></div>
        </div>
    </div>

    {{-- ===== Overview ===== --}}
    <div class="rsb-ov">
        <div class="rsb-hero">
            <div class="cap">FINAL SCORE</div>
            <div class="big">{{ number_format($sbScore) }}</div>
            @if($sbNext)<div class="delta">{{ $sbProg }}% to {{ $sbNext[0] }}</div>@else<div class="delta">Top tier reached &#127881;</div>@endif
            <div class="rsb-medal">&#127941;</div>
            <div class="rsb-tier">{{ $sbTier }} Recruiter</div>
        </div>

        <div class="rsb-ovr">
            {{-- Company / primary rank --}}
            <div class="rsb-card rsb-mini a-rankc">
                <div class="h"><span class="i" style="background:#f3ecff;color:#7c3aed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M9 13l-1.5 8L12 18l4.5 3L15 13"/></svg></span>{{ $primaryLbl }}</div>
                <div class="v">#{{ $sbRank }}</div>
                <div class="d muted">@if($sbTotal)of {{ number_format($sbTotal) }} recruiters @else&mdash;@endif</div>
            </div>
            {{-- Global / secondary rank --}}
            <div class="rsb-card rsb-mini a-rankg">
                <div class="h"><span class="i" style="background:#e9f0ff;color:#2b59ff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg></span>{{ $thirdLbl }}</div>
                <div class="v">#{{ $thirdVal }}</div>
                <div class="d muted">across all companies</div>
            </div>
            {{-- Percentile --}}
            <div class="rsb-card rsb-mini a-pct">
                <div class="h"><span class="i" style="background:#e6f9ee;color:#16a34a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 7a3 3 0 0 1 0 6M19 20a4 4 0 0 0-3-3.8"/></svg></span>Percentile</div>
                <div class="v">@if($sbPct)Top {{ $sbPct }}%@else&mdash;@endif</div>
                <div class="d muted">@if($sbPct)among active recruiters @else no rank yet @endif</div>
            </div>
            {{-- Score trend (illustrative) --}}
            <div class="rsb-card rsb-mini a-trend">
                <div class="h" style="margin-bottom:6px"><span class="i" style="background:#eef2ff;color:#4f46e5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l5-6 4 3 5-7 4 5"/></svg></span>Score Trend</div>
                <div class="rsb-spark">
                    <svg viewBox="0 0 200 54" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <defs><linearGradient id="rsbg{{ $mode }}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#4f46e5" stop-opacity=".22"/><stop offset="1" stop-color="#4f46e5" stop-opacity="0"/></linearGradient></defs>
                        <path d="M0,40 L34,36 L68,38 L102,28 L136,30 L170,18 L200,12 L200,54 L0,54 Z" fill="url(#rsbg{{ $mode }})"/>
                        <path d="M0,40 L34,36 L68,38 L102,28 L136,30 L170,18 L200,12" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="200" cy="12" r="3.4" fill="#4f46e5"/>
                    </svg>
                    <span class="rsb-spark-now">{{ number_format($sbScore) }}</span>
                </div>
            </div>

            {{-- Achievements --}}
            <div class="rsb-card a-ach">
                <div class="rsb-ch">
                    <span class="t"><svg viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3h14v4a7 7 0 0 1-14 0V3Z"/><path d="M9 21h6M12 14v7"/></svg> Achievements</span>
                    <span class="rsb-chip">{{ $sbAchCount }}/{{ count($sbAch) }}</span>
                </div>
                <div class="rsb-ach">
                    @foreach($sbAch as $a)
                        <div class="it {{ $a[2] ? 'on' : 'off' }}" title="{{ $a[0] }} — {{ $a[1] }}">
                            <span class="ic" style="@if($a[2])background:{{ $a[4] }};color:{{ $a[3] }}@endif"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="5"/><path d="M8.5 13.5L7 22l5-3 5 3-1.5-8.5"/></svg></span>
                            <span class="nm">{{ $a[0] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- AI insight --}}
            <div class="rsb-card rsb-ai a-ai">
                <div class="rsb-ch"><span class="t"><span class="rsb-ai-ic">AI</span> Jero Insight</span></div>
                <div class="bd">
                    @foreach(array_slice($sbAi, 0, 2) as $ins)
                        <p>{!! $ins !!}</p>
                    @endforeach
                </div>
            </div>

            {{-- Next milestone --}}
            <div class="rsb-card a-mile">
                <div class="rsb-ch">
                    <span class="t"><svg viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3v18M5 4h11l-2 3 2 3H5"/></svg> Next Milestone</span>
                    @if($sbNext)<span class="rsb-chip">{{ number_format(max(0, $sbNext[1] - $sbScore)) }} pts to go</span>@endif
                </div>
                <div class="rsb-mile">
                    <div class="lbl">{{ $sbNext ? $sbNext[0] . ' Recruiter' : $sbTier . ' Recruiter' }}</div>
                    <div class="bar"><i style="width:{{ $sbProg }}%"></i></div>
                    <div class="num">{{ number_format($sbScore) }} / {{ $sbNext ? number_format($sbNext[1]) : number_format($sbScore) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Main grid ===== --}}
    <div class="rsb-main">
        {{-- Score contribution --}}
        <div class="rsb-card rsb-contrib">
            <h4 class="rsb-h4">Score Contribution</h4>
            <div class="rsb-donut">
                <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">{!! $sbDonut !!}</svg>
                <div class="c"><b>{{ number_format($sbScore) }}</b><span>Total Score</span></div>
            </div>
            <div class="rsb-leg">
                @foreach ($sbContrib as $seg)
                    @php $pc = $sbCT > 0 ? round(max(0, $seg[1]) / $sbCT * 100) : 0; @endphp
                    <div class="r"><span class="d" style="background:{{ $seg[2] }}"></span><span class="nm">{{ $seg[0] }}</span><b>{{ $pc }}%</b></div>
                @endforeach
            </div>
        </div>

        {{-- Score breakdown --}}
        <div class="rsb-card rsb-bd">
            <div class="rsb-bd-h">
                <div><h4 class="rsb-h4" style="margin:0">Score Breakdown</h4><span class="rsb-bd-sub">Points earned per activity this period</span></div>
                <div class="rsb-tabs">
                    <button type="button" class="rsb-tab on" onclick="rsbFilter(this,'all')">All</button>
                    <button type="button" class="rsb-tab" onclick="rsbFilter(this,'pos')">Positive</button>
                    <button type="button" class="rsb-tab" onclick="rsbFilter(this,'zero')">Neutral</button>
                    <button type="button" class="rsb-tab" onclick="rsbFilter(this,'neg')">Negative</button>
                </div>
            </div>
            <div class="rsb-tbl-wrap">
                <table class="rsb-tbl">
                    @foreach($grp as $gk => $gv)
                        @if(count($gv[2]))
                            <tbody class="rsb-grp">
                                @php $gt = $grpTotal($gv[2]); @endphp
                                <tr class="rsb-grp-h"><td><span class="dot" style="background:{{ $gv[1] }}"></span>{{ $gv[0] }}</td><td></td><td></td><td class="r" style="color:{{ $gt < 0 ? '#e5484d' : '#16a34a' }}">{{ $gt >= 0 ? '+' : '' }}{{ number_format($gt) }}</td></tr>
                                @foreach($gv[2] as $e)
                                    @php $ppa=$e[2]; $earned=(int)$e[3]; $act=$ppa!=0?(int)round($earned/$ppa):0; $sign = $earned>0?'pos':($earned<0?'neg':'zero'); @endphp
                                    <tr data-sign="{{ $sign }}">
                                        <td class="ev"><b>{{ $e[0] }}</b><span>{{ $e[1] }}</span></td>
                                        <td class="ppa"><span class="{{ $ppa < 0 ? 'neg' : 'pos' }}">{{ $ppa > 0 ? '+' : '' }}{{ $ppa }}</span></td>
                                        <td class="oc">{{ number_format($act) }}</td>
                                        <td class="er {{ $earned>0?'pos':($earned<0?'neg':'zero') }}">{{ $earned>0?'+':'' }}{{ number_format($earned) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                    @endforeach
                </table>
            </div>
        </div>

        {{-- Side: leaderboard + legend --}}
        <div class="rsb-side">
            <div class="rsb-card rsb-lb">
                <div class="rsb-ch"><span class="t">Leaderboard ({{ $mode === 'global' ? 'Global' : 'Company' }})</span></div>
                @if($sbLeaders->count())
                    <div class="rsb-lb-list">
                        @foreach($sbLeaders as $l)
                            @php $ln = trim($l['name'] ?? ''); $ini = collect(explode(' ', $ln))->filter()->take(2)->map(fn($w)=>strtoupper(substr($w,0,1)))->implode(''); $me = $myId !== null && ($l['id'] ?? null) == $myId; @endphp
                            <div class="row {{ $me ? 'me' : '' }}">
                                <span class="rk">{{ $l['rank'] ?? '-' }}</span>
                                <span class="av">{{ $ini ?: '?' }}</span>
                                <span class="nm">{{ $me ? 'You' : $ln }}@if(($l['rank'] ?? 0) == 1) &#128081;@endif</span>
                                <span class="sc">{{ number_format((int) round((float) ($l['score'] ?? 0))) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="rsb-empty">No leaderboard data for this period.</p>
                @endif
            </div>

            <div class="rsb-card rsb-legend">
                <div class="rsb-ch"><span class="t">Score Legend</span></div>
                @foreach($sbLegend as $lg)
                    <div class="row {{ $lg[3] ? 'on' : '' }}">
                        <span class="ic" style="background:{{ $lg[1]==1000?'#f3ecff':($lg[1]==500?'#fff3e2':($lg[1]==250?'#eef1f7':'#fff1e6')) }};color:{{ $lg[2] }}">&#127941;</span>
                        <span class="nm">{{ $lg[0] }}</span>
                        <span class="th">{{ $lg[1] > 0 ? 'Score ≥ ' . number_format($lg[1]) : 'Score > 0' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== Team captain performance (real data – preserved) ===== --}}
    @if($sbCapTotal != 0 || collect($sbCap)->sum(fn($e) => (int) $e[3]) != 0)
    <div class="rsb-card rsb-cap">
        <div class="rsb-bd-h">
            <div><h4 class="rsb-h4" style="margin:0">Team Captain Performance</h4><span class="rsb-bd-sub">Points earned leading your team</span></div>
            <span class="rsb-pts {{ $sbCapTotal < 0 ? 'neg' : 'pos' }}">{{ $sbCapTotal >= 0 ? '+' : '' }}{{ number_format($sbCapTotal) }} pts</span>
        </div>
        <div class="rsb-tbl-wrap">
            <table class="rsb-tbl">
                <tbody>
                    @foreach($sbCap as $e)
                        @php $ppa=$e[2]; $earned=(int)$e[3]; $act=$ppa!=0?(int)round($earned/$ppa):0; @endphp
                        <tr>
                            <td class="ev"><b>{{ $e[0] }}</b><span>{{ $e[1] }}</span></td>
                            <td class="ppa"><span class="{{ $ppa < 0 ? 'neg' : 'pos' }}">{{ $ppa > 0 ? '+' : '' }}{{ $ppa }}</span></td>
                            <td class="oc">{{ number_format($act) }}</td>
                            <td class="er {{ $earned>0?'pos':($earned<0?'neg':'zero') }}">{{ $earned>0?'+':'' }}{{ number_format($earned) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ===== Footer ===== --}}
    <div class="rsb-foot">
        <div class="l"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l2.5 2.5"/></svg>Score is updated every 24 hours</div>
        <div class="r">
            <span class="el">Bonus Eligibility <b class="{{ $sbEligible ? 'yes' : 'no' }}">{{ $sbEligible ? 'Eligible' : 'Not Eligible' }}</b></span>
            <a href="javascript:void(0)" class="rsb-cta" onclick="sbExportPdf(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v5h5M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-5Z"/><path d="M9 13h6M9 17h4"/></svg>View Detailed Report</a>
        </div>
    </div>
</div>

<script>
    window.rsbFilter = window.rsbFilter || function (btn, sign) {
        var box = btn.closest('.rsb-bd'); if (!box) return;
        box.querySelectorAll('.rsb-tab').forEach(function (t) { t.classList.remove('on'); });
        btn.classList.add('on');
        box.querySelectorAll('tr[data-sign]').forEach(function (tr) {
            tr.style.display = (sign === 'all' || tr.getAttribute('data-sign') === sign) ? '' : 'none';
        });
        box.querySelectorAll('.rsb-grp').forEach(function (gp) {
            var vis = 0;
            gp.querySelectorAll('tr[data-sign]').forEach(function (r) { if (r.style.display !== 'none') vis++; });
            gp.style.display = vis ? '' : 'none';
        });
    };
</script>
@endif
