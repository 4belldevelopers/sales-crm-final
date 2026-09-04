@php
  $sdbActive    = (int)($scp_active ?? $counts->get('active', 0) ?? 0);
  $sdbHold      = (int)($counts->get('hold', 0));
  $sdbClosed    = (int)($counts->get('inactive', 0));
  $sdbAdded     = (int)($total_candidates ?? 0);
  $sdbInReview  = (int)($inreview_count ?? 0);
  $sdbSourcing  = (int)($sourcing_count ?? 0);
  $sdbScreen    = (int)($screening_count ?? 0);
  $sdbEval      = (int)($evaluation_count ?? 0);
  $sdbNego      = (int)($negotiation_count ?? 0);
  $sdbOffer     = (int)($scpOffersTotal ?? $offer_count ?? 0);
  $sdbJoined    = (int)($joined_count ?? 0);
  $sdbReject    = (int)($Rejected_count ?? 0);
  $sdbInterviews= (int)($interviewsTrackerTotal ?? 0);
  $sdbConv      = $sdbAdded > 0 ? round($sdbJoined / $sdbAdded * 100, 1) : 0;
  $sdbStages    = [
    ['In Review', $sdbInReview, '#bdbd26'],
    ['Sourcing', $sdbSourcing, '#000000'],
    ['Screening', $sdbScreen, '#c000ff'],
    ['Evaluation', $sdbEval, '#2896ff'],
    ['Negotiation', $sdbNego, '#ff4098'],
    ['Offer', $sdbOffer, '#ffab00'],
    ['Joined', $sdbJoined, '#06c732'],
    ['Rejected', $sdbReject, '#ff0202']
  ];
@endphp

<div class="sdb-wrap">
  <div class="sdb-top">
    <div>
      <div class="sdb-title">My Dashboard <span>&#128075;</span></div>
      <div class="sdb-sub">Here's what's happening with your recruitment.</div>
    </div>
  </div>
  <div class="sdb-stats">
    <div class="sdb-stat"><span class="sdb-st-ic ic-ind"><i class="fas fa-briefcase"></i></span><div class="sdb-st-lbl">Active Jobs</div><div class="sdb-st-num">{{ $sdbActive }}</div></div>
    <div class="sdb-stat"><span class="sdb-st-ic ic-grn"><i class="fas fa-users"></i></span><div class="sdb-st-lbl">Candidates Added</div><div class="sdb-st-num">{{ $sdbAdded }}</div></div>
    <div class="sdb-stat"><span class="sdb-st-ic ic-blu"><i class="fas fa-calendar-check"></i></span><div class="sdb-st-lbl">Interviews</div><div class="sdb-st-num">{{ $sdbInterviews }}</div></div>
    <div class="sdb-stat"><span class="sdb-st-ic ic-amb"><i class="fas fa-file-signature"></i></span><div class="sdb-st-lbl">Offers</div><div class="sdb-st-num">{{ $sdbOffer }}</div></div>
    <div class="sdb-stat"><span class="sdb-st-ic ic-vio"><i class="fas fa-handshake"></i></span><div class="sdb-st-lbl">Joinings</div><div class="sdb-st-num">{{ $sdbJoined }}</div></div>
    <div class="sdb-stat"><span class="sdb-st-ic ic-rose"><i class="fas fa-chart-line"></i></span><div class="sdb-st-lbl">Joining Rate</div><div class="sdb-st-num">{{ $sdbConv }}%</div></div>
  </div>
  <div class="sdb-mid">
    <div class="sdb-card">
      <div class="sdb-head">
        <span class="sdb-head-t">Recruitment Pipeline</span>
      </div>
      <div class="sdb-funnel">
        @foreach($sdbStages as $i => $stg)
          @php $w = max(34, 100 - $i*8.5); @endphp
          <div class="sdb-fn-row">
            <div class="sdb-fn-bar" style="width:{{ $w }}%;background:{{ $stg[2] }};">{{ $stg[1] }}</div>
            <div class="sdb-fn-lbl"><span>{{ $stg[0] }}</span><small>Candidates</small></div>
          </div>
        @endforeach
      </div>
      <div class="sdb-fn-foot">
        <div><div class="sdb-ff-lbl">Total Added</div><div class="sdb-ff-num">{{ $sdbAdded }}</div></div>
        <div><div class="sdb-ff-lbl">Total Joined</div><div class="sdb-ff-num">{{ $sdbJoined }}</div></div>
        <div><div class="sdb-ff-lbl">Conversion Rate</div><div class="sdb-ff-num sdb-green">{{ $sdbConv }}%</div></div>
      </div>
    </div>
    <div class="sdb-card">
      <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-bolt sdb-bolt"></i> Action Center</span></div>
      <div class="sdb-ac">
        <div class="sdb-ac-row"><span class="sdb-ac-ic ic-ind"><i class="fas fa-briefcase"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Active Jobs</div><div class="sdb-ac-s">Jobs currently open for hiring</div></div><span class="sdb-ac-n">{{ $sdbActive }}</span></div>
        <div class="sdb-ac-row"><span class="sdb-ac-ic ic-amb"><i class="fas fa-file-signature"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Offer Stage</div><div class="sdb-ac-s">Candidates currently at offer stage</div></div><span class="sdb-ac-n">{{ $sdbOffer }}</span></div>
        <div class="sdb-ac-row"><span class="sdb-ac-ic ic-vio"><i class="fas fa-comments-dollar"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">In Negotiation</div><div class="sdb-ac-s">Candidates in salary negotiation</div></div><span class="sdb-ac-n">{{ $sdbNego }}</span></div>
        <div class="sdb-ac-row"><span class="sdb-ac-ic ic-grn"><i class="fas fa-handshake"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Joined</div><div class="sdb-ac-s">Candidates who have joined</div></div><span class="sdb-ac-n">{{ $sdbJoined }}</span></div>
        <div class="sdb-ac-row"><span class="sdb-ac-ic ic-rose"><i class="fas fa-pause-circle"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Jobs on Hold</div><div class="sdb-ac-s">Jobs temporarily paused</div></div><span class="sdb-ac-n">{{ $sdbHold }}</span></div>
      </div>
    </div>
  </div>
  <div class="sdb-charts">
    <div class="sdb-card">
      <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-file-signature ic-t-amb"></i> Offer Trends</span><span class="sdb-head-sub">Last 6 Months</span></div>
      <div class="sdb-chart"><canvas id="sdb_offer_ajax"></canvas></div>
    </div>
    <div class="sdb-card">
      <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-handshake ic-t-grn"></i> Joinings Trend</span><span class="sdb-head-sub">Last 6 Months</span></div>
      <div class="sdb-chart"><canvas id="sdb_join_ajax"></canvas></div>
    </div>
    <div class="sdb-card">
      <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-calendar-check ic-t-vio"></i> Interviews Trend</span><span class="sdb-head-sub">Last 6 Months</span></div>
      <div class="sdb-chart"><canvas id="sdb_interview_ajax"></canvas></div>
    </div>
    <div class="sdb-card">
      <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-users ic-t-blu"></i> Candidates Added</span><span class="sdb-head-sub">Last 7 Days</span></div>
      <div class="sdb-chart"><canvas id="sdb_candidates_ajax"></canvas></div>
    </div>
  </div>
