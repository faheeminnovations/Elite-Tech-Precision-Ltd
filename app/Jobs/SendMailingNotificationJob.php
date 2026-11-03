<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Models\Service;
use App\Models\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContractMailingNotification;
use App\Mail\ServiceMailingNotification;
use Carbon\Carbon;

class SendMailingNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notifiable;
    protected $type;

    public function __construct($notifiable, string $type)
    {
        $this->notifiable = $notifiable;
        $this->type = $type;
    }

    public function handle()
    {
        $recipientEmail = null;
        $mailClass = null;

        if ($this->type === 'contract' && $this->notifiable instanceof Contract) {
            $recipientEmail = $this->notifiable->customer_email ?? $this->notifiable->customer?->email;
            $mailClass = ContractMailingNotification::class;
        } elseif ($this->type === 'service' && $this->notifiable instanceof Service) {
            $recipientEmail = $this->notifiable->customer_email ?? $this->notifiable->customer?->email;
            $mailClass = ServiceMailingNotification::class;
        }

        if (!$recipientEmail) {
            $this->logNotification('failed', 'No recipient email found');
            return;
        }

        // Check for duplicate notification (prevent sending same email twice)
        $existingLog = NotificationLog::where('notifiable_type', get_class($this->notifiable))
            ->where('notifiable_id', $this->notifiable->id)
            ->where('status', 'sent')
            ->where('sent_at', '>=', Carbon::now()->subHours(24))
            ->first();

        if ($existingLog) {
            $this->logNotification('skipped', 'Duplicate notification within 24 hours');
            return;
        }

        try {
            Mail::to($recipientEmail)->send(new $mailClass($this->notifiable));

            // Update last_email_sent and calculate next_email_due
            $this->notifiable->update([
                'last_email_sent' => Carbon::now(),
                'next_email_due' => $this->calculateNextEmailDue($this->notifiable->mailing_frequency),
            ]);

            $this->logNotification('sent', null);
        } catch (\Exception $e) {
            $this->logNotification('failed', $e->getMessage());
            throw $e;
        }
    }

    protected function calculateNextEmailDue(?string $frequency): ?Carbon
    {
        if (!$frequency) {
            return null;
        }

        return match($frequency) {
            'daily' => Carbon::now()->addDay(),
            'weekly' => Carbon::now()->addWeek(),
            'monthly' => Carbon::now()->addMonth(),
            'quarterly' => Carbon::now()->addMonths(3),
            'yearly' => Carbon::now()->addYear(),
            default => null,
        };
    }

    protected function logNotification(string $status, ?string $errorMessage = null): void
    {
        NotificationLog::create([
            'notifiable_type' => get_class($this->notifiable),
            'notifiable_id' => $this->notifiable->id,
            'recipient_email' => $this->notifiable->customer_email ?? $this->notifiable->customer?->email,
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'sent' ? Carbon::now() : null,
            'scheduled_at' => $this->notifiable->next_email_due,
        ]);
    }
}
