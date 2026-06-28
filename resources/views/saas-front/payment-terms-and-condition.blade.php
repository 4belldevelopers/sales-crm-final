<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Terms &amp; Conditions</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'>
    <!-- <link rel="stylesheet" href="./style.css"> -->
    <style>
        body {
            font-family: sans-serif;
        }

        .tab-content {
            display: block;
            padding-top: 20px;
        }

        .nav-tabs>li {
            float: left;
            margin-bottom: -1px;
            font-size: 16px;
        }

        .tab-content .tab-pane p {
            font-size: 15px;
            font-weight: 300;
        }

        .checkbox,
        .radio {
            font-size: 15px;
        }

        .terms__content {
            padding-bottom: 25px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="terms">
            <div class="terms__header">
                <div class="row">
                    <div class="col-xs-12">
                        <h3 class="text-center">Payment Policy</h3>
                    </div>
                </div>
            </div>

            <div class="terms__content">
                <div class="terms__content--tabs">
                    <div class="row">
                        <div class="col-xs-12">
                            <div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#home" aria-controls="home" role="tab" data-toggle="tab"
                                            class="text-uppercase">
                                            Payment Policy
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                   {!! $global_settings->payment_policy !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
</body>

</html>
