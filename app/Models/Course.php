<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $table = 'lms_courses';
protected $fillable = [
    'title','slug','description','thumbnail','category','difficulty',
    'estimated_hours','is_published','is_drip','drip_interval_days',
    'pass_mark','certificate_enabled','certificate_text','created_by',
    'unlocks_badge_ids',
];
    protected $casts = [
    'is_published'        => 'boolean',
    'is_drip'             => 'boolean',
    'certificate_enabled' => 'boolean',
    'unlocks_badge_ids'   => 'array',
];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($c) {
            if (empty($c->slug)) {
                $c->slug = Str::slug($c->title) . '-' . Str::random(4);
            }
        });
    }

    public function modules()    { return $this->hasMany(CourseModule::class)->orderBy('sort_order'); }
    public function lessons()    { return $this->hasMany(CourseLesson::class)->orderBy('sort_order'); }
    public function enrollments(){ return $this->hasMany(CourseEnrollment::class); }
    public function certificates(){ return $this->hasMany(CourseCertificate::class); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }

    public function getProgressFor($userId): int
    {
        $total = $this->lessons()->count();
        if ($total === 0) return 0;
        $done = CourseProgress::where('user_id', $userId)
            ->where('course_id', $this->id)
            ->whereNotNull('completed_at')->count();
        return (int) round(($done / $total) * 100);
    }
}