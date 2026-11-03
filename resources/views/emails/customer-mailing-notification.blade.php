<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 3px solid #ff6b35;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #1a1300;
        }
        .content {
            padding: 30px 20px;
        }
        .customer-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .customer-info p {
            margin: 5px 0;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>EliteFlow - Elite Tech Precision Ltd</h1>
    </div>

    <div class="content">
        <h2>Customer Reminder</h2>

        <p>Dear {{ $customer->name }},</p>

        <p>This is a reminder regarding your customer account with Elite Tech Precision Ltd.</p>

        <div class="customer-info">
            <p><span class="label">Customer Name:</span> {{ $customer->name }}</p>
            @if($customer->email)
            <p><span class="label">Email:</span> {{ $customer->email }}</p>
            @endif
            @if($customer->phone)
            <p><span class="label">Phone:</span> {{ $customer->phone }}</p>
            @endif
            @if($customer->address)
            <p><span class="label">Address:</span> {{ $customer->address }}</p>
            @endif
            @if($customer->region)
            <p><span class="label">Region:</span> {{ $customer->region }}</p>
            @endif
            @if($customer->status)
            <p><span class="label">Status:</span> {{ ucfirst($customer->status) }}</p>
            @endif
            @if($customer->job_ref)
            <p><span class="label">Job Reference:</span> {{ $customer->job_ref }}</p>
            @endif
            @if($customer->mailing_frequency)
            <p><span class="label">Mailing Frequency:</span> {{ ucfirst($customer->mailing_frequency) }}</p>
            @endif
            @if($customer->next_email_due)
            <p><span class="label">Next Email Due:</span> {{ $customer->next_email_due->format('F j, Y') }}</p>
            @endif
        </div>

        @if($customer->job_notes)
        <p><strong>Notes:</strong></p>
        <p>{{ $customer->job_notes }}</p>
        @endif

        <p>If you have any questions or need to update your information, please contact us.</p>

        <p>Best regards,<br>
        Elite Tech Precision Ltd Team</p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} Elite Tech Precision Ltd. All rights reserved.</p>
    </div>
</body>
</html>
