@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
       @php
          $row = (object) $rowArr;
          
        @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $bgTextDecore = $strVal = $image = '';
                    $str = trim(@$row->company_name);
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

                    $client_type_css = 'show-detail';

                    $departmentArr = '------';
                    // if ($row->industries >= 0 && $row->industries !='') {
                    //     $departmentArr = $department[($row->industries) ? @$row->industries : 1];
                    // }

                    $logo_path = '';
                    if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                        $logo_path = @$row->logo;

                        $image =
                            '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 " style="float:left" >
							<img src="' .str_replace('index.php', '', @$logo_path) .'" height="100px" width="100px" data-row-id="' . $row->id .'" class="' . $client_type_css .' border-radius-5   tttt"  />
							</div>';
                    } else {
                        $image =
                            '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
								<a href="javascript:void(0);" data-row-id="' .$row->id .'" class="' .$client_type_css .' border-radius-5  ssss" id="kt_activities3_toggle" ><div class="symbol-label fs-3 ' .@$bgTextDecore .'  border-radius-5">' . ucwords(@$strVal) .
                            '</div>
                            </a>
                        </div>';
                    }
                        $accountVerify = '';
                        if(@$row->accountVerify){
                          $accountVerify = '<i class="bi bi-patch-check-fill text-success fs-5" title="Verified"></i>'; 
                        }else{
                          $accountVerify = '<i class="bi bi-hourglass-split text-warning fs-5" title="Pending Verification"></i>';
                        }

                    echo $image .
                        '		              
                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 ' .
                        $client_type_css .
                        '"  data-toggle="tooltip1" data-html="true" title="' .
                        ucwords(@$row->company_name) . 
                        '">' .
                        ucwords(mb_substr(@$row->company_name, 0, 20)) . $accountVerify.
                        '
                        
                        </a>
                        <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary ' .
                        $client_type_css .
                        '">' .
                        @$row->ind_name .
                        '</a>
                    </div>';
                @endphp
            </td>
            <td>
                @php
                

                   $firstLocation = isset($row->locations_address[0]) && is_array($row->locations_address[0]) ? $row->locations_address[0] : [];
                    
                    $city = $firstLocation['locations_address'][0]['city_name'] ?? null;
                    $state_name = $firstLocation['locations_address'][0]['state_name'] ?? null;
                    $locationHtml = $firstLocation['locations_address'][0]['country_name'] ?? null;

                    echo '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        $city .
                        '</a>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold d-block fs-7">' .
                        $locationHtml .
                        '</span>
                        <div>';
                @endphp
            </td>
            <td>
                @php
                    $daysAgo = '';

                    $created_at = $row->created_at;
                    $newDate = date('d M, Y', strtotime($created_at));
                    $daysAgo = GeneralMethod::timeAgo($row->created_at);
                    $lastActive = '';
                    if(!empty($row->last_login)){
                        $lastActive = date('d M, Y', strtotime(@$row->last_login));
                    }

                    echo '<div class="d-flex flex-column">
                                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        @$newDate .
                        '</a>
                                <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        @$daysAgo .
                        '</a>
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Last Active</a>
                        <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        $lastActive.
                        '</a>
                            </div>';
                @endphp
            </td>
            <td>
                @php

                    $client_id = $clientCompanyId = '';
                    $active_counter = $inactive_counter = $hold_counter = 0;
                    $currentCompanyId = $row->id;
                    $clientCompanyId = $row->id;

                     $active_counter = GeneralMethod::jobStatusCountElastic($row->id,"active");
                     $hold_counter = GeneralMethod::jobStatusCountElastic($row->id,"hold");
                     $inactive_counter = GeneralMethod::jobStatusCountElastic($row->id,"inactive");
                    

                    // $jobs = collect($row->jobs ?? []); // fallback to empty array if not set
                    // $active_counter   = $jobs->where('status', 'active')->count();
                    // $hold_counter     = $jobs->where('status', 'hold')->count();
                    // $inactive_counter = $jobs->where('status', 'inactive')->count();

                    // $jobCounter = GeneralMethod::dataCnt($clientCompanyId);

                    // $active_counter = $jobCounter['jobsActive'];
                    // $inactive_counter = $jobCounter['jobsClose'];
                    // $hold_counter = $jobCounter['jobsHold'];

                    echo '<div class="d-flex flex-column">
                      <span class="fw-bolder text-success fs-6">' .
                        $active_counter .
                        ' Active</span>
                      <div class="d-flex align-items-center">
                        <span class="fw-bold d-block fs-7 text-warning">' .
                        $hold_counter .
                        ' Hold</span>
                          &nbsp;|&nbsp;
                        <span class="fw-bold d-block fs-7 text-danger">' .
                        $inactive_counter .
                        ' Closed</span>
                      <div>
                    </div>
                </div></div>';
                @endphp
            </td>
            <td>
                @php
                    $company_id = $row->id;

                     $candidates = collect($row->candidates ?? []); // fallback to empty array if not set
                     $all_counter   = $candidates->count();
                     $join_counter     = $candidates->where('master_status_id', 6)->count();
                    echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-7">' .$all_counter .'</a>                    
                            <span class="fw-bold d-block fs-7">' .$join_counter .'</span>
                        </div>';
                @endphp
            </td>
            <td>
                @php

                    $bench_candidates = collect($row->bench_candidates ?? []); // fallback to empty array if not set
                    $bench_counter   = $bench_candidates->where('status', 2)->count();
                    $pool_counter     = $bench_candidates->where('status', 1)->count();
                    $place_counter = $bench_candidates->where('status', 3)->count();



                    // $candidateCounter = GeneralMethod::benchCandidateCnt($row->id);

                    // $bench_counter = $candidateCounter['bench_counter'];
                    // $pool_counter = $candidateCounter['pool_counter'];
                    // $place_counter = $candidateCounter['place_counter'];

                    echo '<div class="d-flex flex-column">
                            <span class="fw-bolder text-success fs-6">' .
                        $bench_counter .
                        ' Bench</span>
                            <div class="d-flex align-items-center">
                            <span class="fw-bold d-block fs-7 text-warning">' .
                        $pool_counter .
                        ' Pool</span>
                                &nbsp;|&nbsp;
                            <span class="fw-bold d-block fs-7 text-danger">' .
                        $place_counter .
                        ' Place</span>
                            <div>
                            </div>
                            </div></div>';
                @endphp
            </td>
            <td>
                @php
                    $company_coins = collect($row->company_coins ?? []);
                    $coins = $company_coins[0]['coins'] ?? null;
                    $coin_order_id = $company_coins[0]['coin_order_id'] ?? 0;
                    $company_coins = DB::table('coin_orders')
                        ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_orders.coin_package_id')
                        ->where('coin_orders.id', $coin_order_id)
                        ->where('coin_orders.status', 1)
                        ->select(
                            'coin_package_types.title'
                        )
                        ->first();
                        $title_pack = '';
                        if($company_coins){
                            $title_pack = @$company_coins->title;
                        }


                    echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                                    @ $title_pack .
                                    '</a>
                            <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                                    @$coins.
                                    ' Remaining Coins</a>
                        </div>';
                @endphp
            </td>
            <td>
                @php

                    $company_coins = collect($row->company_coins ?? []);
                    $coins = $company_coins[1]['coins'] ?? null;
                    $coin_order_id = $company_coins[1]['coin_order_id'] ?? 0;
                    $company_coins_sales = DB::table('coin_orders')
                        ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_orders.coin_package_id')
                        ->where('coin_orders.id', $coin_order_id)
                        ->where('coin_orders.status', 1)
                        ->select(
                            'coin_package_types.title'
                        )
                        ->first();
                        $title_pack = '';
                        if($company_coins){
                            $title_pack = @$company_coins_sales->title;
                        }
                    

                    echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                                    @$title_pack .
                                    '</a>
                            <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                                    @$coins.
                                    ' Remaining Coins</a>
                        </div>';

                @endphp
            </td>
            <td>
                @php
                $staffing_relation = collect($row->staffing_company_relation ?? []); // fallback to empty array if not set

                    $relationCount   = $staffing_relation->where('final_status', 1)->count();
                    // $spartnerAll = GeneralMethod::spartnerDataCnt($row->id);
                    // $relationCount = $spartnerAll['spartnerAll'];
                    echo '<span class="fw-bold d-block fs-7">' .
                       $relationCount .
                        '</span>
                </div>';
                @endphp
            </td>
        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="10" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
