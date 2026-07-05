@props([
    'label',
    'id',
    'placeholder' => '',
    'options' => [],
])

<div class="mb-1 w-full">
    <label for="{{ $id }}" class="inline-block text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mb-1">{{ $label }}</label>
    <select
        id="{{ $id }}"
        {{ $attributes->merge(['class' => 'block w-full cursor-pointer rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 py-1.5 px-2 text-xs font-medium leading-5 text-zinc-700 dark:text-zinc-200 transition-colors focus:border-indigo-400 dark:focus:border-indigo-500']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
</div>
