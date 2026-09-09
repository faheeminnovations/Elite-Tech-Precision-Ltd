<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue PPM Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
        }
        .header {
            background: #C0392B;
            color: white;
            padding: 15px;
            border-radius: 6px 6px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            background: white;
            border-radius: 0 0 6px 6px;
        }
        .alert-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
            background: #C0392B;
            color: white;
        }
        .contract-details {
            background: #fce7e4;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #C0392B;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            background: #C0392B;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
        .urgent-note {
            background: #fff3cd;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ OVERDUE PPM ALERT</h1>
        </div>
        <div class="content">
            @if($reminderType === 'customer')
                <h2>Immediate Action Required</h2>
                <p>Dear {{ $customer->name ?? 'Valued Customer' }},</p>
                <p>Your Preventive Maintenance (PPM) is <strong>OVERDUE</strong>. Immediate scheduling action is required.</p>
            @else
                <h2>Internal Overdue PPM Alert</h2>
                <p>This PPM is overdue and requires immediate attention. Please contact the customer and schedule the maintenance as soon as possible.</p>
            @endif
            
            <div class="alert-badge">
                ⚠️ {{ $overdueDays }} {{ $overdueDays == 1 ? 'day' : 'days' }} overdue
            </div>
            
            <div class="urgent-note">
                <strong>URGENT:</strong> This maintenance is overdue and needs immediate scheduling to prevent equipment failure and maintain service level agreements.
            </div>
            
            <div class="contract-details">
                <p><strong>Job Reference:</strong> {{ $contract->job_ref }}</p>
                <p><strong>Customer:</strong> {{ $contract->customer_name ?? 'N/A' }}</p>
                <p><strong>Area:</strong> {{ $contract->area ?? 'N/A' }}</p>
                <p><strong>Frequency:</strong> {{ $contract->frequency ?? 'N/A' }}</p>
                <p><strong>PPM Due Date:</strong> {{ $contract->next_ppm_due ? $contract->next_ppm_due->format('Y-m-d') : 'N/A' }}</p>
                <p><strong>Days Overdue:</strong> {{ $overdueDays }}</p>
            </div>
            
            @if($reminderType === 'customer')
                <p>Please contact us immediately to schedule this overdue maintenance. Delaying PPMs can result in equipment failure and void warranty coverage.</p>
                <p>If you have already scheduled this maintenance, please disregard this notice.</p>
            @else
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Contact customer immediately</li>
                    <li>Schedule PPM at earliest availability</li>
                    <li>Update system with scheduled date</li>
                    <li>Document reason for delay if applicable</li>
                </ul>
            @endif
            
            <p><strong>Alert sent:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            
            <a href="{{ config('app.url') }}" class="button">View in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated alert from EliteFlow - Elite Tech Precision Ltd</p>
            <p>For immediate assistance, please contact support@devfaheem.com</p>
        </div>
    </div>
</body>
</html>