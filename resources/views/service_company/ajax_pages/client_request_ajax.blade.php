@php
    use App\Helper\GeneralMethod;
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
	  @php
        $row = (object) $rowArr;
       
                 $company_name = '';
				$company_name = ucwords($row->company_name);

				$client_id = '';
				$client_id = $row->client_id;



				$locationHtml = '';
				
				

				
                $city = @$row->city_name;
                $country_name = @$row->country_name;
				$locationHtml = ucwords(substr(@$city, 0, 10)) . '&nbsp;' . @$country_name;


				$tags_html = '';
				$tags = $row->tags;
				if (!empty($tags)) {
					$tagsArr = explode(',', $tags);
					foreach ($tagsArr as $key => $val) {
						$tags_html .= '<span class="badge badge-light my-1 me-2">' . $val . '</span>';
					}
				}

				$ind_name = '';
				$ind_name = $row->ind_name;

				$logo_path = url('img/client-default.png');
				if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
					$logo_path = @$row->logo;
				}

				$staffing_company_invitation_id = '';
				$staffing_company_invitation_id = $row->staffing_company_invitation_id;

				$from_company_id = '';
				$from_company_id = $row->from_company_id;
	  @endphp

        <tr style="">
            <td class="show-detail-direct" data-row-id="{{ $row->client_id }}">
				@php
				
				
                    $image = '';
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$company_name);
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

					$logo_path = ''; //url('img/client-default.png');
					if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
						$logo_path = @$row->logo;
					}
					$client_type_css = '';

				@endphp

				@php
				if (!empty($logo_path)) {
                            echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 ' .$client_type_css .'" style="float:left"> 
								<img src="' .$logo_path .'" height="100px" width="100px" class="' .$client_type_css .' border-radius-5">
							</div>';
                        } else {
                            echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
								<a href="javascript:void(0);" data-row-id="' .$row->client_id .'" class="' . $client_type_css . ' border-radius-5  ssss" id="kt_activities3_toggle" >
									<div class="symbol-label fs-3 ' .@$bgTextDecore .'  border-radius-5">' . ucwords(@$strVal) . '</div>
									</a>
							</div>';
                        }
				@endphp	

                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="{{$row->client_id}}" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" data-html="true" title="{{ $company_name }}">{{ $company_name }}</a>
                    <a href="javascript:void(0);" data-row-id="{{ $row->client_id }}" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail">{{ $ind_name }}</a>
				</div>
			</td>
            <td>
				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ $row->city_name }}</a>
				<div class="d-flex align-items-center">
					<span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">{{ $row->country_name }}</span>
				<div>
			</td>
            <td>
				@php
				
                        $created_at = $row->created_at;
                        $newDate = date('d M, Y', strtotime($created_at));
                        $daysAgo = GeneralMethod::timeAgo($row->created_at);

				@endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ $newDate }}</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">{{ $daysAgo }}</a>
                  </div>
            </td>
            <td>
                <div class="d-flex flex-column">
						@php
						
							if($row->to_invitation_status==1){
								echo '
									<a href="javascript:void(0);" type="button" class="btn btn-sm btn-success fw-bolder me-3 fs-6">Accepted</a> 
							';
							}elseif($row->to_invitation_status==-1){
							echo ' 
									<a href="javascript:void(0);" type="button" class="btn btn-sm btn-danger fw-bolder me-3 fs-6">Rejected</a> 
							';
							}elseif($row->to_invitation_status==2){
							echo ' 
									<a href="javascript:void(0);" type="button" class="btn btn-sm btn-danger fw-bolder me-3 fs-6">Expired</a> 
							';
							}else{
							echo '
									<a href="javascript:void(0);" data-row-id="' . $staffing_company_invitation_id . '" data-row-event="-1" data-row-from_company="' . $from_company_id . '" type="button" class="btn btn-sm btn-light fw-bolder  mb-1 me-3 fs-6 request_event">Ignore</a> 
									<a href="javascript:void(0);" data-row-id="' . $staffing_company_invitation_id . '" data-row-event="1" data-row-from_company="' . $from_company_id . '" type="button" class="btn btn-sm btn-primary fw-bolder me-3 fs-6 request_event">Accept</a> 
							';
							}
						@endphp
				</div>            
			</td>
        </tr>

    @endforeach
@else

    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="5" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
