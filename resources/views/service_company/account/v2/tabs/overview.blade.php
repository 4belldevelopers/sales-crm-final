{{-- Account Settings V2 — Overview Tab Partial View --}}
<div class="tab-pane {{ $overview_tab }}" id="overview" role="tabpanel">
  <!--begin::Overview header-->
  <div class="card-header cursor-pointer px-0">
    <!--begin::Card title-->
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Overview</h3>
    </div>
    <!--end::Card title-->
    @if(in_array("edit_settings", $userPermissions))
    <!--begin::Action-->
    <a href="{{ route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}" class="btn btn-primary align-self-center">Edit Profile</a>
    <!--end::Action-->
    @endif
  </div>
  <!--end::Overview header-->
  <div class="card-body px-0 py-7">
    @if(in_array("view_settings", $userPermissions))
    <!--begin::overview Content (redesigned)-->
    <div class="cad">
      <div class="cad-grid3">
        <!-- About -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-bl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>About {{ ucwords(@$account->company_name) }}</div>
          @if(!empty(trim(strip_tags((string)@$account->description))))
            <div class="cad-desc">{!! @$account->description !!}</div>
          @else
            <div class="cad-desc" style="color:#94a3b8;">No company description added yet.</div>
          @endif
          @if(!empty(@$client->tags))
            <div style="margin-top:14px;">
              <div class="cad-row" style="border:none;padding-bottom:6px;"><span class="k">Specialties</span></div>
              <div class="cad-tags">@foreach(explode(',', $client->tags) as $tg)@if(trim($tg)!=='')<span class="cad-tagi">{{ ucwords(trim($tg)) }}</span>@endif @endforeach</div>
            </div>
          @endif
        </div>
        <!-- Company details -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-pu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M8 10h.01M16 10h.01"/></svg></span>Company Information</div>
          <div class="cad-row"><span class="k">Industry</span><span class="v">{{ @$industries_name ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Company Size</span><span class="v">{{ $cad_size ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Headquarters</span><span class="v">{{ trim(ucwords($company_city_name).(($company_city_name&&$company_country_name)?', ':'').ucwords($company_country_name),', ') ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Type</span><span class="v">@if(isset($account->industries_type) && $account->industries_type >= 0){{ @$industries_type_arr[$account->industries_type] }}@else—@endif</span></div>
          <div class="cad-row"><span class="k">Founded</span><span class="v">{{ @$account->founded ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Website</span><span class="v">@if(!empty(@$account->website))<a href="{{ (strpos($account->website,'http')===0?'':'//').$account->website }}" target="_blank">{{ $account->website }}</a>@else—@endif</span></div>
        </div>
        <!-- Configuration Checklist -->
        @php
          $cad_stageCount = count((array)@$interviewStage_sourcing)+count((array)@$interviewStage_screening)+count((array)@$interviewStage_evaluation)+count((array)@$interviewStage_negotiation)+count((array)@$interviewStage_offered)+count((array)@$interviewStage_joined)+count((array)@$interviewStage_reject);
          $cad_cfg = [
            'Company profile completed'   => ($cad_pct >= 100),
            'Interview stages configured' => ($cad_stageCount > 0),
            'Offer letter template set'   => !empty(trim(strip_tags((string)@$account->offer_terms))),
            'Agency terms & conditions'   => !empty(trim(strip_tags((string)@$account->terms))),
            'Salary structure configured' => (!empty(@$offer_points) && count($offer_points) > 0),
            'Commercials configured'      => ((@$contractStatus->status ?? 0) || (@$fullTimeStatus->status ?? 0)),
          ];
        @endphp
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-gr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3 8-8"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></span>Configuration Checklist</div>
          @foreach($cad_cfg as $label => $ok)
            <div class="cad-row"><span class="k">{{ $label }}</span><span class="v">
              @if($ok)<span class="cad-chip ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg> Completed</span>
              @else<span class="cad-chip warn">Pending</span>@endif
            </span></div>
          @endforeach
        </div>
        <!-- Services & Rates -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-am"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>Services &amp; Rates</div>
          <div class="cad-mini">
            <div class="cad-mini-h">Contract Hiring
              <span class="cad-chip {{ (@$contractStatus->status) ? 'ok' : 'off' }}">{{ (@$contractStatus->status) ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="cad-mini-s">{{ (int) @$contractCount }} {{ (int)@$contractCount === 1 ? 'country' : 'countries' }} served</div>
          </div>
          <div class="cad-mini">
            <div class="cad-mini-h">Full-time Hiring
              <span class="cad-chip {{ (@$fullTimeStatus->status) ? 'ok' : 'off' }}">{{ (@$fullTimeStatus->status) ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="cad-mini-s">{{ (int) @$fullTimeCount }} {{ (int)@$fullTimeCount === 1 ? 'country' : 'countries' }} served</div>
          </div>
        </div>
        <!-- Settings -->
        @php
          $cad_cur = optional($currencyArr->firstWhere('id', @$account->currency));
          $cad_tf  = optional($time_formatArr->firstWhere('id', @$account->time_format));
          $cad_lang= optional($languagesArr->firstWhere('id', @$account->language));
          $cad_dup = (@$account->app_duplicate == 2) ? 'Overall system'.((@$account->app_duplicate_days)?' · '.$account->app_duplicate_days.' days':'')
                    : ((@$account->app_duplicate == 1) ? 'Job-wise'.((@$account->app_duplicate_days)?' · '.$account->app_duplicate_days.' days':'') : 'Disabled');
        @endphp
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-pu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>Settings</div>
          <div class="cad-row"><span class="k">Duplicate Status</span><span class="v">{{ $cad_dup }}</span></div>
          <div class="cad-row"><span class="k">Time Zone</span><span class="v">{{ @$account->timezone ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Currency</span><span class="v">{{ $cad_cur->code ? $cad_cur->code.' · '.$cad_cur->country : '—' }}</span></div>
          <div class="cad-row"><span class="k">Time Format</span><span class="v">{{ $cad_tf->name ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Language</span><span class="v">{{ $cad_lang->name ?: '—' }}</span></div>
        </div>
        <!-- Team Overview -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-bl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg></span>Team Overview</div>
          <div class="cad-row"><span class="k">Active Recruiters</span><span class="v">{{ (int) @$team_count }}</span></div>
          <div class="cad-row"><span class="k">Interview Stages</span><span class="v">{{ $cad_stageCount }}</span></div>
          @if(!empty(@$team) && count($team))
            <div class="cad-team">
              @foreach($team as $tm)
                @if(!empty($tm->image))<span class="cad-team-av" title="{{ ucwords($tm->name) }}"><img src="{{ $tm->image }}" alt=""></span>
                @else<span class="cad-team-av" title="{{ ucwords($tm->name) }}">{{ strtoupper(substr($tm->name,0,1)) }}</span>@endif
              @endforeach
              @if(@$team_count > count($team))<span class="cad-team-av">+{{ $team_count - count($team) }}</span>@endif
            </div>
          @endif
        </div>
        <!-- Marketplace Performance -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-am"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l1-5h16l1 5M4 9h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9zM9 13h6"/></svg></span>Marketplace Performance</div>
          <div class="cad-row"><span class="k">Agencies Empaneled</span><span class="v">{{ (int) @$agenciesEmpaneled }}</span></div>
          <div class="cad-row"><span class="k">Active Agencies</span><span class="v">{{ (int) @$agenciesActive }}</span></div>
        </div>
        <!-- Administrators -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-bl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>Administrators</div>
          @forelse(@$administrators ?? [] as $adm)
            <div class="cad-adm">
              @if(!empty($adm->image))<span class="cad-adm-av"><img src="{{ $adm->image }}" alt=""></span>
              @else<span class="cad-adm-av">{{ strtoupper(substr($adm->name,0,1)) }}</span>@endif
              <div class="cad-adm-mid">
                <div class="cad-adm-name">{{ ucwords($adm->name) }}@if(@$adm->root_user)<em class="cad-adm-pri">Primary</em>@endif</div>
                <div class="cad-adm-role">{{ @$adm->designation ?: 'Administrator' }}</div>
                <div class="cad-adm-c">{{ $adm->email }}@if(@$adm->mobile) · {{ $adm->mobile }}@endif</div>
              </div>
            </div>
          @empty
            <div class="cad-desc" style="color:#94a3b8;">No administrators found.</div>
          @endforelse
        </div>
      </div>
    </div>
    <!--end::overview Content (redesigned)-->
    @endif
  </div>
</div>
