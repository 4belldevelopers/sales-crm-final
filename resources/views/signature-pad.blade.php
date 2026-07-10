@php
    use App\Helper\GeneralMethod;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <title>Offer Letter</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
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

        /* Modal Styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
            justify-content: center;
            /* Center modal horizontally */
            align-items: center;
            /* Center modal vertically */
        }

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            /* Could be more or less, depending on screen size */
            max-width: 600px;
            /* Set max-width for the modal */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        #comment_box {
            font-family: Calibri, sans-serif; /* Text inside textarea */
            font-size: 16px;
            color: #333;    
            border: 2px dashed #cfd4dc;
            border-radius: 12px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;

            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;

            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.05);

        }
        #signature-title {
            font-family: Calibri, sans-serif; /* Text inside textarea */
        }

        /* Placeholder styling */
        #comment_box::placeholder {
            font-family: Calibri, sans-serif;
            font-size: 16px;
            color: #999;
        }


       .signature-wrapper {
    max-width: 340px;
    margin: 20px auto;
    text-align: center;
}

.signature-label {
    font-weight: 600;
    margin-bottom: 10px;
    display: block;
}

.signature-box {
    height: 140px;
    border: 2px dashed #cfd4dc;
    border-radius: 8px;
    background: #fafafa;

    display: flex;
    align-items: center;
    justify-content: center;

    position: relative;
    cursor: pointer;
    transition: all 0.25s ease;
}
.signature-box-up {
    width: 320px;              /* 👈 set width */
    height: 95px;
    border: 2px #cfd4dc;
    border-radius: 12px;
    background: #ffffff;

    display: flex;
    align-items: center;       /* vertical center */
    justify-content: center;   /* horizontal center */

    position: relative;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.05);
    margin: 10px auto;
}

/* Hover / Active */
.signature-box-up:hover {
    background: #f1f5ff;
    border-color: #2e59d9;
    box-shadow: 0px 5px 14px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

/* Circle Glow Effect */
.signature-box-up:before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 12px;
    border: 4px solid rgba(78, 115, 223, 0.15);
    top: -6px;
    left: -6px;
    z-index: -1;
    opacity: 0;
    transition: all 0.3s ease;
}

.signature-box-up:hover:before {
    opacity: 1;
}


.signature-box:hover {
    background: #f1f5ff;
    border-color: #4e73df;
}

.signature-placeholder {
    color: #6c757d;
}

.signature-icon {
    font-size: 28px;
    margin-bottom: 6px;
}

.signature-text {
    font-size: 14px;
    font-weight: 500;
}

.signature-hint {
    font-size: 12px;
    color: #9aa0a6;
}

#signature-preview {
    max-height: 100px;
    max-width: 90%;
    display: none;

    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.signature-box.signed {
    border-style: solid;
    border-color: #28a745;
    background: #f6fffa;
}

.signature-preview-box.signed {
    border-style: solid;
    position: relative;
    border-color: #28a745;
    background: #f6fffa;
}

#removeSignature {
    font-size: 13px;
}
#removeSignatureDiv{
    position: absolute;
    top: 2px;
    right: 2px;
}




    </style>
</head>

