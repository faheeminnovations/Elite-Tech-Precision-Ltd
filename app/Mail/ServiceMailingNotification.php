<?php

namespace App\Mail;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceMailingNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Service Notification - ' . $this->service->job_ref,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service_mailing_notification',
            with: [
                'service' => $this->service,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
