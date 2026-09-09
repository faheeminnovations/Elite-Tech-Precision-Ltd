<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contract;
    public $oldStatus;
    public $newStatus;
    public $action;
    public $user;
    public $isAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct($contract, $oldStatus, $newStatus, $action, $user, $isAdmin = false)
    {
        $this->contract = $contract;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->action = $action;
        $this->user = $user;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "EliteFlow: Contract Status Update - {$this->contract->job_ref}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-status',
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
