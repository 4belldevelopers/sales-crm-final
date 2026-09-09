@php
    use App\Helper\GeneralMethod;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();

@endphp

<div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                @php
                // dd($bench_detail);
                    $jobtitle = @$job->title;
                    $bgTextDecore = $strVal = '';
                    $str = @$bench_detail->full_name ?? '';
                    $strVal = strtolower(mb_substr($str, 0, 1));
                    $str = ord($strVal);
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
                    
                    $max_sal_mode =  $max_salary_currency = $max_salary = '';
                    $max_sal_mode = strtoupper(mb_substr($salaryMode[@$bench_detail->bill_rate_mode] ?? '', 0, 1));
                    $max_salary_currency = $currencySymbole[@$bench_detail->bill_rate_currency] ?? '';
                    $max_salary = GeneralMethod::thousandsCurrencyFormat(@$bench_detail->bill_rate_amount);

                @endphp

                <td class="d-flex align-items-center border-0">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                        <a href="javascript:void(0);">
                            <div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
                        </a>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);"
                            class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$bench_detail->full_name) }}</a>
                        <a href="javascript:void(0);"
                            class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">{{ ucwords(@$bench_detail->current_designation) }}</a>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column ml-2">
                        <span class="badge fw-bolder fs-6 {{ @$bench_detail->css_style }} px-2 py-1">{{ ucwords(@$bench_detail->name) }}</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">B-{{ ucwords(@$bench_detail->id) }}</span>
                    </div>
                </td>

                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->

                <div class="d-flex flex-column me-5"> 
                    <span class="text-dark fw-bolder fs-6">{{ @$bench_detail->total_exp }}-{{ @$bench_detail->rel_exp }} Yrs</span> 
                    <span class="text-muted fw-bold d-block fs-7">{!! @$max_salary_currency !!} {{ @$max_salary }} / {{@$max_sal_mode}}</span> 
                </div>
               <span class="h-20px border-gray-200 border-start mx-4"></span>
                <div class="d-flex flex-column">
                     <span class="text-dark fw-bolder fs-6">
                        {{ @$bench_detail->city_name }}
                    </span> <span class="text-muted fw-bold d-block fs-7">{{ @$bench_detail->state_name }} </span> 
                <!--end::Interview Date & No. of Openings-->
                </div>
            </div>

{{-- <div class="d-flex flex-column end">
    <button class="btn btn-primary d-flex align-items-center gap-2" onclick="window.history.back()">
        ← Go Back
    </button>
</div> --}}