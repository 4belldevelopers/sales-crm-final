@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $relationget =  GeneralMethod::getRelationData($row->company_id,$company_id);

    $from_company_id = $relationget->from_company_id ?? null;
    $recommendation_id = $relationget->to_company_id ?? null;
    $final_status = $relationget->final_status ?? null;
    $to_created_at = $relationget->to_created_at ?? null;
    $created_at = $relationget->created_at ?? null;

    
    // $match = collect($row->staffing_company_relation)->first(function ($item) use ($company_id) {
    //     return ($item['from_company_id'] ?? null) == $company_id
    //         || ($item['to_company_id'] ?? null) == $company_id;
    // });

    // $from_company_id = ($match['from_company_id'] ?? null) == $company_id ? $match['from_company_id'] : null;
    // $recommendation_id   = ($match['to_company_id'] ?? null) == $company_id ? $match['to_company_id'] : null;

    // $final_status = collect($row->staffing_company_relation)->first(function ($item) use ($company_id) {
    //     return $item['from_company_id'] == $company_id;
    // })['final_status'] ?? null;


    // $to_created_at = collect($row->staffing_company)->first(function ($item) use ($company_id) {
    //     return $item['from_company_id'] == $company_id;
    // })['to_created_at'] ?? null;

    // $created_at = collect($row->staffing_company)->first(function ($item) use ($company_id) {
    //     return $item['from_company_id'] == $company_id;
    // })['created_at'] ?? null;


    $row->final_status = $final_status;
    $row->staffing_company_relation_id =$recommendation_id;
    $row->to_company_id = $recommendation_id;
    $row->from_company_id = $from_company_id;
    $row->to_created_at = ($to_created_at) ? $to_created_at : $created_at;
    $td_industries = DB::table('td_industries')->where('ind_id','=',$row->industries)->first();

    $logo_path = ''; //url('img/client-default.png');


    if (!empty(@$row->website) && empty(@$row->logo)){
            $domainGet = GeneralMethod::getDomain($row->website);
            $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
            $google = GeneralMethod::getDomainIcon($googleIcon,$row->id,true);
            $logo_path  = ($google) ? $googleIcon : '';
    }
    if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
        $logo_path = @$row->logo;
    }

    $company_name = $ind_name = '';
    $company_name = ucwords($row->company_name);

    if($td_industries){
        $ind_name = $td_industries->ind_name;
    }
    $client_type_css = 'show-detail-direct';
        
    $bgTextDecore = GeneralMethod::icoinText($row->company_name);
    $initials = strtoupper( implode('', array_map(fn($word) => mb_substr($word, 0, 1), explode(' ', trim($row->company_name)))));

            $locationHtml = $locationHtml = $country_name = $address = '';

            $client_id = $row->client_id;
            $locInfo = DB::table('client_location')
                ->select(
                    'client_location.id',
                    'client_location.city',
                    'client_location.address',
                    'client_location.postcode',
                    'client_location.country_id',
                    'city_list.city as city_name',
                    'state_list.state',
                )
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                ->where('client_id', $client_id)
                ->first();
            $country_name = '';
            $locInfostate = '';
            if($locInfo){
                if (@$locInfo->country_id > 0) {
                    $countryArr = GeneralMethod::getCountryName($locInfo->country_id);
                    if (is_array($countryArr)) {
                        if (count($countryArr) > 0) {
                            foreach ($countryArr as $cval) {
                                $country_name = ucwords($cval['name']);
                            }

                            if ($locInfo->state) {
                                $locInfostate = $locInfo->state;
                            }
                        }
                    }
                }
            }

            $city = @$locInfo->city;
            // if (@$locInfo->country_id == 101) {
                $city = @$locInfo->city_name;
            // }
            $locationHtml = ucwords(mb_substr(@$city, 0, 10)) . '&nbsp;' . @$country_name;
            $address = ''; //@$locInfo->address;

            $requisitionRow = DB::table('recommendation_relation')
                        ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                        ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_id', '=', 'jobs.id')
                        ->select(
                            DB::raw('COUNT(add_job_wise_condidates.job_id) AS job_id_count'),
                            DB::raw('COUNT(DISTINCT add_job_wise_condidates.job_id) AS unique_job_id_count'),
                            DB::raw('COUNT(add_job_wise_condidates.job_application_id) AS job_application_id_count'),
                            DB::raw(
                                'COUNT(CASE WHEN add_job_wise_condidates.master_status_id != 8 THEN add_job_wise_condidates.job_application_id ELSE NULL END) AS all_application'),
                            DB::raw(
                                'COUNT(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN add_job_wise_condidates.job_application_id ELSE NULL END) AS join_application'),
                        )
                        ->where('recommendation_relation.from_company_id', '=', $company_id)
                        ->where('recommendation_relation.to_company_id', '=', $row->company_id)
                        ->where('add_job_wise_condidates.thirdparty_company_id', '=', $row->company_id)
                        ->where('recommendation_relation.final_status', '=', 1)
                        ->first();

                        $newDate = $timeAgo = '';
                        $to_created_at = $row->to_created_at;
                        $newDate = date('d M, Y', strtotime($to_created_at));
                        $timeAgo = GeneralMethod::timeAgo($to_created_at);



    @endphp
       
       <div class="tbl-r" onclick="openModal(1)">
