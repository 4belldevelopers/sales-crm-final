@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();
    // dd(count($data));
@endphp

<link href="{{ asset('demo/css/css-circular-prog-bar.css') }}" rel="stylesheet">
<style>
    #recommendedlist td {
        position: relative !important;
    }

    #boostlisting td {
        position: relative !important;
    }

    #staffingPartnersRegister td {
        position: relative !important;
    }

    #boostlisting tbody tr {
        height: 80px !important;
    }

    #recommendedlist tbody tr {
        height: 80px !important;
    }

    #staffingPartnersRegister tbody tr {
        height: 80px !important;
    }

    .modal-backdrop {
        position: unset !important;
    }

    /* ── Skill-tag remove button: white × on the purple circle ── */
    .tagify__tag__removeBtn {
        -webkit-mask-image: none !important;
        mask-image: none !important;
        background-image: none !important;
        color: #fff !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
    }
    .tagify__tag__removeBtn::before,
    .tagify__tag__removeBtn::after {
        display: none !important;
        content: none !important;
    }

    #kt_activitiesStaffing_close svg{
        color: red;
    }

    #apptrackerfilter_close svg {
            color: red;
    }

    /* ============================================================
       Assigned Agency Tab — Modern Card Rows
       ============================================================ */
    .aa-header-row {
        display: flex; align-items: center;
        padding: 5px 16px 8px;
        font-size: 10.5px; font-weight: 600; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .aa-hcol-agency     { flex: 0 0 18%; }
    .aa-hcol-contact    { flex: 0 0 12%; }
    .aa-hcol-perf       { flex: 0 0 35%; text-align: center; }
    .aa-hcol-shared     { flex: 0 0 11%; padding-left: 8px; }
    .aa-hcol-status     { flex: 0 0 9%; text-align: center; }
    .aa-hcol-actions    { flex: 1 1 auto; text-align: right; }

    .aa-row {
        display: flex; align-items: center;
        background: #fff; border: 1px solid #e8ecf1;
        border-radius: 10px; padding: 10px 14px;
        margin-bottom: 6px;
        transition: box-shadow 0.15s ease, border-color 0.15s ease;
        gap: 0; min-height: 54px;
    }
    .aa-row:hover { box-shadow: 0 3px 12px rgba(0,0,0,0.07); border-color: #c7d2de; }

    .aa-col-agency     { flex: 0 0 18%; display: flex; align-items: center; gap: 9px; min-width: 0; padding-right: 8px; }
    .aa-col-contact    { flex: 0 0 12%; min-width: 0; padding-right: 8px; }
    /* Fixed width; KPIs size from content so spacing feels natural */
    .aa-col-perf       { flex: 0 0 35%; display: flex; align-items: center; justify-content: center; gap: 0; flex-wrap: nowrap; overflow: hidden; padding-right: 20px; }
    .aa-col-shared     { flex: 0 0 11%; min-width: 0; padding-left: 8px; padding-right: 6px; }
    .aa-col-status     { flex: 0 0 9%; text-align: center; }
    .aa-col-actions    { flex: 1 1 auto; display: flex; align-items: center; justify-content: flex-end; gap: 6px; position: relative; min-width: 130px; }

    /* Agency logo */
    .aa-logo {
        width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 15px; color: #fff;
        overflow: hidden; border: 2px solid #f0f4f8;
    }
    .aa-logo img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .aa-name    { font-weight: 600; font-size: 12px; color: #0f172a; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .aa-cat     { font-size: 10.5px; color: #64748b; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Contact */
    .aa-cnt-name { font-size: 12px; font-weight: 500; color: #0f172a; line-height: 1.3; }
    .aa-cnt-desg { font-size: 10.5px; color: #64748b; line-height: 1.3; }
    .aa-cnt-dash { font-size: 12px; color: #94a3b8; }
    .aa-cnt-btns { display: flex; gap: 4px; margin-top: 5px; }
    .aa-cnt-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 24px; height: 24px; border-radius: 6px;
        background: #f1f5f9; border: none; cursor: pointer;
        color: #64748b; font-size: 11px;
        transition: background 0.12s, color 0.12s;
    }
    .aa-cnt-btn:hover { background: #e2e8f0; color: #0f172a; }

    /* KPI badges — natural-width per item so padding is consistent */
    .aa-kpi {
        display: flex; flex-direction: column; align-items: center; gap: 3px;
        flex: 0 1 auto; padding: 0 10px;
        border-right: 1px solid #e2e8f0;
    }
    .aa-kpi:last-child { border-right: none; }
    .aa-kpi-num   { font-size: 13px; font-weight: 700; color: #1e293b; line-height: 1; }
    .aa-kpi-label { font-size: 9px; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: 0.2px; white-space: nowrap; }
    .aa-kpi-sep   { display: none; }

    /* Shared date */
    .aa-shared-rel { font-size: 12px; font-weight: 500; color: #1e293b; }
    .aa-shared-abs { font-size: 10.5px; color: #94a3b8; margin-top: 2px; }

    /* Status badges */
    .aa-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 600; line-height: 1; white-space: nowrap;
    }
    .aa-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .aa-badge-open    { background: #dcfce7; color: #16a34a; }
    .aa-badge-closed  { background: #f1f5f9; color: #64748b; }
    .aa-badge-paused  { background: #fef3c7; color: #d97706; }
    .aa-badge-expired { background: #fee2e2; color: #dc2626; }

    /* Actions */
    .aa-terms-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 11px; border-radius: 7px;
        border: 1.5px solid #e2e8f0; background: #fff;
        font-size: 11.5px; font-weight: 500; color: #475569;
        cursor: pointer; white-space: nowrap;
        transition: border-color 0.12s, color 0.12s, background 0.12s;
    }
    .aa-terms-btn:hover { border-color: #6366f1; color: #6366f1; background: #f8f7ff; }

    .aa-dots-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 7px;
        border: 1.5px solid #e2e8f0; background: #fff;
        cursor: pointer; color: #64748b; font-size: 13px;
        transition: background 0.12s, border-color 0.12s;
    }
    .aa-dots-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #334155; }

    .aa-ddmenu {
        position: absolute; right: 0; top: calc(100% + 5px);
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.10);
        z-index: 200; min-width: 165px; padding: 5px 0; display: none;
    }
    .aa-ddmenu.open { display: block; }
    .aa-ddi {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 14px; font-size: 12px; color: #374151;
        cursor: pointer; border: none; background: none;
        width: 100%; text-align: left; white-space: nowrap;
        transition: background 0.1s;
    }
    .aa-ddi:hover { background: #f8fafc; }
    .aa-ddi.danger { color: #ef4444; }
    .aa-ddi.danger:hover { background: #fff5f5; }

    /* Empty state */
    .aa-empty {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 56px 24px; text-align: center;
    }
    .aa-empty-icon {
        width: 68px; height: 68px; border-radius: 50%; background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px; font-size: 26px; color: #94a3b8;
    }
    .aa-empty-title { font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 6px; }
    .aa-empty-sub   { font-size: 12.5px; color: #94a3b8; max-width: 280px; line-height: 1.6; }

    /* Responsive — tablet / mobile */
    @media (max-width: 991px) {
        .aa-header-row { display: none !important; }
        .aa-row        { flex-wrap: wrap; gap: 10px; padding: 12px; }
        .aa-col-agency  { flex: 1 1 100%; }
        .aa-col-contact { flex: 1 1 100%; }
        .aa-col-perf    { flex: 1 1 100%; justify-content: flex-start; flex-wrap: wrap; gap: 6px; }
        .aa-kpi         { flex: 0 0 auto; border-right: none; padding: 3px 8px;
                          background: #f8fafc; border-radius: 5px; min-width: 52px; }
        .aa-col-shared  { flex: 1 1 45%; }
        .aa-col-status  { flex: 1 1 45%; text-align: left; }
        .aa-col-actions { flex: 1 1 100%; justify-content: flex-start; min-width: unset; }
    }

</style>

<link href="{{ asset('demo/css/find-agency.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    


<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript">
</script>
<?php

$current_job_id = '';
$current_job_id = @$job_id;

?>
<input type="hidden" name="current_job_id" id="current_job_id" value="<?php echo @$current_job_id; ?>" />
<div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true"
    data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
    data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px"
    style="">

    <div class="card card-custom mx-2 mb-2 d-none">
        <div class="card-body py-0">

            <table id="job_request" class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class">
                <!--begin::Table head-->
                <tbody>
                    <tr style="height: 102px; position: relative;">
                        <td class="d-flex align-items-center">
                            @php

                                    $bgTextDecore = $strVal = '';
                                    $str = trim(@$job->title);
                                    $str = $strVal = strtolower($str[0]);
                                    $str = ord($str);
                                    if ($str >= 97 && $str <= 101) {
                                        $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                                    } elseif ($str >= 102 && $str <= 106) {
                                        $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                                    } elseif ($str >= 107 && $str <= 111) {
                                        $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                                    } elseif ($str >= 112 && $str <= 116) {
                                        $bgTextDecore = 'fs-3 bg-light-success text-success';
                                    } elseif ($str >= 117 && $str <= 121) {
                                        $bgTextDecore = 'fs-3 bg-dark text-secondary';
                                    } else {
                                        $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                                    }
                                    $clients_name = ucwords(mb_substr($job->client_name, 0, 30));
                                    $recommendation_id = '';
                                    $recommendation_id = $job->recommendation_id;

                                    $logo_path = '';
                                        //$logo_path				=	url('img/client-default.png');
                                        if (!empty($job->logo_logical_path)) {
                                            $logo_path = @$job->logo_logical_path;
                                           
                                            if (
                                                $job->thirdparty_counter > 0 &&
                                                $recommendation_id > 0 &&
                                                @$job->to_company_id == auth()->user()->company_id
                                            ) {
                                                $logo_path = @$job->logo;
                                            }
                                        }elseif($job->self_company==1){
                                            $logo_path = @$job->logo;
                                        }


                                    if ($recommendation_id > 0 && @$job->to_company_id == auth()->user()->company_id) {
                                        $clients_name = ucwords(mb_substr(@$job->company_name, 0, 30));

                                        if (!empty(@$job->logo) && strlen(@$job->logo) > 10) {
                                            $logo_path = @$job->logo;
                                        }
                                    }
                                    

                                    $icon =
                                        '<div data-row-id="' .
                                        $job->id .
                                        '" class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 " style="float:left">
                                    <a href="javascript:void(0);" data-row-id="' .
                                        $job->id .
                                        '" class="show-detail">
                                    <div class="symbol-label ' .
                                        $bgTextDecore .
                                        ' border-radius-5">' .
                                        ucwords($strVal) .
                                        '</div>
                                    </a>
                                </div>';

                               $logo_path = GeneralMethod::companyDetails($job->id);

                                if (!empty($logo_path)) {
                                    $icon =
                                        '<div data-row-id="' .
                                        $job->id .
                                        '" class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                                                    <a href="javascript:void(0);">
                                                        <div class="symbol-label border-radius-5">
                                                        <img src="' .
                                        $logo_path .
                                        '" alt="Company Logo" class="border-radius-5 w-100">
                                                        </div>
                                                    </a>
                                                </div>';
                                    $skilltag = '';
                                    $skilltagclass = '';
                                    if (empty($limit)) {
                                        $skillArrList = $skillArr ?? [];
                                        if (!empty($skillArrList)) {
                                            foreach ($skillArrList as $sval) {
                                                $skilltag .=
                                                    '<span class="badge badge-light my-1">' . ucfirst($sval->skill_name) . '</span>  ';
                                            }
                                        }
                                    } else {
                                        $skilltagclass = 'd-none';
                                    }
                                }

                                    echo ' <div class=" d-flex align-items-center border-0" >
                                            
                                            ' .
                                        $icon .
                                        '
                                        
                                        <div class="d-flex flex-column"> 
                                                <a href="javascript:void(0);" data-row-id="' .
                                        $job->id .
                                        '" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip1" title="" data-bs-original-title="2">
                                                    ' .
                                        ucwords(mb_substr(@$job->title, 0, 30)) .
                                        '
                                                </a> 
                                                <a href="javascript:void(0);" data-row-id="' .
                                        $job->id .
                                        '" class="fw-bold text-muted d-block fs-7 text-hover-primary">
                                                   '.$clients_name.'
                                                </a> 
                                            </div> 
                                        </div> 
                                        <div class="' .
                                        $skilltagclass .
                                        '" style="position: absolute; bottom: 0; white-space: nowrap;width: 100%;overflow: hidden;">
                                                    <span class="badge badge-light-success my-1">Skills:</span>
                                                
                                                ' .
                                        $skilltag .
                                        '';
                            @endphp
                        </td>
                        <td>
                            @php
                                echo '<div class="d-flex flex-column"> 
                                            <span class="text-dark fw-bolder fs-6">' .
                                    @$job->max_exp .
                                    ' Y</span> 
                                            <span class="text-muted fw-bold text-muted d-block fs-7">' .
                                    @$job->min_exp .
                                    ' Y</span> 
                                        </div>';

                            @endphp
                        </td>
                        <td>
                            @php
                                $min_sal_mode = $max_sal_mode = $max_salary_currency = $max_salary = $min_salary = '';
                                $sal_mode =  $job->max_sal_mode;
                                $min_salary_currenc =  $job->min_salary_currency;
                                $min_sal_mode = strtoupper(mb_substr($salaryMode[$sal_mode], 0, 1));
                                $max_sal_mode = strtoupper(mb_substr($salaryMode[$sal_mode], 0, 1));
                                $salary_currency = $currencySymbole[@$min_salary_currency];

                                $cur = $min_salary_currency;
                                $mod = $sal_mode;
                                $currencymain = GeneralMethod::currency(); // Currency code
                                $currency = $currencymain[$cur];
                                $convertedAmounEctc = GeneralMethod::conversionRates($currency, $job->max_salary, 0);
                                $convertedAmounCtc = GeneralMethod::conversionRates($currency, $job->min_salary, 0);

                                $ectc = $convertedAmounEctc[$mod];
                                $ctc = $convertedAmounCtc[$mod];

                                $max_salary = '';
                                $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
                                $min_salary = '';
                                $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);

                                echo '<div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">' .
                                    $salary_currency .
                                    $max_salary .
                                    '/' .
                                    $min_sal_mode .
                                    '</span>                    
                                        <span class="fw-bold text-muted d-block fs-7">' .
                                    $salary_currency .
                                    $min_salary .
                                    '/' .
                                    $max_sal_mode .
                                    '</span>
                                    </div>';
                            @endphp         
                         </td>
                        <td>
                            @php
                            $country_id = $country_name = '';
                            $jobid = $job->id;
                            $client_loc = DB::table('client_location_jobs')
                                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                                ->select(
                                    'client_location.address',
                                    'client_location.city',
                                    'client_location.postcode',
                                    'client_location.country_id',
                                    'city_list.city as city_name',
                                )
                                ->where('client_location_jobs.job_id', $jobid)
                                ->first();

                                // dd($client_loc);
        
                            $country_id = @$client_loc->country_id;
                            if (@$country_id > 0) {
                                $country_name = GeneralMethod::getCountryName($country_id);
                                foreach ($country_name as $cval) {
                                    $country_name = ucwords($cval['sortname']);
                                }
                            }
        
                            $city = mb_substr(@$client_loc->city, 0, 6);
                            // if (@$client_loc->country_id == 101) {
                            $city = mb_substr(@$client_loc->city_name, 0, 6);
                            // }
        
                            $jobLocation = DB::table('client_location_jobs')
                                ->where('client_location_jobs.job_id', $jobid)
                                ->count();
        
                            $jobCitySelected = DB::table('client_city_jobs')->where('client_city_jobs.job_id', $jobid)->count();
        
                            $employment_type = 'Full-time';
                            if ($job->employment_type == 1) {
                                $employment_type = 'Contract';
                            }
        
                            if ($job->work_type == 'yes') {
                                echo ' <div class="d-flex flex-column">
                                <span class="text-dark fw-bolder fs-6">Remote</span>
                                <div class="d-flex align-items-center">
                                  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                    ucwords(@$employment_type) .
                                    '" >' .
                                    ucwords(mb_substr(@$employment_type, 0, 10)) .
                                    '</span>
                                  </div>
                                </div>
                              </div>';
                            } elseif ($job->work_type == 'no') {
                                if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                    $client_loc = DB::table('client_city_jobs')
                                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                        ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                        ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                        ->select('city_list.city as city_name', 'countries.country_name')
                                        ->where('client_city_jobs.job_id', $jobid)
                                        ->first();
                                    $city = mb_substr(@$client_loc->city_name, 0, 21);
                                    $country_name = mb_substr(@$client_loc->country_name, 0, 6);
        
                                    echo ' <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center">
                                          <span class="text-dark fw-bolder fs-6">' .
                                        ucwords(mb_substr(@$city, 0, 10)) .
                                        '</span>
                                        </div>
                                        
                                      <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                        ucwords(@$employment_type) .
                                        '" >' .
                                        ucwords(mb_substr(@$employment_type, 0, 10)) .
                                        '</span>
                                      </div>
                                    </div>
                                  </div>';
                                } else {
                                    echo ' <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center">
                                          <span class="text-dark fw-bolder fs-6">Multiple</span>
                                        </div>
                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                        ucwords(@$employment_type) .
                                        '" >' .
                                        ucwords(mb_substr(@$employment_type, 0, 10)) .
                                        '</span>
                                  </div>
                                </div>
                              </div>';
                                }
                            } elseif ($job->work_type == 'any') {
                                if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                    $client_loc = DB::table('client_city_jobs')
                                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                        ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                        ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                        ->select('city_list.city as city_name', 'countries.country_name')
                                        ->where('client_city_jobs.job_id', $jobid)
                                        ->first();
                                    $city = mb_substr(@$client_loc->city_name, 0, 21);
                                    $country_name = mb_substr(@$client_loc->country_name, 0, 6);
        
                                    echo ' <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center">
                                          <span class="text-dark fw-bolder fs-6">' .
                                        ucwords(mb_substr(@$city, 0, 10)) .
                                        '</span>
                                        </div>
                                        
                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                        ucwords(@$employment_type) .
                                        '" >' .
                                        ucwords(mb_substr(@$employment_type, 0, 10)) .
                                        '</span>
                                      </div>
                                    </div>
                                  </div>';
                                } else {
                                    echo ' <div class="d-flex flex-column">
                                <div class="d-flex align-items-center">
                                  <span class="text-dark fw-bolder fs-6">Remote</span>
                                  &nbsp;|&nbsp;
                                          <span class="text-dark fw-bolder fs-6">Multiple</span>
                                        </div>
                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                        ucwords(@$employment_type) .
                                        '" >' .
                                        ucwords(mb_substr(@$employment_type, 0, 10)) .
                                        '</span>
        
                                  </div>
                                </div>
                              </div>';
                                }
                            }
        
                        @endphp
                        </td>
                        <td>
                            @php
                                    $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                                    $status = ucwords($job->status);
                                    $class = $job_status[$job->status];
                                    $recommendation_id = $job->recommendation_relation_id;

                                    if ($status == 'Active') {
                                        $status = 'Hiring';
                                    }

                                    if ($status == 'Inactive') {
                                        $status = 'Closed';
                                    }

                                    $final_status = '';
                                    if (
                                        ($job->final_status == 2 || $job->final_status == 3) &&
                                        @$job->to_company_id == auth()->user()->company_id
                                    ) {
                                        $status = 'Removed';
                                        $class = 'closed-bg';
                                    }

                                    $ulink =
                                        '<a href="javascript.void(0);" data-row-id="' .
                                        @$job->id .
                                        '" class="d-flex flex-column ' .
                                        @$class .
                                        ' status_stage text-hover-success">' .
                                        $status .
                                        '</a>';

                                    $recommendation_text = 'Custom';
                                   

                                    echo '<div class="d-flex flex-column">
                                    ' .
                                        $ulink .
                                        '
                                    <span class="fw-bold text-muted d-block fs-7">' .
                                        @$job->id .
                                        ' -' .
                                        $recommendation_text .
                                        '</span>
                                    </div>';
                            @endphp           
                        </td>
                        <td>
                            @php

                                $total_positions = '';
                                $total_positions = number_format($job->total_positions);
            
                                $created_at = '';
                                $created_at = $job->created_at;
                                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); //date("d M, Y", strtotime($created_at));
                                echo '<div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">' .
                                    $newDate .
                                    '</span>
                                    <span class="text-muted fw-bold d-block fs-7">' .
                                    $total_positions .
                                    ' Opening</span>
                                </div>';
            
                            @endphp           
                        </td>
                        <th>
                        </th>
                    </tr>
                </tbody>
                <!--end::Table head-->
                
            </table>

        </div>
    </div>
    <div class="card card-custom mx-2 mb-2 d-none">
        <div class="card-body py-0 pt-6">
             <table id="" class="table align-middle table-row-dashed fs-6 d-none">
                <!--begin::Table head-->
                <tbody>
                    <tr style="position: relative;">
                        <td class="d-flex align-items-center">
                           <h3 class="text-muted fw-bold text-muted">Publish On Agency Marketplace</h3>
                        </td>
                    </tr>
                </tbody>
                <!--end::Table head-->
            </table>
            
            <table id="" class="table align-middle table-row-dashed fs-6 d-none">
                <!--begin::Table head-->
                <tbody>
                    <tr style="position: relative;">
                        <td>
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style=" float:left;margin-left: 10px !important;"> 
								<a href="javascript:void(0);">
									<div class="symbol-label border-radius-5"> 
                                        <img alt="Logo" src="{{ asset('img/megaphone.png')}}" class="border-radius-5 w-100">
                                    </div>
								</a> 
							</div>
                            <div class="d-flex flex-column" style=" float:left;"> 
								<span class="text-dark fw-bolder fs-4">Publish this job to the Agency Marketplace</span>   
								<span class="fw-bold text-muted d-block fs-7">Verified agencies can accept the T&C and submit candidate profiles for this job</span>
							</div>
                        </td>
                        <td>           
                        </td>
                        
                        <td>           
                        </td>

                        <td style="">
                           
                            

                                {{-- <span class="text-muted fw-bold d-block fs-7">For Agencies</span> --}}
                            </div>        
                        </td>
                    </tr>
                </tbody>
                <!--end::Table head-->
            </table>
        </div>
    </div>

    <!--begin::Tabs-->
    <div class="mx-2 mb-2">

        <div class="row gy-5 g-xl-10">
                    <!--begin::Col-->
                    <div class="col-xl-4 mb-xl-10">
                        
                        <style>
                            .sidebar-section {
                                border-bottom: 1px solid #e5e7eb;
                                padding-bottom: 2px;
                                margin-bottom: 10px;
                            }
                            .sidebar-section.no-border { border-bottom: none; }

                            .section-header {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                margin-bottom: 16px;
                            }
                            .section-title { display: flex; align-items: center; gap: 8px; }

                            .step-num {
                                background: #2563eb;
                                color: white;
                                width: 20px; height: 20px;
                                border-radius: 4px;
                                display: flex; align-items: center; justify-content: center;
                                font-size: 10px; font-weight: 600;
                            }

                            .section-title h3 { font-size: 14px; font-weight: 600; }

                            .score-green {
                                color: #22c55e;
                                font-weight: 700;
                                font-size: 16px;
                            }

                            /* Job Summary */
                            .job-title-sidebar { font-size: 13px; margin-bottom: 4px; }

                            .company-name {
                                font-size: 11px;
                                color: #6b7280;
                                display: block;
                                margin-bottom: 12px;
                            }

                            .job-meta {
                                display: flex;
                                flex-direction: column;
                                gap: 8px;
                                margin-bottom: 16px;
                                font-size: 12px;
                                color: #4b5563;
                            }

                            .meta-row { display: flex; gap: 16px; }

                            .meta-item {
                                display: flex;
                                align-items: center;
                                gap: 6px;
                            }

                            .meta-item i {
                                color: #9ca3af;
                                font-size: 14px;
                            }

                            .skills-tags {
                                display: flex;
                                gap: 6px;
                                margin-bottom: 16px;
                                flex-wrap: wrap;
                            }

                            .tag {
                                background: #f3f4f6;
                                color: #374151;
                                padding: 4px 10px;
                                border-radius: 100px;
                                font-size: 10px;
                                font-weight: 500;
                            }

                            .tag.more {
                                background: none;
                                border: 1px solid #e5e7eb;
                            }

                            .action-buttons-group {
                                display: flex;
                                gap: 10px;
                            }

                            .action-buttons-group .btn {
                                flex: 1;
                                padding: 6px 0;
                                font-size: 10px;
                            }

                            /* Job Readiness */
                            .progress-bar-container {
                                height: 6px;
                                background: #e5e7eb;
                                border-radius: 4px;
                                margin-bottom: 16px;
                            }

                            .progress-bar {
                                height: 100%;
                                background: #22c55e;
                                border-radius: 4px;
                            }

                            .readiness-list {
                                list-style: none;
                                display: flex;
                                flex-direction: column;
                                gap: 10px;
                                margin-bottom: 12px;
                            }

                            .readiness-list li {
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                font-size: 11px;
                                color: #374151;
                            }

                            .readiness-list li.warning {
                                color: #f59e0b;
                            }

                            .icon-green { color: #22c55e; font-size: 14px; }
                            .icon-yellow { color: #f59e0b; font-size: 14px; }

                            .link-text {
                                color: #2563eb;
                                font-size: 11px;
                                font-weight: 500;
                                display: flex;
                                align-items: center;
                                gap: 4px;
                            }

                            /* Marketplace Insights */
                            .insight-row {
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                margin-bottom: 12px;
                            }

                            .insight-value {
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                font-weight: 600;
                                width: 80px;
                            }

                            .insight-label {
                                color: #4b5563;
                                font-size: 11px;
                            }

                            .icon-blue { color: #3b82f6; }
                            .icon-orange { color: #F97316; }

                            /* Publish Settings */
                            .radio-group {
                                display: flex;
                                flex-direction: column;
                                gap: 12px;
                                margin-bottom: 20px;
                            }

                            .radio-label {
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                font-size: 11px;
                                cursor: pointer;
                            }

                            .radio-label input[type="radio"] {
                                accent-color: #2563eb;
                                width: 16px;
                                height: 16px;
                                cursor: pointer;
                            }

                            .input-controls {
                                display: flex;
                                gap: 12px;
                                margin-bottom: 20px;
                            }

                            .input-group {
                                flex: 1;
                                display: flex;
                                flex-direction: column;
                                gap: 6px;
                            }

                            .input-group label {
                                font-size: 12px;
                                color: #4b5563;
                                font-weight: 500;
                            }

                            .input-group select {
                                padding: 8px 12px;
                                border: 1px solid #e5e7eb;
                                border-radius: 6px;
                                font-family: inherit;
                                font-size: 11px;
                                outline: none;
                            }

                            /* Boost Card */
                            .boost-card {
                                background: #FFFBEB;
                                border: 1px solid #FEF3C7;
                                border-radius: 8px;
                                padding: 12px;
                                margin-bottom: 20px;
                            }

                            .boost-header {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                margin-bottom: 6px;
                            }

                            .boost-title {
                                font-weight: 600;
                                color: #B45309;
                                display: flex;
                                align-items: center;
                                gap: 6px;
                                font-size: 13px;
                            }

                            .tag-recommended {
                                background: #FEF3C7;
                                color: #D97706;
                                font-size: 10px;
                                font-weight: 600;
                                padding: 2px 6px;
                                border-radius: 4px;
                                text-transform: uppercase;
                            }

                            .boost-footer {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                font-size: 10px;
                            }

                            .boost-desc { color: #92400E; }

                            .boost-link {
                                color: #2563eb;
                                font-weight: 600;
                                text-decoration: underline;
                            }

                            /* Notify Box */
                            .notify-box {
                                background: #eff6ff;
                                border-radius: 8px;
                                padding: 12px 16px;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                margin-bottom: 20px;
                            }

                            .notify-text {
                                font-size: 10px;
                                color: #1d4ed8;
                                line-height: 1.5;
                            }

                            .highlight-number {
                                font-size: 14px;
                                font-weight: 700;
                                color: #2563eb;
                            }

                            .notify-icon {
                                color: #2563eb;
                                font-size: 18px;
                            }

                            /* Footer */
                            .footer-actions {
                                display: flex;
                                justify-content: space-between;
                                margin-top: 16px;
                                padding: 0 10px;
                            }

                            input[type="radio"]:checked {
                                border-color: transparent;
                                background-color: currentColor;
                            }

                            /* ===== JOB SUMMARY ===== */
                            .job-summary {
                                margin-bottom: 20px;
                            }

                            .job-summary__header {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                margin-bottom: 16px;
                            }

                            .job-summary__title {
                                font-size: 14.5px;
                                font-weight: 700;
                                color: #0f172a;
                            }

                            .btn-edit {
                                display: inline-flex;
                                align-items: center;
                                gap: 5px;
                                background: none;
                                border: none;
                                font-family: inherit;
                                font-size: 12.5px;
                                font-weight: 500;
                                color: #64748b;
                                cursor: pointer;
                                padding: 4px 8px;
                                border-radius: 6px;
                                transition: all 0.2s;
                            }

                            .btn-edit:hover {
                                color: #2563eb;
                                background: #f1f5f9;
                            }

                            /* ===== JOB DETAILS ===== */
                            .job-details {
                                display: flex;
                                flex-direction: column;
                                gap: 12px;
                            }

                            .job-detail-row {
                                display: flex;
                                align-items: flex-start;
                                gap: 12px;
                            }

                            .job-detail-label {
                                width: 120px;
                                flex-shrink: 0;
                                font-size: 12.5px;
                                color: #94a3b8;
                                font-weight: 500;
                            }

                            .job-detail-value {
                                font-size: 12.5px;
                                color: #1e293b;
                                font-weight: 500;
                            }

                            .job-detail-tags {
                                display: flex;
                                flex-wrap: wrap;
                                gap: 6px;
                            }

                            /* ===== VISIBILITY ===== */
                            .visibility-section {
                                border-top: 1px solid #e2e8f0;
                                padding-top: 20px;
                                margin-bottom: 24px;
                            }

                            .visibility-header {
                                display: flex;
                                align-items: center;
                                gap: 6px;
                                margin-bottom: 16px;
                            }

                            .visibility-title {
                                font-size: 14.5px;
                                font-weight: 700;
                                color: #0f172a;
                            }

                            .visibility-options {
                                display: flex;
                                flex-direction: column;
                                gap: 14px;
                            }

                        </style>

                        <div class="card card-flush h-xl-100">
                                <!--begin::Heading-->
                                <div class="mb-2 mt-5 m-2 sidebar-section">
                                    <!--begin::Title-->
                                    
                                        <div class="section-header">
                                                <div class="section-title" style="position: relative;">
                                                    <span class="step-num">1</span>
                                                    <h3>Publish this job to the Agency Marketplace </h3>
                                                    <span style="position: absolute; top: 15px; left: 28px;">Make your job visible to relevant agencies</span>
                                                </div>
                                                <i class="ri-arrow-up-s-line"></i>
                                        </div>

                                         <div class="notify-box">                                            
                                            <div class="notify-icon"><i class="fa fa-location-arrow"></i></div>
                                            <div class="notify-text">
                                                 <span class="highlight-number">Your job will be visible to 20 verified agencies</span> <br>
                                               Increase your chances of faster hiring
                                            </div>
                                        </div>


                                    <div class="mt-5" style="margin: 5px;">  

                                        @php

                                            $min_sal_mode = $max_sal_mode = $max_salary_currency = $max_salary = $min_salary = '';
                                            $sal_mode =  $job->max_sal_mode;
                                            $min_salary_currenc =  $job->min_salary_currency;
                                            $min_sal_mode = strtoupper(mb_substr($salaryMode[$sal_mode], 0, 1));
                                            $max_sal_mode = strtoupper(mb_substr($salaryMode[$sal_mode], 0, 1));
                                            $salary_currency = $currencySymbole[@$min_salary_currency];

                                            $cur = $min_salary_currency;
                                            $mod = $sal_mode;
                                            $currencymain = GeneralMethod::currency(); // Currency code
                                            $currency = $currencymain[$cur];
                                            $convertedAmounEctc = GeneralMethod::conversionRates($currency, $job->max_salary, 0);
                                            $convertedAmounCtc = GeneralMethod::conversionRates($currency, $job->min_salary, 0);

                                            $ectc = $convertedAmounEctc[$mod];
                                            $ctc = $convertedAmounCtc[$mod];

                                            $max_salary = '';
                                            $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
                                            $min_salary = '';
                                            $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);

                                        @endphp

                                        <!-- Section 1: Job Summary -->
                                        <div class="sidebar-section">
                                            <div class="section-header">
                                                <div class="section-title">
                                                    {{-- <span class="step-num">1</span> --}}
                                                    <h3>Job Summary</h3>
                                                </div>
                                                <i class="ri-arrow-up-s-line"></i>
                                            </div>
                                            <div class="section-content">
                                                



                                                <div class="job-details">
                                                    <div class="job-detail-row">
                                                        <span class="job-detail-label">Job Title</span>
                                                        <span class="job-detail-value">{{ ucwords(mb_substr(@$job->title, 0, 30)) }} ({{ @$job->max_exp }}Y)</span>
                                                    </div>
                                                    <div class="job-detail-row">
                                                        <span class="job-detail-label">Experience</span>
                                                        <span class="job-detail-value">{{ @$job->min_exp }} - {{ @$job->max_exp }} Years</span>
                                                    </div>
                                                    <div class="job-detail-row">
                                                        <span class="job-detail-label">Location</span>

                                                         @php
                             $client_loc = $jobLocation ?? null;
                             if (empty($client_loc)) {
                                 $client_loc = DB::table('client_location_jobs')
                                     ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                                     ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                                     ->select(
                                         'client_location.address',
                                         'client_location.city',
                                         'client_location.postcode',
                                         'client_location.country_id',
                                         'city_list.city as city_name',
                                     )
                                     ->where('client_location_jobs.job_id', $job->id)
                                     ->first();
                             }
        
                                                                $country_id = @$client_loc->country_id;
                                                                if (@$country_id > 0) {
                                                                    $country_name = GeneralMethod::getCountryName($country_id);
                                                                    foreach ($country_name as $cval) {
                                                                        $country_name = ucwords($cval['sortname']);
                                                                    }
                                                                }
        
                                                                $city = mb_substr(@$client_loc->city, 0, 6);
                                                                $city = mb_substr(@$client_loc->city_name, 0, 6);
                                            
                                                                $jobCitySelected = DB::table('client_city_jobs')->where('client_city_jobs.job_id', $job->id)->count();
                                            
                                                                $employment_type = 'Full-time';
                                                                if ($job->employment_type == 1) {
                                                                    $employment_type = 'Contract';
                                                                }
        
                                                                if ($job->work_type == 'yes') {
                                                                    echo '<span class="job-detail-value">Remote</span>';
                                                                } elseif ($job->work_type == 'no') {
                                                                if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                                                    $client_loc = DB::table('client_city_jobs')
                                                                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                                                        ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                                                        ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                                                        ->select('city_list.city as city_name', 'countries.country_name')
                                                                        ->where('client_city_jobs.job_id', $job->id)
                                                                        ->first();
                                                                    $city = mb_substr(@$client_loc->city_name, 0, 21);
                                                                    $country_name = mb_substr(@$client_loc->country_name, 0, 6);
                                        
                                                                    echo ' <span class="job-detail-value">'.ucwords(mb_substr(@$city, 0, 10)).' </span>';
                                                                } else {
                                                                        echo '<span class="job-detail-value">Multiple Locations </span>';
                                                                    }
                                                            } elseif ($job->work_type == 'any') {
                                                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                                                $client_loc = DB::table('client_city_jobs')
                                                                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                                                    ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                                                    ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                                                    ->select('city_list.city as city_name', 'countries.country_name')
                                                                    ->where('client_city_jobs.job_id', $job->id)
                                                                    ->first();
                                                                $city = mb_substr(@$client_loc->city_name, 0, 21);
                                                                $country_name = mb_substr(@$client_loc->country_name, 0, 6);
                                    
                                                                echo ' <span class="job-detail-value">'.ucwords(mb_substr(@$city, 0, 10)) .'</span>';

                                                            } else {
                                                                echo '<span class=job-detail-value">Multiple Locations </span>';
                                                            }
                                                        }
                                                        @endphp

                                                    </div>
                                                    <div class="job-detail-row">
                                                        <span class="job-detail-label">Employment Type</span>
                                                        <span class="job-detail-value">{{ ucwords(mb_substr(@$employment_type, 0, 10)) }}</span>
                                                    </div>
                                                    <div class="job-detail-row">
                                                        <span class="job-detail-label">Salary Range</span>
                                                        <span class="job-detail-value">{!!$salary_currency !!} {{ $min_salary }} - {{ $max_salary }} / {{ $max_sal_mode }}</span>
                                                    </div>
                                                    <div class="job-detail-row">
                                                        <span class="job-detail-label">Openings</span>
                                                        <span class="job-detail-value">{{ number_format($job->total_positions) }}</span>
                                                    </div>
                                                    <div class="job-detail-row">
                                                        <span class="job-detail-label">Skills</span>
                                                        <div class="job-detail-tags">
                                                            @php
                                                    $skilltag = '';
                                                    $skilltagclass = '';
                                                    $totalSkill = 0;
                                                    if (empty($limit)) {
                                                        $skillArrList = $skillArr ?? [];
                                                        if (!empty($skillArrList)) {
                                                            foreach ($skillArrList as $key => $sval) {
                                                                if($key<=2){
                                                                    $skilltag .=
                                                                    '<span class="tag">' . ucfirst($sval->skill_name) . '</span>  ';
                                                                }
                                                                $totalSkill++;
                                                            }
                                                        }
                                                    } else {
                                                        $skilltagclass = 'd-none';
                                                    }
                                                    @endphp
                                                   {!! $skilltag !!}
                                                   @if($totalSkill >3)
                                                     <span class="tag more">+{{ $totalSkill-3 }}</span>
                                                    @endif
                                                        </div>
                                                    </div>
                                                </div>



                                                {{-- <h4 class="job-title-sidebar">{{ ucwords(mb_substr(@$job->title, 0, 30)) }} ({{ @$job->max_exp }}Y)</h4>
                                                <span class="company-name">{{ $clients_name }}</span> --}}
                                               
                                            </div>
                                        </div>

                                        <!-- Section 4: Visibility Settings -->
                                        <div class="sidebar-section no-border">
                                            <div class="section-header">
                                                <div class="section-title">
                                                    {{-- <span class="step-num">3</span> --}}
                                                    <h3>Visibility</h3>
                                                </div>
                                            </div>
                                            <div class="section-content">
                                                <div class="radio-group">
                                                    <label class="radio-label">
                                                        <input type="radio" name="publishTo" checked>
                                                        <span class="radio-custom"></span> Recommended Agencies <span class="dim-text">(Best Match)</span>
                                                    </label>
                                                    <label class="radio-label">
                                                        <input type="radio" name="publishTo">
                                                        <span class="radio-custom"></span> All Agencies <span class="dim-text">(Max Reach)</span>
                                                    </label>
                                                </div>
                                                
                                                <div class="input-controls">
                                                    <div class="input-group">
                                                        <label>Max Applications</label>
                                                        <select><option>20</option></select>
                                                    </div>
                                                    <div class="input-group">
                                                        <label>Auto-expire in</label>
                                                        <select><option>7 Days</option></select>
                                                    </div>
                                                </div>
                                                

                                                 <button id="sp_list_two" class="btn btn-danger btn-block {{ in_array($text_change, ['Published', 'Approval Pending']) ? '' : 'd-none' }} post_to_public" data-c="1" data-row-id="0" data-matchcount="0" data-row-jobid="{{ @$job->id }}" style="width:100%"><i class="fa fa-location-arrow"></i> Unpublish Job</button>

                                                <button id="sp_list_one" class="btn btn-primary btn-block {{ $class_send }} {{ in_array($text_change, ['Published', 'Approval Pending']) ? 'd-none' : '' }}" data-c="0" data-row-id="0" data-matchcount="0" data-row-jobid="{{ @$job->id }}" style="width:100%"><i class="fa fa-location-arrow"></i> {{ $text_change_action }}</button>

                                                <div class="footer-actions">
                                                    <a href="javascript:void(0);" data-row-id="{{ $job->id }}" data-row-recommendation_relation_id="" class="link-muted client-terms-listing">View {{__('messages.Agency')}} T&C</a>
                                                    <a href="javascript:void(0);" class="link-muted text_change_text {{ $color_change_action }}">{{ $text_change }}</a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- <div class="text-center" style="margin: 5px;">                              
                                        <button id="sp_list_two" data-c="1" data-row-id="0" data-matchcount="0" data-row-jobid="{{ @$job->id }}" class="btn btn-sm btn-light-danger w-150 {{ in_array($text_change, ['Published', 'Approval Pending']) ? '' : 'd-none' }} post_to_public" style="width: 100%;">
                                            <i class="fa fa-location-arrow"></i><span class="text-danger text-hover-white fw-bolder fs-6">Unpublish Job</span>
                                        </button>
                                            
                                        <button id="sp_list_one" data-c="0" data-row-id="0" data-matchcount="0" data-row-jobid="{{ @$job->id }}" class="btn btn-sm {{ $color_change }} w-150 {{ $class_send }} {{ in_array($text_change, ['Published', 'Approval Pending']) ? 'd-none' : '' }}" style="width: 100%;">
                                            <i class="fa fa-location-arrow"></i><span class="btn-light-primary fw-bolder fs-6"> {{ $text_change_action }}</span>
                                        </button>    
                                        
                                

                                        <div data-row-id="{{ $job->id }}" data-row-recommendation_relation_id="" class="text-center mt-5">
                                            <a href="javascript:void(0);" class="fw-bolder text_change_text {{ $color_change_action }} w-150 fs-5">{{ $text_change }} </a>
                                            <span class="fw-bold text-muted d-block fs-7">Status</span>
                                        </div> 

                                    </div>

                                    
                                    <div data-row-id="{{ $job->id }}" data-row-recommendation_relation_id="" class="client-terms-listing text-center mt-5 mb-5">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-light-info w-150">View {{__('messages.Agency')}} T&C</a>
                                        <span class="fw-bold text-muted d-block fs-7">Upto 10 Agencies Applications</span>
                                    </div>  --}}

                                    <!--end::Title-->
                                </div>
                                <!--end::Heading-->
                                <!--begin::Links-->
                                
                        </div>
                    </div>    
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-xl-8 mb-5 mb-xl-10 p-0">
                                
                        <!--begin::Table Widget 4-->
                        <div class="card card-flush h-xl-100">
                            
                            <div class="section-header mt-5 m-2">
                                    <div class="section-title">
                                        <span class="step-num">2</span>
                                        <h3>Manually Select the Agency</h3>
                                        <span style="position: absolute; top: 29px; left: 33px;">Agencies that match your job requirements</span>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesStaffing_close" style="margin-right: 10px !important;">
                                    <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
                                    <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                                        <rect x="0" y="7" width="16" height="2" rx="1"></rect>
                                        <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
                                    </g>
                                    </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </button> 

                            </div>

                            {{-- <h5 class="fw-semibold text-gray-800 ml-3 lh-lg">Manually Select the Agency</h5> --}}
                             <!--Begin::Tabs Header-->
                            <div class="card-header card-header-tabs-line">
                                <div class="card-toolbar">
                                    
                                    <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x recommended_tab" role="tablist">
                                        <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#recommended">
                                                <span class="nav-text font-weight-bold">Recommended {{__('messages.Agency')}}</span> <span id="recommendedTotal"></span> </a> </li>
                                        <li class="nav-item me-3 register_tab"> <a class="nav-link" data-toggle="tab" href="#registered"> <span
                                                    class="nav-text font-weight-bold">My {{__('messages.Agency')}}</span> <span id="registeredTotal"></span> </a> </li>
                                        <li class="nav-item me-3 assigned_tab"> <a class="nav-link" data-toggle="tab" href="#assigned"> <span
                                                    class="nav-text font-weight-bold">Assigned {{__('messages.Agency')}}</span> <span id="assignedTotal"></span> </a> </li>
                                    </ul>
                                </div>
                            </div>
                            <!--end::Tabs Header-->


                            <!--begin::Card body-->
                            <div class="card-body p-0 pt-2">
                                <!--begin::Table-->
                                <div id="kt_table_widget_4_table_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                                    <!--Begin::Tabs Body-->
                                     <div class="card-body py-0">
                                        <div class="tab-content pt-5">
                                            
                                             <!--begin::Recommended Content-->
                                            <div class="tab-pane active show" id="recommended" role="tabpanel">
                                                <div class="card-body px-0 py-2">
                                                    <div class="d-flex align-items-center flex-wrap">
                                                        
                                                        <div class="col-lg-6 d-flex align-items-center px-2" style="width:85%; position: relative;">
                                                            <div class="search-input-wrap">
                                                                <input class="search-input" name="inputJobSearch" id="kt_tagify_3" value="{{ @$default_skill }}" type="text" placeholder="Search by skills..."/>
                                                            </div>
                                                            <div data-toggle="modal" data-target="#apptrackerfilter" style="position: absolute; z-index: 99; width: 100%; height: 100%; top: 0;"></div>
                                                            {{-- <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="{{ @$default_skill }}" placeholder="Search..." id="kt_tagify_3">
                                                            <div data-toggle="modal" data-target="#apptrackerfilter" style="position: absolute; z-index: 99; width: 100%; height: 100%; top: 0;"> --}}
                                                        </div>

                                                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                                                            <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end" data-toggle="modal" data-target="#apptrackerfilter">
                                                                <span class="svg-icon svg-icon-2">
                                                                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                        <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black">
                                                                        </path>
                                                                    </svg>
                                                                </span> Filter
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <!--Start Popup-->
                                                    <div class="modal fade show" id="apptrackerfilter" tabindex="-1" style="">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header flex-stack">
                                                                    <div>
                                                                        <h1>Find Trusted Hiring Agencies</h1>
                                                                        <p>Discover verified recruitment partners based on your hiring requirements.</p>
                                                                    </div>
                                                                    <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal" id="apptrackerfilter_close">
                                                                        <span class="svg-icon svg-icon-1">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)"></rect>
                                                                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"></rect>
                                                                            </svg>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="apptrackerfilter_body_content" id="">
                                                                    <div class="modal-body" style="padding: 2em;">

                                                                        <!-- Trust bar -->
                                                                        <div class="trust-bar">
                                                                        <div class="trust-item">
                                                                            <i class="ti ti-shield-check" aria-hidden="true"></i>
                                                                            <span>All agencies are verified</span>
                                                                        </div>
                                                                        <div class="trust-item">
                                                                            <i class="ti ti-medal" aria-hidden="true"></i>
                                                                            <span>Quality &amp; performance checked</span>
                                                                        </div>
                                                                        <div class="trust-item">
                                                                            <i class="ti ti-users" aria-hidden="true"></i>
                                                                            <span>Secure &amp; confidential</span>
                                                                        </div>
                                                                        </div>

                                                                        <!-- Search with tag input -->
                                                                        <div class="section">
                                                                        <p class="section-label">1. Search Skills / Keywords <span class="required">*</span></p>

                                                                        <div class="suggestions-wrap search-input-wrap">
                                                                        <input class="search-input"
                                                                                    name="kt_tagify_users" value="{{ @$default_skill }}" placeholder="Search by skills..."
                                                                                    id="kt_tagify_users" />
                                                                        </div>

                                                                        <p class="input-hint">Press <span>Enter</span> or <span>,</span> to add a keyword &nbsp;·&nbsp; Press <span>Backspace</span> to remove last</p>
                                                                        </div>

                                                                        <!-- Hiring type -->
                                                                        <div class="section">
                                                                            <p class="section-label">2. Hiring Type <span class="required">*</span></p>
                                                                            <div class="hiring-grid">
                                                                                <input type="hidden" id="hiring_type" name="hiring_type" value="2">
                                                                                <div class="hiring-card active-blue" id="card-full" onclick="selectHiringType('full')" role="button" tabindex="0" aria-pressed="true">
                                                                                <div class="hiring-icon blue"><i class="ti ti-briefcase" aria-hidden="true"></i></div>
                                                                                <div class="hiring-text">
                                                                                    <h3>Full-Time Hiring</h3>
                                                                                    <p>Permanent hiring solutions for long-term requirements.</p>
                                                                                </div>
                                                                                <div class="radio-dot checked-blue" id="radio-full"><div class="dot-inner"></div></div>
                                                                                </div>
                                                                                <div class="hiring-card" id="card-contract" onclick="selectHiringType('contract')" role="button" tabindex="0" aria-pressed="false">
                                                                                <div class="hiring-icon teal"><i class="ti ti-clock" aria-hidden="true"></i></div>
                                                                                <div class="hiring-text">
                                                                                    <h3>Contract Hiring</h3>
                                                                                    <p>On-demand talent, immediate joiners &amp; project-based hiring.</p>
                                                                                </div>
                                                                                <div class="radio-dot" id="radio-contract"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Additional filters -->
                                                                        <div class="section">
                                                                        <p class="section-label">3. Additional Filters <span class="optional">(Optional)</span></p>
                                                                        <div class="filters-grid">
                                                                            <select aria-label="Industry" class="" id="industry">
                                                                            <option value="" >Industry</option>
                                                                            @if ($td_industries)
                                                                                @foreach ($td_industries as $row)
                                                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                            </select>
                                                                            <select aria-label="Experience" id="experience">
                                                                            <option value="">Experience</option>
                                                                            <option>0–2 years</option>
                                                                            <option>3–5 years</option>
                                                                            <option>5–10 years</option>
                                                                            <option>10+ years</option>
                                                                            </select>
                                                                            <select aria-label="Location" id="country">
                                                                            <option value="">Location</option>
                                                                            @if ($cities)
                                                                                @foreach ($cities as $country)
                                                                                    <option value="{{ $country }}">{{ $country }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                            </select>
                                                                        </div>
                                                                        </div>

                                                                        <!-- Bottom bar -->
                                                                        <div class="bottom-bar">
                                                                        <div class="match-note">
                                                                            <i class="ti ti-stars" aria-hidden="true"></i>
                                                                            <span>We'll show agencies best matched to your requirements.</span>
                                                                        </div>
                                                                        <div class="agencies-info">
                                                                            <div class="avatars">
                                                                            <img src="https://i.pravatar.cc/28?img=3" alt="" />
                                                                            <img src="https://i.pravatar.cc/28?img=5" alt="" />
                                                                            <img src="https://i.pravatar.cc/28?img=8" alt="" />
                                                                            </div>
                                                                            <div class="agencies-count">
                                                                            <h4>{{ $activeCompanies }} verified agencies available</h4>
                                                                            <p><span class="green-dot"></span> Last updated just now</p>
                                                                            </div>
                                                                        </div>
                                                                        </div>

                                                                        <!-- Actions -->
                                                                        </div>
                                                                        <div class="modal-footer flex-center">
                                                                        <div class="div1">
                                                                            <button type="button" id="" class="btn btn-secondary me-3" data-dismiss="modal">Close</button>
                                                                            <button class="btn btn-primary changetextupdate" id="changetextupdate" onclick="newUpdateJobstatus()"><i class="ti ti-search" aria-hidden="true"></i>Find Agencies</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--End Popup-->                                                 
                                                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="recommendedlist">
                                                        <thead>
                                                            <tr class="fw-bolder text-muted" role="row">
                                                                <th class="sorting_disabled d-flex align-items-center" rowspan="1" colspan="1" style="width: 155px;">Agency & Specialization</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 41px;">Rates</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 93px;">Overview</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 109px;">Location & Team</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 102px;">Last Active</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 128px;">Action</th>
                                                            </tr>
                                                            <span id="boostkeywords"></span>
                                                        </thead>
                                                        <tbody id="recommendedlistBody"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!--end::Recommended Content-->
                                            <!--begin::Registered Content-->
                                            <div class="tab-pane" id="registered" role="tabpanel">
                                                <div class="card-body px-0 py-2">
                                                    <div class="d-flex align-items-center flex-wrap">
                                                        <div class="col-lg-6 d-flex align-items-center px-2" style="width:60%;position: relative;">
                                                            <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="" placeholder="Search..." id="kt_tagify_4" onchange="filtersdatatwo()" />
                                                            <div style="position: absolute; top: 0px; right: 36px;">
                                                                <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 8px;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                                                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="hiringtype4" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdatatwo()">
                                                                <option value="0" {{ @$job->employment_type == 0 ? 'selected' : '' }}> Full-time</option>
                                                                <option value="1" {{ @$job->employment_type == 1 ? 'selected' : '' }}>Contract</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                                                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country4" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdatatwo()">
                                                                <option value="Show All" selected>Location</option>
                                                                    @if (@$countries)
                                                                        @foreach (@$countries as $item)
                                                                        <option value="{{ $item->id }}" {{ @$jobLocationCountselect->id == $item->id ? 'selected' : '' }}>{{ $item->country_name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="staffingPartnersRegister">
                                                        <thead>
                                                            <tr class="fw-bolder text-muted" role="row">
                                                                <th class="sorting_disabled d-flex align-items-center" rowspan="1" colspan="1" style="width: 155px;">Agency Name</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 41px;">Similar Jobs</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 93px;">Rates</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 109px;">Size</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 109px;">Location</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 109px;">Last Active</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 128px;">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-gray-600 fw-bold" id="allregisteragency"></tbody>
                                                    </table>
                                                    <div id="paginationregisteragency"></div>
                                                </div>
                                            </div>
                                            <!--end::Requisitions Content-->
                                            <!--begin::Assigned Content-->
                                            <div class="tab-pane" id="assigned" role="tabpanel">
                                                <div class="px-1 pt-3 pb-1">
                                                    <!-- Search -->
                                                    <div class="mb-3 px-1">
                                                        <input type="text" class="form-control form-control-solid form-control-sm ps-3"
                                                               name="inputJobSearch" value="" placeholder="Search agencies..."
                                                               id="kt_tagify_5" onchange="filtersdatathree()" />
                                                    </div>
                                                    <!-- Column headers (desktop) -->
                                                    <div class="aa-header-row d-none d-lg-flex">
                                                        <div class="aa-hcol-agency">Agency</div>
                                                        <div class="aa-hcol-contact">Contact</div>
                                                        <div class="aa-hcol-perf">Performance <i class="fa-regular fa-circle-question ms-1" style="font-size:11px;color:#cbd5e1;" title="Metrics for this job"></i></div>
                                                        <div class="aa-hcol-shared">Job Shared</div>
                                                        <div class="aa-hcol-status">Status</div>
                                                        <div class="aa-hcol-actions">Actions</div>
                                                    </div>
                                                    <!-- Rows injected by AJAX -->
                                                    <div id="allAssignListagency"></div>
                                                    <div id="paginationAssignListagency" class="mt-3 px-1"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Assigned Content-->
                                    </div>
                                    <!--end::Tabs Body-->
                                </div>
                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Table Widget 4-->
                    </div>
                    <!--end::Col-->
                </div>
            </div>
            <!--end::Tabs-->
        </div>



<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script>

        function initTagifySection() {

            // =========================
            // ELEMENT CHECK
            // =========================

            const inputElm = document.querySelector('#kt_tagify_users');
            const inputJobSearch = document.querySelector('#kt_tagify_3');

            if (!inputElm || !inputJobSearch) {
                return;
            }

            // =========================
            // DESTROY OLD TAGIFY
            // =========================

            if (inputElm._tagify) {
                inputElm._tagify.destroy();
            }

            if (inputJobSearch._tagify) {
                inputJobSearch._tagify.destroy();
            }

            // =========================
            // TAGIFY 1
            // =========================

            const tagify = new Tagify(inputElm, {

                templates: {

                    tag: function(tagData) {

                        tagData = tagData || {};

                        const value = tagData.value || '';

                        return `
                            <tag title="${value}"
                                contenteditable='false'
                                spellcheck="false"
                                class="tagify__tag ${tagData.class ? tagData.class : ""}"
                                ${this.getAttributes(tagData)}
                                >

                                <div style="padding: 2px 6px; display:flex; align-items:center; gap:5px;">

                                    <span class="tagify__tag-text">${value}</span>

                                    <span style="
                                            width:16px;
                                            height:16px;
                                            border-radius:50%;
                                            background:#A5B4FC;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            cursor:pointer;
                                        "><x class="tagify__tag__removeBtn">×</x></span>

                                </div>
                            </tag>
                        `;
                    }
                },

                transformTag: function(tagData) {

                    tagData.checked = tagData.checked || false;

                    return tagData;
                }
            });

            // =========================
            // TAGIFY 2
            // =========================

            const tagify2 = new Tagify(inputJobSearch, {

                maxTags: 5,

                templates: {

                    tag: function(tagData) {

                        tagData = tagData || {};

                        const value = tagData.value || '';

                        return `
                            <tag title="${value}"
                                contenteditable='false'
                                spellcheck="false"
                                class="tagify__tag ${tagData.class ? tagData.class : ""}"
                                ${this.getAttributes(tagData)}
                                >

                                <div style="padding: 2px 6px; display:flex; align-items:center; gap:5px;">

                                    <span class="tagify__tag-text">${value}</span>

                                    <span style="
                                            width:16px;
                                            height:16px;
                                            border-radius:50%;
                                            background:#A5B4FC;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            cursor:pointer;
                                        "><x class="tagify__tag__removeBtn">×</x></span>

                                </div>
                            </tag>
                        `;
                    }
                },

                transformTag: function(tagData) {

                    tagData.checked = tagData.checked || false;

                    return tagData;
                }
            });

            // =========================
            // PREVENT LOOP
            // =========================

            let isSyncing = false;

            // =========================
            // SYNC TO TAGIFY2
            // =========================

            function syncToTagify2() {

                if (isSyncing) return;

                isSyncing = true;

                const tags = tagify.value.map(tag => ({
                    value: tag.value
                }));

                tagify2.removeAllTags();
                tagify2.addTags(tags);

                isSyncing = false;
            }

            // =========================
            // SYNC TO TAGIFY1
            // =========================

            function syncToTagify1() {

                if (isSyncing) return;

                isSyncing = true;

                const tags = tagify2.value.map(tag => ({
                    value: tag.value
                }));

                tagify.removeAllTags();
                tagify.addTags(tags);

                isSyncing = false;
            }

            // =========================
            // UPDATE INPUT
            // =========================

            function updateInputJobSearch() {

                const tags = tagify.value.map(tag => tag.value).join(',');

                inputJobSearch.value = tags;

                syncToTagify2();
            }

            // =========================
            // UPDATE TAGIFY USERS
            // =========================

            function updateTagifyUsers() {

                syncToTagify1();
            }

            // =========================
            // EVENTS
            // =========================

            tagify.on('add', updateInputJobSearch);

            tagify.on('remove', updateInputJobSearch);

            tagify2.on('add', syncToTagify1);

            tagify2.on('remove', syncToTagify1);

            // =========================
            // AJAX SAFE CLICK EVENT
            // =========================

            $(document)
                .off('click.tagifyStar')
                .on('click.tagifyStar', '.star', function () {

                    const tagElm = $(this).closest('.tagify__tag');

                    $(this).toggleClass('checked');

                });

            // =========================
            // DEBOUNCE
            // =========================

            function debounceskill(func, delay) {

                let timeoutskill;

                return function (...args) {

                    clearTimeout(timeoutskill);

                    timeoutskill = setTimeout(() => {
                        func.apply(this, args);
                    }, delay);
                };
            }

            // =========================
            // FETCH SKILLS
            // =========================

            const fetchSuggestionsSkills = debounceskill(function(query) {

                $.ajax({

                    url: "{{ url('/') }}/skill-suggestions?q=" + query,

                    method: 'GET',

                    success: function(data) {

                        if (Array.isArray(data)) {

                            tagify.settings.whitelist = data;

                            tagify.dropdown.show.call(tagify);

                        } else {

                            console.error("Invalid API response");

                        }
                    },

                    error: function(err) {

                        console.error("Skill fetch error:", err);
                    }

                });

            }, 200);

            // =========================
            // INPUT EVENT
            // =========================

            tagify.on('input', function(e) {

                const query = e.detail.value.trim();

                if (query.length < 1) return;

                fetchSuggestionsSkills(query);
            });

            // =========================
            // INITIAL SYNC
            // =========================

            updateInputJobSearch();

            document.addEventListener('click', function (e) {

                if (e.target.classList.contains('tagify__tag__removeBtn')) {

                    // tag element
                    const tagElm = e.target.closest('.tagify__tag');

                    // remove from tagify 1
                    if (tagify.DOM.scope.contains(tagElm)) {

                        tagify.removeTags(tagElm);

                    }

                    // remove from tagify 2
                    if (tagify2.DOM.scope.contains(tagElm)) {

                        tagify2.removeTags(tagElm);

                    }

                }

            });


        }


        // ===================================
        // INITIAL LOAD
        // ===================================

        initTagifySection();


        // ===================================
        // AFTER AJAX LOAD
        // ===================================

        // Example

        /*
        $.ajax({

            success: function(response) {

                $('#yourDiv').html(response);

                setTimeout(() => {

                    initTagifySection();

                }, 100);
            }
        });
        */

</script>

<script>
   
   
    

     /* ── Hiring type toggle ── */
    function selectHiringType(type) {
        var cardFull     = document.getElementById('card-full');
        var cardContract = document.getElementById('card-contract');
        var radioFull    = document.getElementById('radio-full');
        var radioContract = document.getElementById('radio-contract');

        if (type === 'full') {
        document.getElementById('hiring_type').value = 2;
        cardFull.className     = 'hiring-card active-blue';
        cardFull.setAttribute('aria-pressed', 'true');
        cardContract.className = 'hiring-card';
        cardContract.setAttribute('aria-pressed', 'false');
        radioFull.className    = 'radio-dot checked-blue';
        radioFull.innerHTML    = '<div class="dot-inner"></div>';
        radioContract.className = 'radio-dot';
        radioContract.innerHTML = '';
        } else {
        document.getElementById('hiring_type').value = 1;
        cardContract.className  = 'hiring-card active-teal';
        cardContract.setAttribute('aria-pressed', 'true');
        cardFull.className      = 'hiring-card';
        cardFull.setAttribute('aria-pressed', 'false');
        radioContract.className = 'radio-dot checked-teal';
        radioContract.innerHTML = '<div class="dot-inner"></div>';
        radioFull.className     = 'radio-dot';
        radioFull.innerHTML     = '';
        }
    }
    




    function boostlisting(type) {
        if ($('#boostlisting').length === 0) return;
        var jobid = $('#current_job_id').val();
        var boostTable = $('#boostlisting').dataTable({
            pageLength: 3,
            iDisplayLength: 3,
            responsive: true,
            processing: true,
            serverSide: false,
            ordering: false,
            destroy: true,
            stateSave: true,
            "bLengthChange": false,
            sDom: 'lrtip',
            "bInfo": false,
            bPaginate: false,

            ajax: '{!! route('service_company.jobs.boostkeywords') !!}?jobid=' + jobid,
            "fnDrawCallback": function(oSettings) {},
            "initComplete": function(settings, json) {},
            columns: [{
                    data: 'chkbox',
                    name: 'chkbox',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'Name',
                    name: 'Name',
                    orderable: false,
                    searchable: false,
                    width: '15%'
                },
                // {data: 'Match', name: 'Match', searchable : false, orderable : false, width: '15%'},
                {
                    data: 'SimilarReq',
                    name: 'SimilarReq',
                    searchable: false,
                    orderable: false,
                    width: '5%'
                },
                {
                    data: 'Profiles',
                    name: 'Profiles',
                    searchable: false,
                    orderable: false,
                    width: '10%'
                },
                {
                    data: 'HiringType',
                    name: 'HiringType',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'Location',
                    name: 'Location',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'Status',
                    name: 'Status',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'Action',
                    name: 'Action',
                    orderable: false
                }
            ]

        });
        if ($.fn.dataTable && $.fn.dataTable.FixedHeader) {
            new $.fn.dataTable.FixedHeader(boostTable);
        }

        var tbodyrecommendedlist = document.getElementById("recommendedlist");
        var tbodyboostlisting = document.getElementById("boostlisting");
        if (tbodyrecommendedlist && tbodyboostlisting) {
            if (tbodyrecommendedlist.innerHTML.trim() !== "" || tbodyboostlisting.innerHTML.trim() !== "") {
                setTimeout(() => {
                    $(".dataTables_empty").attr("style", "display: none !important;");
                    $(".dataTables_empty").hide();
                }, 1500);
            }
        }
    }
    if ($('#boostlisting').length > 0) {
        boostlisting('load');
    }
</script>
<script>
    function filtersdata() {
        tableLoad('load');
    }
    function loadAllDrawerLists() {
        tableLoad('load');
        staffingPartnersRegister('load');
        recommendedAssignList(1, '');
    }
    loadAllDrawerLists();
    setTimeout(loadAllDrawerLists, 200);

    function tableLoad(type) {
       var page = 1;
       $('.page-loader').css('display', 'flex');
        return $.ajax({
                url: `{!! route('service_company.jobs.recommendedData') !!}?page=${page}`,
                type: 'GET',
                data: {
                    jobid: $('#current_job_id').val(),
                    hiringtype : $('#workhiringtype').val(),
                    country : $('#country').val(),
                    cus_search : $('#kt_tagify_users').val(),
                    min_exp : $('#min_exp').val(),
                    max_exp : $('#max_exp').val(),
                    kt_tagify_location : $('#country').val(),
                    app_int_outsource : $('#hiring_type').val(),
                    experience : $('#experience').val(),
                    industry : $('#industry').val(),
                    min_sal : $('#min_sal').val(),
                    max_sal : $('#max_sal').val(),
                    min_salary_currency : $('#min_salary_currency').val(),
                    sal_mode : $('#sal_mode').val()
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#recommendedlistBody').html(response.html);
                    $('#recommendedTotal').html('('+response.total+')');
                }
            });
    }


    function newUpdateJobstatus() {
        $('.changetextupdate').text('Searching....!')
        tableLoad('load');
        var $this = $('#apptrackerfilter');
        // $this.fadeOut(2000, function() {
        $this.modal('hide');
        // });
        setTimeout(() => {
            $('#apptrackerfilter_close').click();
            $('.changetextupdate').text('Submit')
        }, 1500);
    }
    $(document).on("keyup","#kt_tagify_4",function(){
        staffingPartnersRegister('load');
    });
    $(document).on("keyup","#kt_tagify_5",function(){
        recommendedAssignList(1,'');
    });
    function filtersdatathree() {
        recommendedAssignList(1,'');
    }
    $(document).off('click', '.assigned_tab').on('click', '.assigned_tab', function() {
        recommendedAssignList(1,'');
    });

     $(document).on('click', '#paginationAssignListagency .page-link', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;

            // Disable the clicked pagination link
            $(this).addClass('disabled');

            // Fetch data
            recommendedAssignList(page, $('#search').val()).then(() => {
                // Re-enable the links after successful data fetch
                $('#paginationAssignListagency .page-link').removeClass('disabled');
            }).catch(() => {
                // Re-enable the links even if there's an error
                $('#paginationAssignListagency .page-link').removeClass('disabled');
            });
    });


    function recommendedAssignList(page = 1, search = '') {
        if (page === 'load' || !page) { page = 1; }

        var jobid = $('#current_job_id').val();
        var hiringtype = '';//$('#hiringtype5').val();
        var country = '';//$('#country5').val();
        var cus_search = $('#kt_tagify_5').val();

            return $.ajax({
                url: `{!! route('service_company.jobs.recommendedAssignList') !!}?page=${page}`,
                type: 'GET',
                data: {
                    jobid : jobid,
                    hiringtype : hiringtype,
                    country : country,
                    cus_search : cus_search
                },
                success: function(response) {
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#allAssignListagency').html(response.html);
                    $('#assignedTotal').html('('+response.total+')');
                    $('#paginationAssignListagency').html(response.pagination);
                }
            });
    }

    function filtersdatatwo() {
        staffingPartnersRegister('load');
    }
    $(document).off('click', '.register_tab').on('click', '.register_tab', function() {
        staffingPartnersRegister('load');
    });
    
    $(document).on('click', '#paginationregisteragency .page-link', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;

            // Disable the clicked pagination link
            $(this).addClass('disabled');

            // Fetch data
            staffingPartnersRegister(page, $('#search').val()).then(() => {
                // Re-enable the links after successful data fetch
                $('#paginationregisteragency .page-link').removeClass('disabled');
            }).catch(() => {
                // Re-enable the links even if there's an error
                $('#paginationregisteragency .page-link').removeClass('disabled');
            });
    });

    function staffingPartnersRegister(page = 1, search = '') {
        if (page === 'load' || !page) { page = 1; }

        if($('#kt_tagify_4').val()!=''){
                $('.loader_search').css({ display: 'block' });
        }else{
                $('.page-loader').css('display', 'flex');
        }
        var jobid = $('#current_job_id').val();
        var hiringtype = $('#hiringtype4').val();
        var country = $('#country4').val();
        var cus_search = $('#kt_tagify_4').val();
            return $.ajax({
                url: `{!! route('service_company.jobs.staffingPartnersRegister') !!}?page=${page}`,
                type: 'GET',
                data: {
                    jobid : jobid,
                    hiringtype : hiringtype,
                    country : country,
                    cus_search : cus_search
                },
                success: function(response) {
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#allregisteragency').html(response.html);
                    $('#registeredTotal').html('('+response.total+')');
                    $('#paginationregisteragency').html(response.pagination);
                }
            });
    }
           

    $('body').off('click', '.send_only_job_invitation').on('click', '.send_only_job_invitation', function(event) {
    // $('body').on('click', '.send_only_job_invitation', function(event) {

        event.preventDefault();
        // alert("fhf");
         var ele = $(this);
        var id = $(this).data('row-jobid');
        var fromcompany = $(this).data('row-fromcompany');
        var tocompany = $(this).data('row-tocompany');
        var kt_tagify_location = $('#country4').val();

        var boostercom = $(this).data('boostercom');
        var boostids = '';
        if(boostercom==1){
            var boostids =  $(this).data('boost-ids');
        }
       
        var url = "{{ route('service_company.jobs.send_only_job_invitation', ':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                'jobid': id,
                'fromcompany': fromcompany,
                'tocompany': tocompany,
                'boostercom': boostercom,
                'boostids': boostids,
                'kt_tagify_location': kt_tagify_location
            },
            success: function(response) {
                // alert("dl");
                if (response.status == "success") {
                    ele.removeClass('btn-light-primary');
                    ele.removeClass('send_job_recommanded_request');
                    ele.removeClass('send_only_job_invitation');
                    ele.addClass('btn-light-success');
                    ele.text('Request Sent');
                    // ele.text('Request Sent');
                    Swal.fire("Success", response.message, "success");
                }else if (response.status == "warning") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "info") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "closeJob") {
                    Swal.fire("Warning", response.message, "warning");
                }


            }
        });


    });
    $('body').on('click', '.send_request_from_rsp', function() {

        var id = $(this).data('row-id');
        var jobid = $(this).data('row-jobid');

        var url = "{{ route('service_company.jobs.rsp_job_request_send', ':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

        $.ajax({
            type: 'GET',
            url: url,
            data: {
                'jobid': jobid
            },
            success: function(response) {

                if (response.status == "success") {
                    tableLoad('load');
                    Swal.fire("Success", response.message, "success");
                }
                if (response.status == "warning") {
                    tableLoad('load');
                    Swal.fire("Warning", response.message, "warning");
                }

            }
        });
    });
	
    
	$('body').off('click', '.send_job_recommanded_request').on('click', '.send_job_recommanded_request', function(event) {
        event.preventDefault();

        var $this = $(this);
        // Disable the button to prevent multiple clicks   
        $this.prop('disabled', true);

        var from_company_id = $this.data('row-id');
        var current_jobid = $this.data('row-jobid');
        var matchcount = $this.data('matchcount');
        var id = from_company_id;
        var kt_tagify_location = $('#kt_tagify_location').val();

        var boostercom = $(this).data('boostercom');
        var boostids = '';
        if(boostercom==1){
            var boostids =  $(this).data('boost-ids');
        }


        var url = "{{ route('service_company.jobs.send_job_recommanded_request', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            type: 'GET',
            url: url,
            data: {
                'from_company_id': from_company_id,
                'current_jobid': current_jobid,
                'boostercom': boostercom,
                'boostids': boostids,
                'matchcount': matchcount,
                'kt_tagify_location': kt_tagify_location
            },
            success: function(response) {
                if (response.status === "success") {
                    $('#sp_list_' + id).removeClass('btn-light-primary send_job_recommanded_request send_only_job_invitation')
                                      .addClass('btn-light-success')
                                      .text('Request Sent');
                    Swal.fire("Success", response.message, "success");
                }else if (response.status === "warning") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "info") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "closeJob") {
                    Swal.fire("Warning", response.message, "warning");
                }
                // Re-enable the button after processing
                $this.prop('disabled', false);
            },
            error: function() {
                // Re-enable the button if there's an error
                $this.prop('disabled', false);
            }
        });
    });

   $('body').off('click','.post_to_public').on('click','.post_to_public', function(e){
        e.preventDefault();
        var $this = $(this);
        // Disable the button to prevent multiple clicks
        
        var current_jobid = $this.data('row-jobid');
        var job_cancel = $this.data('c');
        var id = 0;
        var url = "{{ route('service_company.jobs.auction_send_job_request', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: job_cancel == 1 
                ? 'Unpublish this job?.'
                : 'Publish job to agencies?',
            text: job_cancel == 1 
                ? 'This job will no longer be visible to agencies.'
                : 'This job will be visible to verified agencies, and they can start submitting candidates immediately.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'No, cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {

        if (!result.isConfirmed) return;

        // Disable button AFTER confirmation
        $this.prop('disabled', true);

        $.ajax({
            type: 'GET',
            url: url,
            data: {
                'current_jobid': current_jobid,
                'job_cancel':job_cancel
            },
            success: function(response) {
                
                    if(job_cancel==1){
                        
                          $('.text_change_text').removeClass('text-success btn-light-success');
                          $('.text_change_text').addClass('text-black btn-light-black');
                          $('.text_change_text').text('Unpublish Job');
                         
                          $('#sp_list_one').text(response.text_change);
                          $('#sp_list_two').addClass('d-none');
                          $('#sp_list_one').removeClass('d-none');
                         
                    }else{
                        $('#sp_list_two').text(response.text_change);

                        if(response.text_change=='Approval Pending'){
                           $('#sp_list_two').removeClass('d-none');
                           $('#sp_list_one').data('c', 2).attr('data-c', 2);
                           $('#sp_list_one').addClass('d-none'); 
                           $('.text_change_text').text(response.text_change);
                        }else{
                          $('.text_change_text').removeClass('text-black btn-light-black');
                          $('.text_change_text').addClass('text-success btn-light-success');
                          $('.text_change_text').text('Published');
                          $('#sp_list_two').removeClass('d-none');
                          $('#sp_list_one').addClass('d-none');  
                        }
                    }

                if (response.status === "success") {

                    // $('#sp_list_two').removeClass('btn-light-primary post_to_public')
                    //                   .addClass(response.color_change)
                    //                   .text(response.text_change);

                    Swal.fire("Success", response.message, "success");
                    
                }else if (response.status === "warning") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "closeJob") {
                    Swal.fire("Warning", response.message, "warning");
                    // $('#kt_activitiesStaffing_close').click();
                }
                // Re-enable the button after processing
                $this.prop('disabled', false);
            },
            error: function() {
                // Re-enable the button if there's an error
                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                $this.prop('disabled', false);
            },
            complete: function () {
                // Always re-enable button
                $this.prop('disabled', false);
            }
            });
        });

   });

    $('body').on('click', '.remove_assign_job', function() {

        var id = $(this).data('row-id');
        var jobid = $(this).data('row-jobid');

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Ignore it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {

            if (result.isConfirmed) {



                var url = "{{ route('service_company.jobs.assignRejected') }}";
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'id': id,
                        'jobid': jobid
                    },
                    success: function(response) {

                        if (response.status == 'success') {

                            recommendedAssignList('load');
                            swalWithBootstrapButtons.fire(
                                'Ignore!',
                                'Selected request has been removed successfully.',
                                'success'
                            )
                        }
                    }
                });


            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Selected request is safe :)',
                    'error'
                )
            }

        })
    });
    $('body').on('click', '.remove_job', function() {


        var jobid = $(this).data('row-jobid');
        var fromcompany = $(this).data('row-fromcompany');
        var tocompany = $(this).data('row-tocompany');
        var tabname = $(this).data('row-tabname');

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You won't be able to remove this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Ignore it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {

            if (result.isConfirmed) {

                var url = "{{ route('service_company.jobs.removeJob') }}";
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'jobid': jobid,
                        'fromcompany': fromcompany,
                        'tocompany': tocompany
                    },
                    success: function(response) {

                        if (response.status == 'success') {

                            tableLoad('load');
                            recommendedAssignList('load');
                            staffingPartnersRegister('load');

                            swalWithBootstrapButtons.fire(
                                'Removed!',
                                'Selected request has been removed successfully.',
                                'success'
                            );
                        } else {

                            swalWithBootstrapButtons.fire(
                                'Cancelled',
                                'Selected request is safe :)',
                                'error'
                            );


                        }
                    }
                });


            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Selected request is safe :)',
                    'error'
                )
            }
        })
    });
    $('body').off('click', '.resend_job_recommanded_request').on('click', '.resend_job_recommanded_request', function(event) {
        event.preventDefault();
        // $('body').on('click', '.resend_job_recommanded_request', function() {

        var ename = $(this).data('row-ename');
        var to_company_id = $(this).data('row-id');
        var current_jobid = $(this).data('row-jobid');
        var matchcount = $(this).data('matchcount');
        var kt_tagify_location = $('#kt_tagify_location').val();
        var id = to_company_id;

        var url = "{{ route('service_company.jobs.resend_job_recommanded_request', ':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                'to_company_id': to_company_id,
                'current_jobid': current_jobid, 
                'matchcount': matchcount,
                'kt_tagify_location': kt_tagify_location,
                'ename': ename
            },
            success: function(response) {
                tableLoad('load');
                recommendedAssignList('load');
                staffingPartnersRegister('load');
                if (response.status == "success") {
                    Swal.fire("Success", response.message, "success");
                }else if (response.status == "warning") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "closeJob") {
                    Swal.fire("Warning", response.message, "warning");
                }
            }
        });
    });
