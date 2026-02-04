@props([
    'label',
    'id',
    'type' => 'text',
    'rows' => null,
])

<div class="mb-1 w-full">
    <label for="{{ $id }}" class="inline-block text-gray-500 mb-1 text-xs font-normal">{{ $label }}</label>
    @if($rows)
        <textarea
            id="{{ $id }}"
            rows="{{ $rows }}"
            {{ $attributes->merge(['class' => 'control-input w-full focus:ring-0']) }}
        ></textarea>
    @else
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            {{ $attributes->merge(['class' => 'control-input w-full']) }}
        />
    @endif
</div>
