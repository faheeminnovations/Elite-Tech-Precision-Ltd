<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #f4f4f4; padding: 20px; border-radius: 5px;">
            <h2 style="color: #2c3e50; margin-top: 0;">Service Notification</h2>
            <p>Hello,</p>
            <p>This is a notification for your service with the following details:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Job Reference:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $service->job_ref }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Customer:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $service->customer_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Region:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $service->area ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Site / Installation:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $service->site_installation ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Service Type:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $service->service_type ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Status:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ ucfirst($service->status ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Visit Date:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $service->visit_date?->format('d M Y') ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Engineer:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $service->engineer_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Next PPM Due:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $service->next_ppm_due?->format('d M Y') ?? 'N/A' }}</td>
                </tr>
            </table>

            @if($service->job_details)
            <p><strong>Job Details:</strong></p>
            <p style="background: #fff; padding: 10px; border-left: 3px solid #007bff;">{{ $service->job_details }}</p>
            @endif

            @if($service->recommendations)
            <p><strong>Recommendations:</strong></p>
            <p style="background: #fff; padding: 10px; border-left: 3px solid #28a745;">{{ $service->recommendations }}</p>
            @endif

            <p style="margin-top: 20px;">If you have any questions regarding this service, please contact us.</p>
            
            <p style="margin-top: 30px;">Best regards,<br>Elite Tech Precision Ltd</p>
        </div>
    </div>
</body>
</html>
