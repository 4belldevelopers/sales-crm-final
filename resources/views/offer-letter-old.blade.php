<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }



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
            background: #f7bfbf61;
            opacity: 1;
            font-weight: bold;
        }


    @page {
            margin: 50px;
        }

        body {
            font-family: Arial, sans-serif;
            position: relative;
        }

        /* Signature container positioned at the bottom-right of every page */
        .signature-container {
            position: fixed;
            top: 85%; /* Distance from the top */
            left: 75%; /* Center horizontally */
            transform: translateX(-50%); /* Adjust for center alignment */
            text-align: center;
        }

        .signature-container img {
            width: 150px; /* Adjust the width of the signature */
            height: auto;
        }

        /* Make sure the signature appears on every page */
        /* .page {
            page-break-after: always;
            position: relative;
        }

        .page .signature-container {
            position: absolute;
            bottom: 10px;
            right: 20px;
        } */

        /* Additional styles for content formatting */
    
        .offer-details, .terms-and-conditions {
            margin-bottom: 10px;
        }

        .logo-container {
            position: fixed;
            top: -85px; /* Distance from the top */
            left: 45%; /* Center horizontally */
            transform: translateX(-50%); /* Adjust for center alignment */
            text-align: center;
        }

        .logo-container img {
            width: 150px; /* Adjust the size of your logo here */
            height: auto;
        }
    
    </style>
</head>

