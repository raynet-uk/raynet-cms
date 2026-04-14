@extends('layouts.app')
@section('title', $quiz->title)
@section('content')
<style>
:root{--navy:#003366;--red:#C8102E;--teal:#0288d1;--green:#1a6b3c;--green-bg:#eef7f2;--amber:#8a5500;--amber-bg:#fdf8ec;--grey:#f2f5f9;--grey-mid:#dde2e8;--white:#fff;--text:#001f40;--text-mid:#2d4a6b;--muted:#6b7f96;--shadow-sm:0 1px 3px rgba(0,51,102,.09);--font:Arial,'Helvetica Neue',Helvetica,sans-serif;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--font);background:var(--grey);color:var(--text);}
.quiz-header{background:var(--navy);border-bottom:3px solid var(--red);padding:0 1.5rem;}
.quiz-header-inner{max-width:760px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:52px;}
.quiz-header-title{font-size:13px;font-weight:bold;color:#fff;}
.quiz-header-sub{font-size:10px;color:rgba(255,255,255,.45);}
.btn{display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .9rem;border:1px solid;font-family:var(--font);font-size:11px;font-weight:bold;cursor:pointer;text-decoration:none;text-transform:uppercase;letter-spacing:.05em;transition:all .12s;}
.btn-ghost{background:transparent;border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7);}
.btn-ghost:hover{border-color:rgba(255,255,255,.4);color:#fff;}
.btn-primary{background:var(--navy);border-color:var(--navy);color:#fff;}
.btn-primary:hover{background:#002244;}
.btn-sm{padding:.25rem .65rem;font-size:10px;}
.wrap{max-width:760px;margin:0 auto;padding:1.5rem 1.5rem 4rem;}
.quiz-meta-bar{background:var(--white);border:1px solid var(--grey-mid);padding:.75rem 1.1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;box-shadow:var(--shadow-sm);}
.qm-item{font-size:12px;display:flex;flex-direction:column;gap:2px;}
.qm-label{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);}
.qm-val{font-size:13px;font-weight:bold;color:var(--navy);}
.question-card{background:var(--white);border:1px solid var(--grey-mid);margin-bottom:1rem;box-shadow:var(--shadow-sm);overflow:hidden;}
.question-head{padding:.65rem 1.1rem;background:var(--grey);border-bottom:1px solid var(--grey-mid);display:flex;align-items:center;gap:.65rem;}
.question-num{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);}
.question-points{font-size:10px;font-weight:bold;color:var(--teal);margin-left:auto;}
.question-body{padding:1rem 1.1rem;}
.question-text{font-size:14px;font-weight:bold;color:var(--text);margin-bottom:.85rem;line-height:1.5;}
.answer-options{display:flex;flex-direction:column;gap:.5rem;}
.answer-option{display:flex;align-items:center;gap:.75rem;padding:.65rem .9rem;border:1px solid var(--grey-mid);background:var(--grey);cursor:pointer;transition:border-color .12s,background .12s;}
.answer-option:hover{border-color:var(--navy);background:#f0f4f8;}
.answer-option:has(input:checked){border-color:var(--teal);background:#e8f4fb;}
.answer-option input[type="radio"],.answer-option input[type="checkbox"]{width:15px;height:15px;accent-color:var(--teal);flex-shrink:0;cursor:pointer;}
.answer-option-text{font-size:13px;color:var(--text-mid);}
.quiz-footer{display:flex;align-items:center;justify-content:space-between;margin-top:1.25rem;gap:1rem;flex-wrap:wrap;}
.quiz-result{background:var(--white);border:1px solid var(--grey-mid);padding:2rem 1.5rem;text-align:center;box-shadow:var(--shadow-sm);display:none;}
.result-icon{font-size:3rem;margin-bottom:.75rem;}
.result-title{font-size:20px;font-weight:bold;margin-bottom:.4rem;}
.result-score{font-size:36px;font-weight:bold;margin-bottom:.4rem;}
.result-sub{font-size:13px;color:var(--muted);margin-bottom:1.25rem;}
.timer-bar{height:4px;background:var(--grey-mid);overflow:hidden;margin-bottom:.5rem;}
.timer-fill{height:100%;background:var(--teal);transition:width 1s linear;}
.error-box{background:#fdf0f2;border:1px solid rgba(200,16,46,.25);border-left:3px solid var(--red);padding:.75rem 1rem;font-size:12px;font-weight:bold;color:var(--red);margin-bottom:1rem;display:none;}
</style>

<div class="quiz-header">
    <div class="quiz-header-inner">
        <div>
            <div class="quiz-header-title">{{ $quiz->title }}</div>
            <div class="quiz-header-sub">{{ $course->title }}</div>
        </div>
        <a href="{{ route('lms.course', $course->slug) }}" class="btn btn-ghost btn-sm">← Course</a>
    </div>
</div>

<div class="wrap">

    @if($passed)
    <div style="background:var(--green-bg);border:1px solid #b8ddc9;border-left:3px solid var(--green);padding:.75rem 1.1rem;font-size:13px;font-weight:bold;color:var(--green);margin-bottom:1rem;">
        ✓ You have already passed this quiz with {{ $progress->quiz_score }}%.
        <a href="{{ route('lms.course', $course->slug) }}" style="color:var(--green);margin-left:.5rem;">Continue course →</a>
    </div>
    @endif

    <div class="error-box" id="errorBox"></div>

    <div class="quiz-meta-bar">
        <div class="qm-item"><span class="qm-label">Questions</span><span class="qm-val">{{ $quiz->questions->count() }}</span></div>
        <div class="qm-item"><span class="qm-label">Pass Mark</span><span class="qm-val">{{ $quiz->pass_mark }}%</span></div>
        <div class="qm-item"><span class="qm-label">Attempts Used</span><span class="qm-val">{{ $attemptsUsed }} / {{ $quiz->attempts_allowed }}</span></div>
        @if($quiz->time_limit_minutes)
        <div class="qm-item"><span class="qm-label">Time Limit</span><span class="qm-val" id="timerDisplay">{{ $quiz->time_limit_minutes }}:00</span></div>
        @endif
        @if($progress && $progress->quiz_score !== null)
        <div class="qm-item"><span class="qm-label">Last Score</span><span class="qm-val">{{ $progress->quiz_score }}%</span></div>
        @endif
    </div>

    @if($quiz->time_limit_minutes)
    <div class="timer-bar"><div class="timer-fill" id="timerFill"></div></div>
    @endif

    <div id="quizForm" style="{{ !$canAttempt ? 'opacity:.5;pointer-events:none;' : '' }}">
        @foreach($quiz->questions as $i => $q)
        <div class="question-card">
            <div class="question-head">
                <span class="question-num">Question {{ $i+1 }}</span>
                <span class="question-points">{{ $q->points }} {{ $q->points==1?'point':'points' }}</span>
            </div>
            <div class="question-body">
                <div class="question-text">{{ $q->question }}</div>
                <div class="answer-options">
                  @foreach($q->answers as $a)
<label class="answer-option">
    <input type="radio" name="q_{{ $q->id }}" value="{{ $a->id }}">
    <span class="answer-option-text">{{ $a->answer_text }}</span>
</label>
@endforeach
                </div>
            </div>
        </div>
        @endforeach

        <div class="quiz-footer">
            <a href="{{ route('lms.course', $course->slug) }}" class="btn" style="border-color:var(--grey-mid);color:var(--muted);">Cancel</a>
            @if($canAttempt)
                <button class="btn btn-primary" onclick="submitQuiz()" id="submitBtn">Submit Quiz →</button>
            @else
                <span style="font-size:12px;color:var(--red);font-weight:bold;">No attempts remaining.</span>
            @endif
        </div>
    </div>

    <div class="quiz-result" id="quizResult">
        <div class="result-icon" id="resultIcon"></div>
        <div class="result-title" id="resultTitle"></div>
        <div class="result-score" id="resultScore"></div>
        <div class="result-sub" id="resultSub"></div>
        <a href="{{ route('lms.course', $course->slug) }}" class="btn btn-primary" style="display:inline-flex;margin-top:.5rem;">← Back to Course</a>
    </div>
</div>

<script>
const SUBMIT_URL  = '{{ url("/my-training/quiz/" . $quiz->id . "/submit") }}';
const CSRF_TOKEN  = '{{ csrf_token() }}';
const PASS_MARK   = {{ (int) $quiz->pass_mark }};

@if($quiz->time_limit_minutes)
let timeLeft = {{ $quiz->time_limit_minutes * 60 }};
const total  = timeLeft;
const fill   = document.getElementById('timerFill');
const display = document.getElementById('timerDisplay');
fill.style.width = '100%';
const timer = setInterval(() => {
    timeLeft--;
    const m = Math.floor(timeLeft / 60);
    const s = timeLeft % 60;
    display.textContent = m + ':' + (s < 10 ? '0' : '') + s;
    const pct = (timeLeft / total) * 100;
    fill.style.width = pct + '%';
    if (pct < 20) fill.style.background = 'var(--red)';
    if (timeLeft <= 0) { clearInterval(timer); submitQuiz(); }
}, 1000);
@endif

async function submitQuiz() {
    const btn = document.getElementById('submitBtn');
    if (btn) btn.setAttribute('disabled', 'true');

    const errorBox = document.getElementById('errorBox');
    errorBox.style.display = 'none';

    // Collect answers
    const body = {};
    document.querySelectorAll('[name^="q_"]').forEach(el => {
        if (el.checked) {
            const key = el.name;
            if (!body[key]) body[key] = [];
            body[key].push(el.value);
        }
    });
    // Flatten arrays with single value
    Object.keys(body).forEach(k => {
        if (Array.isArray(body[k]) && body[k].length === 1) {
            body[k] = body[k][0];
        }
    });

    let data;
    try {
        const r = await fetch(SUBMIT_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':  CSRF_TOKEN,
                'Content-Type':  'application/json',
                'Accept':        'application/json',
            },
            body: JSON.stringify(body),
        });

        const rawText = await r.text();

        try {
            data = JSON.parse(rawText);
        } catch (parseErr) {
            console.error('JSON parse error. Raw response:', rawText);
            errorBox.textContent = 'Server returned an unexpected response. Check console for details.';
            errorBox.style.display = 'block';
            if (btn) btn.removeAttribute('disabled');
            return;
        }

        if (!r.ok) {
            console.error('HTTP error', r.status, data);
            errorBox.textContent = 'Server error ' + r.status + ': ' + (data.message || 'Unknown error');
            errorBox.style.display = 'block';
            if (btn) btn.removeAttribute('disabled');
            return;
        }

    } catch (networkErr) {
        console.error('Network error:', networkErr);
        errorBox.textContent = 'Network error: ' + networkErr.message;
        errorBox.style.display = 'block';
        if (btn) btn.removeAttribute('disabled');
        return;
    }

    // Validate response shape
    const score     = typeof data.score     !== 'undefined' ? parseInt(data.score)     : null;
    const passMark  = typeof data.pass_mark !== 'undefined' ? parseInt(data.pass_mark) : PASS_MARK;
    const passed    = typeof data.passed    !== 'undefined' ? data.passed              : (score !== null && score >= passMark);

    if (score === null) {
        console.error('Unexpected response shape:', data);
        errorBox.textContent = 'Quiz submitted but response was malformed. Raw: ' + JSON.stringify(data);
        errorBox.style.display = 'block';
        if (btn) btn.removeAttribute('disabled');
        return;
    }

    // Show result
    document.getElementById('quizForm').style.display   = 'none';
    document.getElementById('quizResult').style.display = 'block';
    document.getElementById('resultIcon').textContent   = passed ? '🏅' : '❌';
    document.getElementById('resultTitle').textContent  = passed ? 'Well done — you passed!' : 'Not quite — try again';
    document.getElementById('resultScore').textContent  = score + '%';
    document.getElementById('resultScore').style.color  = passed ? 'var(--green)' : 'var(--red)';
    document.getElementById('resultSub').textContent    = 'Pass mark: ' + passMark + '% · Your score: ' + score + '%';
}
</script>
@endsection