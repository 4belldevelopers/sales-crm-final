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
@endphp

<style>
    /* Same picker language as the offer-approval "Approval Email Send" drawer
       (create-offer.blade.php) — source-toggle cards + search + styled table.
       Scoped with a pkr- prefix since this view is loaded standalone via AJAX. */
    .pkr-source-row { display: flex; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; }
    .pkr-source-card { flex: 1 1 230px; display: flex; align-items: center; gap: 10px; cursor: pointer; border: 1px solid #e4e6ef; border-radius: 10px; padding: 10px 12px; background: #fff; margin: 0; min-width: 0; }
    .pkr-source-card input { margin: 0 6px 0 0; flex-shrink: 0; }
    .pkr-source-card--active { border-color: #1b84ff; background: #f3f8ff; }
    .pkr-source-ico { width: 34px; height: 34px; border-radius: 8px; background: #eef3ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .pkr-source-ico svg { width: 18px; height: 18px; }
    .pkr-source-card > div { min-width: 0; }
    .pkr-source-title { font-size: 12.5px; font-weight: 700; color: #181c32; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pkr-source-sub { font-size: 10.5px; color: #7e8299; margin-top: 1px; }

    .pkr-search-row { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .pkr-searchbox { flex: 1 1 auto; min-width: 0; display: flex; align-items: center; gap: 8px; height: 42px; background: #f8f9fb; border: 1px solid #e4e6ef; border-radius: 21px; padding: 0 16px; }
    .pkr-searchbox svg { width: 15px; height: 15px; color: #a1a5b7; flex-shrink: 0; }
    .pkr-searchbox:focus-within { border-color: #1b84ff; background: #fff; }
    .pkr-search-input { flex: 1 1 auto; min-width: 0; border: none; background: transparent; outline: none; font-size: 12.5px; height: 100%; }

    .pkr-mrow-av { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; margin-right: 8px; flex-shrink: 0; }
    .pkr-mrow-role { font-size: 10px !important; }
    .pkr-mrow-add { padding: 5px 14px !important; font-size: 11.5px !important; font-weight: 600; }
    .pkr-mrow-add.pkr-mrow--added { background: #e8fff3 !important; color: #0f9463 !important; }
    .pkr-empty { font-size: 11.5px; color: #a1a5b7; text-align: center; padding: 18px 4px; }
</style>

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

                    <!--begin::Source toggle-->
                    <div class="pkr-source-row">
                        <label class="pkr-source-card pkr-source-card--active" data-source="internal">
                            <input type="radio" name="pkrSource" value="internal" checked>
                            <span class="pkr-source-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M6 21V7l6-4 6 4v14M10 21v-6h4v6"/></svg></span>
                            <div>
                                <div class="pkr-source-title">Internal Company Approvers</div>
                                <div class="pkr-source-sub">{{ count($picker_internal ?? []) }} available</div>
                            </div>
                        </label>
                        @if(!empty($picker_client))
                        <label class="pkr-source-card" data-source="client">
                            <input type="radio" name="pkrSource" value="client">
                            <span class="pkr-source-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                            <div>
                                <div class="pkr-source-title">Client Company Approvers</div>
                                <div class="pkr-source-sub">{{ count($picker_client) }} available</div>
                            </div>
                        </label>
                        @endif
                    </div>
                    <!--end::Source toggle-->

                    <!--begin::Search-->
                    <div class="pkr-search-row">
                        <div class="pkr-searchbox">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                            <input type="text" class="pkr-search-input" id="pkr_search" placeholder="Search by name, email or company...">
                        </div>
                    </div>
                    <!--end::Search-->

                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_in_tra">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="fw-bolder text-muted">
                                <th class="min-w-200px">Approver</th>
                                <th class="min-w-200px">Contact</th>
                                <th class="min-w-150px">Company</th>
                                <th class="min-w-90px">Action</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold" id="Iteam_Table">
                            @foreach ([['data' => $picker_internal ?? [], 'source' => 'internal'], ['data' => $picker_client ?? [], 'source' => 'client']] as $group)
                                @foreach ($group['data'] as $teamVal)
                                    @php
                                        $bgTextDecore = $strVal = '';
                                        $str = trim(@$teamVal->name);
                                        $str = $strVal = strtolower($str[0] ?? 'c');
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
                                    @endphp

                                    <tr class="pkr-mrow pkr-mrow--{{ $group['source'] }} {{ $group['source'] === 'client' ? 'd-none' : '' }}"
                                        data-search="{{ strtolower(trim((string) @$teamVal->name).' '.@$teamVal->email.' '.@$teamVal->company_name) }}">
                                        <!--begin::Name-->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="pkr-mrow-av {{ @$bgTextDecore }}">{{ strtoupper(@$strVal) }}</div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 22)) }}</span>
                                                    <span class="text-muted fw-bold d-block pkr-mrow-role">{{ ucwords(substr(@$teamVal->display_name, 0, 25)) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <!--end::Name-->
                                        <td>{{ @$teamVal->email }}</td>
                                        <td>{{ @$teamVal->company_name }}</td>
                                        <!--begin::Action-->
                                        @if($panel)
                                        <td>
                                            @if(in_array(@$teamVal->email,$cc_email))
                                                <a id="team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="cp_select_panel pkr-mrow-add pkr-mrow--added btn btn-sm w-130 btn-light-danger" rel="{{ @$teamVal->id }}" data-assign="1" data-title="{{ @$teamVal->name }}" data-email="{{ @$teamVal->email }}" data-designation="{{ @$teamVal->display_name }}">Remove</a>
                                            @else
                                                <a id="team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="cp_select_panel pkr-mrow-add btn btn-sm btn-light-primary w-130" rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}"  data-email="{{ @$teamVal->email }}" data-designation="{{ @$teamVal->display_name }}"  >Add</a>
                                            @endif
                                        </td>
                                        @else
                                        <td>
                                            @if(in_array(@$teamVal->email,$cc_email))
                                                <a id="in_tr_team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="in_tr_select pkr-mrow-add pkr-mrow--added btn btn-sm w-130 btn-light-danger" rel="{{ @$teamVal->id }}" data-assign="1" data-title="{{ @$teamVal->name }}" data-email="{{ @$teamVal->email }}">Remove</a>
                                            @else
                                                <a id="in_tr_team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="in_tr_select pkr-mrow-add btn btn-sm btn-light-primary w-130" rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}" data-email="{{ @$teamVal->email }}">Add</a>
                                            @endif
                                        </td>
                                        @endif
                                        <!--end::Action-->
                                    </tr>
                                @endforeach
                            @endforeach
                            <tr class="pkr-mrow--internal pkr-mrow-empty {{ !empty($picker_internal) ? 'd-none' : '' }}"><td colspan="4" class="pkr-empty">No internal team members found.</td></tr>
                            <tr class="pkr-mrow--client pkr-mrow-empty d-none {{ !empty($picker_client) ? 'd-none' : '' }}"><td colspan="4" class="pkr-empty">No client contacts found.</td></tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Team Content-->
            </div>
            <!--end::Team card-->

<script>
    // Source toggle (Internal / Client) — same interaction as the offer-approval picker.
    $(document).on('click', '#kt_activitiesIntTeamtra_body .pkr-source-card', function () {
        var src = $(this).data('source');
        var $scope = $(this).closest('#kt_activitiesIntTeamtra_body');
        $scope.find('.pkr-source-card').removeClass('pkr-source-card--active');
        $(this).addClass('pkr-source-card--active');
        $(this).find('input[type=radio]').prop('checked', true);
        $scope.find('.pkr-mrow, .pkr-mrow-empty').addClass('d-none');
        $scope.find('.pkr-mrow--' + src + ':not(.pkr-mrow-empty)').removeClass('d-none');
        $scope.find('.pkr-mrow-empty.pkr-mrow--' + src).each(function () {
            if ($scope.find('.pkr-mrow--' + src + ':not(.pkr-mrow-empty)').length === 0) { $(this).removeClass('d-none'); }
        });
        $scope.find('#pkr_search').trigger('input');
    });
    // Search within the active source
    $(document).on('input', '#kt_activitiesIntTeamtra_body #pkr_search', function () {
        var q = $(this).val().trim().toLowerCase();
        var $scope = $(this).closest('#kt_activitiesIntTeamtra_body');
        var src = ($scope.find('.pkr-source-card--active').data('source')) || 'internal';
        $scope.find('.pkr-mrow--' + src + ':not(.pkr-mrow-empty)').each(function () {
            var hay = String($(this).data('search') || '');
            $(this).toggleClass('d-none', q !== '' && hay.indexOf(q) === -1);
        });
    });
</script>
