<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Updated</title>
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
        .update-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
            background: #FF6B35;
            color: white;
        }
        .customer-details {
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
        .changes-list {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Customer Profile Updated</h1>
        </div>
        <div class="content">
            @if($recipientType === 'customer')
                <h2>Your Profile Has Been Updated</h2>
                <p>Dear {{ $customer->name }},</p>
                <p>Your profile in EliteFlow has been updated.</p>
                <div class="update-badge">PROFILE UPDATED</div>
            @else
                <h2>Customer Updated</h2>
                <p>Customer profile has been updated in the EliteFlow system.</p>
                <div class="update-badge">CUSTOMER UPDATED</div>
            @endif
            
            <div class="customer-details">
                <p><strong>Customer Name:</strong> {{ $customer->name }}</p>
                @if($customer->email)
                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                @endif
                @if($customer->phone)
                    <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                @endif
                <p><strong>Status:</strong> {{ ucfirst($customer->status) }}</p>
            </div>
            
            @if(!empty($changes))
                <div class="changes-list">
                    <strong>Changes Made:</strong>
                    <ul>
                        @foreach($changes as $field => $change)
                            <li>{{ ucfirst($field) }}: {{ $change }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if($recipientType === 'customer')
                <p>If you did not make these changes or have any questions, please contact us immediately at support@devfaheem.com.</p>
            @else
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Review the changes made</li>
                    <li>Contact customer if needed</li>
                    <li>Update any related contracts or services</li>
                </ul>
            @endif
            
            <p><strong>Updated at:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            
            <a href="{{ config('app.url') }}" class="button">View in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated notification from EliteFlow - Elite Tech Precision Ltd</p>
            <p>For questions, contact support@devfaheem.com</p>
        </div>
    </div>
</body>
</html>