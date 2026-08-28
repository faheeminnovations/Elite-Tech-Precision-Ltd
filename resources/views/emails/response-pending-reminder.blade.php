<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response Pending Reminder</title>
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
            background: #0B2545;
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
            background: #ffc107;
            color: #333;
        }
        .response-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>EliteFlow Response Pending Reminder</h1>
        </div>
        <div class="content">
            <h2>Customer Response Pending</h2>
            
            <div class="reminder-badge">
                {{ $daysPending }} days pending
            </div>
            
            <div class="response-details">
                <p><strong>Job Reference:</strong> {{ $response->job_ref }}</p>
                <p><strong>Customer:</strong> {{ $customer->name ?? 'N/A' }}</p>
                <p><strong>Service Type:</strong> {{ $response->service_type ?? 'N/A' }}</p>
                <p><strong>Response Type:</strong> {{ $response->response ?? 'N/A' }}</p>
                <p><strong>PPM Due:</strong> {{ $response->ppm_due ? $response->ppm_due->format('Y-m-d') : 'N/A' }}</p>
                <p><strong>Reminder Sent:</strong> {{ $response->reminder_sent ? $response->reminder_sent->format('Y-m-d') : 'N/A' }}</p>
            </div>
            
            <p>This customer response has been pending for {{ $daysPending }} days. Please take appropriate action:</p>
            <ul>
                <li>Follow up with the customer to obtain their response</li>
                <li>Document all communication attempts</li>
                <li>Consider escalating to management if no response received</li>
                <li>Update the response status once customer responds</li>
                <li>Review and adjust reminder lead time if needed</li>
            </ul>
            
            <p><strong>Reminder sent:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            
            <a href="{{ route('responses.show', $response->id) }}" class="button">View Response in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated reminder from EliteFlow - Elite Tech Precision Ltd</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
