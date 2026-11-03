# Elite Tech Precision Ltd - System Functional Documentation

## Overview
This is a Laravel-based web application for managing precision engineering services, contracts, customers, and automated email notifications. The system helps track Preventive Maintenance (PPM) schedules, service visits, customer responses, and sends automated reminders.

---

## Core System Architecture

### Technology Stack
- **Framework**: Laravel 12.0 (PHP 8.2+)
- **Database**: MySQL (with Laravel Eloquent ORM)
- **Queue System**: Laravel Queues (database/Redis support)
- **Task Scheduling**: Laravel Scheduler
- **Authentication**: Laravel Breeze with role-based access control
- **Package**: Spatie Laravel Permission for role management

### Key Components
1. **Models**: Customer, Contract, Service, Response, User, Region, CustomerSite, NotificationLog
2. **Controllers**: Manage CRUD operations for all entities
3. **Services**: NotificationService, AreaScheduleService, CsvExporter, ActivityLogger
4. **Jobs**: Queue-based email processing (SendMailingNotificationJob)
5. **Commands**: Scheduled tasks for email dispatching and reminders
6. **Middleware**: Role-based access control (admin, engineer)

---

## Functional Modules

### 1. User Management & Authentication

**User Roles:**
- **Admin**: Full access to all features including settings and user management
- **Engineer**: Access to customers, contracts, services, responses, dashboard, and reports

**Authentication Flow:**
- Users log in via `/login` route
- Role-based middleware restricts access to routes
- Session-based authentication with optional "remember me" functionality
- Profile management allows users to update their information

**User Management (Admin Only):**
- Create, read, update, delete users
- Assign roles (admin/engineer)
- Track user status (active/inactive)
- Track last login timestamps

---

### 2. Customer Management

**Customer Data Structure:**
- Name, Job Reference, Contact Information (phone, email, address)
- Status: Active, No Contract, Upcoming, Overdue, Expiring, New, Previous, Updated
- Category: Chain Customer, New Customer
- Region: All counties in Ireland (26 counties)
- Sites: Multiple sites per customer (relational via CustomerSite model)

**Customer Features:**
- Full CRUD operations
- Quick-create for rapid data entry
- Search and filter by name, job reference, email, phone, address, status, category, region
- Export to CSV
- Track job details and notes
- Link to contracts and services
- Activity logging for all changes

**Relationships:**
- One-to-many with Contracts
- One-to-many with Services
- One-to-many with CustomerSites

---

### 3. Contract Management

**Contract Data Structure:**
- Customer Name, Job Reference, Contract Reference
- Site Name, Area, Region (via Region model)
- Start Date, Expiry Date
- Frequency: 3 Monthly, 6 Monthly, Annual, Custom
- PPM Tracking: Last PPM Date, Next PPM Due
- Contract Value
- Customer Contact, Customer Email
- Status: Active, Expiring, Expired, Cancelled, Upcoming, Overdue
- Notes
- **Mailing Frequency**: Daily, Weekly, Monthly, Quarterly, Yearly
- **Email Tracking**: Last Email Sent, Next Email Due

**Contract Features:**
- Full CRUD operations
- Status workflow management
- Search and filter by customer name, job ref, site name, contract ref, status, area, frequency, date range
- Export to CSV
- Automatic PPM due date calculation based on frequency
- Email notification triggers on status changes
- Activity logging
- **Mailing notification system** for scheduled customer updates

**Mailing Notification Flow:**
1. Contract has `mailing_frequency` set (daily/weekly/monthly/quarterly/yearly)
2. System tracks `next_email_due` and `last_email_sent`
3. Scheduler runs `mailing:dispatch` command every minute
4. Command checks for contracts where `next_email_due <= now` and `last_email_sent > 24 hours ago`
5. Dispatches `SendMailingNotificationJob` to queue
6. Job sends email and updates `last_email_sent` and `next_email_due`
7. Logs notification status in `notification_logs` table

---

### 4. Service Management

