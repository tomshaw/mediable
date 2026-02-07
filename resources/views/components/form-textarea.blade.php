@props([
    'label',
    'id',
    'rows' => '3',
])

<div class="mb-1 w-full">
    <label for="{{ $id }}" class="inline-block text-gray-700 mb-1 text-xs font-normal">{{ $label }}</label>
    <textarea
        id="{{ $id }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'block text-gray-700 border border-gray-400 w-full px-3 py-1.5 appearance-none rounded-lg text-xs font-medium leading-5 tracking-wide focus:ring-0']) }}
    ></textarea>
</div>
