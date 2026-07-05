@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter</title>
    <style>
        body {
            /* Poppins with a Helvetica/Arial fallback — no Poppins font file is bundled/embedded for
               dompdf yet, so it currently renders via the fallback; kept named so it picks up Poppins
               automatically if a font file is added later. Matches the Step 3 preview's 12px base. */
            font-family: 'Poppins', Helvetica, Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .offer-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .offer-details th,
        .offer-details td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .offer-details th {
            background-color: #f2f2f2;
        }

        .last_tr_all_table {
            opacity: 1;
            font-weight: bold;
        }

        @page {
            margin: 120px 50px 210px 50px; /* top, right, bottom, left margins */
        }

        /* Header styling */
        header {
        position: fixed;
        top: -130px; /* Adjust this to control the distance from the top of the page */
        left: 0;
        right: 0;
        height: 50px;
        display: flex;
        justify-content: center; /* Centers the offer letter text horizontally */
        align-items: center; /* Vertically aligns items */
        }
        .logo-container {
            position: absolute;
            top: 0;
            left: 0;
            margin: 10px; /* Adjust as needed for spacing */
        }

        /* Constrain by HEIGHT (not width) so any logo — tall or wide — lands at the same
           ~50px height and lines up with the address block on the right. max-width guards
           against an unusually wide logo overrunning the address. */
        .logo-container img {
            height: 50px;
            width: auto;
            max-width: 160px;
        }

        /* Matches the Step 3 preview's letterhead: logo top-left, company name/address top-right */
        .address-container {
            position: absolute;
            top: 0;
            right: 0;
            margin: 10px;
            text-align: right;
            font-size: 11px;
            color: #555;
            max-width: 260px;
        }

        .address-container .address-company {
            font-size: 12px;
            font-weight: bold;
            color: #181c32;
        }

        .title-container {
            text-align: center;
            margin: -50px;
        }


        /* Footer styling */
        footer {
            position: fixed;
            bottom: -150px; /* Adjust this to control the distance from the bottom of the page */
            left: 0;
            right: 0;
            height: 50px;
            border-top: 1px solid;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
       
        .page-number:before {
            content: "Page " counter(page);
        }

        .signature-container {
            position: fixed;
            bottom: 150px;
            left: 75%;
            transform: translateX(-50%);
            text-align: center;
        }

        .signature-container img {
            width: 280px;
            height: auto;
        }

        /* Signature section styling */
        .signature-section {
            position: fixed;
            bottom: 20px; /* Adjust this to position just before the footer */
            left: 0;
            right: 0;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 0 50px;
        }
        
        .signature-block{
            position: absolute;
            top: 50px;
            right: 0px;
        }
        .date-block {
            position: absolute;
            left: 0;
            top: 0;
        }
        .marginTop{
            top: 50px;
        }

        .signature-block, .date-block {
            width: 45%;
            text-align: center;
        }

        /* Wrapper spans the full signature column and is anchored just above the line;
           text-align:center centers the signature regardless of its width. Keeping the
           wrapper (not the image) absolutely positioned means the line/label never shift. */
        .signature-image-wrap {
            position: absolute;
            top: -58px;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
        }
        /* Constrain by BOTH dimensions (like the logo) so any signature — a tall scan or a
           wide squiggle — fits the slot above the line instead of overflowing. */
        .signature-image {
            max-width: 180px;
            max-height: 55px;
            width: auto;
            height: auto;
            display: inline;
        }

        .signature-line {
            border: 0;
            border-top: 0.5px solid #000;
            margin: 10px 0;
        }

        
        li p {
            margin: 0 0 0px 0 !important; /* spacing between list items */
            text-align: justify;
        }
        body {
        text-align: justify;
        }
        
    </style>
</head>

@php
$logoBase64 = null;
$filename = $exits_offer->signature_candidate;
$filenameauthor = "6710fbce1cc15.png";
$path = str_replace('index.php', '', url('storage/app/public/signatures/' . $filename . ''));
$signaturePath = !empty($exits_offer->signature_candidate)
    ? storage_path('app/public/signatures/' . $exits_offer->signature_candidate)
    : null;

// Embed the candidate signature as a base64 data URI — same proven approach the logo
// above uses. This avoids dompdf's fragile file:// path handling (which silently fails
// on some hosts / path formats) and works identically everywhere the file is readable.
$signatureBase64 = null;
if (!empty($signaturePath) && is_file($signaturePath)) {
    $sigData = @file_get_contents($signaturePath);
    if ($sigData !== false) {
        $sigFinfo = @finfo_open(FILEINFO_MIME_TYPE);
        $sigMime  = $sigFinfo ? @finfo_buffer($sigFinfo, $sigData) : 'image/png';
        if ($sigFinfo) { finfo_close($sigFinfo); }
        $signatureBase64 = 'data:' . ($sigMime ?: 'image/png') . ';base64,' . base64_encode($sigData);
    }
}
$pathauthor = str_replace('index.php', '', url('storage/app/public/signatures/' . $filenameauthor . ''));
$joined_date = date("d M Y", strtotime($exits_offer->joined_date));

$offer_terms = "";

$rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);
		
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
$offer_terms = preg_replace('/<ul[^>]*>/i', '<ul>', $offer_terms);
$offer_terms = preg_replace('/<ol[^>]*>/i', '<ol>', $offer_terms);
$offer_terms = preg_replace('/<li[^>]*>/i', '<li>', $offer_terms);


$logoUrl = $logo;
$logoBase64 = null;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $logoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // IMPORTANT for AWS
$imageData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($imageData && $httpCode === 200) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_buffer($finfo, $imageData);
    finfo_close($finfo);

    $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
}




