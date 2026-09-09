<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:test-notifications {email? : Optional email to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notification system for both customer and admin recipients';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Testing Email Notification System');
        $this->info('================================');

        // Get test email or use config email
        $testEmail = $this->argument('email') ?? config('mail.from.address');
        $this->info("Using test email: {$testEmail}");

        // Get admin user
        $admin = User::where('email', 'admin-elitetechprecision@devfaheem.com')->first();
        if (!$admin) {
            $this->error('Admin user not found. Please run database seeder first.');
            return Command::FAILURE;
        }

        // Create test data objects
        $testCustomer = (object) [
            'id' => 1,
            'name' => 'Test Customer',
            'email' => $testEmail,
            'status' => 'active',
        ];

        $testContract = (object) [
            'id' => 1,
            'job_ref' => 'TEST-001',
            'customer_name' => 'Test Customer',
            'customer_email' => $testEmail,
            'area' => 'Test Area',
            'frequency' => 'Monthly',
            'status' => 'active',
        ];

        $testResponse = (object) [
            'id' => 1,
            'job_ref' => 'TEST-001',
            'customer_name' => 'Test Customer',
            'customer_email' => $testEmail,
            'service_type' => 'PPM',
            'response' => 'Approved',
            'status' => 'active',
        ];

        $this->info("\nTesting customer notifications...");
        $this->info('------------------------------');

        try {
            $this->info('1. Testing Customer Created notification...');
            $notificationService->sendCustomerCreated($testCustomer, $admin);
            $this->info('✓ Customer Created notification sent');

            $this->info('2. Testing Customer Updated notification...');
            $notificationService->sendCustomerUpdated($testCustomer, $admin);
            $this->info('✓ Customer Updated notification sent');

            $this->info('3. Testing Customer Status Changed notification...');
            $notificationService->sendCustomerStatusChanged($testCustomer, 'active', 'inactive', $admin);
            $this->info('✓ Customer Status Changed notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Customer notification test failed: " . $e->getMessage());
        }

        $this->info("\nTesting contract notifications...");
        $this->info('------------------------------');

        try {
            $this->info('1. Testing Contract Created notification...');
            $notificationService->sendContractCreated($testContract, $admin);
            $this->info('✓ Contract Created notification sent');

            $this->info('2. Testing Contract Updated notification...');
            $notificationService->sendContractUpdated($testContract, $admin);
            $this->info('✓ Contract Updated notification sent');

            $this->info('3. Testing Contract Status Changed notification...');
            $notificationService->sendContractStatusChanged($testContract, 'active', 'pending', $admin);
            $this->info('✓ Contract Status Changed notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Contract notification test failed: " . $e->getMessage());
        }

        $this->info("\nTesting response notifications...");
        $this->info('------------------------------');

        try {
            $this->info('1. Testing Response Created notification...');
            $notificationService->sendResponseCreated($testResponse, $admin);
            $this->info('✓ Response Created notification sent');

            $this->info('2. Testing Response Updated notification...');
            $notificationService->sendResponseUpdated($testResponse, $admin);
            $this->info('✓ Response Updated notification sent');

            $this->info('3. Testing Response Status Changed notification...');
            $notificationService->sendResponseStatusChanged($testResponse, 'active', 'completed', $admin);
            $this->info('✓ Response Status Changed notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Response notification test failed: " . $e->getMessage());
        }

        $this->info("\nTesting service notifications...");
        $this->info('------------------------------');

        $testService = (object) [
            'id' => 1,
            'name' => 'Test Service',
            'customer_email' => $testEmail,
            'status' => 'scheduled',
        ];

        try {
            $this->info('1. Testing Service Created notification...');
            $notificationService->sendServiceCreated($testService, $admin);
            $this->info('✓ Service Created notification sent');

            $this->info('2. Testing Service Updated notification...');
            $notificationService->sendServiceUpdated($testService, $admin);
            $this->info('✓ Service Updated notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Service notification test failed: " . $e->getMessage());
        }

        $this->info("\n================================");
        $this->info('Email notification test completed!');
        $this->info("Please check both {$testEmail} and " . config('mail.from.address') . " for emails.");
        $this->info("Each notification should be sent to both the customer ({$testEmail}) and admin (".config('mail.from.address').").");

        return Command::SUCCESS;
    }
}