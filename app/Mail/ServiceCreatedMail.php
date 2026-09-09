<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $service;
    public $contract;
    public $customer;
    public $recipientType;

    /**
     * Create a new message instance.
     */
    public function __construct($service, $contract, $customer, $recipientType = 'customer')
    {
        $this->service = $service;
        $this->contract = $contract;
        $this->customer = $customer;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'customer' 
            ? "Service Scheduled - {$this->service->job_ref}"
            : "Service Created - {$this->service->job_ref}";
            
        return new Envelope(
            subject: "EliteFlow: {$subject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.service-created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}