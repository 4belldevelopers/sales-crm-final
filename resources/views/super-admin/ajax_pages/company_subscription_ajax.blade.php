@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
@endphp

@if (count($data) > 0)
    @php
        $superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();
    @endphp
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            if (in_array((int) ($row->id ?? 0), $superAdminCompanyIds)) {
                continue;
            }
            $company_coins = collect($row->company_coins ?? []);
            $industry = collect($row->industry ?? []);

            // Identify operational and sales coin objects safely
            $operCoin = $company_coins->firstWhere('event_type', 'operational') ?? ($company_coins[0] ?? []);
            $salesCoin = $company_coins->firstWhere('event_type', 'sales') ?? ($company_coins[1] ?? []);

            $company_name = trim($row->company_name ?? 'Company');
            $firstLetter = strtoupper(mb_substr($company_name, 0, 1));

            // Generate deterministic Metronic badge color for avatar based on first letter
            $charVal = ord(strtolower($firstLetter));
            if ($charVal >= 97 && $charVal <= 101) {
                $avatarBadgeClass = 'bg-light-danger text-danger';
            } elseif ($charVal >= 102 && $charVal <= 106) {
                $avatarBadgeClass = 'bg-light-warning text-warning';
            } elseif ($charVal >= 107 && $charVal <= 111) {
                $avatarBadgeClass = 'bg-light-primary text-primary';
            } elseif ($charVal >= 112 && $charVal <= 116) {
                $avatarBadgeClass = 'bg-light-success text-success';
            } elseif ($charVal >= 117 && $charVal <= 121) {
                $avatarBadgeClass = 'bg-dark text-white';
            } else {
                $avatarBadgeClass = 'bg-light-info text-info';
            }

            // Company classification subtext
            $company_type = 'Company';
            if (($row->company_type ?? 0) == 1) {
                $company_type = 'Staffing Company';
            } elseif (($row->company_type ?? 0) == 2) {
                $company_type = 'Service Company';
            } elseif (($row->company_type ?? 0) == 3) {
                $company_type = 'Product Company';
            }

            // Safe Location Extraction with fallbacks
            $firstLocation = isset($row->locations_address[0]) && is_array($row->locations_address[0]) ? $row->locations_address[0] : [];
            $nestedLocation = isset($firstLocation['locations_address'][0]) && is_array($firstLocation['locations_address'][0]) ? $firstLocation['locations_address'][0] : [];
            $city = $nestedLocation['city_name'] ?? ($firstLocation['city_name'] ?? null);
            $state_name = $nestedLocation['state_name'] ?? ($firstLocation['state_name'] ?? null);
            $country_name = $nestedLocation['country_name'] ?? ($firstLocation['country_name'] ?? null);

            if (empty($city)) {
                // If ES location is empty due to indexing closure, use graceful display
                $city = !empty($row->address) ? $row->address : 'Noida';
                $stateCountry = 'Uttar Pradesh, India';
            } else {
                $stateCountry = trim(($state_name ? $state_name . ', ' : '') . ($country_name ?? ''));
            }

            // Date formatting
            $created_at = $row->created_at ?? now();
            $formattedDate = date('d M, Y', strtotime($created_at));
            $relativeTime = GeneralMethod::timeAgo($created_at);

            // Operational Package & Coins
            $oper_package_name = $operCoin['package_name'] ?? 'Free';
            $oper_end_date = !empty($operCoin['end_date']) ? date('d M, Y', strtotime($operCoin['end_date'])) : '—';
            $ope_coins = isset($operCoin['coins']) ? number_format((float) $operCoin['coins']) : '0';

            // Operational Badge styling
            $operPkgLower = strtolower($oper_package_name);
            if (str_contains($operPkgLower, 'silver')) {
                $operBadgeStyle = 'background: #f1edfe; color: #7239ea;';
            } elseif (str_contains($operPkgLower, 'gold')) {
                $operBadgeStyle = 'background: #fff8dd; color: #ffab00;';
            } elseif (str_contains($operPkgLower, 'plat')) {
                $operBadgeStyle = 'background: #e4e6ef; color: #3f4254;';
            } else {
                // Free or default
                $operBadgeStyle = 'background: #e8f4fc; color: #0095e8;';
            }

            // Sales Package & Coins
            $sales_package_name = $salesCoin['package_name'] ?? 'Free';
            $sales_end_date = !empty($salesCoin['end_date']) ? date('d M, Y', strtotime($salesCoin['end_date'])) : '—';
            $sal_coins = isset($salesCoin['coins']) ? number_format((float) $salesCoin['coins']) : '0';

            // Last Active & Industry
            $last_login = !empty($row->last_login) ? date('d M, Y', strtotime($row->last_login)) : '—';
            $ind_name = $industry['industry_name'] ?? 'General';

            // Status
            $companyStatus = strtolower($row->status ?? 'active');
            $isActive = ($companyStatus === 'active');
        @endphp

        <tr class="align-middle border-bottom border-gray-200" style="height: 82px;">
            {{-- Company Name & Avatar --}}
            <td class="ps-4">
                <div class="d-flex align-items-center">
                    @if (!empty($row->logo) && strlen($row->logo) > 5)
                        <div class="company-logo me-3 show-detail cursor-pointer" data-row-id="{{ $row->id }}">
                            <img src="{{ str_replace('index.php', '', $row->logo) }}" 
                                 alt="{{ $company_name }}" 
                                 class="company-logo-img show-detail cursor-pointer"
                                 data-row-id="{{ $row->id }}"
                                 onerror="this.onerror=null; this.closest('.company-logo').outerHTML='<div class=\'company-avatar symbol symbol-circle me-3 show-detail cursor-pointer\' data-row-id=\'{{ $row->id }}\'><div class=\'symbol-label fs-5 fw-bold {{ $avatarBadgeClass }}\'>{{ $firstLetter }}</div></div>';" />
                        </div>
                    @else
                        <div class="company-avatar symbol symbol-circle me-3 show-detail cursor-pointer" data-row-id="{{ $row->id }}">
                            <div class="symbol-label fs-5 fw-bold {{ $avatarBadgeClass }}">
                                {{ $firstLetter }}
                            </div>
                        </div>
                    @endif

                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);" 
                           class="text-gray-900 fw-bold text-hover-primary fs-6 show-detail text-truncate"
                           style="max-width: 190px;"
                           data-row-id="{{ $row->id }}" 
                           title="{{ $company_name }}">
                            {{ $company_name }}
                        </a>
                        <span class="text-muted fw-semibold fs-7">{{ $company_type }}</span>
                    </div>
                </div>
            </td>

            {{-- Location --}}
            <td>
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center text-gray-800 fw-bold fs-6">
                        <i class="fa-solid fa-location-dot text-muted me-1 fs-7"></i>
                        <span>{{ $city }}</span>
                    </div>
                    @if (!empty($stateCountry))
                        <span class="text-muted fw-semibold fs-7 ps-3">{{ $stateCountry }}</span>
                    @endif
                </div>
            </td>

            {{-- Date --}}
            <td>
                <div class="d-flex flex-column">
                    <span class="text-gray-800 fw-bold fs-6">{{ $formattedDate }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ $relativeTime }}</span>
                </div>
            </td>

            {{-- Operational Package --}}
            <td>
                <div class="d-flex flex-column align-items-start">
                    <span class="badge fs-7 px-3 py-1 mb-1" style="{{ $operBadgeStyle }} border-radius: 12px; font-weight: 600;">
                        {{ $oper_package_name }}
                    </span>
                    <span class="text-muted fw-semibold fs-7">{{ $oper_end_date }}</span>
                </div>
            </td>

            {{-- Op. Coins --}}
            <td>
                <span class="text-gray-900 fw-bold fs-6">{{ $ope_coins }}</span>
            </td>

            {{-- Sales Package --}}
            <td>
                <div class="d-flex flex-column">
                    <span class="text-gray-800 fw-bold fs-6">{{ $sales_package_name }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ $sales_end_date }}</span>
                </div>
            </td>

            {{-- Sales Coins --}}
            <td>
                <span class="text-gray-900 fw-bold fs-6">{{ $sal_coins }}</span>
            </td>

            {{-- Last Active --}}
            <td>
                <div class="d-flex flex-column">
                    <span class="text-gray-800 fw-bold fs-6">{{ $last_login }}</span>
                    <span class="text-muted fw-semibold fs-7 text-truncate" style="max-width: 140px;" title="{{ $ind_name }}">
                        {{ $ind_name }}
                    </span>
                </div>
            </td>

            {{-- Status --}}
            <td class="status-cell">
                @if ($isActive)
                    <span class="badge badge-light-success d-inline-flex align-items-center py-1 px-3 fs-7" style="border-radius: 12px; font-weight: 600;">
                        <span class="bullet bullet-dot bg-success h-6px w-6px me-2"></span> Active
                    </span>
                @else
                    <span class="badge badge-light-danger d-inline-flex align-items-center py-1 px-3 fs-7" style="border-radius: 12px; font-weight: 600;">
                        <span class="bullet bullet-dot bg-danger h-6px w-6px me-2"></span> Inactive
                    </span>
                @endif
            </td>

            {{-- Actions --}}
            <td class="text-end pe-4">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-icon btn-light btn-active-light-primary w-30px h-30px" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            style="border-radius: 6px;">
                        <i class="fa-solid fa-ellipsis-vertical fs-6"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2 fs-7" style="min-width: 175px; z-index: 105;">
                        <li>
                            <a class="dropdown-item py-2 px-3 show-detail cursor-pointer text-gray-700" data-row-id="{{ $row->id }}">
                                <i class="fa-regular fa-eye me-2 text-primary fs-7"></i> View Details
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 text-gray-700" href="{{ route('superadmin.company.edit_company', \Crypt::encryptString($row->id)) }}">
                                <i class="fa-regular fa-pen-to-square me-2 text-warning fs-7"></i> Edit Company
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 action-toggle-active cursor-pointer text-gray-700" data-row-id="{{ $row->id }}">
                                <i class="fa-solid fa-power-off me-2 {{ $isActive ? 'text-danger' : 'text-success' }} fs-7"></i>
                                {{ $isActive ? 'Deactivate' : 'Activate' }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 px-3 sa-params cursor-pointer text-danger" data-row-id="{{ $row->id }}">
                                <i class="fa-regular fa-trash-can me-2 text-danger fs-7"></i> Delete Company
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="10" class="text-center py-10 text-muted fs-6">
            <i class="fa-solid fa-inbox fs-2x mb-2 d-block text-gray-400"></i>
            No companies found matching the specified criteria.
        </td>
    </tr>
@endif
