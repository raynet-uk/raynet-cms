<?php
namespace App\Notifications;

use App\Models\Course;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CourseAbandonedNotification extends Notification
{
    public function __construct(
        public Course $course,
        public int $progressPct,
        public ?string $dueDate = null
    ) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: Continue your RAYNET training')
            ->view('emails.lms.abandoned', [
                'user'        => $notifiable,
                'course'      => $this->course,
                'progressPct' => $this->progressPct,
                'dueDate'     => $this->dueDate,
            ]);
    }
}