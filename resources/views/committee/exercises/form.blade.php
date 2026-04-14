{{-- resources/views/committee/exercises/form.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>{{ $exercise->exists ? 'Edit Activity' : 'Log Activity' }}</h1>
    <p>Record training nights, exercises, real deployments, and the lessons they generated.</p>
</div>

<div class="committee-panel">
    <div class="committee-panel__body">
        <form action="{{ $exercise->exists ? route('committee.exercises.update', $exercise) : route('committee.exercises.store') }}"
              method="POST" class="committee-form">
            @csrf
            @if($exercise->exists) @method('PUT') @endif

            <div class="form-row">
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date" value="{{ old('date', $exercise->date?->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Type *</label>
                    <select name="type">
                        @foreach([
                            'training_night'     => 'Training night',
                            'tabletop'           => 'Tabletop exercise',
                            'practical_exercise' => 'Practical exercise',
                            'real_deployment'    => 'Real deployment',
                            'partner_exercise'   => 'Partner exercise',
                            'other'              => 'Other',
                        ] as $val => $label)
                        <option value="{{ $val }}" {{ old('type', $exercise->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Activity / title *</label>
                <input type="text" name="activity" value="{{ old('activity', $exercise->activity) }}"
                       required placeholder="e.g. Monthly training night — message handling practice">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Capability tested</label>
                    <input type="text" name="capability_tested"
                           value="{{ old('capability_tested', $exercise->capability_tested) }}"
                           placeholder="e.g. Message handling, VHF voice, APRS">
                </div>
                <div class="form-group">
                    <label>Lead / controller</label>
                    <input type="text" name="lead" value="{{ old('lead', $exercise->lead) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Outcome / summary</label>
                <textarea name="outcome" rows="3"
                          placeholder="What went well, what the group practised, overall result.">{{ old('outcome', $exercise->outcome) }}</textarea>
            </div>

            <div class="form-group">
                <label>Lessons identified</label>
                <textarea name="lessons_identified" rows="3"
                          placeholder="Any issues, gaps, or improvement areas identified.">{{ old('lessons_identified', $exercise->lessons_identified) }}</textarea>
                <div class="hint">Lessons that need follow-up action should also be raised in the Actions log.</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Action owner (if follow-up required)</label>
                    <input type="text" name="action_owner" value="{{ old('action_owner', $exercise->action_owner) }}">
                </div>
                <div class="form-group">
                    <label>Action due date</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $exercise->due_date?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Closed date (if action complete)</label>
                <input type="date" name="closed_date" value="{{ old('closed_date', $exercise->closed_date?->format('Y-m-d')) }}" style="max-width:200px;">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="2">{{ old('notes', $exercise->notes) }}</textarea>
            </div>

            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn btn--primary">
                    {{ $exercise->exists ? 'Save changes' : 'Log activity' }}
                </button>
                <a href="{{ route('committee.exercises.index') }}" class="btn btn--secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
