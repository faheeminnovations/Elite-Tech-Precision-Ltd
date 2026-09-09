<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerResponseReviewMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $response;
    public $contract;
    public $customer;
    public $responseType;
    public $responseCount;

    /**
     * Create a new message instance.
     */
    public function __construct($response, $contract, $customer, $responseType = 'review', $responseCount = 1)
    {
        $this->response = $response;
        $this->contract = $contract;
        $this->customer = $customer;
        $this->responseType = $responseType;
        $this->responseCount = $responseCount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->responseType) {
            'accepted' => "Customer Response Accepted - {$this->contract->job_ref}",
            'declined' => "Customer Response Declined - {$this->contract->job_ref}",
            'pending' => "Customer Response Pending Review - {$this->contract->job_ref}",
            'received' => "New Customer Response Received - {$this->contract->job_ref}",
            default => "Customer Response Review Required - {$this->contract->job_ref}",
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
            view: 'emails.customer-response-review',
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