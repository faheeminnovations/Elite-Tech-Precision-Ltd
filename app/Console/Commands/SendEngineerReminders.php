<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Service;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\User;
use App\Mail\EngineerReminderMail;
use Carbon\Carbon;

class SendEngineerReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-engineer-reminders {--test : Send test email instead of actual reminders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send engineer task reminder emails for assigned services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting engineer reminder check...');
        
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        
        // Get services scheduled for today
        $todayServices = Service::whereDate('visit_date', $today)
            ->where('status', 'scheduled')
            ->get();
            
        // Get services scheduled for tomorrow
        $tomorrowServices = Service::whereDate('visit_date', $tomorrow)
            ->where('status', 'scheduled')
            ->get();
        
        // Get services scheduled within next 7 days
        $weekServices = Service::where('visit_date', '>=', $today)
            ->where('visit_date', '<=', Carbon::today()->addDays(7))
            ->where('status', 'scheduled')
            ->get();
        
        $this->info("Found {$todayServices->count()} services scheduled for today");
        $this->info("Found {$tomorrowServices->count()} services scheduled for tomorrow");
        $this->info("Found {$weekServices->count()} services scheduled within 7 days");
        
        if ($this->option('test')) {
            $this->info('Test mode: Sending test email to support@devfaheem.com');
            
            // Send test email
            Mail::raw("Engineer Reminder Test\n\nServices today: {$todayServices->count()}\nServices tomorrow: {$tomorrowServices->count()}\nServices this week: {$weekServices->count()}\n\nTest completed at: " . now()->format('Y-m-d H:i:s'), function ($message) {
                $message->to('support@devfaheem.com')
                    ->subject('EliteFlow Engineer Reminder Test')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info('✓ Test email sent successfully!');
            return 0;
        }
        
        // Get engineers (users with engineer role)
        $engineers = User::engineers()->active()->get();
        
        if ($engineers->isEmpty()) {
            $this->warn('No engineers found in the system');
            return 0;
        }
        
        $this->info("Found {$engineers->count()} engineers");
        
        // Send daily summary to each engineer
        foreach ($engineers as $engineer) {
            // Get services assigned to this engineer for today
            $engineerTodayServices = $todayServices->filter(function ($service) use ($engineer) {
                return $service->engineer_id == $engineer->id;
            });
            
            if ($engineerTodayServices->count() > 0) {
                try {
                    // Send individual task reminders for today
                    foreach ($engineerTodayServices as $service) {
                        $contract = Contract::where('job_ref', $service->job_ref)->first();
                        $customer = $contract ? Customer::where('name', $service->customer_name)->first() : null;
                        
                        Mail::to($engineer->email)->send(new EngineerReminderMail(
                            $engineer, 
                            $service, 
                            $contract, 
                            $customer, 
                            'reminder',
                            $engineerTodayServices->count()
                        ));
                    }
                    
                    $this->info("✓ Sent daily reminders to engineer {$engineer->name} ({$engineerTodayServices->count()} tasks)");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send reminders to engineer {$engineer->name}: {$e->getMessage()}");
                }
            }
            
            // Send reminders for tomorrow's tasks
            $engineerTomorrowServices = $tomorrowServices->filter(function ($service) use ($engineer) {
                return $service->engineer_id == $engineer->id;
            });
            
            if ($engineerTomorrowServices->count() > 0) {
                try {
                    foreach ($engineerTomorrowServices as $service) {
                        $contract = Contract::where('job_ref', $service->job_ref)->first();
                        $customer = $contract ? Customer::where('name', $service->customer_name)->first() : null;
                        
                        Mail::to($engineer->email)->send(new EngineerReminderMail(
                            $engineer, 
                            $service, 
                            $contract, 
                            $customer, 
                            'reminder',
                            $engineerTomorrowServices->count()
                        ));
                    }
                    
                    $this->info("✓ Sent tomorrow's reminders to engineer {$engineer->name} ({$engineerTomorrowServices->count()} tasks)");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send tomorrow's reminders to engineer {$engineer->name}: {$e->getMessage()}");
                }
            }
        }
        
        // Also send summary to internal email
        $internalEmail = config('app.internal_reminder_email', 'support@devfaheem.com');
        $internalEmails = explode(',', $internalEmail);
        try {
            foreach ($internalEmails as $email) {
                Mail::raw("Engineer Task Summary\n\nTotal services today: {$todayServices->count()}\nTotal services tomorrow: {$tomorrowServices->count()}\nTotal services this week: {$weekServices->count()}\nEngineers notified: {$engineers->count()}\n\nSummary generated at: " . now()->format('Y-m-d H:i:s'), function ($message) use ($email) {
                    $message->to(trim($email))
                        ->subject('EliteFlow Engineer Task Summary')
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            }
            
            $this->info("✓ Sent summary to internal email: {$internalEmail}");
        } catch (\Exception $e) {
            $this->error("✗ Failed to send summary to internal email: {$e->getMessage()}");
        }
        
        $this->info('Engineer reminder check completed successfully!');
        
        return 0;
    }
}