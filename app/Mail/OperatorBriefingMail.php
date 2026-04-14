<?php

namespace App\Mail;

use App\Models\EventAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperatorBriefingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly EventAssignment $assignment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Operator Briefing — ' . $this->assignment->event->title
                   . ' · ' . ($this->assignment->event->starts_at?->format('D j M Y') ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.operator-briefing',
        );
    }
}