<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractExpiryReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contract;
    public $customer;
    public $daysUntilExpiry;

    /**
     * Create a new message instance.
     */
    public function __construct($contract, $customer, $daysUntilExpiry)
    {
        $this->contract = $contract;
        $this->customer = $customer;
        $this->daysUntilExpiry = $daysUntilExpiry;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "EliteFlow: Contract Expiry Reminder - {$this->contract->job_ref}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-expiry-reminder',
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
