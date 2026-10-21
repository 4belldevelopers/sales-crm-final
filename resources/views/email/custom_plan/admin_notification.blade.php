<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data->subject ?? 'New Custom Subscription Plan Enquiry – JERO.works' }}</title>
    <style>
        body { font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f5f8fa; margin: 0; padding: 20px; color: #181C32; }
        .email-wrapper { max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #E4E6EF; }
        .email-header { background: #1B1B29; padding: 24px 32px; text-align: left; border-bottom: 3px solid #7239EA; }
        .email-header h2 { color: #ffffff; margin: 0; font-size: 20px; font-weight: 600; letter-spacing: -0.2px; }
        .email-header p { color: #A1A5B7; margin: 6px 0 0 0; font-size: 13px; }
        .email-body { padding: 32px; line-height: 1.6; font-size: 14px; color: #3F4254; }
        .email-body p { margin-top: 0; margin-bottom: 16px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; background: #F8F5FF; color: #7239EA; border: 1px solid #E8FFF3; }
        .detail-box { background-color: #FAFAFB; border: 1px solid #EFF2F5; border-radius: 6px; padding: 18px 24px; margin: 20px 0; }
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table th, .detail-table td { padding: 8px 10px; text-align: left; font-size: 13px; border-bottom: 1px solid #EFF2F5; }
        .detail-table th { width: 35%; color: #7E8299; font-weight: 500; }
        .detail-table td { width: 65%; color: #181C32; font-weight: 600; }
        .cta-container { text-align: center; margin: 28px 0 16px 0; }
        .btn-cta { display: inline-block; background-color: #7239EA; color: #ffffff !important; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; }
        .signature { margin-top: 28px; padding-top: 20px; border-top: 1px solid #EFF2F5; font-size: 13px; color: #5E6278; }
        .signature strong { color: #181C32; }
        .email-footer { background-color: #F9FAFB; padding: 20px 32px; border-top: 1px solid #EFF2F5; font-size: 12px; color: #7E8299; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h2>New Custom Subscription Plan Enquiry</h2>
            <p>JERO Enterprise Sales Notification</p>
        </div>
        <div class="email-body">
            @if(!empty($data->rendered_message ?? null))
                {!! $data->rendered_message !!}
            @else
                <p>Dear Admin Team,</p>

                <p>A new enquiry for a <strong>Custom Subscription Plan</strong> has been submitted through the JERO.works platform.</p>

                <p>The customer is interested in exploring a subscription package tailored to their business requirements.</p>

                <div class="detail-box">
                    <p style="margin: 0 0 10px 0; font-weight: 600; color: #181C32; font-size: 14px;">Enquiry Details</p>
                    <table class="detail-table">
                        <tr>
                            <th>Reference:</th>
                            <td><span class="badge">{{ $data->reference ?? $data['reference'] ?? 'N/A' }}</span></td>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $data->customer_name ?? $data['customer_name'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><a href="mailto:{{ $data->customer_email ?? $data['customer_email'] ?? '' }}" style="color: #009EF7; text-decoration: none;">{{ $data->customer_email ?? $data['customer_email'] ?? 'N/A' }}</a></td>
                        </tr>
                        @if(!empty($data->customer_mobile ?? $data['customer_mobile'] ?? null))
                        <tr>
                            <th>Phone / Mobile:</th>
                            <td>{{ $data->customer_mobile ?? $data['customer_mobile'] }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Company:</th>
                            <td>{{ $data->company_name ?? $data['company_name'] ?? 'N/A' }} (ID: {{ $data->company_id ?? $data['company_id'] ?? 'N/A' }})</td>
                        </tr>
                        @if(!empty($data->location ?? $data['location'] ?? null))
                        <tr>
                            <th>Location:</th>
                            <td>{{ $data->location ?? $data['location'] }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Enquiry Type:</th>
                            <td>Custom Subscription Plan</td>
                        </tr>
                        @if(!empty($data->package_title ?? $data['package_title'] ?? null))
                        <tr>
                            <th>Target Tier:</th>
                            <td>{{ ucfirst($data->package_type ?? 'Operational') }} &mdash; {{ $data->package_title }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Submitted On:</th>
                            <td>{{ $data->submitted_at ?? now()->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>

                <p>Please contact the customer to understand their requirements and recommend the most suitable plan, pricing, and coin package.</p>

                <p>We recommend following up promptly to provide a seamless customer experience.</p>

                <div class="signature">
                    Best regards,<br>
                    <strong>JERO.works System Notification</strong><br>
                    JERO.works Technology
                </div>
            @endif

            @if(!empty($data->admin_url ?? $data['admin_url'] ?? null))
            <div class="cta-container">
                <a href="{{ $data->admin_url ?? $data['admin_url'] }}" class="btn-cta">
                    View Request in Admin Center
                </a>
            </div>
            @endif
        </div>
        <div class="email-footer">
            <p style="margin: 0;"><strong>Notice:</strong> This notification was generated automatically by the JERO Platform. Please review and update the status in the Super Admin Custom Plan Requests dashboard.</p>
            @include('email.global_settings')
        </div>
    </div>
</body>
</html>