<input type="checkbox" class="tbl-cb" onclick="event.stopPropagation()">
<div class="tbl-a">
@if (!empty($logo_path))
<div class="tbl-av {{ $bgTextDecore }}">
    <img data-row-id="{{ $row->client_id  }}" src="{{ $logo_path }}" height="100px" width="100px" class="{{$client_type_css }} border-radius-5" data-row-id="{{$row->client_id }}"></div>
@else
<div class="tbl-av {{$client_type_css }} {{ $bgTextDecore }}" data-row-id="{{$row->client_id }}">{{ $initials }}</div>
@endif
<div class="tbl-ai {{ $client_type_css }}" data-row-id="{{$row->client_id }}">
<div class="tbl-n">{{ ucwords(mb_substr(@$row->company_name, 0, 30))  }}</div>
<div class="tbl-t">{{ @$ind_name }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
</div>
</div>
<div class="tbl-l">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
<div>
<div class="tbl-lc">{{ ucwords(mb_substr($city, 0, 30)) }}</div>
<div class="tbl-ls">{{ @$locInfostate}} | {{ $country_name }}</div>
</div>
</div>
<div class="tbl-j">
    @php
        $Active = $Hold = $Closed = 0;
        $RowJobs = GeneralMethod:: agencyJobsCountAllstatus($row->company_id,$company_id);
        $Active = $RowJobs->job_active ?? 0;
        $Hold = $RowJobs->job_hold ?? 0;
        $Closed = $RowJobs->job_inactive ?? 0;
    @endphp
<div class="tbl-jr"><span class="tbl-jn">{{ $Active }}</span><span class="tbl-js a">Active</span></div>
<div class="tbl-jr"><span class="tbl-jn">{{ $Closed }}</span><span class="tbl-js c">Closed</span></div>
<div class="tbl-jr"><span class="tbl-jn">{{ $Hold }}</span><span class="tbl-js d">Hold</span></div>
</div>
<div class="tbl-p">
    @php
        $all_candidates = $Joined = $offers = $all_interview = 0;
        $candidatesProfiles = GeneralMethod:: agencyCandidatesCountList($row->company_id,$company_id);
        $all_candidates = $candidatesProfiles->all_candidates ?? 0;
        $Joined = $candidatesProfiles->Joined ?? 0;
        $offers = $candidatesProfiles->offers ?? 0;
        $sourced = $candidatesProfiles->sourced ?? 0;
        $evaluated = $candidatesProfiles->evaluated ?? 0;
        $all_interview = $candidatesProfiles->all_interview ?? 0;
    @endphp

<div class="tbl-pi"><div class="tbl-pv">{{ @$sourced}}</div><div class="tbl-pl">Sourced</div></div>
<div class="tbl-pi"><div class="tbl-pv">{{ @$evaluated}}</div><div class="tbl-pl">Evaluated</div></div>
<div class="tbl-pi"><div class="tbl-pv">{{ @$all_interview}}</div><div class="tbl-pl">Interviews</div></div>
<div class="tbl-pi"><div class="tbl-pv">{{ @$offers}}</div><div class="tbl-pl">Offers</div></div>
<div class="tbl-pi"><div class="tbl-pv">{{ @$Joined}}</div><div class="tbl-pl">Joined</div></div>
</div>
<div>
<div class="tbl-co">{{ $newDate }}</div>
<div class="tbl-cv {{$client_type_css }}" data-row-id="{{$row->client_id }}">view agency</div>
</div>
</div>


    @endforeach
@else
    {{-- <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="6" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr> --}}
@endif
