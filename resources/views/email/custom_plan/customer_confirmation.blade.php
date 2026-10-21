<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data->subject ?? "We've Received Your Custom Plan Enquiry | JERO.works" }}</title>
    <style>
        body { font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f5f8fa; margin: 0; padding: 20px; color: #181C32; }
        .email-wrapper { max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #E4E6EF; }
        .email-header { background: #1B1B29; padding: 24px 32px; text-align: left; border-bottom: 3px solid #50CD89; }
        .email-header h2 { color: #ffffff; margin: 0; font-size: 20px; font-weight: 600; }
        .email-header p { color: #A1A5B7; margin: 6px 0 0 0; font-size: 13px; }
        .email-body { padding: 32px; line-height: 1.6; font-size: 14px; color: #3F4254; }
        .email-body p { margin-top: 0; margin-bottom: 16px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; background: #E8FFF3; color: #50CD89; border: 1px solid #D5F6E3; }
        .steps-list { margin: 16px 0 20px 0; padding-left: 20px; }
        .steps-list li { margin-bottom: 10px; font-size: 13px; color: #3F4254; }
        .signature { margin-top: 28px; padding-top: 20px; border-top: 1px solid #EFF2F5; font-size: 13px; color: #5E6278; }
        .signature strong { color: #181C32; }
        .email-footer { background-color: #F9FAFB; padding: 20px 32px; border-top: 1px solid #EFF2F5; font-size: 12px; color: #7E8299; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h2>We've Received Your Custom Plan Enquiry</h2>
            <p>JERO Enterprise Solutions</p>
        </div>
        <div class="email-body">
            @if(!empty($data->rendered_message ?? null))
                {!! $data->rendered_message !!}
            @else
                <p>Dear <strong>{{ $data->customer_name ?? $data['customer_name'] ?? 'Valued Customer' }}</strong>,</p>

                <p>Thank you for your interest in a <strong>Custom Subscription Plan with JERO.works</strong>.</p>

                <p>We've received your enquiry, and our team will review your business requirements to help you find a plan that best fits your needs.</p>

                <p>Whether you're looking for customized coin packages, volume discounts, or dedicated support, we're here to help you find the right solution.</p>

                <p><strong>What happens next?</strong></p>
                <ul class="steps-list">
                    <li>Our team will review your enquiry.</li>
                    <li>A representative will contact you to understand your requirements.</li>
                    <li>We'll discuss suitable package options, pricing, and available benefits.</li>
                </ul>

                <p>Thank you for choosing JERO.works. We look forward to supporting your business and helping you get the most out of our platform.</p>

                <div class="signature">
                    Best regards,<br>
                    <strong>JERO.works Sales Team</strong><br>
                    JERO.works Technology
                </div>
            @endif
        </div>
        <div class="email-footer">
            @include('email.global_settings')
        </div>
    </div>
</body>
</html>
