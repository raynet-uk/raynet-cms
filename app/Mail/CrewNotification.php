<?php
namespace App\Mail;

use App\Models\EventAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrewNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EventAssignment $assignment,
        public string $type,
        public string $customMessage = ''
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->type === 'custom'
            ? 'Message from ' . \App\Helpers\RaynetSetting::groupName() . ' — ' . $this->assignment->event->title
            : 'Event Reminder — ' . $this->assignment->event->title;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.crew-notification');
    }
}
