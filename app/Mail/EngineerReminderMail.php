<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EngineerReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $engineer;
    public $service;
    public $contract;
    public $customer;
    public $reminderType;
    public $taskCount;

    /**
     * Create a new message instance.
     */
    public function __construct($engineer, $service, $contract, $customer, $reminderType = 'assignment', $taskCount = 1)
    {
        $this->engineer = $engineer;
        $this->service = $service;
        $this->contract = $contract;
        $this->customer = $customer;
        $this->reminderType = $reminderType;
        $this->taskCount = $taskCount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->reminderType) {
            'assignment' => "New Task Assignment - {$this->contract->job_ref}",
            'reminder' => "Upcoming Task Reminder - {$this->contract->job_ref}",
            'schedule_change' => "Schedule Change - {$this->contract->job_ref}",
            'daily_summary' => "Daily Task Summary - {$this->taskCount} tasks",
            default => "Engineer Task Notification - {$this->contract->job_ref}",
        };
            
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
            view: 'emails.engineer-reminder',
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