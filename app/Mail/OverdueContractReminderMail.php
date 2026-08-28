<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverdueContractReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contract;
    public $customer;
    public $daysOverdue;

    /**
     * Create a new message instance.
     */
    public function __construct($contract, $customer, $daysOverdue)
    {
        $this->contract = $contract;
        $this->customer = $customer;
        $this->daysOverdue = $daysOverdue;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "EliteFlow: OVERDUE Contract Alert - {$this->contract->job_ref}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.overdue-contract-reminder',
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
