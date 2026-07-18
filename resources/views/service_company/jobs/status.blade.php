<?php
/*
 * Job Status drawer body — AJAX-injected into #status_body_content (see
 * service_company.jobs.index's #status_drawer). Same business rule as before
 * (Hiring=active enables the career page + vendor submissions + candidate
 * applications; Hold/Closed disable them), presented as: hero job summary,
 * status cards, and an Impact Preview comparison table. No sidebar/timeline/
 * stats panel by design — kept to exactly what the approved layout shows.
 */
$statusVal = @$job->status;

$statusMeta = [
    'active' => [
        'label' => 'Hiring', 'icon' => 'fa-user-group', 'color' => '#17c653', 'bg' => '#eafcf3',
        'desc' => 'Receiving Applications',
        'bullets' => ['Career page visible', 'Vendors can submit profiles', 'Candidates can apply', 'All automations active'],
        'career' => 'Visible', 'vendor' => 'Enabled', 'candidates' => 'Open', 'interviews' => 'All Active', 'offers' => 'Allowed', 'notify' => 'Active',
    ],
    'hold' => [
        'label' => 'Hold', 'icon' => 'fa-pause', 'color' => '#ffa800', 'bg' => '#fff8ec',
        'desc' => 'Pause Hiring',
        'bullets' => ['Removed from career page', 'Vendor submission disabled', 'Applications are paused', 'Existing data is retained'],
        'career' => 'Hidden', 'vendor' => 'Disabled', 'candidates' => 'Paused', 'interviews' => 'Existing Only', 'offers' => 'Existing Only', 'notify' => 'Paused',
    ],
    'inactive' => [
        'label' => 'Closed', 'icon' => 'fa-box-archive', 'color' => '#f1416c', 'bg' => '#fdedf0',
        'desc' => 'Finish Recruitment',
        'bullets' => ['Stop all new applications', 'Vendor submission disabled', 'Job will be archived', 'Reports & analytics retained'],
        'career' => 'Hidden', 'vendor' => 'Disabled', 'candidates' => 'Closed', 'interviews' => 'Existing Only', 'offers' => 'Existing Only', 'notify' => 'Stopped',
    ],
];

$currentMeta = $statusMeta[$statusVal] ?? null;

$workType = 'Work From Office';
if (@$job->work_type == 'yes') { $workType = 'Remote'; }
elseif (@$job->work_type == 'other') { $workType = 'Hybrid'; }

$cityName = @$jobLocation->city_name ?: @$jobLocation->city;

// ---- Hero card data (salary, client badge, avatar initial) ----
$currencyIcons = \App\Helper\GeneralMethod::currencySymbole();
$salaryModeArr = \App\Helper\GeneralMethod::salaryMode();
$currencyIcon = $currencyIcons[$job->min_salary_currency ?? 0] ?? $currencyIcons[0];
$salaryModeLetter = isset($salaryModeArr[$job->min_sal_mode]) ? strtoupper(substr($salaryModeArr[$job->min_sal_mode], 0, 1)) : '';
$hasSalary = @$job->min_salary > 0 || @$job->max_salary > 0;

$avatarSource = $clientName ?: @$job->title ?: 'J';
$avatarInitial = strtoupper(substr(trim($avatarSource), 0, 1));
$avatarOrd = ord(strtolower($avatarInitial ?: 'j'));
if ($avatarOrd >= 97 && $avatarOrd <= 101) { $avatarColors = ['#fff5f8', '#f1416c']; }
elseif ($avatarOrd >= 102 && $avatarOrd <= 106) { $avatarColors = ['#fff8ec', '#ffa800']; }
elseif ($avatarOrd >= 107 && $avatarOrd <= 111) { $avatarColors = ['#f5f8ff', '#3e97ff']; }
elseif ($avatarOrd >= 112 && $avatarOrd <= 116) { $avatarColors = ['#eafcf3', '#17c653']; }
else { $avatarColors = ['#f5f5fa', '#5e6278']; }

