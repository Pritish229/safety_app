<!-- resources/views/components/input.blade.php -->
@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'icon' => null,
    'required' => false,
    'id' => null,
])

<div class="form-group">
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}</label>
    @endif
    <div class="relative mt-1">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'form-control']) }}
        >
        @if($icon)
            <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                <i class="fas fa-{{ $icon }} text-gray-400"></i>
            </span>
        @endif
    </div>
    @error($name)
        <div class="error">{{ $message }}</div>
    @enderror
</div>