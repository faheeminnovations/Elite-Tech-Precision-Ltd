<?php

namespace App\Jobs;

use App\Models\Service;
use App\Models\NotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceNotificationMail;

class ServiceEmailNotification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 900];

    public function __construct(
        public Service $service,
        public string $recipientEmail
    ) {
    }

    public function handle(): void
    {
        try {
            $notificationLog = NotificationLog::create([
                'notifiable_type' => Service::class,
                'notifiable_id' => $this->service->id,
                'recipient_email' => $this->recipientEmail,
                'subject' => 'Service Notification - ' . $this->service->job_ref,
                'status' => NotificationLog::STATUS_PENDING,
                'scheduled_for' => now(),
            ]);

            Mail::to($this->recipientEmail)->send(new ServiceNotificationMail($this->service));

            $notificationLog->update([
                'status' => NotificationLog::STATUS_SENT,
                'sent_at' => now(),
            ]);

            $this->service->update([
                'last_email_sent' => now(),
                'next_email_due' => $this->calculateNextEmailDate(),
            ]);

            Log::info('Service email sent successfully', [
                'service_id' => $this->service->id,
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

            Log::error('Failed to send service email', [
                'service_id' => $this->service->id,
                'email' => $this->recipientEmail,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function calculateNextEmailDate(): ?\Carbon\Carbon
    {
        if (!$this->service->mailing_frequency) {
            return null;
        }

        return match($this->service->mailing_frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly' => now()->addYear(),
            default => null,
        };
    }
}
