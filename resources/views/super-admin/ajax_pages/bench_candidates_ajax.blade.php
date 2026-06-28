                        @php
                        use App\Helper\GeneralMethod;
                        use Illuminate\Support\Facades\DB;
                        use Carbon\Carbon;
                        $sr = 1;
                        $salaryMode = GeneralMethod::salaryMode();
                        $currencySymbole = GeneralMethod::currencySymbole();
                    @endphp
                    @if ($data)
                        @foreach ($data as $rowArr)
                         @php
                            $val_tem = (object) $rowArr;
                            @endphp
                            <tr >
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" data-jobid="{{ @$val_tem->id }}" value="{{ @$val_tem->id }}">
                                      </div>
                                </td>
                                <td class="bench_show_application_detail" data-id="{{ @$val_tem->id }}">
                                        @php                                  
                                        $bgTextDecore = $strVal = '';
                                        $str = trim(@$val_tem->full_name);
                                        $str = $strVal = strtolower($str[0]);
                                        $str = ord($str);
                                        if ($str >= 97 && $str <= 101) {
                                            $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                                        } else if ($str >= 102 && $str <= 106) {
                                            $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                                        } else if ($str >= 107 && $str <= 111) {
                                            $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                                        } else if ($str >= 112 && $str <= 116) {
                                            $bgTextDecore = 'fs-3 bg-light-success text-success';
                                        } else if ($str >= 117 && $str <= 121) {
                                            $bgTextDecore = 'fs-3 bg-dark text-secondary';
                                        } else {
                                            $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                                        }
                                        $profile_sidebar_css = '';
                                        @endphp

                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                            <a href="javascript:void(0);"   data-val_tem-id="{{ @$val_tem->id }}" class="{{ $profile_sidebar_css }}">
                                                <div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
                                            </a>
                                        </div>                  
                                        <div class="d-flex flex-column">
                                        <a href="javascript:void(0);"  data-val_tem-id="{{ @$val_tem->id }}"  class="text-dark fw-bolder text-hover-primary fs-6 {{ $profile_sidebar_css }} " data-toggle="tooltip" title="{{ ucwords(@$val_tem->full_name) }}">{{ ucwords(substr(@$val_tem->full_name, 0, 10)) }}</a>
                                        <a href="javascript:void(0);"  data-val_tem-id="{{ @$val_tem->id }}"  class="text-muted fw-bold d-block fs-7 text-hover-primary {{ $profile_sidebar_css }} " data-toggle="tooltip" title="{{ ucwords(@$val_tem->current_designation) }}">{{ ucwords(substr(@$val_tem->current_designation, 0, 10)) }}</a>
                                        </div>


                                </td>

                                <td>
                                   <div class="d-flex flex-column">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ @$val_tem->total_exp }}+</a>
                                        <span class="text-muted fw-bold d-block fs-7">{{ @$val_tem->rel_exp }}+</span>
                                      </div>
                                </td>
                                <td>
                                    @php
                                    $pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode = "";
                                    $pay_rate_currency = $currencySymbole[@$val_tem->pay_rate_currency];
                                    $bill_rate_currency = $currencySymbole[@$val_tem->bill_rate_currency];
                                    $pay_rate_mode = $salaryMode[@$val_tem->pay_rate_mode];
                                    $bill_rate_mode = $salaryMode[@$val_tem->bill_rate_mode];
                                    $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($val_tem->pay_rate_amount);
                                    $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($val_tem->bill_rate_amount);
                                    @endphp
                                    <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{!! $bill_rate_currency !!} {{ @$bill_rate_amount }} / {{  $bill_rate_mode }}</a>
                                        <span class="text-muted fw-bold d-block fs-7">{!! @$pay_rate_currency !!} {{ @$pay_rate_amount }} / {{  $pay_rate_mode }}</span>
                                    </div>
                                </td>
                                <td>
                                   <div class="d-flex flex-column">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{@$val_tem->qualification_name }}">{{ @$val_tem->qualification_name}}</a>
                                        <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="{{$val_tem->skills }}"> {{ substr($val_tem->skills, 0, 12) }}</span> 
                                      </div>
                                </td>
                                <td>
                                    @php
                                    $npArr = array('Notice period', 'Available from', 'Immediate');
                                    $npDate = $val_tem->np_id;
                                    if ($val_tem->serving_notice_period_mode == 0){
                                        $npDate = ($val_tem->np_id==1) ? $val_tem->np_id.' Day' : $val_tem->np_id.' Days';
                                    }elseif($val_tem->serving_notice_period_mode == 1) {
                                        $originalDate = $val_tem->np_id;
                                        $npDate = date("d-M-y", strtotime($originalDate));
                                    }else{
                                        $npDate='';
                                    }
                                    @endphp
                                    <div class="d-flex flex-column">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{$npArr[$val_tem->serving_notice_period_mode] }}</a> 
                                        <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="{{$val_tem->np_id }}">{{ $npDate }}</span>
                                        </div>

                                </td>
                                <td>
                                    @php
                                    $country = $state = $row_state_name = $row_country_name = $city = $row_city_name = '';
                                    if (empty($val_tem->country)) {
                                        $row_country_name = '----';
                                    } else {
                                        if (!empty(@$val_tem->country)) {
                                            if (is_numeric(@$val_tem->country)) {
                                                $country_name_Arr = GeneralMethod::getCountryName(@$val_tem->country);
                                                foreach ($country_name_Arr as $cval) {
                                                    $row_country_name = ucwords($cval['name']);
                                                }
                                            }
                                        }
                                    }
                    
                                    if (empty($val_tem->state)) {
                                        $row_state_name = '----';
                                    } else {
                                        if (@$val_tem->country == 101) {
                                            $row_state = DB::table('state_list')->select('*')->where('id', @$val_tem->state)->first();
                                            $row_state_name = @$row_state->state;
                    
                                        } else {
                                            $state = @$val_tem->state;
                                            if (is_numeric(@$val_tem->country) && is_numeric($state)) {
                                                if ($state > 0) {
                                                    $state_name_Arr = GeneralMethod::getStateName(@$val_tem->country, $state);
                                                    foreach ($state_name_Arr as $sval) {
                                                        $row_state_name = ucwords($sval['name']);
                                                    }
                                                }
                                            }
                                        }
                                    }
                    
                                    if (@$val_tem->country == 101) {
                                        // dd(@$row->city_id);
                                        $row_city = DB::table('city_list')->select('*')->where('city_id', @$val_tem->city_id)->first();
                                        $row_city_name = @$row_city->city;
                                    } else {
                                        $row_city_name = @$val_tem->city;
                                    }
                                    @endphp
                    
                                   <div class="d-flex flex-column">
                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="{{ ucwords($row_city_name) }}">{{ ucwords(substr($row_city_name, 0, 10)) }}</a>
                                            <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ ucwords($row_state_name) }} | {{ strtoupper($row_country_name) }}">{{ ucfirst(substr($row_state_name, 0, 5)) }} | {{ strtoupper(substr($row_country_name, 0, 2)) }}</span>
                                        </div>
                                </td>
                                <td>
                                    @php
                                    $subS = '';
                                    if($val_tem->master_status_name=='Bench'){
                                        $subS = "Open For Work";
                                        if($val_tem->superadminstatus==0){
                                            $subS = "Pending";
                                        }elseif($val_tem->superadminstatus==1){
                                            $subS = "Approved";
                                        }elseif($val_tem->superadminstatus==2){
                                            $subS = "Rejected";
                                        }
                                        
                                    }
                                    @endphp
                                    <div class="d-flex flex-column">
                                        <a href="javascript:void(0);" data-row-id="{{ $val_tem->id}}" class="fw-bold fs-6  stage_sub_stage {{ $val_tem->css_style}}" data-toggle="modal" data-target="#stage_sub_stage" >
                                            {{ $val_tem->master_status_name}}</a>
                                        <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="10 Days to Expire">{{ $subS}}</span>
                                        </div>
                                </td>
                                <td>
                                    @php
                                    $created_at = '';
                                    $created_at = $newDate = date("d M , Y", strtotime($val_tem->created_at))
                                    @endphp
                                   <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ @$val_tem->company_name}}</a>
                                    <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="10 Days to Expire">{{ $created_at }}</span>
                                </div>
                                </td>
                               
                                
                            </tr>
                            @php
                                $sr++;
                            @endphp
                        @endforeach
                    @endif