@endphp

<body>
    <!-- Header -->
    <header>
         <div class="logo-container">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="{{ $comapny_name }}" style="height:50px; width:auto; max-width:160px;">
            @else
                <h3>{{ $comapny_name }}</h3>
            @endif

        </div>
        <div class="address-container">
            @if(!empty($comapny_name))<div class="address-company">{{ $comapny_name }}</div>@endif
            @if(!empty($address))<div>{{ $address }}</div>@endif
        </div>
    </header>
    

    <div class="signature-section">
        <div class="signature-block">
            @if(!empty($signatureBase64))
            <div class="signature-image-wrap">
                <img src="{{ $signatureBase64 }}" class="signature-image">
            </div>
            @endif
            <hr class="signature-line">
            <h4>Candidate Signature</h4>
        </div>
        
        <div class="date-block {{ ($exits_offer->accept_date_time) ? '' : 'marginTop'}}">
           
                @if(!empty($exits_offer->accept_date_time))
                 <h4>
                {{ date('d M Y', strtotime($exits_offer->accept_date_time)) }}
                 </h4>
                @endif
           
            <hr class="signature-line">
            <h4>Date</h4>
        </div>
    </div>


    <!-- Footer: company name & address removed entirely (they live in the top letterhead).
         Neutral copyright only, so the footer isn't left empty/broken. -->
    <footer>
        <div class="">
            <p>&copy; {{ date("Y") }} - All Rights Reserved</p>
        </div>
    </footer>

    {{-- <div class="signature-container">
        <img src="{{ $path }}" alt="Signature">
        <h4 style="">Candidate Signature</h4>
    </div> --}}
    <div class="title-container">
        <h2>Offer Letter</h2>
    </div>
    @if($offer_terms_conditions)
    {!! $offer_terms !!}


    <p><strong>Candidate Acceptance:</strong></p>
        <p>I, <strong>{{ $candidate_name }}</strong>, accept the offer to join <strong>{{ $comapny_name }}</strong> as a
    <strong>{{ $job_title }}</strong> under the terms outlined in this letter.</p>
    

    <div style="page-break-after: always;"></div>
    @endif
     @php

       $currencyArr = ['INR', 'EUR', 'JPY', 'GBP', 'USD', 'AED'];

    @endphp
    <center>
        <h2>Salary Annexure</h2>
    </center>

    
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

</body>

</html>
