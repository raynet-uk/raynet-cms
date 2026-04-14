<?php
namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseCertificate;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CourseCompletedNotification extends Notification
{
    public function __construct(
        public Course $course,
        public ?CourseCertificate $certificate = null,
        public array $unlockedBadges = []
    ) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🏅 Course completed: ' . $this->course->title)
            ->view('emails.lms.completed', [
                'user'           => $notifiable,
                'course'         => $this->course,
                'certificate'    => $this->certificate,
                'unlockedBadges' => $this->unlockedBadges,
            ]);
    }
}