<body>
        <div class="logo-container">
            <img src="https://jero.co.in/img/Icon-meecruit.png" alt="Company Logo">
        </div>
        <div class="signature-container">
            <h4 style="position: absolute;top:0px">Candidate Signature </h4>
            <img src="https://jero.co.in/img/Icon-meecruit.png" alt="Signature">
        </div>

        @if($offer_terms_conditions)

        {!! $offer_terms_conditions->terms_condition !!}
        {{-- Page Break for PDF --}}
        <div style="page-break-after: always;"></div>

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
                @if($offer['salary_mode']==2)
                    <h4>Basic Components</h4>
                    <table border="1" cellspacing="0" cellpadding="10">
                        <tr>
                            <th>Points</th>
                            <th>Hourly ({{ $currencyArr[$offer['currency']] }})</th>
                        </tr>
                        @foreach ($offer['Basic_offer_points'] as $basic)
                            <tr>
                                <td>{{ $basic->basic_poins }}</td>
                                <td> {{( $basic->basic_yearly > 0) ?  $basic->basic_yearly : '---' }}</td>
                            </tr>
                        @endforeach
                        <tr class="last_tr_all_table">
                            <td>Total Gross Amount (A)</td>
                            <td> {{ ($offer['yearly_gross']> 0) ? $offer['yearly_gross'] : '---' }}</td>
                        </tr>

                    </table>

                    {{-- Bonus Offer Points --}}
                    <h4>Bonus Components</h4>
                    <table border="1" cellspacing="0" cellpadding="10">
                        <tr>
                            <th>Points</th>
                            <th>Hourly ({{ $currencyArr[$offer['currency']] }})</th>
                        </tr>
                        @foreach ($offer['Bonus_offer_points'] as $bonus)
                            <tr>
                                <td>{{ $bonus->bonus_poins }}</td>
                                <td> {{ ($bonus->bonus_yearly > 0) ? $bonus->bonus_yearly : '---' }}</td>
                            </tr>
                        @endforeach
                        <tr class="last_tr_all_table">
                            <td>Bonus (B)</td>
                            <td> {{ ($offer['yearly_bonus_gross'] > 0) ? $offer['yearly_bonus_gross'] : '---' }}</td>
                        </tr>
                    </table>

                    {{-- Insurance Offer Points --}}
                    <h4>Insurance Components</h4>
                    <table border="1" cellspacing="0" cellpadding="10">
                        <tr>
                            <th>Points</th>
                            <th>Hourly ({{ $currencyArr[$offer['currency']] }})</th>
                        </tr>
                        @foreach ($offer['Insurance_offer_points'] as $insurance)
                            <tr>
                                <td>{{ $insurance->insurence_poins }}</td>
                                <td> {{( $insurance->insurence_yearly >0) ?  $insurance->insurence_yearly  : '---'}}</td>
                            </tr>
                        @endforeach
                        <tr class="last_tr_all_table">
                            <td>Insurance (C)</td>
                            <td> {{ ($offer['yearly_insurence_gross'] > 0) ? $offer['yearly_insurence_gross'] : '---' }}</td>
                        </tr>
                    </table>

                @else
                    {{-- Basic Offer Points --}}
                    <h4>Basic Components</h4>
                    <table border="1" cellspacing="0" cellpadding="10">
                        <tr>
                            <th>Points</th>
                            <th>Monthly ({{ $currencyArr[$offer['currency']] }})</th>
                            <th>Yearly ({{ $currencyArr[$offer['currency']] }})</th>
                        </tr>
                        @foreach ($offer['Basic_offer_points'] as $basic)
                            <tr>
                                <td>{{ $basic->basic_poins }}</td>
                                <td> {{ ($basic->basic_monthly >0) ? $basic->basic_monthly : '---' }}</td>
                                <td> {{ ($basic->basic_yearly > 0) ? $basic->basic_yearly : '---' }}</td>
                            </tr>
                        @endforeach
                        <tr class="last_tr_all_table">
                            <td>Total Gross Amount (A)</td>
                            <td> {{ ($offer['monthly_gross'] > 0) ? $offer['monthly_gross'] : '---' }}</td>
                            <td> {{ ($offer['yearly_gross'] > 0) ? $offer['yearly_gross'] : '---' }}</td>
                        </tr>

                    </table>

                    {{-- Bonus Offer Points --}}
                    <h4>Bonus Components</h4>
                    <table border="1" cellspacing="0" cellpadding="10">
                        <tr>
                            <th>Points</th>
                            <th>Monthly ({{ $currencyArr[$offer['currency']] }})</th>
                            <th>Yearly ({{ $currencyArr[$offer['currency']] }})</th>
                        </tr>
                        @foreach ($offer['Bonus_offer_points'] as $bonus)
                            <tr>
                                <td>{{ $bonus->bonus_poins }}</td>
                                <td> {{ ($bonus->bonus_monthly > 0) ? $bonus->bonus_monthly : '---' }}</td>
                                <td> {{ ($bonus->bonus_yearly > 0) ? $bonus->bonus_yearly : '---' }}</td>
                            </tr>
                        @endforeach
                        <tr class="last_tr_all_table">
                            <td>Bonus (B)</td>
                            <td> {{ ($offer['monthly_bounus_gross'] > 0) ? $offer['monthly_bounus_gross'] : '---' }}</td>
                            <td> {{ ($offer['yearly_bonus_gross'] > 0) ? $offer['yearly_bonus_gross'] : '---' }}</td>
                        </tr>
                    </table>

                    {{-- Insurance Offer Points --}}
                    <h4>Insurance Components</h4>
                    <table border="1" cellspacing="0" cellpadding="10">
                        <tr>
                            <th>Points</th>
                            <th>Monthly ({{ $currencyArr[$offer['currency']] }})</th>
                            <th>Yearly ({{ $currencyArr[$offer['currency']] }})</th>
                        </tr>
                        @foreach ($offer['Insurance_offer_points'] as $insurance)
                            <tr>
                                <td>{{ $insurance->insurence_poins }}</td>
                                <td> {{ ($insurance->insurence_monthly > 0) ? $insurance->insurence_monthly : '---' }}</td>
                                <td> {{ ($insurance->insurence_yearly > 0) ? $insurance->insurence_yearly : '---' }}</td>
                            </tr>
                        @endforeach
                        <tr class="last_tr_all_table">
                            <td>Insurance (C)</td>
                            <td> {{ ($offer['monthly_insurence_gross'] > 0 ) ? $offer['monthly_insurence_gross'] : '---' }}</td>
                            <td> {{ ($offer['yearly_insurence_gross'] > 0) ? $offer['yearly_insurence_gross'] : '---' }}</td>
                        </tr>
                    </table>
                @endif

            @endforeach



            <h4>Total Gross</h4>
            
            <table border="1" cellspacing="0" cellpadding="10">
                @if($offer['salary_mode']==2)
                    <tr>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th>Hourly ({{ $currencyArr[$offer['currency']] }})</th>
                    </tr>
                    <tr class="last_tr_all_table">
                        <td>Final CTC (A+B+C)</td>
                        <td> {{ ($offer['yearly_final_gross'] > 0) ? $offer['yearly_final_gross'] : '---' }}</td>
                    </tr>
                @else
                    <tr>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th>Monthly ({{ $currencyArr[$offer['currency']] }})</th>
                        <th>Yearly ({{ $currencyArr[$offer['currency']] }})</th>
                    </tr>
                    <tr class="last_tr_all_table">
                        <td>Final CTC (A+B+C)</td>
                        <td> {{ ($offer['monthly_final_gross'] > 0) ? $offer['monthly_final_gross'] : '---' }}</td>
                        <td> {{ ($offer['yearly_final_gross'] > 0) ? $offer['yearly_final_gross'] : '---' }}</td>
                    </tr>
                @endif

            </table>


        </div>
    @else
        <p>No offer details available.</p>
    @endif



  

</body>

</html>
