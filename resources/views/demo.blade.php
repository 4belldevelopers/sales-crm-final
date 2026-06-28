<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="background-color: white; padding: 20px; max-width: 800px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 24px; color: #333;">
                <h1 style="margin: 0;">{}CodeLab</h1>
            </div>
            <div>
                <button style="background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Pay Now</button>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
            <div style="font-size: 14px; color: #666; line-height: 1.6;">
                <p><strong>Invoice #34782</strong></p>
                <p><strong>Issue Date:</strong> 12 Apr 2021</p>
                <p><strong>Due Date:</strong> 02 May 2021 <span style="color: red;">• Due in 7 days</span></p>
            </div>
            <div style="font-size: 14px; color: #666; line-height: 1.6;">
                <p><strong>Issued For:</strong></p>
                <p>KeenThemes Inc.</p>
                <p>8692 Wild Rose Drive</p>
                <p>Livonia, MI 48150</p>
                
                <p><strong>Issued By:</strong></p>
                <p>CodeLab Inc.</p>
                <p>9858 South 53rd Ave.</p>
                <p>Matthews, NC 28104</p>
            </div>
        </div>

        <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
            <thead style="background-color: #f4f4f4; border-bottom: 2px solid #ccc;">
                <tr>
                    <th style="padding: 15px; text-align: left;">Description</th>
                    <th style="padding: 15px; text-align: left;">Hours</th>
                    <th style="padding: 15px; text-align: left;">Rate</th>
                    <th style="padding: 15px; text-align: left;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #ccc;">
                    <td style="padding: 15px;"><span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background-color: red; margin-right: 10px;"></span>Creative Design</td>
                    <td style="padding: 15px;">80</td>
                    <td style="padding: 15px;">$40.00</td>
                    <td style="padding: 15px;">$3200.00</td>
                </tr>
                <tr style="border-bottom: 1px solid #ccc;">
                    <td style="padding: 15px;"><span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background-color: green; margin-right: 10px;"></span>Logo Design</td>
                    <td style="padding: 15px;">120</td>
                    <td style="padding: 15px;">$40.00</td>
                    <td style="padding: 15px;">$4800.00</td>
                </tr>
                <tr style="border-bottom: 1px solid #ccc;">
                    <td style="padding: 15px;"><span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background-color: blue; margin-right: 10px;"></span>Web Development</td>
                    <td style="padding: 15px;">210</td>
                    <td style="padding: 15px;">$60.00</td>
                    <td style="padding: 15px;">$12600.00</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 20px; text-align: right;">
            <div style="display: flex; justify-content: space-between; font-size: 16px; margin-bottom: 5px;">
                <p>Subtotal:</p>
                <p>$20,600.00</p>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 16px; margin-bottom: 5px;">
                <p>VAT 0%:</p>
                <p>$0.00</p>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold;">
                <p><strong>Total:</strong></p>
                <p><strong>$20,600.00</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
