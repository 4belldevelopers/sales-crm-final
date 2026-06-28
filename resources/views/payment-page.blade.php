<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment Page</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>

@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    $currency = GeneralMethod::convertINRToUSD();
    $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();

@endphp

<body>
    <form action="{{ route('payment.callback') }}" method="POST">
        @csrf
        <script
            src="https://checkout.razorpay.com/v1/checkout.js"
            data-key="{{$payment_settings->razorpay_key}}"
            data-amount="{{ $amount }}"  // Amount in paisa (₹5000)
            data-currency="{{ $currency }}"
            data-order_id="{{ $orderId }}"
            data-buttontext="Pay Now"
            data-name="JERO"
            data-description="Payment Description"
            data-theme.color="#F37254">
        </script>
    </form>
</body>
</html>
