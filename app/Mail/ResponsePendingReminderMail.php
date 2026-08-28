<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResponsePendingReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $response;
    public $contract;
    public $customer;
    public $daysPending;

    /**
     * Create a new message instance.
     */
    public function __construct($response, $contract, $customer, $daysPending)
    {
        $this->response = $response;
        $this->contract = $contract;
        $this->customer = $customer;
        $this->daysPending = $daysPending;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "EliteFlow: Pending Customer Response - {$this->response->job_ref}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.response-pending-reminder',
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
