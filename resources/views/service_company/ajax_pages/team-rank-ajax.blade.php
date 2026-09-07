@php
  $rkMonths = collect($rankings['months'] ?? [])->values();
  $rkCur    = $rkMonths->get(0);
  $curI  = $rankings['internalRanks'][$rkCur]  ?? ['rank'=>'-','score'=>0];
  // trend (oldest -> newest, last 12)
  $rkTrend  = $rkMonths->slice(0,12)->reverse()->values();
  $rkTLabels = $rkTrend->map(fn($m)=>date('M y', strtotime($m.'-01')))->all();
  $rkTRanks = $rkTrend->map(fn($m)=> (is_numeric($rankings['internalRanks'][$m]['rank'] ?? null) ? (int)$rankings['internalRanks'][$m]['rank'] : null))->all();
  $rkValidRanks = array_filter($rkTRanks, fn($v)=>$v!==null);
  $rkMaxRank = $rkValidRanks ? max($rkValidRanks) : 10;
  // journey milestones derived from real ranks (oldest first)
  $asc = $rkMonths->reverse()->values();
  $mTop1=$mTop5=$mTop10=$mJoin=null;
  foreach($asc as $m){
    $r = $rankings['internalRanks'][$m]['rank'] ?? null;
    if($mJoin===null) $mJoin=$m;
    if($mTop10===null && is_numeric($r) && $r<=10) $mTop10=$m;
    if($mTop5===null  && is_numeric($r) && $r<=5)  $mTop5=$m;
    if($mTop1===null  && is_numeric($r) && $r==1)  $mTop1=$m;
  }
  $fmtM = fn($m)=>$m?date('M Y',strtotime($m.'-01')):'—';
  $lbTop   = collect($leaderboard['top'] ?? []);
  $lbMe    = $leaderboard['me'] ?? null;
  // monthly history with rank-change columns (rows newest-first)
  $mArr=$rkMonths->values()->all(); $hist=[];
  for($i=0;$i<count($mArr);$i++){
    $m=$mArr[$i]; $older=$mArr[$i+1]??null;
    $ir=$rankings['internalRanks'][$m]??['rank'=>'-','score'=>0];
    $gr=$rankings['globalRanks'][$m]??['rank'=>'-','score'=>0];
    $oi=$older?($rankings['internalRanks'][$older]??null):null;
    $og=$older?($rankings['globalRanks'][$older]??null):null;
    $sc=is_numeric($ir['score'])?$ir['score']:0;
    $osc=($oi&&is_numeric($oi['score']))?$oi['score']:null;
    $scoreChg=$osc===null?null:$sc-$osc;
    $scorePct=($osc&&$osc>0)?round($scoreChg/$osc*100,1):null;
    $iChg=($oi&&is_numeric($oi['rank'])&&is_numeric($ir['rank']))?($oi['rank']-$ir['rank']):null;
    $gChg=($og&&is_numeric($og['rank'])&&is_numeric($gr['rank']))?($og['rank']-$gr['rank']):null;
    $hist[]=compact('m','ir','gr','sc','scoreChg','scorePct','iChg','gChg');
  }
  $rkSpark = '<svg viewBox="0 0 80 32" preserveAspectRatio="none"><polyline points="0,26 14,20 28,23 42,12 56,15 70,6 80,9" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
@endphp

