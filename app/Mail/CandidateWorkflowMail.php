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

    /**
     * Create a new message instance.
     */
    public function __construct(string $subjectText, string $bodyContent, string $candidateName = '')
    {
        $this->subjectText = $subjectText;
        $this->bodyContent = $bodyContent;
        $this->candidateName = $candidateName;
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
            markdown: 'emails.candidate_workflow',
            with: [
                'subjectText' => $this->subjectText,
                'bodyContent' => $this->bodyContent,
                'candidateName' => $this->candidateName,
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
