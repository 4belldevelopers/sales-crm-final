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
        
        .offer-details table {
            width: 100%;
            /* All tables will be 100% of the container width */
            border-collapse: collapse;
            /* Make the borders collapse for a cleaner look */
        }

        .offer-details th,
        .offer-details td {
            padding: 8px;
            /* Add padding to make the content more readable */
            text-align: left;
            /* Align text to the left */
            border: 1px solid #ddd;
            /* Add borders around cells */
        }

        .offer-details th {
            background-color: #f2f2f2;
            /* Add a background color for the table header */
        }

        /* Optional: Style for headers within the offer details */
        .offer-details h3,
        .offer-details h4 {
            margin-top: 20px;
            /* Add some spacing before headers */
        }

        .last_tr_all_table {
            opacity: 1;
            font-weight: bold;
        }


        .container {
            width: 90%;
            max-width: 850px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .header img {
            max-width: 160px;
            margin-bottom: 10px;
        }

        .cta-buttons {
            margin-top: 10px;
        }

        .cta-buttons a {
            display: inline-block;
            padding: 12px 25px;
            margin: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .accept-btn {
            background-color: #28a745;
        }

        .reject-btn {
            background-color: #dc3545;
        }

        .cta-buttons a:hover {
            opacity: 0.9;
        }

        .content {
            padding: 30px 20px;
        }

        .content h2 {
            font-size: 22px;
            color: #0056b3;
            margin-bottom: 15px;
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }

        .offer-details {
            margin-top: 25px;
            padding: 15px;
            background-color: #f9fbfc;
            border-left: 5px solid #0056b3;
            border-radius: 5px;
        }

        .offer-details p {
            margin: 8px 0;
        }

        .address-block {
            margin-bottom: 20px;
            font-size: 15px;
            color: #444;
        }

        .salary-structure {
            margin-top: 30px;
            border: 1px solid #e0e0e0;
            padding: 15px;
            background-color: #fefefe;
            border-radius: 8px;
        }

        .salary-structure h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .salary-structure table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .salary-structure th,
        .salary-structure td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .salary-structure th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .salary-structure td {
            font-size: 14px;
        }

        .terms {
            margin-top: 40px;
            font-size: 14px;
            color: #333;
        }

        .terms h3 {
            font-size: 18px;
            color: #0056b3;
            margin-bottom: 10px;
        }

        .terms ul {
            margin-top: 15px;
            padding-left: 20px;
        }

        .terms ul li {
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #888;
            border-top: 1px solid #e0e0e0;
            margin-top: 30px;
        }

        .signature-block {
            margin-top: 40px;
            padding-top: 10px;
            font-size: 16px;
        }

        .signature-block p {
            margin: 8px 0;
        }

        .signature-line {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .signature-line div {
            width: 35%;
            border-top: 1px solid #333;
            padding-top: 10px;
            text-align: center;
        }

        .company-footer {
            text-align: center;
            padding: 15px;
            background-color: #f9f9f9;
            margin-top: 50px;
            font-size: 14px;
            color: #666;
        }

       

        .signature-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        #signature-pad {
            width: 100%;
            height: 300px;
            border: 2px dashed #bbb;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .signature-actions {
            text-align: center;
            margin-top: 20px;
        }

        .signature-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
        }

        .clear-btn {
            background-color: #ff4d4d;
        }

        .save-btn {
            background-color: #4CAF50;
        }

        .signature-btn:hover {
            opacity: 0.9;
        }

        @media (max-width: 600px) {
            #signature-pad {
                height: 250px;
            }

            .signature-btn {
                width: 100%;
                margin: 5px 0;
            }
        }

        .header_second {
            text-align: center;
            padding-bottom: 2px;
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


@endphp

<body>
        @if($offer_terms_conditions)

        {!! $offer_terms!!}

        @endif
        
    <center>
        <h2>Salary Annexure</h2>
    </center>
    @php

        $currencyArr = ['INR', 'EUR', 'JPY', 'GBP', 'USD', 'AED'];

    @endphp
   
    {{-- Check if the offerDetails array has data --}}
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
                                        <td colspan="2"> </td> 
                                    </tr>
                                    <tr class="last_tr_all_table">
                                        <td>{{ __('messages.gross_amount') }}</td>
                                        <td> {{ $offer['yearly_gross'] > 0 ? $offer['yearly_gross'] : '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"> </td> 
                                    </tr>
                                    @if(isset($offer['Basic_offer_points']) && is_array($offer['Basic_offer_points']))
                                        @foreach ($offer['Bonus_offer_points'] as $bonus)
                                            <tr>
                                                <td>{{ $bonus->bonus_poins }}</td>
                                                <td> {{ $bonus->bonus_yearly > 0 ? $bonus->bonus_yearly : '---' }}</td>
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="3"> </td> 
                                        </tr>
                                        <tr class="last_tr_all_table">
                                            <td>{{ __('messages.final_amount') }}</td>
                                            <td> {{ ($offer['yearly_final_gross'] > 0) ? $offer['yearly_final_gross'] : '---' }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"> </td> 
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
                                        <td colspan="3"> </td> 
                                    </tr>
                                    <tr class="last_tr_all_table" style="font-weight: bold;">
                                        <td>{{ __('messages.gross_amount') }}</td>
                                        <td> {{ $offer['monthly_gross'] > 0 ? $offer['monthly_gross'] : '---' }}</td>
                                        <td> {{ $offer['yearly_gross'] > 0 ? $offer['yearly_gross'] : '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"> </td> 
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
                                            <td colspan="3"> </td> 
                                        </tr>
                                            <tr class="" style="font-weight: bold;">
                                                <td>{{ __('messages.final_amount') }}</td>
                                                <td> {{ ($offer['monthly_final_gross'] > 0) ? $offer['monthly_final_gross'] : '---' }}</td>
                                                <td> {{ ($offer['yearly_final_gross'] > 0) ? $offer['yearly_final_gross'] : '---' }}</td>
                                            </tr>
                                        <tr>
                                            <td colspan="3"> </td> 
                                        </tr>



                                @endif

                                </table>

                            @endif
                        @endforeach

                        
                    </div>
    @else
        <p>No offer details available.</p>
    @endif

    <div class="signature-block">
        <p><strong>Candidate Acceptance:</strong></p>
        <p>I, <strong>{{ $candidate_name }}</strong>, accept the offer to join <strong>{{ $comapny_name }}</strong> as a
            <strong>{{ $job_title }}</strong> under the terms outlined in this letter.</p>
        <div class="signature-line" style="margin-top: 85px;">
            <div style="position: relative">
                <span style="position: absolute;top: -100px;left: 20px">
                    @if(!empty($exits_offer->signature_candidate))
                    <img src="{{ $path }}" alt="signature" style="max-width: 100%;">
                    @endif
                </span>
                <span style="position: relative;top: 9px;">

                </span>

                <p>Candidate Signature</p>
            </div>
            <div style="position: relative">
                <span style="position: absolute;top: -27px;left: 65px">
                    @if(!empty($exits_offer->accept_date_time))
                      {{ date('d M Y', strtotime($exits_offer->accept_date_time)) }}
                    @endif
                </span>
                <p>Date</p>
            </div>
        </div>
    </div>


    <div class="company-footer" style="display: none;">
        <p><strong> {{ $comapny_name }}</strong></p>
        <p>{{ $address }}</p>
        <p>&copy; {{ date("Y") }} {{ $comapny_name }} - All Rights Reserved</p>
    </div>
<hr>
    
</body>

</html>
