# Email Configuration Guide

## SMTP Configuration for EliteFlow

Add the following configuration to your `.env` file to enable email notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=195.35.39.91
MAIL_PORT=65002
MAIL_USERNAME=u249713597
MAIL_PASSWORD=Rac@909090
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=eliteflow@playwithlegit.com
MAIL_FROM_NAME="EliteFlow - Elite Tech Precision Ltd"
```

## Configuration Details

- **Host**: 195.35.39.91
- **Port**: 65002
- **Username**: u249713597
- **Password**: Rac@909090
- **From Email**: eliteflow@playwithlegit.com
- **Encryption**: null (no SSL/TLS)

## Setup Instructions

1. Open your `.env` file in the project root
2. Add or update the mail configuration lines above
3. Clear the configuration cache: `php artisan config:clear`
4. Test the email configuration: `php artisan email:test your-email@example.com`

## Email Notifications

The system now sends email notifications for:
- Contract creation, updates, and deletion
- Customer creation, updates, and deletion
- Service creation, updates, and deletion
- Response creation and updates
- All status changes across all entities

All notifications are sent to the email configured in the Settings page under "Internal reminder inbox".

## Email Templates

Email templates are located in `resources/views/emails/`:
- `status-update.blade.php` - Generic status updates
- `contract-status.blade.php` - Contract-specific updates
- `response-status.blade.php` - Customer response updates

## Email Notification Settings

In the Settings page, you can:
- Enable/disable email notifications globally
- Configure the internal reminder inbox email
- Set reminder lead times for internal and customer reminders

## Troubleshooting

If emails are not sending:
1. Check the SMTP credentials are correct in your `.env` file
2. Verify the SMTP server is accessible from your server
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test with: `php artisan email:test your-email@example.com`
5. Ensure email notifications are enabled in Settings

## Queue Configuration (Optional)

For better performance, you can queue emails:

```env
QUEUE_CONNECTION=database
```

Then run the queue worker:
```bash
php artisan queue:work
```