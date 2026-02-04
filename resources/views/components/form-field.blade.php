@props([
    'label',
    'id',
    'type' => 'text',
])

<div class="mb-1 w-full">
    <label for="{{ $id }}" class="inline-block text-gray-500 mb-1 text-xs font-normal">{{ $label }}</label>
    <input
        type="{{ $type }}"
        id="{{ $id }}"
        {{ $attributes->merge(['class' => 'control-input w-full']) }}
    />
</div>
