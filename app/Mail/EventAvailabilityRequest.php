<?php
namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventAvailabilityRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public User $member,
        public string $availableUrl,
        public string $unavailableUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Can you attend? — ' . $this->event->title . ' · ' . \App\Helpers\RaynetSetting::groupName()
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.event-availability-request');
    }
}
