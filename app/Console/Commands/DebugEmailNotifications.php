<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\EmailNotificationSetting;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DebugEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:debug {email? : Email to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug email notification system to identify issues';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Debugging Email Notification System');
        $this->info('====================================');

        $testEmail = $this->argument('email') ?? config('mail.from.address');
        $this->info("Test email: {$testEmail}");

        // Check email notification settings
        $this->info("\n1. Checking Email Notification Settings:");
        $this->info("---------------------------------------");

        $settings = EmailNotificationSetting::all();
        $this->info("Total notification types: " . $settings->count());

        $customerSettings = $settings->filter(fn($s) => str_starts_with($s->email_type, 'customer_'));
        $this->info("Customer notification types: " . $customerSettings->count());

        foreach ($customerSettings as $setting) {
            $status = $setting->is_enabled ? '✓ ENABLED' : '✗ DISABLED';
            $this->info("  {$setting->email_type}: {$status}");
        }

        // Check notification service status
        $this->info("\n2. Checking Notification Service Status:");
        $this->info("-------------------------------------------");

        $this->info("Global enabled: " . ($notificationService->isEnabled() ? '✓ YES' : '✗ NO'));
        $this->info("Customer created enabled: " . ($notificationService->isEmailTypeEnabled('customer_created') ? '✓ YES' : '✗ NO'));
        $this->info("Customer updated enabled: " . ($notificationService->isEmailTypeEnabled('customer_updated') ? '✓ YES' : '✗ NO'));
        $this->info("Customer status changed enabled: " . ($notificationService->isEmailTypeEnabled('customer_status_changed') ? '✓ YES' : '✗ NO'));

        // Check mail configuration
        $this->info("\n3. Checking Mail Configuration:");
        $this->info("--------------------------------");

        $this->info("MAIL_MAILER: " . config('mail.mailer'));
        $this->info("MAIL_HOST: " . config('mail.host'));
        $this->info("MAIL_PORT: " . config('mail.port'));
        $this->info("MAIL_FROM_ADDRESS: " . config('mail.from.address'));
        $this->info("QUEUE_CONNECTION: " . config('queue.default'));

        // Test with a sample customer
        $this->info("\n4. Testing Customer Status Change Notification:");
        $this->info("--------------------------------------------------");

        $testCustomer = Customer::first();
        if (!$testCustomer) {
            $this->warn("No customers found in database. Creating test customer...");
            $testCustomer = Customer::create([
                'name' => 'Test Customer',
                'email' => $testEmail,
                'status' => 'active',
                'phone' => '1234567890',
            ]);
        }

        $this->info("Test customer: " . $testCustomer->name);
        $this->info("Customer email: " . ($testCustomer->email ?? 'NULL'));
        $this->info("Customer status: " . $testCustomer->status);

        if (empty($testCustomer->email)) {
            $this->warn("⚠ Customer has no email address!");
            $this->info("Updating customer with test email...");
            $testCustomer->email = $testEmail;
            $testCustomer->save();
        }

        $this->info("\nAttempting to send customer status change notification...");

        try {
            // Enable logging
            Log::info('Starting customer status change test');

            $notificationService->sendCustomerStatusChanged(
                $testCustomer,
                'active',
                'inactive',
                auth()->user() ?? \App\Models\User::first()
            );

            $this->info("✓ Notification method called successfully");
            $this->info("Check Laravel logs for detailed information: storage/logs/laravel.log");

        } catch (\Exception $e) {
            $this->error("✗ Error sending notification: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            Log::error('Email notification failed: ' . $e->getMessage());
        }

        // Check recent logs
        $this->info("\n5. Recent Laravel Log Entries:");
        $this->info("-------------------------------");

        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logs = file($logFile);
            $recentLogs = array_slice($logs, -10); // Last 10 lines
            foreach ($recentLogs as $log) {
                $this->info(trim($log));
            }
        } else {
            $this->warn("Log file not found: {$logFile}");
        }

        $this->info("\n====================================");
        $this->info("Debug completed. Check the output above for issues.");

        return Command::SUCCESS;
    }
}