<body>

    <!-- Modal for Signature Pad -->
    <div id="signatureModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span id="close-modal" style="float:right; cursor:pointer;font-size: 26px;">&times;</span>
            <h2 class="signature-title">E-Signature</h2>

            <div class="signature-wrapper" id="signature-upload">
                <!-- Preview box -->
                <div class="signature-box signature-preview-box">
                    <span class="signature-placeholder"></span>
                    <img id="signature-preview" />

                    <div id="removeSignatureDiv">
                        <!-- Remove button -->
                        <button type="button" id="removeSignature" class="btn btn-sm btn-outline-danger mt-2" style="display:none;border: navajowhite; background: transparent; font-size: 17px;cursor: pointer;"
                        >❌ </button>
                    </div>

                </div>

                <!-- Upload box -->
                <div class="signature-box signature-box-up" id="signatureBox">
                    <div class="signature-placeholder">
                        <div class="signature-icon">✍️</div>
                        <div class="signature-text">Click to upload signature</div>
                        <small class="signature-hint">PNG or JPG (Max 2MB)</small>
                    </div>
                </div>

                <input 
                    type="file"
                    id="signature-file"
                    accept="image/png, image/jpeg"
                    hidden
                >

            </div>

                    



            <textarea id="comment_box" rows="8" cols="82" placeholder="Add a comment..."></textarea>
            <div class="signature-actions">
                <button id="clear-signature" class="signature-btn clear-btn">
                    Cancel <i class="fas fa-times"></i> 
                </button>
                <button id="save-signature" class="signature-btn save-btn">
                    Submit <i class="fas fa-save"></i> 
                </button>
                <button id="save-signature_reject" class="signature-btn save-btn">
                    Submit <i class="fas fa-save"></i> 
                </button>
            </div>

            <!-- Hidden form to submit signature data -->
            <form id="signature-form" method="POST" action="{{ route('save.signature') }}">
                @csrf
                <input type="hidden" name="signature" id="signature-input">
                <input type="hidden" value="{{ $offerId }}" name="offerid" id="offerid">
                <input type="hidden" value="{{  $exits_offer->signature_type }}" name="signature_type" id="signature_type">
                <input type="hidden" value="{{ encrypt(2) }}" name="status_value" id="status_value">
                <input type="hidden" value="{{ $mtypeSection }}" name="mtypeSection" id="mtypeSection">
                <input type="hidden" value="" name="comment_box" class="comment_box">
                <input type="hidden" value="{{ $emailSet }}" name="emailSet" id="emailSet">
                 <input type="hidden" value="{{ $from_email }}" name="from_email" id="from_email">
            </form>

            <form id="signature-form-reject" method="POST" action="{{ route('save.signature') }}">
                @csrf
                <input type="hidden" name="signature" id="signature-input">
                <input type="hidden" value="{{ $offerId }}" name="offerid" id="offerid">
                <input type="hidden" value="{{ encrypt(3) }}" name="status_value" id="status_value">
                <input type="hidden" value="{{ $mtypeSection }}" name="mtypeSection" id="mtypeSection">
                <input type="hidden" value="" name="comment_box" class="comment_box">
                <input type="hidden" value="{{ $emailSet }}" name="emailSet" id="emailSet">
                 <input type="hidden" value="{{ $from_email }}" name="from_email" id="from_email">
            </form>


        </div>
    </div>
    <!-- Modal for Signature Pad -->
    @php
        $filename = $exits_offer->signature_candidate;
        $filenameauthor = '6710fbce1cc15.png';
        //  dd($filename);

        // Option 1: Save PDF to storage and attach file path
        // $pdfPath = storage_path('app/public/'.$filename.'');
        // file_put_contents($pdfPath, $pdfContent);
        $path = str_replace('index.php', '', url('storage/app/public/signatures/' . $filename . ''));
        $pathauthor = str_replace('index.php', '', url('storage/app/public/signatures/' . $filenameauthor . ''));


        $candidate_status = $exits_offer->candidate_status;
        $management_status = $exits_offer->management_status;
        $send_approved_date_time = '';
        if(!empty($exits_offer->send_approved_date_time)){
            $send_approved_date_time = date("d M Y", strtotime($exits_offer->send_approved_date_time));
        }
        

        $offerdetails = 1;

        $offer_terms = "";
        $joined_date = '';
        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);
        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);
        if(!empty($rowDataOfferDetails->of_joined_date)){
        
        $joined_date = date("d M Y", strtotime($rowDataOfferDetails->of_joined_date));
        }
                
                $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
                $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
                $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
                $job_title = (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->job_title : '';
                $candidate_name =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->full_name : '';
                $candidate_email =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->email : '';
                $candidate_mobile =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->mobile : '';
                $send_accept_date_time =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->send_accept_date_time : '';


        if($offer_terms_conditions){
            $offer_terms = str_replace(["[CANDIDATE_NAME]","[CANDIDATE_EMAIL]","[CANDIDATE_MOBILE]","[COMPANY_NAME]","[JOB_TITLE]","[JOINING_DATE]","[{JOB_TITLE}]","[TODAY_DATE]"],[$candidate_name, $candidate_email,$candidate_mobile,$comapny_name,$job_title,$joined_date,$job_title,$send_accept_date_time], $offer_terms_conditions->terms_condition);
        }
        


    @endphp

    <div class="container">
        <!-- Header Section -->
        <div class="header" style="position: relative;height: 47px;">
                    @if(!empty($logo))
                      <img src="{{ $logo }}" alt="Company Logo" style="max-width: 54px; margin-bottom: 10px; float: right; position: absolute; right: 18px; top: -12px;">
                     @else
                        <h3> {{$comapny_name}}</h3>
                    @endif

                @if($mtypeSection==1)
                    @if($candidate_status==1)
                    <div class="cta-buttons">
                        <a href="javascript:;" class="accept-btn" id="accept-offer">Accept Offer</a>
                        <a href="javascript:;" class="reject-btn" id="reject-offer">Reject Offer</a>
                    </div>
                    @endif
                @elseif($mtypeSection==2)
                    @if($management_status==1)
                    <div class="cta-buttons">
                        <a href="javascript:;" class="accept-btn" id="accept-offer">Approve Offer</a>
                        <a href="javascript:;" class="reject-btn" id="reject-offer">Reject Offer</a>
                    </div>
                    @endif
                @endif
        </div>
        @if($mtypeSection==2)
        <div class="page-expired" style="text-align: center; padding: 50px;">
                     
            @if($management_status ==2)

            <h2 style="color: green;">Offer Approved</h2>
            <p>You have successfully accepted the offer.</p>
            {{-- @if(session('success')) --}}
            {{-- <button type="button" data-jobId="{{ @$job_id }}"
                data-candidate="{{ @$application_id }}" data-mtype="{{ $mtypeSection }}"
                data-offerId="{{ $offerId }}"
                class="btn btn-success font-weight-bolder px-9 py-4 download_offer"
                data-wizard-type="action-next" style="width: 17%; padding: 6px; border-radius: 7px;">Download <i class="fa fa-download"></i></button> --}}
            {{-- @endif --}}
            @elseif($management_status==3)

            <h2 style="color: orange;">Offer Not approved</h2>
            <p>You have successfully rejected the offer. Please go back to the previous page or close this window.</p>
            
            <button onclick="window.location.reload();">Refresh Page</button>
            @else            
            @php 
              $offerdetails = '';
            @endphp
            @endif

        </div>
        @elseif($mtypeSection==1)
        <div class="page-expired" style="text-align: center; padding: 50px;">
                      
            @if($candidate_status ==2)

            <h2 style="color: green;">Offer Accepted</h2>
            <p>You have successfully accepted the offer. Please proceed with the next steps or go back to the previous page.</p>
            {{-- @if(session('success')) --}}
            <button type="button"
                onclick="window.open('{{ route('offer.letter.public.stream', $offerId) }}','_blank')"
                class="btn btn-success font-weight-bolder px-9 py-4"
                style="width: 17%; padding: 6px; border-radius: 7px;">Download <i class="fa fa-download"></i></button>
            {{-- @endif --}}
            @elseif($candidate_status==3)

            <h2 style="color: orange;">Offer Rejected</h2>
            <p>You have successfully rejected the offer. Please go back to the previous page or close this window.</p>        
            <button onclick="window.location.reload();">Refresh Page</button>
            @elseif($candidate_status==4)

            <h2 style="color: red;">Offer Expired</h2>
            <p>The page you are trying to access has expired. Please refresh the page or go back to the previous page.</p>
            <button onclick="window.location.reload();">Refresh Page</button>
            @elseif($candidate_status==1)
            @php 
              $offerdetails = '';
            @endphp

            @endif
        </div>
        @endif

        @if($offerdetails==1)
           <!-- Page Expired Message -->
         

       @else

    
        <!-- Content Section -->
        <div class="content">


            <div class="header_second">

                <div class="">
                    <h2>OFFER LETTER</h2>
                </div>
            </div>




            <!-- Terms and Conditions Section -->
            <div class="terms">

                @if ($offer_terms_conditions)
                    {!! $offer_terms !!}
                    <div style="page-break-after: always;"></div>
                @endif
            </div>

            <!-- Salary Structure Section -->
            <div class="salary-structure">
                <center><h3>Salary Annexure</h3></center>
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
            </div>

            <!-- Signature Section -->
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
        </div>

        <!-- Footer Section -->
        <div class="company-footer">
            <p><strong> {{ $comapny_name }}</strong></p>
            <p>{{ $address }}</p>
            <p>&copy; {{ date("Y") }} {{ $comapny_name }} - All Rights Reserved</p>
        </div>
       @endif



    </div>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

   <script>
const fileInput = document.getElementById('signature-file');
const uploadBox = document.getElementById('signatureBox');
const previewImg = document.getElementById('signature-preview');
const previewBox = document.querySelector('.signature-preview-box');
const previewPlaceholder = previewBox.querySelector('.signature-placeholder');
const removeBtn = document.getElementById('removeSignature');

uploadBox.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    if (!['image/png', 'image/jpeg'].includes(file.type)) {
        alert('Only PNG or JPG allowed');
        this.value = '';
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('File size must be under 2MB');
        this.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';

        previewPlaceholder.style.display = 'none';
        previewBox.classList.add('signed');
        removeBtn.style.display = 'inline-block';

        // clear + fill signature hidden inputs
        document.querySelectorAll('input[name="signature"]').forEach(el => {
            el.value = e.target.result;
        });
    };

    reader.readAsDataURL(file);
});

