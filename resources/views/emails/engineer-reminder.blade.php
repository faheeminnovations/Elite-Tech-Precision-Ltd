<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engineer Task Reminder</title>
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
            background: #1E8E5A;
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
        .task-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
            background: #1E8E5A;
            color: white;
        }
        .task-details {
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
            background: #1E8E5A;
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
        .urgent-box {
            background: #fce7e4;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #C0392B;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Engineer Task Notification</h1>
        </div>
        <div class="content">
            @if($reminderType === 'assignment')
                <h2>📋 New Task Assignment</h2>
                <p>Dear {{ $engineer->name ?? 'Engineer' }},</p>
                <p>You have been assigned a new maintenance task. Please review the details below.</p>
                <div class="task-badge">NEW ASSIGNMENT</div>
            @elseif($reminderType === 'reminder')
                <h2>⏰ Upcoming Task Reminder</h2>
                <p>Dear {{ $engineer->name ?? 'Engineer' }},</p>
                <p>This is a reminder about an upcoming scheduled task.</p>
                <div class="task-badge">UPCOMING TASK</div>
            @elseif($reminderType === 'schedule_change')
                <h2>🔄 Schedule Change Notification</h2>
                <p>Dear {{ $engineer->name ?? 'Engineer' }},</p>
                <p>There has been a change to your scheduled task. Please review the updated details.</p>
                <div class="task-badge" style="background: #B96A00;">SCHEDULE CHANGED</div>
            @elseif($reminderType === 'daily_summary')
                <h2>📊 Daily Task Summary</h2>
                <p>Dear {{ $engineer->name ?? 'Engineer' }},</p>
                <p>Here is your daily task summary for {{ now()->format('Y-m-d') }}.</p>
                <div class="task-badge">{{ $taskCount }} TASKS TODAY</div>
            @else
                <h2>🔧 Task Notification</h2>
                <p>Dear {{ $engineer->name ?? 'Engineer' }},</p>
                <p>You have a task notification.</p>
                <div class="task-badge">TASK NOTIFICATION</div>
            @endif
            
            @if($reminderType === 'daily_summary' && $taskCount > 1)
                <div class="info-box">
                    <strong>Summary:</strong> You have {{ $taskCount }} tasks scheduled for today. Please prioritize accordingly.
                </div>
            @endif
            
            @if($service)
                <div class="task-details">
                    <p><strong>Job Reference:</strong> {{ $contract->job_ref ?? 'N/A' }}</p>
                    <p><strong>Customer:</strong> {{ $customer->name ?? 'N/A' }}</p>
                    <p><strong>Service Type:</strong> {{ $service->service_type ?? 'PPM' }}</p>
                    <p><strong>Area:</strong> {{ $contract->area ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</p>
                    @if($service->visit_date)
                        <p><strong>Scheduled Date:</strong> {{ $service->visit_date->format('Y-m-d') }}</p>
                    @endif
                    @if($service->notes)
                        <p><strong>Special Instructions:</strong> {{ $service->notes }}</p>
                    @endif
                </div>
            @endif
            
            @if($reminderType === 'assignment')
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Review task details and requirements</li>
                    <li>Confirm availability for scheduled date</li>
                    <li>Prepare necessary tools and equipment</li>
                    <li>Contact customer if access information is needed</li>
                    <li>Check job history and previous service reports</li>
                </ul>
            @elseif($reminderType === 'reminder')
                <p><strong>Preparation Checklist:</strong></p>
                <ul>
                    <li>Confirm scheduled date and time</li>
                    <li>Review task requirements and specifications</li>
                    <li>Prepare tools, equipment, and spare parts</li>
                    <li>Check customer access requirements</li>
                    <li>Review previous service reports</li>
                </ul>
            @elseif($reminderType === 'schedule_change')
                <div class="urgent-box">
                    <strong>Important:</strong> Please update your calendar and confirm the new schedule.
                </div>
                <p><strong>Action Required:</strong></p>
                <ul>
                    <li>Update your personal calendar</li>
                    <li>Confirm availability for new date/time</li>
                    <li>Contact team if there are conflicts</li>
                    <li>Review any changes to task requirements</li>
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