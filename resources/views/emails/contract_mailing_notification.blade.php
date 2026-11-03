<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #f8f9fa; padding: 30px; border-radius: 8px; border: 1px solid #e9ecef;">
            <h2 style="color: #2c3e50; margin-top: 0;">Contract Notification</h2>
            
            <p>Hello,</p>
            
            <p>This is a notification regarding your contract with <strong>{{ $contract->customer_name }}</strong>.</p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6; font-weight: bold; width: 40%;">Contract Reference:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ $contract->contract_ref }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Status:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ ucfirst($contract->status) }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Region:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ $contract->region ?? $contract->area }}</td>
                </tr>
                @if($contract->site)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Site:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ $contract->site->site_name }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Start Date:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ $contract->start_date?->format('F j, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Expiry Date:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ $contract->expiry_date?->format('F j, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6; font-weight: bold;">PPM Frequency:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ $contract->frequency }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Next PPM Due:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ $contract->next_ppm_due?->format('F j, Y') }}</td>
                </tr>
            </table>
            
            @if($contract->notes)
            <p><strong>Notes:</strong></p>
            <p style="background: #fff; padding: 15px; border-radius: 4px; border-left: 4px solid #007bff;">{{ $contract->notes }}</p>
            @endif
            
            <p style="margin-top: 30px; font-size: 14px; color: #6c757d;">
                This is an automated notification. If you have any questions, please contact our support team.
            </p>
            
            <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">
            
            <p style="font-size: 12px; color: #6c757d; text-align: center;">
                &copy; {{ date('Y') }} Elite Tech Precision Ltd. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