</script>

<script>
    // The DOM elements you wish to replace with Tagify
    var inputloc = document.querySelector("#kt_tagify_location");
    if (inputloc) {
        var cities = @json(@$cities);
        // Initialize Tagify script on the above inputs
        var tagifylocation = new Tagify(inputloc, {
            whitelist: cities,
            maxTags: 1,
            dropdown: {
                maxItems: 200, // <- mixumum allowed rendered suggestions
                classname: "", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0, // <- show suggestions on focus
                closeOnSelect: true // <- do not hide the suggestions dropdown once an item has been selected
            }
        });
        // Prevent creating tags on blur or enter key
        tagifylocation.on('input', function(e) {
            tagifylocation.dropdown.show(); // show the dropdown immediately
        });
        // Create tags only on select
        tagifylocation.on('dropdown:select', function(e) {
            tagifylocation.addTags([e.detail.data.value]);
        });
        tagifylocation.on('add', function(e) {
            // Remove the tag if it wasn't selected from the dropdown
            if (!cities.includes(e.detail.data.value)) {
                setTimeout(() => {
                    tagifylocation.removeTag(e.detail.data.value);
                }, 500);
            }
        });
    }

    /* ── Assigned Agency tab: three-dot dropdown ─────────────────── */
    $(document).on('click', '.aa-dots-btn', function(e) {
        e.stopPropagation();
        var $menu = $(this).next('.aa-ddmenu');
        var wasOpen = $menu.hasClass('open');
        // Close all open menus first
        $('.aa-ddmenu.open').removeClass('open');
        if (!wasOpen) { $menu.addClass('open'); }
    });
    // Close dropdown on outside click
    $(document).on('click', function() { $('.aa-ddmenu.open').removeClass('open'); });

</script>
