<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        /* styles.css */

/* Universal Styles */
body, html {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f5f5f5;
}

/* Container */
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    text-align: center;
}

/* Message Box */
.message-box {
    max-width: 500px;
    padding: 40px;
    background: #fff;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

/* Success Page Styles */
body.success-page {
    background-color: #e3f9e5;
}

.success-page .message-box {
    border: 2px solid #28a745;
}

.success-page h1 {
    color: #28a745;
}

/* Failure Page Styles */
body.failure-page {
    background-color: #f9e3e3;
}

.failure-page .message-box {
    border: 2px solid #dc3545;
}

.failure-page h1 {
    color: #dc3545;
}

/* Common Styles */
h1 {
    font-size: 2.5em;
    margin-bottom: 20px;
}

p {
    font-size: 1.2em;
    margin-bottom: 30px;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 1em;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.btn:hover {
    opacity: 0.8;
}

/* Button Styles */
.success-page .btn {
    background-color: #28a745;
}

.failure-page .btn {
    background-color: #dc3545;
}


    </style>
</head>
<body class="success-page">
    <div class="container">
        <div class="message-box">
            <h1>🎉 Payment Successful!</h1>
            <p>Thank you for your payment. Your transaction has been completed successfully.</p>
            <a href="{{ $paymentLink }}" class="btn">Go to Home</a>
        </div>
    </div>
</body>
</html>
