<x-emails.layouts.lms
    headerTitle="A new lesson has unlocked"
    headerSubtitle="{{ $course->title }}">

@php
$typeIcons = [
    'text'=>'📄','video'=>'🎬','audio'=>'🎧','document'=>'📋',
    'presentation'=>'📊','external'=>'🔗','checklist'=>'✅','scorm'=>'📦','quiz'=>'❓',
];
$typeLabels = [
    'text'=>'Reading','video'=>'Video','audio'=>'Audio','document'=>'Document',
    'presentation'=>'Presentation','external'=>'External Link',
    'checklist'=>'Checklist','scorm'=>'SCORM','quiz'=>'Quiz',
];
$icon  = $typeIcons[$lesson->type] ?? '📄';
$label = $typeLabels[$lesson->type] ?? 'Lesson';
@endphp

<div class="body">
    <div class="greeting">Hello {{ $user->name }},</div>
    <p class="text">
        A new lesson has just unlocked in your course <strong>{{ $course->title }}</strong>.
        It's time to continue your training!
    </p>

    <div class="highlight-box">
        <div style="font-size:28px;margin-bottom:8px;">{{ $icon }}</div>
        <div class="course-title">{{ $lesson->title }}</div>
        <div class="course-meta">{{ $label }} · Part of {{ $course->title }}</div>
        @if($lesson->duration_minutes)
        <div class="course-meta" style="margin-top:4px;">⏱ ~{{ $lesson->duration_minutes }} minutes</div>
        @endif
    </div>

    <div class="btn-wrap">
        <a href="{{ url('/my-training/' . $course->slug . '/lesson/' . $lesson->id) }}" class="btn btn-teal">
            {{ $icon }} Open Lesson
        </a>
    </div>

    <div class="divider"></div>

    <p class="text" style="font-size:12px;color:#9aa3ae;text-align:center;">
        Keep up the great work — you're making excellent progress on your RAYNET training.
    </p>
</div>

</x-emails.layouts.lms>