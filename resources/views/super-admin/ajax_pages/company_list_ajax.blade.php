@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $rowId = (int) ($row->id ?? 0);
            $idx = ((($page ?? 1) - 1) * ($size ?? 10)) + $loop->iteration;

            // Company initial & background decoration
            $strVal = '';
            $bgTextDecore = 'bg-light-secondary text-dark';
            $companyName = trim((string) (@$row->company_name ?? ''));
            if (!empty($companyName)) {
                $strVal = strtolower($companyName[0]);
                $ord = ord($strVal);
                if ($ord >= 97 && $ord <= 101) {
                    $bgTextDecore = 'bg-light-danger text-danger';
                } elseif ($ord >= 102 && $ord <= 106) {
                    $bgTextDecore = 'bg-light-warning text-warning';
                } elseif ($ord >= 107 && $ord <= 111) {
                    $bgTextDecore = 'bg-light-primary text-primary';
                } elseif ($ord >= 112 && $ord <= 116) {
                    $bgTextDecore = 'bg-light-success text-success';
                } elseif ($ord >= 117 && $ord <= 121) {
                    $bgTextDecore = 'bg-dark text-white';
                }
            }

            // Location details
            $firstLocation = isset($row->locations_address[0]['locations_address'][0]) && is_array($row->locations_address[0]['locations_address'][0])
                ? $row->locations_address[0]['locations_address'][0]
                : [];
            $cityName = $firstLocation['city_name'] ?? 'Noida';
            $countryName = $firstLocation['country_name'] ?? 'India';

            // Dates
            $createdFormatted = !empty($row->created_at) ? date('d M, Y', strtotime($row->created_at)) : 'N/A';
            $lastActiveFormatted = !empty($row->last_login) ? date('d M, Y', strtotime($row->last_login)) : date('d M, Y');

            // Job Status Counts (Pre-aggregated batch map or fallback)
            if (isset($jobCountsMap) && is_array($jobCountsMap)) {
                $activeJobs = $jobCountsMap[$rowId]['active'] ?? 0;
                $holdJobs = $jobCountsMap[$rowId]['hold'] ?? 0;
                $closedJobs = $jobCountsMap[$rowId]['inactive'] ?? 0;
            } else {
                $activeJobs = GeneralMethod::jobStatusCountElastic($rowId, 'active');
                $holdJobs = GeneralMethod::jobStatusCountElastic($rowId, 'hold');
                $closedJobs = GeneralMethod::jobStatusCountElastic($rowId, 'inactive');
            }

            // Candidates & Join
            $candidates = collect($row->candidates ?? []);
            $allCandidatesCount = $candidates->count();
            $joinCount = $candidates->where('master_status_id', 6)->count();

            // Bench Candidates
            $benchCandidates = collect($row->bench_candidates ?? []);
            $benchCount = $benchCandidates->where('status', 2)->count();
            $poolCount = $benchCandidates->where('status', 1)->count();
            $placeCount = $benchCandidates->where('status', 3)->count();

            // Operational & Sales Packages (100% CQRS from indexed ES company_coins)
            $opCoins = null;
            $salesCoins = null;
            if (!empty($row->company_coins) && is_array($row->company_coins)) {
                foreach ($row->company_coins as $c) {
                    $cObj = (object) $c;
                    $eventType = strtolower(trim((string) ($cObj->event_type ?? '')));
                    if ($eventType === 'operational' && !$opCoins) {
                        $opCoins = $cObj;
                    } elseif ($eventType === 'sales' && !$salesCoins) {
                        $salesCoins = $cObj;
                    }
                }
            }
            $opTitle = !empty($opCoins->package_name) ? $opCoins->package_name : 'Silver';
            $opBalance = isset($opCoins->coins) ? $opCoins->coins : 5029.5;

            $salesTitle = !empty($salesCoins->package_name) ? $salesCoins->package_name : 'Free';
            $salesBalance = isset($salesCoins->coins) ? $salesCoins->coins : 1900;

            // Staffing Partners
            $staffingRelation = collect($row->staffing_company_relation ?? []);
            $spCount = $staffingRelation->where('final_status', 1)->count();

            $industryName = !empty($row->ind_name) ? $row->ind_name : (!empty($row->industry_name) ? $row->industry_name : 'Technology & IT Services');
            $isVerified = !empty($row->accountVerify) && (int) $row->accountVerify === 1;
        @endphp

        <tr class="sa-company-row" data-company-id="{{ $rowId }}">
            {{-- Checkbox --}}
            <td class="col-chkbox ps-4">
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input row-select-check company-row-checkbox" type="checkbox" value="{{ $rowId }}">
                </div>
            </td>

            {{-- Company Name & Avatar --}}
            <td class="col-company">
                <div class="d-flex align-items-center">
                    @if (!empty($row->logo) && strlen($row->logo) > 10)
                        <img src="{{ str_replace('index.php', '', $row->logo) }}" 
                             alt="{{ $companyName }}" 
                             class="company-avatar show-detail me-3 cursor-pointer" 
                             data-row-id="{{ $rowId }}">
                    @else
                        <div class="company-initial-badge {{ $bgTextDecore }} show-detail me-3 cursor-pointer" 
                             data-row-id="{{ $rowId }}">
                            {{ strtoupper($strVal) }}
                        </div>
                    @endif

                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-1">
                            <a href="javascript:void(0);" 
                               class="text-dark fw-bolder text-hover-primary fs-6 show-detail" 
                               data-row-id="{{ $rowId }}"
                               title="{{ $companyName }}">
                                {{ $companyName }}
                            </a>
                            <span class="company-verify-badge-container" data-company-id="{{ $rowId }}">
                                @if ($isVerified)
                                    <i class="bi bi-patch-check-fill text-primary fs-6 verify-badge-icon" title="Verified Organization" data-bs-toggle="tooltip"></i>
                                @else
                                    <i class="bi bi-hourglass-split text-warning fs-6 verify-badge-icon" title="Pending Verification" data-bs-toggle="tooltip"></i>
                                @endif
                            </span>
                        </div>
                        <span class="text-muted fs-7 fw-semibold">{{ $industryName }}</span>
                    </div>
                </div>
            </td>

            {{-- Location --}}
            <td class="col-location">
                <div class="d-flex align-items-center gap-1">
                    <i class="bi bi-geo-alt text-muted fs-7"></i>
                    <span class="text-dark fw-bold fs-7">{{ $cityName }}</span>
                </div>
                <span class="text-muted fs-8 d-block ps-4">{{ $countryName }}</span>
            </td>

            {{-- Date --}}
            <td class="col-date">
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bold fs-7">{{ $createdFormatted }}</span>
                    <span class="text-muted fs-8">Last Active <span class="fw-semibold text-gray-700">{{ $lastActiveFormatted }}</span></span>
                </div>
            </td>

            {{-- Jobs --}}
            <td class="col-jobs">
                <div class="d-flex flex-column">
                    <span class="fw-boldest text-success fs-7">{{ $activeJobs }} Active</span>
                    <div class="d-flex align-items-center gap-1 fs-8">
                        <span class="text-warning fw-semibold">{{ $holdJobs }} Hold</span>
                        <span class="text-muted">|</span>
                        <span class="text-danger fw-semibold">{{ $closedJobs }} Closed</span>
                    </div>
                </div>
            </td>

            {{-- Join --}}
            <td class="col-join">
                <span class="text-dark fw-bold fs-7">{{ $joinCount }}</span>
            </td>

            {{-- Bench --}}
            <td class="col-bench">
                <div class="d-flex flex-column">
                    <span class="fw-boldest text-success fs-7">{{ $benchCount }} Bench</span>
                    <div class="d-flex align-items-center gap-1 fs-8">
                        <span class="text-warning fw-semibold">{{ $poolCount }} Pool</span>
                        <span class="text-muted">|</span>
                        <span class="text-danger fw-semibold">{{ $placeCount }} Place</span>
                    </div>
                </div>
            </td>

            {{-- Operational Package --}}
            <td class="col-oppack">
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bold fs-7">{{ $opTitle }}</span>
                    <span class="text-muted fs-8">{{ is_numeric($opBalance) ? number_format($opBalance, 1) : $opBalance }} Remaining Coins</span>
                </div>
            </td>

            {{-- Sales Package --}}
            <td class="col-salespack">
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bold fs-7">{{ $salesTitle }}</span>
                    <span class="text-muted fs-8">{{ is_numeric($salesBalance) ? number_format($salesBalance) : $salesBalance }} Remaining Coins</span>
                </div>
            </td>

            {{-- SP. --}}
            <td class="col-sp">
                <span class="text-dark fw-bold fs-7">{{ $spCount }}</span>
            </td>

            {{-- Actions Menu --}}
            <td class="col-actions text-end pe-4">
                <div class="dropdown">
                    <button class="btn btn-sm btn-icon btn-light btn-active-light-primary rounded-circle" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            title="Actions">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="min-width: 190px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 show-detail" 
                               href="javascript:void(0);" 
                               data-row-id="{{ $rowId }}">
                                <i class="bi bi-eye text-primary fs-6"></i> View Details
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" 
                               href="{{ route('superadmin.company.edit_company', \Crypt::encryptString($rowId)) }}">
                                <i class="bi bi-pencil-square text-info fs-6"></i> Edit Company
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" 
                               href="{{ route('superadmin.company.changePackage', $rowId) }}">
                                <i class="bi bi-box text-warning fs-6"></i> Change Package
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 toggle-account-verify" 
                               href="javascript:void(0);" 
                               data-row-id="{{ $rowId }}">
                                @if ($isVerified)
                                    <i class="bi bi-x-circle text-danger fs-6"></i> Mark Unverified
                                @else
                                    <i class="bi bi-check-circle text-success fs-6"></i> Verify Company
                                @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 code_generator" 
                               href="javascript:void(0);" 
                               data-id="{{ $rowId }}">
                                <i class="bi bi-key text-secondary fs-6"></i> Access Code
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 btn-login-company" 
                               href="javascript:void(0);" 
                               data-id="{{ $rowId }}">
                                <i class="bi bi-box-arrow-in-right text-success fs-6"></i> Login as Company
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger sa-params" 
                               href="javascript:void(0);" 
                               data-row-id="{{ $rowId }}">
                                <i class="bi bi-trash text-danger fs-6"></i> Delete Company
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="11" class="text-center py-10">
            <div class="d-flex flex-column align-items-center justify-content-center">
                <i class="bi bi-building-exclamation text-muted" style="font-size: 3rem;"></i>
                <div class="text-dark fw-bold fs-5 mt-3">No companies found</div>
                <div class="text-muted fs-7 mt-1">Try adjusting your search criteria or resetting active filters</div>
            </div>
        </td>
    </tr>
@endif
