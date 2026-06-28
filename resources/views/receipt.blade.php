<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            border-right: 1px solid #ccc;
        }
        th:last-child, td:last-child {
            border-right: none; /* Remove right border for the last column */
        }
        thead th {
            background-color: #f4f4f4;
            border-bottom: 2px solid #ccc;
        }
        tbody tr:last-child td {
            border-bottom: none; /* Remove bottom border from the last row */
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .receipt-container {
            background-color: white;
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 24px;
            text-align: center;
            color: #333;
        }
        .table-header, .table-content {
            width: 100%;
            border: 1px solid #ccc;
        }

        .watermark {
            position: fixed;
            top: 20%;
            left: 50%;
            font-size: 100px;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: 99999999;
            text-align: center;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Conditional Watermark -->
    <div class="watermark" style="{{@$status_color}}">
       {{@$status_text}}
    </div>

    <!-- Main container with thin border -->
    <div class="receipt-container">
        <table class="table-header">
            <tr>
                <td colspan="2" style="text-align: center;">
                    <h1>JERO</h1>
                </td>
            </tr>
            <tr>
                <td style="font-size: 14px; color: #666;">
                    @php
                    $number = $reciepts->id;
                    $reciepts_no = sprintf('%05d', $number);
                    @endphp
                    <p><strong>Invoice No: #{{ $reciepts->invoiceId }}</strong></p>
                </td>
                <td style="font-size: 14px; color: #666; text-align: right;">
                    <p><strong>Date:</strong> {{ date("d M Y h:i:s", strtotime($reciepts->invoice_date)) }}</p>
                </td>
            </tr>
            <tr>
                <td style="font-size: 14px; color: #666;width: 50%;">
                    <p><strong>Sold By:</strong></p>
                    {{-- <p>{{ ($reciepts->sold_first_name) ? $reciepts->sold_first_name.' '.$reciepts->sold_last_name : '' }},</p> --}}
                    <p>{{ ($reciepts->sold_organization) ? $reciepts->sold_organization : $company_name }},</p>
                    <p>{{ ($reciepts->sold_street) ? $reciepts->sold_street : $locArrLocation }},<br> {{ $reciepts->sold_city }}, {{ $reciepts->sold_state }}, {{ $reciepts->sold_country_name }}</p>
                    @if($reciepts->sold_country_id==101 ||$reciepts->country_id=='')
                    <p>{{ ($reciepts->sold_gstin) ? 'GSTIN: '.$reciepts->sold_gstin : '' }},</p>
                    @else
                    <p>{{ ($reciepts->sold_gstin) ? 'TATID: '.$reciepts->sold_gstin : '' }},</p>
                    @endif
                    <p>{{ ($reciepts->sold_pan_no) ? 'PAN: '.$reciepts->sold_pan_no : '' }}</p>
                </td>
                <td style="font-size: 14px; color: #666;">
                    <p><strong>Billing Address:</strong></p>
                    @if(@$reciepts->first_name)
                    <p>{{ ($reciepts->first_name) ? $reciepts->first_name.' '.$reciepts->last_name : $user_name }},</p>
                    @endif
                    <p>{{ ($reciepts->organization) ? $reciepts->organization : $company_name }},</p>
                    <p>{{ ($reciepts->street) ? $reciepts->street : $locArrLocation }},
                    <br> {{ $reciepts->city }}, {{ $reciepts->state }}, {{ $reciepts->country_name }}</p>
                    @if($reciepts->country_id==101 ||$reciepts->country_id=='')
                    <p>{{ ($reciepts->gstin) ? 'GSTIN: '.$reciepts->gstin : '' }},</p>
                    @else
                    <p>{{ ($reciepts->gstin) ? 'TATID: '.$reciepts->gstin : '' }},</p>
                    @endif
                    <p>{{ ($reciepts->pan_no) ? 'PAN: '.$reciepts->pan_no : '' }}</p>
                </td>
            </tr>
        </table>

        <table class="table-content" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Duration</th>
                    <th>Coins</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $reciepts->product_name }}
                    </td>
                    <td>{{ $reciepts->duration }} {{ ($reciepts->subscription_type == 'yes') ? 'Days' : '' }}</td>
                    <td>{{ $reciepts->credit }}</td>
                    <td>
                        {{ ($reciepts->currency == 'INR') ? 'Rs.' : '$' }}
                        {{ number_format($reciepts->price,2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table-content" style="margin-top: 20px;">
            <tr>
                <td style="text-align: right; font-size: 16px;">Subtotal:</td>
                <td style="text-align: right; font-size: 16px;">
                    {{ ($reciepts->currency == 'INR') ? 'Rs.' : '$' }}
                    {{ number_format($reciepts->price,2) }}
                </td>
            </tr>
            @if($reciepts->country_id==101 && $reciepts->currency=='INR')

            @if($sold_by_fixed->state_id == $reciepts->state_id)
            <tr>

                <td style="text-align: right; font-size: 16px;">SGST {{$reciepts->gst/2}}%:</td>
                <td style="text-align: right; font-size: 16px;">{{ number_format($reciepts->gst_amount/2,2) }}</td>

            </tr>
            <tr>

                <td style="text-align: right; font-size: 16px;">CGST {{$reciepts->gst/2}}%:</td>
                <td style="text-align: right; font-size: 16px;">{{ number_format($reciepts->gst_amount/2,2) }}</td>

            </tr>
            @else
            <tr>

                <td style="text-align: right; font-size: 16px;">GST {{$reciepts->gst}}%:</td>
                <td style="text-align: right; font-size: 16px;">{{ number_format($reciepts->gst_amount,2) }}</td>

            </tr>
            @endif
            @else
            <tr>
                <td style="text-align: right; font-size: 16px;">TAX {{$reciepts->gst}}%:</td>
                <td style="text-align: right; font-size: 16px;">{{ number_format($reciepts->gst_amount,2) }}</td>
            </tr>
            @endif
            {{-- @if($reciepts->country_id==101 && $reciepts->currency=='INR')
            <tr>
                <td style="text-align: right; font-size: 16px;">GST Amount:</td>
                <td style="text-align: right; font-size: 16px;">{{ number_format($reciepts->gst_amount,2) }}</td>
            </tr>
            @else
            <tr>
                <td style="text-align: right; font-size: 16px;">TAX Amount:</td>
                <td style="text-align: right; font-size: 16px;">{{ number_format($reciepts->gst_amount,2) }}</td>
            </tr>
            @endif --}}
            <tr>
                <td style="text-align: right; font-size: 18px; font-weight: bold;">Total:</td>
                <td style="text-align: right; font-size: 18px; font-weight: bold;">
                    {{ ($reciepts->currency == 'INR') ? 'Rs.' : '$' }}
                    {{ number_format($reciepts->total_price,2) }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