// REMOVE SIGNATURE
removeBtn.addEventListener('click', function () {
    previewImg.src = '';
    previewImg.style.display = 'none';

    previewPlaceholder.style.display = 'block';
    previewBox.classList.remove('signed');

    fileInput.value = '';
    removeBtn.style.display = 'none';

    // clear hidden signature values
    document.querySelectorAll('input[name="signature"]').forEach(el => {
        el.value = '';
    });
});
</script>





    <script>

            function validateEmailInput() {
                let comment_box = this.value;
                
               $('.comment_box').val(comment_box);
            }
            var signaturePadreject = document.getElementById('save-signature_reject');
                signaturePadreject.style.display = 'none';

        document.addEventListener("DOMContentLoaded", function() {

            document.getElementById('comment_box').addEventListener('input', validateEmailInput);
            document.getElementById('comment_box').addEventListener('change', validateEmailInput);



            var canvas = document.getElementById('signature-upload');
            // var signaturePad = new SignaturePad(canvas);

            // Get the modal
            var modal = document.getElementById("signatureModal");

            // Open modal when the accept offer button is clicked
            document.getElementById('accept-offer').addEventListener('click', function() {
                var signaturePad = document.getElementById('signature-upload');
                var signaturePadreject = document.getElementById('save-signature_reject');
                signaturePadreject.style.display = 'none';

                var signaturePadapro = document.getElementById('save-signature');
                signaturePadapro.style.display = 'inline';

               
                var signatureType = document.getElementById('signature_type').value;
                var mtypeSection = document.getElementById('mtypeSection').value;
                if(signatureType==1 && mtypeSection==1){
                    document.querySelector('.signature-title').textContent = 'E-Signature';
                    signaturePad.style.display = 'inline'; 
                }else{
                    document.querySelector('.signature-title').textContent = 'Comments';
                    signaturePad.style.display = 'none';
                }
                modal.style.display = "flex"; // Use flexbox to center the modal
                signaturePad.clear(); // Clear the signature pad when opening
            });

            document.getElementById('reject-offer').addEventListener('click', function() {
                document.querySelector('.signature-title').textContent = 'Reject Comments';
                var signaturePad = document.getElementById('signature-upload');
                signaturePad.style.display = 'none';
                var signaturePadreject = document.getElementById('save-signature_reject');
                signaturePadreject.style.display = 'inline';

                var signaturePadapro = document.getElementById('save-signature');
                signaturePadapro.style.display = 'none';


                var signatureType = document.getElementById('signature_type').value;
                var signatureType = document.getElementById('signature_type').value;
                var mtypeSection = document.getElementById('mtypeSection').value;
                //  document.getElementById('signature-form-reject').submit();
                 modal.style.display = "flex"; // Use flexbox to center the modal
                 signaturePad.clear(); // Clear the signature pad when opening

            });


           

            // Close modal when the close button is clicked
            document.getElementById('close-modal').addEventListener('click', function() {
                modal.style.display = "none";
            });

            // Clear button functionality
            document.getElementById('clear-signature').addEventListener('click', function() {
                 modal.style.display = "none";
            });

            // Save button functionality
            document.getElementById('save-signature').addEventListener('click', function() {
                
                var signatureType = document.getElementById('signature_type').value;
                var mtypeSection = document.getElementById('mtypeSection').value;
                var signaturePad  = document.getElementById('signature-input').value;
                if(signatureType==1 && mtypeSection==1){
                    if (signaturePad && signaturePad.trim() !== '') {
                        // var signatureData = signaturePad.toDataURL('image/png');
                        // document.getElementById('signature-input').value = signatureData;
                        document.getElementById('signature-form').submit();
                    } else {
                        alert("Please provide a signature.");
                    }
                }else{
                    document.getElementById('signature-form').submit();  
                }
                    
            });

            document.getElementById('save-signature_reject').addEventListener('click', function() {
                 document.getElementById('signature-form-reject').submit();
            });

            
             


            // Close modal when clicking outside of it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            };
        });


    </script>

</body>

</html>
