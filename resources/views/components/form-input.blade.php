@props([
    'label',
    'id',
    'type' => 'text',
])

<div class="mb-1 w-full">
    <label for="{{ $id }}" class="inline-block text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mb-1">{{ $label }}</label>
    <input
        type="{{ $type }}"
        id="{{ $id }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-xs font-medium leading-5 tracking-wide text-zinc-700 dark:text-zinc-200 appearance-none transition-colors focus:border-indigo-400 dark:focus:border-indigo-500']) }}
    />
</div>
