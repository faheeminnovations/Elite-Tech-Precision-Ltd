<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Response;
use App\Models\Contract;
use App\Models\Customer;
use App\Mail\CustomerResponseReviewMail;
use Carbon\Carbon;

class SendCustomerResponseReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-customer-response-reminders {--test : Send test email instead of actual reminders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send customer response review reminder emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting customer response reminder check...');
        
        // Get responses that need review
        $pendingResponses = Response::whereIn('response', ['no_response', 'awaiting'])->get();
        $acceptedResponses = Response::where('response', 'accepted')->get();
        $declinedResponses = Response::where('response', 'declined')->get();
        
        $totalResponses = $pendingResponses->count() + $acceptedResponses->count() + $declinedResponses->count();
        
        $this->info("Found {$pendingResponses->count()} pending responses");
        $this->info("Found {$acceptedResponses->count()} accepted responses");
        $this->info("Found {$declinedResponses->count()} declined responses");
        $this->info("Total responses needing review: {$totalResponses}");
        
        if ($this->option('test')) {
            $this->info('Test mode: Sending test email to support@devfaheem.com');
            
            // Send test email
            Mail::raw("Customer Response Reminder Test\n\nPending Responses: {$pendingResponses->count()}\nAccepted Responses: {$acceptedResponses->count()}\nDeclined Responses: {$declinedResponses->count()}\nTotal: {$totalResponses}\n\nTest completed at: " . now()->format('Y-m-d H:i:s'), function ($message) {
                $message->to('support@devfaheem.com')
                    ->subject('EliteFlow Customer Response Reminder Test')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info('✓ Test email sent successfully!');
            return 0;
        }
        
        $internalEmail = config('app.internal_reminder_email', 'support@devfaheem.com');
        
        // Send reminders for pending responses
        foreach ($pendingResponses as $response) {
            $contract = Contract::where('job_ref', $response->job_ref)->first();
            $customer = $contract ? Customer::find($contract->customer_id) : null;
            
            try {
                Mail::to($internalEmail)->send(new CustomerResponseReviewMail($response, $contract, $customer, 'pending', $totalResponses));
                $this->info("✓ Sent pending response reminder for response ID {$response->id}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send pending response reminder for response ID {$response->id}: {$e->getMessage()}");
            }
        }
        
        // Send reminders for accepted responses
        foreach ($acceptedResponses as $response) {
            $contract = Contract::where('job_ref', $response->job_ref)->first();
            $customer = $contract ? Customer::find($contract->customer_id) : null;
            
            try {
                Mail::to($internalEmail)->send(new CustomerResponseReviewMail($response, $contract, $customer, 'accepted', $totalResponses));
                $this->info("✓ Sent accepted response reminder for response ID {$response->id}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send accepted response reminder for response ID {$response->id}: {$e->getMessage()}");
            }
        }
        
        // Send reminders for declined responses
        foreach ($declinedResponses as $response) {
            $contract = Contract::where('job_ref', $response->job_ref)->first();
            $customer = $contract ? Customer::find($contract->customer_id) : null;
            
            try {
                Mail::to($internalEmail)->send(new CustomerResponseReviewMail($response, $contract, $customer, 'declined', $totalResponses));
                $this->info("✓ Sent declined response reminder for response ID {$response->id}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send declined response reminder for response ID {$response->id}: {$e->getMessage()}");
            }
        }
        
        $this->info('Customer response reminder check completed successfully!');
        
        return 0;
    }
}