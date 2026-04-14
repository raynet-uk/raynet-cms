<?php
namespace App\Console\Commands;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseProgress;
use App\Notifications\DripContentUnlockedNotification;
use App\Notifications\CourseAbandonedNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LmsScheduledTasks extends Command
{
    protected $signature   = 'lms:daily';
    protected $description = 'Send drip content unlock and course abandonment reminder emails';

    public function handle(): void
    {
        $this->processDripUnlocks();
        $this->processAbandonedReminders();
        $this->info('LMS daily tasks complete.');
    }

    private function processDripUnlocks(): void
    {
        // Find all active (incomplete) enrollments on drip courses
        $enrollments = CourseEnrollment::whereNull('completed_at')
            ->with(['user', 'course.lessons'])
            ->whereHas('course', fn($q) => $q->where('is_drip', true)->where('is_published', true))
            ->get();

        foreach ($enrollments as $enrollment) {
            $course     = $enrollment->course;
            $enrolledAt = Carbon::parse($enrollment->enrolled_at);

            foreach ($course->lessons->where('drip_days', '>', 0) as $lesson) {
                $unlockDate = $enrolledAt->copy()->addDays($lesson->drip_days);

                // Only notify if it unlocked TODAY
                if (!$unlockDate->isToday()) continue;

                // Check they haven't already completed it
                $alreadyDone = CourseProgress::where('user_id', $enrollment->user_id)
                    ->where('lesson_id', $lesson->id)
                    ->whereNotNull('completed_at')
                    ->exists();

                if ($alreadyDone) continue;

                try {
                    $enrollment->user->notify(new DripContentUnlockedNotification($course, $lesson));
                    $this->info("Drip unlock: {$enrollment->user->name} → {$lesson->title}");
                } catch (\Throwable $e) {
                    $this->error("Failed drip notify for user {$enrollment->user_id}: " . $e->getMessage());
                }
            }
        }
    }

    private function processAbandonedReminders(): void
    {
        // Members who are enrolled, not completed, have made some progress
        // but haven't had any progress updates in 14 days
        $cutoff = Carbon::now()->subDays(14);

        $enrollments = CourseEnrollment::whereNull('completed_at')
            ->with(['user', 'course'])
            ->whereHas('course', fn($q) => $q->where('is_published', true))
            ->get();

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            if (!$course) continue;

            // Get total and completed lessons
            $totalLessons = $course->lessons()->count();
            if ($totalLessons === 0) continue;

            $completedLessons = CourseProgress::where('user_id', $enrollment->user_id)
                ->where('course_id', $course->id)
                ->whereNotNull('completed_at')
                ->count();

            // Must have started but not finished
            if ($completedLessons === 0) continue;
            if ($completedLessons >= $totalLessons) continue;

            // Check last activity
            $lastActivity = CourseProgress::where('user_id', $enrollment->user_id)
                ->where('course_id', $course->id)
                ->max('updated_at');

            if (!$lastActivity || Carbon::parse($lastActivity)->gt($cutoff)) continue;

            $progressPct = (int) round(($completedLessons / $totalLessons) * 100);
            $dueDateStr  = $enrollment->due_date ? $enrollment->due_date->format('d M Y') : null;

            try {
                $enrollment->user->notify(new CourseAbandonedNotification($course, $progressPct, $dueDateStr));
                $this->info("Abandoned reminder: {$enrollment->user->name} → {$course->title} ({$progressPct}%)");
            } catch (\Throwable $e) {
                $this->error("Failed abandoned notify for user {$enrollment->user_id}: " . $e->getMessage());
            }
        }
    }
}