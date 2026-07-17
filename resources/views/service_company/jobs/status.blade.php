<?php
/*
 * Job Status drawer body — AJAX-injected into #status_body_content (see
 * service_company.jobs.index's #status_drawer). Rewritten from the old
 * dropdown/2-bullet modal into a fuller status-change review screen, same
 * business rule as before (Hiring=active enables the career page + vendor
 * submissions + candidate applications; Hold/Closed disable them), just
 * expressed as a richer comparison instead of one bullet list at a time.
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

$lastLog = $statusLogs->first();
?>
<style>
  .jsd-wrap { padding: 20px 28px 28px; font-family: 'Poppins', sans-serif; color: #3f4254; }
  .jsd-wrap * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

  /* ---- Job info card ---- */
  .jsd-jobcard { display: flex; align-items: flex-start; gap: 14px; border: 1px solid #eef0f4; border-radius: 12px; padding: 16px 18px; margin-bottom: 22px; background: #fff; }
  .jsd-jobcard .ic { width: 46px; height: 46px; border-radius: 10px; background: #f5f8ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
  .jsd-jobcard .title { font-size: 15px; font-weight: 700; color: #181c32; line-height: 1.3; }
  .jsd-jobcard .sub { font-size: 11.5px; color: #7e8299; font-weight: 500; margin-top: 2px; }
  .jsd-meta-row { display: flex; flex-wrap: wrap; gap: 8px 22px; margin-top: 10px; }
  .jsd-meta-item { display: flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 500; color: #3f4254; }
  .jsd-meta-item i { color: #a1a5b7; font-size: 11px; width: 12px; text-align: center; }
  .jsd-meta-item.status-pill { padding: 2px 10px; border-radius: 6px; font-weight: 600; }

  /* ---- Section headers ---- */
  .jsd-sec-head { display: flex; align-items: center; gap: 10px; margin: 4px 0 14px; }
  .jsd-sec-num { width: 24px; height: 24px; border-radius: 50%; background: #3e97ff; color: #fff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .jsd-sec-title { font-size: 14px; font-weight: 700; color: #181c32; }
  .jsd-sec-sub { font-size: 11px; color: #7e8299; font-weight: 400; margin-top: 1px; }

  /* ---- Status cards ---- */
  .jsd-status-row { display: flex; gap: 14px; margin-bottom: 28px; flex-wrap: wrap; }
  .jsd-status-card { flex: 1 1 220px; border: 1.5px solid #eef0f4; border-radius: 12px; padding: 16px; cursor: pointer; background: #fff; position: relative; transition: border-color .15s, box-shadow .15s, background .15s; }
  .jsd-status-card:hover { border-color: #cdd4e0; }
  .jsd-status-card.is-current { border-color: #17c653; background: #f2fdf7; }
  .jsd-status-card.is-selected { border-color: #3e97ff; background: #f5f9ff; box-shadow: 0 0 0 1px #3e97ff inset; }
  .jsd-status-card .top { display: flex; align-items: center; gap: 10px; }
  .jsd-status-card .ic { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
  .jsd-status-card .nm { font-size: 14px; font-weight: 700; color: #181c32; }
  .jsd-status-card .ds { font-size: 10.5px; color: #7e8299; }
  .jsd-status-card .chk { position: absolute; top: 14px; right: 14px; width: 20px; height: 20px; border-radius: 50%; border: 1.5px solid #cdd4e0; display: flex; align-items: center; justify-content: center; font-size: 10px; color: transparent; }
  .jsd-status-card.is-current .chk { border-color: #17c653; background: #17c653; color: #fff; }
  .jsd-status-card.is-selected .chk { border-color: #3e97ff; background: #3e97ff; color: #fff; }
  .jsd-status-card ul { list-style: none; margin: 12px 0 0; padding: 0; }
  .jsd-status-card ul li { font-size: 11px; font-weight: 500; color: #3f4254; padding: 3px 0; display: flex; align-items: flex-start; gap: 6px; }
  .jsd-status-card ul li i { font-size: 10px; margin-top: 2px; flex-shrink: 0; }
  .jsd-status-card[data-status="active"] ul li i { color: #17c653; }
  .jsd-status-card[data-status="hold"] ul li i { color: #ffa800; }
  .jsd-status-card[data-status="inactive"] ul li i { color: #f1416c; }

  /* ---- Impact preview table ---- */
  .jsd-impact-table { width: 100%; border: 1px solid #eef0f4; border-radius: 12px; overflow: hidden; border-collapse: collapse; margin-bottom: 8px; }
  .jsd-impact-table th, .jsd-impact-table td { padding: 10px 14px; font-size: 11.5px; text-align: left; border-bottom: 1px solid #f4f5f8; }
  .jsd-impact-table thead th { background: #f8f9fb; font-size: 10.5px; font-weight: 600; color: #7e8299; text-transform: uppercase; letter-spacing: .3px; }
  .jsd-impact-table thead th.col-active { color: #17845a; }
  .jsd-impact-table thead th.col-hold { color: #b5720a; }
  .jsd-impact-table thead th.col-inactive { color: #c22851; }
  .jsd-impact-table tbody th { font-weight: 600; color: #3f4254; white-space: nowrap; }
  .jsd-impact-table tbody tr:last-child td, .jsd-impact-table tbody tr:last-child th { border-bottom: none; }
  .jsd-impact-table td.jsd-current-col { background: #f2fdf7; font-weight: 600; }
  .jsd-impact-table td.jsd-target-col { background: #f5f9ff; font-weight: 600; }

  /* ---- Right sidebar ---- */
  .jsd-side-card { border: 1px solid #eef0f4; border-radius: 12px; padding: 16px 18px; background: #fff; margin-bottom: 16px; }
  .jsd-side-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #181c32; margin-bottom: 12px; }
  .jsd-side-title i { color: #3e97ff; }
  .jsd-kv { display: flex; align-items: center; justify-content: space-between; padding: 6px 0; font-size: 11.5px; }
  .jsd-kv .k { color: #7e8299; font-weight: 500; }
  .jsd-kv .v { font-weight: 600; color: #181c32; }
  .jsd-pill { padding: 2px 10px; border-radius: 6px; font-size: 10.5px; font-weight: 700; }
  .jsd-side-divider { border-top: 1px dashed #eef0f4; margin: 10px 0; }
  .jsd-affect-label { font-size: 10.5px; font-weight: 600; color: #a1a5b7; text-transform: uppercase; letter-spacing: .3px; margin-bottom: 6px; }
  .jsd-important { background: #fff8ec; border: 1px solid #ffe3b0; border-radius: 10px; padding: 12px 14px; display: flex; gap: 10px; margin-top: 6px; }
  .jsd-important i { color: #ffa800; font-size: 14px; margin-top: 1px; }
  .jsd-important .txt { font-size: 10.5px; font-weight: 500; color: #7e6a3f; line-height: 1.45; }

  .jsd-tl-item { display: flex; gap: 10px; position: relative; padding-bottom: 18px; }
  .jsd-tl-item:last-child { padding-bottom: 0; }
  .jsd-tl-item .dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 4px; flex-shrink: 0; position: relative; z-index: 1; }
  .jsd-tl-item .line { position: absolute; left: 4px; top: 12px; bottom: -6px; width: 2px; background: #eef0f4; }
  .jsd-tl-item:last-child .line { display: none; }
  .jsd-tl-item .lbl { font-size: 12px; font-weight: 700; color: #181c32; }
  .jsd-tl-item .meta { font-size: 10.5px; color: #a1a5b7; margin-top: 1px; }
  .jsd-tl-item.upcoming .lbl, .jsd-tl-item.upcoming .dot { color: #cdd4e0; background: #fff; border: 2px solid #cdd4e0; }

  .jsd-lastupdated { font-size: 10.5px; color: #a1a5b7; padding-top: 4px; }

  @media (max-width: 992px) {
    .jsd-grid { grid-template-columns: 1fr !important; }
  }
</style>

<div class="jsd-wrap">
  <div class="jsd-jobcard">
    <div class="ic"><i class="fas fa-briefcase"></i></div>
    <div>
      <div class="title">{{ @$job->title }}</div>
      <div class="sub">{{ @$job->cat_name }}{{ @$clientName ? ' · '.$clientName : '' }}</div>
      <div class="jsd-meta-row">
        <span class="jsd-meta-item"><i class="fas fa-clock"></i>{{ @$job->min_exp }}–{{ @$job->max_exp }} Years</span>
        @if($cityName)
        <span class="jsd-meta-item"><i class="fas fa-location-dot"></i>{{ ucwords($cityName) }}</span>
        @endif
        <span class="jsd-meta-item"><i class="fas fa-house-laptop"></i>{{ $workType }}</span>
        <span class="jsd-meta-item"><i class="fas fa-users"></i>{{ @$job->total_positions }} Opening(s)</span>
        @if(@$job->created_at)
        <span class="jsd-meta-item"><i class="fas fa-calendar"></i>Posted {{ date('d M Y', strtotime($job->created_at)) }}</span>
        @endif
        @if(@$job->closeType == 1 && @$job->end_date)
        <span class="jsd-meta-item"><i class="fas fa-calendar-xmark"></i>Auto-closes {{ date('d M Y', strtotime($job->end_date)) }}</span>
        @endif
      </div>
    </div>
    @if($currentMeta)
    <span class="jsd-meta-item status-pill" style="margin-left:auto;color:{{ $currentMeta['color'] }};background:{{ $currentMeta['bg'] }};"><i class="fas fa-circle" style="font-size:6px;color:{{ $currentMeta['color'] }};"></i>{{ $currentMeta['label'] }}</span>
    @endif
  </div>

  <div class="jsd-grid" style="display:grid;grid-template-columns:1fr 300px;gap:22px;align-items:start;">
    <div>
      <form id="createSchedule" class="ajax-form" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="job_id" id="job_id" value="{{ @$job->id }}" />

        <div class="jsd-sec-head">
          <span class="jsd-sec-num">1</span>
          <div><div class="jsd-sec-title">Choose New Status</div><div class="jsd-sec-sub">Select the new status for this job position</div></div>
        </div>
        <div class="jsd-status-row">
          @foreach($statusMeta as $val => $meta)
          <div class="jsd-status-card {{ $val === $statusVal ? 'is-current' : '' }}" data-status="{{ $val }}" onclick="jsdSelectStatus(this)">
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
            <td class="jsd-cell-{{ $val }} {{ $val === $statusVal ? 'jsd-current-col' : '' }}">{{ $statusMeta[$val][$key] }}</td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div>
      <div class="jsd-side-card">
        <div class="jsd-side-title"><i class="fas fa-shield-halved"></i>Status Summary</div>
        <div class="jsd-kv"><span class="k">Current Status</span><span class="v"><span class="jsd-pill" style="color:{{ $currentMeta['color'] ?? '#7e8299' }};background:{{ $currentMeta['bg'] ?? '#f5f8fa' }};">{{ $currentMeta['label'] ?? 'N/A' }}</span></span></div>
        <div class="jsd-kv"><span class="k">New Status</span><span class="v" id="jsd_new_status"><span class="jsd-pill" style="color:#a1a5b7;background:#f5f8fa;">Select status</span></span></div>
        <div class="jsd-kv"><span class="k">Effective</span><span class="v">Immediately</span></div>
        <div class="jsd-side-divider"></div>
        <div class="jsd-affect-label">This change will affect</div>
        <div class="jsd-kv"><span class="k"><i class="fas fa-user-group" style="width:14px;color:#a1a5b7;"></i> Candidates</span><span class="v">{{ (int) @$candidatesCount }}</span></div>
        <div class="jsd-kv"><span class="k"><i class="fas fa-building" style="width:14px;color:#a1a5b7;"></i> Vendors</span><span class="v">{{ (int) @$vendorsCount }}</span></div>
        <div class="jsd-kv"><span class="k"><i class="fas fa-clock" style="width:14px;color:#a1a5b7;"></i> Interviews</span><span class="v">{{ (int) @$interviewsCount }}</span></div>
        <div class="jsd-kv"><span class="k"><i class="fas fa-file" style="width:14px;color:#a1a5b7;"></i> Career Page</span><span class="v" id="jsd_career_val">{{ $currentMeta['career'] ?? '—' }}</span></div>
        <div class="jsd-important" id="jsd_important_box" style="{{ $statusVal === 'active' ? 'display:none;' : '' }}">
          <i class="fas fa-triangle-exclamation"></i>
          <div class="txt" id="jsd_important_txt">
            @if($statusVal === 'inactive')
              This job is Closed — it is removed from the career page and vendors cannot submit new candidates.
            @elseif($statusVal === 'hold')
              This job is on Hold — it is removed from the career page and vendors cannot submit new candidates.
            @else
              Select a new status to preview its impact.
            @endif
          </div>
        </div>
      </div>

      <div class="jsd-side-card">
        <div class="jsd-side-title"><i class="fas fa-timeline"></i>Status Timeline</div>
        @forelse($statusLogs as $log)
        <div class="jsd-tl-item">
          <span class="dot" style="background:{{ $statusMeta[$log->to_status]['color'] ?? '#a1a5b7' }};"></span>
          <span class="line"></span>
          <div>
            <div class="lbl">{{ $statusMeta[$log->to_status]['label'] ?? ucfirst($log->to_status) }}</div>
            <div class="meta">{{ date('d M Y, h:i A', strtotime($log->created_at)) }} · {{ $log->changed_by_name ?? 'System' }}</div>
          </div>
        </div>
        @empty
        <div class="jsd-tl-item">
          <span class="dot" style="background:#a1a5b7;"></span>
          <span class="line"></span>
          <div>
            <div class="lbl">Job Created</div>
            <div class="meta">{{ @$job->created_at ? date('d M Y', strtotime($job->created_at)) : '—' }} · No status changes recorded yet</div>
          </div>
        </div>
        @endforelse
        <div class="jsd-tl-item upcoming">
          <span class="dot"></span>
          <div>
            <div class="lbl">Upcoming</div>
            <div class="meta">—</div>
          </div>
        </div>
      </div>

      <div class="jsd-lastupdated">
        Last updated {{ $lastLog ? 'by '.($lastLog->changed_by_name ?? 'System').' · '.date('d M Y, h:i A', strtotime($lastLog->created_at)) : (@$job->updated_at ? date('d M Y, h:i A', strtotime($job->updated_at)) : '—') }}
      </div>

      <div class="form-group" id="status_msg" style="text-align:center;font-weight:500;color:#17c653;margin-top:10px;font-size:11.5px;"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
var JSD_STATUS_META = {!! json_encode($statusMeta) !!};

function jsdSelectStatus(el) {
    var val = $(el).attr('data-status');
    $('#jsd_' + val).prop('checked', true);

    $('.jsd-status-card').removeClass('is-selected');
    $(el).addClass('is-selected');

    var meta = JSD_STATUS_META[val];
    $('#jsd_new_status').html('<span class="jsd-pill" style="color:' + meta.color + ';background:' + meta.bg + ';">' + meta.label + '</span>');
    $('#jsd_career_val').text(meta.career);

    $('.jsd-impact-table td.jsd-target-col').removeClass('jsd-target-col');
    $('.jsd-cell-' + val).addClass('jsd-target-col');

    if (val === 'active') {
        $('#jsd_important_box').hide();
    } else {
        var msg = val === 'inactive'
            ? 'This job will be Closed — removed from the career page, vendor submissions disabled, and the job archived.'
            : 'This job will be put on Hold — removed from the career page and vendor submissions disabled until resumed.';
        $('#jsd_important_txt').text(msg);
        $('#jsd_important_box').show();
    }

    $('#status_confirm_btn').prop('disabled', false);
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
