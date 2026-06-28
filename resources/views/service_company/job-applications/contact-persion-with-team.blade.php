 <!--begin::Team card-->

 @php 
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$client_name);
					$str	= $strVal		=	strtolower($str[0]);
					$str					=	ord($str);
					if($str >=97 && $str <=101){
						$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
					}else if($str >=102 && $str <=106){
						$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
					}else if($str >=107 && $str <=111){
						$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
					}else if($str >=112 && $str <=116){
						$bgTextDecore		=	'fs-3 bg-light-success text-success';	
					}else if($str >=117 && $str <=121){
						$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
					}else {
						$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
					}	
					$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="" class="show-detail">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';
				  
				//   if(!empty($company->logo_logical_path)){
				//   		$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                //                         <a href="javascript:void(0);">
                //                             <div class="symbol-label border-radius-5">
                //                             <img src="'.url('public/'.@$company->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
                //                             </div>
                //                         </a>
                //                     </div>';
				//   }	
				  
@endphp


            <div class="card card-custom mx-2">
                <!--begin::Team header-->
                <div class="card-header px-5 py-0" id="kt_in_tra_header">
                    <div class="d-flex align-items-center py-1">
                        <div class=" d-flex align-items-center">

                             <div class=" d-flex align-items-center"> {!! @$icon !!}
                              <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_recruiter_name">{{($panel) ? $client_name : 'Assign Members'}}</a> </div>
                            </div>

                            <div class="d-flex flex-column" style="margin-left: 27px;"> 
                                @if($self_company==0)
                                 <a href="javascript:void(0);" class="btn btn-sm btn-primary align-self-center openModalTeam"  data-target="#add_team_member">Add Contact Person</a> 
                                @endif
                            </div>
                        </div>
                        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                            id="kt_activitiesIntTeamtra_close"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                        fill="#000000">
                                        <rect fill="#000000" x="0" y="7" width="16" height="2"
                                            rx="1" />
                                        <rect fill="#000000" opacity="0.5"
                                            transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                            x="0" y="7" width="16" height="2" rx="1" />
                                    </g>
                                </svg>
                            </span> </button>
                    </div>
                </div>
                <!--end::Team header-->
                <!--begin::Team Content-->
                <div class="card-body py-7">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_in_tra">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="fw-bolder text-muted">
                                <th class="w-10px pe-0">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-target="#Iteam_Table_in_tra .form-check-input-Item"
                                            value="1">
                                    </div>
                                </th>
                                <th class="min-w-250px">Name</th>
                                <th class="min-w-250px">Contact</th>
                                <th class="min-w-250px">Company</th>
                                <th class="min-w-100px">Action</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold" id="Iteam_Table">
                            @if (!empty($cc_email_assignData))

                                @foreach ($cc_email_assignData as $teamVal)
                                    @php
                                        $bgTextDecore = $strVal = '';
                                        $str = trim(@$teamVal->name);
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

                                        $working_status = $working_status_style = '';
                                        $working_status = @$teamVal->working_status == 1 ? 'Enable' : 'Disable';
                                        $working_status_style =
                                            @$teamVal->working_status == 1 ? 'text-success' : 'text-danger';
                                    @endphp

                                    <tr>
                                        <!--begin::Checkbox-->
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input form-check-input-Item" type="checkbox"
                                                    value="1">
                                            </div>
                                        </td>
                                        <!--end::Checkbox-->
                                        <!--begin::Name-->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin::Avatar-->
                                                <div
                                                    class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                                                    <a href="javascript:void(0);" class="">
                                                        <div
                                                            class="symbol-label {{ @$bgTextDecore }} border-radius-5">
                                                            {{ strtoupper(@$strVal) }}</div>
                                                    </a> </div>
                                                <!--end::Avatar-->
                                                <!--begin::Name details-->
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 15)) }}</span>
                                                    <span
                                                        class="text-muted fw-bold d-block fs-7">{{ ucwords(substr(@$teamVal->display_name, 0, 15)) }}</span>
                                                </div>
                                                <!--end::Name details-->
                                            </div>
                                        </td>
                                        <!--end::Name-->
                                        <!--begin::Role-->
                                        <td>
                                            <div class="d-flex flex-column"> <span
                                                    class="text-dark fw-bolder fs-6"></span> {{ @$teamVal->email }}
                                            </div>
                                        </td>
                                        <!--begin::Role-->
                                        <td>
                                            <div class="d-flex flex-column"> <span
                                                    class="text-dark fw-bolder fs-6"></span>
                                                {{ @$teamVal->company_name }}</div>
                                        </td>
                                        <!--begin::Action-->
                                        @if($panel)

                                        <td>
                                            @if(in_array(@$teamVal->email,$cc_email))

                                            <div class="d-flex flex-column"> 
                                                <a id="team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="cp_select_panel btn btn-sm w-130 btn-light-danger" rel="{{ @$teamVal->id }}" data-assign="1" data-title="{{ @$teamVal->name }}" data-email="{{ @$teamVal->email }}">Remove</a> 
                                            </div>

                                            @else

                                            <div class="d-flex flex-column"> 
                                                <a id="team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="cp_select_panel btn btn-sm btn-light-primary w-130" rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}"  data-email="{{ @$teamVal->email }}"  >Add</a> 
                                            </div>

                                            @endif

                                        </td>

                                        @else
                                        <td>
                                            {{-- @dd($cc_email); --}}
                                            @if(in_array(@$teamVal->email,$cc_email))

                                            <div class="d-flex flex-column"> <a id="in_tr_team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="in_tr_select btn btn-sm w-130 btn-light-danger" rel="{{ @$teamVal->id }}" data-assign="1" data-title="{{ @$teamVal->name }}" data-email="{{ @$teamVal->email }}">Remove</a> </div>

                                            @else

                                            <div class="d-flex flex-column"> <a
                                                    id="in_tr_team_select_{{ @$teamVal->id }}"
                                                    href="javascript:void(0);"
                                                    class="in_tr_select btn btn-sm btn-light-primary w-130"
                                                    rel="{{ @$teamVal->id }}" data-assign="0"
                                                    data-title="{{ @$teamVal->name }}"
                                                    data-email="{{ @$teamVal->email }}">Add</a> </div>

                                            @endif
                                        </td>
                                        @endif


                                        <!--end::Action-->
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Team Content-->
            </div>
            <!--end::Team card-->



            