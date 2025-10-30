<!-- resources/views/components/select.blade.php -->
@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'options' => [],
    'placeholder' => '',
    'required' => false,
    'id' => null,
])

<div class="form-group">
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}</label>
    @endif
    <div class="relative mt-1">
        <select
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'form-control']) }}
        >
            @if($placeholder)
                <option value="" disabled {{ old($name, $value) ? '' : 'selected' }}>{{ $placeholder }}</option>
            @endif
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>
            @endforeach
        </select>
    </div>
    @error($name)
        <div class="error">{{ $message }}</div>
    @enderror
</div>