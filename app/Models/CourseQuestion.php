<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseQuestion extends Model
{
    protected $table = 'lms_questions';
    protected $fillable = ['quiz_id','question','type','points','sort_order'];

    public function quiz()    { return $this->belongsTo(CourseQuiz::class); }
    public function answers() { return $this->hasMany(CourseAnswer::class, 'question_id')->orderBy('sort_order'); }
}