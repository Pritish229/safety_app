<!-- resources/views/components/textarea.blade.php -->
@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'rows' => 4,
    'required' => false,
    'id' => null,
])

<div class="form-group">
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}</label>
    @endif
    <div class="relative mt-1">
        <textarea
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'form-control']) }}
        >{{ old($name, $value) }}</textarea>
    </div>
    @error($name)
        <div class="error">{{ $message }}</div>
    @enderror
</div>

<style>
    .form-group {
        margin-bottom: 1rem;
    }
    .form-label {
        font-size: 0.875rem;
        color: #374151;
        font-weight: 500;
    }
    .form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        outline: none;
        transition: all 0.2s;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
    .error {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>