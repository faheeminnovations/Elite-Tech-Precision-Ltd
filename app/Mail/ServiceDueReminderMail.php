<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceDueReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $service;
    public $contract;
    public $customer;

    /**
     * Create a new message instance.
     */
    public function __construct($service, $contract, $customer)
    {
        $this->service = $service;
        $this->contract = $contract;
        $this->customer = $customer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "EliteFlow: Service Due Reminder - {$this->service->job_ref}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.service-due-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
