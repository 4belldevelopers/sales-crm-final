@php
    use App\Helper\GeneralMethod;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter</title>
    <style>
        /* Scoped under .co-offerpreview so nothing here leaks onto the rest of the drawer —
           this fragment is injected via .html() into the create-offer Step 3 panel, so its
           <style> tag (if the browser keeps it) must not redefine generic classes like
           .container/.header/.content/.footer that the rest of the app also uses.
           Font: Poppins only, capped at 12px everywhere; complete white background; card-based. */
        .co-offerpreview, .co-offerpreview * {
            font-family: 'Poppins', sans-serif !important;
            box-sizing: border-box;
        }
        .co-offerpreview {
            background: #fff;
            font-size: 12px;
            color: #3f4254;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
        .co-offerpreview h1, .co-offerpreview h2, .co-offerpreview h3,
        .co-offerpreview h4, .co-offerpreview h5, .co-offerpreview h6 {
            font-size: 12px !important;
            font-weight: 700;
            color: #181c32;
            margin: 0;
        }
        .co-offerpreview p { font-size: 12px; line-height: 1.6; margin: 6px 0; }

        /* Card shell — every section (letterhead, letter body, salary annexure, acceptance)
           is its own card, matching the same enterprise card language used on steps 1 and 2. */
        .co-offerpreview .co-op-card {
            background: #fff; border: 1px solid #eef1f7; border-radius: 12px;
            padding: 16px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(24,28,50,.05);
        }

        /* Letterhead — logo top-left, address top-right (real company data, actual image) */
        .co-op-letterhead { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .co-op-logo img { max-height: 44px; max-width: 170px; object-fit: contain; display: block; }
        .co-op-address { text-align: right; font-size: 11px; color: #7e8299; line-height: 1.5; max-width: 280px; }
        .co-op-company { font-size: 12px; font-weight: 700; color: #181c32; }

        .co-op-section-title { font-size: 12px !important; font-weight: 700; color: #181c32; text-align: center; margin-bottom: 12px; }

        .co-offerpreview table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .co-offerpreview th, .co-offerpreview td { padding: 8px; text-align: left; border: 1px solid #eef1f7; font-size: 11px; }
        .co-offerpreview th { background: #f9fafd; font-weight: 700; color: #181c32; }
        .co-offerpreview tr.last_tr_all_table td { font-weight: 700; background: #f9fafd; color: #181c32; }

        .co-op-signature { margin-top: 30px; }
        .co-op-signature p { font-size: 12px; }
        .co-op-signature-line { margin-top: 70px; display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; }
        .co-op-signature-line > div { position: relative; width: 40%; border-top: 1px solid #d7dae3; padding-top: 8px; text-align: center; font-size: 11px; color: #7e8299; }

        /* Fit a 15.6" laptop and smaller: single fluid column, no fixed max-width, cards stack. */
        @media (max-width: 900px) {
            .co-op-letterhead { flex-direction: column; }
            .co-op-address { text-align: left; max-width: none; }
            .co-offerpreview table, .co-offerpreview th, .co-offerpreview td { font-size: 10px; }
        }
    </style>
</head>
@php
    $offer_terms = "";

    $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);

    $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
    $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
    $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
    $job_title = (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->job_title : '';
    $candidate_name =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->full_name : '';
    $candidate_email =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->email : '';
    $candidate_mobile =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->mobile : '';

    if($offer_terms_conditions){
        $offer_terms = str_replace(["[CANDIDATE_NAME]","[CANDIDATE_EMAIL]","[CANDIDATE_MOBILE]","[COMPANY_NAME]","[JOB_TITLE]","[JOINING_DATE]","[{JOB_TITLE}]"],[$candidate_name, $candidate_email,$candidate_mobile,$comapny_name,$job_title,''], $offer_terms_conditions->terms_condition);
    }

    // Always an actual image — same default-logo asset the Company Profile page falls back to.
    $co_op_logo_src = !empty($logo) ? $logo : asset('assets/logo-not-found.png');

    $currencyArr = ['INR', 'EUR', 'JPY', 'GBP', 'USD', 'AED'];
@endphp

<body>
    <div class="co-offerpreview">

        {{-- Letterhead card: logo left, company address right --}}
        <div class="co-op-card co-op-letterhead">
            <div class="co-op-logo">
                <img src="{{ $co_op_logo_src }}" alt="{{ $comapny_name ?: 'Company' }}"
                     onerror="this.src='{{ asset('assets/logo-not-found.png') }}'">
            </div>
            <div class="co-op-address">
                @if(!empty($comapny_name))<div class="co-op-company">{{ $comapny_name }}</div>@endif
                @if(!empty($address))<div>{{ $address }}</div>@endif
            </div>
        </div>

        @if($offer_terms_conditions)
            {{-- Offer letter body card (company's merged template content) --}}
            <div class="co-op-card">
                {!! $offer_terms !!}
            </div>
        @endif

        {{-- Salary Annexure card --}}
        <div class="co-op-card">
            <div class="co-op-section-title">Salary Annexure</div>

            @if (isset($offerDetails) && count($offerDetails) > 0)
                <div class="offer-details">
                    @foreach ($offerDetails as $offer)
                        @php
                            $salaryMode = ($offer['salary_mode'] == 2) ? 'Hourly' :(($offer['salary_mode'] == 1) ? 'Monthly' : (($offer['salary_mode'] == 3) ? 'Annual' : ''));
                        @endphp
                        @if ($offer['salary_mode'] == 2 || $offer['salary_mode'] == 1 || $offer['salary_mode'] == 3)
                            <table border="1" cellspacing="0" cellpadding="10">
                                <tr>
                                    <th>Components</th>
                                    <th>{{ $salaryMode }} ({{ $currencyArr[$offer['currency']] }})</th>
                                </tr>
                                @foreach ($offer['Basic_offer_points'] as $basic)
                                    <tr>
                                        <td>{{ $basic->basic_poins }}</td>
                                        <td> {{ $basic->basic_yearly > 0 ? $basic->basic_yearly : '---' }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr class="last_tr_all_table">
                                    <td>{{ __('messages.gross_amount') }}</td>
                                    <td> {{ $offer['yearly_gross'] > 0 ? $offer['yearly_gross'] : '---' }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                @if(isset($offer['Basic_offer_points']) && is_array($offer['Basic_offer_points']))
                                    @foreach ($offer['Bonus_offer_points'] as $bonus)
                                        <tr>
                                            <td>{{ $bonus->bonus_poins }}</td>
                                            <td> {{ $bonus->bonus_yearly > 0 ? $bonus->bonus_yearly : '---' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr class="last_tr_all_table">
                                        <td>{{ __('messages.final_amount') }}</td>
                                        <td> {{ ($offer['yearly_final_gross'] > 0) ? $offer['yearly_final_gross'] : '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">&nbsp;</td>
                                    </tr>
                                @endif
                            </table>
                        @else
                            {{-- Basic Offer Points --}}
                            <table border="1" cellspacing="0" cellpadding="10">
                                <tr>
                                    <th>Components</th>
                                    <th>{{ ( $offer['salary_mode']==4) ? 'Hourly' : 'Monthly' }} ({{ $currencyArr[$offer['currency']] }})</th>
                                    <th>Annually ({{ $currencyArr[$offer['currency']] }})</th>
                                </tr>

                                @foreach ($offer['Basic_offer_points'] as $basic)
                                    <tr>
                                        <td>{{ $basic->basic_poins }}</td>
                                        <td> {{ $basic->basic_monthly > 0 ? $basic->basic_monthly : '---' }}</td>
                                        <td> {{ $basic->basic_yearly > 0 ? $basic->basic_yearly : '---' }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr class="last_tr_all_table">
                                    <td>{{ __('messages.gross_amount') }}</td>
                                    <td> {{ $offer['monthly_gross'] > 0 ? $offer['monthly_gross'] : '---' }}</td>
                                    <td> {{ $offer['yearly_gross'] > 0 ? $offer['yearly_gross'] : '---' }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                @if(isset($offer['Basic_offer_points']) && is_array($offer['Basic_offer_points']))
                                    @foreach ($offer['Bonus_offer_points'] as $bonus)
                                        <tr>
                                            <td>{{ $bonus->bonus_poins }}</td>
                                            <td> {{ $bonus->bonus_monthly > 0 ? $bonus->bonus_monthly : '---' }}</td>
                                            <td> {{ $bonus->bonus_yearly > 0 ? $bonus->bonus_yearly : '---' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr class="last_tr_all_table">
                                        <td>{{ __('messages.final_amount') }}</td>
                                        <td> {{ ($offer['monthly_final_gross'] > 0) ? $offer['monthly_final_gross'] : '---' }}</td>
                                        <td> {{ ($offer['yearly_final_gross'] > 0) ? $offer['yearly_final_gross'] : '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">&nbsp;</td>
                                    </tr>
                                @endif
                            </table>
                        @endif
                    @endforeach
                </div>
            @else
                <p>No offer details available.</p>
            @endif
        </div>

        {{-- Candidate acceptance / signature card --}}
        <div class="co-op-card co-op-signature">
            <p><strong>Candidate Acceptance:</strong></p>
            <p>I, <strong>{{ $candidate_name }}</strong>, accept the offer to join <strong>{{ $comapny_name }}</strong> as a
                <strong>{{ $job_title }}</strong> under the terms outlined in this letter.</p>
            <div class="co-op-signature-line">
                <div>
                    <span style="position: absolute; top: -70px; left: 20px;">
                        @if(!empty($exits_offer->signature_candidate))
                            <img src="{{ $path }}" alt="signature" style="max-width: 100%; max-height: 60px;">
                        @endif
                    </span>
                    <p style="margin:0;">Candidate Signature</p>
                </div>
                <div>
                    <span style="position: absolute; top: -22px; left: 0; right: 0;">
                        @if(!empty($exits_offer->accept_date_time))
                            {{ date('d M Y', strtotime($exits_offer->accept_date_time)) }}
                        @endif
                    </span>
                    <p style="margin:0;">Date</p>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
