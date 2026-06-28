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
    @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    if ($row->root_user == 1) {
                        echo '<div class="form-check form-check-sm form-check-solid"><input class="form-check-input form-check-input1"  type="checkbox" disabled></div>';
                    } else {
                        $status_disabled = "disabled";
                        $status = $row->status;
                    if ($status == 'active') {
                        $status_disabled = '';
                    }
                        echo '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input1 chk_list_item" name="chk_list_item[]" type="checkbox" value="' . $row->id . '" '.$status_disabled.'></div>';
                    }
                @endphp
            </td>
            <td>
                @php
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->name);
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
                    $icon =
                        '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 show-detail-team" data-row-id="' .
                        $row->id .
                        '" style="float:left" >
													 <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="show-detail-team" id="kt_activities3_toggle" >
														<div class="symbol-label fs-3 ' .
                        @$bgTextDecore .
                        '  border-radius-5">' .
                        ucwords(@$strVal) .
                        '</div>
													</a>
											</div>';
                    if (!empty($row->image)) {
                        $imagePath = '';
                        $imagePath = @$row->image;
                        $icon =
                            '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 show-detail-team" data-row-id="' .
                            $row->id .
                            '" style="float:left">
														<a href="javascript:void(0);">
															<div class="symbol-label border-radius-5">
															<img src="' .
                            $imagePath .
                            '" alt="User Image" class="border-radius-5 w-100">
															</div>
														</a>
													</div>';
                    }
                    $departmentArr = ucwords(@$row->designation);
                    echo $icon .
                        '<div class="d-flex flex-column" data-row-id="' .
                        $row->id .
                        '">
										<a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="' .
                        ucwords(@$row->name) .
                        '">' .
                        ucwords(mb_substr(@$row->name, 0, 30)) .
                        '</a>
										<a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        @$departmentArr .
                        '</a>
									  </div>
									  ';
                @endphp
            </td>
            <td>
                @php
                    $disabled		=	$row->root_user == 1 ? '':'update-user-role';
					$update		=	$row->root_user == 1 ? '':'';
                    echo '<div class="d-flex flex-column '.$disabled.'" data-row-id="' . $row->id . '">
										<span class="text-dark fw-bolder fs-6">'.ucwords($row->display_name).'
										 
											'.$update.'
										</span>
									</div>';
                @endphp

            </td>
            <td>
                @php
                    
                        echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$row->mobile . '</a>
                            <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">' . @$row->email . '</a>
                          </div>';
                    
                @endphp
            </td>
            <td>
                @php
                    $disabled = $row->root_user == 1 ? '' : 'update-user-role';
                    echo '<span class="bg-light-success text-'.($row->status=='active' ? 'success' : 'danger').' fw-bolder fs-6">'.($row->status=='active' ? 'Active' : 'Inactive').'</span>';
                @endphp
            </td>
            <td>
                <a href="{{route('superadmin.team.edit', [$row->id])}}" class="" data-toggle="tooltip" data-original-title="Edit">
                    <i class="fa fa-edit text-primary fs-4" aria-hidden="true"></i>
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="6" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
