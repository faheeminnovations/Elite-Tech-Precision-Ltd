<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PPMReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contract;
    public $customer;
    public $daysUntilDue;
    public $reminderType;

    /**
     * Create a new message instance.
     */
    public function __construct($contract, $customer, $daysUntilDue, $reminderType = 'customer')
    {
        $this->contract = $contract;
        $this->customer = $customer;
        $this->daysUntilDue = $daysUntilDue;
        $this->reminderType = $reminderType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->reminderType === 'customer' 
            ? "PPM Reminder for {$this->contract->job_ref}"
            : "Internal PPM Reminder: {$this->contract->job_ref}";
            
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
            view: 'emails.ppm-reminder',
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
