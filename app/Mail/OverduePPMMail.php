<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverduePPMMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contract;
    public $customer;
    public $overdueDays;
    public $reminderType;

    /**
     * Create a new message instance.
     */
    public function __construct($contract, $customer, $overdueDays, $reminderType = 'internal')
    {
        $this->contract = $contract;
        $this->customer = $customer;
        $this->overdueDays = $overdueDays;
        $this->reminderType = $reminderType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->reminderType === 'customer' 
            ? "URGENT: Overdue PPM - {$this->contract->job_ref}"
            : "URGENT: Overdue PPM Action Required - {$this->contract->job_ref}";
            
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
            view: 'emails.overdue-ppm',
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