// ---- Impact Preview cell icon: driven by column severity, not the text value ----
$jsdCellIcon = function ($val, $key) {
    if ($val === 'active') return ['fa-check', '#17c653'];
    $color = $val === 'hold' ? '#ffa800' : '#f1416c';
    if (in_array($key, ['interviews', 'offers'])) return ['fa-bullseye', $color];
    if ($val === 'hold' && in_array($key, ['candidates', 'notify'])) return ['fa-pause', $color];
    if ($val === 'hold') return ['fa-circle-exclamation', $color];
    return ['fa-xmark', $color];
};

$footerName = @$lastStatusChange->changed_by_name;
$footerDate = @$lastStatusChange->created_at ?: @$job->updated_at;
$footerInitial = $footerName ? strtoupper(substr(trim($footerName), 0, 1)) : 'S';
?>
<style>
  .jsd-wrap { padding: 20px 28px 24px; font-family: 'Poppins', sans-serif; color: #3f4254; }
  .jsd-wrap * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

  /* ---- Hero / job info card ---- */
  .jsd-hero { display: grid; grid-template-columns: minmax(0,1.6fr) minmax(0,1fr) minmax(0,1.05fr) auto auto; gap: 0; border: 1px solid #eef0f4; border-radius: 12px; margin-bottom: 22px; background: #fff; overflow: hidden; }
  .jsd-hero-col { padding: 16px 20px; display: flex; flex-direction: column; justify-content: center; border-left: 1px solid #eef0f4; min-width: 0; }
  .jsd-hero-col:first-child { border-left: none; }
  .jsd-hero-label { font-size: 10px; font-weight: 700; color: #3e97ff; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 9px; }
  .jsd-hero-id { display: flex; align-items: center; gap: 12px; min-width: 0; }
  .jsd-hero-avatar { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; flex-shrink: 0; }
  .jsd-hero-title { font-size: 14.5px; font-weight: 700; color: #181c32; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .jsd-hero-cat { font-size: 11.5px; color: #3e97ff; font-weight: 500; margin-top: 2px; }
  .jsd-hero-client { font-size: 11.5px; color: #7e8299; font-weight: 400; margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .jsd-hero-row { display: flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: #181c32; padding: 3px 0; }
  .jsd-hero-row i { color: #a1a5b7; font-size: 11px; width: 13px; text-align: center; flex-shrink: 0; }
  .jsd-hero-row .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
  .jsd-hero-date-col { padding: 16px 18px; min-width: 130px; }
  .jsd-hero-date { display: flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: #181c32; margin-top: 8px; }
  .jsd-hero-date i { color: #a1a5b7; font-size: 11px; }

  @media (max-width: 1200px) {
    .jsd-hero { grid-template-columns: 1fr 1fr; }
    .jsd-hero-date-col { grid-column: span 1; border-top: 1px solid #eef0f4; }
  }
  @media (max-width: 640px) {
    .jsd-hero { grid-template-columns: 1fr; }
    .jsd-hero-col, .jsd-hero-date-col { border-left: none !important; border-top: 1px solid #eef0f4; }
    .jsd-hero-col:first-child { border-top: none; }
  }

  /* ---- Section headers ---- */
  .jsd-sec-head { display: flex; align-items: center; gap: 10px; margin: 4px 0 14px; }
  .jsd-sec-num { width: 24px; height: 24px; border-radius: 50%; background: #3e97ff; color: #fff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .jsd-sec-title { font-size: 14px; font-weight: 700; color: #181c32; }
  .jsd-sec-sub { font-size: 11px; color: #7e8299; font-weight: 400; margin-top: 1px; }

  /* ---- Status cards ---- */
  .jsd-status-row { display: flex; gap: 14px; margin-bottom: 24px; flex-wrap: wrap; }
  .jsd-status-card { flex: 1 1 220px; border: 1.5px solid #eef0f4; border-radius: 12px; padding: 16px; cursor: pointer; background: #fff; position: relative; transition: border-color .15s, box-shadow .15s, background .15s; }
  .jsd-status-card:hover { border-color: #cdd4e0; }
  .jsd-status-card.is-current { border-color: #17c653; background: #f2fdf7; }
  .jsd-status-card.is-selected { box-shadow: 0 0 0 1px currentColor inset; }
  .jsd-status-card .top { display: flex; align-items: center; gap: 10px; }
  .jsd-status-card .ic { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
  .jsd-status-card .nm { font-size: 14px; font-weight: 700; color: #181c32; }
  .jsd-status-card .ds { font-size: 10.5px; color: #7e8299; }
  .jsd-status-card .chk { position: absolute; top: 14px; right: 14px; width: 20px; height: 20px; border-radius: 50%; border: 1.5px solid #cdd4e0; display: flex; align-items: center; justify-content: center; font-size: 10px; color: transparent; }
  .jsd-status-card.is-current .chk { border-color: #17c653; background: #17c653; color: #fff; }
  .jsd-status-card.is-selected .chk { color: #fff; }
  .jsd-status-card ul { list-style: none; margin: 12px 0 0; padding: 0; }
  .jsd-status-card ul li { font-size: 11px; font-weight: 500; color: #3f4254; padding: 3px 0; display: flex; align-items: flex-start; gap: 6px; }
  .jsd-status-card ul li i { font-size: 10px; margin-top: 2px; flex-shrink: 0; }
  .jsd-status-card[data-status="active"] ul li i { color: #17c653; }
  .jsd-status-card[data-status="hold"] ul li i { color: #ffa800; }
  .jsd-status-card[data-status="inactive"] ul li i { color: #f1416c; }

  /* ---- Impact preview table ---- */
  .jsd-impact-table { width: 100%; border: 1px solid #eef0f4; border-radius: 12px; overflow: hidden; border-collapse: collapse; }
  .jsd-impact-table th, .jsd-impact-table td { padding: 10px 14px; font-size: 11.5px; text-align: left; border-bottom: 1px solid #f4f5f8; }
  .jsd-impact-table thead th { background: #f8f9fb; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
  .jsd-impact-table thead th.col-active { color: #17845a; }
  .jsd-impact-table thead th.col-hold { color: #b5720a; }
  .jsd-impact-table thead th.col-inactive { color: #c22851; }
  .jsd-impact-table tbody th { font-weight: 600; color: #3f4254; white-space: nowrap; }
  .jsd-impact-table tbody tr:last-child td, .jsd-impact-table tbody tr:last-child th { border-bottom: none; }
  /* .jsd-cell is the inner <span> inside each <td>, not the <td> itself — the selector
     must target it directly, not "td.jsd-cell" (which never matches anything since the
     class never actually lands on the <td>). That mismatch was why the icon and text
     had no gap: this rule simply never applied. */
  .jsd-impact-table .jsd-cell { display: inline-flex; align-items: center; gap: 8px; font-weight: 600; color: #3f4254; }
  .jsd-impact-table .jsd-cell i { width: 13px; height: 13px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 11px; }
  .jsd-impact-table td.jsd-current-col { background: #f2fdf7; }
  .jsd-impact-table td.jsd-target-col { background: #f5f9ff; }

  /* ---- Footer ---- */
  .jsd-footer { display: flex; align-items: center; gap: 10px; margin-top: 18px; padding-top: 14px; border-top: 1px solid #eef0f4; }
  .jsd-footer-avatar { width: 30px; height: 30px; border-radius: 50%; background: #eef4ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
  .jsd-footer-text { font-size: 11px; color: #7e8299; }
  .jsd-footer-text b { color: #3f4254; font-weight: 600; }
  .jsd-footer-text .sep { margin: 0 6px; }
</style>

<div class="jsd-wrap">
  <div class="jsd-hero">
    <!-- Identity -->
    <div class="jsd-hero-col">
      <div class="jsd-hero-id">
        <div class="jsd-hero-avatar" style="background:{{ $avatarColors[0] }};color:{{ $avatarColors[1] }};">{{ $avatarInitial }}</div>
        <div style="min-width:0;">
          <div class="jsd-hero-title" title="{{ @$job->title }}">{{ @$job->title }}</div>
          @if(@$job->cat_name)<div class="jsd-hero-cat">{{ $job->cat_name }}</div>@endif
          @if($clientName)<div class="jsd-hero-client" title="{{ $clientName }}">{{ $clientName }}</div>@endif
        </div>
      </div>
    </div>

    <!-- Experience & compensation -->
    <div class="jsd-hero-col">
      <div class="jsd-hero-label">Experience</div>
      <div class="jsd-hero-row"><i class="fas fa-clock"></i>{{ @$job->min_exp }} – {{ @$job->max_exp }} Years</div>
      @if($hasSalary)
      <div class="jsd-hero-row">{!! $currencyIcon !!}&nbsp;{{ number_format($job->min_salary / 1000) }}K – {{ number_format($job->max_salary / 1000) }}K {{ $salaryModeLetter }}</div>
      @endif
      @if($clientTypeLabel)
      <div class="jsd-hero-row jsd-client-text" style="color:{{ $clientTypeColor }};"><i class="fas {{ $clientTypeIcon }}" style="color:{{ $clientTypeColor }};"></i>{{ $clientTypeLabel }} · #{{ @$job->id }}</div>
      @endif
    </div>

    <!-- Hiring & location -->
    <div class="jsd-hero-col">
      <div class="jsd-hero-label">Hiring &amp; Location</div>
      @if($currentMeta)
      <div class="jsd-hero-row" style="color:{{ $currentMeta['color'] }};"><span class="dot"></span>{{ $currentMeta['label'] }}</div>
      @endif
      @if($cityName)
      <div class="jsd-hero-row"><i class="fas fa-location-dot"></i>{{ ucwords($cityName) }}</div>
      @endif
      <div class="jsd-hero-row"><i class="fas fa-house-laptop"></i>{{ $workType }}</div>
    </div>

    <!-- Job Posted -->
    <div class="jsd-hero-date-col">
      <div class="jsd-hero-label">Job Posted</div>
      <div class="jsd-hero-date"><i class="fas fa-calendar"></i>{{ @$postedDisplayDate ? date('d M Y', strtotime($postedDisplayDate)) : '—' }}</div>
    </div>

    <!-- Automatically Closed -->
    @if(@$job->closeType == 1 && @$job->end_date)
    <div class="jsd-hero-date-col">
      <div class="jsd-hero-label">Automatically Closed</div>
      <div class="jsd-hero-date"><i class="fas fa-calendar-xmark"></i>{{ date('d M Y', strtotime($job->end_date)) }}</div>
    </div>
    @endif
  </div>

  <form id="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="job_id" id="job_id" value="{{ @$job->id }}" />

    <div class="jsd-sec-head">
      <span class="jsd-sec-num">1</span>
      <div><div class="jsd-sec-title">Choose New Status</div><div class="jsd-sec-sub">Select the new status for this job position</div></div>
    </div>
    <div class="jsd-status-row">
      @foreach($statusMeta as $val => $meta)
      <div class="jsd-status-card {{ $val === $statusVal ? 'is-current' : '' }}" data-status="{{ $val }}" style="color:{{ $meta['color'] }};" onclick="jsdSelectStatus(this)">
        <input type="radio" class="master_status d-none" name="status" value="{{ $val }}" id="jsd_{{ $val }}">
        <span class="chk"><i class="fas fa-check"></i></span>
        <div class="top">
          <span class="ic" style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }};"><i class="fas {{ $meta['icon'] }}"></i></span>
          <div>
            <div class="nm">{{ $meta['label'] }}</div>
            <div class="ds">{{ $meta['desc'] }}</div>
          </div>
        </div>
        <ul>
          @foreach($meta['bullets'] as $b)
          <li><i class="fas {{ $val === 'active' ? 'fa-check' : ($val === 'hold' ? 'fa-circle-exclamation' : 'fa-xmark') }}"></i>{{ $b }}</li>
          @endforeach
        </ul>
      </div>
      @endforeach
    </div>
  </form>

  <div class="jsd-sec-head">
    <span class="jsd-sec-num">2</span>
    <div><div class="jsd-sec-title">Impact Preview</div><div class="jsd-sec-sub">See what will change when you update the status</div></div>
  </div>
  <table class="jsd-impact-table">
    <thead>
      <tr>
        <th>Feature / Action</th>
        <th class="col-active">Hiring{{ $statusVal === 'active' ? ' (Current)' : '' }}</th>
        <th class="col-hold">Hold{{ $statusVal === 'hold' ? ' (Current)' : '' }}</th>
        <th class="col-inactive">Closed{{ $statusVal === 'inactive' ? ' (Current)' : '' }}</th>
      </tr>
    </thead>
    <tbody id="jsd_impact_body">
      @php
        $rows = [
          'Career Page Visibility' => 'career',
          'Vendor Submission' => 'vendor',
          'Candidate Applications' => 'candidates',
          'Interviews' => 'interviews',
          'Offer Creation' => 'offers',
          'Notifications / Alerts' => 'notify',
        ];
      @endphp
      @foreach($rows as $label => $key)
      <tr>
        <th>{{ $label }}</th>
        @foreach(['active','hold','inactive'] as $val)
        @php [$cellIcon, $cellColor] = $jsdCellIcon($val, $key); @endphp
        <td class="jsd-cell-{{ $val }} {{ $val === $statusVal ? 'jsd-current-col' : '' }}">
          <span class="jsd-cell"><i class="fas {{ $cellIcon }}" style="color:{{ $cellColor }};"></i>{{ $statusMeta[$val][$key] }}</span>
        </td>
        @endforeach
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="jsd-footer">
    <div class="jsd-footer-avatar">{{ $footerInitial }}</div>
    <div class="jsd-footer-text">
      Last updated by <b>{{ $footerName ?: 'System' }}</b>
      @if($footerDate)<span class="sep">|</span>{{ date('d M Y, h:i A', strtotime($footerDate)) }}@endif
    </div>
  </div>

  <div id="status_msg" style="text-align:center;font-weight:500;color:#17c653;margin-top:10px;font-size:11.5px;"></div>
</div>

<script type="text/javascript">
var JSD_STATUS_META = {!! json_encode($statusMeta) !!};

function jsdSelectStatus(el) {
    var val = $(el).attr('data-status');
    $('#jsd_' + val).prop('checked', true);

    $('.jsd-status-card').removeClass('is-selected');
    $(el).addClass('is-selected');

    var meta = JSD_STATUS_META[val];

    $('.jsd-impact-table td.jsd-target-col').removeClass('jsd-target-col');
    $('.jsd-cell-' + val).addClass('jsd-target-col');

    $('#status_confirm_btn').prop('disabled', false).css('background', meta.color);
    $('.changetextupdate').text('Update Status to ' + meta.label);
}

function newUpdateJobstatus(){
    $('#status_confirm_btn').prop('disabled', true);
    $('.changetextupdate').text('Updating…');
    var page = $('.custom-pagination .active_disabled').data('page');
    $.ajax({
        url: '{{ route("service_company.jobs.newUpdateJobstatus") }}',
        container: '#createSchedule',
        type: "POST",
        data: $('#createSchedule').serialize(),
        success: function (response) {

            if (response.status == 'coinCheck') {
                Swal.fire("Failed", `${response.message}`, "warning").then(function(){
                    $(".btn-active-color-primary").click();
                });
                $('#status_confirm_btn').prop('disabled', false);
                $('.changetextupdate').text('Update Status');
                return false;
            }

            if (response.status == 'success') {
                $('.activeJobs').html(response.activeJobs);
                $('.inactiveJobs').html(response.inactiveJobs);
                $('.holdJobs').html(response.holdJobs);
                $('#status_msg').html("Requisition status updated successfully.");

                setTimeout(() => {
                    $('#status_sub_stage_close').click();

                    fetchCandidates(page, $('#search').val()).then(() => {
                        $('#status_confirm_btn').prop('disabled', false);
                        $('.changetextupdate').text('Update Status');
                        $('.custom-pagination .page-link').removeClass('active_disabled');
                    }).catch(() => {
                        $('.custom-pagination .page-link').removeClass('active_disabled');
                    });
                }, 500);
            } else {
                $('#status_msg').html("<div style='color:#FF0000'>Oops, something went wrong. Please try again</div>");
                $('#status_confirm_btn').prop('disabled', false);
                $('.changetextupdate').text('Update Status');
            }
        }
    });
}
</script>
