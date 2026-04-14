{{-- resources/views/committee/assets/form.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>{{ $asset->exists ? 'Edit Asset' : 'Add Asset' }}</h1>
</div>

<div class="committee-panel">
    <div class="committee-panel__body">
        <form action="{{ $asset->exists ? route('committee.assets.update', $asset) : route('committee.assets.store') }}"
              method="POST" class="committee-form">
            @csrf
            @if($asset->exists) @method('PUT') @endif

            <div class="form-row">
                <div class="form-group">
                    <label>Asset type *</label>
                    <select name="asset_type">
                        @foreach(['Radio (VHF/UHF)','Radio (HF)','DMR Radio','Handheld','Antenna','Battery/Power','Laptop','Go-box','Vehicle','APRS Device','LoRa Node','Cable/Accessories','Other'] as $type)
                        <option value="{{ $type }}" {{ old('asset_type', $asset->asset_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Description *</label>
                    <input type="text" name="description" value="{{ old('description', $asset->description) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Serial number / asset tag</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}">
                </div>
                <div class="form-group">
                    <label>Owner / held by</label>
                    <input type="text" name="owner" value="{{ old('owner', $asset->owner) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Total quantity *</label>
                    <input type="number" name="quantity" min="1" value="{{ old('quantity', $asset->quantity ?? 1) }}" required>
                </div>
                <div class="form-group">
                    <label>Serviceable quantity *</label>
                    <input type="number" name="serviceable_qty" min="0" value="{{ old('serviceable_qty', $asset->serviceable_qty ?? 0) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Last test / inspection date</label>
                    <input type="date" name="last_test_date" value="{{ old('last_test_date', $asset->last_test_date?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label>Power / runtime (hours)</label>
                    <input type="number" step="0.5" name="power_runtime_hours" value="{{ old('power_runtime_hours', $asset->power_runtime_hours) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Location (where is it stored?)</label>
                <input type="text" name="location" value="{{ old('location', $asset->location) }}">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3">{{ old('notes', $asset->notes) }}</textarea>
            </div>

            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn btn--primary">{{ $asset->exists ? 'Save changes' : 'Add asset' }}</button>
                <a href="{{ route('committee.assets.index') }}" class="btn btn--secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
