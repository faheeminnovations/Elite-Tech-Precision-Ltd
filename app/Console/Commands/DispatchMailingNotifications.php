<?php

namespace App\Console\Commands;

use App\Jobs\SendMailingNotificationJob;
use App\Models\Contract;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchMailingNotifications extends Command
{
    protected $signature = 'mailing:dispatch';
    protected $description = 'Dispatch mailing notifications for contracts, services, and customers due for email';

    public function handle()
    {
        $this->info('Starting mailing notification dispatch...');

        $dispatchedCount = 0;

        // Process customers
        $customers = Customer::whereNotNull('mailing_frequency')
            ->whereNotNull('next_email_due')
            ->where('next_email_due', '<=', Carbon::now())
            ->where(function ($query) {
                $query->whereNull('last_email_sent')
                    ->orWhere('last_email_sent', '<', Carbon::now()->subHours(24));
            })
            ->get();

        foreach ($customers as $customer) {
            try {
                SendMailingNotificationJob::dispatch($customer, 'customer');
                $dispatchedCount++;
                $this->info("Dispatched customer notification: {$customer->name}");
            } catch (\Exception $e) {
                $this->error("Failed to dispatch customer notification for {$customer->name}: {$e->getMessage()}");
                Log::error("Failed to dispatch customer notification", [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Process contracts
        $contracts = Contract::whereNotNull('mailing_frequency')
            ->whereNotNull('next_email_due')
            ->where('next_email_due', '<=', Carbon::now())
            ->where(function ($query) {
                $query->whereNull('last_email_sent')
                    ->orWhere('last_email_sent', '<', Carbon::now()->subHours(24));
            })
            ->get();

        foreach ($contracts as $contract) {
            try {
                SendMailingNotificationJob::dispatch($contract, 'contract');
                $dispatchedCount++;
                $this->info("Dispatched contract notification: {$contract->contract_ref}");
            } catch (\Exception $e) {
                $this->error("Failed to dispatch contract notification for {$contract->contract_ref}: {$e->getMessage()}");
                Log::error("Failed to dispatch contract notification", [
                    'contract_id' => $contract->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Process services
        $services = Service::whereNotNull('mailing_frequency')
            ->whereNotNull('next_email_due')
            ->where('next_email_due', '<=', Carbon::now())
            ->where(function ($query) {
                $query->whereNull('last_email_sent')
                    ->orWhere('last_email_sent', '<', Carbon::now()->subHours(24));
            })
            ->get();

        foreach ($services as $service) {
            try {
                SendMailingNotificationJob::dispatch($service, 'service');
                $dispatchedCount++;
                $this->info("Dispatched service notification: {$service->job_ref}");
            } catch (\Exception $e) {
                $this->error("Failed to dispatch service notification for {$service->job_ref}: {$e->getMessage()}");
                Log::error("Failed to dispatch service notification", [
                    'service_id' => $service->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Mailing notification dispatch completed. Total dispatched: {$dispatchedCount}");
        
        return Command::SUCCESS;
    }
}
