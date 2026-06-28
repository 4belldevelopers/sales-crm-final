<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhonePe Payment</title>
</head>
<body>
    <h1>Pay with PhonePe</h1>
    
    <form action="{{ route('phonepe.payment') }}" method="POST">
        @csrf
        <label for="amount">Enter Amount (INR):</label>
        <input type="number" id="amount" name="amount" required>
        <button type="submit">Pay Now</button>
    </form>
</body>
</html>
