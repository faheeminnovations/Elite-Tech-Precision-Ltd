<?php

namespace App\Mail;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contract $contract
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contract Notification - ' . $this->contract->contract_ref,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contract_notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
