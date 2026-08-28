<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $entityType;
    public $entityName;
    public $oldStatus;
    public $newStatus;
    public $action;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($entityType, $entityName, $oldStatus, $newStatus, $action, $user)
    {
        $this->entityType = $entityType;
        $this->entityName = $entityName;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->action = $action;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "EliteFlow: {$this->entityType} Status Update - {$this->entityName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.status-update',
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
