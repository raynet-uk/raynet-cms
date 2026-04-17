{{-- resources/views/committee/networks/form.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>{{ $network->exists ? 'Edit Network' : 'Add Network' }}</h1>
</div>

<div class="committee-panel">
    <div class="committee-panel__body">
        <form action="{{ $network->exists ? route('committee.networks.update', $network) : route('committee.networks.store') }}"
              method="POST" class="committee-form">
            @csrf
            @if($network->exists) @method('PUT') @endif

            <div class="form-row">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" value="{{ old('name', $network->name) }}" required placeholder="e.g. {{ \App\Helpers\RaynetSetting::groupName() }} Calling">
                </div>
                <div class="form-group">
                    <label>Type *</label>
                    <select name="type">
                        @foreach(['VHF/UHF','DMR','YSF','VoIP','LoRa','APRS','HF','Other'] as $type)
                        <option value="{{ $type }}" {{ old('type', $network->type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status">
                        @foreach(['operational','degraded','offline','unknown'] as $s)
                        <option value="{{ $s }}" {{ old('status', $network->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Frequency / Channel</label>
                    <input type="text" name="frequency_channel" value="{{ old('frequency_channel', $network->frequency_channel) }}" placeholder="e.g. 145.500 MHz">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Talkgroup / Network ID</label>
                    <input type="text" name="talkgroup_network_id" value="{{ old('talkgroup_network_id', $network->talkgroup_network_id) }}">
                </div>
                <div class="form-group">
                    <label>Last tested</label>
                    <input type="date" name="last_tested" value="{{ old('last_tested', $network->last_tested?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Test result / notes</label>
                <textarea name="test_result" rows="2">{{ old('test_result', $network->test_result) }}</textarea>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="2">{{ old('description', $network->description) }}</textarea>
            </div>

            <div class="form-group">
                <label>Network owner / responsible operator</label>
                <select name="owner_id">
                    <option value="">— None —</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('owner_id', $network->owner_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn--primary">{{ $network->exists ? 'Save changes' : 'Add network' }}</button>
                <a href="{{ route('committee.networks.index') }}" class="btn btn--secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
