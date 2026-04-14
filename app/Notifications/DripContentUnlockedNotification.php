<?php
namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DripContentUnlockedNotification extends Notification
{
    public function __construct(
        public Course $course,
        public CourseLesson $lesson
    ) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New lesson unlocked: ' . $this->lesson->title)
            ->view('emails.lms.drip', [
                'user'   => $notifiable,
                'course' => $this->course,
                'lesson' => $this->lesson,
            ]);
    }
}