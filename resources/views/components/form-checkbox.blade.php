@props([
    'label',
    'id',
])

<div class="my-2 w-full flex items-center gap-x-2">
    <input
        type="checkbox"
        id="{{ $id }}"
        {{ $attributes->merge(['class' => 'rounded border-zinc-300 dark:border-zinc-600 accent-indigo-600 cursor-pointer']) }}
    />
    <label for="{{ $id }}" class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 cursor-pointer">{{ $label }}</label>
</div>
