# Production Email Automation Setup Guide

This document provides instructions for configuring the production server to handle automated email notifications for contracts and services.

## Prerequisites

- Laravel application deployed to production server
- SSH access to the production server
- Queue driver configured in `.env` (recommended: `redis` or `database`)
- Mail credentials configured in `.env`

## 1. Configure Queue Driver

Update your production `.env` file:

```env
QUEUE_CONNECTION=database
# Or for better performance with Redis:
# QUEUE_CONNECTION=redis
```

If using database queue driver, run the migration (already included in Laravel):

```bash
php artisan queue:table
php artisan migrate
```

## 2. Configure Mail Settings

Update your production `.env` file with your mail provider credentials:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 3. Set Up Cron Job

Add the following cron job to your production server's crontab:

```bash
crontab -e
```

Add this line to run the Laravel scheduler every minute:

```cron
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

Replace `/path/to/your/project` with the actual path to your Laravel application on the production server.

**Example:**
```cron
* * * * * cd /var/www/html/elite-tech-precision && php artisan schedule:run >> /dev/null 2>&1
```

## 4. Set Up Queue Worker

### Option A: Using Supervisor (Recommended for Production)

Supervisor is a process monitor that ensures your queue workers stay running and restart automatically if they fail.

#### Install Supervisor

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install supervisor
```

**CentOS/RHEL:**
```bash
sudo yum install supervisor
```

#### Create Supervisor Configuration

Create a new configuration file:

```bash
sudo nano /etc/supervisor/conf.d/elite-tech-precision-worker.conf
```

Add the following configuration:

```ini
[program:elite-tech-precision-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

**Important:** Replace `/path/to/your/project` with your actual project path and `www-data` with your web server user.

#### Start Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start elite-tech-precision-worker:*
```

#### Check Status

```bash
sudo supervisorctl status
```

### Option B: Using Simple Queue Worker (Not Recommended for Production)

If you cannot use Supervisor, you can run the queue worker manually in the background:

```bash
nohup php artisan queue:work --sleep=3 --tries=3 >> storage/logs/queue.log 2>&1 &
```

**Note:** This method is not recommended for production as the worker will not restart automatically if it fails.

## 5. Verify Setup

### Test Scheduler

Run the scheduler manually to verify it works:

```bash
php artisan schedule:run
```

Check the output to ensure the `emails:dispatch` command runs successfully.

### Test Queue Worker

Test the queue worker by dispatching a test job:

```bash
php artisan tinker
```

Then in tinker:
```php
dispatch(function() {
    \Log::info('Queue worker test successful');
});
```

Check the logs to confirm the job was processed.

## 6. Monitor Logs

### Scheduler Logs
The scheduler output is logged to Laravel's log file:
```bash
tail -f storage/logs/laravel.log
```

### Queue Worker Logs
If using Supervisor, check the worker log:
```bash
tail -f storage/logs/worker.log
```

### Notification Logs
Check the notification_logs table for email delivery status:
```bash
php artisan tinker
```

```php
\App\Models\NotificationLog::latest()->get();
```

## 7. Troubleshooting

### Emails Not Sending

1. Check mail configuration in `.env`
2. Verify queue worker is running: `sudo supervisorctl status`
3. Check Laravel logs: `tail -f storage/logs/laravel.log`
4. Test mail configuration: `php artisan tinker` then `\Mail::raw('Test', function($msg) { $msg->to('your@email.com')->subject('Test'); });`

### Queue Worker Not Processing Jobs

1. Restart supervisor: `sudo supervisorctl restart elite-tech-precision-worker:*`
2. Check queue connection in `.env`
3. Verify jobs are being dispatched: check `jobs` table in database

### Scheduler Not Running

1. Verify cron job is installed: `crontab -l`
2. Check cron logs: `grep CRON /var/log/syslog` (Ubuntu) or `/var/log/cron` (CentOS)
3. Test scheduler manually: `php artisan schedule:run`

## 8. Maintenance

### Restart Queue Worker

```bash
sudo supervisorctl restart elite-tech-precision-worker:*
```

### Clear Failed Jobs

```bash
php artisan queue:flush
```

### Retry Failed Jobs

```bash
php artisan queue:retry all
```

## 9. Security Considerations

- Ensure your `.env` file has restricted permissions: `chmod 600 .env`
- Use environment-specific mail credentials
- Monitor for excessive email sending to prevent spam
- Consider implementing rate limiting for email notifications
- Use HTTPS for all mail connections (TLS/SSL)

## 10. Performance Optimization

For high-volume email sending:

1. **Use Redis** instead of database queue for better performance
2. **Increase worker processes** in Supervisor configuration (adjust `numprocs`)
3. **Use a dedicated email service** like SendGrid, Mailgun, or AWS SES
4. **Implement email batching** for bulk notifications
5. **Monitor queue depth** and scale workers accordingly

## 11. Email Service Providers

Consider using a dedicated email service for better deliverability:

- **SendGrid**: Free tier up to 100 emails/day
- **Mailgun**: Free trial available
- **AWS SES**: Cost-effective for high volume
- **Postmark**: Reliable transactional email service

Each provider has specific configuration settings that need to be added to your `.env` file.

## Contact

For issues or questions regarding the email automation system, contact your development team.
