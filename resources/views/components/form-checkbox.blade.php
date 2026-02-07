@props([
    'label',
    'id',
])

<div class="my-2 w-full flex items-center gap-x-2">
    <input
        type="checkbox"
        id="{{ $id }}"
        {{ $attributes->merge(['class' => 'rounded border-gray-400 text-gray-700']) }}
    />
    <label for="{{ $id }}" class="text-gray-700 text-xs font-normal">{{ $label }}</label>
</div>
