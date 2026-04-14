<?php
namespace App\Notifications;

use App\Models\Course;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CourseEnrolledNotification extends Notification
{
    public function __construct(public Course $course, public ?string $dueDate = null) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been enrolled: ' . $this->course->title)
            ->view('emails.lms.enrolled', [
                'user'     => $notifiable,
                'course'   => $this->course,
                'dueDate'  => $this->dueDate,
            ]);
    }
}