{{-- resources/views/committee/actions/form.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>{{ $action->exists ? 'Edit Action' : 'Log Action' }}</h1>
</div>

<div class="committee-panel">
    <div class="committee-panel__body">
        <form action="{{ $action->exists ? route('committee.actions.update', $action) : route('committee.actions.store') }}"
              method="POST" class="committee-form">
            @csrf
            @if($action->exists) @method('PUT') @endif

            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" value="{{ old('title', $action->title) }}" required
                       placeholder="Short, clear description of what needs to happen.">
            </div>

            <div class="form-group">
                <label>Description / detail</label>
                <textarea name="description" rows="3"
                          placeholder="Context, background, what a successful outcome looks like.">{{ old('description', $action->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Source *</label>
                    <select name="source">
                        @foreach(['exercise','deployment','risk','committee','inspection','other'] as $src)
                        <option value="{{ $src }}" {{ old('source', $action->source ?? 'committee') === $src ? 'selected' : '' }}>
                            {{ ucfirst($src) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Source reference</label>
                    <input type="text" name="source_ref" value="{{ old('source_ref', $action->source_ref) }}"
                           placeholder="e.g. Exercise #3, Risk #7">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Priority *</label>
                    <select name="priority">
                        @foreach(['low','medium','high','critical'] as $p)
                        <option value="{{ $p }}" {{ old('priority', $action->priority ?? 'medium') === $p ? 'selected' : '' }}>
                            {{ ucfirst($p) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status">
                        @foreach(['open','in_progress','closed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $action->status ?? 'open') === $s ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Owner</label>
                    <select name="owner_id">
                        <option value="">— Unassigned —</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('owner_id', $action->owner_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Due date</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $action->due_date?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Closure notes</label>
                <textarea name="closure_notes" rows="2"
                          placeholder="What was done to close this action.">{{ old('closure_notes', $action->closure_notes) }}</textarea>
            </div>

            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn btn--primary">{{ $action->exists ? 'Save changes' : 'Log action' }}</button>
                <a href="{{ route('committee.actions.index') }}" class="btn btn--secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
