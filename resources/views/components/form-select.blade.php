@props([
    'label',
    'id',
    'placeholder' => '',
    'options' => [],
])

<div class="mb-1 w-full">
    <label for="{{ $id }}" class="inline-block text-gray-700 mb-1 text-xs font-normal">{{ $label }}</label>
    <select
        id="{{ $id }}"
        {{ $attributes->merge(['class' => 'block text-gray-700 border border-gray-400 w-full py-1 px-2 appearance-none rounded-md text-xs font-medium leading-5']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
</div>
