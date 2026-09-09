<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Contract Customer Alert</title>
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
            background: #6B3FA0;
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
            background: #6B3FA0;
            color: white;
        }
        .customer-details {
            background: #efe9f7;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #6B3FA0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            background: #6B3FA0;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
        .sales-box {
            background: #fff1de;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #B96A00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💼 No Contract Customer Alert</h1>
        </div>
        <div class="content">
            @if($reminderType === 'sales')
                <h2>Potential Sales Follow-up Candidate</h2>
                <p>This customer has no active contract and represents a potential sales opportunity.</p>
            @else
                <h2>No Active Contract</h2>
                <p>This customer currently has no active contract in the system.</p>
            @endif
            
            <div class="alert-badge">
                ⚠️ No Active Contract
            </div>
            
            @if($customerCount > 1)
                <div class="sales-box">
                    <strong>Summary:</strong> You have {{ $customerCount }} customers with no contract. These are potential sales follow-up candidates.
                </div>
            @endif
            
            <div class="customer-details">
                <p><strong>Customer Name:</strong> {{ $customer->name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}</p>
                @if($customer->address)
                    <p><strong>Address:</strong> {{ $customer->address }}</p>
                @endif
                @if($lastServiceDate)
                    <p><strong>Last Service Date:</strong> {{ $lastServiceDate->format('Y-m-d') }}</p>
                @endif
            </div>
            
            @if($reminderType === 'sales')
                <p><strong>Sales Opportunity:</strong></p>
                <ul>
                    <li>Contact customer to discuss maintenance contract options</li>
                    <li>Offer preventive maintenance packages</li>
                    <li>Highlight benefits of regular maintenance contracts</li>
                    <li>Schedule site assessment if needed</li>
                    <li>Prepare customized proposal based on their needs</li>
                </ul>
                
                <div class="sales-box">
                    <strong>Follow-up Strategy:</strong> Consider reaching out with special offers or flexible contract terms to convert this customer to a contract-based relationship.
                </div>
            @else
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Contact customer to determine contract status</li>
                    <li>Update customer records if contract information is missing</li>
                    <li>Determine if new contract proposal is needed</li>
                    <li>Document reason for no contract status</li>
                </ul>
            @endif
            
            <p><strong>Alert sent:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            
            <a href="{{ config('app.url') }}" class="button">View in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated alert from EliteFlow - Elite Tech Precision Ltd</p>
            <p>For questions, contact support@devfaheem.com</p>
        </div>
    </div>
</body>
</html>