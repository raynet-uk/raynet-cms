<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    protected $table = 'lms_progress';
    protected $fillable = ['user_id','course_id','lesson_id','completed_at','quiz_score','attempts'];
    protected $casts = ['completed_at' => 'datetime'];

    public function user()   { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function lesson() { return $this->belongsTo(CourseLesson::class); }
}