<div class="srk-wrap">
  <!-- Stat cards -->
  <div class="srk-stats">
    <div class="srk-stat"><span class="srk-st-ic ic-ind"><i class="fas fa-users"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Candidates Added</div><div class="srk-st-num">{{ number_format($total_candidates ?? 0) }}</div></div><span class="srk-spark ind">{!! $rkSpark !!}</span></div>
    <div class="srk-stat"><span class="srk-st-ic ic-grn"><i class="fas fa-calendar-check"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Interviews Conducted</div><div class="srk-st-num">{{ number_format($interviewsTrackerTotal ?? 0) }}</div></div><span class="srk-spark grn">{!! $rkSpark !!}</span></div>
    <div class="srk-stat"><span class="srk-st-ic ic-vio"><i class="fas fa-briefcase"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Offers Made</div><div class="srk-st-num">{{ number_format($scpOffersTotal ?? 0) }}</div></div><span class="srk-spark vio">{!! $rkSpark !!}</span></div>
    <div class="srk-stat"><span class="srk-st-ic ic-amb"><i class="fas fa-handshake"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Joinings</div><div class="srk-st-num">{{ number_format($total_candidates_join ?? 0) }}</div></div><span class="srk-spark amb">{!! $rkSpark !!}</span></div>
  </div>

  <!-- Leaderboard + Trend + Journey -->
  <div class="srk-mid3">
    <!-- Leaderboard -->
    <div class="srk-card">
      <div class="srk-head"><span class="srk-head-t">Leaderboard <small>(Top 5)</small></span></div>
      @php $lbTop5 = $lbTop->take(5); @endphp
      @if($lbTop5->count())
        <div class="srk-lb">
          @foreach($lbTop5 as $lrow)
            @php $isMe = (int)($lrow['id'] ?? 0) === (int)(@$users->id ?? -1); $rk=$lrow['rank']; @endphp
            <div class="srk-lb-row {{ $isMe?'me':'' }}">
              <span class="srk-lb-rk {{ $rk==1?'g1':($rk==2?'g2':($rk==3?'g3':'')) }}">{{ $rk<=3 ? '' : $rk }}@if($rk<=3)<i class="fas fa-medal"></i>@endif</span>
              @if(!empty($lrow['image']))
                <img class="srk-lb-ava" src="{{ $lrow['image'] }}" alt="">
              @else
                <span class="srk-lb-ava srk-lb-ini">{{ strtoupper(substr($lrow['name'] ?? 'U',0,1)) }}</span>
              @endif
              <span class="srk-lb-name">{{ $lrow['name'] ?? 'User' }} @if($isMe)<em class="srk-lb-you">You</em>@endif</span>
              <span class="srk-lb-score">{{ $lrow['score'] ?? 0 }}</span>
            </div>
          @endforeach
        </div>
        @if($lbMe && !$lbTop5->pluck('id')->contains($lbMe['id']))
          <div class="srk-lb-row me sep">
            <span class="srk-lb-rk">{{ $lbMe['rank'] }}</span>
            <span class="srk-lb-ava srk-lb-ini">{{ strtoupper(substr($lbMe['name'] ?? 'U',0,1)) }}</span>
            <span class="srk-lb-name">{{ $lbMe['name'] ?? 'You' }} <em class="srk-lb-you">You</em></span>
            <span class="srk-lb-score">{{ $lbMe['score'] ?? 0 }}</span>
          </div>
        @endif
      @else
        <div class="srk-empty">Leaderboard data is not available yet.</div>
      @endif
    </div>

    <!-- Rank Trend -->
    <div class="srk-card">
      <div class="srk-head"><span class="srk-head-t">Rank Trend</span><span class="srk-head-sub">Internal · {{ count($rkTLabels) }} months</span></div>
      <div class="srk-chart"><canvas id="srk_trend_chart"></canvas></div>
      @if($mTop1)
        <div class="srk-trend-note"><i class="fas fa-check-circle"></i> You reached #1 internally in {{ $fmtM($mTop1) }}. Keep up the great work!</div>
      @endif
    </div>

    <!-- Rank Journey -->
    <div class="srk-card">
      <div class="srk-head"><span class="srk-head-t">Rank Journey</span></div>
      <ul class="srk-journey">
        <li class="{{ $mTop1?'done':'todo' }}"><span class="srk-jd ic-amb"><i class="fas fa-crown"></i></span><div><div class="srk-j-t">Reached #1 Internally</div><div class="srk-j-s">{{ $mTop1 ? $fmtM($mTop1).' · top recruiter' : 'Not yet reached' }}</div></div></li>
        <li class="{{ $mTop5?'done':'todo' }}"><span class="srk-jd ic-ind"><i class="fas fa-chart-line"></i></span><div><div class="srk-j-t">Entered Top 5</div><div class="srk-j-s">{{ $mTop5 ? $fmtM($mTop5) : 'Not yet reached' }}</div></div></li>
        <li class="{{ $mTop10?'done':'todo' }}"><span class="srk-jd ic-vio"><i class="fas fa-star"></i></span><div><div class="srk-j-t">Entered Top 10</div><div class="srk-j-s">{{ $mTop10 ? $fmtM($mTop10) : 'Not yet reached' }}</div></div></li>
        <li class="done"><span class="srk-jd ic-grn"><i class="fas fa-flag"></i></span><div><div class="srk-j-t">Joined Leaderboard</div><div class="srk-j-s">{{ $fmtM($mJoin) }} · started your journey</div></div></li>
      </ul>
    </div>
  </div>

  <!-- History -->
  <div class="srk-card">
    <div class="srk-head"><span class="srk-head-t">Monthly Performance History</span><span class="srk-head-sub">Track your growth and performance over time</span></div>
    <div class="srk-table-wrap">
      <table class="srk-table">
        <thead><tr><th>Month</th><th>Score</th><th>Score Change</th><th>Internal Rank</th><th>Rank Change (Int.)</th><th>Global Rank</th><th>Rank Change (Global)</th></tr></thead>
        <tbody>
          @foreach($hist as $h)
            <tr>
              <td class="srk-td-m">{{ date('M Y', strtotime($h['m'].'-01')) }}</td>
              <td class="srk-td-s">{{ $h['sc'] }}</td>
              <td>
                @if($h['scoreChg']===null)<span class="srk-flat">—</span>
                @elseif($h['scoreChg']>=0)<span class="srk-up"><i class="fas fa-arrow-up"></i> {{ $h['scoreChg'] }}{{ $h['scorePct']!==null?' ('.$h['scorePct'].'%)':'' }}</span>
                @else<span class="srk-down"><i class="fas fa-arrow-down"></i> {{ abs($h['scoreChg']) }}{{ $h['scorePct']!==null?' ('.$h['scorePct'].'%)':'' }}</span>@endif
              </td>
              <td class="srk-td-r">#{{ $h['ir']['rank'] }}</td>
              <td>
                @if($h['iChg']===null)<span class="srk-flat">—</span>
                @elseif($h['iChg']>0)<span class="srk-up"><i class="fas fa-arrow-up"></i> {{ $h['iChg'] }}</span>
                @elseif($h['iChg']<0)<span class="srk-down"><i class="fas fa-arrow-down"></i> {{ abs($h['iChg']) }}</span>
                @else<span class="srk-flat">—</span>@endif
              </td>
              <td class="srk-td-r">#{{ $h['gr']['rank'] }}</td>
              <td>
                @if($h['gChg']===null)<span class="srk-flat">—</span>
                @elseif($h['gChg']>0)<span class="srk-up"><i class="fas fa-arrow-up"></i> {{ $h['gChg'] }}</span>
                @elseif($h['gChg']<0)<span class="srk-down"><i class="fas fa-arrow-down"></i> {{ abs($h['gChg']) }}</span>
                @else<span class="srk-flat">—</span>@endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  (function(){
    function drawSrk(){
      if(typeof Chart==='undefined') return;
      var el=document.getElementById('srk_trend_chart'); if(!el) return;
      if(window.teamCharts && window.teamCharts.rankTrend) {
        window.teamCharts.rankTrend.destroy();
      }
      var ctx=el.getContext('2d');
      var g=ctx.createLinearGradient(0,0,0,220);
      g.addColorStop(0,'rgba(59,85,230,0.22)'); g.addColorStop(1,'rgba(59,85,230,0.02)');
      var maxR={{ ($rkMaxRank ?? 10) + 1 }};
      window.teamCharts = window.teamCharts || {};
      window.teamCharts.rankTrend = new Chart(ctx,{
        type:'line',
        data:{
          labels:{!! json_encode($rkTLabels) !!},
          datasets:[{
            data:{!! json_encode($rkTRanks) !!},
            borderColor:'#3b55e6',
            backgroundColor:g,
            borderWidth:2,
            fill:true,
            tension:0.4,
            pointRadius:3,
            pointBackgroundColor:'#3b55e6',
            spanGaps:true
          }]
        },
        options:{
          responsive:true,
          maintainAspectRatio:false,
          plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return 'Rank #'+c.parsed.y;}}}},
          scales:{x:{grid:{display:false},ticks:{font:{size:11,family:'Poppins'}}},y:{reverse:true,min:1,suggestedMax:maxR,ticks:{stepSize:1,precision:0,font:{size:11,family:'Poppins'},callback:function(v){return '#'+v;}},grid:{color:'#f0f2f7'}}}
        }
      });
    }
    setTimeout(drawSrk, 60);
  })();
</script>
