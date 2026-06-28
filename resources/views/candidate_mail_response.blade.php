<!DOCTYPE html>
<html>
<head>
    <title>Candidate Interest Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 40px;">

    @if($mtypeSection == 1)
        <!-- Interested -->
        <div style="max-width: 600px; margin: auto; padding: 20px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 5px;">
            <h2 style="margin-top: 0;">🎉 Thank you!</h2>
            <p>We have received your interest. Our team will get in touch with you shortly regarding the next steps in the recruitment process.</p>
        </div>

    @elseif($mtypeSection == 2)
        <!-- Not Interested -->
        <div style="max-width: 600px; margin: auto; padding: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px;">
            <h2 style="margin-top: 0;">❌ Not Interested</h2>
            <p>We respect your decision. Thank you for considering the opportunity. We’ll keep you in mind for future openings that may be a better fit.</p>
        </div>

    @elseif($mtypeSection == 3)
        <!-- Already Responded -->
        <div style="max-width: 600px; margin: auto; padding: 20px; background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; border-radius: 5px;">
            <h2 style="margin-top: 0;">📝 Already Responded</h2>
            <p>Our records show that you've already submitted your response to this opportunity. If you wish to make changes, please reach out to us directly.</p>
        </div>

    @endif

</body>
</html>
