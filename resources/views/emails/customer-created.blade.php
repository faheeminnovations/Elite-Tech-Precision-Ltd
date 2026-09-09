<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Created</title>
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
        .success-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
            background: #1E8E5A;
            color: white;
        }
        .customer-details {
            background: #e4f5ec;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #1E8E5A;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            background: #0B2545;
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
            <h1>🎉 New Customer Added</h1>
        </div>
        <div class="content">
            @if($recipientType === 'customer')
                <h2>Welcome to EliteFlow!</h2>
                <p>Dear {{ $customer->name }},</p>
                <p>Welcome to Elite Tech Precision Ltd! We are pleased to have you as our valued customer.</p>
                <div class="success-badge">ACCOUNT CREATED</div>
            @else
                <h2>New Customer Added</h2>
                <p>A new customer has been added to the EliteFlow system.</p>
                <div class="success-badge">NEW CUSTOMER</div>
            @endif
            
            <div class="customer-details">
                <p><strong>Customer Name:</strong> {{ $customer->name }}</p>
                @if($customer->email)
                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                @endif
                @if($customer->phone)
                    <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                @endif
                @if($customer->address)
                    <p><strong>Address:</strong> {{ $customer->address }}</p>
                @endif
                @if($customer->region)
                    <p><strong>Region:</strong> {{ $customer->region }}</p>
                @endif
                @if($customer->category)
                    <p><strong>Category:</strong> {{ $customer->category }}</p>
                @endif
                <p><strong>Status:</strong> {{ ucfirst($customer->status) }}</p>
            </div>
            
            @if($recipientType === 'customer')
                <p>We look forward to providing you with excellent service and support. Our team will be in touch with you shortly to discuss your requirements.</p>
                <p>If you have any questions, please don't hesitate to contact us at support@devfaheem.com.</p>
            @else
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Review customer details</li>
                    <li>Contact customer to welcome them</li>
                    <li>Discuss service requirements</li>
                    <li>Create contract if applicable</li>
                </ul>
            @endif
            
            <p><strong>Created at:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            
            <a href="{{ config('app.url') }}" class="button">View in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated notification from EliteFlow - Elite Tech Precision Ltd</p>
            <p>For questions, contact support@devfaheem.com</p>
        </div>
    </div>
</body>
</html>