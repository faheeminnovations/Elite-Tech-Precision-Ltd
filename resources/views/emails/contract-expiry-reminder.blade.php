<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Expiry Reminder</title>
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
        .contract-details {
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
            <h1>EliteFlow Contract Expiry Alert</h1>
        </div>
        <div class="content">
            <h2>Contract Expiring Soon</h2>
            
            <div class="reminder-badge">
                {{ $daysUntilExpiry > 0 ? $daysUntilExpiry . ' days until expiry' : 'Expires today' }}
            </div>
            
            <div class="contract-details">
                <p><strong>Job Reference:</strong> {{ $contract->job_ref }}</p>
                <p><strong>Customer:</strong> {{ $contract->customer_name ?? 'N/A' }}</p>
                <p><strong>Area:</strong> {{ $contract->area ?? 'N/A' }}</p>
                <p><strong>Frequency:</strong> {{ $contract->frequency ?? 'N/A' }}</p>
                <p><strong>Contract Status:</strong> {{ $contract->status ?? 'N/A' }}</p>
            </div>
            
            <p>This contract is approaching its expiry date. Please take appropriate action:</p>
            <ul>
                <li>Contact the customer to discuss contract renewal</li>
                <li>Review contract terms and conditions</li>
                <li>Prepare renewal documentation if needed</li>
                <li>Update contract status accordingly</li>
            </ul>
            
            <p><strong>Reminder sent:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            
            <a href="{{ route('contracts.show', $contract->id) }}" class="button">View Contract in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated reminder from EliteFlow - Elite Tech Precision Ltd</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
