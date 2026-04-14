<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseQuiz extends Model
{
    protected $table = 'lms_quizzes';
    protected $fillable = ['lesson_id','course_id','title','pass_mark','attempts_allowed','time_limit_minutes'];

    public function lesson()    { return $this->belongsTo(CourseLesson::class); }
    public function questions() { return $this->hasMany(CourseQuestion::class, 'quiz_id')->orderBy('sort_order'); }
}