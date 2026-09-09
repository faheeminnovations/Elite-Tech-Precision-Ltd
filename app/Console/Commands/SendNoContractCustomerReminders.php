<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;
use App\Models\Contract;
use App\Mail\NoContractCustomerMail;
use Carbon\Carbon;

class SendNoContractCustomerReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-no-contract-reminders {--test : Send test email instead of actual reminders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for customers with no active contracts (sales follow-up candidates)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting no-contract customer check...');
        
        // Get all customers
        $allCustomers = Customer::all();
        
        // Get customers with active contracts
        $customersWithContracts = [];
        foreach ($allCustomers as $customer) {
            $hasActiveContract = Contract::where('customer_name', $customer->name)
                ->where('status', 'active')
                ->exists();
            if ($hasActiveContract) {
                $customersWithContracts[] = $customer->id;
            }
        }
        
        // Find customers without active contracts
        $customersWithoutContracts = $allCustomers->whereNotIn('id', $customersWithContracts);
        
        $this->info("Found {$customersWithoutContracts->count()} customers with no active contract");
        
        if ($this->option('test')) {
            $this->info('Test mode: Sending test email to support@devfaheem.com');
            
            // Send test email
            Mail::raw("No-Contract Customer Reminder Test\n\nCustomers with no contract: {$customersWithoutContracts->count()}\nThese are potential sales follow-up candidates.\n\nTest completed at: " . now()->format('Y-m-d H:i:s'), function ($message) {
                $message->to('support@devfaheem.com')
                    ->subject('EliteFlow No-Contract Customer Test')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info('✓ Test email sent successfully!');
            return 0;
        }
        
        $internalEmail = config('app.internal_reminder_email', 'support@devfaheem.com');
        $internalEmails = explode(',', $internalEmail);
        
        // Send reminders for each customer without contract
        foreach ($customersWithoutContracts as $customer) {
            // Try to get last service date from services
            $lastService = \App\Models\Service::where('customer_name', $customer->name)
                ->orderBy('created_at', 'desc')
                ->first();
            $lastServiceDate = $lastService ? $lastService->created_at : null;
            
            try {
                foreach ($internalEmails as $email) {
                    Mail::to(trim($email))->send(new NoContractCustomerMail($customer, $lastServiceDate, 'sales', $customersWithoutContracts->count()));
                }
                $this->info("✓ Sent no-contract reminder for customer {$customer->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send no-contract reminder for customer {$customer->name}: {$e->getMessage()}");
            }
        }
        
        $this->info('No-contract customer reminder check completed successfully!');
        
        return 0;
    }
}