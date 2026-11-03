<?php

namespace App\Mail;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractMailingNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $contract;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
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
            view: 'emails.contract_mailing_notification',
            with: [
                'contract' => $this->contract,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
