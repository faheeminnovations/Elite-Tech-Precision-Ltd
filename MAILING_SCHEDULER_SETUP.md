# Mailing Scheduler and Queue Worker Setup

This document provides the production server setup instructions for the mailing notification system.

## Prerequisites

- Laravel application deployed to production server
- Queue driver configured (database, redis, or other)
- Cron access on the server

## Queue Configuration

### 1. Configure Queue Driver

Update your `.env` file on the production server:

```env
QUEUE_CONNECTION=database
# Or use Redis for better performance:
CODE_CONNECTION=redis
```

### 2. Run Queue Worker

The queue worker processes the mailing notification jobs in the background.

#### Option A: Using Supervisor (Recommended)

Install Supervisor on your server:

```bash
sudo apt-get install supervisor
```

Create a Supervisor configuration file:

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Add the following configuration:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

Update the paths to match your actual project path.

Start the Supervisor worker:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

#### Option B: Using Systemd

Create a systemd service file:

```bash
sudo nano /etc/systemd/system/laravel-queue.service
```

Add the following:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/path/to/your/project
ExecStart=/usr/bin/php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always

[Install]
WantedBy=multi-user.target
```

Start the service:

```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
```

#### Option C: Manual (Not Recommended for Production)

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

## Cron Job Configuration

Add the Laravel scheduler to your server's crontab:

```bash
crontab -e
```

Add the following line (update the path to your project):

```cron
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

This will run the Laravel scheduler every minute, which includes the `mailing:dispatch` command that checks for contracts and services due for mailing notifications.

## Database Queue Setup (If Using Database Queue)

If using the database queue driver, ensure the jobs table exists:

```bash
php artisan queue:table
php artisan migrate
```

## Testing the Setup

### Test Queue Worker

```bash
php artisan queue:work --once
```

### Test Scheduler Command

```bash
php artisan mailing:dispatch
```

### Test Queue Job Dispatch

You can test by manually dispatching a job:

```bash
php artisan tinker
```

```php
use App\Jobs\SendMailingNotificationJob;
use App\Models\Contract;

$contract = Contract::first();
SendMailingNotificationJob::dispatch($contract, 'contract');
```

## Monitoring

### Check Queue Status

```bash
php artisan queue:failed
```

### Check Logs

Queue worker logs:
```bash
tail -f storage/logs/worker.log
```

Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

## Troubleshooting

### Queue Not Processing

1. Check if queue worker is running:
   ```bash
   ps aux | grep queue:work
   ```

2. Check Supervisor status:
   ```bash
   sudo supervisorctl status
   ```

3. Restart queue worker:
   ```bash
   sudo supervisorctl restart laravel-worker:*
   ```

### Cron Not Running

1. Check cron logs:
   ```bash
   grep CRON /var/log/syslog
   ```

2. Test cron manually:
   ```bash
   cd /path/to/your/project && php artisan schedule:run
   ```

### Emails Not Sending

1. Check mail configuration in `.env`
2. Check notification logs table for errors
3. Check Laravel logs for mail errors
4. Verify queue is processing jobs

## Security Considerations

- Ensure your `.env` file is not publicly accessible
- Use HTTPS for email SMTP connections
- Rotate API keys and credentials regularly
- Monitor queue worker logs for suspicious activity

## Performance Optimization

For high-volume email sending:

1. Use Redis as queue driver instead of database
2. Increase the number of queue worker processes in Supervisor
3. Consider using a dedicated email service (SendGrid, Mailgun, SES)
4. Implement rate limiting to avoid being flagged as spam
5. Monitor queue size and adjust worker count accordingly

## Additional Resources

- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Laravel Scheduler Documentation](https://laravel.com/docs/scheduling)
- [Supervisor Documentation](http://supervisord.org/)