**Service Data Structure:**
- Job Reference, Customer Name, Area, Region
- Service Type: PPM, Repair, Installation, Service Call, Inspection
- Engineer Assignment: Engineer Name, Engineer ID (links to User model)
- Visit Date, Status: Scheduled, In Progress, Completed, Cancelled
- Site Installation, Address
- Job Details, Work Completed, Job Notes
- Recommendations, Remedial Required (yes/no), Remedial Details
- Next PPM Due
- Created By, Updated By (user tracking)
- **Mailing Frequency**: Daily, Weekly, Monthly, Quarterly, Yearly
- **Email Tracking**: Last Email Sent, Next Email Due

**Service Features:**
- Full CRUD operations
- Engineer assignment and tracking
- Status workflow (Scheduled → In Progress → Completed)
- Search and filter by job ref, customer name, engineer, service type, status, area, date range
- Export to CSV
- Remedial work tracking
- Activity logging
- **Mailing notification system** (same flow as contracts)

**Mailing Notification Flow:**
- Identical to Contract mailing system
- Jobs dispatched when `next_email_due <= now`
- Sends ServiceMailingNotification email
- Updates email timestamps and calculates next due date

---

### 5. Response Management

**Response Data Structure:**
- Job Reference, Customer Name
- Response: Accepted, Rejected, Awaiting
- Status tracking
- Notes and additional information

**Response Features:**
- Full CRUD operations
- Track customer responses to PPM reminders
- Search and filter capabilities
- Export to CSV
- Email notifications on response creation/update
- Activity logging

---

### 6. Email Notification System

**Notification Types:**

**Real-time Notifications (via NotificationService):**
- Contract created/updated/deleted
- Contract status changed
- Customer created/updated/deleted
- Customer status changed
- Service created/updated/deleted
- Response created/updated/deleted
- Response status changed

**Scheduled Reminder Notifications (via Console Commands):**
1. **PPM Reminders** (Daily at 8:00 AM)
   - Sends reminders for overdue and upcoming PPMs
   - Command: `emails:send-ppm-reminders`

2. **Customer Response Reminders** (Daily at 9:00 AM)
   - Reminds to review customer responses
   - Command: `emails:send-customer-response-reminders`

3. **No-Contract Customer Reminders** (Weekly on Monday at 10:00 AM)
   - Identifies customers without active contracts
   - Command: `emails:send-no-contract-reminders`

4. **Engineer Reminders** (Daily at 7:00 AM)
   - Sends task reminders to engineers
   - Command: `emails:send-engineer-reminders`

5. **Mailing Notifications** (Every Minute)
   - Dispatches contract/service mailing notifications based on frequency
   - Command: `mailing:dispatch`

**Email Configuration:**
- Configurable via Settings (admin only)
- Email types can be enabled/disabled individually
- Internal reminder inbox configuration
- SMTP settings in `.env` file
- Notification logs track all sent emails with status, timestamp, and error messages

**Email Sending Flow:**
1. Notification is triggered (real-time or scheduled)
2. NotificationService checks if email type is enabled
3. Sends separate emails to:
   - Admin/internal email (always sent)
   - Customer email (if available)
4. Logs status in notification_logs table
5. For scheduled mailing notifications: dispatched to queue for background processing

---

### 7. Dashboard

**Dashboard Metrics:**
- **PPM Overview**: Upcoming PPMs, Overdue PPMs, Expiring Contracts, No Contract customers
- **Totals**: Total Customers, Contracts, Services
- **Pipeline Metrics**: Scheduled, Reminder Sent, Accepted, Overdue, Completed YTD
- **Reminders**: Internal reminders pending, Customer reminders pending, Responses received
- **Area Schedule**: Upcoming 3 months grouped by area with PPM counts
- **Customer Categories**: Chain customers, New customers
- **Recent Activity**: Upcoming contracts (top 5), Recent responses (top 4)

**Dashboard Updates:**
- Real-time metrics calculated from database
- Area schedule provided by AreaScheduleService
- Responsive layout with visual metrics

---

### 8. Reports & Analytics

**Report Features:**
- Comprehensive reporting interface
- Contract analysis
- Service completion tracking
- Response tracking
- Engineer activity reports (admin only)
- Export functionality for all reports

**Export Capabilities:**
- Export Customers to CSV
- Export Contracts to CSV
- Export Services to CSV
- Export Responses to CSV
- Export Reports to CSV

