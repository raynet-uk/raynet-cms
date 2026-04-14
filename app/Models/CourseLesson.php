<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $table = 'lms_lessons';
    protected $fillable = [
        'module_id','course_id','title','type','content',
        'video_url','scorm_package','duration_minutes',
        'sort_order','is_free_preview','drip_days',
    ];
    protected $casts = ['is_free_preview' => 'boolean'];

    public function module() { return $this->belongsTo(CourseModule::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function quiz()   { return $this->hasOne(CourseQuiz::class, 'lesson_id'); }

    public function isCompletedBy($userId): bool
    {
        return CourseProgress::where('user_id', $userId)
            ->where('lesson_id', $this->id)
            ->whereNotNull('completed_at')->exists();
    }
}