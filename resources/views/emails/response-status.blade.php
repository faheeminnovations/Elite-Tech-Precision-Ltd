<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response Status Update</title>
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
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            margin: 5px 0;
        }
        .old-status {
            background: #ffecb3;
            color: #856404;
        }
        .new-status {
            background: #d4edda;
            color: #155724;
        }
        .action {
            background: #e2e7ef;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
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
            <h1>EliteFlow Response Update</h1>
        </div>
        <div class="content">
            <h2>Customer Response Status Changed</h2>
            
            <div class="response-details">
                <p><strong>Job Reference:</strong> {{ $response->job_ref }}</p>
                <p><strong>Customer:</strong> {{ $response->customer_name ?? 'N/A' }}</p>
                <p><strong>Service Type:</strong> {{ $response->service_type ?? 'N/A' }}</p>
                <p><strong>Response Type:</strong> {{ $response->response ?? 'N/A' }}</p>
            </div>
            
            <p><strong>Action:</strong> {{ $action }}</p>
            
            <div class="status-badge old-status">
                Old Status: {{ $oldStatus }}
            </div>
            <div class="status-badge new-status">
                New Status: {{ $newStatus }}
            </div>
            
            <div class="action">
                <p><strong>Performed by:</strong> {{ $user->name ?? 'System' }} ({{ $user->email ?? 'N/A' }})</p>
                <p><strong>Time:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            </div>
            
            @if(isset($isAdmin) && $isAdmin)
                <p>This customer response status change was performed in the EliteFlow system. This notification is for administrative purposes.</p>
            @else
                <p>This customer response status change was performed in the EliteFlow system. If you did not authorize this change, please contact your administrator immediately.</p>
            @endif
            
            <a href="{{ route('responses.show', $response->id) }}" class="button">View Response in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated notification from EliteFlow - Elite Tech Precision Ltd</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>