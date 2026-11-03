<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Models\NotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContractNotificationMail;

class ContractEmailNotification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 900];

    public function __construct(
        public Contract $contract,
        public string $recipientEmail
    ) {
    }

    public function handle(): void
    {
        try {
            $notificationLog = NotificationLog::create([
                'notifiable_type' => Contract::class,
                'notifiable_id' => $this->contract->id,
                'recipient_email' => $this->recipientEmail,
                'subject' => 'Contract Notification - ' . $this->contract->contract_ref,
                'status' => NotificationLog::STATUS_PENDING,
                'scheduled_for' => now(),
            ]);

            Mail::to($this->recipientEmail)->send(new ContractNotificationMail($this->contract));

            $notificationLog->update([
                'status' => NotificationLog::STATUS_SENT,
                'sent_at' => now(),
            ]);

            $this->contract->update([
                'last_email_sent' => now(),
                'next_email_due' => $this->calculateNextEmailDate(),
            ]);

            Log::info('Contract email sent successfully', [
                'contract_id' => $this->contract->id,
                'email' => $this->recipientEmail,
            ]);

        } catch (\Exception $e) {
            if (isset($notificationLog)) {
                $notificationLog->update([
                    'status' => NotificationLog::STATUS_FAILED,
                    'error_message' => $e->getMessage(),
                    'retry_count' => $notificationLog->retry_count + 1,
                ]);
            }

            Log::error('Failed to send contract email', [
                'contract_id' => $this->contract->id,
                'email' => $this->recipientEmail,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function calculateNextEmailDate(): ?\Carbon\Carbon
    {
        if (!$this->contract->mailing_frequency) {
            return null;
        }

        return match($this->contract->mailing_frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly' => now()->addYear(),
            default => null,
        };
    }
}
