@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();    
    $salaryModeOffer = GeneralMethod::salaryModeOffer();
    $job_status = GeneralMethod::job_status();

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $row->thirdparty_company_id = $row->thirdparty_company_id ?? $row->ClientComID ?? $row->client_comapny_id ?? 0;
    $row->ClientComID = $row->ClientComID ?? $row->thirdparty_company_id ?? 0;
    $row->name = $row->name ?? $row->master_status_name ?? '';
    $row->master_status_id = $row->master_status_id ?? $row->job_master_status_id ?? 0;



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
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->full_name);
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
                    echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="custom-show-application-detail">
                      <div class="symbol-label fs-3 ' .
                        $bgTextDecore .
                        ' text-warning border-radius-5">' .
                        ucwords($strVal) .
                        '</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->wiseId .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 custom-show-application-detail" data-toggle="tooltip" title="' .
                        ucwords(@$row->full_name) .
                        '">' .
                        ucwords(mb_substr(@$row->full_name, 0, 20)) .
                        '</a> 
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->wiseId .
                        '"  class="text-muted fw-bold d-block fs-7 text-hover-primary custom-show-application-detail" data-toggle="tooltip" title="' .
                        ucwords(@$row->current_designation) .
                        '">' .
                        ucwords(mb_substr(@$row->current_designation, 0, 20)) .
                        '</a>
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $rawTitle = $row->title ?? $row->job_title ?? '';
                    $jobtitle = mb_substr($rawTitle, 0, 15);
                    $rawClientName = $row->client_name ?? '';
                    $client_name = mb_substr($rawClientName, 0, 15);

                    $recommendation_id = $row->recommendation_id ?? $row->to_company_id ?? 0;
                    if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $client_name = ucwords(mb_substr(@$row->company_name, 0, 30));
                    }

                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        $rawTitle .
                        '">' .
                        $jobtitle .
                        '</a> 
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' .
                        $rawClientName .
                        '">' .
                        $client_name .
                        '</a>
                  </div>';

                @endphp
            </td>
            <td>
                @php
                    $rowJobs = GeneralMethod::jobDetails($row->jobId);
                    $UploadDate = $className = '';
                    $UploadDate = @$row->created_at;
                    $UploadDate = date('d M, Y', strtotime($UploadDate));
                    
                    $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                    $status = ucwords($rowJobs->status);
                    $class = $job_status[$rowJobs->status];

                    if ($status == 'Active') {
                        $status = 'Hiring';
                    }
                    if ($status == 'Inactive') {
                        $status = 'Closed';
                    }

                    $recommendation_text = __('messages.clinet_tag');
                    if (@$row->client_comapny_id == auth()->user()->company_id) {
                        $recommendation_text = __('messages.internal_tag');
                    }
                    echo '<div class="d-flex flex-column"> 
                        <a href="javascript.void(0);" data-row-id="' .
                        @$row->id .
                        '" class="d-flex flex-column ' .
                        @$class .
                        ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' .
                        $status .
                        '</a>
                <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="">' .
                                $row->jobId .
                                '-' .
                                $recommendation_text .
                                '</span> 
                </div>';
                @endphp
            </td>
            <td>
                @php
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->posted_by);
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

                    echo '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$row->posted_by .
                        '">' .
                        mb_substr($row->posted_by, 0, 10) .
                        '</a>
							
						</div>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords(@$row->company_name) .
                        '">' .
                        ucwords(mb_substr(@$row->company_name, 0, 25)) .
                        '</span>';

                @endphp
            </td>
            <td>
                @php
                    $offer_currency = $offer_mode = '';
                    // if(!empty($row->offer_currency)){
                    $offer_currency = $currencySymbole[@$row->of_offer_currency ? $row->of_offer_currency : 0];
                    // }

                    // if(!empty($row->offer_mode)){
                    $offer_mode = $salaryModeOffer[@$row->of_offer_mode ? $row->of_offer_mode : 0];
                    // }

                    // dd("dkj");

                    $employment_type = 'Full-time';
                    if ($row->employment_type == 1) {
                        $employment_type = 'Contract';
                    }

                    if ($row->of_offer_amount > 0) {
                        echo '<div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">' .
                            $offer_currency .
                            ' ' .
                            $row->of_offer_amount .
                            '/' .
                            mb_substr($offer_mode, 0, 1) .
                            '</span>
						<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' .
                            $employment_type .
                            '">' .
                            $employment_type .
                            '</a>
					</div>';
                    } else {
                        echo '<div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">' .
                            $employment_type .
                            '</span>
                     </div>';
                    }

                @endphp
            </td>
            <td>
                @php
                    $joined_date = $joined_time = $date = '';
                    $date = @$row->of_joined_date;
                    if ($date != '0000-00-00 00:00:00' && !empty($date)) {
                        $joined_date = date('d-M-y', strtotime($date));
                        $joined_time = date('H:i a', strtotime($date));
                    }
                    echo '<div class="d-flex flex-column">
							 <span class="text-dark fw-bolder fs-6">' .
                        $joined_date .
                        '</span>
							 <span class="text-muted fw-bold d-block fs-7">' .
                        $joined_time .
                        '</span>
						 </div>';
                @endphp
            </td>
            <td>
                @php
                    $stage_default_css = 'stage_sub_stage';

                    if ($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) {
                        $stage_default_css = 'client_stage_sub_stage';
                    }
                    if (
                        $row->thirdparty_company_id > 0 &&
                        $row->thirdparty_company_id == $company_id &&
                        $row->master_status_id != 8
                    ) {
                        $stage_default_css = '';
                    }
                    $name = '';
                    if ($row->master_status_id == 8) {
                        $name = '';
                    }
                    if (!empty(@$row->childStatus) && @$row->childStatus > 0) {
                        $childStatus = '';
                        $childStatus = DB::table('candidate_status')
                            ->select('*')
                            ->where('id', $row->childStatus)
                            ->first();
                        if (!empty($childStatus)) {
                            $name = @$childStatus->name;
                        }
                    }
                    echo '<div class="d-flex flex-column">
                            <a href="javascript.void(0);" data-row-id="' .
                        @$row->wiseId .
                        '" class="d-flex flex-column ' .
                        $stage_default_css .
                        '" data-toggle="modal" data-target="#' .
                        $stage_default_css .
                        '">
                                <span class="badge fw-bolder fs-6 ' .
                        @$row->css_style .
                        ' px-2 py-1">' .
                        ucwords(mb_substr($row->name, 0, 10)) .
                        '</span>
                                <span class="text-muted fw-bold d-block fs-7">' .
                        @$name .
                        '</span>
                               </a>
                            </div>';
                @endphp
            </td>

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="8" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
