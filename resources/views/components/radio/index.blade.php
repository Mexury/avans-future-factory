@props([
    'name' => '',
    'id' => null,
    'value' => '',
    'checked' => false
])

<label for="{{ $id ?? $name }}" {{ $attributes->class("relative flex items-center gap-2 p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-gray-600 font-bold has-[input:checked]:bg-gray-600 has-[input:checked]:text-white [&:not(:has(input:checked))]:hover:border-gray-500 [&:not(:has(input:checked))]:hover:text-gray-500") }}>
    <input
        tabindex="0"
        type="radio"
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        value="{{ $value }}"
        @checked($checked)
        {{ $attributes->except(['class', 'value']) }}
    >
    <label class="pointer-events-none select-none">{{ $slot }}</label>
</label>
