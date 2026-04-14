<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    protected $table    = 'lms_quiz_submissions';
    protected $fillable = ['user_id','quiz_id','course_id','lesson_id','attempt_number','score','passed','answers'];
    protected $casts    = ['passed' => 'boolean', 'answers' => 'array'];

    public function user()   { return $this->belongsTo(User::class); }
    public function quiz()   { return $this->belongsTo(CourseQuiz::class, 'quiz_id'); }
    public function course() { return $this->belongsTo(Course::class); }
}