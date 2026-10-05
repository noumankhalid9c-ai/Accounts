@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false, 'placeholder' => ''])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label fw-semibold">{{ $label }} @if($required) <span class="text-danger">*</span> @endif</label>
    @endif
    <input type="{{ $type }}" 
           class="form-control @error($name) is-invalid @enderror" 
           id="{{ $name }}" 
           name="{{ $name }}" 
           value="{{ old($name, $value) }}" 
           placeholder="{{ $placeholder }}"
           {{ $required ? 'required' : '' }}>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
