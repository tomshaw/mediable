@props([
    'placeholder' => '',
    'options' => [],
])

<select
    {{ $attributes->merge(['class' => 'block h-8 cursor-pointer rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 py-1 pl-2 pr-6 text-xs font-medium leading-5 tracking-wide text-zinc-600 dark:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors']) }}
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach($options as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
