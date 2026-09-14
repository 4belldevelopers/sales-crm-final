@php
    use App\Helper\GeneralMethod;
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
	  @php
        $row = (object) $rowArr;
        $company_name = ucwords($row->company_name ?? '');
        $client_id = $row->client_id ?? 0;
        $city = @$row->city_name;
        $country_name = @$row->country_name;
        $ind_name = $row->ind_name ?? '';
        $logo_path = (!empty(@$row->logo) && strlen(@$row->logo) > 10) ? @$row->logo : '';
        $staffing_company_invitation_id = $row->staffing_company_invitation_id ?? 0;
        $from_company_id = $row->from_company_id ?? 0;

        $str = trim(@$company_name);
        $strVal = strtolower($str[0] ?? 'a');
        $ascii = ord($strVal);
        if ($ascii >= 97 && $ascii <= 101) {
            $bgTextDecore = 'bg-light-danger text-danger';
        } elseif ($ascii >= 102 && $ascii <= 106) {
            $bgTextDecore = 'bg-light-warning text-warning';
        } elseif ($ascii >= 107 && $ascii <= 111) {
            $bgTextDecore = 'bg-light-primary text-primary';
        } elseif ($ascii >= 112 && $ascii <= 116) {
            $bgTextDecore = 'bg-light-success text-success';
        } elseif ($ascii >= 117 && $ascii <= 121) {
            $bgTextDecore = 'bg-dark text-white';
        } else {
            $bgTextDecore = 'bg-light-secondary text-dark';
        }
        $initials = strtoupper(mb_substr($strVal, 0, 1));

        $created_at = !empty($row->from_created_at) ? $row->from_created_at : ($row->created_at ?? '');
        $newDate = !empty($created_at) ? date('d M, Y', strtotime($created_at)) : '';
        $daysAgo = !empty($created_at) ? GeneralMethod::timeAgo($created_at) : '';
	  @endphp

        <div class="tbl-r">
            <div class="tbl-a show-detail-direct" data-row-id="{{ $client_id }}">
                @if (!empty($logo_path))
                    <div class="tbl-av show-detail-direct" data-row-id="{{ $client_id }}">
                        <img src="{{ $logo_path }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                @else
                    <div class="tbl-av show-detail-direct {{ $bgTextDecore }}" data-row-id="{{ $client_id }}">
                        {{ $initials }}
                    </div>
                @endif
                <div class="tbl-ai">
                    <div class="tbl-n show-detail-direct" data-row-id="{{ $client_id }}" title="{{ $company_name }}">{{ $company_name }}</div>
                    <div class="tbl-t">{{ $ind_name }}</div>
                </div>
            </div>
            <div>
                <div class="fw-bolder text-dark fs-6">{{ $city }}</div>
                <div class="text-muted fs-7">{{ $country_name }}</div>
            </div>
            <div>
                <div class="fw-bolder text-dark fs-6">{{ $newDate }}</div>
                <div class="text-muted fs-7">{{ $daysAgo }}</div>
            </div>
            <div class="d-flex flex-column gap-1 ms-auto" style="width: 100%; max-width: 180px;">
                @if(($row->to_invitation_status ?? 0) == 1)
                    <span class="btn btn-sm btn-success fw-bolder py-2 w-100">Accepted</span>
                @elseif(($row->to_invitation_status ?? 0) == -1)
                    <span class="btn btn-sm btn-danger fw-bolder py-2 w-100">Rejected</span>
                @elseif(($row->to_invitation_status ?? 0) == 2)
                    <span class="btn btn-sm btn-danger fw-bolder py-2 w-100" style="background-color: #ff2854; border-color: #ff2854; color: #fff;">Expired</span>
                @else
                    <a href="javascript:void(0);" data-row-id="{{ $staffing_company_invitation_id }}" data-row-event="-1" data-row-from_company="{{ $from_company_id }}" class="btn btn-sm btn-light fw-bolder py-2 w-100 request_event" style="background-color: #f5f8fa; color: #5e6278; text-align:center;">Ignore</a>
                    <a href="javascript:void(0);" data-row-id="{{ $staffing_company_invitation_id }}" data-row-event="1" data-row-from_company="{{ $from_company_id }}" class="btn btn-sm btn-primary fw-bolder py-2 w-100 request_event" style="text-align:center;">Accept</a>
                @endif
            </div>
        </div>

    @endforeach
@else
    <div class="p-5 text-center text-muted fw-bold">
        No pending job requests available
    </div>
@endif
