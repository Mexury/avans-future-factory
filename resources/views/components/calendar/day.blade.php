@props([
    'sameMonth' => false,
    'path' => '',
    'day' => 1,
    'slots' => []
])

@if($sameMonth)
    <a href="{{ $path }}"
       class="relative flex flex-col items-center justify-center h-24 bg-gray-700 text-white rounded-sm hover:bg-gray-600">
        <span class="-translate-y-1">{{ $day }}</span>
        <span class="absolute flex gap-1 bottom-6">
            @foreach($slots as $slot)
                @if ($slot['isOccupied'])
                    <span class="block h-2 w-2 rounded-full bg-white"></span>
                @else
                    <span class="block h-2 w-2 rounded-full bg-gray-800"></span>
                @endif
            @endforeach
        </span>
    </a>
@else
    <a href="{{ $path }}"
       class="relative flex items-center justify-center h-24 bg-gray-800 text-white rounded-sm border border-gray-700 hover:border-gray-600 hover:bg-gray-600">
        <span class="-translate-y-1">{{ $day }}</span>
        <span class="absolute flex gap-1 bottom-6">
            @foreach($slots as $slot)
                @if ($slot['isOccupied'])
                    <span class="block h-2 w-2 rounded-full bg-white"></span>
                @else
                    <span class="block h-2 w-2 rounded-full bg-gray-700"></span>
                @endif
            @endforeach
        </span>
    </a>
@endif