---

### 9. Settings Management (Admin Only)

**Settings Categories:**
- Email notification settings (enable/disable individual email types)
- Internal reminder inbox configuration
- System-wide settings storage

**Configuration:**
- Stored in database (email_notification_settings table)
- Accessible via `/settings` route
- Toggle switches for each email type
- Real-time updates to notification behavior

---

### 10. Data Entry & Quick Actions

**Quick Create Features:**
- Quick-create customers from data entry page
- Quick-create users (admin only)
- Simplified forms for rapid data entry

**Data Entry Page:**
- Accessible via `/data-entry`
- Shows all customers for reference
- Shows active engineers for assignment
- Streamlined interface for adding contracts/services

---

### 11. Search Functionality

**Global Search:**
- Accessible via `/search`
- Searches across multiple entities
- Full-text search on key fields
- Filterable results

**Entity-Specific Filters:**
- Customers: Name, job ref, email, phone, address, status, category, region
- Contracts: Customer name, job ref, site name, contract ref, status, area, frequency, date range
- Services: Job ref, customer name, engineer, service type, status, area, date range
- Responses: Job reference, customer name, response status

---

## Scheduled Tasks (Cron Jobs)

**Laravel Scheduler Configuration:**
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**Scheduled Commands:**
- `emails:send-ppm-reminders` - Daily at 08:00
- `emails:send-customer-response-reminders` - Daily at 09:00
- `emails:send-no-contract-reminders` - Weekly on Monday at 10:00
- `emails:send-engineer-reminders` - Daily at 07:00
- `mailing:dispatch` - Every minute

---

## Queue System

**Queue Configuration:**
- Driver: Database or Redis (configurable in `.env`)
- Queue worker processes jobs in background
- Handles email sending asynchronously
- Retry mechanism with configurable attempts

