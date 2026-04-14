<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    protected $table = 'lms_modules';
    protected $fillable = ['course_id','title','description','sort_order'];

    public function course()  { return $this->belongsTo(Course::class); }
    public function lessons() { return $this->hasMany(CourseLesson::class, 'module_id')->orderBy('sort_order'); }
}