{{-- resources/views/admin/activity-logs/_form.blade.php --}}

@if($errors->any())
    <div class="al-form-errors">
        <strong>Please fix the following errors:</strong>
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="al-form-grid">

    <div class="al-form-group">
        <label for="user_id">User <span class="al-required">*</span></label>
        <select name="user_id" id="user_id" class="{{ $errors->has('user_id') ? 'is-error' : '' }}" required>
            <option value="">— Select a user —</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}"
                    {{ old('user_id', $activityLog?->user_id) == $u->id ? 'selected' : '' }}>
                    {{ $u->name }}
                </option>
            @endforeach
        </select>
        @error('user_id')<span class="al-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="al-form-group">
        <label for="event_name">Event Name <span class="al-required">*</span></label>
        <input type="text" name="event_name" id="event_name"
               value="{{ old('event_name', $activityLog?->event_name) }}"
               placeholder="e.g. Club Meeting, Workshop, Training Day"
               class="{{ $errors->has('event_name') ? 'is-error' : '' }}"
               required>
        @error('event_name')<span class="al-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="al-form-group">
        <label for="event_date">Event Date <span class="al-required">*</span></label>
        <input type="date" name="event_date" id="event_date"
               value="{{ old('event_date', $activityLog?->event_date?->format('Y-m-d')) }}"
               class="{{ $errors->has('event_date') ? 'is-error' : '' }}"
               required>
        @error('event_date')<span class="al-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="al-form-group">
        <label for="hours">Hours Attended <span class="al-required">*</span></label>
        <input type="number" name="hours" id="hours"
               value="{{ old('hours', $activityLog?->hours) }}"
               step="0.25" min="0.25" max="24"
               placeholder="e.g. 2.5"
               class="{{ $errors->has('hours') ? 'is-error' : '' }}"
               required>
        @error('hours')<span class="al-field-error">{{ $message }}</span>@enderror
        <span class="al-field-hint">Enter in 0.25 increments. Max 24.</span>
    </div>

</div>

<div class="al-acad-preview" id="acadPreview" style="display:none">
    Academic year: <strong id="acadYear"></strong>
</div>

<script>
(function () {
    const dateInput = document.getElementById('event_date');
    const preview   = document.getElementById('acadPreview');
    const yearSpan  = document.getElementById('acadYear');
    function calcYear(val) {
        if (!val) { preview.style.display = 'none'; return; }
        const d = new Date(val);
        const m = d.getMonth() + 1;
        const y = d.getFullYear();
        const start = m >= 9 ? y : y - 1;
        yearSpan.textContent = start + '/' + String(start + 1).slice(-2);
        preview.style.display = 'block';
    }
    dateInput.addEventListener('change', () => calcYear(dateInput.value));
    calcYear(dateInput.value);
})();
</script>