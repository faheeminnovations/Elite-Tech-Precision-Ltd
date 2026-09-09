<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoContractCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;
    public $lastServiceDate;
    public $reminderType;
    public $customerCount;

    /**
     * Create a new message instance.
     */
    public function __construct($customer, $lastServiceDate = null, $reminderType = 'sales', $customerCount = 1)
    {
        $this->customer = $customer;
        $this->lastServiceDate = $lastServiceDate;
        $this->reminderType = $reminderType;
        $this->customerCount = $customerCount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->reminderType === 'sales' 
            ? "Potential Sales Follow-up - {$this->customer->name}"
            : "No Contract Alert - {$this->customer->name}";
            
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
            view: 'emails.no-contract-customer',
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