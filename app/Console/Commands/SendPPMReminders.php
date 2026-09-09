<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Contract;
use App\Models\Customer;
use App\Mail\OverduePPMMail;
use App\Mail\PPMDueWithin30DaysMail;
use Carbon\Carbon;

class SendPPMReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-ppm-reminders {--test : Send test email instead of actual reminders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send PPM reminder emails for overdue and upcoming PPMs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting PPM reminder check...');
        
        $today = Carbon::today();
        $thirtyDaysFromNow = Carbon::today()->addDays(30);
        
        // Get overdue PPMs
        $overdueContracts = Contract::where('next_ppm_due', '<', $today)
            ->where('status', 'active')
            ->get();
            
        // Get PPMs due within 30 days
        $upcomingContracts = Contract::where('next_ppm_due', '>=', $today)
            ->where('next_ppm_due', '<=', $thirtyDaysFromNow)
            ->where('status', 'active')
            ->get();
        
        $this->info("Found {$overdueContracts->count()} overdue PPMs");
        $this->info("Found {$upcomingContracts->count()} PPMs due within 30 days");
        
        if ($this->option('test')) {
            $this->info('Test mode: Sending test email to support@devfaheem.com');
            
            // Send test email
            Mail::raw("PPM Reminder Test\n\nOverdue PPMs: {$overdueContracts->count()}\nUpcoming PPMs: {$upcomingContracts->count()}\n\nTest completed at: " . now()->format('Y-m-d H:i:s'), function ($message) {
                $message->to('support@devfaheem.com')
                    ->subject('EliteFlow PPM Reminder Test')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info('✓ Test email sent successfully!');
            return 0;
        }
        
        $internalEmail = config('app.internal_reminder_email', 'support@devfaheem.com');
        $internalEmails = explode(',', $internalEmail);
        
        // Send overdue PPM reminders
        foreach ($overdueContracts as $contract) {
            $customer = Customer::find($contract->customer_id);
            $overdueDays = $today->diffInDays($contract->next_ppm_due);
            
            // Send internal reminder
            try {
                foreach ($internalEmails as $email) {
                    Mail::to(trim($email))->send(new OverduePPMMail($contract, $customer, $overdueDays, 'internal'));
                }
                $this->info("✓ Sent overdue PPM reminder for {$contract->job_ref} to internal");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send overdue PPM reminder for {$contract->job_ref}: {$e->getMessage()}");
            }
            
            // Send customer reminder if email exists
            if ($customer && $customer->email) {
                try {
                    Mail::to($customer->email)->send(new OverduePPMMail($contract, $customer, $overdueDays, 'customer'));
                    $this->info("✓ Sent overdue PPM reminder for {$contract->job_ref} to customer");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send customer overdue PPM reminder for {$contract->job_ref}: {$e->getMessage()}");
                }
            }
        }
        
        // Send upcoming PPM reminders
        foreach ($upcomingContracts as $contract) {
            $customer = Customer::find($contract->customer_id);
            $daysUntilDue = $contract->next_ppm_due->diffInDays($today);
            
            // Send internal reminder
            try {
                foreach ($internalEmails as $email) {
                    Mail::to(trim($email))->send(new PPMDueWithin30DaysMail($contract, $customer, $daysUntilDue, 'internal', $upcomingContracts->count()));
                }
                $this->info("✓ Sent upcoming PPM reminder for {$contract->job_ref} to internal");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send upcoming PPM reminder for {$contract->job_ref}: {$e->getMessage()}");
            }
            
            // Send customer reminder if email exists
            if ($customer && $customer->email) {
                try {
                    Mail::to($customer->email)->send(new PPMDueWithin30DaysMail($contract, $customer, $daysUntilDue, 'customer', $upcomingContracts->count()));
                    $this->info("✓ Sent upcoming PPM reminder for {$contract->job_ref} to customer");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send customer upcoming PPM reminder for {$contract->job_ref}: {$e->getMessage()}");
                }
            }
        }
        
        $this->info('PPM reminder check completed successfully!');
        
        return 0;
    }
}