</div>

<script>
  (function(){
    window.teamCharts = window.teamCharts || {};

    function sdbArea(ctx, color, labels, data){
      var g = ctx.createLinearGradient(0,0,0,200);
      g.addColorStop(0, color + '40');
      g.addColorStop(1, color + '05');
      return new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            borderColor: color,
            backgroundColor: g,
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: color,
            pointBorderColor: '#fff',
            pointBorderWidth: 1.5
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11, family: 'Poppins' } } },
            y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11, family: 'Poppins' } }, grid: { color: '#f0f2f7' } }
          }
        }
      });
    }

    function sdbBar(ctx, color, labels, data){
      return new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: color,
            borderRadius: 6,
            borderSkipped: false,
            barThickness: 20,
            maxBarThickness: 26
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10, family: 'Poppins' } } },
            y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11, family: 'Poppins' } }, grid: { color: '#f0f2f7' } }
          }
        }
      });
    }

    function drawSdbAjax(){
      if (typeof Chart === 'undefined') return;

      var off = document.getElementById('sdb_offer_ajax');
      if (off) {
        if (window.teamCharts.sdbOffer && typeof window.teamCharts.sdbOffer.destroy === 'function') {
          try { window.teamCharts.sdbOffer.destroy(); } catch(e) {}
          window.teamCharts.sdbOffer = null;
        }
        window.teamCharts.sdbOffer = sdbArea(off.getContext('2d'), '#f59e0b', {!! json_encode($labelsCommoffer ?? []) !!}, {!! json_encode($candidateValuesCommoffer ?? []) !!});
      }

      var jn = document.getElementById('sdb_join_ajax');
      if (jn) {
        if (window.teamCharts.sdbJoin && typeof window.teamCharts.sdbJoin.destroy === 'function') {
          try { window.teamCharts.sdbJoin.destroy(); } catch(e) {}
          window.teamCharts.sdbJoin = null;
        }
        window.teamCharts.sdbJoin = sdbArea(jn.getContext('2d'), '#16a34a', {!! json_encode($labelsCommjoin ?? []) !!}, {!! json_encode($candidateValuesCommjoin ?? []) !!});
      }

      var iv = document.getElementById('sdb_interview_ajax');
      if (iv) {
        if (window.teamCharts.sdbInterview && typeof window.teamCharts.sdbInterview.destroy === 'function') {
          try { window.teamCharts.sdbInterview.destroy(); } catch(e) {}
          window.teamCharts.sdbInterview = null;
        }
        window.teamCharts.sdbInterview = sdbArea(iv.getContext('2d'), '#7c4dff', {!! json_encode($interviewlabelsMonth ?? []) !!}, {!! json_encode($interviewValuesMonth ?? []) !!});
      }

      var cn = document.getElementById('sdb_candidates_ajax');
      if (cn) {
        if (window.teamCharts.sdbCandidates && typeof window.teamCharts.sdbCandidates.destroy === 'function') {
          try { window.teamCharts.sdbCandidates.destroy(); } catch(e) {}
          window.teamCharts.sdbCandidates = null;
        }
        window.teamCharts.sdbCandidates = sdbBar(cn.getContext('2d'), '#2196f3', {!! json_encode($labels ?? []) !!}, {!! json_encode($candidateValues ?? []) !!});
      }
    }

    setTimeout(drawSdbAjax, 60);
  })();
</script>
