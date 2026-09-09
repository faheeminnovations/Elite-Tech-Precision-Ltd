<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPM Due Within 30 Days</title>
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
            background: #FF6B35;
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
        .reminder-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
            background: #FF6B35;
            color: white;
        }
        .contract-details {
            background: #fff1de;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #FF6B35;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            background: #FF6B35;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
        .info-box {
            background: #e4ebf6;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #13315c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 Upcoming PPM Reminder</h1>
        </div>
        <div class="content">
            @if($reminderType === 'customer')
                <h2>PPM Due Soon</h2>
                <p>Dear {{ $customer->name ?? 'Valued Customer' }},</p>
                <p>This is a friendly reminder that your Preventive Maintenance (PPM) is due within the next 30 days.</p>
            @else
                <h2>Internal PPM Schedule Reminder</h2>
                <p>This is a reminder about an upcoming PPM that needs to be scheduled with the customer.</p>
            @endif
            
            <div class="reminder-badge">
                {{ $daysUntilDue }} {{ $daysUntilDue == 1 ? 'day' : 'days' }} until due
            </div>
            
            @if($ppmCount > 1)
                <div class="info-box">
                    <strong>Summary:</strong> You have {{ $ppmCount }} PPMs due within 30 days. Customer reminders are ready to send.
                </div>
            @endif
            
            <div class="contract-details">
                <p><strong>Job Reference:</strong> {{ $contract->job_ref }}</p>
                <p><strong>Customer:</strong> {{ $contract->customer_name ?? 'N/A' }}</p>
                <p><strong>Area:</strong> {{ $contract->area ?? 'N/A' }}</p>
                <p><strong>Frequency:</strong> {{ $contract->frequency ?? 'N/A' }}</p>
                <p><strong>Next PPM Due:</strong> {{ $contract->next_ppm_due ? $contract->next_ppm_due->format('Y-m-d') : 'N/A' }}</p>
                <p><strong>Days Until Due:</strong> {{ $daysUntilDue }}</p>
            </div>
            
            @if($reminderType === 'customer')
                <p>Please ensure that your facility is accessible and any necessary preparations are made before the scheduled maintenance visit.</p>
                <p>If you need to reschedule or have any questions, please contact us at support@devfaheem.com.</p>
            @else
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Contact customer to schedule PPM</li>
                    <li>Confirm availability and access requirements</li>
                    <li>Prepare necessary equipment and documentation</li>
                    <li>Update system with scheduled date</li>
                </ul>
            @endif
            
            <p><strong>Reminder sent:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            
            <a href="{{ config('app.url') }}" class="button">View in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated reminder from EliteFlow - Elite Tech Precision Ltd</p>
            <p>For questions, contact support@devfaheem.com</p>
        </div>
    </div>
</body>
</html>