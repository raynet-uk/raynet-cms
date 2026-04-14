<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseProgress;
use App\Models\CourseQuiz;
use App\Models\CourseCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LearningController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $userId = auth()->id();
        $myEnrollments = CourseEnrollment::where('user_id', $userId)->with('course')->get()->map(function($e) use ($userId) {
            $e->progress_pct = $e->course->getProgressFor($userId);
            return $e;
        });
        $enrolledIds = $myEnrollments->pluck('course_id')->toArray();
        $availableCourses = Course::where('is_published', true)->whereNotIn('id', $enrolledIds)->get();
        return view('lms.index', compact('myEnrollments','availableCourses'));
    }

    public function show($slug)
    {
        $course = Course::where('slug', $slug)->with(['modules.lessons'])->firstOrFail();
        $userId = auth()->id();
        $enrollment  = CourseEnrollment::where('user_id', $userId)->where('course_id', $course->id)->first();
        $progress    = CourseProgress::where('user_id', $userId)->where('course_id', $course->id)->pluck('completed_at','lesson_id');
        $certificate = CourseCertificate::where('user_id', $userId)->where('course_id', $course->id)->first();
        return view('lms.course', compact('course','enrollment','progress','certificate'));
    }

    public function lesson($courseSlug, $lessonId)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = CourseLesson::where('id', $lessonId)->where('course_id', $course->id)
            ->with('module','quiz.questions.answers')->firstOrFail();
        $userId = auth()->id();
        $enrollment = CourseEnrollment::where('user_id', $userId)->where('course_id', $course->id)->firstOrFail();

        if ($lesson->drip_days > 0) {
            $available = Carbon::parse($enrollment->enrolled_at)->addDays($lesson->drip_days);
            if (now()->lt($available)) {
                return redirect()->route('lms.course', $courseSlug)
                    ->with('error', 'This lesson unlocks on ' . $available->format('d M Y') . '.');
            }
        }

        $allLessons  = $course->lessons()->orderBy('sort_order')->get();
        $idx         = $allLessons->search(fn($l) => $l->id == $lesson->id);
        $prevLesson  = $idx > 0 ? $allLessons[$idx - 1] : null;
        $nextLesson  = $idx < $allLessons->count() - 1 ? $allLessons[$idx + 1] : null;
        $progressRecord = CourseProgress::where('user_id', $userId)->where('lesson_id', $lessonId)->first();

        return view('lms.lesson', compact('course','lesson','prevLesson','nextLesson','progressRecord','enrollment'));
    }

    public function complete(Request $request, $lessonId)
    {
        $userId = auth()->id();
        $lesson = CourseLesson::findOrFail($lessonId);
        $prog   = CourseProgress::firstOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lessonId],
            ['course_id' => $lesson->course_id]
        );
        if (!$prog->completed_at) { $prog->completed_at = now(); $prog->save(); }
        $this->checkCompletion($userId, $lesson->course_id);
        return response()->json(['success' => true]);
    }

    public function quiz($courseSlug, $quizId)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $quiz   = CourseQuiz::with('questions.answers')->findOrFail($quizId);
        $userId = auth()->id();
        $progress     = CourseProgress::where('user_id', $userId)->where('lesson_id', $quiz->lesson_id)->first();
        $attemptsUsed = $progress?->attempts ?? 0;
        $canAttempt   = $attemptsUsed < $quiz->attempts_allowed;
        $passed       = $progress && $progress->completed_at && ($progress->quiz_score >= $quiz->pass_mark);
        return view('lms.quiz', compact('course','quiz','progress','attemptsUsed','canAttempt','passed'));
    }

    public function submitQuiz(Request $request, $quizId)
    {
        $quiz   = CourseQuiz::with('questions.answers')->findOrFail($quizId);
        $userId = auth()->id();
        $total  = 0; $earned = 0;
        foreach ($quiz->questions as $q) {
            $total += $q->points;
            $sub = $request->input('q_' . $q->id);
            if (in_array($q->type, ['multiple_choice','true_false'])) {
                $correct  = $q->answers->where('is_correct', true)->pluck('id')->toArray();
                $subArr   = is_array($sub) ? $sub : [$sub];
                $subInts  = array_map('intval', array_filter($subArr));
                if (!array_diff($correct, $subInts) && !array_diff($subInts, $correct)) {
                    $earned += $q->points;
                }
            }
        }
        $score  = $total > 0 ? round(($earned / $total) * 100) : 0;
        $passed = $score >= $quiz->pass_mark;
        $prog   = CourseProgress::firstOrCreate(
            ['user_id' => $userId, 'lesson_id' => $quiz->lesson_id],
            ['course_id' => $quiz->course_id]
        );
        $prog->attempts   = ($prog->attempts ?? 0) + 1;
        $prog->quiz_score = $score;
        if ($passed && !$prog->completed_at) { $prog->completed_at = now(); }
        $prog->save();
        if ($passed) { $this->checkCompletion($userId, $quiz->course_id); }
        return response()->json(['success'=>true,'score'=>$score,'passed'=>$passed,'pass_mark'=>$quiz->pass_mark]);
    }

    public function certificate($courseId)
    {
        $course      = Course::findOrFail($courseId);
        $userId      = auth()->id();
        $certificate = CourseCertificate::where('user_id', $userId)->where('course_id', $courseId)->firstOrFail();
        $user        = auth()->user();
        return view('lms.certificate', compact('course','certificate','user'));
    }

    private function checkCompletion($userId, $courseId)
    {
        $course = Course::with('lessons')->findOrFail($courseId);
        $total  = $course->lessons->count();
        if ($total === 0) return;
        $done = CourseProgress::where('user_id', $userId)->where('course_id', $courseId)->whereNotNull('completed_at')->count();
        if ($done >= $total) {
            CourseEnrollment::where('user_id', $userId)->where('course_id', $courseId)->whereNull('completed_at')->update(['completed_at' => now()]);
            if ($course->certificate_enabled) {
                CourseCertificate::firstOrCreate(
                    ['user_id' => $userId, 'course_id' => $courseId],
                    ['certificate_number' => 'RAYNET-' . strtoupper(Str::random(8)), 'issued_at' => now()]
                );
            }
        }
    }
}