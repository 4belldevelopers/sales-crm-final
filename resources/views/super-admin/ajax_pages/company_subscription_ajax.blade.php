@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
          $row = (object) $rowArr;
           $company_coins = collect($row->company_coins ?? []);
            $industry = collect($row->industry ?? []);
           
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

                    $company_type = '';
                    if ($row->company_type == 1) {
                        $company_type = 'Staffing Company';
                    } elseif ($row->company_type == 2) {
                        $company_type = 'Service Company';
                    } elseif ($row->company_type == 3) {
                        $company_type = 'Product Company';
                    }

                    $logo_path = url('public/avatar.png');
                    if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                        $logo_path = @$row->logo;

                        $image =
                            '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 " style="float:left" >
														<img src="' .
                            str_replace('index.php', '', @$logo_path) .
                            '" height="100px" width="100px" data-row-id="' .
                            $row->id .
                            '" class="' .
                            $client_type_css .
                            ' border-radius-5   tttt"  />
													</div>';
                    } else {
                        $image =
                            '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
														<a href="javascript:void(0);" data-row-id="' .
                            $row->id .
                            '" class="' .
                            $client_type_css .
                            ' border-radius-5  ssss" id="kt_activities3_toggle" >
															<div class="symbol-label fs-3 ' .
                            @$bgTextDecore .
                            '  border-radius-5">' .
                            ucwords(@$strVal) .
                            '</div>
														</a>
													</div>';
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
                        ucwords(mb_substr(@$row->company_name, 0, 20)) .
                        '</a>
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary ' .
                        $client_type_css .
                        '">' .
                        @$company_type .
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

                    echo '<div class="d-flex flex-column">
                                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        @$newDate .
                        '</a>
                                <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        @$daysAgo .
                        '</a>
                            </div>';
                @endphp
            </td>
            <td>
                @php
                   
                    $oper_end_date = $company_coins[0]['end_date'] ?? null;
                    $oper_package_name = $company_coins[0]['package_name'] ?? null;
                    if(!empty($oper_end_date)){
                    $oper_end_date = date('d M, Y', strtotime($oper_end_date));
                    }

                    echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        @$oper_package_name .
                        '</a>
                            <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        @$oper_end_date .
                        '</a>
                        </div>';
                @endphp
            </td>
            <td>
                @php
                    $ope_coins = $company_coins[0]['coins'] ?? null;
                    $ope_actual_coins = $company_coins[0]['actual_coins'] ?? null;
                    // $ope_coins = @$row->ope_coins;
                    // $ope_actual_coins = @$row->ope_actual_coins;

                    echo '<div class="d-flex flex-column">
							
						<span class="text-muted fw-bold text-muted d-block fs-7">' .
                        $ope_coins .
                        '</span>
					</div>';
                @endphp
            </td>
            <td>
                @php
                    $sales_end_date = $company_coins[1]['end_date'] ?? null;
                    $sales_package_name = $company_coins[1]['package_name'] ?? null;
                    if(!empty($sales_end_date)){
                     $sales_end_date = date('d M, Y', strtotime($sales_end_date));
                    }

                    echo '<div class="d-flex flex-column">
                                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        @$sales_package_name .
                        '</a>
                                <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        @$sales_end_date .
                        ' </a>
                            </div>';
                @endphp
            </td>
            <td>
                @php
                    $sal_coins = $company_coins[1]['coins'] ?? null;
                    $sal_actual_coins = $company_coins[1]['actual_coins'] ?? null;
                    // $sal_coins = @$row->sal_coins;
                    // $sal_actual_coins = @$row->sal_actual_coins;

                    echo '<div class="d-flex flex-column">
							
						<span class="text-muted fw-bold text-muted d-block fs-7">' .
                        $sal_coins .
                        '</span>
					</div>';
                @endphp
            </td>
            <td>
                @php
                    $last_login = $row->last_login;
                    $last_login = date('d M, Y', strtotime($last_login));
                    // $daysAgo = GeneralMethod::timeAgo($row->last_login);

                     $ind_name = $industry['industry_name'] ?? null;

                    echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        $last_login .
                        '</a>
                            <span class="text-muted fw-bold text-muted d-block fs-7">' .
                        ucwords(mb_substr(@$ind_name, 0, 15)) .
                        '</span>
                    </div>';
                @endphp
            </td>
        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="9" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
