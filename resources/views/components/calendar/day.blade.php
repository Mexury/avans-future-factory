@props([
    'sameMonth' => false,
    'path' => '',
    'day' => 1
])

@if($sameMonth)
    <a href="{{ $path }}"
       class="flex items-center justify-center h-20 bg-gray-700 text-white rounded-sm hover:bg-gray-600">
        {{ $day }}
    </a>
@else
    <a href="{{ $path }}"
       class="flex items-center justify-center h-20 bg-gray-800 text-white rounded-sm border border-gray-700 hover:border-gray-600 hover:bg-gray-600">
        {{ $day }}
    </a>
@endif
