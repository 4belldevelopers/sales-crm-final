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
        $staffing_company_invitation_id = $row->staffing_company_invitation_id ?? 0;
        $from_company_id = $row->from_company_id ?? 0;

        $created_at = !empty($row->from_created_at) ? $row->from_created_at : ($row->created_at ?? '');
        $newDate = !empty($created_at) ? date('d M, Y', strtotime($created_at)) : '-';
        $daysAgo = !empty($created_at) ? GeneralMethod::timeAgo($created_at) : '';

        /* ── logo / avatar calculation ── */
        $background_image = '';
        if (!empty($row->logo) && strlen($row->logo) > 10) {
            $background_image = $row->logo;
        } elseif (!empty($row->website)) {
            $domainGet = GeneralMethod::getDomain($row->website);
            if ($domainGet) {
                $background_image = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=128";
            }
        }
        if (!empty($background_image) && !str_starts_with($background_image, 'http') && !str_starts_with($background_image, '//')) {
            $background_image = asset($background_image);
        }

        $cleanName = trim($company_name ?: 'Client');
        $strVal = strtolower($cleanName[0] ?? 'c');
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
            $bgTextDecore = 'bg-light-info text-info';
        } else {
            $bgTextDecore = 'bg-light-primary text-primary';
        }

        $nameParts = array_values(array_filter(explode(' ', $cleanName)));
        $initials = '';
        if (count($nameParts) >= 2) {
            $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
        } elseif (count($nameParts) === 1) {
            $initials = strtoupper(mb_substr($nameParts[0], 0, 2));
        } else {
            $initials = 'CL';
        }
    @endphp

        <div class="tbl-r">
            {{-- Checkbox --}}
            <div class="tbl-cell-check">
                <input type="checkbox" class="tbl-cb" value="{{ $client_id }}" onclick="event.stopPropagation()">
            </div>

            {{-- Client Name (with Company Logo / Initials Avatar) --}}
            <div class="tbl-cell-client show-detail-direct" data-row-id="{{ $client_id }}" title="{{ $company_name }}">
                <div class="cl-company-wrapper">
                    <div class="cl-logo-box">
                        @if (!empty($background_image) && strlen($background_image) > 10)
                            <img src="{{ $background_image }}" alt="{{ $initials }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                            <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                        @else
                            <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                        @endif
                    </div>
                    <div class="cl-details-box">
                        <div class="cl-name">
                            {{ $company_name }}
                        </div>
                        @if(!empty($ind_name))
                            <div class="cl-ind" title="{{ $ind_name }}">{{ $ind_name }}</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Location (NO ICON as requested) --}}
            <div class="tbl-cell-loc">
                @if(!empty($city))
                    <div class="cl-loc-city">{{ $city }}</div>
                @endif
                @if(!empty($country_name))
                    <div class="cl-loc-country">{{ $country_name }}</div>
                @endif
                @if(empty($city) && empty($country_name))
                    <div class="cl-loc-empty">-</div>
                @endif
            </div>

            {{-- Request Date --}}
            <div class="tbl-cell-type">
                <div class="cl-date-box">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>{{ $newDate }}</span>
                </div>
                @if(!empty($daysAgo))
                    <div class="cl-conn-caption">{{ $daysAgo }}</div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="tbl-cell-activity d-flex align-items-center justify-content-end gap-2">
                @if(($row->to_invitation_status ?? 0) == 1)
                    <span class="badge bg-light-success text-success fw-bold px-3 py-2">Accepted</span>
                @elseif(($row->to_invitation_status ?? 0) == -1)
                    <span class="badge bg-light-danger text-danger fw-bold px-3 py-2">Rejected</span>
                @elseif(($row->to_invitation_status ?? 0) == 2)
                    <span class="badge bg-light-danger text-danger fw-bold px-3 py-2">Expired</span>
                @else
                    <a href="javascript:void(0);" data-row-id="{{ $staffing_company_invitation_id }}" data-row-event="-1" data-row-from_company="{{ $from_company_id }}" class="btn btn-sm btn-light fw-bold px-3 py-1.5 request_event" style="background:#f1f5f9; color:#475569; border-radius:6px;">Ignore</a>
                    <a href="javascript:void(0);" data-row-id="{{ $staffing_company_invitation_id }}" data-row-event="1" data-row-from_company="{{ $from_company_id }}" class="btn btn-sm btn-primary fw-bold px-3 py-1.5 request_event" style="background:#2563eb; color:#fff; border-radius:6px;">Accept</a>
                @endif
            </div>
        </div>

    @endforeach
@else
    <div class="cl-empty-state">
        <div class="cl-empty-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
        </div>
        <div class="cl-empty-title">No pending job requests</div>
        <div class="cl-empty-desc">You are all caught up! There are currently no pending requests.</div>
    </div>
@endif
