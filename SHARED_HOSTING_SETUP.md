# Shared Hosting Setup Guide

## Server Environment
- Hosting: Shared Hosting (u136534802@sg-nme-web603)
- URL: https://elite-tech-precision-ltd.devfaheem.com/
- Project Path: /home/u136534802/public_html/Elite-Tech-Precision-Ltd

## Setup Options

### Option 1: Simple Setup (Recommended for Now)
**No queue workers needed - emails send immediately**

1. **Update .env file:**
```env
QUEUE_CONNECTION=sync
```

2. **Add only Laravel Scheduler cron job via cPanel:**
```bash
* * * * * cd /home/u136534802/public_html/Elite-Tech-Precision-Ltd && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

### Option 2: Queue Processing (Better Performance)
**Uses cron jobs instead of Supervisor**

1. **Keep database queue in .env:**
```env
QUEUE_CONNECTION=database
```

2. **Add both cron jobs via cPanel:**
```bash
# Laravel Scheduler (runs every minute)
* * * * * cd /home/u136534802/public_html/Elite-Tech-Precision-Ltd && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1

# Queue Worker (runs every 5 minutes)
*/5 * * * * cd /home/u136534802/public_html/Elite-Tech-Precision-Ltd && /usr/local/bin/php artisan queue:work --once >> /dev/null 2>&1
```

## How to Add Cron Jobs via cPanel

1. **Log in to cPanel**
   - Go to: https://yourdomain.com/cpanel
   - Use your hosting credentials

2. **Navigate to Cron Jobs**
   - Look for "Cron Jobs" in the "Advanced" section
   - Click on "Cron Jobs"

3. **Add New Cron Job**
   - Select "Once per minute" (****)
   - Add the command from the options above
   - Click "Add New Cron Job"

4. **Verify PHP Path**
   - Run this command in SSH to find your PHP path:
   ```bash
   which php
   ```
   - Common paths:
     - `/usr/local/bin/php`
     - `/usr/bin/php`
     - `/opt/cpanel/ea-php81/bin/php`

## Current Scheduled Commands

Your Laravel scheduler will automatically run these commands at their scheduled times:

- **7:00 AM Daily**: Engineer reminders
- **8:00 AM Daily**: PPM reminders  
- **9:00 AM Daily**: Customer response reminders
- **10:00 AM Monday**: No-contract customer reminders

## Testing Setup

After adding cron jobs, test manually:

```bash
# Test scheduler
cd /home/u136534802/public_html/Elite-Tech-Precision-Ltd
php artisan schedule:run

# Test queue (if using Option 2)
php artisan queue:work --once
```

## Monitoring

Check logs to ensure everything is working:

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Queue logs (if using Option 2)
tail -f storage/logs/queue.log
```

## Recommendation

**Start with Option 1** (Simple Setup):
- Easier to configure
- No queue management needed
- Emails send immediately
- Works perfectly for moderate email volume

**Upgrade to Option 2** later if:
- You notice performance issues
- Email volume increases significantly
- You need better queue management

## Support

If you need help finding your PHP path or setting up cPanel cron jobs:
1. Contact your hosting provider
2. Check cPanel documentation
3. Run `which php` in SSH to find correct PHP path