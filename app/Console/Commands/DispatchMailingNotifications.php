<?php

namespace App\Console\Commands;

use App\Jobs\SendMailingNotificationJob;
use App\Models\Contract;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchMailingNotifications extends Command
{
    protected $signature = 'mailing:dispatch';
    protected $description = 'Dispatch mailing notifications for contracts and services due for email';

    public function handle()
    {
        $this->info('Starting mailing notification dispatch...');

        $dispatchedCount = 0;

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
