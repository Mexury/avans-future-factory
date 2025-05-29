@props([
    'calendar'
])

<div class="calendar">
    <header class="flex justify-center items-center gap-3 mb-4">
        <a href="{{ $calendar['prev'] }}" class="flex justify-center items-center p-4 rounded-sm min-w-24 font-bold bg-gray-700 hover:bg-gray-600 text-3xl">←</a>
        <div class="flex flex-col items-center min-w-28">
            <h1 class="text-2xl font-bold">{{ $calendar['year'] }}</h1>
            <p class="text-gray-400">{{ $calendar['monthName'] }}</p>
        </div>
        <a href="{{ $calendar['next'] }}" class="flex justify-center items-center p-4 rounded-sm min-w-24 font-bold bg-gray-700 hover:bg-gray-600 text-3xl">→</a>
    </header>
    <div class="grid grid-cols-5 gap-1">
        @foreach ($calendar['weeks'] as $days)
            @foreach ($days as $day)
                <x-calendar.day
                    :sameMonth="$day['month'] === $calendar['month']"
                    :path="$day['path']" :day="$day['day']"
                    :slots="$day['slots']"/>
            @endforeach
        @endforeach
    </div>
</div>
