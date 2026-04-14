<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $table = 'lms_enrollments';
    protected $fillable = ['user_id','course_id','assigned_by','enrolled_at','completed_at','due_date'];
    protected $casts = ['enrolled_at'=>'datetime','completed_at'=>'datetime','due_date'=>'date'];

    public function user()     { return $this->belongsTo(User::class); }
    public function course()   { return $this->belongsTo(Course::class); }
    public function assigner() { return $this->belongsTo(User::class, 'assigned_by'); }
}