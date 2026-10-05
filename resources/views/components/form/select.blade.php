@props(['name', 'label', 'options' => [], 'selected' => '', 'required' => false])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label fw-semibold">{{ $label }} @if($required) <span class="text-danger">*</span> @endif</label>
    @endif
    <select class="form-select @error($name) is-invalid @enderror" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            {{ $required ? 'required' : '' }}>
        <option value="">-- Select --</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
