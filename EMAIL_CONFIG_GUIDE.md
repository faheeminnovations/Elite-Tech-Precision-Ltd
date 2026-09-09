# Email Configuration Guide

## SMTP Configuration for EliteFlow

Add the following configuration to your `.env` file to enable email notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=195.35.39.91
MAIL_PORT=65002
MAIL_USERNAME=u249713597
MAIL_PASSWORD=d8*wTI0ND
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=support@devfaheem.com
MAIL_FROM_NAME="EliteFlow - Elite Tech Precision Ltd"
INTERNAL_REMINDER_EMAIL=support@devfaheem.com
```

## Configuration Details

- **Host**: 195.35.39.91
- **Port**: 65002
- **Username**: u249713597
- **Password**: d8*wTI0ND
- **From Email**: support@devfaheem.com
- **Encryption**: null (no SSL/TLS)
- **Internal Reminder Email**: support@devfaheem.com (receives system notifications)

## Setup Instructions

1. Open your `.env` file in the project root
2. Add or update the mail configuration lines above
3. Clear the configuration cache: `php artisan config:clear`
4. Test the email configuration: `php artisan email:test your-email@example.com`
5. Run database seeder to ensure email notification settings are configured: `php artisan db:seed --class=DatabaseSeeder`

## Email Notifications - UPDATED

The system now sends email notifications to **both customers and admins** for:
- Contract creation, updates, and deletion
- Customer creation, updates, and deletion
- Service creation, updates, and deletion
- Response creation and updates
- All status changes across all entities

### Dual Recipient System

**Admin Notifications:**
- Always sent to the configured admin email (`MAIL_FROM_ADDRESS`)
- Include administrative context in email content
- Used for tracking and monitoring purposes

**Customer Notifications:**
- Sent to the customer's email address when available
- Include customer-facing language and context
- Used for keeping customers informed about their accounts

### Notification Logic

For each notification event:
1. **Admin email** is always sent (to `MAIL_FROM_ADDRESS`)
2. **Customer email** is sent separately if the customer has an email address
3. Each email has appropriate context (admin vs customer messaging)
4. Both emails are queued and sent independently

## Automated Email Reminders

The system includes automated email reminder commands that can be scheduled:

### PPM Reminders
- **Command**: `php artisan emails:send-ppm-reminders`
- **Schedule**: Daily at 8:00 AM
- **Sends reminders for**:
  - Overdue PPMs (urgent action required)
  - PPMs due within 30 days (customer reminders ready to send)
- **Recipients**: Internal email and customers (if email available)

### Customer Response Reminders
- **Command**: `php artisan emails:send-customer-response-reminders`
- **Schedule**: Daily at 9:00 AM
- **Sends reminders for**:
  - Pending customer responses
  - Accepted customer responses
  - Declined customer responses
- **Recipients**: Internal email (review required)

### No-Contract Customer Reminders
- **Command**: `php artisan emails:send-no-contract-reminders`
- **Schedule**: Weekly on Monday at 10:00 AM
- **Sends reminders for**:
  - Customers with no active contract
  - Potential sales follow-up candidates
- **Recipients**: Internal email (sales team)

### Engineer Reminders
- **Command**: `php artisan emails:send-engineer-reminders`
- **Schedule**: Daily at 7:00 AM
- **Sends reminders for**:
  - Services scheduled for today
  - Services scheduled for tomorrow
  - Daily task summaries
- **Recipients**: Engineers and internal email

### Testing Commands
All reminder commands support a `--test` flag to send test emails instead of actual reminders:
```bash
php artisan emails:send-ppm-reminders --test
php artisan emails:send-customer-response-reminders --test
php artisan emails:send-no-contract-reminders --test
php artisan emails:send-engineer-reminders --test
```

### New Testing Command
Test the complete notification system:
```bash
php artisan emails:test-notifications [email@example.com]
```
This command tests all notification types and sends emails to both customer and admin recipients.

## Email Templates

Email templates are located in `resources/views/emails/`:
- `status-update.blade.php` - Generic status updates
- `contract-status.blade.php` - Contract-specific updates
- `response-status.blade.php` - Customer response updates

### Template Updates
All email templates now support:
- Admin context flag (`$isAdmin`) for different messaging
- Conditional content based on recipient type
- Separate language for administrative vs customer notifications

## Email Notification Settings

The system uses the `email_notification_settings` table to control which notification types are enabled. The database seeder automatically populates this with all available notification types.

In the Settings page, you can:
- Enable/disable email notifications globally
- Configure the internal reminder inbox email
- Set reminder lead times for internal and customer reminders
- Enable/disable specific notification types individually

## Troubleshooting

If emails are not sending:
1. Check the SMTP credentials are correct in your `.env` file
2. Verify the SMTP server is accessible from your server
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test with: `php artisan email:test your-email@example.com`
5. Ensure email notifications are enabled in Settings
6. Run database seeder: `php artisan db:seed --class=DatabaseSeeder`
7. Test notification system: `php artisan emails:test-notifications`

## Queue Configuration (Optional)

For better performance, you can queue emails:

```env
QUEUE_CONNECTION=database
```

Then run the queue worker:
```bash
php artisan queue:work
```

## Recent Updates

### Customer and Admin Dual Notifications
- Fixed notification system to send emails to both customers and admins
- Updated all notification methods in `NotificationService`
- Modified email templates to support admin/customer context
- Added `isAdmin` parameter to mail classes for conditional content
- Updated database seeder to include all reminder notification types

### Notification Types Added
- customer_ppm_reminder
- internal_ppm_reminder
- contract_expiry_reminder
- service_due_reminder
- overdue_contract_reminder
- response_pending_reminder