<?php

namespace App\Console\Commands;

use App\Jobs\ContractEmailNotification;
use App\Jobs\ServiceEmailNotification;
use App\Models\Contract;
use App\Models\Service;
use App\Models\NotificationLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DispatchEmailNotifications extends Command
{
    protected $signature = 'emails:dispatch';
    protected $description = 'Dispatch email notifications for contracts and services due for mailing';

    public function handle()
    {
        $this->info('Starting email notification dispatch...');

        $now = now();
        $contractsDispatched = 0;
        $servicesDispatched = 0;

        DB::beginTransaction();
        try {
            // Dispatch contract emails
            $contracts = Contract::where('mailing_frequency', '!=', null)
                ->where('mailing_frequency', '!=', '')
                ->where(function ($query) use ($now) {
                    $query->whereNull('next_email_due')
                        ->orWhere('next_email_due', '<=', $now);
                })
                ->where('customer_email', '!=', null)
                ->where('customer_email', '!=', '')
                ->whereDoesntHave('notificationLogs', function ($query) use ($now) {
                    $query->where('status', NotificationLog::STATUS_PENDING)
                        ->where('scheduled_for', '>=', $now->subMinutes(5));
                })
                ->lockForUpdate()
                ->get();

            foreach ($contracts as $contract) {
                // Check if a notification was already sent today to prevent duplicates
                $recentNotification = NotificationLog::where('notifiable_type', Contract::class)
                    ->where('notifiable_id', $contract->id)
                    ->where('status', NotificationLog::STATUS_SENT)
                    ->where('sent_at', '>=', $now->startOfDay())
                    ->exists();

                if (!$recentNotification) {
                    ContractEmailNotification::dispatch($contract, $contract->customer_email);
                    $contractsDispatched++;
                    $this->line("Dispatched email for contract: {$contract->contract_ref}");
                }
            }

            // Dispatch service emails
            $services = Service::where('mailing_frequency', '!=', null)
                ->where('mailing_frequency', '!=', '')
                ->where(function ($query) use ($now) {
                    $query->whereNull('next_email_due')
                        ->orWhere('next_email_due', '<=', $now);
                })
                ->whereDoesntHave('notificationLogs', function ($query) use ($now) {
                    $query->where('status', NotificationLog::STATUS_PENDING)
                        ->where('scheduled_for', '>=', $now->subMinutes(5));
                })
                ->lockForUpdate()
                ->get();

            foreach ($services as $service) {
                // Get customer email from customer model
                $customer = \App\Models\Customer::where('name', $service->customer_name)->first();
                $recipientEmail = $customer?->email;

                if ($recipientEmail) {
                    // Check if a notification was already sent today to prevent duplicates
                    $recentNotification = NotificationLog::where('notifiable_type', Service::class)
                        ->where('notifiable_id', $service->id)
                        ->where('status', NotificationLog::STATUS_SENT)
                        ->where('sent_at', '>=', $now->startOfDay())
                        ->exists();

                    if (!$recentNotification) {
                        ServiceEmailNotification::dispatch($service, $recipientEmail);
                        $servicesDispatched++;
                        $this->line("Dispatched email for service: {$service->job_ref}");
                    }
                }
            }

            DB::commit();

            $this->info("Email dispatch completed: {$contractsDispatched} contracts, {$servicesDispatched} services");
            Log::info('Email notification dispatch completed', [
                'contracts' => $contractsDispatched,
                'services' => $servicesDispatched,
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to dispatch email notifications: ' . $e->getMessage());
            Log::error('Email notification dispatch failed', [
                'error' => $e->getMessage(),
            ]);
            return Command::FAILURE;
        }
    }
}