**Queue Worker Setup:**
- Supervisor or Systemd recommended for production
- Runs `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- Processes SendMailingNotificationJob
- Auto-restart on failure

**Job Processing:**
- Contract and Service mailing notifications dispatched to queue
- Duplicate prevention (checks last 24 hours)
- Notification logging on success/failure
- Automatic retry on failure

---

## Database Schema

**Key Tables:**
- `users` - User accounts with roles
- `customers` - Customer information
- `contracts` - Contract details with PPM tracking
- `services` - Service visits and maintenance records
- `responses` - Customer response tracking
- `regions` - Geographic regions (counties)
- `customer_sites` - Customer site locations
- `notification_logs` - Email notification history
- `email_notification_settings` - Email configuration
- `activity_logs` - System activity tracking
- `jobs` - Queue job storage
- `failed_jobs` - Failed queue jobs

**Relationships:**
- Customer → Contracts (one-to-many)
- Customer → Services (one-to-many)
- Customer → CustomerSites (one-to-many)
- Contract → Region (many-to-one)
- Contract → CustomerSite (many-to-one)
- Service → Region (many-to-one)
- Service → CustomerSite (many-to-one)
- Service → Engineer (User) (many-to-one)
- All entities → NotificationLogs (polymorphic)

---

## Security Features

**Authentication:**
- Session-based authentication
- Password hashing (bcrypt)
- CSRF protection on all forms
- Role-based access control

**Authorization:**
- Admin routes protected by `role:admin` middleware
- Engineer routes protected by `role:admin|engineer` middleware
- Settings and user management restricted to admins

**Data Protection:**
- Input validation on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating
- Environment variables for sensitive configuration

---

## Activity Logging

**ActivityLogger Service:**
- Tracks all user actions
- Logs model changes (created, updated, deleted)
- Records user who performed action
- Timestamp for audit trail
- Stored in `activity_logs` table

---

## Development & Deployment

**Local Development:**
```bash
composer setup  # Initial setup
composer dev    # Run server, queue, logs, and vite
composer test    # Run tests
```

**Production Requirements:**
- PHP 8.2+
- MySQL database
- Composer dependencies
- Queue worker (Supervisor recommended)
- Cron job for scheduler
- SMTP server for email
- HTTPS recommended

**Environment Variables:**
- Database credentials
- SMTP configuration
- Queue driver
- Application key
- APP_URL, APP_ENV

---

## Key Workflows

### 1. Contract Lifecycle
1. Create customer (if new)
2. Create contract with PPM frequency
3. System calculates next PPM due date
4. Set mailing frequency (optional)
5. Scheduler sends PPM reminders before due date
6. Engineer completes PPM, creates service record
7. Contract next PPM date updated
8. Mailing notifications sent at configured intervals

### 2. Service Visit Workflow
1. Contract schedules PPM (status: upcoming)
2. Reminder sent to engineer
3. Engineer visits site, creates service record
4. Service status: In Progress → Completed
5. Work completed and recommendations recorded
6. If remedial required: flag and add details
7. Customer response tracked via Response model
8. Next PPM due date calculated

### 3. Email Notification Flow
1. Action triggers notification (e.g., contract updated)
2. NotificationService checks email type settings
3. Sends email to admin/internal inbox
4. Sends separate email to customer (if email available)
5. Logs notification in database
6. For mailing notifications: queued and processed by worker

### 4. Mailing Notification Automation
1. Contract/Service configured with mailing frequency
2. Initial email sent, `last_email_sent` set
3. `next_email_due` calculated based on frequency
4. Scheduler runs every minute
5. Dispatches job when `next_email_due <= now`
6. Queue worker processes job
7. Email sent, timestamps updated
8. Process repeats based on frequency

---

## Monitoring & Maintenance

**Logs:**
- Laravel logs: `storage/logs/laravel.log`
- Queue worker logs: `storage/logs/worker.log`
- PHP error logs

**Queue Monitoring:**
```bash
php artisan queue:failed    # View failed jobs
php artisan queue:retry     # Retry failed jobs
php artisan queue:flush     # Clear failed jobs
```

**Scheduler Testing:**
```bash
php artisan schedule:run    # Run scheduler manually
php artisan mailing:dispatch  # Test mailing dispatch
```

**Email Testing:**
```bash
php artisan tinker
# Test email sending via test commands
```

---

## Integration Points

**External Services:**
- SMTP server for email delivery
- Can integrate with SendGrid, Mailgun, SES for production
- Database queue or Redis for job processing

**Future Extensions:**
- SMS notifications
- API endpoints for mobile app
- Calendar integration (PPM schedules)
- Payment processing
- Document management (attachments)

---

## User Roles & Permissions Summary

| Feature | Admin | Engineer |
|---------|-------|----------|
| Dashboard | ✓ | ✓ |
| Customers (CRUD) | ✓ | ✓ |
| Contracts (CRUD) | ✓ | ✓ |
| Services (CRUD) | ✓ | ✓ |
| Responses (CRUD) | ✓ | ✓ |
| Search | ✓ | ✓ |
| Reports | ✓ | ✓ |
| Exports | ✓ | ✓ |
| Data Entry | ✓ | ✓ |
| Settings | ✓ | ✗ |
| User Management | ✓ | ✗ |
| Engineer Activity | ✓ | ✗ |

---

## Troubleshooting Common Issues

**Emails Not Sending:**
1. Check SMTP configuration in `.env`
2. Verify queue worker is running
3. Check email notification settings
4. Review notification_logs for errors
5. Check Laravel logs

**Queue Not Processing:**
1. Verify queue worker running: `ps aux | grep queue:work`
2. Check Supervisor/Systemd status
3. Restart queue worker
4. Check queue driver configuration

**Scheduled Tasks Not Running:**
1. Verify cron job is configured
2. Check cron logs: `grep CRON /var/log/syslog`
3. Test manually: `php artisan schedule:run`
4. Verify artisan permissions

---

## Summary

The Elite Tech Precision Ltd system is a comprehensive service management platform that:
- Manages customers, contracts, and service visits
- Tracks PPM schedules and engineer assignments
- Sends automated email notifications and reminders
- Provides real-time dashboard metrics
- Supports role-based access control
- Implements robust audit logging
- Handles high-volume email via queue system
- Configurable email notification types
- Export and reporting capabilities

The system is designed for precision engineering companies that need to track maintenance schedules, communicate with customers, and manage service delivery efficiently.
