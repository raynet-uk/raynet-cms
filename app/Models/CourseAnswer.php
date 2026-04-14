<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseAnswer extends Model
{
    protected $table = 'lms_answers';
    protected $fillable = ['question_id','answer_text','is_correct','sort_order'];
    protected $casts = ['is_correct' => 'boolean'];

    public function question() { return $this->belongsTo(CourseQuestion::class); }
}