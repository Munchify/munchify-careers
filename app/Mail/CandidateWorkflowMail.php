<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateWorkflowMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectText;
    public string $bodyContent;
    public string $candidateName;
    public ?string $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subjectText, string $bodyContent, string $candidateName = '', ?string $actionUrl = null)
    {
        $this->subjectText = $subjectText;
        $this->bodyContent = $bodyContent;
        $this->candidateName = $candidateName;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.candidate_workflow',
            with: [
                'candidateName' => $this->candidateName,
                'bodyContent' => $this->bodyContent,
                'subjectContent' => $this->subjectText,
                'actionUrl' => $this->actionUrl ?? 'https://careers.munchify.co.ke',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
