<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Due Reminder</title>
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
            background: #17a2b8;
            color: white;
        }
        .service-details {
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
            <h1>EliteFlow Service Due Reminder</h1>
        </div>
        <div class="content">
            <h2>Service Scheduled</h2>
            
            <div class="reminder-badge">
                Service Due
            </div>
            
            <div class="service-details">
                <p><strong>Job Reference:</strong> {{ $service->job_ref }}</p>
                <p><strong>Customer:</strong> {{ $customer->name ?? 'N/A' }}</p>
                <p><strong>Service Type:</strong> {{ $service->service_type ?? 'N/A' }}</p>
                <p><strong>Area:</strong> {{ $service->area ?? 'N/A' }}</p>
                <p><strong>Visit Date:</strong> {{ $service->visit_date ? $service->visit_date->format('Y-m-d') : 'N/A' }}</p>
                <p><strong>Assigned Engineer:</strong> {{ $service->engineer_name ?? 'Not assigned' }}</p>
                <p><strong>Status:</strong> {{ $service->status ?? 'N/A' }}</p>
            </div>
            
            <p>This service is scheduled and requires attention. Please ensure:</p>
            <ul>
                <li>The assigned engineer is aware of the schedule</li>
                <li>Customer has been notified of the visit</li>
                <li> necessary equipment and materials are prepared</li>
                <li>Site access requirements are confirmed</li>
            </ul>
            
            <p><strong>Reminder sent:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            
            <a href="{{ route('services.show', $service->id) }}" class="button">View Service in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated reminder from EliteFlow - Elite Tech Precision Ltd</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
