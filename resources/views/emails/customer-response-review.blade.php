<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Response Review</title>
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
            background: #13315C;
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
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-accepted {
            background: #E4F5EC;
            color: #1E8E5A;
        }
        .status-declined {
            background: #FCE7E4;
            color: #C0392B;
        }
        .status-pending {
            background: #FFF1DE;
            color: #B96A00;
        }
        .status-received {
            background: #E4EBF6;
            color: #13315C;
        }
        .response-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #13315C;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            background: #13315C;
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
            border-left: 4px solid #13315C;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Customer Response Review</h1>
        </div>
        <div class="content">
            @if($responseType === 'accepted')
                <h2>✅ Customer Response Accepted</h2>
                <p>The customer has accepted the proposed schedule for this contract.</p>
                <div class="status-badge status-accepted">ACCEPTED</div>
            @elseif($responseType === 'declined')
                <h2>❌ Customer Response Declined</h2>
                <p>The customer has declined the proposed schedule. Please review and reschedule.</p>
                <div class="status-badge status-declined">DECLINED</div>
            @elseif($responseType === 'pending')
                <h2>⏳ Customer Response Pending Review</h2>
                <p>A customer response requires your review and action.</p>
                <div class="status-badge status-pending">PENDING REVIEW</div>
            @elseif($responseType === 'received')
                <h2>📨 New Customer Response Received</h2>
                <p>A new customer response has been received and needs to be processed.</p>
                <div class="status-badge status-received">NEW RESPONSE</div>
            @else
                <h2>Customer Response Review Required</h2>
                <p>A customer response requires your attention.</p>
                <div class="status-badge status-pending">REVIEW REQUIRED</div>
            @endif
            
            @if($responseCount > 1)
                <div class="info-box">
                    <strong>Summary:</strong> You have {{ $responseCount }} customer responses that need review. Accepted/declined responses need review.
                </div>
            @endif
            
            <div class="response-details">
                <p><strong>Job Reference:</strong> {{ $contract->job_ref ?? 'N/A' }}</p>
                <p><strong>Customer:</strong> {{ $customer->name ?? 'N/A' }}</p>
                <p><strong>Response Date:</strong> {{ $response->responded_on ?? now()->format('Y-m-d') }}</p>
                <p><strong>Response Type:</strong> {{ $response->response ?? 'N/A' }}</p>
                @if($response->notes)
                    <p><strong>Customer Notes:</strong> {{ $response->notes }}</p>
                @endif
                @if($response->response_time)
                    <p><strong>Response Time:</strong> {{ $response->response_time }}</p>
                @endif
            </div>
            
            @if($responseType === 'declined')
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Review customer's reason for declining</li>
                    <li>Contact customer to discuss alternative scheduling</li>
                    <li>Update contract with new proposed dates</li>
                    <li>Send updated schedule for customer approval</li>
                </ul>
            @elseif($responseType === 'accepted')
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Confirm the scheduled dates in the system</li>
                    <li>Assign engineers to the job</li>
                    <li>Send confirmation to customer</li>
                    <li>Prepare for the scheduled maintenance</li>
                </ul>
            @elseif($responseType === 'pending' || $responseType === 'received')
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Review the customer's response</li>
                    <li>Update contract status accordingly</li>
                    <li>Follow up with customer if needed</li>
                    <li>Document the response in the system</li>
                </ul>
            @endif
            
            <p><strong>Notification sent:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            
            <a href="{{ config('app.url') }}" class="button">View in EliteFlow</a>
        </div>
        <div class="footer">
            <p>This is an automated notification from EliteFlow - Elite Tech Precision Ltd</p>
            <p>For questions, contact support@devfaheem.com</p>
        </div>
    </div>
</body>
</html>