@props([
    'placeholder' => '',
    'options' => [],
])

<select
    {{ $attributes->merge(['class' => 'block cursor-pointer rounded-full border-0 shadow-none outline-none ring-0 bg-[#555] py-1 px-2 appearance-none text-xs font-medium leading-5 tracking-wide text-gray-50']) }}
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach($options as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
