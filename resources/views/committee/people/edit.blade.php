{{-- resources/views/committee/people/edit.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>Edit Availability — {{ $user->name }}</h1>
    <p>Update operational availability and competence status for this operator.</p>
</div>

<div class="committee-panel">
    <div class="committee-panel__body">
        <form action="{{ route('committee.people.update', $user) }}" method="POST" class="committee-form">
            @csrf @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">

                <div>
                    <h4 style="font-size:13px; font-weight:700; color:var(--raynet-navy); margin:0 0 12px;">
                        Availability
                    </h4>
                    @foreach([
                        'is_active_operator'  => 'Active operator (i.e. genuinely deployable, not just a member on paper)',
                        'deployable_60min'    => 'Can deploy within 60 minutes (Liverpool footprint)',
                        'deployable_120min'   => 'Can deploy within 120 minutes (wider area)',
                        'is_team_leader'      => 'Qualified / current team leader',
                    ] as $field => $label)
                    <label style="display:flex; align-items:center; gap:10px; margin-bottom:10px; cursor:pointer;">
                        <input type="checkbox" name="{{ $field }}" value="1"
                               {{ old($field, $availability->$field) ? 'checked' : '' }}
                               style="width:18px; height:18px; accent-color:var(--raynet-navy);">
                        <span style="font-size:13px; color:#374151;">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>

                <div>
                    <h4 style="font-size:13px; font-weight:700; color:var(--raynet-navy); margin:0 0 12px;">
                        Competence
                    </h4>
                    @foreach([
                        'induction_current'        => 'Core induction current',
                        'message_handling_current' => 'Message handling &amp; logging current',
                        'digital_data_competent'   => 'Digital / data modes competent (DMR, YSF, LoRa, etc.)',
                    ] as $field => $label)
                    <label style="display:flex; align-items:center; gap:10px; margin-bottom:10px; cursor:pointer;">
                        <input type="checkbox" name="{{ $field }}" value="1"
                               {{ old($field, $availability->$field) ? 'checked' : '' }}
                               style="width:18px; height:18px; accent-color:var(--raynet-navy);">
                        <span style="font-size:13px; color:#374151;">{!! $label !!}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Induction date</label>
                    <input type="date" name="induction_date"
                           value="{{ old('induction_date', $availability->induction_date?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label>Message handling date</label>
                    <input type="date" name="message_handling_date"
                           value="{{ old('message_handling_date', $availability->message_handling_date?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3">{{ old('notes', $availability->notes) }}</textarea>
            </div>

            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn btn--primary">Save changes</button>
                <a href="{{ route('committee.people.index') }}" class="btn btn--secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
